<?php

class DatabaseError extends Exception {}	


function get_dsn($key = 'default') {
	$confArr = $GLOBALS['DATABASES'][$key];
	$dsn = '';
	
	$dsn .= $confArr['ENGINE'].'://';
	$dsn .= $confArr['USER'].':';
	$dsn .= $confArr['PASSWORD'].'@';
	$dsn .= $confArr['HOST'].'/';
	$dsn .= $confArr['NAME'];
	
	return $dsn;
}