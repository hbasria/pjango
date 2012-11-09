<?php 
namespace Pjango\Util;

class Messages {
        const DEBUG     = 1;    // Most Verbose
        const INFO      = 2;    // ...
        const WARN      = 3;    // ...
        const ERROR     = 4;    // ...
        const FATAL     = 5;    // Least Verbose
        const OFF       = 6;    // Nothing at all.
        
        public static $priority = self::INFO;
        
        public static function Info($line){
            self::Log( $line , self::INFO );
        }
        
        public static function Debug($line){
            self::Log( $line , self::DEBUG );
        }
        
        public static function Warn($line){
            self::Log( $line , self::WARN );    
        }
        
        public static function Error($line){
            self::Log( $line , self::ERROR );       
        }

        public static function Fatal($line){
            self::Log( $line , self::FATAL );
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