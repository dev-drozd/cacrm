<?php
/**
 * @appointment Shop site
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
 
switch($route[2]){
	
	/*
	*  View blog
	*/
	case 'view':
		$id = intval($route[3]);
		$meta['title'] = 'Blog';
		$row = [];
		if($id){
			$row = db_multi_query('
				SELECT
					i.*,
					t.options as opts,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.image as customer_image,
					u.ver as customer_ver,
					u.phone as customer_phone,
					u.address as customer_address,
					o.id as object_id,
					o.name as object_name,
					o.phone as object_phone,
					o.address as object_address,
					o.image as object_image,
					s.name as status_name,
					l.name as location_name,
					c.name as category_name,
					os.name as os_name
				FROM `'.DB_PREFIX.'_inventory` i
				INNER JOIN `'.DB_PREFIX.'_inventory_types`
					t ON i.type_id = t.id
				LEFT JOIN `'.DB_PREFIX.'_users`
					u ON u.id = i.customer_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = i.object_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
					ON s.id = i.status_id
				LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
					ON l.id = i.location_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
					ON c.id = i.category_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_os` os
					ON os.id = i.os_id
				WHERE i.id = '.$id
			);
			foreach(db_multi_query(
				'SELECT i.*, u.name as staff_name, u.lastname
					FROM `'.DB_PREFIX.'_issues` i
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON i.staff_id = u.id
				WHERE inventory_id = '.$id
			, true) as $issue){
				tpl_set('/cicle/devIssue', [
					'id' => $issue['id'],
					'model' => $row['model'],
					'category' => $row['category_name'],
					'staff-id' => $issue['staff_id'],
					'staff-name' => $issue['staff_name'],
					'staff-lastname' => $issue['lastname'],
					'description' => $issue['description'],
					'date' => $issue['date']
				], [], 'issues');
			}
		}
		$options = '';
		if($row['options']){
			$opts = json_decode($row['opts'], true);
			foreach(json_decode($row['options'], true) as $n => $v){
				if(!$v) continue;
				if(is_array($v)){
					$vlue = [];
					foreach($v as $f){
						$vlue[] = $opts[$n]['sOpts'][$f];
					}
					$vlue = implode(', ', $vlue);
				} else {
					$vlue = is_array($opts[$n]['sOpts']) ? $opts[$n]['sOpts'][$v] : $v;
				}
				$options .= '<li><b>'.$opts[$n]['name'].'</b>: '.$vlue.'</li>';
			}
		}
		tpl_set('blog/view', [
			'id' => $id,
			'title' => 'View '.$row['type'],
			'model' => $row['model'],
			'price' => $row['price'],
			'purchase-price' => $row['purchase_price'],
			'category' => $row['category_name'],
			'os' => $row['os_name'],
			'version-os' => $row['ver_os'],
			'serial' => $row['serial'],
			'customer-ver' => $row['customer_ver'],
			'options' => $options,
			'customer-id' => $row['customer_id'],
			'customer-name' => $row['customer_name'],
			'customer-lastname' => $row['customer_lastname'],
			'customer-image' => $row['customer_image'],
			'customer-phone' => $row['customer_phone'],
			'customer-address' => preg_replace(
				"/\n/", "<br>", $row['customer_address']
			),			
			'object-id' => $row['object_id'],
			'object-image' => $row['object_image'],
			'object-phone' => $row['object_phone'],
			'object-address' => preg_replace(
				"/\n/", "<br>", $row['object_address']
			),
			'object' => $row['object_name'],
			'status' => $row['status_name'],
			'location' => $row['location_name'],
			'issues' => $tpl_content['issues'],
			'forms' => $row ? getTypes(
				json_decode($row['opts'], true),
				json_decode($row['options'], true)
			) : '',
		], [
			'customer' => $row['customer_id'],
			'store' => $row['commerce'],
			'ver' => $row['customer_ver'],
			'ava' => $row['customer_image'],
			'object-ava' => $row['object_image'],
			'add' => ($route[2] == 'add'),
			'edit' => ($route[2] == 'edit'),
			'blog' => ($row['type'] == 'stock')
		], 'content');
	break;
	
	/*
	*  View blogs
	*/
	case 'blogs':
		$meta['title'] = 'Blogs';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 20;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				id, IF(name = \'\', model, name) as name FROM `'.DB_PREFIX.'_store_blog`
			WHERE 1 '.(
				$query ? 'AND name LIKE \'%'.$query.'%\' ' : ''
			).(
				in_array($route[2], ['stock', 'service']) ? 'AND type = \''.$route[2].'\' ' : ''
			).'ORDER BY `id` LIMIT '.($page*$count).', '.$count, true
		)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('blog/item', [
					'id' => $row['id'],
					'name' => $row['name']
				], [], 'inventories');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
			tpl_set('noContent', [
				'text' => 'There are no blog'
			], false, 'inventories');
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['inventories'],
			]));
		}
		tpl_set('blog/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'blog' => $tpl_content['inventories']
		], [], 'content');
	break;
	
	/*
	* All invoices
	*/
	default:
	$meta['title'] = 'Inventory';
	$query = text_filter($_POST['query'], 255, false);
	$page = intval($_POST['page']);
	$count = 20;
	if($sql = db_multi_query('
		SELECT SQL_CALC_FOUND_ROWS
			id, IF(name = \'\', model, name) as name FROM `'.DB_PREFIX.'_inventory`
		WHERE 1 '.(
			$query ? 'AND name LIKE \'%'.$query.'%\' ' : ''
		).(
			in_array($route[2], ['stock', 'service']) ? 'AND type = \''.$route[2].'\' ' : ''
		).'ORDER BY `id` LIMIT '.($page*$count).', '.$count, true
	)){
		$i = 0;
		foreach($sql as $row){
			tpl_set('inventory/item', [
				'id' => $row['id'],
				'name' => $row['name']
			], [], 'inventories');
			$i++;
		}
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
	} else {
		tpl_set('noContent', [
			'text' => 'There are no Inventory'
		], false, 'inventories');
	}
	$left_count = intval(($res_count-($page*$count)-$i));
	if($_POST){
		exit(json_encode([
			'res_count' => $res_count,
			'left_count' => $left_count,
			'content' => $tpl_content['inventories'],
		]));
	}
	tpl_set('inventory/main', [
		'res_count' => $res_count,
		'more' => $left_count ? '' : ' hdn',
		'inventory' => $tpl_content['inventories']
	], [], 'content');
}
?>