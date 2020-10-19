<?php
/**
 * @appointment Analytics admin panel
 * @author      Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

defined('ENGINE') or ('hacking attempt!');

	
switch($route[1]){
	
	/*
	* Online users
	*/
	case 'online_users':
		$online = db_multi_query('
			SELECT 
				COUNT(DISTINCT ip) as count 
			FROM `'.DB_PREFIX.'_visitors` WHERE date >= DATE_SUB(
				NOW(), INTERVAL 5 MINUTE
			) AND date <= NOW()
		');
		
		die($online ? $online['count'] : 0);
	break;
	
	
	/*
	* Online users
	*/
	case 'usr_job_st':
		echo '<pre>';
			print_r($_POST);
		echo '</pre>';
		die;
	break;
	
	/*
	* Website stats
	*/
	case 'site_stat':
		is_ajax() or die('Hacking attempt!');
		//$date = explode(' / ', $_POST['date'] ? text_filter($_POST['date'], 30, true) : date('Y-m-d', strtotime(' - 10 days')).' / '.date('Y-m-d', time()));
		$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 20, false) : date('Y-m-d', strtotime(' - 10 days'));
		$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 20, false) : date('Y-m-d', time());
		$visitors = [];
		$hosts = [];
		
		$visitors_array = array_column(db_multi_query('SELECT 
			'.(
				($date_start AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
			).'(date) as date, 
			COUNT(id) as count
		FROM `'.DB_PREFIX.'_visitors`
		WHERE type = 1 '.(
			($date_start AND $date_finish) ? ' AND date >= CAST(\''.$date_start.'\' AS DATE) AND date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND date >= DATE_SUB(
				NOW(), INTERVAL 10 DAY
			) AND DATE(date) <= NOW()'
		).'
		GROUP BY '.(
			($date_start AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
		).'(date)
		ORDER BY '.(
			($date_start AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
		).'(date)', true), 'count', 'date');
		
		$hosts_array = array_column(db_multi_query('SELECT 
			'.(
				($date_start AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
			).'(date) as date, 
			COUNT(id) as count
		FROM `'.DB_PREFIX.'_visitors`
		WHERE '.(
			($date_start AND $date_finish) ? ' date >= CAST(\''.$date_start.'\' AS DATE) AND date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' date >= DATE_SUB(
				NOW(), INTERVAL 10 DAY
			) AND DATE(date) <= NOW()'
		).'
		GROUP BY '.(
			($date_start AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
		).'(date)
		ORDER BY '.(
			($date_start AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
		).'(date)', true), 'count', 'date');
		
		if ($date_start AND $date_start == $date_finish) {
			$count = 23;
			$step = 1;
			if ($visitors_array) {
				foreach($visitors_array as $k => $v){
					$visitors[] = [$k, intval($v)];
				}
			}
			if ($hosts_array) {
				foreach($hosts_array as $k => $v){
					$hosts[] = [$k, intval($v)];
				}
			}
		} else {
			$count = (strtotime($date_finish) - strtotime($date_start)) / 24 / 3600;
			$step = round($count / 15) ? round($count / 15) : 1;
			$date_i = date ("Y-m-d", strtotime($date_start));
			
			for($i = 0; $i <= $count ; $i++) {
				if ($visitors_array[$date_i]) 
					$visitors[] = [$i, intval($visitors_array[$date_i]), 'Date: '.$date_i.' Visitors: '.$visitors_array[$date_i]];
				
				if ($hosts_array[$date_i]) 
					$hosts[] = [$i, intval($hosts_array[$date_i]), 'Date: '.$date_i.' Hosts: '.$hosts_array[$date_i]];
				
				if (!($i % $step)) {
					$labels[] = date ("d-m", strtotime($date_i));
				}
				$date_i = date ("Y-m-d", strtotime("+1 day", strtotime($date_i)));
			}	
		}
		
		
		$online = db_multi_query('
			SELECT 
				COUNT(DISTINCT ip) as count 
			FROM `'.DB_PREFIX.'_visitors` WHERE date >= DATE_SUB(
				NOW(), INTERVAL 5 MINUTE
			) AND date <= NOW()
		');
		
		die(json_encode([
			'data' => [
				'visitors' => $visitors,
				'hosts' => $hosts,
				'online' => ($online ? $online['count'] : 0)
			],
			'labels' => $labels,
			'step' => $step,
			'last' => $count
		]));
	break;
	
	/*
	* Sold plot
	*/
	case 'sold_plot':
		is_ajax() or die('Hacking attempt!');
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$object = $user['manager_report'] ? $user['store_id'] : intval($_POST['object']);
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', time());
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
			
			$inv = intval($_POST['inventory']);
			$tradein = intval($_POST['tradein']);
			$purchases = intval($_POST['purchases']);
			
			$i_ids = '';
			$t_ids = '';
			$p_ids = '';

			if ($si = db_multi_query('
				SELECT
					SQL_CALC_FOUND_ROWS
					id,
					DATE(date) as date,
					HOUR(date) as time,
					inventory_info,
					purchases_info,
					REGEXP_REPLACE(issue_info, \'{(.*?)"inventory":{(.*?)},"services"(.*)}\', \'{\\\2}\') as issue_inventory,
					REGEXP_REPLACE(issue_info, \'{(.*?)"purchases":{(.*?)},"upcharge"(.*)}\', \'{\\\2}\') as issue_purchases
				FROM `'.DB_PREFIX.'_invoices`
				WHERE conducted = 1
				'.(
					$object ? ' AND object_id = '.$object : ''
				).(
					($date_start AND $date_finish) ? ' AND date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).'
				ORDER BY id DESC'
			, true)) {

				foreach($si as $row) {
					if ($inv OR $tradein) {
						$iids = array_keys((json_decode($row['inventory_info'], true) ?? []) + (json_decode($row['issue_inventory'], true) ?? []));
						$i_ids .= ($i_ids ? ',' : '').implode(',', $iids);
					}
					
					if ($purchases) {
						$pids = array_keys((json_decode($row['purchases_info'], true) ?? []) + (json_decode($row['issue_purchases'], true) ?? []));
						$p_ids .= ($p_ids ? ',' : '').implode(',', $pids);
					}
				}
				
				if (ids_filter($i_ids)) {
					$iamount = db_multi_query('
						SELECT 
							id,
							price as sold,
							purchase_price as purchase,
							tradein
						FROM `'.DB_PREFIX.'_inventory`
						WHERE id IN ('.ids_filter($i_ids).')
					', true);
				}
				
				if (ids_filter($p_ids)) {
					$pamount = db_multi_query('
						SELECT 
							id,
							sale as sold,
							price as purchase
						FROM `'.DB_PREFIX.'_purchases`
						WHERE id IN ('.ids_filter($p_ids).')
					', true);
				}
				$result = [
					'sold' => [],
					'purchase' => [],
					'profit' => []
				];
				
				foreach($si as $row) {
					$type = ($date_start == $date_finish ? 'time' : 'date');
					if (!isset($result['sold'][$row[$type]]))
						$result['sold'][$row[$type]] = 0;
					if (!isset($result['purchase'][$row[$type]]))
						$result['purchase'][$row[$type]] = 0;
					if (!isset($result['profit'][$row[$type]]))
						$result['profit'][$row[$type]] = 0;
					
					if ($inv AND $iamount) {
						if ($invs = ((json_decode($row['inventory_info'], true) ?? []) + (json_decode($row['issue_inventory'], true) ?? []))) {
							foreach($invs as $k => $i) {
								$item = array_values(array_filter($iamount, function($a) use(&$k) {
									if ($a['id'] == $k AND !$a['tradein'])
										return $a;
								}))[0];
								$result['sold'][$row[$type]] += $item['sold'];
								$result['purchase'][$row[$type]] += $item['purchase'];
								$result['profit'][$row[$type]] += ($item['sold'] - $item['purchase']);
							}
						}
					}
					
					if ($tradein AND $iamount) {
						if ($trds = ((json_decode($row['inventory_info'], true) ?? []) + (json_decode($row['issue_inventory'], true) ?? []))) {
							foreach($trds as $k => $i) {
								$item = array_values(array_filter($iamount, function($a) use(&$k) {
									if ($a['id'] == $k AND $a['tradein'])
										return $a;
								}))[0];
								$result['sold'][$row[$type]] += $item['sold'];
								$result['purchase'][$row[$type]] += $item['purchase'];
								$result['profit'][$row[$type]] += ($item['sold'] - $item['purchase']);
							}
						}
					}
					
					if ($purchases AND $pamount) {
						if ($purs = ((json_decode($row['purchases_info'], true) ?? []) + (json_decode($row['issue_purchases'], true) ?? []))) {
							foreach($purs as $k => $i) {
								$item = array_values(array_filter($pamount, function($a) use(&$k) {
									if ($a['id'] == $k)
										return $a;
								}))[0];
								$result['sold'][$row[$type]] += $item['sold'];
								$result['purchase'][$row[$type]] += $item['purchase'];
								$result['profit'][$row[$type]] += ($item['sold'] - $item['purchase']);
							}
						}
					}
				}
				
				$data = [];
				$labels = [];
				
				if ($date_start == $date_finish) {
					$count = 23;
					$step = 1;
					foreach($result as $k => $v){
						foreach($v as $kk => $vv) {
							$data[$k][] = [$kk, $vv];
						}
					}
				} else {
					$count = (strtotime($date_finish) - strtotime($date_start)) / 24 / 3600;
					$step = round($count / 15) ? round($count / 15) : 1;
					
					foreach($result as $k => $v){
						$date_i = date ("Y-m-d", strtotime($date_start));
						for($i = 0; $i <= $count ; $i++) {
							if ($v[$date_i]) {
								$data[$k][] = [$i, $v[$date_i], 'Date: '.$date_i.' Sales: $'.$v[$date_i]];
							}
							if (!($i % $step)) {
								$labels[] = date ("d-m", strtotime($date_i));
							}
							$date_i = date ("Y-m-d", strtotime("+1 day", strtotime($date_i)));
						}	
					}	
				}
			}
			die(json_encode([
				'data' => $data,
				'labels' => $labels,
				'step' => $step,
				'last' => $count
			]));
		} else 
			die('no_acc');
	break;
	
	/* 
	* New users
	*/
	case 'new_users':
		is_ajax() or die('Hacking attempt!');
		$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', strtotime(' - 10 days'));
		$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
		$labels = [];
		$count_users = [];
		$count = 0;
		
		$rlabels = [];
		$rcount_users = [];
		$rcount = 0;
		
		$i = 0;
		foreach($items = db_multi_query('
			SELECT '.(
					($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
				).'(u.reg_date) as date,
				COUNT(u.id) as count
			FROM `'.DB_PREFIX.'_users` u
			WHERE u.del = 0 AND FIND_IN_SET(5, u.group_ids) '.(
				$_POST['date_start'] ? ' AND u.reg_date >= CAST(\''.$date_start.'\' AS DATE) AND u.reg_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND u.reg_date >= DATE_SUB(
					NOW(), INTERVAL 10 DAY
				) AND u.reg_date <= \''.date('Y-m-d', time()).'\''
			).'
			GROUP BY '.(
					($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
				).'(u.reg_date) ORDER BY '.(
					($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
				).'(u.reg_date) ASC',
		true) as $item){
			if ($_POST['date_start'] AND $date_start == $date_finish) {
				$count_users[] = [intval($item['date']),intval($item['count']), 'Date: '.$item['date'].':00-'.$item['date'].':59 Count: '.$item['count']];
				$labels[] = $item['date'];
			} else {
				$count_users[] = [$i, intval($item['count']), 'Date: '.$item['date'].' Count: '.$item['count']];
				$labels[] = date("d-m", strtotime($item['date']));
			}
			$i++;
		}
		
		if ($_POST['date_start'] AND $date_start == $date_finish) {
			$count = 23;
			$step = 1;
		} else {
			$count = (strtotime($date_finish) - strtotime($date_start)) / 24 / 3600;
			$step = round($count / 15) ? round($count / 15) : 1;	
		}

		$inv_users = db_multi_query('SELECT COUNT(DISTINCT customer_id) as cusers FROM `'.DB_PREFIX.'_invoices` WHERE customer_id != 0');
		
		$ri = 0;
		foreach($items_returned = db_multi_query('
			SELECT '.(
					($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
				).'(i.date) as date,
				COUNT(u.id) as count
			FROM `'.DB_PREFIX.'_users` u
			INNER JOIN `'.DB_PREFIX.'_issues` i
				ON i.customer_id = u.id
			WHERE u.del = 0 AND i.customer_id != 0 AND DATE(i.date) != DATE(u.reg_date) AND FIND_IN_SET(5, u.group_ids) '.(
				$_POST['date_start'] ? ' AND i.date >= CAST(\''.$date_start.'\' AS DATE) AND i.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND i.date >= DATE_SUB(
					NOW(), INTERVAL 10 DAY
				) AND i.date <= \''.date('Y-m-d', time()).'\''
			).'
			GROUP BY '.(
					($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
				).'(i.date) ORDER BY '.(
					($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
				).'(i.date) ASC',
		true) as $item){
			if ($_POST['date_start'] AND $date_start == $date_finish) {
				$rcount_users[] = [intval($item['date']),intval($item['count']), 'Date: '.$item['date'].':00-'.$item['date'].':59 Count: '.$item['count']];
				$rlabels[] = $item['date'];
			} else {
				$rcount_users[] = [$ri, intval($item['count']), 'Date: '.$item['date'].' Count: '.$item['count']];
				$rlabels[] = date("d-m", strtotime($item['date']));
			}
			$ri++;
		}
		
		die(json_encode([
			'data' => array_values($count_users),
			'returned' => array_values($rcount_users),
			'labels' => $labels,
			'returned_labels' => $rlabels,
			'step' => $step,
			'last' => $count == 1 ? 2 : $count,
			'users' => $inv_users['cusers']
		]));
	break;
	
	/*
	* Users profit
	*/
	case 'users_profit':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			if ($id = intval($route[2])) {
				$page = intval($_REQUEST['page']);
				$type = text_filter($_REQUEST['type'], 8, false);
				$object_id = $user['manager_report'] ? $user['store_id'] : ids_filter($_REQUEST['object']);
				$date_start = $_REQUEST['date_start'] ? text_filter($_REQUEST['date_start'], 30, true) : '';
				$date_finish = $_REQUEST['date_finish'] ? text_filter($_REQUEST['date_finish'], 30, true) : '';
				$count = 20;
				$total = 0;
				
				if ($users = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS DISTINCT * FROM (
						(SELECT
							p.amount,
							p.type,
							p.currency,
							p.object_id,
							p.date,
							o.name as store,
							0 as seconds,
							i.issue_id
						FROM `'.DB_PREFIX.'_users_profit` p
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = p.object_id
						LEFT JOIN `'.DB_PREFIX.'_invoices` i
							ON i.id = p.invoice_id
						WHERE p.staff_id = '.$id.' '.(
							($date_start AND $date_finish) ? ' AND p.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND p.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).(
							($type AND $type != '0') ? ' AND p.type = \''.$type.'\'' : ''
						).(
							$object_id ? ' AND p.object_id IN('.$object_id.')' : ''
						).')
					UNION ALL
						(SELECT 
							t.seconds * u.pay / 3600 * (1 + o.salary_tax / 100) as amount,
							\'salary\' as type,
							\'USD\' as currency,
							t.object_id,
							t.date,
							o.name as store,
							SEC_TO_TIME(t.seconds) as seconds,
							0 as issue_id
						FROM `'.DB_PREFIX.'_timer` t
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = t.user_id
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = t.object_id
						WHERE t.user_id = '.$id.' '.(
							($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).(
							($type AND $type != '0') ? ' AND \'salary\' = \''.$type.'\'' : ''
						).(
							$object_id ? ' AND t.object_id IN('.$object_id.')' : ''
						).'
						)
					) t_all
					ORDER BY date DESC
					LIMIT '.($page*$count).', '.$count
				, true)) {
					
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					
					$issue_ids = [];
					
					foreach($users as $row){
						if($row['issue_id'])
							$issue_ids[$row['issue_id']] = $row['issue_id'];
					}
					
					$statuses = [];
					
					if($issue_ids = implode(',', $issue_ids)){
						db_multi_query('SELECT st.id, issue_id, st.name FROM `'.DB_PREFIX.'_issues_changelog` iss INNER JOIN `'.DB_PREFIX.'_inventory_status` st ON iss.changes_id = st.id WHERE `issue_id` IN('.db_escape_string($issue_ids).') AND `user` = '.$id.' AND `changes` = \'status\'', true, false, function($a) use(&$statuses) {
							$statuses[$a['issue_id']][] = $a['name'];
						});
					}
					

					$i = 0;
					foreach($users as $row) {						
						tpl_set('analytics/users_profit/user/item', [
							'id' => $row['id'],
							'store-id'  => $row['object_id'],
							'store'  => $row['store'],
							'statuses'  => implode(', ', $statuses[$row['issue_id']]),
							'amount' => ($row['type'] == 'salary' ? '-' : '').number_format($row['amount'], 2, '.', ''),
							'date' => $row['date'],
							'issue-id' => $row['issue_id'],
							'staff-id' => $id,
							'type' => $row['type'].($row['type'] == 'salary' ? '('.$row['seconds'].')' : ''),
							'currency' => $config['currency'][$row['currency']]['symbol']
						], [
							'image' => $row['image'],
							'details' => true,
							'issue-id' => $row['issue_id'],
						], 'profit');
						
						$i ++;
					}
					
					$left_count = intval(($res_count-($page*$count)-$i));
				
					$meta['title'] = 'Users profit';
					if($_POST){
						exit(json_encode([
							'res_count' => $res_count,
							'left_count' => $left_count,
							'content' => $tpl_content['profit']
						]));
					}
					tpl_set('analytics/users_profit/user/main', [
						'res_count' => $res_count,
						'more' => $left_count ? '' : ' hdn',
						'profit' => $tpl_content['profit']
					], [
						'details' => true
					], 'content');
					
				}
			} else {
				$page = intval($_REQUEST['page']);
				$type = text_filter($_REQUEST['type'], 8, false);
				$date_start = $_REQUEST['date_start'] ? text_filter($_REQUEST['date_start'], 30, true) : '';
				$date_finish = $_REQUEST['date_finish'] ? text_filter($_REQUEST['date_finish'], 30, true) : '';
				$count = 20;
				$total = 0;
				
				if ($user['manager_report'] AND $user['store_id'])
					$object_id = $user['store_id'];
				
				if ($users = db_multi_query('
					SELECT
						SQL_CALC_FOUND_ROWS
						u.id,
						u.name,
						u.lastname,
						u.image,
						SUM(p.amount) as amount,
						p.type,
						p.currency
					FROM `'.DB_PREFIX.'_users` u
					LEFT JOIN `'.DB_PREFIX.'_users_profit` p
						ON p.staff_id = u.id
					WHERE !FIND_IN_SET(1, u.group_ids) AND  u.del = 0 '.(
						($date_start AND $date_finish) ? ' AND p.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND p.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).(
						($type AND $type != '0') ? ' AND p.type = \''.$type.'\'' : ''
					).(
						$object_id ? ' AND p.object_id = '.$object_id : ''
					).'
					GROUP BY p.staff_id, p.currency
					ORDER BY SUM(p.amount) DESC
					LIMIT '.($page*$count).', '.$count
				, true)) {
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);

					$salary = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
							t.id,
							t.user_id,
							SUM(t.seconds) as seconds,
							u.pay
						FROM `'.DB_PREFIX.'_users` u
						INNER JOIN `'.DB_PREFIX.'_timer` t
							ON t.user_id = u.id
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = t.object_id
						WHERE t.event = \'stop\' AND t.user_id IN('.implode(',', array_column($users, 'id')).') '.(
						($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).' GROUP BY t.user_id ORDER BY t.user_id', true);
					
					$points = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
							s.id,
							s.staff_id,
							SUM(s.point) as points
						FROM `'.DB_PREFIX.'_inventory_status_history` s
						WHERE s.point != 0 AND s.staff_id IN('.implode(',', array_column($users, 'id')).') '.(
						($date_start AND $date_finish) ? ' AND s.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND s.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).' GROUP BY s.staff_id ORDER BY s.staff_id', true);
			
					$i = 0;
					foreach($users as $row) {
						
						$s_user = array_values(array_filter($salary, function($v) use(&$row) {
							if ($v['user_id'] == $row['id'])
								return $v;
						}, ARRAY_FILTER_USE_BOTH));
						
						$p_user = array_values(array_filter($points, function($v) use(&$row) {
							if ($v['staff_id'] == $row['id'])
								return $v;
						}, ARRAY_FILTER_USE_BOTH));
						
						$total = $s_user[0]['seconds'];
						$s = $total % 60;
						$m = ($total % 3600 - $s) / 60;
						$h = ($total % 86400 - $s - $m * 60) / 3600;
						$d = ($total % (86400 * 356) - $s - $m * 60 - $h * 3600) / 86400;
						
						tpl_set('analytics/users_profit/item', [
							'id' => $row['id'],
							'name' => $row['name'].' '.$row['lastname'],
							'image' => $row['image'],
							'amount' => '<a href="/invoices?profit='.$row['id'].'" onclick="Page.get(this.href); return false;">'.number_format(($row['amount'] - $s_user[0]['seconds']/3600 * $s_user[0]['pay']), 2, '.', '').'</a>',
							'seconds' => ($d ? ($d > 1 ? $d.' days' : $d.' day').' ' : '').$h.':'.$m.':'.$s,
							'hour_profit' => ($s_user[0]['seconds'] ? (number_format(($row['amount'] - $s_user[0]['seconds']/3600 * $s_user[0]['pay']) / $s_user[0]['seconds'] * 3600, 2, '.', '')) : 0),
							'currency' => $config['currency'][$row['currency']]['symbol'],
							'points' => number_format($p_user[0]['points']/$total*3600, 2, '.', '')
						], [
							'image' => $row['image'],
							'details' => false
						], 'profit');
						
						$i ++;
					}
				}
				
				$left_count = intval(($res_count-($page*$count)-$i));
				
				$meta['title'] = 'Users profit';
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['profit']
					]));
				}
				tpl_set('analytics/users_profit/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'profit' => $tpl_content['profit']
				], [
					'details' => false
				], 'content');
			}
		}
	break;
	
	
	/*
	* Inventory traking
	*/
	case 'inventory_traking':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$meta['title'] = $lang['InventoryTracking'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$object = $user['manager_report'] ? $user['store_id'] : intval($_POST['object']);
			$create = intval($_POST['create']);
			$type = text_filter($_POST['type'], 8, false);
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : '';
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : '';
			$count = 20;

			if ($inventory = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS * FROM (
				'.(
					(!$type OR $type == '0' OR $type == 'stock') ? '(
						SELECT 
							i.id, 
							IF(i.name = \'\', CONCAT(IFNULL(b.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', IFNULL(i.model, \'\')), i.name) as name,
							i.cr_user, 
							i.cr_date as date,
							i.customer_id,
							i.quantity,
							i.price,
							i.currency,
							i.purchase_price,
							i.purchase_currency,
							u.name as cr_name,
							u.lastname as cr_lastname,
							c.name as c_name,
							c.lastname as c_lastname,
							o.name as object,
							ow.name as object_owner,
							0 as issue,
							\'stock\' as type
						FROM `'.DB_PREFIX.'_inventory` i
						LEFT JOIN  `'.DB_PREFIX.'_inventory_categories` b
							ON b.id = i.category_id
						LEFT JOIN  `'.DB_PREFIX.'_inventory_models` m
							ON m.id = i.model_id
						LEFT JOIN  `'.DB_PREFIX.'_inventory_types` t
							ON t.id = i.type_id
						LEFT JOIN  `'.DB_PREFIX.'_users` u
							ON u.id = i.cr_user
						LEFT JOIN  `'.DB_PREFIX.'_users` c
							ON c.id = i.customer_id
						LEFT JOIN  `'.DB_PREFIX.'_objects` o
							ON o.id = i.object_id
						LEFT JOIN  `'.DB_PREFIX.'_objects` ow
							ON ow.id = i.object_owner
						WHERE i.del = 0 AND i.type = \'stock\' '.(
							$object ? ' AND i.object_id = '.$object : ''
						).(
							$create ? ' AND i.cr_user = '.$create : ''
						).(
							($date_start AND $date_finish) ? ' AND i.cr_date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND i.cr_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).(
							$query ? ' AND IF(i.type = \'service\', 
												i.name LIKE \'%'.$query.'%\', 
												(t.name LIKE \'%'.$query.'%\' OR m.name LIKE \'%'.$query.'%\' OR 
												c.name LIKE \'%'.$query.'%\' OR i.model LIKE \'%'.$query.'%\' OR 
												i.barcode LIKE \'%'.$query.'%\' OR i.id = \''.$query.'\' OR 
												IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name) LIKE \'%'.$query.'%\'))' : ''
						).'
					)' : ''
				).(
					(!$type OR $type == '0') ? 'UNION ALL ' : ''
				).(
					(!$type OR $type == '0' OR $type == 'purchase') ? '(
						SELECT
							p.id,
							IF (p.sale_name != \'\', p.sale_name, p.name) as name,
							p.create_id as cr_user, 
							p.create_date as date,
							p.customer_id,
							p.quantity,
							p.sale as price,
							p.currency as currency,
							p.price as purchase_price,
							p.purchase_currency as purchase_currency,
							u.name as cr_name,
							u.lastname as cr_lastname,
							c.name as c_name,
							c.lastname as c_lastname,
							o.name as object,
							\'\' as object_owner,
							p.issue_id as issue,
							\'purchase\' as type
						FROM `'.DB_PREFIX.'_purchases` p
						LEFT JOIN  `'.DB_PREFIX.'_users` u
							ON u.id = p.create_id
						LEFT JOIN  `'.DB_PREFIX.'_users` c
							ON c.id = p.customer_id
						LEFT JOIN  `'.DB_PREFIX.'_objects` o
							ON o.id = p.object_id
						WHERE p.del = 0 '.(
							$object ? ' AND p.object_id = '.$object : ''
						).(
							$create ? ' AND p.create_id = '.$create : ''
						).(
							$create ? ' AND p.create_id = '.$create : ''
						).(
							($date_start AND $date_finish) ? ' AND p.create_date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND p.create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).(
							$query ? ' AND (p.name LIKE \'%'.$create.'%\' OR p.sale_name LIKE \'%'.$create.'%\')' : ''
						).'
					)' : ''
				).') t_all
				ORDER BY date DESC
				LIMIT '.($page*$count).', '.$count
			, true)) {
				$sids = implode(',', array_column(array_filter($inventory, function($a) {
					if ($a['type'] == 'stock')
						return $a;
				}), 'id'));
				$pids = implode(',', array_column(array_filter($inventory, function($a) {
					if ($a['type'] == 'purchase')
						return $a;
				}), 'id'));
				
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				
				if ($sids) {
					$invoices = db_multi_query('
						SELECT
							i.id,
							inv.id as inv,
							inv.date,
							inv.object_id,
							inv.customer_id,
							inv.inventory_info,
							inv.tradein_info,
							IF(inv.tradein_info LIKE CONCAT(\'%"\', i.id, \'"%\'), 1, 0) as tradein,
							u.name as c_name,
							u.lastname as c_lastname
						FROM `'.DB_PREFIX.'_inventory` i
						LEFT JOIN  `'.DB_PREFIX.'_invoices` inv
							ON (FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(inv.inventory_info, \'(?:"([0-9]+)":{([^{}]+)}(,?))\', \'\\\1\\\3\'), \'[{}]\', \'\')) OR FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(inv.tradein_info, \'(?:"([0-9]+)":{([^{}]+)}(,?))\', \'\\\1\\\3\'), \'[{}]\', \'\')))
						LEFT JOIN  `'.DB_PREFIX.'_users` u
							ON u.id = inv.customer_id
						WHERE inv.conducted = 1 AND i.id IN ('.$sids.') 
						ORDER BY i.id DESC
					', true);
					//AND (inv.inventory_info LIKE CONCAT(\'%"\', i.id, \'"%\') OR inv.tradein_info LIKE CONCAT(\'%"\', i.id, \'"%\'))
					//
					//	LEFT JOIN `'.DB_PREFIX.'_issues` iss
					//		ON FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(iss.inventory_info, \'(?:"([0-9]+)":{([^{}]+)}(,?))\', \'\\\1\\\3\'), \'[{}]\', \'\'))	
					 
					// OR FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(inv.issue_info, \'(.*?)"inventory":{(?:"([0-9]+)":{([^{}]+)}(,?))},"services":(.*?)\', \'\\\2\\\4\'), \'[{}]\', \'\'))
					
					$transfers = db_multi_query('
						SELECT
							i.id,
							t.from_store,
							t.to_store,
							t.from_date as date,
							fs.name as from_store_name,
							ts.name as to_store_name,
							1 as transfer
						FROM `'.DB_PREFIX.'_inventory` i
						LEFT JOIN  `'.DB_PREFIX.'_inventory_transfer` t
							ON i.id IN(t.inventory_ids)
						LEFT JOIN  `'.DB_PREFIX.'_objects` fs
							ON fs.id = t.from_store
						LEFT JOIN  `'.DB_PREFIX.'_objects` ts
							ON ts.id = t.to_store
						WHERE i.id IN ('.$sids.') AND t.received = 1
						ORDER BY i.id DESC
					', true);
				}
				
				if ($pids) {
					$pinvoices = db_multi_query('
						SELECT
							i.id,
							inv.id as inv,
							inv.date,
							inv.object_id,
							inv.customer_id,
							inv.inventory_info,
							inv.tradein_info,
							u.name as c_name,
							u.lastname as c_lastname
						FROM `'.DB_PREFIX.'_purchases` i
						LEFT JOIN `'.DB_PREFIX.'_issues` iss
							ON iss.id = i.issue_id
						LEFT JOIN  `'.DB_PREFIX.'_invoices` inv
							ON (FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(inv.purchases_info, \'(?:"([0-9]+)":{([^{}]+)}(,?))\', \'\\\1\\\3\'), \'[{}]\', \'\')) OR inv.issue_id = iss.id)
						LEFT JOIN  `'.DB_PREFIX.'_users` u
							ON u.id = inv.customer_id
						WHERE inv.conducted = 1 AND i.id IN ('.$pids.') 
						ORDER BY i.id DESC
					', true);
				}
			
				$i = 0;
				foreach($inventory as $inv) {
					
					if (in_to_array($inv['id'], $sids)) {
						$arr = array_merge(array_filter($transfers, function($a) use(&$inv) {
							if ($a['id'] == $inv['id'])
								return $a;
						}), array_filter($invoices, function($a) use(&$inv) {
							if ($a['id'] == $inv['id'])
								return $a;
						}));
					} else {
						$arr = array_filter($pinvoices, function($a) use(&$inv) {
							if ($a['id'] == $inv['id'])
								return $a;
						});
					}
					
					usort($arr, function($a, $b) {
					  return ($a['date'] < $b['date']) ? -1 : 1;
					});
					
					$history = '';
					foreach($arr as $a) {
						$history .= '<li>'.$a['date'].' - '.(
							$a['tradein'] ? '<a href="/invoices/view/'.$a['inv'].'" onclick="Page.get(this.href); return false;">Tradein</a> from <a href="/users/view/'.$a['customer_id'].'" onclick="Page.get(this.href); return false;">'.$a['c_name'].' '.$a['c_lastname'].'</a>' : (
								$a['transfer'] ? 'Transfer from '.$a['from_store_name'].' to '.$a['to_store_name'] : (
									$a['customer_id'] ? 'Customer <a href="/users/view/'.$a['customer_id'].'" onclick="Page.get(this.href); return false;">'.$a['c_name'].' '.$a['c_lastname'].'</a> bought it (<a href="/invoices/view/'.$a['inv'].'" onclick="Page.get(this.href); return false;">Invoice #'.$a['inv'].'</a>)' : 'Quick sell (<a href="/invoices/view/'.$a['inv'].'" onclick="Page.get(this.href); return false;">Invoice #'.$a['inv'].'</a>)'
								)
							)
						).'</li>';
					}
					
					/* $invoice = array_values(array_filter($invoices, function($a) use(&$inv) {
						if ($a['id'] == $inv['id'])
							return $a;
					}));
					
					if ($invoice AND !$invoice[count($invoice) - 1]['tradein'] AND $invoice[count($invoice) - 1]['customer_id'] AND $invoice[count($invoice) - 1]['customer_id'] != $inv['customer_id'] AND $inv['quantity'] == 1) {
						//$history .= '<li>'.$invoice[count($invoice) - 1]['customer_id'].' '.$inv['customer_id'].'</li>';
						db_query('UPDATE `'.DB_PREFIX.'_inventory` SET customer_id = '.$invoice[count($invoice) - 1]['customer_id'].' WHERE id = '.$inv['id']);
					} */
					
					if ($inv['customer_id'] AND $history OR !$inv['customer_id']) {
						tpl_set('analytics/tracking/item', [
							'id' => $inv['id'],
							'type' => $inv['type'],
							'name' => $inv['name'],
							'date_create' => $inv['date'],
							'cr_staff' => '<a href="/users/view/'.$inv['cr_user'].'" onclick="Page.get(this.href); return false;">'.$inv['cr_name'].' '.$inv['cr_lastname'].'</a>',
							'c_name' => $inv['c_name'],
							'c_lastname' => $inv['c_lastname'],
							'c_id' => $inv['customer_id'],
							'store' => $inv['object'],
							'price' => $config['currency'][$inv['currency']]['symbol'].number_format($inv['price'], 2, '.', ''),
							'purchase_price' => $config['currency'][$inv['purchase_currency']]['symbol'].number_format($inv['purchase_price'], 2, '.', ''),
							'income' => $config['currency'][$inv['currency']]['symbol'].number_format(($inv['price'] - $inv['purchase_price']), 2, '.', ''),
							'history' => $history,
							'object_owner' => $inv['object_owner'] ?: $inv['object'],
							'issue' => $inv['issue']
						], [
							'customer' => $inv['customer_id'],
							'stock' => $inv['type'] == 'stock',
							'issue' => $inv['issue']
						], 'inventory');
					}
					$i++;
				}
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['inventory'],
				]));
			}
			tpl_set('analytics/tracking/main', [
				'inventory' => $tpl_content['inventory'],
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn'
			], [
				'manager' => ($user['manager_report'] AND !in_to_array(1, $user['group_ids']) AND !in_to_array(2, $user['group_ids']))
			], 'content');
		}
	break;
	
	/*
	* Feedback report
	*/
	case 'feedback_by_day':
		//is_ajax() or die('Hacking attempt!');
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', strtotime(' - 7 days'));
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
			$stars = [];
			$labels = [];
			$count = 0;
			
			if ($user['manager_report'] AND $user['store_id'])
				$object_id = $user['store_id'];
			
			$feebacks_array = [];
			
			if ($feedbacks = array_column(db_multi_query('
				SELECT
					SUM(f.ratting)/COUNT(f.ratting) as middle,
					'.(
						($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
					).'(f.date) as date
				FROM `'.DB_PREFIX.'_feedback` f
				LEFT JOIN `'.DB_PREFIX.'_issues` i
					ON i.id = f.issue_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = i.object_owner
				WHERE 1 '.(
					($date_start AND $date_finish) ? ' AND f.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND f.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).(
					$object_id ? ' AND i.object_owner = '.$object_id : ''
				).'
				GROUP BY '.(
						($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
					).'(f.date)
			', true), 'middle', 'date')) {

				if ($date_start == $date_finish) {
					$count = 23;
					$step = 1;

					foreach($feedbacks as $k => $v){
						$feebacks_array[] = [$k, number_format($v, 2, '.', '')];
					}

				} else {
					$count = (strtotime($date_finish) - strtotime($date_start)) / 24 / 3600;
					$step = round($count / 15) ? round($count / 15) : 1;
					$date_i = date ("Y-m-d", strtotime($date_start));
					
					for($i = 0; $i <= $count ; $i++) {
						if ($feedbacks[$date_i]) 
							$feebacks_array[] = [$i, floatval(number_format($feedbacks[$date_i], 2, '.', '')), 'Date: '.$date_i.'; Middle value: '.number_format($feedbacks[$date_i], 2, '.', '')];
						
						if (!($i % $step)) {
							$labels[] = date ("d-m", strtotime($date_i));
						}
						$date_i = date ("Y-m-d", strtotime("+1 day", strtotime($date_i)));
					}	
				}
			}

			die(json_encode([
				'data' => array_values($feebacks_array),
				'labels' => $labels,
				'step' => $step,
				'last' => $count
			]));
		}
	break;
	
	/*
	* Feedback report
	*/
	case 'feedback_report':
		is_ajax() or die('Hacking attempt!');
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', strtotime(' - 7 days'));
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
			$stars = [];
			$labels = [];
			$count = 0;
			
			if ($user['manager_report'] AND $user['store_id'])
				$object_id = $user['store_id'];
			
			for($i = 0; $i < 5; $i++) {
				$stars[$i] = [$i, 0, ''];
				$labels[$i] = [$i + 1];
			}
			if ($feedbacks = db_multi_query('
				SELECT
					SUM(IF(f.ratting = 1, 1, 0)) AS r1,
					SUM(IF(f.ratting = 2, 1, 0)) AS r2,
					SUM(IF(f.ratting = 3, 1, 0)) AS r3,
					SUM(IF(f.ratting = 4, 1, 0)) AS r4,
					SUM(IF(f.ratting = 5, 1, 0)) AS r5,
					IF(i.object_owner, i.object_owner, 0) as object,
					o.name
				FROM `'.DB_PREFIX.'_feedback` f
				LEFT JOIN `'.DB_PREFIX.'_issues` i
					ON i.id = f.issue_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = i.object_owner
				WHERE f.type != 4 '.(
					($date_start AND $date_finish) ? ' AND date(f.date) >= date(\''.$date_start.'\') AND date(f.date) <= date(\''.$date_finish.'\')' : ''
				).(
					$object_id ? ' AND i.object_owner = '.$object_id : ''
				).'
				GROUP BY object
			', true)) {

				foreach($feedbacks as $k => $f) {
					$stars[0][1] += $f['r1'];
					$stars[1][1] += $f['r2'];
					$stars[2][1] += $f['r3'];
					$stars[3][1] += $f['r4'];
					$stars[4][1] += $f['r5'];
					
					tpl_set('analytics/feedback/store', [
						'name' => $f['object'] > 0  ? $f['name'] : $lang['NoStore'],
						'object' => $f['object'],
						'date_start' => $date_start,
						'date_finish' => $date_finish,
						'r1' => $f['r1'],
						'r2' => $f['r2'],
						'r3' => $f['r3'],
						'r4' => $f['r4'],
						'r5' => $f['r5']
					], [], 'stores');
				}
			}
			
			foreach($stars as $k => $s) {
				if ($s[1] > 0)
					$count ++;
				else {
					unset($stars[$k]);
				}
			}
			
			$ftafffb = db_multi_query(
				'SELECT u.id, u.name, u.lastname, u.image, COUNT(u.id) as count FROM `'.DB_PREFIX.'_feedback` f LEFT JOIN `'.DB_PREFIX.'_users` u ON f.send_staff_id = u.id WHERE f.send_staff_id > 0 AND f.date >= \''.db_escape_string($date_start).'\' AND f.date <= \''.db_escape_string($date_finish).'\' GROUP BY f.send_staff_id ORDER BY count DESC LIMIT 1000', true
			);

			die(json_encode([
				'data' => array_values($stars),
				'labels' => $labels,
				'step' => 1,
				'last' => 5,
				'stores' => $tpl_content['stores'],
				'staffs' => $ftafffb,
				'date_start' => $date_start,
				'date_finish' => $date_finish
			]));
		}
	break;
	
	/*
	* Feedback analytics
	*/
	case 'feedback':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$meta['title'] = $lang['FeedbackAnalytics'];
			tpl_set('analytics/feedback/main', [
			], [
			], 'content');
		}
	break;
	
	/*
	* Sold inventory
	*/
	case 'sold_inventory':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$object = $user['manager_report'] ? $user['store_id'] : intval($_POST['object']);
			$page = intval($_POST['page']);
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : '';
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : '';
			$count = 20;
			$total = 0;
			
			if ($si = db_multi_query('
				SELECT
					SQL_CALC_FOUND_ROWS
					id,
					inventory_info,
					REGEXP_REPLACE(issue_info, \'{(.*?)"inventory":{(.*?)},"services"(.*)}\', \'{\\\2}\') as issue_inventory
				FROM `'.DB_PREFIX.'_invoices`
				WHERE conducted = 1 AND ((inventory_info != \'\' AND inventory_info != \'{}\') OR REGEXP_REPLACE(issue_info, \'{(.*?)"inventory":{(.*?)},"services"(.*)}\', \'\\\2\') != \'\')
				'.(
					$object ? ' AND object_id = '.$object : ''
				).(
					($date_start AND $date_finish) ? ' AND date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).'
				ORDER BY id DESC
				LIMIT '.($page*$count).', '.$count
			, true)) {
				$i = 0;
				foreach($si as $row) {
					$inventory_info = is_array(json_decode($row['inventory_info'], true)) ? json_decode($row['inventory_info'], true) : [];
					$issue_inventory = is_array(json_decode($row['issue_inventory'], true)) ? json_decode($row['issue_inventory'], true) : [];
					if ($arr = $issue_inventory + $inventory_info) {
						foreach($arr as $id => $a) {
							tpl_set('analytics/sold_inventory/item', [
								'id' => $id,
								'name' => $a['name'],
								'price' => number_format(trim(str_replace('$', '', $a['price'])), 2, '.', '')
							], [], 'inventory');
							$total += floatval(trim(str_replace('$', '', $a['price'])));
						}
					}
					$i ++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			
			$left_count = intval(($res_count-($page*$count)-$i));
			
			$meta['title'] = $lang['SoldInventory'];
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['inventory'],
					'total' => number_format($total, 2, '.', '')
				]));
			}
			tpl_set('analytics/sold_inventory/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'inventory' => $tpl_content['inventory'],
				'total' => number_format($total, 2, '.', '')
			], [], 'content');
		}
	break;
	
	/*
	* Issue statuses statustic
	*/
	case 'issues':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$object = $user['manager_report'] ? $user['store_id'] : intval($_REQUEST['object']);
			$date_start = $_REQUEST['date_start'] ? text_filter($_REQUEST['date_start'], 30, true) : '';
			$date_finish = $_REQUEST['date_finish'] ? text_filter($_REQUEST['date_finish'], 30, true) : '';
			$type = intval($_REQUEST['type']);
			$page = intval($_REQUEST['page']);
			$count = 20;
			
			if ($issues = db_multi_query('
				SELECT
					SQL_CALC_FOUND_ROWS
					i.id,
					u.id as intake_id,
					CONCAT(u.name, \' \', u.lastname) as intake_name,
					u.image
				FROM `'.DB_PREFIX.'_issues` i
				LEFT JOIN `'.DB_PREFIX.'_issues_changelog` c
					ON c.issue_id = i.id AND c.changes = \'status\' AND c.changes_id = 2
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = i.intake_id
				LEFT JOIN `'.DB_PREFIX.'_invoices` inv
					ON inv.issue_id = i.id
				WHERE c.id IS NOT NULL AND inv.conducted = 1
				'.(
					$object ? ' AND i.object_owner = '.$object : ''
				).(
					$type ? ($type == 1 ? ' AND i.intake_id = i.staff_id' : ($type == 2 ? ' AND i.intake_id != i.staff_id' : '')) : ''
				).(
					($date_start AND $date_finish) ? ' AND i.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND i.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).'
				ORDER BY c.id DESC
				LIMIT '.($page*$count).', '.$count
			, true)) { 
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				$cl = db_multi_query('
					SELECT 
						c.issue_id,
						c.user as user_id,
						s.name as status,
						u.id as staff_id,
						CONCAT(u.name, \' \', u.lastname) as staff_name
					FROM `'.DB_PREFIX.'_issues_changelog` c
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON c.changes_id = s.id
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = c.user
					WHERE c.issue_id IN ('.implode(',', array_column($issues, 'id')).') AND c.changes = \'status\'
					ORDER BY c.issue_id DESC, c.id ASC
				', true);
				
				$i = 0;
				foreach($issues as $iss) {
					$sts = array_filter($cl, function($a) use(&$iss) {
						if ($a['issue_id'] == $iss['id'])
							return $a;
					});
					$statuses = '<ul class="st-list">';
					if ($sts) {
						foreach($sts as $s) {
							$statuses .= '<li>'.$s['status'].' - <a href="/users/view/'.$s['staff_id'].'" onclick="Page.get(this.href); return false;">'.$s['staff_name'].'</a></li>';
						}
					}
					$statuses .= '</ul>';
					tpl_set('analytics/issues/item', [
						'id' => $iss['id'],
						'intake-id' => $iss['intake_id'],
						'intake-name' => $iss['intake_name'],
						'intake-ava' => $iss['image'],
						'statuses' => $statuses
					], [
						'intake-ava' => $iss['image']
					], 'issues');
					$i++;
				}
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			
			$meta['title'] = $lang['Issues'];
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['issues'],
				]));
			}
			tpl_set('analytics/issues/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'issues' => $tpl_content['issues']
			], [], 'content');
		}
	break;
	
	/*
	* Report
	*/
	case 'store_report':
		$meta['title'] = $lang['ObjectsReport'];
		if (in_to_array('1,2', $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			tpl_set('analytics/store_report', [
				'store-id' => $user['store_id']
			], [
				'view-report' => in_to_array('1,2', $user['group_ids']),
				'manager' => ($user['manager_report'] AND (!in_to_array(1, $user['group_ids']) AND !in_to_array(2, $user['group_ids'])))
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => $lang['noAcc']
			], [
			], 'content');
		}
	break;
	
	/*
	* Job report
	*/
	case 'job_report':
		$meta['title'] = $lang['JobsReport'];
		if (in_to_array('1,2', $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			tpl_set('analytics/job_report', [
				'store-id' => $user['store_id']
			], [
				'view-report' => in_to_array('1,2', $user['group_ids']),
				'manager' => ($user['manager_report'] AND (!in_to_array(1, $user['group_ids']) AND !in_to_array(2, $user['group_ids'])))
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => $lang['noAcc']
			], [
			], 'content');
		}
	break;
	
	/*
	* Expanses report
	*/
	case 'expanses_report':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			is_ajax() or die('Hacking attempt!');
			$objects = $user['manager_report'] ? $user['store_id'] : ids_filter($_POST['objects']);
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', strtotime(' - 7 days'));
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
			$total_expanses = 0;
			$salary = [];
			$salary_total = [];
			$oId = 0;
			
			$stores = '<table class="storesTotal">
				<tr>
					<th width="50%">'.$lang['Store'].'</th>
					<th>Total</th>
				</tr>';
				
			$sql = db_multi_query('SELECT DISTINCT 
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
					o.tax
				FROM `'.DB_PREFIX.'_users` u
				INNER JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id
				WHERE t.event = \'stop\''.(
				$objects ? ' AND o.id IN ('.$objects.')' : ' AND o.id > 0'
			).(
				($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND t.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 7 DAY)'
			).' GROUP BY t.user_id, t.object_id ORDER BY o.id', true);
			
			$o_info = db_multi_query('
				SELECT 
					SUM(o.paid) as paid,
					o.staff_id,
					o.object_id
				FROM `'.DB_PREFIX.'_users_onsite_changelog` o
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = o.staff_id
				WHERE 1 '.(
						$objects ? ' AND o.object_id IN ('.$objects.')' : ' AND o.object_id > 0'
					).(
						($date_start AND $date_finish) ? ' AND o.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND o.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND o.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
					).'
				GROUP BY o.staff_id, o.object_id ORDER BY o.object_id', true);
			
			$taxes = array_column(db_multi_query('
				SELECT 
					SUM(h.amount) as sum,
					i.object_id
				FROM `'.DB_PREFIX.'_invoices_history` h
				LEFT JOIN `'.DB_PREFIX.'_invoices` i
					ON i.id = h.invoice_id
				WHERE 1'.(
					($date_start AND $date_finish) ? ' AND h.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND h.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND h.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
				).'
				GROUP BY i.object_id', true), 'sum', 'object_id');
			
			foreach($sql as $row){
				$user_id = $row['user_id'];
				$object_id = $row['object_id'];
				
				$o_user = array_values(array_filter($o_info, function($v) use(&$user_id, &$object_id) {
					if ($v['staff_id'] == $user_id AND $v['object_id'] == $object_id)
						return $v;
				}, ARRAY_FILTER_USE_BOTH));
				
				$total = $row['seconds'];
				$s = $total % 60;
				$m = ($total % 3600 - $s) / 60;
				$h = ($total - $s - $m * 60) / 3600;
				
				if ($oId != $object_id) {
					if ($oId > 0) {
						$salary[$oId] .= '</table>';
					}
					$salary[$object_id] = '<table>
						<tr>
							<th>'.$lang['Staff'].'</th>
							<th>'.$lang['WorkingTime'].'</th>
							<th>'.$lang['Salary'].'</th>
							<th>'.$lang['osSalary'].'</th>
							<th>'.$lang['total'].'</th>
						</tr>';
					$oId = $object_id;
					$salary_total[$object_id] = 0;
				}
				
				$salary[$object_id] .= '
					<tr>
						<td><a href="/users/view/'.$row['user_id'].'" target="_blank">'.($row['image'] ? '<img src="/uploads/images/users/'.$row['user_id'].'/thumb_'.$row['image'].'" class="miniRound">' : '<span class="fa fa-user-secret miniRound"></span>').$row['name'].' '.$row['lastname'].'</a></td>
						<td>'.$h.':'.$m.':'.$s.'</td>
						<td>$-'.number_format($row['seconds']/3600*$row['pay'], 2, '.', '').($row['salary'] ? '/$'.number_format($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100), 2, '.', '') : '').'</td>
						<td>$-'.number_format($o_user[0]['paid'], 2, '.', '').' / $'.number_format($o_user[0]['paid']*((100 + $row['salary'])/100), 2, '.', '').'</td>
						<td>$-'.number_format(($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $row['salary'])/100)), 2, '.', '').'</td>
					</tr>';
					
				$salary_total[$object_id] += number_format(($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $row['salary'])/100)), 2, '.', '');
			}
			$salary[$object_id] .= '</table>';
				
			if ($objects_info = db_multi_query('
				SELECT 
					id, 
					name, 
					options,
					tax
				FROM `'.DB_PREFIX.'_objects` 
				WHERE 1 '.(
					$objects ? ' AND id IN('.$objects.')' : ''
				), true)) {
				foreach($objects_info as $o) {
					$options = '';
					$expanses = 0;
					if ($o['options']) {
						foreach(json_decode($o['options'], true) as $k => $v) {
							$options .= $k.': <span class="blue">$'.$v.'</span><br>';
							$expanses += $v;
						}
					}
					
					$expanses += number_format(($salary_total[$o['id']] + ($taxes[$o['id']] * $o['tax'] / 100)), 2, '.', '');
					
					$stores .= '<tr>
						<td>'.$o['name'].'</td>
						<td>$-'.$expanses.'</td>
					</tr>';
					
					tpl_set('analytics/expanses/item', [
						'name' => $o['name'],
						'taxes' => number_format(($taxes[$o['id']] * $o['tax'] / 100), 2, '.', ''),
						'salary' => $salary[$o['id']],
						'salary_total' => $salary_total[$o['id']],
						'expanses' => $options,
						'expanses_total' => $expanses
					], [
					], 'report');
					
					$total_expanses += $expanses;
				}
				
				$stores .= '<tr>
						<td>'.$lang['Total'].':</td>
						<td>$-'.number_format($total_expanses, 2, '.', '').'</td>
					</tr>
				</table>';
				
				print_r(json_encode([
					'report' => $tpl_content['report'],
					'total' => $stores
				]));
			}
			die;
		}
	break;
	
	/*
	* Expanses
	*/
	case 'expanses':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$meta['title'] = $lang['Expanses'];
			tpl_set('analytics/expanses/main', [
			], [
			], 'content');
		}
	break;
	
	/* 
	* Tradein, purchases store report
	*/
	case 'purchases_report':
		is_ajax() or die('Hacking attempt!');
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$page = intval($_POST['page']);
			$objects = $user['manager_report'] ? $user['store_id'] : ids_filter($_POST['objects']);
			$type = text_filter($_POST['type'], 20, false);
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', strtotime(' - 7 days'));
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
			$count = 20;
			
			$purchases_html = [];
			$tradeins_html = [];
			$totals = [
				'purchases' => [],
				'tradeins' => []
			];
			
			$stores = '<table class="storesTotal">
				<tr>
					<th width="50%">'.$lang['Store'].'</th>
					<th>'.$lang['Purchases'].'</th>
					<th>'.$lang['Tradeins'].'</th>
					<th>'.$lang['total'].'</th>
				</tr>';
			
			if (!$type OR $type == 'purchases') {
				$oid = 0;
				if ($purchases = db_multi_query('
					SELECT
						id,				
						price, 
						total,
						quantity,
						name, 
						sale_name,
						object_id
					FROM `'.DB_PREFIX.'_purchases`
					WHERE del = 0 '.(
						$objects ? 'AND object_id IN ('.$objects.')' : ''
					).(
						($date_start AND $date_finish) ? 'AND create_date >= CAST(\''.$date_start.'\' AS DATE) AND create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
					ORDER BY object_id ASC, id DESC', true)) {
					
					foreach($purchases as $p) {
						if ($oid != $p['object_id']) {
							if ($oid) {
								$purchases_html[$oid] .= '</tbody>
									</table>';
							}
							
							$purchases_html[$p['object_id']] = '<h3>'.$lang['Purchases'].'</h3>
								<table>
									<tbody>
										<tr>
											<th>'.$lang['Name'].'</th>
											<th>'.$lang['Price'].'</th>
										</tr>';
										
							$totals['purchases'][$p['object_id']] = 0;			
							$oid = $p['object_id'];
						}
						$price = floatval($p['price']) * $p['quantity'];
						$purchases_html[$p['object_id']] .= '<tr id="pur_'.$p['id'].'">
									<td><a href="/purchases/edit/'.$p['id'].'" target="_blank">'.($p['sale_name'] ?: $p['name']).'</a></td>
									<td>$-'.$price.'</td>
								</tr>';
						$totals['purchases'][$p['object_id']] += $price;
					}
					
					$purchases_html[$oid] .= '</tbody>
						</table>';
				}
			}
			
			$oid = 0;
			if (!$type OR $type == 'tradeins') {
				$oid = 0;
				if ($tradeins = db_multi_query('
					SELECT 
						i.id,
						i.purchase_price, 
						REPLACE(IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name), \'"\', \'\') as name, 
						i.object_id
					FROM `'.DB_PREFIX.'_inventory` i 
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c 
						ON i.category_id = c.id
					LEFT JOIN  `'.DB_PREFIX.'_inventory_models` m
						ON m.id = i.model_id
					LEFT JOIN  `'.DB_PREFIX.'_inventory_types` t
						ON t.id = i.type_id
					WHERE i.tradein = 1 '.(
						$objects ? 'AND i.object_id IN ('.$objects.')' : ''
					).(
						($date_start AND $date_finish) ? 'AND i.cr_date >= CAST(\''.$date_start.'\' AS DATE) AND i.cr_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
					ORDER BY i.object_id ASC, i.id DESC', true)) {
					
					foreach($tradeins as $p) {
						if ($oid != $p['object_id']) {
							if ($oid) {
								$tradeins_html[$oid] .= '</tbody>
									</table>';
							}
							
							$tradeins_html[$p['object_id']] = '<h3>'.$lang['Tradeins'].'</h3>
								<table>
									<tbody>
										<tr>
											<th>'.$lang['Name'].'</th>
											<th>'.$lang['Price'].'</th>
										</tr>';
										
							$oid = $p['object_id'];
							$totals['tradeins'][$p['object_id']] = 0;
						}
						$tradeins_html[$p['object_id']] .= '<tr>
									<td><a href="/inventory/view/'.$p['id'].'" target="_blank">'.$p['name'].'</a></td>
									<td>$-'.$p['purchase_price'].'</td>
								</tr>';
								
						$totals['tradeins'][$p['object_id']] += floatval($p['purchase_price']);
					}
					
					$tradeins_html[$oid] .= '</tbody>
						</table>';
				}
			}
			
			$stores_purchases = 0;
			$stores_tradeins = 0;
			foreach($objs = db_multi_query('
				SELECT 
					id, 
					name 
				FROM `'.DB_PREFIX.'_objects`
				WHERE 1 '.(
					$objects ? 'AND id IN ('.$objects.')' : ''
				).'
				ORDER BY id
			', true) as $o) {
				tpl_set('analytics/purchases/item', [
					'id' => $o['id'],
					'name' => $o['name'],
					'purchases_total' => -1*$totals['purchases'][$o['id']],
					'tradeins_total' => -1*$totals['tradeins'][$o['id']],
					'total' => -1*($totals['purchases'][$o['id']] + $totals['tradeins'][$o['id']]),
					'purchases' => $purchases_html[$o['id']],
					'tradeins' => $tradeins_html[$o['id']]
				], [], 'report');
				
				$stores .= '<tr>
					<td>'.$o['name'].'</td>
					<td>$'.$totals['purchases'][$o['id']].'</td>
					<td>$'.$totals['tradeins'][$o['id']].'</td>
					<td>$'.($totals['purchases'][$o['id']] + $totals['tradeins'][$o['id']]).'</td>
				</tr>';
				
				$stores_purchases += $totals['purchases'][$o['id']];
				$stores_tradeins += $totals['tradeins'][$o['id']];
			}
			
			$stores .= '<tr>
					<td>'.$lang['AllStores'].'</td>
					<td>$'.-1*$stores_purchases.'</td>
					<td>$'.-1*$stores_tradeins.'</td>
					<td class="red">$'.-1*($stores_purchases + $stores_tradeins).'</td>
				</tr>
			</tbody></table>';
			
			print_r(json_encode([
				'report' => $tpl_content['report'],
				'total' => $stores
			]));
			die;
		}
	break;
	
	/* 
	* Tradein, purchases store page
	*/
	case 'purchases':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$meta['title'] = $lang['purchaseTradein'];
			
			tpl_set('analytics/purchases/main', [
			], [], 'content');
		}
	break;
	
	/*
	* Trade in(purchases) stats
	*/
	case 'tradein':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$meta['title'] = $lang['tradeinStat'];
			$page = intval($_REQUEST['page']);
			$partial = intval($_REQUEST['partial']);
			$oId = $user['manager_report'] ? $user['store_id'] : intval($_REQUEST['object']);
			$query = text_filter($_REQUEST['query'], 255, false);
			$type = text_filter($_REQUEST['type'], 20, false);
			$date_start = $_REQUEST['date_start'] ? text_filter($_REQUEST['date_start'], 30, true) : '';
			$date_finish = $_REQUEST['date_finish'] ? text_filter($_REQUEST['date_finish'], 30, true) : '';
			$count = 20;
			
			$inventory = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS * FROM (
				(
					SELECT 
						i.id, 
						IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name) as name, 
						i.price as sale_price,
						i.purchase_price,
						i.cr_date as date,
						IF(i.tradein, \'tradein\', \'inventory\') as type
					FROM `'.DB_PREFIX.'_inventory` i 
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c 
						ON i.category_id = c.id
					LEFT JOIN  `'.DB_PREFIX.'_inventory_models` m
						ON m.id = i.model_id
					LEFT JOIN  `'.DB_PREFIX.'_inventory_types` t
						ON t.id = i.type_id
					
					WHERE i.del = 0 AND i.tradein = 1 '.(
						$oId ? ' AND i.object_id = '.$oId : ''
					).(
						$query ? ' AND IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name) LIKE \'%'.$query.'%\'' : ''
					).(
						$type == 'purchase' ? ' AND i.id = 0' : ''
					).(
						($date_start AND $date_finish) ? ' AND i.cr_date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND i.cr_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
				) UNION ALL (
					SELECT
						p.id,
						IF (p.sale_name != \'\', p.sale_name, p.name) as name,
						p.sale as sale_price,
						p.price as purchase_price,
						p.create_date as date,
						\'purchase\' as type
					FROM `'.DB_PREFIX.'_purchases` p
					WHERE p.confirmed = 1 AND p.del = 0 '.(
						$oId ? ' AND p.object_id = '.$oId : ''
					).(
						$type == 'inventory' ? ' AND p.id = 0' : ''
					).(
						$query ? ' AND IF(p.sale_name, p.sale_name, p.name) LIKE \'%'.$query.'%\'' : ''
					).(
						($date_start AND $date_finish) ? ' AND p.create_date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND p.create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).' 
				)) t_all ORDER BY date DESC LIMIT '.($page*$count).', '.$count.' '
			, true);
			
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			if (!$page) {
				$total = db_multi_query('
				(
					SELECT 
						SUM(i.price) as sale_total,
						SUM(i.purchase_price) as purchase_total,
						\'inventory\' as type
					FROM `'.DB_PREFIX.'_inventory` i 
					WHERE i.tradein = 1 '.(
						$oId ? ' AND i.object_id = '.$oId : ''
					).(
						$query2 ? ' AND IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name) LIKE \'%'.$query.'%\'' : ''
					).(
						$type == 'purchase' ? ' AND i.id = 0' : ''
					).(
						($date_start AND $date_finish) ? ' AND i.cr_date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND i.cr_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
				) UNION ALL (
					SELECT
						SUM(sale) as sale_total,
						SUM(price) as purchase_total,
						\'purchase\' as type
					FROM `'.DB_PREFIX.'_purchases`
					WHERE 1 '.(
						$oId ? ' AND object_id = '.$oId : ''
					).(
						$query ? ' AND IF(sale_name, sale_name, name) LIKE \'%'.$query.'%\'' : ''
					).(
						$type == 'inventory' ? ' AND id = 0' : ''
					).(
						($date_start AND $date_finish) ? ' AND create_date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).' 
				) LIMIT '.($page*$count).', '.$count
			, true);
			}
					/* LEFT JOIN  `'.DB_PREFIX.'_invoices` inv
						ON inv.inventory_info LIKE CONCAT(\'%"\', i.id, \'"%\') AND inv.conducted = 1
			
			LEFT JOIN  `'.DB_PREFIX.'_invoices` inv
						ON inv.purchases_info LIKE CONCAT(\'%"\', p.id, \'"%\') AND inv.conducted = 1
					LEFT JOIN  `'.DB_PREFIX.'_issues` iss
						ON iss.id = p.issue_id
					LEFT JOIN  `'.DB_PREFIX.'_invoices` inv2
						ON inv2.issue_id = iss.id AND inv2.conducted = 1 */
						
			$i = 0;
			if ($inventory) {
				$tinvoices = [];
				$pinvoices = [];
				
				if ($tids = array_column(array_filter($inventory, function($a) {
					if ($a['type'] == 'tradein')
						return $a;
				}), 'id')) {

					$tinvoices = db_multi_query('
						SELECT
							i.id,
							inv.id as inv,
							inv.conducted
						FROM `'.DB_PREFIX.'_inventory` i
						LEFT JOIN  `'.DB_PREFIX.'_issues` iss
							ON FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(iss.inventory_info, \'(?:"([0-9]+)":{([^{}]+)}(,?))\', \'\\\1\\\3\'), \'[{}]\', \'\'))
						LEFT JOIN  `'.DB_PREFIX.'_invoices` inv
							ON (FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(inv.inventory_info, \'(?:"([0-9]+)":{([^{}]+)}(,?))\', \'\\\1\\\3\'), \'[{}]\', \'\')) OR inv.issue_id = iss.id)
						WHERE '.($partial ? 'inv.conducted = 0 AND inv.paid > 0 ' : 'inv.paid > 0 ').' AND i.id IN ('.implode(',', $tids).')
						ORDER BY i.id DESC
					', true, false, function($a){
						return [$a['id'], $a];
					});

				}

				if ($pids = array_column(array_filter($inventory, function($a) {
					if ($a['type'] == 'purchase')
						return $a;
				}), 'id')) {

					$pinvoices = db_multi_query('
						SELECT
							i.id,
							inv.id as inv,
							inv.conducted
						FROM `'.DB_PREFIX.'_purchases` i
						LEFT JOIN `'.DB_PREFIX.'_issues` iss
							ON iss.id = i.issue_id
						LEFT JOIN  `'.DB_PREFIX.'_invoices` inv
							ON (FIND_IN_SET(i.id, REGEXP_REPLACE(REGEXP_REPLACE(inv.purchases_info, \'(?:"([0-9]+)":{([^{}]+)}(,?))\', \'\\\1\\\3\'), \'[{}]\', \'\')) OR inv.issue_id = iss.id)
						WHERE '.($partial ? 'inv.conducted = 0 AND inv.paid > 0 ' : 'inv.paid > 0 ').' AND i.id IN ('.implode(',', $pids).') 
						ORDER BY i.id DESC
					', true, false, function($a){
						return [$a['id'], $a];
					});
				}
				
				foreach($inventory as $inv) {
					tpl_set('/analytics/tradein/item', [
						'id' => '<a href="'.($inv['type'] == 'purchase' ? '/purchases/edit/' : '/inventory/view/').$inv['id'].'" target="_blank">'.$inv['id'].'</a>',
						'name' => $inv['name'],
						'sale_price' => number_format(floatval($inv['sale_price']), 2, '.', ''),
						'purchase_price' => number_format(floatval($inv['purchase_price']), 2, '.', ''),
						'proceed' => number_format(floatval($inv['sale_price']) - floatval($inv['purchase_price']), 2, '.', ''),
						'type' => $inv['type'],
						'invoice' => ($inv['type'] == 'tradein' ? ($tinvoices[$inv['id']]['inv'] ?? null) : ($pinvoices[$inv['id']]['inv'] ?? null))
					], [
						'purchased' => ($inv['type'] == 'tradein' ? isset($tinvoices[$inv['id']]) : isset($pinvoices[$inv['id']])),
						'partial' => (
							$inv['type'] == 'tradein' ? (
								isset($tinvoices[$inv['id']]) && $tinvoices[$inv['id']]['conducted'] == 0
							) : (
								isset($pinvoices[$inv['id']]) && $pinvoices[$inv['id']]['conducted'] == 0
							)
						)
					], 'stats');
					$i ++;
				}
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$total_p = floatval($total[0]['purchase_total']) + floatval($total[1]['purchase_total']);
			$total_s = floatval($total[0]['sale_total']) + floatval($total[1]['sale_total']);
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['stats'],
					'total' => $page ? 0 : [
						'total_purchase' => number_format($total_p, 2, '.', ''),
						'total_sale' => number_format($total_s, 2, '.', ''),
						'total_proceed' => number_format($total_s - $total_p, 2, '.', ''),
					]
				]));
			}
			tpl_set('/analytics/tradein/main', [
				'res_count' => $res_count,
				'query' => $query,
				'more' => $left_count ? '' : ' hdn',
				'stats' => $tpl_content['stats'],
				'total_purchase' => number_format($total_p, 2, '.', ''),
				'total_sale' => number_format($total_s, 2, '.', ''),
				'total_proceed' => number_format($total_s - $total_p, 2, '.', ''),
			], [
			], 'content');
		}
	break;

	/*
	*  Sales filter
	*/
	case 'sales': 
		is_ajax() or die('Hacking attempt!');
		$date = explode(' / ', ($_POST['date'] ? text_filter($_POST['date'], 30, true) : date('Y-m-d', strtotime(' - 10 days')).' / '.date('Y-m-d', time())));
		$object = ids_filter($_POST['object']);
		$cash = intval($_POST['cash']);
		$credit = intval($_POST['credit']);
		$check = intval($_POST['check']);
		$merchaine = intval($_POST['merchaine']);
		$date_type = '';
		$invoices_array = [];
		$invoices = [];
		$labels = [];
		$types = (($cash OR $credit) ? (($cash AND $credit) ? '\'cash\', \'credit\'' : ($cash ? '\'cash\'' : '\'credit\'')) : '');
		if ($check)
			$types .= ($types ? ', ' : '').'\'check\'';
		if ($merchaine)
			$types .= ($types ? ', ' : '').'\'merchaine\'';

		
		foreach(db_multi_query('
			SELECT '.(
				($_POST['date'] AND $date[0] == $date[1]) ? 'HOUR' : 'DATE'
			).'(i.date) as date,
				i.amount as total 
			FROM `'.DB_PREFIX.'_invoices_history` i
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON inv.id = i.invoice_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = inv.object_id
			WHERE'.(
				//$_POST['date'] ? ' i.date >= CAST(\''.$date[0].'\' AS DATE) AND i.date <= CAST(\''.$date[1].' 23:59:59\' AS DATETIME)' : ' i.date >= CURDATE()'
				$_POST['date'] ? ' i.date >= CAST(\''.$date[0].'\' AS DATE) AND i.date <= CAST(\''.$date[1].' 23:59:59\' AS DATETIME)' : ' i.date >= DATE_SUB(
					NOW(), INTERVAL 10 DAY
				) AND i.date <= CURDATE()'
			).(
				$object ? ' AND inv.object_id = '.$object : ''
			).(
				(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? ' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers))' : ''
			).(
				($cash OR $credit OR $check OR $merchaine) ? ' AND i.type IN ('.$types.')' : ''
			).'
			ORDER BY i.date ASC',
		true) as $item){
			$invoices_array[$item['date']] += $item['total'];
		}
		
		if ($_POST['date'] AND $date[0] == $date[1]) {
			$count = 23;
			$step = 1;
			foreach($invoices_array as $k => $v){
				$invoices[] = [$k, $v];
			}
		} else {
			$count = (strtotime($date[1]) - strtotime($date[0])) / 24 / 3600;
			$step = round($count / 15) ? round($count / 15) : 1;
			$date_i = date ("Y-m-d", strtotime($date[0]));
			
			for($i = 0; $i <= $count ; $i++) {
				if ($invoices_array[$date_i]) {
					$invoices[] = [$i, $invoices_array[$date_i], 'Date: '.$date_i.' Sales: $'.$invoices_array[$date_i]];
				}
				if (!($i % $step)) {
					$labels[] = date ("d-m", strtotime($date_i));
				}
				$date_i = date ("Y-m-d", strtotime("+1 day", strtotime($date_i)));
			}	
		}
		
		die(json_encode([
			'data' => $invoices,
			'labels' => $labels,
			'step' => $step,
			'last' => $count
		]));

	break;
	
	/*
	*  Points filter
	*/
	case 'points': 
		is_ajax() or die('Hacking attempt!');
		$date = explode(' / ', $_POST['date'] ? text_filter($_POST['date'], 30, true) : date('Y-m-d', strtotime(' - 10 days')).' / '.date('Y-m-d', time()));
		$object = ids_filter($_POST['object']);
		$puser = ids_filter($_POST['user']);
		$date_type = '';
		
		$invoices_array = [];
		$invoices = [];
		$labels = [];

		foreach(db_multi_query('
			SELECT '.(
					($_POST['date'] AND $date[0] == $date[1]) ? 'HOUR' : 'DATE'
				).'(p.date) as date, 
				p.point 
			FROM `'.DB_PREFIX.'_inventory_status_history` p
			LEFT JOIN `'.DB_PREFIX.'_inventory` i
				ON i.id = p.inventory_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = i.object_id
			WHERE'.(
				//$_POST['date'] ? ' p.date >= CAST(\''.$date[0].'\' AS DATE) AND p.date <= CAST(\''.$date[1].' 23:59:59\' AS DATETIME)' : ' date >= CURDATE()'
				$_POST['date'] ? ' p.date >= CAST(\''.$date[0].'\' AS DATE) AND p.date <= CAST(\''.$date[1].' 23:59:59\' AS DATETIME)' : ' p.date >= DATE_SUB(
					NOW(), INTERVAL 10 DAY
				) AND p.date <= CURDATE()'
			).(
				$puser ? ' AND p.staff_id = '.$puser : ''
			).(
				$object ? ' AND i.object_id = '.$object : ''
			).(
				(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? ' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers))' : ''
			).'
			ORDER BY p.date ASC',
		true) as $item){
			$invoices_array[$item['date']] += $item['point'];
		}
		
		if ($_POST['date'] AND $date[0] == $date[1]) {
			$count = 23;
			$step = 1;
			foreach($invoices_array as $k => $v){
				$invoices[] = [$k, $v];
			}
		} else {
			$count = (strtotime($date[1]) - strtotime($date[0])) / 24 / 3600;
			$step = round($count / 15) ? round($count / 15) : 1;
			$date_i = date ("Y-m-d", strtotime($date[0]));
			
			for($i = 0; $i <= $count ; $i++) {
				if ($invoices_array[$date_i]) {
					$invoices[] = [$i, $invoices_array[$date_i], 'Date: '.$date_i.' Sales: $'.$invoices_array[$date_i]];
				}
				if (!($i % $step)) {
					$labels[] = date ("d-m", strtotime($date_i));
				}
				$date_i = date ("Y-m-d", strtotime("+1 day", strtotime($date_i)));
			}	
		}
		
		die(json_encode([
			'data' => $invoices,
			'labels' => $labels,
			'step' => $step,
			'last' => $count
		]));

	break;
}

?>