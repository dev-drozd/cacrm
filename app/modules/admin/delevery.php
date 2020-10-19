<?php

defined('ENGINE') or ('hacking attempt!');

/* $users = [
	'admin@kabuljan.af',
	'danovan@yoursite.com',
	'kevinandrew5365@yahoo.com',
	'joseph.stalin@hotmail.com',
	'poeticchaos13@yahoo.com',
	'callmeoprah@gmail.com',
	'eliothernandez67@gmail.com',
	'miucp@yoursite.com',
	'kuptjukvm@gmail.com',
	'dev.drozd@gmail.com'
]; */


$users = [
	'dcspcdr@yahoo.com',
	'kuptjukvm@gmail.com',
	'dev.drozd@gmail.com'
];

foreach($users as $email){
	
	$password = substr(md5(uniqid()), 0, 6);
	
	$name = explode('@', $email);
	
	$sql = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\'');

	if(!$sql['count']){
		db_query('INSERT INTO `'.DB_PREFIX.'_users` SET
			hid = \''.md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time()).'\',
			password = \''.md5(md5($password)).'\',
			name = \''.$name[0].'\', 
			email = \''.$email.'\', 
			group_ids = 2'
		);	
	} else {
		db_query('UPDATE `'.DB_PREFIX.'_users` SET
			hid = \''.md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time()).'\',
			password = \''.md5(md5($password)).'\' WHERE email = \''.$email.'\''
		);
	}
	
	// Headers
	$headers  = 'MIME-Version: 1.0'."\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	$headers .= 'To: '.$email. "\r\n";
	$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
	
	$message = '<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>New user on Your Company</title>
	</head>
	<body style="background: #f6f6f6; text-align: center;">
		<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
			<div style="margin: -20px -20px 0; padding: 20px;">
				<a href="http://tfd-dev.yoursite.com/">
					<img src="http://tfd-dev.yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
				</a>
			</div>
			<div style="padding: 0 30px 30px;">
				<p>You have applied for open beta testing of new platform for <br><b>Your Company inc</b>.</p>
				<p>Please use the details below for entrance to new platform:</p>
				<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
					Link: <a href="http://tfd-dev.yoursite.com/admin/" style="color: #0e92d4;">Click here</a><br>
					Login: '.$email.'<br>
					Password: '.$password.'
				</div>
			</div>
		</div>
	</body>
	</html>';

	// Send
	mail($email, 'Welcome to the Your Company', $message, $headers);
}
echo 'OK';
die;
?>