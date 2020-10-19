<?php
/**
 * @appointment Users admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

///if($user['users'] > 0){
 
	switch($route[1]){
		
		case 'phone-incorrect':
			is_ajax() or die('Hacking attempt!');
			db_query('UPDATE `'.DB_PREFIX.'_users` SET incphone = 1 WHERE id = '.(int)$_POST['id']);
			echo 'OK';
			die;
		break;
		
		/*
		* Check by name
		*/
		case 'check_by_name':
			is_ajax() or die('Hacking attempt!');
			$name = text_filter($_POST['name'], 255, false);
			$lastname = text_filter($_POST['lastname'], 255, false);
			
			if ($users = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					id, 
					name, 
					lastname, 
					image,
					phone,
					email
				FROM `'.DB_PREFIX.'_users` 
				WHERE del = 0 AND CONCAT(name, \' \', lastname) LIKE \'%'.$name.'%'.$lastname.'%\'
				LIMIT 0, 10
			', true)) {
				
				echo json_encode([
					'res' => 'OK',
					'data' => $users,
					'count' => intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0])
				]);
			} else {
				echo json_encode([
					'res' => 'ERR'
				]);
			}
			die;
		break;
		
		case 'user_auth':
			$uid = (int)$_REQUEST['id'];
			$ohid = $_SESSION['uid'][3] ?? $_COOKIE['ohid'];
			function getOwner($a){
				return db_multi_query('SELECT id, hid, group_ids FROM '.DB_PREFIX.'_users WHERE hid = \''.$a.'\'');
			}
			if($uid && in_array(1, explode(',', getOwner($ohid ?? $user['hid'])['group_ids'])) && (
				$u = db_multi_query('SELECT id, hid, INET_NTOA(ip) as ip FROM '.DB_PREFIX.'_users WHERE id = '.$uid)
			)){
				$_SESSION['uid'] = [];
				$_SESSION['uid'][0] = (int)$u['id'];
				$_SESSION['uid'][1] = $u['hid'];
				$_SESSION['uid'][3] = $user['hid'];
				$_SESSION['uid'][4] = $u['ip'];
				$ctime = time()+(3600*24*7);
				setcookie('uid', $u['id'], $ctime, '/', 'yoursite.com', null, true);
				setcookie('uid', $u['id'], $ctime, '/', 'admin.yoursite.com', null, true);
				setcookie('hid', $u['hid'], $ctime, '/', 'yoursite.com', null, true);
				setcookie('hid', $u['hid'], $ctime, '/', 'admin.yoursite.com', null, true);
				setcookie('ohid', $user['hid'], $ctime, '/', 'yoursite.com', null, true);
				setcookie('ohid', $user['hid'], $ctime, '/', 'admin.yoursite.com', null, true);
				setcookie('uip', $u['ip'], $ctime, '/', 'yoursite.com', null, true);
				setcookie('uip', $u['ip'], $ctime, '/', 'admin.yoursite.com', null, true);
			} else if($u = getOwner($ohid)){
				$_SESSION['uid'] = [];
				$_SESSION['uid'][0] = (int)$u['id'];
				$_SESSION['uid'][1] = $u['hid'];
				unset($_SESSION['uid'][3]);
				unset($_SESSION['uid'][4]);
				$ctime = time()+(3600*24*7);
				setcookie('uid', $u['id'], $ctime, '/', 'yoursite.com', null, true);
				setcookie('uid', $u['id'], $ctime, '/', 'admin.yoursite.com', null, true);
				setcookie('hid', $u['hid'], $ctime, '/', 'yoursite.com', null, true);
				setcookie('hid', $u['hid'], $ctime, '/', 'admin.yoursite.com', null, true);
				setcookie('ohid', '', 0, '/', 'yoursite.com', null, true);
				setcookie('ohid', '', 0, '/', 'admin.yoursite.com', null, true);
				setcookie('uip', '', 0, '/', 'yoursite.com', null, true);
				setcookie('uip', '', 0, '/', 'admin.yoursite.com', null, true);
			}
			header('Location: /');
			die;
		break;
		
		/*
		* Send lite step
		*/
		case 'send_lete_step':
			is_ajax() or die('Hacking attempt!');
			
			//if($user['id'] == 16){
			//	echo '<pre>';
			//	print_r($_POST);
			//	die;
			//}
			
			// CUSTOMER
			$name = trim(text_filter($_POST['name'], 25, false) ?: text_filter($_POST['cname'], 25, false));
			$contact = intval($_POST['contact']);
			$company = intval($_POST['company']);
			$lastname = trim(text_filter($_POST['lastname'], 25, false));
			$zipcode = text_filter($_POST['zipcode'], 255, false);
			$email = text_filter($_POST['email'], 50, false);
			$phones = str_replace(['(',')', '-'], '', phones_filter($_POST['phone'], $_POST['sms']));
			$bith_date = explode('-', text_filter($_POST['bith_date'], null, false));
			$address = text_filter($_POST['address'], 255, false);
			$login = text_filter($_POST['login'], 25, false);
			
			// ----- Addition fields -----
			$sex = text_filter($_POST['sex'], 20, false);
			$country = text_filter($_POST['country'], 255, false);
			$state = text_filter($_POST['state'], 255, false);
			$city = text_filter($_POST['city'], 255, false);
			$ver = text_filter($_POST['addressConf'], 10, false);
			$pay = floatval($_POST['pay']);
			// -----/ Addition fields -----
			
			// Check email
			$e = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\''.(
				$id ? ' AND id != '.$id : ''
			));
			if(!filter_var($email, FILTER_VALIDATE_EMAIL) OR ($e['count'] > 0 AND $email != 'noreply@yoursite.com')){
				echo 'err_email';
				die;			
			}
			

			// Check login
			if($login){
				$logn = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE login = \''.$login.'\''.(
					$id ? ' AND id != '.$id : ''
				));
				
				if($logn['count'] > 0){
					echo 'err_login';
					die;
				}
			}
			
			$password = substr(md5(uniqid()), 0, 6);
			
			// Headers
			$headers  = 'MIME-Version: 1.0'."\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$headers .= 'To: '.$email. "\r\n";
			$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

			// Send
			mail($email, 'Welcome to the Your Company', '<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>New user on Your Company</title>
			</head>
			<body style="background: #f6f6f6; text-align: center;">
				<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
					<div style="margin: -20px -20px 0; padding: 20px;">
						<a href="http://yoursite.com/">
							<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
						</a>
					</div>
					<div style="padding: 0 30px 30px;">
						<p>You have applied for open beta testing of new platform for <br><b>Your Company inc</b>.</p>
						<p>Please use the details below for entrance to new platform:</p>
						<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
							Link: <a href="http://yoursite.com/admin/" style="color: #0e92d4;">Click here</a><br>
							Login: '.$email.'<br>
							Password: '.$password.'
						</div>
					</div>
				</div>
			</body>
			</html>', $headers);
			
			$sql = 'password = \''.md5(md5($password)).'\'';
			
			if($_POST['del_image']) $sql .= ', image = \'\'';
			
			if($bith_date)
				$sql .= ', birthday = \''.intval($bith_date[2]).'.'.intval($bith_date[0]).'.'.intval($bith_date[1]).'\'';
			
			if($sex)
				$sql .= ', sex = \''.$sex.'\'';

			if($login)
				$sql .= ', login = \''.$login.'\'';
			
			if($country)
				$sql .= ', country = \''.$country.'\'';
			
			if($city)
				$sql .= ', city = \''.$city.'\'';
			
			if($address)
				$sql .= ', address = \''.$address.'\'';

			if($state)
				$sql .= ', state = \''.$state.'\'';

			if($ver)
				$sql .= ', ver = \''.$ver.'\'';
			
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_users` SET
				name = \''.$name.'\',
				lastname = \''.$lastname.'\',
				company = \''.$company.'\',
				phone = \''.$phones.'\',
				sms = \''.explode(',', $phones)[0].'\',
				zipcode = \''.$zipcode.'\',
				referral = \''.intval($_POST['referral']).'\',
				contact = \''.$contact.'\',
				'.($pay ? ' pay = \''.$pay.'\',' : '').'
				email = \''.$email.'\',
				group_ids = 5, '.$sql.' 
				'
			);
			
			$customer_id = intval(mysqli_insert_id($db_link));
			
			$res['href'] = '/users/view/'.$customer_id;
				
			
			// INVENTORY
			$smodel = text_filter($_POST['smodel'], 50, false);
			$barcode = text_filter($_POST['barcode'], 100, false);
			$os_version = text_filter($_POST['os_version'], 50, false);
			$serial = text_filter($_POST['serial'], 50, false);
			$sd_comment = text_filter($_POST['sd_comment'], 1600, false);
			$type_id = (int)$_POST['type_id'];
			$brand = (int)$_POST['brand'];
			$model = (int)$_POST['model'];
			
			// Create inventory
			if($type_id && ($brand OR $_POST['brand-new']) && ($model OR $_POST['model-new'])){
				
				if($_POST['brand-new']){
					$cat = [];
					$cat = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_categories` WHERE name = \''.trim(text_filter($_POST['brand-new'], 100, false)).'\'');
					if ($cat != []) die('cat_exists');
					$brand = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_categories` SET name = \''.trim(text_filter($_POST['brand-new'], 100, false)).'\'');
					$brand = intval(
						mysqli_insert_id($db_link)
					);
				}
				
				if($_POST['model-new']){
					$mdl = [];
					$mdl = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_models` WHERE name = \''.trim(text_filter($_POST['model-new'], 100, false)).'\' AND category_id = '.$brand);
					if ($mdl != []) die('mdl_exists');
					$model = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_models` SET name = \''.trim(text_filter($_POST['model-new'], 100, false)).'\''.($brand ? ', category_id = '.$brand : ''));
					$model = intval(
						mysqli_insert_id($db_link)
					);
				}
				
				$location = intval($_POST['location']);
				
				// Create location
				if (!$location AND $_POST['location_new']) {
					if (!intval($_POST['store'] ?: $store_id)) die('no_object');
					$loc = [];
					$loc = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_objects_locations` WHERE name = \''.trim(text_filter($_POST['location_new'], 100, false)).'\' AND object_id = '.intval($_POST['store'] ?: $store_id));
					if ($loc != []) die('loc_exists');
					$location = db_query('INSERT INTO `'.DB_PREFIX.'_objects_locations` SET name = \''.trim(text_filter($_POST['location_new'], 100, false)).'\''.(intval($_POST['store'] ?: $store_id) ? ', object_id = '.intval($_POST['store'] ?: $store_id) : ''));
					$location = intval(
						mysqli_insert_id($db_link)
					);
				}
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory` SET
					opt_charger =\''.($_POST['opt_charger'] ? 'YES' : 'NO').'\',
					model =\''.$smodel.'\',
					model_id =\''.$model.'\',
					currency = \''.text_filter($_POST['currency'] ?: 'USD', 25, false).'\',
					ver_os =\''.$os_version.'\',
					serial =\''.$serial.'\',
					barcode =\''.$barcode.'\',
					charger =\''.intval($_POST['charger']).'\',
					save_data =\''.intval($_POST['save_data']).'\',
					save_data_comment =\''.text_filter($_POST['sd_comment'], 1600, false).'\',
					object_owner =\''.intval($_POST['store'] ?: $store_id).'\',
					type_id = \''.$type_id.'\',
					customer_id = \''.$customer_id.'\',
					object_id = \''.intval($_POST['store'] ?: $store_id).'\',
					category_id = \''.$brand.'\',
					os_id = \''.intval($_POST['os']).'\',
					location_id = \''.$location.'\',
					type = \'stock\',
					location_count = \''.intval($_POST['sublocation']).'\',
					'.($_POST['opts'] ? 'options = \''.json_encode(array_text_filter($_POST['opts'], 50, false), JSON_UNESCAPED_UNICODE).'\',' : '').'
					cr_user = \''.$user['id'].'\',
					cr_date = \''.date('Y-m-d H:i:s').'\''
				);
				
				$inventory_id = intval(mysqli_insert_id($db_link));
				$res['href'] = '/inventory/view/'.$inventory_id;
			}
			
			// ISSUE
			$descr = text_filter($_POST['descr'], 1600, false);
			$inventory = ids_filter($_POST['inventory']);
			$service = ids_filter($_POST['service']);
			
			if($inventory OR $service){
				
				$s_ids = $inventory.($inventory && $service ? ',' : '').$service;
				
				$inventory = [];
				$service = [];
				
				$c_vals = '';
				$o_vals = '';
				$end = time();
				
				foreach(db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
					i.id,
					i.type,
					REPLACE(IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name), \'"\', \'\') as name, 
					i.price,
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
					WHERE i.id IN('.$s_ids.')', true
				) as $inv){
					if($inv['type'] == 'stock'){
						$inventory[$inv['id']] = [
							'name' => $inv['name'],
							'lastname' => $inv['catname'],
							'price' => '$'.floatval($inv['price'])
						];
					} else {
						$service[$inv['id'].'_'.$end] = [
							'name' => $inv['name'],
							'lastname' => $inv['catname'],
							'price' => floatval($inv['price']),
							'req' => $inv['req'],
							'currency' => $inv['currency']
						];
						$c_vals .= '"'.json_escape($inv['id'].'_'.$end).'":{"staff" : "0", "comment": ""},';
						$o_vals .= '"'.json_escape($inv['id'].'_'.$end).'":"",';
					}
				}
				$inventory = json_encode($inventory);
				$service = json_encode($service);
				
				$sql = db_multi_query('SELECT customer_id, object_owner, status_id as status FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$inventory_id);
		
				db_query('INSERT INTO `'.DB_PREFIX.'_issues` SET
					staff_id = '.$user['id'].',
					intake_id = '.$user['id'].',
					customer_id = '.$sql['customer_id'].',
					object_owner = '.$sql['object_owner'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					status_id = 11,
					description = \''.text_filter($_POST['descr'], 2000, false).'\',
					inventory_info = \''.db_escape_string($inventory).'\',
					service_info = \''.db_escape_string($service).'\',
					currency = \''.text_filter($_POST['currency'] ?: 'USD', 25, false).'\',
					'.($o_vals ? 'options = \'{'.$o_vals.'}\', comments = \'{'.$c_vals.'}\',' : '').'
					inventory_id = '.$inventory_id
				);

				$issue_id = intval(mysqli_insert_id($db_link));
				$res['href'] = '/issues/view/'.$issue_id;
				
				$oId = db_multi_query('SELECT object_id FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$inventory_id);
				db_query('INSERT INTO `'.DB_PREFIX.'_issues_changelog` SET
					issue_id = '.$issue_id.',
					date = \''.date('Y-m-d H:i:s', $end).'\',
					user = '.$user['id'].',
					changes = \'New job\',
					object_id = '.$oId['object_id']
				);
				db_query('UPDATE `'.DB_PREFIX.'_inventory` SET is_issue = 1, pickup = 0 WHERE id = '.intval($_POST['inventory_id']));
			}
			echo json_encode($res);
			die;
		break;
		
		/*
		* Concat
		*/
		case 'concat':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['main']);
			$ids = ids_filter($_POST['ids']);
			$modules = ['issues', 'invoices', 'purchases', 'users_notes', 'users_appointments', 'users_onsite', 'inventory'];
			
			$users = db_multi_query('
				SELECT 
					email, 
					phone 
				FROM `'.DB_PREFIX.'_users` 
				WHERE id IN('.$ids.')
			', true);
			
			if (!$id)
				die('no_main');
			
			if (count(explode(',', $ids)) < 2)
				die('no_profiles');
			
			foreach($modules as $m) {
				db_query('UPDATE `'.DB_PREFIX.'_'.$m.'` SET 
					'.($m == 'users_notes' ? 'user_id' : 'customer_id').' = '.$id.'
				WHERE '.($m == 'users_notes' ? 'user_id' : 'customer_id').' IN ('.$ids.') AND '.($m == 'users_notes' ? 'user_id' : 'customer_id').' != '.$id);
			}
			
			$phone = [];
			foreach(array_column($users, 'phone') as $p) {
				if ($p) {
					foreach(explode(',', $p) as $ph) {
						if (!in_array($ph, $phone))
							$phone[] = $ph;
					}
				}
			}
			
			$email = '';
			$emails = array_column($users, 'email', 'id');
			if (filter_var($emails[$id], FILTER_VALIDATE_EMAIL))
				$email = $emails[$id];
			else {
				foreach($emails as $e) {
					if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
						$email = $e;
						continue;
					}
				}
			}
			
			db_query('UPDATE `'.DB_PREFIX.'_users` SET 
					phone = \''.implode(',', $phone).'\',
					email = \''.$email.'\'
				WHERE id = '.$id
			);
			
			db_query('UPDATE `'.DB_PREFIX.'_users` SET 
					dub = '.$id.',
					del = 1
				WHERE id IN('.$ids.') AND id != '.$id
			);
			
			die('OK');
		break;
		
		/*
		* Dublicates
		*/
		case 'dublicates':
			$type = in_array(text_filter($_POST['type']), ['email', 'name', 'phone']) ? text_filter($_POST['type']) : 'email';
			$page = intval($_POST['page']);
			$count = 10;
			$email = '';
			$content = '';
			$items = '';
			$ids = [];
			
			if ($users = db_multi_query('
				SELECT SQL_NO_CACHE
					u.id uid,
					CONCAT(u.name, \' \', u.lastname) as uname,
					u.email as uemail,
					u.phone as uphone,
					u.image as uimage,
					d.id did,
					CONCAT(d.name, \' \', d.lastname) as dname,
					d.email as demail,
					d.phone as dphone,
					d.image as dimage
				FROM `'.DB_PREFIX.'_users` u
				INNER JOIN `'.DB_PREFIX.'_users` d 
					ON '.(
						$type == 'name' ? 'u.name = d.name AND u.lastname = d.lastname' : 'u.'.$type.' = d.'.$type
					).' AND u.id != d.id
				WHERE u.del = 0 AND d.del = 0 '.(
					$type == 'email' ? 'AND u.email != \'noreply@yoursite.com\' AND u.email != \'\'' : ($type == 'phone' ? 'AND u.phone != \'\'' : '')
				).'
				LIMIT '.($page*$count).', '.$count
			, true)) {
				$vars = '';
				foreach(array_column($users, 'u'.$type) as $u) {
					$vars .= ($vars ? ',' : '').'"'.$u.'"';
				}
				$dublicates = db_multi_query('
					SELECT SQL_NO_CACHE
						u.id,
						CONCAT(u.name, \' \', u.lastname) as name,
						u.email,
						u.phone,
						u.image
					FROM `'.DB_PREFIX.'_users` u
					WHERE u.del = 0 AND '.($type == 'name' ? 'CONCAT(u.name, \' \', u.lastname)' : 'u.'.$type).' IN ('.$vars.')
					ORDER BY '.($type == 'name' ? 'CONCAT(u.name, \' \', u.lastname)' : 'u.'.$type).'
				', true);
				$c = 0;
				$id = $dublicates[0]['id'];
				foreach($dublicates as $i => $d) {
					if (in_array($d['id'], $ids))
						continue;
					$items .= '<div class="dublicate" data-id="'.$d['id'].'">
							<div class="dub-id">
								<a href="#" onclick="user.dontConcat(this);return false;" class="dub-not"><span class="fa fa-times"></span></a>
								#'.$d['id'].'
							</div>
							<div class="dub-image"><a href="/users/view/'.$d['id'].'" onclick="Page.get(this.href); return false;">
								'.(
									$d['image'] ? '<img src="/uploads/images/users/'.$d['id'].'/thumb_'.$d['image'].'">' : '<span class="fa fa-user-secret"></span>'
								).'
							</a></div>
							<div class="dub-name'.($type == 'name' ? ' dub-option' : '').'"><a href="/users/view/'.$d['id'].'" onclick="Page.get(this.href); return false;">'.$d['name'].'</a></div>
							<div class="dClear"></div>
							<div class="dub-email'.($type == 'email' ? ' dub-option' : '').'">'.$d['email'].'</div>
							<div class="dub-phone'.($type == 'phone' ? ' dub-option' : '').'">'.$d['phone'].'</div>
							<div class="dub-main">
								<input type="radio" name="dublicates['.$id.']" value="'.$d['id'].'" data-label="Main profile">
							</div>
						</div>';
					$ids[] = $d['id'];
					$c ++;
					if (strtolower($dublicates[$i + 1][$type]) != strtolower($d[$type])) {
						$content .= '<div class="dublicate-group dub-count-'.$c.'">
										'.$items.'
										<div class="dClear"></div>
										<div class="dub-concat">
											<button class="btn btnSubmit" onclick="user.concat(this, '.$id.');">Merge</button>
										</div>
									</div>';
						$items = '';
						$c = 0;
						$id = $dublicates[$i + 1]['id'];
					}
				}
			}
			$meta['title'] = 'User Dublicates';
			if($_POST){
				exit(json_encode([
					'res_count' => 1,
					'left_count' => 1,
					'content' => $content,
				]));
			}
			tpl_set('users/dublicates', [
				'users' => $content,
				'more' => ''
			], [], 'content');
		break;
		
		/* 
		* Send appointment
		*/
		case 'confirm_app':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$type = intval($_POST['type']);
			
			if (!$user['confirm_appointment'])
				die('NO_ACC');
			
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE id = '.$id.' AND type = \'new_appointment\'');
			if ($type)
				db_query('UPDATE `'.DB_PREFIX.'_users_appointments` SET confirmed = 1, staff_id = '.$user['id'].' WHERE id = '.$id);
			
			$app = db_multi_query('SELECT agent_id FROM `'.DB_PREFIX.'_users_appointments` WHERE id = '.$id);
			if (intval($app['agent_id'])) {
				db_query('UPDATE `'.DB_PREFIX.'_agents_balance` SET done = 1 WHERE appointment_id = '.$id);
				$b = db_multi_query('SELECT amount FROM `'.DB_PREFIX.'_agents_balance` WHERE appointment_id = '.$id);
				db_query('UPDATE `'.DB_PREFIX.'_agents` SET balance = balance + '.$b['amount'].' WHERE id = '.$app['agent_id']);
			}
			die('OK');
		break;
		
		/* 
		* Send appointment
		*/
		case 'send_app':
			is_ajax() or die('Hacking attempt!');
			$user_id = intval($_POST['user_id']);
			$date = text_filter($_POST['date'], 10, false);
			$time = text_filter($_POST['time'], 5, false);
			$object = intval($_POST['object']);
			
			if (!$user_id)
				die('no_user');
			
			if (!$object)
				die('no_object');
			
			db_query('
				INSERT INTO `'.DB_PREFIX.'_users_appointments` SET 
					customer_id = \''.$user_id.'\',
					date = \''.$date.' '.$time.'\',
					object_id = \''.$object.'\'
			');
			
			$id = intval(mysqli_insert_id($db_link));
			
			db_query('
				INSERT INTO `'.DB_PREFIX.'_notifications` SET 
					type = \'new_appointment\', 
					customer_id = '.$user_id.', 
					id = '.$id
			);
			
			$cid = db_multi_query('
				SELECT 
					u.sms,
					o.name
				FROM `'.DB_PREFIX.'_users` u,
					`'.DB_PREFIX.'_objects` o
				WHERE u.id = '.$user_id.' AND o.id = '.$object
			);
			
			if ($cid['sms'] AND $user_id != 17)
				send_sms($cid['sms'], 'You have successfully scheduled a free diagnosis appointment at Your Company of '.$cid['name'].' on '.date('l', strtotime($date)).' '.date('F jS', strtotime($date)).' at '.date('hA', strtotime($date)));
			
			db_query('
				INSERT INTO 
				`'.DB_PREFIX.'_activity` SET 
					user_id = \''.$user['id'].'\', 
					event = \'new_appointment\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					event_id = '.$id.',
					object_id = '.$object.'
			');
			
			$point = $config['user_points']['appointment']['points'];
			
			db_query('
				INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					user_id = '.$user_id.',
					object_id = '.$object.',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					action = \'new_appointment\',
					point = '.($point ?: 0)
			);
			
			db_query(
				'UPDATE `'.DB_PREFIX.'_users`
					SET points = points+'.($point ?: 0).'
				WHERE id = '.$user['id']
			);
				
			die('OK');
		break;
		
		/*
		* Add appointment
		*/
		case 'add_appointment':
			if ($user_id = intval($_GET['user'])) {
				tpl_set('users/appointment', [
					'user_id' => $user_id
				], [
					'add' => $user['add_users'],
					'owner' => in_array(1, explode(
						',',$user['group_ids']
						)
					),
					'user_new' => (in_to_array('1,2', $user['group_ids']))
				], 'content');
			} else {
				tpl_set('forbidden', [
					'text' => 'No user id'
				], [
					'add' => $user['add_users'],
					'owner' => in_array(1, explode(
						',',$user['group_ids']
						)
					),
					'user_new' => (in_to_array('1,2', $user['group_ids']))
				], 'content');
			}
		break;
		
		/*
		* Send suspention
		*/
		case 'send_suspention':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$suspention = intval($_POST['suspention']);
			$comment = text_filter($_POST['comment']);
			$third = intval($_POST['third']);
			
			$point = db_multi_query('SELECT points FROM `'.DB_PREFIX.'_users_writeup` WHERE id = '.$suspention);
			
			db_query('
				INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$id.',
					user_id = '.$user['id'].',
					object_id = '.intval($user['store_id']).',
					writeup_id = '.$suspention.',
					suspention = '.$third.',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					action = \'write_up\',
					comment = \''.$comment.'\',
					point = '.($point ? intval($point['points']) : 0)
			);
			
			db_query(
				'UPDATE `'.DB_PREFIX.'_users`
					SET points = points+'.($point ? floatval($point['points']) : 0).'
				WHERE id = '.$id
			);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET type = \'write_up\', id = '.$suspention.', staff = '.$id);
			send_push($id, [
				'type' => 'purchase',
				'id' => '/users/points/'.$id.'?writeup=1',
				'name' => $user['uname'],
				'lastname' => $user['ulastname'],
				'message' => 'You got new write up'
			]);
			
			if (floatval($point['points']) < 0) {
				if ($wt = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_timer` WHERE DATE(date) = \''.date('Y-m-d', time()).'\' AND user_id = '.$id)) {
					db_query('UPDATE `'.DB_PREFIX.'_timer` SET seconds = seconds + '.(floatval($point['points']) * $config['min_forfeit']*60).' WHERE id = '.$wt['id']);
					db_query('INSERT INTO `'.DB_PREFIX.'_users_time_forfeit` SET user_id = '.$id.', forfeit = '.(floatval($point['points']) * $config['min_forfeit']*60));
				}
			}
			
			die('OK');
		break;
		
		/*
		* Is suspention
		*/
		case 'is_suspention':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$suspentions = '';
			
			if ($user['make_suspention']) {
				$write_up = db_multi_query('
					SELECT 
						SQL_CALC_FOUND_ROWS point
					FROM `'.DB_PREFIX.'_inventory_status_history`
					WHERE staff_id = '.$id.' AND action = \'write_up\'
					ORDER BY id DESC
					LIMIT 0, 2
				', true);
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				
				if ($writeups = db_multi_query('
					SELECT
						id,
						name
					FROM `'.DB_PREFIX.'_users_writeup`
					WHERE 1 '.(
						$res_count % 3 == 2 ? ' AND points < ('.($write_up[0]['point'] + $write_up[1]['point']).')' : ''
					)
				, true)) {
					foreach($writeups as $wr) {
						$suspentions .= '<option value="'.$wr['id'].'">'.$wr['name'].'</option>';
					}
				}
				
				print_r(json_encode([
					'res' => 'OK',
					'third' => ($res_count % 3 == 2 ? 1 : 0),
					'suspentions' => $suspentions
				]));
				die;
			} else 
				print_r([
					'res' => 'no_acc'					
				]);
				die;
		break;
		
		/*
		*  Add/edit writeup
		*/
		case 'send_writeup':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if (in_to_array('1,2', $user['group_ids'])) {
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_users_writeup` SET
						name =\''.text_filter($_POST['name'], 50, false).'\''.(
					$id ? ' WHERE id = '.$id : ''
				));
				echo $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
				die;
			} else 
				die('no_acc');			
		break;
		
		/*
		*  Del writeup
		*/
		case 'del_writeup':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if(in_to_array('1,2', $user['group_ids'])){
				db_query('DELETE FROM `'.DB_PREFIX.'_users_writeup` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('no_acc');
		break;
		
		/*
		*  Write up
		*/
		case 'writeup':
			$meta['title'] = 'Write up';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			
			if (in_to_array('1,2', $user['group_ids'])) {
				
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						id, name
					FROM `'.DB_PREFIX.'_users_writeup` '.(
					$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
				).'LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('users/writeup/item', [
							'id' => $row['id'],
							'name' => $row['name']
						], [
						], 'writeup');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				} else {
					tpl_set('noContent', [
						'text' => 'No write up'
					], [], 'writeup');
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['writeup'],
					]));
				}
			
				tpl_set('users/writeup/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'writeup' => $tpl_content['writeup']
				], [
					'add' => $user['add_users'],
					'owner' => in_array(1, explode(
						',',$user['group_ids']
						)
					),
					'sending' => $user['multi_sending'],
					'user_new' => (in_to_array('1,2', $user['group_ids']))
				], 'content');
			} else {
				tpl_set('forbidden', [
					'text' => 'You have no access to do this'
				], [
					'add' => $user['add_users'],
					'owner' => in_array(1, explode(
						',',$user['group_ids']
						)
					),
					'user_new' => (in_to_array('1,2', $user['group_ids']))
				], 'content');
			}
		break;
		
		/*
		* Confirm onsite
		*/
		case 'dash_confirm_onsite':
			$id = intval($_POST['id']);
			
			$now = date('Y-m-d H:m:s', time());
			$onsite = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users_onsite` WHERE id = '.$id);
			$service = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_onsite` WHERE id = '.$onsite['onsite_id']);
			$tax = db_multi_query('SELECT tax FROM `'.DB_PREFIX.'_objects` WHERE id = '.$onsite['last_object']);
			
			db_query('UPDATE `'.DB_PREFIX.'_users_onsite` SET
					confirmed = 1,
					selected_staff_id = '.intval($user['id']).'
				WHERE id = '.$id
			);       
			
			db_query('INSERT INTO `'.DB_PREFIX.'_users_onsite_changelog` SET
				staff_id = '.$user['id'].',
				date = \''.$now.'\',
				onsite_id = '.$id.',
				object_id = '.$onsite['last_object'].',
				event = \'create\'
			');

            db_query('INSERT INTO `'.DB_PREFIX.'_invoices` SET
                customer_id = '.$onsite['customer_id'].',
                object_id = '.$onsite['last_object'].',
                date = \''.date('Y-m-d H:i:s', time()).'\',
                onsite_id = '.$onsite['onsite_id'].',
				currency = \''.$service['currency'].'\',
				staff_id = '.$user['id'].',
				date = \''.($onsite['date_start'] ?: date('Y-m-d', time())).' 00:00:00\',
                total = \''.(floatval($service['price'])).'\',
                tax = 0
            ');

            $invoice_id = intval(mysqli_insert_id($db_link));

            db_query('UPDATE `'.DB_PREFIX.'_users_onsite` SET
                invoice_id = '.$invoice_id.'
            WHERE id = '.$id
            );
			
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'e_onsite\'');
			
			echo $invoice_id;
			die;
		break;
		
		/* 
		* Edit onsite
		*/
		case 'onsite_update_date':
			is_ajax() or die('hacking');

			db_query('
                UPDATE `'.DB_PREFIX.'_users_onsite` SET
                    date_end = \''.text_filter($_POST['date_end'], 10, false).'\'
                WHERE id = '.intval($_POST['id'])
            );
			
			die('OK');
		break;
		
        /*
        * Update onsite staff
        */
        case 'onsite_update_staff':
            is_ajax() or die('hacking');
            $id = intval($_POST['id']);
            $staff_id = intval($_POST['staff']);

            db_query('
                UPDATE `'.DB_PREFIX.'_users_onsite` SET
                    selected_staff_id = '.$staff_id.',
                    selected_time = \''.text_filter($_POST['time'], 8, false).'\'
                WHERE id = '.$id
            );

            if ($staff_id > 0) {
                $cid = db_multi_query('
					SELECT 
						o.customer_id , 
						u.sms 
					FROM `'.DB_PREFIX.'_users_onsite` o 
					LEFT JOIN `'.DB_PREFIX.'_users` u 
						ON u.id = o.selected_staff_id 
					WHERE o.id = '.$id
				);
                db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE type = \'new_onsite\' AND id = '.$id);
                db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET type = \'new_onsite\', staff = '.$staff_id.', customer_id = '.$cid['customer_id'].', id = '.$id);
				send_push($staff_id, [
					'type' => 'purchase',
					'id' => '/users/view/'.$cid['customer_id'],
					'name' => $user['uname'],
					'lastname' => $user['ulastname'],
					'message' => 'Onsite service #'.$id.' added'
				]);
				if ($cid['sms'])
					send_sms($cid['sms'], str_replace('{customer-id}', $cid['customer_id'], $lang['assignTo']));
            }

            die('OK');
        break;

        /*
        * Onsite info
        */
        case 'onsite_info':
            is_ajax() or die('hacking');           
            $onsite = db_multi_query('
                SELECT 
                    o.*,
                    u.name,
                    u.lastname
                FROM `'.DB_PREFIX.'_users_onsite` o
                LEFT JOIN `'.DB_PREFIX.'_users` u
                    ON u.id = o.selected_staff_id
                WHERE o.id = '.intval($_POST['id'])
            );

            print_r(json_encode($onsite));
            die;
        break;


		/*
		* Del onsite
		*/
		case 'del_onsite':
			is_ajax() or die('hacking');
			db_query('UPDATE `'.DB_PREFIX.'_users_onsite` SET del = 1 WHERE id = '.intval($_POST['id']));
			if (intval($_POST['dash']))
				db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'e_onsite\'');
			die('OK');
		break;
		
		/*
		* Notifications
		*/
		case 'get_notify':
			is_ajax() or die('hacking');
				$ntf = '';
				$count = 0;
				foreach(array_reverse(db_multi_query('
					SELECT * FROM `'.DB_PREFIX.'_notifications`
					WHERE (FIND_IN_SET('.$user['id'].', staff) OR staff = \'\') AND (store_id = 0 OR store_id = '.$store_id.') 
						'.($user['confirm_purchase'] ? '' : ' AND type != \'confirm_purchase\'
					ORDER BY id_del ASC'), true)) as $row){


					switch($row['type']){
						
						case 'confirm_purchase':
							$text = 'Purchase #'.$row['id'].' created. Please, confirm';
							$link = 'Notify.minus(),Page.get(\'/purchases/edit/'.$row['id'].'\');';
						break;

                        case 'new_onsite':
							$text = 'Onsite #'.$row['id'].' added';
							$link = 'Notify.minus(),Page.get(\'/users/view/'.$row['customer_id'].'\');';
						break;
						
						case 'confirm_transfer':
							$text = 'Transfer #'.$row['id'].' created. Please, confirm';
							$link = 'Notify.minus(),Page.get(\'/inventory/transfer/view/'.$row['id'].'\');';
						break;
						
						case 'request_transfer':
							$text = 'Transfer request #'.$row['id'].' created. Please, confirm';
							$link = 'Notify.minus(),Page.get(\'/inventory/transfer/view/'.$row['id'].'\');';
						break;						
						
						case 'returm_request_purchase':
							$text = 'RMA for purchase #'.$row['id'].' created. Please, confirm';
							$link = 'Notify.minus(),Page.get(\'/purchases/edit/'.$row['id'].'\');';
						break;
						
						case 'issue_assigned':
							$link = 'Notify.minus(),Page.get(\'/issues/view/'.$row['id'].'\');';
							$text = '<div onclick="'.$link.'">You assigned to job #'.$row['id'].'. Please, confirm or dicline</div>
									<span class="btn-notif notif-confirm fa fa-check" onclick="issues.assignedConfirm('.$row['id'].', 1);"></span>
									<span class="btn-notif notif-dicline fa fa-times" onclick="issues.assignedConfirm('.$row['id'].', 0);"></span>';
						break;
						
						case 'issue_transfer':
							$link = 'Notify.minus(),Page.get(\'/issues/view/'.$row['id'].'\');';
							$text = '<div onclick="'.$link.'">Transfer job #'.$row['id'].'. Please, confirm</div>
									<span class="btn-notif notif-confirm fa fa-check" onclick="issues.transferConfirm('.$row['id'].');"></span>';
						break;
						
						case 'write_up':
							$text = 'New write up';
							$link = 'Notify.minus(),Page.get(\'/users/points/'.$user['id'].'?writeup=1\');';
						break;
						
						case 'new_quote_request':
							$text = 'New quote request';
							$link = 'Page.get(\'/quote/edit/'.$row['id'].'\'),Notify.minus();';
						break;
						
						case 'new_appointment':
							$link = 'Notify.minus(),Page.get(\'/users/view/'.$row['customer_id'].'\');';
							$text = '<div onclick="'.$link.'">New appointment #'.$row['id'].'. Please, confirm or dicline</div>
									'.($user['confirm_appointment'] ? '<span class="btn-notif notif-confirm fa fa-check" onclick="user.confirmApp(this, '.$row['id'].', 1);"></span>
									<span class="btn-notif notif-dicline fa fa-times" onclick="user.confirmApp(this, '.$row['id'].', 0);"></span>' : '');
						break;
					}
					
					$ntf .= '<div class="nfSigle nfc" '.($row['type'] == 'issue_assigned' ? 'id="notif_'.$row['id'].'"' : ' onclick="'.$link.'"').'>
						<div class="nfDate nfc">'.$row['date'].'</div> '.$text.'
					</div>';
					$count++;
				}
				echo json_encode([
					'count' => $count,
					'html' => $ntf
				]);
			die;
		break;
		
		/* 
		* Activity
		*/
		case 'camera':
			$event = text_filter($_POST['event'], 50, false);
			$object = intval($_POST['object']);
			$date_start = text_filter($_POST['date_start'], 30, true);
			$date_finish = text_filter($_POST['date_finish'], 30, true);
			$updated = ($route[3] == 'updated' ? 1 : 0);
			$page = intval($_POST['page']);
			$count = 20;
			$id = intval($route[2]);


			if($sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS a.*, u.id as uid, u.name, u.lastname, u.image, t.seconds as hours FROM
			`'.DB_PREFIX.'_activity` a
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON a.user_id = u.id 
				LEFT JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = a.user_id AND t.date >= CURDATE()
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON FIND_IN_SET(a.user_id, o.managers) OR FIND_IN_SET(a.user_id, o.staff)
				WHERE (a.user_id = '.$id.' OR '.$id.' IN (a.user_ids)) '.(
				$event ? ' AND a.event = \''.$event.'\'' : ''
			).(
				$object ? 'AND a.object_id = '.$object.' ' : ''
			).(
				$updated ? 'AND (a.camera = 1 OR a.status_id != 0)  ' : ''
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
							$event = $lang['addNew'].' <a href="/purchases/edit/'.$row['event_id'].'" target="_blank">'.$lang['purchase'].'</a>';
						break;
						
						case 'new user':
							$event = $lang['addNew'].' <a href="/users/view/'.$row['event_id'].'" target="_blank">'.$lang['user'].'</a>';
						break;
						
						case 'new inventory':
							$event = $lang['addNew'].' <a href="/inventory/view/'.$row['event_id'].'" target="_blank">'.$lang['inventory'].'</a>';
						break;
						
						case 'new service':
							$event = $lang['addNew'].' <a href="/inventory/edit/'.$row['event_id'].'" target="_blank">'.$lang['service'].'</a>';
						break;
						
						case 'make transaction':
							$event = '<a href="/invoices/view/'.$row['event_id'].'" target="_blank">'.$lang['makeTran'].'</a>';
						break;
						
						default:
							$event = $row['camera'] ? $row['camera_event'] : $row['event'];
						break;
					}
					tpl_set('users/camera/item', [
						'id' => $row['id'],
						'uid' => $row['uid'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'ava' => $row['image'],
						'date' => $row['date'],
						'event' => ucfirst($event),
						'class' => $class
					], [
						'ava' => $row['image']
					], 'activity');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			
			$events = '';
			if ($action = db_multi_query('SELECT DISTINCT event FROM `'.DB_PREFIX.'_activity`', true)) {
				foreach($action as $act) {
					$events .= '<option value="'.$act['event'].'">'.ucfirst(str_replace('_', ' ', $act['event'])).'</option>';
				}
			}

			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'Camera';
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['activity'],
				]));
			}
			tpl_set('users/camera/main', [
				'uid' => $user['id'],
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'activity' => $tpl_content['activity'],
				'events' => $events,
				'group' => 'user',
				'group-id' => '/'.min(explode(',', $u['group_ids'])),
			], [
				'add' => ($user['add_users'] OR $id == $user['id']),
				'user_new' => (in_to_array('1,2', $user['group_ids']))
			], 'content');
		break;

		/*
		* Points history
		*/
		case 'point_details':
			$id = intval($route[2]);
			//if ($user['point_details']) {
				$write_up = intval($_GET['writeup']);
				$object = intval($_REQUEST['object']);
				$date_start = text_filter($_REQUEST['date_start'], 30, true);
				$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
				$page = intval($_REQUEST['page']);
				$count = 50;
				
				$points = $sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
						DATE_FORMAT(sh.date, \'%Y-%m-%d %H:00\') as date,
						SUM(sh.point) as points
					FROM `'.DB_PREFIX.'_inventory_status_history` sh
					WHERE sh.percent = 0 AND sh.point != 0 AND sh.staff_id = '.$id.'
					GROUP BY DATE(sh.date), HOUR(sh.date)
					ORDER BY sh.date DESC
					LIMIT '.($page*$count).', '.$count, true);
					
				
				$sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
						sh.*,
						u.name,
						u.lastname,
						u.image,
						o.name as object,
						s.name as status,
						sw.name as wstatus,
						cs.name as cstatus,
						w.name as write_up,
						i.total as issue_total
					FROM `'.DB_PREFIX.'_inventory_status_history` sh
					LEFT JOIN `'.DB_PREFIX.'_issues` i
						ON sh.issue_id = i.id
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = sh.staff_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = sh.object_id
					LEFT JOIN `'.DB_PREFIX.'_users_writeup` w
						ON w.id = sh.writeup_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON s.id = sh.status_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_warranty_status` sw
						ON sw.id = sh.status_id AND sh.warranty = 1
					LEFT JOIN `'.DB_PREFIX.'_camera_status` cs
						ON cs.id = sh.status_id AND sh.action = \'camera_status\'
					WHERE sh.percent = 0 AND sh.point != 0 AND sh.staff_id = '.$id.' '.(
						$object ? ' AND sh.object_id = \''.$object.'\'' : ''
					).(
						($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).(
						$write_up ? ' AND sh.action = \'write_up\'' : ''
					).'
					ORDER BY sh.date DESC LIMIT '.($page*$count).', '.$count, true);
			
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				
				$total_sum = [];
				if (!$page) {
					$total_sum = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
							SUM(sh.point) as total
						FROM `'.DB_PREFIX.'_inventory_status_history` sh
						LEFT JOIN `'.DB_PREFIX.'_users` u
							ON u.id = sh.staff_id
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = sh.object_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = sh.status_id
						WHERE sh.percent = 0 AND sh.point != 0 AND sh.staff_id = '.$id.' '.(
							$object ? ' AND sh.object_id = \''.$object.'\'' : ''
						).(
							($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).(
							$write_up ? ' AND sh.action = \'write_up\'' : ''
						));
				}
				
				if ($write_up)
					db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE staff = '.$id.' AND type = \'write_up\'');

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
						case 'confirmation_purchase':
							if ($row['purchase_id'])
								$action = '<a href="/purchases/edit/'.$row['purchase_id'].'" target="_blank">'.str_replace('_', ' ', $row['action']).'</a>';
							else 
								$action = str_replace('_', ' ', $row['action']);
						break;
						
						case 'issue_forfeit':
							$action = '<a href="/issues/view/'.$row['issue_id'].'" target="_blank">'.$row['action'].'</a>';
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
					
					tpl_set('users/points/item', [
						'id' => $row['id'],
						'points' => number_format($row['percent'] ? (($row['issue_total']/100)*$row['percent']) : $row['point'], 2, '.', ''),
						'date' => $row['date'],
						'object' => $row['object'],
						'action' => $action,
					], [
						'personal' => $row['action'] != 'store_points',
						'details' => true
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
						'total' => number_format($total_sum['total'], 2, '.', '')
					]));
				}
				tpl_set('users/points/main', [
					'uid' => $id,
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'timers' => $tpl_content['salary'],
					'group' => 'user',
					'group-id' => '/'.min(explode(',', $u['group_ids'])),
					'total' => number_format($total_sum['total'], 2, '.', '')
				], [
					'add' => ($user['add_users'] OR $id == $user['id']),
					'user_new' => (in_to_array('1,2', $user['group_ids'])),
					'details' => true
				], 'content');
			/*} else {
				tpl_set('forbidden', [
					'text' => 'You have no access to this page'
				], [
				], 'content');
			}*/
		break;
		
		/*
		* Points per hout
		*/
		case 'points':
			$id = intval($route[2]);
			$write_up = intval($_GET['writeup']);
			$object = intval($_REQUEST['object']);
			$date_start = text_filter($_REQUEST['date_start'], 30, true);
			$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
			$page = intval($_REQUEST['page']);
			$count = 50;
			
			$points = $sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
					DATE_FORMAT(sh.date, \'%Y-%m-%d %H:00\') as date,
					SUM(sh.point) as points
				FROM `'.DB_PREFIX.'_inventory_status_history` sh
				WHERE sh.percent = 0 AND sh.point != 0 AND sh.staff_id = '.$id
				.(
					$object ? ' AND sh.object_id = \''.$object.'\'' : ''
				).(
					($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).(
					$write_up ? ' AND sh.action = \'write_up\'' : ''
				).'
				GROUP BY DATE(sh.date), HOUR(sh.date)
				ORDER BY sh.date DESC
				LIMIT '.($page*$count).', '.$count, true);
		
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			$total_sum = [];
			if (!$page) {
				$total_sum = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
						SUM(sh.point) as total
					FROM `'.DB_PREFIX.'_inventory_status_history` sh
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = sh.staff_id
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = sh.object_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON s.id = sh.status_id
					WHERE sh.percent = 0 AND sh.point != 0 AND sh.staff_id = '.$id.' '.(
						$object ? ' AND sh.object_id = \''.$object.'\'' : ''
					).(
						($date_start AND $date_finish) ? ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).(
						$write_up ? ' AND sh.action = \'write_up\'' : ''
					));
			}
			
			if ($write_up)
				db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE staff = '.$id.' AND type = \'write_up\'');

			$i = 0;
			foreach($sql as $row){
			
				tpl_set('users/points/item', [
					'points' => number_format($row['points'], 2, '.', ''),
					'date' => $row['date']
				], [
					'details' => false,
					'personal' => false
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
					'total' => number_format($total_sum['total'], 2, '.', '')
				]));
			}
			tpl_set('users/points/main', [
				'uid' => $id,
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'timers' => $tpl_content['salary'],
				'group' => 'user',
				'group-id' => '/'.min(explode(',', $u['group_ids'])),
				'total' => number_format($total_sum['total'], 2, '.', '')
			], [
				'add' => ($user['add_users'] OR $id == $user['id']),
				'user_new' => (in_to_array('1,2', $user['group_ids'])),
				'details' => false,
				'can_details' => $user['point_details']
			], 'content');
		break;
		
		/*
		* Avilable objects
		*/
		case 'objects':
			$lId = intval($_POST['lId']);
			$onsite = (int)$_GET['onsite'];
			$nIds = ids_filter($_POST['nIds']);
			$query = text_filter($_POST['query'], 100, false);
			$id = array_flip($config['object_ips']);
			
			$objects = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, tax FROM `'.DB_PREFIX.'_objects` WHERE 1
			'.(
				(in_to_array('1,2', $user['group_ids']) || $onsite) ? ' ' : 
				' AND id = '.$user['store_id']
			).(
				$nIds ? ' AND id NOT IN('.$nIds.')' : ''
			).(
				$lId ? ' AND id < '.$lId : ''
			).(
				$query ? ' AND `name` LIKE \'%'.$query.'%\' ': ''
			).' ORDER BY `id` DESC LIMIT 20', true);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $objects,
				'count' => $res_count,
			]));
		break;
		
		/*
		* Onsite statistics
		*/
		case 'onsite_stats':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;

			if($sql = db_multi_query('
				SELECT 
					SQL_CALC_FOUND_ROWS c.*,
					u.name,
					u.lastname,
					u.image,
					o.name as object
				FROM `'.DB_PREFIX.'_users_onsite_changelog` c
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.staff_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = c.object_id
				WHERE c.onsite_id = '.$id.'
				ORDER BY c.id DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row) {
					$time = $row['used_time'];
					$s = $time % 60;
					$m = ($time % 3600 - $s) / 60;
					$h = ($time - $s - $m * 60) / 3600;
					
					tpl_set('users/onsite_stat', [
						'id' => $row['id'],
						'date' => $row['date'],
						'event' => $row['event'],
						'icon' => $row['event'] == 'create' ? 'file' : $row['event'],
						'used_time' => $h.':'.$m.':'.$s,
						'note' => $row['note'],
						'user_id' => $row['staff_id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'object' => $row['object']
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
		* Update service
		*/
		case 'update_onsite':
			$id = intval($_POST['id']);
			$onsite_id = intval($_POST['onsite']);
			$store_id = intval($_POST['store']);
			$event = text_filter($_POST['action'], 20, false);
			$service_type = text_filter($_POST['service_type'], 20, false);
			$now = date('Y-m-d H:i:s', time());
			$used_time = 0;
			
			$ct = db_multi_query('
				SELECT 
					s.*, 
					ABS(TIMESTAMPDIFF(SECOND, \''.$now.'\', s.date_control)) as used,
					TIMESTAMPDIFF(SECOND, \''.$now.'\', s.date_end) as date_left, 
					o.onsite_payment,
					i.type,
					i.calls as service_calls,
					i.time as service_time
				FROM `'.DB_PREFIX.'_users_onsite` s
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = s.last_object
				LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` i
					ON i.id = s.onsite_id
				WHERE s.id = '.$id);

            if ($event == 'play') {
                db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE type = \'new_onsite\' AND id = '.$id);
            }
			
			$date = explode('-', $ct['end_date']);
			if (intval($date[0]) > 0 AND ($ct['left_time'] <= 0 AND $ct['calls'] == 0 OR intval($ct['date_left']) <= 0)
				OR (intval($date[0]) == 0 AND $ct['left_time'] <= 0 AND $ct['calls'] == 0)
				OR $ct['del'] == 1)
				die('complited');
				
			if ($service_type == 'call' AND ($ct['calls'] == 0 OR $ct['service_calls'] == 0))
				die('no_calls');

			if ($service_type == 'time' AND ($ct['left_time'] <= 0 OR $ct['service_time'] == 0))
				die('no_time');
			
			if ($ct['last_event'] == $event)
				die('done');
			
			/* if ($user['id'] == 17) {
				print_r($ct);
				//die;
			} */
			
			$send = db_multi_query('
				SELECT 
					u.name, 
					u.lastname, 
					u.phone, 
					u.address, 
					u.sms, 
					u.email,
					os.name as onsite_name,
					os.price,
					os.currency,
					cl.used_time,
					o.left_time,
					o.date_start,
					o.date_end
				FROM `'.DB_PREFIX.'_users_onsite_changelog` cl
				LEFT JOIN `'.DB_PREFIX.'_users_onsite` o
					ON o.id = cl.onsite_id
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = o.customer_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
					ON os.id = o.onsite_id
				WHERE o.id = '.$id);
						
			if ($event == 'stop' OR $event == 'pause') {
				$service_type = ($ct['use_call'] == 1 ? 'call' : 'time');
				$used_time = $ct['used'];
				$payment = $used_time / 3600 * $ct['onsite_payment'];
				
				if ($event == 'stop') {
					$left_time = $send['left_time'] - $used_time;

					$s = $left_time % 60;
					$m = ($left_time % 3600 - $s) / 60;
					$h = ($left_time - $s - $m * 60) / 3600;

					$us = $used_time % 60;
					$um = (($used_time / 60 > 1) ? ($used_time % 3600 - $s) / 60 : 0);
					$uh = (($um AND ($used_time - $s) / 3600) ? (($used_time - $s - $m * 60) / 3600) : 0);

					$sms = str_ireplace([
						'{logo}',
						'{customer_name}',
						'{customer_lastname}',
						'{customer_phone}',
						'{customer_address}',
						'{customer_email}',
						'{service_name}',
						'{price}',
						'{currency}',
						'{used_time}',
						'{time_left}',
						'{date}',
						'{service_start_date}',
						'{service_end_date}'
					],[
						'<img src="//'.$_SERVER['HTTP_HOST'].'/templates/admin/img/logo.svg" style="max-width: 300px">',
						$send['name'],
						$send['lastname'],
						$send['phone'],
						$send['address'],
						$send['email'],
						$send['onsite_name'],
						$send['price'],
						$config['surrency'][$send['currency']]['symbol'] ?: '$',
						$uh.':'.$um.':'.$us,
						$h.':'.$m.':'.$s,
						date('Y-m-d H:i:s', time()),
						$send['date_start'] == '0000-00-00' ? '' : $send['date_start'],
						$send['date_end'] == '0000-00-00' ? '' : $send['date_end']
					], $config['sms_onsite']);

					$email = str_ireplace([
						'{logo}',
						'{customer_name}',
						'{customer_lastname}',
						'{customer_phone}',
						'{customer_address}',
						'{customer_email}',
						'{service_name}',
						'{price}',
						'{currency}',
						'{used_time}',
						'{time_left}',
						'{date}',
						'{service_start_date}',
						'{service_end_date}'
					],[
						'<img src="//'.$_SERVER['HTTP_HOST'].'/templates/admin/img/logo.svg" style="max-width: 300px">',
						$send['name'],
						$send['lastname'],
						$send['phone'],
						$send['address'],
						$send['email'],
						$send['onsite_name'],
						$send['price'],
						$send['currency'],
						$uh.':'.$um.':'.$us,
						$h.':'.$m.':'.$s,
						date('Y-m-d H:i:s', time()),
						$send['date_start'] == '0000-00-00' ? '' : $send['date_start'],
						$send['date_end'] == '0000-00-00' ? '' : $send['date_end']
					], $config['form_onsite']);

					// send sms
					send_sms($send['sms'], $sms);
					
					// Headers
					$headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: '.$send['email']. "\r\n";
					$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

					// Send
					mail($send['email'], 'Onsite service report', $email, $headers);
				}
			} else {
				if (!$store_id) 
					die('no_store');
				else 
					send_sms($send['sms'], text_filter($_POST['user_message'], 500, false));
			}
			
			/* echo 'UPDATE `'.DB_PREFIX.'_users_onsite` SET
					date_control = \''.$now.'\',
					'.($service_type == 'call' ? 'use_call = 1, ' : '').'
					last_event = \''.$event.'\''.(
						($event == 'stop' OR $event == 'pause') ? 
							(
								$service_type == 'call' ? ', calls = (calls - 1)' : ', left_time = (left_time - '.$used_time.')'
							) : ', last_object = '.$store_id
					).'
				WHERE id = '.$id;
				die; */
			
			db_query('UPDATE `'.DB_PREFIX.'_users_onsite` SET
					date_control = \''.$now.'\',
					'.(($service_type == 'call' AND $event == 'play') ? 'use_call = 1, ' : '').'
					last_event = \''.$event.'\''.(
						($event == 'stop' OR $event == 'pause') ? 
							(
								$service_type == 'call' ? ', calls = (calls - 1), use_call = 0' : ', left_time = (left_time - '.$used_time.')'
							) : ', last_object = '.$store_id
					).'
				WHERE id = '.$id
			);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_users_onsite_changelog` SET
				staff_id = '.$user['id'].',
				date = \''.$now.'\',
				note = \''.text_filter($_POST['note'], 1000, false).'\',
				onsite_id = '.$id.',
				object_id = '.($store_id ?: $ct['last_object']).',
				'.($service_type == 'call' ? 'use_call = 1, ' : '').'
				event = \''.$event.'\''.(
					($event == 'stop' OR $event == 'pause') ? 
						', used_time = '.$used_time.',
						paid = \''.$payment.'\''
						: ''
				).'
			');
			
			die('OK');
		break;
		
		/*
		* Send service
		*/
		case 'send_service':
			$id = intval($_POST['id']);
			$service_id = intval($_POST['service']);
			$object_id = intval($_POST['object']);
			$staff_id = intval($_POST['staff']);
			$time = text_filter($_POST['time'], 8, false);
			$date_start = text_filter($_POST['date_start'], 20, false);
			$date_end = text_filter($_POST['date_end'], 20, false);
			$date_ex = explode('-', $date_end);
			
			$now = date('Y-m-d H:m:s', time());
			$service = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_onsite` WHERE id = '.$service_id);
			$tax = db_multi_query('SELECT tax FROM `'.DB_PREFIX.'_objects` WHERE id = '.$object_id);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_users_onsite` SET
				customer_id = '.$id.',
				staff_id = '.$user['id'].',
				onsite_id = '.$service_id.',
				date = \''.$now.'\',
				date_start = \''.$date_start.'\',
				date_end = \''.$date_end.'\',
				date_control = \''.$now.'\',
				left_time = '.($service['time'] * 3600).',
				calls = '.$service['calls'].',
				last_object = '.$object_id.',
				last_event = \'create\',
				selected_staff_id = '.$staff_id.',
				selected_time = \''.$time.'\',
				update_date = \''.($date_start ?: date('Y-m-d', time())).'\',
				confirmed = 1
			');
			
			$onsite_id = intval(mysqli_insert_id($db_link));

            if ($staff_id > 0) {
                db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET type = \'new_onsite\', staff = '.$staff_id.', customer_id = '.$id.', id = '.$onsite_id);
				send_push($staff_id, [
					'type' => 'purchase',
					'id' => '/users/view/'.$id,
					'name' => $user['uname'],
					'lastname' => $user['ulastname'],
					'message' => 'Onsite service #'.$onsite_id.' added'
				]);
            }
			
			db_query('INSERT INTO `'.DB_PREFIX.'_users_onsite_changelog` SET
				staff_id = '.$user['id'].',
				date = \''.$now.'\',
				onsite_id = '.$onsite_id.',
				object_id = '.$object_id.',
				event = \'create\'
			');

            db_query('INSERT INTO `'.DB_PREFIX.'_invoices` SET
                customer_id = '.$id.',
                object_id = '.$object_id.',
                date = \''.date('Y-m-d H:i:s', time()).'\',
                onsite_id = '.$service_id.',
				currency = \''.$service['currency'].'\',
                total = \''.(floatval($service['price'])).'\',
                tax = 0
            ');

            $invoice_id = intval(mysqli_insert_id($db_link));

            db_query('UPDATE `'.DB_PREFIX.'_users_onsite` SET
                invoice_id = '.$invoice_id.'
            WHERE id = '.$onsite_id
            );
			
			echo $invoice_id;
			die;
		break;
		
		/*
		* Get managers
		*/
		case 'all':
			$id = intval($_REQUEST['id']);
			$gid = intval($_REQUEST['gId']);
			$staff = intval($_REQUEST['staff']) OR intval($_REQUEST['noCust']);
			$lId = intval($_REQUEST['lId']);
			$nIds = ids_filter($_REQUEST['nIds']);
			$nCom = intval($_REQUEST['nCom']);
			if(isset($_REQUEST['q']))
				$_REQUEST['query'] = $_REQUEST['q'];
			$query = text_filter($_REQUEST['query'], 100, false);
			
			$managers = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, CONCAT(
				name, \' \', lastname
			) as name, if(image, CONCAT(\'/uploads/images/users/\', id, \'/thumb_\', image), \'/templates/admin/img/nava.png\') as image, CONCAT(email, \'<br>\', phone) as descr FROM `'.DB_PREFIX.'_users` WHERE del = 0 '.(
				$gid ? ' AND FIND_IN_SET('.$gid.', group_ids)' : ''
			).(
				$lId ? ' AND id < '.$lId : ''
			).(
				(!$gid AND $staff) ? ' AND (
					!FIND_IN_SET(5, group_ids)
				)' : ''
			).(
				$nCom ? ' AND company = 0' : ''
			).(
				$query ? ' AND CONCAT(`name`, \' \', `lastname`) LIKE \'%'.$query.'%\'': ''
			).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' '.($_REQUEST['q'] ? '' : 'ORDER BY `image` DESC').' LIMIT 20', true);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $managers,
				'count' => $res_count,
			]));
		break;
		
		/*
		* Is admin
		*/
		case 'is_admin':
			if (in_array(2, explode(',', $user['group_ids'])) OR in_array(1, explode(',', $user['group_ids'])))
				die('OK');
		break;
		
		/*
		* Restore user
		*/
		case 'restore':
			$id = intval($_POST['id']);
			if($user['id'] != $id){
				if($row = db_multi_query('SELECT group_ids FROM `'.DB_PREFIX.'_users` WHERE id = '.$id)){
					if(in_to_array($row['group_ids'], $user['delete_users'])){
						db_query('UPDATE `'.DB_PREFIX.'_users` SET del = 0 WHERE id = '.$id);
						exit('OK');
					} else
						exit('ERR');
				}
			} else
				exit('ERR');
		break;
		
		/*
		* Delete user
		*/
		case 'del':
			$id = intval($_POST['id']);
			if($user['id'] != $id){
				if($row = db_multi_query('SELECT group_ids FROM `'.DB_PREFIX.'_users` WHERE id = '.$id)){
					if(in_to_array($row['group_ids'], $user['delete_users'])){
						db_query('UPDATE `'.DB_PREFIX.'_users` SET del = 1 WHERE id = '.$id);
						exit('OK');
					} else
						exit('ERR');
				}
			} else
				exit('ERR');
		break;
		
		/*
		* Delete note
		*/
		case 'del_note':
			$id = intval($_POST['id']);
			db_query('DELETE FROM `'.DB_PREFIX.'_users_notes` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		break;
		
		/*
		* Send user
		*/
		case 'send':
			is_ajax() or die('hacking');
			$id = intval($_POST['id']);
			$new = intval($_POST['id']);
			$type = $id ? 'edit' : 'add';
			
			//if($id == 16 OR $id == 17)
				//die('Fuck');
			
			// Filters
			$name = trim(text_filter($_POST['name'], 25, false) ?: text_filter($_POST['cname'], 25, false));
			$lastname = trim(text_filter($_POST['lastname'], 25, false));
			$login = text_filter($_POST['login'], 25, false);
			$phone = text_filter($_POST['phone'], 255, false);
			$sms = text_filter($_POST['sms'], 255, false);
			$address = text_filter($_POST['address'], 255, false);
			$country = text_filter($_POST['country'], 255, false);
			$state = text_filter($_POST['state'], 255, false);
			$city = text_filter($_POST['city'], 255, false);
			$zipcode = text_filter($_POST['zipcode'], 255, false);
			$sex = text_filter($_POST['sex'], 20, false);
			$ver = text_filter($_POST['addressConf'], 10, false);
			$email = text_filter($_POST['email'], 50, false);
			$company = intval($_POST['company']);
			$contact = intval($_POST['contact']);
			$pay = floatval($_POST['pay']);
			$bithDate = explode('-', text_filter($_POST['bithDate'], null, false));
			
			$group_ids = ids_filter($_POST['group_id']);

			// If this owner
			if (!in_to_array(1, $user['group_ids']) AND $id AND $user['id'] != $id) {
				$g = db_multi_query('SELECT group_ids FROM `'.DB_PREFIX.'_users` WHERE id = '.$id);
				foreach(explode(',', $g['group_ids']) as $k => $v) {
					if (!in_array($v, $user[$type.'_users'])) {
						echo 'not_allowed';
						die;
					} 
				} 
			}
			
			$not_allow = 0;
			
			// If this owner
			if(!in_to_array(1, $user['group_ids'])){
				foreach(explode(',', $group_ids) as $k => $v){
					if (!in_array($v, $user[$type.'_users'])) {
						$not_allow = 1;
					} 
				}
			} 
				
			if(((!$group_ids AND $user['id'] != $id) OR ($group_ids AND $not_allow))){
				echo 'not_allowed';
				die;
		    }
				
			// Если не переданна группа, или если мы не имеем право редактировать или создавать юзеров данной группы то шлем нахуй
			/* if (!(!$_POST['group_id'] AND $user['id'] == $id)) {
				if((!$group_ids OR !in_to_array($group_ids, $user[$type.'_users']))){
					echo 'not_allowed';
					die;
				}
			} */
			
			// Check
			if (!$company) {
				preg_match("/^[a-zA-Za-яА-ЯїЇіІЄєЎўҐґ'0-9-_\.\s`]{1,255}$/iu", $_POST['name']) or die('Name_not_valid');
				preg_match("/^[a-zA-Za-яА-ЯїЇіІЄєЎўҐґ'0-9-_\.\s`]{1,70}$/iu", $_POST['lastname']) or die('Lastname_not_valid');
			}
			preg_match("/^[0-9-(-+,\s]+$/", $phone) or die('phone_not_valid');
			$e = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\' AND del = 0'.(
				$id ? ' AND id != '.$id : ''
			));
			if(!filter_var($email, FILTER_VALIDATE_EMAIL) OR ($e['count'] > 0 AND $email != 'noreply@yoursite.com')){
				echo 'err_email';
				die;			
			}
			
			if($_POST['steps']){
				
				$password = substr(md5(uniqid()), 0, 6);
				
				// Headers
				$headers  = 'MIME-Version: 1.0'."\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
				$headers .= 'To: '.$email. "\r\n";
				$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

				// Send
				mail($email, 'Welcome to the Your Company', '<!DOCTYPE html>
				<html lang="en">
				<head>
					<meta charset="UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<title>New user on Your Company</title>
				</head>
				<body style="background: #f6f6f6; text-align: center;">
					<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
						<div style="margin: -20px -20px 0; padding: 20px;">
							<a href="http://yoursite.com/">
								<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
							</a>
						</div>
						<div style="padding: 0 30px 30px;">
							<p>You have applied for open beta testing of new platform for <br><b>Your Company inc</b>.</p>
							<p>Please use the details below for entrance to new platform:</p>
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
								Link: <a href="http://yoursite.com/admin/" style="color: #0e92d4;">Click here</a><br>
								Login: '.$email.'<br>
								Password: '.$password.'
							</div>
						</div>
					</div>
				</body>
				</html>', $headers);
				
			} else
				$password = text_filter($_POST['password'], 32, false);
			
			if($login){
				$logn = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE login = \''.$login.'\''.(
					$id ? ' AND id != '.$id : ''
				));
				
				if($logn['count'] > 0){
					echo 'err_login';
					die;
				}
			}
			
			$sql = '';
			if($_POST['del_image']) $sql .= ', image = \'\'';
			
			// Если меняется пароль, то проверяем привелегии на смену пароля
			if($password AND (
				$user['password_users'] OR $id == $user['id']
				)
			){
				$hid = md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time());
				$sql .= ', hid = \''.$hid.'\', password = \''.md5(md5($_POST['password'])).'\'';
				if($id == $user['id']){
					$_SESSION['uid'][1] = $hid;
					setcookie('hid', $hid, time()+(3600*24*7), '/', $_SERVER['HTTP_HOST'], null, true);
				}
			}

			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_users` SET
					name = \''.$name.'\',
					lastname = \''.$lastname.'\',
					login = \''.$login.'\',
					sex = \''.$sex.'\',
					birthday = \''.intval($bithDate[2]).'.'.intval($bithDate[0]).'.'.intval($bithDate[1]).'\',
					company = \''.$company.'\',
					phone = \''.$phone.'\',
					sms = \''.$sms.'\',
					address = \''.$address.'\',
					country = \''.$country.'\',
					state = \''.$state.'\',
					city = \''.$city.'\',
					zipcode = \''.$zipcode.'\',
					ver = \''.$ver.'\',
					referral = \''.intval($_POST['referral']).'\',
					contact = \''.$contact.'\',
					'.(!isset($_POST['google_auth']) ? 'google_id = \'\',' : '').'
					'.($pay ? ' pay = \''.$pay.'\',' : '').'
					email = \''.$email.'\''.$sql.' 
					'.($group_ids ? ', group_ids = \''.$group_ids.'\'' : '').(
				$id ? ' WHERE id = '.$id : ''
			));
			
			$id = $id ? $id : intval(mysqli_insert_id($db_link));
			
			// Is file upload
			if($_FILES AND ( // Если меняется аватарка, проверяем привелегии на смену аватарки
				$user['edit_photo'] OR $id == $user['id']
				)
			){
				
				// Upload max file size
				$max_size = 10;
				
				// path
				$dir = ROOT_DIR.'/uploads/images/users/';
				
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
					
					// 300x300
					$img->cropThumbnailImage(300, 300);
					$img->writeImage($dir.'preview_'.$rename);
					
					// 94x94
					$img->cropThumbnailImage(94, 94);
					$img->writeImage($dir.'thumb_'.$rename);
					$img->destroy();
					
					db_query('UPDATE `'.DB_PREFIX.'_users` SET image = \''.$rename.'\' WHERE id = '.$id);
				}
			}
			
			
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
				
				$points = floatval($config['user_points']['new_user']['points']);
				
				//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'new_user\',
						object_id = '.$user['store_id'].',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						user_id = '.$id.',
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
						action = \'new_user\',
						min_rate = '.$sql_['points'].',
						object_id = '.$user['store_id'].',
						point = \''.$points.'\',
						user_id = '.$id.',
						rate_point = 1'
					);	
				} */
				
				if ($points < 0) {
					if ($wt = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_timer` WHERE DATE(date) = \''.date('Y-m-d', time()).'\' AND user_id = '.$id)) {
						db_query('UPDATE `'.DB_PREFIX.'_timer` SET seconds = seconds + '.($points * $config['min_forfeit'] * 60).' WHERE id = '.$wt['id']);
						db_query('INSERT INTO `'.DB_PREFIX.'_users_time_forfeit` SET user_id = '.$id.', forfeit = '.(floatval($points) * $config['min_forfeit']*60));
					}
				}
			}
			if (!$new) {
				db_query('
					INSERT INTO 
					`'.DB_PREFIX.'_activity` SET 
						user_id = \''.$user['id'].'\', 
						event = \'new user\',
						event_id = '.$id.',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						object_id = '.$user['store_id'].'
				');
			}
			// ------------------------------------------------------------------------------- //
			
			echo $id;
			die;
		break;
		
		/*
		* Time statistic
		*/
		case 'time':
			$meta['title'] = 'Working time';
			//die;
			$id = intval($route[2]);
			if(in_to_array('1,2', $user['group_ids']) OR $user['id'] == $id){				
				$query = text_filter($_POST['query'], 255, false);
				$object = intval($_POST['object']);
				$date_start = text_filter($_POST['date_start'], 30, true);
				$date_finish = text_filter($_POST['date_finish'], 30, true);
				$page = intval($_POST['page']);
				$count = 20;
				$full_total = 0;
				
				if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
						t.id,
						t.user_id,
						DATE(t.date) as date,
						TIME(t.date) as start_time,
						TIME(t.control_point) as end_time,
						TIME(t.break_start) as break_start,
						TIME(t.break_finish) as break_end,
						SEC_TO_TIME(t.seconds) as seconds,
						t.seconds as total,
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
					WHERE t.event = \'stop\' AND t.user_id = '.$id.(
					$object ? ' AND o.id = \''.$object.'\'' : ''
				).(
					$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
				).(
					($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND t.date >= CAST(\''.date("Y-m-d", strtotime("- 7 days", time())).' 23:59:59\' AS DATETIME)'
				).' ORDER BY t.date
				DESC LIMIT '.($page*$count).', '.$count, true)){
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					/* if ($page == 0) {
						$total_time = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
								SUM(t.seconds) as total
							FROM `'.DB_PREFIX.'_timer` t
							INNER JOIN `'.DB_PREFIX.'_users` u
								ON t.user_id = u.id 	
							LEFT JOIN `'.DB_PREFIX.'_objects` o
								ON o.id = t.object_id	
							WHERE t.event = \'stop\' AND t.user_id = '.$id.(
							$object ? ' AND o.id = \''.$object.'\'' : ''
						).(
							$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
						).(
							($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND t.date >= CAST(\''.date("Y-m-d", strtotime("- 7 days", time())).' 23:59:59\' AS DATETIME)'
						));
					} */
				
					$timers = '';
					$total = 0;
					$date = 0;
					$i = 0;
					foreach($sql as $row){
						if ($date != $row['date']) {
							$s = $total % 60;
							$m = ($total % 3600 - $s) / 60;
							$h = ($total - $s - $m * 60) / 3600;
							if ($date) {
								$timers .= '<div class="tr">
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									<div class="td"></div>
									<div class="td">'.$h.':'.$m.':'.$s.'</div>
									'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="td">$'.number_format($total/3600*$row['pay'], 2, '.', '').($row['salary'] ? '/$'.number_format($total/3600*$row['pay']*((100 + $row['salary'])/100), 2, '.', '') : '').'</div>' : '').'
								</div>
								</div>
								<div class="tbl tArea">
									<div class="tr">
										<div class="th wp20">
											<a href="/users/view/'.$id.'" target="_blank">
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
										<div class="th">Working time</div>
										'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="th">Salary</div>' : '').'
									</div>';
							} else {
								$timers .= '<div class="tbl tArea">
									<div class="tr">
										<div class="th wp20">
											<a href="/users/view/'.$id.'" target="_blank">
												'.(
													$sql[0]['image'] ?
														'<img src="/uploads/images/users/'.$sql[0]['user_id'].'/thumb_'.$sql[0]['image'].'" class="miniRound">' :
													'<span class="fa fa-user-secret miniRound"></span>'
												).'
												'.$sql[0]['name'].' '.$sql[0]['lastname'].'
											</a>
										</div>
										<div class="th">Date</div>
										<div class="th">Punch in</div>
										<div class="th">Break start</div>
										<div class="th">Break end</div>
										<div class="th">Punch out</div>
										<div class="th">Working time</div>
										'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="th">Salary</div>' : '').'
									</div>';
							}
							$total = 0;
							$date = $row['date'];
						}
						$timers .= '<div class="tr">
							<div class="td">'.$row['object'].'</div>
							<div class="td">'.$row['date'].'</div>
							<div class="td">'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<span onclick="timer.getCreate(this, \''.$row['start_time'].'\', '.$row['id'].', \'start\')">'.$row['start_time'].'</span>' : $row['start_time']).'</div>
							<div class="td">'.$row['break_start'].'</div>
							<div class="td">'.$row['break_end'].'</div>
							<div class="td">'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<span onclick="timer.getCreate(this, \''.$row['end_time'].'\', '.$row['id'].', \'end\')">'.$row['end_time'].'</span>' : $row['end_time']).'</div>
							<div class="td">'.$row['seconds'].'</div>
							'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="td">$'.number_format($row['total']/3600*$row['pay'], 2, '.', '').($row['salary'] ? '/$'.number_format($row['total']/3600*$row['pay']*((100 + $row['salary'])/100), 2, '.', '') : '').'</div>' : '').'
						</div>';
						$total += $row['total'];
						$i++;
						$full_total += $row['total'];
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
						<div class="td">'.$h.':'.$m.':'.$s.'</div>
						'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<div class="td">$'.number_format($total/3600*$sql[0]['pay'], 2, '.', '').($sql[0]['salary'] ? '/$'.number_format($total/3600*$sql[0]['pay']*((100 + $sql[0]['salary'])/100), 2, '.', '') : '').'</div>' : '').'
					</div>';
				}
				//$timers .= '</div>';
				
				$left_count = intval(($res_count-($page*$count)-$i));

				//$total = $total_time['total'];
				$total = $full_total;
				$s = $total % 60;
				$m = ($total % 3600 - $s) / 60;
				$h = ($total - $s - $m * 60) / 3600;
							
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'more' => $left_count > 0 ? '' : ' hdn',
						'content' =>  $timers ?: $tpl_content['timers'],
						'total' => $h.':'.$m.':'.$s
					]));
				}
				tpl_set('users/time', [
					'uid' => $user['id'],
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'timers' => $timers ?: $tpl_content['timers'],
					'total_time' => $h.':'.$m.':'.$s
				], [
					'user_new' => (in_to_array('1,2', $user['group_ids']))
				], 'content');
			} else {
				tpl_set('forbidden', [
					'text' => $lang['Forbidden'],
				], [], 'content');	
			}
		break;
		
		/*
		*  Send note
		*/
		case 'send_note': 
			is_ajax() or die('Hacking attempt!');
			
			// SQL SET
			db_query('INSERT INTO
			 `'.DB_PREFIX.'_users_notes` SET
					user_id = '.intval($_POST['id']).',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					staff_id = '.$user['id'].',
					comment = \''.text_filter($_POST['note']).'\''
			);
			die('OK');
		break;
		
		/*
		* Devices
		*/
		case 'devices':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;
			$i = 0;
			
			if($devices = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					tb1.id, tb1.model,
					tb1.tradein, tb1.trade_in_id,
					tb1.old_customer_id,
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
				WHERE tb1.del = 0 AND tb1.accessories = 0 AND (tb1.customer_id = '.$id.' OR tb1.old_customer_id = '.$id.')
				ORDER BY tb1.id DESC
				LIMIT '.($page*$count).', '.$count, true
			)){
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				if(!$item['tradein'] OR ($item['tradein'] && $item['old_customer_id'] != $id)){
					$sql = 'SELECT 
						i.*, 
						u.name as staff_name, 
						u.lastname 
					FROM `'.DB_PREFIX.'_issues` i 
					LEFT JOIN `'.DB_PREFIX.'_users` u 
						ON i.staff_id = u.id 
					WHERE i.customer_id = '.$id.' AND i.inventory_id IN ('.implode(',', array_column($devices, 'id')).')';
					$issues = db_multi_query($sql
					, true);
				}
				foreach($devices as $item){
					if(!$item['tradein'] OR ($item['tradein'] && $item['old_customer_id'] != $id)){
						if ($i_issues = array_filter($issues, function($a) use(&$item) {
							if ($a['inventory_id'] == $item['id'])
								return $a;
						})) {
							foreach($i_issues as $issue){
								tpl_set('/cicle/invIssue', [
									'id' => $issue['id'],
									'staff-id' => $issue['staff_id'],
									'staff-name' => $issue['staff_name'],
									'staff-lastname' => $issue['lastname'],
									'description' => $issue['description'],
									'date' => $issue['date']
								], [], 'issues');
							}
						}	
					}
					tpl_set('/cicle/inventory', [
						'id' => $item['id'],
						'user-id' => $id,
						'invoice-id' => $item['trade_in_id'],
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
						'trade-in' => ($item['tradein'] && $item['old_customer_id'] == $id),
						'user' => $route[0] == 'users',
						'has_issue' => $tpl_content['issues'],
					], 'devices');
					unset($tpl_content['issues']);
					$i++;
				}
			}
			
			$left_count = intval(($res_count-($page*$count)-$i));
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['devices'],
			]));
		break;
		
		/*
		* Invoices
		*/
		case 'invoices':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;
			$i = 0;
			
			if ($invoices = db_multi_query(
				'SELECT SQL_CALC_FOUND_ROWS 
					id, 
					date, 
					total, 
					paid, 
					conducted, 
					currency 
				FROM `'.DB_PREFIX.'_invoices` 
				WHERE customer_id = '.$id.'
				ORDER BY id DESC
				LIMIT '.($page*$count).', '.$count
			, true)) {
				foreach($invoices as $invoice){
					$due = $invoice['total'] - $invoice['paid'];
					tpl_set('/cicle/usInvoice', [
						'id' => $invoice['id'],
						'date' => $invoice['date'],
						'total' => $config['currency'][$invoice['currency']]['symbol'].number_format($invoice['total'], 2, '.', ''),
						'paid' => $config['currency'][$invoice['currency']]['symbol'].number_format($invoice['paid'], 2, '.', ''),
						'due' => $config['currency'][$invoice['currency']]['symbol'].number_format((abs($due) < 0.01 ? 0 : $due), 2, '.', ''),
						'status' => $invoice['conducted'] ? 'Paid' : 'Unpaid',
						'date' => $invoice['date']
					], [
						'edit-invoce' => ($invoice['conducted'] ? $user['edit_paid_invoices'] : $user['edit_invoices'])
					], 'invoices');
					$i++;
				}
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			$left_count = intval(($res_count-($page*$count)-$i));
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['invoices'],
			]));
		break;
		
		/*
		* Purchases
		*/
		case 'purchases':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;
			$i = 0;
			
			if ($purchases = db_multi_query(
				'SELECT 
					p.id as pur_id,
					p.name as pur_name,
					p.sale_name as sale_name,
					p.link as pur_link,
					p.status as pur_status,
					p.sale as pur_price,
					p.currency
				FROM `'.DB_PREFIX.'_purchases` p
				WHERE p.del = 0 AND p.customer_id = '.$id.'
				LIMIT '.($page*$count).', '.$count
			, true)) {
				foreach($purchases as $purchase){
					tpl_set('/cicle/usPurchases', [
						'id' => $purchase['pur_id'],
						'name' => $purchase['sale_name'] ?: $purchase['pur_name'],
						'status' => $purchase['pur_status'],
						'link' => '<a href="'.$purchase['pur_link'].'" target="_blank"><span class="fa fa-external-link"></span> View</a>',
						'price' => number_format($purchase['pur_price'], 2, '.', ''),
						'user-id' => $id,
						'currency' => $config['currency'][$purchase['currency']]['symbol']
					], [], 'purchases');
					$i++;
				}
			}
					
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			$left_count = intval(($res_count-($page*$count)-$i));
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['purchases'],
			]));
		break;
		
		/*
		* Notes
		*/
		case 'notes':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;
			$i = 0;
			
			if ($notes = db_multi_query(
				'SELECT SQL_CALC_FOUND_ROWS
					n.*,
					u.name,
					u.lastname
				FROM `'.DB_PREFIX.'_users_notes` n
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = n.staff_id
				WHERE user_id = '.$id.'
				LIMIT '.($page*$count).', '.$count
			, true)) {
				foreach($notes as $note){
					tpl_set('/cicle/usNotes', [
						'id' => $note['id'],
						'date' => $note['date'],
						'note' => $note['comment'],
						'staff-id' => $note['staff-id'],
						'staff-name' => $note['name'],
						'staff-lastname' => $note['lastname']
					], [], 'notes');
					$i++;
				}
			}
					
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			$left_count = intval(($res_count-($page*$count)-$i));
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['notes'],
			]));
		break;
		
		/*
		* Appointments
		*/
		case 'appointments':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;
			$i = 0;
			
			if ($appointments = db_multi_query(
				'SELECT SQL_CALC_FOUND_ROWS
					a.*,
					o.name,
					CONCAT(u.name, \' \', u.lastname) as staff_name
				FROM `'.DB_PREFIX.'_users_appointments` a
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = a.object_id
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = a.staff_id
				WHERE a.customer_id = '.$id.'
				ORDER BY id DESC'
			, true)) {
				foreach($appointments as $app){
					tpl_set('/cicle/usApp', [
						'id' => $app['id'],
						'date' => $app['date'],
						'object' => $app['name'],
						'staff_name' => $app['staff_name'],
						'note' => $app['note']
					], [], 'appointments');
					$i++;
				}
			}
					
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			$left_count = intval(($res_count-($page*$count)-$i));
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['appointments'],
			]));
		break;
		
		/*
		* Onsite services
		*/
		case 'onsite_services':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$page = intval($_POST['page']);
			$count = 10;
			$i = 0;
			
			if ($onsite = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					o.*,
					SEC_TO_TIME(TIMESTAMPDIFF(SECOND, \''.date('Y-m-d H:i:s', time()).'\', o.date_end)) as date_left,
					s.name as service_name,
					s.type,
					s.calls as service_calls,
					s.time as service_time,
					s.currency,
					i.id as add_invoice,
					u.name as uname,
					u.lastname as ulastname,
					DATE_FORMAT(o.selected_time, \'%H:%i\') as time_service,
					inv.id as invoice_id
				FROM `'.DB_PREFIX.'_users_onsite` o
				LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` s
					ON s.id = o.onsite_id
				LEFT JOIN `'.DB_PREFIX.'_invoices` i
					ON o.id = i.add_onsite
				LEFT JOIN `'.DB_PREFIX.'_invoices` inv
					ON inv.id = o.invoice_id
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = o.selected_staff_id
				WHERE o.confirmed = 1 AND o.customer_id = '.$id.'
				ORDER BY id DESC
				LIMIT '.($page*$count).', '.$count, true
			)) {
				foreach($onsite as $os) {
					$date = explode('-', $os['date_end']);
					$time = abs($os['left_time']);
					$s = $time % 60;
					$m = ($time % 3600 - $s) / 60;
					$h = ($time % 86400 - $s - $m * 60) / 3600;
					$d = ($time % (86400 * 356) - $s - $m * 60 - $h * 3600) / 86400;
					tpl_set('/users/onsite', [
						'id' => $os['id'],
						'onsite_id' => $os['onsite_id'],
						'name' => $os['service_name'],
						'time_left' => $os['service_time'] > 0 ? (($os['left_time'] < 0 ? '-' : '').($d ? $d.' days ' : '').$h.':'.$m.':'.$s) : '<span class="inf">&infin;</span>',
						'calls' => $os['service_calls'] > 0 ? $os['calls'] : '<span class="inf">&infin;</span>',
						//'date_left' => intval($date[0]) ? (intval($os['date_left']) >= 0 ? $os['date_left'] : 0) : '<span class="inf">&infin;</span>',
						'date_start' => strtotime($os['date_start']) > 0 ? $os['date_start'] : '<span class="inf">&infin;</span>',
						'date_end' => strtotime($os['date_end']) > 0 ? $os['date_end'] : '<span class="inf">&infin;</span>',
						'invoice-id' => $os['invoice_id'],
						'add_invoice' => $os['add_invoice'],
						'has-calls' => ($os['service_calls'] > 0 AND $os['calls'] > 0) ? 1 : 0,
						'has-time' => ($os['service_time'] > 0 AND $os['left_time'] > 0) ? 1 : 0,
						'selected-staff' => $os['uname'].' '.$os['ulastname'],
						'time' => $os['selected_time'],
						'hnt' => 'Assign to: '.$os['uname'].' '.$os['ulastname']."\r\n".($os['time_service'] == '00:00' ? '' : $os['time_service'])
					], [
						'use' => (intval($date[0]) ? (($os['date_left'] > 0 ? 1 : 0) AND ($os['left_time'] > 0 OR ($os['calls'] == 0 ? 0 : 1))) : (($os['left_time'] > 0 ? 1 : 0) OR ($os['calls'] == 0 ? 0 : 1))),
						'play' => $os['last_event'] == 'play',
						'cr_invoice' => ($os['left_time'] < 0 AND $os['type'] != 'call'),
						'invoice' => $os['add_invoice'] > 0,
						'invoice-id' => $os['invoice_id'],
						'del' => $os['del'] == 1,
						'hnt' => ($os['uname'] AND $os['ulastname']) OR $os['time_service'] != '00:00'
					], 'onsite');
					$i++;
				}
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			$left_count = intval(($res_count-($page*$count)-$i));
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['onsite'],
			]));
		break;
		
		case 'seo_invite':
			$id = (int)$_POST['id'];
			if($u = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users` WHERE id = '.$id)){
				
				$password = substr(md5(uniqid()), 0, 6);

				db_query('UPDATE `'.DB_PREFIX.'_users` SET
					hid = \''.md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time()).'\',
					password = \''.md5(md5($password)).'\' WHERE id = \''.$id.'\''
				);
				
				// Headers
				$headers  = 'MIME-Version: 1.0'."\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
				$headers .= 'To: '.$u['email']. "\r\n";
				$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
				
				$message = '<!DOCTYPE html>
				<html lang="en">
				<head>
					<meta charset="UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<title>New SEO Master on Your Company</title>
				</head>
				<body style="background: #f6f6f6; text-align: center;">
					<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
						<div style="margin: -20px -20px 0; padding: 20px;">
							<a href="http://yoursite.com/">
								<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
							</a>
						</div>
						<div style="padding: 0 30px 30px;">
							<p>You are successfully registered in SEO Standing from <br><b>Your Company inc</b>.</p>
							<p>Please use the details below for entrance to SEO panel:</p>
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
								Link: <a href="http://seo.yoursite.com" style="color: #0e92d4;">Click here</a><br>
								Login: '.$u['email'].'<br>
								Password: '.$password.'
							</div>
						</div>
					</div>
				</body>
				</html>';

				// Send
				mail($email, 'Welcome to the SEO Standing from Your Company', $message, $headers);
			}
			die;
		break;
		
		/*
		* View
		*/
		case 'view':
		
			$id = intval($route[2]);
			
			$u = db_multi_query('
				SELECT 
					u.*,
					u.points as user_points,
					c.name as country_name,
					s.name as state_name,
					ct.city as city_name
				FROM `'.DB_PREFIX.'_users` u
				LEFT JOIN `'.DB_PREFIX.'_countries` c
					ON u.country = c.code
				LEFT JOIN `'.DB_PREFIX.'_states` s
					ON u.state = s.code
				LEFT JOIN `'.DB_PREFIX.'_cities` ct
					ON u.zipcode = ct.zip_code
				WHERE u.id = '.$id);
				if($u['id']) {
					$dbl = '';
					if ($dublicates = db_multi_query('
						SELECT 
							id, 
							name, 
							lastname, 
							email, 
							phone,
							image							
						FROM `'.DB_PREFIX.'_users` 
						WHERE del = 0 AND ('.($u['email'] ? ' email = \''.db_escape_string($u['email']).'\' OR ' : '').'
						CONCAT(name, \' \', lastname) LIKE \''.db_escape_string($u['name']).' '.db_escape_string($u['lastname']).'%\'
						'.($u['phone'] ? 'OR phone = \''.db_escape_string($u['phone']).'\'' : '').') AND id != '.$u['id'].' AND email != \'noreply@yoursite.com\'
					', true)) {
						foreach($dublicates as $dubl) {
							$dbl .= '<div class="user-dbl">
								<div class="flRight"><button class="btn btnSubmit" onclick="user.concat_id('.$dubl['id'].', '.$id.', this);">Merge</button></div>
								'.($dubl['image'] ? '<img src="/uploads/images/users/'.$dubl['id'].'/'.$dubl['image'].'" class="miniRound">' : '<span class="fa fa-user-secret miniRound"></span>').'
								<b>#'.$dubl['id'].'</b> 
								<a href="/users/view/'.$dubl['id'].'" onclick="Page.get(this.href); return false;">
									'.$dubl['name'].' '.$dubl['lastname'].'
								</a><br>
								<b>'.$dubl['email'].'</b> <i>('.$dubl['phone'].')</i>
							</div>';
						}
					}
					
					$forms = '';
					foreach(db_multi_query('
						SELECT id, name FROM `'.DB_PREFIX.'_forms`
						WHERE FIND_IN_SET(\'user\', types) ORDER BY id LIMIT 50'
					, true) as $form){
						$forms .= '<li><a href="javascript:to_print(\'/forms?type=user&id='.$form['id'].'&user_id='.$id.'\', \'user №'.$id.'\');" target="_blank">'.$form['name'].'</a></li>';
					}
					
					$time = db_multi_query('
						SELECT 
							SUM(seconds) as seconds
						FROM `'.DB_PREFIX.'_timer` u
						WHERE user_id = '.$id);
					$time = $time['seconds'];
					$s = $time % 60;
					$m = ($time % 3600 - $s) / 60;
					$h = ($time % 86400 - $s - $m * 60) / 3600;
					$d = ($time % (86400 * 356) - $s - $m * 60 - $h * 3600) / 86400;
					$y = ($time - $s - $m * 60 - $h * 3600 - $d * 86400) / 86400 / 356;
				
					$nvphone = 0;
					$phones = '';
					
					foreach(explode(',', $u['phone']) as $ph) {
						if (strlen(preg_replace("/\D/", '', $ph)) < 10){
							$nvphone = 1;
						} else
							$phones .= '<a href="tel:'.$ph.'">'.$ph.'</a>';
					}
					
					$points_feedback = db_multi_query('
						SELECT 
							SUM(ratting) as total,
							COUNT(id) as count
						FROM `'.DB_PREFIX.'_feedback`
						WHERE staff_id = '.$id
					);
					
					$groups = [];
					$rating = 0;
					$seo_master = false;
					if ($priv = db_multi_query('SELECT rating, seo_master, name FROM `'.DB_PREFIX.'_groups` WHERE group_id IN ('.$u['group_ids'].')', true)) {
						foreach($priv as $p) {
							if ($p['rating'])
								$rating = 1;
							if($p['seo_master'])
								$seo_master = true;
							//break;
							$groups[] = $p['name'];
						}
					}
					
					$appointment = db_multi_query('
						SELECT 
							a.id,
							a.date,
							o.name
						FROM `'.DB_PREFIX.'_users_appointments` a
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = a.object_id
						WHERE customer_id = '.$id.' AND confirmed = 0
						ORDER BY id DESC');
						
					tpl_set('users/view', [
						'id' => $id,
						'dbl' => $dbl,
						'ip' => long2ip($u['ip']),
						'points' => number_format($u['user_points'], 2, '.', ''),
						'time' => ($y ? $y.' years, ' : '').($d ? $d.' days ' : '' ).$h.':'.$m,
						'address' => $u['address'] ? preg_replace(
							"/\n/", "<br>", $u['address']
						).'<br>' : '',
						'country' => $u['country_name'],
						'state' => $u['state_name'] ? $u['state_name'].'<br>' : '',
						'city' => $u['city_name'],
						'zipcode' => $u['zipcode'] ?: '',
						'ver' => $u['ver'],
						'email' => $u['email'],
						'name' => $u['name'],
						'phone' => $phones,
						'lastname' => $u['lastname'],
						'ava' => $u['image'],
						'reg-date' => $u['reg_date'],
						'last-date' => $u['last_visit'],
						'group' => implode(', ', $groups),
						'group-id' => '/'.min(explode(',', $u['group_ids'])),
						'devices' => $tpl_content['devices'] ?: '<div class="noContent">No devices</div>',
						'invoices' => $tpl_content['invoices'] ?: '<div class="noContent">No invoices</div>',
						'forms-list' => $forms,
						'onsite' => $tpl_content['onsite'] ?: '<div class="noContent">No on site services</div>',
						'purchases' => $tpl_content['purchases'] ?: '<div class="noContent">No on site purchases</div>',
						'notes' => $tpl_content['notes'] ?: '<div class="noContent">'.$lang['NoNotes'].'</div>',
						'appointments' => $tpl_content['appointments'] ?: '<div class="noContent">No appointments</div>',
						'feedback' => number_format(($points_feedback['count'] ? ($points_feedback['total'] / $points_feedback['count']) : 0), 2, '.', ''),
						'appointment' => $appointment['id'],
						'appointment_date' => $appointment['date'],
						'appointment_store' => $appointment['name'],
					], [
						'dbl' => $dbl,
						'seo-master' => $seo_master,
						'deleted' => $u['del'],
						'time-show' => in_to_array('1,2', $user['group_ids']) OR $u['id'] == $user['id'],
						'add' => $user['add_users'],
						'suspention' => $user['make_suspention'],
						'ava' => $u['image'],
						'ver' => $u['ver'],
						'ncustomer' => !in_array(5, explode(',', $u['group_ids'])),
						'page-owner' => $u['id'] == $user['id'],
						'user-auth' => in_to_array(1, $user['group_ids']),
						'user_new' => (in_to_array('1,2', $user['group_ids'])),
						'devices' => $tpl_content['devices'],
						'invoices' => $tpl_content['invoices'],
						'onsite' => $tpl_content['onsite'],
						'purchases' => $tpl_content['purchases'],
						'ip' => $u['ip'],
						'is-notes' => $tpl_content['notes'],
						'appointments' => $tpl_content['appointments'],
						'not-valid-phone' => ($nvphone == 1 OR $u['incphone']),
						'rating' => $rating,
						'appointment' => $appointment
					], 'content');
					$meta['title'] = $u['name'];
				} else {
					tpl_set('/forbidden', [
						'text' => 'User page is not created'
					], [], 'content');
				}
		break;
		
		/*
		* Add/edit user
		*/
		case 'add':
		case 'edit':
		case 'step':
		case 'appointment':
			$id = intval($route[2]);
			if ($route[2] === 'company') $company = 1;
			if(($id AND $user['edit_users']) OR $user['add_users']){
				$u = [];
				$g = [];
				$gIds = [];
				$type = $route[1] == 'edit' ? $lang['Edit'] : $lang['Add'];
				$meta['title'] = $type.' '.$lang['user'];
				
				if($route[1] == 'add' AND $id){
					$g = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_groups` WHERE group_id = '.$id);
				}
				
				// Is edit
				if($route[1] != 'add' OR !$id){
					$privileges = in_to_array($user['group_ids'], [1]) ? false : implode(',', $user[mb_strtolower($type).'_users']);
					if($id) {
						if (!$u = db_multi_query('
							SELECT 
							u.*,
							c.name as contact_name,
							c.lastname as contact_lastname,
							g.pay as group_pay
						FROM `'.DB_PREFIX.'_users` u
						LEFT JOIN `'.DB_PREFIX.'_users` c
							ON c.id = u.contact
						LEFT JOIN `'.DB_PREFIX.'_groups` g
							ON FIND_IN_SET(g.group_id, u.group_ids) AND g.pay = 1
						WHERE u.id = '.$id
						)) {
							die(tpl_set('/forbidden', [
								'text' => 'User page is not created'
							], []));
						}
					}
					$groups = db_multi_query('SELECT `group_id`, `name` FROM `'.DB_PREFIX.'_groups`'.(
						$privileges ? ' WHERE group_id IN('.trim($privileges, ',').')' : ''
					).' ORDER BY `group_id`', true);
					$options = '';
					$groupsName = '';
					foreach($groups as $group){
						$options .= '<option value="'.$group['group_id'].'"'.(
							in_array($group['group_id'], explode(',', $u['group_ids'])) ? ' selected' : ''
						).'>'.$group['name'].'</option>';
						$groupsName .= (in_array($group['group_id'], explode(',', $u['group_ids'])) ? $group['name'].', ' : '');
					}
					
					$phones = '';
					$i = 0;
					foreach(explode(',', $u['phone']) as $ph) {
                        $n = explode(' ', $ph);
                        $phones .= '<div class="sPhone">
                                    <span class="fa fa-times rd'.($i == 0 ? ' hide' : '').'" onclick="'.($i == 0 ? '' : '$(this).parent().remove();').'"></span>
                                    <select name="phoneCode">
                                        <option value="+1"'.(($n[0] == '+1' OR !$n[0]) ? ' selected' : '').'>+1</option>
                                        <option value="+3"'.($n[0] == '+3' ? ' selected' : '').'>+3</option>
                                    </select>
                                    <span class="wr">(</span>
                                    <input type="number" name="code" onkeyup="phones.next(this, 3);" value="'.$n[1].'" max="">
                                    <span class="wr">)</span>
                                    <input type="number" name="part1" onkeyup="phones.next(this, 7);" value="'.$n[2].'">
                                    <input type="number" name="part2" value="'.$n[3].'">
                                    <input type="checkbox" name="sms" onchange="phones.onePhone(this);" '.(trim($ph) === $u['sms'] ? ' checked' : '').'>
                                </div>';
						$i ++;
                    }
				}
				
					$select_referral = '';
					foreach(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_referrals` ORDER BY id', true) as $ref){
						$select_referral .= '<option value="'.$ref['id'].'" '.($u['referral'] == $ref['id'] ? 'selected' : '').'>'.$ref['name'].'</option>';
					}
					
					$birthday = explode('.', $u['birthday']);
					
					$not_allow = 0;
					if ($user['id'] != $id) {
						foreach(explode(',', $u['group_ids']) as $k => $v) {
							if (!in_array($v, $user['edit_users'])) {
								$not_allow = 1;
							} 
						} 
					}
					
					$google_url = 'https://accounts.google.com/o/oauth2/auth';

					$google_params = [
						'redirect_uri'  => 'https://crm.yoursite.com?login=true',
						'response_type' => 'code',
						'client_id'     => '1040991714737-pgb2p5765fo2ahk34ui9jrog33cogetl.apps.googleusercontent.com',
						'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
					];
			
					if ($id AND $route[1] == 'edit' AND $not_allow AND $user['id'] != $id) {
						tpl_set('/forbidden', [
							'text' => 'You have no permission to edit this user'
						], [], 'content');
					} else {
					// Output to browser
						tpl_set('users/'.(
							in_array($route[1], ['step', 'appointment']) ? 'step' : 'form'
						), [
							'id' => (($route[1] == 'add' AND $id) ? 0 : $id),
							'options' => $options,
							'google-lnk' => $google_url.'?'.urldecode(http_build_query($google_params)),
							'title' => $meta['title'],
							'day' => $birthday[2],
							'month' => $birthday[1],
							'year' => $birthday[0],
							'sex' => $u['sex'],
							'company' => ($u['company'] ?: 0),
							'cname' => $u['company'] ? $u['name'] : '',
							'login' => $u['login'],
							'address' => $u['address'],
							'country' => $u['country'],
							'state' => $u['state'],
							'zipcode' => $u['zipcode'] ?: 0,
							'zip-input' => $u['zipcode'] ?: 0,
							'google-auth' => $u['google_id'] ? ' checked' : '',
							'ver' => $u['ver'],
							'pay' => $u['pay'],
							'send' => ($id ? 'edit' : 'add'),
							'email' => $u['email'],
							'name' => $u['name'],
							'phone' => $phones,
							'referrals' => $select_referral,
							'lastname' => $u['lastname'],
							'ava' => $u['image'],
							'group' => ($g['name'] AND !$company) ? $g['name'] : ($company ? 'company' : $lang['user']),
							'group-id' => '/'.(!$company ? min(explode(',', $u['group_ids'])) : 'company'),
							'gid' => $id,
							'group-name' => substr($groupsName,0,-2),
							'group-ids' => $u['group_ids'],
							'contact' => ($u['contact'] ? json_encode([$u['contact'] => [
								'name' => $u['contact_name'].' '.$u['contact_lastname'] 
							]], true) : 0),
							'link' => $route[1]
						], [
							'delete' => $user['delete_users'] AND $user['id'] != $u['id'],
							'owner' => $user['id'] == $u['id'],
							'deleted' => $u['del'],
							'multi-group' => $options,
							'ava' => $u['image'],
							'photo' => ($user['edit_photo'] OR $id == $user['id']),
							'address' => $user['address_users'],
							'view-address' => $user['address_users'] == 1,
							'add' => ($user['add_users'] OR $id == $user['id']),
							'email' => ($user['email_users'] OR $id == $user['id']),
							'view-email' => $user['email_users'] == 1,
							'name' => ($user['name_users'] OR $id == $user['id']),
							'view-name' => $user['name_users'] == 1,
							'phone' => ($user['phone_users'] OR $id == $user['id']),
							'view-phone' => $user['phone_users'] == 1,
							'password' => ($user['password_users'] OR $id == $user['id']),
							'ver' => $u['ver'],
							'edit' => $route[1] == 'edit',
							'pay' => $u['group_pay'],
							'pay-edit' => in_array(1, explode(',', $user['group_ids'])) OR in_array(1, explode(',', $user['group_ids'])),
							'user_new' => (in_to_array('1,2', $user['group_ids'])),
							'company' => ($route[2] == 'company' OR $u['company']),
							'steps' => ($route[1] == 'step'),
							'appointment' => ($route[1] == 'appointment')
						], 'content');	
					}
			} else {
				tpl_set('/forbidden', [
					'text' => $lang['Forbidden']
				], [], 'content');
			}
		break;
		
		/* 
		* Delivery
		*/
		case 'sendDetails':
			is_ajax() or die('hacking');
			$users = explode(PHP_EOL, $_POST['data']);

			foreach($users as $email){
				
				$password = substr(md5(uniqid()), 0, 6);
				
				$name = explode('@', $email);
				
				$sql = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\'');

				if(!$sql['count']){
					db_query('INSERT INTO `'.DB_PREFIX.'_users` SET
						hid = \''.md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time()).'\',
						password = \''.md5(md5($password)).'\',
						name = \''.$name[0].'\', 
						email = \''.$email.'\', 
						group_ids = 2'
					);	
				} else {
					db_query('UPDATE `'.DB_PREFIX.'_users` SET
						hid = \''.md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time()).'\',
						password = \''.md5(md5($password)).'\' WHERE email = \''.$email.'\''
					);
				}
				
				// Headers
				$headers  = 'MIME-Version: 1.0'."\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
				$headers .= 'To: '.$email. "\r\n";
				$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
				
				$message = '<!DOCTYPE html>
				<html lang="en">
				<head>
					<meta charset="UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<title>New user on Your Company</title>
				</head>
				<body style="background: #f6f6f6; text-align: center;">
					<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
						<div style="margin: -20px -20px 0; padding: 20px;">
							<a href="http://yoursite.com/">
								<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
							</a>
						</div>
						<div style="padding: 0 30px 30px;">
							<p>You have applied for open beta testing of new platform for <br><b>Your Company inc</b>.</p>
							<p>Please use the details below for entrance to new platform:</p>
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
								Link: <a href="http://admin.yoursite.com/" style="color: #0e92d4;">Click here</a><br>
								Login: '.$email.'<br>
								Password: '.$password.'
							</div>
						</div>
					</div>
				</body>
				</html>';

				// Send
				mail($email, 'Welcome to the Your Company', $message, $headers);
			}
			echo 'OK';
			die;
		break;
		
		/*
		*  New users page
		*/
		case 'new':
			$meta['title'] = $lang['NewUsers'];
			if (in_to_array('1,2', $user['group_ids'])) {
				tpl_set('users/new', [
				], [
					'add' => $user['add_users'],
					'owner' => in_array(1, explode(
						',',$user['group_ids']
						)
					),
					'user_new' => (in_to_array('1,2', $user['group_ids'])),
					'sending' => $user['multi_sending']
				], 'content');
			} else {
				tpl_set('forbidden', [
					'text' => 'You have no access to do this'
				], [
					'add' => $user['add_users'],
					'owner' => in_array(1, explode(
						',',$user['group_ids']
						)
					),
					'user_new' => (in_to_array('1,2', $user['group_ids']))
				], 'content');
			}
		break;
		
		case 'lite_step':
			foreach($config['currency'] as $k => $v){
				$currency[] = [
					'id' => $k,
					'name' => $k.' ('.$v['symbol'].')'
				];
			}
			tpl_set('users/lite_step', [
				'referrals' => json_encode(db_multi_query(
					'SELECT * FROM `'.DB_PREFIX.'_referrals` ORDER BY `name` ASC', true
				)),
				'os-list' => json_encode(db_multi_query('
					SELECT * FROM `'.DB_PREFIX.'_inventory_os` ORDER BY id', true
				)),
				'currency' => json_encode($currency),
				'group' => 'Customers',
				'group-id' => '/5'
			], [
				'add' => false,
				'user_new' => 1
			], 'content');
		break;
		
		/*
		* Email multi sending
		*/
		case 'sending':
			if(!$user['multi_sending']) die('Not permission');
			if($_POST){
				
				$sending_cache = APP_DIR.'/cache/sending.json';
				
				$group_ids = '';
				$sex = '';
				$page = 0;
				$data = [];
				$sending = isset($_POST['send']) && $_POST['send'];
				
				if($sending){
					$data = json_decode(file_get_contents($sending_cache), true);
					$group_ids = $data['group_ids'];
					$sex = $data['sex'];
					$page = intval($data['page']);
				} else {
					$group_ids = ids_filter($_POST['groups']);
					$sex = in_array($_POST['sex'], ['Male', 'Female']) ? $_POST['sex'] : '';
				}
				
				$sql = '';
				foreach(explode(',', $group_ids) as $group_id){
					$sql .= ($sql ? ' OR ' : '').'FIND_IN_SET('.$group_id.', group_ids)';
				}
				
				if($sql) $sql = ' AND ('.$sql.')';
				
				$users = db_multi_query(
					'SELECT '.(
						!$page ? 'SQL_CALC_FOUND_ROWS' : ''
					).' id, email, CONCAT(name, \' \', lastname) as name FROM `'.DB_PREFIX.'_users` WHERE email != \'\' AND del = 0'.($sex ? ' AND sex = \''.$sex.'\'' : '').' '.$sql.' ORDER BY `id` DESC LIMIT '.($page*20).', 20', true
				);
				
				$total = 0;
				$sent = 0;
				
				if($sending){
					$data['page'] = intval($data['page'])+1;
					foreach($users as $user){
						$headers  = 'MIME-Version: 1.0'."\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
						$headers .= 'To: '.$user['name'].' <'.$user['email'].'>'."\r\n";
						$headers .= 'From: Your Company <info@yoursite.com>' . "\r\n";

						mail($user['email'], $data['subject'], '<!DOCTYPE html>
						<html lang="en">
						<head>
							<meta charset="UTF-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">
							<title>New user on Your Company</title>
						</head>
						<body style="background: #f6f6f6; text-align: center;">
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
								<div style="margin: -20px -20px 0; padding: 20px;">
									<a href="https://yoursite.com/">
										<img src="https://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
									</a>
								</div>
								<div style="padding: 0 30px 30px;">'.str_ireplace([
									'{user-name}',
									'src="/uploads/'
								], [
									$user['name'],
									'style="max-width: 100%;" src="https://yoursite.com/uploads/'
								], $data['message']).'</div>
							</div>
						</body>
						</html>', $headers);
					}
					
					$total = $data['total'];
					$data['sent'] = intval($data['sent'])+count($users);
					$sent = $data['sent'];
					
					file_put_contents($sending_cache, json_encode($data));
				} else {
					
					$total = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					
					file_put_contents($sending_cache, json_encode([
						'group_ids' => $group_ids,
						'sex' => $sex,
						'subject' => trim($_POST['subject']),
						'message' => trim($_POST['content']),
						'total' => $total,
						'sent' => 0,
						'page' => 0
					]));
				}
				
				echo json_encode([
					'total' => $total,
					'sent' => $sent,
					'status' => count($users) ? 'processed' : 'complete'
				]);
				die;
			} else {
				$options = '';
				foreach(db_multi_query('SELECT `group_id`, `name` FROM `'.DB_PREFIX.'_groups`  ORDER BY `group_id`', true) as $group){
					$options .= '<option value="'.$group['group_id'].'"'.(
						$group['group_id'] == 5 ? ' selected' : ''
					).'>'.$group['name'].'</option>';
				}
				tpl_set('users/sending', [
					'groups' => $options
				], [
					'sending' => $user['multi_sending'],
					'user_new' => (in_to_array('1,2', $user['group_ids']))
				], 'content');
			}
		break;
		
		/*
		* All users
		*/
		default:
		$group_id = intval($route[1]);
		$staffs = (int)$_GET['staffs'];
		$store_ip = $config['object_ips'][$staffs];
		if ($route[1] === 'company') $company = 1;
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 20;
		$group = [];
		if($group_id  AND !$company){
			$group = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_groups` WHERE group_id = '.$group_id);
		}
		
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_users` WHERE del = 0 AND franchise_id = '.$user['franchise_id'].' '.(
			$query ? ' AND IF(`company` = 1,
								`name`,
								CONCAT(`name`, \' \', `lastname`)
							) LIKE \'%'.$query.'%\' OR email LIKE \'%'.$query.'%\' OR REGEXP_REPLACE(phone, \' \', \'\') LIKE \'%'.$query.'%\' ' : ''
		).(
			$staffs ? ' AND ip = INET_ATON(\''.$store_ip.'\') ' : ''
		).(($group_id AND !$company) ? ' AND FIND_IN_SET(
			'.$group_id.', group_ids
		) ' : (
				$company == 1 ? ' AND company = 1 ' : ''
			)).(
				in_to_array('1', $user['group_ids']) ? '' : ' AND del = 0'
			).($query ? '' : ' ORDER BY `image` DESC').' LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				$edit = in_to_array($row['group_ids'], $user['edit_users']);
				$delete = in_to_array($row['group_ids'], $user['delete_users']) ? true : in_array(1, explode(',', $user['group_ids']));
				tpl_set('users/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'reg-date' => $row['reg_date'],
					'lastname' => $row['lastname'],
					'phone' => $row['phone'],
					'ava' => $row['image']
				], [
					'ava' => $row['image'],
					'delete' => $delete,
					'deleted' => $row['del'],
					'edit' => $edit,
					'deny' => ($edit OR $delete),
				], 'users');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = $lang['All'].' '.($group['name'] ?? $lang['users']);
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['users'],
			]));
		}
		tpl_set('users/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'group' => $group['name'] ? $group['name'] : ($company ? 'company' : 'user'),
			'group-id' => $group_id ? '/'.$group_id : ($company ? '/company' : ''),
			'users' => $tpl_content['users']
		], [
			'add' => $user['add_users'],
			'company' => $company,
			'user_new' => (in_to_array('1,2', $user['group_ids']))
		], 'content');
	}
//} else {
//	tpl_set('forbidden', [
//		'text' => $lang['Forbidden'],
//	], [], 'content');
//}
?>