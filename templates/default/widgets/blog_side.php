<?php
debug_backtrace() || die ('Direct access not permitted');

if(isset($pages[$page_id]['articles'])){ ?>
	<div class="boxed mb20">
		<h3><?php echo $texts['RECENT_ARTICLES']; ?></h3>
		<ul class="nostyle">
			<?php
			$i = 0;
			foreach($pages[$page_id]['articles'] as $id => $row){
				if($id != $article_id){
					if($i++ >= 10) break; ?>
					<li><i class="fa fa-angle-right"></i> <a href="<?php echo DOCBASE.$row['alias']; ?>"><?php echo $row['title']; ?></a></li>
					<?php
				}
			} ?>
		</ul>
	</div>
	<div class="boxed mb20">
		<h3><?php echo $texts['ARCHIVES']; ?></h3>
		<?php
		$months = array();
		$end_month = mktime(23, 59, 59, date('n'), date('t'), date('Y'));
		$result_article = $db->query('
			SELECT * FROM pm_article
			WHERE id_page = '.$page_id.' AND checked = 1
				AND (publish_date IS NULL || publish_date <= '.time().')
				AND (unpublish_date IS NULL || unpublish_date > '.time().')
				AND CASE WHEN publish_date IS NOT NULL THEN publish_date ELSE add_date END <= '.$end_month.'
				AND lang = '.LANG_ID.'
				AND (show_langs IS NULL || show_langs = \'\' || show_langs REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
				AND (hide_langs IS NULL || hide_langs = \'\' || hide_langs NOT REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
			ORDER BY CASE WHEN publish_date IS NOT NULL THEN publish_date ELSE add_date END DESC');
		if($result_article !== false && $db->last_row_count() > 0){
			foreach($result_article as $row){
				$publish_date = !is_null($row['publish_date']) ? $row['publish_date'] : $row['add_date'];
				$d = mktime(0, 0, 0, date('n', $publish_date), 1, date('Y', $publish_date));
				
				if(!isset($months[$d])) $months[$d] = 1;
				else $months[$d]++;
			} ?>
			<ul class="nostyle">
				<?php
				foreach($months as $d => $count){ ?>
					<li><i class="fa fa-angle-right"></i> <a href="<?php echo DOCBASE.$page['alias']; ?>?month=<?php echo date('n', $d); ?>&year=<?php echo date('Y', $d); ?>"><?php echo strftime('%B %Y', $d).' ('.$count.')'; ?></a></li>
					<?php
				} ?>
			</ul>
			<?php
		} ?>
	</div>
	<?php
}

$result_tag = $db->query('SELECT * FROM pm_tag WHERE pages REGEXP \'(^|,)'.$page_id.'(,|$)\' AND checked = 1 AND lang = '.LANG_ID.' ORDER BY rank');
if($result_tag !== false && $db->last_row_count() > 0){ ?>
	<div class="boxed mb20">
		<h3><?php echo $texts['TAGS']; ?></h3>
		<?php
		foreach($result_tag as $i => $row){ ?>
			<a href="<?php echo DOCBASE.$page['alias']; ?>?tag=<?php echo $row['id']; ?>" class="btn btn-default mb5"><?php echo $row['value']; ?></a>
			<?php
		} ?>
	</div>
	<?php
}

