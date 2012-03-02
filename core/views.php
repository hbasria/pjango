<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

class CoreViews {
	
	function pjangoimage_addchange($request, $id = false) {
		$templateArr = array();
		
		$templateArr['extraheads'] = array(
		sprintf('<script type="text/javascript" src="%s/js/tinymce/plugins/imagemanager/js/mcimagemanager.js"></script>', pjango_ini_get('ADMIN_MEDIA_URL')),
		sprintf('<script type="text/javascript" src="%s/js/pjango_image_addchange.js"></script>', pjango_ini_get('ADMIN_MEDIA_URL')),
					'<style>.pjango_image.custom-state-active { background: #eee; }
			    	.pjango_image { float: left; width: 96px; padding: 0.4em; margin: 0 0.4em 0.4em 0; text-align: center; }
			    	.pjango_image h5 { margin: 0 0 0.4em; cursor: move; }
			    	.pjango_image a { float: right; }
			    	.pjango_image a.ui-icon-zoomin { float: left; }
			    	.pjango_image img { width: 100%; cursor: move; }</style>'
		);		
	
		$modelClass = 'PjangoImage';
		$formClass = $modelClass.'Form';
		$formData = array();
		
		if ($id){
			$formData['object_id'] = $id;
		}
		
		if ($request->POST){
			$form = new $formClass($request->POST);
		
			try {
				if (!$form->is_valid()) throw new Exception('There are incomplete required fields. Please complete them.');
				$formData = $form->cleaned_data();
				if(!$modelObj) $modelObj = new $modelClass();
				
				$modelObj->fromArray($formData);				
				$modelObj->save();
	
				Messages::Info(pjango_gettext('The operation completed successfully'));
				HttpResponseRedirect($_SERVER['HTTP_REFERER']);
			} catch (Exception $e) {
				Messages::Error($e->getMessage());
			}
			
		}		
		
		
		if (!$form) $form = new $formClass($formData);
		$templateArr['addchange_form'] = $form->as_list();
			
		render_to_response('admin/addchange.html', $templateArr);
	}
		
}