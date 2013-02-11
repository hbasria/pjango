<?php
namespace Pjango\Contrib\Post\Forms;
use \Pjango\Phorm,
\Pjango\Phorm\Fields\HiddenField,
\Pjango\Phorm\Fields\TextField,
\Pjango\Phorm\Fields\PasswordField,
\Pjango\Phorm\Fields\MultipleChoiceField,
\Pjango\Phorm\Fields\DropDownField,
\Pjango\Phorm\Fields\LargeTextField,
\Pjango\Phorm\Fields\DateField,
\Post,
\PostCategory;

class PostForm extends Phorm {
    private $taxonomy = 'Post';

    public function __construct($taxonomy, $data=array()){
        $this->taxonomy = $taxonomy;
        parent::__construct($data);
    }

    protected function define_fields(){ 
        $this->categories = new MultipleChoiceField(__("Post Categories"), PostCategory::findAllAsChoice($this->taxonomy));
        $this->status = new DropDownField(__("Post Status"), Post::get_status_choices());
        $this->pub_date = new DateField(__('Publish Date'), array(), array('class'=>'pDateField'));
        $this->title = new TextField(__("Post Title"), 35, 255);
        $this->slug = new TextField(__("Post Slug"), 35, 255);
        $this->excerpt = new LargeTextField(__("Post Excerpt"), 5, 60);
        $this->content = new LargeTextField(__("Post Content"), 15, 75, array(), array('class'=>'pLargeTextField pEditor'));
        $this->weight = new TextField(__("Post Weight"), 10, 255);        	
    }
}