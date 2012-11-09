<?php
namespace Pjango\Contrib\Admin;

class AdminSite {
    protected static $_instance;
    public $_registry = array();

    public static function getInstance(){
        if ( ! isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public static function autodiscover(){
        self::get_urls();
    }

    public function __construct(){
    
    }    
    
    public function getRegistry(){
        return $this->_registry;
    }    

    public function register($model_or_iterable, $admin_class=null){

        /*if (is_null($admin_class)) {
         $admin_class = ModelAdmin;
        }

        if ($model_or_iterable instanceof ModelAdmin){
        	
        }else {
        	
        }*/

        $this->_registry[$model_or_iterable] = $admin_class;
    }
    
    public static function get_urls(){     
        global $urlpatterns;   
        $urlpatterns = patterns('',
            array('^admin/$', 'Pjango\Contrib\Admin\Views\index'),            
            array('^admin/settings/(?P<category>.*)/$', 'Pjango\Contrib\Admin\Views\settings'),
        	
        	array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/$', 'Pjango\Contrib\Admin\Views\app_index'),
        	array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/settings/$', 'Pjango\Contrib\Admin\Views\app_model_settings'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/add/$', 'Pjango\Contrib\Admin\Views\app_addchange'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/edit/$', 'Pjango\Contrib\Admin\Views\app_addchange'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/delete/$', 'Pjango\Contrib\Admin\Views\app_delete'),
        	array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/meta/$', 'Pjango\Contrib\Admin\Views\app_meta'),
        	array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/files/$', 'Pjango\Contrib\Admin\Views\app_files'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/files/add/$', 'Pjango\Contrib\Admin\Views\app_files_addchange'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/files/(?P<files_id>\d+)/edit/$', 'Pjango\Contrib\Admin\Views\app_files_addchange'),
        	array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/files/(?P<files_id>\d+)/delete/$', 'Pjango\Contrib\Admin\Views\app_files_delete'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/images/$', 'Pjango\Contrib\Admin\Views\app_files'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/images/add/$', 'Pjango\Contrib\Admin\Views\app_files_addchange'),
            array('^admin/(?P<app_label>\w+)/(?P<model>\w+)/(?P<id>\d+)/images/(?P<image_id>\d+)/edit/$', 'Pjango\Contrib\Admin\Views\app_files_addchange')
        );
    

        return $urlpatterns;
    }    
    
    
    public function index(){
    
    }    
    

    
    
}