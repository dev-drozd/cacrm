<?php
/**
 * @appointment Issues admin panel
 * @author      Victoria Shovkovych
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

if($gid = intval($_POST['type_id'])){
	echo json_encode(getTypes($gid), JSON_UNESCAPED_UNICODE);
	die;
}

switch($route[1]){
	
	/*
	* Send total
	*/
	case 'send_total':
		db_query('
			UPDATE `'.DB_PREFIX.'_issues` SET 
				total = \''.floatval($_POST['total']).'\' 
			WHERE id = '.intval($_POST['id'])
		);
		die('OK');
	break;
	
	/*
	* All feedbacks
	*/
	case 'feedbacks':
		$meta['title'] = 'Feedbacks';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		if($sql = db_multi_query('
			SELECT 
				f.*, 
				u.name, 
				u.lastname,
				u.image as ava,
				c.name as cname, 
				c.lastname as clastname,
				c.phone,
				c.image as cava 
			FROM `'.DB_PREFIX.'_feedback` f 
			LEFT JOIN `'.DB_PREFIX.'_users` u 
				ON u.id = f.staff_id 
			LEFT JOIN `'.DB_PREFIX.'_issues` i
				ON i.id = f.issue_id 
			LEFT JOIN `'.DB_PREFIX.'_users` c 
				ON c.id = f.customer_id  
			WHERE 1 '.(
				$query ? 'AND (CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' OR CONCAT(c.name, \' \', c.lastname) LIKE \'%'.$query.'%\' OR f.issue_id = \''.$query.'\') ' : ''
			).'ORDER BY f.id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				$star = '';
				for($j = 1; $j <= 5; $j++){
					$star .= '<span class="fa fa-star rStart mini r_'.$row['ratting'].(
						$j <= $row['ratting'] ? ' active' : ''
					).'"></span>';
				}
					
				tpl_set('issues/feedback/item', [
					'id' => $row['id'],
					'staff_id' => $row['staff_id'],
					'staff_name' => $row['name'].' '.$row['lastname'],
					'customer_id' => $row['customer_id'],
					'customer_name' => $row['cname'].' '.$row['clastname'],
					'phone' => $row['phone'],
					'descr' => $row['description'],
					'issue' => $row['issue_id'],
					'ava' => $row['ava'],
					'cava' => $row['cava'],
					'date' => $row['date'],
					'comment' => $row['comment'],
					'star' => $star 
				], [
					'ava' => $row['ava'],
					'cava' => $row['cava']
				], 'feedback');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['feedback'],
			]));
		}
		tpl_set('issues/feedback/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'feedback' => $tpl_content['feedback']
		], [
		], 'content');
	break;
	
	/*
	* Send invisible
	*/
	case 'send_invisible':
		$id = intval($_POST['id']);
		if ($id) {
			db_query('UPDATE `'.DB_PREFIX.'_issues` SET
					upcharge_id = '.intval($_POST['service']).'
					WHERE id = '.$id
			);
			echo 'OK';
		}
		die;
	break;
	
	/*
	* Send Feedback
	*/
	case 'send_feedback':
		$id = (int)$_POST['id'];
		$issue_id = (int)$_POST['issue_id'];
		$ratting = (int)$_POST['ratting'];
		if($ratting > 0 AND $ratting < 6 AND $issue_id){
			$uid = db_multi_query('
				SELECT 
					i.customer_id 
				FROM `'.DB_PREFIX.'_issues` iss
				LEFT JOIN `'.DB_PREFIX.'_inventory` i
					ON i.id = iss.inventory_id
				WHERE iss.id = '.$issue_id
			);
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_feedback` SET
					issue_id = '.$issue_id.',
					staff_id = \''.$user['id'].'\',
					customer_id = \''.$uid['customer_id'].'\',
					comment = \''.text_filter(
						$_POST['comment'], 1600, false
					).'\',
					ratting = '.$ratting.(
				$id ? ' WHERE id = '.$id : ''
			));
			
			if ($user['store_id'] > 0 AND $ratting == 5){
				$sql_ = db_multi_query('
					SELECT
						SUM(tb1.point) as sum,
						tb2.points
					FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
						 `'.DB_PREFIX.'_objects` tb2
					WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
						NOW(), INTERVAL 1 HOUR
					) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
				);
				
				$points = floatval($config['user_points']['feedback']['points']);
				
				if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'feedback\',
						object_id = '.$user['store_id'].',
						issue_id = '.$issue_id.',
						min_rate = '.$sql_['points'].',
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
						action = \'make_transaction\',
						min_rate = '.$sql_['points'].',
						point = \''.$points.'\',
						issue_id = '.$issue_id.',
						object_id = '.$user['store_id'].',
						rate_point = 1'
					);	
				}
			}
			
			echo 'OK';
		} else
			echo 'ERR';
		die;
	break;

	/*
	* Delete issues
	*/
	case 'del':
		$id = intval($_POST['id']);
		if($user['del_issue']){
			db_query('DELETE FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	*  Add/edit issues
	*/
	case 'add':
	case 'edit':
	case 'step':
		$id = intval($route[2]);
		$row = [];
		$inventories = [];
		$services = [];
		$nconfirmed = 0;
		$type = ($route[1] == 'edit' ? 'Edit' : 'Add');
		if($id AND $route[1] == 'edit'){
			$row = db_multi_query('SELECT 
				tb1.*,
				tb2.name as inname, tb2.status_id as inv_status, tb2.location_id as inv_location, tb2.object_id,
				tb3.name as status_name,
				tb4.name as location_name,
				tb5.name as object_name
				FROM `'.DB_PREFIX.'_issues` tb1 
				INNER JOIN `'.DB_PREFIX.'_inventory` tb2 ON tb1.inventory_id = tb2.id 
				LEFT JOIN `'.DB_PREFIX.'_inventory_status` tb3 ON tb2.status_id = tb3.id 
				LEFT JOIN `'.DB_PREFIX.'_objects_locations` tb4 ON tb2.location_id = tb4.id 
				LEFT JOIN `'.DB_PREFIX.'_objects` tb5 ON tb2.object_id = tb5.id 
				WHERE tb1.id = '.$id);
				
			  if(($row['inventory_ids'] ?: $row['service_ids']) && $route[1] == 'edit'){
				if($inv = db_multi_query('SELECT 
					i.id, i.name, i.model, i.category_id, i.type, i.confirmed,
					c.name as category_name, m.name as model_name
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
					ON c.id = i.category_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
					ON m.id = i.model_id
				WHERE i.id IN('.implode(',',
					array_map('intval', explode(',', trim(
						(
					(
					$row['inventory_ids'] AND $row['service_ids']) ? $row['inventory_ids'].$row['service_ids'] : (
					$row['inventory_ids'] ? $row['inventory_ids'] : $row['service_ids']
				)), ',')
					)
				  )
				).')', true)){
					foreach($inv as $i){
						if($i['type'] == 'stock'){
							$inventories[$i['id']] = [
								'name' => $i['category_name'].' '.$i['model_name'].' '.$i['model']
							];
						} else {
							if ($i['confirmed'] == 0) $nconfirmed = 1;
							$services[$i['id']] = [
								'name' => $i['name']
							];
						}
					}
				}
			}
		} else if ($route[1] == 'add' AND $id) {
			$row = db_multi_query('SELECT 
				i.object_id,
				o.name as object_name
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_objects` o 
					ON i.object_id = o.id 
				WHERE i.id = '.$id);
		}
		$objects_ip = array_flip($config['object_ips']);
		$meta['title'] = $route[1] == 'edit' ? $lang['editIssue'] : $lang['addIssue'];
		if($route[1] == 'add' OR (
			$route[1] == 'edit' AND $id
		) OR (
			$route[1] == 'step'
		)){
			if ($route[1] == 'edit') {
				if ($row['inv_status'] > 0) {
					$status = json_encode([$row['inv_status'] => [
						'name' => $row['status_name']
					]]);
				}
			} else {
				$status = json_encode([11 => [
					'name' => 'New'
				]]);
			}
			tpl_set('issues/'.($route[1] == 'step' ? 'step' : 'form'), [
				'id' => $id, //($route[1] == 'edit' AND $id ? $id : 0),
				'title' => $type.' issues',
				'name' => $row['name'],
				'descr' => $row['description'],
				'inventory-ids' => json_encode($inventories) ?: 0,
				'service-ids' => json_encode($services) ?: 0,
				'device-id' => (($route[1] == 'add' AND $id) OR ($route[1] == 'step' AND $id)) ? $id : ($row['inventory_id'] ?: 0),
				'device' => json_encode([$row['inventory_id'] => [
					'name' => $row['inname']
				]]),
				'status' => $status,
				'location' => json_encode(($row['inv_location'] AND $row['location_name']) ? [$row['inv_location'] => [
					'name' => $row['location_name']
				]] : []),
				'object-id' => json_encode($row['object_id'] ? [$row['object_id'] => [
					'name' => $row['object_name']
				]] : []),
				'send' => ($route[1] == 'edit' ? 'edit' : 'add')
			], [
				'confirmed' => $nconfirmed == 0,
				'edit' => ($route[1] == 'edit'),
				'view' => $route[1] == 'view',
				'show' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR $objects_ip[$user['oip']] != 0)
			], 'content');
		}
	break;
	
	/*
	*  Send issues
	*/
	case 'send': 
		is_ajax() or die('Hacking attempt!');
		// Filters
		$id = intval($_POST['id']) ?: 0;
		$end = time();
		$services = '';
		if ($_POST['service']) {
			foreach (explode(',', $_POST['service']) as $s) {
				$services .= $s.'_'.$end.',';
			}
		}
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.description as descr,
				i.inventory_ids as inventory,
				i.purchase_ids as purchases,
				i.service_ids,
				i.inventory_id,
				d.status_id as status,
				d.location_id as location,
				d.object_id
			FROM
				`'.DB_PREFIX.'_issues` i
			INNER JOIN
				`'.DB_PREFIX.'_inventory` d ON i.inventory_id = d.id
			WHERE i.id = '.$id)
			)
		){
			foreach($_POST as $field => $value){
				if($field == 'service' AND $sql['service_ids'] !== $services){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						changes = \''.$field.'\',
						changes_id = \''.ids_filter($_POST['service']).'\',
						object_id = '.$sql['object_id']
					);
				} else if($sql[$field] !== $value AND $field != 'object_id'){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						object_id = '.$sql['object_id'].',
						changes = \''.$field.'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.$value.'\''
					));
				}
			}
		} else
			$sql = db_multi_query('SELECT customer_id, object_owner FROM `'.DB_PREFIX.'_inventory` WHERE id = '.intval($_POST['inventory_id']));
		
		// SQL SET
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_issues` SET
				staff_id = '.$user['id'].','.(
					!$id ? 
						'customer_id = '.$sql['customer_id'].',
						 object_owner = '.$sql['object_owner'].',' : ''
				).'description = \''.text_filter($_POST['descr'], 2000, false).'\',
				inventory_ids = \''.($_POST['inventory'] ? ids_filter($_POST['inventory']).',' : '').'\',
				purchase_ids = \''.($_POST['purchases'] ? ids_filter($_POST['purchases']).',' : '').'\',
				service_ids = \''.$services.'\',
				inventory_id = '.intval($_POST['inventory_id']).(
			$id ? ' WHERE id = '.$id : ''
		));
		
		$id = $id ?: intval(mysqli_insert_id($db_link));
		
		if(!$_POST['id']){
			$oId = db_multi_query('SELECT object_id FROM `'.DB_PREFIX.'_inventory` WHERE id = '.intval($_POST['inventory_id']));
			db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
				issue_id = '.$id.',
				user = '.$user['id'].',
				changes = \'New issue\',
				object_id = '.$oId['object_id']
			);
			
			// Insert change log
			db_query(
				'INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					status_id = '.((int)$_POST['status'] ?: 11).',
					staff_id = '.$user['id'].',
					object_id = '.$oId['object_id'].',
					issue_id = '.$id.',
					action = \''.($id ? 'update_status' : 'new_issue').'\',
					inventory_id = '.intval($_POST['inventory_id'])
			);
		}
		
		
		if ($_POST['service']) {
			$end = time();
			$t = '';
			$value = $_POST['service'];
			foreach(explode(',', $value) as $val) {
				$val .= '_'.$end;
				$t .= $val.',';
				$vals .= '"'.$val.'":"'.$_POST['value'][$val].'",';
				$c_vals .= '"'.$val.'":{"staff" : "0", "comment": ""},';
			}
			$value = $t;
			$field = 'service_ids';

			// SQL SET
			db_query(
				'UPDATE `'.DB_PREFIX.'_issues` SET '.(
					true ? $field.' = \''.$value.'\'' : (
						false ? $field.' = REPLACE(
							'.$field.', \''.$value.',\', \'\'
						)' : $field.' = CONCAT('.$field.',\''.$value.'\')'
				)).(
					$field == 'service_ids' ? (
						false ?
						', options = REGEXP_REPLACE(options, \'"'.$value.'":"([^"]*?)",\', \'\')
						 , comments = REGEXP_REPLACE(comments, \'"'.$value.'":{([^"]*?)},\', \'\')' : 
						', options = IF(options != \'\', (REGEXP_REPLACE(options, \'{(.*)}\', \'{\\\1'.$vals.'}\')), \'{'.$vals.'}\')
						 , comments = IF(comments != \'\', (REGEXP_REPLACE(comments, \'{(.*)}\', \'{\\\1'.$c_vals.'}\')), \'{'.$c_vals.'}\')'
					) : (
						$field == 'purchase_ids' ? (
							false ? 
							', purchase_prices = REGEXP_REPLACE(purchase_prices, \'"'.$value.'":"([^"]*?)",\', \'\')' :
							', purchase_prices = IF(purchase_prices != \'\', (REGEXP_REPLACE(purchase_prices, \'{(.*)}\', \'{\\\1'.$vals.'}\')), \'{'.$vals.'}\')'
						) : ''
					)
				).' WHERE id = '.$id
			);
		}
		
		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET 
				pickup = 0'.(
				$_POST['status'] ? ', status_id = '.$_POST['status'] : ''
			).(
				$_POST['location'] ? ', location_id = '.intval($_POST['location']) : ''
			).(
				$_POST['location_count'] ? ', location_count = '.intval($_POST['location_count']) : ''
			).'
			 WHERE id = '.intval($_POST['inventory_id'])
		);
		
		echo $id;
		
		die;
	break;
	
	/*
	*  Send updates
	*/
	case 'send_update': 	
		is_ajax() or die('Hacking attempt!');
		// Filters
		$id = intval($_POST['id']);
		$status_id = (int)$_POST['status'];
		$set_services = ($_POST['set_services'] AND count(explode(',', $_POST['set_services'])) > 0) ? 1 : 0;
		$set_inventory =  ($_POST['set_inventory'] AND count(explode(',', $_POST['set_inventory'])) > 0) ? 1 : 0;
		
		$status = db_multi_query('SELECT service, inventory FROM `'.DB_PREFIX.'_inventory_status` WHERE id = '.$status_id);
		if ($set_services == 0 AND $status['service'] == 1)
			die('set_service');
		
		if ($set_inventory == 0 AND $status['inventory'] == 1)
			die('set_inventory');
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.description as descr,
				i.important as important,
				i.inventory_ids as inventory,
				i.purchase_ids as purchases,
				i.service_ids,
				i.inventory_id,
				d.customer_id,
				d.status_id as status,
				d.location_id as location,
				d.object_id,
				d.model,
				m.name as model_name,
				b.name as brand,
				p.date as previous_date,
				p.point as previous_point,
				p.staff_id '.(
					$status_id ? ', s.forfeit, s.point_group' : ''
				).'
			FROM '.($status_id ? ' `'.DB_PREFIX.'_inventory_status` s,' : '').'
				`'.DB_PREFIX.'_issues` i
			INNER JOIN
				`'.DB_PREFIX.'_inventory` d ON i.inventory_id = d.id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_models` m ON m.id = d.model_id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_status_history` p
				ON d.status_id = p.status_id
				AND p.inventory_id = i.inventory_id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_categories` b ON b.id = d.category_id
			WHERE i.id = '.$id.(
				$status_id ? ' AND s.id = '.$status_id : '').' ORDER by p.date DESC LIMIT 1'
				)
			)
		){
			foreach($_POST as $field => $value){
				if($sql[$field] !== $value){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						object_id = '.$sql['object_id'].',
						changes = \''.$field.'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.$value.'\''
					));
				}
			}
		}
		
		$service_part = '';
		$end = time();
		$svalue = '';
		$vals = '';
		$c_vals = '';
		if ($_POST['set_services']) {
			foreach(explode(',', $_POST['set_services']) as $val) {
				if (stristr($val, '_') === FALSE) $val .= '_'.$end;
				$svalue .= $val.',';
				$vals .= '"'.$val.'":"",';
				$c_vals .= '"'.$val.'":{"staff" : "0", "comment": ""},';
			}
		}
		
		// SQL SET
		db_query('UPDATE `'.DB_PREFIX.'_issues` SET
				important = \''.intval($_POST['important']).'\',
				inventory_ids = \''.($_POST['set_inventory'] ? ids_filter($_POST['set_inventory']).',' : '').'\',
				'.($_POST['set_services'] ? '
					service_ids = \''.$svalue.'\',
					options = IF(options != \'\', (REGEXP_REPLACE(options, \'{(.*)}\', \'{\\\1'.$vals.'}\')), \'{'.$vals.'}\'),
					comments = IF(comments != \'\', (REGEXP_REPLACE(comments, \'{(.*)}\', \'{\\\1'.$c_vals.'}\')), \'{'.$c_vals.'}\')' :
					'service_ids = \'\''
				).'
			WHERE id = '.$id 
		);

		$id = $id ?: intval(mysqli_insert_id($db_link));
		
		$issue_total = db_multi_query('
			SELECT 
				i.total,
				inv.id				
			FROM `'.DB_PREFIX.'_issues` i
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON inv.issue_id = i.id
			WHERE i.id = '.$id);
		
		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET 
				pickup = 0, 
				location_count = '.intval($_POST['sublocation']).(
				$_POST['status'] ? ', status_id = '.intval($_POST['status']) : ''
			).(
				$_POST['location'] ? ', location_id = '.intval($_POST['location']) : ''
			).'
			WHERE id = '.intval($_POST['inventory_id'])
		);
		
		if ($sql['status'] != $status_id) {

			$usr = db_multi_query('SELECT u.id as uid, u.name, u.lastname, u.sms as sms_number, f.content, s.sms as sms, s.sms_form as sms_form, s.purchase 
				FROM `'.DB_PREFIX.'_users` u,
					`'.DB_PREFIX.'_inventory_status` s
				LEFT JOIN `'.DB_PREFIX.'_forms` f
					ON f.id = s.sms_form
				WHERE s.id = '.$status_id.' 
					AND u.id = '.$sql['customer_id']);
					
			// ---------------------------------------------------------------------------------------- //
			
			if ($usr['purchase'] == 1) {
				db_query(
					'UPDATE `'.DB_PREFIX.'_issues`
						SET purchase_done = 1
					WHERE id = '.$id
				);
			}
					
			$point = 0;
			$sql_ = [];
			$rate_point = 1;
			
			// Is date
			if($sql['forfeit']){
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points-'.(int)$sql['forfeit'].'
					WHERE id = '.$sql['staff_id']
				);
				$point = -(int)$sql['forfeit'];
			} else if($sql['previous_date'] AND $sql['point_group']){
				$time = ceil(
					(time()-strtotime($sql['previous_date']))/60
				);
				$points = (array)json_decode($sql['point_group'], true);
				ksort($points);
				foreach($points as $k3 => $v){
					if($k3 <= $time OR $k3 == 0){
						$point = $v ?: 0;
						if ($user['store_id'] > 0){
							$sql_ = db_multi_query('
								SELECT
									SUM(tb1.point) as sum,
									tb2.points
								FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
									 `'.DB_PREFIX.'_objects` tb2
								WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
									NOW(), INTERVAL 1 HOUR
								) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
							);
							if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points'] AND ($issue_total['total'] >= 50)){
								db_query(
									'UPDATE `'.DB_PREFIX.'_users`
										SET points = points+'.($v ?: 0).'
									WHERE id = '.$user['id']
								);
								$rate_point = 0;
							}
						}
						break;
					}
				}
			}	
			
			print_r($issue_total['total']);
			// Insert change log
			if ($issue_total['total'] >= 50) {
				db_query(
					'INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						status_id = \''.$status_id.'\',
						staff_id = '.$user['id'].',
						min_rate = '.(int)$sql_['points'].',
						object_id = '.(int)$sql['object_id'].',
						issue_id = '.$id.',
						action = \'update_status\',
						rate_point = '.$rate_point.',
						inventory_id = '.(int)$_POST['inventory_id'].(
							$sql['previous_date'] ? ', point = \''.$point.'\'' : ''
						)
				);
			}
			
			// ---------------------------------------------------------------------------------------- //
			
			if ($usr['sms'] == 1 AND $usr['sms_form'] > 0) {
			
				//$phone = preg_replace("/\D/", '',$usr['phone']);
				$phone = $usr['sms_number'];

				if (strlen($phone) >=10) {
					$rand = rand();
					$url = 'http://192.168.1.206/default/en_US/sms_info.html';
					$line = '1';
					//$telnum = (strlen($phone) == 10 ? '+1'.$phone : '+'.$phone);
					$telnum = $phone;
					$smscontent = str_ireplace([
						'{name}',
						'{device}'
					], [
						$usr['name'].' '.$usr['lastname'],
						$sql['brand'].' '.$sql['model_name'].' '.$sql['model']
					], $usr['content']); 
					$username = "admin";
					$password = "admin";
					
					/* $headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: kuptjukvm@gmail.com'."\r\n";
					$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
					
					mail('kuptjukvm@gmail.com', 'Welcome to the Your Company', $smscontent, $headers); */

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
				}
			}
		}
		echo $id;
		die;
	break;
	
	/*
	*  Send updates
	*/
	case 'send_update2': 	
		is_ajax() or die('Hacking attempt!');
		// Filters
		$id = intval($_POST['id']);
		$status_id = (int)$_POST['status'];
		$set_services = ($_POST['set_services'] AND count(explode(',', $_POST['set_services'])) > 0) ? 1 : 0;
		$set_inventory =  ($_POST['set_inventory'] AND count(explode(',', $_POST['set_inventory'])) > 0) ? 1 : 0;
		
		$status = db_multi_query('SELECT service, inventory FROM `'.DB_PREFIX.'_inventory_status` WHERE id = '.$status_id);
		if ($set_services == 0 AND $status['service'] == 1)
			die('set_service');
		
		if ($set_inventory == 0 AND $status['inventory'] == 1)
			die('set_inventory');
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.description as descr,
				i.important as important,
				i.inventory_ids as inventory,
				i.purchase_ids as purchases,
				i.service_ids,
				i.inventory_id,
				d.customer_id,
				d.status_id as status,
				d.location_id as location,
				d.object_id,
				d.model,
				m.name as model_name,
				b.name as brand,
				p.date as previous_date,
				p.point as previous_point,
				p.staff_id '.(
					$status_id ? ', s.forfeit, s.point_group' : ''
				).'
			FROM '.($status_id ? ' `'.DB_PREFIX.'_inventory_status` s,' : '').'
				`'.DB_PREFIX.'_issues` i
			INNER JOIN
				`'.DB_PREFIX.'_inventory` d ON i.inventory_id = d.id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_models` m ON m.id = d.model_id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_status_history` p
				ON d.status_id = p.status_id
				AND p.inventory_id = i.inventory_id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_categories` b ON b.id = d.category_id
			WHERE i.id = '.$id.(
				$status_id ? ' AND s.id = '.$status_id : '').' ORDER by p.date DESC LIMIT 1'
				)
			)
		){
			foreach($_POST as $field => $value){
				if($sql[$field] !== $value){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						object_id = '.$sql['object_id'].',
						changes = \''.$field.'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.$value.'\''
					));
				}
			}
		}
		
		$service_part = '';
		$end = time();
		$svalue = '';
		$vals = '';
		$c_vals = '';
		if ($_POST['set_services']) {
			foreach(explode(',', $_POST['set_services']) as $val) {
				if (stristr($val, '_') === FALSE) $val .= '_'.$end;
				$svalue .= $val.',';
				$vals .= '"'.$val.'":"",';
				$c_vals .= '"'.$val.'":{"staff" : "0", "comment": ""},';
			}
		}
		
		// SQL SET
		db_query('UPDATE `'.DB_PREFIX.'_issues` SET
				important = \''.intval($_POST['important']).'\',
				inventory_ids = \''.($_POST['set_inventory'] ? ids_filter($_POST['set_inventory']).',' : '').'\',
				'.($_POST['set_services'] ? '
					service_ids = \''.$svalue.'\',
					options = IF(options != \'\', (REGEXP_REPLACE(options, \'{(.*)}\', \'{\\\1'.$vals.'}\')), \'{'.$vals.'}\'),
					comments = IF(comments != \'\', (REGEXP_REPLACE(comments, \'{(.*)}\', \'{\\\1'.$c_vals.'}\')), \'{'.$c_vals.'}\')' :
					'service_ids = \'\''
				).'
			WHERE id = '.$id 
		);

		$id = $id ?: intval(mysqli_insert_id($db_link));
		
		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET 
				pickup = 0, 
				location_count = '.intval($_POST['sublocation']).(
				$_POST['status'] ? ', status_id = '.intval($_POST['status']) : ''
			).(
				$_POST['location'] ? ', location_id = '.intval($_POST['location']) : ''
			).'
			WHERE id = '.intval($_POST['inventory_id'])
		);
		
		if ($sql['status'] != $status_id) {

			$usr = db_multi_query('SELECT u.id as uid, u.name, u.lastname, u.sms, f.content, s.sms as sms, s.sms_form as sms_form 
				FROM `'.DB_PREFIX.'_users` u,
					`'.DB_PREFIX.'_inventory_status` s
				LEFT JOIN `'.DB_PREFIX.'_forms` f
					ON f.id = s.sms_form
				WHERE s.id = '.$status_id.' 
					AND u.id = '.$sql['customer_id']);
					
			// ---------------------------------------------------------------------------------------- //
					
			$point = 0;
			$sql_ = [];
			$rate_point = 1;
			
			// Is date
			if($sql['forfeit']){
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points-'.(int)$sql['forfeit'].'
					WHERE id = '.$sql['staff_id']
				);
				$point = -(int)$sql['forfeit'];
			} else if($sql['previous_date'] AND $sql['point_group']){
				$time = ceil(
					(time()-strtotime($sql['previous_date']))/60
				);
				$points = (array)json_decode($sql['point_group'], true);
				ksort($points);
				foreach($points as $k3 => $v){
					if($k3 <= $time OR $k3 == 0){
						$point = $v ?: 0;
						if ($user['store_id'] > 0){
							$sql_ = db_multi_query('
								SELECT
									SUM(tb1.point) as sum,
									tb2.points
								FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
									 `'.DB_PREFIX.'_objects` tb2
								WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
									NOW(), INTERVAL 1 HOUR
								) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
							);
							if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
								db_query(
									'UPDATE `'.DB_PREFIX.'_users`
										SET points = points+'.($v ?: 0).'
									WHERE id = '.$user['id']
								);
								$rate_point = 0;
							}
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
					min_rate = '.(int)$sql_['points'].',
					object_id = '.(int)$sql['object_id'].',
					issue_id = '.$id.',
					action = \'update_status\',
					rate_point = '.$rate_point.',
					inventory_id = '.(int)$_POST['inventory_id'].(
						$sql['previous_date'] ? ', point = \''.$point.'\'' : ''
					)
			);
			
			// ---------------------------------------------------------------------------------------- //
			
			if ($usr['sms'] == 1 AND $usr['sms_form'] > 0) {
			
				//$phone = preg_replace("/\D/", '',$usr['phone']);
				$phone = $usr['phone'];

				if (strlen($phone) >=10) {
					$rand = rand();
					$url = 'http://192.168.1.206/default/en_US/sms_info.html';
					$line = '1';
					//$telnum = (strlen($phone) == 10 ? '+1'.$phone : '+'.$phone);
					$telnum = $phone;
					$smscontent = str_ireplace([
						'{name}',
						'{device}'
					], [
						$usr['name'].' '.$usr['lastname'],
						$sql['brand'].' '.$sql['model_name'].' '.$sql['model']
					], $usr['content']); 
					$username = "admin";
					$password = "admin";
					
					/* $headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: kuptjukvm@gmail.com'."\r\n";
					$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
					
					mail('kuptjukvm@gmail.com', 'Welcome to the Your Company', $smscontent, $headers); */

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
				}
			}
		}
		echo $id;
		die;
	break;
	
	/*
	*  Send updates
	*/
	case 'send_update3': 	
		is_ajax() or die('Hacking attempt!');
		// Filters
		$id = intval($_POST['id']);
		$status_id = (int)$_POST['status'];
		$set_services = ($_POST['set_services'] AND count(explode(',', $_POST['set_services'])) > 0) ? 1 : 0;
		$set_inventory =  ($_POST['set_inventory'] AND count(explode(',', $_POST['set_inventory'])) > 0) ? 1 : 0;
		
		$status = db_multi_query('SELECT service, inventory FROM `'.DB_PREFIX.'_inventory_status` WHERE id = '.$status_id);
		if ($set_services == 0 AND $status['service'] == 1)
			die('set_service');
		
		if ($set_inventory == 0 AND $status['inventory'] == 1)
			die('set_inventory');
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.description as descr,
				i.important as important,
				i.inventory_ids as inventory,
				i.purchase_ids as purchases,
				i.service_ids,
				i.inventory_id,
				d.customer_id,
				d.status_id as status,
				d.location_id as location,
				d.object_id,
				d.model,
				m.name as model_name,
				b.name as brand,
				p.date as previous_date,
				p.point as previous_point,
				p.staff_id '.(
					$status_id ? ', s.forfeit, s.point_group' : ''
				).'
			FROM '.($status_id ? ' `'.DB_PREFIX.'_inventory_status` s,' : '').'
				`'.DB_PREFIX.'_issues` i
			INNER JOIN
				`'.DB_PREFIX.'_inventory` d ON i.inventory_id = d.id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_models` m ON m.id = d.model_id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_status_history` p
				ON d.status_id = p.status_id
				AND p.inventory_id = i.inventory_id
			LEFT JOIN
				`'.DB_PREFIX.'_inventory_categories` b ON b.id = d.category_id
			WHERE i.id = '.$id.(
				$status_id ? ' AND s.id = '.$status_id : '').' ORDER by p.date DESC LIMIT 1'
				)
			)
		){
			foreach($_POST as $field => $value){
				if($sql[$field] !== $value){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						object_id = '.$sql['object_id'].',
						changes = \''.$field.'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.$value.'\''
					));
				}
			}
		}
		
		$service_part = '';
		$end = time();
		$svalue = '';
		$vals = '';
		$c_vals = '';
		if ($_POST['set_services']) {
			foreach(explode(',', $_POST['set_services']) as $val) {
				if (stristr($val, '_') === FALSE) $val .= '_'.$end;
				$svalue .= $val.',';
				$vals .= '"'.$val.'":"",';
				$c_vals .= '"'.$val.'":{"staff" : "0", "comment": ""},';
			}
		}
		
		// SQL SET
		db_query('UPDATE `'.DB_PREFIX.'_issues` SET
				important = \''.intval($_POST['important']).'\',
				inventory_ids = \''.($_POST['set_inventory'] ? ids_filter($_POST['set_inventory']).',' : '').'\',
				'.($_POST['set_services'] ? '
					service_ids = \''.$svalue.'\',
					options = IF(options != \'\', (REGEXP_REPLACE(options, \'{(.*)}\', \'{\\\1'.$vals.'}\')), \'{'.$vals.'}\'),
					comments = IF(comments != \'\', (REGEXP_REPLACE(comments, \'{(.*)}\', \'{\\\1'.$c_vals.'}\')), \'{'.$c_vals.'}\')' :
					'service_ids = \'\''
				).'
			WHERE id = '.$id 
		);

		$id = $id ?: intval(mysqli_insert_id($db_link));
		
		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET 
				pickup = 0, 
				location_count = '.intval($_POST['sublocation']).(
				$_POST['status'] ? ', status_id = '.intval($_POST['status']) : ''
			).(
				$_POST['location'] ? ', location_id = '.intval($_POST['location']) : ''
			).'
			WHERE id = '.intval($_POST['inventory_id'])
		);
		
		if ($sql['status'] != $status_id) {

			$usr = db_multi_query('SELECT u.id as uid, u.name, u.lastname, u.sms, f.content, s.sms as sms, s.sms_form as sms_form 
				FROM `'.DB_PREFIX.'_users` u,
					`'.DB_PREFIX.'_inventory_status` s
				LEFT JOIN `'.DB_PREFIX.'_forms` f
					ON f.id = s.sms_form
				WHERE s.id = '.$status_id.' 
					AND u.id = '.$sql['customer_id']);
					
			// ---------------------------------------------------------------------------------------- //
					
			$point = 0;
			$sql_ = [];
			$rate_point = 1;
			
			// Is date
			if($sql['forfeit']){
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points-'.(int)$sql['forfeit'].'
					WHERE id = '.$sql['staff_id']
				);
				$point = -(int)$sql['forfeit'];
			} else if($sql['previous_date'] AND $sql['point_group']){
				$time = ceil(
					(time()-strtotime($sql['previous_date']))/60
				);
				$points = (array)json_decode($sql['point_group'], true);
				ksort($points);
				foreach($points as $k3 => $v){
					if($k3 <= $time OR $k3 == 0){
						$point = $v ?: 0;
						if ($user['store_id'] > 0){
							$sql_ = db_multi_query('
								SELECT
									SUM(tb1.point) as sum,
									tb2.points
								FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
									 `'.DB_PREFIX.'_objects` tb2
								WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
									NOW(), INTERVAL 1 HOUR
								) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
							);
							if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
								db_query(
									'UPDATE `'.DB_PREFIX.'_users`
										SET points = points+'.($v ?: 0).'
									WHERE id = '.$user['id']
								);
								$rate_point = 0;
							}
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
					min_rate = '.(int)$sql_['points'].',
					object_id = '.(int)$sql['object_id'].',
					issue_id = '.$id.',
					action = \'update_status\',
					rate_point = '.$rate_point.',
					inventory_id = '.(int)$_POST['inventory_id'].(
						$sql['previous_date'] ? ', point = \''.$point.'\'' : ''
					)
			);
			
			// ---------------------------------------------------------------------------------------- //
			
			if ($usr['sms'] == 1 AND $usr['sms_form'] > 0) {
			
				//$phone = preg_replace("/\D/", '',$usr['phone']);
				$phone = $usr['phone'];

				if (strlen($phone) >=10) {
					$rand = rand();
					$url = 'http://192.168.1.206/default/en_US/sms_info.html';
					$line = '1';
					//$telnum = (strlen($phone) == 10 ? '+1'.$phone : '+'.$phone);
					$telnum = $phone;
					$smscontent = str_ireplace([
						'{name}',
						'{device}'
					], [
						$usr['name'].' '.$usr['lastname'],
						$sql['brand'].' '.$sql['model_name'].' '.$sql['model']
					], $usr['content']); 
					$username = "admin";
					$password = "admin";
					
					/* $headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: kuptjukvm@gmail.com'."\r\n";
					$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
					
					mail('kuptjukvm@gmail.com', 'Welcome to the Your Company', $smscontent, $headers); */

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
				}
			}
		}
		echo $id;
		die;
	break;
	
	/*
	*  Pick up
	*/
	case 'pickup': 
		is_ajax() or die('Hacking attempt!');

		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET pickup = 1
			WHERE id = '.intval($_POST['inventory_id'])
		);
				
		die('OK');
	break;
	
	/*
	*  View issues
	*/
	
	case 'view':
		$id = intval($route[2]);
		$row = [];
		$services_ids = '';
		$nconfirmed = 0;
		$nconfirmed_set = [
			'services' => [],
			'inventory' => []
		];
		$set_inventory = [];
		$set_services = [];
		$service_price = 0;
		if($id){
			$row = db_multi_query('
				SELECT 
					iss.*, 
					i.*,
					i.id as device_id,
					i.confirmed as confirmed,
					i.options as options,
					i.location_count as sublocation,
					t.options as opts,
					u.id as cust_id,
					u.name as customer_name,
					u.lastname as customer_lastname,
					it.id as intake_id,
					it.name as intake_name,
					it.lastname as intake_lastname,
					u.image as customer_image,
					u.ver as customer_ver,
					u.phone as customer_phone,
					u.address as customer_address,
					o.id as object_id,
					o.name as object_name,
					o.phone as object_phone,
					o.tax as object_tax,
					o.address as object_address,
					o.image as object_image,
					s.name as status_name,
					l.name as location_name,
					l.count as location_count,
					c.name as category_name,
					inv.id as invoice,
					os.name as os_name,
					d.name as discount_name,
					d.percent as discount_percent,
					inv.conducted as conducted,
					up.name as upcharge_name,
					up.price as inv_service
				FROM `'.DB_PREFIX.'_issues` iss 
				INNER JOIN `'.DB_PREFIX.'_inventory` 
					i ON iss.inventory_id = i.id 
				LEFT JOIN `'.DB_PREFIX.'_inventory_upcharge` 
					up ON iss.upcharge_id = up.id 
				LEFT JOIN `'.DB_PREFIX.'_inventory_types`
					t ON i.type_id = t.id
				LEFT JOIN `'.DB_PREFIX.'_users`
					u ON u.id = IF(iss.customer_id, iss.customer_id, i.customer_id)
				LEFT JOIN `'.DB_PREFIX.'_users`
					it ON it.id = iss.staff_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = IF(iss.object_owner, iss.object_owner, i.object_id)
				LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
					ON s.id = i.status_id
				LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
					ON l.id = i.location_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
					ON c.id = i.category_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_os` os
					ON os.id = i.os_id
				LEFT JOIN `'.DB_PREFIX.'_invoices` inv
					ON inv.issue_id = iss.id
				LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
					ON d.id = iss.discount
				WHERE iss.id = '.$id
			);
			$fb = '';
			if ($feedbacks = db_multi_query('
				SELECT f.*, u.name, u.lastname FROM `'.DB_PREFIX.'_feedback` f INNER JOIN `'.DB_PREFIX.'_users` u ON u.id = f.staff_id
				WHERE f.issue_id = '.$id.' ORDER by f.id', true
			)) {
				foreach($feedbacks as $feedback){
					$star = '';
					for($i = 1; $i <= 5; $i++){
						$star .= '<span class="fa fa-star rStart mini'.(
							$i <= $feedback['ratting'] ? ' active' : ''
						).'"></span>';
					}
					$fb .= '<div class="tr">
						<div class="td">
							'.$feedback['date'].'
						</div>					
						<div class="td">
							<a href="/users/view/'.$feedback['staff_id'].'" onclick="Page.get(this.href); return false;">'.$feedback['name'].' '.$feedback['lastname'].'</a>
						</div>
						<div class="td">'.$star.'
						</div>
						<div class="td">
							'.$feedback['comment'].'
						</div>
						<div class="td w100">
							<a href="#" class="hnt hntTop" data-title="Edit"><span class="fa fa-pencil"></span></a>
							<a href="#" class="hnt hntTop" data-title="Delete"><span class="fa fa-times"></span></a>
						</div>
					</div>';
				}
			}
			
			if ($row['device_id']) {
			
					if ($row['service_ids']) {
						foreach(explode(',', substr($row['service_ids'], 0, -1)) as $val) {
							$services_ids .= intval($val).',';
						}
					}
					
					$purchase_ids = substr($row['purchase_ids'], 0, -1);
					
					$users_ids = '';
					$users = [];
					$comments = db_multi_query('SELECT 
						i.comments
					FROM `'.DB_PREFIX.'_issues` i
					WHERE i.id = '.$id);
					if ($comments['comments']) {
						foreach(json_decode(substr($comments['comments'], 0, -2).'}', true) as $c) {
							$users_ids .= $c['staff'].',';
						}
						$users_sql = db_multi_query('SELECT id, CONCAT(name, \' \', lastname) as name FROM `'.DB_PREFIX.'_users` WHERE id IN('.substr($users_ids, 0, -1).')', true);
						$users = array_column($users_sql, 'name', 'id');
					}
					
					if ($row['inventory_ids'] AND $row['inventory_ids'] != ',' OR $row['service_ids'] AND $row['service_ids'] != ',') {
						$services = '';
						$inventories = '';
						$purchases = '';
						$serv_json = [];
						// get inventory and services
						$inv_serv = db_multi_query(
							'SELECT 
								iss.id, 
								iss.inventory_ids, 
								iss.service_ids,
								iss.options,
								iss.comments,
								inv.confirmed,
								inv.id as inv_id, 
								inv.name as inv_name, 
								inv.type as inv_type, 
								inv.type_id as inv_type_id, 
								inv.options as inv_options, 
								inv.location_id as inv_location_id, 
								inv.price as inv_price,
								inv.model as inv_model,
								inv.parts_required as req,
								inv.category_id as inv_category_id,
								c.name as inv_category_name, 
								t.name as inv_type_name, 
								t.options as opts, 
								l.name as inv_location_name,
								u.id as staff_id,
								u.name as staff_name,
								u.lastname as staff_lastname
							FROM `'.DB_PREFIX.'_issues` iss
							LEFT JOIN `'.DB_PREFIX.'_inventory` inv
								ON inv.id IN('.ids_filter((
									$row['inventory_ids'] AND $row['inventory_ids'] != ',' AND $row['service_ids'] AND $row['service_ids'] != ',') ? $row['inventory_ids'].substr($services_ids, 0, -1) : (
									($row['inventory_ids'] AND $row['inventory_ids'] != ',') ? substr($row['inventory_ids'], 0, -1) : substr($services_ids, 0, -1)
								)).')
							LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
								ON inv.category_id = c.id
							LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
								ON inv.type_id = t.id
							LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
								ON inv.location_id = l.id 
							LEFT JOIN `'.DB_PREFIX.'_users` u
								ON u.id = REGEXP_REPLACE(iss.comments, CONCAT(\'(.*)(?:"\', inv.id, \'":{"staff" : "(.*?)", "comment": "(.*?)"},)(.*)\'), \'\\\2\') 
							WHERE iss.id = '.$id
							, true);
						$cont_service = 0;
						foreach (array_values(array_filter($inv_serv, function($v) {
							if ($v['inv_type'] == 'service' AND $v['inv_price'] > 0)
								return $v;
						}, ARRAY_FILTER_USE_BOTH)) as $s) {
							$cont_service += substr_count($row['service_ids'], $s['inv_id'].'_');
						}
						$inv_price = ($cont_service > 0) ? $row['inv_service'] / $cont_service : 0;
						
						foreach($inv_serv as $issue){
							if ($issue['inv_type'] == 'stock') {
								if ($issue['confirmed'] == 0) {
									$nconfirmed = 1;
									$nconfirmed_set['inventory'][] = $issue['inv_id'];
								}
								tpl_set('/cicle/isStock', [
									'id' => $issue['inv_id'],
									'model' => $issue['inv_model'],
									'category' => $issue['inv_category_name'],
									'type' => $issue['inv_type_name'],
									'location' => $issue['inv_location_name'],
									'price' => number_format($issue['inv_price'], 2, '.', ""),
									'issue-id' => $issue['id']
								], [], 'inventory');
								$inventories .= $issue['inv_id'].',';
								$set_inventory[$issue['inv_id']] = [
									'name' => $issue['inv_type_name'].' '.$issue['inv_category_name'].' '.$issue['inv_model'],
									'price' => number_format($issue['inv_price'], 2, '.', "")
								];
							} else {
								$service_price += $issue['inv_price'];
								$comments = json_decode(substr($issue['comments'], 0, -2).'}', true);
								$count = substr_count($row['service_ids'], $issue['inv_id'].'_');
								$sIDs = explode(',', substr($row['service_ids'], 0, -1));
								foreach($sIDs as $k => $v) {
									if (strrpos($v, $issue['inv_id']) === false)
										unset($sIDs[$k]);
								}
								$sIDs = array_values($sIDs);
								if ($sIDs) {
									for ($j = 0; $j < $count; $j++) {
										$uId = $comments[$sIDs[$j]]['staff'];
										if ($issue['confirmed'] == 0) {
											$nconfirmed = 1;
											$nconfirmed_set['services'][] = $issue['inv_id'];
										}
										tpl_set('/cicle/isServices', [
											'id' => $sIDs[$j],
											'id-show' => $issue['inv_id'],
											'name' => $issue['inv_name'],
											'price' => number_format(($issue['inv_price'] > 0 ? ($issue['inv_price'] + $inv_price) : $issue['inv_price']), 2, '.', ""),
											'comment' => $comments[$sIDs[$j]]['comment'],
											'staff-id' => $uId,
											'staff-name' => $users[$uId],
											'issue-id' => $issue['id']
										], [
											'comment' => $comments[$sIDs[$j]]['comment']
										], 'services');
										
										tpl_set('/cicle/miniServices', [
											'id' => $sIDs[$j],
											'name' => $issue['inv_name'],
											'price' => '$'.number_format(($issue['inv_price'] > 0 ? ($issue['inv_price'] + $inv_price) : $issue['inv_price']), 2, '.', ''),
										], [
										], 'miniServices');
										
										$set_services[$sIDs[$j]] = [
											'name' => $issue['inv_name'],
											'price' => number_format($issue['inv_price'], 2, '.', ""),
											'req' => $issue['req']
										];
									
										$services .= $sIDs[$j].',';
										$opts = json_decode($issue['inv_options'], true);
										$options = [];
										if ($issue['inv_options']) {
											foreach(json_decode($issue['inv_options'], true) as $n => $v){
												$steps = json_decode(substr($issue['options'], 0, -2).'}', true);
												if(!$v) continue;
												$options[$n] = [
													'name' => $opts[$n],
													'value' => in_array($n, (explode(',', $steps[$sIDs[$j]])))
												];
											}
										}
										
										$serv_json[$sIDs[$j]] = [
											'name' => $issue['inv_name'],
											'steps' => $options
										];
									}
								}
							} 
						}
					}

					$set_purchases = [];
					if ($purchase_ids) {
						// get purchaces
						foreach(db_multi_query(
							'SELECT 
								iss.id, 
								iss.purchase_ids, 
								REGEXP_REPLACE(iss.purchase_prices, CONCAT(\'{(.*?)"\', p.id, \'":"(.*)",(.*?)}\'), \'\\\2\') as price, 
								p.id as pur_id,
								p.name as pur_name,
								p.sale_name as sale_name,
								p.link as pur_link,
								p.status as pur_status,
								p.sale as pur_price,
								p.total as total
							FROM `'.DB_PREFIX.'_issues` iss
							INNER JOIN `'.DB_PREFIX.'_purchases` p
								ON p.id in('.$purchase_ids.')
							WHERE p.del = 0 AND iss.id = '.$id
							, true) as $issue){
							$set_purchases[$issue['pur_id']] = [
								'name' => $issue['pur_name'],
								'price' => number_format(floatval(floatval($issue['price']) > 0 ? $issue['price'] : $issue['pur_price']), 2, '.', '')
							];
							tpl_set('/cicle/isPurchases', [
								'id' => $issue['pur_id'],
								'name' => $issue['sale_name'] ?: $issue['pur_name'],
								'status' => $issue['pur_status'],
								'link' => '<a href="'.$issue['pur_link'].'" target="_blank"><span class="fa fa-external-link"></span> View</a>',
								'price' => number_format(floatval(floatval($issue['price']) > 0 ? $issue['price'] : $issue['pur_price']), 2, '.', ''),
								'issue-id' => $issue['id']
							], [], 'purchases');
							$purchases .= $issue['pur_id'].',';
						}
					}

					
					// get invoices
					foreach(db_multi_query(
						'SELECT id, date, total, paid, conducted FROM `'.DB_PREFIX.'_invoices` WHERE issue_id = '.$id
					, true) as $invoice){
						tpl_set('/cicle/usInvoice', [
							'id' => $invoice['id'],
							'date' => $invoice['date'],
							'total' => $invoice['total'],
							'paid' => $invoice['paid'],
							'due' => $invoice['total'] - $invoice['paid'],
							'status' => $invoice['conducted'] ? 'Paid' : 'Unpaid'
						], [], 'invoices');
					}
					
					
					// get notes
					foreach(db_multi_query(
						'SELECT n.*,
								u.name,
								u.lastname
						FROM `'.DB_PREFIX.'_issues_notes` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.user
						WHERE issue_id = '.$id
					, true) as $note){
						tpl_set('/cicle/isNotes', [
							'id' => $note['id'],
							'date' => $note['date'],
							'note' => $note['comment'],
							'staff-id' => $note['user'],
							'staff-name' => $note['name'],
							'staff-lastname' => $note['lastname']
						], [], 'notes');
					}
					
					
					// get changes
					if ($stats = db_multi_query(
						'SELECT SQL_CALC_FOUND_ROWS
								n.*,
								u.name,
								u.lastname,
								s.name as status_name,
								l.name as location_name,
								d.name as discount_name,
								p.name as pur_name,
								p.sale_name as pur_sale_name
						FROM `'.DB_PREFIX.'_issues_changelog` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.user
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = n.changes_id AND n.changes = \'status\'
						LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
							ON l.id = n.changes_id AND n.changes = \'location\'
						LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
							ON d.id = n.changes_id AND n.changes = \'discount\'
						LEFT JOIN `'.DB_PREFIX.'_purchases` p
							ON p.id IN (n.changes_id) AND n.changes = \'purchase_ids\'
						WHERE n.issue_id = '.$id.' ORDER BY n.id DESC LIMIT 0, 20', true
					)){
						$invs = implode(',', array_map(function($v) {
								if (($v['changes'] == 'service_ids' OR $v['changes'] == 'inventory_ids'))
									return $v['changes_id'];
							}, $stats));
						$finvs = '';
						foreach(array_unique(explode(',', $invs)) as $inv) {
							if (intval($inv)) {
								if ($finvs) $finvs .= ',';
								$finvs .= intval($inv);
							}
						}
						if ($finvs) {
							$inventory = array_column(db_multi_query('SELECT 
									i.id, 
									IF(i.type = \'service\',
										i.name,
										CONCAT(IFNULL(t.name, \'\'), \' \', IFNULL(c.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model)) as name
								FROM `'.DB_PREFIX.'_inventory` i
								LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
									ON t.id = i.type_id
								LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
									ON c.id = i.category_id
								LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
									ON m.id = i.model_id
								WHERE i.id IN ('.$finvs.')
							', true), 'name', 'id');
						}
						foreach($stats as $note) {
							$change = '';
							switch ($note['changes']) {
								case 'status':
									$change = 'New status: '.$note['status_name'];
								break;
								
								case 'location':
									$change = 'New location: '.$note['location_name'];
								break;
								
								case 'service_ids':
									$links = '';
									foreach(explode(',', $note['changes_id']) as $cid) {
										if ($cid) {
											if ($links) $links .= ',';
											$links .= '<a href="/inventory/edit/service/'.intval($cid).'" target="_blank" class="statLink">'.$inventory[intval($cid)].'</a>';
										}
									}
									$change = 'New service: '.$links;
								break;
								
								case 'inventory_ids':
									$links = '';
									foreach(explode(',', $note['changes_id']) as $cid) {
										if ($cid) {
											if ($links) $links .= ',';
											$links .= '<a href="/inventory/view/'.intval($cid).'" target="_blank" class="statLink">'.$inventory[$cid].'</a>';
										}
									}
									$change = 'New inventory: '.$links;
								break;
								
								case 'purchase_ids':
									$links = '';
									foreach(explode(',', substr($note['changes_id'], 0, -1)) as $cid) {
										if ($links) $links .= ',';
										$links .= '<a href="/purchases/edit/'.intval($cid).'" target="_blank" class="statLink">'.($note['pur_sale_name'] ?: $note['pur_name']).'</a>';
									}
									$change = 'New purchace: '.$links;
								break;
								
								case 'New issue':
									$change = 'New issue';
								break;
								
								case 'discount':
									$change = 'New discount: '.$note['discount_name'];
								break;
								
								case 'reason':
									$change = 'New discount reason: '.$note['changes_id'];
								break;
								
								case 'discount_confirmed':
									if ($note['changes_id'] == 1)
										$change = 'Confirmed discount';
								break;
							}
							if ($change) {
								tpl_set('/cicle/isStats', [
									'id' => $note['id'],
									'date' => $note['date'],
									'changes' => $change,
									'staff-id' => $note['user'],
									'staff-name' => $note['name'],
									'staff-lastname' => $note['lastname']
								], [], 'stats');
							}
						}
					$res_count_changes = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					$left_count_changes = intval(($res_count_changes-20));
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
				$meta['title'] = $lang['viewIssue'];
				$objects_ip = array_flip($config['object_ips']);

				if ($route[1] == 'view' AND $id) {
					$forms = '';
					foreach(db_multi_query('
						SELECT id, name FROM `'.DB_PREFIX.'_forms`
						WHERE FIND_IN_SET(\'issue\', types) ORDER BY id LIMIT 50'
					, true) as $form){
						$forms .= '<li><a href="javascript:to_print(\'/forms?type=issue&id='.$form['id'].'&issue_id='.$id.'\', \'issue №'.$id.'\');" target="_blank">'.$form['name'].'</a></li>';
					}
					tpl_set('issues/view', [
						'id' => $id,
						'date' => $row['date'],
						'total' => $row['total'],
						'device-id' => $row['device_id'],
						'doItPrice' => $row['doit'] ?: 0,
						'quotePrice' => $row['quote'] ?: 0,
						'title' => 'View issue',
						'descr' => $row['description'],
						'device' => json_encode([$row['inventory_id'] => [
							'name' => $row['sname']
						]]),
						'set-inventory' => json_encode($set_inventory),
						'set-services' => json_encode($set_services),
						'set-purchases' => json_encode($set_purchases),
						'feedback' => $fb,
						'model' => $row['model'],
						'price' => $row['price'],
						'forms-list' => $forms,
						'purchase-price' => $row['purchase_price'],
						'sale-price' => $row['sale_price'],
						'category' => $row['category_name'],
						'os' => $row['os_name'],
						'version-os' => $row['ver_os'],
						'serial' => $row['serial'],
						'customer-ver' => $row['customer_ver'],
						'options' => $options,
						'intake-id' => $row['intake_id'],
						'intake-name' => $row['intake_name'],
						'intake-lastname' => $row['intake_lastname'],
						'customer-id' => $row['customer_id'],
						'customer-name' => $row['customer_name'],
						'customer-lastname' => $row['customer_lastname'],
						'customer-image' => $row['customer_image'],
						'customer-phone' => $row['customer_phone'],
						'customer-address' => preg_replace(
							"/\n/", "<br>", $row['customer_address']
						),			
						'object-id' => $row['object_id'],
						'object-tax' => $row['object_tax'],
						'object-image' => $row['object_image'],
						'object-phone' => $row['object_phone'],
						'object-address' => preg_replace(
							"/\n/", "<br>", $row['object_address']
						),
						'object' => $row['object_name'],
						'status' => $row['status_name'] ?: ($row['status_id'] == 'new' ? 'new' : 'finished'),
						'status-id' => $row['status_id'],
						'location' => $row['location_name'],
						'location-count' => $row['location_count'],
						'location-id' => $row['location_id'],
						'sublocation' => $row['sublocation'] ?: '',
						'inventory' => $tpl_content['inventory'] ?: '<div class="noContent">'.$lang['NoInventories'].'</div>',
						'services' => $tpl_content['services'] ?: '<div class="noContent">'.$lang['NoServices'].'</div>',
						'miniServices' => $tpl_content['miniServices'] ?: '<div class="noContent">'.$lang['NoServices'].'</div>',
						'purchases' => $tpl_content['purchases'] ?: '<div class="noContent">'.$lang['NoPurchases'].'</div>',
						'invoices' => $tpl_content['invoices'] ?: '<div class="noContent">'.$lang['NoInvoices'].'</div>',
						'notes' => $tpl_content['notes'] ?: '<div class="noContent">'.$lang['NoNotes'].'</div>',
						'stats' => $tpl_content['stats'] ?: '<div class="noContent">'.$lang['NoStats'].'</div>',
						'inventory-ids' => $inventories ?: 0,
						'service-ids' => $services ?: 0,
						'purchase-ids' => $purchases ?: 0,
						'discount-name' => $row['discount_name'] ? '('.$row['discount_name'].')' : '',
						'discount-id' => $row['discount'] ?: 0,
						'discount' => $row['discount_percent'] ?: 0,
						'forms' => $row['opts'] ? getTypes(
							json_decode($row['opts'], true),
							json_decode($row['options'], true)
						) : '',
						'serv_json' => json_encode($serv_json),
						'invoice' => $row['invoice'],
						'nconfirmed' => $nconfirmed,
						'more' => ($left_count_changes > 0 ? '' : ' hdn'),
						'discount-confirmed' => $row['discount_confirmed'],
						'discount-reason' => $row['discount_reason'],
						'unconfirmed_services' => implode(',', $nconfirmed_set['services']),
						'unconfirmed_inventory' => implode(',', $nconfirmed_set['inventory']),
						'unconfirmed_discount' => ($row['discount'] AND $row['discount_confirmed'] == 0 ? $row['discount_name'] : ''),
						'inv-service' => $row['inv_service'],
						'upcharge' => json_encode($row['upcharge_id'] ? [$row['upcharge_id'] => [
							'name' => $row['upcharge_name'],
							'price' => number_format($row['inv_service'], 2, '.', '')
						]] : [])
					], [
						'unconfirmed_services' => $nconfirmed_set['services'],
						'unconfirmed_inventory' => $nconfirmed_set['inventory'],
						'unconfirmed_discount' => ($row['discount'] AND $row['discount_confirmed'] == 0),
						'unconfirmed_feedback' => true,
						'nconfirmed' => $nconfirmed == 1 AND ($row['discount'] ? $row['discount_confirmed'] == 0 : 1),
						'confirmed' => $nconfirmed == 0 AND ($row['discount'] ? $row['discount_confirmed'] == 1 : 1),
						'important' => $row['important'],
						'customer' => $row['customer_id'],
						'store' => $row['commerce'],
						'ver' => $row['customer_ver'],
						'ava' => $row['customer_image'],
						'doit' => $row['doit'] > 0,
						'object-ava' => $row['object_image'],
						'add' => ($route[1] == 'add'),
						'edit' => ($route[1] == 'edit'),
						'inventory' => ($row['type'] == 'stock'),
						'is-inventory' => $tpl_content['inventory'],
						'is-services' => $tpl_content['services'],
						'is-purchase' => $tpl_content['purchases'],
						'is-notes' => $tpl_content['notes'],
						'invoice' => $row['invoice'],
						'invoice-done' => $row['condected'],
						'pickup' => $row['pickup'] == 1,
						'view' => $route[1] == 'view',
						'sublocation' => $row['sublocation'],
						'service-price' => $service_price > 0,
						'feedback' => $fb,
						'create-invoice' => $id < 1535 OR $row['total'] > 0,
						'show' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR $objects_ip[$_SERVER['REMOTE_ADDR']] != 0)
					], 'content');
				}
				
			} else {
				tpl_set('noContent', [
					'text' => 'Sorry, issue device was deleted'
				], [
				], 'content');
			}
		}
	break;
	
	/*
	* Doload stats
	*/
	case 'doload_stats':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$page = intval($_POST['page']);
		$count = 20;
		$i = 0;
		if ($stats = db_multi_query(
				'SELECT SQL_CALC_FOUND_ROWS
								n.*,
								u.name,
								u.lastname,
								s.name as status_name,
								l.name as location_name,
								d.name as discount_name,
								p.name as pur_name,
								p.sale_name as pur_sale_name
						FROM `'.DB_PREFIX.'_issues_changelog` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.user
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = n.changes_id AND n.changes = \'status\'
						LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
							ON l.id = n.changes_id AND n.changes = \'location\'
						LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
							ON d.id = n.changes_id AND n.changes = \'discount\'
						LEFT JOIN `'.DB_PREFIX.'_purchases` p
							ON p.id IN (n.changes_id) AND n.changes = \'purchase_ids\'
						WHERE n.issue_id = '.$id.' ORDER BY n.id DESC LIMIT '.($page*$count).', '.$count
			, true)) {
				
				$invs = implode(',', array_map(function($v) {
						if (($v['changes'] == 'service_ids' OR $v['changes'] == 'inventory_ids'))
							return $v['changes_id'];
					}, $stats));
				$finvs = '';
				foreach(array_unique(explode(',', $invs)) as $inv) {
					if (intval($inv)) {
						if ($finvs) $finvs .= ',';
						$finvs .= intval($inv);
					}
				}
				if ($finvs) {
					$inventory = array_column(db_multi_query('SELECT 
							i.id, 
							IF(i.type = \'service\',
								i.name,
								CONCAT(IFNULL(t.name, \'\'), \' \', IFNULL(c.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model)) as name
						FROM `'.DB_PREFIX.'_inventory` i
						LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
							ON t.id = i.type_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
							ON c.id = i.category_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
							ON m.id = i.model_id
						WHERE i.id IN ('.$finvs.')
					', true), 'name', 'id');
				}
						
				foreach($stats as $note) {
					$change = '';
					switch ($note['changes']) {
						case 'status':
							$change = 'New status: '.$note['status_name'];
						break;
						
						case 'location':
							$change = 'New location: '.$note['location_name'];
						break;
						
						case 'service_ids':
							$links = '';
							foreach(explode(',', $note['changes_id']) as $cid) {
								if ($cid) {
									if ($links) $links .= ',';
									$links .= '<a href="/inventory/edit/service/'.intval($cid).'" target="_blank" class="statLink">'.$inventory[intval($cid)].'</a>';
								}
							}
							$change = 'New service: '.$links;
						break;
						
						case 'inventory_ids':
							$links = '';
							foreach(explode(',', $note['changes_id']) as $cid) {
								if ($cid) {
									if ($links) $links .= ',';
									$links .= '<a href="/inventory/view/'.intval($cid).'" target="_blank" class="statLink">'.$inventory[$cid].'</a>';
								}
							}
							$change = 'New inventory: '.$links;
						break;
						
						case 'purchase_ids':
							$links = '';
							foreach(explode(',', substr($note['changes_id'], 0, -1)) as $cid) {
								if ($links) $links .= ',';
								$links .= '<a href="/purchases/edit/'.intval($cid).'" target="_blank" class="statLink">'.($note['pur_sale_name'] ?: $note['pur_name']).'</a>';
							}
							$change = 'New purchace: '.$links;
						break;
						
						case 'New issue':
							$change = 'New issue';
						break;
					}
					if ($change) {
						tpl_set('/cicle/isStats', [
							'id' => $note['id'],
							'date' => $note['date'],
							'changes' => $change,
							'staff-id' => $note['user'],
							'staff-name' => $note['name'],
							'staff-lastname' => $note['lastname']
						], [], 'stats');
					}
					$i++;
				}
			$res_count_changes = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			$left_count_changes = intval(($res_count_changes-($page*$count)-$i));
		}
		
		print_r(json_encode([
			'content' => $tpl_content['stats'],
			'left_count' => $left_count_changes
		]));
		die;
	break;
	
	/*
	* Send field
	*/
	case 'send_field':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		if ($_POST['link'] AND floatval($_POST['cprice']) < min_price(floatval($_POST['price']))) {
			die('min_price');
		}
		
		if ($_POST['link'] AND !text_filter($_POST['salename'], 1000, false)) {
			die('empty_salename');
		}
		
		$services_ids = '';
		if ($_POST['field'] == 'service_ids') {
			foreach(explode(',', substr($_POST['value'], 0, -1)) as $val) {
				if ($services_ids) $services_ids .= ',';
				$services_ids .= intval($val);
			}
		}
		
		if ($_POST['field'] == 'service_ids' AND $user['id'] == 17) {
			$services = db_multi_query('SELECT SUM(parts_required) as req FROM `'.DB_PREFIX.'_inventory` WHERE id IN ('.$services_ids.')');
			if ($services['req'] > 0 AND !ids_filter($_POST['inventory']) AND !ids_filter($_POST['purchases']) AND !$_POST['link'])
				die('req');
		}
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.description as descr,
				i.inventory_ids as inventory,
				i.purchase_ids as purchases,
				i.service_ids,
				i.inventory_id,
				d.status_id as status,
				d.location_id as location
			FROM
				`'.DB_PREFIX.'_issues` i
			INNER JOIN
				`'.DB_PREFIX.'_inventory` d ON i.inventory_id = d.id
			WHERE i.id = '.$id)
			)
		){
			/* foreach($_POST as $field => $value){
				if($field == 'service' AND $sql['service_ids'] !== $services){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						changes = \''.$field.'\',
						changes_id = \''.ids_filter($_POST['service']).'\''
					);
				} else if($sql[$field] !== $value){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						changes = \''.$field.'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.$value.'\''
					));
				}
			} */
			db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
				issue_id = '.$id.',
				user = '.$user['id'].',
				changes = \''.text_filter($_POST['field'], 50, false).'\''.(
				$field == 'descr' ? '' : ', changes_id = \''.text_filter($_POST['value'], null, false).'\''
			));
		}
		
		if ($_POST['link']) {
			$objects_ip = array_flip($config['object_ips']);
			db_query('INSERT INTO `'.DB_PREFIX.'_purchases` SET
				name = \''.text_filter($_POST['name'], 100, false).'\',
				sale_name = \''.text_filter($_POST['salename'], 1000, false).'\',
				link = \''.text_filter($_POST['link'], 200, false).'\',
				price = \''.text_filter($_POST['price'], 30, false).'\',
				tracking = \''.text_filter($_POST['itemID'], 50, false).'\',
				sale = \''.text_filter($_POST['cprice'], 30, false).'\',
				quantity = 1,
				total = \''.text_filter($_POST['price'], 30, false).'\',
				object_id = \''.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).'\',
				status = \'Purchased\',
				customer_id = '.intval($_POST['cid']).',
				create_id = '.$user['id'].', 
				create_date = \''.date('Y-m-d H:i:s', time()).'\',
				issue_id = '.$id
			);
			
			$pid = intval(mysqli_insert_id($db_link));
			
			db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', event = \'add_purchase\', object_id = '.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).', event_id = '.$pid);
			
			if (isset($_POST['photo'])) {
				$dir = ROOT_DIR.'/uploads/images/';
				if(!is_dir($dir.$pid)){
					@mkdir($dir.$pid, 0777);
					@chmod($dir.$pid, 0777);
				}
				$dir = $dir.$pid.'/';
				$type = mb_strtolower(pathinfo($_POST['photo'], PATHINFO_EXTENSION));
				
				$rename = uniqid('', true).'.'.$type;
				file_put_contents($dir.$rename, file_get_contents($_POST['photo']));
				
				$img = new Imagick($dir.$rename);
				$img->cropThumbnailImage(94, 94);
				$img->stripImage();
				$img->writeImage($dir.'thumb_'.$rename);
				$img->destroy();
				db_query('UPDATE `'.DB_PREFIX.'_purchases` SET photo = \''.$rename.'\' WHERE id = '.$pid);
			}
			
			send_push(0, [
				'type' => 'purchase',
				'id' => '/purchases/edit/'.$pid,
				'name' => $user['uname'],
				'lastname' => $user['ulastname'],
				'message' => 'Purchase #'.$pid.' created. Please, confirm',
				'arguments' => [
					'confirm_purchase' =>md5(md5(1).md5(SOLT, true))
				]
			]);
		}
		$value = text_filter($_POST['value'], null, false).(($_POST['link'] && $pid && $field != 'service_ids') ? $pid.',' : '');
		$field = text_filter($_POST['field'], 50, true);
		$end = time();
		$t = '';
		if (($field == 'service_ids' OR $field == 'purchase_ids') AND !$_POST['del']) {
			foreach(explode(',', substr($value, 0, -1)) as $val) {
				if ($field == 'service_ids') {
					if (stripos($val, '_') === false) {
						$val .= '_'.$end;
						$t .= $val.',';
					}
				}
				$vals .= '"'.$val.'":"'.($val == $pid ? (intval($_POST['cprice']) != 0 ? intval($_POST['cprice']) : '') : $_POST['value'][$val]).'",';
				$c_vals .= '"'.$val.'":{"staff" : "0", "comment": ""},';
			}
			if ($field == 'service_ids') $value = $t;
			
			$purch = '';
			if ($field == 'service_ids' AND ($_POST['purchases'] OR $pid)) {
				foreach(explode(',', substr(($_POST['purchases'] ?: '').($pid ? $pid.',' : ''), 0, -1)) as $val) {
					$purch .= '"'.$val.'":"",';
				}
			}
		}

		// SQL SET
		db_query(
			'UPDATE `'.DB_PREFIX.'_issues` SET '.(
				$_POST['full'] ? $field.' = \''.$value.'\'' : (
					$_POST['del'] ? $field.' = REPLACE(
						'.$field.', \''.$value.',\', \'\'
					)' : $field.' = CONCAT('.$field.',\''.$value.'\')'
			)).(
				$field == 'service_ids' ? (
					$_POST['del'] ?
					', options = REGEXP_REPLACE(options, \'"'.$value.'":"([^"]*?)",\', \'\')
					 , comments = REGEXP_REPLACE(comments, \'"'.$value.'":{([^"]*?)},\', \'\')' : 
					', options = IF(options != \'\', (REGEXP_REPLACE(options, \'{(.*)}\', \'{\\\1'.$vals.'}\')), \'{'.$vals.'}\')
					 , comments = IF(comments != \'\', (REGEXP_REPLACE(comments, \'{(.*)}\', \'{\\\1'.$c_vals.'}\')), \'{'.$c_vals.'}\')
					, inventory_ids = \''.text_filter($_POST['inventory'] == ',' ? '' : $_POST['inventory']).'\'
													, purchase_ids = \''.text_filter($_POST['purchases']).($pid ? $pid.',' : '').'\'
													, purchase_prices = IF(purchase_prices != \'\', (REGEXP_REPLACE(purchase_prices, \'{(.*)}\', \'{\\\1'.$purch.'}\')), \'{'.$purch.'}\')'
				) : (
					$field == 'purchase_ids' ? (
						$_POST['del'] ? 
						', purchase_prices = REGEXP_REPLACE(purchase_prices, \'"'.$value.'":"([^"]*?)",\', \'\')' :
						', purchase_prices = IF(purchase_prices != \'\', (REGEXP_REPLACE(purchase_prices, \'{(.*)}\', \'{\\\1'.$vals.'}\')), \'{'.$vals.'}\')'
					) : ''
				)
			).' WHERE id = '.intval($_POST['id'])
			);
		die('OK');
	break;
	
	/*
	* Send discount
	*/
	case 'send_discount':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$discount = 0;
		if (!in_to_array('1,2', $user['group_ids'])) {
			$d = db_multi_query('SELECT percent FROM `'.DB_PREFIX.'_invoices_discount` WHERE id = '.$id);
			$discount = $d['percent'];
		}
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.discount,
				i.discount_reason as reason,
				d.object_id
			FROM `'.DB_PREFIX.'_issues` i
			LEFT JOIN `'.DB_PREFIX.'_inventory` d
				ON d.id = i.inventory_id
			WHERE i.id = '.$id
		))){
			foreach($_POST as $field => $value){
				if($sql[$field] !== $value){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						object_id = '.$sql['object_id'].',
						changes = \''.$field.'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.$value.'\''
					));
				}
			}
		}
		
		// SQL SET
		db_query(
			'UPDATE `'.DB_PREFIX.'_issues` SET 
				discount = '.text_filter($_POST['discount'], null, false).',
				discount_reason = \''.text_filter($_POST['reason'], null, false).'\',
				discount_user = \''.$user['id'].'\',
				discount_confirmed = '.((in_to_array('1,2', $user['group_ids']) OR $discount < 5) ? 1 : 0).'
			WHERE id = '.$id);
		die('OK');
	break;
	
	/*
	* Send step
	*/
	case 'send_step':
		is_ajax() or die('Hacking attempt!');
		// SQL SET
		db_query(
			'UPDATE `'.DB_PREFIX.'_issues` SET options = REGEXP_REPLACE(
				options, '.(
					$_POST['del'] ? 
					'\'"'.$_POST['sId'].'":"([^"]*?)'.text_filter($_POST['value'], null, false).',([^"]*?)"\',
					 \'"'.$_POST['sId'].'":"\\\1\\\2"\'' :
					'\'"'.$_POST['sId'].'":"([^"]*?)"\',
					 \'"'.$_POST['sId'].'":"\\\1'.text_filter($_POST['value'], null, false).',"\''
				).') 
				WHERE id = '.intval($_POST['id'])
			);
		die('OK');
	break;
	
	/*
	* Send comment
	*/
	case 'send_comment':
		is_ajax() or die('Hacking attempt!');
		$value = '"staff" : "'.$user['id'].'", "comment": "'.text_filter($_POST['value'], null, false).'"';
		
		// SQL SET
		db_query(
			'UPDATE `'.DB_PREFIX.'_issues` SET comments = REGEXP_REPLACE(
				comments, \'(.*)"'.$_POST['sId'].'":{(.*)},(.*)\',
					 \'\\\1"'.$_POST['sId'].'":{'.$value.'},\\\3\') 
				WHERE id = '.intval($_POST['id'])
			);
		die('OK');
	break;
	
	/*
	*  Send note
	*/
	case 'send_note': 
		is_ajax() or die('Hacking attempt!');
		
		// SQL SET
		db_query('INSERT INTO
		 `'.DB_PREFIX.'_issues_notes` SET
				issue_id = '.intval($_POST['id']).',
				user = '.$user['id'].',
				comment = \''.text_filter($_POST['note']).'\''
		);
		die('OK');
	break;
	
	/*
	*  Send price
	*/
	case 'send_price': 
		is_ajax() or die('Hacking attempt!');
		
		// SQL SET
		db_query(
			'UPDATE `'.DB_PREFIX.'_issues` SET purchase_prices = REGEXP_REPLACE(
				purchase_prices, \'(.*)"'.intval($_POST['pur']).'":"(.*)",(.*)\',
					 \'\\\1"'.intval($_POST['pur']).'":"'.floatval($_POST['value']).'",\\\3\') 
				WHERE id = '.intval($_POST['id'])
			);
		die('OK');
	break;
	
	/*
	* All issues
	*/
	default:
		$meta['title'] = $lang['allIssues'];
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_issues` '.(
			$query ? 'WHERE name LIKE \'%'.$query.'%\' ' : ''
		).'ORDER BY `id` LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('issues/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'descr' => $row['description']
				], [], 'issues');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'issues' => $tpl_content['issues'],
			]));
		}
		tpl_set('issues/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'issues' => $tpl_content['issues']
		], [
			'edit' => $user['edit_issues'],
			'add' => $user['add_issues']
		], 'content');
}
?>