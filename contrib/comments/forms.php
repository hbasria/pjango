<?php 
require_once('pjango/forms.php');
require_once('pjango/forms/validators.php');

class CommentForm extends Form {
	protected function define_fields(){
		$this->content_type_id = new HiddenField();
		$this->object_pk = new HiddenField();
    	$this->comment = new LargeTextField("Yorum", 4, 35, array('required_validation'));
  	}
}