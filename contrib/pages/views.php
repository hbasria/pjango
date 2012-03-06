<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

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

	function admin_pages($request) {
		$templateArr = array('current_admin_menu' => 'pages',
				'current_admin_submenu' => 'pages',
				'title' => pjango_gettext('Pages')); 	
		
		$q = Doctrine_Query::create()
			->from('Post p')
			->where('p.post_type = ?', Post::TYPE_PAGE);
		
		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;		
		        
		render_to_response('admin/change_list.html', $templateArr);
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