<?php
debug_backtrace() || die ("Direct access not permitted");
require_once("inc_header_common.php"); ?>

<script src="//twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>

<script src="<?php echo DOCBASE.ADMIN_FOLDER; ?>/js/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo DOCBASE.ADMIN_FOLDER; ?>/js/plugins/ckeditor/adapters/jquery.js"></script>
<script>
    var typeahead_opts = new Array();
    function init_typeahead(input){
        var target = input.attr('rel');
        input.typeahead({
            highlight: true,
            minLength: 0
        }, typeahead_opts[target]);
    }
    function init_datepicker(input){
        var target = input.attr('rel');
        if(input.attr('readonly') != 'readonly'){
            $(input).datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: null
            });
        }
    }
        
    $(function(){
        
        $('.typeahead').each(function(){
            var input = $(this);
            var target = input.attr('rel');
            init_typeahead(input);
        });
        
        $('select[name="multiple_actions_file"]').on('change', function(){
            if(($(this).val() == 'delete_multi_file' && confirm('<?php echo $texts['DELETE_CONFIRM1']." ".$texts['LOOSE_DATAS']; ?>'))
            || ($(this).val() != 'delete_multi_file' && confirm('<?php echo $texts['ACTION_CONFIRM']." ".$texts['LOOSE_DATAS']; ?>')))
                $('#form').attr('action','index.php?view=form&csrf_token=<?php echo $csrf_token; ?>&action='+$(this).val()).submit();
        });
        $('.btn-slide').on('click', function(){
            var library = $('#wrap-library');
            if($(this).hasClass('left')){
                $(this).removeClass('left').addClass('right');
                library.animate({
                    right:'0px'
                }, 1000);
            }else{
                $(this).removeClass('right').addClass('left');
                library.animate({
                    right:-library.width()+'px'
                }, 1000);
            }
            return false;
        });
        $('.actions-file a').on('click', function(){
            if(!confirm('<?php echo $texts['ACTION_CONFIRM']." ".$texts['LOOSE_DATAS']; ?>')) return false;
        });
        $('textarea[data-editor="1"]').ckeditor();
        
        $('.datepicker:not([readonly="readonly"])').each(function(){
            init_datepicker($(this));
        });
            
        $('.add_option').on('click', function(){
            var list_id = $(this).attr('rel');
            $('#'+list_id+'_tmp > option:selected').remove().appendTo('#'+list_id);
            $('#'+list_id+' > option').prop('selected', true);
            return false;
            
        });
        $('.remove_option').on('click', function(){
            var list_id = $(this).attr('rel');
            $('#'+list_id+' > option:selected').remove().appendTo('#'+list_id+'_tmp');
            $('#'+list_id+' > option').prop('selected', true);
            return false;
        });
        var indexes = Array();
        $('.new_entry').on('click', function(){
            var table = $($(this).attr('href'));
            var row = $('tr', table);
            var index = row.length-1;
            var last_row = row.last();
            
            $('.typeahead', last_row).typeahead('destroy');
            $('.datepicker', last_row).datepicker('destroy').attr('id', '');
            row = last_row.clone();
            
            $('.typeahead', last_row).each(function(){
                init_typeahead($(this));
            });
            $('.datepicker', last_row).each(function(){
                init_datepicker($(this));
            });
            
            $('input, textarea', row).val('');
            $('select > option', row).prop('selected', false);
            $('checkbox, radio', row).prop('checked', false);
            
            $('input, textarea, select, checkbox, radio', row).each(function(){
                old_name = $(this).attr('name');
                if(old_name !== undefined) $(this).attr('name', old_name.replace(/([a-zA-Z_0-9\[\]]+)\[([0-9]+)\](\[\])?/, '$1['+index+']$3'));
                old_id = $(this).attr('id');
                if(old_id !== undefined) $(this).attr('id', old_id.replace(/([a-zA-Z_0-9]+)_([0-9]+)/, '$1_'+index));
                  
                if($(this).hasClass('typeahead')) init_typeahead($(this));
                if($(this).hasClass('datepicker')) init_datepicker($(this));
            });
            row.appendTo(table);
            return false;
        });
        $('form').on('submit', function(){
            $('#overlay').show();
        });
        if($('textarea[data-editor="1"]').length){
            setTimeout(function(){
                $('.btn-slide').trigger('click');
            }, 800);
        }
        $('.tab-content').on('keyup', '.numeric > input', function(){
			var val = $(this).val().replace(/[^\d.-]/g, '');
			$(this).val(val);
		});
        $('.tab-content').on('blur', '.numeric > input', function(){
			var val = parseFloat($(this).val());
			if(isNaN(val)) val = '';
			$(this).val(val);
		});
    });
</script>
<?php
if(NB_FILES > 0 && (in_array("add", $permissions) || in_array("edit", $permissions) || in_array("all", $permissions))){ ?>
    <script src="<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/uploadifive/jquery.uploadifive.js"></script>
    <link href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/uploadifive/uploadifive.css" rel="stylesheet">
    <script src="<?php echo DOCBASE.ADMIN_FOLDER; ?>/js/toolMan.js"></script>
    <script>
        var dragsort = ToolMan.dragsort();
        var junkdrawer = ToolMan.junkdrawer();
        
        $(function() {
            
            $('.files-list').each(function(){
                
                var id_list = $(this).attr('id');
                
                dragsort.makeListSortable(document.getElementById(id_list), saveOrder);
                
                function saveOrder(item) {
                    var group = item.toolManDragGroup;
                    var id = group.element.parentNode.getAttribute('id');
                    if(id == null) return;
                    group.register('dragend', function(){
                        
                        var list = ToolMan.junkdrawer().serializeList(document.getElementById(id_list));
                        
                        $.ajax({
                            type: "POST",
                            url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/order_medias.php?list='+list+'&table=<?php echo MODULE."_file"; ?>&id_item=<?php echo $id; ?>&prefix=file'
                        });
                    })
                }
            });
            
            <?php
            if(empty($_SESSION['msg_error'])){ ?>
                $.ajax({
                    type: 'POST',
                    url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/clear_tmp.php',
                    data: 'dir=medias/<?php echo MODULE; ?>/tmp&token=<?php echo $_SESSION['token'];?>',
                    success: function(data){
                        
                    }
                });
                <?php
            } ?>
            
            $('.file_upload').each(function(){
                
                var id = $(this).attr('id');
                var rel = $(this).attr('rel').split(',');
                var lang = rel[0];
                var max_file = rel[1];
                
                if(max_file > 10) max_file = 10;
                
                var container = $('#file_uploaded_'+lang);
                if($('.prev-file', container).size() > 0) container.slideDown();
                
                $('#'+id).uploadifive({
                    'formData'         : {
                        'timestamp' : '<?php echo $_SESSION['timestamp'];?>',
                        'uniqid' : '<?php echo $_SESSION['uniqid'];?>',
                        'token' : '<?php echo $_SESSION['token'];?>',
                        'dir' : '<?php echo MODULE; ?>',
                        'root_bo' : '<?php echo DOCBASE.ADMIN_FOLDER; ?>/',
                        'exts' : '<?php echo serialize(array_keys($allowable_file_exts)); ?>',
                        'lang' : lang
                    },
                    'buttonText'     : '<i class="fa fa-folder-open"></i> <?php echo $texts['CHOOSE_FILE']; ?>',
                    'fileTypeDesc'     : 'Files',
                    'fileTypeExts'     : '<?php foreach(array_keys($allowable_file_exts) as $file_ext) echo "*.".$file_ext.";*.".mb_strtoupper($file_ext, "UTF-8").";"; ?>',
                    'multi'            : (max_file > 1),
                    'queueSizeLimit': max_file,
                    'uploadLimit'     : max_file,
                    'queueID'        : 'file_upload_'+lang+'-queue',
                    'uploadScript'     : '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/uploadifive/uploader/uploadifive.php',
                    'onUploadComplete' : function(file, data, response){
                        
                        data = data.split('|');
                        
                        if($('.prev-file', container).size() == 0) container.slideDown();
                            
                        var filename = data[0].substring(data[0].lastIndexOf('/')+1);
                        var ext = filename.substring(filename.lastIndexOf('.')+1).toLowerCase();
                        
                        if((data[2] == 0 && data[3] == 0) || ext == 'swf'){
                        
                            var icon_file = '';
                            
                            switch(ext){
                                <?php
                                foreach($allowable_file_exts as $file_ext => $icon_file)
                                    echo "case '".$file_ext."' : icon_file = '".$icon_file."'; break;\n"; ?>
                            }
                            
                            container.append('<div class="prev-file"><img src="<?php echo DOCBASE; ?>common/images/'+icon_file+'" alt="" border="0">'+filename.substring(0, filename.lastIndexOf('.')).substring(0, 15)+'...'+ext+'<br>'+data[1]+'</div>');
                        
                        }else
                            container.append('<div class="prev-file"><img src="'+data[0]+'" alt="" border="0">'+filename.substring(0, filename.lastIndexOf('.')).substring(0, 15)+'...'+ext+'<br>'+data[1]+' | '+data[2]+' x '+data[3]+'</div>');
                        
                        if($('.prev-file', container).size() == 1) container.slideDown();
                    }
                });
            });
        });
    </script>
    <?php
} ?>
