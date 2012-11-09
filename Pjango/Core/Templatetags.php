<?php 

use Pjango\Util\Messages;

class Get_Messages_Tag extends H2o_Node {
    function __construct($argstring, $parser, $pos=0) {

    }
    
    function render($contxt, $stream) {
        $messages = array();
        $messagesTemplate = ' 
        		<div class="messagePlaceholder" id="messagePlaceholder">
	      			<div class="message localMessage">
	      				<div class="%s">
	      					<div class="icon"></div>
		      				%s
		      				<div class="topleft"></div>
		      				<div class="topright"></div>
		      				<div class="bottomleft"></div>
		      				<div class="bottomright"></div>
		      				<div class="close">x</div>
	      				</div>
	      			</div>
	      		</div>';
        
        $messagesClass = 'confirm';
        if (is_array($_SESSION['MESSAGES'])){
            foreach ($_SESSION['MESSAGES'] as $value) {
                $messages[] = '<p>'.$value['message'].'</p>';
                
                if ($value['priority'] == Messages::ERROR) $messagesClass = 'error';
            }
        }
        
        
        
        if (count($messages)>0){
            Messages::clear();
            $stream->write(sprintf($messagesTemplate, $messagesClass, implode('', $messages)));
        }
        
    }
}

H2o::addTag(array('get_messages'));


class Meta_Data_Tag extends H2o_Node {
    function __construct($argstring, $parser, $pos=0) {
    	$this->args = H2o_Parser::parseArguments($argstring);      

    	$this->shortcut = str_replace(':', '', $this->args[2]);

    }
    
    function render($context, $stream) {
    	
    	$obj = $context->resolve($this->args[0]);    	
    	$ct = ContentType::get_for_model(get_class($obj));
    	
    	$metaDataArr = array();
    	$metaData = PjangoMeta::getMeta($ct->id, $obj->id);    	
    	
    	
    	
    	foreach ($metaData as $metaDataItem) {
    		$metaDataArr[$metaDataItem->meta_key] = $metaDataItem->meta_value;
    	}    	    	    	

    	$context->push(array($this->shortcut => $metaDataArr));
    }
}

H2o::addTag(array('meta_data'));
