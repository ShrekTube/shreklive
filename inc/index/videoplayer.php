<div class="mdl-grid mdl-grid--no-spacing fill">
	<div class="mdl-content fill">
		<div class="live-player">
			<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered" controls autoplay preload
				   id="videoPlayer"
				   width="100%" height="100%"
				   poster="//<?= $surl ?>/img/video_<?php echo str_replace(".mp4", ".png", $video); ?>">
			</video>
		</div>
	</div>
</div>

<script>var ischat = false;</script>