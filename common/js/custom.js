var isMobile = false;
var isDesktop = false;
function screenSize(){
	//mobile detection
	if(Modernizr.mq('only all and(max-width: 991px)'))
		isMobile = true;
	else
		isMobile = false;
	
	//tablette and mobile detection
	if(Modernizr.mq('only all and(max-width: 1024px)'))
		isDesktop = false;
	else
		isDesktop = true;
}
function getNumericInput(input){
	var val = parseFloat(input.val().replace(/[^\d.-]/g, ''));
	return (val > 0) ? val : 0;
}
function setNumericInput(input, val){
	if(val > 0) input.val(Math.round(val*100)/100); else input.val(0);
}
/* =====================================================================
 * DOCUMENT READY
 * =====================================================================
 */
$(document).ready(function(){
	//RESIZE EVENTS
	$(window).resize(function(){
		screenSize();
		Modernizr.addTest('ipad', function(){
			return !!navigator.userAgent.match(/iPad/i);
		});
	});
	screenSize();
	'use strict';
    
    /* =================================================================
     * form placeholder for IE
     * =================================================================
     */
	if(!Modernizr.input.placeholder){
		$('[placeholder]').focus(function(){
			var input = $(this);
			if(input.val() == input.attr('placeholder')){
				input.val('');
				input.removeClass('placeholder');
			}
		}).blur(function(){
			var input = $(this);
			if(input.val() == '' || input.val() == input.attr('placeholder')){
				input.addClass('placeholder');
				input.val(input.attr('placeholder'));
			}
		}).blur();
		$('[placeholder]').parents('form').submit(function(){
			$(this).find('[placeholder]').each(function(){
				var input = $(this);
				if(input.val() == input.attr('placeholder')){
					input.val('');
				}
			})
		});
	}
    /* =================================================================
     * MAGNIFIC POPUP
     * =================================================================
     */
	if($('a.image-link').length){
        $('a.image-link').magnificPopup({
            type:'image',
            mainClass: 'mfp-with-zoom',
            gallery:{
                enabled: true 
            },
            zoom: {
                enabled: true
            }
        });
	}
    if($('.ajax-popup-link').length){
        $('.ajax-popup-link').each(function(){
            $(this).magnificPopup({
                type: 'ajax',
                ajax: {
                    settings: {
                        method: 'POST',
                        data: $(this).data('params')
                    }
                }
            });
        });
    }
    /* =================================================================
     * TOOLTIP
     * =================================================================
     */
	$('.tips').tooltip({placement:'auto'});
    
    /* =================================================================
     * ALERT
     * =================================================================
     */ 
	$('.alert').delegate('button', 'click', function(){
		$(this).parent().fadeOut('fast');
	});
	
	/* =================================================================
     * AJAX / FORM
     * =================================================================
     */
    if($('form.ajax-form').length){
        $.fn.clear = function(){
            $(this)
                .find('input').not('.noreset')
                    .filter(':text, :password, :file, :hidden').val('').end()
                    .filter(':checkbox, :radio').prop('checked', false).removeAttr('checked').end()
                .end()
                .find('textarea').not('.noreset').val('').end()
                .find('select').not('.noreset').prop('selectedIndex', -1).prop('selected', false)
                    .find('option:selected').removeAttr('selected');

            return this;
        };
        function sendAjaxForm(form, action, targetCont, refresh, clear, extraTarget, onload){
            var posQuery = action.indexOf('?');
            var extraData = '';
            if(posQuery != -1){
                extraData = action.substr(posQuery+1);
                if(extraData != '') extraData = '&'+extraData;
                action = action.substr(0, posQuery);
            }
            $.ajax({
                url: action,
                type: form.attr('method'),
                data: form.serialize()+extraData,
                success: function(response){
                    if(onload != 1){
						$('.field-notice',form).html('').hide().parent().removeClass('alert alert-danger');
						$('.alert.alert-danger').html('').hide();
						$('.alert.alert-success').html('').hide();
                    }
                    
                    var response = $.parseJSON(response);
                    
                    if(targetCont != '') $(targetCont).removeClass('loading-ajax');
                    
                    if(response.error != '') $('.alert.alert-danger', form).html(response.error).slideDown();
                    else if(response.redirect != '' && response.redirect != undefined) window.location.href = response.redirect;
                    else if(refresh === true){
                        var href = window.location.href;
                        window.location = href.substr(0, href.lastIndexOf('#'));
                     }
                    if(response.success != ''){
                        $('.alert.alert-success', form).html(response.success).slideDown();
                        if(clear && response.error == '' && response.notices.length == 0)
                            form.clear();
                    }
                    
                    if(!$.isEmptyObject(response.notices)){
                        if(targetCont != "") $(targetCont).hide();
                        $.each(response.notices, function(field,notice){
                            var elm = $('.field-notice[rel="'+field+'"]', form);
                            if(elm.get(0) !== undefined) elm.html(notice).fadeIn('slow').parent().addClass('alert alert-danger');
                        });
                        $('.captcha_refresh', form).trigger('click');
                    }else{
                        if(targetCont != ''){
                            $(targetCont).html(response.html);
                            $('.open-popup-link').magnificPopup({
                                type:'inline',
                                midClick: true
                            });
                            $('.selectpicker').selectpicker('refresh');
                        }
                        if(extraTarget != '')
                            $(extraTarget).html(response.extraHtml);
                    }
                    
                    if($('.alert:visible', form).length){
                        
                        var scroll_1 = $('html, body').scrollTop();
                        var scroll_2 = $('body').scrollTop();
                        var scrolltop = scroll_1;
                        if(scroll_1 == 0) scrolltop = scroll_2;
                        
                        var scrolltop2 = $('.alert:visible:first', form).offset().top - 80;
                        if(scrolltop2 < scrolltop) $('html, body').animate({scrollTop: scrolltop2+'px'});
                    }
                } 
            });
        }
        $('form.ajax-form').on('click change', '.sendAjaxForm', function(e){
            e.defaultPrevented;
            var elm = $(this);
            var onload = elm.attr('data-sendOnload');
            var tagName = elm.prop('tagName');
            if((e.type == 'click' && ((tagName == 'INPUT' && (elm.attr('type') == 'submit' || elm.attr('type') == 'image')) || tagName == 'A' || tagName == 'BUTTON')) || e.type == 'change'){
                var targetCont = elm.data('target');
                var refresh = elm.data('refresh');
                var clear = elm.data('clear');
                if(targetCont != "") $(targetCont).html('').addClass('loading-ajax').show();
                sendAjaxForm(elm.parents('form.ajax-form'), elm.data('action'), targetCont, refresh, clear, elm.data('extratarget'), onload);
                //if(tagName == 'A') return false;
            }else{
                //if(tagName == 'A') return false;
            }
        });
        $('.submitOnClick').on('click', function(e){
            e.defaultPrevented;
            $(this).parents('form').submit();
            return false;
        });
        $('.sendAjaxForm[data-sendOnload="1"]').trigger('change');
    }
    if($('a.ajax-link').length){
        $('a.ajax-link').on('click', function(e){
            e.defaultPrevented;
            var elm = $(this);
            var href = elm.attr('href');
            $.ajax({
                url: elm.data('action'),
                type: 'get',
                success: function(response){
                    if(href != '' && href != '#') $(location).attr('href', href);
                } 
            });
            return false;
        });
    }
});
