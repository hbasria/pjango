<?php

class PagesViews {
	
	function flatpage($request, $url) {
		$template = 'pages/default.html';
		$templateArr = array();
		
		$fp = Doctrine_Query::create()
			->from('Post o')
			->leftJoin('o.Translation t')
	        ->where('o.site_id = ? AND t.slug = ?',array(pjango_ini_get('SITE_ID'),$url))
			->fetchOne();
		
		$page = Doctrine_Query::create()
			->from('Post o')
			->leftJoin('o.Translation t')
	        ->where('o.id = ?',array($fp->id))
			->fetchOne();
        
        $templateArr['page'] = $page;
        
        if (strlen($page->template)>0)
        	$template = $page->template;
        
        
		render_to_response($template, $templateArr);
	}	

    function admin_changestatus($request, $id = false) {
    	
        $post = Doctrine::getTable('Post')->find($id);
        
        if ($post) {        	
        	try {
        		$post->status = $_POST['value'];
	        	$post->save();
	        	echo pjango_gettext("post.status ".$post->status);
        	} catch (Exception $e) {
        		//YOKBİŞE
        	}
        }	               
    }
	
}