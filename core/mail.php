<?php

function get_mailer($mailer = false) {
	require_once("pjango/utils/phpmailer.php");
	
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