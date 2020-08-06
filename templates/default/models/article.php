<?php
if($article_alias == '') err404();

if($article_id > 0){
    
    $title_tag = $article['title'].' - '.$title_tag;
    $page_title = $article['title'];
    $page_subtitle = $article['subtitle'];
    $page_alias = $article['alias'];
    $publish_date = $article['publish_date'];
    $edit_date = $article['edit_date'];
    
    if(is_null($publish_date)) $publish_date = $article['add_date'];
    if(is_null($edit_date)) $edit_date = $publish_date;
    
    $result_article_file = $db->query('SELECT * FROM pm_article_file WHERE id_item = '.$article_id.' AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
    if($result_article_file !== false && $db->last_row_count() > 0){
        
        $row = $result_article_file->fetch();
        
        $file_id = $row['id'];
        $filename = $row['file'];
        
        if(is_file(SYSBASE.'medias/article/medium/'.$file_id.'/'.$filename))
            $page_img = getUrl(true).DOCBASE.'medias/article/medium/'.$file_id.'/'.$filename;
    }
    
    $result_tag = $db->query('SELECT * FROM pm_tag WHERE id IN ('.$article['tags'].') AND checked = 1 AND lang = '.LANG_ID.' ORDER BY rank');
    if($result_tag !== false){
        $nb_tags = $db->last_row_count();
        
        $article_tags = '';
        foreach($result_tag as $i => $row){
            $tag_id = $row['id'];
            $tag_value = $row['value'];

            $article_tags .= $tag_value;
            if($i+1 < $nb_tags) $article_tags .= ', ';
        }
    }
    
}else err404();

check_URI(DOCBASE.$page_alias);

/* ==============================================
 * CSS AND JAVASCRIPT USED IN THIS MODEL
 * ==============================================
 */
$javascripts[] = DOCBASE.'js/plugins/sharrre/jquery.sharrre.min.js';

$stylesheets[] = array('file' => '//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/assets/owl.carousel.min.css', 'media' => 'all');
$stylesheets[] = array('file' => '//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/assets/owl.theme.default.min.css', 'media' => 'all');
$javascripts[] = '//cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.2.4/owl.carousel.min.js';

$stylesheets[] = array('file' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/3.5.5/css/star-rating.min.css', 'media' => 'all');
$javascripts[] = '//cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/3.5.5/js/star-rating.min.js';

require(getFromTemplate('common/send_comment.php', false));

require(getFromTemplate('common/header.php', false)); ?>

<article id="page">
    <?php include(getFromTemplate('common/page_header.php', false)); ?>
    
    <div id="content" class="pt30 pb30">
        <div class="container">

            <div class="alert alert-success" style="display:none;"></div>
            <div class="alert alert-danger" style="display:none;"></div>
            
            <div class="row">
                <div class="col-sm-6 mb10">
                    <?php
                    $nb_comments = 0;
                    $item_type = 'article';
                    $item_id = $article_id;
                    $allow_comment = $article['comment'];
                    $allow_rating = $article['rating'];
                    if($allow_comment == 1){
                        $result_comment = $db->query('SELECT * FROM pm_comment WHERE id_item = '.$item_id.' AND item_type = \''.$item_type.'\' AND checked = 1 ORDER BY add_date DESC');
                        if($result_comment !== false)
                            $nb_comments = $db->last_row_count();
                    } ?>
                    <div class="mb10 labels" dir="ltr">
                        <span class="label label-default"><i class="fas fa-fw fa-thumbtack"></i> <?php echo (!RTL_DIR) ? strftime(DATE_FORMAT, $article['add_date']) : strftime('%F', $article['add_date']); ?></span>
                        <span class="label label-default"><i class="fas fa-fw fa-comment"></i> <?php echo $nb_comments.' '.mb_strtolower($texts['COMMENTS'], 'UTF-8'); ?></span>
                        <?php
                        $result_tag = $db->query('SELECT * FROM pm_tag WHERE id IN ('.$article['tags'].') AND checked = 1 AND lang = '.LANG_ID.' ORDER BY rank');
                        if($result_tag !== false){
                            $nb_tags = $db->last_row_count();
                            
                            if($nb_tags > 0){ ?>
                                <span class="label label-default"><i class="fas fa-fw fa-tags"></i>
                                    <?php
                                    foreach($result_tag as $i => $row){
                                        $tag_id = $row['id'];
                                        $tag_value = $row['value'];

                                        echo $tag_value;
                                        if($i+1 < $nb_tags) echo ', ';
                                    } ?>
                                </span>
                                <?php
                            }
                        } ?>
                    </div>
                    <?php
                    echo $article['text'];
                    
                    $short_text = strtrunc(strip_tags($article['text']), 100);
                    $site_url = getUrl(); ?>
                   
                    <div id="twitter" data-url="<?php echo $site_url; ?>" data-text="<?php echo $short_text; ?>" data-title="Tweet"></div>
                    <div id="facebook" data-url="<?php echo $site_url; ?>" data-text="<?php echo $short_text; ?>" data-title="Like"></div>
                    <div id="pinterest" data-media="<?php if(isset($page_img)) echo $page_img; ?>" data-text="<?php echo $short_text; ?>"></div>
                
                    <div class="clearfix"></div>
                </div>
                <div class="col-sm-6">
                    <div class="owl-carousel owlWrapper owl-theme" data-items="1" data-autoplay="true" data-dots="true" data-nav="false" data-rtl="<?php echo (RTL_DIR) ? "true" : "false"; ?>">
                        <?php
                        $result_article_file = $db->query('SELECT * FROM pm_article_file WHERE id_item = '.$article_id.' AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank');
                        if($result_article_file !== false){
                            
                            foreach($result_article_file as $i => $row){
                            
                                $file_id = $row['id'];
                                $filename = $row['file'];
                                $label = $row['label'];
                                
                                $realpath = SYSBASE.'medias/article/big/'.$file_id.'/'.$filename;
                                $thumbpath = DOCBASE.'medias/article/big/'.$file_id.'/'.$filename;
                                
                                if(is_file($realpath)){ ?>
                                    <img alt="<?php echo $label; ?>" src="<?php echo $thumbpath; ?>" class="img-responsive" style="max-height:600px;"/>
                                    <?php
                                }
                            }
                        } ?>
                    </div>
                </div>
            </div>
			<?php
			$result_article = $db->query('SELECT *
										FROM pm_article
										WHERE id_page = '.$page_id.'
											AND id != '.$article_id.'
											AND checked = 1
											AND (publish_date IS NULL || publish_date <= '.time().')
											AND (unpublish_date IS NULL || unpublish_date > '.time().')
											AND lang = '.LANG_ID.'
											AND (show_langs IS NULL || show_langs = \'\' || show_langs REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
											AND (hide_langs IS NULL || hide_langs = \'\' || hide_langs NOT REGEXP \'(^|,)'.LANG_ID.'(,|$)\')
										ORDER BY rand()
										LIMIT 3');
			if($result_article !== false){
				$nb_articles = $db->last_row_count();
				
				if($nb_articles > 0){ ?>
					
					<div class="row mt30">
						<div class="col-md-12">
							<h2><?php echo $texts['DISCOVER_ALSO']; ?></h2>
						</div>
					
						<div class="clearfix">
							<?php
							$article_id = 0;
							$result_article_file = $db->prepare('SELECT * FROM pm_article_file WHERE id_item = :article_id AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
							$result_article_file->bindParam(':article_id',$article_id);
							foreach($result_article as $i => $row){
								$article_id = $row['id'];
								$article_title = $row['title'];
								$article_alias = $row['alias'];
								$article_text = strtrunc(strip_tags($row['text']),190);
								$article_page = $row['id_page'];
								
								if(isset($pages[$article_page])){
								
									$article_alias = DOCBASE.$pages[$article_page]['alias'].'/'.text_format($article_alias); ?>
									
									<article class="article-<?php echo $article_id; ?> col-sm-4 articleItem" itemscope itemtype="http://schema.org/Article">
										<div class="isotopeInner matchHeight">
											<a itemprop="url" href="<?php echo $article_alias; ?>" class="moreLink">
												<?php
												if($result_article_file->execute() !== false && $db->last_row_count() == 1){
													$row = $result_article_file->fetch(PDO::FETCH_ASSOC);
													
													$file_id = $row['id'];
													$filename = $row['file'];
													$label = $row['label'];
													
													$realpath = SYSBASE.'medias/article/medium/'.$file_id.'/'.$filename;
													$thumbpath = DOCBASE.'medias/article/medium/'.$file_id.'/'.$filename;
													$zoompath = DOCBASE.'medias/article/big/'.$file_id.'/'.$filename;
													
													if(is_file($realpath)){ ?>
														<figure class="more-link img-container md">
															<img alt="<?php echo $label; ?>" src="<?php echo $thumbpath; ?>">
															<span class="more-action">
															</span>
														</figure>
														<?php
													}
												} ?>
												<div class="isotopeContent">
													<h3 itemprop="name"><?php echo $article_title; ?></h3>
													<p>
														<?php echo $article_text; ?>
													</p>
												</div>
											</a>
										</div>
									</article>
									<?php
								}
							} ?>
						</div>
					</div>
					<?php
				}
			} ?>
            
            <?php include(getFromTemplate('common/comments.php', false)); ?>
        </div>
    </div>
</article>
