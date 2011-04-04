<?php 


function recursive_call_user_func($obj, $objectArr){
	$tmpObj = $objectArr[0];
	array_shift($objectArr);
	
	if (count($objectArr) == 0){
		$obj = $obj->{$tmpObj};
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
        
        $resultHeaders = array();
        
        foreach ($cl->list_display as $field_name) {
        	$text = str_replace('_', ' ', $field_name);
        	$text = ucwords(strtolower($text));        	
            $text = pjango_gettext($text);
            $resultHeaders[] = array(
               'text' => $text,
               'sortable' => false,
               'url' => '',
               'class_attrib' => ' class="manage-column"',
            );
        }       
        
        $context['result_headers'] = $resultHeaders;
        
        $tableRowCellTemplate = '<td class="">%s<div class="row-actions">%s</div></td>';
        
        $resultRows = array();
        
        
        foreach ($cl->result_list as $resultItem) {
        	$tableRowCells = array('<th scope="row" class="check-column"><input type="checkbox" name="result_items[]" value="'.$resultItem->id.'" /></th>');
        	

			//$cl->row_actions tanımlıysa yerleştir
        	$rowActions = array();        
            if (is_array($cl->row_actions)){
                foreach ($cl->row_actions as $rowAction) {
                	$rowActionName = $rowAction;
                	$rowActionUrl = './'.$resultItem->id.'/'.$rowActionName.'/';
                	$confirmTxt = '';
                	
                	if (is_array($rowAction)){
                		$rowActionName = $rowAction['name'];
                		$rowActionUrl = $GLOBALS['SITE_URL'].$rowAction['url'].$resultItem->id.'/';
                	}
					
                	//eğer silme ise confirm koy
                	//FIXME silme komutunu kontrol et
                	if ($rowActionName == 'delete'){
                        $confirmTxt = "if ( confirm('Bu kaydı silmek istediğinizden eminmisiniz.') ) return true; else return false; ";
                    }
                    
                    $rowActions[] = '<span class="'.$rowActionName.'"><a href="'.$rowActionUrl.'" onclick="'.$confirmTxt.'" title="'.$rowActionName.' this record">'.pjango_gettext(ucfirst($rowActionName)).'</a></span>';
                }
            }        	

            $isFirstColumn = true;
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

				if ($isFirstColumn){
                    $tableRowCells[] = sprintf($tableRowCellTemplate, $cellTxt, implode(' | ', $rowActions));
                }else {
                	$tableRowCells[] = sprintf($tableRowCellTemplate, $cellTxt, '');
                }
        		
                $isFirstColumn = false;
        	}
        	
        	$resultRows[] = $tableRowCells;
        	
        }
        
        $context['results'] = $resultRows;
        
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
    	
    	$html = '';
    	$adminMenuArr = array();
    	
    	$currentMenu = $context->resolve($this->args[0]);
    	$currentSubMenu = $context->resolve($this->args[1]);
    	
    	foreach ($site->_registry as $value) {
    		$modelAdmin = new $value;
    		
    		if ($modelAdmin instanceof ModelAdmin){
    			if ($modelAdmin->admin_menu){
    				$adminMenuArr[] = $modelAdmin->admin_menu;	
    			}
    			
    		}
    		
    	}

    	$menuItemCount = count($adminMenuArr);	
    	$menuItemCounter = 1;
    	foreach ($adminMenuArr as $adminMenuItem) {
    		
    		
    		$menuName = $adminMenuItem[0];
    		$menuUrl = $GLOBALS['SITE_URL'].$adminMenuItem[2];
    		$menuValue = $adminMenuItem[1];
    		
    		$subMenuItems = $adminMenuItem;
    		unset($subMenuItems[0]);
    		unset($subMenuItems[1]);
    		unset($subMenuItems[2]);
    		

    		$has_submenu = '';
    		
    		$first_menu_class = '';
    		$last_menu_class = '';
    		$current_menu_class = '';
    		
    		
    		
    		if ($menuItemCounter == 1) $first_menu_class = ' menu-top-first';
    		if ($menuItemCounter == $menuItemCount) $last_menu_class = ' menu-top-last';
    		
    		
    		if ($menuName == $currentMenu){
    			$current_menu_class = ' wp-has-current-submenu wp-menu-open';
    		}
    		
    		if (count($adminMenuItem)>3){
    			$has_submenu = ' wp-has-submenu';
    		}
    		
    		$html .= '<li class="'.$has_submenu.$current_menu_class.' menu-top menu-icon-'.$menuName.' '.$first_menu_class.$last_menu_class.'" id="menu-'.$menuName.'">';
			$html .= '<div class="wp-menu-image"><a href='.$menuUrl.'"><br /></a></div>';
			$html .= '<div class="wp-menu-toggle"><br /></div>';
			$html .= '<a href="'.$menuUrl.'" class="'.$has_submenu.$current_menu_class.' menu-top menu-icon-'.$menuName.' '.$first_menu_class.$last_menu_class.'">'.$menuValue.'</a>';
			
			
    	    if (count($adminMenuItem)>3){
    			$html .= '<div class="wp-submenu">';
    			$html .= '<div class="wp-submenu-head">'.$menuName.'</div>';
    			$html .= '<ul>';
    			
    			$subMenuItemCounter = 1;

    			foreach ($subMenuItems as $subMenuItem) {
		    		$subMenuName = $subMenuItem[0];
		    		$subMenuUrl = $GLOBALS['SITE_URL'].$subMenuItem[2];
		    		$subMenuValue = $subMenuItem[1];    
		    		
		    		$current_submenu_class = '';
    				$first_submenu_class = '';		    						

    				if ($subMenuItemCounter == 1){
    					$first_submenu_class = ' wp-first-item';
    				}  

    			    if ($subMenuName == $currentSubMenu){
    					$current_submenu_class = ' current';
    				}    				
    				
    				$html .= '<li class="'.$first_submenu_class.$current_submenu_class.'"><a href='.$subMenuUrl.' class="'.$first_submenu_class.$current_submenu_class.'">'.$subMenuValue.'</a></li>';
    				
    				$subMenuItemCounter++;
    			}
    			
    			
    			$html .= '</ul>';
    			$html .= '</div>';
    		}
			
			
    		
    		$html .= '</li>'."\n";
    		$menuItemCounter++;
    	}
    	
    	
		$stream->write($html);
    }
}

H2o::addTag(array('result_list'));
H2o::addTag(array('get_admin_menu'));
