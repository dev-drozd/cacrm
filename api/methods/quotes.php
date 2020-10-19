<?php

auth_app();

switch($sub_method){
	
	case 'reply':
		if(is_token()){
			$id = (int)$_REQUEST['id'];
			db_query('UPDATE `'.DB_PREFIX.'_quote_requests` SET sent_id = '.$user['id'].', reply_text = \''.db_escape_string($_REQUEST['text']).'\', send_date = Now() WHERE id = '.$id);
		}
	break;
	
	case 'send':
		if(is_token()){
			$id = (int)$_REQUEST['id'];
			if($quote = db_multi_query('SELECT q.*, o.address as store_address, o.phone as store_phone, CONCAT(TIME_FORMAT(o.work_time_start, "%H:%i"), \'-\', TIME_FORMAT(o.work_time_end, "%H:%i")) as store_hours, o.map as store_loc FROM `'.DB_PREFIX.'_quote_requests` q LEFT JOIN `'.DB_PREFIX.'_objects` o ON q.store_id = o.id WHERE q.id = '.$id)){
				db_query('UPDATE `'.DB_PREFIX.'_quote_requests` SET sent_id = '.$user['id'].', reply_text = \''.db_escape_string($_REQUEST['text']).'\', send_date = Now() WHERE id = '.$id);
				if($form = db_multi_query('SELECT content FROM `'.DB_PREFIX.'_forms` WHERE id = 21')){
					
					$text = str_ireplace([
						'{staff-name}',
						'{customer-question}',
						'{staff-answer}',
						'{store-location}',
						'{store-hours}',
						'{store-phone}'
					], [
						$user['first_name'].' '.$user['last_name'],
						$quote['issue'],
						'<blockquote style="padding: 15px;background: #fbfbfb;border-radius: 4px;">'.$_REQUEST['text'].'</blockquote>',
						'<a href="https://maps.google.com/?q=loc:'.$quote['store_loc'].'">'.$quote['store_address'].'</a>',
						$quote['store_hours'],
						'<a href="tel:+1 '.$quote['store_phone'].'">'.$quote['store_phone'].'</a>'
					], $form['content']);
					
					$headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: '.$quote['email']. "\r\n";
					$headers .= 'From: Your Company <info@yoursite.com>' . "\r\n";
					
					mail($quote['email'], 'Reply to your request #'.$id, '<html lang="en">
						<head>
							<meta charset="UTF-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">
							<title>Reply to your request #2260</title>
						</head>
						<body style="background: #f6f6f6; text-align: center;">
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
								<div style="margin: -20px -20px 0; padding: 20px;">
									<a href="http://yoursite.com/">
										<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
									</a>
								</div>
								<div style="padding: 0 30px 30px;">
									'.$text.'
								</div>
							</div>
						</body>
						</html', $headers
					);
				
					send_sms($_REQUEST['phone'], $text, 'Quote request #'.$id);	
				}	
			}
		}
	break;
	
	case 'get':
		is_token();
		$id = (int)$_REQUEST['id'];
		$res['quote'] = db_multi_query('SELECT r.email, r.phone, r.id, r.first_name, r.last_name, r.issue, r.customer_id, r.sent_id, r.reply_text, o.name as store, IF(r.date < CURDATE(), DATE_FORMAT(r.date, "%c/%d/%y %h:%i"), DATE_FORMAT(r.date, "%h:%i")) as date, u.image FROM `'.DB_PREFIX.'_quote_requests` r LEFT JOIN `'.DB_PREFIX.'_objects` o ON r.store_id = o.id LEFT JOIN `'.DB_PREFIX.'_users` u ON r.customer_id = u.id WHERE r.id = '.$id, false, function($a,$b){
			$a['issue'] = outputMsg($a['issue']);
			$a['reply_text'] = outputMsg($a['reply_text']);
			return [$b,$a];
		});
	break;
	
	case 'newlist':
		is_token();
		$res['quotes'] = db_multi_query('SELECT r.phone, r.id, r.first_name, r.last_name, r.sent_id, r.customer_id, IF(SUBSTR(r.issue, 1, 70) = r.issue, r.issue, CONCAT(SUBSTR(r.issue, 1, 70), \'...\')) issue, o.name as store, IF(r.date < CURDATE(), DATE_FORMAT(r.date, "%c/%d/%y"), DATE_FORMAT(r.date, "%h:%i")) as date, u.image FROM `'.DB_PREFIX.'_quote_requests` r LEFT JOIN `'.DB_PREFIX.'_objects` o ON r.store_id = o.id LEFT JOIN `'.DB_PREFIX.'_users` u ON r.customer_id = u.id WHERE r.del = 0 ORDER BY r.sent_id > 0, r.date DESC, r.reply_text != \'\' ASC LIMIT 20', true, function($a,$b){
			$a['issue'] = outputMsg($a['issue']);
			$a['reply_text'] = outputMsg($a['reply_text']);
			return [$b,$a];
		});
	break;
	
	case null:
		is_token();
		$res['quotes'] = db_multi_query('SELECT r.*, r.sent_id as reply, o.name as store, IF(r.date < CURDATE(), DATE_FORMAT(r.date, "%c/%d/%y"), DATE_FORMAT(r.date, "%h:%i")) as date FROM `'.DB_PREFIX.'_quote_requests` r LEFT JOIN `'.DB_PREFIX.'_objects` o ON r.store_id = o.id WHERE r.del = 0 ORDER BY r.sent_id = 0, r.date DESC, r.reply_text != \'\' ASC LIMIT 200', true, function($a,$b){
			$a['issue'] = outputMsg($a['issue']);
			$a['reply_text'] = outputMsg($a['reply_text']);
			return [$b,$a];
		});
	break;
}
?>