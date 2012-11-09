<?php
namespace Pjango\Contrib\Admin;

class PagerLayoutWithArrows extends \Doctrine_Pager_Layout {
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