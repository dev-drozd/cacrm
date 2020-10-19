<?php
/**
 * @appointment Salary Page
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

defined('ENGINE') or ('hacking attempt!');
 
if ($user['salary']) {
	switch($route[1]){
		/*
		* Time forfeit
		*/
		case 'time_forfeit':
			$query = text_filter($_REQUEST['query'], 255, false);
			$staff = intval($_REQUEST['staff']);		
			$date_start = text_filter($_REQUEST['date_start'], 30, true);
			$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
			$page = intval($_REQUEST['page']);
			$count = 50;

			$sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS 
					t.*,
					u.name,
					u.lastname,
					u.image
					FROM `'.DB_PREFIX.'_users_time_forfeit` t
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = t.user_id
					WHERE 1 '.(
						$staff ? ' AND t.user_id = '.$staff.' ' : ''
					).(
						$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
					).(
						($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
					ORDER BY t.id DESC LIMIT '.($page*$count).', '.$count, true);
		
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);

			$i = 0;
			foreach($sql as $row){
				$total = $row['forfeit'];
				$s = $total % 60;
				$m = ($total % 3600 - $s) / 60;
				$h = ($total - $s - $m * 60) / 3600;
				tpl_set('salary/time_forfeit/item', [
					'user-id' => $row['user_id'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'image' => $row['image'],
					'date' => $row['date'],
					'forfeit' => $h.':'.$m.':'.$s,
				], [
					'ava' => $row['image']
				], 'forfeit');
				$i++;
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'Salary';
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' =>  $tpl_content['forfeit'],
				]));
			}
			tpl_set('salary/time_forfeit/main', [
				'uid' => $user['id'],
				'query' => $query,
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'forfeit' => $tpl_content['forfeit']
			], [
			], 'content');
		break;
		
        /*
        * Edit points
        */
        case 'edit_points':
            is_ajax() or die('Hacking attempt!');
            $id = intval($_POST['id']);
            $value = floatval($_POST['value']);
            $oldValue = floatval($_POST['oldValue']);

            if (!in_to_array(1, $user['group_ids']))
                die('no_acc');

            db_query('
                UPDATE `'.DB_PREFIX.'_inventory_status_history` SET
                    point = '.$value.'
                WHERE id = '.$id
            );
            
            $uid = db_multi_query('
                SELECT 
                    staff_id 
                FROM `'.DB_PREFIX.'_inventory_status_history`
                WHERE id = '.$id);

            db_query('
                UPDATE `'.DB_PREFIX.'_users` SET
                    points = points + '.($value - $oldValue).'
                WHERE id = '.$uid['staff_id']
            );
            die('OK');
        break;

        /*
        * Points statistics
        */
		case 'points_stat':
			$query = text_filter($_POST['query'], 255, false);
			$staff = intval($_POST['staff']);		
			$object = intval($_POST['object']);
			$date_start = text_filter($_POST['date_start'], 30, true);
			$type = text_filter($_POST['type'], 50, true);
			$date_finish = text_filter($_POST['date_finish'], 30, true);
			$page = intval($_POST['page']);
			$count = 50;

			$sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
					sh.*,
					u.name,
					u.lastname,
					u.image,
					o.name as object,
					s.name as status,
					sw.name as wstatus,
					cs.name as cstatus
					FROM `'.DB_PREFIX.'_inventory_status_history` sh
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = sh.staff_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = sh.object_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON s.id = sh.status_id AND sh.warranty = 0
					LEFT JOIN `'.DB_PREFIX.'_users_writeup` w
						ON w.id = sh.writeup_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_warranty_status` sw
						ON sw.id = sh.status_id AND sh.warranty = 1
					LEFT JOIN `'.DB_PREFIX.'_camera_status` cs
						ON cs.id = sh.status_id AND sh.action = \'camera_status\'
					WHERE u.del = 0 AND IF(sh.action = \'sell_inventory\', 1, sh.point != 0) AND sh.percent = 0 '.(
						$object ? ' AND sh.object_id = \''.$object.'\'' : ''
					).(
						$staff ? ' AND sh.staff_id = '.$staff.' ' : ''
					).(
						$type ? ' AND sh.action = \''.$type.'\' ' : ''
					).(
						$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
					).(
						($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
					ORDER BY sh.date DESC LIMIT '.($page*$count).', '.$count, true);
		
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			$actions = '';
			if ($action = db_multi_query('SELECT DISTINCT action FROM `'.DB_PREFIX.'_inventory_status_history`', true)) {
				foreach($action as $act) {
					$actions .= '<option value="'.$act['action'].'">'.str_replace('_', ' ', $act['action']).'</option>';
				}
			}

			$i = 0;
			foreach($sql as $row){
				$total = $row['seconds'];
				$s = $total % 60;
				$m = ($total % 3600 - $s) / 60;
				$h = ($total - $s - $m * 60) / 3600;
				
				$action = '';
				switch($row['action']) {
					case 'update_status':
						$action = '<a href="/issues/view/'.$row['issue_id'].'" target="_blank">'.($row['warranty'] ? 'Warranty: '.$row['wstatus'] : ($row['status'] ?: 'update status')).'</a>';
					break;
					
					case 'new_user':
						if ($row['invoice_id'])
							$action = '<a href="/users/view/'.$row['user_id'].'" target="_blank">'.str_replace('_', ' ', $row['action']).'</a>';
						else 
							$action = str_replace('_', ' ', $row['action']);
					break;
					
					case 'make_transaction':
						if ($row['invoice_id'])
							$action = '<a href="/invoices/view/'.$row['invoice_id'].'" target="_blank">made transaction</a>';
						else 
							$action = 'made transaction';
					break;
					
					case 'trade_in':
					case 'trade_in_selling':
						if ($row['inventory_id'])
							$action = '<a href="/inventory/view/'.$row['inventory_id'].'" target="_blank">'.str_replace('_', ' ', $row['action']).'</a>';
						else 
							$action = str_replace('_', ' ', $row['action']);
					break;
					
					case 'new_service':
					case 'new_inventory':
					case 'sell_inventory':
						if ($row['inventory_id'])
							$action = '<a href="/inventory/'.($row['action'] == 'new_service' ? '/edit/service/' : 'view/').$row['inventory_id'].'" target="_blank">'.($row['action'] == 'sell_inventory' ? 'sold inventory' : str_replace('_', ' ', $row['action'])).'</a>';
						else 
							$action = ($row['action'] == 'sell_inventory' ? 'sold inventory' : str_replace('_', ' ', $row['action']));
					break;
					
					case 'new_purchase':
					case 'return_purchase':
						if ($row['purchase_id'])
							$action = '<a href="/purchase/edit/'.$row['purchase_id'].'" target="_blank">'.str_replace('_', ' ', $row['action']).'</a>';
						else 
							$action = str_replace('_', ' ', $row['action']);
					break;
					
					case 'issue_forfeit':
						$action = '<a href="/issues/view/'.$row['issue_id'].'" target="_blank">'.str_replace('_', ' ', $row['action']).'</a>';
					break;
					
					case 'camera_status':
						$action = str_replace('_', ' ', $row['action']).': '.$row['cstatus'];
					break;
					
					case 'write_up':
						$action = ($row['suspention'] ? 'Suspention' : str_replace('_', ' ', $row['action'])).': '.$row['writeup_up'].'<br><i>'.$row['comment'].'</i>';
					break;
					
					default:
						$action = str_replace('_', ' ', $row['action']);
					break;
				}

				tpl_set('salary/points_stat/item', [
					'id' => $row['id'],
					'user-id' => $row['staff_id'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'image' => $row['image'],
					'points' => number_format($row['point'], 2, '.', ''),
					'date' => $row['date'],
					'object' => $row['object'],
					'action' => $action,
				], [
					'ava' => $row['image'],
					'rate' => $row['action'] != 'store_points',
                    'owner' => in_to_array(1, $user['group_ids'])
				], 'salary');
				$i++;
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'Salary';
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' =>  $tpl_content['salary'],
				]));
			}
			tpl_set('salary/points_stat/main', [
				'uid' => $user['id'],
				'types' => $actions,
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'timers' => $tpl_content['salary']
			], [
			], 'content');
		break;
		
        /*
        * Points
        */
		case 'points':
			if ($route[2] == 'history') {
				$query = text_filter($_REQUEST['query'], 255, false);
				$staff = intval($_REQUEST['staff']);		
				$date_start = text_filter($_REQUEST['date_start'], 30, true);
				$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
				$page = intval($_REQUEST['page']);
				$count = 50;

				$sql = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS 
						h.*,
						u.name,
						u.lastname,
						u.image,
						pu.name as pname,
						pu.lastname as plastname,
						pu.image as pimage
						FROM `'.DB_PREFIX.'_salary_points_history` h
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = h.staff_id
						LEFT JOIN `'.DB_PREFIX.'_users` pu
							ON pu.id = h.payroll_id
						WHERE u.del = 0 '.(
							$staff ? ' AND h.staff_id = '.$staff.' ' : ''
						).(
							$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
						).(
							($date_start AND $date_finish) ? ' AND h.date >= CAST(\''.$date_start.'\' AS DATE) AND h.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						ORDER BY h.id DESC LIMIT '.($page*$count).', '.$count, true);
			
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);

				$i = 0;
				foreach($sql as $row){
					$total = $row['seconds'];
					$s = $total % 60;
					$m = ($total % 3600 - $s) / 60;
					$h = ($total - $s - $m * 60) / 3600;
					tpl_set('salary/points/history/item', [
						'user-id' => $row['staff_id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'puser-id' => $row['payroll_id'],
						'pname' => $row['pname'],
						'plastname' => $row['plastname'],
						'pimage' => $row['pimage'],
						'amount' => $row['amount'],
						'points' => number_format($row['points'], 2, '.', ''),
						'date' => $row['date'],
					], [
						'ava' => $row['image'],
						'pava' => $row['pimage'],
					], 'salary');
					$i++;
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				$meta['title'] = 'Salary';
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' =>  $tpl_content['salary'],
					]));
				}
				tpl_set('salary/points/history/main', [
					'uid' => $user['id'],
					'query' => $query,
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'timers' => $tpl_content['salary']
				], [
				], 'content');
			} elseif ($route[2] == 'analytics') {
				$staff = ids_filter($_REQUEST['staff']);		
				$object = intval($_REQUEST['object']);		
				$date_start = text_filter($_REQUEST['date_start'], 30, true);
				$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
				$page = intval($_REQUEST['page']);
				$count = 50;
				
				$sql = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS 
						SUM(sh.point) as points,
						sh.staff_id,
						sh.object_id,
						u.name,
						u.lastname,
						u.image,
						u.points as current,
						o.points_equal
						FROM `'.DB_PREFIX.'_users` u
						LEFT JOIN `'.DB_PREFIX.'_inventory_status_history` sh
							ON sh.staff_id = u.id
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = sh.object_id
						WHERE u.del = 0 AND sh.action != \'store_points\' AND sh.percent = 0 '.(
							$object ? ' AND sh.object_id = \''.$object.'\'' : ''
						).(
							$staff ? ' AND sh.staff_id IN ('.$staff.') ' : ''
						).(
							$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
						).(
							($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						GROUP BY sh.staff_id ORDER BY points DESC LIMIT '.($page*$count).', '.$count, true);
			
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				
				$o = db_multi_query('SELECT
						u.id,
						t.object_id,
						SUM(t.seconds) as seconds
					FROM `'.DB_PREFIX.'_users` u
					LEFT JOIN `'.DB_PREFIX.'_timer` t
						ON t.user_id = u.id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = t.object_id
					WHERE u.del = 0 '.(
						$object ? ' AND t.object_id = \''.$object.'\'' : ''
					).(
						$staff ? ' AND u.id IN ('.$staff.') ' : ''
					).(
						$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
					).(
						($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
					GROUP BY u.id LIMIT '.($page*$count).', '.$count, true);
					
				$sp = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS 
						u.id,
						SUM(sh.point) as points,
						sh.staff_id,
						sh.object_id
						FROM `'.DB_PREFIX.'_users` u
						LEFT JOIN `'.DB_PREFIX.'_inventory_status_history` sh
							ON sh.staff_id = u.id

						WHERE u.del = 0 AND sh.action = \'store_points\' AND sh.percent = 0 '.(
							$object ? ' AND sh.object_id = \''.$object.'\'' : ''
						).(
							$staff ? ' AND sh.staff_id IN ('.$staff.') ' : ''
						).(
							$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
						).(
							($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						GROUP BY sh.staff_id ORDER BY points DESC LIMIT '.($page*$count).', '.$count, true);
						
				$i = 0;
				foreach($sql as $row){
					$user_id = $row['staff_id'];
					$object_id = $row['object_id'];
					
					$o_user = array_values(array_filter($o, function($v) use(&$user_id, &$object_id) {
						if ($v['id'] == $user_id AND ($object ? $v['object_id'] == $object_id : 1))
							return $v;
					}, ARRAY_FILTER_USE_BOTH));
					
					$sp_user = array_values(array_filter($sp, function($v) use(&$user_id, &$object_id) {
						if ($v['id'] == $user_id AND ($object ? $v['object_id'] == $object_id : 1))
							return $v;
					}, ARRAY_FILTER_USE_BOTH));
					
					$total = $o_user[0]['seconds'];
					$s = $total % 60;
					$m = ($total % 3600 - $s) / 60;
					$h = ($total - $s - $m * 60) / 3600;
					
					$per_hour = ($total > 0 ? $row['points']/$total*3600 : 0);
					tpl_set('salary/points/analytics/item', [
						'id' => $row['id'],
						'user-id' => $row['staff_id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'per_hour' => number_format($per_hour, 2, '.', ''),
						'seconds' => $h.':'.$m.':'.$s,
						'points' => number_format($row['points'], 2, '.', ''),
						'store_points' => number_format(($sp_user[0]['points']*(-1)), 2, '.', ''),
						'total_points' => number_format(($sp_user[0]['points'] + $row['points']), 2, '.', ''),
					], [
						'ava' => $row['image'],
					], 'salary');
					$i++;
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				$meta['title'] = 'Points analytics';
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' =>  $tpl_content['salary'],
					]));
				}
				tpl_set('salary/points/analytics/main', [
					'uid' => $user['id'],
					'query' => $query,
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'timers' => $tpl_content['salary']
				], [
				], 'content');
			} else {
				$query = text_filter($_REQUEST['query'], 255, false);
				$staff = intval($_REQUEST['staff']);		
				$object = intval($_REQUEST['object']);
				$date_start = text_filter($_REQUEST['date_start'], 30, true);
				$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
				$page = intval($_REQUEST['page']);
				$count = 50;

				$sql = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS 
						SUM(sh.point) as points,
						sh.staff_id,
						sh.object_id,
						u.name,
						u.lastname,
						u.image,
						u.points as current,
						o.points_equal
						FROM `'.DB_PREFIX.'_inventory_status_history` sh
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = sh.staff_id
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = sh.object_id
						WHERE u.del = 0 AND sh.percent = 0 '.(
							$object ? ' AND sh.object_id = \''.$object.'\'' : ''
						).(
							$staff ? ' AND sh.staff_id = '.$staff.' ' : ''
						).(
							$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
						).(
							($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						GROUP BY sh.staff_id ORDER BY points DESC LIMIT '.($page*$count).', '.$count, true);
			
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);

				$i = 0;
				foreach($sql as $row){
					$total = $row['seconds'];
					$s = $total % 60;
					$m = ($total % 3600 - $s) / 60;
					$h = ($total - $s - $m * 60) / 3600;
					tpl_set('salary/points/item', [
						'id' => $row['id'],
						'user-id' => $row['staff_id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'points' => number_format($row['points'] ? ($row['points'] - $row['current']) : $row['points'], 2, '.', ''),
						'current' => $row['current'],
						'money' => number_format($row['current']*$row['points_equal']/100, 2, '.', ''),
					], [
						'ava' => $row['image'],
					], 'salary');
					$i++;
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				$meta['title'] = 'Salary';
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' =>  $tpl_content['salary'],
					]));
				}
				tpl_set('salary/points/main', [
					'uid' => $user['id'],
					'res_count' => $res_count,
					'query' => $query,
					'more' => $left_count ? '' : ' hdn',
					'timers' => $tpl_content['salary']
				], [
				], 'content');
			}
		break;
		
        /*
        * Points payout
        */
		case 'points_payout':
            is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			
			$points = db_multi_query('SELECT points FROM `'.DB_PREFIX.'_users` WHERE id = '.$id);
			db_query('UPDATE `'.DB_PREFIX.'_users` SET points = 0, points_update = \''.date('Y-m-d', time()).'\' WHERE id = '.$id);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_salary_points_history` SET
						payroll_id = '.$user['id'].',
						staff_id = '.$id.',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						points = \''.$points['points'].'\',
						amount = \''.floatval($_POST['amount']).'\'
					');
			die('OK');
		break;
		
        /*
        * Make payment
        */
		case 'make':
            is_ajax() or die('Hacking attempt!');
			$id = intval($_REQUEST['id']);
			$object_id = intval($_REQUEST['object_id']);
			$salary_date = $_REQUEST['lDate'] ?: date('Y-m-d', time());
			$start_date = $_REQUEST['sDate'] ?: 0;
			
			$last_date = db_multi_query('SELECT REGEXP_REPLACE(last_salary, \'{(.*?)"'.$object_id.'":"(.*?)"(.*?)}\', \'\\\2\') as last_date FROM `'.DB_PREFIX.'_users` WHERE id = '.$id);
			if (strtotime($last_date['last_date']) > strtotime($start_date))
				die('DONE');
			
			db_query('UPDATE `'.DB_PREFIX.'_users` SET 
				last_salary = IF(REGEXP_REPLACE(last_salary, \'{(.*?)"'.$object_id.'":"(.*?)"(.*?)}\', \'\\\2\'),
						REGEXP_REPLACE(last_salary, \'{(.*?)"'.$object_id.'":"(.*?)"(.*?)}\', \'{\\\1"'.$object_id.'":"'.$salary_date.'"\\\3}\'),
						IF(
							REGEXP_REPLACE(last_salary, \'{(.*?)}\', \'{\\\1}\'),
							REGEXP_REPLACE(last_salary, \'{(.*?)}\', \'{\\\1,"'.$object_id.'":"'.$salary_date.'"}\'),
							\'{"'.$object_id.'":"'.$salary_date.'"}\'
						)
					)
				WHERE id = '.$id
			);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_salary_history` SET
						booker_id = '.$user['id'].',
						staff_id = '.$id.',
						object_id = '.$object_id.',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						amount = \''.floatval($_REQUEST['amount']).'\'
					');
			die('OK');
		break;
		
        /*
        * Payment history
        */
		case 'history':
			$query = text_filter($_REQUEST['query'], 255, false);
			$staff = intval($_REQUEST['staff']);		
			$object = intval($_REQUEST['object']);
			$date_start = text_filter($_REQUEST['date_start'], 30, true);
			$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
			$page = intval($_REQUEST['page']);
			$count = 50;

			$sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
					s.*,
					u.name,
					u.lastname,
					u.image,
					b.name as bname,
					b.lastname as blastname,
					b.image as bimage,
					o.name as object
				FROM `'.DB_PREFIX.'_salary_history` s
				LEFT JOIN `'.DB_PREFIX.'_users` b
					ON s.booker_id = b.id
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON s.staff_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = s.object_id
				WHERE 1 '.(
				$object ? ' AND o.id = \''.$object.'\'' : ' AND o.id > 0'
			).(
				$staff ? ' AND u.id = '.$staff.' ' : ''
			).(
				$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND b.date >= CAST(\''.$date_start.'\' AS DATE) AND b.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).' ORDER BY id DESC LIMIT '.($page*$count).', '.$count, true);
			
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			$i = 0;
			foreach($sql as $row){
				tpl_set('salary/history/item', [
					'id' => $row['id'],
					'staff-id' => $row['staff_id'],
					'booker-id' => $row['booker_id'],
					'store' => $row['object'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'image' => $row['image'],
					'bname' => $row['bname'],
					'blastname' => $row['blastname'],
					'bimage' => $row['bimage'],
					'date' => $row['date'],
					'amount' => '$'.number_format($row['amount'], 2, '.', ''),
				], [
					'ava' => $row['image'],
					'bava' => $row['bimage'],
				], 'salary');
				$i++;
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'Salary';
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' =>  $tpl_content['salary'],
				]));
			}
			tpl_set('salary/history/main', [
				'uid' => $user['id'],
				'query' => $query,
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'timers' => $tpl_content['salary']
			], [
				'time-money' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])))
			], 'content');
		break;
		
        /*
        * Salary
        */
		default:
			$query = text_filter($_REQUEST['query'], 255, false);
			$staff = intval($_REQUEST['staff']);		
			$object = intval($_REQUEST['object']);
			$date_start = text_filter($_REQUEST['date_start'], 30, true);
			$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
			$page = intval($_REQUEST['page']);
			$count = 50;
			
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
					DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY) as date
				FROM `'.DB_PREFIX.'_users` u
				INNER JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id
				WHERE t.event = \'stop\' AND u.id NOT IN(17, 16, 2)'.(
				$object ? ' AND o.id = \''.$object.'\'' : ' AND o.id > 0'
			).(
				$staff ? ' AND u.id = '.$staff.' ' : ''
			).(
				$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND t.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
			).' GROUP BY t.user_id, t.object_id ORDER BY t.user_id, o.id LIMIT '.($page*$count).', '.$count, true);
			
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			$o = db_multi_query('
					SELECT SUM(o.paid) as paid,
					o.staff_id,
					o.object_id
					FROM `'.DB_PREFIX.'_users_onsite_changelog` o
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = o.staff_id
					WHERE u.id NOT IN(17, 16, 2)'.(
						$object ? ' AND o.object_id = \''.$object.'\'' : ' AND o.object_id > 0'
					).(
						$staff ? ' AND o.staff_id = '.$staff.' ' : ''
					).(
						$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
					).(
						($date_start AND $date_finish) ? ' AND o.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND o.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND o.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
					).'
					GROUP BY o.staff_id, o.object_id LIMIT '.($page*$count).', '.$count, true);
					
					
			$sql_total = db_multi_query('SELECT
					SUM(t.seconds / 3600 * u.pay * (IF(o.salary_tax, ((100 + o.salary_tax) / 100), 1))) as total
				FROM `'.DB_PREFIX.'_users` u
				INNER JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id
				WHERE t.event = \'stop\' AND u.id NOT IN(17, 16, 2)'.(
				$object ? ' AND o.id = \''.$object.'\'' : ' AND o.id > 0'
			).(
				$staff ? ' AND u.id = '.$staff.' ' : ''
			).(
				$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND t.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
			));	

			$o_total = db_multi_query('
				SELECT 
					SUM(o.paid * IF(s.salary_tax, ((100 + s.salary_tax) / 100), 1)) as total
				FROM `'.DB_PREFIX.'_users_onsite_changelog` o
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = o.staff_id
				LEFT JOIN `'.DB_PREFIX.'_objects` s
					ON s.id = o.object_id
				WHERE u.id NOT IN(17, 16, 2)'.(
					$object ? ' AND o.object_id = \''.$object.'\'' : ' AND o.object_id > 0'
				).(
					$staff ? ' AND o.staff_id = '.$staff.' ' : ''
				).(
					$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
				).(
					($date_start AND $date_finish) ? ' AND o.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND o.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND o.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
				)
			);
		
			$i = 0;
			foreach($sql as $row){
				$user_id = $row['user_id'];
				$object_id = $row['object_id'];
				
				$o_user = array_values(array_filter($o, function($v) use(&$user_id, &$object_id) {
					if ($v['staff_id'] == $user_id AND $v['object_id'] == $object_id)
						return $v;
				}, ARRAY_FILTER_USE_BOTH));
				
				$total = $row['seconds'];
				$s = $total % 60;
				$m = ($total % 3600 - $s) / 60;
				$h = ($total - $s - $m * 60) / 3600;
				tpl_set('salary/item', [
					'id' => $row['id'],
					'user-id' => $row['user_id'],
					'object-id' => $row['object_id'],
					'store' => $row['object'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'image' => $row['image'],
					//'points' => ($p_user[0]['points'] ? $p_user[0]['points'].' / $'.number_format($p_user[0]['points'] * $row['points_equal'] / 100, 2, '.', '') : 0),
					'date' => $date_start ? $date_start.' - '.$date_finish : $row['date'], //$row['last_salary'],
					'onsite' => '$'.number_format($o_user[0]['paid'], 2, '.', '').' / $'.number_format($o_user[0]['paid']*((100 + $row['salary'])/100), 2, '.', ''),
					'seconds' => $h.':'.$m.':'.$s,
					'total' => number_format(($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $row['salary'])/100)), 2, '.', ''),
					'pay' => number_format($row['seconds']/3600*$row['pay'], 2, '.', '').($row['salary'] ? '/$'.number_format($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100), 2, '.', '') : '')
				], [
					'ava' => $row['image'],
				], 'salary');
				$i++;
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'Salary';
			unset($sql);
			unset($sql_total);
			unset($o_total);
			unset($o);
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' =>  $tpl_content['salary'],
					'total' => number_format($sql_total['total'] + $o_total['total'], 2, '.', '')
				]));
			}
			tpl_set('salary/main', [
				'uid' => $user['id'],
				'query' => $query,
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'timers' => $tpl_content['salary'],
				'total' => number_format($sql_total['total'] + $o_total['total'], 2, '.', '')
			], [
				'time-money' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])))
			], 'content');
		break;
	}
} else {
	tpl_set('forbidden', [
		'text' => 'You have no access to this page'
	], [
	], 'content');
}