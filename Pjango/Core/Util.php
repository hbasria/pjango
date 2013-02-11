<?php

function pjango_gettext($message) {
    return Pjango\PTrans::gettext($message);
}

function pjango_ngettext($param) {
    return Pjango\PTrans::gettext($message);
}

function __($message) {
    return Pjango\PTrans::gettext($message);
}

function get_mailer($mailer = false) {
    require_once("Pjango/Util/PHPMailer.php");

    $mail = new PHPMailer();
    $mail->CharSet   =   pjango_ini_get('DEFAULT_CHARSET');

    if ($mailer) {
        $mail->Mailer = $mailer;

        if ($mailer == 'smtp') {
            $mail->SMTPAuth   = true;
            //$mail->SMTPSecure = "ssl";
            $mail->Host       = pjango_ini_get('EMAIL_HOST');
            $mail->Port       = pjango_ini_get('EMAIL_PORT');
            $mail->Username   = pjango_ini_get('EMAIL_HOST_USER');
            $mail->Password   = pjango_ini_get('EMAIL_HOST_PASSWORD');
            	
            $mail->SetFrom(pjango_ini_get('DEFAULT_FROM_EMAIL'), pjango_ini_get('DEFAULT_FROM_EMAIL_NAME'));
            $mail->Subject    = pjango_ini_get('EMAIL_SUBJECT_PREFIX');
        }
    }

    return $mail;
}

function get_client_ip() {
    $ipaddress = '';
    
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

function stripslashes_deep($value){
	$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
	return $value;
}


function translation($obj, $param){
	$lng = pjango_ini_get('LANGUAGE_CODE');
	//if (!is_a($object, 'Doctrine_Collection')) return '';
	//TODO : kontrol yapılacka	
	
	$translation = $obj->toArray();	
	if (isset($translation[$lng])) return $translation[$lng][$param];
	
	$translation = reset($translation);
	return  $translation[$param];
}


function slugify($text){
	$text = str_replace('&', ' and ', $text);

	if(strlen($text) > 100) {
		$text = substr($text, 0, 100);
	}

	// convert all characters to ascii equivalent.
	$map = array(
		'/à|á|å|â/' 	=> 'a',
		'/è|é|ê|ẽ|ë/' 	=> 'e',
		'/ì|í|î/' 		=> 'i',
		'/ò|ó|ô|ø/' 	=> 'o',
		'/ù|ú|ů|û/' 	=> 'u',
		'/ç/' 			=> 'c',
		'/ñ/' 			=> 'n',
		'/ä|æ/' 		=> 'ae',
		'/ö/' 			=> 'o',
		'/ü/'			=> 'u',
		'/Ä/' 			=> 'A',
		'/Ü/' 			=> 'U',
		'/Ö/' 			=> 'O',
		'/ß/' 			=> 's',
		'/[^\w\s]/' 	=> ' ');

	// remove any non letter or digit
	$text = preg_replace(array_keys($map), array_values($map), $text);
	$text = preg_replace('~[^\w\d]+~u', '-', $text);

	// trim
	$text = trim($text, '-');

	// lowercase
	$text = strtolower($text);

	return $text;
}

