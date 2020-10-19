<?php
/**
 * @appointment Inventory
 * @author      Alexandr Drozd & Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

/*
*  Get forms group
*/
function getTypes($id, $values = []){
	$forms = '';
	if(!is_array($id)){
		$sql = db_multi_query('SELECT options FROM `'.DB_PREFIX.'_inventory_types` WHERE id = '.$id);
		$id = json_decode($sql['options'], true);
	}
	if ($id) {
		foreach($id as $hid => $row){
			$name = create_name($row['name']);
			$type = '';
			if($row['type'] == 'select'){
				$options = '';
				foreach($row['sOpts'] as $k => $v){
					if(is_array($values[$hid])){
						$sel = in_array($k, $values[$hid]);
					} else {
						$sel = ($k == $values[$hid]);
					}
					$options .= '<option value="'.$k.'"'.(
						$sel ? ' selected' : ''
					).'>'.$v.'</option>';
				}
				$type .= '<select name="'.$name.'" data-id="'.$hid.'" '.(
					$row['mult'] ? ' multiple' : ''
				).($row['req'] ? ' required' : '').'>'.$options.'</select>';
			} else if($row['type'] == 'textarea'){
				$type .= '<textarea name="'.$name.'" data-id="'.$hid.'">'.$values[$hid].'</textarea>';
			} else {
				$type .= '<input name="'.$name.'" data-id="'.$hid.'" type="'.(
					$row['type'] == 'input' ? 'text' : $row['type']
				).'"'.(($row['req'] AND $row['type'] != 'checkbox')  ? ' required' : '').(
					$values[$hid] ? (
						$row['type'] == 'checkbox' ? ' checked' : ' value="'.$values[$hid].'"'
					) : ''
				).'>';
			}
			$forms .= '<div class="iGroup">
				<label>'.$row['name'].'</label>'.$type.'
			</div>';
		}
		return $forms;
	} 
		
}

/*
*  Get forms group
*/
function getNewTypes($id, $values = []){
	$forms = '';
	if(!is_array($id)){
		$sql = db_multi_query('SELECT options FROM `'.DB_PREFIX.'_inventory_types` WHERE id = '.$id);
		$id = json_decode($sql['options'], true);
	}
	if ($id) {
		foreach($id as $hid => $row){
			$name = create_name($row['name']);
			$type = '';
			if($row['type'] == 'select'){
				$options = '';
				foreach($row['sOpts'] as $k => $v){
					if(is_array($values[$hid])){
						$sel = in_array($k, $values[$hid]);
					} else {
						$sel = ($k == $values[$hid]);
					}
					$options .= '<option value="'.$k.'"'.(
						$sel ? ' selected' : ''
					).'>'.$v.'</option>';
				}
				$type .= '<select name="opts['.$hid.']" '.(
					$row['mult'] ? ' multiple' : ''
				).($row['req'] ? ' required' : '').'>'.$options.'</select>';
			} else if($row['type'] == 'textarea'){
				$type .= '<textarea name="opts['.$hid.']">'.$values[$hid].'</textarea>';
			} else {
				$type .= '<input name="opts['.$hid.']"" type="'.(
					$row['type'] == 'input' ? 'text' : $row['type']
				).'"'.(($row['req'] AND $row['type'] != 'checkbox')  ? ' required' : '').(
					$values[$hid] ? (
						$row['type'] == 'checkbox' ? ' checked' : ' value="'.$values[$hid].'"'
					) : ''
				).'>';
			}
			$forms .= '<div class="iGroup">
				<label>'.$row['name'].'</label>'.$type.'
			</div>';
		}
		return $forms;
	} 
		
}

if($gid = intval($_POST['step_type_id'])){
	$iId = intval($_POST['inventory']);
	if ($iId) {
		$r = db_multi_query('SELECT options FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$iId);
		echo json_encode(getNewTypes($gid, json_decode($r['options'], true)));
	} else
		echo getNewTypes($gid);
	die;
}

if($gid = intval($_POST['type_id'])){
	$iId = intval($_POST['inventory']);
	if ($iId) {
		$r = db_multi_query('SELECT options FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$iId);
		echo json_encode(getTypes($gid, json_decode($r['options'], true)));
	} else
		echo json_encode(getTypes($gid), JSON_UNESCAPED_UNICODE);
	die;
}

/* if($gid = intval($_POST['type_id'])){
	echo json_encode(getTypes($gid), JSON_UNESCAPED_UNICODE);
	die;
} */

switch($route[1]){
	
	case 'buy_device':
		is_ajax() or die('Hacking attempt!');
		$id = (int)$_POST['id'];
				
		if($row = db_multi_query('
			SELECT inv.id as inv_id, iss.object_owner, iss.inventory_id, iss.customer_id, REPLACE(IF(inv.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', inv.model), inv.name), \'"\', \'\') as inv_name FROM `'.DB_PREFIX.'_issues` iss 
				INNER JOIN `'.DB_PREFIX.'_inventory` inv 
					ON iss.inventory_id = inv.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c 
					ON inv.category_id = c.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o 
					ON o.id = inv.object_id
				LEFT JOIN  `'.DB_PREFIX.'_inventory_models` m
					ON m.id = inv.model_id
				LEFT JOIN  `'.DB_PREFIX.'_inventory_types` t
					ON t.id = inv.type_id			
			WHERE iss.id = '.$id.' AND iss.customer_id > 0')){
				
			db_query('
				UPDATE `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_inventory` inv ON iss.inventory_id = inv.id SET 
					iss.customer_id = 0, inv.old_customer_id = inv.customer_id, inv.customer_id = 0, inv.purchase_price = \''.floatval($_POST['pprice']).'\', inv.sale_price = \''.floatval($_POST['sprice']).'\' 
				WHERE iss.id = '.$id
			);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_invoices` SET 
				object_id = '.$row['object_owner'].',
				customer_id = '.$row['customer_id'].',
				staff_id = '.$user['id'].',
				date = \''.date('Y-m-d H:i:s', time()).'\',
				total = \''.floatval($_POST['pprice']).'\',
				tax = 0,
				tax_exempt = \'\',
				inventory_info = \'{}\',
				tradein_info = \'{\"'.intval($row['inv_id']).'\":{\"name\":\"'.db_escape_string($row['inv_name']).'\",\"purchase\":\"'.floatval($_POST['pprice']).'\",\"price\":\"'.floatval($_POST['sprice']).'\",\"currency\":\"USD\"}}\',
				invoices = \'\',
				purchases_info = \'{}\',
				estimate = 0,
				addition_info = \'{}\',
				services_info = \'{}\',
				currency = \'USD\'
			');
			echo intval(mysqli_insert_id($db_link));
		}
		die;
	break;	
	
	case 'buy_min_price':
		is_ajax() or die('Hacking attempt!');
		echo number_format(min_price(floatval($_GET['price']), ($user['store_id'] ?? 2)), 2, '.', '');
		die;
	break;
	
	case 'send_addition':
		is_ajax() or die('Hacking attempt!');
		db_query('INSERT INTO `'.DB_PREFIX.'_addition_fields` SET 
			staff_id = '.$user['id'].', 
			invoice_id = '.intval($_POST['in_id']).',
			store_id = '.intval($_POST['store_id']).',
			name = \''.text_filter($_POST['name']).'\',
			price = \''.floatval($_POST['price']).'\',
			tax = '.intval($_POST['tax']).',
			type = \''.text_filter($_POST['type']).'\'
		');
		die;
	break;
	
	case 'send_approve_field':
		is_ajax() or die('Hacking attempt!');
		$id = (int)$_POST['id'];
		die;
		if($field = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_addition_fields` WHERE id = '.$id)){
			db_query("INSERT INTO `".DB_PREFIX."_inventory` SET name = '{$field['name']}'");
			db_query('INSERT INTO `'.DB_PREFIX.'_inventory` SET
				name =\''.$field['name'].'\',
				opt_charger =\'NO\',
				price =\''.$field['price'].'\',
				currency = \'USD\',
				object_owner =\''.intval($field['store_id']).'\',
				object_id = \''.intval($field['store_id']).'\',
				type = \''.(
					$field['type'] == 'inventory' ? 'stock' : 'service'
				).'\',
				cr_user = \''.$user['staff_id'].'\',
				cr_date = \''.date('Y-m-d H:i:s').'\''
			);
		}
		die;
	break;
	
	case 'del_addition_fields':
		db_query('DELETE FROM `'.DB_PREFIX.'_addition_fields` WHERE id = '.intval($_POST['id']));
		die;
	break;
	
	case 'addition_fields':
        $meta['title'] = 'Addition fields';
		$query = text_filter($_REQUEST['query'], 255, false);
		$page = intval($_REQUEST['page']);
		
		$count = 20;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
                a.*,
				u.image,
                u.name as user_name,
                u.lastname as user_lastname,
				o.name as store,
				o.image as store_image
			FROM `'.DB_PREFIX.'_addition_fields` a
			LEFT JOIN `'.DB_PREFIX.'_users` u 
				ON a.staff_id = u.id
			LEFT JOIN `'.DB_PREFIX.'_objects` o 
				ON a.store_id = o.id
			WHERE 1 '.(
			$query ? 'AND MATCH (a.name) AGAINST (\''.$query.'\' IN BOOLEAN MODE) ' : ''
		).' ORDER BY a.id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0; // f.title LIKE \'%'.$query.'%\' OR f.content LIKE \'%'.$query.'%\'
			foreach($sql as $row){
				tpl_set('inventory/addition_fields/item', [
					'id' => $row['id'],
					'type' => $row['type'] ?: 'inventory',
					'store-id' => $row['store_id'],
					'invoice-id' => $row['invoice_id'],
					'name' => $row['name'],
					'price' => $row['price'],
					'store' => $row['store'],
					'tax' => $row['tax'] ? 'yes' : 'no',
					'image' => $row['image'],
					'store-image' => $row['store_image'],
                    'date' => $row['date'],
                    'staff-name' => $row['user_name'],
                    'staff-lastname' => $row['user_lastname'],
                    'staff-id' => $row['staff_id']
                ],[
					'ava' => $row['image'],
					'sava' => $row['store_image'],
					'inv' => $row['invoice_id']
				], 'fields');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
            tpl_set('noContent', [
                'text' => $lang['noFaqs']
            ],false, 'fields');
        }
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['fields'],
			]));
		}
		tpl_set('inventory/addition_fields/main', [
			'title' => $meta['title'],
			'query' => $query,
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'fields' => $tpl_content['fields']
		], [], 'content');
	break;
	
	/*
	* Warranty status sort
	*/
	case 'stWarrantyPriority':
		is_ajax() or die('Hacking attempt!');
		$i = 1;
		foreach($_POST as $row){
			db_query('UPDATE `'.DB_PREFIX.'_inventory_warranty_status` SET sort = '.$i.', not_priority = '.(
				$row['pr'] ? 1 : '0'
			).' WHERE id = '.$row['id']);
				$i++;	
		}
		die;
	break;
	
	
	/*
	* Warranty statuses
	*/
	case 'warranty_statuses':
		if($id = intval($route[2])){
			
			$row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_warranty_status` WHERE id = '.$id);
			$locations = [];
			if($row['locations']){
				$sql = db_multi_query('SELECT l.*, o.name as object 
										FROM `'.DB_PREFIX.'_objects_locations` l
										LEFT JOIN `'.DB_PREFIX.'_objects` o
											ON o.id = l.object_id
										WHERE l.id IN('.$row['locations'].')', true);
				foreach($sql as $loc){
					$locations[$loc['id']] = [
						'name' => $loc['name'],
						'object' => $loc['object'],
						'objectid' => $loc['object_id']
					];
				}
			}
			$meta['title'] = $row['name'];
			tpl_set('inventory/warranty_status/location', [
				'id' => $id,
				'name' => $row['name'],
				'locations' => json_encode($locations),
				'point-group' => $row['point_group'],
				'forfeit' => $row['forfeit'],
				'send' => 'Save'
			], [
				'rservice' => $row['service'],
				'rinventories' => $row['inventory'],
				'purchase' => $row['purchase'],
				'create' => $user['create_inventory_transfer'],
				'default' => $id != 1 AND $id != 2
			], 'content');
		} else {
			$meta['title'] = $lang['Statuses'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					*
				FROM `'.DB_PREFIX.'_inventory_warranty_status` '.(
				$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY `sort` ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/warranty_status/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'priority' => $row['not_priority'] ? ' checked' : '',
						'point-group' => $row['point_group'] ?: 0,
						'forfeit' => $row['forfeit'],
						'sms' => intval($row['sms']),
						'smsForm' => intval($row['sms_form'])
					], [
						'default' => $row['id'] != 1 AND $row['id'] != 2,
						'nnew' => $row['id'] != 1
					], 'status');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noStatuses']
				], false, 'status');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['status'],
				]));
			}
			tpl_set('inventory/warranty_status/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'status' => $tpl_content['status']
			], [
				'create' => $user['create_inventory_transfer']
			], 'content');
		}
	break;
	
	/*
	* Del transfer
	*/
	case 'del_transfer':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$sql = db_multi_query('SELECT from_manager, from_store, del FROM `'.DB_PREFIX.'_inventory_transfer` WHERE id = '.$id);
		if ((($sql['from_manager'] == $user['id'] OR in_to_array(1, $user['group_ids']))) AND $sql['del'] == 0) {
			db_query('UPDATE `'.DB_PREFIX.'_inventory_transfer` SET del = 1 WHERE id ='.$id);
			db_query('
				INSERT INTO 
				`'.DB_PREFIX.'_activity` SET 
					user_id = \''.$user['id'].'\', 
					event = \'delete transfer\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					event_id = '.$id.',
					object_id = '.$sql['from_store'].'
			');
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE (type = \'confirm_transfer\' OR type = \'request_transfer\') AND id = '.$id);
			die('OK');
		} else 
			die('no_acc');
	break;
	
	
	/*
	* Confirm transfer
	*/
	case 'confirm_transfer':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$sql = db_multi_query('SELECT to_manager, to_store, received, inventory_ids, request, quantity FROM `'.DB_PREFIX.'_inventory_transfer` WHERE id = '.$id);
		$inv = array_column(db_multi_query('SELECT id, quantity FROM `'.DB_PREFIX.'_inventory` WHERE id IN ('.$sql['inventory_ids'].')', true), 'quantity', 'id');
		$quantity = json_decode($sql['quantity'], true);
		
		if (($sql['to_manager'] == $user['id'] OR in_to_array(1, $user['group_ids'])) AND $sql['received'] == 0 AND $user['confirm_inventory_transfer'] AND $sql['request'] == 0) {
			db_query('UPDATE `'.DB_PREFIX.'_inventory_transfer` SET received = 1, to_date = \''.date('Y-m-d H:i:s', time()).'\' WHERE id ='.$id);
			
			foreach(explode(',', $sql['inventory_ids']) as $id) {
				if ($quantity[$id] == $inv[$id])
					db_query('UPDATE `'.DB_PREFIX.'_inventory` SET object_id = '.$sql['to_store'].' WHERE id = '.$id);
				else {
					db_query('UPDATE `'.DB_PREFIX.'_inventory` SET quantity = quantity - '.($quantity[$id] ?? 1).' WHERE id = '.$id);
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory` (model, os_id, price,object_id, options, type, type_id, category_id, model_id, owner_type, object_owner, barcode, quantity, accessories, cr_user, cr_date, confirmed
							) SELECT i.model, i.os_id, i.price, '.$sql['to_store'].', i.options, i.type, i.type_id, i.category_id, i.model_id, "external", i.object_owner, i.barcode, '.($quantity[$id] ?? 1).', i.accessories, '.$user['id'].', \''.date('Y-m-d H:i:s').'\', 1 FROM `'.DB_PREFIX.'_inventory` AS i WHERE i.id = '.$id);
				}
			}
			
			db_query('
				INSERT INTO 
				`'.DB_PREFIX.'_activity` SET 
					user_id = \''.$user['id'].'\', 
					event = \'confirm transfer\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					event_id = '.$id.',
					object_id = '.$sql['to_store'].'
			');
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE (type = \'confirm_transfer\' OR type = \'request_transfer\') AND id = '.$id);
			die('OK');
		} else 
			die('no_acc');
	break;
	
	/*
	* Confirm transfer request
	*/
	case 'confirm_transfer_request':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$sql = db_multi_query('SELECT request, to_store FROM `'.DB_PREFIX.'_inventory_transfer` WHERE id = '.$id);
		if ($sql['request'] == 1 AND $user['confirm_inventory_transfer']) {
			db_query('UPDATE `'.DB_PREFIX.'_inventory_transfer` SET request = 0, from_date = \''.date('Y-m-d H:i:s', time()).'\', from_manager = '.$user['id'].' WHERE id ='.$id);
			db_query('
				INSERT INTO 
				`'.DB_PREFIX.'_activity` SET 
					user_id = \''.$user['id'].'\', 
					event = \'confirm transfer request\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					event_id = '.$id.',
					object_id = '.$sql['to_store'].'
			');
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE type = \'request_transfer\' AND id = '.$id);
			die('OK');
		} else 
			die('no_acc');
	break;
	
	/*
	* Make transfer
	*/
	case 'make_transfer':
		is_ajax() or die('Hacking attempt!');
		$from_object = intval($_POST['from_object']);
		$to_object = intval($_POST['to_object']);
		$receive_staff = intval($_POST['receive_staff']);
		$request = text_filter($_POST['request'], 10, false);
		
		if (!$_POST['trItems'])
			die('no_items');
		
		$trItems = implode(',', array_map(function($a) {
			return explode(':', $a)[0];
		}, explode(',', $_POST['trItems'])));
		
		if (!$user['create_inventory_transfer'])
			die('no_acc');
		
		if (!$from_object OR !$to_object)
			die('no_store');
		
		if ($from_object == $to_object)
			die('same_store');
		
		if (!$request AND !$receive_staff)
			die('no_staff');
		
		if (!$trItems)
			die('no_items');
		
		$q = [];
		if (is_array(explode(',', $_POST['trItems']))) {
			foreach(explode(',', $_POST['trItems']) as $e) {
				$e = explode(':', $e);
				$q[$e[0]] = $e[1] ?? 1; 
			}
		}
		
		db_query('INSERT INTO `'.DB_PREFIX.'_inventory_transfer` SET 
			from_store = \''.$from_object.'\',
			from_date = \''.date('Y-m-d H:i:s', time()).'\',
			from_manager = \''.($request ? 0 : $user['id']).'\',
			to_store = \''.$to_object.'\',
			to_manager = \''.($request ? $user['id'] : $receive_staff).'\',
			inventory_ids = \''.$trItems.'\',
			quantity = \''.json_encode($q).'\'
		'.(
			$request ? ', request = 1' : ''
		));
		
		$id = intval(mysqli_insert_id($db_link));
		
		$object_staff = implode(',', array_column(db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
			u.id
		FROM `'.DB_PREFIX.'_users` u
		LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON FIND_IN_SET(u.id, o.managers) OR FIND_IN_SET(u.id, o.staff)
		WHERE u.del = 0  
			AND ((FIND_IN_SET(u.id, o.managers) OR FIND_IN_SET(u.id, o.staff)) AND o.id = '.$from_object.') OR FIND_IN_SET(1, u.group_ids) OR FIND_IN_SET(2, u.group_ids)
		ORDER BY u.id DESC LIMIT 20', true), 'id'));
		
		db_query('
			INSERT INTO 
			`'.DB_PREFIX.'_activity` SET 
				user_id = \''.$user['id'].'\', 
				event = \'new transfer'.($request ? ' request' : '').'\',
				event_id = '.$id.',
				date = \''.date('Y-m-d H:i:s', time()).'\',
				object_id = '.$from_object.',
				tr_staff = '.($request ? $user['id'] : $receive_staff).',
				tr_store = '.$to_object.'
		');
		
		db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET type = \''.($request ? 'request_transfer' : 'confirm_transfer').'\', id = '.$id.', staff = \''.($request ? $object_staff : $receive_staff).'\'');
		if (!$request) {
			send_push($receive_staff, [
				'type' => 'purchase',
				'id' => '/inventory/transfer/view/'.$id,
				'name' => $user['uname'],
				'lastname' => $user['ulastname'],
				'message' => $lang['Transfer'].' #'.$id.' '.$lang['tadded'].'. '.$lang['PleaseConfirm']
			]);
		}
		die('OK');
	break;
	
	/*
	* Transfer page
	*/
	case 'transfer':
		if ($route[2] == 'add' OR $route[2] == 'view' OR $route[2] == 'request') {
			$id = intval($route[3]);
			$meta['title'] = $lang['TransferInventories'];
			$row = [];
			$inventory = '';
			$forms = '';
			
			if ($route[2] == 'view') {
				$row = db_multi_query('
					SELECT 
						t.*,
						CONCAT(fu.name, \' \', fu.lastname) as fu_name,
						CONCAT(tu.name, \' \', tu.lastname) as tu_name,
						fs.name as fs_name,
						ts.name as ts_name,
						TO_SECONDS(ADDTIME(t.from_date, 1000)) - TO_SECONDS(NOW()) as to_confirm
					FROM `'.DB_PREFIX.'_inventory_transfer` t
					LEFT JOIN `'.DB_PREFIX.'_users` fu
						ON fu.id = t.from_manager
					LEFT JOIN `'.DB_PREFIX.'_users` tu
						ON tu.id = t.to_manager
					LEFT JOIN `'.DB_PREFIX.'_objects` fs
						ON fs.id = t.from_store
					LEFT JOIN `'.DB_PREFIX.'_objects` ts
						ON ts.id = t.to_store
					WHERE t.id = '.$id);
					
/* 				if($ser['id'] == 16){
					echo row['inventory_ids'];
					die;
				} */
					
				$quantity = json_decode($row['quantity'], true);
				
				foreach($inv = db_multi_query('
					SELECT 
						i.id,
						i.price,
						i.model,
						t.name as type_name,
						c.name as category_name,
						m.name as model_name
					FROM `'.DB_PREFIX.'_inventory` i
					LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
						ON t.id = i.type_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
						ON c.id = i.category_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_models` m 
						ON m.id = i.model_id
					WHERE i.id IN ('.$row['inventory_ids'].')
				', true) as $item) {
					
					$inventory .= '<div class="invItem">
						<a href="/inventory/view/'.$item['id'].'" target="_blank">'.(
						($item['type_name'] ? $item['type_name'].' ' : '').
						($item['category_name'] ? $item['category_name'].' ' : '').
						($item['model_name'] ? $item['model_name'].' ' : '').
						($item['model'] ?: '')
					).'</a> [Q-ty: '.($quantity[$item['id']] ?? 1).']<span class="trPrice">$'.(($quantity[$item['id']] ?? 1) * $item['price']).'</span></div>';
				}

				foreach(db_multi_query('
					SELECT id, name FROM `'.DB_PREFIX.'_forms`
					WHERE FIND_IN_SET(\'transfer\', types) ORDER BY id LIMIT 50'
				, true) as $form){
					$forms .= '<li><a href="javascript:to_print(\'/forms?type=transfer&id='.$form['id'].'&transfer_id='.$id.'\', \'transfer №'.$id.'\');" target="_blank">'.$form['name'].'</a></li>';
				}
				
			}
			tpl_set('inventory/transfer/form', [
				'send_staff' => $route[2] == 'add' ? $user['uname'].' '.$user['ulastname'] : $row['fu_name'],
				'id' => $id,
				'receive_staff' => ($route[2] == 'request' ? $user['uname'].' '.$user['ulastname'] : $row['tu_name']),
				'from_store' => $row['fs_name'],
				'to_store' => $row['ts_name'],
				'inventory' => $inventory,
				'forms' => $forms,
				'time_confirm' => $row['to_confirm'] > 0 ? gmdate('H:i:s', $row['to_confirm']) : 0,
				'sec_confirm' => $row['to_confirm']
			], [
				'forms' => $forms,
				'view' => ($route[2] == 'view'),
				'show_uMore' => $forms OR $row['del'] == 0,
				'del' => $row['del'] == 1,
				'can_confirm' => ($row['to_manager'] == $user['id'] OR in_to_array(1, $user['group_ids'])) AND $row['received'] == 0,
				'can_del' => ($row['from_manager'] == $user['id'] OR in_to_array(1, $user['group_ids'])) AND $row['del'] == 0,
				'confirmed' => $row['received'] == 1,
				'create' => $user['create_inventory_transfer'],
				'add-service' => $user['add_service'],
				'add-iservice' => $user['add_iservice'],
				'requested' => $row['request'] == 1,
				'request' => $route[2] == 'request',
				'time_confirm' => $row['to_confirm'] < 0 OR ABS($row['to_confirm']) < 3
			], 'content');
		} else {
			$meta['title'] = $lang['InventoryTransfers'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 20;

			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					t.*,
					CONCAT(fu.name, \' \', fu.lastname) as fu_name,
					CONCAT(tu.name, \' \', tu.lastname) as tu_name,
					fs.name as fs_name,
					ts.name as ts_name,
					TO_SECONDS(ADDTIME(t.from_date, 1000)) - TO_SECONDS(NOW()) as to_confirm
				FROM `'.DB_PREFIX.'_inventory_transfer` t
				LEFT JOIN `'.DB_PREFIX.'_users` fu
					ON fu.id = t.from_manager
				LEFT JOIN `'.DB_PREFIX.'_users` tu
					ON tu.id = t.to_manager
				LEFT JOIN `'.DB_PREFIX.'_objects` fs
					ON fs.id = t.from_store
				LEFT JOIN `'.DB_PREFIX.'_objects` ts
					ON ts.id = t.to_store
				WHERE 1 '.(
					$query ? 'AND (CONCAT(fu.name, \' \', fu.lastname) LIKE \'%'.$query.'%\' OR CONCAT(tu.name, \' \', tu.lastname) LIKE \'%'.$query.'%\' OR ts.name LIKE \'%'.$query.'%\' OR fs.name LIKE \'%'.$query.'%\') ' : ''
				).'ORDER BY t.id DESC LIMIT '.($page*$count).', '.$count, true
			)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/transfer/item', [
						'id' => $row['id'],
						'send_staff' => $row['fu_name'],
						'receive_staff' => $row['tu_name'],
						'from_store' => $row['fs_name'],
						'to_store' => $row['ts_name'],
						'confirmed' => $row['received'] == 1 ? 'confirmed' : 'not-confirmed',
						'deleted' => $row['del'] == 1 ? 'deleted' : '',
						'requested' => $row['request'] == 1 ? 'request' : ''
					], [
						'del' => $row['del'] == 1,
						'confirmed' => $row['received'] == 1,
						'requested' => $row['request'] == 1,
						'can_confirm' => ($row['to_manager'] == $user['id'] OR in_to_array(1, $user['group_ids'])) AND $row['received'] == 0,
						'can_del' => ($row['from_manager'] == $user['id'] OR in_to_array(1, $user['group_ids'])) AND $row['del'] == 0,
						'time_confirm' => $row['to_confirm'] < 0
					], 'transfers');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noTransfers']
				], false, 'services');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['transfers'],
				]));
			}
			tpl_set('inventory/transfer/main', [
				'title' => 'transfers',
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'transfers' => $tpl_content['transfers']
			], [
				'transfers' => $user['transfers'],
				'create' => $user['create_inventory_transfer'],
				'add-service' => $user['add_service'],
				'add-iservice' => $user['add_iservice']
			], 'content');
		}
	break;
	
	
	/*
	* Owner Price
	*/
	case 'owner_price':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$price = floatval($_POST['price']);
		$type = text_filter($_POST['type'], 10, false);
		
		if (in_array(1, explode(',', $user['group_ids']))) {
			if ($price <= 0) 
				die('empty');
			else {
				db_query('UPDATE `'.DB_PREFIX.'_inventory` SET '.($type == 'price' ? 'price' : 'purchase_price').' = '.$price.' WHERE id = '.$id);
				die('OK');
			}
		} else 
			die('no_acc');
	break;
	/*
	* Upcharge all
	*/
	case 'allUpcharge':
		is_ajax() or die('Hacking attempt!');
		
		$lId = intval($_POST['lId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$all = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				id,
				name,
				price
			FROM `'.DB_PREFIX.'_inventory_upcharge` 
			WHERE 1 '.(
				$nIds ? ' AND id NOT IN('.$nIds.')' : ''
			).(
				$lId ? ' AND id < '.$lId : ''
			).(
				$query ? ' AND name LIKE \'%'.$query.'%\'' : ''
			).' ORDER BY id DESC LIMIT 20', true
		);
		$res_count = intval(
			mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]
		);
		
		die(json_encode([
			'list' => $all,
			'count' => $res_count,
		]));
		
		die;
	break;
	
	/*
	* Delete upcharge
	*/
	case 'del_upcharge':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_upcharge']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_upcharge` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('no_acc');
	break;
	
	/*
	* Send upcharge
	*/
	case 'send_upcharge':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		if ((!$id AND $user['add_upcharge']) OR ($user['edit_upcharge'] AND $id)) {
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_inventory_upcharge` SET 
				name = \''.text_filter($_POST['name'], 255, false).'\',
				price = \''.floatval($_POST['price']).'\''.(
				$id ? ' WHERE id = '.$id : ''
			));
		
			die('OK');
		} else 
			die('forbidden');
	break;
	
	/*
	* Upcharge
	*/
	case 'upcharge':
		$meta['title'] = $lang['UpchargeServices'];

		$query = text_filter($_REQUEST['query'], 255, false);
		$page = intval($_REQUEST['page']);
		$status = text_filter($_REQUEST['status'], 20, false);
		$type = text_filter($_REQUEST['type'], 20, false);
		$count = 20;

		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				i.*
				FROM `'.DB_PREFIX.'_inventory_upcharge` i
			WHERE 1 '.(
				$query ? 'AND i.name LIKE \'%'.$query.'%\' ' : ''
			).($query ? '' : 'ORDER BY i.id DESC').' LIMIT '.($page*$count).', '.$count, true
		)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('inventory/upcharge/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'price' => $row['price'],
				], [
					'edit' => $user['edit_upcharge'],
					'del' => $user['delete_upcharge'],
					'action' => $user['edit_upcharge'] OR $user['delete_upcharge']
				], 'upcharge');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['upcharge'],
			]));
		}
		tpl_set('inventory/upcharge/main', [
			'res_count' => $res_count,
			'query' => $query,
			'more' => $left_count ? '' : ' hdn',
			'upcharges' => $tpl_content['upcharge'] ?: '<div class="noContent">'.$lang['noUpcharge'].'</div>'
		], [
			'add-service' => $user['add_service'],
			'add-iservice' => $user['add_iservice'],
			'create' => $user['create_inventory_transfer'],
			'add' => $user['add_upcharge']
		], 'content');
	break;
	
	/*
	* Onsite forms
	*/
    case 'onsite_forms':
        $meta['title'] = $lang['OnsiteForms'];
        tpl_set('inventory/onsite_forms', [
            'sms_onsite' => $config['sms_onsite'],
            'form_onsite' => $config['form_onsite']
        ], false, 'content');
    break;

	/*
	* Send Onsite forms
	*/
    case 'send_onsite_forms':
		if (in_array(1, explode(',', $user['group_ids']))) {
			$config['sms_onsite'] = text_filter($_POST['sms_form'], 200, false);
			$config['form_onsite'] = text_filter($_POST['email_form']);
            if(conf_save())
			    die('OK');
		} else
			die('no_acc');
    break;
	
	/*
	* Delete request
	*/
	case 'del_request':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_request']){
			db_query('DELETE FROM `'.DB_PREFIX.'_requested` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('no_acc');
	break;
	
	/*
	*  Confirm request
	*/
	case 'confirm_request':
		is_ajax() or die('Hacking attempt!');
		if ($user['confirm_request']) {
			db_query('UPDATE `'.DB_PREFIX.'_requested` SET confirm_user = '.$user['id'].', confirm_date = \''.date('Y-m-d H:i:s', time()).'\' WHERE id ='.intval($_POST['id']));
			die('OK');
		} else 
			die('no_acc');
	break;
	
	/*
	* Send request
	*/
	case 'send_request':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		if (!$id OR ($user['edit_request'] AND $id)) {
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_requested` SET 
				name = \''.text_filter($_POST['name'], 255, false).'\',
				type = \''.text_filter($_POST['type'], 10, false).'\',
				price = \''.floatval($_POST['price']).'\''.(
					!$id ? ', create_user = '.$user['id'].', date = \''.date('Y-m-d H:i:s', time()).'\'' : ''
			).(
				$id ? ' WHERE id = '.$id : ''
			));
		
			die('OK');
		} else 
			die('forbidden');
	break;
	
	/*
	* Requested
	*/
	case 'requested':
		$meta['title'] = $lang['Requests'];
		if($user['service'] > 0){
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$status = text_filter($_POST['status'], 20, false);
			$type = text_filter($_POST['type'], 20, false);
			$count = 20;

			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					i.*,
					u.name as cr_name,
					u.lastname as cr_lastname,
					c.name as cn_name,
					c.lastname as cn_lastname
					FROM `'.DB_PREFIX.'_requested` i
					LEFT JOIN  `'.DB_PREFIX.'_users` u
						ON u.id = i.create_user
					LEFT JOIN  `'.DB_PREFIX.'_users` c
						ON c.id = i.confirm_user
				WHERE 1 '.(
					$status == 'confirmed' ? 'AND i.confirm_user > 0 ' : ($status == 'notconfirmed' ? 'AND i.confirm_user = 0 ' : '')
				).(
					$type ? 'AND i.type = \''.$type.'\' ' : ''
				).(
					$query ? 'AND i.name LIKE \'%'.$query.'%\' OR CONCAT(
							u.name, u.lastname
						) LIKE \'%'.$query.'%\' OR u.email LIKE \'%'.$query.'%\' OR u.phone LIKE \'%'.$query.'%\' ' : ''
				).($query ? '' : 'ORDER BY i.confirm_user, i.id DESC').' LIMIT '.($page*$count).', '.$count, true
			)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/requested/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'type' => $row['type'],
						'price' => $row['price'],
						'cr-date' => $row['date'],
						'cr-user' => '<a href="/users/view/'.$row['create_user'].'">'.$row['cr_name'].' '.$row['cr_lastname'].'</a>',
						'cn-date' => $row['confirm_date'],
						'cn-user' => '<a href="/users/view/'.$row['confirm_user'].'">'.$row['cn_name'].' '.$row['cn_lastname'].'</a>',
						'confirmed' => ($row['confirm_user'] > 0 ? 'confirmed' : 'not-confirmed')
					], [
						'confirmed' => $row['confirm_user'] > 0,
						'service' => $row['type'] == 'service',
						'edit' => $user['edit_request'],
						'confirm' => $user['confirm_request'],
						'del' => $user['delete_request']
					], 'requests');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noRequests']
				], false, 'content');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['requests'],
				]));
			}
			tpl_set('inventory/requested/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'requests' => $tpl_content['requests']
			], [
				'stock' => $route[1] == 'stock',
				'create' => $user['create_inventory_transfer'],
				'add-service' => $user['add_service'],
				'add-iservice' => $user['add_iservice']
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => $lang['Forbidden'],
			], [], 'content');
		}
	break;
	
	/*
	* On site all
	*/
	case 'AllOnsite':
		is_ajax() or die('Hacking attempt!');
		
		$lId = intval($_POST['lId']);
		$oId = intval($_POST['oId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$all = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				id,
				name,
				price,
				currency
			FROM `'.DB_PREFIX.'_inventory_onsite` 
			WHERE confirmed = 1 AND (FIND_IN_SET('.$oId.', object_id) OR object_id = \'\')'.(
				$nIds ? ' AND id NOT IN('.$nIds.')' : ''
			).(
				$lId ? ' AND id < '.$lId : ''
			).(
				$query ? ' AND name LIKE \'%'.$query.'%\'' : ''
			).' ORDER BY id DESC LIMIT 20', true
		);
		$res_count = intval(
			mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]
		);
		
		die(json_encode([
			'list' => $all,
			'count' => $res_count,
		]));
		
		die;
	break;
	
	/*
	* On site services
	*/
	case 'onsite':
		if ($route[2] == 'add' OR $route[2] == 'edit') {
			$id = intval($route[3]);
			$meta['title'] = ($route[2] == 'add' ? $lang['add'] : $lang['Edit']).' '.$lang['onsiteService'];
			$sql = [];
			
			if ($user['edit_service']) {
				if ($route[2] == 'edit') {
					$sql = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS
							*
							FROM `'.DB_PREFIX.'_inventory_onsite`
						WHERE 1 '.(
							$query ? 'AND name LIKE \'%'.$query.'%\'' : ''
						).' AND id = '.$id
					);
				}
				
				$objects = [];
				if ($sql['object_id']) {
					foreach(db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects` WHERE id IN ('.$sql['object_id'].')', true) as $obj) {
						$objects[$obj['id']] = [
							'name' => $obj['name']
						];
					}
				}
				
				$currency = '';
				foreach($config['currency'] as $k => $c) {
					$currency .= '<option value="'.$k.'"'.($k == $sql['currency'] ? ' selected' : '').'>'.$k.' ('.$c['symbol'].')</option>';
				}
				
				tpl_set('inventory/onsite/form', [
					'title' => $meta['title'],
					'id' => $id,
					'name' => $sql['name'],
					'desc' => $sql['description'],
					'time' => $sql['time'],
					'price' => $sql['price'],
					'currency' => $currency,
					'type' => $sql['type'],
					'calls' => $sql['calls'],
					'add_hour' => $sql['add_hour_pay'],
					'objects' => json_encode($objects),
					'start_period' => $sql['start_period'],
					'end_period' => $sql['end_period'],
					'send' => $route[2] == 'add' ? $lang['Send'] : $lang['Save']
				], [
					'add-service' => $user['add_service'],
					'add-iservice' => $user['add_iservice'],
					'edit' => $route[2] == 'edit',
					'create' => $user['create_inventory_transfer'],
					'notconfirmed' => $sql['confirmed'] == 0
				], 'content');
			} else {
				tpl_set('forbidden', [
					'text' => $lang['noAcc']
				], [
				], 'content');
			}
		} else {
			$meta['title'] = $lang['OnsiteServices'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 20;

			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					id, name, confirmed
					FROM `'.DB_PREFIX.'_inventory_onsite`
				WHERE 1 '.(
					$query ? 'AND name LIKE \'%'.$query.'%\' ' : ''
				).'ORDER BY id DESC LIMIT '.($page*$count).', '.$count, true
			)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/onsite/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'confirmed' => $row['confirmed'] == 1 ? $lang['confirmed'] : $lang['unconfirmed']
					], [
						'edit-service' => $user['edit_service'],
						'delete-service' => $user['delete_service'],
						'menu' => ($user['edit_service'] OR $user['delete_service'])
					], 'services');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noOnsite']
				], false, 'services');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['services'],
				]));
			}
			tpl_set('inventory/onsite/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'services' => $tpl_content['services']
			], [
				'create' => $user['create_inventory_transfer'],
				'add-service' => $user['add_service'],
				'add-iservice' => $user['add_iservice']
			], 'content');
		}
	break;
	
	/*
	* Send onsite
	*/
	case 'send_onsite':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		if (($user['add_service'] AND !$id) OR ($user['edit_service'] AND $id)) {
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_inventory_onsite` SET 
				name = \''.text_filter($_POST['name'], 255, false).'\',
				description = \''.text_filter($_POST['desc'], null, false).'\',
				type = \''.text_filter($_POST['type'], 20, false).'\',
				object_id = \''.ids_filter($_POST['store']).'\',
				time = \''.floatval($_POST['time']).'\',
				calls = \''.intval($_POST['calls']).'\',
				add_hour_pay = \''.floatval($_POST['add_hour']).'\',
				price = \''.floatval($_POST['price']).'\',
				currency = \''.text_filter($_POST['currency'], 25, false).'\',
				start_period = \''.text_filter($_POST['start_period'], 50, false).'\',
				end_period = \''.text_filter($_POST['end_period'], 50, false).'\' '.(
					!$id ? ', confirmed = '.($user['confirm_service'] ? '1' : '0') : ''
			).(
				$id ? ' WHERE id = '.$id : ''
			));
			
			$id = $id ?: intval(mysqli_insert_id($db_link));
			echo $id;
			die;
		} else 
			die('forbidden');
	break;
	
	/*
	* Delete On site
	*/
	case 'del_onsite':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_service']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_onsite` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	*  Confirm on site
	*/
	case 'confirm_onsite':
		is_ajax() or die('Hacking attempt!');
		if ($user['confirm_service']) {
			db_query('UPDATE `'.DB_PREFIX.'_inventory_onsite` SET confirmed = 1 WHERE id ='.intval($_POST['id']));
			die('OK');
		} else {
			die('no_acc');
		}
	break;
	
	/*
	* Get sublocations
	*/
	case 'get_sublocations':
		is_ajax() or die('Hacking attempt!');
		$res = db_multi_query('
			SELECT 
				i.location_count
			FROM `'.DB_PREFIX.'_inventory` i
			WHERE i.id = '.intval($_POST['inventory'])
		);
		/* $options = '';
		if ($res['count'] > 0) {
			$options = '<option value="0">Not selected</option>';
			for($i = 1; $i <= $res['count']; $i++) {
				$options .= '<option value="'.$i.'"'.($res['location_count'] == $i ? ' selected' : '').'>'.$i.'</option>';
			}
		} */
		echo $res['location_count'] ?: 0;
		die;
	break;
	
	/*
	* Dublicate service
	*/
	case 'dub_service':
		is_ajax() or die('Hacking attempt!');
		db_query('INSERT INTO `'.DB_PREFIX.'_inventory` (name, price,object_id, options, type, type_id
			) SELECT i.name, i.price,i.object_id, i.options, i.type, i.type_id FROM `'.DB_PREFIX.'_inventory` AS i WHERE i.id = '.intval($_POST['id'])
		);

		echo intval(mysqli_insert_id($db_link));
		die;
	break;
	
	/*
	*  All inventary
	*/
	case 'confirm_tradein':
		is_ajax() or die('Hacking attempt!');
		$id = $_POST['id'];
		db_query('UPDATE `'.DB_PREFIX.'_tradein` SET
				cn_user = '.$user['id'].',
				cn_date = \''.date('Y-m-d H:i:s', time()).'\',
				cn_price = '.floatval($_POST['price']).',
				confirmed = 1
			WHERE id = '.$id
		);
		
		$info = db_multi_query('SELECT t.*, i.purchase_price, i.price, i.object_owner 
			FROM `'.DB_PREFIX.'_tradein` t
			LEFT JOIN `'.DB_PREFIX.'_inventory` i
				ON t.inventory_id = i.id
			WHERE t.id = '.$id);
		
		if ($info['price'] != floatval($_POST['price'])) {
			db_query('UPDATE `'.DB_PREFIX.'_inventory` SET
				price = \''.floatval($_POST['price']).'\' WHERE id = '.$info['inventory_id']
			);
		}
		
		
		// ------------------------------------------------------------------------------- //
		//if($user['store_id'] > 0){
			/* $sql_ = db_multi_query('
				SELECT
					SUM(tb1.point) as sum,
					tb2.points
				FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
					 `'.DB_PREFIX.'_objects` tb2
				WHERE tb1.staff_id = '.$info['cr_user'].' AND tb1.date >= DATE_SUB(
					NOW(), INTERVAL 1 HOUR
				) AND tb1.rate_point = 1 AND tb2.id = '.$info['object_owner']
			); */
			$points = (floatval($_POST['price'])-floatval($info['purchase_price']))*floatval($config['user_points']['trade_in']['points'])/100;
			//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$info['cr_user'].',
					action = \'trade_in\',
					object_id = '.$info['object_owner'].',
					inventory_id = '.$info['inventory_id'].',
					point = \''.$points.'\''
				);	//min_rate = '.$sql_['points'].',
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$info['cr_user']
				);
			/* } else {
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$info['cr_user'].',
					action = \'trade_in\',
					min_rate = '.$sql_['points'].',
					object_id = '.$info['object_owner'].',
					inventory_id = '.$info['inventory_id'].',
					point = \''.$points.'\',
					rate_point = 1'
				);	
			} */
		//}
		// ------------------------------------------------------------------------------- //
		die('OK');
	break;
	
	/*
	* To object
	*/
	/* case 'to_object':
		is_ajax() or die('Hacking attempt!');
		db_query('UPDATE `'.DB_PREFIX.'_inventory` SET
			customer_id = \'0\',
			owner_type = \'internal\',
			object_owner = '.intval($_POST['object']).',
			purchase_price = \''.floatval($_POST['purchase']).'\',
			price = \''.floatval($_POST['sale']).'\' WHERE id = '.intval($_POST['id'])
		);
		
		db_query('INSERT INTO `'.DB_PREFIX.'_tradein` SET
			inventory_id = '.intval($_POST['id']).',
			cr_user = '.$user['id'].',
			cr_date = \''.date('Y-m-d H:i:s', time()).'\',
			cr_price = '.floatval($_POST['sale']).(
				(in_to_array('1,2', $user['group_ids'])) ? ', 
					confirmed = 1,
					cn_user = '.$user['id'].',
					cn_date = \''.date('Y-m-d H:i:s', time()).'\',
					cn_price = '.floatval($_POST['sale']) : ''
			)
		);
		
		if($user['store_id'] > 0 AND in_to_array('1,2', $user['group_ids'])){
			$sql_ = db_multi_query('
				SELECT
					SUM(tb1.point) as sum,
					tb2.points
				FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
					 `'.DB_PREFIX.'_objects` tb2
				WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(NOW(), INTERVAL 1 HOUR) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
			);
			$points = (floatval($_POST['sale'])-floatval($_POST['purchase']))*floatval($config['user_points']['trade_in']['points'])/100;
			if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'trade_in\',
					min_rate = '.$sql_['points'].',
					object_id = '.$user['store_id'].',
					point = \''.$points.'\''
				);	
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$user['id']
				);
			} else {
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'trade_in\',
					min_rate = '.$sql_['points'].',
					object_id = '.$user['store_id'].',
					point = \''.$points.'\',
					rate_point = 1'
				);	
			}
		}

		die('OK');
	break; */
	
	/*
	* To store
	*/
	case 'to_store':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$inv = db_multi_query('SELECT images FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$id);
		if (!$inv['images'])
			die('no_images');
		
		db_query('UPDATE `'.DB_PREFIX.'_inventory` SET commerce = 1 WHERE id = '.$id);
		
		if ($user['store_id'] > 0){
			/* $sql_ = db_multi_query('
				SELECT
					SUM(tb1.point) as sum,
					tb2.points
				FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
					 `'.DB_PREFIX.'_objects` tb2
				WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
					NOW(), INTERVAL 1 HOUR
				) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
			); */
			
			$points = floatval($config['user_points']['ecommerce']['add_to_store']);
			
			//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'add to e-commerce\',
					inventory_id = '.$id.',
					object_id = '.$user['store_id'].',
					point = \''.$points.'\''
				);	//min_rate = '.$sql_['points'].',
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$user['id']
				);
			/* } else {
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'add to e-commerce\',
					min_rate = '.$sql_['points'].',
					inventory_id = '.$id.',
					object_id = '.$user['store_id'].',
					point = \''.$points.'\',
					rate_point = 1'
				);	
			} */
		}
			
		die('OK');
	break;
	
	/*
	* Inventory all
	*/
	case 'all':
		is_ajax() or die('Hacking attempt!');
		
		$lId = intval($_REQUEST['lId']);
		$oId = intval($_REQUEST['oId']);
		$object = intval($_REQUEST['object']);
		$user_query = intval($_REQUEST['user']);
		$by_user = intval($_REQUEST['by_user']);
		$store = intval($_REQUEST['store']);
		$nIds = ids_filter($_REQUEST['nIds']);
		if(isset($_REQUEST['q']))
			$_REQUEST['query'] = $_REQUEST['q'];
		$query = text_filter($_REQUEST['query'], 100, false);
		$type = text_filter($_REQUEST['type'], 100, false);

		
		$objects = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
			i.id, 
			REPLACE(IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name), \'"\', \'\') as name, 
			i.purchase_price as cost_price,
			i.price,
			CONCAT(\'Price: \', \'<font color="#4c91bb">\', \'$\', i.price, \'</font>\''.(
				$type == 'stock' ? ', \' Quantity:\', \'<font color="#4c91bb">\', i.quantity, \'</font>\'' : ''
			).') as descr,
			i.currency,
			i.quantity,
			i.purchase_price as purchase,
			c.name as catname, 
			i.options as options,
			i.parts_required as req,
			o.name as object,
			i.quantity
			FROM `'.DB_PREFIX.'_inventory` i 
			LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c 
				ON i.category_id = c.id
			LEFT JOIN `'.DB_PREFIX.'_objects` o 
				ON o.id = i.object_id
			LEFT JOIN  `'.DB_PREFIX.'_inventory_models` m
				ON m.id = i.model_id
			LEFT JOIN  `'.DB_PREFIX.'_inventory_types` t
				ON t.id = i.type_id
			WHERE 1'.(
				$type ? ' AND i.type = \''.$type.'\'' : ''
			).(
				$nIds ? ' AND i.id NOT IN('.$nIds.')' : ''
			).(
				intval($_POST['noCust']) ? ' AND i.customer_id = 0' : ''
			).(
				$oId ? ' AND (FIND_IN_SET('.$oId.', i.object_id) OR IF(i.type = \'service\', i.object_id = \'all\', 0))' : ''
			).(
				$by_user ? ($user_query ? ' AND i.customer_id = '.$user_query : ' AND i.customer_id < 0 ') : ''
			).(
				$lId ? ' AND i.id < '.$lId : ''
			).(
				$object ? ($user['service_by_store'] ? ' AND (FIND_IN_SET('.$object.', i.object_id) OR IF(i.type = \'service\', i.object_id = \'all\', 0))' : '') : ''
			).(
				$store ? ' AND (i.commerce = 1 AND i.type = \'stock\' OR i.commerce = 0 AND i.type = \'service\') ': ''
			).(
				$query ? ' AND IF(i.type = \'service\', 
									i.name LIKE \'%'.$query.'%\', 
									(t.name LIKE \'%'.$query.'%\' OR m.name LIKE \'%'.$query.'%\' OR 
									c.name LIKE \'%'.$query.'%\' OR i.model LIKE \'%'.$query.'%\' OR 
									i.barcode LIKE \'%'.$query.'%\' OR i.id = \''.$query.'\' OR 
									IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name) LIKE \'%'.$query.'%\'))' : ''
			).' ORDER BY i.id DESC LIMIT 20', true
		);
		$res_count = intval(
			mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]
		);
		
		function has($var) {
			return(!$var);
		}
		
		/* if ($type == 'service') {
			foreach($objects as $k => $v) {
				if (array_filter(json_decode($v['options'], true), "has")) {
					unset($objects[$k]);
				}
			}
		} */
		
		print_r(json_encode([
			'list' => $objects,
			'count' => $res_count,
		]));
		
		die;
	break;
	
	/*
	*  All status history
	*/
	case 'status_history':
	
		is_ajax() or die('Hacking attempt!');
		
		$id = intval($_POST['id']);
		$lId = intval($_POST['lId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		
		$history = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				h.id, h.date, u.name, u.lastname, s.name as status
			FROM `'.DB_PREFIX.'_inventory_status_history` h
				INNER JOIN `'.DB_PREFIX.'_users` u ON h.staff_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_status` s ON h.status_id = s.id
			WHERE h.inventory_id = '.$id.(
				$nIds ? ' AND h.id NOT IN('.$nIds.')' : ''
			).(
				$lId ? ' AND h.id < '.$lId : ''
			).(
				$query ? ' AND u.name LIKE \'%'.$query.'%\'': ''
			).' ORDER BY h.id DESC LIMIT 20', true
		);
		$res_count = intval(
			mysqli_fetch_array(
				db_query('SELECT FOUND_ROWS()')
			)[0]
		);
		die(json_encode([
			'list' => $history,
			'count' => $res_count,
		]));
		
		die;
	break;
	
	/*
	* Status sort
	*/
	case 'stPriority':
		is_ajax() or die('Hacking attempt!');
		$i = 1;
		foreach($_POST as $row){
			db_query('UPDATE `'.DB_PREFIX.'_inventory_status` SET sort = '.$i.', not_priority = '.(
				$row['pr'] ? 1 : '0'
			).' WHERE id = '.$row['id']);
				$i++;	
		}
		die;
	break;

	/*
	* Save location
	*/
	case 'getLocation':
		is_ajax() or die('Hacking attempt!');
		$sId = intval($_POST['sId']);
		$row = db_multi_query('SELECT locations FROM `'.DB_PREFIX.'_inventory_locations` WHERE id = '.$id);
		echo json_encode($row['locations']);
		die;
	break;
	
	/*
	* Save os
	*/
	case 'save_os':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_inventory_os` SET name = \''.text_filter($_POST['name'], 50, false).'\''.(
			$id ? ' WHERE id = '.$id : ''
		));
		die('OK');
	break;
	
	/*
	* Delete OS
	*/
	case 'del_os':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		//if($user['delete_inventary']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_os` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		//} else
		//	exit('ERR');
	break;
	
	/*
	* Save location
	*/
	case 'location':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$warranty = intval($_POST['warranty']);
		
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_inventory_'.($warranty ? 'warranty_' : '').'status` SET 
			locations = \''.ids_filter($_POST['loc']).'\',
			service = \''.intval($_POST['service']).'\',
			inventory = \''.intval($_POST['inventory']).'\',
			'.(
				$warranty ? '' : 'assigned = \''.intval($_POST['assigned']).'\',
									remove_purchase = \''.intval($_POST['remove_purchase']).'\',
									note = \''.intval($_POST['note']).'\','
			).'
			purchase = \''.intval($_POST['purchase']).'\'
		'.(
			$id ? ' WHERE id = '.$id : ''
		));
		die('OK');
	break;
	
	/*
	* Locations
	*/
	case 'locations':
		tpl_set('inventory/locations/main', [], [], 'content');
	break;

	/*
	* Delete group
	*/
	case 'delGroup':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_inventory']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_types` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	*  Send types
	*/
	case 'send_inventory':
		is_ajax() or die('Hacking attempt!');
		
		$id = intval($_POST['id']);
		
		if ((!$id AND !$user['add_inventory']) OR ($id AND !$user['edit_inventory']))
			die('no_acc');
		
		if (text_filter($_POST['intype'], 15, false) == 'service' AND (($id && !$user['edit_iservice']) OR (!$id AND !$user['add_iservice'])))
			die('no_acc');
		
		if (text_filter($_POST['intype'], 15, false) == 'stock' AND !intval($_POST['customer']) AND floatval($_POST['price']) < min_price(floatval($_POST['purchase-price']), intval($_POST['object'])))
			die('min_price');
		
		
		$new = intval($_POST['id']);
		$optCharger = (int)$_POST['opt_charger'];
		$status_id = text_filter($_POST['status'], 8, false);
		if($_POST['opts']){
			$delete = '';
			if($_POST['delete']){
				foreach($_POST['delete'] as $del){
					$delete .= ', images = REPLACE(
						images, \''.$del.'|\', \'\'
					)';
				}
			}
			if($id AND text_filter($_POST['intype'], 15, false) == 'stock'){
				$h = db_multi_query('
				SELECT
					i.price,
					i.model,
					i.os_id as os,
					i.vendor_id as vendor,
					i.ver_os as os_version,
					i.serial,
					i.descr,
					i.purchase_price as purchace,
					i.main,
					i.owner_type,
					i.object_owner,
					i.category_id as category,
					i.customer_id as customer,
					i.object_id as object,
					i.status_id as status,
					i.store_category_id as storeCat,
					i.location_id as location,
					i.options as opts,
					o.name as os_name,
					c.name as brand,
					m.name as model_name,
					t.options,
					p.date as previous_date,
					p.point as previous_point,
					p.staff_id '.(intval($status_id) ? ', s.forfeit, s.point_group, s.name as status_name, s.sms as sms, s.sms_form as sms_form' : '').'
					FROM '.(intval($status_id) ? ' `'.DB_PREFIX.'_inventory_status` s,' : '').'
						 `'.DB_PREFIX.'_inventory_os` o,
						 `'.DB_PREFIX.'_inventory_categories` c,
						 `'.DB_PREFIX.'_inventory` i
					LEFT JOIN
						 `'.DB_PREFIX.'_inventory_types` t
						ON i.type_id = t.id
					LEFT JOIN
						 `'.DB_PREFIX.'_inventory_models` m
						ON i.model_id = m.id
					LEFT JOIN
						 `'.DB_PREFIX.'_inventory_status_history` p
						ON i.status_id = p.status_id AND p.inventory_id = '.$id.'
					WHERE i.id = '.$id.' AND o.id = '.intval($_POST['os']).' AND c.id = '.intval($_POST['category']).(intval($status_id) ? ' AND s.id = '.$status_id : '').' ORDER by p.date DESC LIMIT 1'
				);
				
				if ($h['status'] != $status_id AND $h['sms'] == 1 AND $h['sms_form'] > 0) {
							
					$usr = db_multi_query('SELECT u.id as uid, u.name, u.lastname, u.sms, f.content, s.sms as sms, s.sms_form as sms_form 
						FROM `'.DB_PREFIX.'_users` u,
							`'.DB_PREFIX.'_inventory_status` s
						LEFT JOIN `'.DB_PREFIX.'_forms` f
							ON f.id = s.sms_form
						WHERE s.id = '.$status_id.' 
							AND u.id = '.$h['customer_id']);
							
					$phone = $usr['sms'];

/* 					if (strlen($phone) >=10) {
						$rand = rand();
						$url = 'http://192.168.1.206/default/en_US/sms_info.html';
						$line = '1';
						$telnum = $phone;
						$smscontent = str_ireplace([
							'{name}',
							'{device}'
						], [
							$usr['name'].' '.$usr['lastname'],
							$h['brand'].' '.$h['model_name'].' '.$h['model']
						], $usr['content']); 
						$username = "admin";
						$password = "admin";
						
						$headers  = 'MIME-Version: 1.0'."\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
						$headers .= 'To: kuptjukvm@gmail.com'."\r\n";
						$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

						$fields = array(
							'line' => urlencode($line),
							'smskey' => urlencode($rand),
							'action' => urlencode('sms'),
							'telnum' => urlencode($telnum),
							'smscontent' => urlencode($smscontent),
							'send' => urlencode('send')
						);

						$fields_string = "";
						foreach($fields as $key=>$value) { 
							$fields_string .= $key.'='.$value.'&'; 
						}
						rtrim($fields_string, '&');

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
						curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
						curl_setopt($ch, CURLOPT_PORT, 80);
						curl_setopt($ch, CURLOPT_POST, count($fields));
						curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
						curl_exec($ch);
						curl_getinfo($ch);
						curl_close($ch);
					} */
				}
				$abs = [];
				foreach($_POST as $k => $v){
					if($k == 'id' OR (!$h[$k] AND !$v)) continue;
					if($h[$k] != $v){
						if($k == 'opts'){
							$v = json_decode($_POST['opts'], true);
							$opts = json_decode($h['options'], true);
							$opt = json_decode($h['opts'], true);
							foreach($v as $k2 => $v2){
								if($opt[$k2] !== $v2){
									$abs[$opts[$k2]['name']] = (is_array($v2) ? implode(',', array_map(function($a){
										global $k2, $opts;
										return $opts[$k2]['sOpts'][$a];
									}, $v2)) : (is_array($opts[$k2]['sOpts']) ? $opts[$k2]['sOpts'][$v2] : $v2));	
								}
							}
						} else if($k == 'status'){
							
							print_r($h);

							$point = 0;
							$sql_ = [];
							$rate_point = 1;
							
							// Is date
							if($h['forfeit']){
								db_query(
									'UPDATE `'.DB_PREFIX.'_users`
										SET points = points-'.intval($h['forfeit']).'
									WHERE id = '.$h['staff_id']
								);
								$point = -(int)$h['forfeit'];
							} else if($h['previous_date'] AND $h['point_group']){
								$time = ceil(
									(time()-strtotime($h['previous_date']))/60
								);
								$points = json_decode($h['point_group'], true);
								ksort($points);
								foreach($points as $k3 => $v){
									if($k3 <= $time OR $k3 == 0){
										$point = $v ?: 0;
										if($user['id'] == 17){
											$user['store_id'] = 2;
										}

										if ($user['store_id'] > 0){
											db_query(
												'UPDATE `'.DB_PREFIX.'_users`
													SET points = points+'.($v ?: 0).'
												WHERE id = '.$user['id']
											);
											$rate_point = 0;
										}
										break;
									}
								}
							}
							
							// Insert change log
							db_query(
								'INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									status_id = \''.$status_id.'\',
									staff_id = '.$user['id'].',
									object_id = '.intval($_POST['object']).',
									action = \'update_status\',
									rate_point = '.$rate_point.',
									inventory_id = '.$id.(
										$h['previous_date'] ? ', point = \''.$point.'\'' : ''
									)
							);
							
							$abs[$k] = ($h['status_name'] ?: $v);

						} else if($k == 'os'){
							$abs[$k] = $h['os_name'];
						} else if($k == 'category'){
							$abs['Brand'] = $h['brand'];
						} else {
							$abs[$k] = $v;
						}
					}
				}
				if($abs){
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_history` SET
						inventory_id = '.$id.',
						object_id = '.intval($_POST['object']).',
						staff_id = '.$user['id'].',
						events = \''.json_encode($abs, JSON_UNESCAPED_UNICODE).'\''
					);
				}
			}
			
			
			if (text_filter($_POST['intype'], 20, false) == 'service') {
				$option = '';
				foreach(array_text_filter(json_decode($_POST['opts'], true), JSON_UNESCAPED_UNICODE) as $i => $v) {
					$options .= '"'.($v['id'] === intval($v['id']) ? hash('adler32', $v['name'].time()) : $v['id']).'":"'.$v['name'].'",';
				}
				$options = '{'.substr($options, 0, -1).'}';
				$category = intval($_POST['category']);
			} else {
				
				$type = intval($_POST['type']);
				$category = intval($_POST['category']);
				$model = intval($_POST['model']);
				if (!$model AND $_POST['model_new']) {
					$mdl = [];
					$mdl = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_models` WHERE name = \''.trim(text_filter($_POST['model_new'], 100, false)).'\' AND category_id = '.$category);
					if ($mdl != []) die('mdl_exists');
					$model = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_models` SET name = \''.trim(text_filter($_POST['model_new'], 100, false)).'\''.($category ? ', category_id = '.$category : ''));
					$model = intval(
						mysqli_insert_id($db_link)
					);
				}

				$location = intval($_POST['location']);
				if (!$location AND $_POST['location_new']) {
					if (!intval($_POST['object'])) die('no_object');
					$loc = [];
					$loc = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_objects_locations` WHERE name = \''.trim(text_filter($_POST['location_new'], 100, false)).'\' AND object_id = '.intval($_POST['object']));
					if ($loc != []) die('loc_exists');
					$location = db_query('INSERT INTO `'.DB_PREFIX.'_objects_locations` SET name = \''.trim(text_filter($_POST['location_new'], 100, false)).'\''.(intval($_POST['object']) ? ', object_id = '.intval($_POST['object']) : ''));
					$location = intval(
						mysqli_insert_id($db_link)
					);
				}
			}
			
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_inventory` SET
					name =\''.text_filter($_POST['name'], 50, false).'\',
					opt_charger =\''.($_POST['opt_charger'] ? 'YES' : 'NO').'\',
					model =\''.text_filter($_POST['smodel'], 50, false).'\',
					model_id =\''.$model.'\',
					currency = \''.text_filter($_POST['currency'] ?: 'USD', 25, false).'\',
					purchase_currency = \''.text_filter($_POST['purchase_currency'] ?: 'USD', 25, false).'\',
					ver_os =\''.text_filter($_POST['os_version'], 50, false).'\',
					serial =\''.text_filter($_POST['serial'], 50, false).'\',
					descr =\''.text_filter($_POST['descr'], 1600, false).'\',
					'.(($_POST['pathname'] && !empty($_POST['pathname'])) ? '
					pathname =\''.text_filter($_POST['pathname'], 255, false).'\',
					' : '').'
					stitle =\''.text_filter($_POST['stitle'], 60, false).'\',
					description =\''.text_filter($_POST['description'], 255, false).'\',
					keywords =\''.text_filter($_POST['keywords'], null, false).'\',
					canonical =\''.text_filter($_POST['canonical'], null, false).'\',
					barcode =\''.text_filter($_POST['barcode'], 100, false).'\',
					charger =\''.intval($_POST['charger']).'\',
					save_data =\''.intval($_POST['save_data']).'\',
					save_data_comment =\''.text_filter($_POST['sd_comment'], 1600, false).'\',
					'.(
						$id ? '' : 'object_owner =\''.intval($_POST['object']).'\','
					).'
					owner_type =\''.($_POST['customer'] ? 'external' : 'internal').'\',
					price =\''.floatval($_POST['price']).'\',
					quantity =\''.intval($_POST['quantity']).'\',
					time =\''.floatval($_POST['time']).'\',
					purchase_price =\''.floatval($_POST['purchase-price']).'\',
					type_id = \''.$type.'\',
					parts_required = '.intval($_POST['parts']).',
					'.($_POST['customer'] ? 'customer_id = \''.intval($_POST['customer']).'\',' : '').'
					main = \''.intval($_POST['main']).'\',
					publish = \''.intval($_POST['publish']).'\',
					object_id = \''.(text_filter($_POST['intype'], 20, false) == 'stock' ? intval($_POST['object']) : (
						in_array('all', explode(',', $_POST['object'])) ? 'all' : ids_filter($_POST['object'])
					
					)).'\',
					'.($status_id ? 'status_id = \''.$status_id.'\',' : '').'
					store_status_id = \''.intval($_POST['store_status']).'\',
					category_id = \''.$category.'\',
					store_category_id = \''.intval($_POST['storeCat']).'\',
					os_id = \''.intval($_POST['os']).'\',
					vendor_id = \''.intval($_POST['vendor']).'\',
					location_id = \''.$location.'\',
					'.(
						strpos($_SERVER['HTTP_REFERER'], "/add/stock") !== false ? ' commerce = 1,' : ''
					).'
					location_count = \''.intval($_POST['location_count']).'\',
					'.(!$id ? 
						'type = \''.text_filter($_POST['intype'], 20, false).'\',
						cr_user = \''.$user['id'].'\',
						cr_date = \''.date('Y-m-d H:i:s').'\','.(
							(in_to_array('1,2', $user['group_ids']) OR $_POST['customer']) ? 'confirmed = 1, ' : 'confirmed = 0, '
						)
					: '').'
					options = \''.(
						text_filter($_POST['intype'], 20, false) == 'stock' ? json_encode(
						array_text_filter(json_decode($_POST['opts'], true), 50, false),
					JSON_UNESCAPED_UNICODE) : $options
					).'\''.(
						$delete ? $delete : ''
					).(
						$id ? ' WHERE id = '.$id : ''
				)
			);
			
			$id = $id ? $id : intval(
				mysqli_insert_id($db_link)
			);
			
			if (!$new AND !in_to_array('1,2', $user['group_ids']) AND !$_POST['customer'])
				db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'un_inventory\'');
			
			// Is file upload
			if($_FILES){
				
				$images = [];
				
				// Upload max file size
				$max_size = 10;
				
				// path
				$dir = ROOT_DIR.'/uploads/images/inventory/';
				
				// Is not dir
				if(!is_dir($dir.$id)){
					@mkdir($dir.$id, 0777);
					@chmod($dir.$id, 0777);
				}
				
				$dir = $dir.$id.'/';
				
				foreach($_FILES["image"]["error"] as $key => $error){
					
					// temp file
					$tmp = $_FILES['image']['tmp_name'][$key];
					
					$type = mb_strtolower(pathinfo($_FILES['image']['name'][$key], PATHINFO_EXTENSION));
					
					// Check
					if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array(
						$type, ['jpeg', 'jpg', 'png', 'gif']
					)){
						echo 'err_image_type';
						die;
					}
					if($_FILES['image']['size'][$key] >= 1024*$max_size*1024){
						echo 'err_file_size';
						die;
					}
					
					// New name
					$rename = uniqid('', true).'.'.$type;
					
					// Upload image
					if(move_uploaded_file($tmp, $dir.$rename)){
						
						$img = new Imagick($dir.$rename);
						
						if($img->getImageWidth() > 1920){
							$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
							auto_rotate_image($img);
							$img->stripImage();
							$img->writeImage($dir.$rename);
						}
						
						$img->cropThumbnailImage(300, 300);
						$img->stripImage();
						$img->writeImage($dir.'preview_'.$rename);
						
						$img->cropThumbnailImage(94, 94);
						$img->stripImage();
						$img->writeImage($dir.'thumb_'.$rename);
						$img->destroy();
						
						$images[] = $rename;
					}
				}
				if($images){
					db_query('UPDATE `'.DB_PREFIX.'_inventory` SET images = CONCAT(
						images, \''.implode('|', $images).'|\'
					) WHERE id = '.$id);
				}
			}
			
			$pType = text_filter($_POST['intype'], 20, false) == 'stock' ? 'inventory' : 'service';
			
			// ------------------------------------------------------------------------------- //
			if (!$new AND $user['store_id'] > 0 AND !intval($_POST['customer'])){
				/* $sql_ = db_multi_query('
					SELECT
						SUM(tb1.point) as sum,
						tb2.points
					FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
						 `'.DB_PREFIX.'_objects` tb2
					WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
						NOW(), INTERVAL 1 HOUR
					) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
				); */
				
				$points = floatval($config['user_points']['new_'.$pType]['points']);
				
				//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'new_'.$pType.'\',
						inventory_id = '.$id.',
						object_id = '.$user['store_id'].',
						point = \''.$points.'\''
					);	//min_rate = '.$sql_['points'].',
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$points.'
						WHERE id = '.$user['id']
					);
				/* } else {
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'new_'.$pType.'\',
						min_rate = '.$sql_['points'].',
						inventory_id = '.$id.',
						object_id = '.$user['store_id'].',
						point = \''.$points.'\',
						rate_point = 1'
					);	
				} */
			}
			
			if (!$new) {
				db_query('
					INSERT INTO 
					`'.DB_PREFIX.'_activity` SET 
						user_id = \''.$user['id'].'\', 
						event = \'new '.$pType.'\',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						event_id = '.$id.',
						object_id = '.$user['store_id'].'
				');
			}
			
			
			// ------------------------------------------------------------------------------- //
		
		

			echo $id;
		} else
			echo 'ERR';
		die;
	break;
	
	/*
	*  Send short inventiry
	*/
	case 'confirm':
		is_ajax() or die('Hacking attempt!');
		if (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR in_array(3, explode(',', $user['group_ids']))) {
			db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
				confirmed = 1,
				cn_user = '.$user['id'].',
				cn_date = \''.date('Y-m-d H:i:s', time()).'\'
			WHERE id ='.intval($_POST['id']));
			
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'un_inventory\'');
			die('OK');
		} else {
			die('no_acc');
		}
	break;
	
	/*
	*  Send short inventiry
	*/
	case 'send_short_inventory':
		is_ajax() or die('Hacking attempt!');

		$id = intval($_POST['id']);
		$new = intval($_POST['id']);
		
		/* $type = intval($_POST['type']);
		if (!$type AND $_POST['type_new']) {
			$tp = [];
			$tp = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_types` WHERE name = \''.text_filter($_POST['type_new'], 100, false).'\'');
			if ($tp != []) die('tp_exists');
			$type = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_types` SET name = \''.text_filter($_POST['type_new'], 100, false).'\'');
			$type = intval(
				mysqli_insert_id($db_link)
			);
		}

		$category = intval($_POST['category']);
		if (!$category AND $_POST['category_new']) {
			$cat = [];
			$cat = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_categories` WHERE name = \''.text_filter($_POST['category_new'], 100, false).'\'');
			if ($cat != []) die('cat_exists');
			$category = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_categories` SET name = \''.text_filter($_POST['category_new'], 100, false).'\'');
			$category = intval(
				mysqli_insert_id($db_link)
			);
		}
		
		$model = intval($_POST['model']);
		if (!$model AND $_POST['model_new']) {
			$mdl = [];
			$mdl = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_models` WHERE name = \''.text_filter($_POST['model_new'], 100, false).'\' AND category_id = '.$category);
			if ($mdl != []) die('mdl_exists');
			$model = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_models` SET name = \''.text_filter($_POST['model_new'], 100, false).'\''.($category ? ', category_id = '.$category : ''));
			$model = intval(
				mysqli_insert_id($db_link)
			);
		} */
		
		$objects_ip = array_flip($config['object_ips']);
		if ($store_inv = db_multi_query('SELECT object_id FROM `'.DB_PREFIX.'_inventory` WHERE id = '.intval($_POST['exist_inventory']))) 
			$store = $store_inv['object_id'];
		else 
			$store = $objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0;
		
		if ($store == 0) die('no_obj');
			
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_inventory` SET
				name =\''.text_filter($_POST['name'], 50, false).'\',
				model =\''.text_filter($_POST['smodel'], 50, false).'\',
				ver_os =\''.text_filter($_POST['os_version'], 50, false).'\',
				serial =\''.text_filter($_POST['serial'], 50, false).'\',
				descr =\''.$_POST['descr'].'\',
				price =\''.floatval($_POST['price']).'\',
				customer_id = \''.intval($_POST['customer']).'\',
				main = \''.intval($_POST['main']).'\',
				object_id = \''.$store.'\',
				object_owner = \''.$store.'\',
				owner_type = \'internal\',
				'.($status_id ? 'status_id = \''.$status_id.'\',' : '').'
				category_id = \''.$category.'\',
				type_id = \''.$type.'\',
				model_id = \''.$model.'\',
				store_category_id = \''.intval($_POST['storeCat']).'\',
				os_id = \''.intval($_POST['os']).'\',
				location_id = \''.intval($_POST['location']).'\',
				'.(!$id ? 
					'type = \''.text_filter($_POST['intype'], 20, false).'\',
					 cr_user = \''.$user['id'].'\',
					 cr_date = \''.date('Y-m-d H:i:s').'\',
					 cr_issue = \''.intval($_POST['issue_id']).'\','.(
							in_to_array('1,2', $user['group_ids']) ? 'confirmed = 1, ' : ''
						)
					: '').'
				options = \''.json_encode(
					array_text_filter(json_decode($_POST['opts'], true), 50, false),
				JSON_UNESCAPED_UNICODE).'\''.(
					$delete ? $delete : ''
				).(
					$id ? ' WHERE id = '.$id : ''
			)
		);
			
		$id = $id ? $id : intval(
			mysqli_insert_id($db_link)
		);
		
		$name = db_multi_query('SELECT
									i.model,
									m.name as mname,
									b.name as brand
								FROM `'.DB_PREFIX.'_inventory` i
								LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
									ON m.id = i.model_id
								LEFT JOIN `'.DB_PREFIX.'_inventory_categories` b
									ON b.id = i.category_id
								WHERE i.id = '.$id);
		
		// ------------------------------------------------------------------------------- //
		
		if (!$new AND $user['store_id'] > 0){
			/* $sql_ = db_multi_query('
				SELECT
					SUM(tb1.point) as sum,
					tb2.points
				FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
					 `'.DB_PREFIX.'_objects` tb2
				WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
					NOW(), INTERVAL 1 HOUR
				) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
			); */
			
			$pType = text_filter($_POST['intype'], 20, false) == 'stock' ? 'inventory' : 'service';
			
			$points = floatval($config['user_points']['new_'.$pType]['points']);
			
			//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'new_'.$pType.'\',
					object_id = '.$user['store_id'].',
					point = \''.$points.'\''
				);	//min_rate = '.$sql_['points'].',
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$user['id']
				);
			/* } else {
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'new_'.$pType.'\',
					min_rate = '.$sql_['points'].',
					object_id = '.$user['store_id'].',
					point = \''.$points.'\',
					rate_point = 1'
				);	
			} */
		}
		
		// ------------------------------------------------------------------------------- //
		
		
			
		print_r(json_encode([
			'id' => $id,
			'name' => $name['brand'].' '.$name['mname'].' '.$name['model']
		]));
		die;
	break;
	
	/*
	*  Send new type
	*/
	case 'new_type':
		is_ajax() or die('Hacking attempt!');
	
			db_query(
				'INSERT INTO `'.DB_PREFIX.'_inventory_types` SET 
				name = \''.text_filter($_POST['name']).'\',
				type = \'inventory\''
			);
			echo intval(mysqli_insert_id($db_link));
		die;
	break;
	
	/*
	*  Send new brand
	*/
	case 'new_brand':
		is_ajax() or die('Hacking attempt!');
	
			db_query(
				'INSERT INTO `'.DB_PREFIX.'_inventory_categories` SET 
				name = \''.text_filter($_POST['name']).'\',
				type = \'inventory\''
			);
			echo intval(mysqli_insert_id($db_link));
		die;
	break;
	
	/*
	*  Send types
	*/
	case 'send':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$category = intval($_POST['brand']);
			$opts = '';
			if($_POST['opts']){
				$options = [];
				foreach($_POST['opts'] as $item){
					if(isset($item['sOpts'])){
						foreach($item['sOpts'] as $i => $n){
							if(is_int($i)){
								$item['sOpts'][hash('adler32', $n.time())] = $n;
								unset($item['sOpts'][$i]);
							} else
								continue;
						}
					}
					if(isset($item['id'])){
						$hid = $item['id'];
						unset($item['id']);
						$options[$hid] = $item;
					} else
						$options[hash('adler32', $item['name'].time())] = $item;
				}
				$opts .= ', options = \''.json_encode(
					array_text_filter($options, 50, false)
				, JSON_UNESCAPED_UNICODE).'\'';
			}
			$sql_set = 'name = \''.text_filter($_POST['name']).'\',
						type = \''.text_filter($_POST['type']).'\',
						category_id = '.$category.$opts;		
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_inventory_types` SET '.$sql_set.(
				$id ? ' WHERE id = '.$id : ''
			));
			echo $id ? $id : intval(
				mysqli_insert_id($db_link)
			);
		die;
	break;
	
	/*
	* Select categories
	*/
	case 'allCategories':
		$id = intval($_REQUEST['id']);
		$lId = intval($_REQUEST['lId']);
		$nIds = ids_filter($_REQUEST['nIds']);
		if($_REQUEST['q'])
			$_REQUEST['query'] = $_REQUEST['q'];
		$query = text_filter($_REQUEST['query'], 100, false);
		$categories = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS tb1.id, CONCAT(
			IFNULL(tb2.name, \'\'), IF(
				tb2.name IS NOT NULL, \' <span class="fa fa-angle-right"></span> \', \'\'
			), tb1.name
			) as name
				FROM `'.DB_PREFIX.'_inventory_categories` tb1
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb2
				ON tb1.parent_id = tb2.id
				WHERE 1'.(
				$lId ? ' AND tb1.id < '.$lId : ''
			).(
				$query ? ' AND (tb1.name LIKE \''.$query.'%\' OR tb2.name LIKE \''.$query.'%\')' : ''
			).($nIds ? ' AND tb1.id NOT IN('.$nIds.')' : '').(
				$id ? ' AND tb1.id != '.$id.' AND tb1.parent_id != '.$id : ''
			).' ORDER BY tb1.name ASC LIMIT 20', true
		);
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $categories,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Delete category
	*/
	case 'delCategory':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_inventory_categories']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_categories` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	*  Send category
	*/
	case 'sendCategory':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		db_query(($id ? 'UPDATE' : 'INSERT INTO').' `'.DB_PREFIX.'_inventory_categories` SET
			name = \''.text_filter($_POST['name'], 50, false).'\',
			type_id = \''.intval($_POST['type']).'\',
			parent_id = '.intval($_POST['parent']).(
				$id ? ' WHERE id = '.$id : ''
		));
		echo $id ? $id : intval(mysqli_insert_id($db_link));
		die;
	break;
	
	/*
	*  Categories
	*/
	case 'categories':
		if($route[2] == 'add' OR (
			$route[2] == 'edit' AND intval($route[3])
		)){
			$id = intval($route[3]);
			$type = $id ? $lang['Edit'] : $lang['Add'];
			$row = [];
			$meta['title'] = $type.' '.'category';
			if($id){
				$row = db_multi_query('
					SELECT tb1.*, tb2.name as parent_name
					FROM `'.DB_PREFIX.'_inventory_categories` tb1
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb2
						ON tb1.parent_id = tb2.id
					WHERE tb1.id = '.$id
				);
			}
			tpl_set('inventory/types/categories/form', [
				'id' => $id,
				'title' => $type.' '.'category',
				'name' => $row['name'],
				'type' => $row['type'] ?? 'service',
				'parent-id' => json_encode($row['parent_id'] ? [
					$row['parent_id'] => $row['parent_name']
				] : []),
				'send' => isset($route[3]) ? $lang['Save'] : $lang['Send'],
			], [], 'content');
		} else {
			$meta['title'] = $lang['allCategories'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					tb1.id, tb1.name, tb1.type_id, tb2.name as pname, tb1.parent_id
				FROM `'.DB_PREFIX.'_inventory_categories` tb1
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb2
					ON tb1.parent_id = tb2.id	'.(
				$query ? 'WHERE (tb1.name LIKE \'%'.$query.'%\' OR tb2.name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY tb1.id DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/types/categories/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'parent-name' => $row['pname'] ?? '',
						'parent-json' => json_encode($row['parent_id'] ? [
							$row['parent_id'] => ['name' => $row['pname']]
						] : [])
					], [], 'categories');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noCategories']
				], false, 'categories');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['categories'],
				]));
			}
			tpl_set('inventory/types/categories/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'invCategories' => $tpl_content['categories']
			], [
				'create' => $user['create_inventory_transfer'],
				'store' => $route[1] == 'store'
			], 'content');
		}
	break;
	
	/*
	*  Send model
	*/
	case 'sendModel':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		db_query(($id ? 'UPDATE' : 'INSERT INTO').' `'.DB_PREFIX.'_inventory_models` SET
			name = \''.text_filter($_POST['name'], 50, false).'\',
			category_id = '.intval($_POST['brand']).(
				$id ? ' WHERE id = '.$id : ''
		));
		echo $id ? $id : intval(mysqli_insert_id($db_link));
		die;
	break;
	
	/*
	* Delete category
	*/
	case 'delModel':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		//if($user['delete_inventory_categories']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_models` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		/* } else
			exit('ERR'); */
	break;
	
	/*
	*  Models
	*/
	case 'models':
		if($route[2] == 'add' OR (
			$route[2] == 'edit' AND intval($route[3])
		)){
			$id = intval($route[3]);
			$type = $id ? $lang['Edit'] : $lang['Add'];
			$row = [];
			$meta['title'] = $type.' '.'model';
			if($id){
				$row = db_multi_query('
					SELECT m.*, t.name as type
					FROM `'.DB_PREFIX.'_inventory_models` m
					LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
						ON m.type_id = t.id
					WHERE t.id = '.$id
				);
			}
			tpl_set('inventory/types/models/form', [
				'id' => $id,
				'title' => $type.' '.'category',
				'name' => $row['name'],
				'type' => $row['type'] ?? 'service',
				'type-id' => json_encode($row['type_id'] ? [
					$row['type_id'] => $row['type']
				] : []),
				'send' => isset($route[3]) ? $lang['Save'] : $lang['Send'],
			], [
				'create' => $user['create_inventory_transfer']
			], 'content');
		} else {
			$meta['title'] = $lang['Models'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					tb1.id, tb1.name, tb2.name as category_name, tb1.category_id
				FROM `'.DB_PREFIX.'_inventory_models` tb1
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb2
					ON tb1.category_id = tb2.id '.(
				$query ? 'WHERE (tb1.name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY tb1.id DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/types/models/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'category-name' => $row['category_name'] ?? '',
						'category-json' => json_encode($row['category_id'] ? [
							$row['category_id'] => ['name' => $row['category_name']]
						] : [])
					], [], 'models');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noModels']
				], false, 'models');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['models'],
				]));
			}
			tpl_set('inventory/types/models/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'invModels' => $tpl_content['models']
			], [
				'create' => $user['create_inventory_transfer'],
				'store' => $route[1] == 'store'
			], 'content');
		}
	break;
	
	/*
	* Vendors
	*/
	case 'vendors':
		$meta['title'] = $lang['Vendors'];
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_inventory_vendors` WHERE 1 '.(
			$query ? ' AND name LIKE \'%'.$query.'%\' ' : ''
		).'ORDER BY id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('inventory/vendors/item', [
					'id' => $row['id'],
					'name' => $row['name']
				], [], 'vendors');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
			tpl_set('noContent', [
				'text' => $lang['noVendors']
			], false, 'vendors');
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['vendors'],
			]));
		}
		tpl_set('inventory/vendors/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'vendors' => $tpl_content['vendors']
		], [
			'create' => $user['create_inventory_transfer']
		], 'content');	
	break;
	
	/*
	*  Send vendor
	*/
	case 'sendVendor':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		db_query(($id ? 'UPDATE' : 'INSERT INTO').' `'.DB_PREFIX.'_inventory_vendors` SET
			name = \''.text_filter($_POST['name'], 50, false).'\''.(
				$id ? ' WHERE id = '.$id : ''
		));
		die('OK');
	break;
	
	/*
	* Delete vendor
	*/
	case 'delVendor':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		//if($user['delete_inventory_vendors']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_vendors` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		/* } else
			exit('ERR'); */
	break;
	
	/*
	* Store statuses
	*/
	case 'store_statuses':
		$meta['title'] = $lang['StoreSatuses'];
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				id, name, not_priority
			FROM `'.DB_PREFIX.'_inventory_store_status` '.(
			$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
		).'ORDER BY `sort` ASC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('inventory/types/store-statuses/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'priority' => $row['not_priority'] ? ' checked' : ''
				], [
					'default' => $row['id'] != 11 AND $row['id'] != 2,
					'nnew' => $row['id'] != 11
				], 'status');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
			tpl_set('noContent', [
				'text' => $lang['noStatuses']
			], false, 'status');
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['status'],
			]));
		}
		tpl_set('inventory/types/store-statuses/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'status' => $tpl_content['status']
		], [], 'content');
	break;
	
	/*
	*  Add/edit status
	*/
	case 'sendStoreStatus':
		is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_inventory_store_status` SET
					name =\''.text_filter($_POST['name'], 50, false).'\''.(
				$id ? ' WHERE id = '.$id : ''
			));
			echo $id ? $id : intval(
				mysqli_insert_id($db_link)
			);
		die;
	break;
	
	/*
	*  Del status
	*/
	case 'delStoreStatus':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_inventory_status']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_store_status` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	*  Types
	*/
	case 'types':
		$meta['title'] = 'Inventory';
		if($route[2] == 'add' OR (
			$route[2] == 'edit' AND intval($route[3])
		)){
			$id = intval($route[3]);
			$types = [
				'input' => $lang['Text'],
				'number' => $lang['Number'],
				'textarea' => $lang['Textarea'],
				'checkbox' => $lang['On/Off'],
				'select' => $lang['Select']
			];
			$row = [];
			if($id){
				$row = db_multi_query('SELECT 
										t.*,
										c.name as category_name
										FROM `'.DB_PREFIX.'_inventory_types` t 
										LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
											ON c.id = t.category_id
										WHERE t.id = '.$id);
				if($sql = json_decode($row['options'], true)){
					foreach($sql as $hid => $option){
						$select_options = '';
						foreach($types as $k => $v){
							$select_options .= '<option value="'.$k.'"'.(
								$option['type'] == $k ? ' selected' : ''
							).'>'.$v.'</option>';
						}
						if($option['type'] == 'select'){
							foreach($option['sOpts'] as $hi => $v){
								tpl_set('inventory/types/selectOption', [
									'hid' => $hi,
									'option' => $v
								], false, 'select-options');
							}
							$s = true;
						} else {
							$s = false;
						}
						tpl_set('inventory/types/option', [
							'name' => $option['name'],
							'id' => $hid,
							'options' => $select_options,
							'select-options' => $tpl_content['select-options']
						], [
							'select' => $s,
							'multiple' => $option['mult'],
							'req' => $option['req']
						], 'options');
						unset($tpl_content['select-options']);
					}
				}
			}
			tpl_set('inventory/types/form', [
				'id' => $id,
				'send' => isset($route[3]) ? 'Save' : 'Send',
				'brand-id' => json_encode($row['category_id'] ? [
					$row['category_id'] => [
						'name' => $row['category_name']
					]
				] : []),
				'name' => $row['name'],
				'type' => $row['type'],
				'options' => $tpl_content['options']
			], [
				'create' => $user['create_inventory_transfer']
			], 'content');
		} else if($route[2] == 'status'){
			if($id = intval($route[3])){
				$row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_status` WHERE id = '.$id);
				$locations = [];
				if($row['locations']){
					$sql = db_multi_query('SELECT l.*, o.name as object 
											FROM `'.DB_PREFIX.'_objects_locations` l
											LEFT JOIN `'.DB_PREFIX.'_objects` o
												ON o.id = l.object_id
											WHERE l.id IN('.$row['locations'].')', true);
					foreach($sql as $loc){
						$locations[$loc['id']] = [
							'name' => $loc['name'],
							'object' => $loc['object'],
							'objectid' => $loc['object_id']
						];
					}
				}
				
				tpl_set('inventory/types/location', [
					'id' => $id,
					'name' => $row['name'],
					'locations' => json_encode($locations),
					'point-group' => $row['point_group'],
					'forfeit' => $row['forfeit'],
					'sms' => $row['sms'],
					'smsForm' => $row['sms_form'],
					'percent' => intval($row['percent']),
					'send' => 'Save'
				], [
					'rservice' => $row['service'],
					'rinventories' => $row['inventory'],
					'purchase' => $row['purchase'],
					'remove_purchase' => $row['remove_purchase'],
					'assigned' => $row['assigned'],
					'create' => $user['create_inventory_transfer'],
					'default' => $id != 11 AND $id != 2 AND $id != 30,
					'forfeit' => $row['forfeit'],
					'note' => $row['note'],
				], 'content');
			} else {
				$meta['title'] = $lang['Statuses'];
				$query = text_filter($_POST['query'], 255, false);
				$page = intval($_POST['page']);
				$count = 10;
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						id, name, point_group, forfeit, not_priority, sms, sms_form, percent
					FROM `'.DB_PREFIX.'_inventory_status` '.(
					$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
				).'ORDER BY `sort` ASC LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('inventory/types/itemStatus', [
							'id' => $row['id'],
							'name' => $row['name'],
							'priority' => $row['not_priority'] ? ' checked' : '',
							'point-group' => $row['point_group'] ?: 0,
							'forfeit' => $row['forfeit'],
							'sms' => intval($row['sms']),
							'percent' => intval($row['percent']),
							'smsForm' => intval($row['sms_form'])
						], [
							'default' => $row['id'] != 11 AND $row['id'] != 2,
							'nnew' => $row['id'] != 11 AND $row['id'] != 30 AND $row['id'] != 2
						], 'status');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				} else {
					tpl_set('noContent', [
						'text' => $lang['noStatuses']
					], false, 'status');
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['status'],
					]));
				}
				tpl_set('inventory/types/status', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'status' => $tpl_content['status']
				], [
					'create' => $user['create_inventory_transfer']
				], 'content');
			}
		} else if($route[2] == 'os'){
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_inventory_os` WHERE 1 '.(
				$query ? ' AND name LIKE \'%'.$query.'%\' ' : ''
			).'ORDER BY id DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/types/itemOS', [
						'id' => $row['id'],
						'name' => $row['name']
					], [], 'os');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noOS']
				], false, 'os');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['os'],
				]));
			}
			tpl_set('inventory/types/os', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'os' => $tpl_content['os']
			], [
				'create' => $user['create_inventory_transfer']
			], 'content');	
		} else {
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			$sqlType = in_array($route[2], ['service', 'inventory']) ? ' AND tb1.type =\''.$route[2].'\'' : '';
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					tb1.id, tb1.name, tb1.type, tb1.category_id, tb2.name as catname, tb3.name as pname
				FROM `'.DB_PREFIX.'_inventory_types` tb1
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb2
					ON tb1.category_id = tb2.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb3
					ON tb2.parent_id = tb3.id
				WHERE 1 '.$sqlType.(
					$query ? ' AND tb1.name LIKE \'%'.$query.'%\' ' : ''
				).'ORDER BY tb1.id DESC LIMIT '.($page*$count).', '.$count, true
			)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('inventory/types/item', [
						'id' => $row['id'],
						'category' => json_encode($row['category_id'] ? [
							$row['category_id'] => (
								$row['pname'] ? $row['pname'].' ' : ''
							).$row['catname']
						] : []),
						'name' => $row['name'],
						'type' => $row['type']
					], [], 'inventory');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noInventory']
				], false, 'inventory');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['inventory'],
				]));
			}
			tpl_set('inventory/types/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'invTypes' => $tpl_content['inventory']
			], [
				'create' => $user['create_inventory_transfer']
			], 'content');
		}
	break;
	
	/*
	*  Add/edit status
	*/
	case 'sendStatus':
		is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_inventory_'.(intval($_POST['warranty']) ? 'warranty_' : '').'status` SET
					name =\''.text_filter($_POST['name'], 50, false).'\',
					parent_ids =\''.ids_filter($_POST['parent_ids']).'\',
					forfeit =\''.intval($_POST['forfeit']).'\',
					sms =\''.intval($_POST['sms']).'\',
					percent =\''.intval($_POST['percent']).'\',
					sms_form =\''.intval($_POST['smsForm']).'\',
					point_group = \''.json_encode(
						array_text_filter(
							$_POST['pointGroup'], 50, false
						), JSON_UNESCAPED_UNICODE
					).'\''.(
				$id ? ' WHERE id = '.$id : ''
			));
			echo $id ? $id : intval(
				mysqli_insert_id($db_link)
			);
		die;
	break;
	
	/*
	*  Del status
	*/
	case 'delStatus':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_inventory_status']){
			db_query('DELETE FROM `'.DB_PREFIX.'_inventory_'.(intval($_POST['warranty']) ? 'warranty_' : '').'status` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	* Get all OS
	*/
	case 'allOS':
		$id = intval($_POST['id']);
		$lId = intval($_POST['lId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_inventory_os` WHERE 1'.(
			$lId ? ' AND id < '.$lId : ''
		).(
			$query ? ' AND name LIKE \'%'.$query.'%\'': ''
		).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' ORDER BY `id` DESC LIMIT 20', true);
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $statuses,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Get all Models
	*/
	case 'allModels':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_REQUEST['id']);
		$lId = intval($_REQUEST['lId']);
		$nIds = ids_filter($_REQUEST['nIds']);
		$type = intval($_REQUEST['type']);
		if($_REQUEST['q'])
			$_REQUEST['query'] = $_REQUEST['q'];
		$query = text_filter($_REQUEST['query'], 100, false);
		$models = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_inventory_models` WHERE 1'.(
			$lId ? ' AND id < '.$lId : ''
		).(
			$type ? ' AND category_id = '.$type : ' AND category_id < 0'
		).(
			$query ? ' AND name LIKE \''.$query.'%\'': ''
		).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' ORDER BY `name` ASC LIMIT 20', true);
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $models,
			'count' => $res_count,
		]));
	break;
	
	
	/*
	* Get all new statuses
	*/
	case 'allStatuses':
		//is_ajax() or die('Hacking attempt!');
		$id = intval($_REQUEST['id']);
		$curent_id = intval($_REQUEST['nIds']);
		$query = text_filter($_REQUEST['query'], 100, false);
		$curent = [];
		if($curent_id && (
			$curent = db_multi_query('SELECT parent_ids FROM `'.DB_PREFIX.'_inventory_'.(intval($_REQUEST['warranty']) ? 'warranty_' : '').'status` WHERE id = '.$curent_id)
		)){
		}
		$statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, inventory, service FROM `'.DB_PREFIX.'_inventory_'.(intval($_REQUEST['warranty']) ? 'warranty_' : '').'status` WHERE '.(
			$curent['parent_ids'] ? 'id IN('.$curent['parent_ids'].')' : 'id = 0'
		).(
			$query ? ' AND name LIKE \'%'.$query.'%\'': ''
		).' ORDER BY `sort` ASC LIMIT 20', true, false, function($a){
			return [$a['id'], $a];
		});
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $statuses,
			'count' => $res_count,
		]));
	break;
	
	
	/*
	* Get all statuses
	*/
	case 'allStatuses2':
		//is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$lId = intval($_POST['lId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$set_services = ($_POST['set_services'] AND count(explode(',', $_POST['set_services'])) > 0) ? 1 : 0;
		$set_inventory =  ($_POST['set_inventory'] AND count(explode(',', $_POST['set_inventory'])) > 0) ? 1 : 0;
		$array = [];
		$curent = 0;
		$statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, sort, not_priority, inventory, service, note FROM `'.DB_PREFIX.'_inventory_'.(intval($_POST['warranty']) ? 'warranty_' : '').'status` WHERE 1'.(
			$lId ? ' AND id < '.$lId : ''
		).(
			$query ? ' AND name LIKE \'%'.$query.'%\'': ''
		).' ORDER BY `sort` ASC LIMIT 20', true, false, function($a) use(&$array, &$curent, &$set_services, &$set_inventory){
			//if (($a['inventory'] AND $set_inventory OR !$a['inventory']) AND ($a['service'] AND $set_services OR !$a['service'])) {
				if($a['not_priority']){
					$curent = $a['id'];
					$array[$a['id']] = [$a['id'] => $a];
				} else {
					$array[$curent][$a['id']] = $a;
				}
			//}
		});
		$fb = [];
		foreach($array as $k => $v){
			if(array_filter($v, function($k) use($nIds){
				return $k == $nIds;
			}, ARRAY_FILTER_USE_KEY)){
				$fb = $v;
			} else if($fb && array_filter($v, function($a){
				return $a['not_priority'] > 0;
			})){
				$fb = $fb+$v;
				break;
			} else if($fb){
				$fb = $fb+$v;
			}
		}
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $fb,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Get all new statuses
	*/
	case 'allNewStatuses':
		//is_ajax() or die('Hacking attempt!');
		$id = intval($_REQUEST['id']);
		$curent_id = intval($_REQUEST['curent_id']);
		$query = text_filter($_REQUEST['query'], 100, false);
		$curent = [];
		if($curent_id && (
			$curent = db_multi_query('SELECT parent_ids FROM `'.DB_PREFIX.'_inventory_'.(intval($_REQUEST['warranty']) ? 'warranty_' : '').'status` WHERE id = '.$curent_id)
		)){
		}
		$statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, inventory, service'.(
			$curent['parent_ids'] ? ', FIND_IN_SET(id, \''.$curent['parent_ids'].'\') as selected' : ''
		).' FROM `'.DB_PREFIX.'_inventory_'.(intval($_REQUEST['warranty']) ? 'warranty_' : '').'status` WHERE id != '.$curent_id.(
			$id ? ' AND FIND_IN_SET('.$id.', parent_ids)' : ''
		).(
			$query ? ' AND name LIKE \'%'.$query.'%\'': ''
		).' ORDER BY `sort` ASC LIMIT 20', true);
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $statuses,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Get all store statuses
	*/
	case 'allStoreStatuses':
		$id = intval($_POST['id']);
		$lId = intval($_POST['lId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, sort FROM `'.DB_PREFIX.'_inventory_store_status` WHERE 1'.(
			$lId ? ' AND id < '.$lId : ''
		).(
			$query ? ' AND name LIKE \'%'.$query.'%\'': ''
		).' ORDER BY `sort` ASC LIMIT 20', true);
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $statuses,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Get all types
	*/
	case 'allTypes':
		$id = intval($_REQUEST['id']);
		$lId = intval($_REQUEST['lId']);
		$nIds = ids_filter($_REQUEST['nIds']);
		if($_REQUEST['q'])
			$_REQUEST['query'] = $_REQUEST['q'];
		$query = text_filter($_REQUEST['query'], 100, false);
		$name = intval($_REQUEST['name']);
		$type = text_filter($_REQUEST['type'], 100, false) ?: 'inventory';
		
		$types = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
			t.id, t.name
		FROM `'.DB_PREFIX.'_inventory_types` t 
		WHERE 1'.(
			$lId ? ' AND t.id < '.$lId : ''
		).(
			$query ? ' AND t.name LIKE \''.$query.'%\'': ''
		).($nIds ? ' AND t.id NOT IN('.$nIds.')' : '').' '.($_REQUEST['q'] ? '' : 'ORDER BY t.name ASC').' LIMIT 20', true);
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $types,
			'count' => $res_count,
		]));
	break;
	
	/*
	*  Add/edit inventary
	*/
	case 'add':
	case 'edit':
	case 'step':
		$objects_ip = array_flip($config['object_ips']);
		$id = intval($route[3]) ? intval($route[3]) : intval($route[2]);		
		$meta['title'] = ucfirst($route[1]).' '.$route[2];
		if(($route[1] == 'add') OR (
			$route[1] == 'edit' AND $id
		) OR $route[1] == 'step'){
			$row = [];
			if($id){
				$row = db_multi_query('
					SELECT
						i.*,
						t.options as opts,
						u.name as customer_name,
						u.lastname as customer_lastname,
						o.name as object_name,
						o.tax as object_tax,
						s.name as status_name,
						ss.name as store_status_name,
						oo.name as object_owner_name,
						m.name as model_name,
						l.name as location_name,
						l.count as count,
						t.name as type_name,
						c.name as category_name,
						serc.name as scategory_name,
						sc.name as store_category_name
					FROM `'.DB_PREFIX.'_inventory` i
					LEFT JOIN `'.DB_PREFIX.'_inventory_types`
						t ON i.type_id = t.id
					LEFT JOIN `'.DB_PREFIX.'_users`
						u ON u.id = i.customer_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = i.object_id
					LEFT JOIN `'.DB_PREFIX.'_objects` oo
						ON oo.id = i.object_owner
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON s.id = i.status_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_store_status` ss
						ON ss.id = i.store_status_id
					LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
						ON l.id = i.location_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
						ON c.id = i.category_id
					LEFT JOIN `'.DB_PREFIX.'_service_categories` serc
						ON serc.id = i.category_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
						ON m.id = i.model_id
					LEFT JOIN `'.DB_PREFIX.'_store_categories` sc
						ON sc.id = i.store_category_id
					WHERE i.id = '.$id
				);
			}
			
			
			$currency = '';
			$purchase_currency = '';
			foreach($config['currency'] as $k => $c) {
				$currency .= '<option value="'.$k.'" data-symbol="'.$c['symbol'].'"'.($k == $row['currency'] ? ' selected' : '').'>'.$k.' ('.$c['symbol'].')</option>';
				$purchase_currency .= '<option value="'.$k.'" data-symbol="'.$c['symbol'].'"'.($k == $row['purchase_currency'] ? ' selected' : '').'>'.$k.' ('.$c['symbol'].')</option>';
			}
			
			$next = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_inventory` WHERE id != '.$id.' AND confirmed = 0 AND type= "'.($route[2] == 'service' ? 'service' : 'stock').'" ORDER BY RAND() LIMIT 0, 1');
			if (($route[1] == 'add' OR ($route[1] == 'edit' AND $id)) AND $route[2] == 'service') {
				if(($route[1] == 'add' AND $user['add_iservice']) OR ($route[1] == 'edit' AND $user['edit_iservice'])){
					$steps = '';
					if ($id AND $row['options']) {
						foreach(json_decode($row['options'], true) as $i => $step) {
							$steps .= '<div class="iGroup optGroup sInput">
									<span class="fa fa-bars" draggable="true" ondragover="options.dragover(event)" ondragstart="options.dragstart(event)" ondragend="options.dragend(event)" onmousedown="$(this).parent().addClass(\'drag\');" onmouseup="$(this).parent().removeClass(\'drag\');"></span>
									<div class="sSide fw">
										<input name="oName" data-id="'.$i.'" value="'.$step.'">
									</div>
									<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
								</div>';
						}
						$object = [];
						if ($row['object_id']) {
							if ($row['object_id'] != 'all') {
								foreach(db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects` WHERE id IN('.$row['object_id'].')', true) as $obj) {
									$object[$obj['id']] = [
										'name' => $obj['name']
									];
								}
							} else {
								$object['all'] = [
									'name' => $lang['AllStores']
								];
							}
						}
					}
					
					tpl_set('inventory/service', [
						'id' => $id,
						'name' => $row['name'],
						'descr' => $row['descr'],
						//'type-id' => $row['type_id'] ?: 0,
						'price' => $row['price'],
						'currency' => $currency,
						'time' => $row['time'],
						'title' => ($route[1] == 'add' ? $lang['Add'].' ' : $lang['Edit'].' ').' '.$lang['Service'],
						'type' => 'service',
						'send' => $id ? 'Save' : 'Send',
						'steps' => $steps,
						'object-id' => $row['type'] == 'service' ? json_encode($object) : (
							json_encode($row['object_id'] ? [
								$row['object_id'] => [
									'name' => $row['object_name'],
									'tax' => $row['object_tax'] 
								]
							] : [])
						),
						'category-id' => json_encode($row['category_id'] ? [
							$row['category_id'] => [
								'name' => $row['scategory_name']
							]
						] : []),
						'model-id' => json_encode($row['model_id'] ? [
							$row['model_id'] => [
								'name' => $row['model_name']
							]
						] : []),
						'type-id' => json_encode($row['type_id'] ? [
							$row['type_id'] => [
								'name' => $row['type_name']
							]
						] : []),
						'store-category-id' => json_encode($row['store_category_id'] ? [
							$row['store_category_id'] => [
								'name' => $row['store_category_name']
							]
						] : []),
						'next' => $next['id'],
						'backusr' => $_GET['backusr'] ?: 0
					], [
						'store' => $row['commerce'],
						'parts' => $row['parts_required'],
						'main' => $row['main'],
						'add' => ($route[1] == 'add'),
						'edit' => ($route[1] == 'edit'),
						'backusr' => ($route[1] == 'edit' OR $route[1] == 'add') AND $_GET['backusr'],
						'show' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR $objects_ip[$user['oip']] != 0),
						'notconfirmed' => ($row['confirmed'] == 0 AND (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR in_array(3, explode(',', $user['group_ids'])))),
						'next' => intval($next['id']),
						'create' => $user['create_inventory_transfer'],
						'add-service' => $user['add_service'],
						'add-iservice' => $user['add_iservice']
					], 'content');
				} else {
					tpl_set('/forbidden', [
						'text' => $lang['Forbidden']
					], [], 'content');
				}
			} else {
				$options = '';
				//if($route[1] == 'add' OR $route[1] == 'step'){
					if($types = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS
							id, name FROM `'.DB_PREFIX.'_inventory_types`
						WHERE options != \'\''.(
						in_array($route[2], [
							'service',
							'inventory',
							'user',
							'object'
						]) ? ' AND type = \''.(
							isset($route[3]) ? 'inventory' : $route[2]
						).'\' ' : (($route[1] == 'step' OR $route[1] == 'add') ? ' AND type = \'inventory\'' : '')
					).' ORDER BY `id` LIMIT 0, 20', true)){
						foreach($types as $group){
							$options .= '<option value="'.$group['id'].'"'.(
								$group['id'] == $row['type_id'] ? ' selected' : ''
							).'>'.$group['name'].'</option>';
						}
						$res_count = intval(
							mysqli_fetch_array(
								db_query('SELECT FOUND_ROWS()')
							)[0]
						);
					}	
				//}
				$options_os = '';
				foreach(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_os` ORDER BY id', true) as $os){
					$options_os .= '<option value="'.$os['id'].'"'.(
						$os['id'] == $row['os_id'] ? ' selected' : ''
					).'>'.$os['name'].'</option>';
				}
				
				$options_vendors = '<option value="0">Not selected</option>';
				foreach(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_vendors` ORDER BY id', true) as $vendor){
					$options_vendors .= '<option value="'.$vendor['id'].'"'.(
						$vendor['id'] == $row['vendor_id'] ? ' selected' : ''
					).'>'.$vendor['name'].'</option>';
				}
				
				$images = '';
				$count_images = 0;
				foreach(explode('|', $row['images']) as $img){
					if($img){
						$images .= '<div class="thumb">
							<img src="/uploads/images/inventory/'.$id.'/thumb_'.$img.'" onclick="showPhoto(this.src);">
							<span class="fa fa-times" onclick="delete_image(this);"></span>
						</div>';
						$count_images++;
					}
				}
				
				tpl_set('inventory/'.(
					$route[1] == 'step' ? 'step' : 'form'
				), [
					'id' => $id,
					'uri' => $row['pathname'] ?: $id,
					'cid' => intval($_GET['user']),
					'name' => $row['name'],
					'descr' => $row['descr'],
					'pathname' => $row['pathname'],
					'stitle' => $row['stitle'],
					'description' => $row['description'],
					'keywords' => $row['keywords'],
					'canonical' => $row['canonical'],
					'smodel' => $row['model'],
					//'type-id' => $row['type_id'] ?: 0,
					'serial' => $row['serial'],
					'os-version' => $row['ver_os'],
					'price' => $row['price'],
					'currency' => $currency,
					'purchase-currency' => $purchase_currency,
					'barcode' => $row['barcode'],
					'quantity' => $row['quantity'] ?: 1,
					'purchase-price' => $row['purchase_price'],
					'sale-price' => $row['sale_price'],
					'title' => $route[1] == 'add' ? 'Add '.(
						isset($route[3]) ? 'Inventory' : $route[2]
					) : 'Edit '.$row['type'],
					'type' => ($row['type'] ? ($row['type'] == 'service' ? 'service' : 'inventory') : $route[2]),
					'send' => $id ? $lang['Save'] : $lang['Send'],
					'options' => $options,
					'sd-comment' => $row['save_data_comment'],
					'count-images' => $count_images,
					'images' => $images,
					'options-os' => $options_os,
					'options-vendors' => $options_vendors,
					'customer-id' => json_encode($row['customer_id'] ? [
						$row['customer_id'] => [
							'name' => $row['customer_name'].' '.$row['customer_lastname'],
							'lastname' => $row['customer_lastname']
						]
					] : []),
					'object-id' => json_encode($row['object_id'] ? [
						$row['object_id'] => [
							'name' => $row['object_name'],
							'tax' => $row['object_tax'] 
						]
					] : []),
					'object-owner' => json_encode($row['object_owner'] ? [
						$row['object_owner'] => [
							'name' => $row['object_owner_name']
						]
					] : []),
					'status-id' => json_encode($row['status_id'] ? [
						$row['status_id'] => [
							'name' => ($row['status_name'] ?: $row['status_id'])
						]
					] : []),
					'store-status-id' => json_encode($row['store_status_id'] ? [
						$row['store_status_id'] => [
							'name' => ($row['store_status_name'] ?: $row['store_status_id'])
						]
					] : []),
					'location-id' => json_encode($row['location_id'] ? [
						$row['location_id'] => [
							'name' => $row['location_name'],
							'count' => $row['count']
						]
					] : []),
					'location-count' => $row['location_count'] ?: 0,
					'category-id' => json_encode($row['category_id'] ? [
						$row['category_id'] => [
							'name' => $row['category_name']
						]
					] : []),
					'type-id' => json_encode($row['type_id'] ? [
						$row['type_id'] => [
							'name' => $row['type_name']
						]
					] : []),
					'model-id' => json_encode($row['model_id'] ? [
						$row['model_id'] => [
							'name' => $row['model_name']
						]
					] : []),
					'store-category-id' => json_encode($row['store_category_id'] ? [
						$row['store_category_id'] => [
							'name' => $row['store_category_name']
						]
					] : []),
					'forms' => ($row['opts']) ? getTypes(
						json_decode($row['opts'], true),
						json_decode($row['options'], true)
					) : '',
					'next' => $next['id'],
					'backusr' => $_GET['backusr'] ?: 0,
					'type-get' => (int)$_GET['type'],
					'brand' => $_GET['brand'],
					'model' => $_GET['model'],
					'owner-type' => $row['owner_type']
				], [
					'store' => $row['commerce'] OR $route[2] == 'stock',
					'type-get' => $_GET['type'],
					'brand' => $_GET['brand'],
					'model' => $_GET['model'],
					'main' => $row['main'],
					'publish' => $row['publish'],
					'save-data' => $row['save_data'] == 1,
					'backusr' => ($route[1] == 'edit' OR $route[1] == 'add') AND $_GET['backusr'],
					'customer-id' => $row['customer_id'],
					//'user' => (($route[1] == 'add' AND $id) OR ($route[1] == 'step' AND $_GET['user'])),
					'user' => (($route[1] == 'add' AND $_GET['user']) OR ($route[1] == 'step' AND $_GET['user'])),
					'is_user' => (($route[1] == 'add' AND $_GET['user']) OR ($route[1] == 'step' AND $_GET['user']) OR $row['customer_id'] OR ($row['customer_id'] AND !$row['commerce'])),
					'add' => ($route[1] == 'add'),
					'edit' => ($route[1] == 'edit'),
					'charger' => $row['charger'] == 1,
					'inventory' => (
						($row['type'] OR $route[2]) == 'stock' OR isset($route[3]) OR (
							$route[1] == 'add' AND $route[2] == 'inventory'
						) OR ($route[1] == 'add' AND $_GET['user']) 
					),
					'show' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR $objects_ip[$user['oip']] != 0),
					'notconfirmed' => ($row['confirmed'] == 0 AND (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR in_array(3, explode(',', $user['group_ids'])))),
					'next' => intval($next['id']),
					'create' => $user['create_inventory_transfer'],
					'add-service' => $user['add_service'],
					'add-iservice' => $user['add_iservice']
				], 'content');
			}
		}
	break;
	
	
	/*
	*  View inventory
	*/
	case 'view':
		$id = intval($route[2]);
		$meta['title'] = $lang['Inventory'];
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
					m.name as model_name,
					t.name as type_name,
					os.name as os_name,
					tr.cr_user,
					tr.cr_date,
					tr.cr_price,
					tr.cn_user,
					tr.cn_date,
					tr.cn_price,
					cru.name as cr_name,
					cru.lastname as cr_lastname,
					cnu.name as cn_name,
					cnu.lastname as cn_lastname
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_inventory_types`
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
				LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
					ON m.id = i.model_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_os` os
					ON os.id = i.os_id
				LEFT JOIN `'.DB_PREFIX.'_tradein` tr
					ON tr.inventory_id = i.id
				LEFT JOIN `'.DB_PREFIX.'_users` cru
					ON cru.id = tr.cr_user
				LEFT JOIN `'.DB_PREFIX.'_users` cnu
					ON cnu.id = tr.cn_user
				WHERE i.id = '.$id
			);

			if ($row['type'] == 'service') {
				tpl_set('/forbidden', [
					'text' => $lang['noAcc']
				], [], 'content');
			} else {
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
			
				$options = '';
				if($row['options'] AND $row['opts']){
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
				$history = '';
				foreach(db_multi_query('
						SELECT h.*, u.name, u.lastname, t.name as type, m.name as model, b.name as brand
					FROM `'.DB_PREFIX.'_inventory_history` h
						INNER JOIN `'.DB_PREFIX.'_users` u
					ON h.staff_id = u.id
						LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
					ON t.id = REGEXP_REPLACE(h.events, \'{(.*?)"type":"(.*?)"(.*?)}\', \'\\\2\')
						LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
					ON m.id = REGEXP_REPLACE(h.events, \'{(.*?)"model":"(.*?)"(.*?)}\', \'\\\2\')
						LEFT JOIN `'.DB_PREFIX.'_inventory_categories` b
					ON b.id = REGEXP_REPLACE(h.events, \'{(.*?)"brand":"(.*?)"(.*?)}\', \'\\\2\')
						WHERE h.inventory_id = '.$id.'
					ORDER BY h.id DESC LIMIT 50', true) as $hs){
						$events = '';
						foreach(json_decode($hs['events'], true) as $k => $v){
							if ($v AND $v != 'undefined') {
								if($events) $events .= ', <br>';
								switch ($k) {
									case 'type':
										$v = $hs['type'];
									break;
									case 'model':
										$v = $hs['model'];
									break;
									case 'brand':
										$v = $hs['brand'];
									break;
								}
								$events .= '<b>'.ucfirst($k).'</b> : '.$v;
							}
						}
					$history .= '<div class="tr dev">
						<div class="td w10">
							'.$hs['date'].'
						</div>
						<div class="td w100">
							<a href="/admin/users/view/'.$hs['staff_id'].'" target="_blank">'.$hs['name'].' '.$hs['lastname'].'</a>
						</div>
						<div class="td">
							'.$events.'
						</div>
					</div>';
				}
				
				$transfers = '';
				if ($transfer = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						t.*,
						CONCAT(fu.name, \' \', fu.lastname) as fu_name,
						CONCAT(tu.name, \' \', tu.lastname) as tu_name,
						fs.name as fs_name,
						ts.name as ts_name
					FROM `'.DB_PREFIX.'_inventory_transfer` t
					LEFT JOIN `'.DB_PREFIX.'_users` fu
						ON fu.id = t.from_manager
					LEFT JOIN `'.DB_PREFIX.'_users` tu
						ON tu.id = t.to_manager
					LEFT JOIN `'.DB_PREFIX.'_objects` fs
						ON fs.id = t.from_store
					LEFT JOIN `'.DB_PREFIX.'_objects` ts
						ON ts.id = t.to_store
					WHERE FIND_IN_SET('.$id.', t.inventory_ids) ORDER BY t.id DESC			
				', true)) {
					foreach($transfer as $tr) {
						$transfers = '<div class="tr'.($tr['del'] ? ' deleted' : ($tr['received'] ? ' confirmed' : '')).'">
							<div class="td">'.$tr['fs_name'].'</div>
							<div class="td">'.$tr['from_date'].'</div>
							<div class="td">'.$tr['fu_name'].'</div>
							<div class="td">'.$tr['ts_name'].'</div>
							<div class="td">'.$tr['to_date'].'</div>
							<div class="td">'.$tr['tu_name'].'</div>
						</div>';
					}
				}
				
				foreach(db_multi_query(
					'SELECT id, date, total, paid, conducted FROM `'.DB_PREFIX.'_invoices` WHERE inventory_info LIKE \'%"'.$id.'":%\''
				, true) as $invoice){
					$due = $invoice['total'] - $invoice['paid'];
					tpl_set('/cicle/usInvoice', [
						'id' => $invoice['id'],
						'date' => $invoice['date'],
						'total' => '$'.number_format($invoice['total'], 2, '.', ''),
						'paid' => '$'.number_format($invoice['paid'], 2, '.', ''),
						'due' => '$'.number_format((abs($due) < 0.01 ? 0 : $due), 2, '.', ''),
						'status' => $invoice['conducted'] ? 'Paid' : 'Unpaid',
						'date' => $invoice['date']
					], [
						'edit-invoce' => ($invoice['conducted'] ? $user['edit_paid_invoices'] : $user['edit_invoices'])
					], 'invoices');
				}
				
				tpl_set('inventory/view', [
					'id' => $id,
					'title' => 'View '.$row['type'],
					'model' => $row['model'],
					'charger' => $row['charger'] ? 'yes' : 'no',
					'model_name' => $row['model_name'],
					'password' => $row['device_password'],
					'type' => $row['type_name'],
					'currency' => $config['currency'][$row['currency']]['symbol'],
					'purchase-currency' => $config['currency'][$row['purchase_currency']]['symbol'],
					'income-price' => number_format($row['price'] - $row['purchase_price'], 2, '.', ''),
					'price' => number_format($row['price'], 2, '.', ''),
					'quantity' => $row['quantity'],
					'purchase-price' => number_format($row['purchase_price'], 2, '.', ''),
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
					'sublocation' => $row['location_ount'],
					'issues' => $tpl_content['issues'],
					'transfers' => $transfers,
					'stats' => $history,
					'forms' => $row['opts'] ? getTypes(
						json_decode($row['opts'], true),
						json_decode($row['options'], true)
					) : '',
					'invoices' => $tpl_content['invoices'] ?: '<div class="noContent">'.$lang['NoInvoices'].'</div>',
					'cr-date' => $row['cr_date'],
					'cr-price' => number_format($row['cr_price'], 2, '.', ''),
					'cr-user' => $row['cr_user'],
					'cr-name' => $row['cr_name'].' '.$row['cr_lastname'],
					'cn-date' => $row['cn_date'],
					'cn-price' => number_format($row['cn_price'], 2, '.', ''),
					'cn-user' => $row['cn_user'],
					'cn-name' => $row['cn_name'].' '.$row['cn_lastname']
				], [
					'transfers' => $transfers,
					'password' => $row['device_password'],
					'history' => $history,
					'customer' => $row['customer_id'],
					'store' => $row['commerce'],
					'not-customer' => $row['customer_id'] == 0,
					'ver' => $row['customer_ver'],
					'ava' => $row['customer_image'],
					'object-ava' => $row['object_image'],
					'add' => ($route[1] == 'add'),
					'edit' => ($route[1] == 'edit'),
					'inventory' => ($row['type'] == 'stock'),
					'add-service' => $user['add_service'],
					'add-iservice' => $user['add_iservice'],
					'create' => $user['create_inventory_transfer'],
					'edit-inventory' => ($user['service'] > 0 AND $row['customer_id'] == 0) OR $row['customer_id'] > 0,
					'invoices' => $tpl_content['invoices'],
					'cn' => $row['cn_user'] > 0,
					'tradein' => $row['tradein'] == 1,
					'Alexandr' => $user['id'] == 2
				], 'content');
			}
		}
	break;
	
	/*
	*  All inventary
	*/
	case null:
	case 'service':
	case 'stock':
	case 'tradein':
	case 'deleted':
		$meta['title'] = ($route[1] == 'stock' ? $lang['Inventory'] : ($route[1] == 'service' ? $lang['Services'] : ($route[1] == 'tradein' ? $lang['tradeinConfirm'] : ($route[1] == 'deleted' ? $lang['Deleted'] : $lang['StockServices']))));
		$query = text_filter($_REQUEST['query'], 255, false);
		$page = intval($_REQUEST['page']);
		$object = intval($_REQUEST['object']);
		$create = intval($_REQUEST['create']);
		$confirm = intval($_REQUEST['confirm']);
		$status = text_filter($_REQUEST['status'], 20, false);
		$craiglist = intval($_REQUEST['craiglist']);
		$type = (int)$_REQUEST['type'];
		$count = 20;
			
		$object_info = [];
		if (($user['store_id'] AND !$object) AND !$page AND !$_POST) {
			$object_info = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects` WHERE id = '.intval($user['store_id']));
		}
			
			if($sql = db_multi_query('
				SELECT DISTINCT SQL_CALC_FOUND_ROWS
					i.id, 
					IF(i.name = \'\', CONCAT(IFNULL(b.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name) as name,
					i.type, 
					i.price, 
					i.purchase_price, 
					i.quantity, 
					i.confirmed, 
					i.cr_user, 
					i.cr_date, 
					i.cr_issue,
					i.del,
					u.name as cr_name,
					u.lastname as cr_lastname,
					i.cn_user, 
					i.cn_date,
					i.currency,
					i.purchase_currency,
					i.craglist_url,
					i.craglist_date,
					cn.name as cn_name,
					cn.lastname as cn_lastname,
					'.($route[1] == 'tradein' ?
					'p.id as trade_id,
					p.cr_date as trade_cr_date,
					p.cr_price as trade_cr_price,
					p.cr_user as trade_cr_user,
					cru.name as trade_cr_name,
					cru.lastname as trade_cr_lastname,
					p.cn_date as trade_cn_date,
					p.cn_price as trade_cn_price,
					p.cn_user as trade_cn_user,
					cnu.name as trade_cn_name,
					cnu.lastname as trade_cn_lastname,
					p.confirmed as trade_confirmed,' : '').'
					o.name as object
					FROM `'.DB_PREFIX.'_inventory` i
					LEFT JOIN  `'.DB_PREFIX.'_inventory_categories` b
						ON b.id = i.category_id
					LEFT JOIN  `'.DB_PREFIX.'_inventory_models` m
						ON m.id = i.model_id
					LEFT JOIN  `'.DB_PREFIX.'_inventory_types` t
						ON t.id = i.type_id
					LEFT JOIN  `'.DB_PREFIX.'_users` u
						ON u.id = i.cr_user
					LEFT JOIN  `'.DB_PREFIX.'_users` cn
						ON cn.id = i.cn_user
					LEFT JOIN  `'.DB_PREFIX.'_users` c
						ON c.id = i.customer_id
					'.($route[1] == 'tradein' ?
					'LEFT JOIN  `'.DB_PREFIX.'_tradein` p
						ON p.inventory_id = i.id
					LEFT JOIN  `'.DB_PREFIX.'_users` cru
						ON cru.id = p.cr_user
					LEFT JOIN  `'.DB_PREFIX.'_users` cnu
						ON cnu.id = p.cn_user' : '' ).'
					LEFT JOIN  `'.DB_PREFIX.'_objects` o
						ON o.id = i.object_id
				WHERE i.customer_id = 0 '.(
					$status == 'confirmed' ? 'AND i.confirmed = 1 ' : ($status == 'notconfirmed' ? 'AND i.confirmed = 0 ' : '')
				).(
					$type ? (
						$type == 1 ? 'AND i.is_issue = 1 ' : 'AND i.is_issue = 0 '
					) : ''
				).(
					$route[1] == 'deleted' ? 'AND i.del = 1 ' : 'AND i.del = 0 '
				).(
					$query ? 'AND (CONCAT(
							IFNULL(b.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model
						) LIKE \'%'.$query.'%\' OR i.name LIKE \'%'.$query.'%\' OR CONCAT(
							c.name, c.lastname
						) LIKE \'%'.$query.'%\' OR c.email LIKE \'%'.$query.'%\' OR c.phone LIKE \'%'.$query.'%\' OR i.barcode LIKE \'%'.$query.'%\') ' : ''
				).(
					in_array($route[1], ['stock', 'service']) ? 'AND i.type = \''.$route[1].'\' ' : ''
				).(
					$route[1] === 'tradein' ? 'AND p.inventory_id = i.id ' : ''
				).(
					$create ? 'AND i.cr_user = '.$create.' ' : ''
				).(
					$confirm ? 'AND i.cn_user = '.$confirm.' ' : ''
				).(
					$craiglist ? 'AND i.craglist_date IS NOT NULL ' : ''
				).(
					$object ? 'AND i.object_id = '.$object.' ' : ''
				).(
					$object_info['id'] ? 'AND i.object_id = '.$object_info['id'].' ' : ''
				).($query ? '' : 'ORDER BY i.confirmed, '.($route[1] == 'tradein' ? 'p.confirmed,' : '').' i.id DESC').' LIMIT '.($page*$count).', '.$count, true
			)){
				$i = 0;
					foreach($sql as $row){
						tpl_set($route[1] === 'tradein' ? 'inventory/item' : 'inventory/store_devices', [
							'id' => $row['id'],
							'name' => $row['name'],
							'store' => $row['object'],
							'price' => $row['price'],
							'purchase_price' => $row['purchase_price'],
							'currency' => $config['currency'][$row['currency']]['symbol'],
							'purchase-currency' => $config['currency'][$row['purchase_currency']]['symbol'],
							'quantity' => $row['quantity'],
							'cr-date' => $row['cr_date'],
							'cn-date' => $row['cn_date'],
							'cr-issue' => $row['cr_issue'],
							'cr-user' => '<a href="/users/view/'.$row['cr_user'].'">'.$row['cr_name'].' '.$row['cr_lastname'].'</a>',
							'cn-user' => '<a href="/users/view/'.$row['cn_user'].'">'.$row['cn_name'].' '.$row['cn_lastname'].'</a>',
							'trade-id' => $row['trade_id'],
							'trade-cr-date' => $row['trade_cr_date'],
							'trade-cr-price' => number_format($row['trade_cr_price'], 2, '.', ''),
							'trade-cr-user' => '<a href="/users/view/'.$row['trade_cr_user'].'">'.$row['trade_cr_name'].' '.$row['trade_cr_lastname'].'</a>',
							'trade-cn-date' => $row['trade_cn_date'],
							'trade-cn-price' => number_format($row['trade_cn_price'], 2, '.', ''),
							'trade-cn-user' => '<a href="/users/view/'.$row['trade_cn_user'].'">'.$row['trade_cn_name'].' '.$row['trade_cn_lastname'].'</a>',
							'confirmed' => ($route[1] === 'tradein' ? ($row['trade_confirmed'] == 1 ? $lang['confirmed'] : $lang['unconfirmed']) : ($row['confirmed'] == 1 ? $lang['confirmed'] : $lang['unconfirmed'])),
							'craiglist_url' => $row['craglist_url'],
							'craiglist_date' => $row['craglist_date']
						], [
							'service' => $row['type'] == 'service',
							'stock' => $row['type'] == 'stock',
							'cr-issue' => $row['cr_issue'],
							'cr-info' => $row['cr_user'],
							'cn-info' => $row['cn_user'],
							'trade-conf' => $row['trade_confirmed'],
							'tradein' => $route[1] === 'tradein',
							'notconfirmed' => ($row['confirmed'] == 0 AND (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR in_array(3, explode(',', $user['group_ids'])))),
							'add-service' => $user['add_service'],
							'add-iservice' => $user['add_iservice'],
							'edit-iservice' => $user['edit_iservice'],
							'del-iservice' => $user['delete_iservice'],
							'del' => ($row['del'] == 1),
							'owner' => in_array(1, explode(',', $user['group_ids'])),
							'craiglist' => $row['craglist_url']
						], 'inventories');
						$i++;
					}

				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noInventory']
				], [], 'inventories');
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
				'query' => $query,
				'more' => $left_count ? '' : ' hdn',
				'inventory' => $tpl_content['inventories'],
				'title' => ($route[1] == 'stock' ? $lang['Inventory'] : ($route[1] == 'service' ? $lang['Services'] : ($route[1] == 'tradein' ? $lang['tradeinCofirm'] : ($route[1] == 'deleted' ? $lang['Deleted'] : $lang['StockServices'])))),
				'type' => $route[1],
				'store' => $object_info['id'] ? json_encode([$object_info['id'] => [
					'name' => $object_info['name']
				]]) : ''
			], [
				'stock' => $route[1] != 'service',
				'service' => $route[1] == 'service',
				'add-service' => $user['add_service'],
				'add-iservice' => $user['add_iservice'],
				'header' => $user['service'] == 0,
				'create' => $user['create_inventory_transfer'],
				'tradein' => $route[1] == 'tradein',
				'store' => $object_info['id']
			], 'content');
		/* } else {
			tpl_set('forbidden', [
				'text' => $lang['Forbidden'],
			], [], 'content');
		} */
	break;
	
	/*
	*  Del inventory
	*/
	case 'del':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_inventory']){
			//db_query('DELETE FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$id);
			db_query('UPDATE `'.DB_PREFIX.'_inventory` SET del = 1 WHERE id = '.$id);
			$i = db_multi_query('SELECT confirmed FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$id);
			if (!$i['confirmed'])
				db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'un_inventory\'');
			exit('OK');
		} else
			exit('ERR');
	break;
}
?>