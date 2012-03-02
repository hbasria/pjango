<?php

class DatabaseError extends Exception {}	


function get_dsn($key = 'default') {
	$databases = pjango_ini_get('DATABASES');
	$confArr = $databases[$key];
	$dsn = '';
	
	$dsn .= $confArr['ENGINE'].'://';
	$dsn .= $confArr['USER'].':';
	$dsn .= $confArr['PASSWORD'].'@';
	$dsn .= $confArr['HOST'].'/';
	$dsn .= $confArr['NAME'];
	
	return $dsn;
}