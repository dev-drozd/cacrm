<?php

auth_app();

$query = text_filter($_REQUEST['query'], 255, false);
$page = intval($_REQUEST['page']);
$count = 10;
				
switch($sub_method){
	
	case 'del':

	break;
	
	case null:
		is_token();
		$res['requests'] = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
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
			WHERE o.del = 0 '.(
				$query ? 'AND (id LIKE \'%'.$query.'%\' OR CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' OR CONCAT(u.lastname, \' \', u.name) LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY o.id DESC LIMIT '.($page*$count).', '.$count
		, true);
	break;
}
?>