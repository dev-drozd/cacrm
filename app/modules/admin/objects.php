<?php
/**
 * @appointment Objects admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]){
	
	/*
	* Open/Close store
	*/
	case 'close':
		$id = (int)$_POST['id'];
		$close = (int)$_POST['close'];
		db_query('UPDATE `'.DB_PREFIX.'_objects` SET close = '.$close.' WHERE id = '.$id);
		echo 'OK';
		die;
	break;
	
	/*
	* Get staff
	*/
	case 'staff':
		$id = intval($_POST['id']);
		$lId = intval($_POST['lId']);
		$query = text_filter($_POST['query'], 100, false);
		
		$managers = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
			u.id, 
			CONCAT(
				u.name, \' \', u.lastname
			) as name, 
			u.image 
		FROM `'.DB_PREFIX.'_users` u
		LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON FIND_IN_SET(u.id, o.managers) OR FIND_IN_SET(u.id, o.staff)
		WHERE u.del = 0 '.(
			$lId ? ' AND id < '.$lId : ''
		).(
			$id ? ' AND ((FIND_IN_SET(u.id, o.managers) OR FIND_IN_SET(u.id, o.staff)) AND o.id = '.$id.') OR FIND_IN_SET(1, u.group_ids) OR FIND_IN_SET(2, u.group_ids)' : ''
		).(
			$query ? ' AND CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\'': ''
		).' ORDER BY u.id DESC LIMIT 20', true);
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $managers,
			'count' => $res_count,
		]));
	break;
		
	
	/*
	* Report
	*/
	case 'report':
		$meta['title'] = 'Objects report';
		if (in_to_array('1,2', $user['group_ids'])) {
			tpl_set('objects/report', [
			], [
				'view-report' => in_to_array('1,2', $user['group_ids'])
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => 'You have no access to this page'
			], [
			], 'content');
		}
	break;
	
	
	case 'report_result':
		//is_ajax() or die('Hacking attempt!');

		if (in_to_array('1,2', $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			
			$objects = $user['manager_report'] ? $user['store_id'] : ids_filter($_REQUEST['objects']);
			$date_start = $_REQUEST['date_start'] ? text_filter($_REQUEST['date_start'], 30, true) : date('Y-m-d', time());//, strtotime(' - 7 days')
			$date_finish = $_REQUEST['date_finish'] ? text_filter($_REQUEST['date_finish'], 30, true) : date('Y-m-d', time());
		
			$stores = '<table class="responsive storesTotal">
				<thead>
					<tr>
						<th>Store</th>
						<th>Asset</th>
						<th>Tradein</th>
						<th>Purchases</th>
						<th>Expanses</th>
						<th>Salary</th>
						<th>Total</th>
					</tr>
				</thead>';
			$sum = db_multi_query('SELECT
								o.name,
								o.id as id,
								o.options, 
								o.tax,
								COUNT(ih.amount) as count,
								SUM(ih.amount) as total, 
								i.object_id,
								SUM(c.amount) as cash,
								SUM(cr.amount) as credit,
								SUM(ch.amount) as tcheck
							FROM `'.DB_PREFIX.'_objects` o
							LEFT JOIN `'.DB_PREFIX.'_invoices` i
								ON i.object_id = o.id
							LEFT JOIN `'.DB_PREFIX.'_invoices_history` ih
								ON ih.invoice_id = i.id '.(
								($date_start AND $date_finish) ? 
									' AND ih.date >= CAST(\''.$date_start.'\' AS DATE) AND ih.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
							).'
							LEFT JOIN `'.DB_PREFIX.'_invoices_history` c
								ON c.id = ih.id AND c.type = \'cash\'
							LEFT JOIN `'.DB_PREFIX.'_invoices_history` cr
								ON cr.id = ih.id AND cr.type = \'credit\'
							LEFT JOIN `'.DB_PREFIX.'_invoices_history` ch
								ON ch.id = ih.id AND ch.type = \'check\'
							WHERE 1 '.($objects ? 'AND i.object_id IN ('.$objects.')' : '').'
							GROUP BY o.id ORDER BY o.id', true);
			$lack = db_multi_query('SELECT
								object_id,
								SUM(lack) as lack
							FROM `'.DB_PREFIX.'_cash`
							WHERE 1 '.($objects ? 'AND object_id IN ('.$objects.')' : '').'
								AND type = \'cash\'
							'.(
								($date_start AND $date_finish) ? 
									  ' AND date >= CAST(\''.$date_start.'\' AS DATE) AND date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
							).'
							GROUP BY object_id ORDER BY object_id', true);
			$status = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_status`', true);
			
			$statuses_html = '';
			foreach($status as $st) {
				$statuses_html .= '<option value="'.$st['id'].'">'.$st['name'].'</option>';
			}
			
			// working time
			$timers = db_multi_query('
				SELECT DISTINCT
					t.*,
					o.salary_tax,
					SEC_TO_TIME(t.seconds) as seconds,
					t.seconds as seconnds2,
					u.name,
					u.lastname,
					u.image,
					u.pay,
					o.id as object_id
				FROM `'.DB_PREFIX.'_timer` t
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON t.user_id = u.id 	
				INNER JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id
				WHERE t.event = \'stop\''.(
						$objects ? 'AND t.object_id IN ('.$objects.')' : 'AND t.object_id > 0'
					).'
				'.(
					($date_start AND $date_finish) ? 
						  ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).'
				ORDER BY t.object_id, t.user_id, t.date DESC LIMIT 0, 15',
				true);		
			$timer = [];
			$oid = '';
			foreach($timers as $item){
				if (!$timer[$item['object_id']])
					$timer[$item['object_id']] = '';
				$timer[$item['object_id']] .= '<tr>
						<td class="lh45">
							<a href="/users/view/'.$item['user_id'].'" target="_blank">
								'.(
									$item['image'] ?
										'<img src="/uploads/images/users/'.$item['user_id'].'/thumb_'.$item['image'].'" class="miniRound">' :
									'<span class="fa fa-user-secret miniRound"></span>'
								).'
								'.$item['name'].' '.$item['lastname'].'
							</a>
						</td>
						<td data-label="Punch in:">'.$item['date'].'</td>
						<td data-label="Punch out:">'.$item['control_point'].'</td>
						<td data-label="Working time:">'.$item['seconds'].'</td>
						<td data-label="Salary:">$'.(number_format($item['seconnds2']/3600*$item['pay'], 2, '.', '').($item['salary_tax'] ? ' / '.'$'.number_format($item['seconnds2']/3600*$item['pay']+((($item['seconnds2']/3600*$item['pay'])/100)*$item['salary_tax']), 2, '.', '') : '')).'</td>
					</tr>';
			}
			
			// issue status changed
			$html_header = '<div class="issue_status"><h3>Issue status changes</h3><table class="responsive">
				<thead><tr><th class="wtUser">User</th>';
			$sts = [];
			foreach($status as $st) {
				$sts[] = $st['name'];
				$html_header .= '<th class="wtSt"><span class="hnt hntTop" data-title="'.$st['name'].'">'.substr($st['name'], 0, 3).'</span></th>';
			}
			$html_header .= '</tr></thead><tbody>';
			$status_ids = array_column($status, 'id');
			$user_id = 0;
			$users = [];
			$statuses_total = [];
			$statuses = db_multi_query('SELECT
							CONCAT(u.name, \' \', u.lastname) as name,
							sh.user as user_id,
							s.name as status,
							IF(sh.changes = \'New issue\', 11, sh.changes_id) as status_id,
							sh.object_id as object_id,
							COUNT(sh.changes_id) as count_status
						FROM `'.DB_PREFIX.'_issues_changelog` sh
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = sh.user
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = IF(sh.changes = \'New issue\', 11, sh.changes_id)
						WHERE sh.changes IN (\'status\', \'New issue\') AND u.id NOT IN (2,17,16)'.($object ? ' AND sh.object_id IN ('.$object.')' : ' AND sh.object_id > 0').'
						'.(
							($date_start AND $date_finish) ? 
								  ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						GROUP BY sh.user, s.id ORDER BY sh.user, s.id', true);

			foreach($statuses as $row) {
				$users[$row['object_id']][$row['user_id']]['statuses'][$row['status_id']] = $row['count_status'];
				$users[$row['object_id']][$row['user_id']]['name'] = $row['name'];
				
				if (!$statuses_total[$row['object_id']][$row['status_id']])
					$statuses_total[$row['object_id']][$row['status_id']] = 0;
				$statuses_total[$row['object_id']][$row['status_id']] += $row['count_status'];
			}

			$html_statuses = [];
			foreach($users as $io => $obj) {
				$html_statuses[$io] = $html_header;
				foreach($obj as $iu => $usr) {
						$html_statuses[$io] .= '<tr><td data-label="User: "><a href="/users/view/'.$iu.'" target="_blank">'.$usr['name'].'</a></td>';
						$si = 0;
						foreach($status_ids as $i => $st) {
							$html_statuses[$io] .= '<td data-label="'.$sts[$si].'">'.(
								$usr['statuses'][$st] ? '<a href="/activity/issues/?sDate='.$date_start.'
									&eDate='.$date_finish.'
									&staff_name='.$usr['name'].'
									&staff='.$iu.'
									&status_issues='.$st.'">'.$usr['statuses'][$st].'</a>' : '0'
							).'</td>';
							$si++;
						}
						$html_statuses[$io] .= '</tr>';
				}
				if ($statuses_total[$io]) {
					$html_statuses[$io] .= '<tr><td>Total</td>';
					$si = 0;
					foreach($status as $st) {
						$html_statuses[$io] .= '<td data-label="'.$sts[$si].'">'.($statuses_total[$io][$st['id']] ? $statuses_total[$io][$st['id']] : 0).'</td>';
						$si++;
					}
					$html_statuses[$io] .= '</tr>';
				}
				$html_statuses[$io] .= '</tbody></table></div>';
			}
					
					
			$tran = db_multi_query('SELECT 
										i.id, 
										ih.date, 
										i.customer_id,
										ih.type,
										ih.amount,
										i.object_id,
										u.name,
										u.lastname
									FROM `'.DB_PREFIX.'_invoices_history` ih
									LEFT JOIN `'.DB_PREFIX.'_invoices` i
										ON i.id = ih.invoice_id
									LEFT JOIN `'.DB_PREFIX.'_users` u
										ON u.id = i.customer_id
									WHERE 1 '.($object ? ' AND i.object_id IN ('.$object.')' : ' AND i.object_id > 0').' 
									'.(
										($date_start AND $date_finish) ? 
											  ' AND ih.date >= CAST(\''.$date_start.'\' AS DATE) AND ih.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
									).'
									ORDER BY i.object_id, ih.date', true);
			$transacions = [];
			$tId = 0;
			foreach($tran as $i => $row){
				if ($tId != $row['object_id']) {
					if ($tId != 0) $transacions[$tId] .= '</table></div>';
					$transacions[$row['object_id']] = '<div class="transactions"><h3>Transactions</h3><table class="responsive">
															<thead>
																<tr>
																	<th>Invoice</th>
																	<th>Customer</th>
																	<th>Date</th>
																	<th>Pay method</th>
																	<th>Total</th>
																</tr>
															</thead>';
					$tId = $row['object_id'];
				}
				
				$transacions[$row['object_id']] .= '<tr>
														<td data-label="Invoice:"><a href="/invoices/view/'.$row['id'].'" target="_blank">'.$row['id'].'</a></td>
														<td data-label="Customer:">'.($row['name'] ? '<a href="/users/view/'.$row['customer_id'].'" target="_blank">'.$row['name'].' '.$row['lastname'].'</a>' : 'Quick sell').'</td>
														<td data-label="Date:">'.$row['date'].'</td>
														<td data-label="Pay method:">'.$row['type'].'</td>
														<td data-label="Total:">$'.number_format($row['amount'], 2, '.', '').'</td>
													</tr>';			
			}
			$transacions[$tId] .= '</table></div>';
			
			$purchases_html = [];
			$tradeins_html = [];
			$totals = [
				'purchases' => [],
				'tradeins' => []
			];
			
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
					$object ? 'AND object_id IN ('.$object.')' : ''
				).(
					($date_start AND $date_finish) ? 'AND create_date >= CAST(\''.$date_start.'\' AS DATE) AND create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).'
				ORDER BY object_id ASC, id DESC', true)) {
				
				foreach($purchases as $p) {
					if ($oid != $p['object_id']) {
						if ($oid) {
							$purchases_html[$oid] .= '</tbody></table>';
						}
						
						$purchases_html[$p['object_id']] = '<h3>Purchases</h3>
							<table class="responsive">
								<thead>
									<tr>
										<th>Name</th>
										<th>Price</th>
									</tr>
								</thead><tbody>';
									
						$totals['purchases'][$p['object_id']] = 0;			
						$oid = $p['object_id'];
					}
					$price = floatval($p['price']) * $p['quantity'];
					$purchases_html[$p['object_id']] .= '<tr id="pur_'.$p['id'].'">
								<td data-label="Name:"><a href="/purchases/edit/'.$p['id'].'" target="_blank">'.($p['sale_name'] ?: $p['name']).'</a></td>
								<td data-label="Price:">$-'.$price.'</td>
							</tr>';
					$totals['purchases'][$p['object_id']] += $price;
				}
				
				$purchases_html[$oid] .= '</tbody></table>';
			}
			
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
					$object ? 'AND i.object_id IN ('.$object.')' : ''
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
						
						$tradeins_html[$p['object_id']] = '<h3>Tradeins</h3>
							<table class="responsive">
								<thead>
									<tr>
										<th>Name</th>
										<th>Price</th>
									</tr>
								</thead><tbody>';
									
						$oid = $p['object_id'];
						$totals['tradeins'][$p['object_id']] = 0;
					}
					$tradeins_html[$p['object_id']] .= '<tr>
								<td data-label="Name:"><a href="/inventory/view/'.$p['id'].'" target="_blank">'.$p['name'].'</a></td>
								<td data-label="Price:">$-'.$p['purchase_price'].'</td>
							</tr>';
							
					$totals['tradeins'][$p['object_id']] += floatval($p['purchase_price']);
				}
				
				$tradeins_html[$oid] .= '</tbody>
					</table>';
			}
			
			// expanses (object expanses + salary)
			$salary = [];
			$salary_total = [];
			$oId = 0;
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
					o.points_equal as points_equal
				FROM `'.DB_PREFIX.'_users` u
				INNER JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id
				WHERE t.event = \'stop\''.(
				$object ? ' AND o.id IN ('.$object.')' : ' AND o.id > 0'
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
						$object ? ' AND o.object_id IN ('.$object.')' : ' AND o.object_id > 0'
					).(
						($date_start AND $date_finish) ? ' AND o.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND o.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND o.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
					).'
				GROUP BY o.staff_id, o.object_id ORDER BY o.object_id', true);
			
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
						$salary[$oId] .= '</tbody></table>';
					}
					$salary[$object_id] = '<h3>Salary</h3><table class="responsive">
						<thead>
							<tr>
								<th>Staff</th>
								<th>Working time</th>
								<th>Salary</th>
								<th>On site Salary</th>
								<th>Total</th>
							</tr>
						</thead><tbody>';
					$oId = $object_id;
					$salary_total[$object_id] = 0;
				}
				
				$salary[$object_id] .= '
					<tr>
						<td><a href="/users/view/'.$row['user_id'].'" target="_blank">'.($row['image'] ? '<img src="/uploads/images/users/'.$row['user_id'].'/thumb_'.$row['image'].'" class="miniRound">' : '<span class="fa fa-user-secret miniRound"></span>').$row['name'].' '.$row['lastname'].'</a></td>
						<td data-label="Working time:">'.$h.':'.$m.':'.$s.'</td>
						<td data-label="Salary:">$-'.number_format($row['seconds']/3600*$row['pay'], 2, '.', '').($row['salary'] ? '/$'.number_format($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100), 2, '.', '') : '').'</td>
						<td data-label="On site Salary:">$-'.number_format($o_user[0]['paid'], 2, '.', '').' / $'.number_format($o_user[0]['paid']*((100 + $row['salary'])/100), 2, '.', '').'</td>
						<td data-label="Total:">$-'.number_format(($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $row['salary'])/100)), 2, '.', '').'</td>
					</tr>';
					
				$salary_total[$object_id] += number_format(($row['seconds']/3600*$row['pay']*((100 + $row['salary'])/100) + $o_user[0]['paid']*((100 + $row['salary'])/100)), 2, '.', '');
			}
			$salary[$object_id] .= '</tbody></table>';
			
			$total = [
				'all' => 0,
				'asset' => 0,
				'tradeins' => 0,
				'purchases' => 0,
				'expanses' => 0,
				'salary' => 0
			];
			$days = abs(round((strtotime($date_start) - strtotime($date_finish))/86400)) + 1;
			foreach($sum as $i => $row){
				$options = '';
				$expanses = 0;
				if ($row['options']) {
					foreach(json_decode($row['options'], true) as $k => $v) {
						$options .= $k.': <span class="blue">$'.number_format((floatval($v) / 30 * $days), 2, '.', '').'</span><br>';
						$expanses += (floatval($v) / 30 * $days);
					}
				}
				
				//$expanses += $salary_total[$row['object_id']];
					
				$stotal = $row['total'] - ($totals['purchases'][$row['object_id']] + $totals['tradeins'][$row['object_id']]) - $expanses - $salary_total[$row['object_id']];
				$total['all'] += $stotal;
				$total['asset'] += $row['total'];
				$total['tradeins'] -= $totals['tradeins'][$row['object_id']];
				$total['purchases'] -= $totals['purchases'][$row['object_id']];
				$total['expanses'] -= $expanses;
				$total['salary'] -= $salary_total[$row['object_id']];
				$stores .= '<tr>
					<td data-label="Store:">'.$row['name'].'</td>
					<td data-label="Asset:">$'.number_format($row['total'], 2, '.', '').'</td>
					<td data-label="Tradein:">$'.number_format(-1*$totals['tradeins'][$row['object_id']], 2, '.', '').'</td>
					<td data-label="Purchases:">$'.number_format(-1*$totals['purchases'][$row['object_id']], 2, '.', '').'</td>
					<td data-label="Expanses:">$'.number_format(-1*$expanses, 2, '.', '').'</td>
					<td data-label="Salary:">$'.number_format(-1*$salary_total[$row['object_id']], 2, '.', '').'</td>
					<td data-label="Total:" class="'.(floatval($stotal) < 0 ? 'red' : 'green').'">$'.number_format($stotal, 2, '.', '').'</td>
				</tr>';
				tpl_set('objects/reports/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'count' => $row['count'],
					'statuses' => $statuses_html,
					'total' => '$'.number_format($stotal ?: 0, 2, '.', ''),
					'tax' => '$'.number_format($row['total'] ? $row['total']*$row['tax']/100 : 0, 2, '.', ''),
					'cash' => '$'.number_format($row['cash'] ?: 0, 2, '.', ''),
					'lack' => '$'.number_format($lack[$i]['lack'] ?: 0, 2, '.', ''),
					'credit' => '$'.number_format($row['credit'] ?: 0, 2, '.', ''),
					'check' => '$'.number_format($row['tcheck'] ?: 0, 2, '.', ''),
					'tclass' => (floatval($row['total']) < 0 ? 'red' : 'blue'),
					'cclass' => (floatval($row['cash']) < 0 ?'red' : 'blue'),
					'crclass' => (floatval($row['credit']) < 0 ? 'red' : 'blue'),
					'chclass' => (floatval($row['tcheck']) < 0 ? 'red' : 'blue'),
					'lclass' => (floatval($lack[$i]['lack']) < 0 ? 'red' : 'green'),
					'timer' => $timer[$row['object_id']] ? '<h3>Working time</h3>
								<table class="responsive">
									<thead>
										<tr>
											<th class="wp20">Staff</th>
											<th>Punch in</th>
											<th>Punch out</th>
											<th>Working time</th>
											<th>Salary</th>
										</tr>
									</thead>
									<tbody>
									'.$timer[$row['object_id']].'</tbody>
								</table>' : '',
					'Transactions' => $transacions[$row['object_id']] ?: '',
					'status_changes' => $html_statuses[$row['object_id']] ?: '',
					'purchases_total' => $totals['purchases'][$row['object_id']],
					'tradeins_total' => $totals['tradeins'][$row['object_id']],
					'purchases' => $purchases_html[$row['object_id']],
					'tradeins' => $tradeins_html[$row['object_id']],
					'expanses' => $options,
					'expanses_total' => $expanses,
					'salary' => $salary[$row['object_id']],
					'salary_total' => $salary_total[$row['object_id']] ?: 0
				], [
				], 'report');
			}
			$stores .= '<tr>
				<td>All stores</td>
				<td>$'.number_format($total['asset'], 2, '.', '').'</td>
				<td>$'.number_format($total['tradeins'], 2, '.', '').'</td>
				<td>$'.number_format($total['purchases'], 2, '.', '').'</td>
				<td>$'.number_format($total['expanses'], 2, '.', '').'</td>
				<td>$'.number_format($total['salary'], 2, '.', '').'</td>
				<td class="'.(floatval($total['all']) < 0 ? 'red' : 'green').'">$'.number_format($total['all'], 2, '.', '').'</td>
			</tr>';
			if(isset($_GET['debug']))
				echo db_debug();
			else {
				print_r(json_encode([
					'report' => $tpl_content['report'],
					'total' => $stores
				]));
			}
		}
		die;
	break;
	
	/*
	* Mini reports
	*/
	case 'mini_reports':
		is_ajax() or die('Hacking attempt!');
	
		$type = text_filter($_POST['type'], 50, true);
		$object = intval($_POST['object']);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		
		switch ($type) {
			case 'working_time':
				$html = '<div class="working_time"><h3>Users who worked</h3><table>
					<tr><th class="wtUser">User</th><th class="wtDate">Date</th></tr>';
				$user_id = 0;
				$time = db_multi_query('SELECT
								CONCAT(u.name, \' \', u.lastname) as name,
								a.user_id as user_id,
								DATE(a.date) as date
							FROM `'.DB_PREFIX.'_activity` a
							LEFT JOIN `'.DB_PREFIX.'_users` u
								ON u.id = a.user_id
							WHERE a.object_id IN ('.$object.') 
							AND a.event = \'start working time\'
							'.(
								($date_start AND $date_finish) ? 
									  ' AND a.date >= CAST(\''.$date_start.'\' AS DATE) AND a.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
							).'
							ORDER BY a.user_id', true);
							
				foreach($time as $i => $row) {
					if ($user_id != $row['user_id']) {
						if ($user_id != 0)
							$html .= '</tr>';
						$html .= '<tr>
							<td>'.$row['name'].'</td><td>'.$row['date'];
						$user_id = $row['user_id'];
					} else
						$html .= ', '.$row['date'];
				}	
				$html .= '</tr></table></div>';
				echo $html;
			break;
			
			case 'issue_status':
				$html = '<div class="issue_status"><h3>Issue status changes</h3><table>
					<tr><th class="wtUser">User</th>';
				foreach($status = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_status`', true) as $st) {
					$html .= '<th class="wtSt">'.$st['name'].'</th>';
				}
				$html .= '</tr>';
				$status = array_column($status, 'id');
				$user_id = 0;
				$users = [];
				$statuses = db_multi_query('SELECT
								CONCAT(u.name, \' \', u.lastname) as name,
								sh.staff_id as user_id,
								s.name as status,
								sh.status_id as status_id,
								COUNT(sh.status_id) as count_status
							FROM `'.DB_PREFIX.'_inventory_status_history` sh
							LEFT JOIN `'.DB_PREFIX.'_users` u
								ON u.id = sh.staff_id
							LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
								ON s.id = sh.status_id
							WHERE sh.object_id IN ('.$object.') 
							'.(
								($date_start AND $date_finish) ? 
									  ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
							).'
							GROUP BY sh.staff_id, sh.status_id ORDER BY sh.staff_id, sh.status_id', true);
				foreach($statuses as $row) {
					$users[$row['user_id']]['statuses'][$row['status_id']] = $row['count_status'];
					$users[$row['user_id']]['name'] = $row['name'];
				}
				foreach($users as $usr) {
						$html .= '<tr><td>'.$usr['name'].'</td>';
						foreach($status as $i => $st) {
							$html .= '<td>'.($usr['statuses'][$i] ? $usr['statuses'][$i] : '0').'</td>';
						}
						$html .= '</tr>';
				}
				$html .= '</table></div>';
				echo $html;
			break;
			
			case 'transactions':
				$tran = db_multi_query('SELECT 
											i.id, 
											i.date, 
											i.customer_id,
											i.pay_method,
											i.total,
											u.name,
											u.lastname
										FROM `'.DB_PREFIX.'_invoices` i
										LEFT JOIN `'.DB_PREFIX.'_users` u
											ON u.id = i.customer_id
										WHERE i.object_id IN ('.$object.') 
										AND i.conducted = 1
										'.(
											($date_start AND $date_finish) ? 
												  ' AND i.date >= CAST(\''.$date_start.'\' AS DATE) AND i.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
										).'
										ORDER BY i.date', true);
				foreach($tran as $i => $row){
					tpl_set('objects/reports/invoice', [
						'id' => $row['id'],
						'date' => $row['date'],
						'pay_method' => $row['pay_method'],
						'total' => '$'.number_format($row['total'], 2, '.', ''),
						'customer' => $row['name'].' '.$row['lastname']
					], [
					], 'report');
				}
				echo '<div class="transactions"><h3>Transactions</h3><table>
						<tr>
							<th>ID</th>
							<th>Customer</th>
							<th>Date</th>
							<th>Pay method</th>
							<th>Total</th>
						</tr>	
						'.$tpl_content['report'].'
					</table></div>';
			break;
			
			case 'status_issues':
				$status = text_filter($_POST['status'], 8, false);

				$stats = db_multi_query('SELECT 
											i.id, 
											i.date, 
											d.customer_id,
											d.object_id,
											u.name,
											u.lastname,
											s.name as status_name,
											m.name as model_name,
											b.name as brand_name,
											d.model as model,
											o.name as object_name
										FROM `'.DB_PREFIX.'_issues_changelog` c
										LEFT JOIN `'.DB_PREFIX.'_issues` i
											ON i.id = c.issue_id
										LEFT JOIN `'.DB_PREFIX.'_inventory` d
											ON d.id = i.inventory_id
										LEFT JOIN `'.DB_PREFIX.'_users` u
											ON u.id = d.customer_id
										LEFT JOIN `'.DB_PREFIX.'_objects` o
											ON o.id = d.object_id
										LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
											ON s.id = d.status_id
										LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
											ON m.id = d.model_id
										LEFT JOIN `'.DB_PREFIX.'_inventory_categories` b
											ON b.id = d.category_id
										WHERE d.del = 0 AND d.type AND  c.object_id IN ('.$object.') 
										AND c.changes_id = \''.$status.'\'
										'.(
											($date_start AND $date_finish) ? 
												  ' AND c.date >= CAST(\''.$date_start.'\' AS DATE) AND c.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
										).'
										ORDER BY c.date', true);
				if ($stats) {
					foreach($stats as $i => $row){
						tpl_set('objects/reports/issue', [
							'date' => '<a href="/issues/view/'.$row['id'].'" target="_blank">'.$row['date'].'</a>',
							'model' => $row['brand'].' '.$row['model_name'].' '.$row['model'],
							'status' => $row['status_name'],
							'name' => $row['name'] ? '<a href="/users/view/'.$row['customer_id'].'" target="_blank">'.$row['name'].' '.$row['lastname'].'</a>' : '<a href="/objects/edit/'.$row['object_id'].'" target="_blank">'.$row['object_name'].'</a>' 
						], [
						], 'report');
					}
					$stat = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_status` WHERE id =\''.$status.'\'');
					echo '<div class="status_issues_'.$status.'"><h3>Issues('.($status == 'new' ? 'New' : ($status == 'finished' ? 'Finished' : $stat['name'])).')</h3><table>
							<tr>
								<th>Date</th>
								<th>Customer</th>
								<th>Model</th>
								<th>Status</th>
							</tr>	
							'.$tpl_content['report'].'
						</table></div>';
				} else {
					$stat = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_status` WHERE id =\''.$status.'\'');
					echo '<div class="status_issues_'.$status.'"><h3>Issues('.($status == 'new' ? 'New' : ($status == 'finished' ? 'Finished' : $stat['name'])).')</h3><div class="noContent">No info</div></div>';
				}
			break;
		}
		die;
	break;
	
	/*
	* View devices
	*/
	case 'devices':
		$id = $route[2];
		
		$o = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_objects` WHERE id = '.$id);
		
		if($devices = db_multi_query('
			SELECT
				tb1.id, tb1.model,
				tb2.name as type_name,
				tb3.name as location_name,
				tb4.name as os_name,
				tb6.name as category_name
			FROM `'.DB_PREFIX.'_inventory` tb1
			LEFT JOIN `'.DB_PREFIX.'_inventory_types` tb2
				ON tb1.type_id = tb2.id
			LEFT JOIN `'.DB_PREFIX.'_objects_locations` tb3
				ON tb1.location_id = tb3.id
			LEFT JOIN `'.DB_PREFIX.'_inventory_os` tb4
				ON tb1.os_id = tb4.id
			LEFT JOIN `'.DB_PREFIX.'_inventory_types` tb5
				ON tb1.type_id = tb5.id
			LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb6
				ON tb1.category_id = tb6.id
			WHERE tb1.customer_id = 0 AND tb1.object_id = '.$id, true
		)){
			foreach($devices as $item){
				foreach(db_multi_query(
					'SELECT i.*, u.name as staff_name, u.lastname FROM `'.DB_PREFIX.'_issues` i INNER JOIN `'.DB_PREFIX.'_users` u ON i.staff_id = u.id WHERE inventory_id = '.$item['id']
				, true) as $issue){
					tpl_set('/cicle/invIssue', [
						'id' => $issue['id'],
						'staff-id' => $issue['staff_id'],
						'staff-name' => $issue['staff_name'],
						'staff-lastname' => $issue['lastname'],
						'description' => $issue['description'],
						'date' => $issue['date']
					], [], 'issues');
				}
				tpl_set('/cicle/inventory', [
					'id' => $item['id'],
					'user-name' => $u['name'],
					'user-lastname' => $u['lastname'],
					'name' => $item['name'],
					'model' => $item['model'],
					'os' => $item['os_name'],
					'category' => $item['category_name'],
					'location' => $item['location_name'],
					'issues' => $tpl_content['issues'],
					'type' => $item['type_name']
				], [
					'user' => $route[0] == 'users',
					'has_issue' => $tpl_content['issues']
				], 'devices');
				unset($tpl_content['issues']);
			}
		}
		
		tpl_set('objects/devices', [
			'id' => $id,
			'title' => $o['name'],
			'devices' => $tpl_content['devices'] ?? '<div class="noContent">No devices</div>'
		], [
			'view-report' => in_to_array('1,2', $user['group_ids'])
		], 'content');
		$meta['title'] = $o['name'];
	break;
	
	/*
	* Get locations
	*/
	case 'get_locations':
		is_ajax() or die('Hacking attempt!');
		
		// Filters data
		$lId = intval($_REQUEST['lId']);
		$oId = intval($_REQUEST['oId']);
		$nIds = ids_filter($_REQUEST['nIds']);
		$query = text_filter($_REQUEST['query'], 100, false);
		$id = array_flip($config['object_ips']);

		// SQL
		if($sId = intval($_REQUEST['sId'])){
			$objects = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
				tb2.id, tb2.name, tb2.count, tb3.name as object, tb2.object_id
				FROM `'.DB_PREFIX.'_inventory_'.(intval($_REQUEST['warranty']) ? 'warranty_' : '').'status` tb1 
				INNER JOIN `'.DB_PREFIX.'_objects_locations` tb2 ON FIND_IN_SET(
					tb2.id, tb1.locations
				) 
				LEFT JOIN `'.DB_PREFIX.'_objects` tb3
					ON tb3.id = tb2.object_id
				WHERE tb1.id = '.$sId.(
				$nIds ? ' AND tb2.id NOT IN('.$nIds.')' : ''
			).(
				$lId ? ' AND tb2.id < '.$lId : ''
			).(
				$oId ? ' AND tb2.object_id = '.$oId : ' AND tb2.object_id < 0'
			).(
				$query ? ' AND tb2.name LIKE \'%'.$query.'%\'': ''
			).' ORDER BY tb2.id DESC LIMIT 20', true);	
		} else {
			$objects = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
				tb1.id, tb1.name, tb2.name as object, tb1.count, tb1.object_id
				FROM `'.DB_PREFIX.'_objects_locations` tb1
				LEFT JOIN `'.DB_PREFIX.'_objects` tb2
					ON tb2.id = tb1.object_id
				WHERE 1'.(
				$nIds ? ' AND tb1.id NOT IN('.$nIds.')' : ''
			).(
				$lId ? ' AND tb1.id < '.$lId : ''
			).(
				$oId ? ' AND tb1.object_id = '.$oId : ' AND tb1.object_id < 0'
			).(
				$query ? ' AND tb1.name LIKE \'%'.$query.'%\'': ''
			).' ORDER BY tb1.id DESC LIMIT 20', true);
		}
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $objects,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Delete location
	*/
	case 'del_location':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['add_location']){
			db_query('DELETE FROM `'.DB_PREFIX.'_objects_locations` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	* Save location
	*/
	case 'save_location':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$name = text_filter($_POST['name'], 25, false);
		$count = intval($_POST['count']);
		$object = intval($_POST['object']);
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_objects_locations` SET
					name = \''.$name.'\',
					count = \''.$count.'\',
					object_id = '.$object.(
				$id ? ' WHERE id = '.$id : ''
			));
			echo 'OK';
		die;
	break;
	
	/*
	* Locations
	*/
	case 'locations':
		$meta['title'] = $lang['Locations'];
		$id = intval($route[2]);
		if($user['add_location']){
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			$object = db_multi_query('SELECT 
				name as object_name,
				id as object_id
			FROM `'.DB_PREFIX.'_objects` WHERE id = '.$id.' AND franchise_id = '.$user['franchise_id']);
			
			if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
				tb1.*, 
				tb2.name as object_name 
			FROM `'.DB_PREFIX.'_objects_locations` tb1 
			LEFT JOIN `'.DB_PREFIX.'_objects` tb2 
				ON tb1.object_id = tb2.id WHERE 1'.(
				$query ? ' AND tb1.name LIKE \'%'.$query.'%\' ' : ''
			).' AND tb1.object_id = '.$id.'
			 ORDER BY tb1.id LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('objects/locations/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'count' => $row['count'],
						'object-id' => $row['object_id']
					], [
						'edit' => true,
						'add' => $user['add_location']
					], 'locations');
					$i++;
				}
				
				// Get count
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noLoc']
				], [], 'locations');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['locations'],
				]));
			}
			tpl_set('objects/locations/main', [
				'uid' => $user['id'],
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'locations' => $tpl_content['locations'],
				'object' => $object['object_name'],
				'object-id' => $object['object_id']
			], [
				'add' => $user['add_location'],
				'view-report' => in_to_array('1,2', $user['group_ids'])
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => $lang['forb'],
			], [], 'content');
		}
	break;
	
	/*
	* Get objects
	*/
	case 'all':
		is_ajax() or die('Hacking attempt!');
		$lId = intval($_POST['lId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$objects = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_objects` WHERE close = 0 AND franchise_id = '.$user['franchise_id'].(
			$nIds ? ' AND id NOT IN('.$nIds.')' : ''
		).(
			$lId ? ' AND id < '.$lId : ''
		).(
			$query ? ' AND name LIKE \'%'.$query.'%\'': ''
		).' ORDER BY `id` DESC LIMIT 20', true);
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $objects,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Delete object
	*/
	case 'del':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['delete_object']){
			db_query('DELETE FROM `'.DB_PREFIX.'_objects` WHERE id = '.$id.' AND franchise_id = '.$user['franchise_id']);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('ERR');
	break;
	
	/*
	* Send object
	*/
	case 'send':
		is_ajax() or die('Hacking attempt!');
		
		// Filters
		$id = intval($_POST['id']);
		$type = $id ? 'edit' : 'add';
		$name = text_filter($_POST['name'], 255, false);
		$zipcode = text_filter($_POST['zipcode'], 50, false);
		$address = text_filter($_POST['address'], 255, false);
		$timezone = text_filter($_POST['timezone'], 50, false);
		$phone = text_filter($_POST['phone'], 25, false);
		$email = text_filter($_POST['email'], 50, false);
		$descr = text_filter($_POST['desc'], 255, false);
		$managers = ids_filter($_POST['managers']);
		$staff = ids_filter($_POST['staff']);
		$tax = floatval($_POST['tax']);
		$salary_tax = floatval($_POST['salary_tax']);
		$onsite_payment = floatval($_POST['onsite_payment']);
		$week_hours = floatval($_POST['week_hours']);
		$map = text_filter($_POST['map'], 255, false);
		$rent = intval($_POST['rent']);
		$rent_cost = floatval($_POST['rent_cost']);
		$points_equal = floatval($_POST['points_equal']);
		$points = floatval($_POST['points']);
		$craigslist_email = text_filter($_POST['craigslist_email'], 100, false);
		$craigslist_password = text_filter($_POST['craigslist_password'], 100, false);
		$ebay_devID = text_filter($_POST['ebay_devID'], NULL, false);
		$ebay_appID = text_filter($_POST['ebay_appID'], NULL, false);
		$ebay_certID = text_filter($_POST['ebay_certID'], NULL, false);
		$ebay_token = text_filter($_POST['ebay_token'], NULL, false);
		$options = text_filter($_POST['options']);
		$purchase_price = text_filter($_POST['purchase_price']);
		
		$twitter = text_filter($_POST['twitter']);
		$facebook = text_filter($_POST['facebook']);
		$google_plus = text_filter($_POST['google_plus']);
		$youtube = text_filter($_POST['youtube']);

		
		// Check time
		preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $_POST['work_time_start']) or die('Err time format');
		preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $_POST['work_time_end']) or die('Err time format');
		preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $_POST['punch_out']) or die('Err time format');
		
		// Check confirm
		if($id ? $user['edit_object'] : $user['add_object']){
			
			// Check phone
			preg_match("/^[0-9-(-+]+$/", $phone) or die('phone_not_valid');
			
			// Check email
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				echo 'err_email';
				die;			
			}
			
			// is ip
			if(isset($_POST['ip'])){
				$config['object_ips'][$id] = $_POST['ip'];
				conf_save();
			}
			
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_objects` SET
					franchise_id = '.$user['franchise_id'].',
					name = \''.$name.'\',
					zipcode = \''.$zipcode.'\',
					address = \''.$address.'\',
					phone = \''.$phone.'\',
					email = \''.$email.'\',
					descr = \''.$descr.'\',
					points_equal = \''.$points_equal.'\',
					managers = \''.$managers.'\',
					tax = \''.$tax.'\',
					points = \''.$points.'\',
					salary_tax = \''.$salary_tax.'\',
					onsite_payment = \''.$onsite_payment.'\',
					week_hours = \''.$week_hours.'\',
					map = \''.$map.'\',
					timezone = \''.$timezone.'\',
					work_time_start = \''.$_POST['work_time_start'].'\',
					work_time_end = \''.$_POST['work_time_end'].'\',
					punch_out = \''.$_POST['punch_out'].'\',
					rent = \''.$rent.'\',
					rent_cost = \''.$rent_cost.'\',
					craigslist_email = \''.$craigslist_email.'\',
					craigslist_password = \''.$craigslist_password.'\',
					ebay_devID = \''.$ebay_devID.'\',
					ebay_appID = \''.$ebay_appID.'\',
					ebay_certID = \''.$ebay_certID.'\',
					ebay_token = \''.$ebay_token.'\',
					options = \''.$options.'\',
					purchase_price = \''.$purchase_price.'\',
					twitter = \''.$twitter.'\',
					facebook = \''.$facebook.'\',
					google_plus = \''.$google_plus.'\',
					youtube = \''.$youtube.'\',
					staff = \''.$staff.'\''.(
						$_POST['del_image'] ? ', image = \'\'' : ''
					).(
				$id ? 'WHERE id = '.$id : ''
			));
			
			$id = $id ? $id : intval(
				mysqli_insert_id($db_link)
			);
			
			// Is file upload
			if($_FILES){
				
				// Upload max file size
				$max_size = 10;
				
				// path
				$dir = ROOT_DIR.'/uploads/images/stores/';
				
				// Is not dir
				if(!is_dir($dir.$id)){
					@mkdir($dir.$id, 0777);
					@chmod($dir.$id, 0777);
				}
				
				$dir = $dir.$id.'/';
				
				// temp file
				$tmp = $_FILES['image']['tmp_name'];
				
				$type = mb_strtolower(
					pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION)
				);
				
				// Check
				if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array(
					$type, ['jpeg', 'jpg', 'png', 'gif']
				)){
					echo 'err_image_type';
					die;
				}
				if($_FILES['image']['size'] >= 1024*$max_size*1024){
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
					
					$img->cropThumbnailImage(94, 94);
					$img->stripImage();
					$img->writeImage($dir.'thumb_'.$rename);
					$img->destroy();
					
					db_query('UPDATE `'.DB_PREFIX.'_objects` SET image = \''.$rename.'\' WHERE id = '.$id);
				}
			}
			
			echo $id;
		} else
			echo 'Hacking attempt!';
		die;
	break;
	
	/*
	* Edit, add object
	*/
	case 'add':
	case 'edit':
	
	$id = (int)$route[2];
	$type = $id ? $lang['Edit'] : $lang['Add'];
	$meta['title'] = $type.' '.$lang['object'];
	
	// Check confirm
	if($id ? $user['edit_object'] : $user['add_object']){
		$u = [];
		$managers = [];
		$staff = [];
		if($id){
			$u = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_objects` WHERE id = '.$id);
			if($u['managers'] OR $u['staff']){
				if($m = db_multi_query('SELECT id, group_ids, name, lastname, image FROM `'.DB_PREFIX.'_users` WHERE id IN('.((
					$u['managers'] && $u['staff']) ? $u['managers'].','.$u['staff'] : (
					$u['managers'] ? $u['managers'] : $u['staff']
				)).')', true)){
					foreach($m as $r){
						if(in_array($r['id'], explode(',', $u['managers']))){
							$managers[$r['id']] = [
								'name' => $r['name'],
								'lastname' => $r['lastname'],
								'photo' => $r['image'],
							];
						} else
							$staff[$r['id']] = [
								'name' => $r['name'],
								'lastname' => $r['lastname'],
								'photo' => $r['image'],
							];
					}
				}
			}
		}
		$timezones = '';
		$timezones_array = include APP_DIR.'/data/timezones.php';
		foreach($timezones_array as $v => $n){
			$timezones .= '<option value="'.$v.'"'.(
				$u['timezone'] == $v ? 'selected' : ''
			).'>'.$n.'</option>';
		}
		
		$options = '';
		if ($u['options']) {
			foreach(json_decode($u['options'], true) as $k => $v) {
				$options .= '<div class="iGroup optGroup refPoints">
					<div class="sSide">
						<label>Name</label>
						<input type="text" name="ex_name" value="'.$k.'">
					</div>
					<div class="sSide">
						<label>Value</label>
						<input type="number" name="ex_value" value="'.$v.'">
					</div>
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</div>';
			}
		}
		
		$purchase_price = '';
		if ($u['purchase_price']) {
			foreach(json_decode($u['purchase_price'], true) as $k => $v) {
				$purchase_price .= '<div class="iGroup optGroup refPoints">
					<div class="sSide">
						<label>Min price</label>
						<input type="number" step="0.001" name="min_price" value="'.$k.'">
					</div>
					<div class="sSide">
						<label>Upcharge price</label>
						<input type="number" step="0.001" name="mark-up" value="'.$v.'">
					</div>
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</div>';
			}
		}
		
		tpl_set('objects/form', [
			'id' => $id,
			'ip' => $config['object_ips'][$id],
			'title' => $type.' '.'object',
			'send' => ($id ? 'edit' : 'add'),
			'name' => $u['name'],
			'tax' => $u['tax'],
			'points' => $u['points'],
			'salary-tax' => $u['salary_tax'],
			'map' => $u['map'],
			'rent' => $u['rent'] ? ' checked' : '',
			'rent-cost' => $u['rent_cost'],
			'points-equal' => $u['points_equal'],
			'onsite-payment' => $u['onsite_payment'],
			'week-hours' => $u['week_hours'],
			'zipcode' => $u['zipcode'],
			'address' => $u['address'],
			'phone' => $u['phone'],
			'email' => $u['email'],
			'ava' => $u['image'],
			'managers' => json_encode($managers),
			'staff' => json_encode($staff),
			'descr' => $u['descr'],
			'work-time-start' => $u['work_time_start'],
			'work-time-end' => $u['work_time_end'],
			'punch-out' => $u['punch_out'],
			'timezones' => $timezones,
			'craigslist-email' => $u['craigslist_email'],
			'craigslist-password' => $u['craigslist_password'],
			'ebay-devID' => $u['ebay_devID'],
			'ebay-appID' => $u['ebay_appID'],
			'ebay-certID' => $u['ebay_certID'],
			'ebay-token' => $u['ebay_token'],
			'twitter' => $u['twitter'],
			'facebook' => $u['facebook'],
			'google-plus' => $u['google_plus'],
			'youtube' => $u['youtube'],
			'options' => $options,
			'purchase-price' => $purchase_price
		], [
			'rent' => $u['rent'],
			'ava' => $u['image'],
			'edit' => $id,
			'view-report' => in_to_array('1,2', $user['group_ids'])
		], 'content');
	} else {
		tpl_set('forbidden', [
			'text' => $lang['forb'],
		], [], 'content');
	}
	break;
	
	/*
	* All object
	*/
	case null:
	$meta['title'] = $lang['Objects'];
	if($user['add_object'] OR $user['edit_object']){
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_objects` WHERE franchise_id = '.$user['franchise_id'].' '.(
			$query ? 'AND name LIKE \'%'.$query.'%\' OR descr LIKE \'%'.$query.'%\' ' : ''
		).'ORDER BY `id` LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('objects/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'ava' => $row['image'],
					'descr' => $row['descr']
				], [
					'ava' => $row['image'],
					'close' => $row['close'],
					'edit' => $user['edit_object'],
					'add' => $user['add_object']
				], 'objects');
				$i++;
			}
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
			tpl_set('noContent', [
				'text' => $lang['noObj']
			], [], 'objects');
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['objects'],
			]));
		}
		tpl_set('objects/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'objects' => $tpl_content['objects']
		], [
			'add' => $user['add_objects'],
			'view-report' => in_to_array('1,2', $user['group_ids'])
		], 'content');
	} else {
		//echo '<pre>';
		//print_r($user);
		//die;
		tpl_set('forbidden', [
			'text' => $lang['forb'],
		], [], 'content');
	}
	break;
}

?>