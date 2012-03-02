<?php 

class Get_Comment_Form_Tag extends H2o_Node {
	 /*       
    [0] => :for
    [1] => :policy
    [2] => :as
    [3] => :policy_comment_form
    
    {% get_comment_form for event as comment_form %}
	*/
	function __construct($argstring, $parser, $position = 0) {
        $this->args = H2o_Parser::parseArguments($argstring); 
        $this->comment_form_context = substr($this->args[3], 1, strlen($this->args[3]));
    }

    function render($context, $stream) {
    	require_once(dirname(__FILE__).'/forms.php');
    	
    	$obj = $context->resolve($this->args[1]);
    	$object_pk = 0;
    	
    	if($obj){
    		$ct = ContentType::get_for_model(get_class($obj));
    		
    		$content_type_id = $ct->id;
    		$object_pk = $obj->id;
    		$commentData = array('content_type_id' => $content_type_id, 'object_pk' => $object_pk);
    	}
    	
    	$form = new CommentForm($commentData);
    	$context[$this->comment_form_context] = $form->as_list();
    	
	$html = '';
    $html .= '<script type="text/javascript">';
    $html .= 'jQuery(document).ready( function($) {';
    $html .= '	$("#comment_list").load("'.$GLOBALS['SITE_URL'].'/comments/'.$content_type_id.':'.$object_pk.'/");';
    $html .= '	$("#submit-comment").click(function() {';
    $html .= '		var serializedData = $("#comment_form").serialize();';
	$html .= '		$.ajax({';
	$html .= '		    type: "POST",';
	$html .= '		    url: "'.$GLOBALS['SITE_URL'].'/comments/add/",';
	$html .= '		    data: serializedData,';
	$html .= '		    dataType: "json",';
	$html .= '		    success: function(msg){';
	$html .= '		        if(parseInt(msg.status)==0){';
	$html .= '		        	$("#comment_list").load("'.$GLOBALS['SITE_URL'].'/comments/'.$content_type_id.':'.$object_pk.'/");';
	$html .= '		        }';
	$html .= '		    }';
	$html .= '		});';		  
	$html .= '		return false;';
    $html .= '	});';
    $html .= '	';
    $html .= '});';
    $html .= '</script>';
    $html .= '<div id="comment_list"></div>';
    	
    	
		$stream->write($html);
    }
}

###
#	Get the target URL for the comment form.
#
#   Example::
#
#   	<form action="{% comment_form_target %}" method="post">
###
class Comment_Form_Target_Tag extends H2o_Node {
	function __construct($argstring, $parser, $position = 0) {
    }

    function render($context, $stream) {
		$stream->write('');
    }
}


H2o::addTag(array('get_comment_form'));
H2o::addTag(array('comment_form_target'));
