<?php
debug_backtrace() || die ('Direct access not permitted');

if(ENABLE_ICAL){

	$ical_url = getUrl(true).DOCBASE.'includes/icalendar/ical_render.php?uid='.$_SESSION['user']['id'].$_SESSION['user']['add_date'].'&room='.$id; ?>

	<div class="panel-body mt10">
		<fieldset>
			<legend><b><?php echo $texts['ICAL_SYNCHRONIZATION']; ?></b></legend>
			<div class="row mb10">
				<label class="col-lg-2 control-label">
					<?php echo $texts['CALENDAR_URL']; ?>
				</label>
				<div class="col-lg-6">
					<div class="input-group">
						<input type="text" value="<?php echo $ical_url; ?>" class="form-control" id="iCalUrl" readonly="readonly">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button" onclick="copy_iCalUrl()" data-toggle="tooltip" data-placement="button" title="<?php echo $texts['COPY_TO_CLIPBOARD']; ?>" id="iCalUrl_btn"><i class="fa fa-fw fa-clipboard"></i> <?php echo $texts['COPY']; ?></button>
						</span>
					</div>
					<a class="btn btn-default mt5" href="<?php echo $ical_url; ?>" target="_blank"><i class="fa fa-fw fa-download"></i> <?php echo $texts['DOWNLOAD_CALENDAR']; ?></a>
				</div>
			</div>
		</fieldset>
	</div>


	<script>
		function copy_iCalUrl() {
			var copyText = document.getElementById('iCalUrl');
			copyText.select();
			copyText.setSelectionRange(0, 99999);
			document.execCommand('copy');
			
			$('#iCalUrl_btn').attr('title', '<?php echo $texts['COPIED']; ?>')
				.tooltip('fixTitle')
				.tooltip('show')
				.attr('title', '<?php echo $texts['COPY_TO_CLIPBOARD']; ?>')
				.tooltip('fixTitle');
		}
	</script>
	<?php
} ?>
