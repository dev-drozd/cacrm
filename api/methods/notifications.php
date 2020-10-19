<?php

//auth_app();

switch($sub_method){
	
	case 'count':
		$res['dialogs'] = (int)db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_chat_im` WHERE email != \'\' AND count > 0')['count'];
		$res['quotes'] = (int)db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_quote_requests` r LEFT JOIN `'.DB_PREFIX.'_objects` o ON r.store_id = o.id WHERE r.del = 0 AND r.sent_id = 0')['count'];
		$res['requests'] = (int)db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_orders` WHERE del = 0')['count'];
		$res['total'] = $res['dialogs']+$res['quotes']+$res['requests'];
	break;
	
	case null:
		//is_token();
		$res['dialogs'] = db_multi_query('SELECT *, IF(name != \'\', name, email) as email, IF(SUBSTR(REPLACE(last_msg, \'\r\n\', \' \'), 1, 30) = REPLACE(last_msg, \'\r\n\', \' \'), REPLACE(last_msg, \'\r\n\', \' \'), CONCAT(SUBSTR(REPLACE(last_msg, \'\r\n\', \' \'), 1, 30), \'...\')) as last_msg, date as sortdate, IF(date < CURDATE(), DATE_FORMAT(date, "%c/%d/%y"), DATE_FORMAT(date, "%h:%i")) as date FROM `'.DB_PREFIX.'_chat_im` WHERE email != \'\' AND count > 0 ORDER BY `sortdate` DESC LIMIT 50', true, function($a,$b){
			$a['type'] = 'dialog';
			return [$b,$a];
		});
		
		$res['quotes'] = db_multi_query('SELECT r.phone, r.id, r.first_name, r.last_name, r.sent_id, r.customer_id, IF(SUBSTR(r.issue, 1, 70) = r.issue, r.issue, CONCAT(SUBSTR(r.issue, 1, 70), \'...\')) issue, o.name as store, IF(r.date < CURDATE(), DATE_FORMAT(r.date, "%c/%d/%y"), DATE_FORMAT(r.date, "%h:%i")) as date, u.image FROM `'.DB_PREFIX.'_quote_requests` r LEFT JOIN `'.DB_PREFIX.'_objects` o ON r.store_id = o.id LEFT JOIN `'.DB_PREFIX.'_users` u ON r.customer_id = u.id WHERE r.del = 0 AND r.sent_id = 0 ORDER BY r.date DESC, r.reply_text != \'\' ASC LIMIT 50', true, function($a,$b){
			$a['type'] = 'quote';
			$a['issue'] = outputMsg($a['issue']);
			return [$b,$a];
		});
		
		$res['requests'] = db_multi_query('
			SELECT
				o.*,
				DATE(o.date) as date,
				TIME(o.date) as time,
				u.id as uid,
				CONCAT(u.name, \' \', u.lastname) as uname,
				u.phone as uphone,
				s.name as status,
				s.color,
				s.alt_color,
				d.name as delivery,
				p.name as payment
			FROM `'.DB_PREFIX.'_orders` o 
			LEFT JOIN `'.DB_PREFIX.'_users` u 
				ON u.id = o.customer_id
			LEFT JOIN `'.DB_PREFIX.'_orders_status` s 
				ON s.id = o.status_id
			LEFT JOIN `'.DB_PREFIX.'_orders_delivery` d 
				ON d.id = o.delivery_id
			LEFT JOIN `'.DB_PREFIX.'_orders_payment` p 
				ON p.id = o.payment_id
			WHERE o.del = 0 ORDER BY o.id DESC LIMIT 50', true);
	break;
}
?>