<?php
/**
 * @appointment Jobs admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]){
	
	/*
	* Jobs
	*/
	case null:
		$query = text_filter($_REQUEST['query'], 255, false);
		$object = intval($_REQUEST['object']);
		$date_start = text_filter(($_REQUEST['date_start'] ?: $_REQUEST['sDate']), 30, true);
		$date_finish = text_filter(($_REQUEST['date_finish'] ?: $_REQUEST['eDate']), 30, true);
		$payment = text_filter($_REQUEST['payment'], 30, true) == 'paid' ?: 'unpaid';
		$page = intval($_REQUEST['page']);
		$staff = intval($_REQUEST['staff']);
		$status = (int)$_REQUEST['status'];
		$cstatus = intval($_REQUEST['current_status']);
		$instore = intval($_REQUEST['instore']);
		$pickedup = intval($_REQUEST['pickedup']);
		$all = intval($_REQUEST['all']);
		$count = 20;
		$store_issues = ($route[2] == 'store' ? 1 : 0);
		
		if($sql = db_multi_query('SELECT SQL_NO_CACHE SQL_CALC_FOUND_ROWS 
			iss.*,
			DATE(iss.date) as date,
			inv.id as inv_id,  
			inv.object_id as object_id,  
			inv.type_id as inv_type_id, 
			iss.status_id as inv_status_id, 
			inv.location_id as inv_location_id,
			inv.location_count as inv_location_count,
			o.id as object_id,
			o.name as object_name,
			o.image as object_image,
			c.name as inv_category_name, 
			s.name as inv_status_name, 
			s.back,
			s.back_even,
			t.name as inv_type_name,  
			l.name as inv_location_name,
			u.id as user_id,
			u.name as user_name,
			u.lastname as user_lastname,
			u.reg_date,
			u.image as user_image,
			m.id as staff_id,
			m.name as staff_name,
			m.lastname as staff_lastname,
			st.name as selected_status,
			i.conducted,
			i.paid,
			i.total,
			i.tax
		FROM `'.DB_PREFIX.'_issues` iss
		'.($status ? ' INNER JOIN `'.DB_PREFIX.'_issues_changelog` cl '.(
			$status == 11 ? 'ON cl.changes LIKE \'New issue\'' : 'ON cl.changes_id LIKE '.$status. ' AND cl.changes LIKE \'status\' '
		) : '').'
		LEFT JOIN `'.DB_PREFIX.'_inventory` inv
			ON inv.id = iss.inventory_id
		LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
			ON inv.category_id = c.id
		LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
			ON inv.type_id = t.id
		LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
			ON s.id = iss.status_id
		LEFT JOIN `'.DB_PREFIX.'_inventory_status` st
			ON st.id = '.$status.'
		LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
			ON inv.location_id = l.id 
		LEFT JOIN `'.DB_PREFIX.'_users` m
			ON m.id = iss.staff_id
		LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = IF (iss.object_owner !=0, iss.object_owner, inv.object_id)
		LEFT JOIN `'.DB_PREFIX.'_users` u
			ON u.id = iss.customer_id
		LEFT JOIN `'.DB_PREFIX.'_invoices` i
			ON i.issue_id = iss.id
		WHERE 1 '.($all ? '' : 'AND IF(iss.warranty, 0, 1) ').(
			$store_issues ? ' AND iss.customer_id = 0 ' : ''
		).(
			$object ? ' AND iss.object_owner = \''.$object.'\'' : ''
		).(
			$user['issues_show_all'] ? '' : (
				$user['issues_show_anywhere'] ? ' AND (iss.staff_id = '.$user['id'].(
					$store_id ? ' OR o.id = '.$store_id : ''
				).')' : (
					$store_id ? ' AND (iss.staff_id = '.$user['id'].' OR o.id = '.$store_id.')' : ' AND 0'
				)
			)
		).(
			$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
		).(
			$staff ? 'AND iss.staff_id = '.$staff.' ' : ''
		).(
			$pickedup ? 'AND iss.status_id = 2 AND iss.warranty = 0 AND i.conducted = 1 ' : ''
		).(
			$instore ? 'AND iss.customer_id = 0 ' : ''
		).(
			$all ? '' : ($pickedup ? '' : 'AND iss.status_id NOT IN(2,22) ')
		).(
			(!$pickedup AND isset($_REQUEST['payment'])) ? ($payment == 'paid' ? 'AND i.conducted = 1 ' : 'AND (i.conducted = 0 OR i.id IS NULL)') : ''
		).(
			($cstatus AND !$pickedup AND !$all) ? 'AND iss.status_id = '.$cstatus : ''
		).(
			($date_start AND $date_finish) ? ' AND Date(iss.date) >= Date(\''.$date_start.'\') AND Date(iss.date) <= Date(\''.$date_finish.'\')' : ''
		).' ORDER BY iss.date DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
		
			foreach($sql as $row){
				$i++;
				$inv_paid = number_format((float)$row['paid'], 2, '.', '');
				$inv_total = number_format((float)$row['total']+(((float)$row['total']/100)*(float)$row['tax']), 2, '.', '');
				tpl_set('activity/issues/item', [
					'id' => $row['id'],
					'paid' => $row['conducted'] ? '<span style="color: green">PAID</span>' : (
						$inv_paid > 0 ? '$'.$inv_paid.'/'.'$'.$inv_total : '<span style="color: red">UNPAID</span>'
					),
					'user_id' => $row['user_id'],
					'user_name' => $row['user_name'],
					'user_lastname' => $row['user_lastname'],
					'user_image' => $row['user_image'],
					'status' => (!$pickedup AND $status) ? $row['selected_status'] : $row['inv_status_name'],
					'current_status' => $row['inv_status_name'],
					'object_image' => $row['object_image'],
					'object_id' => $row['object_id'],
					'object_name' => $row['object_name'],
					'type' => $row['inv_type_name'],
					'date' => convert_date($row['date'], true),
					'total' => number_format($row['total'], 2, '.', ''),
					'currency' => $config['currency'][$row['currency']]['symbol'],
					'staff_id' => $row['staff_id'],
					'staff_name' => $row['staff_name'],
					'staff_lastname' => $row['staff_lastname'],
					'location' => $row['inv_location_name'].' '.$row['inv_location_count'],
					'class' => (strtolower($row['inv_status_id']) == 2 ? 'finished' : (
						$row['important'] ? 'important' : ((strtolower($row['inv_status_id']) != 11 ? 'some' : '')
					))),
					'style' => ($i % 2 == 0 ? ($row['back_even'] ? ' style="background: #'.$row['back_even'].'"' : '') : ($row['back'] ? ' style="background: #'.$row['back'].'"' : ''))
				], [
					'object_image' => $row['object_image'],
					'user_image' => $row['user_image'],
					'user' => $row['user_id']
				], 'issues');
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = 'All jobs';
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['issues'],
			]));
		}
		$statuses = '<option value="0">'.$lang['notSelected'].'</option>';
		foreach (db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_status`', true) as $st) {
			$statuses .= '<option value="'.$st['id'].'">'.$st['name'].'</option>';
		}
		
		$stores = '<option value="0">Not selected</option>';
		foreach (db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects`', true) as $st) {
			$stores .= '<option value="'.$st['id'].'">'.$st['name'].'</option>';
		}
		
		tpl_set('activity/issues/main', [
			'uid' => $user['id'],
			'query' => $query,
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'issues' => $tpl_content['issues'],
			'statuses' => $statuses,
			'stores' => $stores
		], [
			'archive' => $user['archive_job_view'],
			'filters' => true
		], 'content');
	break;
	
	case 'test':
		$query = text_filter($_REQUEST['query'], 255, false);
		$object = intval($_REQUEST['object']);
		$date_start = text_filter(($_REQUEST['date_start'] ?: $_REQUEST['sDate']), 30, true);
		$date_finish = text_filter(($_REQUEST['date_finish'] ?: $_REQUEST['eDate']), 30, true);
		$payment = text_filter($_REQUEST['payment'], 30, true) == 'paid' ?: 'unpaid';
		$page = intval($_REQUEST['page']);
		$staff = intval($_REQUEST['staff']);
		$status = intval($_REQUEST['status']);
		$cstatus = intval($_REQUEST['current_status']);
		$instore = intval($_REQUEST['instore']);
		$pickedup = intval($_REQUEST['pickedup']);
		$all = intval($_REQUEST['all']);
		$count = 20;
		$tbl = $status ? 'iss' : 'cl';
		$store_issues = ($route[2] == 'store' ? 1 : 0);
		
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
			iss.*,
			DATE(iss.date) as date,
			inv.id as inv_id,  
			inv.object_id as object_id,  
			inv.type_id as inv_type_id, 
			iss.status_id as inv_status_id, 
			inv.location_id as inv_location_id,
			inv.location_count as inv_location_count,
			o.id as object_id,
			o.name as object_name,
			o.image as object_image,
			c.name as inv_category_name, 
			s.name as inv_status_name, 
			s.back,
			s.back_even,
			t.name as inv_type_name,  
			l.name as inv_location_name,
			u.id as user_id,
			u.name as user_name,
			u.lastname as user_lastname,
			u.reg_date,
			u.image as user_image,
			m.id as staff_id,
			m.name as staff_name,
			m.lastname as staff_lastname,
			st.name as selected_status,
			i.conducted,
			i.paid,
			i.total,
			i.tax
		FROM `'.DB_PREFIX.'_issues_changelog` cl
		LEFT JOIN `'.DB_PREFIX.'_issues` iss
			ON iss.id = cl.issue_id
		LEFT JOIN `'.DB_PREFIX.'_inventory` inv
			ON inv.id = iss.inventory_id
		LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
			ON inv.category_id = c.id
		LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
			ON inv.type_id = t.id
		LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
			ON s.id = iss.status_id
		LEFT JOIN `'.DB_PREFIX.'_inventory_status` st
			ON '.($status ?: 0).' = st.id
		LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
			ON inv.location_id = l.id 
		LEFT JOIN `'.DB_PREFIX.'_users` m
			ON m.id = iss.staff_id
		LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = IF (iss.object_owner !=0, iss.object_owner, inv.object_id)
		LEFT JOIN `'.DB_PREFIX.'_users` u
			ON u.id = iss.customer_id
		LEFT JOIN `'.DB_PREFIX.'_invoices` i
			ON i.issue_id = iss.id
		WHERE 1 '.($all ? '' : 'AND IF(iss.warranty, 0, 1) ').(
			$store_issues ? ' AND iss.customer_id = 0 ' : ''
		).(
			$object ? ' AND iss.object_owner = \''.$object.'\'' : ''
		).(
			$user['issues_show_all'] ? '' : (
				$user['issues_show_anywhere'] ? ' AND (iss.staff_id = '.$user['id'].(
					$store_id ? ' OR o.id = '.$store_id : ''
				).')' : (
					$store_id ? ' AND (iss.staff_id = '.$user['id'].' OR o.id = '.$store_id.')' : ' AND 0'
				)
			)
			//(!in_array(2, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
				//' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).' OR u.id = '.$user['id'].'))' : ''
		).(
			$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
		).(
			$staff ? 'AND iss.staff_id = '.$staff.' ' : ''
		).(
			$pickedup ? 'AND iss.status_id = 2 AND iss.warranty = 0 AND invoice.conducted = 1 ' : ''
		).(
			$instore ? 'AND iss.customer_id = 0 ' : ''
		).(
			$all ? '' : ($pickedup ? '' : 'AND iss.status_id NOT IN(2,22) ')
		).(
			(!$pickedup AND isset($_REQUEST['payment'])) ? ($payment == 'paid' ? 'AND invoice.conducted = 1 ' : 'AND (invoice.conducted = 0 OR invoice.id IS NULL)') : ''
		).(
			($cstatus AND !$pickedup AND !$all) ? 'AND iss.status_id = '.$cstatus : ($status ? ($status == 11 ? 'AND cl.changes = \'New issue\'' : 'AND cl.changes_id = '.$status. ' AND cl.changes = \'status\' ') : '')
		).(
			($date_start AND $date_finish) ? ' AND cl.date >= \''.$date_start.'\' AND cl.date <= \''.$date_finish.'\'' : ''
		).' GROUP BY cl.issue_id DESC, iss.date ASC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			
/* ORDER BY CASE iss.status_id
				 WHEN 11 THEN 1
				 WHEN 1 THEN 2
				 ELSE 3
				 END ASC iss.id DESC*/
			foreach($sql as $row){
				$i++;
				tpl_set('activity/issues/item', [
					'id' => $row['id'],
					'paid' => (float)$row['paid'],
					'total' => (float)$row['total']+(((float)$row['total']/100)*$row['tax']),
					'user_id' => $row['user_id'],
					'user_name' => $row['user_name'],
					'user_lastname' => $row['user_lastname'],
					'user_image' => $row['user_image'],
					'status' => (!$pickedup AND $status) ? $row['selected_status'] : $row['inv_status_name'],
					'current_status' => $row['inv_status_name'],
					'object_image' => $row['object_image'],
					'object_id' => $row['object_id'],
					'object_name' => $row['object_name'],
					'type' => $row['inv_type_name'],
					'date' => $row['date'],
					'total' => number_format($row['total'], 2, '.', ''),
					'currency' => $config['currency'][$row['currency']]['symbol'],
					'staff_id' => $row['staff_id'],
					'staff_name' => $row['staff_name'],
					'staff_lastname' => $row['staff_lastname'],
					'location' => $row['inv_location_name'].' '.$row['inv_location_count'],
					'class' => (strtolower($row['inv_status_id']) == 2 ? 'finished' : (
						$row['important'] ? 'important' : ((strtolower($row['inv_status_id']) != 11 ? 'some' : '')
					))),
					'style' => ($i % 2 == 0 ? ($row['back_even'] ? ' style="background: #'.$row['back_even'].'"' : '') : ($row['back'] ? ' style="background: #'.$row['back'].'"' : ''))
				], [
					'object_image' => $row['object_image'],
					'user_image' => $row['user_image'],
					'user' => $row['user_id']
				], 'issues');
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = $lang['All'].' '.$lang['Issues'];
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['issues'],
			]));
		}
		$statuses = '<option value="0">'.$lang['notSelected'].'</option>';
		foreach (db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_status`', true) as $st) {
			$statuses .= '<option value="'.$st['id'].'">'.$st['name'].'</option>';
		}
		
		$stores = '<option value="0">Not selected</option>';
		foreach (db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects`', true) as $st) {
			$stores .= '<option value="'.$st['id'].'">'.$st['name'].'</option>';
		}
		
		tpl_set('activity/issues/main', [
			'uid' => $user['id'],
			'query' => $query,
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'issues' => $tpl_content['issues'],
			'statuses' => $statuses,
			'stores' => $stores
		], [
			'archive' => $user['archive_job_view'],
			'filters' => true
		], 'content');
	break;
}
?>