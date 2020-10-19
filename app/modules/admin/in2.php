<?php
/**
 * @appointment Invoices admin panel
 * @author      Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
switch($route[1]){	
	/*
	* Del invoice
	*/
	case 'del_invoice':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$del = intval($_POST['del']);
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
			invoices = REGEXP_REPLACE(invoices, \'(,?)'.$del.'\', \'\')
		WHERE id = '.$id);
	break;
	
	/*
	* Create estimate
	*/
	case 'add_discount_code':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$code = text_filter($_POST['code'], 10, false);
		
		$invoice = db_multi_query('SELECT customer_id FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
		
		if ($discount = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_discounts` WHERE id = \''.$code.'\'')) {
			if ($invoice['customer_id'] != $discount['customer_id'])
				die('no_user');
			else if (strtotime($discount['date_exp']) < time())
				die('no_date');
			else if ($discount['user']) {
				die('used');
			} else {
				db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
					store_discount = \''.$code.'\'
				WHERE id = '.$id);
				
				db_query('UPDATE `'.DB_PREFIX.'_store_discounts` SET 
					used = 1
				WHERE id = \''.$code.'\'');
				
				die('OK');
			}
		} else
			die('no_code');
	break;
		
	/*
	* Create estimate
	*/
	case 'send_estimate':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$customer = intval($_POST['customer']);
		
		if (!$customer)
			die('no_customer');
		
		if ($id) {
			db_query('
				UPDATE `'.DB_PREFIX.'_invoices` SET
					customer_id = '.$customer.',
					estimate = 0
				WHERE id = '.$id
			);
			die('OK');
		} else 
			die;
	break;
	
	/*
	* Confirm discount
	*/
	case 'confirm_discount_invoise':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		if ($user['confirm_discount']) {
			if ($d = db_multi_query('SELECT discount_confirmed FROM `'.DB_PREFIX.'_invoices` WHERE discount_confirmed = 0 AND id = '.$id)) {
				db_query('UPDATE `'.DB_PREFIX.'_invoices` SET discount_confirmed = 1 WHERE id = '.$id);
				
				$inv = db_multi_query('SELECT issue_id FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
				if ($inv['issue_id'])
					db_query('UPDATE `'.DB_PREFIX.'_issues` SET discount_confirmed = 1 WHERE id = '.$inv['issue_id']);
				
				db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'un_discount\'');
			}
			die('OK');
		} else 
			die('no_acc');
	break;
	
	/*
	* Update invoices
	*/
	case 'update':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$invoice = db_multi_query('
			SELECT 
				i.issue_id, 
				iss.inventory_info, 
				iss.service_info, 
				iss.purchase_info, 
				iss.upcharge_info, 
				iss.total 
			FROM `'.DB_PREFIX.'_invoices` i
			LEFT JOIN `'.DB_PREFIX.'_issues` iss
				ON iss.id = i.issue_id
			WHERE i.id = '.$id);
		if ($invoice['issue_id']) {
			//$issue_info1 = [
			//	'total' => $invoice['total'],
			//	'inventory' => $invoice['inventory_info'] ?: '{}',
			//	'services' => $invoice['service_info'] ?: '{}',
			//	'purchases' => $invoice['purchase_info'] ?: '{}',
			//	'upcharge' => $invoice['upcharge_info'] ?: '{}'
			//];
			//if($user['id'] == 16){
			//	echo json_encode($issue_info1);
			//	die;
			//}
			$issue_info = '{"total":"'.$invoice['total'].'","inventory":'.($invoice['inventory_info'] ?: '{}').',"services":'.($invoice['service_info'] ?: '{}').',"purchases":'.($invoice['purchase_info'] ?: '{}').',"upcharge":'.($invoice['upcharge_info'] ?: '{}').'}';
			db_query('UPDATE `'.DB_PREFIX.'_invoices` SET issue_info = \''.db_escape_string($issue_info).'\', conducted = 0 WHERE id = '.$id);
			die('OK');
		} else 
			die('no_issue');
	break;
	
	/*
	* Refund confirm
	*/
	case 'refund_confirm':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		if ($user['confirm_refund']) {
			if ($invoice['refund'] != 0)
				die('ERR');
			
			db_query('UPDATE `'.DB_PREFIX.'_invoices` SET refund = 1 WHERE id = '.$id);
				
			die('OK');
		} else 
			die('no_acc');
	break;
	
	/*
	* Refund decline
	*/
	case 'refund_decline':
		is_ajax() or die('Hacking attempt!');
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET refund = 2 WHERE id = '.intval($_POST['id']));
		die('OK');
	break;
	
	
	/*
	* Refund request
	*/
	case 'create_refund':
		is_ajax() or die('Hacking attempt!');
		
		$info = db_multi_query('
			SELECT customer_id, staff_id, object_id, discount, total, paid, conducted 
			FROM `'.DB_PREFIX.'_invoices` 
			WHERE id = '.(int)$_POST['refund_id']
		);
		
		db_query('INSERT INTO `'.DB_PREFIX.'_invoices` SET 
		    date = \''.date('Y-m-d H:i:s', time()).'\',
			refund_invoice = '.(int)$_POST['refund_id'].', 
			refund_info = \''.text_filter($_POST['refund_info']).'\', 
			refund_user = '.(int)$info['staff_id'].',
			refund_comment = \''.text_filter($_POST['refund_comment']).'\',
			object_id = '.(int)$info['object_id'].',
			staff_id = '.(int)$user['id'].',
			currency = \''.(text_filter($_POST['currency'], 25, false) ?: 'USD').'\',
			discount = \''.$info['discount'].'\',
			customer_id = '.$info['customer_id'].(
				$info['conducted'] == 1 ? '' : ', refund_paid = \''.$info['paid'].'\''
			)
		);
		$id = intval(mysqli_insert_id($db_link));
		echo $id;   
		die;
	break;
	
	/*
	* All
	*/
	case 'all':
        is_ajax() or die('Hacking attempt!');
		$lId = intval($_POST['lId']);
		$paid = text_filter($_POST['paid'], 10, false);
		$partial = intval($_POST['partial']);
		$oId = intval($_POST['oId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$objects = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, id as name FROM `'.DB_PREFIX.'_invoices` WHERE 1 '.(
				$nIds ? ' AND id NOT IN('.$nIds.')' : ''
			).(
				$oId ? ' AND object_id = '.$oId.'' : ''
			).(
				$paid == 'unpaid' ? ' AND conducted = 0' : ($paid == 'paid' ? ' AND conducted = 1' : '')
			).(
				$partial ? ' AND paid = 0' : ''
			).(
				$lId ? ' AND id < '.$lId : ''
			).(
				$query ? ' AND id LIKE \'%'.$query.'%\'': ''
			).' ORDER BY `id` DESC LIMIT 20'
		, true);
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $objects,
			'count' => $res_count,
		]));
	break;

    /*
	* Send prices 
	*/
    case 'send_prices':
        is_ajax() or die('Hacking attempt!');
		$tradein = $_POST['tradein'];
		$object = intval($_POST['object']);
		$ids = [];
		$err = '';
		foreach($tradein as $i => $tr) {
			if (!strlen((string)$tr['price']) OR !strlen((string)$tr['purchase'])) {
				print_r(json_encode([
					'error' => 'empty_price',
					'id' => $i
				]));
				die;
			}
			if ($tr['price'] < min_price($tr['purchase'], $object)) {
				$err = 'min_price';
				$ids[] = $i;
			}
		}
		
		if ($err) {
			print_r(json_encode([
				'error' => $err,
				'ids' => $ids
			]));
		} else 
			echo 'OK';
        die;
    break;
	
    /*
	* Send onsite 
	*/
    case 'send_onsite':
        is_ajax() or die('Hacking attempt!');
        $id = intval($_POST['id']);
        
        if ($onsite = db_multi_query('
			SELECT 
				os.customer_id, 
				os.last_object, 
				os.left_time,
				o.tax,
				io.add_hour_pay
			FROM `'.DB_PREFIX.'_users_onsite` os
			LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` io
				ON io.id = os.onsite_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = os.last_object
			WHERE os.id = '.$id)) {
            db_query('INSERT INTO `'.DB_PREFIX.'_invoices` SET
                customer_id = '.$onsite['customer_id'].',
                object_id = '.$onsite['last_object'].',
                add_onsite = '.$id.',
                date = \''.date('Y-m-d H:i:s', time()).'\',
                add_onsite_price = \''.floatval($onsite['add_hour_pay'] * abs($onsite['left_time']) / 3600).'\',
				total = \''.floatval($onsite['add_hour_pay'] * abs($onsite['left_time']) / 3600).'\',
				tax = 0
            ');
            $in_id = intval(mysqli_insert_id($db_link));
            echo $in_id;   
        }
        die;
    break;
	
	/*
	* New purchase
	*/
	case 'newPurchase':
        is_ajax() or die('Hacking attempt!');
		$objects_ip = array_flip($config['object_ips']);
		if (floatval($_POST['cprice']) < min_price(floatval($_POST['price']), ($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0)))
			die('min_price');
		
		if (!text_filter($_POST['salename'], 1000, false)) {
			die('empty_salename');
		}
		
		db_query('INSERT INTO `'.DB_PREFIX.'_purchases` SET
			name = \''.text_filter($_POST['name'], 1000, false).'\',
			sale_name = \''.text_filter($_POST['salename'], 100, false).'\',
			link = \''.text_filter($_POST['link'], 200, false).'\',
			price = \''.text_filter($_POST['price'], 30, false).'\',
			tracking = \''.text_filter($_POST['itemID'], 50, false).'\',
			quantity = 1,
			total = \''.text_filter($_POST['price'], 30, false).'\',
			sale = \''.floatval($_POST['cprice']).'\',
			customer_id = \''.intval($_POST['customer_id']).'\',
			object_id = \''.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).'\',
			status = \'Purchased\',
			create_id = '.$user['id'].', 
			create_date = \''.date('Y-m-d H:i:s', time()).'\''
		);
		
		$pid = intval(mysqli_insert_id($db_link));
		
		db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', date = \''.date('Y-m-d H:i:s', time()).'\', event = \'add_purchase\', object_id = '.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).', event_id = '.$pid);
		
		if ($_POST['photo'] && strpos($_POST['photo'], 'thumb') == false) {
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
		echo $pid;
		die;
	break;
	
	/*
	* All discounts
	*/
	case 'allDiscounts':
        is_ajax() or die('Hacking attempt!');
		$lId = intval($_POST['lId']);
		$oId = intval($_POST['oId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$discounts = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, CONCAT(name, \', \', percent, \'%\') as name, percent as price FROM `'.DB_PREFIX.'_invoices_discount` 
			WHERE confirmed = 1'.(
				$nIds ? ' AND id NOT IN('.$nIds.')' : ''
			).(
				$oId ? ' AND object_id = '.$oId.'' : ''
			).(
				$lId ? ' AND id < '.$lId : ''
			).(
				$query ? ' AND MATCH(`id`) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE)': ''
			).' ORDER BY `id` DESC LIMIT 20'
		, true);
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $discounts,
			'count' => $res_count,
		]));
	break;
	
	
	/*
	* Make transaction
	*/
	case 'make_tran':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$type = text_filter($_POST['method'], 10, false);
		$amount = (floatval($_POST['amount']) ?: 0);
		$refund_inventory = [
			'stock' => [],
			'tradein' => []
		];

		$invoice = db_multi_query('	
			SELECT 
				i.*,
				u.name,
				u.lastname
			FROM `'.DB_PREFIX.'_invoices` i
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id  = i.customer_id
			WHERE i.id = '.$id);
		$issue_info = json_decode($invoice['issue_info'], true);
		
		/* if ($amount == 0 AND !$invoice['buy_inventory'])
			die('null_invoice'); */
		
		if ($invoice['conducted'] == 1)
			die('conducted');

		$full = abs(floatval($invoice['paid']) + $amount - floatval($invoice['total']));
		$total = floatval($invoice['total']);
		
		if($_POST['iss_complete']){
			db_query('UPDATE `'.DB_PREFIX.'_issues` SET status_id = 30 WHERE id = '.$invoice['issue_id']);
		}
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
			pay_method = \''.$type.'\',
			transaction = 1,
			tr_date = \''.date('Y-m-d H:i:s', time()).'\',
			paid = \''.(floatval($invoice['paid']) + $amount).'\' WHERE id = '.$id);
		
		db_query('INSERT INTO `'.DB_PREFIX.'_invoices_history` SET 
			invoice_id = '.$id.',
			amount = \''.$amount.'\',
			currency = \''.$invoice['currency'].'\',
			staff_id = '.$user['id'].',
			date = \''.date('Y-m-d H:i:s', time()).'\',
			type = \''.$type.'\''.(
				$type == 'check' ? ', check_number = \''.text_filter($_POST['check'], 25, false).'\'' : ''
			));
			
		// ------------------------------------------------------------------------------- //
		
		if ($full < 0.01 AND $invoice['conducted'] == 0) {
			
			// --- start send to tablet --- //
			file_put_contents(ROOT_DIR.'/tablet_push.log', $invoice['object_id'].$config['tablet_user'].$config['tablet_password'].' '.md5($invoice['object_id'].$config['tablet_user'].$config['tablet_password'])."\n", FILE_APPEND);
			if($invoice['issue_id'] > 0){
				send_push(md5($invoice['object_id'].$config['tablet_user'].$config['tablet_password']), [
					'type' => 'tablet_feedback',
					'customer_id' => $invoice['customer_id'],
					'name' => $invoice['customer_id'] ? $invoice['name'].' '.$invoice['lastname'] : 'Customer',
					'issue_id' => $invoice['issue_id'],
					'staff_id' => $invoice['staff_id']
				]);
			}
			
			// --- end send to tablet --- //
			
			if (strlen($invoice['refund_info']) > 2) {
				$refund_info = json_decode($invoice['refund_info'], true);
				$inv_refund = db_multi_query('SELECT refund_inventory, customer_id, object_id FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$invoice['refund_invoice']);
				$invs = json_decode($inv_refund['refund_inventory'], true);

				$stock_refund = array_filter($invs['stock'], function($a, $k) use(&$refund_info, &$invs) {
					if (in_array($k, array_keys($refund_info)))
						return $a;
				}, ARRAY_FILTER_USE_BOTH);
				
				$tradein_refund = array_filter($invs['tradein'], function($a, $k) use(&$refund_info, &$invs) {
					if (in_array($a, array_keys($refund_info)))
						return $a;
				}, ARRAY_FILTER_USE_BOTH);
				
				if ($invs) {
					if ($stock_refund) {
						db_query('
							UPDATE `'.DB_PREFIX.'_inventory` SET 
								object_id = '.$inv_refund['object_id'].',
								customer_id = 0, 
								quantity = 1
							WHERE id IN ('.implode(',', $stock_refund).')
						');
					}
					if ($tradein_refund) {
						db_query('
							UPDATE `'.DB_PREFIX.'_inventory` SET 
								customer_id = '.$invoice['customer_id'].', 
								quantity = 1
							WHERE id IN ('.implode(',', $tradein_refund).')
						');
					}
				}
				
				db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
						conducted = 1,
						paid = total
					WHERE id = '.$invoice['id']);
			} else {
				foreach($invoices = db_multi_query('	
					SELECT 
						*
					FROM `'.DB_PREFIX.'_invoices`
					WHERE id IN ('.ids_filter($id.','.$invoice['invoices']).')', true) as $invoice) {
					$issue_info = json_decode($invoice['issue_info'], true);
							
					if ($invoice['issue_id']) {
						db_query('
							INSERT INTO `'.DB_PREFIX.'_feedback_queue` SET
								customer_id = '.$invoice['customer_id'].', 
								date = DATE_ADD(\''.date('Y-m-d', time()).'\', INTERVAL 3 DAY),
								email_date = DATE_ADD(\''.date('Y-m-d', time()).'\', INTERVAL 1 DAY),
								email = 0,
								issue_id = '.$invoice['issue_id']
						);
						
						if ($points = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_inventory_status_history` WHERE issue_id = '.$invoice['issue_id'].' AND percent = 1', true)) {
							foreach($points as $p) {
								db_query('
									UPDATE `'.DB_PREFIX.'_users`
										SET points = points+'.(intval($issue_info['total'] ?: 0) / 100 * floatval($p['point'])).'
									WHERE id = '.$p['staff_id']
								);
							}
						}
						
						db_query('
							UPDATE `'.DB_PREFIX.'_inventory_status_history` SET
								point = '.intval($issue_info['total'] ?: null).' / 100 * point,
								percent = 0,
								rate_point = 0,
								staff_id = IF(status_id = 2, '.($invoice['staff_id'] ?? 0).', staff_id)
							WHERE issue_id = '.$invoice['issue_id'].' AND percent = 1
						');
					
					}
						
					if ($invoice['inventory_info'] AND strlen($invoice['inventory_info']) > 2 OR $issue_info['inventory']) {
						$inventory_ids = ids_filter(($invoice['inventory_info'] ? implode(',', array_keys(json_decode($invoice['inventory_info'], true))) : '').','.($issue_info['inventory'] ? implode(',', array_keys($issue_info['inventory'])) : ''));
					}
					
					
					if ($inventory_ids) {
						$inventory_arr = (
							($invoice['inventory_info'] AND strlen($invoice['inventory_info']) > 2 AND $issue_info['inventory']) ? 
								array_merge(json_decode($invoice['inventory_info'], true), json_decode($issue_info['inventory'], true)) :
								(
									($invoice['inventory_info'] AND strlen($invoice['inventory_info']) > 2) ? 
										json_decode($invoice['inventory_info'], true) : $issue_info['inventory']
								)
						);
						
						$issue_inv = $issue_info ? array_keys($issue_info['inventory']) : [];
						foreach(explode(',', $inventory_ids) as $inv) {
							$inv_ids = '';
							$inv_info = db_multi_query('SELECT *, purchase_price as purchase FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$inv);
							if ($inv_info['quantity'] > 1) {
								for($i = 0; $i < (intval($inventory_arr[$inv]['items']) ?: 1); $i ++) {
									db_query('INSERT INTO `'.DB_PREFIX.'_inventory` (model, os_id, price,object_id, options, type, type_id, category_id, model_id, owner_type, object_owner, customer_id, barcode, quantity, accessories, cr_user, cr_date, confirmed
										) SELECT i.model, i.os_id, i.price,i.object_id, i.options, i.type, i.type_id, i.category_id, i.model_id, "external", 0, '.$invoice['customer_id'].', i.barcode, 1, '.(in_array($inv, $issue_inv) ? 1 : 0).', '.$user['id'].', \''.date('Y-m-d H:i:s').'\', 1 FROM `'.DB_PREFIX.'_inventory` AS i WHERE i.id = '.$inv								
									);
									if ($inv_ids) $inv_ids .= ',';
									$inv_ids .= intval(mysqli_insert_id($db_link));
								}
								db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
									quantity = (quantity - '.(intval($inventory_arr[$inv]['items']) ?: 1).')
								WHERE id  = '.$inv);
							} else {
								db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
									customer_id = '.$invoice['customer_id'].',
									owner_type = \'external\''.(
										in_array($inv, $issue_inv) ? ', accessories = 1' : ''
									).' 
								WHERE id  = '.$inv);
								$inv_ids = $inv;
							}
							
							$refund_inventory['stock'][$inv] = $inv_ids;
							
							// points 
							//if($user['store_id'] > 0){
								$points = (floatval($inv_info['price'])-floatval($inv_info['purchase']))*floatval($config['user_points']['trade_in']['selling'])/100;
								$points_si = floatval($inv_info['price']) / 200 * floatval($config['user_points']['new_inventory']['sell_inventory']);
								
								db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									staff_id = '.$user['id'].',
									date = \''.date('Y-m-d H:i:s', time()).'\',
									action = \''.($inv_info['tradein'] == 1 ? 'trade_in_selling' : 'sell_inventory').'\',
									object_id = '.$user['store_id'].',
									inventory_id = '.$inv.',
									point = \''.($inv_info['tradein'] == 1 ? $points : $points_si).'\''
								);
								if (($inv_info['tradein'] == 1 ? $points : $points_si) != 0) {
									db_query(
										'UPDATE `'.DB_PREFIX.'_users`
											SET points = points+'.($inv_info['tradein'] == 1 ? $points : $points_si).'
										WHERE id = '.$user['id']
									);
								}
							//}

						}
					}

					if ($invoice['tradein_info'] AND strlen($invoice['tradein_info']) > 2) {
						$inventory_ids = ids_filter(implode(',', array_keys(json_decode($invoice['tradein_info'], true))));
						
						$tradein = db_multi_query('
							SELECT 
								DISTINCT i.*,
								iss.id as issue_id,
								iss.purchase_info
							FROM `'.DB_PREFIX.'_inventory` i
							LEFT JOIN `'.DB_PREFIX.'_issues` iss
								ON iss.inventory_id = i.id AND iss.finished = 0 AND iss.purchase_info != \'\' AND iss.purchase_info != \'{}\'
							WHERE i.id IN ('.$inventory_ids.') 
							GROUP BY i.id
						', true);
						
						if ($ptr = array_filter($tradein, function($a) {
							if ($a['purchase_info'] AND $a['purchase_info'] != '{}')
								return $a;
						})) {
							die('hasissue_'.$ptr[0]['issue_id']);
						}
						
						
						db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
							object_id = '.$invoice['object_id'].',
							customer_id = 0, 
							quantity = 1,
							tradein = 1
						WHERE id IN('.$inventory_ids.')');

						
						$tradein_info = json_decode($invoice['tradein_info'], true);
						foreach($tradein as $tr) {
							db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
								purchase_price = \''.$tradein_info[$tr['id']]['purchase'].'\',
								price = \''.$tradein_info[$tr['id']]['price'].'\',
								tradein = 1
							WHERE id = '.$tr['id']);
							
							$refund_inventory['tradein'][] = $tr['id'];
							
							db_query('INSERT INTO `'.DB_PREFIX.'_tradein` SET
								inventory_id = '.intval($tr['id']).',
								cr_user = '.$user['id'].',
								cr_date = \''.date('Y-m-d H:i:s', time()).'\',
								cr_price = '.floatval($v['price']).(
									(in_to_array('1,2', $user['group_ids'])) ? ', 
										confirmed = 1,
										cn_user = '.$user['id'].',
										cn_date = \''.date('Y-m-d H:i:s', time()).'\',
										cn_price = '.floatval($v['price']) : ''
								)
							);
							if ($user['id'] == 17) $user['store_id'] = 2;
							if(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))){
								$points = (floatval($tr['price'])-floatval($tr['purchase']))*floatval($config['user_points']['trade_in']['points'])/100;
								db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									staff_id = '.$user['id'].',
									action = \'trade_in\',
									date = \''.date('Y-m-d H:i:s', time()).'\',
									object_id = '.$user['store_id'].',
									inventory_id = '.intval($tr['id']).',
									point = \''.$points.'\''
								);	//min_rate = '.$sql_['points'].',
								db_query(
									'UPDATE `'.DB_PREFIX.'_users`
										SET points = points+'.$points.'
									WHERE id = '.$user['id']
								);
							}

						}

					}
					
					db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
						conducted = 1,
						paid = total,
						refund_inventory = \''.json_encode($refund_inventory).'\'
					WHERE id = '.$invoice['id']);
				}
			}
			
		
		// ------------------------------------------------------------------------------- //
			if ($total >= 50) {

				$points = floatval($config['user_points']['make_transaction']['points']);
				
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$user['id'].',
					action = \'make_transaction\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					object_id = '.$user['store_id'].',
					invoice_id = '.$id.',
					point = \''.$points.'\''
				);
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$user['id']
				);
			}
			
			db_query('
				INSERT INTO 
				`'.DB_PREFIX.'_activity` SET 
					user_id = \''.$user['id'].'\', 
					event = \'make transaction\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					event_id = '.$id.',
					object_id = '.$user['store_id'].'
			');
		}
		
		
		if(mysqli_affected_rows($db_link)){
			exit('OK');
		} else
			exit('ERR');
		die;
	break;
	
	/*
	* Update partial
	*/
	case 'partial':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$partial = floatval($_POST['partial']);

		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET paid = IF(
			'.(intval($_POST['purchace']) ? 'total-paid <= -'.$partial.', paid+\'-'.$partial.'\', paid' :
			'total-paid >= '.$partial.', paid+\''.$partial.'\', paid').'
		) WHERE id = '.$id);
		if(mysqli_affected_rows($db_link)){
			exit('OK');
		} else
			exit('ERR');
		die;
	break;
	
	/*
	* Add invoice
	*/
	case 'add_invoice':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
			invoices = IF(invoices,
				CONCAT(invoices, \',\', '.intval($_POST['invoice']).'),
				'.intval($_POST['invoice']).') WHERE id = '.$id);
		if(mysqli_affected_rows($db_link)){
			exit('OK');
		} else
			exit('ERR');
		die;
	break;
	
	/*
	* Save form
	*/
	case 'save_form':
		is_ajax() or die('Hacking attempt!');
		$config['device_form'] = str_replace('\n', '', text_filter($_POST['content']));
		if(conf_save())
			echo 'OK';
		else
			echo 'ERR';
		die;
	break;
	
	/*
	* Save email form
	*/
	case 'save_email_form':
		is_ajax() or die('Hacking attempt!');
		$config['invoice_email'] = str_replace('\n', '', text_filter($_POST['content']));
		if(conf_save())
			echo 'OK';
		else
			echo 'ERR';
		die;
	break;

	/*
	* Update discount
	*/
	case 'discount':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$discount_id = intval($_POST['discount']);
		$inv = db_multi_query('SELECT issue_id, discount_confirmed, discount FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
		$discount_old = json_decode($inv['discount'], true);
		
		$discount = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices_discount` WHERE id = '.$discount_id);
		
		$disjson = $discount ? '{"'.$discount['id'].'":{"name":"'.$discount['name'].'","percent":"'.$discount['percent'].'"}}' : '';
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET discount = \''.$disjson.'\', discount_confirmed = 0 WHERE id = '.$id);
		
		if (!$inv['discount_confirmed'] AND is_array($discount_old) AND array_keys($discount_old)[0] AND !$discount['id'])
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'un_discount\'');
		
		if ((is_array($discount_old) AND array_keys($discount_old)[0] != $discount['id'] AND $inv['discount_confirmed']) OR !$discount_old)
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'un_discount\'');
		
		if ($inv['issue_id'])
			db_query('UPDATE `'.DB_PREFIX.'_issues` SET discount = \''.$disjson.'\', discount_confirmed = 0 WHERE id = '.$inv['issue_id']);
		die('OK');
	break;
	
	/*
	* Send invoices
	*/
	case 'send':
		is_ajax() or die('Hacking attempt!');
		if($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips']))){
			die('no_store');
		}
		$id = intval($_POST['id']);
		$new = intval($_POST['id']);
		$discount = '';
		$tradein = json_decode($_POST['tradein'], true);
		$issue_id = intval($_POST['issue']);
		$estimate = intval($_POST['estimate']);
		$add_onsite_price = floatval($_POST['add_onsite_price']);
		$issue_info = '';
		$currency = 'USD';
		
		if (!$id AND $issue_id) {
			$iinv = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_invoices` WHERE issue_id = '.$issue_id);
			if ($iinv['id'])
				die('issue_invoice');
		}
		

		if ($id) {
			$row = db_multi_query('SELECT conducted, tradein_info, purchases_info FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
			$evalue = array_keys(json_decode($row['purchases_info'], true) ?: []);
			$pur_value = array_keys(json_decode($_POST['purchases'], true) ?: []);
			$deleted = implode(',', (($pur_value AND $evalue) ? array_diff($pur_value, $evalue) : ($pur_value ? $pur_value : $evalue)));
			
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
				
			if ($deleted) {
				$purchases = db_multi_query('SELECT SUM(confirmed) as conf FROM `'.DB_PREFIX.'_purchases` WHERE id IN('.$deleted.')');

				if ($purchases['conf'])
					die('confirmed');
			
				db_query('
					UPDATE `'.DB_PREFIX.'_purchases` SET
						del = 1
					WHERE id IN ('.$deleted.')'
				);
			}
		}
		
		
		if ((!intval($_POST['customer']) AND $issue_id) OR (!intval($_POST['customer']) AND !$issue_id AND floatval($_POST['total']) > $config['quick_sell']) AND !$estimate)
			die('no_customer');
		
		if (!$id OR ($id AND ($row['conducted'] AND $user['edit_paid_invoices']) OR (!$row['conducted'] AND $user['edit_invoices']))) {
            if ($issue_id AND $issue = db_multi_query('
                SELECT 
                    inventory_info, service_info, purchase_info, upcharge_info, customer_id, total, currency, discount_confirmed
                FROM `'.DB_PREFIX.'_issues` 
                WHERE id = '.$issue_id
            )) {
				$issue_total = $issue['total'];
				if ($issue['inventory_info'] AND strlen($issue['inventory_info']) > 2) {
					$inventories = json_decode($issue['inventory_info'], true);
					$inv_arr = array_column(db_multi_query('SELECT purchase_price FROM `'.DB_PREFIX.'_inventory` WHERE id IN ('.implode(',', array_keys($inventories)).')', true), 'purchase_price', 'id');
					foreach($inventories as $k => $inv) {
						$price = floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
						$issue_total = $issue_total - $price + ($inv_arr[$k] > 0 ? ($price - $inv_arr[$k]) : ($price / 2));
					}
				}
                $issue_info = '{"total":"'.$issue_total.'","inventory":'.($issue['inventory_info'] ?: '{}').',"services":'.($issue['service_info'] ?: '{}').',"purchases":'.($issue['purchase_info'] ?: '{}').',"upcharge":'.($issue['upcharge_info'] ?: '{}').'}';
            }
			
			if (!$id) {
				if ($issue_info)
					$currency = $issue['currency'];
				elseif ($_POST['services'] AND $_POST['services'] != '{}')
					$currency = array_values(json_decode($_POST['services'], true))[0]['currency'];
				elseif ($_POST['inventory'] AND $_POST['inventory'] != '{}')
					$currency = array_values(json_decode($_POST['inventory'], true))[0]['currency'];
				elseif ($_POST['tradein'] AND $_POST['tradein'] != '{}')
					$currency = array_values(json_decode($_POST['tradein'], true))[0]['currency'];
				elseif ($_POST['purchases'] AND $_POST['purchases'] != '{}')
					$currency = array_values(json_decode($_POST['purchases'], true))[0]['currency'];
			}
			
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_invoices` SET
				object_id = '.intval($_POST['object']).',
				customer_id = '.($issue_id ? $issue['customer_id'] : intval($_POST['customer'])).',
				'.(!$id ? 'staff_id = '.$user['id'].', date = \''.date('Y-m-d H:i:s', time()).'\', ' : '').'
				total = \''.floatval($_POST['total']).'\',
				tax = \''.floatval($_POST['tax']).'\',
				tax_exempt = \''.db_escape_string($_POST['tax_exempt']).'\',
				'.(intval($_POST['issue']) ? 'issue_id = '.$issue_id.', issue_info = \''.db_escape_string($issue_info).'\',' : '').'
				inventory_info = \''.addslashes($_POST['inventory']).'\',
				tradein_info = \''.addslashes($_POST['tradein']).'\',
				invoices = \''.ids_filter($_POST['invoices']).'\',
				purchases_info = \''.addslashes($_POST['purchases']).'\',
				estimate = \''.$estimate.'\',
				addition_info = \''.db_escape_string($_POST['additions']).'\',
				services_info = \''.addslashes($_POST['services']).'\''.(
					!$id ? ', currency = \''.$currency.'\'' : ''
				).(
					$_POST['discount'] ? ', discount = \''.$_POST['discount'].'\'' : ''
				).(
					$add_onsite_price ? ', add_onsite_price = '.$add_onsite_price : ''
				).(
					(!$id AND $issue_id) ? ', discount_confirmed = '.$issue['discount_confirmed'] : ''
				).(
				$id ? ' WHERE id = '.$id : ''
			));
			$id = $id ?: intval(
				mysqli_insert_id($db_link)
			);
			if ($_POST['purchases']) {
				$parr = implode(',', array_keys(JSON_DECODE($_POST['purchases'], true)));
				if ($parr)
					db_query('UPDATE `'.DB_PREFIX.'_purchases` SET invoice_id = '.$id.' WHERE id IN('.$parr.')');
			}
		} else 
			die('ERR');
		
		echo $id;
		die;
	break;
	
	/*
	* Get objects
	*/
	case 'objects':
		$lId = intval($_POST['lId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$id = array_flip($config['object_ips']);
		$all = intval($_POST['all']);
		
		$objects = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, tax FROM `'.DB_PREFIX.'_objects` WHERE 1'.(
			(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR in_array(16, explode(',', $user['group_ids'])) OR $all) ? '' :
			(
				$id[$_SERVER['REMOTE_ADDR']] != 0 ? 
				' AND (FIND_IN_SET('.$user['id'].', managers) 
				OR FIND_IN_SET('.$user['id'].', staff) 
				OR id = '.($id[$_SERVER['REMOTE_ADDR']] ?: 0).')' : ' AND id = 0 '
			)
		).(
			$nIds ? ' AND id NOT IN('.$nIds.')' : ''
		).(
			$lId ? ' AND id < '.$lId : ''
		).(
			$query ? ' AND MATCH(`name`) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE)': ''
		).' ORDER BY `id` DESC LIMIT 20', true);
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $objects,
			'count' => $res_count,
		]));
	break;
	
	/*
	* Delete store
	*/
	case 'del_discount':
		$id = intval($_POST['id']);
		//if($user['delete_discount']){
			db_query('DELETE FROM `'.DB_PREFIX.'_invoices_discount` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		//} else
		//	exit('ERR');
	break;
	
	/*
	*  View invoises
	*/
	case 'view':
	case 'print':
		$id = intval($route[2]);
		$meta['title'] = 'Invoice';
		$pur_ids = [];
		$total = 0;
		$tradein = 0;
		$tax = 0;
		$has_purchase = 0;

		$invoices_arr = [];
        $onsite = '';
        $onsite_total = 0;
        $tradein_total = 0;
        $discount = [];
        $html = [];
        
		if($row = db_multi_query('
			SELECT
				i.*,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.address as customer_address,
					u.email as customer_email,
					u.phone as customer_phone,
					u.zipcode as zipcode,
                        o.tax as object_tax,
                        o.name as object_name,
                        o.address as object_address,
                        o.phone as object_phone,
                            c.city as city_name,
								os.name as onsite_name,
								os.price as onsite_price,
								os.description as onsite_description,
								uos_info.name as user_onsite_name,
								SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time,
									su.name as staff_name,
									su.lastname as staff_lastname,
										ri.id as refund_id,
											d.name as delivery,
											d.price as delivery_price,
											d.currency as delivery_currency,
												sd.amount as store_discount_amount
			FROM `'.DB_PREFIX.'_invoices` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_invoices` ri
			ON ri.refund_invoice = i.id
				LEFT JOIN `'.DB_PREFIX.'_users` su
			ON i.staff_id = su.id	
				LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = i.object_id
				LEFT JOIN `'.DB_PREFIX.'_cities` c
			ON c.zip_code = u.zipcode
				LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
			ON uos.id = i.add_onsite
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
			ON uos_info.id = uos.onsite_id
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
			ON os.id = i.onsite_id
				LEFT JOIN `'.DB_PREFIX.'_orders` ord
			ON ord.id = i.order_id
				LEFT JOIN `'.DB_PREFIX.'_orders_delivery` d
			ON d.id = ord.delivery_id
				LEFT JOIN `'.DB_PREFIX.'_store_discounts` sd
			ON sd.id = i.store_discount
				WHERE i.id = '.$id
		)){
			if ($row['onsite_id']) {
                $onsite .= '<div class="tr" data-id="'.$row['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$row['onsite_id'].'">
                    <div class="td">
                        '.$row['onsite_name'].'
                        <br><i>'.$row['onsite_description'].'</i>
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_price">
                        '.$config['currency'][$row['currency']]['symbol'].$row['onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['onsite_price']);
                $onsite_total += floatval($row['onsite_price']);
            }
            if ($row['add_onsite']) {
                 $onsite .= '<div class="tr" data-id="'.$row['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$row['add_onsite'].'">
                    <div class="td">
                        '.$row['user_onsite_name'].'(Additional time - '.$row['user_onsite_time'].')
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_add_price">
                        '.$config['currency'][$row['currency']]['symbol'].$row['add_onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['add_onsite_price']);
                $onsite_total += floatval($row['add_onsite_price']);
            }
			
            if ($row['discount'])
                $discount = array_values(json_decode(($row['discount'] ?: '{}'), true));

            $issue_mhtml = '';
			
			/* if($user['id'] == 17) {
						echo '<pre>';
						print_r($row['issue_info']);
						die;
					} */
            if ($issue = json_decode($row['issue_info'], true)) {  
                    $issue_total = 0;
                    $issue_html = '';
					
                    if ($issue['inventory']) {
                        foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
                            $issue_html .= '<div class="tr">
									<div class="td isItem">
										 <a href="/inventory/view/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

                    if ($issue['services']) {
						
                        $upcharge = 0;
                        if ($issue['upcharge']) {
                            $upcharge = floatval(preg_replace('/[^0-9.-]/i', '', array_values($issue['upcharge'])[0]['price']));
							$service_len = count(array_filter($issue['services'], function($a) {
								if (floatval(preg_replace('/[^0-9.-]/i', '', $a['price'])) > 0)
									return $a;
							}));
							$upcharge /= $service_len;
						}

                        foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                            $price = floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                            $issue_html .= '<div class="tr">
									<div class="td isItem">
										 '.$iss_inv['name'].'
									</div>
									<div class="td w10">
										'.($iss_inv['quantity'] ?: '1').'
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format((($price > 0 && $upcharge) ? $price + $upcharge : $price) * ($iss_inv['quantity'] ?: 1), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += (($price > 0 && $upcharge) ? $price + $upcharge : $price) * ($iss_inv['quantity'] ?: 1);
                        }
                    }

                    if ($issue['purchases']) {
                        foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
                            $issue_html .= '<div class="tr">
									<div class="td isItem">
										 <a href="/purchases/edit/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price'])), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                        }
                    }

                    $issue_mhtml .= '<div class="tr">
								<div class="td">
									<b><a href="/issues/view/'.$row['issue_id'].'" target="_blank">Issue #'.$row['issue_id'].'</a></b>
								</div> 
								<div class="td w10"></div>
								<div class="td w100"><b>'.$config['currency'][$row['currency']]['symbol'].number_format($issue_total, 2, '.', '').'</b></div>
								<div class="td w10">
									yes
								</div>
							</div>'.$issue_html;
            }

            $total += $issue_total;

            if ($row['inventory_info']) {
                foreach(json_decode($row['inventory_info'], true) as $inv_id => $inv) {
                    $html['inventory'] .= '<div class="tr">
                            <div class="td">
                                    <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                '.($inv['items'] ?: 1).'
                            </div>
                            <div class="td w100">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }

            if ($row['services_info']) {
                foreach(json_decode($row['services_info'], true) as $inv_id => $inv) {
                    $html['services'] .= '<div class="tr">
                            <div class="td">
                                    '.$inv['name'].'
                            </div>
                            <div class="td w10">
                                '.($inv['items'] ?: 1).'
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }

            if ($row['purchases_info']) {
                foreach(json_decode($row['purchases_info'], true) as $inv_id => $inv) {
                    $html['purchases'] .= '<div class="tr">
                            <div class="td isem">
                                    <a href="/purchases/edit/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                1
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }

            if ($row['tradein_info']) {
                foreach(json_decode($row['tradein_info'], true) as $inv_id => $inv) {
                    $html['tradein'] .= '<div class="tr">
                            <div class="td isem">
                                    <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                1
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).'-'.number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                no
                            </div>
                        </div>';
                    $tradein += floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase']));
                }
            }
			
			if ($row['refund_info'] AND strlen($row['refund_info']) > 2) {
				$html['refund'] .= '<div class="tr">
                            <div class="td isem">
								<b>Refund for <a href="/invoices/view/'.$row['refund_invoice'].'" target="blank">Invoice #'.$row['refund_invoice'].'</a></b>
                            </div>
                            <div class="td w10">
                                
                            </div>
                            <div class="td w100 nPay">
                               
                            </div>
                            <div class="td w10">
                                
                            </div>
                        </div>';
                foreach(json_decode($row['refund_info'], true) as $inv_id => $inv) {
                    $html['refund'] .= '<div class="tr">
                            <div class="td isem">
								'.$inv['name'].'
                            </div>
                            <div class="td w10">
                                '.($inv['items'] ?: 1).'
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
					if ($inv['type'] == 'onsite')
						$onsite_total += floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }
			
            if ($row['addition_info']) {
                foreach(json_decode($row['addition_info'], true) as $adt_id => $adt) {
                    $html['additions'] .= '<div class="tr">
                            <div class="td">
                                    '.$adt['name'].'
                            </div>
                            <div class="td w10">
                                '.($adt['quantity'] ?: 1).'
                            </div>
                            <div class="td w100">
                                '.($config['currency'][$adt['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($adt['quantity'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $adt['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($adt['quantity'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $adt['price']));
                }
            }

			$total *= (100 - $row['store_discount_amount']) / 100;
			
            $invoices_html = '';
            $invoice_discount = [];
            if ($row['invoices']) {
                if ($invoices = db_multi_query('
					SELECT 
						i.*,
						sd.amount as store_discount_amount,
						os.name as onsite_name,
						os.price as onsite_price,
						os.description as onsite_description,
						uos_info.name as user_onsite_name
					FROM `'.DB_PREFIX.'_invoices` i 
					LEFT JOIN `'.DB_PREFIX.'_store_discounts` sd
						ON sd.id = i.store_discount
					LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
						ON uos.id = i.add_onsite
					LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
						ON uos_info.id = uos.onsite_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
						ON os.id = i.onsite_id
					WHERE i.id IN('.$row['invoices'].')
				', true)) {
                    foreach($invoices as $invoice) {
                        $invoice_total = 0;
                        $issue_total = 0;
                        //$onsite_total = 0;
                        $issue_html = '';
                        $invoices_html .= '<div class="tbl payInfo">
                                    <div class="tr">
                                        <div class="th">
                                            <a href="/invoices/view/'.$invoice['id'].'" target="_blank">Invoice #'.$invoice['id'].'</a>
                                        </div>
                                        <div class="th w10">
                                            Qty
                                        </div>
                                        <div class="th w100">
                                            Amount
                                        </div>
                                        <div class="th w10">
                                            Tax
                                        </div>
                                    </div>';

						if ($invoice['onsite_id']) {
							$invoices_html .= '<div class="tr" data-id="'.$invoice['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$invoice['onsite_id'].'">
								<div class="td">
									'.$invoice['onsite_name'].'
									<br><i>'.$invoice['onsite_description'].'</i>
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 onsite_price">
									'.$config['currency'][$invoice['currency']]['symbol'].$invoice['onsite_price'].'
								</div>
								<div class="td w10">
									no
								</div>
							</div>';
							$invoice_total += floatval($row['onsite_price']);
							$onsite_total += floatval($row['onsite_price']);
						}
						if ($invoice['add_onsite']) {
							 $invoices_html .= '<div class="tr" data-id="'.$invoice['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$invoice['add_onsite'].'">
								<div class="td">
									'.$invoice['user_onsite_name'].'(Additional time - '.$invoice['user_onsite_time'].')
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 onsite_add_price">
									'.$config['currency'][$invoice['currency']]['symbol'].$invoice['add_onsite_price'].'
								</div>
								<div class="td w10">
									no
								</div>
							</div>';
							$invoice_total += floatval($row['add_onsite_price']);
							$onsite_total += floatval($row['add_onsite_price']);
						}
			
                        if ($invoice['discount'])
                            $invoice_discount = array_values(json_decode(($invoice['discount'] ?: '{}'), true));

                        if ($issue = json_decode($invoice['issue_info'], true)) {                  
                                if ($issue['inventory']) {
                                    foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
                                        $issue_html .= '<div class="tr">
                                                <div class="td isItem">
                                                    <a href="/inventory/view/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
                                                </div>
                                                <div class="td w10">
                                                    1
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price'])), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                                    }
                                }

                                if ($issue['services']) {
                                    $upcharge = 0;
                                    if ($issue['upcharge']) {
										$upcharge = floatval(preg_replace('/[^0-9.-]/i', '', array_values($issue['upcharge'])[0]['price']));
										$service_len = count(array_filter($issue['services'], function($a) {
											if (floatval(preg_replace('/[^0-9.-]/i', '', $a['price'])) > 0)
												return $a;
										}));
										$upcharge /= $service_len;
									}

                                    foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                                        $price = floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                                        $issue_html .= '<div class="tr">
                                                <div class="td isItem">
                                                    '.$iss_inv['name'].'
                                                </div>
                                                <div class="td w10">
                                                    '.($iss_inv['quantity'] ?: '1').'
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += ($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1);
                                    }
                                }

                                if ($issue['purchases']) {
                                    foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
                                        $issue_html .= '<div class="tr">
                                                <div class="td isItem">
                                                    <a href="/purchases/edit/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
                                                </div>
                                                <div class="td w10">
                                                    1
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price'])), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                                    }
                                }

                                $invoices_html .= '<div class="tr">
                                            <div class="td">
                                                <b><a href="/issues/view/'.$invoice['issue_id'].'" target="_blank">Issue #'.$invoice['issue_id'].'</a></b>
                                            </div> 
                                            <div class="td w10"></div>
                                            <div class="td w100"><b>'.$config['currency'][$invoice['currency']]['symbol'].number_format($issue_total, 2, '.', '').'</b></div>
                                            <div class="td w10">
                                                yes
                                            </div>
                                        </div>'.$issue_html;
                        }

                        $invoice_total += $issue_total;
                        $invoice_html .= $issue_html;

                        if ($invoice['inventory_info']) {
                            foreach(json_decode($invoice['inventory_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td">
                                                <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
											'.($inv['items'] ?: 1).'
										</div>
										<div class="td w100">
											'.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
										</div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                            }
                        }
                        

                        if ($invoice['services_info']) {
                            foreach(json_decode($invoice['services_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td">
                                                '.$inv['name'].'
                                        </div>
                                        <div class="td w10">
                                            '.($inv['items'] ?: 1).'
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['purchases_info']) {
                            foreach(json_decode($invoice['purchases_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td isem">
                                                <a href="/purchases/edit/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
                                            1
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['tradein_info']) {
                            foreach(json_decode($invoice['tradein_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td isem">
                                                <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
                                            1
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).'-'.number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            no
                                        </div>
                                    </div>';
                                $tradein += floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase']));
                            }
                        }
                        $invoices_html .= '</div>';

                        if ($invoice['discount']) {
                            $invoice_discount = array_values(json_decode($invoice['discount'], true));
                            $invoices_html .= '<div class="tbl payInfo discount">
                                    <div class="tr">
                                        <div class="td">
                                            '.$invoice_discount[0]['name'].'
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                        <div class="td w100">
                                            -'.$invoice_discount[0]['percent'].'%
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                    </div>
                                </div>';
                        }
						
						if ($invoice['store_discount']) {
                            $invoices_html .= '<div class="tbl payInfo discount">
                                    <div class="tr">
                                        <div class="td">
                                            Discount code: '.$invoice['store_discount'].'
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                        <div class="td w100">
                                            -'.$invoice['store_discount_amount'].'%
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                    </div>
                                </div>';
								
							$invoice_total *= (100 - $invoice['store_discount_amount']) / 100;
                        }
						
                        $total += ($invoice_discount[0]['percent'] ? $invoice_total * (100 - $invoice_discount[0]['percent']) / 100 : $invoice_total);
                    }
                }
            }
            
			$tax = $row['purchace'] ? 0 : ($total - $onsite_total) * $row['object_tax'] / 100; //$onsite_total + $tradein_total
			/* if ($user['id'] == 17)
				echo $total.' '.$onsite_total; */
			
			$tax = $discount[0]['percent'] ? round(
				$tax * (100 - $discount[0]['percent']
			) / 100, 2) : $tax;
			
			if($row['tax_exempt'])
				$tax = 0;
			
			$total = $discount[0]['percent'] ? round($total * (
				100 - $discount[0]['percent']) / 100, 2
			) : $total;

            
			$history_html = '';
			if ($history = db_multi_query('
				SELECT 
					i.*,
					u.name as user_name,
					u.lastname as user_lastname
				FROM `'.DB_PREFIX.'_invoices_history` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = i.staff_id
				WHERE i.invoice_id = '.$id, true)) {
				$history_html = '<h3 class="trLog">Transaction log</h3><div class="tbl">
								<div class="tHead">
									<div class="th">Date</div>
									<div class="th">Amount</div>
									<div class="th">Type</div>
									<div class="th">Staff</div>
								</div>
								<div class="tBody">';
				foreach($history as $h) {
					$history_html .= '<div class="tr">
										<div class="td"><span class="thShort">Date: </span>'.$h['date'].'</div>
										<div class="td"><span class="thShort">Amount: </span>'.$config['currency'][$h['currency']]['symbol'].number_format($h['amount'], 2, '.', '').'</div>
										<div class="td"><span class="thShort">Type: </span>'.$h['type'].'</div>
										<div class="td"><span class="thShort">Staff: </span><a href="/users/view/'.$h['staff_id'].'" onclick="Page.get(this.href)">'.$h['user_name'].' '.$h['user_lastname'].'</a></div>
									</div>';
				}
				$history_html .= '</div></div>';
			}
			
			$delivery = '';
			if ($row['order_id']) {
				$delivery = '<div class="tr">
							<div class="td">
								'.$row['delivery'].'
							</div>
							<div class="td w10">
							</div>
							<div class="td w100">
								'.($config['currency'][$row['delivery_currency'] ?: $row['currency']]['symbol']).number_format($row['delivery_price'], 2, '.', '').'
							</div>
							<div class="td w10">
							</div>
						</div>';
			}
		// ---------------------------------------------------------------------------------//
			$options = '';
			if($discounts_inf = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices_discount` ORDER BY `id` LIMIT 0, 50', true)){
				foreach($discounts_inf as $disc_info){
					$options .= '<option value="'.$disc_info['id'].'"'.(
						($row['discount'] AND $disc_info['id'] == array_keys(json_decode($row['discount'], true))[0]) ? ' selected' : ''
					).'>'.$disc_info['name'].'</option>';
				}
			}
		
			if (!$row['order_id'] AND abs($total+$tax-$tradein - $row['total']) > 0.001)
                db_query('UPDATE `'.DB_PREFIX.'_invoices` SET total = \''.number_format($total+$tax-$tradein, 2, '.', '').'\' WHERE id = '.$id);

			if ($row['refund_info'] AND strlen($row['refund_info']) > 2 AND $row['refund_paid'] > 0) {
				$total = (-1) * $row['refund_paid'];
				$tax = 0; 
				$tradein = 0;
			}
			$due = $total+$tax-$tradein - $row['paid'];
			if ($route[1] == 'view') {
				tpl_set('invoices/view', [
					'id' => $id,
					'currency' => ($config['currency'][$row['currency']]['symbol'] ?: '$'),
					'total' => sprintf("%0.2f", ($row['order_id'] ? $row['total'] : ($total+$tax-$tradein))),
					'subtotal' => number_format(($row['order_id'] ? ($row['total'] - $row['tax']) : ($total-$tradein)), '2', '.', ''),
					'paid' => number_format($row['paid'], '2', '.', ''),
					'tax' => number_format(($row['order_id'] ? $row['tax'] : $tax), '2', '.', ''),
					'tax-exempt' => $row['tax_exempt'],
					'due' => sprintf("%0.2f", ($row['order_id'] ? ($row['total'] - $row['paid']) : (abs($due) < 0.01 ? 0 : $due))),
					'customer-id' => $row['customer_id'],
					'customer-name' => $row['customer_name'],
					'customer-lastname' => $row['customer_lastname'],
					'customer-address' => $row['customer_address'],
					'staff-id' => $row['staff_id'],
					'staff-name' => $row['staff_name'],
					'staff-lastname' => $row['staff_lastname'],
					'inventory' => $html['inventory'].$html['services'],
					'purchases' => $html['purchases'],
					'additions' => $html['additions'],
					'tradein' => $html['tradein'],
					'refund' => $html['refund'],
					'object' => $row['object_id'],
					'discounts' => $options,
					'discount-name' => $discount[0]['name'],
					'discount-percent' => $discount[0]['percent'],
					'purchace' => $row['purchace'],
					'date' => date('m-d-Y H:i:s', strtotime($row['date'])),
					'invoices' => $invoices_html,
					'issues' => $issue_mhtml,
					'history' => $history_html,
					'onsite' => $onsite,
					'delivery' => $delivery,
					'refund_comment' => $row['refund_comment'],
					'store-discount-amount' => $row['store_discount_amount'],
					'store-discount' => $row['store_discount']
				], [
					'estimate' => $row['estimate'],
					'tax-exempt' => $row['tax_exempt'],
					'add' => !($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips']))),
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'conducted' => ($row['conducted'] != 0),
					'refund_confirm' => (strlen($row['refund_info']) > 2 AND $row['refund'] == 0),
					'user_refund_confirm' => $user['confirm_refund'] AND $row['refund'] == 0,
					'refund_request' => $row['refund'] == 0,
					'discount' => $row['discount'],
					'discount-confirmed' => $row['discount_confirmed'],
					'edit' => $route[1] == 'edit',
					'paid' => $row['paid'],
					'owner' => strrpos($user['group_ids'], '1') !== false,
					'purchace' => $row['purchace'],
					'can-edit' => (($row['conducted'] AND $user['edit_paid_invoices']) AND $row['transaction'] OR (!$row['conducted'] AND $user['edit_invoices'])),
					'has_purchase' => $has_purchase == 1,
					'refund' => strlen($row['refund_info']) > 2,
					'can_pay' => ((strlen($row['refund_info']) <= 2 OR (strlen($row['refund_info']) > 2 AND $row['refund'] == 1)) AND (!$row['discount'] OR ($row['discount'] AND $row['discount_confirmed']))),
					'issue' => $row['issue_id'],
					'refund-invoice' => $row['refund_id'] > 0,
					'order' => $row['order_id'] > 0,
					'confirm-discount' => (!$row['discount_confirmed'] AND $user['confirm_discount']),
					'store-discount' => $row['store_discount'],
					'del_invoice' => ((!$row['conducted'] AND !$row['paid']) AND $user['del_invoices']),
					'deleted' => $row['del']
				], 'content');
			} else {
				if ($row['issue_id']) {
					$issues = db_multi_query('SELECT
						tb1.id as issue_id,
						tb1.doit,
						tb1.quote,
						tb1.description,
						tb1.purchase_prices,
						tb1.service_ids,
						tb1.upcharge_id,
						IF(tb2.name = \'\', tb2.model, tb2.name) as name,
							tb2.price,
							tb2.purchase_price,
							tb2.type,
							tb2.id as inv_id,
							tb2.quantity, 
							tb2.tradein, 
								tb3.name as catname,
									tb4.id as pur_id,
									tb4.name as pur_name,
									tb4.sale as pur_or_price,
									REGEXP_REPLACE(tb1.purchase_prices, CONCAT(\'{(.*?)"\', tb4.id, \'":"(.*?)",(.*?)}\'), \'\\\2\') as pur_price,
										m.name as model_name,
											up.price as inv_service
							FROM `'.DB_PREFIX.'_issues` tb1
						LEFT JOIN `'.DB_PREFIX.'_inventory` tb2
							ON FIND_IN_SET(tb2.id, CONCAT(tb1.inventory_ids, ",", REGEXP_REPLACE(tb1.service_ids,\'(.*?)_(.*?),\', \'\\\1,\')))
						LEFT JOIN `'.DB_PREFIX.'_inventory_upcharge` up
							ON up.id = tb1.upcharge_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb3
							ON tb2.category_id = tb3.id
						LEFT JOIN `'.DB_PREFIX.'_purchases` tb4
							ON FIND_IN_SET(tb4.id, tb1.purchase_ids)
						LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
							ON tb2.model_id = m.id
						WHERE tb1.id = '.$row['issue_id'].' LIMIT 0, 50'
					, true);
				}
				print '<style>
					body {
						font-size: 14px;
						font-family: sans-serif;
					}

					.inv {
						font-size: 28px;
					}

					.wid50 {
						width: 50%;
						float: left;
					}

					.aCenter {
						text-align: center;
					}

					.uTitle {
						margin: 20px;
					}

					.tbl {
						margin: 20px;
						display: table;
						width: -webkit-calc(100% - 40px);
						width: -moz-calc(100% - 40px);
						width: calc(100% - 40px);
					}

					.tr {
						display: table-row;
					}

					.th {
						display: table-cell;
						border-bottom: 2px solid #ddd;
						padding: 10px 10px!important;
						color: #777;
						font-weight: bold;
						font-size: 13px;
						vertical-align: middle;
					}

					.td {
						display: table-cell;
						padding: 8px 10px!important;
						color: #777;
						font-size: 13px;
					}

					.tbl a {
						color: #299CCE;
					}

					.w100 span.fa {
						font-size: 18px;
					}

					.tr:nth-child(even)>.td {
						background: #F7F8FA;
					}

					.td:last-child>a {
						margin: 0 5px;
						color: #769E26
					}

					.td:last-child {
						width: 120px;
					}

					.td:last-child>a:nth-child(3) {
						color: #299CCE;
					}

					.td:last-child>a:last-child {
						color: #CE1212;
					}

					.tr:hover>.td {
						background: #F3F7FF;
						color: #7F94BD;
					}

					.payTotalInfo {
						font-weight: bold;
						width: 300px;
						text-align: right;
						float: right;
					}

					.invInfo {
						display: table-cell;
						vertical-align: middle;
						padding: 0 10px;
						color: #555;
						text-decoration: none;
						font-size: 13px;
					}

					.sUser.head {
						height: 55px;
						font-weight: bold;
						background: #F5F6F9;
					}

					.sUser.head>div {
						border-bottom: 1px solid #EEF0F3;
						color: #858994;
						font-size: 13px;
					}

					.usLiHead {
						display: table;
						width: 100%;
					}

					.dClear:after {
						content: \' \';
						display: block;
						clear: both;
					}

					@media print {
						.more {
							page-break-after: always;
						}
					}
				</style>';
				print stripcslashes(str_ireplace([
					'{id}',
					'{logo}',
					'{name}',
					'{address}',
					'{email}',
					'{cellphone}',
					'{subtotal}',
					'{total}',
					'{tax}',
					'{paid}',
					'{due}',
					'{date}',
					'{invoices}',
					'{issues}',
					'{inventory}',
					'{purchases}',
					'{tradein}',
					'{discount-name}',
					'{discount-percent}',
					'{object-name}',
					'{invoice-barcode}',
					'{customer-barcode}',
					'{issue-barcode}',
					'{issue_dsc}',
					'{city}',
					'{zipcode}',
					'{type}',
					'{serial}',
					'{model}',
					'{quote}',
					'{store_cell}',
					'{store_name}',
					'{store_address}',
					'{opt_charger}',
					'{assigned}',
					'{issue_status}',
					'{onsite}',
					'{currency}'
				],[
					$row['id'],
					'<img src="//'.$_SERVER['HTTP_HOST'].'/templates/site/img/logo.png" style="max-width: 300px">',
					$row['customer_name'].' '.$row['customer_lastname'],
					$row['customer_address'],
					$row['customer_email'],
					$row['customer_phone'],
					number_format($total, '2', '.', ''),
					number_format(($total+$tax), '2', '.', ''),
					number_format($tax, '2', '.', ''),
					number_format($row['paid'], '2', '.', ''),
					number_format((abs($due) < 0.01 ? 0 : $due), '2', '.', ''),
					$row['date'],
					$invoices_html,
					$issue_mhtml,
					$html['inventory'].$html['services'].$html['additions'],
					$html['purchases'],
                    $html['tradein'],
					$discount[0]['name'],
					$discount[0]['percent'] ? '-'.$discount[0]['percent'].'%' : '',
					$row['object_name'],
					'<img src="data:image/png;base64,'.
						to_barcode('in '.str_pad(
							$id, 11, '0', STR_PAD_LEFT
							)
						)
					.'">',
					'<img src="data:image/png;base64,'.
						to_barcode('us '.str_pad(
							$row['customer_id'], 11, '0', STR_PAD_LEFT
							)
						)
					.'">',
					'<img src="data:image/png;base64,'.
						to_barcode('is '.str_pad(
							$row['issue_id'], 11, '0', STR_PAD_LEFT
							)
						)
					.'">',
					$issues[0]['description'],
					$row['city_name'],
					$row['zipcode'] ?: '',
					$row['type_name'] ?: '',
					$row['inv_serial'] ?: '',
					$row['inv_cat'].' '.$row['inv_model_name'].' '.($row['inv_model'] ?: ''),
					$config['currency'][$row['currency']]['symbol'].number_format($issues[0]['quote'], 2, '.', ''),
					$row['object_phone'],
					$row['object_name'],
					$row['object_address'],
					'',
					($row['staff_name'].' '.$row['staff_lastname'] ?: ''),
					($row['status_name'] ?: ''),
					$onsite,
					$config['currency'][$row['currency']]['symbol']
				], $config['device_form']));
				die;
			}
		}
	break;
	
	case 'send_mail':
		$id = intval($_POST['id']);

		$meta['title'] = 'Invoice';
		$pur_ids = [];
		$total = 0;
		$tradein = 0;
		$tax = 0;
		$has_purchase = 0;

		$invoices_arr = [];
        $onsite = '';
        $onsite_total = 0;
        $discount = [];
        $html = [];
        
		if($row = db_multi_query('
			SELECT
				i.*,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.address as customer_address,
					u.email as customer_email,
					u.phone as customer_phone,
					u.zipcode as zipcode,
                        o.tax as object_tax,
                        o.name as object_name,
                        o.address as object_address,
                        o.phone as object_phone,
                            c.city as city_name,
								os.name as onsite_name,
								os.price as onsite_price,
								uos_info.name as user_onsite_name,
								SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time
			FROM `'.DB_PREFIX.'_invoices` i
				INNER JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id				
				LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = i.object_id
				LEFT JOIN `'.DB_PREFIX.'_cities` c
			ON c.zip_code = u.zipcode
				LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
			ON uos.id = i.add_onsite
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
			ON uos_info.id = uos.onsite_id
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
			ON os.id = i.onsite_id
				WHERE i.id = '.$id
		)){
			if ($row['customer_email']) {
			if ($row['onsite_id']) {
$onsite .= '<tr data-id="'.$row['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$row['onsite_id'].'">
<td width="340">'.$row['onsite_name'].'</td><td width="100">1</td><td width="200">$ '.$row['onsite_price'].'</td><td width="100">no</td>
</tr>';
                $total += floatval($row['onsite_price']);
                $onsite_total += floatval($row['onsite_price']);
            }
            if ($row['add_onsite']) {
				$onsite .= '
<tr data-id="'.$row['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$row['add_onsite'].'">
<td width="340">'.$row['user_onsite_name'].'(Additional time - '.$row['user_onsite_time'].')</td><td width="100">1</td><td width="200">$ '.$row['add_onsite_price'].'</td><td width="100">no</td>
</tr>';
                $total += floatval($row['add_onsite_price']);
                $onsite_total += floatval($row['add_onsite_price']);
            }
			
            if ($row['discount'])
                $discount = array_values(json_decode(($row['discount'] ?: '{}'), true));

            $issue_mhtml = '';
			
            if ($issue = json_decode($row['issue_info'], true)) {  
                    $issue_total = 0;
                    $issue_html = '';

                    if ($issue['inventory']) {
                        foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
$issue_html .= '
<tr>
<td width="340">'.$iss_inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

                    if ($issue['services']) {
						
                        $upcharge = 0;
                        if ($issue['upcharge'])
                            $upcharge = floatval(preg_replace('/[^0-9.]/i', '', array_values($issue['upcharge'])[0]['price']));

                        foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                            $price = floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
$issue_html .= '
<tr>
<td width="340">'.$iss_inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(($price > 0 ? $price + $upcharge : $price), 2, '.', '').'</td><td width="100">yes</td></tr>';
                            $issue_total += ($price > 0 ? $price + $upcharge : $price);
                        }
                    }

                    if ($issue['purchases']) {
                        foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
$issue_html .= '
<tr>
<td width="340">'.$iss_inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

$issue_mhtml .= '<tr>
<td width="440">Issue #'.$row['issue_id'].'</td><td width="200">$'.number_format($issue_total, 2, '.', '').'</td><td width="100">yes</td></tr>'.$issue_html;
            }

            $total += $issue_total;

            if ($row['inventory_info']) {
                foreach(json_decode($row['inventory_info'], true) as $inv_id => $inv) {
$html['inventory'] .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">'.($inv['items'] ?: 1).'</td><td width="200">$'.number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                }
            }

            if ($row['services_info'] AND strlen($row['services_info']) > 2) {
                foreach(json_decode($row['services_info'], true) as $inv_id => $inv) {
$html['services'] .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                    $total += floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                }
            }

            if ($row['purchases_info']) {
                foreach(json_decode($row['purchases_info'], true) as $inv_id => $inv) {
$html['purchases'] .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                    $total += floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                }
            }

            if ($row['tradein_info']) {
                foreach(json_decode($row['tradein_info'], true) as $inv_id => $inv) {
$html['tradein'] .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">1</td><td width="200">$-'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['purchase'])), 2, '.', '').'</td><td width="100">no</td></tr>';
                    $tradein += floatval(preg_replace('/[^0-9.]/i', '', $inv['purchase']));
                }
            }
			
			if ($row['refund_info'] AND strlen($row['refund_info']) > 2) {
                foreach(json_decode($row['refund_info'], true) as $inv_id => $inv) {
$html['refund'] .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">'.($inv['items'] ?: 1).'</td><td width="200">$'.number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">no</td></tr>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                }
            }

            $invoices_html = '';
            $invoice_discount = [];
            if ($row['invoices']) {
                if ($invoices = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices` WHERE id IN('.$row['invoices'].')', true)) {
                    foreach($invoices as $invoice) {
                        $invoice_total = 0;
                        $issue_total = 0;
                        $issue_html = '';
$invoices_html .= '<table border="1">
<tr>
<td width="340">Invoice #'.$invoice['id'].'</td><td width="100">Qty</td><td width="200">Amount</td><td width="100">Tax</td></tr>';

                        if ($invoice['discount'])
                            $invoice_discount = array_values(json_decode(($invoice['discount'] ?: '{}'), true));

                        if ($issue = json_decode($invoice['issue_info'], true)) {                  
                                if ($issue['inventory']) {
                        foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
$issue_html .= '
<tr>
<td width="340">'.$iss_inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

                    if ($issue['services']) {
						
                        $upcharge = 0;
                        if ($issue['upcharge'])
                            $upcharge = floatval(preg_replace('/[^0-9.]/i', '', array_values($issue['upcharge'])[0]['price']));

                        foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                            $price = floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
$issue_html .= '
<tr>
<td width="340">'.$iss_inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(($price > 0 ? $price + $upcharge : $price), 2, '.', '').'</td><td width="100">yes</td></tr>';
                            $issue_total += ($price > 0 ? $price + $upcharge : $price);
                        }
                    }

                    if ($issue['purchases']) {
                        foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
$issue_html .= '
<tr>
<td width="340">'.$iss_inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

$invoices_html .= '
<tr>
<td width="440">Issue #'.$invoice['issue_id'].'</td><td width="200">$'.number_format($issue_total, 2, '.', '').'</td><td width="100">yes</td></tr>'.$issue_html;

						}

                        $invoice_total += $issue_total;
                        $invoice_html .= $issue_html;

                        if ($invoice['inventory_info']) {
                            foreach(json_decode($invoice['inventory_info'], true) as $inv_id => $inv) {
$invoices_html .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">'.($inv['items'] ?: 1).'</td><td width="200">$'.number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                                $invoice_total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                            }
                        }
                        

                        if ($invoice['services_info']) {
                            foreach(json_decode($invoice['services_info'], true) as $inv_id => $inv) {
$invoices_html .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                                $invoice_total += floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['purchases_info']) {
                            foreach(json_decode($invoice['purchases_info'], true) as $inv_id => $inv) {
$invoices_html .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                                $invoice_total += floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['tradein_info']) {
                            foreach(json_decode($invoice['tradein_info'], true) as $inv_id => $inv) {
$invoices_html .= '
<tr>
<td width="340">'.$inv['name'].'</td><td width="100">1</td><td width="200">$'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'</td><td width="100">yes</td></tr>';
                                $tradein += floatval(preg_replace('/[^0-9.]/i', '', $inv['purchase']));
                            }
                        }
                        $invoices_html .= '</table>';

                        if ($invoice['discount']) {
                            $invoice_discount = array_values(json_decode($invoice['discount'], true));
$invoices_html .= '<br><table border="1">
<tr>
<td width="440">'.$invoice_discount[0]['name'].'</td><td width="300">-'.$invoice_discount[0]['percent'].'%</td></tr>
</table>';
                        }

                        $total += ($invoice_discount[0]['percent'] ? $invoice_total * (100 - $invoice_discount[0]['percent']) / 100 : $invoice_total);
                        
                    }
                }
            }
            
			if ($row['refund_info'] AND strlen($row['refund_info']) > 2)
				$tax = 0;
			else
				$tax = $row['purchace'] ? 0 : ($total - $onsite_total) * $row['object_tax'] / 100; //$onsite_total + $tradein_total
			
			$tax = $discount[0]['percent'] ? round(
				$tax * (100 - $discount[0]['percent']
			) / 100, 2) : $tax;
			
			$total = $discount[0]['percent'] ? round($total * (
				100 - $discount[0]['percent']) / 100, 2
			) : $total;

			$due = $total+$tax-$tradein - $row['paid'];
			
$text = '<h1 align="center">Invoice</h1>
<h2 align="center">'.$row['date'].'</h2>
<h3 align="center">'.$row['object_phone'].'</h3>
<br><img src="http://'.$_SERVER['HTTP_HOST'].'/templates/site/img/logo.png" width="200" height="100" x="350" y="30" style="max-width: 300px">
<br><b>'.$row['customer_name'].' '.$row['customer_lastname'].'</b>
<br>'.$row['customer_address'].'
<br><br>
<br>'.$invoices_html.'
<br><br><br>
<table border="1"><tr>
<td width="340">Item</td><td width="100">Qty</td><td width="200">Amount</td><td width="100">Tax</td></tr>
'.$onsite.$issue_mhtml.$html['inventory'].$html['services'].$html['tradein'].$html['refund'].'
<tr>
<td>'.$discount[0]['name'].'</td><td width="50"></td><td width="100">'.($discount[0]['percent'] ? '-'.$discount[0]['percent'].'%' : '').'</td><td width="50"></td>
</tr></table>
<br><br>
<table border="1"><tr><td width="200" align="right">Subtotal</td><td width="200" align="right">$'.number_format($total, '2', '.', '').'</td></tr>
<tr><td width="200" align="right">Tax</td><td width="200" align="right">$'.number_format($tax, '2', '.', '').'</td></tr>
<tr><td width="200" align="right">Total</td><td width="200" align="right">$'.number_format(($total+$tax), '2', '.', '').'</td></tr>
<tr><td width="200" align="right">Paid</td><td width="200" align="right">$'.number_format($row['paid'], '2', '.', '').'</td></tr>
<tr><td width="200" align="right">Due</td><td width="200" align="right">$'.number_format((abs($due) < 0.01 ? 0 : $due), '2', '.', '').'</td></tr></table>';
			
			require('app/pdf_html.php');
			
			// path
			$dir = ROOT_DIR.'/uploads/pdfs/invoices/';
			
			// Is not dir
			if(!is_dir($dir.$id)){
				@mkdir($dir.$id, 0777);
				@chmod($dir.$id, 0777);
			}
			
			$dir = $dir.$id.'/';
			$name = md5(md5(SOLT).md5($id));
			
			$pdf=new createPDF(str_replace('/\s\s+/', ' ', $text), 'Invoice #'.$id, '', '',  time());
			$pdf->run($dir.$name.'.pdf', 'F', 1);
			
			// Headers
			$header  = 'MIME-Version: 1.0'."\r\n";
			$header .= 'Content-type:text/html;charset=iso-8859-1'."\r\n";
			$header .= 'To: '.$row['customer_email']. "\r\n";
			$header .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

			// Send
			if (mail($row['customer_email'], 'Your Company. Invoice #'.$id, '<!DOCTYPE html>
				<html lang="en">
				<head>
					<meta charset="UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<title>Your Company. Invoice #'.$id.'</title>
				</head>
				<body style="background: #f6f6f6; text-align: center;">
					<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
						<div style="margin: -20px -20px 0; padding: 20px;">
							<a href="http://yoursite.com/">
								<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 300px; margin: 25px 0;">
							</a>
						</div>
						<div style="padding: 0 30px 30px;">
							<p>Invoice #'.$id.' from Your Company</b>.</p>
							<p>Please, follow the link for downloading:</p>
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
								Link: <a href="http://yoursite.com'.'/uploads/pdfs/invoices/'.$id.'/'.$name.'.pdf'.'" style="color: #0e92d4;">Click here</a>
							</div>
						</div>
					</div>
				</body>
				</html>', $header))
				die('OK');
		} else 
			die('no_email-'.$row['customer_name'].' '.$row['customer_lastname']);
		} 
	break;
	
	/*
	*  Add/edit invoices
	*/
	case 'add':
	case 'edit':
	case 'make_refund':
	
	if($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips']))){
		tpl_set('forbidden', [
			'text' => 'You have no access to do this'
		], [
		], 'content');
	} else {
		// Data
		$id = intval($route[2]);
		$meta['title'] = 'Invoice';
		$pur_ids = [];
		$total = 0;
		$tradein = 0;
		$tax = 0;
		$has_purchase = 0;

		$invoices_arr = [];
        $onsite = '';
        $onsite_total = 0;
        $discount = [];
        $html = [];
        
		if($row = db_multi_query('
			SELECT
				i.*,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.address as customer_address,
					u.email as customer_email,
					u.phone as customer_phone,
					u.zipcode as zipcode,
                        o.tax as object_tax,
                        o.name as object_name,
                        o.address as object_address,
                        o.phone as object_phone,
                            c.city as city_name,
								os.name as onsite_name,
								os.price as onsite_price,
								uos_info.name as user_onsite_name,
								SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time
			FROM `'.DB_PREFIX.'_invoices` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id				
				LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = i.object_id
				LEFT JOIN `'.DB_PREFIX.'_cities` c
			ON c.zip_code = u.zipcode
				LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
			ON uos.id = i.add_onsite
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
			ON uos_info.id = uos.onsite_id
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
			ON os.id = i.onsite_id
				WHERE i.id = '.$id
		)){
            if ($row['discount'])
                $discount = array_values(json_decode(($row['discount'] ?: '{}'), true));

			if ($row['onsite_id']) {
                $onsite .= '<div class="tr" data-id="'.$row['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$row['onsite_id'].'">
                    <div class="td">
                        '.(
						$route[1] == 'make_refund' ? 
							'<input type="checkbox" name="onsite_'.$row['onsite_id'].'">' :
							''
						).$row['onsite_name'].'
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_price">
                        '.$config['currency'][$row['currency']]['symbol'].$row['onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['onsite_price']);
                $onsite_total += floatval($row['onsite_price']);
            }
            if ($row['add_onsite']) {
                 $onsite .= '<div class="tr" data-id="'.$row['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$row['add_onsite'].'">
                    <div class="td">
                        '.(
						$route[1] == 'make_refund' ? 
							'<input type="checkbox" name="onsite_'.$row['add_onsite'].'">' :
							''
						).$row['user_onsite_name'].'(Additional time - '.$row['user_onsite_time'].')
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_add_price">
                        '.$config['currency'][$row['currency']]['symbol'].$row['add_onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['add_onsite_price']);
                $onsite_total += floatval($row['add_onsite_price']);
            }
			
            $issue_mhtml = '';
            if ($issue = json_decode($row['issue_info'], true)) {                  
                    $issue_total = 0;
                    $issue_html = '';

                    if ($issue['inventory']) {
                        foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
                            $issue_html .= '<div class="tr" data-currency="'.($iss_inv['currency'] ?: $row['currency']).'">
									<div class="td isItem">'.(
										$route[1] == 'make_refund' ? 
											'<input type="checkbox" name="inventory_'.$iss_inv_id.'"'.((text_filter($_GET['type']) == 'inventory' AND intval($_GET['id']) == $iss_inv_id) ? ' checked' : '').'>' :
											''
										).'<a href="/inventory/view/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

                    if ($issue['services']) {
                        $upcharge = 0;
                        if ($issue['upcharge']) {
                            $upcharge = floatval(preg_replace('/[^0-9.]/i', '', array_values($issue['upcharge'])[0]['price']));
							$service_len = count(array_filter($issue['services'], function($a) {
								if (floatval(preg_replace('/[^0-9.]/i', '', $a['price'])) > 0)
									return $a;
							}));
							$upcharge /= $service_len;
						}

                        foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                            $price = floatval(str_replace('/[^0-9.]/i', '', $iss_inv['price']));
                            $issue_html .= '<div class="tr" data-currency="'.($iss_inv['currency'] ?: $row['currency']).'">
									<div class="td isItem">'.(
										$route[1] == 'make_refund' ? 
											'<input type="checkbox" name="service_'.$iss_inv_id.'">' :
											''
										).$iss_inv['name'].'
									</div>
									<div class="td w10">
										'.($iss_inv['quantity'] ?: '1').'
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += ($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1);
                        }
                    }

                    if ($issue['purchases']) {
                        foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
                            $issue_html .= '<div class="tr" data-currency="'.($iss_inv['currency'] ?: $row['currency']).'">
									<div class="td isItem">'.(
										$route[1] == 'make_refund' ? 
											'<input type="checkbox" name="purchase_'.$iss_inv_id.'"'.((text_filter($_GET['type']) == 'purchase' AND intval($_GET['id']) == $iss_inv_id) ? ' checked' : '').'>' :
											''
										).'<a href="/purchases/edit/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

                    $issue_mhtml .= '<div class="tr">
								<div class="td">
									<b><a href="/issues/view/'.$row['issue_id'].'" target="_blank">Issue #'.$row['issue_id'].'</a></b>
								</div> 
								<div class="td w10"></div>
								<div class="td w100"><b>'.$config['currency'][$row['currency']]['symbol'].number_format($issue_total, 2, '.', '').'</b></div>
								<div class="td w10">
									yes
								</div>
							</div>'.$issue_html;
            }

            $total += $issue_total;

            if ($row['inventory_info']) {
                foreach(json_decode($row['inventory_info'], true) as $inv_id => $inv) {
                    $html['inventory'] .= '<div class="tr" id="tr_stock_'.$inv_id.'" data-type="stock" data-id="'.$inv_id.'" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                            <div class="td">'.(
								$route[1] == 'make_refund' ? 
								'<input type="checkbox" name="inventory_'.$inv_id.'">' :
                                '<span class="fa fa-times del" onclick="invoices.delInvItem(this, \'stock\', \''.$inv_id.'\')"></span>'
                                ).'<a href="/inventory/view/'.$inv_id.'" target="_blank" class="iname">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10 qty">
								'.(
								$route[1] == 'make_refund' ? 
								($inv['items'] ?: 1) :
                                '<input type="number" name="quantity" class="quickEdit" max="'.($route[1] == 'make_refund' ? ($inv['items'] ?: 1) : ($inv['quantity'] ?: 1)).'" min="1" value="'.($inv['items'] ?: 1).'" onchange="changeQuantity(this);" onkeyup="changeQuantity(this);">'
                                ).'
                            </div>
                            <div class="td w100">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                }
            }

            if ($row['services_info'] AND strlen($row['services_info']) > 2) {
                foreach(json_decode($row['services_info'], true) as $inv_id => $inv) {
                    $html['services'] .= '<div class="tr" id="tr_service_'.$inv_id.'" data-type="service" data-id="'.$inv_id.'" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                            <div class="td">'.(
								$route[1] == 'make_refund' ? 
								'<input type="checkbox" name="service_'.$inv_id.'">' :
                                '<span class="fa fa-times del" onclick="invoices.delInvItem(this, \'service\', \''.$inv_id.'\')"></span>'
                                ).'<span class="iname">'.$inv['name'].'</span>
                            </div>
                            <div class="td w10">
                                '.(
								$route[1] == 'make_refund' ? 
								($inv['items'] ?: 1) :
                                '<input type="number" name="quantity" class="quickEdit" max="'.($route[1] == 'make_refund' ? ($inv['items'] ?: 1) : ($inv['quantity'] ?: 1)).'" min="1" value="'.($inv['items'] ?: 1).'" onchange="changeQuantity(this);" onkeyup="changeQuantity(this);">'
                                ).'
                            </div>
                            <div class="td w100">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                }
            }

            if ($row['purchases_info']) {
                foreach(json_decode($row['purchases_info'], true) as $inv_id => $inv) {
                    $html['purchases'] .= '<div class="tr" id="tr_purchase_'.$inv_id.'" data-type="purchase" data-id="'.$inv_id.'" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                            <div class="td isem">'.(
								$route[1] == 'make_refund' ? 
								'<input type="checkbox" name="purchase_'.$inv_id.'">' :
                                '<span class="fa fa-times del" onclick="invoices.delInvItem(this, \'purchase\', \''.$inv_id.'\')"></span>'
                                ).'<a href="/purchases/edit/'.$inv_id.'" target="_blank" class="iname">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                1
                            </div>
                            <div class="td w100" data-purchase="'.$inv['purchase'].'">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                }
            }

            if ($row['tradein_info']) {
                foreach(json_decode($row['tradein_info'], true) as $inv_id => $inv) {
                    $html['tradein'] .= '<div class="tr" id="tr_tradein_'.$inv_id.'" data-type="tradein" data-id="'.$inv_id.'" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                            <div class="td isem">'.(
								$route[1] == 'make_refund' ? 
								'<input type="checkbox" name="tradein_'.$inv_id.'">' :
                                '<span class="fa fa-times del" onclick="invoices.delInvItem(this, \'tradein\', \''.$inv_id.'\')"></span>'
                                ).'<a href="/inventory/view/'.$inv_id.'" target="_blank" class="iname">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                1
                            </div>
                            <div class="td w100 nPay" price="'.$inv['price'].'">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).'-'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['purchase'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                no
                            </div>
                        </div>';
                    $tradein += floatval(preg_replace('/[^0-9.]/i', '', $inv['purchase']));
                }
            }
			
            if ($row['addition_info']) {
                foreach(json_decode($row['addition_info'], true) as $adt_id => $adt) {
                    $html['additions'] .= '<div class="tr" id="tr_addition_'.$adt_id.'" data-type="addition" data-id="'.$adt_id.'" data-currency="'.($adt['currency'] ?: $row['currency']).'">
                            <div class="td">'.(
								$route[1] == 'make_refund' ? 
								'<input type="checkbox" name="addition_'.$adt_id.'">' :
                                '<span class="fa fa-times del" onclick="invoices.delInvItem(this, \'addition\', \''.$adt_id.'\')"></span>'
                                ).'<span class="iname">'.$adt['name'].'</span>
                            </div>
                            <div class="td w10 qty">
								'.(
								$route[1] == 'make_refund' ? 
								($adt['quantity'] ?: 1) :
                                '<input type="number" name="quantity" class="quickEdit" max="'.($route[1] == 'make_refund' ? ($adt['quantity'] ?: 1) : ($adt['quantity'] ?: 1)).'" min="1" value="'.($adt['quantity'] ?: 1).'" onchange="changeQuantity(this);" onkeyup="changeQuantity(this);">'
                                ).'
                            </div>
                            <div class="td w100">
                                '.($config['currency'][$adt['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($adt['quantity'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $adt['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($adt['quantity'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $adt['price']));
                }
            }

            $invoices_html = '';
            $invoice_discount = [];
            if ($row['invoices']) {
                if ($invoices = db_multi_query('SELECT 
						i.*,
						sd.amount as store_discount_amount,
						os.name as onsite_name,
						os.price as onsite_price,
						os.description as onsite_description,
						uos_info.name as user_onsite_name
					FROM `'.DB_PREFIX.'_invoices` i 
					LEFT JOIN `'.DB_PREFIX.'_store_discounts` sd
						ON sd.id = i.store_discount
					LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
						ON uos.id = i.add_onsite
					LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
						ON uos_info.id = uos.onsite_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
						ON os.id = i.onsite_id
					WHERE i.id IN('.$row['invoices'].')', true)) {
                    foreach($invoices as $invoice) {
                        $invoice_total = 0;
                        $issue_total = 0;
                        $issue_html = '';
                        $invoices_html .= '<div class="tbl payInfo inTbl" data-id="'.$invoice['id'].'">
                                    <div class="tr">
                                        <div class="th">
                                            <span class="fa fa-times del" onclick="invoices.delInInvoice(this, '.$id.')"></span>
                                            <a href="/invoices/view/'.$invoice['id'].'" target="_blank">Invoice #'.$invoice['id'].'</a>
                                        </div>
                                        <div class="th w10">
                                            Qty
                                        </div>
                                        <div class="th w100">
                                            Amount
                                        </div>
                                        <div class="th w10">
                                            Tax
                                        </div>
                                    </div>';
									
						if ($invoice['onsite_id']) {
							$invoices_html .= '<div class="tr" data-id="'.$invoice['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$invoice['onsite_id'].'">
								<div class="td">
									'.$invoice['onsite_name'].'
									<br><i>'.$invoice['onsite_description'].'</i>
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 onsite_price">
									'.$config['currency'][$invoice['currency']]['symbol'].$invoice['onsite_price'].'
								</div>
								<div class="td w10">
									no
								</div>
							</div>';
							$invoice_total += floatval($row['onsite_price']);
							$onsite_total += floatval($row['onsite_price']);
						}
						if ($invoice['add_onsite']) {
							 $invoices_html .= '<div class="tr" data-id="'.$invoice['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$invoice['add_onsite'].'">
								<div class="td">
									'.$invoice['user_onsite_name'].'(Additional time - '.$invoice['user_onsite_time'].')
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 onsite_add_price">
									'.$config['currency'][$invoice['currency']]['symbol'].$invoice['add_onsite_price'].'
								</div>
								<div class="td w10">
									no
								</div>
							</div>';
							$invoice_total += floatval($row['add_onsite_price']);
							$onsite_total += floatval($row['add_onsite_price']);
						}

                        if ($invoice['discount'])
                            $invoice_discount = array_values(json_decode(($invoice['discount'] ?: '{}'), true));

                        if ($issue = json_decode($invoice['issue_info'], true)) {                  
                                if ($issue['inventory']) {
                                    foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
                                        $issue_html .= '<div class="tr" data-currency="'.($iss_inv['currency'] ?: $row['currency']).'">
                                                <div class="td isItem">'.(
													$route[1] == 'make_refund' ? 
														'<input type="checkbox" name="inventory_'.$iss_inv_id.'">' :
														''
													).'<a href="/inventory/view/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
                                                </div>
                                                <div class="td w10">
                                                    1
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                                    }
                                }

                                if ($issue['services']) {
                                    $upcharge = 0;
                                    if ($issue['upcharge']) {
										$upcharge = floatval(preg_replace('/[^0-9.]/i', '', array_values($issue['upcharge'])[0]['price']));
										$service_len = count(array_filter($issue['services'], function($a) {
											if (floatval(preg_replace('/[^0-9.]/i', '', $a['price'])) > 0)
												return $a;
										}));
										$upcharge /= $service_len;
									}

                                    foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                                        $price = floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                                        $issue_html .= '<div class="tr" data-currency="'.($iss_inv['currency'] ?: $row['currency']).'">
                                                <div class="td isItem">'.(
													$route[1] == 'make_refund' ? 
														'<input type="checkbox" name="service_'.$iss_inv_id.'">' :
														''
													).$iss_inv['name'].'
                                                </div>
                                                <div class="td w10">
                                                    '.($iss_inv['quantity'] ?: '1').'
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += ($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1);
                                    }
                                }

                                if ($issue['purchases']) {
                                    foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
                                        $issue_html .= '<div class="tr" data-currency="'.($iss_inv['currency'] ?: $row['currency']).'">
                                                <div class="td isItem">'.(
													$route[1] == 'make_refund' ? 
														'<input type="checkbox" name="purchase_'.$iss_inv_id.'">' :
														''
													).'<a href="/purchases/edit/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
                                                </div>
                                                <div class="td w10">
                                                    1
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                                    }
                                }

                                $invoices_html .= '<div class="tr">
                                            <div class="td">
                                                <b><a href="/issues/view/'.$invoice['issue_id'].'" target="_blank">Issue #'.$invoice['issue_id'].'</a></b>
                                            </div> 
                                            <div class="td w10"></div>
                                            <div class="td w100"><b>'.($invoice['currency'] ?: $config['currency'][$row['currency']]['symbol']).number_format($issue_total, 2, '.', '').'</b></div>
                                            <div class="td w10">
                                                yes
                                            </div>
                                        </div>'.$issue_html;
                        }

                        $invoice_total += $issue_total;
                        $invoice_html .= $issue_html;

                        if ($invoice['inventory_info']) {
                            foreach(json_decode($invoice['inventory_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                                        <div class="td">'.(
										$route[1] == 'make_refund' ? 
											'<input type="checkbox" name="inventory_'.$inv_id.'">' :
											''
										).'<a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
											'.($inv['items'] ?: 1).'"
										</div>
										<div class="td w100">
											'.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'
										</div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                            }
                        }
                        

                        if ($invoice['services_info']) {
                            foreach(json_decode($invoice['services_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                                        <div class="td">'.(
											$route[1] == 'make_refund' ? 
											'<input type="checkbox" name="service_'.$inv_id.'">' :
											''
											).$inv['name'].'
                                        </div>
                                        <div class="td w10">
                                            '.($inv['items'] ?: 1).'"
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['purchases_info']) {
                            foreach(json_decode($invoice['purchases_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                                        <div class="td isem">'.(
											$route[1] == 'make_refund' ? 
												'<input type="checkbox" name="purchase_'.$inv_id.'">' :
												''
											).'<a href="/purchases/edit/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
                                            1
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['price'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += floatval(preg_replace('/[^0-9.]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['tradein_info']) {
                            foreach(json_decode($invoice['tradein_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr" data-currency="'.($inv['currency'] ?: $row['currency']).'">
                                        <div class="td isem">'.(
											$route[1] == 'make_refund' ? 
												'<input type="checkbox" name="tradein_'.$inv_id.'">' :
												''
											).'<a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
                                            1
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).'-'.number_format(floatval(preg_replace('/[^0-9.]/i', '', $inv['purchase'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            no
                                        </div>
                                    </div>';
                                $tradein += floatval(preg_replace('/[^0-9.]/i', '', $inv['purchase']));
                            }
                        }
                        $invoices_html .= '</div>';

                        if ($invoice['discount']) {
                            $invoice_discount = array_values(json_decode($invoice['discount'], true));
                            $invoices_html .= '<div class="tbl payInfo discount">
                                    <div class="tr">
                                        <div class="td">
                                            '.$invoice_discount[0]['name'].'
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                        <div class="td w100">
                                            -'.$invoice_discount[0]['percent'].'%
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                    </div>
                                </div>';
                        }

                        $total += ($invoice_discount[0]['percent'] ? $invoice_total * (100 - $invoice_discount[0]['percent']) / 100 : $invoice_total);
                        
                    }
                }
            }
            
			$tax = $row['purchace'] ? 0 : ($total - $onsite_total) * $row['object_tax'] / 100; //$onsite_total + $tradein_total
			
			$tax = $discount[0]['percent'] ? round(
				$tax * (100 - $discount[0]['percent']
			) / 100, 2) : $tax;
			
			$total = $discount[0]['percent'] ? round($total * (
				100 - $discount[0]['percent']) / 100, 2
			) : $total;
			
		}
		
		$options = '';
		if($discounts_inf = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices_discount` ORDER BY `id` LIMIT 0, 50', true)){
			foreach($discounts_inf as $disc_info){
				$options .= '<option value="'.$disc_info['id'].'"'.(
					($row['discount'] AND $disc_info['id'] == array_keys(json_decode($row['discount'], true))[0]) ? ' selected' : ''
				).'>'.$disc_info['name'].'</option>';
			}
		}

		if (intval($_GET['user']) AND $route[1] == 'add') {
			$usr = db_multi_query('SELECT name, lastname FROM `'.DB_PREFIX.'_users` WHERE id = '.intval($_GET['user']));
		}
		
		if($row['tax_exempt'])
			$tax = 0;

		$due = $total+$tax-$tradein-$row['paid'];
		if ($route[1] == 'add' OR $route[1] == 'make_refund' OR ($route[1] == 'edit' AND strlen($row['refund_info']) <= 2 AND (($row['conducted'] AND $user['edit_paid_invoices']) OR (!$row['conducted'] AND $user['edit_invoices']))) OR !$row['del']) {
			tpl_set('invoices/form', [
				'id' => $id,
				'customer-id' => $row['customer_id'] ?: intval($_GET['user']),
				'customer-name' => (intval($_GET['user']) AND $route[1] == 'add') ? $usr['name'] : $row['customer_name'],
				'customer-lastname' => (intval($_GET['user']) AND $route[1] == 'add') ? $usr['lastname'] : $row['customer_lastname'],
				'js-customer-name' => str_ireplace("'","\'", (intval($_GET['user']) AND $route[1] == 'add') ? $usr['name'] : $row['customer_name']),
				'js-customer-lastname' => str_ireplace("'","\'", (intval($_GET['user']) AND $route[1] == 'add') ? $usr['lastname'] : $row['customer_lastname']),
				'customer-address' => $row['customer_address'],
				'date' => $row['date'],
				'currency' => ($config['currency'][$row['currency']]['symbol'] ?: '$'),
				'total' => sprintf("%0.2f", ($total+$tax-$tradein)),
				'paid' => ($route[1] == 'make_refund') ? 0 : number_format($row['paid'], '2', '.', ''),
				'inventory' => $html['inventory'].$html['services'],
				'purchases' => $html['purchases'],
				'additions' => $html['additions'],
				'tradein' => $html['tradein'],
				'tax' => number_format($tax, '2', '.', ''),
				'tax-exempt' => $row['tax_exempt'],
				'object' => $row['object_id'],
				'onsite' => $onsite,
				'object-name' => $row['object_name'],
				'object-tax' => $row['object_tax'],
				'due' => ($route[1] == 'make_refund') ? $total-$tradein : sprintf("%0.2f", (abs($due) < 0.01 ? 0 : $due)),
				'subtotal' => number_format($total-$tradein, '2', '.', ''),
				'discounts' => $options,
				'discount-name' => $discount[0]['name'],
				'discount-percent' => $discount[0]['percent'],
				'invoices' => $invoices_html,
				'issues' => $issue_mhtml,
				'user-id' => intval($_GET['user']) ?: 0,
				'inventorys' => json_encode($row['object_name'] ? [
					$row['object_id'] => [
						'name' => $row['object_name']
					]] : []),
				'refund_comment' => $row['refund_comment']
			], [
				'store' => $store_id > 0,
				'edit' => ($route[1] == 'edit'),
				'add' => ($route[1] == 'add'),
				'refund' => ($route[1] == 'make_refund'),
				'discount' => $row['discount'],
				'quick_sell' => isset($_GET['quicksell']),
				'estimate' => isset($_GET['estimate']),
				'user' => (intval($_GET['user']) AND $route[1] == 'add')
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => 'You have no access to do this'
			], [
			], 'content');
		}	
	}
	break;
	
	/*
	*  Send discount
	*/
	case 'send_discount': 
		is_ajax() or die('Hacking attempt!');
		
		// Filters
		$id = intval($_POST['id']);
		
		// SQL SET
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_invoices_discount` SET
				name = \''.text_filter($_POST['name'], 255, false).'\',
				percent = \''.floatval($_POST['percent']).'\''.(
					(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR floatval($_POST['percent']) < 5) ? ', confirmed = 1' : ''
				).(
			$id ? ' WHERE id = '.$id : ''
		));
		
		$id = $id ? $id : intval(
			mysqli_insert_id($db_link)
		);
		
		print_r(json_encode([
			'id' => $id,
			'confirmed' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR floatval($_POST['percent']) < 5) ? 'confirmed' : 'not-confirmed'
		]));
		die;
	break;

	/*
	*  Send invoices
	*/
/* 	case 'send': 
		is_ajax() or die('Hacking attempt!');
		
		// Filters
		$id = intval($_POST['id']);
		
		
		// SQL SET
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_issues` SET
				staff_id = '.$user['id'].',
				description = \''.text_filter($_POST['descr'], 255, false).'\',
				inventory_ids = \''.ids_filter($_POST['inventory']).'\',
				service_ids = \''.$services.'\',
				inventory_id = '.intval($_POST['inventory_id']).'\''.(
			$id ? ' WHERE id = '.$id : ''
		));
		
		exit($id = $id ? $id : intval(
			mysqli_insert_id($db_link)
		));
	break; */
	
	/*
	* Delete invoice
	*/
	case 'del':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		//if($user['delete_invoice']){
			$oids = db_multi_query('SELECT purchases_info, paid, conducted FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
			
			if(($user['del_invoices'] AND (!intval($oids['paid']) AND !intval($oids['conducted']))) OR in_array(1, explode(',', $user['group_ids']))){
				$deleted = implode(',', array_keys(json_decode($oids['purchases_info'], true) ?: []));
				
				if ($deleted) {
					$purchases = db_multi_query('SELECT SUM(confirmed) as conf FROM `'.DB_PREFIX.'_purchases` WHERE id IN('.$deleted.')');

					if ($purchases['conf'])
						die('confirmed');
				
					db_query('
						UPDATE `'.DB_PREFIX.'_purchases` SET
							del = 1
						WHERE id IN ('.$deleted.')'
					);
				}
				
				db_query('INSERT INTO `'.DB_PREFIX.'_invoices_log` (invoice_id, total, paid, staff_id) SELECT id, total, paid, '.$user['id'].' FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
				db_query('DELETE FROM `'.DB_PREFIX.'_invoices_history` WHERE invoice_id = '.$id);
				if (in_array(1, explode(',', $user['group_ids'])))
					db_query('DELETE FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
				else 
					db_query('UPDATE `'.DB_PREFIX.'_invoices` SET del = 1 WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
		} else
			exit('no_acc');
	break;
	
	/*
	* Restore invoice
	*/
	case 'restore':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);

		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET del = 0 WHERE id = '.$id);
		if(mysqli_affected_rows($db_link)){
			exit('OK');
		} else
			exit('ERR');
	break;
	
	/*
	* Delete discount
	*/
	case 'confirm_discount':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices_discount` SET confirmed = 1 WHERE id = '.$id);
		if(mysqli_affected_rows($db_link)){
			exit('OK');
		} else
			exit('ERR');

	break;
	
	/*
	* All discounts
	*/
	case 'discounts': 
		$meta['title'] = 'Discounts';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_invoices_discount` '.(
			$query ? 'WHERE name LIKE \'%'.$query.'%\' ' : ''
		).'ORDER BY `id` LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('invoices/discount/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'percent' => $row['percent'],
					'confirmed' => $row['confirmed'] == 1 ? 'confirmed' : 'not-confirmed'
				], [
					'edit' => true,
					'confirmed' => $row['confirmed'] == 1,
					'add' => true,
				], 'discounts');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['discounts'],
			]));
		}
		tpl_set('invoices/discount/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'discounts' => $tpl_content['discounts']
		], [], 'content');
	break;
	
	/*
	* Print form
	*/
	case 'form':
	case 'email_form':
		$meta['title'] = 'Form';
		tpl_set('invoices/printForm', [
			'content' => stripslashes($config[$route[1] == 'email_form' ? 'invoice_email' : 'device_form'])
		], [
			'email_form' => $route[1] == 'email_form'
		], 'content');
	break;
	
	/*
	* All invoices
	*/
	case 'history':
		$meta['title'] = 'Invoice history';
		$query = text_filter($_REQUEST['query'], 255, false);
		$type = text_filter($_REQUEST['type'], 10, false);
		$status = text_filter($_REQUEST['status'], 10, false);
		$page = intval($_REQUEST['page']);
		$object = intval($_REQUEST['object']);
		$customer = intval($_REQUEST['staff']);
		
		$date_start = text_filter($_REQUEST['date_start'], 30, true);
		$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
		
		if($date = text_filter($_REQUEST['date'], 30, true)){
			$date_start = $date;
			$date_finish = $date;
		}
		
		$count = 10;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				h.*,
				i.customer_id,
				i.conducted,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.phone as customer_phone,
						s.name as staff_name,
						s.lastname as staff_lastname
			FROM `'.DB_PREFIX.'_invoices_history` h
			INNER JOIN `'.DB_PREFIX.'_invoices` i
				ON h.invoice_id = i.id
			INNER JOIN `'.DB_PREFIX.'_users` u
				ON i.customer_id = u.id
			LEFT JOIN `'.DB_PREFIX.'_users` s
				ON h.staff_id = s.id
			WHERE 1 '.(
			$query ? 'AND MATCH(
					u.name, u.lastname, u.email, u.phone
				) AGAINST (
					\'*'.$query.'*\' IN BOOLEAN MODE
				)  ' : ''
		).(
			$status === 'paid' ? 'AND i.conducted = 1 ' : (
				$status === 'unpaid' ? 'AND i.conducted = 0 ' : (
					$status === 'partial' ? 'AND i.conducted = 0 AND i.paid > 0 ' : ''
				)
			)
		).(
			$type ? 'AND h.type '.(
				$type[0] == '-' ? '!' : ''
			).'= \''.str_ireplace('-','', $type).'\' ' : ''
		).(
			$object ? 'AND i.object_id = '.$object.' ' : ''
		).(
			$customer ? 'AND i.customer_id = '.$customer.' ' : ''
		).(
			($date_start AND $date_finish) ? ' AND h.date >= CAST(\''.$date_start.'\' AS DATE) AND h.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME) ' : ''
		).'ORDER BY i.id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('invoices/item_history', [
					'id' => $row['id'],
					'amount' => $row['amount'],
					'type' => $row['type'],
					'invoice-id' => $row['invoice_id'],
					'date' => date('d.m.y', strtotime($row['date'])),
					'customer-id' => $row['customer_id'],
					'customer-name' => $row['customer_name'],
					'customer-lastname' => $row['customer_lastname'],
					'staff-id' => $row['staff_id'],
					'staff-name' => $row['staff_name'],
					'staff-lastname' => $row['staff_lastname'],
					'currency' => $config['currency'][$row['currency']]['symbol']
				], [
					'conducted' => $row['conducted'],
					'paid' => $row['paid'],
					'owner' => strrpos($user['group_ids'], '1') !== false
				], 'invoices');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));

        $total = [];
        if (!$page) {
            $total = db_multi_query('
                SELECT SQL_CALC_FOUND_ROWS
                    SUM(h.amount) as total
                FROM `'.DB_PREFIX.'_invoices_history` h
                INNER JOIN `'.DB_PREFIX.'_invoices` i
                    ON h.invoice_id = i.id
                INNER JOIN `'.DB_PREFIX.'_users` u
                    ON i.customer_id = u.id
                WHERE 1 '.(
                $query ? 'AND MATCH(
                        u.name, u.lastname, u.email, u.phone
                    ) AGAINST (
                        \'*'.$query.'*\' IN BOOLEAN MODE
                    )  ' : ''
            ).(
                $status === 'paid' ? 'AND i.conducted = 1 ' : ($status === 'unpaid' ? 'AND i.conducted = 0 ' : '')
            ).(
                $type ? 'AND h.type '.(
					$type[0] == '-' ? '!' : ''
				).'= \''.str_ireplace('-','', $type).'\' ' : ''
            ).(
                $object ? 'AND i.object_id = '.$object.' ' : ''
            ).(
                $customer ? 'AND i.customer_id = '.$customer.' ' : ''
            ).(
                ($date_start AND $date_finish) ? ' AND h.date >= CAST(\''.$date_start.'\' AS DATE) AND h.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME) ' : ''
            )
            );
			
			if($type == '-check'){
				$total_credit = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						SUM(h.amount) as total
					FROM `'.DB_PREFIX.'_invoices_history` h
					INNER JOIN `'.DB_PREFIX.'_invoices` i
						ON h.invoice_id = i.id
					INNER JOIN `'.DB_PREFIX.'_users` u
						ON i.customer_id = u.id
					WHERE 1 '.(
					$query ? 'AND MATCH(
							u.name, u.lastname, u.email, u.phone
						) AGAINST (
							\'*'.$query.'*\' IN BOOLEAN MODE
						)  ' : ''
				).(
					$status === 'paid' ? 'AND i.conducted = 1 ' : ($status === 'unpaid' ? 'AND i.conducted = 0 ' : '')
				).(
					$type ? 'AND h.type = \'credit\' ' : ''
				).(
					$object ? 'AND i.object_id = '.$object.' ' : ''
				).(
					$customer ? 'AND i.customer_id = '.$customer.' ' : ''
				).(
					($date_start AND $date_finish) ? ' AND h.date >= CAST(\''.$date_start.'\' AS DATE) AND h.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME) ' : ''
				)
				);
				$total_cash = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						SUM(h.amount) as total
					FROM `'.DB_PREFIX.'_invoices_history` h
					INNER JOIN `'.DB_PREFIX.'_invoices` i
						ON h.invoice_id = i.id
					INNER JOIN `'.DB_PREFIX.'_users` u
						ON i.customer_id = u.id
					WHERE 1 '.(
					$query ? 'AND MATCH(
							u.name, u.lastname, u.email, u.phone
						) AGAINST (
							\'*'.$query.'*\' IN BOOLEAN MODE
						)  ' : ''
				).(
					$status === 'paid' ? 'AND i.conducted = 1 ' : ($status === 'unpaid' ? 'AND i.conducted = 0 ' : '')
				).(
					$type ? 'AND h.type = \'cash\' ' : ''
				).(
					$object ? 'AND i.object_id = '.$object.' ' : ''
				).(
					$customer ? 'AND i.customer_id = '.$customer.' ' : ''
				).(
					($date_start AND $date_finish) ? ' AND h.date >= CAST(\''.$date_start.'\' AS DATE) AND h.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME) ' : ''
				)
				);	
			}
        }
		

		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['invoices'] ?: '<div class="noContent">No history</div>',
                'total' => number_format($total['total'], 2, '.', '')
			]));
		}
		$cash = $_GET['from_cash'] ? explode(',', $_GET['from_cash']) : [];
		
		tpl_set('invoices/history', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'invoices' => $tpl_content['invoices'],
			'system' => number_format($cash[2] ?? 0, 2, '.', ''),
			'uamount' => $cash[0],
			'lack' => $cash[1] ? '<font color="'.($cash[1] < 0 ? '#f00' : '#4ac14a').'">'.$cash[1].'</span>' : '',
			'credit' => number_format(($total_credit ? $total_credit['total'] : 0), 2, '.', ''),
			'cash' => number_format(($total_cash ? $total_cash['total'] : 0), 2, '.', ''),
            'total' => number_format($total['total'], 2, '.', '')
		], [
			'cash' => $_GET['from_cash'],
			'open' => isset($total_credit),
			'add' => !($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips'])))
		], 'content');
	break;
	
	
	/*
	* All invoices
	*/
	case 'refund':
	case 'estimate':
	case 'deleted':
	default:
		$meta['title'] = 'Invoices';
		$query = text_filter($_POST['query'], 255, false);
		$type = text_filter($_REQUEST['type'], 10, false);
		$status = text_filter($_REQUEST['status'], 10, false);
		$page = intval($_POST['page']);
		$object = intval($_REQUEST['object']);
		$customer = intval($_REQUEST['staff']);
		$profit = intval($_REQUEST['profit']);
		$date_start = text_filter($_REQUEST['date_start'], 30, true);
		$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
		$action = text_filter($_REQUEST['action'], 3, true);
		
		$count = 10;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				i.id,
				i.date,
				i.tr_date,
				i.total,
				i.paid,
				i.tax,
				i.pay_method,
				i.inventory,
				i.customer_id,
				i.conducted,
				i.refund,
				i.refund_info,
				i.refund_user,
				i.staff_id,
				i.order_id,
				i.currency,
				i.estimate,
				i.del,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.phone as customer_phone,
						ru.id as refund_id,
						ru.name as refund_name,
						ru.lastname as refund_lastname,
							ri.refund_invoice as refund_id
			FROM `'.DB_PREFIX.'_invoices` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_invoices` ri
			ON ri.refund_invoice = i.id
				LEFT JOIN `'.DB_PREFIX.'_users` ru
			ON i.staff_id = ru.id
			WHERE 1 '.(
			$query ? 'AND (CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' 
					OR CONCAT(u.lastname, \' \', u.name) LIKE \'%'.$query.'%\' 
					OR u.email LIKE \'%'.$query.'%\' 
					OR u.phone LIKE \'%'.$query.'%\' 
					OR i.id = \''.$query.'%\')  ' : ''
		).(
			$status === 'paid' ? 'AND i.conducted = 1 ' : (
				$status === 'unpaid' ? 'AND i.conducted = 0 ' : (
					$status === 'partial' ? 'AND i.conducted = 0 AND i.paid > 0 ' : ''
				)
			)
		).(
			$type ? 'AND i.pay_method = \''.$type.'\' ' : ''
		).(
			$object ? 'AND i.object_id = '.$object.' ' : ''
		).(
			$customer ? 'AND i.customer_id = '.$customer.' ' : ''
		).(
			$profit ? 'AND i.staff_id = '.$profit.' AND i.conducted = 1 ' : ''
		).(
			$route[1] == 'refund' ? 'AND i.refund_invoice != 0 ' : ''
		).(
			$route[1] == 'estimate' ? 'AND i.estimate = 1 ' : 'AND i.estimate = 0 '
		).(
			$route[1] == 'deleted' ? 'AND i.del = 1 ' : 'AND i.del = 0 '
		).(
			$action ? ($action == 'yes' ? 'AND (i.onsite_id OR i.add_onsite) ' : 'AND !i.onsite_id AND !i.add_onsite ') : ''
		).(
			($date_start AND $date_finish) ? ' AND IF(i.tr_date, i.tr_date, i.date) >= CAST(\''.$date_start.'\' AS DATE) AND IF(i.tr_date, i.tr_date, i.date) <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME) ' : ''
		).'ORDER BY i.id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('invoices/item', [
					'id' => $row['id'],
					'amount' => number_format($row['total'], 2, '.', ''),
					'emailed' => $row['discount_id'] ? 'YES' : 'NO',
					'paid' => number_format($row['paid'], 2, '.', ''),
					'pay' => $row['pay_method'],
					'due' => number_format((abs($row['total']-$row['paid']) < 0.01 ? 0 : $row['total']-$row['paid']), 2, '.', ''),
					'date' => date('m.d.y', strtotime($row['date'])),
					'customer-id' => $row['customer_id'],
					'customer-name' => $row['customer_name'],
					'customer-lastname' => $row['customer_lastname'],
					'customer-phone' => str_replace(',', '<br>', $row['customer_phone']),
					'refund' => $row['refund'] == 1 ? 'refund' : '',
					'refund-confirm' => ($row['refund'] == 0 AND strlen($row['refund_info']) > 2) ? 'refund-conf' : '',
					'refund-id' => $row['refund_id'],
					'refund-name' => $row['refund_name'],
					'refund-lastname' => $row['refund_lastname'],
					'currency' => $config['currency'][$row['currency']]['symbol'],
				], [
					'conducted' => $row['conducted'],
					'estimate' => $row['estimate'],
					'add' => !($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips']))),
					'refund_confirm' => (strlen($row['refund_info']) > 2 AND $row['refund'] == 0),
					'refund_request' => (strlen($row['refund_info']) > 2),
					'user_refund_confirm' => $user['confirm_refund'] AND $row['refund'] == 0,
					'paid' => $row['paid'],
					'owner' => strrpos($user['group_ids'], '1') !== false,
					'edit' => ($row['conducted'] AND $user['edit_paid_invoices']) OR (!$row['conducted'] AND $user['edit_invoices']),
					'quick_sell' => !$row['customer_id'],
					'refund-invoice' => $row['refund_id'] > 0,
					'order' => $row['order_id'],
					'deleted' => $row['del']
				], 'invoices');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));

        $total = [];
        if (!$page) {
            $total = db_multi_query('
                SELECT 
                    SUM(i.total) as total
                FROM `'.DB_PREFIX.'_invoices` i
                INNER JOIN `'.DB_PREFIX.'_users` u
			        ON i.customer_id = u.id
                WHERE 1 '.(
					$query ? 'AND (CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' 
							OR CONCAT(u.lastname, \' \', u.name) LIKE \'%'.$query.'%\' 
							OR u.email LIKE \'%'.$query.'%\' 
							OR u.phone LIKE \'%'.$query.'%\' 
							OR i.id = \''.$query.'%\')  ' : ''
				).(
                    $status === 'paid' ? 'AND i.conducted = 1 ' : ($status === 'unpaid' ? 'AND i.conducted = 0 ' : '')
                ).(
                    $type ? 'AND i.pay_method = \''.$type.'\' ' : ''
                ).(
                    $object ? 'AND i.object_id = '.$object.' ' : ''
                ).(
                    $customer ? 'AND i.customer_id = '.$customer.' ' : ''
                ).(
					$profit ? 'AND i.staff_id = '.$profit.' AND i.conducted = 1 ' : ''
				).(
					$route[1] == 'refund' ? 'AND i.refund = 1 ' : ''
				).(
					$route[1] == 'estimate' ? 'AND i.estimate = 1 ' : 'AND i.estimate = 0 '
				).(
					$action ? ($action == 'yes' ? 'AND (i.onsite_id OR i.add_onsite) ' : 'AND !i.onsite_id AND !i.add_onsite ') : ''
				).(
                    ($date_start AND $date_finish) ? ' AND IF(i.tr_date, i.tr_date, i.date) >= CAST(\''.$date_start.'\' AS DATE) AND IF(i.tr_date, i.tr_date, i.date) <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME) ' : ''
                )
            );
        }

		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['invoices'],
                'total' => number_format($total['total'], 2, '.', '')
			]));
		}
		tpl_set('invoices/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'invoices' => $tpl_content['invoices'],
            'total' => number_format($total['total'], 2, '.', ''),
			'profit' => $profit ? '<input type="hidden" name="profit"  value="'.$profit.'">' : ''
		], [
			'add' => !($user['check_ip_invoice'] AND !intval(array_search($_SERVER['REMOTE_ADDR'], $config['object_ips']))),
			'owner' => in_to_array(1, $user['group_ids'])
		], 'content');
}
 ?>