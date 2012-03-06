<?php 
require_once('pjango/forms.php');

class PostCategoryForm extends Form {
	private $taxonomy = 'post';
	
	public function __construct($taxonomy, $data=array()){
		$this->taxonomy = $taxonomy;
		parent::__construct($data);
	}	
	
	protected function define_fields(){
		$this->id = new HiddenField();

		$this->parent_id = new DropDownField(pjango_gettext("PostCategory.parent"),
				PostCategory::findAllAsChoice($this->taxonomy));

		$this->name = new TextField(pjango_gettext('PostCategory.name'), 25, 255);
		$this->slug = new TextField(pjango_gettext('PostCategory.slug'), 25, 255);
	}
}

class PostForm extends Form {
	private $taxonomy = 'post';
	
	public function __construct($taxonomy, $data=array()){
		$this->taxonomy = $taxonomy;
		parent::__construct($data);
	}
		
	protected function define_fields(){
		$this->id = new HiddenField();
		$this->images = new HiddenField();
		
		$this->categories = new MultipleChoiceField(pjango_gettext("post.categories"), PostCategory::findAllAsChoice($this->taxonomy));
		
		$this->status = new DropDownField(pjango_gettext("post.status"),
				Post::getStatusChoices());

		$this->title = new TextField(pjango_gettext("post.title"), 35, 255);
		$this->slug = new TextField(pjango_gettext("post.slug"), 35, 255);
		$this->excerpt = new LargeTextField(pjango_gettext("post.excerpt"), 5, 60);
		$this->content = new LargeTextField(pjango_gettext("post.content"), 25, 75);
		$this->template = new TextField(pjango_gettext("post.template"), 35, 255);
		$this->weight = new TextField(pjango_gettext("post.weight"), 10, 255);
		 
	}
}