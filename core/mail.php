<?php

function get_mailer($mailer = false) {
	require_once("class.phpmailer.php");
	
	$mail = new PHPMailer();
	$mail->CharSet   =   $GLOBALS['SETTINGS']['DEFAULT_CHARSET'];

	if ($mailer) {
		$mail->Mailer = $mailer;
		
		if ($mailer == 'smtp') {
			$mail->SMTPAuth   = true;
			$mail->SMTPSecure = "ssl";
			$mail->Host       = $GLOBALS['SETTINGS']['EMAIL_HOST'];
			$mail->Port       = $GLOBALS['SETTINGS']['EMAIL_PORT'];
			$mail->Username   = $GLOBALS['SETTINGS']['EMAIL_HOST_USER'];
			$mail->Password   = $GLOBALS['SETTINGS']['EMAIL_HOST_PASSWORD'];
		}
	}
	
	return $mail;
}