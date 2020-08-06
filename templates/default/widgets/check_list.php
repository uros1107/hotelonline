<?php debug_backtrace() || die ("Direct access not permitted");

if($widget['content'] != ''){
	$text_widget = explode('<hr />',$widget['content']);
	if(count($text_widget) > 0){ ?>
        <div class="row mb30">
			<?php
			foreach($text_widget as $i => $content){ ?>
				<div class="col-md-4">
					<article class="iconBlocHome<?php if(RTL_DIR) echo ' rtl'; ?>">
						<i class="fa fa-check"></i>
						<?php echo $content; ?>
					</article>
				</div>
				<?php
			} ?>
		</div>
		<?php
	}
} ?>
