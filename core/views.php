<?php
require_once 'pjango/shortcuts.php';
require_once 'pjango/contrib/admin/util.php';
require_once 'pjango/http.php';
require_once 'pjango/utils/phpthumb.php';


class CoreViews {
    
    function thumb($request, $url = false) {
        header("Content-Type: image/png");
        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));        

        $w = isset($_GET['w']) ? $_GET['w'] : 100;
        $h = isset($_GET['h']) ? $_GET['h'] : 100;   

        if ($w > 1024) $w = 1024;
        if ($h > 1024) $h = 1024;
        
        $url = preg_replace('/(.*?)media\/uploads/i', '/media/uploads', $url);

        $file = APPLICATION_PATH."/".$url;
        $fileInfo = pathinfo($file);

        $cacheFile = sprintf('%s/cache/%s_%dx%d.%s', $fileInfo['dirname'], $fileInfo['filename'], $w, $h, $fileInfo['extension']);
            
        if (!is_file($cacheFile)){
    
            if(!is_dir($fileInfo['dirname'].'/cache')){
                @mkdir($fileInfo['dirname'].'/cache', 0744);
            }
            
            if (is_file($file)){
                $thumb = new PhpThumb($file);
                $thumb->resize($w, $h);
                $thumb->save($cacheFile, 'png');
            }              
        }
        
        if (is_file($cacheFile)){
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($cacheFile)) . ' GMT');
            readfile($cacheFile);
        }else {
            readfile(APPLICATION_PATH.'/media/img/no-image.jpg');
        }
    }
}