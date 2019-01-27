/**
 * Created by Joel on 10/27/2016.
 */
if (window.jQuery) {
	$(function () {
		let pauseHeartbeat = false;
		let heartbeatXHR;
		let recIconName = $('.record-button').eq(0).next('i').text();
		var lastid = 0;
		let toggled = false;
		var scrolledPct = 0;

		/** CHAT FUNCTIONS **/

		// Chatbox show/hide
		$("#toggleChat").click(function () {
			if (toggled) {
				$('.chat-container').stop().animate({'width': '20%'}, 500);
				$('.video-container').stop().animate({'width': '80%'}, 500);
				toggled = false;
			} else {
				$('.chat-container').stop().animate({'width': '0%'}, 500);
				$('.video-container').stop().animate({'width': '100%'}, 500);
				toggled = true;
			}
		});

		// Join request sent on page load, but only if chat is present on the page.
		if (ischat === true) {
			$.ajax({
				url: "/api/" + api_key + "/chat/join/" + current_channel,
				dataType: 'json'
			});
		}

		// Leave request sent on page unload
		$(window).on('beforeunload', function () {
			if (ischat === true) {
				$.ajax({
					url: "/api/" + api_key + "/chat/leave/" + current_channel,
					dataType: 'json'
				});
			}
		});

		// Simple regex for identifying URLs in the chat.
		function urlify(text) {
			let regex = /(https?:\/\/[^\s]+)/g;
			return text.replace(regex, '<a href="$1">$1</a>')
		}

		// checks for current_channel being present, which is a clear indicator that we're looking at a channel
		// and want to run these functions.
		if (typeof current_channel === "undefined") {
			console.log('Heartbeats not run.');
		} else {

			// Check for new messages every 500ms and output to chatbox
			setInterval(function () {
					let chatbox = $('#output .mCSB_container');
					$.ajax({
						url: "/api/" + api_key + "/chat/read/" + current_channel,
						dataType: 'json'
					}).done(function (getLines) {
						let scrollHeight = chatbox.prop('scrollHeight') - chatbox.height();
						$.each(getLines, function (id, line) {
							id = parseInt(line.id, 10);
							let type = line.type;
							if (lastid < id) {
								let unixTimeStamp = line.timestamp;
								let timestampInMilliSeconds = unixTimeStamp * 1000;
								let date = new Date(timestampInMilliSeconds);
								let formattedDate = date.format('h:i a');
								lastid = id;
								if (type === 'SYSTEM') {
									if (jp_status === 't') {
										chatbox.append(" <span style='color: rgb(179, 179, 179);'>(" + formattedDate + ')<span style="font-style: italic"> ' + line.sender + ' ' + urlify(line.message) + '</span></span><br />');

									}
								} else {
									chatbox.append('(' + formattedDate + ") <span style='color: rgb(0, 188, 212); font-weight: bold'>" + line.sender + ':</span> ' + urlify(line.message) + '<br />');

								}
							}
						});
						if (scrolledPct === 100) {
							$('#output').mCustomScrollbar('scrollTo', 'bottom');
						}
					});
				},
				500
			);

			// Set recording button status, and refreshes page if stream was offline and is now live
			setInterval(function () {
				if (!pauseHeartbeat) {
					heartbeatXHR = $.getJSON('/api/' + api_key + '/stream/ping', function (info) {
						//console.log('Thump thump... \r\nChannel Info: ', info);
						if (typeof stream_key != "undefined") {
							if (typeof info[stream_key] !== 'undefined' && info[stream_key].active === true && live_status === false) {
								console.log("We're live!");
								resetPlayer();
							}
							if (typeof info[stream_key] === 'undefined' && live_status === true) {
								console.log("Channel offline.");
								live_status = false;
								$('.vjs-error-display').hide();

								$('.vjs-poster').css({
									'background-image': 'url(' + videoposter + ')',
									'display': 'block'
								});
								$('.vjs-paused .vjs-big-play-button').css({'display': 'none'});
							}
						}

						$('.record-button').each(function () {
							let channel = $(this).parent('label').parent('td').parent('tr').attr('channel');
							if (typeof info[channel] !== 'undefined') {
								if (info[channel].recording === true && $(this).not(':checked')) {
									$(this).prop('checked', true);
									$(this).parent('label').addClass('is-checked');
									$(this).next('i').text('stop');
								} else if (info[channel].recording === false && $(this).is(':checked')) {
									$(this).prop('checked', false);
									$(this).parent('label').removeClass('is-checked');
									$(this).next('i').text(recIconName);
								}
							}
						});
					});
				}
			}, 2000);
		}

		// Capture the input box so that enter submits a message instead of newline, but still allow for shift+enter
		$("#inputMessage").keypress(function (e) {
			if (e.which === 13 && !e.shiftKey) {
				$(this).closest("form").submit();
				e.preventDefault();
				return false;
			}
		});

		// Submit message to chat API
		$('#chatMessage').submit(function (event) {
			const data = {
				'message': $('#inputMessage').val(),
				'timestamp': Math.round(new Date().getTime() / 1000),
				'user': display_name,
				'channel': current_channel,
				'type': 'USER'
			};
			event.preventDefault();
			$.ajax({
				type: "POST",
				url: "/api/" + api_key + "/chat/write/",
				data: data,
				dataType: 'json'
			});
			$('#inputMessage').val("").parent('div').removeClass('is-dirty');
		});

		/** CHANNEL FUNCTIONS **/

		// Set recording button status, and refreshes page if stream was offline and is now live
		if (typeof api_key === "undefined") {
			console.log('Heartbeat not run');
		} else {

		}

		// Recording start/stop on icon button click
		$('.record-button').click(function () {
			let channel = $(this).parent('label').parent('td').parent('tr').attr('channel');
			let icon = $(this).next('i');
			pauseHeartbeat = true;
			heartbeatXHR.abort();
			if ($(this).is(':checked')) {
				icon.text('stop');
				$.getJSON('/api/' + api_key + '/stream/record-start/' + channel, function (recordingPath) {
					if (recordingPath === "") {
						console.log('Error starting recording');
					} else {
						console.log(recordingPath);
					}
					pauseHeartbeat = false;
				});
			} else {
				icon.text(recIconName);
				$.getJSON('/api/' + api_key + '/stream/record-stop/' + channel, function (recordingPath) {
					if (recordingPath === "") {
						console.log('Error stopping recording');
					} else {
						console.log(recordingPath);
					}
					pauseHeartbeat = false;
				});
			}
		});

		// Subscribe button functions
		$('#subButton').click(function () {
			let snackbarContainer = document.querySelector('#subToast');
			let button = $(this);
			let channel = $(this).attr('channel');
			let action = $(this).text();
			'use strict';
			if (action === 'Unsubscribe') {
				console.log('Action = Unsubscribe');
				$.getJSON('/api/' + api_key + '/subscription/remove/' + channel, function (result) {
					if (result === false) {
						console.log('Error unsubscribing');
					} else {
						const data = {
							message: 'Unsubscribed from ' + stream_key + '.',
							timeout: 5000
						};
						console.log(result);
						button.text('Subscribe');
						button.css('background-color', '');
						snackbarContainer.MaterialSnackbar.showSnackbar(data);
					}
				});
			} else if (action === 'Subscribe') {
				console.log('Action = Subscribe');
				$.getJSON('/api/' + api_key + '/subscription/add/' + channel, function (result) {
					if (result === false) {
						const data = {
							message: 'Error subscribing (probably already subscribed)!',
							timeout: 5000
						};
						console.log('Error subscribing' + result);
						snackbarContainer.MaterialSnackbar.showSnackbar(data);
					} else {
						const data = {
							message: 'Subscribed to ' + stream_key + '!',
							timeout: 5000
						};
						console.log(result);
						button.text('Unsubscribe');
						button.css('background-color', '#00bcd4');
						snackbarContainer.MaterialSnackbar.showSnackbar(data);
					}
				});
			}
		});

		// Link copy toast
		$('#copyButton').click(function () {
			let snackbarContainer = document.querySelector('#keyCopy');
			const data = {
				message: 'Stream key copied!',
				timeout: 5000
			};
			snackbarContainer.MaterialSnackbar.showSnackbar(data);
		});

		// File upload name update workaround
		$('#avatar').change(function () {
			document.getElementById("avatar_file").value = this.files[0].name;
		});
		$('#offline').change(function () {
			document.getElementById("offline_file").value = this.files[0].name;
		});

		if (window.mCustomScrollbar) {
			// Custom scrollbar initializations
			$(window).load(function () {
				$(".scrollContent").mCustomScrollbar({
					theme: "inset",
					scrollInertia: 400,
					scrollButtons: {enable: true}
				});
			});
			$("#output").mCustomScrollbar({
				theme: "inset",
				scrollInertia: 400,
				callbacks: {
					onInit: function () {
						$("#output").mCustomScrollbar('scrollTo', 'bottom');
					},
					whileScrolling: function () {
						scrolledPct = this.mcs.topPct;
					},
				}
			});
		} else {
			console.log('No mCustomScrollbar found! Please load it first.');
		}
	});

} else {
	console.log('No jQuery found! Please load jQuery first.');
}

