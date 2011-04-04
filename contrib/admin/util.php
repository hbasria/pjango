<?php 

# The system will display a "Show all" link on the change list only if the
# total result count is less than or equal to this setting.
define('MAX_SHOW_ALL_ALLOWED', 200);


# Changelist settings
define('LANG_VAR', 'lng');
define('ALL_VAR', 'all');
define('ORDER_VAR', 'o');
define('ORDER_TYPE_VAR', 'ot');
define('PAGE_VAR', 'p');
define('SEARCH_VAR', 'q');
define('TO_FIELD_VAR', 't');
define('IS_POPUP_VAR', 'pop');
define('ERROR_FLAG', 'e');

/*
$q = Doctrine_Query::create()
    ->from('Comment c')
    ->leftJoin('c.CommentMeta cm')
    ->where('c.status = ? AND c.is_removed = ?', array(1,0))
    ->orderBy('c.submit_date DESC');  
            
$cl = new ChangeList($q);

*/


class ChangeList {
	private $model = null;
	private $model_admin = false;
	
    public $list_display = array();
    public $list_display_links = array();
    public $list_filter = array();
    public $list_select_related = false;
    public $list_per_page = false;
    public $list_editable = array();
    public $search_fields = array();
    public $date_hierarchy = false;
    public $save_as = false;
    public $save_on_top = false;
    public $ordering = false;
    public $actions = array();    	
    public $row_actions = array();
	
    var $page_num;
    var $show_all;
    var $is_popup;
    var $params;
    var $order_field;
    var $order_type; 
    var $query; 
    var $query_set; 
    var $chunk_length = 5;
    
    var $result_count = false;
    var $result_list = false;
    var $can_show_all = false;
    var $multi_page = false;
    var $paginator = false;
    var $paginator_display = '';
    
    //def __init__(self, request, model, list_display, list_display_links, list_filter, date_hierarchy, search_fields, list_select_related, list_per_page, list_editable, model_admin):
    function __construct($q, $list_display=false, $list_display_links=false, $list_filter=false, $date_hierarchy=false, $search_fields=false, $list_per_page=false, $row_actions=false) {
        global $site;
    	
    	
    	
    	//$className = ucwords(strtolower($parts[$partsSize-3])).ucwords(strtolower($parts[$partsSize-2]));
    	//$callable = array( $className, $methodName );    
        //if( @is_callable( $callable ) === true ){ 
        
    	//FROM
    	$dql = $q->getDql();
    	$fromPos = strpos('FROM', $dql);
    	$dql = substr($dql, $fromPos+5, strlen($dql));
    	$dqlArr = explode(' ', trim($dql));
    	
    	
    	
    	$this->model = $dqlArr[0];
    	
    	if (isset($site->_registry[$this->model])){
    		$modelAdmin = $site->_registry[$this->model];
    		$this->model_admin = new $modelAdmin;
    	}
    	
    	
    	if ($this->model_admin){
		    $this->list_display           = $this->model_admin->list_display;
		    $this->list_display_links     = $this->model_admin->list_display_links;
		    $this->list_filter            = $this->model_admin->list_filter;
		    $this->list_select_related    = $this->model_admin->list_select_related;
		    $this->list_per_page          = $this->model_admin->list_per_page;
		    $this->list_editable          = $this->model_admin->list_editable;
		    $this->search_fields          = $this->model_admin->search_fields;
		    $this->date_hierarchy         = $this->model_admin->date_hierarchy;
		    $this->save_as                = $this->model_admin->save_as;
		    $this->save_on_top            = $this->model_admin->save_on_top;
		    $this->ordering               = $this->model_admin->ordering;
		    $this->actions                = $this->model_admin->actions;        
		    $this->row_actions            = $this->model_admin->row_actions;
    	}
    	
    	if ($list_display) $this->list_display = $list_display;
    	if ($list_display_links) $this->list_display_links = $list_display_links;
    	if ($list_filter) $this->list_filter = $list_filter;
    	if ($date_hierarchy) $this->date_hierarchy = $date_hierarchy;
    	if ($search_fields) $this->search_fields = $search_fields;
    	if ($row_actions) $this->row_actions = $row_actions;
    	
    	
    	
        $this->params = $_GET;
    	$this->query_set = $q;
        $this->query = isset($_GET[SEARCH_VAR]) ? $_GET[SEARCH_VAR] : '';
    	$this->page_num = isset($_GET[PAGE_VAR]) ? $_GET[PAGE_VAR] : 1;
        $this->show_all = in_array(ALL_VAR, $_GET);
        $this->is_popup = in_array(IS_POPUP_VAR, $_GET);
        $this->to_field = isset($_GET[TO_FIELD_VAR]) ? $_GET[TO_FIELD_VAR] : false;

        foreach (array(ALL_VAR, ORDER_VAR, ORDER_TYPE_VAR, PAGE_VAR, SEARCH_VAR, TO_FIELD_VAR, IS_POPUP_VAR, ERROR_FLAG, LANG_VAR) as $value) {
        	if (isset($this->params[$value])) unset($this->params[$value]);
        }
        
        $this->get_query_set();
        $this->get_results();
    }
    
    function get_ordering() {
        
    }
    
    function get_query_set() {
    	

    	
        if ($this->list_display){
            //$this->query_set->select('id, '.implode($this->list_display, ', '));
        }
            	
    	foreach ($this->params as $key => $value) {
    		$this->query_set->addWhere($this->query_set->getRootAlias().'.'.$key.' = ?', $value);
    	}
    	


        if (isset($_POST[SEARCH_VAR])){
            
        	$searchFields = array();
	        foreach ($this->search_fields as $value) {
	        	//FIXME $_POST[SEARCH_VAR] kontrol edilmeli
	            $searchFields[] = $value." LIKE '%".$_POST[SEARCH_VAR]."%'";
	        }        	
        	$this->query_set->addWhere('('.implode(' OR ', $searchFields).')');
        }
        
        
        //Ordering date_hierarchy$q->orderBy($orderby)

        $ordering = array();
        if (is_string($this->date_hierarchy)){
            if (strpos($this->date_hierarchy, '-') == 0){
        	   $ordering[] = substr($this->date_hierarchy, 1, strlen($this->date_hierarchy)).' DESC';
        	}else{
        		$ordering[] = $this->date_hierarchy.' ASC';
        	}
        }
        
        if (count($ordering)>0){
        	$this->query_set->orderBy(implode(', ', $ordering));
        }
        
        return $this->query_set;
    }
    
    function get_results() {

    	$pagerLayout = new Doctrine_Pager_Layout(
            new Doctrine_Pager($this->query_set, $this->page_num, $this->list_per_page),
            new Doctrine_Pager_Range_Sliding(array('chunk' => $this->chunk_length)),
            '?p={%page_number}'
        );
        
        $pagerLayout->setTemplate('[<a href="{%url}">{%page}</a>]');
        $pagerLayout->setSelectedTemplate('[{%page}]');
        
        
                    
        $pager = $pagerLayout->getPager();   
        //$rs = $pager->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
        $rs = $pager->execute();
        

        
        $this->full_result_count = $pager->getNumResults();
        $this->result_list = $rs;        
        $this->result_count = count($this->result_list);
        $this->can_show_all = $this->result_count <= MAX_SHOW_ALL_ALLOWED;
        $this->multi_page = $this->result_count > $this->list_per_page;
        $this->paginator = $pager;
        $this->paginator_display = $pagerLayout->display(array(), true);
    }
    
    function get_model() {
    	return $this->model;
    }
    
    	
    
    
}