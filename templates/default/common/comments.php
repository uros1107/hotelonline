<?php
debug_backtrace() || die ("Direct access not permitted");

if($allow_comment == 1 && $result_comment !== false && $item_id > 0 && isset($item_type)){ ?>
    
    <!-- Comments -->
    <h3 class="mb10"><?php echo $texts['LET_US_KNOW']; ?></h3>

    <div class="alert alert-success" style="display:none;"></div>
    <div class="alert alert-danger" style="display:none;"></div>
    
    <div class="row">
        <form method="post" action="<?php echo DOCBASE.$page_alias; ?>">
        
            <input type="hidden" name="item_type" value="<?php echo $item_type; ?>">
            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fas fa-fw fa-quote-left"></i></div>
                        <textarea class="form-control" name="msg" placeholder="<?php echo $texts['COMMENT']; ?> *" rows="9"><?php echo htmlentities($msg, ENT_QUOTES, "UTF-8"); ?></textarea>
                    </div>
                    <div class="field-notice" rel="msg"></div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fas fa-fw fa-user"></i></div>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlentities($name, ENT_QUOTES, "UTF-8"); ?>" placeholder="<?php echo $texts['LASTNAME']." ".$texts['FIRSTNAME']; ?> *">
                    </div>
                    <div class="field-notice" rel="name"></div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fas fa-fw fa-envelope"></i></div>
                        <input type="text" class="form-control" name="email" value="<?php echo htmlentities($email, ENT_QUOTES, "UTF-8"); ?>" placeholder="<?php echo $texts['EMAIL']; ?> *">
                    </div>
                    <div class="field-notice" rel="email"></div>
                </div>
                <?php
                if(CAPTCHA_PKEY != '' && CAPTCHA_SKEY != ''){ ?>
                    <div class="form-group">
                        <div class="input-group mb5"></div>
                        <div class="g-recaptcha" data-sitekey="<?php echo CAPTCHA_PKEY; ?>"></div>
                    </div>
                    <?php
                }
                if($allow_rating == 1){ ?>
                    <div class="form-group form-inline">
                        <label for="rating">Rating</label>
                        <div class="input-group mb5">
                            <input type="hidden" name="rating" class="rating" value="<?php echo $rating; ?>" data-rtl="<?php echo (RTL_DIR) ? true : false; ?>" min="1" max="5" data-step="1" data-size="xs" data-show-clear="false" data-show-caption="false">
                        </div>
                    </div>
                    <?php
                } ?>
                <div class="form-group row">
                    <span class="col-sm-12"><button type="submit" class="btn btn-primary" name="send_comment"><i class="fas fa-fw fa-paper-plane"></i> <?php echo $texts['SEND']; ?></button> <i> * <?php echo $texts['REQUIRED_FIELD']; ?></i></span>
                </div>
            </div>
        </form>
    </div>
    <?php
    if($nb_comments > 0){ ?>
        <section class="clearfix">
            <h3 class="commentNumbers">
                <?php
                echo $texts['COMMENTS']." ";
                if(RTL_DIR) echo "&rlm;";
                echo "(".$nb_comments.")"; ?>
            </h3>
            <?php
            foreach($result_comment as $i => $row){ ?>
                <div class="media row">
                    <div class="col-sm-1 col-xs-2">
                        <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&amp;s=50" alt="" class="img-responsive">
                    </div>
                    <div class="media-body col-sm-8 col-xs-7">
                        <div class="clearfix">
                            <h4 class="media-heading"><?php echo $row['name']; ?></h4>
                            <div class="commentInfo"> <span><?php echo (!RTL_DIR) ? strftime(DATE_FORMAT, $row['add_date']) : strftime("%F", $row['add_date']); ?></span></div>
                            <?php echo nl2br($row['msg']); ?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <?php
                        if($allow_rating == 1 && $row['rating'] > 0 && $row['rating'] <= 5){ ?>
                            <input type="hidden" class="rating" value="<?php echo $row['rating']; ?>" data-rtl="<?php echo (RTL_DIR) ? true : false; ?>" data-size="xs" readonly="true" data-show-clear="false" data-show-caption="false">
                            <?php
                        } ?>
                    </div>
                </div>
                <?php
            } ?>
        </section>
        <?php
    }
} ?>
