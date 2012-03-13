<?php 

class Get_Post_Tag extends H2o_Node {
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

#{% get_posts as shortcut taxonomy:"post", category__slug:"" %}
class Get_Posts_Tag extends H2o_Node {
    function __construct($argstring, $parser, $pos=0) {
        $this->args = H2o_Parser::parseArguments($argstring);
        $this->shortcut = str_replace(':', '', $this->args[1]);
        $this->params = $this->args[2];
    }

    function render($context, $stream) {
        $q = Doctrine_Query::create()
            ->from('Post p')
            ->leftJoin('p.Translation t')
            ->leftJoin('p.Categories c')
            ->leftJoin('c.Translation ct')
            ->where('c.site_id = ?',array(pjango_ini_get('SITE_ID')))
            ->orderBy('p.weight ASC');
        
        if(isset($this->params['taxonomy'])){
            $q->addWhere('c.taxonomy = ?', str_replace(array('"',"'"), '', $this->params['taxonomy']));
        }
        
        if(isset($this->params['category__slug'])){
            $q->addWhere('ct.slug = ?', str_replace(array('"',"'"), '', $this->params['category__slug']));
        }        
        
        $context->push(array($this->shortcut => $q->execute()));
    }
}

H2o::addTag(array('get_post'));
H2o::addTag(array('get_posts'));