<?php 


class ModelAdmin {
    private $model = false;
	
	public $list_display = array();
    public $list_display_links = array();
    public $list_filter = array();
    public $list_select_related = false;
    public $list_per_page = 10;
    public $list_editable = array();
    public $search_fields = array();
    public $date_hierarchy = false;
    public $save_as = false;
    public $save_on_top = false;
    public $ordering = false;
    public $actions = array();
    public $row_actions = array();   

    public $admin_menu = false;
    
    public function __construct($model){
        
        
    }

}

