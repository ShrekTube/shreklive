<div class="fill stream-container">
	<div class="video-container" id="videoBox">
		<div class="live-player">
			<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered" controls autoplay preload
				   id="streamPlayer"
				   width="100%" height="100%" poster=poster="//<?= $surl ?>/img/channel/channel_<?= $streamkey ?>.png">
			</video>
		</div>
	</div>
	<div class="mdl-card mdl-shadow--2dp employer-form chat-container" id="channelchat">
		<div class="mdl-card__title">
			<span class="mdl-color-text--cyan-500">Channel Chat</span>
		</div>
		<div class="mdl-card__supporting-text full-height">
			<div class="fill">
				<div id="output" class="fill">

				</div>
			</div>
		</div>
		<div class="chatBorder"></div>
		<form action="" method="POST" class="form full-wdith" id="chatMessage">
			<div class="chatinput full-width" style="position:relative;">
				<div class="mdl-grid--no-spacing chatbox">
					<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                        <textarea class="mdl-textfield__input" type="text" name="inputMessage" id="inputMessage"
								  rows="1" autocomplete="off"></textarea>
						<label class="mdl-textfield__label" for="inputMessage">Enter message</label>
					</div>
					<button type="submit" form="chatMessage"
							class="mdl-button mdl-js-button mdl-button--icon sendbutton">
						<i class="material-icons sendbuttonicon">send</i>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script> var ischat = true;</script>