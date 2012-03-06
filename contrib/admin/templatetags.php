<?php 


function recursive_call_user_func($obj, $objectArr){
	$tmpObj = $objectArr[0];
	array_shift($objectArr);
	
	if (count($objectArr) == 0){
	    try {
	        //FIXME saglam bi cozum olmadi
	        $obj = @call_user_func(array($obj, $tmpObj));
	    } catch (Exception $e) {
		    $obj = $obj->{$tmpObj};
	    }
	}else {
		$obj = recursive_call_user_func($obj->{$tmpObj}, $objectArr);
	}

	return $obj;        		
}

class Result_List_Tag extends H2o_Node {
    private $nodelist;
    
    function __construct($argstring, $parser, $position = 0) {
        $this->args = H2o_Parser::parseArguments($argstring);        

        $this->filename = 'admin/change_list_results.html';
        $this->nodelist = $parser->runtime->loadSubTemplate($this->filename, $parser->options);
        $parser->storage['templates'] = array_merge(
            $this->nodelist->parser->storage['templates'], $parser->storage['templates']
        );
        $parser->storage['templates'][] = $this->filename;
    }

    function render($context, $stream) {
        $cl = $context->resolve($this->args[0]);
        
        if (!$cl) return false;
        
        $resultHeaders = array();
        

        
        foreach ($cl->list_display as $field_name) {
        	$text = str_replace('_', ' ', $field_name);
        	$text = ucwords(strtolower($text));        	
            $text = pjango_gettext(trim($text));
            
            $new_order_type = 'asc';
            $new_order_type_arr = array('asc'=>'desc', 'desc'=>'asc');     

            
            
            if ($cl->order_field == $field_name){
            	$new_order_type = $new_order_type_arr[$cl->order_type];
            }
            
            
            $resultHeaders[] = array(
               'text' => $text,
               'sortable' => true,
               'url' => $cl->get_query_string(array('o'=>$field_name, 'ot'=>$new_order_type)),
               'class_attrib' => ' class="manage-column"',
            );
        }   

        //eğer action varsa header ekle
        if (is_array($cl->row_actions)){
        	$resultHeaders[] = array(
               'text' => '&nbsp;',
               'sortable' => false,
               'url' => '',
               'class_attrib' => ' class="manage-column"',
            );
        }
        
        
        $context['result_headers'] = $resultHeaders;
        
        $tableRowCellTemplate = '<td class="">%s</td>';
        
        $tableRows = array();

        foreach ($cl->result_list as $resultItem) {
        	
        	$identifier = implode('.', $resultItem->identifier());
        	
        	$tableRowCells = array('<td class="toggleSelection"><input type="checkbox" value="'.$identifier.'" /></td>');
        
        	

			//$cl->row_actions tanımlıysa yerleştir
			$rowActionTemplate = '<li><a href="%s" onClick="%s" class="%s">%s</a></li>';     
        	$rowActions = array();        
            if (is_array($cl->row_actions)){
                foreach ($cl->row_actions as $rowAction) {
                	$rowActionName = $rowAction;
                	$rowActionUrl = './'.$identifier.'/'.$rowActionName.'/';
                	$confirmTxt = '';
                	
                	if (is_array($rowAction)){
                		$rowActionName = $rowAction['name'];
                		$rowActionUrl = pjango_ini_get('SITE_URL').$rowAction['url'].$identifier.'/';
                	}
					
                	//eğer silme ise confirm koy
                	//FIXME silme komutunu kontrol et
                	if ($rowActionName == 'delete'){
                        $confirmTxt = "if ( confirm('Bu kaydı silmek istediğinizden eminmisiniz.') ) return true; else return false; ";
                    }
                    

                    $rowActions[] = sprintf($rowActionTemplate,$rowActionUrl,$confirmTxt,$rowActionName,'&nbsp;');                   
                }
            }

        	foreach ($cl->list_display as $columnName) {
        		$columnNameArr = explode('__', $columnName);
        		$cellTxt = '';
        		
        		
       		
        		if(count($columnNameArr)>1){
        			$cellTxt = recursive_call_user_func($resultItem, $columnNameArr);
        		}else {
	        		if(method_exists($resultItem, $columnNameArr[0])){
	                	$cellTxt = call_user_func(array($resultItem, $columnNameArr[0]));
	                }else {
	                	$cellTxt = $resultItem->{$columnNameArr[0]};	//$class->{$method}
	                }         			
        		}

        		$tableRowCells[] = sprintf($tableRowCellTemplate, $cellTxt);
        	}
        	
        	$tableRowCells[] = sprintf('<td class="alignRight horizontalActions"><ul class="rowActions">%s</ul></td>', implode('', $rowActions));
        	
        	
        	$tableRows[] = $tableRowCells;
        	
        }
        
        $context['results'] = $tableRows;
        
        /*
        
        
        $tmpResultList = $cl->result_list;//$cl->paginator->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
        
        foreach ($tmpResultList as $resultItem) {
            
            $rowActionsArr = array();
        
            if (is_array($cl->row_actions)){
                foreach ($cl->row_actions as $rowAction) {
                	$rowActionName = $rowAction;
                	$rowActionUrl = './'.$resultItem['o_id'].'/'.$rowActionName.'/';
                	$confirmTxt = '';
                	
                	if (is_array($rowAction)){
                		$rowActionName = $rowAction['name'];
                		$rowActionUrl = $GLOBALS['SITE_URL'].$rowAction['url'].$resultItem['o_id'].'/';
                	}
                	
                    
                    
                    if ($rowActionName == 'delete'){
                        $confirmTxt = "if ( confirm('Bu kaydı silmek istediğinizden eminmisiniz.') ) return true; else return false; ";
                    }
                    
                    
                    
                    $rowActionsArr[] = '<span class="'.$rowActionName.'"><a href="'.$rowActionUrl.'" onclick="'.$confirmTxt.'" title="'.$rowActionName.' this record">'.ucfirst($rowActionName).'</a></span>';
                }
            }
            
            $resultItems = array('<th scope="row" class="check-column"><input type="checkbox" name="result_items[]" value="'.$resultItem['o_id'].'" /></th>');
            
            $isFirstRow = true;
            foreach ($cl->list_display as $fieldName) {             
                $tmpFieldName = str_replace('.', '_', $fieldName);
                $tmpResultItem = '';
                
                if (in_array($fieldName, $cl->list_display_links)){
                    $tmpResultItem = sprintf('<strong><a class="row-title" href="%s">%s</a></strong>', 
                                           './'.$resultItem['o_id'].'/',
                                           $resultItem[$tmpFieldName]);
                }else{
                    
                	//print_r($resultItem->get_title());
                	
                	if(method_exists($resultItem,$tmpFieldName)){
                		$tmpResultItem = call_user_func(array($resultItem, $tmpFieldName));
                	}else {
                		$tmpResultItem = $resultItem[$tmpFieldName];	
                	}
                	
                	    
                }
                
                if ($isFirstRow){
                    $resultItems[] = sprintf('<td class="">%s<div class="row-actions">%s</div></td>', 
                                    $tmpResultItem, implode(' | ', $rowActionsArr));    
                }else {
                    $resultItems[] = sprintf('<td class="">%s<div class="row-actions">%s</div></td>', 
                                    $tmpResultItem, '');   
                }
                
                
                              
                
                $isFirstRow = false;
            }
            
            
            $results[] = $resultItems;
            
            
        }
        
        $context['results'] = $results;
        
        */
        
        $this->nodelist->render($context, $stream);
    }
}

class Get_Admin_Menu_Tag extends H2o_Node {

	function __construct($argstring, $parser, $position = 0) {
        $this->args = H2o_Parser::parseArguments($argstring);        
    }

    function render($context, $stream) {
    	global $site;

    	$menuTemplate = '<li class="%s"><div class="left"><div class="right"><a href="%s">%s</a></div></div></li>';    	
    	$html = '';
    	$adminMenuArr = array(
    	array('home', pjango_gettext('Home'), '/admin/'));    	    	
    	
    	$currentMenuName = $context->resolve($this->args[0]);
    	
    	foreach ($site->_registry as $value) {
    		$modelAdmin = new $value;
    		
    		if ($modelAdmin instanceof ModelAdmin){
    			if ($modelAdmin->admin_menu){
    				$adminMenuArr[] = $modelAdmin->admin_menu;	
    			}
    		}
    	}

    	$i = 0;
    	$currentMenuId = false;
    	$adminMenuCount = count($adminMenuArr);
    	foreach ($adminMenuArr as $adminMenuItem) {
    		$menuName = $adminMenuItem[0];
    		$menuUrl = pjango_ini_get('SITE_URL').$adminMenuItem[2];
    		$menuValue = $adminMenuItem[1];
    		
    		$currentMenuClass = 'passive';   		
    		
    	    if ($menuName == $currentMenuName){
    			$currentMenuClass = 'active';
    			$currentMenuId = $i;
    		}
    		
    		if ($i == 0) $currentMenuClass .= ' first';    		
    		if ($currentMenuId && $i == ($currentMenuId+1)) $currentMenuClass .= ' after-active';
    		if ($i == ($adminMenuCount-1)) $currentMenuClass .= ' last';
    		
    		
			
			$html .= sprintf($menuTemplate."\n", $currentMenuClass, $menuUrl, $menuValue);
			
			$i++;
    	}
    	
    	
		$stream->write($html);
    }
}

//{% get_admin_menu current_admin_menu current_admin_submenu %}
class Get_Admin_Submenu_Tag extends H2o_Node {

	function __construct($argstring, $parser, $position = 0) {
        $this->args = H2o_Parser::parseArguments($argstring);        
    }

    function render($context, $stream) {
    	global $site;
    	    	
    	$html = '';
    	$menuTemplate = '<li class="%s"><a href="%s">%s<span class="top"></span><span class="bottom"></span></a>%s</li>';
    	$menuArr = array();    	
    	
    	$currentMainMenuName = $context->resolve($this->args[0]);
    	$currentMenuName = $context->resolve($this->args[1]);
    	$currentSubMenuName = $context->resolve($this->args[2]);
    	
    	foreach ($site->_registry as $value) {
    		$modelAdmin = new $value;
    		
    		if ($modelAdmin instanceof ModelAdmin){
    			
    			if (is_array($modelAdmin->admin_menu) && $modelAdmin->admin_menu[0] == $currentMainMenuName){
    				$menuArr = $modelAdmin->admin_menu;
    				unset($menuArr[0]);
					unset($menuArr[1]);
					unset($menuArr[2]);
    			}
    		}
    	}
    	
    	$i = 0;
    	$currentMenuId = false;
    	$menuCount = count($menuArr);
    	foreach ($menuArr as $menuItem) {
    		$subMenuArr = array();
    		$subHtml = '';
    		
    		if (count($menuItem) > 3){
    			$subMenuArr = $menuItem;
    			unset($subMenuArr[0]);
				unset($subMenuArr[1]);
				unset($subMenuArr[2]);
				
				$j = 0;
				$currentSubMenuId = false;
				$subMenuCount = count($subMenuArr);
				foreach ($subMenuArr as $subMenuItem) {
		    		$subMenuName = $subMenuItem[0];
		    		$subMenuUrl = pjango_ini_get('SITE_URL').$subMenuItem[2];
		    		$subMenuValue = $subMenuItem[1];	

		    		$currentSubMenuClass = 'passive';  
		    		
				   	if ($subMenuName == $currentSubMenuName){
		    			$currentSubMenuClass = 'active';
		    			$currentSubMenuId = $i;
		    		}		    		
		    		
		    		if ($j == 0) $currentSubMenuClass .= ' first';    		
		    		if ($currentSubMenuId && $j == ($currentSubMenuId+1)) $currentSubMenuClass .= ' after-active';
		    		if ($j == ($subMenuCount-1)) $currentSubMenuClass .= ' last';	

		    		$subHtml .= sprintf($menuTemplate."\n", $currentSubMenuClass, $subMenuUrl, $subMenuValue, '');
		    		$j++;
					
				}
    		}
    		$menuName = $menuItem[0];
    		$menuUrl = pjango_ini_get('SITE_URL').$menuItem[2];
    		$menuValue = $menuItem[1];
    		
    		$currentMenuClass = 'passive';   	
    		$subMenuHtml = '';	
    		
    	    if ($menuName == $currentMenuName){
    			$currentMenuClass = 'active';
    			$currentMenuId = $i;
    			$subMenuHtml = '<ul class="navigation">'.$subHtml.'</ul>';
    		}
    		
    		if ($i == 0) $currentMenuClass .= ' first';    		
    		if ($currentMenuId && $i == ($currentMenuId+1)) $currentMenuClass .= ' after-active';
    		if ($i == ($menuCount-1)) $currentMenuClass .= ' last';
			$html .= sprintf($menuTemplate."\n", $currentMenuClass, $menuUrl, $menuValue, $subMenuHtml);
			
			$i++;
    	}
    	
    	
		$stream->write($html);
    }
}

class Admin_List_Filter_Tag extends H2o_Node {
	private $nodelist;

	function __construct($argstring, $parser, $position = 0) {
		$this->args = H2o_Parser::parseArguments($argstring);
	}

	function render($context, $stream) {
		$cl = $context->resolve($this->args[0]);
		if (!$cl) return false;
		
		$model = $cl->get_model();
		$filterArr = array();
		$conn = Doctrine_Manager::connection();
		
		foreach ($cl->list_filter as $list_filter_value) {
			$filter = array();
			$filterKeyArr = explode('__', $list_filter_value); //Categories__name
			

				
			if (count($filterKeyArr)>1){
			    $filterKey = $list_filter_value; //status
			    $filterKeyStr = ucfirst($filterKey); //Status
			    
// 				$q = Doctrine_Query::create()
// 					->select('DISTINCT(f.'.$filterKeyArr[1].') AS filterKey')
// 					->from($model.' o')
// 					->leftJoin('o.'.$filterKeyArr[0].' f');
				
// 				$stmt = $conn->prepare($q->getSqlQuery());
// 				$stmt->execute();
// 				$selecboxData = $stmt->fetchAll();
			}else {				    
			    $filterKey = $list_filter_value; //status
			    $filterKeyStr = ucfirst($filterKey); //Status			    

				//filtrelenecek sütunun DISTINCT ile alınmış listesi
				$q = Doctrine_Query::create()
					->select('DISTINCT(o.'.$filterKey.') AS filterKey')
					->from($model.' o');
			}
			
			$stmt = $conn->prepare($q->getSqlQuery());
			$stmt->execute();
			$selecboxData = $stmt->fetchAll();			
			
			$selecboxDataArr = array(array(
								'name'=>'All '.$filterKeyStr, 
								'value'=>$cl->get_query_string(array(), array($filterKey))));
			
			foreach ($selecboxData as $selecboxDataItem) {
			    $selecboxItemName = ucfirst($selecboxDataItem[0]);
			    $selecboxItemValue = $cl->get_query_string(array($filterKey=>$selecboxDataItem[0]));
			    $selecboxDataArr[] = array('name'=>$selecboxItemName, 'value'=>$selecboxItemValue);
			}			
			
			//eğer filtreleme yapılmış ise
			$selectedFilterKeyValue = isset($cl->params[$filterKey]) ? $cl->params[$filterKey] : false ;
			
			if ($selectedFilterKeyValue){
			    $filter['selected'] = ucfirst($selectedFilterKeyValue);
			}else {
			    $filter['selected'] = 'All '.$filterKeyStr;
			}			
			
			$filter['label'] = $filterKeyStr;
			$filter['key'] = $filterKey;
			$filter['selecbox_data'] = $selecboxDataArr;
			$filterArr[] = $filter;			
		}	
		
		$retVal = array();
		
		foreach ($filterArr as $filterArrvalue) {
			$liElem = '<li>';
			$liElem .= '<div class="label">'.$filterArrvalue['label'].'</div>';
			$liElem .= '<div class="dropDown"><span><span>'.$filterArrvalue['selected'].'</span></span>';
			$liElem .= '<div class="panel"><div><ul>';
			
			foreach ($filterArrvalue['selecbox_data'] as $selecboxItem) {
				$liElem .= '<li><a href="'.$selecboxItem['value'].'">'.$selecboxItem['name'].'</a></li>';
			}
			
			$liElem .= '</ul></div></div><div class="mask"></div></div></li>';
			$retVal[] = $liElem;
		}
		
		$stream->write(implode('', $retVal));
	}
}

class Account_Switcher_Tag extends H2o_Node {

    function __construct($argstring, $parser, $position = 0) {
        $this->args = H2o_Parser::parseArguments($argstring);
    }

    function render($context, $stream) {
        
        
        $html = '<li class="accountSwitcher">
        <div class="triggerContainer">
        <div class="switchTrigger">%s</div>
        <a accesskey="w" href="#"><u>W</u>orking as</a>
        </div>
        <div class="accountsPanel">
        <div class="switchTop">&nbsp;</div>
        <div class="switchMiddle">
        <div class="switchMiddleBody">
        <div class="label">Switch to</div>
        <div id="accountLoading"></div>
        <div class="result">
        <div>
        <ul id="accounts">
        <li style="display: none"></li>
        %s
        </ul>
        </div>
        </div>
        </div>
        </div>
        <div class="switchDown">&nbsp;</div>
        </div>
        </li>';
        
        $sites = Doctrine_Query::create()
            ->from('Site s')
            ->execute();        

        $sitesArr = array();
        $selectedsiteName = '';
        $siteId = pjango_ini_get('SITE_ID');
        foreach ($sites as $value) {
            $sitesArr[] = sprintf('<li><a href="?site=%d">%s</a></li>', $value->id, $value->name);
            
            if($siteId == $value->id) $selectedsiteName = $value->name;
        }
         
        $stream->write(sprintf($html, $selectedsiteName, implode('', $sitesArr)));        
    }
}

H2o::addTag(array('result_list'));
H2o::addTag(array('get_admin_menu'));
H2o::addTag(array('get_admin_submenu'));
H2o::addTag(array('admin_list_filter'));
H2o::addTag(array('account_switcher'));
