<?php
$javascripts[] = DOCBASE.'js/plugins/jquery-activmap/js/jquery-activmap.js';
$javascripts[] = DOCBASE.'js/plugins/jquery-activmap/js/markercluster.min.js';
$stylesheets[] = array('file' => DOCBASE.'js/plugins/jquery-activmap/css/skin-compact/activmap-compact.css', 'media' => 'all');
$stylesheets[] = array('file' => DOCBASE.'js/plugins/jquery-activmap/css/skin-compact/activmap-dark-blue.css', 'media' => 'all');

require(getFromTemplate('common/header.php', false)); ?>

<script>
	var locations = [
		<?php
		$result_hotels = $db->query('SELECT * FROM pm_hotel WHERE checked = 1 AND lang = '.LANG_ID.' AND lat != \'\' AND lng != \'\'');
		if($result_hotels !== false){
			$nb_hotels = $db->last_row_count();
			
			$hotel_id = 0;
			
			$result_hotel_file = $db->prepare('SELECT * FROM pm_hotel_file WHERE id_item = :hotel_id AND checked = 1 AND lang = '.DEFAULT_LANG.' AND type = \'image\' AND file != \'\' ORDER BY rank LIMIT 1');
			$result_hotel_file->bindParam(':hotel_id',$hotel_id);
			
			$result_rate = $db->prepare('SELECT MIN(price) as min_price FROM pm_rate WHERE id_hotel = :hotel_id');
			$result_rate->bindParam(':hotel_id', $hotel_id);
					
			foreach($result_hotels as $i => $row){
				$hotel_id = $row['id'];
				$hotel_title = $row['title'];
				$hotel_subtitle = $row['subtitle'];
				$hotel_phone = $row['phone'];
				$hotel_email = $row['email'];
				$hotel_web = $row['web'];
				$hotel_address = $row['address'];
				$hotel_lat = $row['lat'];
				$hotel_lng = $row['lng'];
				$hotel_tags = $row['tags'];
				$hotel_alias = DOCBASE.$pages[9]['alias'].'/'.text_format($row['alias']);
				
                if($hotel_tags != '') $hotel_tags = "'tag_".str_replace(",","','tag_", $hotel_tags)."'";
				
				$min_price = 0;
				if($result_rate->execute() !== false && $db->last_row_count() > 0){
					$row = $result_rate->fetch();
					$price = $row['min_price'];
					if($price > 0) $min_price = $price;
				}
				$custom = '';
				if($min_price > 0)
					$custom = '<b>'.$texts['FROM_PRICE'].' <a href="'.$hotel_alias.'"><big>'.formatPrice($min_price*CURRENCY_RATE).'</big></a> / '.$texts['NIGHT'].'</b>';
				
				$hotel_img = '';
				if($result_hotel_file->execute() !== false && $db->last_row_count() > 0){
					$row = $result_hotel_file->fetch();
					$hotel_img = DOCBASE.'medias/hotel/small/'.$row['id'].'/'.$row['file'];
				}
						
				echo "{title: '<a href=\"".$hotel_alias."\">".addslashes($hotel_title)."</a>', address: '".addslashes($hotel_address)."', phone: '".addslashes($hotel_phone)."', url: '".$hotel_web."', custom: '".addslashes($custom)."', tags: [".$hotel_tags."], lat: ".$hotel_lat.", lng: ".$hotel_lng.", img: '".$hotel_img."', icon: '".DOCBASE."js/plugins/jquery-activmap/img/marker-hotel-blue.png'}";
				if($i+1 < $nb_hotels) echo ",\n";
			}
		} ?>
	];
</script>

<section id="page">
    
    <?php include(getFromTemplate('common/page_header.php', false)); ?>
    
	<div id="activmap-wrapper" data-lat="51.507333" data-lng="-0.127733" data-zoom="3" data-icon="<?php echo DOCBASE; ?>js/plugins/jquery-activmap/img/marker.png" data-center_icon="<?php echo DOCBASE; ?>js/plugins/jquery-activmap/img/marker-center.png">
    
		<!-- Places panel (auto removable) -->
		<div id="activmap-places">
			<div id="activmap-results-num"></div>
		</div>
		
		<!-- Activ'Map global wrapper -->
		<div id="activmap-container">
			<!-- Toolbar -->
			<div id="activmap-ui-wrapper">
				<div id="activmap-search">
					
					<!-- Optional: possibility for the user to change the center location -->
					<!-- ** Remove this part if not needed ** -->
					<input id="activmap-location" type="text" name="location" value="" placeholder="<?php echo $texts['LOCATION']; ?>...">
					
					<!-- Optional: possibility for the user to be geolocated -->
					<!-- ** Remove this part if not needed ** -->
					<a class="activmap-action" id="activmap-geolocate" href="#"><i class="fa fa-crosshairs"></i></a>
					
					<!-- Optional: possibility for the user to reset all the map (location, radius, filters) -->
					<!-- ** Remove this part if not needed ** -->
					<a class="activmap-action" id="activmap-reset" href="#"><i class="fa fa-ban"></i></a>
					
					<!-- Optional: possibility for the user to target the results on the map -->
					<!-- ** Remove this part if not needed ** -->
					<a class="activmap-action" id="activmap-target" href="#"><i class="fa fa-bullseye"></i></a>
					
					<div>
						<!-- Optional: possibility for the user to change the radius -->
						<!-- ** Remove this part if not needed ** -->
						<!--Radius:
						<small>
							<input type="radio" name="activmap_radius" value="0"> None
							<input type="radio" name="activmap_radius" value="3"> 3km
							<input type="radio" name="activmap_radius" value="20"> 20km
							<input type="radio" name="activmap_radius" value="50"> 50km
							<input type="radio" name="activmap_radius" value="100"> 100km
						</small>-->
					
					</div>
				</div>
			
				<!-- Activ'Map categories and tags -->
				<div id="activmap-filters">
					<?php
                    $result_tag = $db->query("SELECT * FROM pm_tag WHERE pages REGEXP '(^|,)".$page_id."(,|$)' AND checked = 1 AND lang = ".LANG_ID." ORDER BY rank");
                    if($result_tag !== false){
                        $nb_tags = $db->last_row_count();
                        
                        if($nb_tags > 0){
                            foreach($result_tag as $i => $row){
                                $tag_id = $row['id'];
                                $tag_value = $row['value'];
                                $tag_icon = $row['icon']; ?>
                                
								<div class="marker-selector">
									<!-- Add checked="checked" to show the markers of this filter on page loading -->
									<input type="checkbox" name="marker_type[]" value="tag_<?php echo $tag_id; ?>" id="tag_<?php echo $tag_id; ?>">
									<label for="tag_<?php echo $tag_id; ?>"><i class="fas fa-<?php echo $tag_icon; ?>"></i><?php echo $tag_value; ?></label>
								</div>
                                <?php
                            }
                        }
                    } ?>
				</div>
			</div>

			<!-- Map container REQUIRED -->
			<div id="activmap-canvas"></div>
		</div>
	</div>
</section>

