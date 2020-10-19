<?php
/**
 * @appointment Purchases admin panel
 * @author      Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$objects_ip = array_flip($config['object_ips']);

if($user['purchase'] > 0){
	switch($route[1]){
		/*
		* Receive purchase
		*/
		case 'receive':
			$id = intval($_POST['id']);
			$type = intval($_POST['type']);
			$brand = intval($_POST['brand']);
			$model = intval($_POST['model']);
			$model_new = text_filter($_POST['model_new'], 50, false);
			$stock_id = 0;
			
			$p = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$id);
			
			if (!$p['customer_id'] AND !$p['invoice_id'] AND !$p['issue_id']) {
				if (!$type OR !$brand OR (!$model AND !$model_new))
					die('no_info');
				else {
					if (!$model) {
						$mdl = [];
						$mdl = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_inventory_models` WHERE name = \''.trim(text_filter($_POST['model_new'], 100, false)).'\' AND category_id = '.$brand);
						if ($mdl != []) die('mdl_exists');
						$model = db_query('INSERT INTO `'.DB_PREFIX.'_inventory_models` SET name = \''.trim(text_filter($_POST['model_new'], 100, false)).'\''.($brand ? ', category_id = '.$brand : ''));
						$model = intval(
							mysqli_insert_id($db_link)
						);
					}
					
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory` SET
						model_id =\''.$model.'\',
						currency = \''.text_filter($p['currency'] ?: 'USD', 25, false).'\',
						purchase_currency = \''.text_filter($p['purchase_currency'] ?: 'USD', 25, false).'\',
						object_owner =\''.intval($p['object_id']).'\',
						price =\''.floatval($p['sale']).'\',
						quantity =\''.intval($p['quantity']).'\',
						purchase_price =\''.floatval($p['price']).'\',
						type_id = \''.$type.'\',
						object_id = \''.$p['object_id'].'\',
						category_id = \''.$brand.'\',
						type = \'stock\',
						cr_user = \''.$user['id'].'\',
						cr_date = \''.date('Y-m-d H:i:s').'\','.(
							in_to_array('1,2', $user['group_ids']) ? 'confirmed = 1 ' : 'confirmed = 0 '
						)
					);
					
					$stock_id = intval(mysqli_insert_id($db_link));
				}
			}
			
			db_query('UPDATE `'.DB_PREFIX.'_purchases` SET
				recived_id = '.$user['id'].',
				recived_date = \''.date('Y-m-d H:i:s', time()).'\',
				stock_id = '.$stock_id.'
				WHERE id = '.$id
			);
			
			if ($p['issue_id']) 
				db_query('UPDATE `'.DB_PREFIX.'_issues` SET staff_id = '.$user['id'].' WHERE id = '.$p['issue_id']);
			
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'purchases\'');
			
			die('OK');
		break;
		
		/*
		* Get objects
		*/
		case 'all':
			$lId = intval($_POST['lId']);
			$store = intval($_POST['store']);
			$object_id = intval($_POST['object_id']);
			$nIds = ids_filter($_POST['nIds']);
			$query = text_filter($_POST['query'], 100, false);
			$purchases = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS 
					p.id, 
					REPLACE(IF(p.sale_name = \'\', p.name, p.sale_name), \'"\', \'\') as name, 
					p.price as cost_price, 
					p.sale as price, 
					p.currency,
					o.name as object
				FROM `'.DB_PREFIX.'_purchases` p
				LEFT JOIN `'.DB_PREFIX.'_objects` o 
					ON o.id = p.object_id
				WHERE p.del = 0 AND p.status NOT IN (\'Rejected\')'.(
				$nIds ? ' AND p.id NOT IN('.$nIds.')' : ''
			).(
				$lId ? ' AND p.id < '.$lId : ''
			).(
				$object_id ? ' AND p.object_id = '.$object_id : ''
			).(
				$store ? ' AND p.issue_id = 0 AND p.invoice_id = 0 ' : ''
			).(
				$query ? ' AND IF(p.sale_name = \'\', p.name, p.sale_name) LIKE \'%'.$query.'%\'' : ''
			).' ORDER BY p.id DESC LIMIT 20', true);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $purchases,
				'count' => $res_count,
			]));
		break;
		
		/*
		*  Add/edit Purchase
		*/
		case 'add':
		case 'edit':
			$id = intval($route[2]);
			$meta['title'] = $id ? 'Edit purchase' : 'Add purchase';
			if($route[1] == 'add' OR ($route[1] == 'edit' AND $id)){
				$row = [];
				if ($id) {
					$row = db_multi_query('
						SELECT
							tb1.*,
							tb2.id as customer_id,
							tb2.name as customer_name,
							tb2.lastname as customer_lastname,
							tb3.name as object_name,
							cr.image as create_image,
							cr.name as create_name,
							cr.lastname as create_lastname,
							cn.image as confirm_image,
							cn.name as confirm_name,
							cn.lastname as confirm_lastname,
							ed.image as edited_image,
							ed.name as edited_name,
							ed.lastname as edited_lastname,
							iinv.conducted as iss_conducted,
							iinv.paid as iss_paid,
							iinv.id as iinv_id,
							iinv.issue_info as iinv_issue,
							iinv.conducted as iss_conducted,
							iss.id as issue_id,
							iss.purchase_done as purchase_done,
							inv.conducted as inv_conducted,
							inv.id as inv_id,
							inv.paid as inv_paid,
							inv.conducted as inv_conducted,
							inv.purchases_info as inv_purchases,
							i.customer_id as i_cid
						FROM `'.DB_PREFIX.'_purchases` tb1
						LEFT JOIN `'.DB_PREFIX.'_users` tb2
							ON tb1.customer_id > 0
							AND tb1.customer_id = tb2.id
						LEFT JOIN `'.DB_PREFIX.'_users` cr
							ON tb1.create_id > 0
							AND tb1.create_id = cr.id
						LEFT JOIN `'.DB_PREFIX.'_users` cn
							ON tb1.confirm_id > 0
							AND tb1.confirm_id = cn.id
						LEFT JOIN `'.DB_PREFIX.'_users` ed
							ON tb1.edited_id > 0
							AND tb1.edited_id = ed.id
						LEFT JOIN `'.DB_PREFIX.'_objects` tb3
							ON tb3.id = tb1.object_id
						LEFT JOIN `'.DB_PREFIX.'_issues` iss
							ON tb1.issue_id = iss.id
						LEFT JOIN `'.DB_PREFIX.'_invoices` iinv 
							ON iinv.issue_id = iss.id
						LEFT JOIN `'.DB_PREFIX.'_inventory` i 
							ON i.id = iss.inventory_id
						LEFT JOIN `'.DB_PREFIX.'_invoices` inv
							ON tb1.invoice_id = inv.id
						WHERE tb1.id = '.$id);
				}
				
				if (!$id) {
					$usr = db_multi_query('SELECT name, lastname FROM `'.DB_PREFIX.'_users` WHERE id = '.intval($_GET['usr']));
				}
				
				//if($user['id'] == 16){
				//	echo 'SELECT id FROM `'.DB_PREFIX.'_purchases` WHERE issue_id = '.$row['issue_id'].' AND link = \''.$row['link'].'\' AND id != '.$id;
			//		die;
				//}
				
				$issue = '';
				if ($row['issue_id'] AND $same_purchases = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_purchases` WHERE issue_id = '.$row['issue_id'].' AND link = \''.$row['link'].'\' AND del = 0 AND id != '.$id, true)) {
					foreach($same_purchases as $sp) {
						$issue .= '<a href="/purchases/edit/'.$sp['id'].'">Purchase #'.$sp['id'].'</a> ';
					}
				}
				
				if ($row['issue_id']) {
					$issue_status = db_multi_query('
						SELECT 
							s.name 
						FROM `'.DB_PREFIX.'_issues_changelog` cl
						LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
							ON s.id = cl.changes_id AND cl.changes = \'status\'
						WHERE cl.issue_id = '.$row['issue_id'].' AND cl.changes = \'status\'
						ORDER BY cl.id DESC
						LIMIT 0,1
					');
				}
				
				
				$currency = '';
				$purchase_currency = '';
				foreach($config['currency'] as $k => $c) {
					$currency .= '<option value="'.$k.'"'.($k == $row['currency']).'>'.$k.' ('.$c['symbol'].')</option>';
					$purchase_currency .= '<option value="'.$k.'"'.($k == $row['purchase_currency']).'>'.$k.' ('.$c['symbol'].')</option>';
				}
				
				$statuses = ['Purchased','Active','CancelPending','Completed','Inactive','Shipped','Rejected'];
				$options = '';
				foreach($statuses as $status){
					$options .= '<option value="'.$status.'"'.(
						$status == $row['status'] ? ' selected' : ''
					).'>'.$status.'</option>';
				}
				
				$can_cofirm = 1;
				$merged = [];
				
				if ($row['inv_id'] OR $row['iinv_id']) {
					$merged = db_multi_query('SELECT id, paid, invoices, conducted FROM `'.DB_PREFIX.'_invoices` WHERE FIND_IN_SET('.(
						$row['inv_id'] ? $row['inv_id'] : $row['iinv_id']
					).', invoices)');
					
					if ($merged) {
						if (!$merged['paid'])
							$can_cofirm = 0;
						else {
							$ptotal = 0;
							if ($minvoices = db_multi_query('SELECT purchases_info, issue_info FROM `'.DB_PREFIX.'_invoices` WHERE id IN ('.$merged['invoices'].','.$merged['id'].')', true)) {
								foreach($minvoices as $mi) {
									$ipurchases = json_decode($mi['purchases_info'], true);
									$iissue = json_decode($mi['issue_info'], true);
									if (is_array($ipurchases) AND count($ipurchases)) {
										foreach($ipurchases as $pi) {
											$ptotal += floatval(preg_replace('/[^0-9.]/i', '', $pi['price']));
										}
									}
									if (is_array($iissue) AND count($iissue['purchases'])) {
										foreach($iissue['purchases'] as $ii) {
											$ptotal += floatval(preg_replace('/[^0-9.]/i', '', $ii['price']));
										}
									}
								}
								
								if ($ptotal > $merged['paid'])
									$can_cofirm = 0;
								
									if ($merged['conducted'] == 1)
										$can_confirm = 1;
							}
						}
					} else {
						if ($merged['conducted'] == 1)
							$can_confirm = 1;
					}
				}
				
				$recived = $row['recived_id'] ? ' > 0' : ' = 0';
				
				if ($id AND (!$row['confirmed'] OR !$row['recived_id'])) {
					$prev = db_multi_query('
						SELECT 
							p.id
						FROM `'.DB_PREFIX.'_purchases` p
						LEFT JOIN `'.DB_PREFIX.'_invoices` i
							ON i.id = p.invoice_id
						LEFT JOIN `'.DB_PREFIX.'_invoices` ii
							ON ii.issue_id = p.issue_id
						LEFT JOIN `'.DB_PREFIX.'_issues` iss
							ON iss.id = p.issue_id
						WHERE p.del = 0 AND p.id < '.$id.' AND p.confirmed = '.$row['confirmed'].' AND p.recived_id '.$recived.' AND IF(p.invoice_id > 0, (i.paid >= p.price*p.quantity OR i.conducted = 1), IF(p.issue_id > 0, (iss.purchase_done = 1 AND ii.id > 0 AND (ii.paid >= p.price*p.quantity OR ii.conducted = 1)), (p.invoice_id = 0 AND p.issue_id = 0)))
						ORDER BY p.id DESC LIMIT 1');
					$next = db_multi_query('
						SELECT 
							p.id
						FROM `'.DB_PREFIX.'_purchases` p
						LEFT JOIN `'.DB_PREFIX.'_invoices` i
							ON i.id = p.invoice_id
						LEFT JOIN `'.DB_PREFIX.'_invoices` ii
							ON ii.issue_id = p.issue_id
						LEFT JOIN `'.DB_PREFIX.'_issues` iss
							ON iss.id = p.issue_id
						WHERE p.del = 0 AND p.id > '.$id.' AND p.confirmed = '.$row['confirmed'].' AND p.recived_id '.$recived.' AND IF(p.invoice_id > 0, (i.paid >= p.price*p.quantity OR i.conducted = 1), IF(p.issue_id > 0, (iss.purchase_done = 1 AND ii.id > 0 AND (ii.paid >= p.price*p.quantity OR ii.conducted = 1)), (p.invoice_id = 0 AND p.issue_id = 0)))
						ORDER BY p.id DESC LIMIT 1');
				}
				
				if ($row['inv_id'] AND $row['inv_purchases'] AND $row['inv_purchases'] != '{}' AND is_array(json_decode($row['inv_purchases'], true)))
					$inv_purchases = json_decode($row['inv_purchases'], true);
				
				if ($row['iinv_id'] AND $row['iinv_issue'] AND $row['iinv_issue'] != '{}' AND is_array(json_decode($row['iinv_issue'], true)))
					$iinv_purchases = json_decode($row['iinv_issue'], true)['purchases'];
				
				if (($route[1] == 'add' AND $user['add_purchase']) OR $route[1] == 'edit') {
					/* if ($user['id'] == 17) {
					print_r($row);
					die;
					} */
					
/* 					if($user['id'] == 16){
if($row['issue_id'] AND $row['purchase_done'] AND ((
													$row['total'] >= $config['min_purchase'] ? (
														$merged ? $can_cofirm : (
															$row['iss_conducted'] == 1 OR $row['iss_paid'] >= floatval($row['price'])*$row['quantity']
														)
													) : 1))){
														echo 'confirm';
													} else {
														echo 'uncomfirm';
													}
							if(true){
								echo '<br \>good';
								echo '<br \> Total:'.$row['total'];
								echo '<br \> Config:'.$config['min_purchase'];
							}
						die;
					} */
					
					tpl_set('purchases/form', [
						'id' => $id,
						'options' => $options,
						'name' => $row['name'],
						'sale-name' => $row['sale_name'],
						'link' => $row['link'],
						'price' => $row['price'],
						'shipment-cost' => $row['shipment_cost'],
						'currency' => $currency,
						'purchase-currency' => $purchase_currency,
						'quantity' => $row['quantity'],
						'total' => $row['total'],
						'sale' => $row['sale'],
						'proceeds' => floatval($row['sale']) - floatval($row['price']),
						'estimated' => $row['estimated'],
						'photo' => $row['photo'],
						'issue-id' => $row['issue_id'],
						'tracking' => $row['tracking'],
						'ship-tracking' => $row['ship_tracking'],
						'user_status' => $row['user_status'],
						'status' => $row['status'],
						'comment' => $row['comment'],
						'title' => $route[1] == 'add' ? 'Add purchase' : 'Edit purchase',
						'send' => $id ? 'save' : 'send',
						'customer-id' => json_encode($row['customer_id'] ? [$row['customer_id'] => [
							'name' => $row['customer_name'].' '.$row['customer_lastname']
						]] : ((!$id AND intval($_GET['usr'])) ? [intval($_GET['usr']) => [
							'name' => $usr['name'].' '.$usr['lastname']
						]] : [])),
						'object-id' => json_encode($row['object_id'] ? [$row['object_id'] => [
							'name' => $row['object_name']
						]] : []),
						'customer_id' => $row['customer_id'],
						'customer-name' => $row['customer_name'].' '.$row['customer_lastname'],
						'create-id' => $row['create_id'],
						'create-image' => $row['create_image'],
						'create-date' => convert_date($row['create_date'], true),
						'create-name' => $row['create_name'],
						'create-lastname' => $row['create_lastname'],
						'confirm-id' => $row['confirm_id'],
						'confirm-image' => $row['confirm_image'],
						'confirm-date' => convert_date($row['confirm_date'], true),
						'confirm-name' => $row['confirm_name'],
						'confirm-lastname' => $row['confirm_lastname'],
						'edited-id' => $row['edited_id'],
						'edited-image' => $row['edited_image'],
						'edited-date' => convert_date($row['edited_date'], true),
						'edited-name' => $row['edited_name'],
						'edited-lastname' => $row['edited_lastname'],
						'backusr' => $_GET['usr'] ?: 0,
						'issue' => $issue,
						'issue-status' => (($row['issue_id'] AND $issue_status) ? $issue_status['name'] : 'New'),
						'invoice' => $row['iinv_id'] ?: ($row['inv_id'] ?: 0),
						'left' => ($inv_purchases AND $inv_purchases[$id]) ? ($row['quantity'] - ($inv_purchases[$id]['quantity'] ?: 1)) : (($iinv_purchases AND $iinv_purchases[$id]) ? ($row['quantity'] - ($iinv_purchases[$id]['quantity'] ?: 1)) : 0),
						'prev' => $prev['id'],
						'next' => $next['id']
					], [
						'add' => ($route[1] == 'add'),
						'issue' => $row['issue_id'],
						'save' => (($route[1] == 'edit' && $user['edit_purchase']) OR ($route[1] == 'add' && $user['add_purchase'])),
						'del' => ($row['del'] == 1),
						'edit' => ($route[1] == 'edit'),
						'edited' => $row['edited_id'] > 0,
						'create' => $row['create_id'] > 0,
						'confirm' => $row['confirm_id'] > 0,
						'customer-id' => $row['customer_id'],
						'edited-image' => $row['edited_image'],
						'create-image' => $row['create_image'],
						'confirm-image' => $row['confirm_image'],
						'photo' => ($row['photo']),
						'confirmed' => $row['confirmed'] == 1,
						'can_confirm' => $user['confirm_purchase'],
						'can_confirm_cond' => (!$row['i_cid'] OR 
												($row['issue_id'] AND $row['purchase_done'] AND ((
													$row['total'] >= $config['min_purchase'] ? (
														$merged ? $can_cofirm : (
															$row['iss_conducted'] == 1 OR $row['iss_paid'] >= floatval($row['price'])*$row['quantity']
														)
													) : 1))) OR 
												(!$row['issue_id'] AND $row['customer_id'] AND $row['inv_id'] AND ($row['sale']*$row['quantity'] >= $config['min_purchase'] ? ($merged ? $can_cofirm : ($row['inv_conducted'] == 1 OR $row['inv_paid'] >= floatval($row['price'])*$row['quantity'])) : 1)) OR 
												(!$row['issue_id'] AND !$row['inv_id'] AND !$row['customer_id'])),
						'purchase_done' => ($row['issue_id'] AND !$row['purchase_done']),
						'backusr' => ($route[1] == 'edit' OR $route[1] == 'add') AND $_GET['usr'],
						'delete' => $user['delete_purchase'],
						'rma' => $row['rma'] == 2,
						'rma_request' => $row['rma'] == 1,
						'rma_close' => $row['rma_status'] == 'close',
						'rma_page' => $route[1] == 'rma',
						'request_rma' => $row['rma_request_staff'],
						'confirm_rma' => $row['rma_confirm_staff'],
						'pickup_rma' => $row['rma_pickup_staff'],
						'comments' => $row['rma'] != 0,
						'staff' => $row['create_id'] == $user['id'],
						'received' => $row['recived_id'],
						'issue_error' => $issue,
						'in-store' => (!$row['customer_id'] AND !$row['issue_id']),
						'just-customer' => (!$row['issue_id'] AND !$row['invoice_id']),
						'invoice' => $row['iinv_id'] ?: ($row['inv_id'] ?: 0),
						'quantity' => $row['quantity'] > 1,
						'prev' => $id AND $prev['id'],
						'next' => $id AND $next['id'],
						'arrows' => ($id AND !$row['recived_id'] AND !$row['confirmed'])
					], 'content');
					
				} else {
					tpl_set('forbidden', [
						'text' => 'You have no access to this page'
					], [
					], 'content');
				}
			}
		break;
		
		/*
		* Confirm purchases 
		*/ 
		case 'confirm_purchase':
			$id = intval($_POST['id']);
			db_query('UPDATE `'.DB_PREFIX.'_purchases` SET
				confirmed = 1,
				confirm_id = '.$user['id'].',
				confirm_date = \''.date('Y-m-d H:i:s', time()).'\'
				WHERE id = '.$id
			);
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE type = \'confirm_purchase\' AND id = '.$id);
			
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'purchases\'');
			
			$usr = db_multi_query('SELECT object_id, create_id FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$id);			
			// ------------------------------------------------------------------------------- //
			if ($usr['object_id'] > 0){
				/* $sql_ = db_multi_query('
					SELECT
						SUM(tb1.point) as sum,
						tb2.points
					FROM `'.DB_PREFIX.'_inventory_status_history` tb1,
						 `'.DB_PREFIX.'_objects` tb2
					WHERE tb1.staff_id = '.$usr['create_id'].' AND tb1.date >= DATE_SUB(
						NOW(), INTERVAL 1 HOUR
					) AND tb1.rate_point = 1 AND tb2.id = '.$usr['object_id']
				); */
				
				$points = floatval($config['user_points']['new_purchase']['points_confirmation']);
				
				//if((int)$sql_['sum'] > 0 AND (int)$sql_['sum'] >= (int)$sql_['points']){
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$usr['create_id'].',
						action = \'confirmation_purchase\',
						object_id = '.$usr['object_id'].',
						purchase_id = '.$id.',
						point = \''.$points.'\''
					);	//min_rate = '.$sql_['points'].',
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$points.'
						WHERE id = '.$usr['create_id']
					);
				/* } else {
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$usr['create_id'].',
						action = \'new_purchase\',
						min_rate = '.$sql_['points'].',
                        object_id = '.$usr['object_id'].',
						purchase_id = '.$id.',
						point = \''.$points.'\',
						rate_point = 1'
					);	
				} */
			}
			
			// ------------------------------------------------------------------------------- //
			
			die('OK');
		break;
		
		/*
		*  Send purchases
		*/
		case 'send': 
			is_ajax() or die('Hacking attempt!');
			/* print_r($_POST);
			die; */
			$id = intval($_POST['id']);
			$new = intval($id);
			/*if ($user['id'] == 17) {
				print_r(floatval($_POST['sale']));
				print_r(min_price(floatval($_POST['price'])));
			}*/
			if (!floatval($_POST['price']))
				die('enter_price');
			
			$mp = min_price(floatval($_POST['price']), intval($_POST['object']));
			if (floatval($_POST['sale']) < $mp)
				die('min_price_'.$mp);
			
			if (!text_filter($_POST['salename'], 1000, false)) 
				die('empty_salename');
			
/* 			if (!$id AND !$_POST['photo'])
				die('no_photo'); */
			
			if ((!$id AND $user['add_purchase']) OR ($id AND $user['edit_purchase'])) {
				if(($row = db_multi_query('SELECT issue_id FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$id.' AND issue_id > 0')) AND $user['id'] == 16){
					$job = db_multi_query('SELECT purchase_info FROM `'.DB_PREFIX.'_issues` WHERE id = '.$row['issue_id']);
					$data = json_decode($job['purchase_info'], true);
					$data[$id]['name'] = db_escape_string($_POST['salename']);
					$data[$id]['price'] = floatval($_POST['price']);
					$data[$id]['cost_price'] = floatval($_POST['sale']);
					db_query('UPDATE `'.DB_PREFIX.'_issues` SET purchase_info = \''.db_escape_string(json_encode($data)).'\' WHERE id = '.$row['issue_id']);
				}
				if($_POST['del_photo']) $sql .= ' photo = \'\',';
				db_query(($id ? 'UPDATE' : 'INSERT INTO').' `'.DB_PREFIX.'_purchases` SET
					name = \''.text_filter($_POST['name'], 100, false).'\',
					sale_name = \''.text_filter($_POST['salename'], 1000, false).'\',
					link = \''.text_filter($_POST['link'], 200, false).'\',
					price = \''.text_filter($_POST['price'], 30, false).'\',
					shipment_cost = \''.floatval($_POST['shipment_cost']).'\',
					currency = \''.text_filter($_POST['currency'] ?: 'USD', 25, false).'\',
					purchase_currency = \''.text_filter($_POST['purchase_currency'] ?: 'USD', 25, false).'\',
					quantity = \''.(intval($_POST['quantity']) ?: 1).'\',
					sale = \''.floatval($_POST['sale']).'\',
					total = \''.trim(preg_replace('/[^0-9.]/i', '', text_filter($_POST['total'], 30, false))).'\',
					tracking = \''.text_filter($_POST['tracking'], 50, false).'\',
					ship_tracking = \''.text_filter($_POST['ship-tracking'], 50, false).'\',
					estimated = \''.text_filter($_POST['estimated'], 255, false).'\',
					object_id = \''.ids_filter($_POST['object']).'\',
					status = \''.($id ? (
							in_array($_POST['status'], ['Purchased','Active','CancelPending','Completed','Inactive','Shipped','Rejected']) ? $_POST['status'] : ''
						) : 'Purchased'
					).'\',
					customer_id = '.intval($_POST['customer']).','.$sql.(
						$id ? '' : ' create_id = '.$user['id'].', create_date = \''.date('Y-m-d H:i:s', time()).'\','
					).(
						!$id ? '' : ' edited_id = '.$user['id'].', edited_date = \''.date('Y-m-d H:i:s', time()).'\','
					).(
						!$id ? 'issue_id = '.intval($_POST['issue']).', ' : ''
					).' comment = \''.text_filter($_POST['comment'], null, false).'\' '.(
						$id ? ' WHERE id = '.$id : ''
				));
			} else {
				die('no_acc');
			}
				
			$id = $id ? $id : intval(mysqli_insert_id($db_link));
			
			if (!$new) db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', object_id = '.$user['store_id'].', event = \'add_purchase\', event_id = '.$id);
			
			$_POST['photo'] = trim($_POST['photo']);
			
			if ($_POST['photo'] && strpos($_POST['photo'], 'thumb') == false) {
				$dir = ROOT_DIR.'/uploads/images/';
				if(!is_dir($dir.$id)){
					@mkdir($dir.$id, 0777);
					@chmod($dir.$id, 0777);
				}
				$dir = $dir.$id.'/';
				
				$type = mb_strtolower(pathinfo($_POST['photo'], PATHINFO_EXTENSION));
				
				if(preg_match('/^data:image\/(\w+);base64,/', $_POST['photo'], $type)){
					$data = base64_decode(substr($_POST['photo'], strpos($_POST['photo'], ',')+1));
					$type = strtolower($type[1]) == 'jpeg' ? 'jpg' : strtolower($type[1]);
				} else
					$data = file_get_contents($_POST['photo']);
				
				$rename = uniqid('', true).'.'.$type;
				
				file_put_contents($dir.$rename, $data);
				
				$img = new Imagick($dir.$rename);

				$img->cropThumbnailImage(94, 94);
				$img->stripImage();
				$img->writeImage($dir.'thumb_'.$rename);
				$img->destroy();
				db_query('UPDATE `'.DB_PREFIX.'_purchases` SET photo = \''.$rename.'\' WHERE id = '.$id);
			}
			echo $id;
			
			if (!$new) {
				$points = floatval($config['user_points']['new_purchase']['points']);

				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$user['id'].',
						action = \'new_purchase\',
						object_id = '.ids_filter($_POST['object']).',
						purchase_id = '.$id.',
						point = \''.$points.'\''
					);
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$points.'
						WHERE id = '.$user['id']
					);

				db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET type = \'confirm_purchase\', id = '.$id);
				send_push(0, [
					'type' => 'purchase',
					'id' => '/purchases/edit/'.$id,
					'name' => $user['uname'],
					'lastname' => $user['ulastname'],
					'message' => 'Purchase #'.$id.' created. Please, confirm',
					'arguments' => [
						'confirm_purchase' => md5(md5(1).md5(SOLT, true))
					]
				]);
			}

			die;
		break;
		
		/*
		*  Get ebay page
		*/
		case 'ebay':
			is_ajax() or die('Hacking attempt!');
			$res = get_curl_page($_POST['url']);
			if ($res['errno'])
				exit('ERR');
			else {
				if ($res['http_code'] != 200)
					exit('NOP');
				else 
					exit($res['content']);
			}
		break;
		
		/*
		*  Del purchases
		*/
		case 'del':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['delete_purchase']){
				//db_query('DELETE FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$id);
				db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE (type = \'confirm_purchase\' OR type = \'returm_request_purchase\') AND id = '.$id);
				db_query('UPDATE `'.DB_PREFIX.'_purchases` SET del = 1 WHERE id = '.$id);
				db_query('INSERT INTO `'.DB_PREFIX.'_purchase_delete` SET purchase_id = '.$id.', staff_id = '.$user['id'].', info = \'from purches\'');
				if(mysqli_affected_rows($db_link)){
					$p = db_multi_query('SELECT confirmed, recived_id FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$id);
					if ($p['confirmed'] AND !$p['recived_id'])
						db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'purchases\'');
					db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', event = \'Delete purchase\'');
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('no_acc');
		break;
		
		/*
		*  Get RMA comment
		*/
		case 'get_rma_comment':
			is_ajax() or die('Hacking attempt!');
			$c = db_multi_query('SELECT rma_comment FROM `'.DB_PREFIX.'_purchases` WHERE id = '.intval($_POST['id']));
			if ($c['rma_comment'])
				echo $c['rma_comment'];
			die;
		break;
		
		/*
		*  Return purchases
		*/
		case 'send_rma':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$action = text_filter($_POST['action'], 10, false);
			$comment = text_filter($_POST['comment']);
			
			/* if (!$action AND (!$comment OR strlen($comment) < 10))
				die('no_comment'); */
			
			$purchase = db_multi_query('SELECT create_id, DATE(create_date) as create_date FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$id);
			
			if($user['confirm_purchase'] AND $action OR !$action) {
				db_query('UPDATE `'.DB_PREFIX.'_purchases` SET
					'.($action == 'confirm' ? '
						rma = 2,
						rma_status = \'open\',
						rma_confirm_staff = '.$user['id'].',
						rma_confirm_date = \''.date('Y-m-d H:i:s', time()).'\'
					' : ($action == 'pickup' ? '
						rma = 2,
						rma_status = \'close\',
						rma_pickup_staff = '.$user['id'].',
						rma_pickup_date = \''.date('Y-m-d H:i:s', time()).'\'
					' : '
						rma = 1,
						rma_request_staff = '.$user['id'].',
						rma_request_date = \''.date('Y-m-d H:i:s', time()).'\',
						rma_comment = \''.$comment.'\',
						recived_id = IF(recived_id = 0, '.$user['id'].', recived_id),
						recived_date = IF(recived_id = 0, \''.date('Y-m-d H:i:s', time()).'\', recived_date)
					')).'
				WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					if (!$action) {
						db_query('INSERT INTO `'.DB_PREFIX.'_notifications` SET type = \'returm_request_purchase\', id = '.$id);
						send_push(0, [
							'type' => 'purchase',
							'id' => '/purchases/edit/'.$id,
							'name' => $user['uname'],
							'lastname' => $user['ulastname'],
							'message' => 'Purchase #'.$id.' return request created. Please, confirm',
							'arguments' => [
								'confirm_purchase' => md5(md5(1).md5(SOLT, true))
							]
						]);
					} else if ($action == 'confirm') {
						db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE type = \'returm_request_purchase\' AND id = '.$id);
						
						if (!intval($_POST['type'])) {
							$points = floatval($config['user_points']['new_purchase']['points_return']);
							
							db_query(
								'INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
									staff_id = '.$purchase['create_id'].',
									action = \'return_purchase\',
									purchase_id = '.$id.',
									point = '.$points
							);
							
							db_query(
								'UPDATE `'.DB_PREFIX.'_users`
									SET points = points+'.$points.'
								WHERE id = '.$purchase['create_id']
							);
							
							if ($points < 0) {
								if ($wt = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_timer` WHERE DATE(date) = \''.$purchase['create_date'].'\' AND user_id = '.$purchase['create_id'])) {
									db_query('UPDATE `'.DB_PREFIX.'_timer` SET seconds = seconds + '.($points * $config['min_forfeit'] * 60).' WHERE id = '.$wt['id']);
									db_query('INSERT INTO `'.DB_PREFIX.'_users_time_forfeit` SET user_id = '.$purchase['create_id'].', forfeit = '.(floatval($points) * $config['min_forfeit']*60));
								}
							}
						}
					}
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('no_acc');
		break;
		
		/*
		* Send comment
		*/
		case 'send_comment': 
			is_ajax() or die('Hacking attempt!');
			
			db_query(
				'INSERT INTO `'.DB_PREFIX.'_purchases_comments` SET 
					staff_id = '.$user['id'].',
					purchase_id = '.intval($_POST['id']).',
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
				FROM `'.DB_PREFIX.'_purchases_comments` c
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.staff_id
				WHERE c.purchase_id = '.$id.'
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
		* RMA restore
		*/
		case 'rma_restore':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			
			db_query('UPDATE `'.DB_PREFIX.'_purchases` SET
				rma = 0,
				rma_status = \'open\',
				rma_request_staff = 0,
				rma_request_date = \'0000-00-00 00:00:00\',
				rma_confirm_staff = 0,
				rma_confirm_date = \'0000-00-00 00:00:00\',
				rma_pickup_staff = 0,
				rma_pickup_date = \'0000-00-00 00:00:00\'
			WHERE id = '.$id);
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE type = \'returm_request_purchase\' AND id = '.$id);
			die('OK');
		break;
		
		/*
		* All purchases
		*/
		case 'deleted':
		case 'rma':
		case 'received':
		default:
			$meta['title'] = 'Purchases';
			$ids = isset($_GET['ids']) ? ids_filter($_GET['ids']) : 0;
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$object = intval($_REQUEST['object']);
			$create = intval($_POST['create']);
			$confirm = intval($_POST['confirm']);
			$customer = intval($_REQUEST['staff']);
			$verified = intval($_POST['verified']);
			$status = text_filter($_POST['status'], 20, false);
			$oredered = text_filter($_POST['action'], 20, false);
			$date_start = text_filter($_REQUEST['date_start'], 30, true);
			$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
			$type = text_filter($_POST['type'], 30, true);
			$payment = ($type == 'no_confirm' ? 'deposit' : text_filter($_POST['payment'], 30, true));
			$count = 10;

			$statuses = '';
			if ($st_sql = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_inventory_status` WHERE purchase = 1', true)) {
				foreach($st_sql as $st) {
					if ($statuses) $statuses .= ',';
					$statuses .= $st['id'];
				}
			} else
				$statuses = 0;
			
			//$order_by = 'p.confirmed ASC, p.id DESC';
			
			if($user['id'] == 16){
				$order_by = 'ORDER BY p.create_date DESC, p.rma = 2 DESC, p.confirmed = 0 DESC';
			} else
				$order_by = 'ORDER BY p.rma = 2 DESC, p.create_date DESC';
				//$order_by = 'ORDER BY p.confirmed ASC, p.create_date DESC, inv.paid ASC';
			
			
			if(isset($_POST['sort-photo']))
				$order_by = 'ORDER BY p.photo '.getOrderByType($_POST['sort-photo']);

			if(isset($_POST['sort-name']))
				$order_by = 'ORDER BY p.name '.getOrderByType($_POST['sort-name']);
			
			if(isset($_POST['sort-date']))
				$order_by = 'ORDER BY p.create_date '.getOrderByType($_POST['sort-date']);

			if(isset($_POST['sort-transaction']))
				$order_by = 'ORDER BY p.transaction '.getOrderByType($_POST['sort-transaction']);
			
			if(isset($_POST['sort-status']))
				$order_by = 'ORDER BY p.recived_id '.getOrderByType($_POST['sort-status']);

			if(isset($_POST['sort-tracking']))
				$order_by = 'ORDER BY p.tracking '.getOrderByType($_POST['sort-tracking']);

			if(isset($_POST['sort-estimated']))
				$order_by = 'ORDER BY p.estimated '.getOrderByType($_POST['sort-estimated']);
			
			if(isset($_POST['sort-price']))
				$order_by = ' ORDER BY p.price '.getOrderByType($_POST['sort-price']);

			if(isset($_POST['sort-quantity']))
				$order_by = 'ORDER BY p.quantity '.getOrderByType($_POST['sort-quantity']);

			if(isset($_POST['sort-total']))
				$order_by = 'ORDER BY p.total '.getOrderByType($_POST['sort-total']);
			
			if($sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
				p.*,
				cr.name as create_name,
				cr.lastname as create_lastname,
				cn.name as confirm_name,
				cn.lastname as confirm_lastname,
				ed.name as edited_name,
				ed.lastname as edited_lastname,
				rr.name as request_rma_name,
				rr.lastname as request_rma_lastname,
				rc.name as confirm_rma_name,
				rc.lastname as confirm_rma_lastname,
				rp.name as pickup_rma_name,
				rp.lastname as pickup_rma_lastname,
				iinv.conducted as iss_conducted,
				iinv.paid as iss_paid,
				iinv.id as iinv_id,
				iinv.conducted as iinv_conducted,
				iss.id as issue_id,
				iss.purchase_done,
				inv.conducted as inv_conducted,
				inv.id as inv_id,
				inv.paid as inv_paid,
				i.customer_id as i_cid,
				cus.name as customer_name,
				cus.lastname as customer_lastname
			FROM `'.DB_PREFIX.'_purchases` p
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = p.object_id
			LEFT JOIN `'.DB_PREFIX.'_users` cr
				ON p.create_id = cr.id
			LEFT JOIN `'.DB_PREFIX.'_users` cn
				ON p.confirm_id = cn.id
			LEFT JOIN `'.DB_PREFIX.'_users` ed
				ON p.edited_id = ed.id
			LEFT JOIN `'.DB_PREFIX.'_users` rr
				ON rr.id = p.rma_request_staff
			LEFT JOIN `'.DB_PREFIX.'_users` rc
				ON rc.id = p.rma_confirm_staff
			LEFT JOIN `'.DB_PREFIX.'_users` rp
				ON rp.id = p.rma_pickup_staff
			LEFT JOIN `'.DB_PREFIX.'_issues` iss
				ON p.issue_id = iss.id
			LEFT JOIN `'.DB_PREFIX.'_invoices` iinv 
				ON iinv.issue_id = iss.id
			LEFT JOIN `'.DB_PREFIX.'_inventory` i 
				ON i.id = iss.inventory_id
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON (p.invoice_id = inv.id)
			LEFT JOIN `'.DB_PREFIX.'_users` cus
				ON cus.id = p.customer_id
			WHERE p.del = '.(
				$route[1] == 'deleted' ? 1 : 0
			).(
				$ids ? ' AND p.id IN('.$ids.') ' : ''
			).' AND p.rma IN ('.(
				$route[1] == 'rma' ? '2,1' : '0,1,2'
			).') '.(
				$payment == 'no_deposit' ? ' AND !i.customer_id AND IF(iss.id, IF(p.total > '.$config['min_purchase'].', iinv.paid < p.sale, 0), 0) ' : ($payment == 'deposit' ? ' AND IF(iss.id, (iinv.id AND IF(p.total > '.$config['min_purchase'].', iinv.paid >= p.sale, 1)), 1) ' : '')
			).(
				$query ? 'AND (p.name LIKE \'%'.$query.'%\' OR p.sale_name LIKE \'%'.$query.'%\' OR p.ship_tracking LIKE \'%'.$query.'%\' OR p.tracking LIKE \'%'.$query.'%\' OR p.id = \''.$query.'\') ' : ''
			).(
				$object ? 'AND p.object_id = '.$object.' ' : ''
			).(
				$create ? 'AND p.create_id = '.$create.' ' : ''
			).(
				$confirm ? 'AND p.confirm_id = '.$confirm.' ' : ''
			).(
				$verified ? 'AND p.transaction != \'\' ' : ''
			).(
				$type == 'return' ? 'AND p.rma = 1 ' : ($type == 'active' ? 'AND p.rma = 0 ' : ($type == 'no_confirm' ? 'AND p.confirmed = 0 ' : ''))
			).(
				$oredered == 'store' ? ' AND p.customer_id = 0 AND p.issue_id = 0 ' : ($oredered == 'customer' ? ' AND p.customer_id > 0 AND p.issue_id = 0 ' : ($oredered == 'issue' ? ' AND p.issue_id > 0 ' : ''))
			).(
				$status ? ($status == 'panding' ? 'AND p.confirmed = 0 AND p.recived_id = 0 ' : ($status == 'confirmed' ? ' AND p.confirmed = 1 AND p.recived_id = 0' : ' AND p.recived_id = 1')) : ''
			).(
				($date_start AND $date_finish) ? ' AND p.create_date >= CAST(\''.$date_start.'\' AS DATE) AND p.create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).(
				$customer ? ' AND (p.customer_id = '.$customer.' OR iss.customer_id = '.$customer.')' : ''
			).(
				$route[1] == 'received' ? ' AND p.recived_id = 0 AND p.confirmed = 1' : ''
			).(
				(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '' : 
					(
						$objects_ip[$user['oip']] != 0 ? 
						' AND (FIND_IN_SET('.$user['id'].', o.managers) 
						OR FIND_IN_SET('.$user['id'].', o.staff) 
						OR o.id = '.($objects_ip[$user['oip']] ?: 0).')' : ' AND o.id = 0 '
					)
			).'
			'.$order_by.' LIMIT '.($page*$count).', '.$count, true)){
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				
				$invoices = array_filter($sql, function($a) {
					if ($a['inv_id'] OR $a['iinv_id'])
						return $a;
				});
				
				$merged = [];
				if ($invoices) {
					$inv_list = array_merge(array_column($invoices, 'inv_id'), array_column($invoices, 'iinv_id'));
					
					$merged = db_multi_query('
						SELECT 
							i.id,
							mi.id as mid,
							mi.invoices,
							mi.paid
						FROM `'.DB_PREFIX.'_invoices` i 
						LEFT JOIN `'.DB_PREFIX.'_invoices` mi
							ON FIND_IN_SET(i.id, mi.invoices)
						WHERE i.id IN ('.ids_filter(implode(',', $inv_list)).')
					', true);
					
					$invoices_paided = '';
					if ($merged_paided = array_filter($merged, function($a) {
						if ($a['paid'])
							return $a;
					})) {
						foreach($merged_paided as $m) {
							if ($invoices_paided)
								$invoices_paided .= ',';
							$invoices_paided .= $m['mid'].','.$m['invoices'];
						}
						
						$sql_paided = db_multi_query('SELECT id, purchases_info, issue_info FROM `'.DB_PREFIX.'_invoices` WHERE id IN ('.ids_filter($invoices_paided).')', true);
					}
				}
				
				$i = 0;
				
				foreach($sql as $row) {
					$can_cofirm = 1;
					if ($merged) {
						if (in_to_array($row['inv_id'], $inv_list) OR in_to_array($row['iinv_id'], $inv_list)) {
							if ($invoice = array_filter($merged, function($a) use($row) {
								if (($a['id'] == $row['inv_id'] OR $a['id'] == $row['iinv_id']) AND ids_filter($a['invoices']))
									return $a;
							})) {
								if (!$invoice[0]['paid'])
									$can_cofirm = 0;
								else {
									$ptotal = 0;
									if ($minvoices = array_filter($sql_paided, function($a) use($invoice) {
										if (in_to_array($a['id'], $invoice[0]['invoices']) OR $a['id'] == $invoice[0]['mid'])
											return $a;
									})) {
										foreach($minvoices as $mi) {
											$ipurchases = json_decode($mi['purchases_info'], true);
											$iissue = json_decode($mi['issue_info'], true);
											if (is_array($ipurchases) AND count($ipurchases)) {
												foreach($ipurchases as $pi) {
													$ptotal += floatval(preg_replace('/[^0-9.]/i', '', $pi['price']));
												}
											}
											if (is_array($iissue) AND count($iissue['purchases'])) {
												foreach($iissue['purchases'] as $ii) {
													$ptotal += floatval(preg_replace('/[^0-9.]/i', '', $ii['price']));
												}
											}
										}
										
										if ($ptotal > $invoice[0]['paid'])
											$can_cofirm = 0;
									}
								}
							}
						}
					}
					$cstatus = '';
					$class = '';
					if ($row['recived_id'])
						$cstatus = 'Received';
					else {
						//if (($row['issue_id'] AND !$row['iss_conducted']) OR ($row['inv_id'] AND !$row['inv_conducted'])) {
						if (($row['i_cid'] AND $row['issue_id'] AND ((($row['total'] >= $config['min_purchase'] ? ($invoice ? !$can_cofirm : ($row['iss_paid'] < floatval($row['price'])*$row['quantity'] AND !$row['iinv_conducted'])) : 0)) OR !$row['purchase_done'])) OR ($row['customer_id'] AND $row['inv_id'] AND ($row['total'] >= $config['min_purchase'] ? ($invoice ? !$can_cofirm : ($row['inv_paid'] < floatval($row['price'])*$row['quantity'] AND !$row['inv_conducted'])) : 0))) {
							$cstatus = 'Not conducted';
							$class = 'p_red';
						} else {
							if ($row['confirm_id']) {
								$cstatus = 'Confirmed';
								$class = 'p_green';
							} else {
								$cstatus = 'Not confirmed';
								$class = 'p_yellow';
							}
						}
					}
					
					if($row['rma'] > 0){
						//$cstatus = 'RMA';
						$class = 'p_orange';
					}
					
					tpl_set('purchases/item2', [
						'id' => $row['id'],
						'class' => $class,
						'cstatus' => $cstatus,
						'name' => ($row['sale_name'] ?: $row['name']),
						'status' => ($row['recived_id'] ? 'Received' : ($row['confirmed'] ? 'Confirmed' : 'Pending')),
						'comment' => $row['comment'],
						'price' => $row['price'],
						'currency' => $config['currency'][$row['currency']]['symbol'],
						'purchase-currency' => $config['currency'][$row['purchase_currency']]['symbol'],
						'quantity' => $row['quantity'],
						'tracking' => $row['tracking'],
						'estimated' => $row['estimated'],
						'total' => number_format($row['total'], 2, '.', ''),
						'photo' => $row['photo'],
						'create-id' => $row['create_id'],
						'date' => date('y.m.d h:m:s', strtotime($row['create_date'])),
						'create-name' => $row['create_name'],
						'create-lastname' => $row['create_lastname'],
						'confirm-id' => $row['confirm_id'],
						'confirm-name' => $row['confirm_name'],
						'confirm-lastname' => $row['confirm_lastname'],
						'edited-id' => $row['edited_id'],
						'edited-name' => $row['edited_name'],
						'edited-lastname' => $row['edited_lastname'],
						'customer-id' => $row['customer_id'],
						'customer-name' => $row['customer_name'],
						'customer-lastname' => $row['customer_lastname'],
						'request-rma-id' => $row['rma_request_staff'],
						'request-rma-name' => $row['request_rma_name'],
						'request-rma-lastname' => $row['request_rma_lastname'],
						'confirm-rma-id' => $row['rma_confirm_staff'],
						'confirm-rma-name' => $row['confirm_rma_name'],
						'confirm-rma-lastname' => $row['confirm_rma_lastname'],
						'pickup-rma-id' => $row['rma_pickup_staff'],
						'pickup-rma-name' => $row['pickup_rma_name'],
						'pickup-rma-lastname' => $row['pickup_rma_lastname'],
						'confirmed' => $row['rma'] < 2 ? ($row['confirmed'] ? 'paid' : 'unpaid') : ($row['rma_status'] == 'open' ? 'unpaid' : 'paid'),
						'transaction' => $row['transaction']
					], [
						'photo' => $row['photo'],
						'notdel' => $row['del'] == 0,
						'comment' => $row['comment'],
						'edited' => $row['edited_id'] > 0,
						'create' => $row['create_id'] > 0,
						'confirm' => $row['confirm_id'] > 0,
						'customer' => $row['customer_id'],
						'edit' => $user['edit_purchase'],
						'add' => $user['add_purchase'],
						'delete' => $user['delete_purchase'],
						'can_confirm' => $user['confirm_purchase'],
						'rma' => $row['rma'] == 2,
						'rma_request' => $row['rma'] == 1,
						'rma_any' => $row['rma'] == 1 OR $row['rma'] == 2,
						'rma_close' => $row['rma_status'] == 'close',
						'rma_page' => $route[1] == 'rma',
						'request_rma' => $row['rma_request_staff'],
						'confirm_rma' => $row['rma_confirm_staff'],
						'pickup_rma' => $row['rma_pickup_staff'],
						'comments' => $row['rma'] != 0,
						'transaction' => $row['transaction'],
						'received' => $row['recived_id'],
						'in-store' => (!$row['customer_id'] AND !$row['issue_id']),
					], 'orders');
					$i++;
				}
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			
			$total = [];
			if (!$page) {
				$total = db_multi_query('
					SELECT 
						SUM(p.total) as total,
						SUM(p.quantity) as total_q
					FROM `'.DB_PREFIX.'_purchases` p
					LEFT JOIN `'.DB_PREFIX.'_objects` o
						ON o.id = p.object_id
					LEFT JOIN `'.DB_PREFIX.'_users` cr
						ON p.create_id = cr.id
					LEFT JOIN `'.DB_PREFIX.'_users` cn
						ON p.confirm_id = cn.id
					LEFT JOIN `'.DB_PREFIX.'_users` ed
						ON p.edited_id = ed.id
					LEFT JOIN `'.DB_PREFIX.'_users` rr
						ON rr.id = p.rma_request_staff
					LEFT JOIN `'.DB_PREFIX.'_users` rc
						ON rc.id = p.rma_confirm_staff
					LEFT JOIN `'.DB_PREFIX.'_users` rp
						ON rp.id = p.rma_pickup_staff
					LEFT JOIN `'.DB_PREFIX.'_issues` iss
						ON p.issue_id = iss.id
					LEFT JOIN `'.DB_PREFIX.'_invoices` iinv 
						ON iinv.issue_id = iss.id
					LEFT JOIN `'.DB_PREFIX.'_invoices` inv
						ON p.invoice_id = inv.id
					WHERE p.del = '.(
						$route[1] == 'deleted' ? 1 : 0
					).' AND p.rma IN ('.(
						$route[1] == 'rma' ? 2 : '0,1'
					).') '.(
						$payment == 'no_deposit' ? ' AND IF(iss.id, iss.purchase_done = 1 AND IF(p.price > 50, iinv.paid < p.sale, 0), 0) ' : ($payment == 'deposit' ? ' AND IF(iss.id, iss.purchase_done = 1 AND IF(p.price > 50, iinv.paid >= p.sale, 1), 1) ' : '')
					).(
						$query ? 'AND (p.name LIKE \'%'.$query.'%\' OR p.ship_tracking LIKE \'%'.$query.'%\') ' : ''
					).(
						$object ? 'AND p.object_id = '.$object.' ' : ''
					).(
						$create ? 'AND p.create_id = '.$create.' ' : ''
					).(
						$customer ? ' AND (p.customer_id = '.$customer.' OR iss.customer_id = '.$customer.')' : ''
					).(
						$confirm ? 'AND p.confirm_id = '.$confirm.' ' : ''
					).(
						$type == 'return' ? 'AND p.rma = 1 ' : ($type == 'active' ? 'AND p.rma = 0 ' : ($type == 'no_confirm' ? 'AND p.confirmed = 0 ' : ''))
					).(
						$status ? 'AND p.status = \''.$status.'\' ' : ''
					).(
						($date_start AND $date_finish) ? ' AND p.create_date >= CAST(\''.$date_start.'\' AS DATE) AND p.create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).(
						$route[1] == 'received' ? ' AND p.recived_id = 0 AND p.confirmed = 1' : ''
					).(
						(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '' : 
							(
								$objects_ip[$user['oip']] != 0 ? 
								' AND (FIND_IN_SET('.$user['id'].', o.managers) 
								OR FIND_IN_SET('.$user['id'].', o.staff) 
								OR o.id = '.($objects_ip[$user['oip']] ?: 0).')' : ' AND o.id = 0 '
							)
					)	
				);
			} 
		
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['orders'],
					'total' => number_format($total['total'], 2, '.', ''),
					'total_q' => $total['total_q']
				]));
			}
			
			
			$objects = '';
			foreach(db_multi_query('SELECT id, name 
				FROM `'.DB_PREFIX.'_objects` '.(
				(in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '' :  
					(
					$objects_ip[$user['oip']] != 0 ? 
						'WHERE (FIND_IN_SET(
							'.$user['id'].', managers
						) OR FIND_IN_SET(
							'.$user['id'].', staff
						) OR id = '.($objects_ip[$user['oip']] ?: 0).')' : ' WHERE id = 0 '
					)
				), true) as $row) {
				$objects .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}
			
			tpl_set('purchases/main2', [
				'uid' => $user['id'],
				'objects' => $objects,
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'orders' => $tpl_content['orders'],
				'total' => number_format($total['total'], 2, '.', ''),
				'total-q' => $total['total_q']
			], [
				'owner' => in_to_array(1, $user['group_ids'])
			], 'content');
		break;
	}
} else {
	tpl_set('forbidden', [
		'text' => $lang['Forbidden'],
	], [], 'content');
}
?>