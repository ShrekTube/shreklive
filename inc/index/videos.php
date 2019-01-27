<div class="mdl-grid mdl-grid--no-spacing">
	<div class="mdl-grid mdl-cell mdl-cell--12-col-desktop mdl-cell--12-col-tablet mdl-cell--4-col-phone mdl-cell--top mdl-cell--stretch">
		<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
			<?php
			$videos = glob("$site_recpath*.mp4");
			if (count($videos) > 0) {
				foreach ($videos as $key => $eachVideo) {
					$file = substr($eachVideo, strrpos($eachVideo, "/") + 1);
					$skey = substr($file, 0, strrpos($file, "-"));
					$skey = $user->updateStreamkey($skey, 'channel');
					$timestamp = substr($file, strrpos($file, "-") + 1, -4);
					$datetime = date("Y-m-d H:i:s", $timestamp);
					$screenshot = 'img/video/video_' . str_replace('mp4', 'png', $file);
					if (!file_exists($screenshot)) {
						$screenshot = 'img/no-preview.jpg';
					}

					$mediainfo = [];
					try {
						$mediainfo = mediainfo::fetchVideo($eachVideo);

						// eval FPS to get rid of fractions:
						// - 30/1 becomes 30
						// - 2997/100 becomes 29.97

						if (isset($mediainfo["streams"][0]["r_frame_rate"])) {
							eval('$mediainfo["streams"][0]["r_frame_rate"] = ' . $mediainfo["streams"][0]["r_frame_rate"] . ';');
						} else {
							$mediainfo["streams"][0]["r_frame_rate"] = 'n/a';
						}
					} catch
					(Exception $e) {
						print 'Cauth Exception with message ' . $e->getMessage();
					}

					$videos[$key] = ["file" => $file, "screenshot" => $screenshot, "channel" => $skey, "timestamp" => $timestamp, "datetime" => $datetime, "mediainfo" => $mediainfo];
				}
				?>
				<div class="mdl-tabs__tab-bar">
					<a href="#grid-view" class="mdl-tabs__tab is-active">Grid View</a>
					<a href="#list-view" class="mdl-tabs__tab">List View</a>
				</div>

				<div class="mdl-tabs__panel is-active" id="grid-view">
					<div class="mdl-grid">
						<?php
						foreach ($videos as $key => $eachVideo) {
							echo '
							<div class="mdl-cell mdl-cell--4-col">
								<div class="grid">
									<a href="/video/' . $eachVideo["file"] . '">
										<figure class="effect-sarah">
											<img src="' . $eachVideo["screenshot"] . '" alt="' . $eachVideo["channel"] . '">
											<figcaption>
												<h2>' . $eachVideo['channel'] . '</h2>
												<p>' . $eachVideo['datetime'] . '</p>
											</figcaption>
										</figure>
									</a>
								</div>
							</div>
							';
						}
						?>


					</div>
				</div>
				<div class="mdl-tabs__panel" id="list-view">
					<div class="mdl-grid">
						<div class="mdl-cell mdl-cell--4-col"></div>
						<div class="mdl-cell mdl-cell--4-col">
							<table class="mdl-data-table mdl-js-data-table full-width">
								<thead>
								<tr>
									<th class="mdl-data-table__cell--non-numeric">Channel</th>
									<th class="mdl-data-table__cell--non-numeric">Date</th>
									<th class="mdl-data-table__cell--non-numeric">Duration</th>
									<th class="mdl-data-table__cell--non-numeric">Definition</th>
									<th class="mdl-data-table__cell--non-numeric">Size</th>
									<th class="mdl-data-table__cell--non-numeric">Download</th>
									<th class="mdl-data-table__cell--non-numeric">Play</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$tooltipHtml = '';
								foreach ($videos as $eachVideo) {
									$seed = uniqid();
									echo '
										<tr>
											<td class="mdl-data-table__cell--non-numeric">' . $eachVideo["channel"] . '</td>
											<td class="mdl-data-table__cell--non-numeric">' . date("Y-m-d H:i:s", $eachVideo["timestamp"]) . '</td>
											<td>' . date("H:i:s", $eachVideo["mediainfo"]["format"]["duration"]) . '</td>
											<td class="mdl-data-table__cell--non-numeric" id="stream-detail-' . $seed . '">' . $eachVideo["mediainfo"]["streams"][0]["height"] . 'p@' . $eachVideo["mediainfo"]["streams"][0]["r_frame_rate"] . 'fps</td>
											<td>' . bytesConvert($eachVideo["mediainfo"]["format"]["size"]) . 'B</td>
											<td class="mdl-data-table__cell--non-numeric mdl-typography--text-center"><a href="/download/' . $eachVideo["file"] . '"><i class="material-icons">file_download</i></a></td>
											<td class="mdl-data-table__cell--non-numeric mdl-typography--text-center"><a href="/video/' . $eachVideo["file"] . '"><i class="material-icons">play_circle_filled</i></a></td>
										</tr>
										';

									$tooltipHtml .= '
											<div class="mdl-tooltip mdl-tooltip--large" for="stream-detail-' . $seed . '">
												<p>Video</p>
												<ul class="mdl-list">
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Codec: ' . (isset($eachVideo["mediainfo"]["streams"][0]["codec_name"]) ? $eachVideo["mediainfo"]["streams"][0]["codec_name"] : 'Undefined') . ' ' . (isset($eachVideo["mediainfo"]["streams"][0]["profile"]) ? $eachVideo["mediainfo"]["streams"][0]["profile"] : 'Undefined') . '</li>
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Bitrate: ' . (isset($eachVideo["mediainfo"]["streams"][0]["bit_rate"]) ? bitsConvert($eachVideo["mediainfo"]["streams"][0]["bit_rate"]) . 'b/s' : 'Undefined') . '</li>
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Definition: ' . (isset($eachVideo["mediainfo"]["streams"][0]["width"]) ? $eachVideo["mediainfo"]["streams"][0]["width"] : 'Undefined') . 'x' . (isset($eachVideo["mediainfo"]["streams"][0]["height"]) ? $eachVideo["mediainfo"]["streams"][0]["height"] : 'Undefined') . '</li>
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Framerate: ' . (isset($eachVideo["mediainfo"]["streams"][0]["r_frame_rate"]) ? $eachVideo["mediainfo"]["streams"][0]["r_frame_rate"] . ' fps' : 'Undefined') . '</li>
												</ul>
												<br />
												<p>Audio</p>
												<ul class="mdl-list">
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Codec: ' . (isset($eachVideo["mediainfo"]["streams"][1]["codec_name"]) ? $eachVideo["mediainfo"]["streams"][1]["codec_name"] : 'Undefined') . '</li>
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Bitrate: ' . (isset($eachVideo["mediainfo"]["streams"][1]["bit_rate"]) ? bitsConvert($eachVideo["mediainfo"]["streams"][1]["bit_rate"]) . 'b/s' : 'Undefined') . '</li>
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Sample Rate: ' . (isset($eachVideo["mediainfo"]["streams"][1]["sample_rate"]) ? $eachVideo["mediainfo"]["streams"][1]["sample_rate"] . ' Hz' : 'Undefined') . '</li>
													<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Channels: ' . (isset($eachVideo["mediainfo"]["streams"][1]["channels"]) ? $eachVideo["mediainfo"]["streams"][1]["channels"] : 'Undefined') . '</li>
												</ul>
											</div>
										';
								}
								?>
								</tbody>
							</table>
							<?= $tooltipHtml; ?>
						</div>
						<div class="mdl-cell mdl-cell--4-col"></div>
					</div>
				</div>
				<?php
			} else {
				?>
				<div class="mdl-card mdl-shadow--2dp full-height">
					<div class="mdl-card__title mdl-card--expand">
						<h2 class="mdl-card__title-text">No videos available.</h2>
					</div>
					<div class="mdl-card__supporting-text">
						There are currently no recorded videos. Check back later, maybe someone will record something!
					</div>
					<div class="mdl-card__actions mdl-card--border">
						<a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" href="/videos"><i
									class="material-icons">refresh</i> Refresh
						</a>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

<script>
	var ischat = false;
	$(window).load(function () {
		$("#mainContent").addClass('scrollContent');
	});
</script>