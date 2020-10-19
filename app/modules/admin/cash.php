<?php
/**
 * @appointment Cash admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        https://yoursite.com
 * This code is copyrighted
 */

defined('ENGINE') or ('hacking attempt!');
 
if($user['cash'] > 0){
	
	$cash_table = $user['id'] == 999999999 ? '_cash_dev' : '_cash';
	
	switch($route[1]){
		
		/* 
		* Update info
		*/
		case 'update_info':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$cash_id = intval($_POST['cash_id']);
			$val = floatval($_POST['value']);
			$field = text_filter($_POST['field'], 15, false);
			
			if (!in_to_array(1, $user['group_ids']))
				die('no_acc');
			
			if (!in_array($field, ['amount', 'out_cash', 'lack']))
				die('ERR');
				
			db_query('UPDATE `'.DB_PREFIX.$cash_table.'` SET '.$field.' = \''.$val.'\' WHERE id = '.$id);
			
			$row = db_multi_query('SELECT 
					c.*,
					cr.system as cr_system,
					cr.lack as cr_lack,
					cr.type as cr_type,
					cr.id as cr_id,
					DATE(c.date) as ddate,
					u.name,
					u.lastname,
					o.name as object
				FROM `'.DB_PREFIX.$cash_table.'` c
				LEFT JOIN `'.DB_PREFIX.$cash_table.'` cr
					ON cr.object_id  = c.object_id AND DATE(cr.date) = DATE(c.date) AND cr.type = \'credit\' AND cr.action = \'open\'
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.user_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = c.object_id
				WHERE c.type = \'cash\' AND c.id = '.$cash_id);
				
			tpl_set('cash/item', [
				'id' => $row['id'],
				'cr_id' => $row['cr_id'],
				'system' => $user['show_amount'] == 1 ? $row['system'] : '',
				'amount' => $user['show_amount'] == 1 ? $row['system'] + $row['lack'] : '',
				'drawer' => $user['show_amount'] == 1 ? $row['amount'] : '',
				'cr-system' => $user['show_amount'] == 1 ? ($row['cr_system'] ?: 0) : '',
				'cr-amount' => $user['show_amount'] == 1 ? $row['cr_system'] + $row['cr_lack'] : '',
				'cr-lack' => $user['show_amount'] == 1 ? $row['cr_lack'] : '',
				'action' => $lang[$row['action']],
				'status' => abs($row['lack']) > 0.001 ? $lang['dicline'] : $lang['accept'],
				'object' => $row['object'],
				'object_id' => $row['object_id'],
				'drop' => '$'.number_format($row['out_cash'], 2, '.', ''),
				'date' => $row['date'],
				'ddate' => $row['ddate'],
				'cr-type' => $row['cr_type'],
				'type' => $row['type'],
				'lack' => $user['show_amount'] == 1 ? $row['lack'] : '',
				'user-id' => $row['user_id'],
				'user-name' => $row['name'],
				'currency' => $config['currency'][$row['currency']]['symbol'],
				'user-lastname' => $row['lastname']
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
				'close' => $row['action'] == 'close',
				'owner' => in_to_array('1', $user['group_ids'])
			], 'item');
					
			print_r(json_encode([
				'res' => 'OK',
				'html' => $tpl_content['item']
			]));
			die;
		break;
		
		/*
		* All drops
		*/
		case 'drops':
			if ($route[2] == 'history') {
				$meta['title'] = $lang['Drops'];
				$query = text_filter($_POST['query'], 255, false);
				$page = intval($_POST['page']);
				$object = intval($_POST['object']);
				$date_start = $_POST['date_start'] != 'undenfined' ? text_filter($_POST['date_start'], 30, true) : false;
				$date_finish = $_POST['date_finish'] != 'undenfined' ? text_filter($_POST['date_finish'], 30, true) : false;
				$count = 50;
				
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						d.*,
						c.out_cash,
						c.date as create_date,
						u.name as staff_name,
						u.lastname as staff_lastname,
						uc.name as confirm_name,
						uc.lastname as confirm_lastname,
						o.name as object_name
					FROM `'.DB_PREFIX.'_cash_drops_history` d
					LEFT JOIN `'.DB_PREFIX.$cash_table.'` c
						ON c.id = d.drop_id
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = c.user_id
					LEFT JOIN `'.DB_PREFIX.'_users` uc
						ON uc.id = d.staff_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = c.object_id
					WHERE c.type = \'cash\' AND c.action = \'close\'
					'.(
						($date_start AND $date_finish) ? 
							  ' AND d.date >= CAST(\''.$date_start.'\' AS DATE) AND d.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).(
						$object ? ' AND c.object_id IN ('.$object.')' : ' AND c.object_id > 0'
					).'
					ORDER BY d.date DESC
					LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('cash/drops/history/item', [
							'name' => $row['staff_name'].' '.$row['staff_lastname'],
							'cname' => $row['confirm_name'].' '.$row['confirm_lastname'],
							'object_name' => $row['object_name'],
							'date' => $row['date'],
							'create_date' => $row['create_date'],
							'amount' => '$'.number_format($row['out_cash'], 2, '.', ''),
						], [
							'owner' => in_array(1, explode(',', $user['group_ids']))
						], 'drops');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['drops'] ?: '<div class="noContent">'.$lang['NoHistory'].'</div>',
					]));
				}
				tpl_set('cash/drops/history/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'drops' => $tpl_content['drops']
				], [], 'content');
			} else {
				$meta['title'] = 'Drops';
				$query = text_filter($_POST['query'], 255, false);
				$status = text_filter($_POST['status'], 10, false);
				$page = intval($_POST['page']);
				$object = intval($_POST['object']);
				$date_start = $_POST['date_start'] != 'undenfined' ? text_filter($_POST['date_start'], 30, true) : false;
				$date_finish = $_POST['date_finish'] != 'undenfined' ? text_filter($_POST['date_finish'], 30, true) : false;
				
				
/* 						d.confirm_name,
						d.confirm_lastname,
						d.date as confirm_date */
				$count = 50;
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						c.*,
						u.name as staff_name,
						u.lastname as staff_lastname,
						o.name as object_name,
						d.date as confirm_date,
						d.staff_id as confirm_staff_id,
						ud.name as confirm_name,
						ud.lastname as confirm_lastname
					FROM `'.DB_PREFIX.$cash_table.'` c
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = c.user_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = c.object_id
					LEFT JOIN `'.DB_PREFIX.'_cash_drops_history` d
						ON c.id = d.drop_id
					LEFT JOIN `'.DB_PREFIX.'_users` ud
						ON ud.id = d.staff_id
					WHERE c.type = \'cash\' AND c.action = \'close\' AND c.out_cash > 0
					'.(
						($date_start AND $date_finish) ? 
							  ' AND c.date >= CAST(\''.$date_start.'\' AS DATE) AND c.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).(
						$object ? ' AND c.object_id IN ('.$object.')' : ' AND c.object_id > 0'
					).(
						$status === 'confirmed' ? ' AND c.confirmed = 1' : ($status === 'unconfirmed' ? ' AND c.confirmed = 0' : '')
					).'
					ORDER BY c.confirmed ASC, c.date DESC, c.object_id ASC
					LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('cash/drops/item', [
							'id' => $row['id'],
							'user-id' => $row['user_id'],
							'name' => $row['staff_name'].' '.$row['staff_lastname'],
							'object_name' => $row['object_name'],
							'date' => $row['date'],
							'confirm-staff' => $row['confirm_staff_id'] ? '<a href="/users/view/'.$row['confirm_staff_id'].'" onclick="Page.get(this.href); return false;">'.$row['confirm_name'].' '.$row['confirm_lastname'].'</a>' : ' - ',
							'confirm-date' => $row['confirm_date'] ?: ' - ',
							'system' => $config['currency'][$row['currency']]['symbol'].number_format(($row['system'] - $row['amount']), 2, '.', ''),
							'amount' => $config['currency'][$row['currency']]['symbol'].number_format($row['out_cash'], 2, '.', ''),
							'confirmed' => $row['confirmed'] ? 'Confirmed' : '<span class="conf_drop fa fa-check gr" onclick="Dashboard.confirm_drop('.$row['id'].', \'confirm\', this);"> Confirm</span>',
							'class' => $row['confirmed'] ? 'confirmed' : 'unconfirmed'
						], [
							'owner' => in_array(1, explode(',', $user['group_ids']))
						], 'drops');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['drops'] ?: '<div class="noContent">'.$lang['NoHistory'].'</div>',
					]));
				}
				tpl_set('cash/drops/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'drops' => $tpl_content['drops']
				], [], 'content');
			}
		break;
	
		
		/*
		* Drop confirm
		*/
		case 'confirm_drop':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if (in_to_array('1,2', $user['group_ids'])) {
				db_query('UPDATE `'.DB_PREFIX.$cash_table.'` SET 
					confirmed = 1 WHERE id = '.$id);
				
				db_query('INSERT INTO `'.DB_PREFIX.'_cash_drops_history` SET 
					staff_id = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					drop_id = '.$id);
				die('OK');
			} else {
				die('ERR');
			}
		break;
		
		/*
		* Drop confirm
		*/
		case 'confirm_drop_object':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['id'] == 16){
				echo 'OK';
				die;
			}
			if (in_to_array('1,2', $user['group_ids'])) {
				db_query('UPDATE `'.DB_PREFIX.$cash_table.'` SET 
					confirmed = 1 WHERE confirmed = 0 AND object_id = '.$id);
				
				/*db_query('INSERT INTO `'.DB_PREFIX.'_cash_drops_history` SET 
					staff_id = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					drop_id = '.$id);*/
				die('OK');
			} else {
				die('ERR');
			}
		break;
		
		/*
		* Open cash
		*/
		case 'open':
			is_ajax() or die('Hacking attempt!');
			$stamp = date('d-m-Y H:i:s', time());
			file_put_contents(ROOT_DIR.'/logs/cash_open_'.$stamp.'.log', var_export($_POST, true));
			$currency = 'USD';
			
			if (!$_POST['action']) 
				die('no_action');
			
			if (db_multi_query('
				SELECT 
					action 
				FROM `'.DB_PREFIX.$cash_table.'` 
				WHERE object_id = '.intval($_POST['id']).'
				AND type = \''.text_filter($_POST['type'], 10, true).'\'
				AND action = \''.text_filter($_POST['action'], 10, true).'\'
				AND DATE(date) = CURDATE()'
			) AND !$user['cash_unlimited_open']) {
				die(json_encode([
					'amount' => 'DONE',
					'show' => $user['show_amount']
				]));
			} else {
				/* if ($user['id'] == 17) {
					echo 'SELECT 
					c.*
					FROM `'.DB_PREFIX.'_cash` c
					WHERE '.($_POST['type'] == 'credit' ? 'c.action = \'open\' AND c.type= \'credit\'' : 'c.action = \'close\'').' AND c.object_id = '.intval($_POST['id']).' ORDER BY c.id DESC LIMIT 0, 1';
				} */
				$date = db_multi_query('SELECT 
					c.*
					FROM `'.DB_PREFIX.$cash_table.'` c
					WHERE '.($_POST['type'] == 'credit' ? 'c.action = \'open\' AND c.type= \'credit\'' : 'c.action = \'close\'').' AND c.object_id = '.intval($_POST['id']).' ORDER BY c.id DESC LIMIT 0, 1');
				$currency = $date['currency'];
				if ($_POST['action'] == 'open' AND $_POST['type'] == 'cash') {
					/* $query = 'SELECT 
						amount, 
						action,
						type
					FROM `'.DB_PREFIX.'_cash`
					WHERE object_id = '.intval($_POST['id']).' AND type = \''.text_filter($_POST['type'], 10, true).'\'
					ORDER BY `id` DESC LIMIT 0, 1'; */
				} else {
					if($user['id'] == 999999999){
/* 						$query = 'SELECT 
							SUM(h.amount) as amount,
							h.date,
							h.currency
						FROM `'.DB_PREFIX.'_invoices_history` h
						LEFT JOIN `'.DB_PREFIX.'_invoices` i
							ON h.invoice_id = i.id
						WHERE i.object_id = '.intval($_POST['id']).' 
						AND h.type = \''.text_filter($_POST['type'], 10, true).'\' 
						AND h.date >= \''.($date['date'] ?: 0).'\''; */
						
						$query = 'SELECT 
							SUM(h.amount) as amount,
							h.date,
							h.currency
						FROM `'.DB_PREFIX.'_invoices_history` h
						LEFT JOIN `'.DB_PREFIX.'_invoices` i
							ON h.invoice_id = i.id
						WHERE i.object_id = '.intval($_POST['id']).' 
						AND h.type = \''.text_filter($_POST['type'], 10, true).'\' 
						AND h.date >= \''.($date['date'] ?: 0).'\'';
						if($_POST['type'] == 'credit'){
						echo $query;
						die;
						}
					} else {
						$query = 'SELECT 
							SUM(h.amount) as amount,
							h.date,
							h.currency
						FROM `'.DB_PREFIX.'_invoices_history` h
						LEFT JOIN `'.DB_PREFIX.'_invoices` i
							ON h.invoice_id = i.id
						WHERE i.object_id = '.intval($_POST['id']).' 
						AND h.type = \''.text_filter($_POST['type'], 10, true).'\' 
						AND h.date >= \''.($date['date'] ?: 0).'\'';
					}
				}

				if (($query AND ($sql = db_multi_query($query))) OR ($_POST['action'] == 'open' AND $_POST['type'] == 'cash')
				) {
					$currency = $sql ? $sql['currency'] : $currency;
					/* if ($user['id'] == 17) {
						print_r($sql);
						print_r($currency);
					} */
					if (text_filter($_POST['action'], 10, true) == 'open' AND text_filter($_POST['type'], 10, true) == 'cash') {
						$exitst_amount = ($date ? ($date['system'] - $date['out_cash'] + $date['lack']) : 0);				
					} elseif (text_filter($_POST['action'], 10, true) == 'close') {
						$exist = db_multi_query('SELECT 
								c.amount as exist,
								c.currency
							FROM `'.DB_PREFIX.$cash_table.'` c
							WHERE c.object_id = '.intval($_POST['id']).'
								AND c.type = \''.text_filter($_POST['type'], 10, true).'\'
								AND c.action = \'open\'
							ORDER BY c.id DESC LIMIT 0, 1');
						$exitst_amount = $exist['exist'];
						$currency = $sql['currency'] ?: $exist['currency'];
					}
					if ($sql['action'] != $_POST['action'] OR $_POST['type'] == 'credit' OR !$sql['action']) {
						$res = [
							'amount' => number_format(floatval($sql['amount']) + floatval($exitst_amount), 2, '.', ''),
							'invoices' => floatval($sql['amount']),
							'exist_date' => floatval($exitst_amount),
							'pre_date' => $date['date'],
							'exist_quey' => floatval($exist['exist']),
							'show' => $user['show_amount'],
							'currency' => $currency ?: 'USD'
						];
						file_put_contents(ROOT_DIR.'/logs/cash_open_res_'.$stamp.'.log', var_export($res, true));
						die(json_encode($res));
					} else {
						die(json_encode([
							'amount' => 'ERR',
							'show' => $user['show_amount']
						]));
					}	
				} else {
					die(json_encode([
						'amount' => 0,
						'show' => $user['show_amount']
					]));
				}
			}
		break;
		
		/*
		* Send comment
		*/
		case 'sendCom':
			is_ajax() or die('Hacking attempt!');
			
			db_query('UPDATE `'.DB_PREFIX.$cash_table.'` SET
					adm_comment = \''.text_filter($_POST['comment'], null, true).'\',
					status = \'check\'
					WHERE id = '.intval($_POST['id'])
				);
		
			die('OK');
		break;
		
		/*
		* Get user comment
		*/
		case 'getComment':
			is_ajax() or die('Hacking attempt!');
			
			if ($sql = db_multi_query('
				SELECT 
					c.user_comment,
					c.user_id,
					u.name,
					u.lastname
				FROM `'.DB_PREFIX.$cash_table.'` c
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.user_id
				WHERE c.id = '.intval($_POST['id'])
			)) {
				die(json_encode([
					'content' => $sql['user_comment'],
					'user' => '<a href="/admin/users/view/'.$sql['user_id'].'" target="_blank">'.$sql['name'].' '.$sql['lastname'].'</a>'
				]));	
			}
		break;
		
		/*
		* Send cash
		*/
		case 'send': 
			is_ajax() or die('Hacking attempt!');
			
			$d = intval($_POST['d']);
			
			$object_id = intval($_POST['object']);
			
			$stamp = date('d-m-Y H:i:s', time());
			
			$action = text_filter($_POST['acType'], 10, true);
			
			$lack = floatval($_POST['lack'])*(-1);
			
			if($action == 'close' && ($lack < $config['min_lack'] || $lack > $config['max_lack'])){
				
				$store = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects` WHERE id = '.$object_id);
				
				$msg = 'Drawer amount mismatch $'.$lack.' at '.$store['name'].'- '.date('Y-m-d H:i:s');
				
				sPush('1,31735', 'Close cash: '.$store['name'], $msg, [
					'type' => 'alert',
					'msg' => $msg,
					'id' => 'cash_close-'.$store['id'],
				]);
				
				$email = 'dev.drozd@gmail.com, pavppz1@gmail.com';
				
				// Headers
				$headers  = 'MIME-Version: 1.0'."\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
				$headers .= 'To: '.$email. "\r\n";
				$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

				// Send
				mail($email, 'Close cash: '.$store['name'], '<!DOCTYPE html>
				<html lang="en">
				<head>
					<meta charset="UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<title>Close cash: '.$store['name'].'</title>
				</head>
				<body style="background: #f6f6f6; text-align: center;">
					<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
						<div style="margin: -20px -20px 0; padding: 20px;">
							<a href="http://yoursite.com/">
								<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
							</a>
						</div>
						<div style="padding: 0 30px 30px;">
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
								<p>Drawer amount mismatch <font color="'.($lack > 0 ? 'green' : 'red').'">$'.$lack.'</font> at '.$store['name'].'- '.date('Y-m-d H:i:s').'</p>
							</div>
						</div>
					</div>
				</body>
				</html>', $headers);
			}
			
			//file_put_contents(ROOT_DIR.'/logs/cash_send_'.$stamp.'.log', var_export($_POST, true));
			
			//$date = db_multi_query('SELECT c.* FROM `'.DB_PREFIX.'_cash` c WHERE c.action = \'close\' AND c.object_id = '.$object_id.' ORDER BY c.id DESC LIMIT 0, 1');
			
			//$recount = db_multi_query('SELECT SUM(h.amount) as amount, h.date, h.currency FROM `'.DB_PREFIX.'_invoices_history` h LEFT JOIN `'.DB_PREFIX.'_invoices` i ON h.invoice_id = i.id WHERE i.object_id = '.$object_id.' AND h.type = \'cash\' AND date(h.date) > date(\''.($date['date'] ?: 0).'\')');
			
/* 			if($user['id'] == 16){
				echo 'GOOD';
				die;
			} */
			
			db_query((
					$d ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.$cash_table.'` SET
					action = \''.$action.'\'
					'.(
						$_POST['amount'] ? ', amount = \''.(
							floatval($_POST['amount'])
						).'\'' : '' 
					).(
						$_POST['out'] ? ', out_cash = \''.(
							floatval($_POST['out'])
						).'\'' : '' 
					).(!$d ? 
						', system = \''.floatval($_POST['system']).'\',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						'.($_POST['pre_date'] ? 'pre_date = \''.date("Y-m-d H:i:s", strtotime($_POST['pre_date'])).'\',' : '').'
						type = \''.text_filter($_POST['type'], 10, true).'\',
						user_id = \''.$user['id'].'\',
						currency = \''.($_POST['currency'] ?: 'USD').'\',
						status = \''.(
							$_POST['d'] ? 'dicline' : (
								$_POST['action'] ? text_filter($_POST['action'], 10, true) : ''
							)
						).'\''.(
							$_POST['lack'] ? ', lack = \''.$lack.'\'': ''
						).(
							$_POST['comment'] ? ', user_comment = \''.text_filter($_POST['comment'], null, true).'\'': ''
						).'
						, object_id = '.$object_id : ''
					).(
						$d ? ' WHERE id = '.intval($d) : ''
					)
				);
				$id = $d ? $d : intval(
					mysqli_insert_id($db_link)
				);
				
				if($_FILES){
					$max_size = 10;
					
					// path
					$dir = ROOT_DIR.'/uploads/images/cash/';
					
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
					if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime'])){
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
						
						// 1920
						if($img->getImageWidth() > 1920){
							$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
						}
						
						auto_rotate_image($img);
						
						$img->stripImage();
						
						$img->writeImage($dir.$rename);

						$img->destroy();
						
						db_query('UPDATE `'.DB_PREFIX.$cash_table.'` SET credit_check = \''.$rename.'\' WHERE id = '.$id);
					}
				}
				
				$pType = text_filter($_POST['acType'], 10, true).'_cash';
				if ($user['id'] == 17)
					$user['store_id'] = 2;
				
				if($user['id'] != 16){
				// ------------------------------------------------------------------------------- //
				if (!$d AND $user['store_id'] > 0 AND text_filter($_POST['type'], 10, true) == 'cash'){
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
					
					$points = floatval($config['user_points'][text_filter($_POST['acType'], 10, true).'_cash']['points']);
					
					//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
						db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
							staff_id = '.$user['id'].',
							action = \''.$pType.'\',
							date = \''.date('Y-m-d H:i:s', time()).'\',
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
							action = \''.$pType.'\',
							min_rate = '.$sql_['points'].',
							object_id = '.$user['store_id'].',
							point = \''.$points.'\',
							rate_point = 1'
						);	
					} */
				}
				
				// ------------------------------------------------------------------------------- //
				
				db_query('
					INSERT INTO 
					`'.DB_PREFIX.'_activity` SET 
						user_id = \''.$user['id'].'\', 
						event = \''.str_replace('_', ' ', $pType).'\',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						object_id = '.$user['store_id'].'
				');
				
				if ($tasks = db_multi_query('
					SELECT 
						id, note
					FROM `'.DB_PREFIX.'_tasks` 
					WHERE type = \''.(text_filter($_POST['acType'], 10, true) == 'open' ? '1' : '2').'\' 
						AND visible = 0 
						AND (user_id = '.$user['id'].' OR object_id = '.$object_id.')
				', true)) {
					db_query('UPDATE `'.DB_PREFIX.'_tasks` SET visible = 1 WHERE id IN('.implode(',', array_column($tasks, 'id')).')');
					foreach($tasks as $task) {
						send_push(intval($user['id']), [
							'type' => 'new_task',
							'id' => $task['id'],
							'note' => $task['note']
						]);
					}
				}
				
				}
			
				die(json_encode([
					'id' => $id,
					'date' => date('Y-m-d h:m:s'),
					'ddate' => date('Y-m-d'),
					'status' => (
							$_POST['d'] ? 'dicline' : (
								$_POST['action'] ? text_filter($_POST['action'], 10, true) : ''
							)
						),
					'user' => '<a href="/admin/users/view/'.$user['id'].'">'.$user['uname'].' '.$user['ulastname'].'</a>'
				]));
		break;
		
		/*
		* Send comment
		*/
		case 'send_comment': 
			is_ajax() or die('Hacking attempt!');
			
			db_query(
				'INSERT INTO `'.DB_PREFIX.'_cash_comments` SET 
					staff_id = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					cash_id = '.intval($_POST['id']).',
					text = \''.text_filter($_POST['text'], 1000, false).'\''
			);
		
			$id = intval(mysqli_insert_id($db_link));
			
			print_r(json_encode([
				'res' => 'OK',
				'html' => '<div class="comment">
						<div class="user">
							<a href="/users/view/'.$user['id'].'">'.(
								$user['image'] ? '<img src="/uploads/images/users/'.$user['id'].'/thumb_'.$user['image'].'" class="miniRound">' : '<span class="fa fa-user-secret miniRound"></span>'
							).'</a>
						</div>
						<div class="commentText">
							<div class="usrname"><a href="/users/view/'.$user['id'].'">'.$user['uname'].' '.$user['ulastname'].'</a></div>
							<div class="date">'.date('m-d-Y H:i:s', time()).'</div>
							'.text_filter($_POST['text'], 1000, false).'
						</div>
					</div>'
			]));
			die;
		break;
		
		/*
		* All comments
		*/
		case 'comments':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;

			if($sql = db_multi_query('
				SELECT 
					SQL_CALC_FOUND_ROWS c.*,
					u.name,
					u.lastname,
					u.image
				FROM `'.DB_PREFIX.'_cash_comments` c
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.staff_id
				WHERE c.cash_id = '.$id.'
				ORDER BY c.id ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row) {
					tpl_set('cash/comment_item', [
						'id' => $row['id'],
						'date' => $row['date'],
						'text' => $row['text'],
						'user_id' => $row['staff_id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image']
					], [
						'image' => $row['image']
					], 'list');
					$i++;
				}
				
				// Get count
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				$left_count = intval(($res_count-($page*$count)-$i));
			}
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['list'],
				'more' => $left_count ? '' : ' hdn'
			]));
		break;

		/*
		* All cashes
		*/
		default:
			$meta['title'] = $lang['Cash'];
			$id = (intval($_REQUEST['object'] ?: $_REQUEST['id']) ?: ($route[1] ?: 0));
			$query = text_filter($_REQUEST['query'], 255, false);
			$page = intval($_REQUEST['page']);
			$type = text_filter($_REQUEST['type'], 255, false);
			$action = text_filter($_REQUEST['action'], 255, false);
			$status = text_filter($_REQUEST['status'], 255, false);
			$date_start = text_filter($_REQUEST['date_start'], 255, false);
			$date_end = text_filter($_REQUEST['date_finish'] ?: $_REQUEST['date_end'], 255, false);
			$staff = intval($_REQUEST['staff']);
			$count = 20;
			$credits = [];

			if($sql = db_multi_query('
				SELECT 
					SQL_CALC_FOUND_ROWS c.*,
					cr.system as cr_system,
					cr.lack as cr_lack,
					cr.type as cr_type,
					cr.id as cr_id,
					cr.credit_check,
					DATE(c.pre_date) as credit_date,
					DATE(c.date) as ddate,
					u.name,
					u.lastname,
					o.name as object
				FROM `'.DB_PREFIX.$cash_table.'` c
				LEFT JOIN `'.DB_PREFIX.$cash_table.'` cr
					ON cr.object_id  = c.object_id AND DATE(cr.date) = DATE(c.date) AND cr.type = \'credit\' AND cr.action = \'open\'
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.user_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = c.object_id
				WHERE c.type = \'cash\' '.(
					$id ? 'AND c.object_id = '.$id.' ' : ($user['cash_check_ip'] ? 'AND c.object_id = '.$user['store_id'].' ' : '')
				).(
					$staff ? 'AND c.user_id = '.$staff.' ' : ''
				).(
					$status ? 'AND c.status = \''.$status.'\' ' : ''
				).(
					$type ? 'AND c.type = \''.$type.'\' ' : ''
				).(
					$action ? 'AND c.action = \''.$action.'\' ' : ''
				).(
					($date_start AND $date_end) ? 'AND c.date >= CAST(\''.$date_start.'\' AS DATE) AND c.date <= CAST(\''.$date_end.' 23:59:59\' AS DATETIME) ' : 'AND YEAR(c.date) = '.date('Y', time()).' AND MONTH(c.date) = '.date('m', time()).' '
				).(
					$query ? 'AND `date` LIKE \'%'.$query.'%\' ' : ''
				).' GROUP BY c.id ORDER BY c.date DESC LIMIT '.($page*$count).', '.$count, true, false, function($a) use(&$credits){
					if($a['action'] == 'open'){
						$credits[$a['id']] = [
							'object_id' => $a['object_id'],
							'date' => $a['date']
						];
					}
				})){
				$i = 0;
				$c = 0;
				if($user['id'] == 16){
					//echo implode(',', array_keys($credits));
					//echo 'OK';
					//die;
				}
				foreach($sql as $row) {
					if ($row['type'] == 'cash' AND !$c) {
						$status = $row['action'];
						$c = 1;
					}
					tpl_set('cash/item', [
						'id' => $row['id'],
						'cr_id' => $row['cr_id'],
						'system' => $user['show_amount'] == 1 ? $row['system'] : '',
						'amount' => $user['show_amount'] == 1 ? $row['system'] + $row['lack'] : '',
						'drawer' => $user['show_amount'] == 1 ? $row['amount'] : '',
						'cr-data' => $row['cr_lack'],
						'cr-system' => $user['show_amount'] == 1 ? ($row['cr_system'] ?: 0) : '',
						'cr-amount' => $user['show_amount'] == 1 ? $row['cr_system'] + $row['cr_lack'] : '',
						'cr-lack' => $user['show_amount'] == 1 ? $row['cr_lack'] : '',
						'action' => $lang[$row['action']],
						'status' => abs($row['lack']) > 0.001 ? $lang['dicline'] : $lang['accept'],
						'object' => $row['object'],
						'object_id' => $row['object_id'],
						'drop' => '$'.number_format($row['out_cash'], 2, '.', ''),
						'date' => convert_date($row['date'], true),
						'ddate' => $row['ddate'],
						'credit-date' => $row['credit_date'],
						'cr-type' => $row['cr_type'],
						'type' => $row['type'],
						'lack' => $user['show_amount'] == 1 ? $row['lack'] : '',
						'user-id' => $row['user_id'],
						'user-name' => $row['name'],
						'user-lastname' => $row['lastname'],
						'credit_check' => $row['credit_check'],
						'currency' => $config['currency'][$row['currency']]['symbol']
					], [
						'accept' => $row['status'] == 'accept',
						'credit' => $row['credit_date'],
						'dicline' => $row['lack'] < -0.001 OR ($row['action'] == 'open' ? $row['cr_lack'] < -0.001 : 0),
						'dicline_more' => $row['lack'] > 0.001 OR ($row['action'] == 'open' ? $row['cr_lack'] > 0.001 : 0),
						'check' => $row['status'] == 'check',
						'lack' => abs($row['lack']) > 0.001,
						'min' => $row['lack'] < 0,
						'cr-lack' => abs($row['cr_lack']) > 0.001,
						'cr-min' => $row['cr_lack'] < 0,
						'show' => $user['show_amount'] == 1,
						'close' => $row['action'] == 'close',
						'owner' => in_to_array('1', $user['group_ids']),
						'credit_check' => $row['credit_check'],
						'object' => $id
					], 'history');
					$i++;
				}
				
				// Get count
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				$left_count = intval(($res_count-($page*$count)-$i));
			}
			if ($_POST) {
				if ($sql) {
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['history'],
						'more' => $left_count ? '' : ' hdn',
						'status' => $status == 'open' ? 'close' : 'open'
					]));
				} else {
					tpl_set('noContent', [
						'text' => 'No information'
					], [], 'history');
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'more' => $left_count ? '' : ' hdn',
						'content' => $tpl_content['history']
					]));
				}
			} else {
				if ($sql) {
					tpl_set('cash/main', [
						'query' => $query,
						'history' => $tpl_content['history'],
						'left_count' => $left_count,
						'content' => $tpl_content['history'],
						'more' => ($left_count ? '' : ' hdn'),	
						'object' => $sql[0]['object'],
						'object_id' => $sql[0]['object_id'],
					], [
						'object' => isset($route[1]),
						'owner' => in_to_array('1', $user['group_ids']),
					], 'content');
				
				} else {
					tpl_set('cash/main', [
						'query' => $query,
						'history' => '',
						//'object' => 'Test',
						//'object_id' => 2,
						'more' => ' hdn',
					], [
						'object' => isset($route[1]),
						'owner' => in_to_array('1', $user['group_ids'])
					], 'content');
				}
			}
			
		break;
	}
} else {
	tpl_set('forbidden', [
		'text' => $lang['Forbidden'],
	], [], 'content');
}
?>