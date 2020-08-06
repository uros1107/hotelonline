<?php
debug_backtrace() || die ('Direct access not permitted');
/**
 * Functions needed by the form of a module
 * build (from config.xml), check and display fields
 */

/***********************************************************************
 * checkFields() checks the values send by POST
 *
 * @param $db       database connection ressource
 * @param $fields   collection of field objects
 * @param $id       ID of the item
 *
 * @return boolean
 *
 */
function checkFields($db, $fields, $id)
{
    global $fields;
    global $texts;
    
    $valid = true;
	
    foreach($fields as $tableName => $fields_table){
				
		$id_lang = ($tableName != MODULE && $fields_table['table']['multi'] == 0) ? 0 : DEFAULT_LANG;
								
        foreach($fields_table['fields'] as $fieldName => $field){
            
            $values = $field->getAllValues();
            $label = $field->getLabel();
            $validation = $field->getValidation();
            
            foreach($values as $index => $value){
                
				$value = $field->getValue(false, $index, $id_lang);
				
				if($field->isRequired() && $value == ''){
					$field->setNotice($texts['REQUIRED_FIELD'], $index);
					$valid = false;
				}
				
				switch($field->getValidation()){
					case 'mail':
						if(!filter_var($value, FILTER_VALIDATE_EMAIL) && $value != ''){
							$field->setNotice($texts['INVALID_EMAIL'], $index);
							$valid = false;
						}
					break;
					case 'numeric':
						if(!is_numeric($value)){
							if($value != ''){
								$field->setNotice($texts['NUMERIC_EXPECTED'], $index);
								$valid = false;
							}
							$field->setValue(0, $index);
						}                        
					break;
				}
				
				if($field->isUnique() && $value != '' && $db != false){
					$query_unique = 'SELECT * FROM pm_'.$tableName.' WHERE '.$fieldName.' = \''.$value.'\' AND id != ';
					$query_unique .= ($tableName == MODULE) ? $id : $fields_table['fields']['id']->getValue(false, $index);
					if(db_column_exists($db, 'pm_'.$tableName, 'lang')) $query_unique .= ' AND lang = '.DEFAULT_LANG;
					$res_unique = $db->query($query_unique);
					if($res_unique !== false && $db->last_row_count() > 0){
						$field->setNotice($texts['VALUE_ALREADY_EXISTS'], $index);
						$valid = false;
					}
				}
            }
        }
    }
    return $valid;
}

/***********************************************************************
 * getFieldsFromNode() returns a collection of field objects
 *
 * @param $db           database connection ressource
 * @param $itemList     DOMNodeList
 *
 * @return array
 *
 */
function getFieldsFromNode($db, $itemList)
{
    $fields = array();
            
    foreach($itemList as $item){
        
        $type = htmlentities($item->getAttribute('type'), ENT_QUOTES, 'UTF-8');
        $label = get_translation(htmlentities($item->getAttribute('label'), ENT_QUOTES, 'UTF-8'));
        $name = htmlentities($item->getAttribute('name'), ENT_QUOTES, 'UTF-8');
        $required = htmlentities($item->getAttribute('required'), ENT_QUOTES, 'UTF-8');
        $multilingual = htmlentities($item->getAttribute('multi'), ENT_QUOTES, 'UTF-8');
        $editor = htmlentities($item->getAttribute('editor'), ENT_QUOTES, 'UTF-8');
        $options = array();
        $optFilters = array();
        $validation = htmlentities($item->getAttribute('validation'), ENT_QUOTES, 'UTF-8');
        $unique = htmlentities($item->getAttribute('unique'), ENT_QUOTES, 'UTF-8');
        $comment = get_translation(htmlentities($item->getAttribute('comment'), ENT_QUOTES, 'UTF-8'));
        $active = htmlentities($item->getAttribute('active'), ENT_QUOTES, 'UTF-8');
        $optionTable = '';
        $roles = htmlentities($item->getAttribute('roles'), ENT_QUOTES, 'UTF-8');
        if($roles == '') $roles = 'all';
        $roles = explode(',', str_replace(' ', '', $roles));
        $filterName = '';
        $itemOptions = $item->getElementsByTagName('options')->item(0);
        
        if(in_array($_SESSION['user']['type'], $roles) || in_array('all', $roles)){

            if($comment != '') $comment = str_ireplace('{currency}', DEFAULT_CURRENCY_SIGN, $comment);

            if(!empty($itemOptions)){
                $optionList = $itemOptions->getElementsByTagName('option');
                $optionTable = htmlentities($itemOptions->getAttribute('table'), ENT_QUOTES, 'UTF-8');
                $fieldLabel = htmlentities($itemOptions->getAttribute('fieldlabel'), ENT_QUOTES, 'UTF-8');
                $fieldValue = htmlentities($itemOptions->getAttribute('fieldvalue'), ENT_QUOTES, 'UTF-8');
                $filterName = htmlentities($itemOptions->getAttribute('filtername'), ENT_QUOTES, 'UTF-8');
                $optFilter = htmlentities($itemOptions->getAttribute('optfilter'), ENT_QUOTES, 'UTF-8');
                
                if($db !== false && $optionTable != '' && $fieldLabel != '' && $fieldValue != ''){
                    if($optionList->length > 0){
                        foreach($optionList as $option)
                            $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = htmlentities($option->nodeValue, ENT_QUOTES, 'UTF-8');
                    }
                    
                    $order = htmlentities($itemOptions->getAttribute('order'), ENT_QUOTES, 'UTF-8');
                    
                    $query_option = 'SELECT * FROM '.$optionTable;
                    $query_option_cond = '';
                    
                    if(db_column_exists($db, $optionTable, 'lang')){
                        $query_option_cond .= ($query_option_cond != '') ? ' AND ' : ' WHERE ';
                        $query_option_cond .= 'lang = '.DEFAULT_LANG;
                    }
                    
                    if(!in_array($_SESSION['user']['type'], array('administrator', 'manager', 'editor')) && db_column_exists($db, $optionTable, 'users')){
                        $query_option_cond .= ($query_option_cond != '') ? ' AND ' : ' WHERE ';
                        $query_option_cond .= 'users REGEXP \'(^|,)'.$_SESSION['user']['id'].'(,|$)\'';
                    }
                    
                    $query_option .= $query_option_cond;
                    
                    if($order != '') $query_option .= ' ORDER BY '.$order;

                    $result_option = $db->query($query_option);
                    if($result_option !== false){
                        $optionLabel = '';
                        $nb_values = $db->last_row_count();
                        foreach($result_option as $j => $row_option){
                            
                            $arr_fieldLabel = array_filter(preg_split('/([^a-z0-9_]+)/i', $fieldLabel));
                            $seps = array_values(array_filter(preg_split('/([a-z0-9_]+)/i', $fieldLabel)));
                            
                            $optionLabel = '';
                            $n2 = 0;
                            $lgt2 = count($arr_fieldLabel);
                            foreach($arr_fieldLabel as $str_fieldLabel){
                                $optionLabel .= $row_option[$str_fieldLabel];
                                if(isset($seps[$n2]) && $n2+1 < $lgt2) $optionLabel .= $seps[$n2];
                                $n2++;
                            }
                            $optionValue = $row_option[$fieldValue];
                            $options[$optionValue] = $optionLabel;
                            if($optFilter != '')
                                $optFilters[$optionValue] = $row_option[$optFilter];
                        }
                    }
                    
                }elseif($optionList->length > 0){
                    foreach($optionList as $option)
                        $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = get_translation(htmlentities(str_ireplace('{currency}', DEFAULT_CURRENCY_SIGN, $option->nodeValue), ENT_QUOTES, 'UTF-8'));
                
                }elseif($itemOptions->getElementsByTagName('min')->length == 1 && $itemOptions->getElementsByTagName('max')->length == 1){
                    $min = htmlentities($itemOptions->getElementsByTagName('min')->item(0)->nodeValue, ENT_QUOTES, 'UTF-8');
                    $max = htmlentities($itemOptions->getElementsByTagName('max')->item(0)->nodeValue, ENT_QUOTES, 'UTF-8');
                    if(is_numeric($min) && is_numeric($max)){
                        for($i = $min; $i <= $max; $i++)
                            $options[$i] = $i;
                    }
                }
            }
            if($type == 'filelist'){
                $itemOptions = $item->getElementsByTagName('options')->item(0);
                $optionDirectory = htmlentities($itemOptions->getAttribute('directory'), ENT_QUOTES, 'UTF-8');
                $optionDirectory = str_replace('{template}', TEMPLATE, $optionDirectory);
                $rep = opendir($optionDirectory) or die('Error directory opening : '.$optionDirectory);
                while($entry = @readdir($rep)){
                    if($entry != '.' && $entry != '..'){
                        $entry = str_replace('.php', '', $entry);
                        $options[$entry] = $entry;
                    }
                }
            }
            $fields[$name] = new Field($name, $label, $type, $required, $validation, $options, $multilingual, $unique, $comment, $active, $editor, $optionTable, $roles, $filterName, $optFilters);
        }
    }
    return $fields;
}
/***********************************************************************
 * getFields() returns a collection of field objects
 *
 * @param $db database connection ressource
 *
 * @return array
 *
 */
function getFields($db)
{
    $file = 'config.xml';
    $dom = new DOMDocument();
    if(!$dom->load($file))
        die('Unable to load the XML file');
    if(!$dom->schemaValidate(dirname(__FILE__).'/config.xsd'))
        die('The XML file does not respect the schema');
        
    $root = $dom->getElementsByTagName('module')->item(0);
    $form = $root->getElementsByTagName('form')->item(0);
    
    $fields = array();
    
    $tables = $form->getElementsByTagName('table');
    foreach($tables as $table){
        $tableName = $table->getAttribute('name');
        $tableLabel = get_translation($table->getAttribute('label'));
        $fieldRef = $table->getAttribute('fieldref');
        $multi = $table->getAttribute('multi');
        $itemList = $table->getElementsByTagName('field');
        
        $tmp_fields = array();
        $tmp_fields['id'] = new Field('id', 'ID', 'id', 0, 'numeric', null, 0, 1, '', 0, 0, '', 'all', '', null);
        $tmp_fields += getFieldsFromNode($db, $itemList);
        
        $fields[$tableName] = array('table' => array('tableLabel' => $tableLabel, 'fieldRef' => $fieldRef, 'multi' => $multi), 'fields' => $tmp_fields);
    }
    
    if($form->hasChildNodes()){  

        $childNodes = $form->childNodes;
        $tableNodes = array();
        foreach($childNodes as $node)
            if($node->nodeName == 'table') $tableNodes[] = $node;
        
        foreach($tableNodes as $node)
            $node->parentNode->removeChild($node);
            
        $itemList = $form->getElementsByTagName('field');

        $fields = array(MODULE => array('table' => null, 'fields' => getFieldsFromNode($db, $itemList))) + $fields;
    }
    
    return $fields;
}

/***********************************************************************
 * displayField() displays a field in the form
 *
 * @param $field    field object
 * @param $table    name of the table
 * @param $id_lang  ID of the current language
 *
 * @return void
 *
 */
function displayField($field, $table, $index, $id_lang)
{
    $name = $field->getName();
    $type = $field->getType();
    $label = $field->getLabel();
    $required = $field->isRequired();
    $options = $field->getOptions();
    $editor = $field->isEditor();
    $multilingual = $field->isMultilingual();
    $validation = $field->getValidation();
    $comment = $field->getComment();
    $notice = $field->getNotice($index);
    $active = $field->isActive();
    $optFilters = $field->getOptFilters();
    $filterName = $field->getFilterName();
    
    $value = $field->getValue(true, $index, $id_lang);
    if(!is_array($value)) $value = stripslashes($value);
    
    $str_active = ($active == 0) ? ' readonly="readonly"' : '';
    
    $str_filter = ($filterName != '') ? ' data-filter="'.$filterName.'_'.$id_lang.'['.$index.']"' : '';
    
    $inputdescr = $table.'_'.$name.'_'.$id_lang;
    $input_id = $inputdescr.'_'.$index;
    $inputname = $inputdescr.'['.$index.']';
                
    switch($type){
        case 'id' :
            if($value > 0) echo $value;
            echo '<input type="hidden"'.$str_active.' name="'.$inputname.'" id="'.$input_id.'" value="'.$value.'">'."\n";
        break;
        case 'text' :
        case 'alias' :
            echo '<input type="text"'.$str_active.' name="'.$inputname.'" id="'.$input_id.'" value="'.$value.'" rel='.$inputdescr.' class="form-control'.(!empty($options) ? ' typeahead' : '').'">'."\n";
            if(!empty($options) && $index == 0){
                echo '
                <script>
                    var dataset_'.$inputdescr.' = new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.whitespace,
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        local: [';
                            foreach($options as $option){
                                $key = key($options);
                                echo '\''.addslashes($options[$key]).'\'';
                                if(next($options) !== false) echo ', ';
                            }
                            echo '
                        ]
                    })
                    function search_'.$inputdescr.'(q, sync){
                        if(q === \'\')
                            sync(dataset_'.$inputdescr.'.index.all());
                        else
                            dataset_'.$inputdescr.'.search(q, sync);
                    }
                    typeahead_opts[\''.$inputdescr.'\'] = {
                        name: \''.$inputdescr.'\',
                        source: search_'.$inputdescr.'
                    };
                </script>';
            }
        break;
        case 'password' :
            echo '<input type="password"'.$str_active.' name="'.$inputname.'" value="" size="30" class="form-control">'."\n";
        break;
        case 'textarea' :
            echo '<textarea name="'.$inputname.'"'.$str_active.' id="'.$input_id.'" cols="40" rows="5" class="form-control" data-editor="'.$editor.'">'.$value.'</textarea>'."\n";
        break;
        case 'select' :
        case 'filelist' :
            echo '<select name="'.$inputname.'"'.$str_active.' class="form-control"'.$str_filter.'>'."\n";
            
            $selected = ($value == '') ? ' selected="selected"' : '';
            echo '<option value=""'.$selected.'>-</option>'."\n";
            
            foreach($options as $option){
                $key = key($options);
                $selected = (strval($value) == strval($key)) ? ' selected="selected"' : '';
                $rel = (is_array($optFilters) && isset($optFilters[$key])) ? ' rel="'.$optFilters[$key].'"' : '';
                echo '<option value="'.$key.'"'.$rel.' '.$selected.'>'.$options[$key].'</option>'."\n";
                next($options);
            }
            echo '</select>'."\n";
        break;
        case 'multiselect' :
            $size = (count($options) > 4) ? 8 : 4;
            $selected = array();
            $value = explode(',', $value);
            
            echo '<select name="'.$inputname.'_tmp[]" multiple="multiple" id="'.$input_id.'_tmp" size="'.$size.'"'.$str_active.' class="form-control">'."\n";
            
            foreach($options as $key => $option){
                if((is_array($value) && !in_array($key, $value)) || (!is_array($value) && $key != $value))
                    echo '<option value="'.$key.'">'.$options[$key].'</option>'."\n";
            }
            echo '</select>';
            
            echo '
                <a href="#" class="btn btn-default remove_option" rel="'.$input_id.'"><i class="fa fa-arrow-left"></i></a>
                <a href="#" class="btn btn-default add_option" rel="'.$input_id.'"><i class="fa fa-arrow-right"></i></a>
                <select name="'.$inputname.'[]" multiple="multiple" id="'.$input_id.'" size="'.$size.'"'.$str_active.' class="form-control">'."\n";
                foreach($options as $key => $option){
                    if(((is_array($value) && in_array($key, $value)) || (!is_array($value) && $key == $value)) && $key != '')
                        echo '<option value="'.$key.'" selected="selected">'.$options[$key].'</option>'."\n";
                }
                echo '</select>'."\n";
        break;
        case 'checkbox' :
            foreach($options as $option){
                $key = key($options);
                $checked = (in_array($key, explode(',', $value))) ? ' checked="checked"' : '';                    
                echo '<label class="checkbox-inline"><input name="'.$inputname.'[]" type="checkbox"'.$str_active.' value="'.$key.'"'.$checked.'>&nbsp;'.$options[$key].'</label>'."\n";
                next($options);
            }
        break;
        case 'radio' :
            foreach($options as $option){
                $key = key($options);
                $checked = ($value == $key) ? ' checked="checked"' : '';        
                echo '<label class="radio-inline"><input name="'.$inputname.'" type="radio"'.$str_active.' value="'.$key.'"'.$checked.'>&nbsp;'.$options[$key].'</label>'."\n";
                next($options);
            }
        break;
        case 'date' :
        case 'datetime' :
                
            if($type == 'datetime'){
                $date = is_numeric($value) ? gmdate('Y-m-d', $value) : '';
                if(is_numeric($value)){
                    $hour = gmdate('H', $value);
                    $minute = gmdate('i', $value);
                }else{
                    $hour = '';
                    $minute = 0;
                }
            }else
                $date = is_numeric($value) ? gmdate('Y-m-d', $value) : '';
                
            
            echo '
            <div class="input-group">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" class="form-control datepicker" name="'.$inputname.'[date]"'.$str_active.' value="'.$date.'">
            </div>';
            
            if($type == 'datetime'){
                echo '&nbsp;&nbsp;<select name="'.$inputname.'[hour]"'.$str_active.' class="form-control"'.$str_active.'>'."\n";
                $selected = ($hour == '') ? ' selected="selected"' : '';
                echo '<option value=""'.$selected.'>-</option>'."\n";
                for($i = 0; $i <= 23; $i++){
                    $selected = (strval($i) == strval($hour)) ? ' selected="selected"' : '';
                    echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>'."\n";
                }
                echo '</select>&nbsp;:&nbsp;'."\n";
                
                echo '<select name="'.$inputname.'[minute]"'.$str_active.' class="form-control"'.$str_active.'>'."\n";
                $selected = ($minute == '') ? ' selected="selected"' : '';
                echo '<option value=""'.$selected.'>-</option>'."\n";
                for($i = 0; $i <= 59; $i++){
                    $selected = (strval($i) == strval($minute)) ? ' selected="selected"' : '';
                    $zero = ($i < 10) ? '0' : '';
                    echo '<option value="'.$i.'"'.$selected.'>'.$zero.$i.'</option>'."\n";
                }
                echo '</select>'."\n";
            }
        break;
    }
}
/***********************************************************************
 * getClassAttr() generates the class name attribute of a field
 *
 * @param $type         field type
 * @param $validation   field validation
 * @param $type         field notice
 * @param $id_lang      ID of the current language
 *
 * @return string
 *
 */
function getClassAttr($type, $validation, $notice, $id_lang)
{
    $class = '';
    if(($type == 'text' || $type == 'select') && $validation == 'numeric')
        $class .= ' numeric';
    if(($type == 'text' && $validation == 'numeric')
        || $type == 'select'
        || $type == 'filelist'
        || $type == 'multiselect'
        || $type == 'date'
        || $type == 'datetime')
        $class .= ' form-inline';
    if($notice != '' && ($id_lang == DEFAULT_LANG || $id_lang == 0))
        $class .= ' has-error has-feedback';
    return $class;
}
/***********************************************************************
 * getNumMaxRows() retuns the number of rows in the form table
 *
 * @param $fields       collection of field objects
 * @param $tableName    name of the table
 *
 * @return integer
 *
 */
function getNumMaxRows($fields, $tableName)
{
    $maxRows = 0;
    foreach($fields[$tableName]['fields'] as $fieldName => $field){
        $numRows = count($field->getAllValues());
        if($numRows > $maxRows) $maxRows = $numRows;
    }
    return $maxRows;
}
/*function getIndexList($fields, $tableName, $id_lang = DEFAULT_LANG)
{
    $indexes = array();
    foreach($fields[$tableName]['fields'] as $fieldName => $field){
        $indexes = array_keys($field->getAllValues($id_lang));
    }
    $indexes = array_unique($indexes);
    sort($indexes);
    return $indexes;
}*/
