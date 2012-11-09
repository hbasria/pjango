<?php
namespace Pjango\Contrib\Post\Forms;
use \Pjango\Phorm,
\Pjango\Phorm\Fields\HiddenField,
\Pjango\Phorm\Fields\TextField,
\Pjango\Phorm\Fields\PasswordField,
\Pjango\Phorm\Fields\MultipleChoiceField,
\Pjango\Phorm\Fields\DropDownField,
\Pjango\Phorm\Fields\LargeTextField,
\Post,
\PostCategory;

class PostCategoryForm extends Phorm {
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