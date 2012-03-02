<?php
require_once('pjango/forms.php');

class PageForm extends Form {
	protected function define_fields(){
		$this->id = new HiddenField();
		
		$this->status = new DropDownField(pjango_gettext("pages.Status"),
				Post::getStatusChoices());

		$this->title = new TextField(pjango_gettext("pages.Title"), 35, 255);
		$this->slug = new TextField(pjango_gettext("pages.Slug"), 35, 255);
		$this->excerpt = new LargeTextField(pjango_gettext("pages.Excerpt"), 5, 60);
		$this->content = new LargeTextField(pjango_gettext("pages.Content"), 25, 90);
		$this->template = new TextField(pjango_gettext("pages.Template"), 35, 255);
		 
	}
}