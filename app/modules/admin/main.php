<?php
/**
 * @appointment Main admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
*/
 
defined('ENGINE') or ('hacking attempt!');

$objects_ip = array_flip($config['object_ips']);
$store_id = (int)array_search(
			$_SERVER['REMOTE_ADDR'], $config['object_ips']
		);
if($logged){
	switch($route[1]) {
		
		case 'send_tab':
			db_query('INSERT INTO `'.DB_PREFIX.'_tabs` SET title = \''.text_filter($_POST['title'], 255, false).'\', link = \''.db_escape_string($_POST['link']).'\', user_id = '.$user['id']. ' ON DUPLICATE KEY UPDATE link = \''.db_escape_string($_POST['link']).'\'');
			echo 'OK';
		die;
		break;

		case 'del_tab':
			db_query('DELETE FROM `'.DB_PREFIX.'_tabs` WHERE title = \''.text_filter($_POST['title'], 255, false).'\' AND user_id = '.$user['id']);
			echo 'OK';
		die;
		break;
		
		case 'tasks':
			if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
				t.*, 
				u.id as uid, 
				u.name, 
				u.lastname, 
				c.id as cid, 
				c.name as cname, 
				c.lastname as clastname, 
				c.image as cimage,
				o.name as object
			FROM `'.DB_PREFIX.'_tasks` t
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON t.user_id = u.id 
			LEFT JOIN `'.DB_PREFIX.'_users` c
				ON t.create_id = c.id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = t.object_id
			WHERE 1 '.(
				(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
					' AND t.object_id = '.$user['store_id'].' AND (t.customer_id = 0 OR t.customer_id = '.$user['id'].') ' : ''
			).'ORDER BY t.id DESC LIMIT 0, 25', true
			)){
				foreach($sql as $row){
					tpl_set('tasks/item', [
						'id' => $row['id'],
						'uid' => $row['uid'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'cid' => $row['cid'],
						'cname' => $row['cname'],
						'clastname' => $row['clastname'],
						'cava' => $row['cimage'],
						'date' => $row['date'],
						'time' => $row['time'],
						'type' => $row['type'] == 3 ? 'time' : ($row['type'] == 2 ? 'Cash opened' : 'Cash closed'),
						'note' => $row['note'],
						'object' => $row['object'],
						'class' => $row['complited'] ? 'dicline_more' : 'dicline'
					], [
						'cava' => $row['cimage'],
						'time' => $row['type'] == 3,
						'object' => $row['object_id'],
						'user' => $row['user_id'],
						'both' => ($row['object_id'] && $row['user_id'])
					], 'tasks');
				}
			}
		
			echo $tpl_content['tasks'];
			die;
		break;
		
		case 'issue_transfer':
			$it_html = '';
			
			if ($transfers = db_multi_query('
				SELECT
					t.*,
					o.name as object,
					CONCAT(u.name, \' \', u.lastname) as create_user,
					CONCAT(c.name, \' \', c.lastname) as confirm_user,
					CONCAT(s.name, \' \', s.lastname) as staff_user
				FROM `'.DB_PREFIX.'_issues_transfer` t
				INNER JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.store_id
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON u.id = t.create_id
				LEFT JOIN `'.DB_PREFIX.'_users` c
					ON c.id = t.confirm_id
				LEFT JOIN `'.DB_PREFIX.'_users` s
					ON s.id = t.staff_id
				LIMIT 0,30
			', true)) {
				foreach($transfers as $t) {
					$it_html .= '<tr class="'.($t['confirm_id'] ? 'dicline_more' : 'dicline').'">
						<td class="td w150" data-label="Job id:">
							<a href="/issues/view/'.$t['issue_id'].'" target="_blank">#'.$t['issue_id'].'</a>
						</td>
						<td class="td" data-label="Date:">'.$t['create_date'].'</td>
						<td class="td wAmount" data-label="To store/staff:">'.$t['object'].($t['staff_id'] ? '/<a href="/users/view/'.$t['staff_id'].'" target="_blank">'.$t['staff_user'].'</a>' : '').'</td>
						<td class="td wAmount" data-label="Staff created:">
							<a href="/users/view/'.$t['create_id'].'" target="_blank">'.$t['create_user'].'</a>
						</td>
						<td class="td" data-label="To staff:">'.($t['confirm_id'] ? '<a href="/users/view/'.$t['confirm_id'].'" target="_blank">'.$t['confirm_user'].'</a>' : '').'</td>
						<td class="td" data-label="Confirmed:">'.($t['confirm_id'] ? 'Confirmed' : 'Not confirmed').'</td>
					</tr>';
				}
			}
			
			/*
				<div class="tr">
					<div class="th w150">id проблемы</div>
					<div class="th">дата создания</div>
					<div class="th wAmount">В какой магазин</div>
					<div class="th wAmount">кто создал</div>
					<div class="th">Если подтвердил или какому стафу отправили</div>
					<div class="th">статус подтвержден или нет</div>
				</div>
			*/
			
			echo $it_html;
			die;
		break;
		
		case 'feedbacks':
			is_ajax() or die('Hacking attempt!');
			
			$fb = '';
			if ($feedbacks = db_multi_query('
				SELECT 
					i.id,
					i.total,
					i.date,
					i.currency,
					u.id as uid,
					u.name,
					u.lastname,
					u.phone
				FROM `'.DB_PREFIX.'_issues` i 
				LEFT JOIN `'.DB_PREFIX.'_feedback` f 
					ON f.issue_id = i.id
				LEFT JOIN `'.DB_PREFIX.'_inventory` d 
					ON d.id = i.inventory_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o 
					ON o.id = IF(i.object_owner, i.object_owner, d.object_id)
				LEFT JOIN `'.DB_PREFIX.'_invoices` inv 
					ON inv.issue_id = i.id
				LEFT JOIN `'.DB_PREFIX.'_users` u 
					ON u.id = IF(i.customer_id, i.customer_id, d.customer_id)
				WHERE f.id IS NULL AND (i.customer_id > 0 OR d.customer_id > 0) AND (inv.conducted = 1 OR d.status_id = 6)
				'.(!in_array(1, explode(',', $user['group_ids'])) ? 
						' AND !FIND_IN_SET(
							'.$user['id'].', o.managers
						) AND !FIND_IN_SET(
							'.$user['id'].', o.staff
						) AND (o.id != '.($store_id ?: 0).' AND u.id != '.$user['id'].')' : ''
				).'
				ORDER by i.id ASC LIMIT 0, 50
			', true)) {
				foreach($feedbacks as $feedback){
					$fb .= '<tr>
						<td class="td w10" data-label="Issue id:">
							<a href="/issues/view/'.$feedback['id'].'" target="_blank">#'.$feedback['id'].'</a>
						</td>		
						<td class="td" data-label="Date:">
							'.$feedback['date'].'
						</td>						
						<td class="td" data-label="Customer:">
							<a href="/users/view/'.$feedback['uid'].'" target="_blank">'.$feedback['name'].' '.$feedback['lastname'].'</a>
						</td>
						<td class="td" data-label="Phone:">
							'.$feedback['phone'].'
						</td>
						<td class="td" data-label="Total:">
							'.$config['currency'][$feedback['currency']]['symbol'].number_format($feedback['total'], 2, '.', '').'
						</td>
						<td class="td w100" data-label="Add feedback:">
							<a href="javascript:issues.addFeedback('.$feedback['id'].');"><span class="fa fa-plus"></span></a>
						</td>	
					</tr>';
					unset($feedback);
				}
				unset($feedbacks);
			}
			
			echo $fb;
			die;
		break;
			
		case 'cash_stat':
			is_ajax() or die('Hacking attempt!');
			$cash = '';
			if($sql = db_multi_query('
				SELECT
					c.*,
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
					ON c.object_id = o.id
				WHERE c.type = \'cash\'
				'.(!in_array(1, explode(',', $user['group_ids'])) ? 
						' AND FIND_IN_SET(
							'.$user['id'].', o.managers
						) OR FIND_IN_SET(
							'.$user['id'].', o.staff
						) OR (o.id = '.($store_id ?: 0).' OR u.id = '.$user['id'].')' : ''
				).' ORDER BY c.id DESC LIMIT 0, 50', true)){
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
					unset($row);
				}
				unset($sql);
			}	
			
			echo $tpl_content['cash'];
			die;
		break;
		
		case 'timer':
			is_ajax() or die('Hacking attempt!');
			$timer = '';
			foreach(db_multi_query('
				SELECT DISTINCT
					t.*,
					o.salary_tax,
					SEC_TO_TIME(t.seconds) as seconds,
					t.seconds as seconnds2,
					u.name,
					u.lastname,
					u.image,
					u.pay,
					t.date as mdate
				FROM `'.DB_PREFIX.'_timer` t
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON t.user_id = u.id 	
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON FIND_IN_SET(t.user_id, o.managers) OR FIND_IN_SET(t.user_id, o.staff)
				WHERE t.event = \'stop\''.(
						(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
							' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($store_id ?: 0).' OR u.id = '.$user['id'].'))' : ''
					).'
				GROUP BY DATE(t.date)
				ORDER BY t.date DESC LIMIT 0, 15',
			true) as $item){
				$p = db_multi_query('
					SELECT SUM(point) as points
					FROM `'.DB_PREFIX.'_inventory_status_history`
					WHERE staff_id = '.$item['user_id'].' AND rate_point = 0 AND DATE(date) = DATE(
						\''.$item['mdate'].'\'
					)', false, 'timer_'.$item['user_id'].$item['mdate']
				);
				$timer .= '<div data-id="'.$item['id'].'"  class="tr'.(
					$item['confirm'] == 0 ? ' stop' : ''
				).'">
						<div class="td lh45">
							<span class="thShort flLeft" style="margin-right: 10px">Staff: </span><a href="/users/view/'.$item['user_id'].'" target="_blank">
								'.(
									$item['image'] ?
										'<img src="/uploads/images/users/'.$item['user_id'].'/thumb_'.$item['image'].'" class="miniRound">' :
									'<span class="fa fa-user-secret miniRound"></span>'
								).'
								'.$item['name'].' '.$item['lastname'].'
							</a>
						</div>
						<div class="td"><span class="thShort">Punch in: </span>'.$item['date'].'</div>
						<div class="td"><span class="thShort">Punch out: </span>'.$item['control_point'].'</div>
						<div class="td"><span class="thShort">Working time: </span>'.$item['seconds'].''.(
							($item['confirm'] == 0 && $user['confirm_working_time']) ? '<button class="confirm" onclick="timer.confirm('.$item['id'].');">Confirm</button>' : ''
						).'</div>
						'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? ('<div class="td"><span class="thShort">Salary: </span>$'.(number_format($item['seconnds2']/3600*$item['pay'], 2, '.', '').($item['salary_tax'] ? ' / '.'$'.number_format($item['seconnds2']/3600*$item['pay']+((($item['seconnds2']/3600*$item['pay'])/100)*$item['salary_tax']), 2, '.', '') : '').'</div>')) : '').'
						'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="td"><span class="thShort">Points: </span>'.number_format($p['points'], 2, '.', '').'</div>' : '').'
					</div>';
				unset($p);
				unset($item);
			}
			
			echo $timer;
			die;
		break;
		
		case 'my_issues':
		case 'store_issues':
		is_ajax() or die('Hacking attempt!');
			
			$my_issues = ''; 
			$sql_my_issues = db_multi_query('
					SELECT 
						iss.*,
						DATE(iss.date) as date,
						inv.id as inv_id,  
						inv.type_id as inv_type_id, 
						inv.status_id as inv_status_id, 
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
						u.image as user_image,
						m.id as staff_id,
						m.name as staff_name,
						m.lastname as staff_lastname
					FROM `'.DB_PREFIX.'_issues` iss
					INNER JOIN `'.DB_PREFIX.'_inventory` inv
						ON inv.id = iss.inventory_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
						ON inv.category_id = c.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
						ON inv.type_id = t.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON iss.status_id = s.id
					LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
						ON inv.location_id = l.id 
					LEFT JOIN `'.DB_PREFIX.'_users` m
						ON m.id = iss.staff_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = IF(iss.object_owner !=0, iss.object_owner, inv.object_id)
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = iss.customer_id
					LEFT JOIN `'.DB_PREFIX.'_invoices` i
						ON i.issue_id = iss.id
					WHERE IF(i.id > 0, i.conducted = 0, 1) AND '.(
						$route[1] == 'my_issues' ? 'iss.staff_id = '.$user['id'] : 'iss.customer_id = 0'
					).'	AND IF(inv.status_id = 2, inv.warranty != 0, 1) AND iss.finished = 0'.(
						$user['issues_show_all'] ? '' : ($user['issues_show_anywhere'] ? ' AND (iss.staff_id = '.$user['id'].($store_id ? ' OR o.id = '.$store_id : '').')' : ($store_id ? ' AND (iss.staff_id = '.$user['id'].' OR o.id = '.$store_id.')' : ' AND 0'))
					).'
					ORDER BY CASE iss.status_id
						 WHEN 11 THEN 1
						 WHEN 1 THEN 2
						 ELSE 3
						 END ASC, iss.date ASC, iss.id DESC
					LIMIT 0, 50
					', true
			);
			$i = 0;
			foreach($sql_my_issues as $row) {
				$i++;
				$style = ($i % 2 == 0 ? ($row['back_even'] ? ' style="background: #'.$row['back_even'].'"' : '') : ($row['back'] ? ' style="background: #'.$row['back'].'"' : ''));
				$my_issues .= '<tr onclick="Page.get(\'/issues/view/'.$row['id'].'\');">
						<td class="td"'.$style.' data-label="ID:">#'.$row['id'].($cofirmed[$row['id']] > 0 ? '<span class="fa fa-block"></span>' : '').'</td>
						<td class="td lh45"'.$style.'>'.($row['user_id'] ?
							'<a href="/users/view/'.$row['user_id'].'" target="_blank" class="nc">
								'.(
									$row['user_image'] ?
										'<img src="/uploads/images/users/'.$row['user_id'].'/thumb_'.$row['user_image'].'" class="miniRound">' :
									'<span class="fa fa-user-secret miniRound"></span>'
								).'
								'.$row['user_name'].' '.$row['user_lastname'].'
							</a>' : ''.(
									$row['object_image'] ?
										'<img src="/uploads/images/stores/'.$row['object_id'].'/thumb_'.$row['object_image'].'" class="miniRound">' :
									'<span class="fa fa-user-secret miniRound"></span>'
								).'
								'.$row['object_name']
						).'</td>
						<td class="td"'.$style.' data-label="Date:">'.$row['date'].'</td>
						<td class="td"'.$style.' data-label="Total:">'.$config['currency'][$row['currency']]['symbol'].number_format($row['total'], 2, '.', '').'</td>
						<td class="td"'.$style.' data-label="Type:">'.$row['inv_type_name'].'</td>
						<td class="td"'.$style.' data-label="Staff:"><a href="/users/view/'.$row['staff_id'].'" target="_blank" class="nc">'.$row['staff_name'].' '.$row['staff_lastname'].'</a></td>
						<td class="td"'.$style.' data-label="Location:">'.$row['inv_location_name'].' '.$row['inv_location_count'].'</td>
						<td class="td stL"'.$style.' data-label="Status:">'.(strtolower($row['inv_status_id']) == 'finished' ? 'finished' : (strtolower($row['inv_status_id']) == 'new' ? 'new' : $row['inv_status_name'])).'</td>
					</tr>';
				unset($row);
			}
			unset($sql_my_issues);
			echo $my_issues;
			die;
		break;
		
		case 'appointments':
			is_ajax() or die('Hacking attempt!');
			
			$appointments_html = '';
			if ($appointments = db_multi_query('
				SELECT 
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
				ORDER BY a.date DESC
				LIMIT 0, 50
			', true)) {
				foreach($appointments as $i => $row){
					$appointments_html .= '<div class="tr '.($row['confirmed'] ? 'dicline_more' : 'dicline').'" id="app_'.$row['id'].'">
										<div class="td"><span class="thShort">ID: </span>'.$row['id'].'</div>
										<div class="td"><span class="thShort">Customer: </span><a href="/users/view/'.$row['customer_id'].'" onclick="Page.get(this.href); return false;">'.$row['customer_name'].' '.$row['customer_lastname'].'</a></div>
										<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
										<div class="td"><span class="thShort">Store: </span>'.$row['object_name'].'</div>
										<div class="td"><span class="thShort">Staff: </span><a href="/users/view/'.$row['staff_id'].'" onclick="Page.get(this.href); return false;">'.$row['staff_name'].' '.$row['staff_lastname'].'</a></div>
									</div>';			
				}
			}
			
			echo $appointments_html;
			die;
		break;
		
		case 'purchases':
			is_ajax() or die('Hacking attempt!');
			$sp = '';
			if ($purchases = db_multi_query('
				SELECT 
					p.id, 
					p.sale_name, 
					p.name, 
					p.sale, 
					p.quantity, 
					p.total, 
					p.create_date,
					p.recived_id,
					p.confirm_id,
					p.currency,
					p.customer_id,
					p.issue_id,
					p.invoice_id,
					iinv.conducted as iss_conducted,
					iinv.paid as iss_paid,
					iss.id as issue_id,
					inv.conducted as inv_conducted,
					inv.id as inv_id,
					o.name as store,
					u.id as uid,
					inv.paid as inv_paid,
					CONCAT(u.name, \' \', u.lastname) as uname
				FROM `'.DB_PREFIX.'_purchases` p
				LEFT JOIN `'.DB_PREFIX.'_issues` iss
					ON p.issue_id = iss.id
				LEFT JOIN `'.DB_PREFIX.'_invoices` iinv 
					ON iinv.issue_id = iss.id
				LEFT JOIN `'.DB_PREFIX.'_invoices` inv
					ON p.invoice_id = inv.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON p.object_id = o.id
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON IF(p.invoice_id, 
							inv.customer_id, 
							IF (p.issue_id, 
								iss.customer_id, 
								p.customer_id)) = u.id
				WHERE p.del = 0 AND p.confirmed = 1 AND p.recived_id = 0 '.(
					$user['store_id'] ? ' AND p.object_id = '.$user['store_id'] : ''
				).(
					(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '' : 
						(
							$objects_ip[$user['oip']] != 0 ? 
							' AND (FIND_IN_SET('.$user['id'].', o.managers) 
							OR FIND_IN_SET('.$user['id'].', o.staff) 
							OR o.id = '.($objects_ip[$user['oip']] ?: 0).')' : ' AND o.id = 0 '
						)
				).' 
				ORDER BY p.create_date DESC, p.recived_id ASC 
				LIMIT 0, 20
			', true)) {
				foreach($purchases as $pur) {
					$status = '';
					$class = '';
					if ($pur['recived_id'])
						$status = 'Received';
					else {
						if (($pur['issue_id'] AND !$pur['iss_conducted'] AND ($pur['total'] >= $config['min_purchase'] ? ($pur['iss_paid'] < $pur['sale']*$pur['quantity'] AND !$pur['iinv_conducted']) : 0)) OR ($pur['inv_id'] AND !$pur['inv_conducted'] AND ($pur['total'] >= $config['min_purchase'] ? ($pur['inv_paid'] < $pur['sale']*$pur['quantity'] AND !$pur['inv_conducted']) : 0))) {
							$status = 'Not conducted';
							$class = 'p_red';
						} else {
							if ($pur['confirm_id']) {
								if (!$pur['customer_id'] AND !$pur['issue_id'] AND !$pur['invoice_id'])
									$status = 'Confirmed <a href="javascript:purchases.reciveStock('.$pur['id'].');" class="btn btnReceived">Received</a>';
								else
									$status = 'Confirmed <a href="javascript:purchases.receiveMdl('.$pur['id'].');" class="btn btnReceived">Received</a>';
								$class = 'p_green';
							} else {
								$status = 'Not confirmed';
								$class = 'p_yellow';
							}
						}
					}
					
					$sp .= '<div class="tr '.$class.'">
							<div class="td" style="width: 50px"><span class="thShort">ID: </span><a href="/purchases/edit/'.$pur['id'].'" onclick="Page.get(this.href); return false;">'.$pur['id'].'</a></div>
							<div class="td"><span class="thShort">Name: </span><a href="/purchases/edit/'.$pur['id'].'" onclick="Page.get(this.href); return false;">'.($pur['sale_name'] ?: $pur['name']).'</a></div>
							<div class="td"><span class="thShort">Store: </span>'.$pur['store'].'</div>
							<div class="td"><span class="thShort">Customer: </span>'.($pur['uid'] ? '<a href="/users/view/'.$pur['uid'].'" onclick="Page.get(this.href);">'.$pur['uname'].'</a>'.($pur['issue_id'] ? ' (Issue: <a href="/issues/view/'.$pur['issue_id'].'" onclick="Page.get(this.href); return false">#'.$pur['issue_id'].'</a>)' : ($pur['invoice_id'] ? ' (Invoice: <a href="/invoices/view/'.$pur['invoice_id'].'" onclick="Page.get(this.href); return false;">#'.$pur['invoice_id'].'</a>)' : '')) : 'For store '.$pur['store']).'</div>
							<div class="td"><span class="thShort">Date: </span>'.$pur['create_date'].'</div>
							<div class="td"><span class="thShort">Price: </span>'.$config['currency'][$pur['currency']]['symbol'].number_format($pur['total'], 2, '.', '').'</div>
							<div class="td"><span class="thShort">Status: </span>'.$status.'</div>
						</div>';
					unset($pur);
				}
				unset($purchases);
			}
			
			print_r(json_encode([
				'purchases' => $sp
			]));
			unset($sp);
			die;
		break;
		
		case 'new_issues':
			is_ajax() or die('Hacking attempt!');
			$issues = '';
			$store_issues = '';
			$status = intval($_REQUEST['status']);
			
			if ($user['id'] == 17)
				$store_id = 0;
			
			if ($sql_issues = db_multi_query('
					SELECT 
						iss.*,
						DATE(iss.date) as date,
						inv.id as inv_id,  
						inv.object_id as object_id,  
						inv.type_id as inv_type_id, 
						inv.status_id as inv_status_id, 
						inv.location_id as inv_location_id,
						inv.location_count as inv_location_count,
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
						u.image as user_image,
						m.id as staff_id,
						m.name as staff_name,
						m.lastname as staff_lastname,
						ik.name as intake_name,
						ik.lastname as intake_lastname,
						i.conducted,
						i.paid,
						i.total,
						i.tax
					FROM `'.DB_PREFIX.'_issues` iss
					INNER JOIN `'.DB_PREFIX.'_inventory` inv
						ON inv.id = iss.inventory_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
						ON inv.category_id = c.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
						ON inv.type_id = t.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON s.id = iss.status_id
					LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
						ON inv.location_id = l.id 
					LEFT JOIN `'.DB_PREFIX.'_users` m
						ON m.id = iss.staff_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = IF(iss.object_owner !=0, iss.object_owner, inv.object_id)
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = iss.customer_id
					LEFT JOIN `'.DB_PREFIX.'_invoices` i
						ON i.issue_id = iss.id
					LEFT JOIN `'.DB_PREFIX.'_users` ik
						ON ik.id = iss.intake_id
					WHERE iss.customer_id > 0 AND inv.customer_id > 0 '.(
						$status ? (
							$status == -1 ? 'AND iss.warranty = 1' : (
								$status == -2 ? 'AND iss.intake_id > 0 AND IF(i.id > 0, i.conducted = 0, 1) AND IF(inv.status_id = 2, inv.warranty != 0, 1) AND iss.finished = 0 AND iss.date > NOW() - INTERVAL 30 DAY' : 'AND iss.status_id = '.$status
							)
						) : ''
					).(
						$user['issues_show_all'] ? '' : (
							$user['issues_show_anywhere'] ? ' AND (iss.staff_id = '.$user['id'].(
								$store_id ? ' OR o.id = '.$store_id : ''
							).')' : (
								$store_id ? ' AND (iss.staff_id = '.$user['id'].' OR o.id = '.$store_id.')' : ' AND 0'
							)
						)
						//(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
						//	' AND (o.id = '.$store_id.' OR u.id = '.$user['id'].')' : ''
					).'
					ORDER BY iss.date DESC, iss.id ASC
					LIMIT 0, 100
					', true
			)) {
				$i = 0;
				foreach($sql_issues as $row){
					
					$inv_paid = number_format((float)$row['paid'], 2, '.', '');
					$inv_total = number_format((float)$row['total']+(((float)$row['total']/100)*(float)$row['tax']), 2, '.', '');
					
					$i ++;
					
					$style = (
						$i % 2 == 0 ? (
							$row['back_even'] ? ' style="display: table-cell;background: #'.$row['back_even'].'"' : ' style="display: table-cell;"'
						) : (
							$row['back'] ? ' style="display: table-cell;background: #'.$row['back'].'"' : ' style="display: table-cell;"'
						)
					);
					
					
					if(strtotime($row['date']) <= strtotime("-30 day"))
						$style = ' style="display: table-cell;background:#ff000045;"';
					
					$issues .= '<tr onclick="if (!$(this).hasClass(\'no_click\') && event.currentTarget == event.target) Page.get(\'/issues/view/'.$row['id'].'\');">
							<td'.$style.' scope="row" data-label="ID" class="tooltip">
								<a href="/issues/view/'.$row['id'].'" class="no_click" target="_blank" onclick="event.stopPropagation();">#'.$row['id'].'</a>
								'.($cofirmed[$row['id']] > 0 ? '<span class="fa fa-block"></span>' : '').'
								<div style="width: 220px;">
									<b>Staff:</b> <a href="/users/view/'.$row['staff_id'].'" onclick="Page.get(this.href); return false;">'.$row['staff_name'].' '.$row['staff_lastname'].'</a>
									<br />
									<b>Intake:</b> <a href="/users/view/'.$row['intake_id'].'" onclick="Page.get(this.href); return false;">'.$row['intake_name'].' '.$row['intake_lastname'].'</a>
									<br />
									<b>Device type:</b> '.$row['inv_type_name'].'
									<br />
									<b>Location:</b> '.$row['inv_location_name'].' '.$row['inv_location_count'].'
									<br />
									<b>Date:</b> '.$row['date'].'
								</div>
							</td>
							<td'.$style.' data-label="CUSTOMER">'.($row['user_id'] ?
								'<a href="/users/view/'.$row['user_id'].'" target="_blank" class="nc">
									'.(
										$row['user_image'] ?
											'<img src="/uploads/images/users/'.$row['user_id'].'/thumb_'.$row['user_image'].'" class="miniRound">' :
										'<span class="fa fa-user-secret miniRound"></span>'
									).'
									'.$row['user_name'].' '.$row['user_lastname'].'
								</a>' : ''.(
										$row['object_image'] ?
											'<img src="/uploads/images/stores/'.$row['object_id'].'/thumb_'.$row['object_image'].'" class="miniRound">' :
										'<span class="fa fa-user-secret miniRound"></span>'
									).'
									'.$row['object_name']
							).'</td>
							<td'.$style.' data-label="TOTAL">'.$config['currency'][$row['currency']]['symbol'].number_format($row['total'], 2, '.', '').'</td>
							<td'.$style.' data-label="PAID">'.(
								$row['conducted'] ? '<span style="color: green">PAID</span>' : (
									$inv_paid > 0 ? '$'.$inv_paid.'/'.'$'.$inv_total : '<span style="color: red">UNPAID</span>'
								)
							).'</td>
							<td'.$style.' data-label="STATUS">'.(strtolower($row['inv_status_id']) == 'finished' ? 'finished' : (strtolower($row['inv_status_id']) == 'new' ? 'new' : $row['inv_status_name'])).'</td>
						</tr>';
					unset($row);
				}
			}
			unset($sql_issues);
			
			if ($sql_store = db_multi_query('
				SELECT 
					iss.id,
					iss.customer_id,
					iss.description,
					DATE(iss.date) as date,
					CONCAT(u.name, \' \', u.lastname) as customer_name,
					o.name as object
				FROM `'.DB_PREFIX.'_issues` iss
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = iss.object_owner
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = iss.customer_id
				WHERE iss.intake_id = 0
			', true)) {
				foreach($sql_store as $row) {
					$store_issues .= '<div class="tr" id="issue_store_'.$row['id'].'">
							<div class="td"><span class="thShort">ID: </span><a href="/issues/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">#'.$row['id'].'</a></div>
							<div class="td"><span class="thShort">Customer: </span><a href="/users/view/'.$row['customer_id'].'" target="_blank" class="nc">'.$row['customer_name'].'</a></div>
							<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
							<div class="td"><span class="thShort">Store: </span>'.$row['object'].'</div>
							<div class="td"><span class="thShort">Description: </span>'.$row['description'].'</div>
							<div class="td stL"><a href="javascript:issues.confirmStoreIssue('.$row['id'].')"><span class="fa fa-check"></span> Confirm</a></div>
						</div>';
					unset($row);
				}
			}
			unset($sql_store);
			
			print_r(json_encode([
				'issues' => $issues,
				'store_issues' => $store_issues
			]));
			unset($issues);
			unset($store_issues);
			die;
		break;
		
		case 'issues':
			is_ajax() or die('Hacking attempt!');
			$issues = '';
			$store_issues = '';
			
			if ($user['id'] == 17)
				$store_id = 0;
			
			if ($sql_issues = db_multi_query('
					SELECT 
						iss.*,
						DATE(iss.date) as date,
						inv.id as inv_id,  
						inv.object_id as object_id,  
						inv.type_id as inv_type_id, 
						inv.status_id as inv_status_id, 
						inv.location_id as inv_location_id,
						inv.location_count as inv_location_count,
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
						u.image as user_image,
						m.id as staff_id,
						m.name as staff_name,
						m.lastname as staff_lastname
					FROM `'.DB_PREFIX.'_issues` iss
					INNER JOIN `'.DB_PREFIX.'_inventory` inv
						ON inv.id = iss.inventory_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
						ON inv.category_id = c.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
						ON inv.type_id = t.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON s.id = iss.status_id
					LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
						ON inv.location_id = l.id 
					LEFT JOIN `'.DB_PREFIX.'_users` m
						ON m.id = iss.staff_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = IF(iss.object_owner !=0, iss.object_owner, inv.object_id)
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = iss.customer_id
					LEFT JOIN `'.DB_PREFIX.'_invoices` i
						ON i.issue_id = iss.id
					WHERE iss.customer_id > 0 AND iss.intake_id > 0 AND IF(i.id > 0, i.conducted = 0, 1) AND IF(inv.status_id = 2, inv.warranty != 0, 1) AND iss.finished = 0'.(
						$user['issues_show_all'] ? '' : ($user['issues_show_anywhere'] ? ' AND (iss.staff_id = '.$user['id'].($store_id ? ' OR o.id = '.$store_id : '').')' : ($store_id ? ' AND (iss.staff_id = '.$user['id'].' OR o.id = '.$store_id.')' : ' AND 0'))
						//(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
						//	' AND (o.id = '.$store_id.' OR u.id = '.$user['id'].')' : ''
					).'
					ORDER BY iss.date DESC, iss.id ASC
					LIMIT 0, 20
					', true
			)) {
				$i = 0;
/* CASE iss.status_id
						 WHEN 11 THEN 1
						 WHEN 1 THEN 2
						 ELSE 3
						 END ASC,  */
				foreach($sql_issues as $row) {
					$i ++;
					$style = ($i % 2 == 0 ? ($row['back_even'] ? ' style="background: #'.$row['back_even'].'"' : '') : ($row['back'] ? ' style="background: #'.$row['back'].'"' : ''));
					$issues .= '<div class="tr" onclick="if (!$(this).hasClass(\'no_click\')) Page.get(\'/issues/view/'.$row['id'].'\');">
							<div class="td"'.$style.'><span class="thShort">ID: </span><a href="/issues/view/'.$row['id'].'" class="no_click" target="_blank" onclick="event.stopPropagation();">#'.$row['id'].'</a>'.($cofirmed[$row['id']] > 0 ? '<span class="fa fa-block"></span>' : '').'</div>
							<div class="td lh45"'.$style.'><span class="thShort flLeft" style="margin-right: 10px;">Owner: </span>'.($row['user_id'] ?
								'<a href="/users/view/'.$row['user_id'].'" target="_blank" class="nc">
									'.(
										$row['user_image'] ?
											'<img src="/uploads/images/users/'.$row['user_id'].'/thumb_'.$row['user_image'].'" class="miniRound">' :
										'<span class="fa fa-user-secret miniRound"></span>'
									).'
									'.$row['user_name'].' '.$row['user_lastname'].'
								</a>' : ''.(
										$row['object_image'] ?
											'<img src="/uploads/images/stores/'.$row['object_id'].'/thumb_'.$row['object_image'].'" class="miniRound">' :
										'<span class="fa fa-user-secret miniRound"></span>'
									).'
									'.$row['object_name']
							).'</div>
							<div class="td"'.$style.'><span class="thShort">Date: </span>'.$row['date'].'</div>
							<div class="td"'.$style.'><span class="thShort">Total: </span>'.$config['currency'][$row['currency']]['symbol'].number_format($row['total'], 2, '.', '').'</div>
							<div class="td"'.$style.'><span class="thShort">Type: </span>'.$row['inv_type_name'].'</div>
							<div class="td"'.$style.'><span class="thShort">Staff: </span><a href="/users/view/'.$row['staff_id'].'" target="_blank" class="nc">'.$row['staff_name'].' '.$row['staff_lastname'].'</a></div>
							<div class="td"'.$style.'><span class="thShort">Location: </span>'.$row['inv_location_name'].' '.$row['inv_location_count'].'</div>
							<div class="td stL"'.$style.'><span class="thShort">Status: </span>'.(strtolower($row['inv_status_id']) == 'finished' ? 'finished' : (strtolower($row['inv_status_id']) == 'new' ? 'new' : $row['inv_status_name'])).'</div>
						</div>';
					unset($row);
				}
			}
			unset($sql_issues);
			
			if ($sql_store = db_multi_query('
				SELECT 
					iss.id,
					iss.customer_id,
					iss.description,
					DATE(iss.date) as date,
					CONCAT(u.name, \' \', u.lastname) as customer_name,
					o.name as object
				FROM `'.DB_PREFIX.'_issues` iss
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = iss.object_owner
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = iss.customer_id
				WHERE iss.intake_id = 0
			', true)) {
				foreach($sql_store as $row) {
					$store_issues .= '<div class="tr" id="issue_store_'.$row['id'].'">
							<div class="td"><span class="thShort">ID: </span><a href="/issues/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">#'.$row['id'].'</a></div>
							<div class="td"><span class="thShort">Customer: </span><a href="/users/view/'.$row['customer_id'].'" target="_blank" class="nc">'.$row['customer_name'].'</a></div>
							<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
							<div class="td"><span class="thShort">Store: </span>'.$row['object'].'</div>
							<div class="td"><span class="thShort">Description: </span>'.$row['description'].'</div>
							<div class="td stL"><a href="javascript:issues.confirmStoreIssue('.$row['id'].')"><span class="fa fa-check"></span> Confirm</a></div>
						</div>';
					unset($row);
				}
			}
			unset($sql_store);
			
			print_r(json_encode([
				'issues' => $issues,
				'store_issues' => $store_issues
			]));
			unset($issues);
			unset($store_issues);
			die;
		break;
		
		case 'onsite':
			is_ajax() or die('Hacking attempt!');
			$onsite_html = '';
			$onsited_html = '';
			$onsite_invoices = '';
			$onsite_uncofirmed = '';
			
			if ($onsite = db_multi_query('
					SELECT 
						o.*,
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
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = o.selected_staff_id
					LEFT JOIN `'.DB_PREFIX.'_users` cu
						ON cu.id = o.customer_id
					WHERE o.confirmed = 1 AND o.del = 0
					ORDER BY o.id DESC
					LIMIT 0,20', true
				)) {
					foreach($onsite as $row) {
						$onsite_html .= '<div class="tr" id="onsite_'.$row['id'].'">
									<div class="td"><span class="thShort">ID: </span>'.$row['id'].'</div>
									<div class="td"><span class="thShort">Service: </span>'.$row['service_name'].'</div>
									<div class="td"><span class="thShort">Customer: </span>'.$row['cuname'].' '.$row['culastname'].'</div>
									<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
									<div class="td"><span class="thShort">Price: </span>'.$config['currency'][$row['currency']]['symbol'].number_format($row['price'], 2, '.', '').'</div>
									<div class="td"><span class="thShort">Staff: </span>'.$row['uname'].' '.$row['ulastname'].'</div>
								</div>';
					}
					unset($onsite);
				}
				
				if ($onsite_details = db_multi_query('
					SELECT 
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
					ORDER BY o.id DESC
					LIMIT 0,20', true
				)) {
					foreach($onsite_details as $row) {
						$onsited_html .= '<div class="tr">
									<div class="td"><span class="thShort">Service: </span>'.$row['service_name'].'</div>
									<div class="td"><span class="thShort">Customer: </span>'.$row['cuname'].' '.$row['culastname'].'</div>
									<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
									<div class="td"><span class="thShort">Event: </span>'.$row['event'].'</div>
									<div class="td" style="max-width: 300px"><span class="thShort"><span class="thShort">Note: </span>: </span>'.$row['note'].'</div>
									<div class="td"><span class="thShort">Staff: </span>'.$row['uname'].' '.$row['ulastname'].'</div>
								</div>';
						unset($row);
					}
					unset($onsite_details);
				}
				
				if ($onsite_inv = db_multi_query('
					SELECT 
						i.id,
						i.paid,
						i.total - i.paid as due,
						i.date,
						i.currency,
						u.id as cid,
						u.name as uname,
						u.lastname as ulastname
					FROM `'.DB_PREFIX.'_invoices` i
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = i.customer_id
					WHERE i.onsite_id OR i.add_onsite
					ORDER BY i.id DESC
					LIMIT 0,20', true
				)) {
					foreach($onsite_inv as $row) {
						$onsite_invoices .= '<div class="tr">
									<div class="td"><span class="thShort">ID: </span><a href="/invoices/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">'.$row['id'].'</a></div>
									<div class="td"><span class="thShort">Customer: </span><a href="/invoices/view/'.$row['cid'].'" onclick="Page.get(this.href); return false;">'.$row['uname'].' '.$row['uname'].'</a></div>
									<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
									<div class="td"><span class="thShort">Paid: </span>'.$config['currency'][$row['currency']]['symbol'].number_format($row['paid'], 2, '.', '').'</div>
									<div class="td"><span class="thShort">Due: </span>'.$config['currency'][$row['currency']]['symbol'].number_format($row['due'], 2, '.', '').'</div>
								</div>';
						unset($row);
					}
					unset($onsite_inv);
				}
				
				if ($onsite_un = db_multi_query('
					SELECT 
						o.*,
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
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = o.selected_staff_id
					LEFT JOIN `'.DB_PREFIX.'_users` cu
						ON cu.id = o.customer_id
					WHERE o.confirmed = 0 AND o.del = 0
					ORDER BY o.id DESC
					LIMIT 0,20', true
				)) {
					foreach($onsite_un as $row) {
						$onsite_uncofirmed .= '<div class="tr" id="onsite_unconf_'.$row['id'].'">
									<div class="td"><span class="thShort">ID: </span>'.$row['id'].'</div>
									<div class="td"><span class="thShort">Service: </span>'.$row['service_name'].'</div>
									<div class="td"><span class="thShort">Customer: </span>'.$row['cuname'].' '.$row['culastname'].'</div>
									<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
									<div class="td"><span class="thShort">Price: </span>'.$config['currency'][$row['currency']]['symbol'].number_format($row['price'], 2, '.', '').'</div>
									<div class="td"><span class="thShort">Confirm: </span><a href="javascript:user.dashConfirmOnsite('.$row['id'].')">Confirm</a> <a href="javascript:user.dashDeleteOnsite('.$row['id'].')" class="red">Delete</a></div>
								</div>';
						unset($row);
					}
					unset($onsite_un);
				}
				
				print_r(json_encode([
					'list' => $onsite_html,
					'details_list' => $onsited_html,
					'invoices_list' => $onsite_invoices,
					'uncofirmed_list' => $onsite_uncofirmed
				]));
				unset($onsite_html);
				unset($onsited_html);
				unset($onsite_uncofirmed);
				die;
		break;
		
		case 'drops':
			//is_ajax() or die('Hacking attempt!');
			$drops = db_multi_query('
				SELECT 
					c.*,
					u.name as staff_name,
					u.lastname as staff_lastname,
					o.name as object_name
				FROM `'.DB_PREFIX.'_cash` c
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.user_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = c.object_id
				WHERE c.type = \'cash\' AND c.action = \'close\' AND c.out_cash > 0 AND c.confirmed = 0
				'.(
					($date_start AND $date_finish) ? 
						  ' AND c.date >= CAST(\''.$date_start.'\' AS DATE) AND c.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).($object ? ' AND c.object_id IN ('.$object.')' : ' AND c.object_id > 0').'
				ORDER BY c.object_id ASC, c.date DESC
				', true);
			
			$drop_html = '<div class="tbl">
							<div class="tHead">
								<div class="tr">
									<div class="th">ID</div>
									<div class="th">Staff</div>
									<div class="th">Store</div>
									<div class="th">Date</div>
									<div class="th">Drop amount</div>
								</div>
							</div>
							<div class="tBody">';

			$object_id = 0;
			$object_name = '';
			$total = 0;
			foreach($drops as $i => $row){
				if ($object_id != $row['object_id']) {
					if ($object_id != 0) {
						$drop_html .= '<div class="tr">
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"><b>'.$object_name.'</b></div>
									<div class="td"><b>$'.number_format($total, 2, '.', ' ').'</b></div>
									<div class="td"><b id="drop_object_'.$object_id.'"><a href="javascript:Dashboard.confirmDropObject('.$object_id.', this);">Confirm</a></b></div>
								</div>';
					}
					$object_id = $row['object_id'];
					$object_name = $row['object_name'];
					$total = 0;
				}
				$drop_html .= '<div class="tr '.(
									$row['confirmed'] ? 'confirmed' : 'unconfirmed'
								).'" id="drop_'.$row['id'].'">
									<div class="td"><span class="thShort">ID: </span>'.$row['id'].'</div>
									<div class="td"><span class="thShort">Staff: </span><a href="/users/view/'.$row['user_id'].'" onclick="Page.get(this.href); return false;">'.$row['staff_name'].' '.$row['staff_lastname'].'</a></div>
									<div class="td"><span class="thShort">Store: </span>'.$row['object_name'].'</div>
									<div class="td"><span class="thShort">Date: </span>'.$row['date'].'</div>
									<div class="td"><span class="thShort">Drop amount: </span>'.$config['currency'][$row['currency']]['symbol'].number_format($row['out_cash'], 2, '.', '').'</div>
								</div>';	
				$total += $row['out_cash'];
			}
			$drop_html .= '<div class="tr">
								<div class="td"></div>
								<div class="td"></div>
								<div class="td"><b>'.$object_name.'</b></div>
								<div class="td"><b>$'.number_format($total, 2, '.', ' ').'</b></div>
								<div class="td"><b id="drop_object_'.$object_id.'"><a href="javascript:Dashboard.confirmDropObject('.$object_id.', this);">Confirm</a></b></div>
							</div>';
			$drop_html .= '</div></div>';
			
			echo $drop_html;
			die;
		break;
		
		case 'uncofirmed':  
			is_ajax() or die('Hacking attempt!');
			// Services
			$serv_html = '';
			if ($services = db_multi_query('
				SELECT 
					i.id, 
					i.name, 
					i.price,
					i.cr_date,
					i.cr_user,
					i.currency,
					u.name as staff_name,
					u.lastname as staff_lastname
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON i.cr_user = u.id
				WHERE i.confirmed = 0 AND i.type = \'service\' LIMIT 0, 20
			', true)) {
				foreach($services as $serv) {
					$serv_html .= '<div class="tr">
										<div class="td"><span class="thShort">Name: </span><a href="/inventory/edit/'.$serv['id'].'" onclick="Page.get(this.href); return false;">'.$serv['name'].'</a></div>
										<div class="td"><span class="thShort">Price: </span>'.$config['currency'][$serv['currency']]['symbol'].number_format($serv['price'], 2, '.', '').'</div>
										<div class="td"><span class="thShort">Date: </span>'.$serv['cr_date'].($serv['cr_issue'] ? 'for <a href="/issues/view/'.$serv['cr_issue'].'" onclick="Page.get(this.href); return false;">issue #'.$serv['cr_issue'].'</a>' : '').'</div>
										<div class="td"><span class="thShort">Staff: </span>'.($serv['cr_user'] ? '<a href="/users/view/'.$serv['cr_user'].'">'.$serv['staff_name'].' '.$serv['staff_lastname'].'</a>' : '').'</div>
									</div>';
				}
			}
			
			// Inventories
			$inv_html = '';
			if ($inventory = db_multi_query('
				SELECT 
					i.id, 
					i.model, 
					i.price,
					i.cr_date,
					i.cr_user,
					i.currency,
					u.name as staff_name,
					u.lastname as staff_lastname,
					m.name as model_name,
					b.name as brand_name,
					t.name as type_name
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON i.cr_user = u.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
					ON i.model_id = m.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` b
					ON i.category_id = b.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
					ON i.type_id = t.id
				WHERE i.confirmed = 0 AND i.type = \'stock\' LIMIT 0, 20
			', true)) {
				foreach($inventory as $serv) {
					$inv_html .= '<div class="tr">
										<div class="td"><span class="thShort">Name: </span><a href="/inventory/edit/'.$serv['id'].'" onclick="Page.get(this.href); return false;">'.$serv['type_name'].' '.$serv['brand_name'].' '.$serv['model_name'].' '.$serv['model'].'</a></div>
										<div class="td"><span class="thShort">Price: </span>'.$config['currency'][$serv['currency']]['symbol'].number_format($serv['price'], 2, '.', '').'</div>
										<div class="td"><span class="thShort">Date: </span>'.$serv['cr_date'].($serv['cr_issue'] ? 'for <a href="/issues/view/'.$serv['cr_issue'].'" onclick="Page.get(this.href); return false;">issue #'.$serv['cr_issue'].'</a>' : '').'</div>
										<div class="td"><span class="thShort">Staff: </span>'.($serv['cr_user'] ? '<a href="/users/view/'.$serv['cr_user'].'">'.$serv['staff_name'].' '.$serv['staff_lastname'].'</a>' : '').'</div>
									</div>';
				}
			}
			
			// Discounts
			$dis_html = '';
			if ($discounts = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS * FROM (
				(
					SELECT 
						i.id,
						i.discount, 
						i.discount_reason, 
						i.discount_user, 
						MAX(h.date) as date,
						u.name as staff_name,
						u.lastname as staff_lastname,
						d.name as discount_name,
						d.percent,
						\'issue\' as type
					FROM `'.DB_PREFIX.'_issues` i
					LEFT OUTER JOIN `'.DB_PREFIX.'_issues_changelog` h
						ON h.issue_id = i.id AND h.changes = \'discount\' AND h.changes_id = i.discount 
					LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
						ON i.discount = d.id
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON i.discount_user = u.id
					WHERE i.discount_confirmed = 0 AND i.discount != \'\' AND i.discount != \'{}\' GROUP BY h.issue_id, h.changes_id 
				) UNION ALL (
					SELECT 
						i.id,
						i.discount, 
						\'invoice discount\' as discount_reason, 
						i.staff_id as discount_user, 
						i.date as date,
						u.name as staff_name,
						u.lastname as staff_lastname,
						d.name as discount_name,
						d.percent,
						\'invoice\' as type
					FROM `'.DB_PREFIX.'_invoices` i
					LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
						ON d.id = REGEXP_REPLACE(i.discount, \'{"([0-9]+)":{(.*?)}}\', \'\\\1\')
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON i.staff_id = u.id
					WHERE !i.issue_id AND i.discount_confirmed = 0 AND i.discount != \'\' AND i.discount != \'{}\' GROUP BY i.id
				) 
				) t_all
				ORDER BY date DESC LIMIT 0, 20
			', true)) {
				foreach($discounts as $serv) {
					$discount = json_decode($serv['discount'], true);
					if ($discount AND is_array($discount) AND array_keys($discount)[0]) {
						$discount = array_values($discount);
						$dis_html .= '<div class="tr">
										<div class="td"><span class="thShort">Name: </span><a href="/'.($serv['type'] == 'issue' ? 'issues' : 'invoices').'/view/'.$serv['id'].'" onclick="Page.get(this.href); return false;">'.$discount[0]['name'].' - '.$discount[0]['percent'].'%</a></div>
										<div class="td"><span class="thShort">Reason: </span>'.$serv['discount_reason'].'</div>
										<div class="td"><span class="thShort">Date: </span>'.$serv['date'].'</div>
										<div class="td"><span class="thShort">Staff: </span>'.($serv['discount_user'] ? '<a href="/users/view/'.$serv['user'].'">'.$serv['staff_name'].' '.$serv['staff_lastname'].'</a>' : '').'</div>
									</div>';
					}
				}
			}
			
			// Warranties
			$warr_html = '';
			if ($warranty = db_multi_query('
				SELECT 
					i.id, 
					i.model, 
					i.price,
					i.warranty_issue,
					i.warranty_date,
					iss.staff_id,
					iss.warranty_reason,
					u.name as staff_name,
					u.lastname as staff_lastname,
					t.name as type_name,
					m.name as model_name,
					b.name as brand_name
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_issues` iss
					ON iss.id = i.warranty_issue
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = iss.staff_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
					ON i.model_id = m.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` b
					ON i.category_id = b.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
					ON i.type_id = t.id
				WHERE i.warranty = 1
				ORDER BY i.warranty_date DESC LIMIT 0, 20
			', true)) {
				foreach($warranty as $serv) {
					$warr_html .= '<div class="tr">
										<div class="td"><span class="thShort">Issue: </span><a href="/issues/view/'.$serv['warranty_issue'].'" onclick="Page.get(this.href); return false;">issue #'.$serv['warranty_issue'].'</a></div>
										<div class="td"><span class="thShort">Device: </span><a href="/inventory/view/'.$serv['id'].'" onclick="Page.get(this.href); return false;">'.$serv['type_name'].' '.$serv['brand_name'].' '.$serv['model_name'].' '.$serv['model'].'</a></div>
										<div class="td"><span class="thShort">Reason: </span><span '.($serv['warranty_reason'] ? 'class="hnt hntTop" data-title="'.$serv['warranty_reason'].'"' : '').'><span class="fa fa-exclamation-circle red"></span></span></div>
										<div class="td"><span class="thShort">Staff: </span><a href="/users/view/'.$serv['staff_id'].'">'.$serv['staff_name'].' '.$serv['staff_lastname'].'</a></div>
										<div class="td"><span class="thShort">Date: </span>'.$serv['warranty_date'].'</div>
									</div>';
				}
			}
			
			print_r(json_encode([
				'services' => $serv_html ?: '<div class="tr"><div class="td">No info</div></div>',
				'inventory' => $inv_html ?: '<div class="tr"><div class="td">No info</div></div>',
				'discounts' => $dis_html ?: '<div class="tr"><div class="td">No info</div></div>',
				'warranty' => $warr_html ?: '<div class="tr"><div class="td">No info</div></div>'
			]));
			die;
		break; 
		
		default:
			$meta['title'] = 'Control panel';

			$stats = '';

			$invoices_array = [];
			$invoices = '';

			$points_array = [];
			$points = '';
			
			foreach(db_multi_query('
				SELECT DISTINCT
					a.*,
					u.name,
					u.lastname,
					u.image,
					SEC_TO_TIME(t.seconds) as seconds,
					ao.name as object_name
				FROM `'.DB_PREFIX.'_activity` a
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON a.user_id = u.id 
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON FIND_IN_SET(a.user_id, o.managers) OR FIND_IN_SET(a.user_id, o.staff)
				LEFT JOIN `'.DB_PREFIX.'_objects` ao
					ON ao.id = a.object_id
				LEFT JOIN `'.DB_PREFIX.'_timer` t ON a.user_id = t.user_id AND t.date >= CURDATE()
				WHERE a.camera = 0 AND a.date >= CURDATE() '.(
						(!in_array(2, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
							' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).' OR u.id = '.$user['id'].'))' : ''
					).'
				ORDER BY date DESC LIMIT 0, 15',
			true) as $item){
				$event = '';
				$class = '';
					switch($item['event']){
						
						case 'stop working time':
							$event = 'stop working time ('.$item['seconds'].')';
							$class = 'stop';
						break;	

						case 'start working time':
							$class = 'start';
						break;		

						case 'pause working time':
							$class = 'pause';
						break;					
						
						case 'add_purchase':
							$event = 'Add new <a href="/purchases/edit/'.$item['event_id'].'" onclick="Page.get(this.href); return false;">purchase</a>';
						break;
						
						case 'new_invoice':
							//$event = 'Create new invoice: <a href="/invoices/view/'.$item['event_id'].'" onclick="Page.get(this.href); return false;">#'.$item['event_id'].'</a>';
						break;
						
						case 'new_job':
							//$event = 'Create new job: <a href="/issues/view/'.$item['event_id'].'" onclick="Page.get(this.href); return false;">#'.$item['event_id'].'</a>';
						break;
						
						case 'remove_job':
							//$event = 'Remove job: <a href="/issues/view/'.$item['event_id'].'" onclick="Page.get(this.href); return false;">#'.$item['event_id'].'</a>';
							//$class = 'stop';
						break;

						case 'replied_to_chat':
							//$event = 'Replied to chat: <a href="/im/support/'.$item['event_id'].'" onclick="Page.get(this.href); return false;">#'.$item['event_id'].'</a>';
						break;
						
						default:
							$event = str_replace('_', ' ', $item['event']);
						break;
					}
/* 				$stats .= '<div class="tr '.$class.'">
						<div class="td lh45">
							<span class="thShort flLeft" style="margin-right: 10px;">Customer: </span><a href="/users/view/'.$item['user_id'].'" target="_blank">
								'.(
									$item['image'] ?
										'<img src="/uploads/images/users/'.$item['user_id'].'/thumb_'.$item['image'].'" class="miniRound">' :
									'<span class="fa fa-user-secret miniRound"></span>'
								).'
								'.$item['name'].' '.$item['lastname'].'
							</a>
						</div>
						<div class="td"><span class="thShort">Event: </span>'.($event ?: $item['event']).'</div>
						<div class="td"><span class="thShort">Store: </span>'.(
							$item['object_name'] ? '<a href="/objects/edit/'.$item['object_id'].'" target="_blank">'.$item['object_name'].'</a>' : ''
						).'</div>
						<div class="td"><span class="thShort">Date: </span>'.$item['date'].'</div>
					</div>'; */
					
				if($event){
					$stats .= '<tr class="'.$class.'">
							<td class="td lh45">
								<a href="/users/view/'.$item['user_id'].'" target="_blank">
									'.(
										$item['image'] ?
											'<img src="/uploads/images/users/'.$item['user_id'].'/thumb_'.$item['image'].'" class="miniRound">' :
										'<span class="fa fa-user-secret miniRound"></span>'
									).'
									'.$item['name'].' '.$item['lastname'].'
								</a>
							</td>
							<td class="td" data-label="Event:">'.($event ?: $item['event']).'</td>
							<td class="td" data-label="Store:">'.(
								$item['object_name'] ? '<a href="/objects/edit/'.$item['object_id'].'" target="_blank">'.$item['object_name'].'</a>' : ''
							).'</td>
							<td class="td" data-label="Date:">'.$item['date'].'</td>
						</tr>';
				}
				unset($item);
			}

			$works = '';
			
			if(preg_replace('/(www)?.?yoursite.com/i', '', $_SERVER['HTTP_HOST']) == 'dev'){
				$test = db_multi_query('SELECT id as uid, name, lastname, image FROM `'.DB_PREFIX.'_users` WHERE image != \'\' ORDER BY id DESC LIMIT 1', true, 'works_online');	
			} else {
				$test = db_multi_query('
						SELECT t.*, u.id as uid, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_timer` t
					INNER JOIN `'.DB_PREFIX.'_users` u
						ON t.user_id = u.id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON FIND_IN_SET(t.user_id, o.managers) OR FIND_IN_SET(t.user_id, o.staff)
					WHERE t.date >= \''.date('Y-m-d').'\' '.(
							(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
								' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($store_id ?: 0).' OR u.id = '.$user['id'].'))' : ''
						).' AND t.event= \'start\' GROUP BY t.user_id ORDER BY t.id DESC', true
				);	
			}

			foreach($test as $row){
				$works .= '<li class="hnt hntTop" data-title="'.$row['name'].' '.$row['lastname'].'">
					<a href="/users/view/'.$row['uid'].'" onclick="Page.get(this.href); return false;">
						<img src="'.(
						$row['image'] ? 
							'/uploads/images/users/'.$row['uid'].'/thumb_'.$row['image'].'' :
							'/templates/admin/img/userDef.jpg'
						).'">
					</a>
				</li>';
			}
			unset($test);

			$statuses = '<option value="0">Not selected</option>';
			foreach (db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_status`', true) as $st) {
				$statuses .= '<option value="'.$st['id'].'">'.$st['name'].'</option>';
			}
			
			$stores = '<option value="0">Not selected</option>';
			foreach (db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects`', true) as $st) {
				$stores .= '<option value="'.$st['id'].'">'.$st['name'].'</option>';
			}
				
			$db_count = $db_count-3;
			
			$top_stafs = '';
			
			if($top_staffs_sql = array_reverse(db_multi_query('SELECT 
				u.*,
				(u.points/SUM(t.seconds)*3600) as eff
			FROM `'.DB_PREFIX.'_users` u 
			LEFT JOIN `'.DB_PREFIX.'_timer` t
				ON t.user_id = u.id
			LEFT JOIN `'.DB_PREFIX.'_chat_messages` m
				ON m.staff_id = t.user_id
			WHERE u.id NOT IN(16, 17) AND NOT FIND_IN_SET(
				5, u.group_ids
			) AND NOT FIND_IN_SET(
				1, u.group_ids
			) AND u.del = 0 AND IF(u.points_update, t.date >= u.points_update, 1)
			GROUP BY u.id
			ORDER BY eff DESC LIMIT 0, 3', true))){
				$star = '<span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span>';
				foreach($top_staffs_sql as $row){
					$top_stafs = '<div class="dreamUser">
						<a href="#" onclick="return false;">
							'.($row['image'] ? '<img src="/uploads/images/users/'.$row['id'].'/preview_'.$row['image'].'" onclick="showPhoto(this.src);">' : '<span class="fa fa-user-secret miniRound no_float"></span>').'
							<div onclick="Page.get(\'/users/view/'.$row['id'].'\');">'.$row['name'].' '.$row['lastname'].'</div>
						</a>
						'.$star.'
					</div>'.$top_stafs;
					$star .= '<span class="fa fa-star"></span>';
					unset($row);
				}
				unset($top_staffs_sql);
			}
			
			$weak_staffs = '';
			
			/* if($weak_staffs_sql = array_reverse(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users` WHERE id NOT IN(16, 17, 23034) AND NOT FIND_IN_SET(
				5, group_ids
			) AND NOT FIND_IN_SET(
				1, group_ids
			) AND del = 0 
			ORDER BY `points` ASC LIMIT 0, 3', true))){
				foreach($weak_staffs_sql as $row){
					$weak_staffs = '<div class="dreamUser">
						<a href="#" onclick="return false;">
							'.($row['image'] ? '<img src="/uploads/images/users/'.$row['id'].'/preview_'.$row['image'].'" onclick="showPhoto(this.src);">' : '<span class="fa fa-user-secret miniRound"></span>').'<br>
							<span onclick="Page.get(\'/users/view/'.$row['id'].'\');">'.$row['name'].' '.$row['lastname'].'</span>
						</a>
					</div>'.$weak_staffs;
					unset($row);
				}
				unset($weak_staffs_sql);
			} */
			

			$tasks_html = '';
			if ($tasks = db_multi_query('
				SELECT id, note FROM `'.DB_PREFIX.'_tasks` 
				WHERE complited = 0 AND visible = 1 AND (user_id IN(0,'.$user['id'].')'.(
					$store_id ? ' OR object_id = '.$store_id : ''
				).') AND IF(type = 3, IF(time != \'00:00:00\', CONCAT(date, \' \', time) >= \''.date('Y-m-d H:i:s').'\', date >= \''.date('Y-m-d').'\'), 1)
			', true)) {
				foreach($tasks as $t) {
					$tasks_html .= '<div id="task_'.$t['id'].'" class="taskNotif"><div class="flRight"><button class="btn btnComplite" onclick="tasks.complite('.$t['id'].');">Complete</button></div><p>'.preg_replace(["~(Purchase|RMA|Issue|Invoice|Stock|User|Camera)\s\#([0-9]+)\,?~i","/@id([0-9]+)\s\((.*)\)/U"], ['<div class="tooltip" onmousemove="Im.tooltip(this, \'$1\', $2);"><a href="javascript:Im.tagLink(\'$1\', $2);">$1 #$2</a><div></div></div>',"<a href=\"/users/view/$1\" onclick=\"Page.get(this.href); return false;\">$2</a>"], $t['note']).'</p></div>';
				}
			}

			
			tpl_set('main', [
				'uid' => $user['id'],
				'stats' => $stats,
				'invoices' => $invoices,
				'feedbacks' => $fb,
				'works' => $works,
				'points' => $points,
				'top-stafs' => $top_stafs,
				'weak-staffs' => $weak_staffs,
				'statuses' => $statuses,
				'stores' => $stores,
				'tasks' => $tasks_html,
				'e_issues-count' => $counters['e_issues'],
				'e_onsite-count' => $counters['e_onsite'],
				'purchases-count' => $counters['purchases'],
				'un_discount-count' => $counters['un_discount'],
				'un_inventory-count' => $counters['un_inventory'],
				'un_warranty-count' => $counters['un_warranty'],
				'un-count' => $counters['un_discount'] + $counters['un_inventory'] + $counters['un_warranty']
			], [
				'time-money' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))),
				'owner' => in_array(1, explode(',', $user['group_ids'])),
				'dvi' => preg_replace('/(www)?.?yoursite.com/i', '', $_SERVER['HTTP_HOST']) == 'dev',
				'add-invoice' => !($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips']))),
				'service' => $user['service'],
				'purchase' => $user['purchase'],
				'invoces' => $user['invoces'],
				'users' => $user['users'],
				'e_issues' => $counters['e_issues'],
				'e_onsite' => $counters['e_onsite'],
				'purchases' => $counters['purchases'],
				'un_discount' => $counters['un_discount'],
				'un_inventory' => $counters['un_inventory'],
				'un_warranty' => $counters['un_warranty'],
				'un' => $counters['un_discount'] OR $counters['un_inventory'] OR $counters['un_warranty'],
				'settings' => $user['settings'],
				'login' => $user,
				'object' => $user['edit_object'],
				'im' => $user['im'],
				'store' => $user['store'],
				'commerce' => $user['commerce'],
				'issues' => $user['issues_show_all'],
				'cash' => $user['cash'],
				'organizer' => $user['organizer'],
				'salary' => $user['salary'],
				'feedback' => $user['feedback'],
				'analytics' => $user['analytics'],
				'camera' => $user['camera'],
				'e-order' => $counters['e_order']
			], 'content');
			unset($top_stafs);
			unset($works);
			unset($stats);
			unset($statuses);
		break;
	}
/* 	if($user['id'] == 16){
		echo '<pre>';
		print_r($db_queries);
		$time = 0;
		foreach($db_queries as $row){
			$time += $row['time'];
		}
		echo 'Total time: '.$time;
		die;
	} */
}
?>