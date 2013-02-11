<?php
namespace Pjango\Contrib\Post\Forms;
use \Pjango\Phorm,
\Pjango\Phorm\Fields\TextField,
\Pjango\Phorm\Fields\DropDownField;

class PostCategoryForm extends Phorm {
    private $taxonomy = 'post';

    public function __construct($taxonomy, $data=array()){
        $this->taxonomy = $taxonomy;
        parent::__construct($data);
    }

    protected function define_fields(){
        $this->parent_id = new DropDownField(__("Parent PostCategory"), \PostCategory::findAllAsChoice($this->taxonomy));
        $this->name = new TextField(__('PostCategory Name'), 25, 255);
        $this->slug = new TextField(__('PostCategory Slug'), 25, 255);
    }
}