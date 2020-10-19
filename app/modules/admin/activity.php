<?php
/**
 * @appointment Activity admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$objects_ip = array_flip($config['object_ips']);
 
switch($route[1]){
	
	/*
	*  Del
	*/
	case 'del':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_camera_activity']){
			db_query('DELETE FROM `'.DB_PREFIX.'_activity` WHERE camera = 1 AND id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('no_acc');
	break;
	
	/*
	* Appointments
	*/
	case 'appointments':
		$object = intval($_REQUEST['object']);
		$date_start = text_filter($_GET['sDate'] ?: $_POST['date_start'], 30, true);
		$date_finish = text_filter($_GET['eDate'] ?: $_POST['date_finish'], 30, true);
		$staff = intval($_REQUEST['staff']);
		$status = text_filter($_REQUEST['status'], 8, false);
		$page = intval($_POST['page']);
		$count = 20;
		
		if($sql = db_multi_query('SELECT 
				a.*,
				u.name as customer_name,
				u.lastname as customer_lastname,
				s.name as staff_name,
				s.lastname as staff_lastname,
				o.name as object_name
			FROM `'.DB_PREFIX.'_users_appointments` a
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id = a.customer_id
			LEFT JOIN `'.DB_PREFIX.'_users` s
				ON s.id = a.staff_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = a.object_id
			WHERE 1 '.(
				$status ? ($status == 'accept' ? ' AND a.confirmed = 1 ' : ' AND a.confirmed = 0 ') : ''
			).(
				$staff ? 'AND a.staff_id = '.$staff.' ' : ''
			).(
				$object ? 'AND a.object_id = '.$object.' ' : ''
			).(
				($date_start AND $date_finish) ? ' AND a.date >= CAST(\''.$date_start.'\' AS DATE) AND a.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'ORDER BY a.date DESC LIMIT '.($page*$count).', '.$count, true
			)) {

			$i = 0;
			foreach($sql as $row){
				tpl_set('activity/appointments/item', [
					'id' => $row['id'],
					'cid' => $row['customer_id'],
					'cname' => $row['customer_name'],
					'clastname' => $row['customer_name'],
					'sid' => $row['staff_id'],
					'sname' => $row['staff_name'],
					'slastname' => $row['staff_name'],
					'date' => $row['date'],
					'object' => $row['object_name'],
				], [
					'confirmed' => $row['confirmed']
				], 'appointments');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}

		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = 'Appointments';
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['appointments'],
			]));
		}
		tpl_set('activity/appointments/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'appointments' => $tpl_content['appointments']
		], [], 'content');
	break;
	
	/*
	* Onsite detail statictics
	*/
	case 'onsite_details':
		$object = intval($_POST['object']);
		$query = text_filter($_POST['query'], 255, false);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$staff = intval($_POST['staff']);
		$page = intval($_POST['page']);
		$payment = text_filter($_POST['payment'], 30, true);
		$count = 20;
		
		$onsite_html = '';
		
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				oc.id,
				oc.event,
				oc.date,
				oc.note,
				s.name as service_name,
				u.name as uname,
				u.lastname as ulastname,
				cu.name as cuname,
				cu.lastname as culastname
			FROM `'.DB_PREFIX.'_users_onsite_changelog` oc
			LEFT JOIN `'.DB_PREFIX.'_users_onsite` o
				ON o.id = oc.onsite_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` s
				ON s.id = o.onsite_id
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id = oc.staff_id
			LEFT JOIN `'.DB_PREFIX.'_users` cu
				ON cu.id = o.customer_id
			WHERE 1 '.(
				$query ? ' AND s.name LIKE \'%'.$query.'%\'' : ''
			).(
				$staff ? ' AND oc.staff_id = '.$staff : ''
			).(
				$object ? ' AND oc.object_id = '.$object : ''
			).(
				($date_start AND $date_finish) ? ' AND oc.date >= CAST(\''.$date_start.'\' AS DATE) AND oc.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'
			ORDER BY oc.id DESC
			LIMIT '.($page*$count).', '.$count, true)){
			foreach($sql as $row) {
				$onsite_html .= '<div class="tr" id="onsite_'.$row['id'].'">
									<div class="td"><span class="thShort">Service: </span>'.$row['service_name'].'</div>
									<div class="td"><span class="thShort">Customer: </span>'.$row['cuname'].' '.$row['culastname'].'</div>
									<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
									<div class="td"><span class="thShort">Event: </span>'.$row['event'].'</div>
									<div class="td" style="max-width: 300px"><span class="thShort">Note: </span>'.$row['note'].'</div>
									<div class="td"><span class="thShort">Staff: </span>'.$row['uname'].' '.$row['ulastname'].'</div>
								</div>';
				$i++;
			}
		}	
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		$left_count = intval(($res_count-($page*$count)-$i));
		
		$meta['title'] = $lang['allonsite'];
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' =>  $onsite_html,
			]));
		}
		tpl_set('activity/onsite_details/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'onsite' => $onsite_html
		], [], 'content');
	break;
	
	/*
	* Onsite statictics
	*/
	case 'onsite':
		$object = intval($_POST['object']);
		$query = text_filter($_POST['query'], 255, false);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$staff = intval($_POST['staff']);
		$page = intval($_POST['page']);
		$payment = text_filter($_POST['payment'], 30, true);
		$count = 20;
		
		$onsite_html = '';
		
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				o.*,
				SEC_TO_TIME(TIMESTAMPDIFF(SECOND, \''.date('Y-m-d H:i:s', time()).'\', o.date_end)) as date_left,
				s.name as service_name,
				s.type,
				s.price,
				s.calls as service_calls,
				s.time as service_time,
				s.currency,
				u.name as uname,
				u.lastname as ulastname,
				cu.name as cuname,
				cu.lastname as culastname,
				DATE_FORMAT(o.selected_time, \'%H:%i\') as time_service
			FROM `'.DB_PREFIX.'_users_onsite` o
			LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` s
				ON s.id = o.onsite_id
			LEFT JOIN `'.DB_PREFIX.'_invoices` i
				ON o.id = i.onsite_id
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id = o.selected_staff_id
			LEFT JOIN `'.DB_PREFIX.'_users` cu
				ON cu.id = o.customer_id
			WHERE 1  '.(
				$query ? ' AND s.name LIKE \'%'.$query.'%\'' : ''
			).(
				$staff ? ' AND o.selected_staff_id = '.$staff : ''
			).(
				$object ? ' AND o.last_object = '.$object : ''
			).(
				$payment ? ($payment == 'paid' ? ' AND i.conducted = 1 ' : 'AND i.conducted = 0 ') : ''
			).(
				($date_start AND $date_finish) ? ' AND o.date >= CAST(\''.$date_start.'\' AS DATE) AND o.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'
			ORDER BY o.id DESC
			LIMIT '.($page*$count).', '.$count, true)){
			foreach($sql as $row) {
				$onsite_html .= '<div class="tr" id="drop_'.$row['id'].'">
									<div class="td"><span class="thShort">ID: </span>'.$row['id'].'</div>
									<div class="td"><span class="thShort">Service: </span>'.$row['service_name'].'</div>
									<div class="td"><span class="thShort">Customer: </span>'.$row['cuname'].' '.$row['culastname'].'</div>
									<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
									<div class="td"><span class="thShort">Price: </span>'.$config['currency'][$row['currency']]['symbol'].number_format($row['price'], 2, '.', '').'</div>
									<div class="td"><span class="thShort">Staff: </span>'.$row['uname'].' '.$row['ulastname'].'</div>
								</div>';
				$i++;
			}
		}	
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		$left_count = intval(($res_count-($page*$count)-$i));
		
		$meta['title'] = $lang['All'].' '.$lang['onsiteStat'];
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' =>  $onsite_html,
			]));
		}
		tpl_set('activity/onsite/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'onsite' => $onsite_html
		], [], 'content');
	break;
	
	/*
	* Cash statictics
	*/
	case 'cash':
		$object = intval($_POST['object']);
		$query = text_filter($_POST['query'], 255, false);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$type = text_filter($_POST['type'], 255, false);
		$action = text_filter($_POST['action'], 255, false);
		$status = text_filter($_POST['status'], 255, false);
		$staff = intval($_POST['staff']);
		$page = intval($_POST['page']);
		$count = 100;
		
		if($sql = db_multi_query('
			SELECT 
				SQL_CALC_FOUND_ROWS c.*,
				cr.system as cr_system,
				cr.lack as cr_lack,
				cr.type as cr_type,
				DATE(c.date) as ddate,
				u.name,
				u.lastname,
				o.name as object
			FROM `'.DB_PREFIX.'_cash` c
			LEFT JOIN `'.DB_PREFIX.'_cash` cr
					ON cr.object_id  = c.object_id AND DATE(cr.date) = DATE(c.date) AND cr.type = \'credit\' AND cr.action = \'open\'
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id = c.user_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = c.object_id
			WHERE c.type = \'cash\' '.(
				!in_array(1, explode(',', $user['group_ids'])) ? 
					'AND (FIND_IN_SET('.$user['id'].', o.managers) OR FIND_IN_SET('.$user['id'].', o.staff) OR (o.id = '.($objects_ip[$user['oip']] ?: 0).' OR u.id = '.$user['id'].'))' : ''
			).(
				$object ? 'AND c.object_id = '.$object.' ' : ''
			).(
				$staff ? 'AND c.user_id = '.$staff.' ' : ''
			).(
				$status ? 'AND c.status = \''.$status.'\' ' : ''
			).(
				$type ? 'AND c.type = \''.$type.'\' ' : ''
			).(
				$action ? 'AND c.action = \''.$action.'\' ' : ''
			).(
				($date_start AND $date_finish) ? ' AND c.date >= CAST(\''.$date_start.'\' AS DATE) AND c.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).(
			$query ? 'AND c.date LIKE \'%'.$query.'%\' ' : ''
		).'ORDER BY c.id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			$c = 0;
			foreach($sql as $row) {
				if ($row['type'] == 'cash' AND !$c) {
					$status = $row['action'];
					$c = 1;
				}
				tpl_set('activity/cash/item', [
					'id' => $row['id'],
						'system' => $user['show_amount'] == 1 ? $row['system'] : '',
						'amount' => $user['show_amount'] == 1 ? $row['system'] + $row['lack'] : '',
						'drawer' => $user['show_amount'] == 1 ? $row['amount'] : '',
						'cr-system' => $user['show_amount'] == 1 ? ($row['cr_system'] ?: 0) : '',
						'cr-amount' => $user['show_amount'] == 1 ? $row['cr_system'] + $row['cr_lack'] : '',
						'cr-lack' => $user['show_amount'] == 1 ? $row['cr_lack'] : '',
						'action' => $row['action'],
						'status' => abs($row['lack']) > 0.001 ? $lang['dicline'] : $lang['accept'],
						'object' => $row['object'],
						'object_id' => $row['object_id'],
						'date' => $row['date'],
						'ddate' => $row['ddate'],
						'cr-type' => $row['cr_type'],
						'type' => $row['type'],
						'lack' => $user['show_amount'] == 1 ? $row['lack'] : '',
						'user-id' => $row['user_id'],
						'user-name' => $row['name'],
						'user-lastname' => $row['lastname'],
						'currency' => $config['currency'][$row['currency']]['symbol']
					], [
						'accept' => $row['status'] == 'accept',
						'dicline' => $row['lack'] < -0.001 OR ($row['action'] == 'open' ? $row['cr_lack'] < -0.001 : 0),
						'dicline_more' => $row['lack'] > 0.001 OR ($row['action'] == 'open' ? $row['cr_lack'] > 0.001 : 0),
						'check' => $row['status'] == 'check',
						'lack' => abs($row['lack']) > 0.001,
						'min' => $row['lack'] < 0,
						'cr-lack' => abs($row['cr_lack']) > 0.001,
						'cr-min' => $row['cr_lack'] < 0,
						'show' => $user['show_amount'] == 1,
						'close' => $row['action'] == 'close'
				], 'cash');
				$i++;
			}
		}	
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		$left_count = intval(($res_count-($page*$count)-$i));
		
		$meta['title'] = $lang['All'].' '.$lang['cashStat'];
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' =>  $cash ?: $tpl_content['cash'],
			]));
		}
		tpl_set('activity/cash/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'cash' => $cash ?: $tpl_content['cash']
		], [], 'content');
	break;
	
	/*
	* Timer
	*/
	case 'timer':
		$query = text_filter($_POST['query'], 255, false);
		$object = intval($_POST['object']);
		$salary_check = intval($_POST['salary']);
		$tax = intval($_POST['tax']);
		$hours = intval($_POST['hours']);
		$staff = intval($_POST['staff']);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$page = intval($_POST['page']);
		$count = 100;
		
		$week_salary = 0;
		$week_salary_fee = 0;
		$week_total = 0;
		
		if($sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
				t.id,
				t.confirm,
				t.user_id,
				DATE(t.date) as date,
				TIME(t.date) as start_time,
				TIME(t.control_point) as end_time,
				TIME(t.break_start) as break_start,
				TIME(t.break_finish) as break_end,
				SEC_TO_TIME(t.seconds) as seconds,
				t.seconds as total,
				t.per_hour,
				t.flag,
				u.name,
				u.lastname,
				u.image,
				u.pay,
				o.name as object,
				o.salary_tax as salary
			FROM `'.DB_PREFIX.'_timer` t
			INNER JOIN `'.DB_PREFIX.'_users` u
				ON t.user_id = u.id 	
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = t.object_id
			WHERE t.hide = 0 AND t.event = \'stop\' AND t.user_id NOT IN(23059,23973,26022,96,13004,24838,28,1) '.(
				$object ? ' AND o.id = \''.$object.'\'' : ''
			).(
				$staff ? 'AND t.user_id = '.$staff.' ' : ''
			).(
				$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).(
				(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
					' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($objects_ip[$user['oip']] ?: 0).' OR u.id = '.$user['id'].'))' : ''
			).'ORDER BY '.(
				($date_start AND $date_finish) ? 't.user_id DESC, DATE(t.date)' : 't.date'
			).' DESC LIMIT '.($page*$count).', '.$count, true)){
			if ($date_start AND $date_finish) {
				$now_date = $sql[0]['date'];
				$timers = '';
				$total = 0;
				$salary_user = 0;
				$salary_user_fee = 0;
				$id = 0;
				$i = 0;
				$pay = 0;
				$salary = 0;
				foreach($sql as $row){
					if(isset($_POST['group']) && $_POST['group'] && $now_date){
						$sd = date_create($row['date']);
						$sd2 = date_create($now_date);
						$diff = (int)date_diff($sd, $sd2)->days;
						if($diff > ($_POST['group'] == 1 ? 7 : 14)){
							$s = $week_total % 60;
							$m = ($week_total % 3600 - $s) / 60;
							$h = ($week_total - $s - $m * 60) / 3600;
							$timers .= '<div class="tr line-sub">
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								'.($hours ? '<div class="td">'.$h.':'.$m.':'.$s.'</div>' : '').'
								'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
								'<div class="td">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</div>' : 
								'') : '').'
							</div>';
							$week_total = 0;
							$week_salary = 0;
							$week_salary_fee = 0;
							
							$now_date = $row['date'];
						}
					}
					
					if ($id != $row['user_id']) {
						if ($id) {
							if ($week_total) {
								$s = $week_total % 60;
								$m = ($week_total % 3600 - $s) / 60;
								$h = ($week_total - $s - $m * 60) / 3600;
								$timers .= '<div class="tr line-sub">
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									'.($hours ? '<div class="td">'.$h.':'.$m.':'.$s.'</div>' : '').'
									'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
									'<div class="td">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</div>' : 
									'') : '').'
								</div>';
								$week_total = 0;
								$week_salary = 0;
								$week_salary_fee = 0;
							}
							
							$s = $total % 60;
							$m = ($total % 3600 - $s) / 60;
							$h = ($total - $s - $m * 60) / 3600;
							$timers .= '<div class="tr">
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"></div>
								'.($hours ? '<div class="td">'.$h.':'.$m.':'.$s.'</div>' : '').'
								'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
								'<div class="td">$'.number_format($salary_user, 2, '.', '').($tax ? '/$'.number_format($salary_user_fee, 2, '.', '') : '').'</div>' :
								//'<div class="td">$'.number_format($total/3600*$pay, 2, '.', '').($salary ? '/$'.number_format($total/3600*$pay*((100 + $salary)/100), 2, '.', '') : '').'</div>' : 
								'') : '').'
							</div>
							</div>
							<div class="tbl tArea">
								<div class="tr">
									<div class="th wp20">
										<a href="/users/view/'.$row['user_id'].'" target="_blank">
											'.(
												$row['image'] ?
													'<img src="/uploads/images/users/'.$row['user_id'].'/thumb_'.$row['image'].'" class="miniRound">' :
												'<span class="fa fa-user-secret miniRound"></span>'
											).'
											'.$row['name'].' '.$row['lastname'].'
										</a>
									</div>
									<div class="th">Date</div>
									<div class="th">Punch in</div>
									<div class="th">Break start</div>
									<div class="th">Break end</div>
									<div class="th">Punch out</div>
									'.($hours ? '<div class="th">Working time</div>' : '').'
									'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="th">Salary</div>' : '') : '').'
								</div>';
						} else {
							$timers .= '<div class="tbl tArea">
								<div class="tr">
									<div class="th wp20">
										<a href="/users/view/'.$row['user_id'].'" target="_blank">
											'.(
												$row['image'] ?
													'<img src="/uploads/images/users/'.$row['user_id'].'/thumb_'.$row['image'].'" class="miniRound">' :
												'<span class="fa fa-user-secret miniRound"></span>'
											).'
											'.$row['name'].' '.$row['lastname'].'
										</a>
									</div>
									<div class="th">Date</div>
									<div class="th">Punch in</div>
									<div class="th">Break start</div>
									<div class="th">Break end</div>
									<div class="th">Punch out</div>
									'.($hours ? '<div class="th">Working time</div>' : '').'
									'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="th">Salary</div>' : '') : '').'
								</div>';
						}
						$total = 0;
						$salary_user = 0;
						$salary_user_fee = 0;
						$id = $row['user_id'];
						$pay = $row['pay'];
					}
					$timers .= '<div class="tr">
						<div class="td"><span class="thShort">Store: </span>'.$row['object'].'</div>
						<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
						<div class="td"><span class="thShort">Clock in: </span>'.$row['start_time'].'</div>
						<div class="td"><span class="thShort">Lunch: </span>'.$row['break_start'].'</div>
						<div class="td"><span class="thShort">End lunch: </span>'.$row['break_end'].'</div>
						<div class="td"><span class="thShort">Clock out: </span>'.$row['end_time'].'</div>
						'.($hours ? '<div class="td"><span class="thShort">Working time: </span>'.$row['seconds'].'</div>' : '').'
						'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
								($row['flag'] ?
									'<div class="td"><span class="thShort">Salary: </span>$'.number_format($row['total']/3600*$row['per_hour'], 2, '.', '').($tax ? '/$'.number_format($row['total']/3600*$row['per_hour']*1.2, 2, '.', '') : '').'</div>' :
									'<div class="td"><span class="thShort">Salary: </span>$'.number_format($row['total']/3600*$row['pay'], 2, '.', '').($tax ? ($row['salary'] ? '/$'.number_format($row['total']/3600*$row['pay']*((100 + $row['salary'])/100), 2, '.', '') : '') : '').'</div>'
								) : 
							'') : '').'
					</div>';
					$total += $row['total'];
					$salary_user += $row['flag'] ? $row['total']/3600*$row['per_hour'] : $row['total']/3600*$row['pay'];
					$salary_user_fee += $row['flag'] ? $row['total']/3600*$row['per_hour']*1.2 : $row['total']/3600*$row['pay']*((100 + $row['salary'])/100);
					$week_total += $row['total'];
					$week_salary += $row['flag'] ? $row['total']/3600*$row['per_hour'] : $row['total']/3600*$row['pay'];
					$week_salary_fee += $row['flag'] ? $row['total']/3600*$row['per_hour']*1.2 : $row['total']/3600*$row['pay']*((100 + $row['salary'])/100);
					$i++;
				}
				
				/* $s = $week_total % 60;
				$m = ($week_total % 3600 - $s) / 60;
				$h = ($week_total - $s - $m * 60) / 3600;
				$timers .= '<div class="tr line-sub">
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					'.($hours ? '<div class="td">'.$h.':'.$m.':'.$s.'</div>' : '').'
					'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
					'<div class="td">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</div>' : 
					'') : '').'
				</div>'; */
							
				$s = $total % 60;
				$m = ($total % 3600 - $s) / 60;
				$h = ($total - $s - $m * 60) / 3600;
				$timers .= '<div class="tr'.($row['confirm'] == 0 ? ' stop' : '').'">
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					<div class="td"></div>
					'.($hours ? '<div class="td">'.$h.':'.$m.':'.$s.'</div>' : '').'
					'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
						'<div class="td">$'.number_format($salary_user, 2, '.', '').($tax ? '/$'.number_format($salary_user_fee, 2, '.', '') : '').'</div>' : 
						//'<div class="td">$'.number_format($total/3600*$pay, 2, '.', '').($salary ? '/$'.number_format($total/3600*$pay*((100 + $salary)/100), 2, '.', '') : '').'</div>' : 
					'') : '').'
				</div>';
			} else {
				$i = 0;
				if (!$page) {
					$_SESSION['now_date'] = $sql[0]['date'];
					$_SESSION['line'] = false;
				}
				foreach($sql as $row){
					if(isset($_POST['group']) && $_POST['group'] && $_SESSION['now_date']){
						$sd = date_create($row['date']);
						$sd2 = date_create($_SESSION['now_date']);
						$diff = (int)date_diff($sd, $sd2)->days;
						if($diff > ($_POST['group'] == 1 ? 7 : 14)) {
							$_SESSION['line'] = true;
							$_SESSION['now_date'] = $row['date'];
							
							$_SESSION['week_total_print'] = $_SESSION['week_total'];
							$_SESSION['week_salary_print'] = $_SESSION['week_salary'];
							$_SESSION['week_salary_fee_print'] = $_SESSION['week_salary_fee'];
							
							$_SESSION['week_total'] = 0;
							$_SESSION['week_salary'] = 0;
							$_SESSION['week_salary_fee'] = 0;
						}
						//echo $_SESSION['now_date'].' '.intval($_SESSION['line']).' '.$diff.'<br>';
					}
					
					$_SESSION['week_total'] += $row['total'];
					$_SESSION['week_salary'] += $row['flag'] ? $row['total']/3600*$row['per_hour'] : $row['total']/3600*$row['pay'];
					$_SESSION['week_salary_fee'] += $row['flag'] ? $row['total']/3600*$row['per_hour']*1.2 : $row['total']/3600*$row['pay']*((100 + $row['salary'])/100);
							
					$total = $_SESSION['week_total_print'];
					$s = $total % 60;
					$m = ($total % 3600 - $s) / 60;
					$h = ($total - $s - $m * 60) / 3600;
				
					tpl_set('activity/timer/item', [
						'id' => $row['id'],
						'user-id' => $row['user_id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'date' => $row['date'],
						'start_time' => $row['start_time'],
						'end_time' => $row['end_time'],
						'break_start' => $row['break_start'],
						'break_end' => $row['break_end'],
						'seconds' => $row['seconds'],
						'confirm' => $row['confirm'] == 0 ? ' stop' : '',
						'confirmbt' => ($row['confirm'] == 0 && $user['confirm_working_time']) ? '<button class="confirm" onclick="timer.confirm('.$row['id'].');">Confirm</button>' : '',
						'week-total' => $h.':'.$m.':'.$s,
						'week-salary' => number_format($_SESSION['week_salary_print'], 2, '.', ''),
						'week-salary-fee' => number_format($_SESSION['week_salary_fee_print'], 2, '.', ''),
						'pay' => $row['flag'] ? 
							number_format($row['total']/3600*$row['per_hour'], 2, '.', '').($tax ? '/$'.number_format($row['total']/3600*$row['per_hour']*1.2, 2, '.', '') : '') :
							number_format($row['total']/3600*$row['pay'], 2, '.', '').($tax ? ($row['salary'] ? '/$'.number_format($row['total']/3600*$row['pay']*((100 + $row['salary'])/100), 2, '.', '') : '') : '')
					], [
						'ava' => $row['image'],
						'time-money' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))),
						'salary' => $salary_check,
						'hours' => $hours,
						'tax' => $tax,
						'line' => $_SESSION['line']
					], 'timers');
					$i++;
					$_SESSION['line'] = false;
				}
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = $lang['All'].' '.$lang['workingTime'];
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' =>  $timers ?: $tpl_content['timers'],
				'salary' => $salary_check,
				'hours' => $hours,
				'tax' => $tax
			]));
		}
		tpl_set('activity/timer/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'timers' => $timers ?: $tpl_content['timers']
		], [
			'time-money' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))),
			'salary' => $salary_check,
			'hours' => $hours,
			'tax' => $tax,
		], 'content');
	break;
	
	/*
	* Issues report
	*/
	case 'issues_report':
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
		$tbl = $status ? 'iss' : 'iss';
		$store_issues = ($route[2] == 'store' ? 1 : 0);

		$sql = 'SELECT SQL_CALC_FOUND_ROWS 
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
			invoice.conducted
		FROM `'.DB_PREFIX.'_issues` iss
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
		LEFT JOIN `'.DB_PREFIX.'_invoices` invoice
			ON invoice.issue_id = iss.id
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
			$instore ? 'AND iss.customer_id = 0 ' : ''
		).(
			(!$pickedup AND isset($_REQUEST['payment'])) ? ($payment == 'paid' ? 'AND invoice.conducted = 1 ' : 'AND (invoice.conducted = 0 OR invoice.id IS NULL)') : ''
		).(
			($cstatus AND !$pickedup AND !$all) ? 'AND iss.status_id = '.$cstatus : ''
		).(
			($date_start AND $date_finish) ? ' AND iss.date >= \''.$date_start.'\' AND iss.date <= \''.$date_finish.'\'' : ''
		).'ORDER BY iss.date ASC LIMIT '.($page*$count).', '.$count;
		
/* 		echo $sql;
		die; */

		if($sql = db_multi_query($sql, true)){
			$i = 0;
			foreach($sql as $row){
				$i++;
				tpl_set('activity/issues/item', [
					'id' => $row['id'],
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
			'filters' => false
		], 'content');
	break;
	
	/*
	* Issues
	*/
	case 'issues':
		$query = text_filter($_REQUEST['query'], 255, false);
		$object = intval($_REQUEST['object']);
		$date_start = text_filter(($_REQUEST['date_start'] ?: $_REQUEST['sDate']), 30, true);
		$date_finish = text_filter(($_REQUEST['date_finish'] ?: $_REQUEST['eDate']), 30, true);
		$payment = text_filter($_REQUEST['payment'], 30, true) == 'paid' ?: 'unpaid';
		$page = intval($_REQUEST['page']);
		$archive = intval($_REQUEST['title']);
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
		WHERE '.(
			$archive ? 'inv.customer_id = 0' : 'inv.customer_id > 0'
		).' '.($all ? '' : 'AND IF(iss.warranty, 0, 1) ').(
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
			$pickedup ? 'AND iss.status_id = 2 AND iss.warranty = 0 AND i.conducted = 1 ' : ''
		).(
			$instore ? 'AND iss.customer_id = 0 ' : ''
		).(
			$all ? '' : ($pickedup ? '' : 'AND iss.status_id NOT IN(2,22) ')
		).(
			(!$pickedup AND isset($_REQUEST['payment'])) ? ($payment == 'paid' ? 'AND i.conducted = 1 ' : 'AND (i.conducted = 0 OR i.id IS NULL)') : ''
		).(
			($cstatus AND !$pickedup AND !$all) ? 'AND iss.status_id = '.$cstatus : ($status ? ($status == 11 ? 'AND cl.changes = \'New issue\'' : 'AND cl.changes_id = '.$status. ' AND cl.changes = \'status\' ') : '')
		).(
			($date_start AND $date_finish) ? ' AND Date(cl.date) >= Date(\''.$date_start.'\') AND Date(cl.date) <= Date(\''.$date_finish.'\')' : ''
		).' GROUP BY cl.issue_id, iss.date LIMIT '.($page*$count).', '.$count, true)){
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
	
	/*
	* All activity
	*/
	default:
		$query = text_filter($_POST['query'], 255, false);
		$event = text_filter($_POST['event'], 50, false);
		$staff = intval($_POST['staff']);
		$object = intval($_POST['object']);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$page = intval($_POST['page']);
		$count = 20;
		

		if($sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS a.*, u.id as uid, u.name, u.lastname, u.image, t.seconds as hours FROM
		`'.DB_PREFIX.'_activity` a
			INNER JOIN `'.DB_PREFIX.'_users` u
				ON a.user_id = u.id 
			LEFT JOIN `'.DB_PREFIX.'_timer` t
				ON t.user_id = a.user_id AND t.date >= CURDATE()
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON FIND_IN_SET(a.user_id, o.managers) OR FIND_IN_SET(a.user_id, o.staff)
			WHERE camera = 0 '.(
			$event ? ' AND a.event = \''.$event.'\' ' : ''
		).(
			(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
				' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($objects_ip[$user['oip']] ?: 0).' OR u.id = '.$user['id'].'))' : ''
		).(
			$query ? 'AND MATCH(u.name, u.lastname, u.email) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
		).(
			$staff ? 'AND a.user_id = '.$staff.' ' : ''
		).(
			$object ? 'AND a.object_id = '.$object.' ' : ''
		).(
			($date_start AND $date_finish) ? ' AND a.date >= CAST(\''.$date_start.'\' AS DATE) AND a.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
		).'ORDER BY a.id DESC LIMIT '.($page*$count).', '.$count, true
		)){

			$i = 0;
			foreach($sql as $row){
				$event = $row['event'];
				$class = '';
				switch($row['event']) {
					
					case 'stop working time':
						$event = $lang['stopTime'].' '.$row['seconds'];
						$class = 'stop';
					break;	

					case 'start working time':
						$event = $lang['startTime'];
						$class = 'start';
					break;		

					case 'pause working time':
						$event = $lang['pauseTime'];
						$class = 'pause';
					break;					
					
					case 'add_purchase':
						$event = $lang['addNew'].' <a href="/purchases/edit/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">'.$lang['purchase'].'</a>';
					break;
					
					case 'new_invoice':
					$event = '';
						//$event = 'Create new invoice: <a href="/invoices/view/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
					break;
					
					case 'new_job':
					$event = '';
						//$event = 'Create new job: <a href="/issues/view/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
					break;
					
					case 'remove_job':
					$event = '';
						//$event = 'Remove job: <a href="/issues/view/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
						//$class = 'stop';
					break;
					
					case 'replied_to_chat':
					$event = '';
						//$event = 'Replied to chat: <a href="/im/support/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
					break;
					
				}
				if($event){
					tpl_set('activity/item', [
						'id' => $row['id'],
						'uid' => $row['uid'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'ava' => $row['image'],
						'date' => $row['date'],
						'event' => $event,
						'class' => $class
					], [
						'ava' => $row['image']
					], 'activity');
					$i++;
				}
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}

		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = $lang['All'].' '.$lang['Activity'];
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['activity'],
			]));
		}
		tpl_set('activity/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'activity' => $tpl_content['activity']
		], [], 'content');
}
?>