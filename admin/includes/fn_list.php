<?php
debug_backtrace() || die ('Direct access not permitted');
/**
 * Functions needed by the listing of a module
 * build (from config.xml) and display columns and filters
 */

/***********************************************************************
 * getCols() returns a collection of column objects
 *
 * @return array
 *
 */
function getCols()
{
    $file = 'config.xml';
    $dom = new DOMDocument();
    if(!$dom->load($file))
        die('Unable to load the XML file');
    if(!$dom->schemaValidate(dirname(__FILE__).'/config.xsd'))
        die('The XML file does not respect the schema');
    
    $root = $dom->getElementsByTagName('module')->item(0);
    $list = $root->getElementsByTagName('list')->item(0);
    $itemList = $list->getElementsByTagName('col');

    $columns = array();
    
    foreach ($itemList as $item){
        
        $label = get_translation(htmlentities($item->getAttribute('label'), ENT_QUOTES, 'UTF-8'));
        $name = htmlentities($item->getAttribute('name'), ENT_QUOTES, 'UTF-8');
        $type = htmlentities($item->getAttribute('type'), ENT_QUOTES, 'UTF-8');
        $table = htmlentities($item->getAttribute('table'), ENT_QUOTES, 'UTF-8');
        $fieldRef = htmlentities($item->getAttribute('fieldref'), ENT_QUOTES, 'UTF-8');
        $fieldValue = htmlentities($item->getAttribute('fieldvalue'), ENT_QUOTES, 'UTF-8');
        
        $caseValues = array();
        $itemValues = $item->getElementsByTagName('values')->item(0);
        if(!empty($itemValues)){
            $valueList = $itemValues->getElementsByTagName('value');
            if($valueList->length > 0){
                foreach($valueList as $value)
                    $caseValues[htmlentities($value->getAttribute('case'), ENT_QUOTES, 'UTF-8')] = get_translation($value->nodeValue);
            }
        }
        
        if($fieldValue != '' && $table != '') $key = $table.'.'.$fieldValue; else $key = $name;
        
        $columns[$key] = new Column($name, $label, $type, $table, $fieldRef, $fieldValue, $caseValues);
    }
    return $columns;
}
 
/***********************************************************************
 * getColsValues() sets the value of columns and returns a collection of column objects
 *
 * @param $db   database connection ressource
 * @param $row  current row in the database
 * @param $i    index of the current row
 * @param $cols collection of column objects
 * 
 * @return array
 *
 */
function getColsValues($db, $row, $i, $cols)
{
    foreach($cols as $col){
        $table = $col->getTable();
        $colname = $col->getName();

        if($table != ''){
            $value = $row[$colname];
            
            if($db !== false && db_table_exists($db, $table)){
                
                if(preg_match('/.*(int).*/i', db_column_type($db, $table, $col->getFieldRef())) === false || !empty($value)){
                    
                    if($value == 0) $value = '-';
                    else{
                        $req_table = 'SELECT * FROM '.$table.' WHERE '.$col->getFieldRef().' IN('.$value.')';
                        if(db_column_exists($db, $table, 'lang')) $req_table .= ' AND lang = '.DEFAULT_LANG;
                        $res_table = $db->query($req_table);
                        if($res_table !== false){
                            $value = '';
                            $nb_values = $db->last_row_count();
                            
                            foreach($res_table as $j => $row_table){
                                $fieldValue = $col->getFieldValue();

                                $arr_fieldValue = preg_split('/([^a-z0-9_]+)/i', $fieldValue);
                                $seps = array_values(array_filter(preg_split('/([a-z0-9_]+)/i', $fieldValue)));
                                
                                $label = '';
                                $n2 = 0;
                                $lgt2 = count($arr_fieldValue);
                                foreach($arr_fieldValue as $str_fieldValue){
                                    $value .= $row_table[$str_fieldValue];
                                    if(isset($seps[$n2]) && $n2+1 < $lgt2) $value .= $seps[$n2];
                                    $n2++;
                                }
                                if($j+1 < $nb_values) $value .= ', ';
                            }
                        }
                    }
                }
            }else
                die($table.': Table not found, check the file config.xml');
        }else{

            $arr_colname = preg_split('/([^a-z0-9_]+)/i', $colname);
            $lgt1 = count($arr_colname);
            
            if($lgt1 > 1){
                $arr_seps = array_values(array_filter(preg_split('/([a-z0-9_]+)/i', $colname)));
                $value = '';
                $n1 = 0;
                foreach($arr_colname as $str_colname){

                    $curr_value = $row[$str_colname];
                    
                    if(!is_null($curr_value) && $curr_value != '' && isset($arr_seps[$n1-1])) $value .= $arr_seps[$n1-1].$curr_value;
                    else $value .= $curr_value;
                    
                    $n1++;
                }
            }else{
                $value = $row[$colname];
                if(!is_null($value)){
                    switch($col->getType()){
                        case 'date' :
                            $value = gmstrftime(DATE_FORMAT, $value);
                        break;
                        case 'datetime' :
                            $value = gmstrftime(DATE_FORMAT.' '.TIME_FORMAT, $value);
                        break;
                        case 'price' :
                            $value = formatPrice($value, DEFAULT_CURRENCY_SIGN);
                        break;
                        case 'case' :
                            $value = $col->getCaseValue($value);
                        break;
                        default :
                            $value = preg_replace('/\s\s+/', ' ', preg_replace('/([\n\r])/', ' ', $value));
                        break;
                    }
                    $value = strtrunc($value, 50);
                }else
                    $value = '';
            }
        }
        $col->setValue($i, $value);
    }
    return $cols;
}

 /***********************************************************************
 * getFilters() returns a collection of filter objects
 *
 * @param $db database connection ressource
 * 
 * @return array
 *
 */
function getFilters($db)
{
    $file = 'config.xml';
    $dom = new DOMDocument();
    if(!$dom->load($file))
        die('Unable to load the XML file');
    if(!$dom->schemaValidate(dirname(__FILE__).'/config.xsd'))
        die('The XML file does not respect the schema');
    
    $root = $dom->getElementsByTagName('module')->item(0);
	$list = $root->getElementsByTagName('list')->item(0);
	$itemList = $list->getElementsByTagName('filter');

	$filters = array();
	
	foreach($itemList as $item){
		
		$label = get_translation(htmlentities($item->getAttribute('label'), ENT_QUOTES, 'UTF-8'));
		$name = htmlentities($item->getAttribute('name'), ENT_QUOTES, 'UTF-8');
		$options = array();
        $optFilters = array();
        $optionTable = '';
        $order = '';
		
		$itemOptions = $item->getElementsByTagName('options')->item(0);
        $optionList = $itemOptions->getElementsByTagName('option');
        $optionTable = htmlentities($itemOptions->getAttribute('table'), ENT_QUOTES, 'UTF-8');
        $fieldLabel = htmlentities($itemOptions->getAttribute('fieldlabel'), ENT_QUOTES, 'UTF-8');
        $fieldValue = htmlentities($itemOptions->getAttribute('fieldvalue'), ENT_QUOTES, 'UTF-8');
        $filterName = htmlentities($itemOptions->getAttribute('filtername'), ENT_QUOTES, 'UTF-8');
        $optFilter = htmlentities($itemOptions->getAttribute('optfilter'), ENT_QUOTES, 'UTF-8');
        
        if($optionTable != '' && $fieldLabel != '' && $fieldValue != ''){
            if($optionList->length > 0){
                foreach($optionList as $option)
                    $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = get_translation($option->nodeValue);
            }
            $order = htmlentities($itemOptions->getAttribute('order'), ENT_QUOTES, 'UTF-8');
            if($order != ''){
                $order_select = ','.str_ireplace(' asc', '', $order);
                $order_select = str_ireplace(' desc', '', $order_select);
            }else $order_select = '';
            
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
                    
                    $arr_fieldLabel = preg_split('/([^a-z0-9_]+)/i', $fieldLabel);
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
                $options[htmlentities($option->getAttribute('value'), ENT_QUOTES, 'UTF-8')] = get_translation($option->nodeValue);
        }
		$filters[$name] = new Filter($name, $label, $options, $order, $filterName, $optFilters);
	}
	return $filters;
}

/***********************************************************************
 * displayFilters() displays the filters fields in the listing
 *
 * @param $filters collection of filter objects
 *
 * @return void
 *
 */
function displayFilters($filters)
{
    foreach($filters as $filter){
        
        $label = $filter->getLabel();
        $name = $filter->getName();
        $options = $filter->getOptions();
        $value = $filter->getValue();
        $optFilters = $filter->getOptFilters();
        $filterName = $filter->getFilterName();
        
        $str_filter = ($filterName != '') ? ' data-filter="'.$filterName.'"' : '';
        
        echo '<select name="'.$name.'" id="'.$name.'" class="form-control input-sm"'.$str_filter.'>'."\n";
        echo '<option value="">- '.$label.' -</option>'."\n";
        foreach($options as $option){
            $key = key($options);
            $selected = ($value == $key) ? ' selected="selected"' : '';
            $rel = (is_array($optFilters) && isset($optFilters[$key])) ? ' rel="'.$optFilters[$key].'"' : '';
            echo '<option value="'.$key.'"'.$rel.' '.$selected.'>'.$options[$key].'</option>'."\n";
            next($options);
        }
        echo '</select>'."\n";
    }
}

/***********************************************************************
 * getSearchFieldsList() returns the colmuns name of the listing 
 *
 * @param $cols collection of colmun objects
 *
 * @return array
 *
 */
function getSearchFieldsList($cols)
{
    $list = array();
    foreach($cols as $col){
        if($col->getTable() == '') $list[] = $col->getName();
    }
    return $list;
}

/***********************************************************************
 * getSearchFieldsList() returns the value of the "order" attribute (config.xml)
 *
 * @return string
 *
 */
function getOrder()
{
    $file = 'config.xml';
    $dom = new DOMDocument();
    if(!$dom->load($file))
        die('Unable to load the XML file');

    $root = $dom->getElementsByTagName('module')->item(0);
    $list = $root->getElementsByTagName('list')->item(0);

    $order = $list->getAttribute('order');
    
    return $order;
}
