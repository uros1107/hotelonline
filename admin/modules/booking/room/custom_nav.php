<?php
debug_backtrace() || die ('Direct access not permitted'); ?>

<a href="../booking/availabilities.php" class="btn btn-default mt15 mb15">
    <i class="fa fa-calendar"></i> <?php echo $texts['AVAILABILITIES']; ?>
</a>
<?php
if(ENABLE_ICAL){ ?>
	<a class="btn btn-default mt15 mb15 sendAjaxForm" data-action="<?php echo DOCBASE; ?>includes/icalendar/ical_import.php?ical_sync_mode=manual&ical_sync_all=1"><i class="fa fa-calendar"></i> <?php echo $texts['SYNC_ALL_CALENDARS']; ?></a>
	<?php
} ?>
