<?php 

class Messages {
        const DEBUG     = 1;    // Most Verbose
        const INFO      = 2;    // ...
        const WARN      = 3;    // ...
        const ERROR     = 4;    // ...
        const FATAL     = 5;    // Least Verbose
        const OFF       = 6;    // Nothing at all.
        
        public static $priority = Messages::INFO;
        
        public static function Info($line){
            self::Log( $line , Messages::INFO );
        }
        
        public static function Debug($line){
            self::Log( $line , Messages::DEBUG );
        }
        
        public static function Warn($line){
            self::Log( $line , Messages::WARN );    
        }
        
        public static function Error($line){
            self::Log( $line , Messages::ERROR );       
        }

        public static function Fatal($line){
            self::Log( $line , Messages::FATAL );
        }
        
        public static function Log($line, $priority){
            	$logItem = array(
            	   'time' => date("Y-m-d G:i:s"),
            	   'priority' => $priority,
            	   'message' => $line
            	);
            	
            	$_SESSION['MESSAGES'][] = $logItem;
        }
        
        public static function clear(){
        	$_SESSION['MESSAGES'] = array();
        }
        
        
}