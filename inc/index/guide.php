<div class="mdl-card mdl-shadow--2dp employer-form" style="width: 700px;">
	<div class="mdl-card__title">
		<span class="mdl-color-text--cyan-500">How to Configure OBS Studio</span>
	</div>
	
	<div class="mdl-card__supporting-text">
		<div class="mdl-grid">
			<ul class='mdl-list'>
				<li class="mdl-list__item">
					<span class="mdl-list__item-primary-content">Open <a href="https://obsproject.com" target="_blank"> OBS Studio</a>, and click on&nbsp;<b>Settings</b></span>
				</li>
				<li class="mdl-list__item">
					<span class="mdl-list__item-primary-content">Click on the&nbsp;<b>Stream tab</b></span>
				</li>
				<li class="mdl-list__item">
					<span class="mdl-list__item-primary-content">For&nbsp;<b>Stream Type</b>, select Custom Streaming Server from the dropdown</span>
				</li>
				<li class="mdl-list__item">
					<span class="mdl-list__item-primary-content">For&nbsp;<b>URL</b>, enter: rtmp://<?= $surl ?>/live</span>
				</li>
				<li class="mdl-list__item mdl-list__item--two-line">
					<span class="mdl-list__item-primary-content">
						<span>For <b>Stream Key</b>, you must enter your current Display Name and Stream Key as follows:</span>
						<span class="mdl-list__item-sub-title mdl-color-text--cyan-500 mdl-typography--text-center"><?= $accountinfo['display_name'] ?>?key=<?= $accountinfo['stream_key'] ?></span>
					</span>
				</li>
				<li class="mdl-list__item">
					<span class="mdl-list__item-primary-content">Note: if you update your Display Name, you will need to update it in OBS. The value displayed on this page is generated dynamically</span>
				</li>
			</ul>
		</div>
	</div>
</div>
<script>
	var ischat = false;
	$(window).load(function () {
		$("#mainContent").addClass('scrollContent');
	});
</script>