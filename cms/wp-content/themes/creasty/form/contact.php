<?php

if(!defined('CONTACT_FORM') || !CONTACT_FORM)
	exit;

define('CONTACT_FORM_TO', 'contact@creasty.com');

mb_language('ja');
session_start();

if(wp_verify_nonce($_POST['cf-nonce'], 'contactform')){
	
	function verify_form(){
		extract(array(
			'name' => trim($_POST['cf-name']),
			'email' => is_email(sanitize_email(trim($_POST['cf-email']))),
			'url' => trim($_POST['cf-url']),
			'message' => trim($_POST['cf-message']), 
			'captcha' => strtolower(trim($_POST['cf-captcha'])),
		));
		
		$validation = array();
		$send = false;
		$error = 0;
		
		if(empty($name) || strlen($name) < 3){
			$error++;
			$validation['cf-name'] = '名前を入力してください。';
		}else{
			$validation['cf-name'] = true;
		}
		
		if(!$email){
			$error++;
			$validation['cf-email'] = '有効なメールアドレスを入力してください。';
		}else{
			$validation['cf-email'] = true;
		}
		
		if(empty($message)){
			$error++;
			$validation['cf-message'] = 'メッセージを入力してください。';
		}else{
			$validation['cf-message'] = true;
		}
		
		if(empty($captcha) || $captcha != $_SESSION['captcha']){
			$error++;
			$validation['cf-captcha'] = '正しい文字を入力してください。';
		}else{
			$validation['cf-captcha'] = true;
		}
		
		/*	Mail Send
		-----------------------------------------------*/
		if($error == 0){
			$message = strip_tags($message);
			
			$date = date('Y年n月j日 l H:i:s');
			
			$headers = '';	
/*
$headers = <<<HEADER
From: webmaster@example.com
Reply-To: webmaster@example.com
HEADER;
*/
			$body = <<<MSG
送信日: $date
送信者: $name
メール: $email
サイト: $url
====================

$message
MSG;
			$body = wordwrap($body, 70);
			
			@mb_send_mail(CONTACT_FORM_TO, 'お問い合わせ', $body, $headers);
			$send = true;
		}
		
		if(isset($_POST['ajax_call'])){
			header('Content-type: application/json');
			echo json_encode(array(
				'mailsend' => $send,
				'validation' => $validation,
				'error' => $error
			));
			exit;
		}
	}
	
	verify_form();
	
}

?>