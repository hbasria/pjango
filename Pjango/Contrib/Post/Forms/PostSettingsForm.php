<?php
namespace Pjango\Contrib\Post\Forms;
use Pjango\Phorm;
use Pjango\Phorm\Fields\DropDownField;
use Pjango\Phorm\Fields\BooleanField;
use Pjango\Phorm\Fields\TextField;
use Pjango\Phorm\Fields\LargeTextField;

class PostSettingsForm extends Phorm {
	protected function define_fields(){
		$this->is_active = new BooleanField(__('Module Is Active'));
		$this->title = new TextField(__('Module Title'), 65, 255);
		$this->show_title = new BooleanField(__('Show Module Title'));
		$this->category_id = new DropDownField(__('Module Position'), \PageLayoutCategory::findAllAsChoice());
		$this->content = new LargeTextField(__('Module Content'), 3, 65);	
	}
}