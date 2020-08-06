<?php debug_backtrace() || die ("Direct access not permitted"); ?>
<ul>
    <?php
    foreach($menus['footer'] as $nav_id => $nav){ ?>
		<li class="mb5"><a href="<?php echo $nav['href']; ?>" title="<?php echo $nav['title']; ?>"><?php echo $nav['name']; ?></a></li>
		<?php
    } ?>
</ul>
