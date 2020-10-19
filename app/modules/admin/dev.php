<?php
//die;
/**
 * @appointment Developers
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
*/
switch($route[1]){
	
	case 'push':
		$row = db_multi_query('SELECT total, purchase_info FROM `'.DB_PREFIX.'_issues` WHERE id = 20409');
		$ap = json_decode($row['purchase_info'], true);
		if (is_array($ap)) {
			$purchases = db_multi_query('SELECT SUM(price) as price FROM `'.DB_PREFIX.'_purchases` WHERE id IN ('.implode(',', array_keys($ap)).')');
		}
		echo '<pre>';
		print_r($row);
		print_r($purchases);
		die;
		sPush(0, 'Test notification', 'The test notification', [
			'type' => 'alert',
			'msg' => 'The test notification',
			'id' => 1
		]);
		echo 'OK';
		die;
	break;
	
	case 'email_feedback':
		$form_email = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_forms` WHERE id = 17');
		
		$issue_id = 18476;
		$customer_id = 22750;
		
		$mail = str_ireplace([
			'{name}', 
			'{date}',
			'{issue_id}',
			'{hash}',
			'{twitter}',
			'{facebook}',
			'{google-plus}',
			'{youtube}'
		], [
			'Pavel Zaichenko',
			date('d.m.Y'),
			$issue_id,
			md5($issue_id.$customer_id),
			'',
			'',
			'',
			''
		], $form_email['content']);
		
		// Headers
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		$headers .= "To:  pavppz1@gmail.com\r\n";
		$headers .= 'From: Your Company <info@yoursite.com>' . "\r\n";

		// Send
		mail('pavppz1@gmail.com', 'Thank you for your recent visit to Your Company', '<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Thank you for your recent visit to Your Company</title>
		</head>
		<body style="text-align: center;">
			'.$mail.'
		</body>
		</html>', $headers);
	break;
	
	case 'reload':
		send_push(0, ['type' => 'reload']);
		header("Location: /");
		die;
	break;
}
?>