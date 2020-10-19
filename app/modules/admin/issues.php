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
function getTypes($a, $b = []){
	$r = '';
	if(!is_array($a)){
		$s = db_multi_query('SELECT options FROM `'.DB_PREFIX.'_inventory_types` WHERE id = '.$a);
		$a = json_decode($s['options'], true);
	}
	foreach($a as $h => $i){
		$n = create_name($i['name']);
		$t = '';
		if($i['type'] == 'select'){
			$o = '';
			foreach($i['sOpts'] as $k => $v){
				if(is_array($b[$h])){
					$l = in_array($k, $b[$h]);
				} else {
					$l = ($k == $b[$h]);
				}
				$o .= '<option value="'.$k.'"'.(
					$l ? ' selected' : ''
				).'>'.$v.'</option>';
			}
			$t .= '<select name="'.$n.'" data-id="'.$h.'" '.(
				$i['mult'] ? ' multiple' : ''
			).($i['req'] ? ' required' : '').'>'.$o.'</select>';
		} else if($i['type'] == 'textarea'){
			$t .= '<textarea name="'.$n.'" data-id="'.$h.'">'.$b[$h].'</textarea>';
		} else {
			$t .= '<input name="'.$n.'" data-id="'.$h.'" type="'.(
				$i['type'] == 'input' ? 'text' : $i['type']
			).'"'.(($i['req'] AND $i['type'] != 'checkbox')  ? ' required' : '').(
				$b[$h] ? (
					$i['type'] == 'checkbox' ? ' checked' : ' value="'.$b[$h].'"'
				) : ''
			).'>';
		}
		$r .= '<div class="iGroup">
			<label>'.$i['name'].'</label>'.$t.'
		</div>';
	}
	return $r;
}

if($gid = intval($_POST['type_id'])){
	echo json_encode(getTypes($gid), JSON_UNESCAPED_UNICODE);
	die;
}

switch($route[1]){
	
	case 'report_result':
		is_ajax() or die('Hacking attempt!');

		if (in_to_array('1,2', $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])){
			
			$store_ids = $user['manager_report'] ? $user['store_id'] : ids_filter($_REQUEST['store_ids']);
			$date_start = $_REQUEST['date_start'] ? text_filter($_REQUEST['date_start'], 30, true) : date('Y-m-d', time());
			$date_finish = $_REQUEST['date_finish'] ? text_filter($_REQUEST['date_finish'], 30, true) : date('Y-m-d', time());
			
			$sql = '';
			
			$objects = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects`'.($store_ids ? ' WHERE id IN('.$store_ids.')' : '').'', true, false, function($a){
				return [$a['id'], $a['name']];
			});
			
			if(!$store_ids){
				$store_ids = implode(',', array_keys($objects));
			}
			
			$statuses = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_status`', true, false, function($a) use(&$sql, $store_ids){
				foreach(explode(',', $store_ids) as $store_id) $sql .= (
					$sql ? ', ' : ''
				).'
				COUNT(IF(iss.status_id = '.$a['id'].' AND iss.object_owner = '.$store_id.', 1, NULL)) as `'.$store_id.'-'.$a['id'].'-status`,
				COUNT(IF(inv.paid = 0 AND iss.object_owner = '.$store_id.' AND iss.status_id = '.$a['id'].', 1, NULL)) as `'.$store_id.'-'.$a['id'].'-not_paid`,
				COUNT(IF(inv.conducted = 0 AND inv.paid > 0 AND iss.object_owner = '.$store_id.' AND iss.status_id = '.$a['id'].', 1, NULL)) as `'.$store_id.'-'.$a['id'].'-part_paid`,
				COUNT(IF(inv.conducted > 0 AND iss.object_owner = '.$store_id.' AND iss.status_id = '.$a['id'].', 1, NULL)) as `'.$store_id.'-'.$a['id'].'-full_paid`';
				return [$a['id'], $a['name']];
			});
			
			$statuses[0] = 'Total';
			
			$stores = [];
			
			$report_sql = db_multi_query('SELECT '.$sql.', COUNT(iss.id) as `Total` FROM `'.DB_PREFIX.'_issues` iss LEFT JOIN `'.DB_PREFIX.'_invoices` inv ON inv.issue_id = iss.id WHERE iss.date >= \''.$date_start.'\' AND iss.date <= \''.$date_finish.'\''.(
				$store_ids ? ' AND iss.object_owner IN ('.$store_ids.')' : ''
			));
			
			$rsql = [];
			
			foreach($report_sql as $k => $v){
				$exp = explode('-', $k);
				if($exp[0] == 'Total') continue;
				$rsql[$exp[0].'-'.$exp[1]][$exp[2]] += $v;
				$stores[$k[0]]['total-'.$exp[2]] += $v;
			}
			
			//print_r($rsql);
			//print_r($stores);
			//die;
			
			
			$total = $report_sql['Total'];
			// 15/65*100
/* 			foreach($report_sql as $k => $v){
				if($k == 'Total') continue;
				$k = explode('-', $k);
				if($k[1] == '0'){
					$stores[$k[0]]['total'] = $v;
					continue;
				}
			} */
			foreach($rsql as $k => $v){
				$k = explode('-', $k);
				if($k[1] == 0) continue;
				
				$stores[$k[0]]['id'] = $k[0];
				$stores[$k[0]]['content'] .= '<tr onclick="window.open(\'/activity/issues_report?object='.$k[0].'&date_start='.urlencode($date_start).'&date_finish='.urlencode($date_finish).'&current_status='.$k[1].'\');">
					<td data-label="Status:"><b>'.$statuses[$k[1]].'</b></td>
					<td data-label="Quantity:"><b>'.$v['status'].'</b></td>
					<td data-label="%:"><b>'.(
						$stores[$k[0]]['total-status'] > 0 ? round($v['status']/$stores[$k[0]]['total-status']*100) : 0
					).'%</b></td>
					<td data-label="Not paid:"><b>'.$v['not_paid'].'</b></td>
					<td data-label="Part paid:"><b>'.$v['part_paid'].'</b></td>
					<td data-label="Full paid:"><b>'.$v['full_paid'].'</b></td>
				</tr>';
			}
			
			$report = '<div class="repObj">';
			
			foreach($stores as $store){
				$report .= '
					<div class="roName">'.$objects[$store['id']].'</div>
					<table class="responsive">
					<thead>
						<tr>
							<th>Status</th>
							<th>Quantity</th>
							<th>%</th>
							<th>Not paid</th>
							<th>Part paid</th>
							<th>Full paid</th>
						</tr>
					</thead>
					'.$store['content'].'</table>
					<table>
						<thead>
							<tr>
								<td><b>TOTAL JOBS</b></td>
								<td>'.$store['total-status'].'</td>
							</tr>
							<tr>
								<td><b>TOTAL NOT PAID</b></td>
								<td>'.$store['total-not_paid'].'</td>
							</tr>
							<tr>
								<td><b>TOTAL PART PAID</b></td>
								<td>'.$store['total-part_paid'].'</td>
							</tr>
							<tr>
								<td><b>TOTAL FULL PAID</b></td>
								<td>'.$store['total-full_paid'].'</td>
							</tr>
						</thead>
					</table>
				';
			}
			
			$report .= '</div>';
			
			if(isset($_GET['debug']))
				echo db_debug();
			else {
				print_r(json_encode([
					'report' => $report,
					'total' => $total
				]));
			}
		}
		die;
	break;
	
	case 'send_quantity':
		is_ajax() or die('Hacking attempt!');
		$id = text_filter($_POST['id'], 20, false);
		$issue_id = intval($_POST['issue_id']);
		$quantity = intval($_POST['quantity']);
		
		if ($quantity > 0 AND $id AND $issue_id) {
			$issue = db_multi_query('SELECT service_info FROM `'.DB_PREFIX.'_issues` WHERE id = '.$issue_id);
			if ($issue['service_info'] AND $issue['service_info'] != '{}') {
				if ($services = json_decode($issue['service_info'], true)) {
					foreach($services as $k => $v) {
						if ($k == $id)
							$services[$k]['quantity'] = $quantity;
					}
					db_query('UPDATE `'.DB_PREFIX.'_issues` SET service_info = \''.json_encode($services, JSON_UNESCAPED_UNICODE).'\' WHERE id = '.$issue_id);
					echo 'OK';
				}
			}
		} else
			echo 'err';
		die;
	break;
	
	case 'confirm_transfer':
		is_ajax() or die('Hacking attempt!');
		if(($issue_id = (int)$_POST['id']) AND (
			$transfer = db_multi_query('
				SELECT * FROM `'.DB_PREFIX.'_issues_transfer`
				WHERE confirm_id = 0 AND issue_id = '.$issue_id
			)
		)){
			db_query('UPDATE `'.DB_PREFIX.'_issues_transfer` SET
				confirm_id = '.$user['id'].',
				confirm_date = \''.date('Y-m-d').'\'
				WHERE issue_id = '.$issue_id.' AND confirm_id = 0
			');
			
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE id = '.$issue_id.' AND type = \'issue_transfer\'');
			
			db_query('
				UPDATE `'.DB_PREFIX.'_issues` tb1 LEFT JOIN `'.DB_PREFIX.'_inventory` tb2 ON tb1.inventory_id = tb2.id SET
				tb1.staff_id = '.$user['id'].',
				tb1.object_owner = '.$transfer['store_id'].',
				tb2.object_id = '.$transfer['store_id'].',
				tb2.location_id = '.$transfer['location_id'].',
				tb2.location_count = '.$transfer['location_count'].'
				WHERE tb1.id = '.$issue_id
			);
			echo 'OK';
		} else
			echo 'ERR';
		die;
	break;
	
	case 'transfer':
		is_ajax() or die('Hacking attempt!');
		$issue_id = (int)$_POST['issue_id'];
		$staff_id = (int)$_POST['staff_id'];
		$store_id = (int)$_POST['store_id'];
		$location_id = (int)$_POST['location_id'];
		$location_count = (int)$_POST['location_count'];
		if($issue_id AND $store_id){
			
			if(!db_multi_query('
				SELECT * FROM `'.DB_PREFIX.'_issues_transfer`
				WHERE confirm_id = 0 AND issue_id = '.$issue_id
			)){
				db_query('INSERT INTO `'.DB_PREFIX.'_issues_transfer` SET
					create_id = '.$user['id'].',
					create_date = \''.date('Y-m-d').'\',
					issue_id = '.$issue_id.',
					staff_id = '.$staff_id.',
					store_id = '.$store_id.',
					location_id = '.$location_id.',
					location_count = '.$location_count
				);
				
				db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET
					id = '.$issue_id.',
					staff = '.$staff_id.',
					store_id = '.$store_id.',
					type = \'issue_transfer\''
				);	
				echo 'OK';
			} else
				echo 'PENDING';
		} else
			echo 'ERR';
		die;
	break;
	
	/*
	* Confirm store issue
	*/
	case 'confirm_store_issue':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		$issue = db_multi_query('SELECT intake_id FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id);
		if ($issue['intake_id'])
			die('confirmed');
		
		db_query('UPDATE `'.DB_PREFIX.'_issues` SET intake_id = '.$user['id'].', staff_id = '.$user['id'].' WHERE id = '.$id);
		
		db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'e_issues\'');
		die('OK');
	break;
	
	/* 
	* Confirm assigned
	*/
	case 'confirm_assigned':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if ($notif = db_multi_query('
			SELECT * 
			FROM `'.DB_PREFIX.'_notifications` 
			WHERE staff = '.$user['id'].' AND id = '.$id.' AND type = \'issue_assigned\'')
		) {
			if (intval($_POST['type'])) {
				db_query('UPDATE `'.DB_PREFIX.'_issues` SET staff_id = '.$user['id'].' WHERE id = '.$id);
				db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
					issue_id = '.$id.',
					user = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					changes = \'staff\',
					changes_id = \''.$user['id'].'\''
				);
			}
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE id = '.$id.' AND type = \'issue_assigned\'');
			die('OK');
		} else 
			die('not_staff');
	break;
	
	/*
	* Send assigned
	*/
	case 'send_assigned':
		is_ajax() or die('Hacking attempt!');
			$staff = intval($_POST['staff']);
			$id = intval($_POST['id']);
			
			if ($staff) {
				if ($notif = db_multi_query('SELECT * 
					FROM `'.DB_PREFIX.'_notifications` 
					WHERE id = '.$id.' AND type = \'issue_assigned\'')) {
						db_query('UPDATE `'.DB_PREFIX.'_notifications` SET 
							id = '.$id.', 
							type = \'issue_assigned\',
							staff = '.$staff.'
						');
					} else {
						db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET 
							id = '.$id.', 
							type = \'issue_assigned\',
							staff = '.$staff.'
						');
					}
				
				send_push($staff, [
					'type' => 'purchase',
					'id' => '/issues/view/'.$id,
					'name' => $user['uname'],
					'lastname' => $user['ulastname'],
					'message' => 'You assigned to issue #'.$id.'. Please, confirm'
				]);
				echo 'OK';
			}
		die;
	break;
	
	/*
	* Is issue in warranty
	*/
	case 'is_warranty':
		is_ajax() or die('Hacking attempt!');
		$info = db_multi_query('
			SELECT 
				warranty
			FROM `'.DB_PREFIX.'_issues` 
			WHERE id = '.intval($_POST['id'])
		);
		echo ($info['warranty'] == 2 ? 1 : 0);
		die;
	break;
	
	/*
	* Warranty confirm
	*/
	case 'confirm_warranty':
		is_ajax() or die('Hacking attempt!');
		if ($d = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_issues` WHERE warranty = 2 AND id = '.intval($_POST['issue'])))
			die('OK');
	
		if ($user['confirm_warranty']) {
			db_query('
				UPDATE `'.DB_PREFIX.'_inventory` SET 
					warranty = 2,
					warranty_status = 1
				WHERE id = '.intval($_POST['device'])
			);
			db_query('
				UPDATE `'.DB_PREFIX.'_issues` SET 
					warranty = 2,
					warranty_status = 1
				WHERE id = '.intval($_POST['issue'])
			);
			$user_sub = db_multi_query('
				SELECT 
					sh.staff_id, 
					sh.point,
					sh.object_id,
					s.forfeit,
					s.point_group
				FROM `'.DB_PREFIX.'_inventory_status_history` sh,
				`'.DB_PREFIX.'_inventory_warranty_status` s
				WHERE sh.issue_id = '.intval($_POST['issue']).' AND sh.status_id = 2 AND s.id = 1');
				
			$points = ($user_sub['forfeit'] ? ($user_sub['point']*$user_sub['forfeit']/100*(-1)) : json_decode($user_sub['point_group'])[0]);
			db_query('
				INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET 
					staff_id = '.$user_sub['staff_id'].',
					point = '.$points.',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					issue_id = '.intval($_POST['issue']).',
					status_id = 1,
					warranty = 1,
					action = \'update_status\',
					object_id = '.intval($_POST['object_id']).',
					inventory_id = '.intval($_POST['device'])
			);
			db_query(
				'UPDATE `'.DB_PREFIX.'_users`
					SET points = points+'.$points.'
				WHERE id = '.$user_sub['staff_id']
			);
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'un_warranty\'');
			die('OK');
		} else 
			die('no_acc');
	break;
	
	/*
	* Warranty request
	*/
	case 'request_warranty':
		is_ajax() or die('Hacking attempt!');
		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET 
				warranty = '.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '2' : '1').', 
				'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 'warranty_status = 1,' : '').'
				warranty_date = \''.date('Y-m-d H:i:s', time()).'\', 
				warranty_issue = \''.intval($_POST['issue']).'\' 
			WHERE id = '.intval($_POST['device'])
		);
		
		db_query('
			UPDATE `'.DB_PREFIX.'_issues` SET 
				warranty = '.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '2' : '1').', 
				'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 'warranty_status = 1,' : '').'
				warranty_reason = \''.text_filter($_POST['comment'], 1000, false).'\'
			WHERE id = '.intval($_POST['issue'])
		);
		
		db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'un_warranty\'');
		
		die('OK');
	break;
	
	/*
	* Send total
	*/
	case 'send_total':
		is_ajax() or die('Hacking attempt!');
		db_query('
			UPDATE `'.DB_PREFIX.'_issues` SET 
				total = \''.floatval($_POST['total']).'\' 
			WHERE id = '.intval($_POST['id'])
		);
		die('OK');
	break;

	/*
	* Update ratting feedback
	*/
	case 'feedbacks_update_ratting':
		is_ajax() or die('Hacking attempt!');
		if($user['edit_fb_ratting']){
			$ratting = (int)$_POST['ratting'];
			$id = (int)$_POST['id'];
			if($ratting == 5 && (($fb = db_multi_query('SELECT issue_id, staff_id FROM `'.DB_PREFIX.'_feedback` WHERE id = '.$id) && !intval($_POST['random'])) OR intval($_POST['random']))){
				$points = floatval($config['user_points']['feedback']['points']);
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.(intval($_POST['random']) ? $user['id'] : intval($fb['staff_id'])).',
					issue_id = '.intval($fb['issue_id']).',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					action = \'feedback\',
					point = \''.$points.'\''
				);	
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.(intval($_POST['random']) ? $user['id'] : intval($fb['staff_id']))
				);
			}
			
			if((int)$_POST['random']){
				db_query('
					INSERT INTO `'.DB_PREFIX.'_feedback_random` 
						(id,staff_id,ratting)
						VALUES ('.$id.', '.$user['id'].', \''.$ratting.'\')
					ON DUPLICATE KEY
						UPDATE id='.$id.', staff_id='.$user['id'].', ratting='.$ratting.'
				');
			} else {
				db_query('
					UPDATE `'.DB_PREFIX.'_feedback` SET 
						ratting = \''.$ratting.'\' 
					WHERE id = '.$id
				);
			}
			die('OK');
		} else
			die('403');
	break;
	
	/*
	* All feedbacks
	*/
	case 'feedbacks':
		if ($route[2] == 'random') {
			$meta['title'] = 'Random feedbacks';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			
			if($sql = db_multi_query('
				SELECT DISTINCT SQL_CALC_FOUND_ROWS
					f.*,
					u.id, 
					u.name as cname, 
					u.lastname as clastname,
					u.phone,
					u.image as cava 
				FROM `'.DB_PREFIX.'_users` u 
				LEFT JOIN `'.DB_PREFIX.'_feedback_random` f
					ON f.id = u.id
				LEFT JOIN `'.DB_PREFIX.'_issues` i
					ON i.customer_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_invoices` inv
					ON inv.customer_id = u.id
				WHERE i.id IS NULL AND inv.id IS NULL AND f.id IS NULL '.(
					$query ? 'AND CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' ' : ''
				).'ORDER BY u.id DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					$star = '';
					for($j = 1; $j <= 5; $j++){
						$star .= '<span class="fa fa-star rStart mini r_'.$row['ratting'].(
							$j <= $row['ratting'] ? ' active' : ''
						).'" ratting="'.$j.'"></span>';
					}
						
					tpl_set('issues/feedback/random_item', [
						'id' => $row['id'],
						'customer_name' => $row['cname'].' '.$row['clastname'],
						'phone' => $row['phone'],
						'cava' => $row['cava'],
						'comment' => $row['comment'],
						'ratting' => $row['ratting'],
						'star' => $star 
					], [
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
			tpl_set('issues/feedback/random_main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'feedback' => $tpl_content['feedback']
			], [
			], 'content');
		} else {
			$meta['title'] = 'Feedbacks';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$staff = intval($_POST['staff'] ? $_POST['staff'] : $_GET['staff']);
			$rating = intval($_REQUEST['rating']);
			$type = intval($_POST['type']);
			$create = intval($_POST['create']);
			$date_start = text_filter($_REQUEST['date_start'], 30, true);
			$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
			$object = intval($_REQUEST['object']);
			//print_r($staff);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					f.*,
					DATE(f.date) as date,
					TIME(f.date) as time,
					u.name, 
					u.lastname,
					u.image as ava,
					c.name as cname, 
					c.lastname as clastname,
					c.phone,
					c.image as cava,
					ss.name as ss_name, 
					ss.lastname as ss_lastname,
					IF(cm.name != \'\', cm.name, cm.email) as chat_name,
					cm.image as cava
				FROM `'.DB_PREFIX.'_feedback` f 
				LEFT JOIN `'.DB_PREFIX.'_users` u 
					ON u.id = f.staff_id 
				LEFT JOIN `'.DB_PREFIX.'_issues` i
					ON i.id = f.issue_id 
				LEFT JOIN `'.DB_PREFIX.'_users` c 
					ON c.id = f.customer_id 
				LEFT JOIN `'.DB_PREFIX.'_users` ss 
					ON ss.id = f.send_staff_id
				LEFT JOIN `'.DB_PREFIX.'_chat_im` cm 
					ON cm.id = f.customer_id 
				WHERE f.date > CURRENT_DATE - INTERVAL 6 MONTH '.(
					$query ? 'AND (CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' OR CONCAT(c.name, \' \', c.lastname) LIKE \'%'.$query.'%\' OR f.issue_id = \''.$query.'\') ' : ''
				).(
					$staff ? 'AND f.staff_id = '.$staff.' ' : ''
				).(
					$create ? 'AND f.send_staff_id = '.$create.' ' : ''
				).(
					$type > 0 ? 'AND f.type = '.$type.' ' : ''
				).(
					$rating ? 'AND f.ratting = '.$rating.' ' : ''
				).(
					$object ? 'AND i.object_owner = '.$object.' ' : ''
				).(
					($date_start AND $date_finish) ? ' AND f.date >= CAST(\''.$date_start.'\' AS DATE) AND f.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).'ORDER BY f.type DESC, f.ratting ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					$star = '';
					for($j = 1; $j <= 5; $j++){
						$star .= '<span class="fa fa-star rStart mini r_'.$row['ratting'].(
							$j <= $row['ratting'] ? ' active' : ''
						).'" ratting="'.$j.'"></span>';
					}
						
					tpl_set('issues/feedback/item', [
						'id' => $row['id'],
						'staff_id' => $row['staff_id'],
						'staff_name' => $row['name'].' '.$row['lastname'],
						'send_staff_id' => $row['send_staff_id'],
						'send_staff_name' => $row['ss_name'].' '.$row['ss_lastname'],
						'customer_id' => $row['customer_id'],
						'customer_name' => $row['type'] == 4 ? $row['chat_name'] : $row['cname'].' '.$row['clastname'],
						'phone' => $row['type'] != 4 ? $row['phone'] : '',
						'issue' => $row['issue_id'],
						'ava' => $row['ava'],
						'cava' => $row['cava'],
						'date' => $row['date'],
						'time' => $row['time'],
						'comment' => $row['comment'],
						'ratting' => $row['ratting'],
						'star' => $star
					], [
						'ava' => $row['ava'],
						'cava' => $row['cava'],
						'sms' => $row['type'] == 1,
						'email' => $row['type'] == 2,
						'tablet' => $row['type'] == 3,
						'chat' => $row['type'] == 4,
						'custom' => $row['type'] == 0,
						'staff' => $row['staff_id'] == $user['id']
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
		}
	break;
	
	/*
	* Send invisible
	*/
	case 'send_invisible':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if ($id) {
			db_query('UPDATE `'.DB_PREFIX.'_issues` SET
					upcharge_info = \''.$_POST['service'].'\'
					WHERE id = '.$id
			);
			echo 'OK';
		}
		die;
	break;
	
	/*
	* Send Random Feedback
	*/
	case 'send_random_feedback':
	
		is_ajax() or die('Hacking attempt!');
		
		$id = (int)$_POST['id'];
		$ratting = (int)$_POST['ratting'];
		$comment = text_filter($_POST['comment'], 1600, false);
		
		if($ratting > 0 AND $ratting < 6 AND $id){
			
			if($_POST['type'] == 'job' && (
				$job = db_multi_query('SELECT staff_id, customer_id, object_owner FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id)
			)){
				db_query('INSERT INTO `'.DB_PREFIX.'_feedback` SET
					issue_id = '.$id.',
					date = Now(),
					staff_id = \''.$job['staff_id'].'\',
					send_staff_id = \''.$user['id'].'\',
					customer_id = \''.$job['customer_id'].'\',
					comment = \''.$comment.'\',
					ratting = '.$ratting
				);
				
				if ($job['object_owner'] > 0 AND $ratting == 5){
					$points = floatval($config['user_points']['feedback']['points']);
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$job['staff_id'].',
						action = \'feedback\',
						date = Now(),
						object_id = '.$job['object_owner'].',
						point = \''.$points.'\''
					);
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$points.'
						WHERE id = '.$job['staff_id']
					);
				}
				
				db_query('UPDATE `'.DB_PREFIX.'_issues` SET fb_ratting = '.$ratting.' WHERE id = '.$id);
				
			} else {
				
				db_query('
					INSERT INTO `'.DB_PREFIX.'_feedback_random` 
						(id,ratting,comment)
						VALUES ('.$id.', \''.$ratting.'\', \''.$comment.'\')
					ON DUPLICATE KEY
						UPDATE id='.$id.', ratting='.$ratting.', comment=\''.$comment.'\'
				');
				
				if ($user['store_id'] > 0 AND $ratting == 5) {
					$points = floatval($config['user_points']['feedback']['points']);
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'feedback\',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						object_id = '.$user['store_id'].',
						point = \''.$points.'\''
					);
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$points.'
						WHERE id = '.$user['id']
					);
				}
			}
			
			echo 'OK';
			
		} else
			echo 'ERR';
		die;
	break;
	
	/*
	* Send Feedback
	*/
	case 'send_feedback':
	
		is_ajax() or die('Hacking attempt!');
		
		$id = (int)$_POST['id'];
		$issue_id = (int)$_POST['issue_id'];
		$ratting = (int)$_POST['ratting'];
		if($ratting > 0 AND $ratting < 6 AND $issue_id){
			$uid = db_multi_query('
				SELECT 
					i.customer_id,
					iss.staff_id,
					iss.object_owner
				FROM `'.DB_PREFIX.'_issues` iss
				LEFT JOIN `'.DB_PREFIX.'_inventory` i
					ON i.id = iss.inventory_id
				WHERE iss.id = '.$issue_id
			);
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_feedback` SET
					issue_id = '.$issue_id.',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					staff_id = \''.$uid['staff_id'].'\',
					send_staff_id = \''.$user['id'].'\',
					customer_id = \''.$uid['customer_id'].'\',
					comment = \''.text_filter(
						$_POST['comment'], 1600, false
					).'\',
					ratting = '.$ratting.(
				$id ? ' WHERE id = '.$id : ''
			));
			
			$obj = ($uid['object_owner'] ?: $user['store_id']);
			
			if ($user['store_id'] > 0 AND $ratting == 5){
				/* $sql_ = db_multi_query('
					SELECT
						SUM(tb1.point) as sum,
						tb2.points
					FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
						 `'.DB_PREFIX.'_objects` tb2
					WHERE tb1.staff_id = '.$uid['staff_id'].' AND tb1.date >= DATE_SUB(
						NOW(), INTERVAL 1 HOUR
					) AND tb1.rate_point = 1 AND tb2.id = '.$obj
				); */
				
				$points = floatval($config['user_points']['feedback']['points']);
				
				//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$uid['staff_id'].',
						action = \'feedback\',
						object_id = '.$obj.',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						issue_id = '.$issue_id.',
						point = \''.$points.'\''
					);	//min_rate = '.$sql_['points'].',
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$points.'
						WHERE id = '.$user['id']
					);
				/* } else {
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$uid['staff_id'].',
						action = \'feedback\',
						min_rate = '.$sql_['points'].',
						point = \''.$points.'\',
						issue_id = '.$issue_id.',
						object_id = '.$obj.',
						rate_point = 1'
					);	
				} */
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
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['del_issue']){
			db_query('DELETE FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				db_query('
					INSERT INTO `'.DB_PREFIX.'_activity`
					SET user_id = \''.$user['id'].'\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					object_id = '.(int)$user['store_id'].',
					event_id = '.$id.',
					event = \'remove_job\''
				);
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
				LEFT JOIN `'.DB_PREFIX.'_inventory_status` tb3 ON tb1.status_id = tb3.id 
				LEFT JOIN `'.DB_PREFIX.'_objects_locations` tb4 ON tb2.location_id = tb4.id 
				LEFT JOIN `'.DB_PREFIX.'_objects` tb5 ON tb2.object_id = tb5.id 
				WHERE tb1.id = '.$id);
			
			if ($row['service_info'] AND strlen($row['service_info']) > 2) {
				$services = json_decode($row['service_info'], true);
				if (is_array($services)) {
					$services_count = 0;
					$service_ids_full = '';
					foreach($services as $k => $s) {
						if ($service_ids_full)
							$service_ids_full .= ',';
						$service_ids_full .= intval($k);
						if (floatval(str_replace('$', '', $s['price'])) > 0)
							$services_count ++;
					}
					
					$ids = '';

					foreach($services as $i => $service) {
						$price = floatval(trim(str_replace('$', '', $service['price'])));
						$service_price += $price;
						
						$services[$i] = [
							'name' => $service['name'],
							'price' => number_format($price, 2, '.', ""),
							'currency' => $service['currency'] ?: 'USD',
							'req' => $service['req']
						];

						if ($ids)
							$ids .= ',';
						$ids .= intval($i);
					}
				}
			}
			
			if ($row['inventory_info'] AND strlen($row['inventory_info']) > 2) {
				if (is_array(json_decode($row['inventory_info'], true))) {
					foreach(json_decode($row['inventory_info'], true) as $i => $inventory) {
						$price = floatval(trim(preg_replace('/[^0-9.]/i', '', $inventory['price'])));
						$inventories[$i] = [
							'name' => $inventory['name'],
							'price' => number_format($price, 2, '.', ""),
							'currency' => $service['currency'] ?: 'USD',
						];
					}
				}
			}
		} else if ($route[1] == 'add' AND $id) {
			$row = db_multi_query('SELECT 
				'.(
					$user['store_id'] > 0 ? 'IF(i.object_id != '.$user['store_id'].', '.$user['store_id'].', i.object_id)' : 'i.object_id'
				).' as object_id,
				o.name as object_name
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_objects` o 
					ON '.(
						$user['store_id'] > 0 ? 'IF(i.object_id != '.$user['store_id'].', '.$user['store_id'].', i.object_id)' : 'i.object_id'
					).' = o.id 
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
				if ($row['status_id'] > 0) {
					$status = json_encode([$row['status_id'] => [
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
		$new = intval($_POST['id']) ?: 0;
		$services = '';

        $end = time();
        $c_vals = '';
        $o_vals = '';
		$t = '{';
		$si = 0;
		$currency = '';
		if ($_POST['service']) {
			foreach(json_decode($_POST['service'], true) as $i => $val) {
				if (!$si)
					$currency = $val['currency'];
                $k = $i.((stripos($i, '_') === false) ? '_'.$end : '');
                $t .= '"'.$k.'":{"name":"'.json_escape($val['name']).
                    '","price":"'.preg_replace('/[^0-9.]/i', '', $val['price']).'","req":"'.intval($val['req']).'","currency":"'.$val['currency'].'"},';

                if (stripos($i, '_') === false) {
				    $c_vals .= '"'.json_escape($k).'":{"staff" : "0", "comment": ""},';
				    $o_vals .= '"'.json_escape($k).'":"",';
                }
			}
			$services = substr($t, 0, -1).'}';
		}
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.description as descr,
				i.inventory_ids as inventory,
				i.purchase_ids as purchases,
				i.service_ids,
				i.inventory_id,
				i.discount,
				i.status_id as status,
				i.discount_confirmed,
				i.object_owner,
				i.customer_id,
				i.staff_id as issue_staff,
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
						date = \''.date('Y-m-d H:i:s', time()).'\',
						changes = \''.$field.'\',
						changes_id = \''.ids_filter($_POST['service']).'\',
						object_id = '.$sql['object_id']
					);
				} else if($sql[$field] !== $value AND $field != 'object_id'){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						user = '.$user['id'].',
						object_id = '.$sql['object_id'].',
						changes = \''.$field.'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.$value.'\''
					));
				}
			}
		} else
			$sql = db_multi_query('SELECT customer_id, object_owner, status_id as status FROM `'.DB_PREFIX.'_inventory` WHERE id = '.intval($_POST['inventory_id']));
		
		$store_id = intval($user['store_id'] ?: $sql['object_owner']);
		
		// SQL SET
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_issues` SET
				staff_id = '.$user['id'].','.(
					$new ? '' : 'intake_id = '.$user['id'].','
				).(
					!$id ? 
						'customer_id = '.$sql['customer_id'].',
						 object_owner = '.$store_id.',
						 date = \''.date('Y-m-d H:i:s', time()).'\',
						 status_id = 11,' : ''
				).'description = \''.text_filter($_POST['descr'], 2000, false).'\',
				inventory_info = \''.($_POST['inventory'] ? $_POST['inventory'] : '').'\',
				purchase_info = \''.($_POST['purchases'] ? ids_filter($_POST['purchases']).',' : '').'\',
				service_info = \''.$services.'\',
				currency = \''.$currency.'\',
				inventory_id = '.intval($_POST['inventory_id']).(
			$id ? ' WHERE id = '.$id : ''
		));
		
		$id = $id ?: intval(mysqli_insert_id($db_link));
		
		if(!$_POST['id']){
			
			db_query('
				INSERT INTO `'.DB_PREFIX.'_activity`
				SET user_id = \''.$user['id'].'\',
				date = \''.date('Y-m-d H:i:s', time()).'\',
				object_id = '.$sql['object_owner'].',
				event_id = '.$id.',
				event = \'new_job\''
			);
			
			$oId = db_multi_query('SELECT object_id FROM `'.DB_PREFIX.'_inventory` WHERE id = '.intval($_POST['inventory_id']));
			db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
				issue_id = '.$id.',
				date = \''.date('Y-m-d H:i:s', time()).'\',
				user = '.$user['id'].',
				changes = \'New issue\',
				object_id = '.$oId['object_id']
			);
		}
		
		$status_id = intval($_POST['status']) ?: 11;
		
		if (!intval($_POST['id']) OR ($sql['status'] != $status_id)) {
					
			$issue_total = db_multi_query('
			SELECT 
				i.total,
				inv.id				
			FROM `'.DB_PREFIX.'_issues` i
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON inv.issue_id = i.id
			WHERE i.id = '.$id);
			
			$usr = db_multi_query('SELECT p.date as previous_date, u.id as uid, u.name, u.lastname, u.sms as sms_number, f.content, s.sms as sms, s.sms_form as sms_form, s.purchase, s.point_group, s.forfeit'.(intval($_POST['warranty']) ? '' : ', s.percent, s.assigned').' 
				FROM `'.DB_PREFIX.'_users` u,
					`'.DB_PREFIX.'_inventory_status` s
				LEFT JOIN `'.DB_PREFIX.'_forms` f
					ON f.id = s.sms_form 
				LEFT JOIN `'.DB_PREFIX.'_inventory_status_history` p
					ON p.status_id = '.intval($sql['status']).' AND p.inventory_id = '.intval($_POST['inventory_id']).'
				WHERE s.id = '.$status_id.' 
					AND u.id = '.$sql['customer_id']);
					

			// ---------------------------------------------------------------------------------------- //
			
			db_query(
				'UPDATE `'.DB_PREFIX.'_issues`
					SET 
					status_id = '.$status_id.(
						$usr['purchase'] ? ', purchase_done = 1' : ''
					).(
						$usr['assigned'] ? ', assigned = 1' : ''
					).(
						(in_array($status_id, [2, 6])) ? ', finished = 1 ' : ''
					).'
				WHERE id = '.$id
			);
					
			$point = 0;
			$sql_ = [];
			$rate_point = 1;
			
			
			// Is date
			if($usr['forfeit']){
				$point = -abs((int)$usr['forfeit']);
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$point.'
					WHERE id = '.$user['id']
				);
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'update_status\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					status_id = '.$status_id.',
					object_id = '.(int)$sql['object_id'].',
					issue_id = '.$id.',
					inventory_id = '.(int)$_POST['inventory_id'].',
					point = \''.$point.'\''
				);
			} else if($usr['previous_date'] AND $usr['point_group']){
				$time = ceil(
					(time()-strtotime($usr['previous_date']))/60
				);
				$points = (array)json_decode($usr['point_group'], true);
				ksort($points);
				foreach($points as $k3 => $v){
					if($k3 <= $time OR $k3 == 0){
						$point = $v ?: 0;
						if ((intval($_POST['id']) ? $issue_total['total'] >= floatval($config['issue_min_total']) : 1) AND !intval($usr['percent'])){
							db_query(
								'UPDATE `'.DB_PREFIX.'_users`
									SET points = points+'.($v ?: 0).'
								WHERE id = '.$user['id']
							);
							
							db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
								staff_id = '.$user['id'].','.(
									intval($_POST['warranty']) ? 'warranty = 1,' : ''
								).'
								action = \'update_status\',
								status_id = '.$status_id.',
								date = \''.date('Y-m-d H:i:s', time()).'\',
								object_id = '.(int)$sql['object_owner'].',
								issue_id = '.$id.',
								inventory_id = '.(int)$_POST['inventory_id'].',
								point = \''.($v ?: 0).'\''
							);
						} else if ((intval($_POST['id']) ? $issue_total['total'] >= floatval($config['issue_min_total']) : 1) AND intval($usr['percent'])) {
							db_query(
								'INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									status_id = \''.$status_id.'\','.(
										intval($_POST['warranty']) ? 'warranty = 1,' : ''
									).'
									staff_id = '.$user['id'].',
									object_id = '.(int)$sql['object_owner'].',
									issue_id = '.$id.',
									date = \''.date('Y-m-d H:i:s', time()).'\',
									action = \'update_status\',
									rate_point = 1,
									percent = 1,
									inventory_id = '.(int)$_POST['inventory_id'].(
										$usr['previous_date'] ? ', point = \''.$point.'\'' : ''
									)
							);
						}
						break;
					}
				}
			}	
			

			if (intval($_POST['id']) AND ($sql['issue_staff'] != $user['id'])) {
				if ($usr['assigned'] == 1) {
					$points = $config['user_points']['issue_forfeit']['points'];
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$sql['issue_staff'].',
						action = \'issue_forfeit\',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						object_id = '.$sql['object_owner'].',
						issue_id = '.$id.',
						point = \''.$points.'\''
					);
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.($points ?: 0).'
						WHERE id = '.$sql['issue_staff']
					);
					if ($points < 0) {
						if ($wt = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_timer` WHERE DATE(date) = \''.date('Y-m-d', time()).'\' AND user_id = '.$sql['issue_staff'])) {
							db_query('UPDATE `'.DB_PREFIX.'_timer` SET seconds = seconds + '.($points * $config['min_forfeit'] * 60).' WHERE id = '.$wt['id']);
							db_query('INSERT INTO `'.DB_PREFIX.'_users_time_forfeit` SET user_id = '.$sql['issue_staff'].', forfeit = '.(floatval($points) * $config['min_forfeit']*60));
						}
					}
				}
				db_query(
					'UPDATE `'.DB_PREFIX.'_issues`
						SET staff_id = '.$user['id'].'
					WHERE id = '.$id
				);
				db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
					issue_id = '.$id.',
					user = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					changes = \'staff\',
					changes_id = \''.$user['id'].'\''
				);
			}
			
			// ---------------------------------------------------------------------------------------- //
			
			if ($usr['sms'] == 1 AND $usr['sms_form'] > 0) {

				if (strlen($usr['sms_number']) >=10) {
					$smscontent = str_ireplace([
						'{name}',
						'{device}',
						'{store_name}',
						'{store_address}',
						'{store_cell}'
					], [
						$usr['name'].' '.$usr['lastname'],
						$sql['brand'].' '.$sql['model_name'].' '.$sql['model'],
						$user['object_name'],
						$user['object_address'],
						'',
					], $usr['content']);
					
					send_sms($usr['sms_number'], strip_tags($smscontent));
					
					/* $headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: kuptjukvm@gmail.com'."\r\n";
					$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
					
					mail('kuptjukvm@gmail.com', 'Welcome to the Your Company', $smscontent, $headers); */
				}
			}
		}

			// Insert change log
			/* db_query(
				'INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					status_id = '.((int)$_POST['status'] ?: 11).',
					staff_id = '.$user['id'].',
					object_id = '.$oId['object_id'].',
					issue_id = '.$id.',
					action = \''.($id ? 'update_status' : 'new_issue').'\',
					inventory_id = '.intval($_POST['inventory_id'])
			); */

		
		
		if ($_POST['service']) {
			if ($new) {
				db_query('
					UPDATE `'.DB_PREFIX.'_issues` SET
						options = IF(options != \'\', CONCAT(REGEXP_REPLACE(options, \'(.*?).\', \'\\\1\'), \','.$o_vals.'}\'), \'{'.$o_vals.'}\'),
						comments = IF(comments != \'\', CONCAT(REGEXP_REPLACE(comments, \'(.*?).\', \'\\\1\'), \'{'.$c_vals.'\', \'}\'), \'{'.$c_vals.'}\')
					WHERE id = '.$id
				);
			} else {
				db_query('
					UPDATE `'.DB_PREFIX.'_issues` SET
						options = \'{'.$o_vals.'}\',
						comments = \'{'.$c_vals.'}\'
					WHERE id = '.$id
				);
			}
		}
		
		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET
				is_issue = 1,
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
		$purchases = ($_POST['purchases'] AND count(explode(',', $_POST['purchases'])) > 0) ? 1 : 0;
		$link = text_filter($_POST['link']);
		$deleted = '';
		$warranty_purchases = '';
		
		$oids = db_multi_query('SELECT i.purchase_info, i.warranty, i.warranty_purchases, i.status_id, i.object_owner, s.confirm_purchase FROM `'.DB_PREFIX.'_issues` i LEFT JOIN `'.DB_PREFIX.'_inventory_status` s ON s.id = i.status_id WHERE i.id = '.$id);
		
		if ($oids['purchase_info'] AND $oids['purchase_info'] != '{}') {
			$evalue = array_keys(json_decode($oids['purchase_info'], true));
			$pur_value = array_keys(json_decode($_POST['purchases'], true));
			$deleted = (($pur_value AND $evalue) ? array_diff($pur_value, $evalue) : ($pur_value ? $pur_value : $evalue));
				
			if ($deleted) {
				$d_str = '';
				$n_str = '';
				foreach($deleted as $d) {
					if (in_array($d, $evalue)) {
						if ($d_str) $d_str .= ',';
						$d_str .= $d;
					} else {
						if ($n_str) $n_str .= ',';
						$n_str .= $d;
					}
				}
				if ($d_str) {
					$purchases = db_multi_query('SELECT SUM(confirmed) as conf FROM `'.DB_PREFIX.'_purchases` WHERE id IN('.$d_str.')');

					if ($purchases['conf'] > 0)
						die('confirmed');
					
					db_query('
						UPDATE `'.DB_PREFIX.'_purchases` SET
							del = IF((issue_id > 0 OR customer_id > 0), 1, del)
						WHERE id IN ('.$d_str.')'
					);
					
					db_query('INSERT INTO `'.DB_PREFIX.'_purchase_delete` SET issue_id = '.$id.', staff_id = '.$user['id'].', info = \'from issue, ids: '.$d_str.'\'');
				}
				
				if (($n_str OR $_POST['link']) AND $oids['warranty']) {
					$warranty_purchases = (
						$oids['warranty_purchases'] ? $oids['warranty_purchases'].',' : ''
					).$n_str;
					
					$total_normal = 0;
					$total_warranty = 0;
					$new_arr = explode(',', $n_str);
					
					foreach(json_decode($_POST['purchases'], true) as $k => $p) {
						if (in_array($k, $new_arr))
							$total_warranty += $p['price'] * ($p['quantity'] ?: 1);
						else
							$total_normal += $p['price'] * ($p['quantity'] ?: 1);
					}
					
					if ($_POST['link'])
						$total_warranty += floatval(preg_replace('/[^0-9.]/i', '', $_POST['cprice']));

					if ($total_normal > 0 AND $total_warranty > $total_normal * 1.5)
						die('warranty_total');
				}
			}
		}
		
		if ($_POST['purchases'] AND $_POST['purchases'] !='{}' AND is_array(json_decode($_POST['purchases'], true))) {
			$current_status = db_multi_query('SELECT confirm_purchase FROM `'.DB_PREFIX.'_inventory_status` WHERE id = '.$status_id);
			// set_confirm_purchase
			if ($oids['confirm_purchase'] OR $current_status['confirm_purchase']) {
				if($user['id'] == 16){
					print_r($oids);
					print_r($current_status);
					die;
				}
				$purchases_res = db_multi_query('SELECT COUNT(id) as count FROM `'.DB_PREFIX.'_purchases` WHERE id IN('.implode(',', array_keys(json_decode($_POST['purchases'], true))).') AND recived_id = 0');
				if ($purchases_res['count'] > 0)
					die('receive_purchases');
			}
		}
		
		$status = db_multi_query('SELECT service, inventory, remove_purchase, note FROM `'.DB_PREFIX.'_inventory_status` WHERE id = '.$status_id);
		if ($set_services == 0 AND $status['service'] == 1)
			die('set_service');
		
		if ($status_id != $oids['status_id'] AND $status['note'] AND !$_POST['status_note'])
			die('no_note');
		
		if ($set_inventory == 0 AND $purchases == 0 AND !$link AND $status['inventory'] == 1)
			die('set_inventory');
		
		if ($_POST['link']) {
			$mp = min_price(floatval($_POST['price']), $oids['object_owner']);
			if (floatval($_POST['cprice']) < $mp) 
				die('min_price_'.$mp);
		}
		
		if ($_POST['link'] AND !text_filter($_POST['salename'], 1000, false)) {
			die('empty_salename');
		}
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.description as descr,
				i.important as important,
				i.inventory_ids as inventory,
				i.service_ids,
				i.service_info as set_services,
				i.inventory_info as set_inventory,
				i.purchase_info as purchases,
				i.inventory_id,
				i.staff_id as issue_staff,
				i.assigned,
				i.object_owner,
				i.customer_id,
				i.status_id as status,
				i.warranty,
				d.location_id as location,
				d.object_id,
				d.model,
				m.name as model_name,
				b.name as brand,
				p.date as previous_date,
				p.point as previous_point,
				p.staff_id '.(
					$status_id ? ', s.forfeit, s.point_group, s.percent, s.remove_purchase' : ''
				).'
			FROM '.($status_id ? ' `'.DB_PREFIX.'_inventory_'.(intval($_POST['warranty']) ? 'warranty_' : '').'status` s,' : '').'
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
				if(((in_array($field, ['set_services', 'set_inventory', 'purchases'])) ? array_keys(json_decode($sql[$field], true)) != array_keys(json_decode($value, true)) : $sql[$field] !== $value) AND $value){
					db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
						issue_id = '.$id.',
						user = '.$user['id'].',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						object_id = '.$sql['object_id'].',
						changes = \''.(($field == 'status' AND intval($_POST['warranty'])) ? 'wstatus' : $field).'\''.(
						$field == 'descr' ? '' : ', changes_id = \''.text_filter($value, 16000, false).'\''
					));
				}
			}
		}
		
		$service_part = '';
		$value = '';
		$end = time();
        $c_vals = '';
        $o_vals = '';
		$t = '{';
		if (isset($_POST['set_services']) and $_POST['set_services'] != '{}' and strlen($_POST['set_services']) > 2) {
			foreach(json_decode($_POST['set_services'], true) as $i => $val) {
                $k = $i.((stripos($i, '_') === false) ? '_'.$end : '');
                $t .= '"'.$k.'":{"name":"'.json_escape($val['name']).
                    '","price":"'.preg_replace('/\$/i', '', $val['price']).'","currency":"'.$val['currency'].'","req":"'.intval($val['req']).'"},';

                if (stripos($i, '_') === false) {
				    $c_vals .= '"'.json_escape($k).'":{"staff" : "0", "comment": ""},';
				    $o_vals .= '"'.json_escape($k).'":"",';
                }
			}
			$value = substr($t, 0, -1).'}';
		}
		
		$purchases = $_POST['purchases'];
		if ($link) {
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
			
			$warranty_purchases .= (
					$warranty_purchases ? ',' : ''
			).$pid;
			
			db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', event = \'add_purchase\', date = \''.date('Y-m-d H:i:s', time()).'\', object_id = '.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).', event_id = '.$pid);
			
			if (isset($_POST['photo']) AND $_POST['photo'] != 'undefined') {
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
			
			$purchases = (($purchases AND $purchases != '{}') ? substr($purchases, 0, -1).',' : '{').'"'.$pid.'":{"name":"'.text_filter($_POST['salename'], 1000, false).'","price":"'.text_filter($_POST['cprice'], 30, false).'"}}';
		}
		
		// SQL SET
		db_query('UPDATE `'.DB_PREFIX.'_issues` SET
				important = \''.intval($_POST['important']).'\',
				inventory_info = \''.db_escape_string($_POST['set_inventory']).'\',
				purchase_info = \''.db_escape_string($purchases).'\',
				'.($_POST['set_services'] ? '
					service_info = \''.db_escape_string($value).'\',
					options = IF(options != \'\', CONCAT(REGEXP_REPLACE(options, \'(.*?).\', \'\\\1\'), \'{'.$o_vals.'\', \'}\'), \'{'.$o_vals.'}\'),
					comments = IF(comments != \'\', CONCAT(REGEXP_REPLACE(comments, \'(.*?).\', \'\\\1\'), \'{'.$c_vals.'\', \'}\'), \'{'.$c_vals.'}\')' :
					'service_info = \'\''
				).(
					$_POST['purchases'] ? 
						', warranty_purchases = \''.$warranty_purchases.'\' ' : ''
				).'
			WHERE id = '.$id 
		);
		
		$issue_total = db_multi_query('
			SELECT 
				i.total,
				i.purchase_info,
				inv.id				
			FROM `'.DB_PREFIX.'_issues` i
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON inv.issue_id = i.id
			WHERE i.id = '.$id
		);
		
		db_query('
			UPDATE `'.DB_PREFIX.'_inventory` SET 
				pickup = 0'.(
				($_POST['status'] AND !intval($_POST['warranty'])) ? ', status_id = '.intval($_POST['status']) : ''
			).(
				$_POST['sublocation'] ? ', location_count = '.intval($_POST['sublocation']) : ''
			).(
				$_POST['location'] ? ', location_id = '.intval($_POST['location']) : ''
			).(
				(intval($_POST['warranty']) AND intval($_POST['status'])) ? ', warranty_status = '.intval($_POST['status']) : ''
			).'
			WHERE id = '.intval($_POST['inventory_id'])
		);
		
		if($status_id > 0 && $_POST['status_note']){
			db_query('INSERT INTO `'.DB_PREFIX.'_issues_status_notes` SET 
				issue_id = '.$id.',
				status_id = '.$status_id.',
				staff_id = '.$user['id'].',
				date = \''.date('Y-m-d H:i:s').'\',
				note = \''.text_filter($_POST['status_note'], 16000, false).'\''
			);	
		}
		
		if ($sql['status'] != $status_id) {
			
/* 			db_query('
					INSERT INTO `'.DB_PREFIX.'_issues_notes` SET
					issue_id = '.$id.',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					user = '.$user['id'].',
					comment = \''.text_filter($_POST['status_note']).'\''
			); */
			
			///if ($status['note'])


			$sqtest = 'SELECT u.id as uid, u.name, u.lastname, u.sms as sms_number, f.content, s.sms as sms, s.sms_form as sms_form, s.forfeit, s.point_group, s.purchase, s.remove_purchase'.(intval($_POST['warranty']) ? '' : ', s.percent, s.assigned').' 
				FROM `'.DB_PREFIX.'_users` u,
					`'.DB_PREFIX.'_inventory_'.(intval($_POST['warranty']) ? 'warranty_' : '').'status` s
				LEFT JOIN `'.DB_PREFIX.'_forms` f
					ON f.id = s.sms_form
				WHERE s.id = '.$status_id.' 
					AND u.id = '.$sql['customer_id'];
			$usr = db_multi_query($sqtest);
					
			//if($user['id'] == 2)
				//file_put_contents(ROOT_DIR.'/sqtest.txt', $sqtest);
					
			// ---------------------------------------------------------------------------------------- //
			
			db_query(
				'UPDATE `'.DB_PREFIX.'_issues`
					SET 
					'.(
						intval($_POST['warranty']) ? 'warranty_status' : 'status_id'
					).' = '.$status_id.(
						$usr['purchase'] ? ', purchase_done = 1' : ''
					).(
						$usr['assigned'] ? ', assigned = 1' : ''
					).(
						(!intval($_POST['warranty']) AND in_array($status_id, [2, 6])) ? ', finished = 1 ' : ''
					).'
				WHERE id = '.$id
			);
					
			$point = 0;
			$sql_ = [];
			$rate_point = 1;

			// Is date
			if($sql['forfeit']){
				$point = -abs((int)$sql['forfeit']);
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$point.'
					WHERE id = '.$sql['staff_id']
				);
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'update_status\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					status_id = '.$status_id.',
					object_id = '.(int)$sql['object_id'].',
					issue_id = '.$id.',
					inventory_id = '.(int)$_POST['inventory_id'].',
					point = \''.$point.'\''
				);
			} else {
				if($sql['point_group']){
					$time = ceil(
						(time()-($sql['previous_date'] ? strtotime($sql['previous_date']) : 0))/60
					) ?: 0;
					$points = (array)json_decode($sql['point_group'], true);
					ksort($points);
					foreach($points as $k3 => $v){
						
						if($k3 <= $time OR $k3 == 0){
							
							$point = $v ?: 0;
							
							if(intval($usr['percent'])){
								$ttl = $issue_total['total'];
								$purchases_info = json_decode($issue_total['purchase_info'], true);
								if (is_array($purchases_info) AND count($purchases_info) > 0){
									$purchases = db_multi_query('SELECT SUM(price) as price FROM `'.DB_PREFIX.'_purchases` WHERE id IN ('.implode(',', array_keys($purchases_info)).')');
									if($purchases['price']){
										$ttl = $ttl-$purchases['price'];
										//$point = ($ttl/100)*$point;
									}
									
								}
								$point = ($ttl/100)*$point;
							}
							
							if ($issue_total['total'] >= floatval($config['issue_min_total'])){
								
								db_query(
									'UPDATE `'.DB_PREFIX.'_users`
										SET points = points+'.($v ?: 0).'
									WHERE id = '.$user['id']
								);
								
								db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									staff_id = '.$user['id'].',
									'.(
										intval($_POST['warranty']) ? 'warranty = 1,' : ''
									).'
									action = \'update_status\',
									status_id = '.$status_id.',
									date = \''.date('Y-m-d H:i:s', time()).'\',
									object_id = '.(int)$sql['object_id'].',
									issue_id = '.$id.',
									inventory_id = '.(int)$_POST['inventory_id'].',
									percent = 0,
									point = \''.$point.'\''
								);
								
							} //min_rate = '.(int)$sql_['points'].',
							db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_log` SET
								staff_id = '.$user['id'].',
								date = \''.date('Y-m-d H:i:s', time()).'\',
								status_id = '.$status_id.',
								issue_id = '.$id.',
								points = \''.$point.'\',
								percent = '.intval($usr['percent']).''
							);
							break;
						}
					}
				}	
			}
			
			if ($sql['issue_staff'] != $user['id']) {
				if ($usr['assigned'] == 1) {
					$points = $config['user_points']['issue_forfeit']['points'];
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$sql['issue_staff'].',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						action = \'issue_forfeit\',
						object_id = '.$sql['object_owner'].',
						issue_id = '.$id.',
						point = \''.$points.'\''
					);
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.($points ?: 0).'
						WHERE id = '.$sql['issue_staff']
					);
					if ($points < 0) {
						if ($wt = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_timer` WHERE DATE(date) = \''.date('Y-m-d', time()).'\' AND user_id = '.$sql['issue_staff'])) {
							db_query('UPDATE `'.DB_PREFIX.'_timer` SET seconds = seconds + '.($points * $config['min_forfeit'] * 60).' WHERE id = '.$wt['id']);
							db_query('INSERT INTO `'.DB_PREFIX.'_users_time_forfeit` SET user_id = '.$sql['issue_staff'].', forfeit = '.(floatval($points) * $config['min_forfeit']*60));
						}
					}
				}
				db_query(
					'UPDATE `'.DB_PREFIX.'_issues`
						SET staff_id = '.$user['id'].'
					WHERE id = '.$id
				);
				db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
					issue_id = '.$id.',
					user = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					changes = \'staff\',
					changes_id = \''.$user['id'].'\''
				);
			}
			
			// ---------------------------------------------------------------------------------------- //
			
			if ($usr['sms'] == 1 AND $usr['sms_form'] > 0) {
				$smscontent = str_ireplace([
						'{name}',
						'{device}',
						'{store_name}',
						'{store_address}',
						'{store_cell}'
					], [
						$usr['name'].' '.$usr['lastname'],
						$sql['brand'].' '.$sql['model_name'].' '.$sql['model'],
						$user['object_name'],
						$user['object_address'],
						'',
					], $usr['content']); 
					
				send_sms($usr['sms_number'], $smscontent);
			}
			
			if ($status['remove_purchase'] AND $pur_value) {
				db_query('UPDATE `'.DB_PREFIX.'_purchases` SET del = IF(confirmed = 1, del, 1) WHERE id IN ('.implode(',', $pur_value).')');
				db_query('INSERT INTO `'.DB_PREFIX.'_purchase_delete` SET purchase_id = '.$id.', staff_id = '.$user['id'].', info = \'from statuses, ids: '.implode(',', $pur_value).'\'');
			}
			
			if (!$status['remove_purchase']) {
				$old_status = db_multi_query('SELECT remove_purchase FROM `'.DB_PREFIX.'_inventory_status` WHERE id = '.intval($sql['status']));

				if (intval($old_status['remove_purchase']) AND $pur_value)
					db_query('UPDATE `'.DB_PREFIX.'_purchases` SET del = 0 WHERE id IN ('.implode(',', $pur_value).')');
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
	
	case 'test':
		echo '<pre>';
		print_r(db_multi_query('SELECT COUNT(iss.id) as count, SUM(inv.paid) as paid FROM `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_invoices` inv ON iss.invoice_id = inv.id WHERE inv.conducted = 1 AND inv.date > NOW() - INTERVAL 30 DAY', true));
		die;
	break;
	
	case 'test2':
		echo '<pre>';
		//print_r(db_multi_query('SELECT COUNT(iss.id) as count, SUM(inv.paid) as paid FROM `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_invoices` inv ON iss.invoice_id = inv.id WHERE inv.conducted = 1 AND inv.date > NOW() - INTERVAL 30 DAY', true));
			
			$row['object_id'] = 2;
			
			$sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
					t.id,
					t.user_id,
					SUM(t.seconds) as seconds,
					u.name,
					u.lastname,
					u.image,
					u.pay,
					o.id as object_id,
					o.name as object,
					o.salary_tax as salary,
					o.points_equal as points_equal,
					t.date
				FROM `'.DB_PREFIX.'_users` u
				INNER JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id
				WHERE t.event = \'stop\' AND u.id NOT IN(17, 16, 2) AND o.id = '.$row['object_id'].' AND t.date > NOW() - INTERVAL 30 DAY GROUP BY t.user_id, t.object_id ORDER BY t.user_id, o.id', true
			);

			$o = db_multi_query('
					SELECT SUM(o.paid) as paid,
					o.staff_id,
					o.object_id
					FROM `'.DB_PREFIX.'_users_onsite_changelog` o
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = o.staff_id
					WHERE u.id NOT IN(17, 16, 2) AND o.object_id = '.$row['object_id'].' AND o.date > NOW() - INTERVAL 30 DAY
					GROUP BY o.staff_id, o.object_id', true
			);
					
			$total = 0;
			
			$expanses = 0;
			
			foreach($sql as $row){
				$user_id = $row['user_id'];
				$object_id = $row['object_id'];
				$o_user = array_values(array_filter($o, function($v) use(&$user_id, &$object_id) {
					if ($v['staff_id'] == $user_id AND $v['object_id'] == $object_id)
						return $v;
				}, ARRAY_FILTER_USE_BOTH));
				$total += $row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $row['salary'])/100);
			}
			
			$expanses = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_objects` WHERE id = '.$row['object_id']);
			
			$total += array_sum(json_decode($expanses['options'], true));
			
			$income = db_multi_query('SELECT COUNT(iss.id) as count, SUM(inv.paid) as paid FROM `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_invoices` inv ON iss.invoice_id = inv.id WHERE iss.object_owner = '.$row['object_id'].' AND inv.conducted = 1 AND inv.date > NOW() - INTERVAL 30 DAY');
			
			echo number_format(+-($total/$income['count']), 2, '.', '');
			
		die;
	break;
	
	/*
	*  View issues
	*/
	
	case 'view':
		$_GET['new_ui'] = true;
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
					iss.description as issue_note, 
					iss.status_id as iss_status_id,
					iss.warranty as iss_warranty,
					iss.warranty_status as iss_warranty_status,
					iss.options as iss_options,
					REGEXP_REPLACE(iss.service_info, \'(.*?)"(.*?)_(.*?)":{(.*?)}(.*?).\', \'\\\2,\') as service_ids,
					REGEXP_REPLACE(iss.purchase_info, \'(.*?)"(.*?)":{(.*?)}(.*?).\', \'\\\2,\') as purchase_ids,
					i.*,
					i.id as device_id,
					i.confirmed as confirmed,
					i.options as options,
					i.location_count as sublocation,
					i.old_customer_id,
					mdl.name as model_name,
					iss.warranty as warranty,
					iss.warranty_status as warranty_status,
					iss.status_id as status_id,
					iss.customer_id as customer_id,
					t.name as type_name,
					t.options as opts,
					u.id as cust_id,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.phone as cus_phone,
					u.sms as cus_sms,
					it.id as intake_id,
					it.name as intake_name,
					it.lastname as intake_lastname,
					st.id as staff_id,
					st.name as staff_name,
					st.lastname as staff_lastname,
					u.image as customer_image,
					u.ver as customer_ver,
					u.phone as customer_phone,
					u.address as customer_address,
					u.email,
					o.id as object_id,
					o.name as object_name,
					o.phone as object_phone,
					o.tax as object_tax,
					o.address as object_address,
					o.image as object_image,
					s.name as status_name,
					ws.name as warranty_status_name,
					l.name as location_name,
					l.count as location_count,
					c.name as category_name,
					inv.id as invoice,
					os.name as os_name,
					d.name as discount_name,
					d.percent as discount_percent,
					inv.conducted as conducted,
					inv.paid as partial,
					inv.order_id,
					inv.refund_info,
					inv.refund_paid,
					inv.service_charge,
					inv.purchace,
					up.name as upcharge_name,
					up.price as inv_service,
					sh.id as sh_finished,
					country.name as country_name,
					state.name as state_name,
					ct.city as city_name
				FROM `'.DB_PREFIX.'_issues` iss 
				INNER JOIN `'.DB_PREFIX.'_inventory` 
					i ON iss.inventory_id = i.id 
				LEFT JOIN `'.DB_PREFIX.'_inventory_upcharge` 
					up ON iss.upcharge_id = up.id 
				LEFT JOIN `'.DB_PREFIX.'_inventory_types`
					t ON i.type_id = t.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_models`
					mdl ON i.model_id = mdl.id
				LEFT JOIN `'.DB_PREFIX.'_users`
					u ON u.id = iss.customer_id
				LEFT JOIN `'.DB_PREFIX.'_users` it
					ON it.id = iss.intake_id
				LEFT JOIN `'.DB_PREFIX.'_users` st 
					ON st.id = iss.staff_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = iss.object_owner
				LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
					ON s.id = IF(iss.status_id, iss.status_id, i.status_id)
				LEFT JOIN `'.DB_PREFIX.'_inventory_warranty_status` ws
					ON ws.id = IF(iss.warranty_status, iss.warranty_status, i.warranty_status)
				LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
					ON l.id = i.location_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
					ON c.id = i.category_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_os` os
					ON os.id = i.os_id
				LEFT JOIN `'.DB_PREFIX.'_invoices` inv
					ON inv.issue_id = iss.id AND inv.del = 0
				LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
					ON d.id = iss.discount
				LEFT JOIN `'.DB_PREFIX.'_issues_changelog` sh
					ON sh.changes_id = 2 AND sh.changes = \'status\' AND sh.issue_id = iss.id
				LEFT JOIN `'.DB_PREFIX.'_countries` country
					ON u.country = country.code
				LEFT JOIN `'.DB_PREFIX.'_states` state
					ON u.state = state.code
				LEFT JOIN `'.DB_PREFIX.'_cities` ct
					ON u.zipcode = ct.zip_code
				LEFT JOIN `'.DB_PREFIX.'_orders` ord
					ON ord.id = inv.order_id
				WHERE iss.id = '.$id
			);

			$row['object_id'] = (int)$row['object_id'];
			
			$income = db_multi_query('SELECT COUNT(iss.id) as count, SUM(inv.paid) as paid FROM `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_invoices` inv ON iss.invoice_id = inv.id WHERE iss.object_owner = '.$row['object_id'].' AND inv.conducted = 1 AND inv.date > NOW() - INTERVAL 30 DAY');
			
			
			$sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
					t.id,
					t.user_id,
					SUM(t.seconds) as seconds,
					u.name,
					u.lastname,
					u.image,
					u.pay,
					o.id as object_id,
					o.name as object,
					o.salary_tax as salary,
					o.points_equal as points_equal,
					t.date
				FROM `'.DB_PREFIX.'_users` u
				INNER JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id
				WHERE t.event = \'stop\' AND u.id NOT IN(17, 16, 2) AND o.id = '.$row['object_id'].' AND t.date > NOW() - INTERVAL 30 DAY GROUP BY t.user_id, t.object_id ORDER BY t.user_id, o.id', true
			);

			$o = db_multi_query('
					SELECT SUM(o.paid) as paid,
					o.staff_id,
					o.object_id,
					o.note
					FROM `'.DB_PREFIX.'_users_onsite_changelog` o
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = o.staff_id
					WHERE u.id NOT IN(17, 16, 2) AND o.object_id = '.$row['object_id'].' AND o.date > NOW() - INTERVAL 30 DAY
					GROUP BY o.staff_id, o.object_id', true
			);
					
			$total_income = 0;
			
			$income_salary = 0;
			$income_expanses = 0;
			
			$expanses = 0;
			
			foreach($sql as $r){
				$user_id = $r['user_id'];
				$object_id = $r['object_id'];
				$o_user = array_values(array_filter($o, function($v) use(&$user_id, &$object_id) {
					if ($v['staff_id'] == $user_id AND $v['object_id'] == $object_id AND $v['note'] != 'Not signed.')
					//if ($v['staff_id'] == $user_id AND $v['object_id'] == $object_id)
						return $v;
				}, ARRAY_FILTER_USE_BOTH));
				if(isset($_GET['expanses'])){
					//echo '<pre>';
					//print_r($sql);
					//die;
					print_r($o_user[0]);
					echo $r['seconds']/3600*$r['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $r['salary'])/100).'<br>';
					//die;
				}
				$total_income += $r['seconds']/3600*$r['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $r['salary'])/100);
				$income_salary += $r['seconds']/3600*$r['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $r['salary'])/100);
			}
			
			$expanses = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_objects` WHERE id = '.$row['object_id']);
			
			$total_income += array_sum(json_decode($expanses['options'], true));

			//30 Days((expenses + salary)/#jobs)-purchase Price
			
			
			$income_expanses += array_sum(json_decode($expanses['options'], true));
			
			$income2 = db_multi_query('SELECT COUNT(iss.id) as count, SUM(inv.paid) as paid FROM `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_invoices` inv ON iss.invoice_id = inv.id WHERE iss.object_owner = '.$row['object_id'].' AND inv.conducted = 1 AND inv.date > NOW() - INTERVAL 30 DAY');
			
			$total_income = number_format(+-($total_income > 0 && $income2['count'] > 0 ? ($total_income/$income2['count']) : 0), 2, '.', '');

			if(isset($_GET['test'])){
				echo '<pre>';
				print_r($income);
				die;
			}
			
			if(isset($_GET['expanses'])){
				echo '<pre>';
				print_r($sql);
				echo 'Salary: $'.$income_salary;
				echo '<pre>Expances: $'.array_sum(json_decode($expanses['options'], true));
/* 				print_r($income_salary);
				echo '<pre>';
				print_r($income);
				echo '<pre>';
				print_r(array_sum(json_decode($expanses['options'], true)));
				echo '<pre>';
				echo ($income_salary+array_sum(json_decode($expanses['options'], true)))/$income['count']; */
				die;
			}
			
			
			$prev = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_issues` WHERE id < '.$id.' AND IF(warranty = 0, status_id = '.intval($row['iss_status_id']).', (warranty = '.intval($row['iss_warranty']).' AND warranty_status = '.intval($row['iss_warranty_status']).')) ORDER BY id DESC');
			$next = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_issues` WHERE id > '.$id.' AND IF(warranty = 0, status_id = '.intval($row['iss_status_id']).', (warranty = '.intval($row['iss_warranty']).' AND warranty_status = '.intval($row['iss_warranty_status']).'))');

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
							<span class="thShort">Date: </span>'.$feedback['date'].'
						</div>					
						<div class="td">
							<span class="thShort">Staff: </span><a href="/users/view/'.$feedback['staff_id'].'" onclick="Page.get(this.href); return false;">'.$feedback['name'].' '.$feedback['lastname'].'</a>
						</div>
						<div class="td"><span class="thShort">Rating: </span>'.$star.'
						</div>
						<div class="td">
							<span class="thShort">Comment: </span>'.$feedback['comment'].'
						</div>
						<div class="td w100">
							<span class="thShort">Action: </span><a href="#" class="hnt hntTop" data-title="Edit"><span class="fa fa-pencil"></span></a>
							<a href="#" class="hnt hntTop" data-title="Delete"><span class="fa fa-times"></span></a>
						</div>
					</div>';
				}
			}

			if ($row['device_id']) {
					
					$users_ids = '';
					$users = [];
					if ($row['comments'] AND $row['comments'] != '{}') {
						foreach(json_decode(substr($row['comments'], 0, -2).'}', true) as $c) {
							$users_ids .= $c['staff'].',';
						}
						$users_sql = db_multi_query('SELECT id, CONCAT(name, \' \', lastname) as name FROM `'.DB_PREFIX.'_users` WHERE id IN('.substr($users_ids, 0, -1).')', true);
						$users = array_column($users_sql, 'name', 'id');
					}
					
					if ($row['service_info'] AND strlen($row['service_info']) > 2) {
						$services = json_decode($row['service_info'], true);
						if (is_array($services)) {
							$services_count = 0;
							$service_ids_full = '';
							foreach($services as $k => $s) {
								if ($service_ids_full)
									$service_ids_full .= ',';
								$service_ids_full .= intval($k);
								if (floatval(str_replace('$', '', $s['price'])) > 0)
									$services_count ++;
							}
							
							$issue = db_multi_query('SELECT inv.id, inv.options as inv_options FROM `'.DB_PREFIX.'_inventory` inv WHERE inv.id IN('.$service_ids_full.')', true);
							

							$comments = json_decode(substr($row['comments'], 0, -2).'}', true);
							$upcharge = $row['upcharge_info'] ? array_values(json_decode($row['upcharge_info'], true)) : [];
							$inv_price = ($services_count > 0) ? (str_replace('$', '', $upcharge[0]['price']) ?: 0) / $services_count : 0;
							$ids = '';

							foreach($services as $i => $service) {
								$price = floatval(trim(str_replace('$', '', $service['price']))) * ($service['quantity'] ?: 1);
								$service_price += $price;
								tpl_set('/cicle/isServices'.(isset($_GET['new_ui']) ? '2' : ''), [
									'id' => $i,
									'id-show' => intval($i),
									'name' => $service['name'],
									'type' => $service['type'] == 'stock' ? 'Inventory' : 'Service',
									'price' => number_format(($price > 0 ? ($price + $inv_price) : $price), 2, '.', ""),
									'sprice' => number_format(floatval(trim(str_replace('$', '', $service['price']))), 2, '.', ""),
									'inv_price' => number_format($inv_price, 2, '.', ''),
									'currency' => $service['currency'] ? $config['currency'][$service['currency']]['symbol'] : '$',
									'comment' => $comments[$i]['comment'],
									'staff-id' => $uId,
									'staff-name' => $users[$uId],
									'issue-id' => $id,
									'quantity' => $service['quantity'] ?: 1
								], [
									'comment' => $comments[$i]['comment']
								], 'services');
								
								tpl_set('/cicle/miniServices', [
									'id' => $i,
									'name' => $service['name'],
									'price' => number_format(($price > 0 ? ($price + $inv_price) : $price), 2, '.', ""),
									'sprice' => number_format(floatval(trim(str_replace('$', '', $service['price']))), 2, '.', ""),
									'inv_price' => number_format($inv_price, 2, '.', ''),
									'currency' => $service['currency'] ? $config['currency'][$service['currency']]['symbol'] : '$',
									'issue-id' => $id,
									'quantity' => $service['quantity'] ?: 1
								], [
								], 'miniServices');
								
								$set_services[$i] = [
									'name' => $service['name'],
									'price' => number_format($price, 2, '.', ""),
									'currency' => $service['currency'] ?: 'USD',
									'req' => $service['req'],
									'quantity' => ($service['quantity'] ?: 1)
								];
								

								if ($ids)
									$ids .= ',';
								$ids .= intval($i);

								$options = [];
								$opts = array_values(array_filter($issue, function($a) use(&$i) {
									if ($a['id'] == intval($i))
										return $a;
								}));
								$opts = json_decode($opts[0]['inv_options'], true);
								
								if ($opts) {
									$steps = json_decode(substr($row['iss_options'], 0, -2).'}', true);
									foreach($opts as $n => $v){
										if(!$v) continue;
										$options[$n] = [
											'name' => $v,
											'value' => ($steps[$i] && in_array($n, (explode(',', substr($steps[$i], 0, -1))))) ? 1 : 0
										];
									}
								}
								
								$serv_json[$i] = [
									'name' => $service['name'],
									'steps' => $options
								];
							}
						}
					}
                    
                    if ($row['inventory_info'] AND strlen($row['inventory_info']) > 2) {
						if (is_array(json_decode($row['inventory_info'], true))) {
							foreach(json_decode($row['inventory_info'], true) as $i => $inventory) {
								$price = floatval(trim(preg_replace('/[^0-9.]/i', '', $inventory['price'])));
								$cost_price = floatval(trim(preg_replace('/[^0-9.]/i', '', $inventory['cost_price'])));
								tpl_set('/cicle/isStock'.(isset($_GET['new_ui']) ? '2' : ''), [
									'id' => $i,
									'name' => $inventory['name'],
									'price' => number_format($price, 2, '.', ""),
									'cost-price' => number_format($cost_price, 2, '.', ""),
									'currency' => $service['currency'] ? $config['currency'][$service['currency']]['symbol'] : '$',
									'issue-id' => $id,
									'invoice' => $row['invoice']
								], [
									'invoice-partial' => $row['partial'] > 0
								], 'inventory');
								$inventories .= $i.',';
								$set_inventory[$i] = [
									'name' => $inventory['name'],
									'price' => number_format($price, 2, '.', ""),
									'cost_price' => number_format($cost_price, 2, '.', ""),
									'currency' => $service['currency'] ?: 'USD',
								];
							}
						}
                    }
                    
					$set_purchases = [];
					$purchase_price = '';
                    if ($row['purchase_info'] AND strlen($row['purchase_info']) > 2) {
						$ap = json_decode($row['purchase_info'], true);
/* 							if($user['id'] == 16){
								echo '<pre>';
								echo $row['purchase_info'];
								print_r($ap);
								die;
							} */
						if (is_array($ap)) {
							$allpurchases = db_multi_query('
								SELECT
									id,
									link,
									status,
									price,
									confirmed,
									recived_id,
									object_id,
									customer_id, 
									invoice_id
								FROM `'.DB_PREFIX.'_purchases`
								WHERE id IN ('.implode(',', array_keys($ap)).')
							', true);
							
							$purchase_info = json_decode($row['purchase_info'], true);
							

							if(is_array($purchase_info)){
								foreach($purchase_info as $i => $purchase) {
									$p = array_values(array_filter($allpurchases, function($a) use(&$i) {
										if ($a['id'] == $i)
											return $a;
									}));
									
									$price = floatval(trim(preg_replace('/[^0-9.]/i', '', $purchase['price'])));
									$cost_price = floatval(trim(preg_replace('/[^0-9.]/i', '', $purchase['cost_price'])));
									
									tpl_set('/cicle/isPurchases'.(isset($_GET['new_ui']) ? '2' : ''), [
										'id' => $i,
										'name' => $purchase['name'],
										'link' => '<a href="'.$p[0]['link'].'" target="_blank">View</a>' ?: '',
										'status' => $p[0]['status'] ?: '',
										'price' => number_format($price, 2, '.', ""),
										'cost-price' => number_format($cost_price, 2, '.', ""),
										'currency' => $service['currency'] ? $config['currency'][$service['currency']]['symbol'] : '$',
										'invoice' => $row['invoice'],
										'issue-id' => $id
									], [
										'invoice-partial' => $row['partial'] > 0,
										'instore' => (!$p[0]['customer_id'] AND !$p[0]['invoice_id'] AND !$p[0]['issue_id']),
										'can_receive' => ($p[0]['confirmed'] == 1 AND $p[0]['recived_id'] == 0)
									], 'purchases');
									$purchases .= $i.',';
									$set_purchases[$i] = [
										'name' => $purchase['name'],
										'price' => number_format($price, 2, '.', ""),
										'cost_price' => number_format($cost_price, 2, '.', ""),
										'currency' => $service['currency'] ?: 'USD',
									];
									if ($p[0]['price'] > 50) {
										$purchase_price = 1;
									}
								}
							}
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
						], [
							'edit-invoce' => $user['edit_invoices']
						], 'invoices');
					}
					
					
					// get notes
					foreach(db_multi_query(
						'SELECT n.*,
								u.name,
								u.lastname
						FROM `'.DB_PREFIX.'_issues_notes` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.user
						WHERE n.issue_id = '.$id
					, true) as $note){
						tpl_set('/cicle/isNotes', [
							'id' => $note['id'],
							'date' => convert_date($note['date'], true),
							'note' => $note['comment'],
							'staff-id' => $note['user'],
							'staff-name' => $note['name'],
							'staff-lastname' => $note['lastname']
						], [], 'notes');
					}
					
					// get status notes
					foreach(db_multi_query(
						'SELECT n.*,
								u.name,
								u.lastname,
								s.name as status_name
						FROM `'.DB_PREFIX.'_issues_status_notes` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.staff_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = n.status_id
						WHERE n.issue_id = '.$id
					, true) as $note){
						tpl_set('/cicle/isStatusNotes', [
							'id' => $note['id'],
							'date' => convert_date($note['date'], true),
							'note' => $note['note'],
							'staff-id' => $note['staff_id'],
							'staff-name' => $note['name'],
							'staff-lastname' => $note['lastname'],
							'status' => $note['status_name']
						], [], 'status-notes');
					}
					
					// get assigned
					$assigned_tbl = '<div class="tr">
							<div class="td w10">
								<span class="thShort">Date: </span>'.convert_date($row['date'], true).'
							</div>
							<div class="td">
								<span class="thShort">Staff: </span><a href="/users/view/'.$row['intake_id'].'" target="_blank">'.$row['intake_name'].' '.$row['intake_lastname'].'</a>
							</div>
						</div>';
					if($assign = db_multi_query(
						'SELECT n.*,
								u.name,
								u.lastname,
								u.id as uid
						FROM `'.DB_PREFIX.'_issues_changelog` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.changes_id
						WHERE n.issue_id = '.$id.' AND n.changes = \'staff\''
					, true)) {
						foreach ($assign as $note){
							$assigned_tbl .= '<div class="tr">
								<div class="td w10">
									<span class="thShort">Date: </span>'.$note['date'].'
								</div>
								<div class="td">
									<span class="thShort">Staff: </span><a href="/users/view/'.$note['uid'].'" target="_blank">'.$note['name'].' '.$note['lastname'].'</a>
								</div>
							</div>';
						}
					}
					
					// get changes
					if ($stats = db_multi_query(
						'SELECT SQL_CALC_FOUND_ROWS
								n.*,
								u.name,
								u.lastname,
								s.name as status_name,
								ws.name as wstatus_name,
								l.name as location_name,
								d.name as discount_name,
								p.name as pur_name,
								p.sale_name as pur_sale_name,
								CONCAT(us.name, \' \', us.lastname) as staff_name,
								us.id as staff_id
						FROM `'.DB_PREFIX.'_issues_changelog` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.user
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = n.changes_id AND n.changes = \'status\'
						LEFT JOIN `'.DB_PREFIX.'_inventory_warranty_status` ws
							ON ws.id = n.changes_id AND n.changes = \'wstatus\'
						LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
							ON l.id = n.changes_id AND n.changes = \'location\'
						LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
							ON d.id = n.changes_id AND n.changes = \'discount\'
						LEFT JOIN `'.DB_PREFIX.'_users` us
							ON us.id = n.changes_id AND n.changes = \'staff\'
						LEFT JOIN `'.DB_PREFIX.'_purchases` p
							ON p.id IN (n.changes_id) AND n.changes = \'purchase_ids\'
						WHERE n.issue_id = '.$id.' AND n.changes_id != \'\' ORDER BY n.id DESC LIMIT 0, 20', true
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
							$inv = '';
							switch ($note['changes']){
								
								case 'status':
									$change = 'New status: '.$note['status_name'];
								break;
								
								case 'status':
									$change = 'New status: '.$note['status_name'];
								break;
								
								case 'staff':
									$change = 'New staff: <a href="/users/view/'.$note['staff_id'].'" target="_blank">'.$note['staff_name'].'</a>';
								break;
								
								case 'wstatus':
									$change = 'New warranty status: '.$note['wstatus_name'];
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
								
								case 'set_inventory':
								case 'inventory_info':
									$links = '';
									$inv = json_decode($note['changes_id'], true);
									if (is_array($inv)) {
										foreach($inv as $k => $in) {
											$links .= '<a href="/invetory/view/'.$k.'" target="_blank">'.$in['name'].'</a>';
										}
										if ($links)
											$change = 'New inventory: '.$links;
									}
								break;
								
								case 'set_services':
								case 'service_info':
									$links = '';
									$inv = json_decode($note['changes_id'], true);
									if (is_array($inv)) {
										foreach($inv as $k => $in) {
											$links .= '<a href="/invetory/edit/'.$k.'" target="_blank">'.$in['name'].'</a>';
										}
										if ($links)
											$change = 'New service: '.$links;
									}
								break;
								
								case 'purchases':
								case 'purchase_info':
									$links = '';
									$inv = json_decode($note['changes_id'], true);
									if (is_array($inv)) {
										foreach($inv as $k => $in) {
											$links .= '<a href="/purchases/edit/'.$k.'" target="_blank">'.$in['name'].'</a>';
										}
										if ($links)
											$change = 'New service: '.$links;
									}
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
									'date' => convert_date($note['date'], true),
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
				$meta['title'] = ($row['old_customer_id'] > 0 ? 'View internal job' : $lang['viewIssue']);
				$objects_ip = array_flip($config['object_ips']);

				$discount = json_decode($row['discount'], true);
				if (is_array($discount) AND !array_keys($discount)[0])
					$discount = [];

				if ($route[1] == 'view' AND $id) {
					$forms = '';
					foreach(db_multi_query('
						SELECT id, name FROM `'.DB_PREFIX.'_forms`
						WHERE FIND_IN_SET(\'issue\', types) ORDER BY id LIMIT 50'
					, true) as $form){
						$forms .= '<li><a href="javascript:to_print(\'/forms?type=issue&id='.$form['id'].'&issue_id='.$id.'\', \'issue №'.$id.'\');" title="'.$form['name'].'">'.$form['name'].'</a></li>';
					}
					
					$nvphone = 0;
					foreach(explode(',', $row['cus_phone']) as $ph) {
						if (strlen(preg_replace("/\D/", '', $ph)) < 10)
							$nvphone = 1;
					}
					if (strlen(preg_replace("/\D/", '', $row['cus_sms'])) < 10)
						$nvphone = 1;
					
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
					//echo '<pre>';
					//print_r($row);
					//die;
					
					$total = $row['total'];
					
					$tax = $row['purchace'] ? 0 : ($total - $onsite_total) * $row['object_tax'] / 100;
					
					if ($row['discount'])
						$discount2 = array_values($discount);
					
					$tax = $discount2[0]['percent'] ? round(
						$tax * (100 - $discount2[0]['percent']
					) / 100, 2) : $tax;
					
					if($row['tax_exempt'])
						$tax = 0;
					
					$total = $discount2[0]['percent'] ? round($total * (
						100 - $discount2[0]['percent']) / 100, 2
					) : $total;
					
					if($row['service_charge']){
						$s_charge = (($row['order_id'] ? $row['total'] : ($total+$tax-$tradein))/100)*$row['service_charge'];
						$total += $s_charge;
					}
					
					if ($row['refund_info'] AND strlen($row['refund_info']) > 2 AND $row['refund_paid'] > 0) {
						$total = (-1) * $row['refund_paid'];
						$tax = 0; 
						$tradein = 0;
					}
					
					$due = $total+$tax-$tradein - $row['partial'];

					tpl_set(((isset($_GET['new_ui'])) ? 'issues/view2' : 'issues/view'), [
						'id' => $id,
						'date' => convert_date($row['date'], true),
						'total-income' => $total_income,
						'total' => $row['total'],
						'due' => sprintf("%0.2f", ($row['order_id'] ? ($row['total'] - $row['partial']) : (abs($due) < 0.01 ? 0 : $due))),
						'device-id' => $row['device_id'],
						'doItPrice' => $row['doit'] ?: 0,
						'quotePrice' => $row['quote'] ?: 0,
						'title' => 'View issue',
						'income' => number_format(($income['paid'] > 0 ? $income['paid']/$income['count'] : 0), '2', '.', ''),
						'income-paid' => number_format($income['paid'], '2', '.', ''),
						'income-count' => (int)$income2['count'],
						'income-salary' => (int)$income_salary,
						'income-expanses' => (int)$income_expanses,
						'descr' => $row['issue_note'],
						'device' => json_encode([$row['inventory_id'] => [
							'name' => $row['sname']
						]]),
						'options' => $options,
						'set-inventory' => json_encode($set_inventory),
						'set-services' => json_encode($set_services),
						'set-purchases' => json_encode($set_purchases),
						'feedback' => $fb,
						'assigned_tbl' => $assigned_tbl,
						'password' => $row['password'],
						'model' => $row['model'],
						'model-name' => $row['model_name'],
						'type-name' => $row['type_name'],
						'price' => $row['price'],
						'currency' => $row['currency'] ? $config['currency'][$row['currency']]['symbol'] : '$',
						'forms-list' => $forms,
						'purchase-price' => $row['purchase_price'],
						'sale-price' => $row['sale_price'],
						'category' => $row['category_name'],
						'os' => $row['os_name'],
						'version-os' => $row['ver_os'],
						'charger' => $row['charger'] ? 'yes' : 'no',
						'serial' => $row['serial'],
						'customer-ver' => $row['customer_ver'],
						'intake-id' => $row['intake_id'],
						'intake-name' => $row['intake_name'],
						'intake-lastname' => $row['intake_lastname'],
						'staff-id' => $row['staff_id'],
						'staff-name' => $row['staff_name'],
						'staff-lastname' => $row['staff_lastname'],
						'customer-id' => $row['customer_id'],
						'customer-name' => $row['customer_name'],
						'customer-lastname' => $row['customer_lastname'],
						'customer-image' => $row['customer_image'],
						'customer-phone' => $row['customer_phone'],
						'customer-address' => preg_replace(
							"/\n/", "<br>", $row['customer_address']
						),	
						'customer-email' => $row['email'],
						'country-name' => $row['country_name'],
						'state-name' => $row['state_name'],
						'city-name' => $row['city_name'],
						'object-id' => intval($row['object_id']),
						'object-tax' => $row['object_tax'],
						'object-image' => $row['object_image'],
						'object-phone' => $row['object_phone'],
						'object-address' => preg_replace(
							"/\n/", "<br>", $row['object_address']
						),
						'object' => $row['object_name'],
						'status' => $row['status_name'] ?: ($row['status_id'] == 'new' ? 'new' : 'finished'),
						'status-id' => $row['status_id'],
						'warranty-reason' => $row['warranty_reason'],
						'warranty-status' => $row['warranty_status_name'],
						'warranty-status-id' => $row['warranty_status'],
						'location' => $row['location_name'],
						'location-count' => $row['location_count'],
						'location-id' => $row['location_id'],
						'sublocation' => $row['sublocation'] ?: '',
						'inventory' => $tpl_content['inventory'],
						'services' => $tpl_content['services'],
						'miniServices' => $tpl_content['miniServices'],
						'purchases' => $tpl_content['purchases'],
						'invoices' => $tpl_content['invoices'] ?: '<div class="noContent">'.$lang['NoInvoices'].'</div>',
						'notes' => $tpl_content['notes'] ?: '<div class="noContent">'.$lang['NoNotes'].'</div>',
						'status-notes' => $tpl_content['status-notes'] ?: '',
						'stats' => $tpl_content['stats'] ?: '<div class="noContent">'.$lang['NoStats'].'</div>',
						'inventory-ids' => $inventories ?: 0,
						'service-ids' => $services ?: 0,
						'purchase-ids' => $purchases ?: 0,
						'discount-name' => $discount ? '('.array_values($discount)[0]['name'].')' : '',
						'discount-id' => $discount? array_keys($discount)[0] : 0,
						'discount' => $discount ? (array_values($discount)[0]['percent'] ?? array_values($discount)[0]['price']) : 0,
						'forms' => $row['opts'] ? getTypes(
							json_decode($row['opts'], true),
							json_decode($row['options'], true)
						) : '',
						'serv_json' => json_encode($serv_json),
						'invoice' => $row['invoice_id'],
						'nconfirmed' => $nconfirmed,
						'more' => ($left_count_changes > 0 ? '' : ' hdn'),
						'discount-confirmed' => $row['discount_confirmed'],
						'discount-reason' => $row['discount_reason'],
						'unconfirmed_services' => implode(',', $nconfirmed_set['services']),
						'unconfirmed_inventory' => implode(',', $nconfirmed_set['inventory']),
						'unconfirmed_discount' => ($row['discount'] AND $row['discount_confirmed'] == 0 ? $row['discount_name'] : ''),
						'inv-service' => $row['inv_service'],
						'upcharge' => $row['upcharge_info'] ?: '{}',
						'password' => $row['device_password'],
						'prev' => $prev['id'],
						'next' => $next['id']
					], [
						'finish' => $row['finished'] && !$row['publish'],
						'buy' => $row['customer_id'],
						'password' => $row['device_password'],
						'internal' => ($row['old_customer_id'] > 0),
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
						'is-status-notes' => $tpl_content['status-notes'],
						'invoice' => $row['invoice'],
						'invoice-done' => $row['conducted'],
						'pickup' => $row['pickup'] == 1,
						'view' => $route[1] == 'view',
						'sublocation' => $row['sublocation'],
						'service-price' => $service_price > 0,
						'feedback' => $fb,
						'create-invoice' => (($id < 1535 OR $row['total'] > 0) AND !($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips'])))),
						'show' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR $objects_ip[$_SERVER['REMOTE_ADDR']] != 0),
						'show-purchase-message' => $purchase_price == 1 AND !intval($row['invoice']),
						'not-valid-phone' => $nvphone == 1,
						'can_be_warranty' => $row['sh_finished'] > 0,
						'warranty' => $row['warranty'] > 0,
						'warranty_request' => $row['warranty'] == 1,
						'confirm_warranty' => ($user['confirm_warranty'] AND $row['warranty'] == 1),
						'confirmed_warranty' => $row['warranty'] == 2,
						'pickup-status' => (intval($row['conducted']) == 1 AND $row['warranty'] == 0 AND intval($row['sh_finished']) > 0),
						'switch-assigned' => ($row['staff_id'] == $user['id'] OR $user['issue_assigned']),
						'staff' => $row['staff_id'] == $user['id'],
						'vika' => $user['id'] == 17,
						'confirm-discount' => (!$row['discount_confirmed'] AND $user['confirm_discount']),
						'prev' => $prev['id'],
						'next' => $next['id'],
						'arrows' => ($prev['id'] AND $next['id'])
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
								ws.name as wstatus_name,
								l.name as location_name,
								d.name as discount_name,
								p.name as pur_name,
								p.sale_name as pur_sale_name,
								CONCAT(us.name, \' \', us.lastname) as staff_name,
								us.id as staff_id
						FROM `'.DB_PREFIX.'_issues_changelog` n
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = n.user
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = n.changes_id AND n.changes = \'status\'
						LEFT JOIN `'.DB_PREFIX.'_inventory_warranty_status` ws
							ON ws.id = n.changes_id AND n.changes = \'wstatus\'
						LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
							ON l.id = n.changes_id AND n.changes = \'location\'
						LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
							ON d.id = n.changes_id AND n.changes = \'discount\'
						LEFT JOIN `'.DB_PREFIX.'_users` us
							ON us.id = n.changes_id AND n.changes = \'staff\'
						LEFT JOIN `'.DB_PREFIX.'_purchases` p
							ON p.id IN (n.changes_id) AND n.changes = \'purchase_ids\'
						WHERE n.issue_id = '.$id.' AND n.changes_id != \'\' ORDER BY n.id DESC LIMIT '.($page*$count).', '.$count
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
					$inv = '';
					switch ($note['changes']) {
						case 'status':
							$change = 'New status: '.$note['status_name'];
						break;
						
						case 'wstatus':
							$change = 'New warranty status: '.$note['wstatus_name'];
						break;
						
						case 'staff':
							$change = 'New staff: <a href="/users/view/'.$note['staff_id'].'" target="_blank">'.$note['staff_name'].'</a>';
						break;
						
						case 'location':
							$change = 'New location: '.$note['location_name'];
						break;
						
						case 'set_inventory':
						case 'inventory_info':
							$links = '';
							$inv = json_decode($note['changes_id'], true);
							if (is_array($inv)) {
								foreach($inv as $k => $in) {
									$links .= '<a href="/invetory/view/'.$k.'" target="_blank">'.$in['name'].'</a>';
								}
								if ($links)
									$change = 'New inventory: '.$links;
							}
						break;
						
						case 'set_services':
						case 'service_info':
							$links = '';
							$inv = json_decode($note['changes_id'], true);
							if (is_array($inv)) {
								foreach($inv as $k => $in) {
									$links .= '<a href="/invetory/edit/'.$k.'" target="_blank">'.$in['name'].'</a>';
								}
								if ($links)
									$change = 'New service: '.$links;
							}
						break;
						
						case 'purchases':
						case 'purchase_info':
							$links = '';
							$inv = json_decode($note['changes_id'], true);
							if (is_array($inv)) {
								foreach($inv as $k => $in) {
									$links .= '<a href="/purchases/edit/'.$k.'" target="_blank">'.$in['name'].'</a>';
								}
								if ($links)
									$change = 'New service: '.$links;
							}
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
    * Del fiels
    */
    case 'del_field':
        is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$value_id = intval($_POST['value_id']);
        $field = text_filter($_POST['field'], 50, false);

		if ($field == 'purchase_info'){
			
			$oids = db_multi_query('SELECT purchase_info FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id);
			$purchase = db_multi_query('SELECT confirmed FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$value_id);
			
			if (!$purchase['confirmed']){
				if ($value_id) {
					db_query('
						UPDATE `'.DB_PREFIX.'_purchases` SET
							del = IF((issue_id > 0 OR customer_id > 0), 1, del)
						WHERE id IN ('.$value_id.')'
					);
				}
						
				if ($oids['purchase_info'] AND $oids['purchase_info'] != '{}') {
					$value = array_keys(json_decode($_POST['value'], true));
					$evalue = array_keys(json_decode($oids['purchase_info'], true));
					if ($ids = implode(',', (
						($value AND $evalue) ? array_diff($value, $value) : ($value ? $value : $evalue)
					))) {
						db_query('
							UPDATE `'.DB_PREFIX.'_purchases` SET
								del = IF((issue_id > 0 OR customer_id > 0), 1, del)
							WHERE id IN ('.$ids.')'
						);
					}
				}
			}
				//die('confirmed');
		}
		
        db_query('
            UPDATE `'.DB_PREFIX.'_issues` SET
            	'.$field.' = \''.$_POST['value'].'\'
			WHERE id = '.$id
        );
		
        die('OK');
    break;
	
	/*
	* Send field
	*/
	case 'send_field':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$field = text_filter($_POST['field'], 255, false);
		$deleted = '';
		$warranty_purchases = '';
		
		if ($field == 'discount_confirmed' AND !$user['confirm_discount'])
			die('no_acc');
		
		if ($field == 'service_info' OR $field == 'purchase_info' OR $_POST['link'])
			$oids = db_multi_query('SELECT purchase_info, warranty, warranty_purchases, object_owner FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id);
		
		
		if ($_POST['link']) {
			$mp = min_price(floatval($_POST['price']), $oids['object_owner']);
			if (floatval($_POST['cprice']) < $mp) 
				die('min_price_'.$mp);
		}
		
		if ($_POST['link'] AND !text_filter($_POST['salename'], 1000, false)) {
			die('empty_salename');
		}
		
		if ($field == 'service_info' OR $field == 'purchase_info') {
			if ($oids['purchase_info'] AND $oids['purchase_info'] != '{}') {
				$evalue = array_keys(json_decode($oids['purchase_info'], true));
				$pur_value = array_keys(json_decode($field == 'purchase_info' ? $_POST['value'] : $_POST['purchases'], true));
				$deleted = (($pur_value AND $evalue) ? array_diff($pur_value, $evalue) : ($pur_value ? $pur_value : $evalue));
				
				if ($deleted) {
					$d_str = '';
					$n_str = '';
					foreach($deleted as $d) {
						if (in_array($d, $evalue)) {
							if ($d_str) $d_str .= ',';
							$d_str .= $d;
						} else {
							if ($n_str) $n_str .= ',';
							$n_str .= $d;
						}
					}
					if ($d_str) {
						$purchases = db_multi_query('SELECT COUNT(confirmed) as conf FROM `'.DB_PREFIX.'_purchases` WHERE id IN('.$d_str.')');

						if ($purchases['conf'])
							die('confirmed');
						
						db_query('
							UPDATE `'.DB_PREFIX.'_purchases` SET
								del = IF((issue_id > 0 OR customer_id > 0), 1, del)
							WHERE id IN ('.$d_str.')'
						);
					}
				}

				if (($n_str OR $_POST['link']) AND $oids['warranty']) {
					$warranty_purchases = (
						$oids['warranty_purchases'] ? $oids['warranty_purchases'].',' : ''
					).$n_str;
					
					$total_normal = 0;
					$total_warranty = 0;
					$new_arr = explode(',', $n_str);
					
					foreach(json_decode($field == 'purchase_info' ? $_POST['value'] : $_POST['purchases'], true) as $k => $p) {
						if (in_array($k, $new_arr))
							$total_warranty += $p['price'] * ($p['quantity'] ?: 1);
						else
							$total_normal += $p['price'] * ($p['quantity'] ?: 1);
					}
					
					if ($_POST['link'])
						$total_warranty += floatval(preg_replace('/[^0-9.]/i', '', $_POST['cprice']));

					if ($total_normal > 0 AND $total_warranty > $total_normal * 1.5)
						die('warranty_total');
				}
			}
		}
		
		$services_ids = '';
		if ($field == 'service_info') {
			foreach(json_decode($_POST['value']) as $i => $val) {
				if ($services_ids) $services_ids .= ',';
				$services_ids .= intval($i);
			}
		}
		
		if ($field == 'service_info') {
			$services = db_multi_query('SELECT SUM(parts_required) as req FROM `'.DB_PREFIX.'_inventory` WHERE id IN ('.$services_ids.')');
			if ($services['req'] > 0 AND (!$_POST['inventory'] OR $_POST['inventory'] == '{}') AND (!$_POST['purchases'] OR $_POST['purchases'] == '{}') AND !$_POST['link'])
				die('req');
		}

		
		if ($_POST['link']) {
			$shipment_cost = floatval($_POST['shipment_cost']);
			$cost_price = floatval($_POST['price']) ?: floatval($_POST['p_price']);
			$total = $cost_price+$shipment_cost;
			$objects_ip = array_flip($config['object_ips']);
			db_query('INSERT INTO `'.DB_PREFIX.'_purchases` SET
				name = \''.text_filter($_POST['name'], 100, false).'\',
				sale_name = \''.text_filter($_POST['salename'], 1000, false).'\',
				link = \''.text_filter($_POST['link'], 200, false).'\',
				price = \''.$cost_price.'\',
				shipment_cost = \''.$shipment_cost.'\',
				tracking = \''.text_filter($_POST['itemID'], 50, false).'\',
				sale = \''.text_filter($_POST['cprice'], 30, false).'\',
				quantity = 1,
				total = \''.$total.'\',
				object_id = \''.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).'\',
				status = \'Purchased\',
				customer_id = '.intval($_POST['customer_id']).',
				create_id = '.$user['id'].', 
				create_date = \''.date('Y-m-d H:i:s', time()).'\',
				issue_id = '.$id
			);
			
			$pid = intval(mysqli_insert_id($db_link));
			
			$warranty_purchases .= ($warranty_purchases ? ',' : '').$pid;
			
			db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', date = \''.date('Y-m-d H:i:s', time()).'\', event = \'add_purchase\', object_id = '.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).', event_id = '.$pid);
			
			if (isset($_POST['photo']) AND $_POST['photo'] != 'undefined') {
				$dir = ROOT_DIR.'/uploads/images/';
				if(!is_dir($dir.$pid)){
					@mkdir($dir.$pid, 0777);
					@chmod($dir.$pid, 0777);
				}
				$dir = $dir.$pid.'/';
				
				
				if(strpos($_POST['photo'], 'data:image/') !== false){
					$exp = explode(',', $_POST['photo']);
					$exp[0] = trim(str_ireplace([
						'data:image/',
						';base64'
					], '', $exp[0]));
					$type = $exp[0] == 'jpeg' ? 'jpg' : $match[1];
					$imgdata = base64_decode($exp[1]);
				} else {
					$type = mb_strtolower(pathinfo($_POST['photo'], PATHINFO_EXTENSION));
					$imgdata = file_get_contents($_POST['photo']);
				}
				
				$rename = uniqid('', true).'.'.$type;
				
				file_put_contents($dir.$rename, $imgdata);
				
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
		$value = $_POST['value'];
		$field = text_filter($_POST['field'], 50, true);
		$end = time();
        $c_vals = '';
        $o_vals = '';
		$t = '{';
		if ($field == 'service_info') {
			foreach(json_decode($value, true) as $i => $val) {
                $k = $i.((stripos($i, '_') === false) ? '_'.$end : '');
                $t .= '"'.$k.'":{"name":"'.json_escape($val['name']).
                    '","price":"'.preg_replace('/[^0-9.]/i', '', $val['price']).'","req":"'.intval($val['req']).'","currency":"'.$val['currency'].'"},';

                if (stripos($i, '_') === false) {
				    $c_vals .= '"'.json_escape($k).'":{"staff" : "0", "comment": ""},';
				    $o_vals .= '"'.json_escape($k).'":"",';
                }
			}
			$value = substr($t, 0, -1).'}';
		} elseif ($field == 'purchase_info' AND $_POST['link'])
            $value = (($value AND $value != '{}') ? substr($value, 0, -1).',' : '{').'"'.$pid.'":{"name":"'.json_escape($_POST['salename']).'","price":"'.json_escape($_POST['cprice']).'","cost_price":"'.json_escape($cost_price).'"}}';

		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.'.$field.'
			FROM
				`'.DB_PREFIX.'_issues` i
			WHERE i.id = '.$id)
			)
		){
			if ((in_array($field, ['service_info', 'inventory_info', 'purchase_info'])) ? array_keys(json_decode($sql[$field], true)) != array_keys(json_decode($value, true)) : $sql[$field] != $value) {
				db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
					issue_id = '.$id.',
					user = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					changes = \''.$field.'\''.(
					$field == 'descr' ? '' : ', changes_id = \''.db_escape_string($value).'\''
				));
			}
		}
		
		if ($field == 'discount_confirmed') {
			if (!$d = db_multi_query('SELECT discount_confirmed FROM `'.DB_PREFIX.'_issues` WHERE discount_confirmed = 0 AND id = '.$id))
				die('OK');
		}
		
        db_query('
            UPDATE `'.DB_PREFIX.'_issues` SET
                '.$field.' = \''.db_escape_string($value).'\''.(
                    $field == 'service_info' ? '
                        , options = IF(options != \'\', CONCAT(REGEXP_REPLACE(options, \'(.*?).\', \'\\\1\'), \'{'.$o_vals.'\', \'}\'), \'{'.$o_vals.'}\')
                        , comments = IF(comments != \'\', CONCAT(REGEXP_REPLACE(comments, \'(.*?).\', \'\\\1\'), \'{'.$c_vals.'\', \'}\'), \'{'.$c_vals.'}\')
                        , inventory_info = \''.db_escape_string($_POST['inventory']).'\'
                        , purchase_info = \''.db_escape_string($_POST['link'] ? 
                            ((isset($_POST['purchases']) AND $_POST['purchases'] != '{}') ? substr($_POST['purchases'], 0, -1).',' : '{').'"'.$pid.'":{"name":"'.json_escape($_POST['salename']).'","price":"'.json_escape($_POST['cprice']).'","cost_price":"'.json_escape($cost_price).'"}}'
                            : $_POST['purchases']
                        ).'\''
                     : ''
                ).(
					($field == 'service_info' OR $field == 'purchase_info') ?
					', warranty_purchases = \''.$warranty_purchases.'\'' : ''
				).' WHERE id = '.$id
        );
		
		/* if ($_POST['purchases']) {
			$parr = implode(',', array_keys(JSON_DECODE($_POST['purchases'], true)));
			if ($parr)
				db_query('UPDATE `'.DB_PREFIX.'_purchases` SET issue_id = '.$id.' WHERE id IN('.$parr.')');
		} */

		
		if ($field == 'discount_confirmed') {
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'un_discount\'');
			
			if ($iss_inv = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_invoices` WHERE issue_id = '.$id)) {
				db_query('UPDATE `'.DB_PREFIX.'_invoices` SET discount_confirmed = 1 WHERE id = '.$iss_inv['id']);
			}
		}
		
		die('OK');
	break;
	
	/*
	* Send discount
	*/
	case 'send_discount':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$discount = 0;
		/*if (!in_to_array('1,2', $user['group_ids'])) {
			$d = db_multi_query('SELECT percent FROM `'.DB_PREFIX.'_invoices_discount` WHERE id = '.$id);
			$discount = $d['percent'];
		}*/
		
		if($id AND (
			$sql = db_multi_query('SELECT
				i.id,
				i.discount,
				i.discount_confirmed,
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
						date = \''.date('Y-m-d H:i:s', time()).'\',
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
				discount = \''.$_POST['discount'].'\',
				discount_reason = \''.text_filter($_POST['reason'], null, false).'\',
				discount_user = \''.$user['id'].'\',
				discount_confirmed = '.(in_to_array('1,2', $user['group_ids']) ? 1 : 0).'
			WHERE id = '.$id);
			
		$discount_old = json_decode($sql['discount'], true);
		$discount = json_decode($_POST['discount'], true);
		
		if (!in_to_array('1,2', $user['group_ids']) AND ((is_array($discount_old) AND is_array($discount) AND array_keys($discount_old)[0] != array_keys($discount)[0] AND $sql['discount_confirmed']) OR !$discount_old))
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'un_discount\'');
		
		if (!$sql['discount_confirmed'] AND is_array($discount_old) AND array_keys($discount_old)[0] AND !array_keys($discount)[0])
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'un_discount\'');
			
		die('OK');
	break;
	
	/*
	* Send step
	*/
	case 'send_step':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$options = db_multi_query('SELECT options FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id);
		$opts = $options['options'];
		
		if (!$options['options'] OR $options['options'] == '{}' OR !is_array(json_decode(substr($options['options'], 0, -2).'}', true))) {
			$opts = '{"'.$_POST['sId'].'":"",}';
		} elseif (!in_array($_POST['sId'], array_keys(json_decode(substr($options['options'], 0, -2).'}', true))))
			$opts = substr($opts, 0, -1).',"'.$_POST['sId'].'":"",}';
			
		if ($options['options'] != $opts)
			db_query('UPDATE `'.DB_PREFIX.'_issues` SET options = \''.$opts.'\' WHERE id = '.$id);

		db_query(
			'UPDATE `'.DB_PREFIX.'_issues` SET options = REGEXP_REPLACE(
				options, '.(
					$_POST['del'] ? 
					'\'"'.$_POST['sId'].'":"(.*?)'.text_filter($_POST['value'], null, false).',(.*?)"\',
					 \'"'.$_POST['sId'].'":"\\\1\\\2"\'' :
					'\'"'.$_POST['sId'].'":"(.*?)"\',
					 \'"'.$_POST['sId'].'":"\\\1'.text_filter($_POST['value'], null, false).',"\''
				).') 
				WHERE id = '.$id
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
				date = \''.date('Y-m-d H:i:s', time()).'\',
				user = '.$user['id'].',
				comment = \''.text_filter($_POST['note']).'\''
		);
		die('OK');
	break;
	
	case 'addfield':
		is_ajax() or die('Hacking attempt!');
		$id = (int)$_POST['id'];
/* 		if($_POST['type'] != 'service'){
			echo 'Hacking';
			die;
		} */
		if($issue = db_multi_query('SELECT service_info FROM `'.DB_PREFIX.'_issues` WHERE id = '.$id)){
			$info = json_decode($issue['service_info'], true);
			$info[uniqid('adf_')] = [
				'name' => $_POST['name'],
				'price' => floatval($_POST['price']),
				'type' => $_POST['type'],
				'currency' => 'USD',
				'req' => 0,
				'quantity' => intval($_POST['quantity'])
			];
			db_query('UPDATE `'.DB_PREFIX.'_issues` SET service_info = \''.db_escape_string(json_encode($info)).'\' WHERE id = '.intval($_POST['id']));
			echo 'OK';
		}
		die;
	break;
	
	/*
	*  Send price
	*/
	case 'send_price': 
		is_ajax() or die('Hacking attempt!');
		$data = json_decode($_POST['value'], true);
		$type = text_filter($_POST['type'], 20, false);
		$item_id = $_POST['pur'];

		//if (floatval($data[$item_id]['price']) < floatval($_POST['old']))
		//	die('less');
		
		if ($type == 'inventory_info') {
			$inv = db_multi_query('SELECT quantity FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$item_id);
			if ($inv['quantity'] == 1) 
				db_query('UPDATE `'.DB_PREFIX.'_inventory` SET price = \''.floatval($data[$item_id]['price']).'\' WHERE id = '.$item_id);
		}

		if ($type == 'purchase_info' && $item_id > 0) {
			db_query('UPDATE `'.DB_PREFIX.'_purchases` SET sale = \''.floatval($data[$item_id]['price']).'\' WHERE id = '.$item_id);
		}
		
		// SQL SET
		db_query(
			'UPDATE `'.DB_PREFIX.'_issues` SET '.$type.' = \''.$_POST['value'].'\'
				WHERE id = '.intval($_POST['id'])
			);
		die('OK');
	break;
	
	case 'approve_publish':
		if($user['archive_job_approve'])
			db_query('UPDATE `'.DB_PREFIX.'_work_archive` SET confirm = 1 WHERE id = '.intval($_POST['id']));
	break;
	
	/*
	* Send job publication to website
	*/
	case 'send_req_pub':
		$id = (int)$_POST['id'];
		$issue_id = (int)$_POST['issue_id'];
		if(!$id OR $user['archive_job_edit']){
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_work_archive` SET
					device = \''.text_filter($_POST['device'], 255, false).'\',
					title = \''.text_filter($_POST['title'], 165, false).'\',
					description = \''.text_filter($_POST['description'], 255, false).'\',
					keywords = \''.text_filter($_POST['keywords'], 1000, false).'\',
					services = \''.db_escape_string(json_encode($_POST['service'])).'\',
					content = \''.db_escape_string($_POST['content']).'\'
					'.(
				$id ? ' WHERE id = '.$id : ', staff_id = '.$user['id'].', issue_id = '.$issue_id
			));
			
			$id = $id ?: intval(mysqli_insert_id($db_link));
			
			if(!$_POST['id']) db_query('UPDATE `'.DB_PREFIX.'_issues` SET publish = 1 WHERE id = '.$issue_id);
			
			if($_FILES){
				
				// Upload max file size
				$max_size = 10;
				
				// path
				$dir = ROOT_DIR.'/uploads/images/work_archive/';
				
				// Is not dir
				if(!is_dir($dir.$id)){
					@mkdir($dir.$id, 0777);
					@chmod($dir.$id, 0777);
				}
				
				$dir = $dir.$id.'/';
				
				// temp file
				$tmp = $_FILES['image']['tmp_name'];
				
				$type = mb_strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
				
				// Check
				if ($_FILES['image']) {
					if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array($type, ['jpeg', 'jpg', 'png', 'gif'])){
						echo 'err_image_type';
						die;
					}
					if($_FILES['image'] AND $_FILES['image']['size'] >= 1024*$max_size*1024){
						echo 'err_file_size';
						die;
					}
					
				}
				
				// New name
				$rename = uniqid('', true).'.'.$type;
				
				// Upload image
				if(move_uploaded_file($tmp, $dir.$rename)){
					
					$img = new Imagick($dir.$rename);
					
					// 1920
					if($img->getImageHeight() > 400){
						$img->resizeImage(0, 400, imagick::FILTER_LANCZOS, 0.9);
						auto_rotate_image($img);
						$img->stripImage();
						$img->writeImage($dir.$rename);
					}
					
					// 94x94
					$img->cropThumbnailImage(135, 135);
					auto_rotate_image($img);
					$img->stripImage();
					$img->writeImage($dir.'thumb_'.$rename);
					$img->destroy();
					
					db_query('UPDATE `'.DB_PREFIX.'_work_archive` SET image = \''.$rename.'\' WHERE id = '.$id);
				}
			}
			echo 'OK';
		}
		die;
	break;
	
	/*
	* Job publication to website
	*/
	case 'publish':
		if($user['archive_job_view']){
			$id = (int)$route[2];
			if($row = db_multi_query('SELECT a.*, CONCAT(u.name, \' \', u.lastname) as author_name FROM `'.DB_PREFIX.'_work_archive` a INNER JOIN `'.DB_PREFIX.'_users` u ON a.staff_id = u.id WHERE a.id = '.$id)){
				$meta['title'] = 'Publish to website #'.$row['id'];
				$services = '';
				if($row['services']){
					foreach(json_decode($row['services'], 1) as $service){
						$services .= '<input type="text" name="service[]" value="'.$service.'"'.(
							$services ? ' style="margin-top: 10px;"' : ''
						).'>';
					}
				}
				tpl_set('issues/pub', [
					'header' => $meta['title'],
					'title' => $row['title'],
					'author-id' => $row['staff_id'],
					'author-name' => $row['author_name'],
					'descr' => $row['description'],
					'keywords' => $row['keywords'],
					'services' => $services,
					'device' => $row['device'],
					'image' => $row['image'],
					'content' => $row['content'],
					'issue-id' => $row['issue_id'],
					'id' => $id
				], [
					'new' => $row['id'] && !$row['confirm'] && $user['archive_job_approve'],
					'author' => true,
					'edit' => $user['archive_job_edit'],
					'image' => $row['image'],
					'view' => true,
					'confirmed' => true,
					'invoice' => true,
					'inventories' => $inventories,
					'services' => $services
				], 'content');
			}
		}
	break;
	
	/*
	* Job publication to website
	*/
	case 'req_pub':
		$id = (int)$route[2];
		if($row = db_multi_query('SELECT 
					tb1.*,
					CONCAT(tb3.name, \' \', tb5.name, \' \', tb4.name, \' \', tb2.model) as device
					FROM `'.DB_PREFIX.'_issues` tb1 
					INNER JOIN `'.DB_PREFIX.'_inventory` tb2 ON tb1.inventory_id = tb2.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_types`
						tb3 ON tb2.type_id = tb3.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_models`
						tb4 ON tb2.model_id = tb4.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb5
						ON tb5.id = tb2.category_id
					WHERE tb1.id = '.$id.' AND tb1.finished = 1'
		)){
			$meta['title'] = 'Request publish to website';
			$services = '';
			if($row['service_info']){
				foreach(json_decode($row['service_info'], 1) as $service){
					$services .= '<input type="text" name="service[]" value="'.$service['name'].'"'.(
						$services ? ' style="margin-top: 10px;"' : ''
					).'>';
				}
			}
			$inventories = '';
			if($row['inventory_info']){
				foreach(json_decode($row['inventory_info'], 1) as $service){
					$inventories .= '<input type="text" name="inventory[]" value="'.$service['name'].'"'.(
						$inventories ? ' style="margin-top: 10px;"' : ''
					).'>';
				}
			}
			if($row['purchase_info']){
				foreach(json_decode($row['purchase_info'], 1) as $service){
					$inventories .= '<input type="text" name="inventory[]" value="'.$service['name'].'"'.(
						$inventories ? ' style="margin-top: 10px;"' : ''
					).'>';
				}
			}
			tpl_set('issues/pub', [
				'header' => $meta['title'],
				'title' => $row['device'].(
					$row['description'] ? ' - '.$row['description'] : ''
				),
				'descr' => $row['description'],
				'keywords' => '',
				'services' => $services,
				'device' => $row['device'],
				'content' => '',
				'id' => 0,
				'issue-id' => $id
			], [
				'edit' => true,
				'new' => false,
				'image' => false,
				'author' => false,
				'view' => true,
				'confirmed' => true,
				'invoice' => true,
				'inventories' => $inventories,
				'services' => $services
			], 'content');	
		}
	break;
	
	/*
	* All issues
	*/
	case 'archive':
		if($user['archive_job_view']){
			$meta['title'] = 'Archive job';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS a.*, CONCAT(u.name, \' \', u.lastname) as author_name FROM 
					`'.DB_PREFIX.'_work_archive` a
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON a.staff_id = u.id WHERE 1 '.(
				$query ? 'AND a.description LIKE \'%'.$query.'%\' ' : ''
			).'ORDER BY a.date DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('issues/archive_item', [
						'id' => $row['id'],
						'issue' => $row['description'],
						'author-id' => $row['author_id'],
						'author-name' => $row['author_name'],
						'title' => $row['title'],
						'image' => $row['image'],
						'device' => $row['device'],
						'date' => $row['date']
					], [
						'new' => !$row['confirm']
					], 'archive-job');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['archive-job'],
				]));
			}
			tpl_set('issues/archive_main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'archive-job' => $tpl_content['archive-job']
			], [
				'edit' => $user['edit_issues'],
				'add' => $user['add_issues']
			], 'content');
		}
	break;
	
	/*
	* All issues
	*/
	default:
		$meta['title'] = $lang['allIssues'];
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS i.id, i.description, i.customer_id, i.date, i.inventory_id, c.name as cname, c.lastname as clastname, t.model FROM 
				`'.DB_PREFIX.'_issues` i
			INNER JOIN `'.DB_PREFIX.'_users` c
				ON i.customer_id = c.id
			INNER JOIN `'.DB_PREFIX.'_inventory` t
				ON i.inventory_id = t.id WHERE i.status_id NOT IN(2,22) '.(
			$query ? 'AND i.description LIKE \'%'.$query.'%\' ' : ''
		).'ORDER BY i.date DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('issues/new_item', [
					'id' => $row['id'],
					'customer-id' => $row['customer_id'],
					'customer-name' => $row['cname'],
					'customer-lastname' => $row['clastname'],
					'date' => $row['date'],
					'inventory-id' => $row['inventory_id'],
					'inventory' => $row['model'],
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
				'content' => $tpl_content['issues'],
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