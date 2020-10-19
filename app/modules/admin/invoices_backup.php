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
	* Refund confirm
	*/
	case 'refund_confirm':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		if ($user['confirm_refund']) {
			$invoice = db_multi_query('	
				SELECT 
					i.*,
					s.inventory_ids
				FROM `'.DB_PREFIX.'_invoices` i
				LEFT JOIN `'.DB_PREFIX.'_issues` s
					ON i.id = i.issue_id
				WHERE i.id = '.$id);
				
			if ($invoice['conducted'] == -1)
				die('ERR');
			
			db_query('UPDATE `'.DB_PREFIX.'_invoices` SET conducted = -1 WHERE id = '.$id);
			
			db_query('INSERT INTO `'.DB_PREFIX.'_invoices_history` SET 
				invoice_id = '.$id.',
				amount = \''.(floatval($invoice['total'])*(-1) ?: 0).'\',
				staff_id = '.$user['id'].',
				type = \''.$type.'\'');
				
			if ($invoice['inventory_ids'] OR $invoice['inventory']) {
				db_query('
					UPDATE `'.DB_PREFIX.'_inventory` SET
						customer_id = 0,
						object_owner = '.$invoice['object_id'].'
					WHERE id IN ('.(
						($invoice['inventory_ids'] AND $invoice['inventory']) ? ids_filter($invoice['inventory_ids']).','.ids_filter($invoice['inventory']) :
							($invoice['inventory_ids'] ? ids_filter($invoice['inventory_ids']) : ids_filter($invoice['inventory']))
					).')
				');
			}
			
			if ($invoice['buy_inventory']) {
				db_query('
					UPDATE `'.DB_PREFIX.'_inventory` SET
						customer_id = '.$invoice['customer_id'].',
						object_owner = 0
					WHERE id IN ('.ids_filter($invoice['buy_inventory']).')
				');
			}
				
			die('OK');
		} else 
			die('no_acc');
	break;
	
	/*
	* Refund decline
	*/
	case 'refund_decline':
		is_ajax() or die('Hacking attempt!');
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET refund = 0 WHERE id = '.intval($_POST['id']));
		die('OK');
	break;
	
	
	/*
	* Refund request
	*/
	case 'refund_request':
		is_ajax() or die('Hacking attempt!');
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET refund = 1, refund_user = '.$user['id'].' WHERE id = '.intval($_POST['id']));
		die('OK');
	break;
	
	/*
	* All
	*/
	case 'all':
        is_ajax() or die('Hacking attempt!');
		$lId = intval($_POST['lId']);
		$paid = text_filter($_POST['paid'], 10, false);
		$oId = intval($_POST['oId']);
		$nIds = ids_filter($_POST['nIds']);
		$query = text_filter($_POST['query'], 100, false);
		$objects = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, id as name FROM `'.DB_PREFIX.'_invoices` WHERE 1'.(
				$nIds ? ' AND id NOT IN('.$nIds.')' : ''
			).(
				$oId ? ' AND object_id = '.$oId.'' : ''
			).(
				$paid == 'unpaid' ? ' AND conducted = 0' : ($paid == 'paid' ? ' AND conducted = 1' : '')
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
		$ids = [];
		$err = '';
		foreach($tradein as $i => $tr) {
			if (!$tr['price'] OR !$tr['purchase']) {
				print_r(json_encode([
					'error' => 'empty_price',
					'id' => $i
				]));
				die;
			}
			if ($tr['price'] < min_price($tr['purchase'])) {
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
		if (floatval($_POST['cprice']) < min_price(floatval($_POST['price'])))
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
			object_id = \''.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).'\',
			status = \'Purchased\',
			create_id = '.$user['id'].', 
			create_date = \''.date('Y-m-d H:i:s', time()).'\''
		);
		
		$pid = intval(mysqli_insert_id($db_link));
		
		db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', event = \'add_purchase\', object_id = '.($objects_ip[$_SERVER['REMOTE_ADDR']] ?: 0).', event_id = '.$pid);
		
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
		$discounts = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, CONCAT(name, \', \', percent, \'%\') as name FROM `'.DB_PREFIX.'_invoices_discount` 
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
		$total = 0;
		$amount = (floatval($_POST['amount']) ?: 0);

		$invoice = db_multi_query('	
			SELECT 
				i.*,
				s.inventory_ids
			FROM `'.DB_PREFIX.'_invoices` i
			LEFT JOIN `'.DB_PREFIX.'_issues` s
				ON i.issue_id = s.id
			WHERE i.id = '.$id);
		
		if ($amount == 0 AND !$invoice['buy_inventory'])
			die('null_invoice');
		
		if ($invoice['conducted'] == 1)
			die('condected');

		$full = abs(floatval($invoice['paid']) + $amount - floatval($invoice['total']));
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
			pay_method = \''.$type.'\',
			transaction = 1,
			tr_date = \''.date('Y-m-d H:i:s', time()).'\',
			paid = \''.(floatval($invoice['paid']) + $amount).'\' WHERE id = '.$id);
		
		db_query('INSERT INTO `'.DB_PREFIX.'_invoices_history` SET 
			invoice_id = '.$id.',
			amount = \''.$amount.'\',
			staff_id = '.$user['id'].',
			type = \''.$type.'\''.(
				$type == 'check' ? ', check_number = \''.text_filter($_POST['check'], 25, false).'\'' : ''
			));
			
		// ------------------------------------------------------------------------------- //
		
		
		
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
/* 		if ($user['id'] == 17) {
		
		print_r('UPDATE `'.DB_PREFIX.'_invoices` SET paid = IF(
			'.(intval($_POST['purchace']) ? 'total-paid <= -'.$partial.', paid+\'-'.$partial.'\', paid' :
			'total-paid >= '.$partial.', paid+\''.$partial.'\', paid').'
		) WHERE id = '.$id);
		die;
} */
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
	* Update discount
	*/
	case 'discount':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		db_query('UPDATE `'.DB_PREFIX.'_invoices` SET discount_id = '.intval($_POST['discount']).' WHERE id = '.$id);
		die('OK');
	break;
	
	/*
	* Send invoices
	*/
	case 'send':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		$discount = intval($_POST['discount']);
		$tradein = ids_filter($_POST['tradein']);
		$issue_id = intval($_POST['issue']);
		$add_onsite_price = floatval($_POST['add_onsite_price']);
		$trade_unique = array_unique(explode(',', $tradein));
		
		$row = db_multi_query('SELECT conducted, buy_inventory FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
		if ($issue_id) {
			$issue = db_multi_query('SELECT u.id as uid
									FROM `'.DB_PREFIX.'_issues` i
									LEFT JOIN `'.DB_PREFIX.'_inventory` d
										ON d.id = i.inventory_id
									LEFT JOIN `'.DB_PREFIX.'_users` u
										ON u.id = d.customer_id
									WHERE i.id = '.$issue_id);
		}
		
		if ($tradein) {
			foreach(array_text_filter($_POST['trade_arr']) as $i => $v) {
				if (in_array($i, $trade_unique)) {
					db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
						purchase_price = \''.$v['purchase'].'\',
						price = \''.$v['price'].'\',
						tradein = 1
					WHERE id = '.$i);
					
					if (!in_array($i, explode(',', $row['buy_inventory']))) {
						db_query('INSERT INTO `'.DB_PREFIX.'_tradein` SET
							inventory_id = '.intval($i).',
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
						
						if($user['store_id'] > 0 AND in_to_array('1,2', $user['group_ids'])){
							$sql_ = db_multi_query('
								SELECT
									SUM(tb1.point) as sum,
									tb2.points
								FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
									 `'.DB_PREFIX.'_objects` tb2
								WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(NOW(), INTERVAL 1 HOUR) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
							);
							$points = (floatval($v['price'])-floatval($v['purchase']))*floatval($config['user_points']['trade_in']['points'])/100;
							if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
								db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									staff_id = '.$user['id'].',
									action = \'trade_in\',
									min_rate = '.$sql_['points'].',
									object_id = '.$user['store_id'].',
									inventory_id = '.intval($i).',
									point = \''.$points.'\''
								);	
								db_query(
									'UPDATE `'.DB_PREFIX.'_users`
										SET points = points+'.$points.'
									WHERE id = '.$user['id']
								);
							} else {
								db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									staff_id = '.$user['id'].',
									action = \'trade_in\',
									min_rate = '.$sql_['points'].',
									object_id = '.$user['store_id'].',
									inventory_id = '.intval($i).',
									point = \''.$points.'\',
									rate_point = 1'
								);	
							}
						}
					}
					$trade_unique = array_diff($trade_unique, [$i]);
				}
			}
		}
		
		if (!$id OR ($id AND ($row['conducted'] AND $user['edit_paid_invoices']) OR (!$row['conducted'] AND $user['edit_invoices']))) {
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_invoices` SET
				object_id = '.intval($_POST['object']).',
				customer_id = '.($issue_id ? $issue['uid'] : intval($_POST['customer'])).',
				total = \''.floatval($_POST['total']).'\',
				tax = \''.floatval($_POST['tax']).'\',
				paid = \''.floatval($_POST['paid']).'\',
				'.(intval($_POST['issue']) ? 'issue_id = '.$issue_id.',' : '').'
				inventory = \''.ids_filter($_POST['inventory']).'\',
				buy_inventory = \''.$tradein.'\',
				invoices = \''.ids_filter($_POST['invoices']).'\',
				purchases = \''.ids_filter($_POST['purchases']).'\',
				purchace = \''.intval($_POST['purchase']).'\',
				services = \''.ids_filter($_POST['services']).'\''.(
					$discount ? ', discount_id = '.$discount : ''
				).(
					$add_onsite_price ? ', add_onsite_price = '.$add_onsite_price : ''
				).(
				$id ? ' WHERE id = '.$id : ''
			));
		} else 
			die('ERR');
		
		echo $id ?: intval(
			mysqli_insert_id($db_link)
		);
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
		
		$objects = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, tax FROM `'.DB_PREFIX.'_objects` WHERE 1'.(
			(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '' :
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
	case 'send_mail':
		$id = ($route[1] == 'send_mail' ? intval($_POST['id']) : intval($route[2]));
		$meta['title'] = 'Invoice';
		$pur_ids = [];
		$total = 0;
		$tradein = 0;
		$tax = 0;
		$has_purchase = 0;
		$transfer_ids = [
			'inventory' => [],
			'tradein' => []
		];
		$invoices_arr = [];
        $onsite = '';
        $onsite_total = 0;
		if($row = db_multi_query('
			SELECT
				i.id,
				i.date,
				i.total,
				i.paid,
				i.tax,
				i.refund,
				i.inventory,
				i.transaction,
				i.buy_inventory,
				i.services,
				i.invoices,
				i.issue_id,
				i.object_id,
				i.conducted,
				i.purchace,
				i.purchases,
				i.discount_id,
				i.customer_id,
                i.onsite_id,
                i.add_onsite,
                i.add_onsite_price,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.address as customer_address,
					u.email as customer_email,
					u.phone as customer_phone,
					u.zipcode as zipcode,
						d.percent as discount,
						d.name as discount_name,
							o.tax as object_tax,
							o.name as object_name,
							o.address as object_address,
							o.phone as object_phone,
								c.city as city_name,
									inv.model as inv_model,
									inv.serial as inv_serial,
										cat.name as inv_cat,
											model.name as inv_model_name,
												type.name as type_name,
													iss.description,
													iss.upcharge_id,
													iss.staff_id as staff_id,
														staff.name as staff_name,
														staff.lastname as staff_lastname,
															status.name as status_name,
                                                            os.name as onsite_name,
                                                            os.price as onsite_price,
                                                            uos_info.name as user_onsite_name,
                                                            SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time,
																up.price as inv_service
			FROM `'.DB_PREFIX.'_invoices` i
				INNER JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id				
				LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
			ON i.discount_id = d.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = i.object_id
				LEFT JOIN `'.DB_PREFIX.'_cities` c
			ON c.zip_code = u.zipcode
				LEFT JOIN `'.DB_PREFIX.'_issues` iss
			ON iss.id = i.issue_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_upcharge` up
			ON up.id = iss.upcharge_id
				LEFT JOIN `'.DB_PREFIX.'_users` staff
			ON iss.staff_id = staff.id
				LEFT JOIN `'.DB_PREFIX.'_inventory` inv
			ON inv.id = iss.inventory_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` cat
			ON cat.id = inv.category_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_types` type
			ON type.id = inv.type_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_models` model
			ON model.id = inv.model_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_status` status
			ON status.id = inv.status_id
                LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
			ON uos.id = i.add_onsite
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
			ON uos_info.id = uos.onsite_id
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
			ON os.id = i.onsite_id
				WHERE i.id = '.$id
		)){
            if ($row['onsite_id']) {
                $onsite .= '<div class="tr" data-id="'.$row['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$row['onsite_id'].'">
                    <div class="td">
                        '.$row['onsite_name'].'
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_price">
                        $ '.$row['onsite_price'].'
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
                        $ '.$row['add_onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['add_onsite_price']);
                $onsite_total += floatval($row['add_onsite_price']);
            }
			if($row['inventory'] OR $row['services'] OR $row['buy_inventory']){
				foreach(db_multi_query('
					SELECT IF(i.name = \'\', i.model, i.name) as name, 
						i.price, 
						i.purchase_price, 
						i.id, 
						i.type, 
						i.quantity,
						i.tradein,
						c.name as catname,
						m.name as model_name
					FROM `'.DB_PREFIX.'_inventory` i
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
						ON i.category_id = c.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
						ON i.model_id = m.id
					WHERE 1'.(
							($row['inventory'] or $row['services']) ? 
								(' AND i.id IN('.(
									($row['inventory'] and $row['services']) ? $row['inventory'] . ',' . $row['services'] : (
										$row['inventory'] ? $row['inventory'] : $row['services'])
								).')'.($row['buy_inventory'] ? ' OR i.id IN('.$row['buy_inventory'].')' : '')) : 
								($row['buy_inventory'] ? ' AND i.id IN('.$row['buy_inventory'].')' : '')
						), true) as $item){
							
						tpl_set('invoices/viewItem', [
							'id' => $item['id'],
							'name' => $item['name'],
							'catname' => ($item['type'] == 'service') ? '' : $item['catname'].' '.$item['model_name'],
							'price' => number_format((in_to_array($item['id'], $row['buy_inventory']) ? (-$item['purchase_price']) : $item['price']), '2', '.', ''),
							'sale' => number_format((in_to_array($item['id'], $row['buy_inventory']) ? $item['price'] : 0), '2', '.', ''),
							'type' => in_to_array($item['id'], $row['buy_inventory']) ? 'tradein' : $item['type']
						], [
							'tradein' => in_to_array($item['id'], $row['buy_inventory']),
							'edit' => $route[1] == 'edit',
							'stock' => $item['type'] == 'stock'
						], 'inventory');
						
						$total += in_to_array($item['id'], $row['buy_inventory']) ? 0 : $item['price'];
						$tradein += in_to_array($item['id'], $row['buy_inventory']) ? $item['purchase_price'] : 0;
				
						if ($item['type'] == 'stock') {
							if (!$transfer_ids[(in_to_array($item['id'], $row['buy_inventory']) ? 'tradein' : 'inventory')][$row['customer_id']]) 
								$transfer_ids[(in_to_array($item['id'], $row['buy_inventory']) ? 'tradein' : 'inventory')][$row['customer_id']] = [];
							$transfer_ids[(in_to_array($item['id'], $row['buy_inventory']) ? 'tradein' : 'inventory')][$row['customer_id']][] = (in_to_array($item['id'], $row['buy_inventory']) ? [$item['id'], NULL, $item['tradein']] : [0 => $item['id'], 1 => $item['quantity'], 2 => $item['tradein'], 'price' => $item['price'], 'purchase' => $item['purchase_price']]);
						}
				}
			}
			
			if($row['purchases']){
				$has_purchase = 1;
				foreach(db_multi_query('
					SELECT 
							p.name as pur_name, 
							p.sale_name, 
							p.sale as pur_price, 
							p.id as pur_id
					FROM `'.DB_PREFIX.'_purchases` p
					WHERE p.id IN('.$row['purchases'].')', true) as $item){
						
							tpl_set('invoices/viewItem', [
								'id' => $item['pur_id'],
								'name' => $item['sale_name'] ?: $item['pur_name'],
								'price' => number_format(($item['pur_price'] ?: 0), '2', '.', ''),
								'catname' => '',
								'type' => 'purchase'
							], [
								'tradein' => false,
								'stock' => false
							], 'purchases');
							$total += $item['pur_price'];
							$pur_ids[] = $item['pur_ids'];
				}
			}
			
			$options = '';
			
			if($discounts = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices_discount` ORDER BY `id` LIMIT 0, 50', true)){
				foreach($discounts as $discount){
					$options .= '<option value="'.$discount['id'].'"'.(
						$discount['id'] == $row['discount_id'] ? ' selected' : ''
					).'>'.$discount['name'].'</option>';
				}
			}
			
			if($row['issue_id']){
				$issues_html = '';
				$issue_total = 0;
				$pur_ids = [];
				$inv_ids = [];
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
				
				$cont_service = 0;
				$servs = [];
				foreach (array_values(array_filter($issues, function($v) {
					if ($v['type'] == 'service' AND $v['price'] > 0)
						return $v;
				}, ARRAY_FILTER_USE_BOTH)) as $s) {
					if (!in_array($s['inv_id'], $servs))
						$cont_service += substr_count($issues[0]['service_ids'], $s['inv_id'].'_');
					$servs[] = $s['inv_id'];
				}
				$inv_price = ($cont_service > 0) ? $row['inv_service'] / $cont_service : 0;
		
				foreach($issues as $issue){
					if ($issue['inv_id'] AND !in_array($issue['inv_id'], $inv_ids)) {
						if ($issue['type'] == 'service') {
							$count = substr_count($issue['service_ids'], $issue['inv_id'].'_');
						} else {
							$count = 1;
							if (!$transfer_ids['inventory'][$row['customer_id']]) 
								$transfer_ids['inventory'][$row['customer_id']] = [];
							$transfer_ids['inventory'][$row['customer_id']][] = [0 => $issue['id'], 1 => $issue['quantity'], 2 => $issue['tradein'], 'price' => $issue['price'], 'purchase' => $issue['purchase_price']];
						}
						
						for($i = 0; $i < $count; $i ++) {
							$issues_html .= '
								<div class="tr">
									<div class="td isItem">
										'.(($issue['type'] == 'service') ? '' : '<a href="/inventory/view/'.$issue['inv_id'].'" target="_blank">'.$issue['catname'].' '.$issue['model_name']).' '.$issue['name'].($issue['type'] == 'stock' ? '</a>' : '').'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.(
											(floatval($issue['doit']) > 0) ? '' : '$'.number_format(floatval(($issue['price'] > 0 AND $issue['type'] == 'service') ? $issue['price'] + $inv_price : $issue['price']), '2', '.', '')
										).'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
							if (floatval($issue['doit']) == 0){
								$issue_total += ($issue['price'] > 0 AND $issue['type'] == 'service') ? $issue['price'] + $inv_price : $issue['price'];
							} 
						}
						$inv_ids[] = $issue['inv_id'];
					}
					if ((!in_array($issue['pur_id'], $pur_ids)) AND $issue['pur_id']) {
						$has_purchase = 1;
						$issues_html .= '<div class="tr">
							<div class="td isItem">
								<a href="/purchases/edit/'.$issue['pur_id'].'" target="_blank">'.$issue['pur_name'].'</a>
							</div>
							<div class="td w10">
								1
							</div>
							<div class="td w100 nPay">
								'.(
									(floatval($issue['doit']) > 0) ? '' : '$'.number_format(floatval(($issue['pur_price'] ?: $issue['pur_or_price'])), '2', '.', '')
								).'
							</div>
							<div class="td w10">
								yes
							</div>
						</div>';
						if (floatval($issue['doit'] == 0)){
							$issue_total += ($issue['pur_price'] ?: $issue['pur_or_price']);
						} 
						$pur_ids[] = $issue['pur_id'];
					}
				}
				
				$issue_mhtml = '<div class="tr">
								<div class="td">
									<b><a href="/issues/view/'.$issues[0]['issue_id'].'" target="_blank">Issue #'.$issues[0]['issue_id'].'</a></b>
								</div>
								<div class="td w10"></div>
								<div class="td w100"><b>$'.number_format((
									(floatval($issue['doit']) > 0) ? $issue['doit'] : $issue_total
								), '2', '.', '').'</b></div>
								<div class="td w10">
									yes
								</div>
							</div>'.$issues_html;
				
				$total += (floatval($issue['doit']) > 0) ? $issue['doit'] : $issue_total;
			}
			
			if($row['invoices']){
				$now_invoce_id = 0;
				$invoces_html = '';
				$index = 0;
				$pur_ids = [];
				$invoices = db_multi_query('SELECT
					tb1.id as invoice_id,
					tb1.total as invoice_total,
					tb1.paid as invoice_paid,
					tb1.tax as invoice_tax,
					tb1.issue_id,
					tb1.customer_id,
					tb1.services, 
					tb1.inventory,
					tb1.buy_inventory,
					IF(tb2.name = \'\', tb2.model, tb2.name) as name,
						tb2.price,
						tb2.purchase_price,
						tb2.type,
						tb2.id as inv_id,
						tb2.quantity,
						tb2.tradein,
							tb3.name as catname,
								tb4.name as discount_name,
								tb4.percent,
									tb5.id as pur_id,
									tb5.name as pur_name,
									tb5.sale_name,
									tb5.sale as pur_price,
										tb6.tax as object_tax,
											m.name as model_name,
                                            tb1.onsite_id,
                                            tb1.add_onsite,
                                            tb1.add_onsite_price,
                                            os.name as onsite_name,
                                            os.price as onsite_price,
                                            uos_info.name as user_onsite_name,
                                            SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time
						FROM `'.DB_PREFIX.'_invoices` tb1
					LEFT JOIN `'.DB_PREFIX.'_inventory` tb2
						ON FIND_IN_SET(tb2.id, CONCAT(tb1.inventory, ",", tb1.services, ",", tb1.buy_inventory))
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb3
						ON tb2.category_id = tb3.id
					LEFT JOIN `'.DB_PREFIX.'_invoices_discount` tb4
						ON tb1.discount_id = tb4.id
					LEFT JOIN `'.DB_PREFIX.'_purchases` tb5
						ON FIND_IN_SET(tb5.id, tb1.purchases)
					LEFT JOIN `'.DB_PREFIX.'_objects` tb6
						ON tb6.id = tb1.object_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
						ON tb2.model_id = m.id
                    LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
                        ON uos.id = tb1.add_onsite
                    LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
                        ON uos_info.id = uos.onsite_id
                    LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
                        ON os.id = tb1.onsite_id
					WHERE tb1.id IN('.$row['invoices'].') ORDER BY tb1.id LIMIT 0, 50'
				, true);
				
				$issues_html_inv = '';
				$issue_total_inv = 0;
				$issue_tradein_inv = 0;
				$pur_ids_inv = [];
				$issues_inv = db_multi_query('SELECT
					tb1.id as issue_id,
					tb1.doit,
					tb1.service_ids,
					tb1.purchase_prices,
					tb1.upcharge_id,
					IF(tb2.name = \'\', tb2.model, tb2.name) as name,
						tb2.price,
						tb2.purchase_price,
						tb2.id as inv_id,
						tb2.type,
						tb2.quantity,
						tb2.tradein,
							tb3.name as catname,
								tb4.id as pur_id,
								tb4.name as pur_name,
								tb4.sale_name,
								tb4.sale as pur_sale,
								REGEXP_REPLACE(tb1.purchase_prices, CONCAT(\'{(.*)?"\', tb4.id, \'":"(.*)",(.*)?}\'), \'\\\2\') as pur_price,
									m.name as model_name,
										tb5.id as invoice_id,
										tb5.customer_id,
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
					LEFT JOIN `'.DB_PREFIX.'_invoices` tb5
						ON tb5.issue_id = tb1.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
						ON tb2.model_id = m.id
					WHERE tb5.id IN('.$row['invoices'].') LIMIT 0, 50'
				, true);
				
				// issue html
				$iss_index = 0;
				$issue_mhtml_inv = [];
				$issue_mhtml_inv[$issues_inv[0]['issue_id']] = '';
				$issue_id = $issues_inv[0]['issue_id'];
				$issue_servs = [];
				
				foreach($issues_inv as $issue){
					$cont_service_iss = 0;
					$servs = [];
					foreach (array_values(array_filter($issues_inv, function($v) use(&$issue_id) {
						if ($v['type'] == 'service' AND $v['price'] > 0 AND $v['issue_id'] == $issue_id)
							return $v;
					}, ARRAY_FILTER_USE_BOTH)) as $s) {
						if (!in_array($s['inv_id'], $servs))
							$cont_service_iss += substr_count($issue['service_ids'], $s['inv_id'].'_');
						$servs[] = $s['inv_id'];
					}
					$inv_price = ($cont_service_iss > 0) ? $issue['inv_service'] / $cont_service_iss : 0;
					
					/* if ($user['id'] == 17){
						print_r(array_values(array_filter($issues_inv, function($v) use(&$issue_id) {
						if ($v['type'] == 'service' AND $v['price'] > 0 AND $v['issue_id'] == $issue_id)
							return $v;
					}, ARRAY_FILTER_USE_BOTH)));
					} */
					if (!in_array($issue['inv_id'], $issue_servs)) {
						$issues_html_inv .= '
							<div class="tr">
								<div class="td isItem">
									'.($issue['type'] == 'stock' ? '<a href="/inventory/edit/'.$issue['inv_id'].'" target="_blank">' : '').($issue['catname'] ? $issue['catname'].' '.$issue['model_name'] : '').' '.$issue['name'].($issue['type'] == 'stock' ? '</a>' : '').'
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 nPay">$
									'.number_format(
									(($issue['price'] > 0 AND $issue['type'] == 'service') ? ($issue['price'] + $inv_price) : $issue['price'])
									, '2', '.', '').'
								</div>
								<div class="td w10">
									yes
								</div>
							</div>';
					
							if ($issue['type'] == 'stock') {
								if (!$transfer_ids['inventory'][$issue['customer_id']]) 
									$transfer_ids['inventory'][$issue['customer_id']] = [];
								$transfer_ids['inventory'][$issue['customer_id']][] = [0 => $issue['id'], 1 => $issue['quantity'], 2 => $issue['tradein'], 'price' => $issue['price'], 'purchase' => $issue['purchase_price']];
							}
							$issue_total_inv += in_to_array($issue['inv_id'], $issue['buy_inventory']) ? 0 : (($issue['price'] > 0 AND $issue['type'] == 'service') ? ($issue['price'] + $inv_price) : $issue['price']);
							$issue_tradein_inv += in_to_array($issue['inv_id'], $issue['buy_inventory']) ? $issue['purchase_price'] : 0;
							$issue_servs[] = $issue['inv_id'];
						}
						if ((!in_array($issue['pur_id'], $pur_ids_inv)) AND $issue['pur_id']) {
							$has_purchase = 1;
							$issues_html_inv .= '<div class="tr">
								<div class="td isItem">
									<a href="/purchases/edit/'.$issue['pur_id'].'" target="_blank">'.($issue['sale_name'] ?: $issue['pur_name']).'</a>
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 nPay">$
									'.number_format(floatval(
										(floatval($issue['doit']) > 0) ? '' : ($issue['pur_price'] ?: ($issue['pur_sale'] ?: 0))
									), '2', '.', '').'
								</div>
								<div class="td w10">
									yes
								</div>
							</div>';
							if (floatval($issue['doit']) == 0){
								$issue_total_inv += ($issue['pur_price'] ?: ($issue['pur_sale'] ?: 0));
							} 
							$pur_ids_inv[] = $issue['pur_id'];
						}
						
					
					if ($issues_inv[$iss_index+1]['issue_id'] != $issue['issue_id']) {
						$has_purchase = 1;
						$issue_id = $issues_inv[$iss_index+1]['issue_id'];
						$issue_mhtml_inv[$issue['issue_id']] .= '<div class="tr">
										<div class="td">
											<b><a href="/issues/view/'.$issue['issue_id'].'" target="_blank">Issue #'.$issue['issue_id'].'</a></b>
										</div>
										<div class="td w10"></div>
										<div class="td w100"><b>$'.number_format((
											(floatval($issue['doit']) > 0) ? $issue['doit'] : $issue_total_inv
										), '2', '.', '').'</b></div>
										<div class="td w10">
											yes
										</div>
									</div>'.$issues_html_inv;
						$issue_mhtml_inv[$issues_inv[$iss_index+1]['issue_id']] = '';
						$total += (floatval($issue['doit']) > 0) ? $issue['doit'] : $issue_total_inv;
						$tradein += $issue_tradein_inv;
						$issues_html_inv = '';
						$issue_total_inv = 0;
					}
					$iss_index ++;			
				}
			
				// invoices html
				$inv_total = 0;
				$inv_tradein = 0;
				foreach($invoices as $invoice){
					if($now_invoce_id != $invoice['invoice_id']){
                        $invoice_onsite = '';
                        if ($invoice['onsite_id']) {
                            $invoice_onsite .= '<div class="tr" data-id="'.$invoice['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$invoice['onsite_id'].'">
                                <div class="td">
                                    '.$invoice['onsite_name'].'
                                </div>
                                <div class="td w10">
                                    1
                                </div>
                                <div class="td w100 onsite_price">
                                    $ '.$invoice['onsite_price'].'
                                </div>
                                <div class="td w10">
                                    no
                                </div>
                            </div>';
                            $total += floatval($invoice['onsite_price']);
                            $onsite_total += floatval($row['onsite_price']);
                        }
                        if ($invoice['add_onsite']) {
                            $invoice_onsite .= '<div class="tr" data-id="'.$invoice['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$invoice['add_onsite'].'">
                                <div class="td">
                                    '.$invoice['user_onsite_name'].'(Additional time - '.$invoice['user_onsite_time'].')
                                </div>
                                <div class="td w10">
                                    1
                                </div>
                                <div class="td w100 onsite_price">
                                    $ '.$invoice['add_onsite_price'].'
                                </div>
                                <div class="td w10">
                                    no
                                </div>
                            </div>';
                            $total += floatval($invoice['add_onsite_price']);
                            $onsite_total += floatval($row['add_onsite_price']);
                        }
						if($now_invoce_id != 0){
							$invoces_html .= '</div>';
						}
						
						$invoces_html .= '<div class="tbl payInfo">
							<div class="tr">
								<div class="th">
									<a href="/invoices/view/'.$invoice['invoice_id'].'" targer="_blank">Invoice #'.$invoice['invoice_id'].'</a>
								</div>
								<div class="th w10"></div>
								<div class="th w100"></div>
								<div class="th w10"></div>
							</div>'.(
								$invoice['issue_id'] ? $issue_mhtml_inv[$invoice['issue_id']] : ''
							).($invoice['inv_id'] ? '<div class="tr">
								<div class="td">
									'.($invoice['type'] == 'stock' ? '<a href="/inventory/view/'.$invoice['inv_id'].'" target="_blank">' : '').($invoice['catname'] ? $invoice['catname'].' '.$invoice['model_name'] : '').' '.$invoice['name'].($invoice['type'] == 'stock' ? '</a>' : '').'
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100">$
									'.(number_format((in_to_array($invoice['inv_id'], $invoice['buy_inventory']) ? -1*$invoice['purchase_price'] : $invoice['price']), '2', '.', '')).'
								</div>
								<div class="td w10">
									yes
								</div>
							</div>' : '');
						if ($invoice['type'] == 'stock') {
							if (!$transfer_ids[(in_to_array($invoice['inv_id'], $invoice['buy_inventory']) ? 'tradein' : 'inventory')][$invoice['customer_id']]) 
								$transfer_ids[(in_to_array($invoice['inv_id'], $invoice['buy_inventory']) ? 'tradein' : 'inventory')][$invoice['customer_id']] = [];
							$transfer_ids[(in_to_array($invoice['inv_id'], $invoice['buy_inventory']) ? 'tradein' : 'inventory')][$invoice['customer_id']][] = (in_to_array($invoice['inv_id'], $invoice['buy_inventory']) ? [$invoice['inv_id'], NULL, $invoice['tradein']] : [0 => $invoice['id'], 1 => $invoice['quantity'], 2 => $invoice['tradein'], 'price' => $invoice['price'], 'purchase' => $invoice['purchase_price']]);
						}
						if ((!in_array($invoice['pur_id'], $pur_ids)) AND $invoice['pur_id']) {
							$has_purchase = 1;
							$invoces_html .= '<div class="tr">
								<div class="td">
									<a href="/purchases/edit/'.$invoice['pur_id'].'" target="_blank">'.($invoice['sale_name'] ?: $invoice['pur_name']).'</a>
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100">$
									'.number_format($invoice['pur_price'], '2', '.', '').'
								</div>
								<div class="td w10">
									yes
								</div>
							</div>';
							$inv_total += $invoice['pur_price'];
							$pur_ids[] = $invoice['pur_id'];
						}
					} else {
						if ($invoice['inv_id']) {
							$invoces_html .= '<div class="tr">
									<div class="td">
										'.($invoice['type'] == 'stock' ? '<a href="/inventory/edit/'.$invoice['inv_id'].'" target="_blank">' : '').($invoice['catname'] ?? '').' '.$invoice['name'].($invoice['type'] == 'stock' ? '</a>' : '').'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100">$
										'.number_format($invoice['price'], '2', '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
						}
						if (!in_array($invoice['pur_id'], $pur_ids) AND $invoice['pur_id']) {
							$has_purchase = 1;
							$invoces_html .= '<div class="tr">
								<div class="td">
									<a href="/purchases/edit/'.$invoice['pur_id'].'">'.($invoice['sale_name'] ?: $invoice['pur_name']).'</a>
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100">$
									'.number_format($invoice['pur_price'], '2', '.', '').'
								</div>
								<div class="td w10">
									yes
								</div>
							</div>';
							$inv_total += $invoice['pur_price'];
							$pur_ids[] = $invoice['pur_id'];
						}
					}
					$inv_total += (in_to_array($invoice['inv_id'], $invoice['buy_inventory']) ? 0 : $invoice['price']);
					$inv_tradein += (in_to_array($invoice['inv_id'], $invoice['buy_inventory']) ? $invoice['purchase_price'] : 0);

					 if($now_invoce_id != $invoice['invoice_id'] AND $now_invoce_id != 0){
						if ($invoice['discount_name']) {
							$invoces_html .= '<div class="tr discountTr">
								<div class="td">
									'.$invoice['discount_name'].' '.$invoices[$index+1]['invoice_id'].' '.$now_invoce_id.'
								</div>
								<div class="td w10"></div>
								<div class="td w100">
									-'.$invoice['percent'].'%
								</div>
								<div class="td w10"></div>
							</div>';
						}
						$total += ($invoice['percent'] ? round(
								$inv_total * (100 - $invoice['percent']) / 100, 2
							) : $inv_total);
						$tradein += $inv_tradein;
						
						$invoices_arr[$invoice['invoice_id']] = $inv_total;
					} 
					$now_invoce_id = $invoice['invoice_id'];
					$index++;
				}
				$invoces_html .= '</div>';
			}

			$total += ($invoices[count($invoices) - 1]['percent'] ? round(
					$inv_total * (100 - $invoices[count($invoices) - 1]['percent']) / 100, 2
				) : $inv_total);
			$tradein += $inv_tradein;
			
			$tax = $row['purchace'] ? 0 : ($total - $onsite_total) * $row['object_tax'] / 100;
			
			$tax = $row['discount'] ? round(
				$tax * (100 - $row['discount']
			) / 100, 2) : $tax;
			
			$total = $row['discount'] ? round($total * (
				100 - $row['discount']) / 100, 2
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
										<div class="td">'.$h['date'].'</div>
										<div class="td">$'.number_format($h['amount'], 2, '.', '').'</div>
										<div class="td">'.$h['type'].'</div>
										<div class="td"><a href="/users/view/'.$h['staff_id'].'" onclick="Page.get(this.href)">'.$h['user_name'].' '.$h['user_lastname'].'</a></div>
									</div>';
				}
				$history_html .= '</div></div>';
			}
			
			
			// ---------------------------------- //
		// Transfer //
		/* if ($user['id'] == 17) {
		print_r($invoices_arr);
		die;
		} */

 		$full = abs($total + $tax - $tradein - $row['paid']);
		
		if ($full < 0.01 AND $row['conducted'] == 0 AND $row['transaction'] == 1) {
			db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
				conducted = 1,
				total = \''.(($total + $tax - $tradein)).'\'
			WHERE id = '.$id);
			
			if ($row['invoices']) {
				foreach($invoices_arr as $k => $inv) {
					db_query('UPDATE `'.DB_PREFIX.'_invoices` SET 
						conducted = 1,
						total = \''.($inv * (1 + $row['object_tax']/100)).'\',
						paid = \''.($inv * (1 + $row['object_tax']/100)).'\'
					WHERE id ='.$k);
				}
			}
			
			if ($invoice['inventory'] OR $invoice['inventory_ids']) {
				db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
						customer_id = '.$invoice['customer_id'].',
					owner_type = \'external\',
					object_owner = 0
				WHERE id IN('.(
					($invoice['inventory'] AND $invoice['inventory_ids']) ? $invoice['inventory_ids'].$invoice['inventory'] :
						($invoice['inventory'] ? $invoice['inventory'] : substr($invoice['inventory_ids'], 0, -1))
				).')');
			}
			
			if ($transfer_ids['inventory']) {
				foreach($transfer_ids['inventory'] as $k => $in_user) {
					foreach($in_user as $inv) {
						if ($inv[1] > 1) {
							db_query('INSERT INTO `'.DB_PREFIX.'_inventory` (model, os_id, price,object_id, options, type, type_id, category_id, model_id, owner_type, object_owner, customer_id, barcode, quantity
								) SELECT i.model, i.os_id, i.price,i.object_id, i.options, i.type, i.type_id, i.category_id, i.model_id, "external", 0, '.$k.', i.barcode, 1 FROM `'.DB_PREFIX.'_inventory` AS i WHERE i.id = '.$inv[0]
							);
							db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
								quantity = (quantity - 1)
							WHERE id  = '.$inv[0]);
						} else {
							db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
								customer_id = '.$k.',
								owner_type = \'external\',
								object_owner = 0 
							WHERE id  = '.$inv[0]);
						}
						
						if ($user['id'] == 17)
							$user['store_id'] = 2;
						
						if ($inv[2] == 1) {
							if($user['store_id'] > 0){
								$sql_ = db_multi_query('
									SELECT
										SUM(tb1.point) as sum,
										tb2.points
									FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
										 `'.DB_PREFIX.'_objects` tb2
									WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(NOW(), INTERVAL 1 HOUR) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
								);
								$points = (floatval($inv['price'])-floatval($inv['purchase']))*floatval($config['user_points']['trade_in']['selling'])/100;
								if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
									db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
										staff_id = '.$user['id'].',
										action = \'trade_in_selling\',
										min_rate = '.$sql_['points'].',
										object_id = '.$user['store_id'].',
										inventory_id = '.$inv[0].',
										point = \''.$points.'\''
									);	
									db_query(
										'UPDATE `'.DB_PREFIX.'_users`
											SET points = points+'.$points.'
										WHERE id = '.$user['id']
									);
								} else {
									db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
										staff_id = '.$user['id'].',
										action = \'trade_in_selling\',
										min_rate = '.$sql_['points'].',
										object_id = '.$user['store_id'].',
										inventory_id = '.$inv[0].',
										point = \''.$points.'\',
										rate_point = 1'
									);	
								}
							}
						}
					}
				}
			}
			
			if ($transfer_ids['tradein']) {
				foreach($transfer_ids['tradein'] as $tr_user) {
					foreach($tr_user as $tr_item) {
						db_query('UPDATE `'.DB_PREFIX.'_inventory` SET 
							object_owner = '.$row['object_id'].',
							owner_type = \'internal\',
							customer_id = 0, 
							quantity = 1,
							tradein = 1
						WHERE id = '.$tr_item[0]);
					}
				}
			}
		
		// ------------------------------------------------------------------------------- //
			if ($user['store_id'] > 0 AND $total > 50){
				$sql_ = db_multi_query('
					SELECT
						SUM(tb1.point) as sum,
						tb2.points
					FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
						 `'.DB_PREFIX.'_objects` tb2
					WHERE tb1.staff_id = '.$user['id'].' AND tb1.date >= DATE_SUB(
						NOW(), INTERVAL 1 HOUR
					) AND tb1.rate_point = 1 AND tb2.id = '.$user['store_id']
				);
				
				$points = floatval($config['user_points']['make_transaction']['points']);
				
				if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'make_transaction\',
						object_id = '.$user['store_id'].',
						invoice_id = '.$id.',
						min_rate = '.$sql_['points'].',
						point = \''.$points.'\''
					);	
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$points.'
						WHERE id = '.$user['id']
					);
				} else {
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'make_transaction\',
						min_rate = '.$sql_['points'].',
						point = \''.$points.'\',
						invoice_id = '.$id.',
						object_id = '.$user['store_id'].',
						rate_point = 1'
					);	
				}
			}
		}
		
		// ---------------------------------------------------------------------------------//
			
			$due = $total+$tax-$tradein - $row['paid'];
			if ($route[1] == 'view') {
				tpl_set('invoices/view', [
					'id' => $id,
					'total' => sprintf("%0.2f", ($total+$tax-$tradein)),
					'subtotal' => number_format($total-$tradein, '2', '.', ''),
					'paid' => number_format($row['paid'], '2', '.', ''),
					'tax' => number_format($tax, '2', '.', ''),
					'due' => sprintf("%0.2f", (abs($due) < 0.01 ? 0 : $due)),
					'customer-id' => $row['customer_id'],
					'customer-name' => $row['customer_name'],
					'customer-lastname' => $row['customer_lastname'],
					'customer-address' => $row['customer_address'],
					'inventory' => $tpl_content['inventory'],
					'purchases' => $tpl_content['purchases'],
					'object' => $row['object_id'],
					'discounts' => $options,
					'discount-name' => $row['discount_name'],
					'discount-percent' => $row['discount'],
					'purchace' => $row['purchace'],
					'date' => date('m-d-Y H:i:s', strtotime($row['date'])),
					'invoices' => $invoces_html,
					'issues' => $issue_mhtml,
					'history' => $history_html,
					'onsite' => $onsite
				], [
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'conducted' => ($row['conducted'] != 0 OR ($full < 0.01 AND $row['transaction'] == 1)),
					'refund_confirm' => ($row['refund'] == 1 AND $row['conducted'] == 1),
					'user_refund_confirm' => $user['confirm_refund'],
					'refund_request' => $row['refund'] == 0,
					'discount' => $row['discount'],
					'edit' => $route[1] == 'edit',
					'paid' => $row['paid'],
					'owner' => strrpos($user['group_ids'], '1') !== false,
					'purchace' => $row['purchace'],
					'can-edit' => (($row['conducted'] AND $user['edit_paid_invoices']) AND $row['transaction'] OR (!$row['conducted'] AND $user['edit_invoices'])),
					'has_purchase' => $has_purchase == 1
				], 'content');
			} elseif ($route[1] == 'send_mail') {
				$text = stripcslashes(str_ireplace([
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
					'{issue_status}'
				],[
					'<img src="http://'.$_SERVER['HTTP_HOST'].'/templates/admin/img/logo.svg" style="max-width: 300px">',
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
					$invoces_html,
					$issue_mhtml,
					preg_replace('/\[(\/?)edit\]/', '', $tpl_content['inventory']),
					$tpl_content['purchases'],
					$row['discount_name'],
					$row['discount'] ? '-'.$row['discount'].'%' : '',
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
					'$'.number_format($issues[0]['quote'], 2, '.', ''),
					$row['object_phone'],
					$row['object_name'],
					$row['object_address'],
					'',
					($row['staff_name'].' '.$row['staff_lastname'] ?: ''),
					$row['status_name'] ?: ''
				], $config['device_form']));
				
				// Headers
					$headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: '.$row['customer_email']. "\r\n";
					$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

					// Send
					if (mail($row['customer_email'], 'Your Company. Invoice #'.$id, $text, $headers))
						die('OK');
			} else {
				print stripcslashes(str_ireplace([
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
					'{issue_status}'
				],[
					'<img src="//'.$_SERVER['HTTP_HOST'].'/templates/admin/img/logo.svg" style="max-width: 300px">',
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
					$invoces_html,
					$issue_mhtml,
					preg_replace('/\[(\/?)edit\]/', '', $tpl_content['inventory']),
					$tpl_content['purchases'],
					$row['discount_name'],
					$row['discount'] ? '-'.$row['discount'].'%' : '',
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
					'$'.number_format($issues[0]['quote'], 2, '.', ''),
					$row['object_phone'],
					$row['object_name'],
					$row['object_address'],
					'',
					($row['staff_name'].' '.$row['staff_lastname'] ?: ''),
					$row['status_name'] ?: ''
				], $config['device_form']));
				die;
			}
		}
	break;
	
	/*
	*  Add/edit invoices
	*/
	case 'add':
	case 'edit':
	
		// Data
		$id = intval($route[2]);
		$meta['title'] = 'Invoice';
		$row = [];
		
		// Is edit
		if($route[1] == 'edit' AND $id){
			$total = 0;
			$tradein = 0;
			$tax = 0;
			$pur_ids = [];
            $onsite = '';
			if($row = db_multi_query('SELECT
				i.id,
				i.date,
				i.total,
				i.paid,
				i.tax,
				i.buy_inventory,
				i.inventory,
				i.services,
				i.invoices,
				i.issue_id,
				i.object_id,
				i.conducted,
				i.purchace,
				i.purchases,
				i.discount_id,
				i.customer_id,
                i.onsite_id,
                i.add_onsite,
                i.add_onsite_price,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.address as customer_address,
						d.percent as discount,
						d.name as discount_name,
							o.tax as object_tax,
                                os.name as onsite_name,
                                os.price as onsite_price,
                                uos_info.name as user_onsite_name,
                                SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time
			FROM `'.DB_PREFIX.'_invoices` i
				INNER JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_invoices_discount` d
			ON i.discount_id = d.id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = i.object_id
                LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
			ON uos.id = i.add_onsite
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
			ON uos_info.id = uos.onsite_id
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
			ON os.id = i.onsite_id
				WHERE i.id = '.$id
		)){
            if ($row['onsite_id']) {
                $onsite .= '<div class="tr" data-id="'.$row['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$row['onsite_id'].'">
                    <div class="td">
                        '.$row['onsite_name'].'
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_price">
                        $ '.$row['onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['onsite_price']);
            }
            if ($row['add_onsite']) {
                 $onsite .= '<div class="tr" data-id="'.$row['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$row['add_onsite'].'">
                    <div class="td">
                        '.$row['user_onsite_name'].'(Additional time - '.$row['user_onsite_time'].')
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_add_price nPay">
                        $ '.number_format($row['add_onsite_price'], 2, '.', '').'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['add_onsite_price']);
            }
			if($row['inventory'] OR $row['services'] OR $row['buy_inventory']){
				foreach(db_multi_query('
					SELECT
						IF(i.name = \'\', i.model, i.name) as name, 
						i.price, 
						i.purchase_price, 
						i.id, 
						i.type, 
						c.name as catname,
						m.name as model_name
					FROM `'.DB_PREFIX.'_inventory` i
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
						ON i.category_id = c.id
					LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
						ON i.model_id = m.id
					WHERE 1 '.(
							($row['inventory'] or $row['services']) ? 
								(' AND i.id IN('.(
									($row['inventory'] and $row['services']) ? $row['inventory'] . ',' . $row['services'] : (
										$row['inventory'] ? $row['inventory'] : $row['services'])
								).')'.($row['buy_inventory'] ? ' OR i.id IN('.$row['buy_inventory'].')' : '')) : 
								($row['buy_inventory'] ? ' AND i.id IN('.$row['buy_inventory'].')' : '')
						), true) as $item){
					tpl_set('invoices/viewItem', [
						'id' => $item['id'],
						'name' => $item['name'],
						'catname' => $item['catname'].' '.$item['model_name'],
						'price' => number_format((in_to_array($item['id'], $row['buy_inventory']) ? $item['purchase_price'] : $item['price']), '2', '.', ''),
						'sale' => number_format((in_to_array($item['id'], $row['buy_inventory']) ? $item['price'] : 0), '2', '.', ''),
						'type' => in_to_array($item['id'], $row['buy_inventory']) ? 'tradein' : $item['type']
					], [
						'tradein' => in_to_array($item['id'], $row['buy_inventory']),
						'stock' => $item['type'] == 'stock',
						'edit' => $route[1] == 'edit'
					], 'inventory');
					$total += in_to_array($item['id'], $row['buy_inventory']) ? 0 : $item['price'];
					$tradein += in_to_array($item['id'], $row['buy_inventory']) ? $item['purchase_price'] : 0;
				}
			}
			
			if($row['purchases']){
				foreach(db_multi_query('
					SELECT 
							p.name as pur_name, 
							p.sale_name, 
							p.price as pur_price, 
							p.sale as pur_sale, 
							p.id as pur_id
					FROM `'.DB_PREFIX.'_purchases` p
					WHERE p.id IN('.$row['purchases'].')', true) as $item){
						
							tpl_set('invoices/viewItem', [
								'id' => $item['pur_id'],
								'name' => $item['sale_name'] ?: $item['pur_name'],
								'price' => number_format(($item['pur_sale'] ?: 0), '2', '.', ''),
								'catname' => '',
								'type' => 'purchase'
							], [
								'tradein' => false
							], 'purchases');
							$total += ($item['pur_sale'] ?: 0);
							$pur_ids[] = $item['pur_ids'];
				}
			}
			
			$options = '';
			
			if($discounts = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices_discount` ORDER BY `id` LIMIT 0, 50', true)){
				foreach($discounts as $discount){
					$options .= '<option value="'.$discount['id'].'"'.(
						$discount['id'] == $row['discount_id'] ? ' selected' : ''
					).'>'.$discount['name'].'</option>';
				}
			}
			
			if($row['issue_id']){
				$issues_html = '';
				$issue_total = 0;
				$pur_ids = [];
				$inv_ids = [];
				$issues = db_multi_query('SELECT
					tb1.id as issue_id,
					tb1.doit,
					tb1.purchase_prices,
					tb1.service_ids,
					tb1.upcharge_id,
					IF(tb2.name = \'\', tb2.model, tb2.name) as name,
						tb2.price,
						tb2.type,
						tb2.id as inv_id,
							tb3.name as catname,
								tb4.id as pur_id,
								tb4.name as pur_name,
								tb4.total as pur_or_price,
								tb4.sale as pur_sale_price,
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
				
				$cont_service = 0;
				$servs = [];
				foreach (array_values(array_filter($issues, function($v) {
					if ($v['type'] == 'service' AND $v['price'] > 0)
						return $v;
				}, ARRAY_FILTER_USE_BOTH)) as $s) {
					if (!in_array($s['inv_id'], $servs))
						$cont_service += substr_count($issues[0]['service_ids'], $s['inv_id'].'_');
					$servs[] = $s['inv_id'];
				}
				$inv_price = ($cont_service > 0) ? $issues[0]['inv_service'] / $cont_service : 0;
				
				foreach($issues as $issue){
					if (!in_array($issue['inv_id'], $inv_ids) AND $issue['inv_id']) {
						if ($issue['type'] == 'service') {
							$count = substr_count($issue['service_ids'], $issue['inv_id'].'_');
						} else 
							$count = 1;
						for($i = 0; $i < $count; $i ++) {
							$issues_html .= '
								<div class="tr">
									<div class="td isItem">
										'.($issue['catname'] ? $issue['catname'].' '.$issue['model_name'] : '').' '.$issue['name'].'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.(
											(floatval($issue['doit']) > 0) ? '' : '$'.number_format(floatval(($issue['price'] > 0 AND $issue['type'] == 'service') ? $issue['price'] + $inv_price : $issue['price']), '2', '.', '')
										).'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
							if (floatval($issue['doit']) == 0){
								$issue_total += ($issue['price'] > 0 AND $issue['type'] == 'service') ? $issue['price'] + $inv_price : $issue['price'];
							} 
						}
						$inv_ids[] = $issue['inv_id'];
					}
					if (!in_array($issue['pur_id'], $pur_ids) AND $issue['pur_id'] > 0) {
						$issues_html .= '<div class="tr">
							<div class="td isItem">
								'.$issue['pur_name'].'
							</div>
							<div class="td w10">
								1
							</div>
							<div class="td w100 nPay">
								'.(
									(floatval($issue['doit']) > 0) ? '' : '$'.number_format(($issue['pur_price'] ?: ($issue['pur_sale_price'] ?: 0)), '2', '.', '')
								).'
							</div>
							<div class="td w10">
								yes
							</div>
						</div>';
						if (floatval($issue['doit'] == 0)){
							$issue_total += ($issue['pur_price'] ?: ($issue['pur_sale_price'] ?: 0));
						} 
						$pur_ids[] = $issue['pur_id'];
					}
				}
				
				$issue_mhtml = '<div class="tr">
								<div class="td">
									<b>Issue #'.$issues[0]['issue_id'].'</b>
								</div>
								<div class="td w10"></div>
								<div class="td w100"><b>$'.number_format((
									(floatval($issue['doit']) > 0) ? $issue['doit'] : $issue_total
								), '2', '.', '').'</b></div>
								<div class="td w10">
									yes
								</div>
							</div>'.$issues_html;
				
				$total += (floatval($issue['doit']) > 0) ? $issue['doit'] : $issue_total;
			}
			
			
				if($row['invoices']){
					$now_invoce_id = 0;
					$invoces_html = '';
					$index = 0;
					$pur_ids = [];
					$invoices = db_multi_query('SELECT
						tb1.id as invoice_id,
						tb1.total as invoice_total,
						tb1.paid as invoice_paid,
						tb1.tax as invoice_tax,
						tb1.issue_id,
						IF(tb2.name = \'\', tb2.model, tb2.name) as name,
							tb2.price,
							tb2.purchase_price,
							tb2.type,
							tb2.id as inv_id,
							tb2.quantity,
								tb3.name as catname,
									tb4.name as discount_name,
									tb4.percent,
										tb5.id as pur_id,
										tb5.name as pur_name,
										tb5.sale_name,
										tb5.sale as pur_sale,
											tb6.tax as object_tax,
												m.name as model_name,
                                                    tb1.onsite_id,
                                                    tb1.add_onsite,
                                                    tb1.add_onsite_price,
                                                    os.name as onsite_name,
                                                    os.price as onsite_price,
                                                    uos_info.name as user_onsite_name,
                                                    SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time
							FROM `'.DB_PREFIX.'_invoices` tb1
						LEFT JOIN `'.DB_PREFIX.'_inventory` tb2
							ON FIND_IN_SET(tb2.id, CONCAT(tb1.inventory, ",", tb1.services))
						LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb3
							ON tb2.category_id = tb3.id
						LEFT JOIN `'.DB_PREFIX.'_invoices_discount` tb4
							ON tb1.discount_id = tb4.id
						LEFT JOIN `'.DB_PREFIX.'_purchases` tb5
							ON FIND_IN_SET(tb5.id, tb1.purchases)
						LEFT JOIN `'.DB_PREFIX.'_objects` tb6
							ON tb6.id = tb1.object_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
							ON tb2.model_id = m.id
                        LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
			                ON uos.id = tb1.add_onsite
                        LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
			                ON uos_info.id = uos.onsite_id
                        LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
			                ON os.id = tb1.onsite_id
						WHERE tb1.id IN('.$row['invoices'].') ORDER BY tb1.id LIMIT 0, 50'
					, true);
			
					$issues_html_inv = '';
					$issue_total_inv = 0;
					$pur_ids_inv = [];
					$issues_inv = db_multi_query('SELECT
						tb1.id as issue_id,
						tb1.doit,
						tb1.service_ids,
						tb1.purchase_prices,
						IF(tb2.name = \'\', tb2.model, tb2.name) as name,
							tb2.price,
							tb2.purchase_price,
							tb2.id as inv_id,
							tb2.type,
							tb2.quantity,
								tb3.name as catname,
									tb4.id as pur_id,
									tb4.name as pur_name,
									tb4.sale_name,
									tb4.sale as pur_sale,
									REGEXP_REPLACE(tb1.purchase_prices, CONCAT(\'{(.*)?"\', tb4.id, \'":"(.*)",(.*)?}\'), \'\\\2\') as pur_price,
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
						LEFT JOIN `'.DB_PREFIX.'_invoices` tb5
							ON tb5.issue_id = tb1.id
						LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
							ON tb2.model_id = m.id
						WHERE tb5.id IN('.$row['invoices'].') LIMIT 0, 50'
					, true);
					
					// issue html
					$iss_index = 0;
					$issue_mhtml_inv = [];
					$issue_mhtml_inv[$issues_inv[0]['issue_id']] = '';
					$issue_id = $issues_inv[0]['issue_id'];
					$issue_servs = [];
					
					foreach($issues_inv as $issue){
						$cont_service_iss = 0;
						$servs = [];
						foreach (array_values(array_filter($issues_inv, function($v) use(&$issue_id) {
							if ($v['type'] == 'service' AND $v['price'] > 0 AND $v['issue_id'] == $issue_id)
								return $v;
						}, ARRAY_FILTER_USE_BOTH)) as $s) {
							if (!in_array($s['inv_id'], $servs))
								$cont_service_iss += substr_count($issue['service_ids'], $s['inv_id'].'_');
							$servs[] = $s['inv_id'];
						}
						$inv_price = ($cont_service_iss > 0) ? $issue['inv_service'] / $cont_service_iss : 0;
						if (!in_array($issue['inv_id'], $issue_servs)) {
							$issues_html_inv .= '
								<div class="tr">
									<div class="td isItem">
										'.($issue['catname'] ? $issue['catname'].' '.$issue['model_name'] : '').' '.$issue['name'].'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.(
											$issue['doit'] ? '' : '$'.number_format(((($issue['price'] > 0 AND $issue['type'] == 'service') ? ($issue['price'] + $inv_price) : $issue['price'])), '2', '.', '')
										).'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
								
								$issue_total_inv += in_to_array($issue['inv_id'], $issue['buy_inventory']) ? 0 : (($issue['price'] > 0 AND $issue['type'] == 'service') ? ($issue['price'] + $inv_price) : $issue['price']);
								$issue_tradein_inv += in_to_array($issue['inv_id'], $issue['buy_inventory']) ? $issue['purchase_price'] : 0;
								$issue_servs[] = $issue['inv_id'];
						}
						if ((!in_array($issue['pur_id'], $pur_ids_inv)) AND $issue['pur_id']){
							$issues_html_inv .= '<div class="tr">
								<div class="td isItem">
									'.($issue['sale_name'] ?: $issue['pur_name']).'
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 nPay">
									'.(
										$issue['doit'] ? '' : '$'.number_format($issue['pur_price'], '2', '.', '')
									).'
								</div>
								<div class="td w10">
									yes
								</div>
							</div>';
							if (!$issue['doit']){
								$issue_total_inv += ($issue['price'] ?: ($issue['pur_sale'] ?: 0));
							} 
							$pur_ids_inv[] = $issue['pur_id'];
						}
						
					
						if ($issues_inv[$iss_index+1]['issue_id'] != $issue['issue_id']) {
							$issue_id = $issues_inv[$iss_index+1]['issue_id'];
							$issue_mhtml_inv[$issue['issue_id']] .= '<div class="tr">
											<div class="td">
												<b>Issue #'.$issue['issue_id'].'</b>
											</div>
											<div class="td w10"></div>
											<div class="td w100"><b>'.number_format((
												$issue['doit'] ? $issue['doit'] : $issue_total_inv
											), '2', '.', '').'$</b></div>
											<div class="td w10">
												yes
											</div>
										</div>'.$issues_html_inv;
							$issue_mhtml_inv[$issues_inv[$iss_index+1]['issue_id']] = '';
							$total += $issue['doit'] ? $issue['doit'] : $issue_total_inv;
							$issues_html_inv = '';
							$issue_total_inv = 0;
						}
						$iss_index ++;			
					}
				
					// invoices html
					$inv_total = 0;
					foreach($invoices as $invoice){
						if($now_invoce_id != $invoice['invoice_id']){
                            $invoice_onsite = '';
                            if ($invoice['onsite_id']) {
                                $invoice_onsite .= '<div class="tr" data-id="'.$invoice['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$invoice['onsite_id'].'">
                                    <div class="td">
                                        '.$invoice['onsite_name'].'
                                    </div>
                                    <div class="td w10">
                                        1
                                    </div>
                                    <div class="td w100 onsite_price">
                                        $ '.$invoice['onsite_price'].'
                                    </div>
                                    <div class="td w10">
                                        no
                                    </div>
                                </div>';
                                $total += floatval($invoice['onsite_price']);
                            }
                            if ($invoice['add_onsite']) {
                                $invoice_onsite .= '<div class="tr" data-id="'.$invoice['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$invoice['add_onsite'].'">
                                    <div class="td">
                                        '.$invoice['user_onsite_name'].'(Additional time - '.$invoice['user_onsite_time'].')
                                    </div>
                                    <div class="td w10">
                                        1
                                    </div>
                                    <div class="td w100 onsite_price">
                                        $ '.$invoice['add_onsite_price'].'
                                    </div>
                                    <div class="td w10">
                                        no
                                    </div>
                                </div>';
                                $total += floatval($invoice['add_onsite_price']);
                            }
							if($now_invoce_id != 0){
								$invoces_html .= '</div>';
							}
							$invoces_html .= '<div class="tbl payInfo">
								<div class="tr">
									<div class="th">
										<span class="fa fa-times del" onclick="invoices.delInInvoice(this)"></span>
										Invoice #'.$invoice['invoice_id'].'
									</div>
									<div class="th w10"></div>
									<div class="th w100"></div>
									<div class="th w10"></div>
								</div>'.(
									$invoice['issue_id'] ? $issue_mhtml_inv[$invoice['issue_id']] : ''
								).(
									$invoice_onsite ?: ''
								).($invoice['inv_id'] ? '<div class="tr">
									<div class="td">
										'.($invoice['catname'] ? $invoice['catname'].' '.$invoice['model_name'] : '').' '.$invoice['name'].'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100">$
										'.number_format($invoice['price'], '2', '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>' : '');
							if ((!in_array($invoice['pur_id'], $pur_ids)) AND $invoice['pur_id']){
								$invoces_html .= '<div class="tr">
									<div class="td">
										'.($invoice['sale_name'] ?: $invoice['pur_name']).'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100">$
										'.number_format(($invoice['pur_sale'] ?: 0), '2', '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
								$inv_total += $invoice['pur_sale'] ?: 0;
								$pur_ids[] = $invoice['pur_id'];
							}
						} else {
							$invoces_html .= '<div class="tr">
									<div class="td">
										'.($invoice['catname'] ? $invoice['catname'].' '.$invoice['model_name'] : '').' '.$invoice['name'].'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100">$
										'.number_format($invoice['price'], '2', '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
							if ((!in_array($invoice['pur_id'], $pur_ids)) AND $invoice['pur_id']) {
								$invoces_html .= '<div class="tr">
									<div class="td">
										'.($invoice['sale_name'] ?: $invoice['pur_name']).'
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100">$
										'.number_format(($invoice['pur_sale'] ?: 0), '2', '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
								$inv_total += ($invoice['pur_sale'] ?: 0);
								$pur_ids[] = $invoice['pur_id'];
							}
						}
						if ($invoice['price']) 
							$inv_total += $invoice['price'];

						if($invoices[$index+1]['invoice_id'] != $invoice['invoice_id']){
							if ($invoice['discount_name']) {
								$invoces_html .= '<div class="tr discountTr">
									<div class="td">
										'.$invoice['discount_name'].' '.$invoices[$index+1]['invoice_id'].' '.$now_invoce_id.'
									</div>
									<div class="td w10"></div>
									<div class="td w100">
										-'.$invoice['percent'].'%
									</div>
									<div class="td w10"></div>
								</div>';
							}
							$total += ($invoice['percent'] ? round(
									$inv_total * (100 - $invoice['percent']) / 100, 2
								) : $inv_total);
						}
						$now_invoce_id = $invoice['invoice_id'];
						$index++;
					}
					$invoces_html .= '</div>';
				}
				
				$tax = $row['purchace'] ? 0 : $total * $row['object_tax'] / 100;
				
				$tax = $row['discount'] ? round(
					$tax * (100 - $row['discount']
				) / 100, 2) : $tax;
				
				$total = $row['discount'] ? round($total * (
					100 - $row['discount']) / 100, 2
				) : $total;
			
			}
		}
		
		$options = '';
		if($discounts = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices_discount` ORDER BY `id` LIMIT 0, 50', true)){
			foreach($discounts as $discount){
				$options .= '<option value="'.$discount['id'].'"'.(
					$discount['id'] == $row['discount_id'] ? ' selected' : ''
				).'>'.$discount['name'].'</option>';
			}
		}

		if (intval($_GET['user']) AND $route[1] == 'add') {
			$usr = db_multi_query('SELECT name, lastname FROM `'.DB_PREFIX.'_users` WHERE id = '.intval($_GET['user']));
		}
		$due = $total+$tax-$tradein-$row['paid'];
		if ($route[1] == 'add' OR $route[1] == 'edit' AND (($row['conducted'] AND $user['edit_paid_invoices']) OR (!$row['conducted'] AND $user['edit_invoices']))) {
			tpl_set('invoices/form', [
				'id' => $id,
				'customer-id' => $row['customer_id'] ?: intval($_GET['user']),
				'customer-name' => (intval($_GET['user']) AND $route[1] == 'add') ? $usr['name'] : $row['customer_name'],
				'customer-lastname' => (intval($_GET['user']) AND $route[1] == 'add') ? $usr['lastname'] : $row['customer_lastname'],
				'customer-address' => $row['customer_address'],
				'date' => $row['date'],
				'total' => sprintf("%0.2f", ($total+$tax-$tradein)),
				'paid' => number_format($row['paid'], '2', '.', ''),
				'inventory' => $tpl_content['inventory'],
				'purchases' => $tpl_content['purchases'],
				'tax' => number_format($tax, '2', '.', ''),
				'object' => $row['object_id'],
				'onsite' => $onsite,
				'object-name' => $row['object_name'],
				'object-tax' => $row['object_tax'],
				'due' => sprintf("%0.2f", (abs($due) < 0.01 ? 0 : $due)),
				'subtotal' => number_format($total-$tradein, '2', '.', ''),
				'discounts' => $options,
				'discount-name' => $row['discount_name'],
				'discount-percent' => $row['discount'],
				'invoices' => $invoces_html,
				'issues' => $issue_mhtml,
				'user-id' => intval($_GET['user']) ?: 0,
				'inventorys' => json_encode($row['object_name'] ? [
					$row['object_id'] => [
						'name' => $row['object_name']
					]] : [])
			], [
				'edit' => ($route[1] == 'edit'),
				'add' => ($route[1] == 'add'),
				'discount' => $row['discount'],
				'user' => (intval($_GET['user']) AND $route[1] == 'add')
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => 'You hav no access to do this'
			], [
			], 'content');
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
			db_query('DELETE FROM `'.DB_PREFIX.'_invoices_history` WHERE invoice_id = '.$id);
			db_query('DELETE FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		//} else
		//	exit('ERR');
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
	* All invoices
	*/
	case 'form':
		$meta['title'] = 'Form';
		tpl_set('invoices/printForm', [
			'content' => stripslashes($config['device_form'])
		], [], 'content');
	break;
	
	/*
	* All invoices
	*/
	case 'history':
		$meta['title'] = 'Invoice history';
		$query = text_filter($_POST['query'], 255, false);
		$type = text_filter($_POST['type'], 10, false);
		$status = text_filter($_POST['status'], 10, false);
		$page = intval($_POST['page']);
		$object = intval($_POST['object']);
		$customer = intval($_POST['staff']);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		
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
			INNER JOIN `'.DB_PREFIX.'_users` s
				ON h.staff_id = s.id
			WHERE 1 '.(
			$query ? 'AND MATCH(
					u.name, u.lastname, u.email, u.phone
				) AGAINST (
					\'*'.$query.'*\' IN BOOLEAN MODE
				)  ' : ''
		).(
			$status === 'paid' ? 'AND i.conducted = 1 ' : ($status === 'unpaid' ? 'AND i.conducted = 0 ' : '')
		).(
			$type ? 'AND h.type = \''.$type.'\' ' : ''
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
					'staff-lastname' => $row['staff_lastname']
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
                $type ? 'AND h.type = \''.$type.'\' ' : ''
            ).(
                $object ? 'AND i.object_id = '.$object.' ' : ''
            ).(
                $customer ? 'AND i.customer_id = '.$customer.' ' : ''
            ).(
                ($date_start AND $date_finish) ? ' AND h.date >= CAST(\''.$date_start.'\' AS DATE) AND h.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME) ' : ''
            )
            );
        }

		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['invoices'] ?: '<div class="noContent">No history</div>',
                'total' => number_format($total['total'], 2, '.', '')
			]));
		}
		tpl_set('invoices/history', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'invoices' => $tpl_content['invoices'],
            'total' => number_format($total['total'], 2, '.', '')
		], [], 'content');
	break;
	
	
	/*
	* All invoices
	*/
	case 'refund':
	default:
		$meta['title'] = 'Invoices';
		$query = text_filter($_POST['query'], 255, false);
		$type = text_filter($_POST['type'], 10, false);
		$status = text_filter($_POST['status'], 10, false);
		$page = intval($_POST['page']);
		$object = intval($_POST['object']);
		$customer = intval($_POST['staff']);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$action = text_filter($_POST['action'], 3, true);
		
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
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.phone as customer_phone,
						ru.id as refund_id,
						ru.name as refund_name,
						ru.lastname as refund_lastname
			FROM `'.DB_PREFIX.'_invoices` i
				INNER JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_users` ru
			ON i.refund_user = ru.id
			WHERE 1 '.(
			$query ? 'AND MATCH(
					u.name, u.lastname, u.email, u.phone
				) AGAINST (
					\'*'.$query.'*\' IN BOOLEAN MODE
				)  ' : ''
		).(
			$status === 'paid' ? 'AND i.conducted = 1 ' : ($status === 'unpaid' ? 'AND i.conducted = 0 ' : '')
		).(
			$type ? 'AND i.pay_method = \''.$type.'\' ' : ''
		).(
			$object ? 'AND i.object_id = '.$object.' ' : ''
		).(
			$customer ? 'AND i.customer_id = '.$customer.' ' : ''
		).(
			$route[1] == 'refund' ? 'AND i.refund = 1 ' : ''
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
					'customer-phone' => $row['customer_phone'],
					'refund' => $row['refund'] == 1 ? 'refund' : '',
					'refund-id' => $row['refund_id'],
					'refund-name' => $row['refund_name'],
					'refund-lastname' => $row['refund_lastname']
				], [
					'conducted' => $row['conducted'],
					'refund_confirm' => $row['refund'] == 1 AND $row['conducted'] == 1,
					'user_refund_confirm' => $user['confirm_refund'],
					'refund_request' => $row['refund'] == 0,
					'paid' => $row['paid'],
					'owner' => strrpos($user['group_ids'], '1') !== false,
					'edit' => ($row['conducted'] AND $user['edit_paid_invoices']) OR (!$row['conducted'] AND $user['edit_invoices'])
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
                    $query ? 'AND MATCH(
                            u.name, u.lastname, u.email, u.phone
                        ) AGAINST (
                            \'*'.$query.'*\' IN BOOLEAN MODE
                        )  ' : ''
                ).(
                    $status === 'paid' ? 'AND i.conducted = 1 ' : ($status === 'unpaid' ? 'AND i.conducted = 0 ' : '')
                ).(
                    $type ? 'AND i.pay_method = \''.$type.'\' ' : ''
                ).(
                    $object ? 'AND i.object_id = '.$object.' ' : ''
                ).(
                    $customer ? 'AND i.customer_id = '.$customer.' ' : ''
                ).(
					$route[1] == 'refund' ? 'AND i.refund = 1 ' : ''
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
            'total' => number_format($total['total'], 2, '.', '')
		], [], 'content');
}
 ?>