<?php 
#{% page 'page-slug' as shortcut %}
class Page_Tag extends H2o_Node {
    function __construct($argstring, $parser, $pos=0) {
    	$this->args = H2o_Parser::parseArguments($argstring);      
    	$this->shortcut = str_replace(':', '', $this->args[2]);
    }
    
    function render($context, $stream) {
        
        $argPrefix = substr($this->args[0], 0, 1);
        if($argPrefix == ':'){
            $postSlug = $context->resolve($this->args[0]);
        }else {
            $postSlug = str_replace('"', '', $this->args[0]);    	
        	$postSlug = str_replace("'", '', $postSlug);
        }        
    	
    	$post = Post::findBySlug($postSlug, 'Pages');
    	
    	if($post){
    	    $context->push(array($this->shortcut => $post));    	    
    	}
    }
}
H2o::addTag(array('page'));