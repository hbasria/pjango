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
        $this->id = new HiddenField();
        $this->images = new HiddenField();

        $this->categories = new MultipleChoiceField(pjango_gettext("Post Categories"), PostCategory::findAllAsChoice($this->taxonomy));

        $this->status = new DropDownField(pjango_gettext("Post Status"),
                Post::get_status_choices());
        
        $this->pub_date = new DateField(__('Publish Date'));

        $this->title = new TextField(pjango_gettext("Post Title"), 35, 255);
        $this->slug = new TextField(pjango_gettext("Post Slug"), 35, 255);
        //$this->excerpt = new LargeTextField(pjango_gettext("Post Excerpt"), 5, 60);
        $this->content = new LargeTextField(pjango_gettext("Post Content"), 25, 75);
        //$this->template = new TextField(pjango_gettext("Post Template"), 35, 255);
        $this->weight = new TextField(pjango_gettext("Post Weight"), 10, 255);        	
    }
}