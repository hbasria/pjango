<?php 
use Pjango\Contrib\Admin\AdminSite;

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
        
        $tableRowCellTemplate = '<td class="%s">%s</td>';
        
        $tableRows = array();

        foreach ($cl->result_list as $resultItem) {
        	
        	$identifier = $resultItem->identifier();
        	if(is_array($identifier) && count($identifier)>0){
        		$identifier = implode('.', $resultItem->identifier());
        	}else {
        		$identifier =  $resultItem->id;
        	}

        	$tableRowCells = array('<td class="toggleSelection"><input type="checkbox" name="row_id[]" value="'.$identifier.'" /></td>');

			//$cl->row_actions tanımlıysa yerleştir
			//<a href="#" class="btn mini green-stripe">View</a>
			$rowActionTemplate = '<a href="%s" class="btn mini black" onClick="%s"><i class="icon-%s"></i>%s</a>';     
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
        		$cellClass = $columnName;
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

        		$tableRowCells[] = sprintf($tableRowCellTemplate, $cellClass, $cellTxt);
        	}
        	
        	$tableRowCells[] = sprintf('<td class="alignRight horizontalActions">%s</td>', implode('&nbsp;', $rowActions));
        	
        	
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
    	$menuTemplate = '<li class="%s"><a href="%s">%s</a></li>';    	
    	$html = '';
    	$adminMenuArr = array(array('home', pjango_gettext('Home'), '/admin/'));    	    	
    	
    	$currentMenuName = $context->resolve($this->args[0]);
    	if(!$currentMenuName){
    		$currentMenuName = 'home';
    	}
    	
    	$site = AdminSite::getInstance();
    	$registry = $site->getRegistry();
    	
    	$appsMenuArr = array();
    	
    	foreach ($registry as $appName => $modelArr) {
    		foreach ($modelArr as $modelName => $modelAdminClass) {
    			if(is_string($modelAdminClass)){
    				$modelAdmin = new $modelAdminClass;
    				if ($modelAdmin instanceof \Pjango\Core\ModelAdmin){
    					if ($modelAdmin->admin_menu){
    						$adminMenuArr[] = $modelAdmin->admin_menu;
    					}
    					if ($modelAdmin->apps_menu){
    						$appsMenuArr[] = $modelAdmin->apps_menu;
    					}
    				}    				
    			}   			
    		}
    	}

    	foreach ($adminMenuArr as $adminMenuItem) {
    		$menuName = $adminMenuItem[0];
    		$menuUrl = pjango_ini_get('SITE_URL').$adminMenuItem[2];
    		$menuValue = $adminMenuItem[1];
    		
    		$currentMenuClass = '';   		
    		
    	    if ($menuName == $currentMenuName){
    			$currentMenuClass = 'active';
    			$currentMenuId = $i;
    		}

    		$html .= sprintf($menuTemplate."\n", $currentMenuClass, $menuUrl, $menuValue);
    	}
    	
    	if(count($appsMenuArr)>0){
    		$appsHtml = '';
    		$appsSelected = '';
    		 
    		foreach ($appsMenuArr as $appsMenuItem) {
    			$menuName = $appsMenuItem[0];
    			$menuUrl = pjango_ini_get('SITE_URL').$appsMenuItem[2];
    			$menuValue = $appsMenuItem[1];
    			 
    			$currentMenuClass = '';
    			 
    			if ($menuName == $currentMenuName){
    				$currentMenuClass = 'active';
    				$appsSelected .= ': '.__($currentMenuName);
    			}
    			 
    			$appsHtml .= sprintf($menuTemplate."\n", $currentMenuClass, $menuUrl, $menuValue);
    		}
    		    		
    		$html .= '<li class="dropdown">
    				    	<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.__('Apps').$appsSelected.'<b class="caret"></b></a>
    				    	<ul class="dropdown-menu">'.$appsHtml.'</ul></li>'; 
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
    	$site = AdminSite::getInstance();
    	$headerTemplate = '<li class="nav-header">%s</li>';
    	$menuTemplate = '<li class="%s"><a href="%s">%s</a></li>';
    	
    	$currentMainMenuName = $context->resolve($this->args[0]);
    	$currentMenuName = $context->resolve($this->args[1]);
    	$currentSubMenuName = $context->resolve($this->args[2]);
    	
    	$site = AdminSite::getInstance();
    	$registry = $site->getRegistry();    	
    	
    	$menuArr = array();
    	
    	foreach ($registry as $appName => $modelArr) {
    		foreach ($modelArr as $modelName => $modelAdminClass) {
    			if(is_string($modelAdminClass)){
    				$modelAdmin = new $modelAdminClass;
    				
    				if ($modelAdmin instanceof \Pjango\Core\ModelAdmin){
    					if (is_array($modelAdmin->admin_menu) && $modelAdmin->admin_menu[0] == $currentMainMenuName){
    						$menuArr = array_slice($modelAdmin->admin_menu, 3);
    					}
    					if (is_array($modelAdmin->apps_menu) && $modelAdmin->apps_menu[0] == $currentMainMenuName){
    						$menuArr = array_slice($modelAdmin->apps_menu, 3);
    					}
    				}
    			}    			
    		}
    	}    	
    	
    	$navArray = array();
    	foreach ($menuArr as $menuItem) {
    		$navArray[] = sprintf($headerTemplate, $menuItem[1]);

    		if (count($menuItem) > 3){
    			$subMenuArr = array_slice($menuItem, 3);

				foreach ($subMenuArr as $subMenuItem) {
		    		$subMenuName = $subMenuItem[0];
		    		$subMenuUrl = pjango_ini_get('SITE_URL').$subMenuItem[2];
		    		$subMenuValue = $subMenuItem[1];

		    		$currentSubMenuClass = '';  
		    		
				   	if ($subMenuName == $currentSubMenuName){
		    			$currentSubMenuClass = 'active';
		    		}		

		    		$navArray[] = sprintf($menuTemplate, $currentSubMenuClass, $subMenuUrl, $subMenuValue);
				}
    		}
    		
    		
    		
    	}
    	
    	
		$stream->write(implode('', $navArray));
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
		
		foreach ($cl->list_filter as $filterItem) {
			$filter = array();
			$filterItemArr = explode('__', $filterItem); //Categories__name
			
			//join varsa
			if (count($filterItemArr)>1){
			    $filterKey = $filterItemArr[0].'__id'; //Categories__id
			    $filterKeyStr = ucfirst($filterKey); //Categories__name
			    
				$selecboxData = Doctrine_Query::create()
					->select('o.id, f.id, f.'.$filterItemArr[1].' AS filterKey')
					->from($model.' o')
					->leftJoin('o.'.$filterItemArr[0].' f')
					->groupBy('f.'.$filterItemArr[1])
					->execute(array(), Doctrine_Core::HYDRATE_NONE);				
			}else {				    
			    $filterKey = $filterItem; //status
			    $filterKeyStr = ucfirst($filterKey); //Status	

			    $selecboxData = Doctrine_Query::create()
					->select('o.id, o.'.$filterKey.' AS filterKey, o.'.$filterKey.' AS filterKey')
					->from($model.' o')
					->groupBy('o.'.$filterKey)
					->execute(array(), Doctrine_Core::HYDRATE_NONE);					    
			}
			
			$selecboxDataArr = array(array(
				'name'=>'All '.$filterKeyStr, 
				'value'=>$cl->get_query_string(array(), array($filterKey))));
			
			$selectedFilterKeyValue = false ;
			foreach ($selecboxData as $selecboxDataItem) {
				$selecboxItemName = ucfirst($selecboxDataItem[2]);
				
				//nuberik bir değerse başına filterkey ekle
				if (is_numeric($selecboxItemName)){
					$selecboxItemName = $filterKeyStr.' '.$selecboxItemName;
				}
				
			    $selecboxItemValue = $cl->get_query_string(array($filterKey=>$selecboxDataItem[1]));
			    $selecboxDataArr[] = array('name'=>$selecboxItemName, 'value'=>$selecboxItemValue);

			    if($cl->params[$filterKey] == $selecboxDataItem[1]){
			    	$selectedFilterKeyValue = $selecboxItemName ;
			    }
			}		
			
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
			$liElem = '<div class="btn-group">';
			$liElem .= '<button class="btn btn-mini">'.__($filterArrvalue['selected']).'</button>';
			$liElem .= '<button class="btn dropdown-toggle btn-mini" data-toggle="dropdown"><span class="caret"></span></button>';
			$liElem .= '<ul class="dropdown-menu">';

			foreach ($filterArrvalue['selecbox_data'] as $selecboxItem) {
				$liElem .= '<li><a href="'.$selecboxItem['value'].'">'.__($selecboxItem['name']).'</a></li>';
			}
			
			$liElem .= '</ul></div>';
			$retVal[] = $liElem;
		}
		
		$stream->write(implode('', $retVal));
	}
}

class Admin_List_Search_Tag extends H2o_Node {

	function __construct($argstring, $parser, $position = 0) {
		$this->args = H2o_Parser::parseArguments($argstring);
	}

	function render($context, $stream) {
		$cl = $context->resolve($this->args[0]);
		if (!$cl) return false;
		
		if(is_array($cl->search_fields) && count($cl->search_fields)>0){
			$value = isset($_GET['q']) ? $_GET['q'] : '';
			
			$retVal = '<form action="" method="get" class="form-search pull-right">
								<div class="input-append">
									<input name="q" type="text" value="'.$value.'" class="search-query">
									<button type="submit" class="btn">'.__('Search').'</button>
								</div>			
							</form>';
			
			$stream->write($retVal);			
		}		
	}
}

class Admin_Actions_Tag extends H2o_Node {	

	function __construct($argstring, $parser, $position = 0) {
		$this->args = H2o_Parser::parseArguments($argstring);
	}

	function render($context, $stream) {
		$cl = $context->resolve($this->args[0]);
		if (!$cl) return false;

		$html = '<div class="btn-group">';		
		$html .= '<button class="btn btn-primary btn-mini">'.__('Actions').'</button>';
		$html .= '<button class="btn btn-primary dropdown-toggle btn-mini" data-toggle="dropdown"><span class="caret"></span></button>';
		$html .= '<ul class="dropdown-menu">';		

		foreach ($cl->actions as $actionValue) {
			if($actionValue == 'divider'){
				$html .= '<li class="divider"></li>';
			}else {
				$html .= sprintf('<li><a href="./%s/">%s</a></li>', $actionValue, __('Action '.$actionValue));
			}
		}			
		
		
		$html .= '</ul>';		
		$html .= '</div>';

		$stream->write($html);
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
        $siteId = SITE_ID;
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
H2o::addTag(array('admin_list_search'));
H2o::addTag(array('admin_actions'));
H2o::addTag(array('account_switcher'));
