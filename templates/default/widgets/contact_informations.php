<?php debug_backtrace() || die ('Direct access not permitted'); ?>
<div itemscope itemtype="http://schema.org/Corporation">
    <h3 itemprop="name"><?php echo OWNER; ?></h3>
    <address>
        <p>
            <?php if(ADDRESS != '') : ?><span class="fas fa-fw fa-map-marker"></span> <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><?php echo nl2br(ADDRESS); ?></span><br><?php endif; ?>
            <?php if(PHONE != '') : ?><span class="fas fa-fw fa-phone"></span> <a href="tel:<?php echo PHONE; ?>" itemprop="telephone" dir="ltr"><?php echo PHONE; ?></a><br><?php endif; ?>
            <?php if(MOBILE != '') : ?><span class="fas fa-fw fa-mobile"></span> <a href="tel:<?php echo MOBILE; ?>" itemprop="telephone" dir="ltr"><?php echo MOBILE; ?></a><br><?php endif; ?>
            <?php if(FAX != '') : ?><span class="fas fa-fw fa-fax"></span> <span itemprop="faxNumber" dir="ltr"><?php echo FAX; ?></span><br><?php endif; ?>
            <?php if(EMAIL != '') : ?><span class="fas fa-fw fa-envelope"></span> <a itemprop="email" dir="ltr" href="mailto:<?php echo EMAIL; ?>"><?php echo EMAIL; ?></a><?php endif; ?>
        </p>
    </address>
</div>
<p class="lead">
    <?php
    $result_social = $db->query('SELECT * FROM pm_social WHERE checked = 1 ORDER BY rank');
    if($result_social !== false){
        foreach($result_social as $row){ ?>
            <a href="<?php echo $row['url']; ?>" target="_blank">
                <i class="fab fa-fw fa-<?php echo $row['type']; ?>"></i>
            </a>
            <?php
        }
    } ?>
</p>
