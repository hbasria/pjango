<?php 
#{% post 'post-slug' as shortcut %}
class Post_Tag extends H2o_Node {
    function __construct($argstring, $parser, $pos=0) {
    	$this->args = H2o_Parser::parseArguments($argstring);      
    	$this->shortcut = str_replace(':', '', $this->args[2]);
    }
    
    function render($context, $stream) {
    	
    	$postSlug = str_replace('"', '', $this->args[0]);    	
    	$postSlug = str_replace("'", '', $postSlug);
    	
    	$post = Post::findBySlug($postSlug);
    	
    	if($post){
    	    $context->push(array($this->shortcut => $post));    	    
    	}
    }
}

H2o::addTag(array('post'));

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
            ->where('p.status = ? AND c.site_id = ?',array(POST::STATUS_PUBLISHED, pjango_ini_get('SITE_ID')))
            ->orderBy('p.weight DESC, p.created_at DESC');
        
        if(isset($this->params['taxonomy'])){
            $q->addWhere('c.taxonomy = ?', str_replace(array('"',"'"), '', $this->params['taxonomy']));
        }
        
        if(isset($this->params['category__slug'])){
            $q->addWhere('ct.slug = ?', str_replace(array('"',"'"), '', $this->params['category__slug']));
        }        
        
        $context->push(array($this->shortcut => $q->execute()));
    }
}


H2o::addTag(array('get_posts'));

/**
 * pjango post_section tag for setcion tag
 *
 * {% post_section "category_name" "date,image,title|35,excerpt" 10 %}
 *
 * @link       		http://www.pjangoproject.com/
 * @author			Hasan Basri Ateş <hbasria@4net.com.tr>
 */
class Post_Section_Tag extends H2o_Node {
	function __construct($argstring, $parser, $pos=0) {
		$this->args = H2o_Parser::parseArguments($argstring);
	}
	
	function clearQuotes($value){
		$value = str_replace(array('"',"'"), '', $value);
		return $value;
	}
	

	function render($contxt, $stream) {
		$contentType = ContentType::get_for_model('Post', 'Pjango\Contrib\Post');
		$pageLayout = Doctrine_Query::create()
			->from('PageLayout o')
			->where('o.site_id = ? AND o.content_type_id = ? AND o.is_active = 1', array(SITE_ID, $contentType->id))
			->fetchOne();	

		$slug = $this->clearQuotes($this->args[0]);
		$options = $this->clearQuotes($this->args[1]);
		$optionsArr = explode(',', $options);

		$limit = isset($this->args[2]) ? $this->args[2] : false;
		
		$q = Doctrine_Query::create()
			->from('Post p')
			->leftJoin('p.Translation pt')
			->leftJoin('p.Categories c')
			->leftJoin('c.Translation ct')
			->where('p.site_id = ? AND p.post_type = ?', array(SITE_ID, Post::TYPE_POST));			
		
		$category = PostCategory::findBySlug($slug, Post::TYPE_POST);		
		if ($category){
			$q->andWhere('c.lft >= ? AND c.rgt <= ?', array($category->lft, $category->rgt));
		}

		if($limit){
			$q->limit($limit);
		}
		
		$results = $q->execute();
		
		$htmlItems = array();

		foreach ($results as $resultItem) {
			$liElementItems = array();
			
			foreach ($optionsArr as $optionValue) {
				$optionLength = 255;
				$optionValueArr = explode('|', $optionValue);
				 				 
				if(count($optionValueArr)>1){
					$optionValue = $optionValueArr[0];
					$optionLength = $optionValueArr[1];
				}else {
					$optionValue = $optionValueArr[0];
				}

				if ($optionValue == 'date'){
					$dateValue = date(pjango_ini_get('DATE_FORMAT'), strtotime($resultItem->created_at));					
					
					if(count($optionValueArr)>1){
						$dateValue = date($optionLength, strtotime($resultItem->created_at));
					}
					
					$dateValueArr = explode(' ', $dateValue);
					$dateValueItems = array();
					for ($i = 0; $i < count($dateValueArr); $i++) {
						$dateValueItems[] = sprintf('<dl class="date%d">%s</dl>', $i+1, $dateValueArr[$i]);
					}
					
					$liElementItems[] = sprintf('<div class="date">%s</div>', implode('', $dateValueItems));					
				}

				if ($optionValue == 'image'){
					$liElementItems[] = sprintf('<div class="image"><a href="%s"><img src="%s"/></a></div>', $resultItem->get_absolute_url(), $resultItem->get_image_url());
				}

				if ($optionValue == 'title'){
					$liElementItems[] = sprintf('<div class="title"><a href="%s">%s</a></div>',
							$resultItem->get_absolute_url(),
							substr($resultItem->get_title(), 0, $optionLength));
				}

				if ($optionValue == 'excerpt'){
					$liElementItems[] = sprintf('<div class="excerpt">%s</div>', 
							substr($resultItem->get_excerpt(), 0, $optionLength));
				}
				
				if ($optionValue == 'content'){
					$liElementItems[] = sprintf('<div class="postcontent">%s</div>',
							substr($resultItem->get_content(), 0, $optionLength));
				}	

				if ($optionValue == 'gobutton'){
					$liElementItems[] = sprintf('<a href="%s" class="gobutton">%s</a>', $resultItem->get_absolute_url(), __('Go'));					
				}				

			}
			$htmlItems[] = '<li>'.implode('', $liElementItems).'</li>';
		}
		
		$htmlContent = '<div class="section post">';
		
		if($pageLayout->show_title){
			$htmlContent .= '<h2>'.__($pageLayout->title).'</h2>';
		}		
		$htmlContent .= '<div class="box-content">';
		$htmlContent .= '<ul>'.implode('', $htmlItems).'</ul>';
		$htmlContent .= '</div>';
		$htmlContent .= '<div class="buttons"><div class="right"><a href="'.pjango_ini_get('SITE_URL').'/post/">'.__('All Posts').' &raquo;</a></div></div>';			
		$htmlContent .= '<div style="clear:both;"></div>';
		$htmlContent .= '</div>';

		$stream->write($htmlContent);
	}
}

H2o::addTag(array('post_section'));




/**
 * pjango post_category_tree
 *
 * {% post_category_tree "main_category" %}
 *
 * @link       		http://www.pjangoproject.com/
 * @author			Hasan Basri Ateş <hbasria@4net.com.tr>
 */
class Post_Category_Tree_Tag extends H2o_Node {
	function __construct($argstring, $parser, $pos=0) {
		$this->args = H2o_Parser::parseArguments($argstring);
	}

	function clearQuotes($value){
		$value = str_replace(array('"',"'"), '', $value);
		return $value;
	}

	function render($contxt, $stream) {
		$contentType = ContentType::get_for_model('Post', 'Pjango\Contrib\Post');
		
		$slug = false;
		if(isset($this->args[0])){
			$slug = $this->clearQuotes($this->args[0]);
		}
		
		$q = Doctrine_Query::create()
			->from('PostCategory c')
			->leftJoin('c.Translation t')
			->where('c.site_id = ? AND c.taxonomy = ?', array(SITE_ID, Post::TYPE_POST));
		
		if($slug){
			$category = PostCategory::findBySlug($slug, Post::TYPE_POST);
			if ($category){
				$q->andWhere('c.lft >= ? AND c.rgt <= ?', array($category->lft, $category->rgt));
			}			
		}

		$results = $q->execute();

		//print_r($results->toArray());

		$htmlContent = '<div class="section post">';

		if($pageLayout->show_title){
			$htmlContent .= '<h2>'.__($pageLayout->title).'</h2>';
		}
		$htmlContent .= '<div class="box-content">';
		//$htmlContent .= '<ul>'.implode('', $htmlItems).'</ul>';
		$htmlContent .= '</div>';
		$htmlContent .= '<div class="buttons"><div class="right"><a href="'.pjango_ini_get('SITE_URL').'/post/">'.__('All Posts').' &raquo;</a></div></div>';
		$htmlContent .= '<div style="clear:both;"></div>';
		$htmlContent .= '</div>';

		//$stream->write($htmlContent);
	}
}

H2o::addTag(array('post_category_tree'));