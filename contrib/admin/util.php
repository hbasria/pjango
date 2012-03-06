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
define('RESPONSE_TYPE_VAR', 'responsetype');
define('ERROR_FLAG', 'e');
define('SITE_VAR', 'site');

/*
$q = Doctrine_Query::create()
    ->from('Comment c')
    ->leftJoin('c.CommentMeta cm')
    ->where('c.status = ? AND c.is_removed = ?', array(1,0))
    ->orderBy('c.submit_date DESC');  
            
$cl = new ChangeList($q);

*/



class PagerLayoutWithArrows extends Doctrine_Pager_Layout {
	public function display($options = array(), $return = false){
		$pager = $this->getPager();
		$str = '';
		// First page
		$this->addMaskReplacement('page', '&laquo;', true);
		$options['page_number'] = $pager->getFirstPage();
		$str .= $this->processPage($options);
		// Previous page
		$this->addMaskReplacement('page', '&lsaquo;', true);
		$options['page_number'] = $pager->getPreviousPage();
		$str .= $this->processPage($options);
		// Pages listing
		$this->removeMaskReplacement('page');
		$str .= parent::display($options, true);
		// Next page
		$this->addMaskReplacement('page', '&rsaquo;', true);
		$options['page_number'] = $pager->getNextPage();
		$str .= $this->processPage($options);
		// Last page
		$this->addMaskReplacement('page', '&raquo;', true);
		$options['page_number'] = $pager->getLastPage();
		$str .= $this->processPage($options);
		// Possible wish to return value instead of print it on screen
		if ($return) {
			return $str;
		}
		echo $str;
	}
}




class ChangeList {
	private $model = null;
	private $model_admin = false;
	
    public $list_display = array();
    public $list_display_links = array();
    public $list_filter = array();
    public $has_filters = false;
    public $list_select_related = false;
    public $list_per_page = false;
    public $list_editable = array();
    public $search_fields = false;
    public $date_hierarchy = false;
    public $save_as = false;
    public $save_on_top = false;
    public $ordering = false;
    public $actions = array();    	
    public $row_actions = array();
	
    public $page_num;
    public $show_all;
    public $is_popup;
    public $params;
    public $order_field = false;
    public $order_type = false; 
    public $query; 
    public $query_set; 
    public $chunk_length = 5;
    
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
        preg_match('/FROM\s[a-zA-Z]{3,30}/i', $q->getDql() , $matches);
    	$this->model = trim(str_replace('FROM', '', $matches[0]));
    	
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
    	if ($list_per_page) $this->list_per_page = $list_per_page;
    	
    	if (is_array($this->list_filter)){
    		$this->has_filters = true;
    	}
    	

    	
        $this->params = $_GET;
        

    	$this->query_set = $q;
        $this->query = isset($this->params[SEARCH_VAR]) ? $this->params[SEARCH_VAR] : '';
    	$this->page_num = isset($this->params[PAGE_VAR]) ? $this->params[PAGE_VAR] : 1;
    	$this->order_field = isset($this->params[ORDER_VAR]) ? $this->params[ORDER_VAR] : false;
    	$this->order_type = isset($this->params[ORDER_TYPE_VAR]) ? $this->params[ORDER_TYPE_VAR] : false;    	 
        $this->show_all = in_array(ALL_VAR, $this->params);
        $this->is_popup = in_array(IS_POPUP_VAR, $this->params);
        $this->to_field = isset($this->params[TO_FIELD_VAR]) ? $this->params[TO_FIELD_VAR] : false;

        foreach (array(ALL_VAR, ORDER_VAR, ORDER_TYPE_VAR, PAGE_VAR, SEARCH_VAR, TO_FIELD_VAR, IS_POPUP_VAR, ERROR_FLAG, LANG_VAR, RESPONSE_TYPE_VAR, SITE_VAR) as $value) {
        	if (isset($this->params[$value])) unset($this->params[$value]);
        }
        
        $this->get_query_set();
        $this->get_results();
    }
    
    function get_ordering() {
        $orderingStr = false;
        
        if(!$this->order_field && $this->ordering){
            if (is_string($this->ordering)) $orderingStr = $this->ordering;
            if (is_array($this->ordering)) $orderingStr = $this->ordering[0];
       	    
    		if (substr($orderingStr, 0,1) == '-'){
    			$this->order_field = substr($orderingStr, 1, strlen($orderingStr));
    			$this->order_type = 'desc';
        	}else{
        		$this->order_field = $orderingStr;
        		$this->order_type = 'asc';
        	}                 
        }
    	
    	if($this->order_field)
    	    return array($this->order_field, $this->order_type);
    	else return false;
    	
    	

    }
    
    function get_query_set() {

        if ($this->list_display){
            //$this->query_set->select('id, '.implode($this->list_display, ', '));
        }
            	
    	foreach ($this->params as $key => $value) {
    		$paramsKeyArr = explode('__', $key);
    		
    		//?t1__id=24 gibi
    		if (count($paramsKeyArr)>1){
    			$this->query_set->addWhere($paramsKeyArr[0].'.'.$paramsKeyArr[1].' = ?', $value);
    		}else {
    			$this->query_set->addWhere($this->query_set->getRootAlias().'.'.$key.' = ?', $value);
    		}
    		
    		
    	}
    	
    	# Set ordering.    	
    	$ordering = $this->get_ordering();
    	
    	if ($ordering){
    		$this->query_set->orderBy($ordering[0].' '.$ordering[1]);
    	}

    	if($this->search_fields){
    	    
        if (isset($_POST[SEARCH_VAR]) || isset($_GET[SEARCH_VAR])){
            
        	$searchQ = isset($_POST[SEARCH_VAR]) ? $_POST[SEARCH_VAR] : '';
        	$searchQ = isset($_GET[SEARCH_VAR]) ? $_GET[SEARCH_VAR] : '';
        	
        	
        	$searchFields = array();
        	foreach ($this->search_fields as $value) {
        		$search_fields_arr = explode('__', $value);
        	
        		if(count($search_fields_arr)>1){
//         			$searchFields[] = $value." LIKE '%".$searchQ."%'";
        		}else {
        			$searchFields[] = $value." LIKE '%".$searchQ."%'";
        		}
        	
        		//FIXME $_POST[SEARCH_VAR] kontrol edilmeli
        		      
        	}      	
	        
	        
        	$this->query_set->addWhere('('.implode(' OR ', $searchFields).')');
        }
        
    	}
        
        
        return $this->query_set;
    }
    
    function get_query_string($new_params=array(), $remove=array()){
    	$p = $this->params;    	
    	
    	foreach ($new_params as $key => $value) {
    		$p[$key] = $value;
    	}
    	
    	foreach ($remove as $removeItem) {
    		unset($p[$removeItem]);
    	}    	
    	
    	return '?'.http_build_query($p);
    }
    
    function get_results() {
    	
    	
    	
    	$pagerParams = '';
    	$pagerParamsArr = array();
    	
		foreach ($this->params as $key => $value) {
    		$pagerParamsArr[] = $key.'='.$value;
    	}  

    	$pagerParams = implode('&', $pagerParamsArr);
    	
    	//PagerLayoutWithArrows   Doctrine_Pager_Layout
    	$pagerLayout = new PagerLayoutWithArrows(
            new Doctrine_Pager($this->query_set, $this->page_num, $this->list_per_page),
            new Doctrine_Pager_Range_Sliding(array('chunk' => $this->chunk_length)),
            '?p={%page_number}&'.$pagerParams
        );
        
        $pagerLayout->setTemplate('<a href="{%url}">{%page}</a>');
        $pagerLayout->setSelectedTemplate('<span>{%page}</span>');
        
        
                    
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