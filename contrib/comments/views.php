<?php
require_once 'pjango/shortcuts.php';

class CommentsViews {
    
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
        $templateArr = array();
        
        if($_POST['comment']){
            $comment = new Comment($comment);
            $comment->fromArray($_POST);
            
            $comment->status = 0; 
            $comment->submit_date = date("Y-m-d H:i:s");   
            $comment->ip_address =  $_SERVER['REMOTE_ADDR'];
            $comment->is_public = true;
            $comment->is_removed =false;

            $comment->CommentMeta[0]->meta_key = 'user_name';
            $comment->CommentMeta[0]->meta_value = $_POST['user_name'];
            
            $comment->CommentMeta[1]->meta_key = 'user_email';
            $comment->CommentMeta[1]->meta_value = $_POST['user_email'];
            
            $comment->CommentMeta[2]->meta_key = 'user_location';
            $comment->CommentMeta[2]->meta_value = $_POST['user_location'];
            
            $comment->CommentMeta[3]->meta_key = 'user_phone';
            $comment->CommentMeta[3]->meta_value = $_POST['user_phone'];
            
            try {
                $comment->save();
                $templateArr['succes_message'] = 'Yorumunuz kaydedildi';
            } catch (Exception $e) {
                $templateArr['error_message'] = $e->getMessage();
            }
        }
        
        render_to_response('comments/add.html', $templateArr);
    }
    

    
    
    function admin_index() {
        $templateArr = array('admin_menu_current'=>'menu_comments',
        'admin_submenu_current'=>'');   

        $q = Doctrine_Query::create()
            ->from('Comment c')
        ->leftJoin('c.CommentMeta cm')
            ->where('c.is_removed = ?', array(0))
            ->orderBy('c.submit_date DESC');
            
            
        $results = $q->execute();
        $comments = $results->toArray();    
        
    $commentsArr = array();
        

        foreach ($comments as $commentValue) {
            $tmpArr = array();
            
            foreach ($commentValue['CommentMeta'] as $commentMetavalue) {
                $commentValue['CommentMeta'][$commentMetavalue['meta_key']] = $commentMetavalue['meta_value'];
            }
            
            $commentsArr[] = $commentValue;
            
        }
        
        $templateArr['comments'] = $commentsArr;    
        
    
        render_to_response('comments/admin/index.html', $templateArr);
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