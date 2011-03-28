<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';


class FlatpagesViews {
	
	function flatpage($url) {

		$q = Doctrine_Query::create()
		->from('FlatPage o')
		->leftJoin('o.Translation t')
        ->where('t.slug = ?',array($url));

		$fp = $q->fetchOne();
		
		$q2 = Doctrine_Query::create()
		->from('FlatPage o')
		->leftJoin('o.Translation t')
        ->where('o.id = ?',array($fp->id));
        
        $flatPage = $q2->fetchOne();
		
		render_to_response('flatpages/default.html', array('flatpage'=>$flatPage));
	}
	
	
	function admin_index() {
		$templateArr = array('admin_menu_current'=>'menu_flatpages',
		'admin_submenu_current'=>'submenu_pages'); 

		if (isset($_POST['action']) && $_POST['action'] == 'trash'){
			$q = Doctrine_Query::create()
			->delete('FlatPage o')
			->whereIn('id', $_POST['result_items']);
			
			$numDeleted = $q->execute();	

			Messages::Info($numDeleted.' kayıt silindi.');
		}
		
		$q = Doctrine_Query::create()
		    ->from('FlatPage o')
		    ->leftJoin('o.Translation t')
		    ->where('t.lang = ?',array($GLOBALS['LANGUAGE_CODE']));
		    
		
		    
        $cl = new ChangeList($q);
		$templateArr['cl'] = $cl;
		
		render_to_response('flatpages/admin/index.html', $templateArr);
	}
	
    function admin_addchange($id = false) {
        $templateArr = array('admin_menu_current'=>'menu_flatpages',
        'admin_submenu_current'=>'submenu_addchange');   

 
        
        if (isset($_POST['publish'])){
        	
        	$flatPage = Doctrine::getTable('FlatPage')->find($_POST['id']);
        	
        	if(!$flatPage){
        		$flatPage = new FlatPage();
        	}
        	
        	try {
        		$flatPage->status = $_POST['publish'];
        		$flatPage->fromArray($_POST);
        		$flatPage->Translation[$GLOBALS['LANGUAGE_CODE']]->title = $_POST['title'];
        		$flatPage->Translation[$GLOBALS['LANGUAGE_CODE']]->content = $_POST['content'];
        		
        		$flatPage->save();
        		Messages::Info($_POST['title'].' isimli sayfa kaydedeldi.');
        		HttpResponseRedirect('/admin/fp/');        
        	} catch (Exception $e) {
        		Messages::Error($e->getMessage());
        	}
        	
        }
        
        if ($id){
            $q = Doctrine_Query::create()
                ->from('FlatPage o')
                ->leftJoin('o.Translation t')
                ->where('o.id = ?',array($id));
                
            //$flatPage = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SCALAR);
            $flatPage = $q->fetchOne();
            
            if($flatPage){
            	$templateArr['flat_page'] = $flatPage->toArray();
            }
        }   
         
        
        
        
        render_to_response('flatpages/admin/addchange.html', $templateArr);
    }	
    
    function admin_delete($id) {
        $templateArr = array('admin_menu_current'=>'menu_flatpages',
        'admin_submenu_current'=>'submenu_pages');  
                
        $o = Doctrine::getTable('FlatPage')->find($id);
        
        if($o){
        	try {
        	   $o->delete();	
        	   Messages::Info('1 kayıt silindi.');     	   
        	} catch (Exception $e) {
        		Messages::Error($e->getMessage());
        	}
            
        }
        
        HttpResponseRedirect('/admin/fp/');        
    }    
	
	
	

}
