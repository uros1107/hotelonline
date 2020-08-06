<?php
debug_backtrace() || die ("Direct access not permitted");

$javascripts[] = '//cdn.jsdelivr.net/npm/jquery.stellar@0.6.2/jquery.stellar.js'; ?>

<div id="parallaxHome" class="stellar mt30" data-stellar-background-ratio="0.5" style="background-image:url('<?php echo getFromTemplate('images/bg-parallax.jpg'); ?>');">
	<div class="parallaxContent">
		<div class="container">
			<div class="row">
				<div class="col-md-10 col-md-offset-1 text-center">
					<?php echo $widget['content']; ?>
				</div>
			</div>
		</div>
	</div>
</div>
