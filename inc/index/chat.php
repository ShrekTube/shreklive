<div class="mdl-grid mdl-grid fill">
	<div class="mdl-cell mdl-card mdl-shadow--2dp employer-form" style="width: 75%;">
		<div class="mdl-card__title">
			<span class="mdl-color-text--cyan-500">Global Chat Channel</span>
		</div>

		<div class="mdl-card__supporting-text full-height">
			<div class="fill">
				<div id="output" class="fill">

				</div>
			</div>
		</div>

		<div class="chatBorder"></div>

		<form action="" method="POST" class="form" id="chatMessage">

			<div class="chatinput" style="position:relative;">

				<div class="mdl-grid--no-spacing chatbox">
					<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">

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
<script> var ischat = true; </script>