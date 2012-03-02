<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

class CommentsViews {
	
    function comments($request, $id=false) {
		$templateArr = array();
		
		$ids = explode(':', $id);
		
    	$q = Doctrine_Query::create()
				->from('Comment c')
        		->leftJoin('c.CommentMeta cm')
        		->where('c.content_type_id = ? AND c.object_pk = ?',$ids);
            
		$templateArr['comments'] = $q->execute();	  
          
        render_to_response('comments/comments.html', $templateArr);
    }	
    
    function index($page = 1) {
        $templateArr = array();
        
        $resultsPerPage = 2;
        
        $q = Doctrine_Query::create()
            ->from('Comment c')
            ->leftJoin('c.CommentMeta cm')
            ->where('c.status = ? AND c.is_removed = ?', array(1,0))
            ->orderBy('c.submit_date DESC');        
            
            
        $pagerLayout = new Doctrine_Pager_Layout(
            new Doctrine_Pager($q, $page, $resultsPerPage ),
            new Doctrine_Pager_Range_Sliding(array('chunk' => 5)),
            'https://www.hcgdamla-satis.com/hcg-damla-musteri-yorumlari/{%page_number}/'
        );
        
        $pagerLayout->setTemplate('[<a href="{%url}">{%page}</a>]');
        $pagerLayout->setSelectedTemplate('[{%page}]');
                    
        $pager = $pagerLayout->getPager();        
        
        $results = $pager->execute();

        $templateArr['pager_layout'] = $pagerLayout->display(array(), true);
        $templateArr['comments'] = $results->toArray(); 

    //print_r( $templateArr['comments']);
        
    //$commentsArr = array();
    //echo "ee-".memory_get_usage();

        
/*
        foreach ($comments as $commentValue) {
            $tmpArr = array();
            
            foreach ($commentValue['CommentMeta'] as $commentMetavalue) {
                $commentValue['CommentMeta'][$commentMetavalue['meta_key']] = $commentMetavalue['meta_value'];
            }
            
            $commentsArr[] = $commentValue;
            
        }
        */
            //$templateArr['comments'] = $commentsArr;  
        
        render_to_response('comments/index.html', $templateArr);
    }   
    
    function add() {
    	$returnData = array('status'=>'0');

        if($_POST['object_pk']){
        	
        	try {
        		$ct = ContentType::get_for_id($_POST['content_type_id']);
        		if ($ct){
        			$comment = new Comment($comment);
	            	$comment->fromArray($_POST);
	            	
	            	$comment->ContentType = $ct;
	            	$comment->submit_date = date("Y-m-d H:i:s");   
		            $comment->ip_address =  $_SERVER['REMOTE_ADDR'];
		            $comment->is_public = true;
		            $comment->user_id = $_SESSION['user']['id'];
		            
	                $comment->save();
	                $returnData['message'] = 'Yorumunuz kaydedildi';
        		}else{
        			$returnData['status'] = $_POST['content_type_id'];
                	$returnData['message'] = 'ContentType bulunamadı';
	            
        		}
        		
	            
	            
            } catch (Exception $e) {
            	$returnData['status'] = $e->getCode();
                $returnData['message'] = $e->getMessage();
            }
        }
        
        echo json_encode($returnData);
    }
    

    
    
    function admin_comments() {
        $templateArr = array('current_admin_menu'=>'comment', 'current_admin_submenu'=>'comment'); 

        $q = Doctrine_Query::create()
				->from('Comment c')
        		->leftJoin('c.CommentMeta cm');
            
            
		$cl = new ChangeList($q);
		$templateArr['cl'] = $cl;	  
            
        render_to_response('admin/change_list.html', $templateArr);
    }
    
    function admin_approve($id) {
        $templateArr = array('admin_menu_current'=>'menu_comments',
        'admin_submenu_current'=>'');  
                
        $comment = Doctrine::getTable('Comment')->find($id);
        
        if($comment){
            $comment->status = 1;
            $comment->save();
        }
        
        HttpResponseRedirect('/comments/admin/');        
    }
    
    function admin_unapprove($id) {
        $templateArr = array('admin_menu_current'=>'menu_comments',
        'admin_submenu_current'=>'');  
                
        $comment = Doctrine::getTable('Comment')->find($id);
        
        if($comment){
            $comment->status = 0;
            $comment->save();
        }
        
        HttpResponseRedirect('/comments/admin/');        
    }
    
    function admin_trash($id) {
        $templateArr = array('admin_menu_current'=>'menu_comments',
        'admin_submenu_current'=>'');  
                
        $comment = Doctrine::getTable('Comment')->find($id);
        
        if($comment){
            $comment->is_removed = true;
            $comment->save();
        }
        
        HttpResponseRedirect('/comments/admin/');        
    }
    
}