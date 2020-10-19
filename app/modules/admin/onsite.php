<?php
/**
 * @appointment Onsites admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2018
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]){

	case 'view':
		$id = (int)$route[2];
		if($row = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
				o.*,
				i.name,
				i.price,
				CONCAT(s.name, \' \', s.lastname) as staff_name,
				CONCAT(c.name, \' \', c.lastname) as customer_name,
				s.image as image_staff,
				c.image as image_customer,
				c.phone as customer_phone,
				c.email as customer_email,
				c.address as customer_address
			FROM 
				`'.DB_PREFIX.'_onsite` o INNER JOIN `'.DB_PREFIX.'_inventory_onsite` i ON o.service_id = i.id 
			INNER JOIN `'.DB_PREFIX.'_users` s ON o.staff_id = s.id
			INNER JOIN `'.DB_PREFIX.'_users` c ON o.customer_id = c.id
			WHERE o.id = '.$id)){
			tpl_set('onsite/review', [
				'id' => $row['id'],
				'staff-id' => $row['staff_id'],
				'staff-name' => $row['staff_name'],
				'customer-id' => $row['customer_id'],
				'customer-name' => $row['customer_name'],
				'customer-phone' => $row['customer_phone'],
				'customer-email' => $row['customer_email'],
				'customer-address' => $row['customer_address'],
				'service-name' => $row['name'],
				'service-price' => $row['price'],
				'service-date' => convert_date($row['date'], true),
				'create-date' => convert_date($row['create_date'], true),
				'image-staff' => $row['image_staff'],
				'image-customer' => $row['image_customer'],
				'issue' => $row['issue']
			], [
				'image-staff' => $row['image_staff'],
				'image-customer' => $row['image_customer']
			], 'content');
		}
	break;

	case 'send':
		db_query('INSERT INTO `'.DB_PREFIX.'_onsite` SET 
			create_id = '.intval($user['id']).',
			customer_id = '.intval($_POST['customer_id']).',
			store_id = '.intval($_POST['store_id']).',
			service_id = '.intval($_POST['service_id']).',
			staff_id = '.intval($_POST['staff_id']).',
			date = \''.text_filter($_POST['date'].' '.$_POST['time']).'\',
			issue = \''.text_filter($_POST['issue'], 16000, false).'\'
		');
		echo 'OK';
		die;
	break;

	case null:
		$query = text_filter($_REQUEST['query'], 100, false);
		$page = intval($_POST['page']);
		$count = 20;
		
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
				o.*,
				i.name,
				i.price,
				CONCAT(s.name, \' \', s.lastname) as staff_name,
				CONCAT(c.name, \' \', c.lastname) as customer_name,
				s.image as image_staff,
				c.image as image_customer
			FROM 
				`'.DB_PREFIX.'_onsite` o INNER JOIN `'.DB_PREFIX.'_inventory_onsite` i ON o.service_id = i.id 
			INNER JOIN `'.DB_PREFIX.'_users` s ON o.staff_id = s.id
			INNER JOIN `'.DB_PREFIX.'_users` c ON o.customer_id = c.id
			WHERE del_id = 0'.(
				$query ? ' AND (CONCAT(s.name, \' \', s.lastname) LIKE \'%'.$query.'%\' OR CONCAT(c.name, \' \', c.lastname) LIKE \'%'.$query.'%\' OR i.name LIKE \'%'.$query.'%\')' : ''
			).' LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('onsite/item', [
					'id' => $row['id'],
					'staff-id' => $row['staff_id'],
					'staff-name' => $row['staff_name'],
					'customer-id' => $row['customer_id'],
					'customer-name' => $row['customer_name'],
					'service-name' => $row['name'],
					'service-price' => $row['price'],
					'service-date' => convert_date($row['date'], true),
					'create-date' => convert_date($row['create_date'], true),
					'image-staff' => $row['image_staff'],
					'image-customer' => $row['image_customer']
				], [
					'image-staff' => $row['image_staff'],
					'image-customer' => $row['image_customer']
				], 'onsites');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = $lang['All'].' '.($group['name'] ?? $lang['users']);
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['onsites'],
			]));
		}

		tpl_set('onsite/main', [
			'query' => $_GET['query'] ?? '',
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'onsites' => $tpl_content['onsites']
		], [
		], 'content');
}