<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';

function recursiveDelete($directory) {
    if (is_dir($directory)) {
        $handle = opendir($directory);
    }

    if (!$handle) {
        return false;
    }

    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            if (!is_dir($directory . '/' . $file)) {
                unlink($directory . '/' . $file);
            } else {
                recursiveDelete($directory . '/' . $file);
            }
        }
    }
    closedir($handle);
    rmdir($directory);
    return true;
}

function recursiveFolders($directory) {
    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
    $output = '';

    $output .= '<option value="' . substr($directory, strlen($uploadsPath)) . '">' . substr($directory, strlen($uploadsPath)) . '</option>';

    $directories = glob(rtrim(str_replace('../', '', $directory), '/') . '/*', GLOB_ONLYDIR);

    foreach ($directories  as $directory) {
        $output .= recursiveFolders($directory);
    }

    return $output;
}

function recursiveCopy($source, $destination) {
    $directory = opendir($source);

    @mkdir($destination);

    while (false !== ($file = readdir($directory))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($source . '/' . $file)) {
                recursiveCopy($source . '/' . $file, $destination . '/' . $file);
            } else {
                copy($source . '/' . $file, $destination . '/' . $file);
            }
        }
    }

    closedir($directory);
}

class FilemanagerViews {
	
	function index($request) {
	    $templateArr = array();
	    if (isset($_GET['field'])) {
	        $templateArr['field'] = $_GET['field'];
	    } else {
	        $templateArr['field'] = '';
	    }
	    	    
		render_to_response('filemanager/index.html', $templateArr);
	}
	
	function image($request) {
	    $uploadsUrl = pjango_ini_get('MEDIA_URL').'/uploads/';

	    if (isset($request->POST['image'])) {
	        echo $uploadsUrl.$request->POST['image'];
	    }
	}	

	function directory($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	    
	    if (isset($request->POST['directory'])) {
	        $directories = glob(rtrim($uploadsPath . str_replace('../', '', $request->POST['directory']), '/') . '/*', GLOB_ONLYDIR);

	        if ($directories) {
	            $i = 0;
	            	
	            foreach ($directories as $directory) {
	                $json[$i]['data'] = basename($directory);
	                $json[$i]['attributes']['directory'] = substr($directory, strlen($uploadsPath . ''));
	                	
	                $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);
	                	
	                if ($children)  {
	                    $json[$i]['children'] = ' ';
	                }
	                	
	                $i++;
	            }
	        }	        
	        
	    }
	    
	    echo json_encode($json);	    
	}	
	
	function files($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	    
	    $siteUrl = pjango_ini_get('SITE_URL');
	    $mediaUrl = pjango_ini_get('MEDIA_URL').'/uploads/';
	    
	 
	    
	    
	
	    if (isset($request->POST['directory']) && $request->POST['directory']) {
	        $directory = $uploadsPath . str_replace('../', '', $request->POST['directory']);
	    } else {
	        $directory = $uploadsPath;
	    }
	
	    $allowed = array(
				'.jpg',
				'.jpeg',
				'.png',
				'.gif'
	    );
	
	    $files = glob(rtrim($directory, '/') . '/*');
	
	    if ($files) {
	        foreach ($files as $file) {
	            if (is_file($file)) {
	                $ext = strrchr($file, '.');
	            } else {
	                $ext = '';
	            }
	
	            if (in_array(strtolower($ext), $allowed)) {
	                $size = filesize($file);
	
	                $i = 0;
	
	                $suffix = array(
							'B',
							'KB',
							'MB',
							'GB',
							'TB',
							'PB',
							'EB',
							'ZB',
							'YB'
	                );
	
	                while (($size / 1024) > 1) {
	                    $size = $size / 1024;
	                    $i++;
	                }
	                
	                $fileName = substr($file, strlen($uploadsPath));
	
	                $json[] = array(
							'file'     => substr($file, strlen($uploadsPath)),
							'filename' => basename($file),
							'size'     => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
							'thumb'    => $siteUrl.'/thumb'.$mediaUrl.$fileName
	                );
	            }
	        }
	    }
	
	    echo json_encode($json);	    
	}	
	
	function create($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	
	    if (isset($request->POST['directory'])) {
	        if (isset($request->POST['name']) || $request->POST['name']) {
	            $directory = rtrim($uploadsPath . str_replace('../', '', $request->POST['directory']), '/');
	
	            if (!is_dir($directory)) {
	                $json['error'] = pjango_gettext('Warning: Please select a directory!');	                
	            }
	
	            if (file_exists($directory . '/' . str_replace('../', '', $request->POST['name']))) {
	                $json['error'] = pjango_gettext('Warning: A file or directory with the same name already exists!');
	            }
	        } else {
	            $json['error'] = pjango_gettext('Warning: Please enter a new name!');
	        }
	    } else {
	        $json['error'] = pjango_gettext('Warning: Please select a directory!');	       
	    }
	
// 	    if (!$this->user->hasPermission('modify', 'common/filemanager')) {
// 	      		$json['error'] = pjango_gettext('error_permission');
// 	    }
	
	    if (!isset($json['error'])) {
	        mkdir($directory . '/' . str_replace('../', '', $request->POST['name']), 0777);
	        	
	        $json['success'] = pjango_gettext('Success: Directory created!');
	    }
	
	    echo json_encode($json);	    
	}
	
	public function delete($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	
	    if (isset($request->POST['path'])) {
	        $path = rtrim($uploadsPath . str_replace('../', '', $request->POST['path']), '/');
	
	        if (!file_exists($path)) {
	            $json['error'] = pjango_gettext('Warning: Please select a directory or file!');
	        }
	        	
	        if ($path == rtrim($uploadsPath, '/')) {
	            $json['error'] = pjango_gettext('Warning: You can not delete this directory!');
	        }
	    } else {
	        $json['error'] = pjango_gettext('Warning: Please select a directory or file!');
	    }
	
// 	    if (!$this->user->hasPermission('modify', 'common/filemanager')) {
// 	      		$json['error'] = pjango_gettext('error_permission');
// 	    }
	
	    if (!isset($json['error'])) {
	        if (is_file($path)) {
	            unlink($path);
	        } elseif (is_dir($path)) {
	            recursiveDelete($path);
	        }
	        	
	        $json['success'] = pjango_gettext('Success: Your file or directory has been deleted!');
	    }
	
	    echo json_encode($json);	
	}
	
	public function folders($request) {
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	    echo recursiveFolders($uploadsPath);
	}	
	
	function move($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	
	    if (isset($request->POST['from']) && isset($request->POST['to'])) {
	        $from = rtrim($uploadsPath . str_replace('../', '', $request->POST['from']), '/');
	        	
	        if (!file_exists($from)) {
	            $json['error'] = pjango_gettext('Warning: File or directory does not exist!');
	        }
	        	
	        if ($from == $uploadsPath) {
	            $json['error'] = pjango_gettext('Warning: Can not alter your default directory!');
	        }
	        	
	        $to = rtrim($uploadsPath . str_replace('../', '', $request->POST['to']), '/');
	
	        if (!file_exists($to)) {
	            $json['error'] = pjango_gettext('Warning: Move to directory does not exists!');
	        }
	        	
	        if (file_exists($to . '/' . basename($from))) {
	            $json['error'] = pjango_gettext('Warning: A file or directory with the same name already exists!');
	        }
	    } else {
	        $json['error'] = pjango_gettext('Warning: Please select a directory!');
	    }
	
// 	    if (!$this->user->hasPermission('modify', 'common/filemanager')) {
// 	      		$json['error'] = pjango_gettext('error_permission');
// 	    }
	
	    if (!isset($json['error'])) {
	        rename($from, $to . '/' . basename($from));
	        	
	        $json['success'] = pjango_gettext('Success: Your file or directory has been moved!');
	    }
	
	    echo json_encode($json);	
	}
	
	function copy($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	
	    if (isset($request->POST['path']) && isset($request->POST['name'])) {
	        if ((strlen(utf8_decode($request->POST['name'])) < 3) || (strlen(utf8_decode($request->POST['name'])) > 255)) {
	            $json['error'] = pjango_gettext('Warning: Filename must be a between 3 and 255!');
	        }
	
	        $old_name = rtrim($uploadsPath . str_replace('../', '', $request->POST['path']), '/');
	        	
	        if (!file_exists($old_name) || $old_name == $uploadsPath) {
	            $json['error'] = pjango_gettext('Warning: Can not copy this file or directory!');
	        }
	        	
	        if (is_file($old_name)) {
	            $ext = strrchr($old_name, '.');
	        } else {
	            $ext = '';
	        }
	        	
	        $new_name = dirname($old_name) . '/' . str_replace('../', '', $request->POST['name'] . $ext);
	
	        if (file_exists($new_name)) {
	            $json['error'] = pjango_gettext('Warning: A file or directory with the same name already exists!');
	        }
	    } else {
	        $json['error'] = pjango_gettext('Warning: Please select a directory or file!');
	    }
	
// 	    if (!$this->user->hasPermission('modify', 'common/filemanager')) {
// 	      		$json['error'] = pjango_gettext('error_permission');
// 	    }
	
	    if (!isset($json['error'])) {
	        if (is_file($old_name)) {
	            copy($old_name, $new_name);
	        } else {
	            recursiveCopy($old_name, $new_name);
	        }
	        	
	        $json['success'] = pjango_gettext('Success: Your file or directory has been copied!');
	    }
	
	    echo json_encode($json);	
	}
	
	function rename($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	
	    if (isset($request->POST['path']) && isset($request->POST['name'])) {
	        if ((strlen(utf8_decode($request->POST['name'])) < 3) || (strlen(utf8_decode($request->POST['name'])) > 255)) {
	            $json['error'] = pjango_gettext('Warning: Filename must be a between 3 and 255!');
	        }
	
	        $old_name = rtrim($uploadsPath . str_replace('../', '', $request->POST['path']), '/');
	        	
	        if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
	            $json['error'] = pjango_gettext('Warning: Can not rename this directory!');
	        }
	        	
	        if (is_file($old_name)) {
	            $ext = strrchr($old_name, '.');
	        } else {
	            $ext = '';
	        }
	        	
	        $new_name = dirname($old_name) . '/' . str_replace('../', '', $request->POST['name'] . $ext);
	
	        if (file_exists($new_name)) {
	            $json['error'] = pjango_gettext('Warning: A file or directory with the same name already exists!');
	        }
	    }
	
// 	    if (!$this->user->hasPermission('modify', 'common/filemanager')) {
// 	      		$json['error'] = pjango_gettext('error_permission');
// 	    }
	
	    if (!isset($json['error'])) {
	        rename($old_name, $new_name);
	        	
	        $json['success'] = pjango_gettext('Success: Your file or directory has been renamed!');
	    }
	
	    echo json_encode($json);	
	}
	
	
	public function upload($request) {
	    $json = array();
	    $uploadsPath = APPLICATION_PATH.'/media/uploads/';
	
	    if (isset($request->POST['directory'])) {
	        if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
	            if ((strlen(utf8_decode($_FILES['image']['name'])) < 3) || (strlen(utf8_decode($_FILES['image']['name'])) > 255)) {
	                $json['error'] = pjango_gettext('Warning: Filename must be a between 3 and 255!');
	            }
	            	
	            $directory = rtrim($uploadsPath . str_replace('../', '', $request->POST['directory']), '/');
	
	            if (!is_dir($directory)) {
	                $json['error'] = pjango_gettext('Warning: Please select a directory!');
	            }
	
	            if ($_FILES['image']['size'] > 300000) {
	                $json['error'] = pjango_gettext('Warning: File to big please keep below 300kb and no more than 1000px height or width!');
	            }
	
	            $allowed = array(
						'image/jpeg',
						'image/pjpeg',
						'image/png',
						'image/x-png',
						'image/gif',
						'application/x-shockwave-flash'
	            );
	
	            if (!in_array($_FILES['image']['type'], $allowed)) {
	                $json['error'] = pjango_gettext('Warning: Incorrect file type!');
	            }
	
	            $allowed = array(
						'.jpg',
						'.jpeg',
						'.gif',
						'.png',
						'.flv'
	            );
	
	            if (!in_array(strtolower(strrchr($_FILES['image']['name'], '.')), $allowed)) {
	                $json['error'] = pjango_gettext('Warning: Incorrect file type!');
	            }
	
	
	            if ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
	                $json['error'] = 'error_upload_' . $_FILES['image']['error'];
	            }
	        } else {
	            $json['error'] = pjango_gettext('Warning: Please select a file!');
	        }
	    } else {
	        $json['error'] = pjango_gettext('Warning: Please select a directory!');
	    }
	
// 	    if (!$this->user->hasPermission('modify', 'common/filemanager')) {
// 	      		$json['error'] = pjango_gettext('error_permission');
// 	    }
	
	    if (!isset($json['error'])) {
	        if (@move_uploaded_file($_FILES['image']['tmp_name'], $directory . '/' . basename($_FILES['image']['name']))) {
	            $json['success'] = pjango_gettext('Success: Your file has been uploaded!');
	        } else {
	            $json['error'] = pjango_gettext('Warning: File could not be uploaded for an unknown reason!');
	        }
	    }
	
	    echo json_encode($json);	
	}
	

}