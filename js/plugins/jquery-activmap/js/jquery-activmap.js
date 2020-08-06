/* Activ'Map Plugin 2.0.0
 * Copyright (c) 2015-2020 Pandao
 * Documentation : https://activmap.pandao.eu/doc
 */
(function($){
    
    $.activmap = {
        // Default values (if not modified in the plugin init)
        // DON'T CHANGE THE VALUES HERE, overwrite the options in your HTML file
        defaults: {
            places: [],            //list of places objects {title, address, phone, tags, lat, lng, img},
            lat: 51.507351,        //latitude of the center
            lng: -0.127758,        //longitude of the center
            zoom: 10,              //default zoom level between 0 and 21
            cluster: true,         //enables / disables clustering for large amounts of markers
            mapType: 'roadmap',    //map type : "roadmap", "satellite", "perspective"
            posPanel: 'left',      //position of the removable panel : "left" or "right"
            showPanel: true,       //shows / hides the removable panel
            radius: 0,             //max radius in kilometers
            unit: 'km',            //distance unit (km or mile)
            country: null,         //country limit for location input (ex. "ca": Canada, "us": United States, "fr": France...)
            autogeolocate: false,  //auto geolocation to set the center of the map
            allowMultiSelect: true,//allow user to select multiple filters
            icon: 'images/icons/marker.png',
            center_icon: 'images/icons/marker-center.png',
            styles: [{"featureType":"administrative.country","elementType":"geometry","stylers":[{"visibility":"simplified"},{"hue":"#ff0000"}]}],
            request: 'large',      //type of request "large" (show results of each filter) or "strict" (show results of all filters)
            locationTypes: ['geocode','establishment'], //types of locations returned by autocomplete requests
            show_center: true //show or hide the center of the map marker
        }
    };

    $.arrayIntersect = function(a, b)
    {
        return $.grep(a, function(i)
        {
            return $.inArray(i, b) > -1;
        });
    };
    
    $.fn.extend({
        
        activmap : function(settings){
            
            var s = $.extend({}, $.activmap.defaults, settings);

            //Map init
            var init_latlng = new google.maps.LatLng(s.lat,s.lng)
            var latlng = init_latlng;
            var opendInfoWindow = null;
            var markers = [];
            var infoWindow = [];
            var ids = [];
            var markerCluster;
            var centerMarker;
            var bounds = new google.maps.LatLngBounds();
            var num_places = 0;
            var old_results = 0;
            var mapTypeId = google.maps.MapTypeId.ROADMAP;
            if(s.mapType == 'satellite' || s.mapType == 'perspective') mapTypeId = google.maps.MapTypeId.HYBRID;
            var map = new google.maps.Map(document.getElementById('activmap-canvas'), {
                zoom: s.zoom,
                center: latlng,
                mapTypeId: mapTypeId,
                styles: s.styles,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.BOTTOM_CENTER
                },
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_CENTER
                },
                scaleControl: true,
                streetViewControl: true,
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                },
                fullscreenControl: false
            });
            bounds.extend(init_latlng); 
            var init_latlngBounds = map.getBounds();
            if(s.mapType == 'perspective') map.setTilt(45);
            
            //Containers
            var activmap_canvas = $('#activmap-container');
            var activmap_places = $('#activmap-places');
            var map_w = activmap_canvas.width();
            var cont_w = activmap_places.outerWidth();
            
            //Panel
            if(!s.showPanel) activmap_places.hide();
            else{
                if(s.posPanel == 'left'){
                    activmap_places.css({
                        left: -cont_w,
                        right: 'auto'
                    });
                    activmap_canvas.css({
                        float: 'right'
                    });
                }
                if(s.posPanel == 'right'){
                    activmap_places.css({
                        right: -cont_w,
                        left: 'auto'
                    });
                    activmap_canvas.css({
                        float: 'left'
                    });
                }
            }
            
            //if($('input[name="marker_type[]"]').length) $('input[name="marker_type[]"]').prop('checked',false);
            
            if($('input[name="activmap_radius"]').length){
                $('input[name="activmap_radius"]').prop('checked', false).each(function(){
                    if(s.radius == $(this).val()) $(this).prop('checked', true);
                });
            }

            _init = function(){

                //Places init
                $.each(s.places, function(i, place){
                    place.num_tags = 0;
                    place.id = i;
                    //Marker init
                    var myLatlng = new google.maps.LatLng(place.lat, place.lng);
                    var myIcon = (place.icon != '' && place.icon != undefined) ? place.icon : s.icon;
                    var marker = new google.maps.Marker({
                        map: map,
                        position: myLatlng,
                        icon: myIcon,
                        title: place.title
                    });
                    //Info window content building
                    var mycontent = '<div class="activmap-infowindow">';
                    if(place.img != undefined && place.img != '') mycontent += '<div class="activmap-brand"><img src="'+place.img+'"></div>';
                    mycontent += '<div class="activmap-details"><h4 class="activmap-title">'+place.title+'</h4>';
                    if(place.address != undefined && place.address != '') mycontent += '<div class="activmap-address">'+place.address+'</div>';
                    if(place.phone != undefined && place.phone != '') mycontent += '<div class="activmap-phone">'+place.phone+'</div>';
                    if(place.url != undefined && place.url != '') mycontent += '<a href="'+place.url+'" target="_blank" class="activmap-url">'+place.url+'</a>';
                    if(place.custom != undefined && place.custom != '') mycontent += '<div class="activmap-custom">'+place.custom+'</div>';
                    mycontent += '</div><div style="clear: both;"></div></div>';

                    //Info window init
                    infoWindow[i] = new google.maps.InfoWindow({
                        content  : mycontent,
                        position : myLatlng
                    });
                    
                    //infoWindow close event
                    google.maps.event.addListener(infoWindow[i], 'closeclick', function(){
                        $('.activmap-place').removeClass('active');
                        activmap_places.scrollTop();
                    });
                    
                    //Marker click event
                    google.maps.event.addListener(marker, 'click', function(){
                        if(opendInfoWindow != null) opendInfoWindow.close();
                        infoWindow[i].open(map, marker);
                        
                        map.setCenter(marker.getPosition());
                        var padbnds = _paddedBounds(20, 280, 50, 50);
                        map.fitBounds(padbnds);

                        opendInfoWindow = infoWindow[i];
                        
                        $('.activmap-place').removeClass('active');
                        $('#activmap-place_'+i).addClass('active');
                        activmap_places.scrollTop(activmap_places.scrollTop()+$('#activmap-place_'+i).position().top);
                    });
                    
                    marker.setVisible(false);
                    place.marker = marker;
                    markers.push(marker);
                    
                    place.title = place.title.replace(/<a\b[^>]*>(.*?)<\/a>/i,'$1');
                    place.html = '<div class="activmap-place" id="activmap-place_'+i+'"><h3>'+place.title+'</h3><p>'+place.address+'</p></div>';
                    
                    //Places indexed by tag name (checkbox name)
                    $.each(place.tags, function(j, tag){
                        if(ids[tag] === undefined) ids[tag] = [];
                        ids[tag].push(place.id);
                    }); 
                });
                
                
                //Cluster init
                if(s.cluster){
                    markerCluster = new MarkerClusterer(map, markers, {
                        maxZoom: 12,
                        gridSize: 40
                    });
                }
                
                //Center marker init
                if(s.show_center){
					centerMarker = new google.maps.Marker({
						map: map,
						position: latlng,
						icon: (s.center_icon != '' && s.center_icon != undefined) ? s.center_icon : s.icon
					});
					centerMarker.setVisible(true);
					markers.push(centerMarker);
				}
                
                //Order places in the panel
                _order();

                //Auto geolocation
                if(s.autogeolocate == true){
                    _geolocate();
                }
                
                //Geolocate event
                if($('#activmap-geolocate').length){
                    $('#activmap-geolocate').on('click', function(){
                        _geolocate();
                    });
                }
                
                //Target results event
                if($('#activmap-target').length){
                    $('#activmap-target').on('click', function(){
                        latlng = init_latlng;
                        _update_places_bounds();
                        _update_map();
                    });
                }
                
                //Location field change event
                if($('#activmap-location').length){
                    if(s.country !== null){
                        var options = {
                           types: s.locationTypes,
                           componentRestrictions: {country: s.country}
                        };
                    }else{
                        var options = {
                           types: s.locationTypes
                        };
                    }
                    var input = document.getElementById('activmap-location');
                    autocomplete = new google.maps.places.Autocomplete(input, options);
                
                    google.maps.event.addListener(autocomplete, 'place_changed', function(){
                        var place = autocomplete.getPlace();
                        latlng = place.geometry.location;
                        //map.setCenter(place.geometry.location);
                        if($('.activmap-place').length) _order();
                        $('input[name="marker_type[]"], select[name="marker_type[]"]').each(function(){
                            _update_places_tag($(this), false);
                        });
                        _update_center_marker();
                        //bounds = map.getBounds();
                        _update_map();
                    });
                }
                
                //Radius change event
                $('input[name="activmap_radius"]').on('change', function(){
                    s.radius = $(this).val();
                    $('input[name="marker_type[]"], select[name="marker_type[]"]').each(function(){
                        _update_places_tag($(this), false);
                    });
                    _update_map();
                });

                //Filter change event
                $('input[name="marker_type[]"], select[name="marker_type[]"]').on('change', function(){
                    _update_places_tag($(this), true);
                    _update_map();
                });

                //Reset click event
                $('#activmap-reset').on('click', function(){
                    $('input[name="marker_type[]"]').prop('checked',false);
                    $('select[name="marker_type[]"] > option').prop('selected', false);
                    $('input[name="marker_type[]"], select[name="marker_type[]"]').each(function(){
                        _update_places_tag($(this), false);
                    });
                    latlng = init_latlng;
                    _update_center_marker();
                    _update_map();
                    return false;
                });
                
                //Place (panel with results) click event
                $(document).on('click', '.activmap-place', function(){
                    var id = $(this).attr('id').replace('activmap-place_','');
                    google.maps.event.trigger(markers[id], 'click');
                });
                
                //Window resize event
                $(window).on('resize', function(){
                    setTimeout('_update_map()', 300);
                });
                
                //For each selected tags on page loading
                $('input[name="marker_type[]"]:checked').add($('select[name="marker_type[]"]').prop('selected', true)).each(function(){
                    _update_places_tag($(this), false);
                });
                
                _update_map();
            }
            
            /* _sort_by_dist() compares 2 distances
             * 
             * @param a coordinates of point 1
             * @param b coordinates of point 2
             */
            _sort_by_dist = function(a, b){
                return ((a.dist < b.dist) ? -1 : ((a.dist > b.dist) ? 1 : 0));
            }
            
            /* _order() sorts the places in the panel
             * 
             */
            _order = function(){
                $.each(s.places, function(i, place){
                    place.dist = _get_distance(place.marker.position, latlng);
                });
                s.places.sort(_sort_by_dist);
                $('.activmap-place').remove();
                $.each(s.places, function(i, place){
                    activmap_places.append(place.html);
                    if(place.isVisible) $('#activmap-place_'+place.id).show();
                });
            }
            
            /* _update_center_marker() modifies the position of the center marker
             * 
             */
            _update_center_marker = function(){
                centerMarker.setPosition(latlng);
                _update_places_bounds();
            }
            
            /* _geolocate() geolocates the user
             * 
             */
            _geolocate = function(){
                if(navigator.geolocation){
                    browserSupportFlag = true;
                    navigator.geolocation.getCurrentPosition(function(position){
                        initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                        latlng = initialLocation;
                        //map.setCenter(latlng);
                        if($('.activmap-place').length) _order();
                        $('input[name="marker_type[]"], select[name="marker_type[]"]').each(function(){
                            _update_places_tag($(this), false);
                        });
                        _update_center_marker();
                        //bounds = map.getBounds();
                        _update_map();
                    }, function(){
                        handleNoGeolocation(browserSupportFlag);
                    });
                }else{
                    browserSupportFlag = false;
                    handleNoGeolocation(browserSupportFlag);
                }
            }
            
            /* _handleNoGeolocation() handles the geolocation errors
             * 
             */
            _handleNoGeolocation = function(errorFlag){
                if(errorFlag == true){
                    console.log('Geolocation service failed.');
                    initialLocation = latlng;
                }else{
                    console.log('Your browser doesn\'t support geolocation.');
                    initialLocation = latlng;
                }
                map.setCenter(initialLocation);
            }
            
            /* _rad() gets the radius distance
             * 
             * @param a distance
             * 
             */
            _rad = function(a){
                return a * Math.PI / 180;
            }
            
            /* _paddedBounds() adds offsets all around the map
             * 
             * @param npad north padding in pixels
             * @param spad south padding in pixels
             * @param epad east padding in pixels
             * @param wpad west padding in pixels
             * 
             */
            _paddedBounds = function(npad, spad, epad, wpad) {
                var SW = map.getBounds().getSouthWest();
                var NE = map.getBounds().getNorthEast();
                var topRight = map.getProjection().fromLatLngToPoint(NE);
                var bottomLeft = map.getProjection().fromLatLngToPoint(SW);
                var scale = Math.pow(2, map.getZoom());

                var SWtopoint = map.getProjection().fromLatLngToPoint(SW);
                var SWpoint = new google.maps.Point(((SWtopoint.x - bottomLeft.x) * scale) + wpad, ((SWtopoint.y - topRight.y) * scale) - spad);
                var SWworld = new google.maps.Point(SWpoint.x / scale + bottomLeft.x, SWpoint.y / scale + topRight.y);
                var pt1 = map.getProjection().fromPointToLatLng(SWworld);

                var NEtopoint = map.getProjection().fromLatLngToPoint(NE);
                var NEpoint = new google.maps.Point(((NEtopoint.x - bottomLeft.x) * scale) - epad, ((NEtopoint.y - topRight.y) * scale) + npad);
                var NEworld = new google.maps.Point(NEpoint.x / scale + bottomLeft.x, NEpoint.y / scale + topRight.y);
                var pt2 = map.getProjection().fromPointToLatLng(NEworld);

                return new google.maps.LatLngBounds(pt1, pt2);
            }

            /* _getDistance() returns the distance between 2 points in meters
             * 
             * @param p1 coordinates of point 1
             * @param p2 coordinates of point 2
             * 
             */
            _get_distance = function(p1, p2){
                var R = (s.unit == 'km') ? 6378137 : 3963190;
                var dLat = _rad(p2.lat() - p1.lat());
                var dLong = _rad(p2.lng() - p1.lng());
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(_rad(p1.lat())) * Math.cos(_rad(p2.lat())) *
                    Math.sin(dLong / 2) * Math.sin(dLong / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                var d = R * c;
                return d;
            }
            
            /* _update_map() hides or shows the places panel. Shrinks, enlarges and refresh the map
             * 
             */
            _update_map = function(){
                map_w = $('#activmap-wrapper').width();
                cont_w = activmap_places.outerWidth();
                if(num_places > 0){
                    //results found
                    if(old_results == 0){
                        //no previous result
                        //side panel displaying for 1st time => shows places
                        if(s.showPanel){
                            if(s.posPanel == 'left'){
                                activmap_places.stop(true,true).animate({
                                    left: 0
                                },400);
                            }
                            if(s.posPanel == 'right'){
                                activmap_places.stop(true,true).animate({
                                    right: 0
                                },400);
                            }
                            //side panel is visible on screen device only => shrinks map
                            if(activmap_places.is(':visible')){
                                activmap_canvas.animate({
                                    width: map_w-cont_w
                                },400);
                            }
                        }
                    }else{
                        if(s.showPanel && activmap_places.is(':visible') && $(window).width()+17 > 1190) activmap_canvas.width(map_w-cont_w);
                        else activmap_canvas.width(map_w);
                    }
                }else{
                    //no result found
                    if(opendInfoWindow != null) opendInfoWindow.close();
                    if(s.showPanel){
                        if(activmap_places.is(':visible') && $(window).width()+17 > 1190){
                            //there were previous results
                            //side panel is visible on screen device only => hides places, enlarges map
                            activmap_canvas.animate({
                                width: map_w
                            },400);
                            if(s.posPanel == 'left'){
                                 activmap_places.animate({
                                    left: -cont_w
                                },400);
                            }
                            if(s.posPanel == 'right'){
                                 activmap_places.animate({
                                    right: -cont_w
                                },400);
                            }
                        }else
                            activmap_canvas.width(map_w);
                    }else
                        activmap_canvas.width(map_w);
                }
                old_results = num_places;
                setTimeout('_map_resize()',400);
            }
            
            /* _map_resize() updates bounds and clusters on map resize
             * 
             */
            _map_resize = function() {
                google.maps.event.trigger(map, 'resize');
                
                var listener = google.maps.event.addListenerOnce(map, 'idle', function(){ 
                    if(map.getZoom() > 16) map.setZoom(16);
                });
                
                if(num_places > 0){
                    map.fitBounds(bounds);
                    var padbnds = _paddedBounds(10, 150, 50, 50);
                    map.fitBounds(padbnds);
                    if(s.cluster) markerCluster.repaint();
                }else{
                    map.setZoom(s.zoom);
                    map.setCenter(latlng);
                    if(s.cluster) markerCluster.repaint();
                }
            }
            
            /* _update_places_bounds() updates map bounds according to visible places
             * 
             */
            _update_places_bounds = function(){
                bounds = new google.maps.LatLngBounds();
                var i = 0;
                $.each(s.places, function(j, place){
                    if(place.isVisible == true){
                        bounds.extend(place.marker.getPosition());
                        i++;
                    }
                });
                bounds.extend(centerMarker.getPosition());
                num_places = i;
            }
            
            /* _update_places_tag(input) updates the array of places and the map's bounds
             * 
             * @param input checkbox object
             * 
             */
            _update_places_tag = function(input, uncheck){
                
                //Checkbox selection
                if(!s.allowMultiSelect){
                    if(uncheck){
                        $('.marker-selector').removeClass('active');
                        $('input[name="marker_type[]"]').not(input).prop('checked',false);
                        $('select[name="marker_type[]"]').not(input).find('option').prop('selected', false);
                    }
                }
                var checked = (input.prop('tagName') == 'SELECT') ? input.prop('selected') : input.prop('checked');
                
                if(checked == true)
                    input.parents('.marker-selector').addClass('active');
                else
                    input.parents('.marker-selector').removeClass('active');
                    
                var val;
                var i = 0;
                var p_ids = [];
                var ids_copy = [];
                $('input[name="marker_type[]"]:checked').add($('select[name="marker_type[]"]').prop('selected', true)).each(function(){
                    val = $(this).val();
                    if(val != ''){
                        if(ids[val] === undefined) ids[val] = [];
                        if(i == 0) p_ids = $.merge([], ids[val]);
                        else{
                            if(s.request == 'strict') p_ids = $.arrayIntersect(ids[val], p_ids);
                            if(s.request == 'large'){
                                ids_copy = $.merge([], ids[val]);
                                p_ids = $.merge(ids_copy, p_ids);
                            }
                        }
                        i++;
                    }
                });
                
                if(opendInfoWindow != null) opendInfoWindow.close();
                    
                $.each(s.places, function(j, place){
                    place.num_tags = 0;
                    place.dist = 0;
                    if($.inArray(place.id, p_ids) >= 0){
                        place.dist = _get_distance(place.marker.position, latlng);
                        //the place is in all tags => set visible
                        if(s.radius == 0 || place.dist <= s.radius*1000){
                            place.num_tags++;
                            place.marker.setVisible(true);
                            place.isVisible = true;
                            $('#activmap-place_'+place.id).show();
                        }else{
                            //perform only if a checkbox is clicked
                            if(place.num_tags > 0) place.num_tags--;
                            if(place.num_tags == 0){
                                place.marker.setVisible(false);
                                place.isVisible = false;
                                place.isVisible = false;
                                $('#activmap-place_'+place.id).hide();
                            }
                        }
                    }else{
                        //perform only if a checkbox is clicked
                        if(place.num_tags > 0) place.num_tags--;
                        if(place.num_tags == 0){
                            place.marker.setVisible(false);
                            place.isVisible = false;
                            $('#activmap-place_'+place.id).hide();
                        }
                    }
                });
                
                _update_places_bounds();
                
                $('#activmap-results-num').html(num_places+' result(s)');
            }

            if(!Array.isArray(s.places)){
                $.ajax({
                    url: s.places,
                    dataType: 'json',
                    cache: false,
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    },
                    success: function(data){
                        var obj = eval(data);
                        s.places = obj.places;
                        _init();
                    }
                });
            }else
                _init();
        }
    });
})(jQuery);
