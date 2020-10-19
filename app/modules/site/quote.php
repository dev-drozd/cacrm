<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$store_id = (int)$_POST['store'];
	$company = text_filter($_POST['company'], 255, false);
	$first_name = text_filter($_POST['name'], 100, false);
	$last_name = text_filter($_POST['lastname'], 100, false);
	$phone = text_filter($_POST['phone'], 17, false);
	$email = text_filter($_POST['email'], 100, false);
	$issue = text_filter($_POST['issue'], 500, false);
	$pathname = str_ireplace('https://yoursite.com','', urldecode($_SERVER['HTTP_REFERER']));
		
	if(($user && $issue) OR ($first_name && $last_name && $phone && $email && $issue)){
		$phone = str_replace(['(', ')', '-'], '', $phone);
		
		if (!$email OR !db_multi_query('SELECT id FROM `'.DB_PREFIX.'_users` WHERE email = \''.db_escape_string($email).'\'')){
			
			if(!$first_name){
				$first_name = $user['uname'];
				$last_name = $user['ulastname'];
				$phone = $user['phone'];
				$email = $user['email'];
			}
			
			$user_id = intval($user['id']);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_quote_requests` SET
				store_id = '.$store_id.',
				customer_id = \''.$user_id.'\',
				company = \''.$company.'\',
				first_name = \''.$first_name.'\',
				last_name = \''.$last_name.'\',
				phone = \''.$phone.'\',
				email = \''.$email.'\',
				issue = \''.$issue.'\',
				ip = \''.CLIENT_IP.'\',
				pathname = \''.db_escape_string($pathname).'\'
			');
			
			$id = intval(mysqli_insert_id($db_link));

			db_query('
				INSERT INTO `'.DB_PREFIX.'_notifications` SET 
					type = \'new_quote_request\', 
					customer_id = '.$user_id.', 
					id = '.$id
			);
			
			$message = '<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>Welcome to the Your Company</title>
			</head>
			<body style="background: #f6f6f6; text-align: center;">
				<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
					<div style="margin: -20px -20px 0; padding: 20px;">
						<a href="http://yoursite.com/">
							<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
						</a>
					</div>
					<div style="padding: 0 30px 30px;">
						<p>'.$first_name.' '.$last_name.' thank you for visiting our web site and we will contact to you with an estimate for repairing your device</p>
					</div>
				</div>
			</body>
			</html>';
			

			$headers  = 'MIME-Version: 1.0'."\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$headers .= 'To: '.$email. "\r\n";
			$headers .= 'From: Your Company <info@yoursite.com>' . "\r\n";
			
			mail($email, 'Welcome to the Your Company', $message, $headers);
			
	/* 		sendPush2(0, $first_name.' '.$last_name, 'New quote request from the site', [
				'type' => 'new_quote',
				'first_name' => $first_name,
				'last_name' => $last_name,
				'phone' => $phone,
				'email' => $email,
				'issue' => $issue,
				'date' => 'just now',
				'id' => $id
			], 'quote'); */

			//if($user['id'] == 31735){
				sPush(0, $first_name.' '.$last_name, 'New quote request from the site', [
					'type' => 'new_quote',
					'first_name' => $first_name,
					'last_name' => $last_name,
					'phone' => $phone,
					'email' => $email,
					'issue' => $issue,
					'date' => 'just now',
					'id' => $id
				], 'quote');
			//}
			
			
			send_push(0, [
				'type' => 'new_quote',
				'id' => $id,
				'name' => $first_name,
				'lastname' => $last_name,
				'message' => 'New request from the site #'.$id.''
			]);
			
			echo 'OK';
		} else
			echo 'email_err';
	} else
		echo 'enter_req';
} else {
	header('Location: /get-quote', true, 301);
	die;
}
die;
	
	

	if (!$user AND (!$name OR !$lastname OR !$phone OR !$email OR !$issue))
		die('enter_req');
	
	if (!$type OR !$brand OR !$model OR !$smodel OR !$os OR !$issue OR !$date OR !$time OR !$store)
		die('enter_req');
	
	if (strtotime($date.' '.$time) < date('Y-m-d H:i:s'))
		die('err_date');
	
	if (!$user) {
		$emails = db_multi_query('SELECT COUNT(id) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\'');
		if ($emails['count'] > 0)
			die('email_err');
		
		$password = substr(md5(uniqid()), 0, 6);
		
		db_query('INSERT INTO `'.DB_PREFIX.'_users` SET
			name = \''.$name.'\',
			lastname = \''.$lastname.'\',
			phone = \''.$phone.'\',
			sms = \''.$phone.'\',
			address = \''.$address.'\',
			email = \''.$email.'\',
			password = \''.$password.'\',
			group_ids = \'5\'
		');
		
		$user_id = intval(mysqli_insert_id($db_link));
		
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
					<a href="http://yoursite.com/">
						<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
					</a>
				</div>
				<div style="padding: 0 30px 30px;">
					<p>You successfully registered on <br><b>Your Company inc</b>.</p>
					<p>Please use the details below for login to website:</p>
					<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
						Link: <a href="http://yoursite.com/" style="color: #0e92d4;">Click here</a><br>
						Login: '.$email.'<br>
						Password: '.$password.'
					</div>
				</div>
			</div>
		</body>
		</html>';

		// Send
		mail($email, 'Welcome to the Your Company', $message, $headers);
	} else
		$user_id = $user['id'];
	
	db_query('INSERT INTO `'.DB_PREFIX.'_inventory` SET
		type = \'stock\',
		customer_id = '.$user_id.',
		object_id = '.$store.',
		type_id = '.$type.',
		category_id = '.$brand.',
		model_id = '.$model.',
		model = \''.$smodel.'\',
		os_id = '.$os.'
		'.($os_ver ? ', ver_os = \''.$os_ver.'\'' : '').'
		'.($sn ? ', serial = \''.$sn.'\'' : '')
	);
	
	// insert quote
	db_query('
		INSERT INTO `'.DB_PREFIX.'_users_appointments` SET 
			customer_id = \''.$user_id.'\',
			date = \''.$date.' '.$time.'\',
			object_id = \''.$store.'\',
			note = \''.$issue.'\'
	');
	
	$id = intval(mysqli_insert_id($db_link));
	
	db_query('
		INSERT INTO `'.DB_PREFIX.'_notifications` SET 
			type = \'new_appointment\', 
			customer_id = '.$user_id.', 
			id = '.$id
	);
	
	$cid = db_multi_query('
		SELECT 
			u.sms,
			o.name
		FROM `'.DB_PREFIX.'_users` u,
			`'.DB_PREFIX.'_objects` o
		WHERE u.id = '.$user_id.' AND o.id = '.$store
	);
	
	if ($cid['sms'] AND $user_id != 17)
		send_sms($cid['sms'], 'You have successfully scheduled a free diagnosis appointment at Your Company of '.$cid['name'].' on '.date('l', strtotime($date)).' '.date('F jS', strtotime($date)).' at '.date('hA', strtotime($date)));
	
	die('OK');
	
?>