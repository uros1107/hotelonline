<?php
/* ==============================================
 * CSS AND JAVASCRIPT USED IN THIS MODEL
 * ==============================================
 */
$stylesheets[] = array('file' => DOCBASE.'js/plugins/lazyloader/lazyloader.css', 'media' => 'all');
$javascripts[] = DOCBASE.'js/plugins/lazyloader/lazyloader.js';

$stylesheets[] = array('file' => DOCBASE.'js/plugins/star-rating/css/star-rating.min.css', 'media' => 'all');
$javascripts[] = DOCBASE.'js/plugins/star-rating/js/star-rating.min.js';

require(getFromTemplate('common/send_comment.php', false));

require(getFromTemplate('common/header.php', false)); ?>

<section id="page">
    
    <?php include(getFromTemplate('common/page_header.php', false)); ?>
    
    <div id="content" class="pt30 pb20">
        <div class="container" itemprop="text">
            
            <div class="alert alert-success" style="display:none;"></div>
            <div class="alert alert-danger" style="display:none;"></div>
            
            <div class="row">
                <?php
                $widgetsLeft = getWidgets('left', $page_id);
                $widgetsRight = getWidgets('right', $page_id);
                
                if(!empty($widgetsLeft)){ ?>
                    <div class="col-sm-3">
                        <?php displayWidgets('left', $page_id); ?>
                    </div>
                    <?php
                } ?>
                
                <div class="col-sm-<?php if(!empty($widgetsLeft) && !empty($widgetsRight)) echo 5; elseif(!empty($widgetsLeft) || !empty($widgetsRight)) echo 8; else echo 12; ?>">
                    <?php
                    $lz_offset = 1;
                    $lz_limit = 10;
                    $lz_pages = 0;
                    $num_records = 0;
                    $result = $db->query('SELECT count(*)
										FROM pm_article
										WHERE id_page = '.$page_id.'
											AND checked = 1
											AND (publish_date IS NULL || publish_date <= '.time().')
											AND (unpublish_date IS NULL || unpublish_date > '.time().')
											AND lang = '.LANG_ID.'
											AND (show_langs IS NULL || show_langs = \'\' || show_langs REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
											AND (hide_langs IS NULL || hide_langs = \'\' || hide_langs NOT REGEXP \'(^|,)'.LANG_ID.'(,|$)\')');
                    if($result !== false){
                        $num_records = $result->fetchColumn(0);
                        $lz_pages = ceil($num_records/$lz_limit);
                    }
                    if($num_records > 0){
                        
                        if(isset($_GET['month']) && is_numeric($_GET['month']) && isset($_GET['year']) && is_numeric($_GET['year'])){
                            $start_month = mktime(0, 0, 0, $_GET['month'], 1, $_GET['year']);
                            $nb_days = date('t', $start_month);
                            $end_month = mktime(0, 0, 0, $_GET['month'], $nb_days, $_GET['year']);
                        }else{
                            $start_month = null;
                            $end_month = null;
                        }
                        $tag = (isset($_GET['tag']) && is_numeric($_GET['tag'])) ? $_GET['tag'] : 0; ?>
                        
                        <div itemscope itemtype="http://schema.org/Blog" id="blog-content" class="clearfix lazy-wrapper" data-loader="<?php echo getFromTemplate('common/get_articles_blog.php'); ?>" data-mode="click" data-limit="<?php echo $lz_limit; ?>" data-pages="<?php echo $lz_pages; ?>" data-more_caption="<?php echo $texts['LOAD_MORE']; ?>" data-is_isotope="false" data-variables="page_id=<?php echo $page_id; ?>&page_alias=<?php echo $page['alias']; ?>&start_month=<?php echo $start_month; ?>&end_month=<?php echo $end_month; ?>&tag=<?php echo $tag; ?>">
                            <?php include(getFromTemplate('common/get_articles_blog.php', false)); ?>
                        </div>
                        <?php
                    } ?>
                </div>
                
                <?php
                if(!empty($widgetsRight)){ ?>
                    <div class="col-sm-4">
                        <?php displayWidgets('right', $page_id); ?>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</section>
