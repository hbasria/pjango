<?php 
//{% get_pages "category_slug" %}
class Get_Pages_Tag extends H2o_Node {
    function __construct($argstring, $parser, $pos=0) {
    	$this->args = H2o_Parser::parseArguments($argstring);      

    	$this->shortcut = str_replace(':', '', $this->args[2]);

    }
    
    function render($context, $stream) {
    	
    	$postSlug = str_replace('"', '', $this->args[0]);    	
    	$postSlug = str_replace("'", '', $postSlug);

    	$post = Doctrine_Query::create()
        	->from('Post o')
        	->leftJoin('o.Translation t')
        	->where('t.slug = ?',array($postSlug))
        	->fetchOne();
    	
    	if($post){
    	    $context->push(array($this->shortcut => $post));
    	    
    	}
    }
}

H2o::addTag(array('get_pages'));
