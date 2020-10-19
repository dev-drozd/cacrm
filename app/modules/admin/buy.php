<?php
/**
 * @appointment Buy admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$objects_ip = array_flip($config['object_ips']);

if($user['purchase'] > 0){
	
	switch($route[1]){

		case 'add':
		case 'edit':
			$id = (int)$route[2];
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
				
				if (!$id && isset($_GET['usr'])) {
					$usr = db_multi_query('SELECT name, lastname FROM `'.DB_PREFIX.'_users` WHERE id = '.(int)$_GET['usr']);
				}
				
				$issue = '';
				if ($row['issue_id'] AND $same_purchases = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_purchases` WHERE issue_id = '.$row['issue_id'].' AND link = \''.$row['link'].'\' AND del = 0 AND id != '.$id, true)) {
					foreach($same_purchases as $sp) {
						$issue .= '<a href="/buy/edit/'.$sp['id'].'">Purchase #'.$sp['id'].'</a> ';
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
					
					tpl_set('buy/form', [
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
						'create-date' => $row['create_date'],
						'create-name' => $row['create_name'],
						'create-lastname' => $row['create_lastname'],
						'confirm-id' => $row['confirm_id'],
						'confirm-image' => $row['confirm_image'],
						'confirm-date' => $row['confirm_date'],
						'confirm-name' => $row['confirm_name'],
						'confirm-lastname' => $row['confirm_lastname'],
						'edited-id' => $row['edited_id'],
						'edited-image' => $row['edited_image'],
						'edited-date' => $row['edited_date'],
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
						'del' => ($row['del'] == 1),
						'edit' => ($route[1] == 'edit'),
						'save' => $user['edit_purchase'] > 0,
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
		
			$id = (int)$_POST['id'];
			
			db_query('UPDATE `'.DB_PREFIX.'_purchases` SET
				confirmed = 1,
				confirm_id = '.$user['id'].',
				confirm_date = \''.date('Y-m-d H:i:s', time()).'\'
				WHERE id = '.$id
			);
			
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE type = \'confirm_purchase\' AND id = '.$id);
			
			db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'purchases\'');
			
			$usr = db_multi_query('SELECT object_id, create_id FROM `'.DB_PREFIX.'_purchases` WHERE id = '.$id);			

			if ($usr['object_id'] > 0){
				$points = floatval($config['user_points']['new_purchase']['points_confirmation']);
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$usr['create_id'].',
					action = \'confirmation_purchase\',
					object_id = '.$usr['object_id'].',
					purchase_id = '.$id.',
					point = \''.$points.'\''
				);
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$usr['create_id']
				);
			}
			
			die('OK');
		break;
		
		/*
		*  Send purchases
		*/
		case 'send': 
		
			is_ajax() or die('Hacking attempt!');
			
			$id = (int)$_POST['id'];
			$new = $id;
			
			if (!floatval($_POST['price']))
				die('enter_price');
			
			$mp = min_price(floatval($_POST['price']), intval($_POST['object']));
			
			if (floatval($_POST['sale']) < $mp)
				die('min_price_'.$mp);
			
			if (!text_filter($_POST['salename'], 1000, false)) 
				die('empty_salename');
			
			if (!$id AND !$_POST['photo'])
				die('no_photo');
			
			if ((!$id AND $user['add_purchase']) OR ($id AND $user['edit_purchase'])){
				
				if($_POST['del_photo'])
					$sql .= ' photo = \'\',';
				
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_purchases` SET
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
							in_array($_POST['status'], ['Purchased', 'Active', 'CancelPending', 'Completed', 'Inactive', 'Shipped', 'Rejected']) ? $_POST['status'] : ''
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
			
			if (!$new)
				db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET user_id = \''.$user['id'].'\', object_id = '.$user['store_id'].', event = \'add_purchase\', event_id = '.$id);
			
			$_POST['photo'] = trim($_POST['photo']);
			
			if ($_POST['photo'] && strpos($_POST['photo'], 'thumb') == false){
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
					'id' => '/buy/edit/'.$id,
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
		* All purchases
		*/
		default:
		
		$ids = isset($_GET['ids']) ? ids_filter($_GET['ids']) : 0;
		$query = text_filter($_REQUEST['query'], 255, false);
		$date_start = text_filter($_REQUEST['date_start'], 30, true);
		$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
		$store_id = intval($_REQUEST['object']);
		$page = intval($_REQUEST['page']);
		$count = 10;
		$i = 0;
		
		$where = ' WHERE p.del = 0 ';
		$order_by = ' ORDER BY p.create_date DESC';
		
		if(isset($_REQUEST['sort-photo']))
			$order_by = 'ORDER BY p.photo '.getOrderByType($_REQUEST['sort-photo']);

		if(isset($_REQUEST['sort-name']))
			$order_by = 'ORDER BY p.name '.getOrderByType($_REQUEST['sort-name']);
		
		if(isset($_REQUEST['sort-date']))
			$order_by = 'ORDER BY p.create_date '.getOrderByType($_REQUEST['sort-date']);

		if(isset($_REQUEST['sort-transaction']))
			$order_by = 'ORDER BY p.transaction '.getOrderByType($_REQUEST['sort-transaction']);
		
		if(isset($_REQUEST['sort-status']))
			$order_by = 'ORDER BY p.recived_id '.getOrderByType($_REQUEST['sort-status']);

		if(isset($_REQUEST['sort-tracking']))
			$order_by = 'ORDER BY p.tracking '.getOrderByType($_REQUEST['sort-tracking']);

		if(isset($_REQUEST['sort-estimated']))
			$order_by = 'ORDER BY p.estimated '.getOrderByType($_REQUEST['sort-estimated']);
		
		if(isset($_REQUEST['sort-price']))
			$order_by = ' ORDER BY p.price '.getOrderByType($_REQUEST['sort-price']);

		if(isset($_REQUEST['sort-quantity']))
			$order_by = 'ORDER BY p.quantity '.getOrderByType($_REQUEST['sort-quantity']);

		if(isset($_REQUEST['sort-total']))
			$order_by = 'ORDER BY p.total '.getOrderByType($_REQUEST['sort-total']);
		
		switch($route[1]){
			
			case 'pending':
				$where .= ' AND p.confirmed = 0 AND p.recived_id = 0 ';
			break;
			
			case 'partial':
				$where .= ' AND p.confirmed = 0 AND IF(iss.id, (inv.id AND IF(p.total > '.$config['min_purchase'].', (inv.paid > 0 AND inv.paid < p.sale), 1)), 1) ';
			break;
			
			case 'confirmed':
				$where .= ' AND p.confirmed = 1 AND p.recived_id = 0 ';
			break;
			
			case 'unconfirmed':
				$where .= ' AND p.confirmed = 0 ';
			break;
			
			case 'recived':
				$where .= ' AND p.recived_id = 1 ';
			break;
			
			case 'with_deposit':
				$where .= ' AND IF(iss.id, (inv.id AND IF(p.total > '.$config['min_purchase'].', inv.paid >= p.sale, 1)), 1) ';
			break;
			
			case 'store':
				$where .= ' AND p.recived_id = 1 ';
			break;
			
			case 'partial_deposit':
				$where .= ' AND p.recived_id = 1 ';
			break;
			
			case 'rma':
				$where .= ' AND p.rma IN(2,1) ';
			break;

			case 'deleted':
				$where = ' WHERE p.del = 1 ';
			break;
			
			case null:
/* 				if(count(array_filter($_REQUEST, function($v = false, $k = false){
						return !empty($v) ? true : false;
				})) <= 2) */
				
				//if(!isset($_REQUEST['date_start']))
					//$where = ' WHERE p.del = 0 AND IF(iss.id, (inv.id AND IF(p.total > '.$config['min_purchase'].', inv.paid >= p.sale, 1)), 1) AND p.confirmed = 0 AND p.recived_id = 0 ';
			break;
		}
		
		if($ids)
			$where .= ' AND p.id IN('.$ids.')';

		if($query){
			$where .= ' AND (p.name LIKE \'%'.$query.'%\' OR p.sale_name LIKE \'%'.$query.'%\' OR p.ship_tracking LIKE \'%'.$query.'%\' OR p.tracking LIKE \'%'.$query.'%\' OR p.id = \''.$query.'\')';
			//$order_by = 'ORDER BY p.name = \'%'.$query.'\' DESC';
		}
		
		if($date_start AND $date_finish)
			$where .= ' AND p.create_date >= CAST(\''.$date_start.'\' AS DATE) AND p.create_date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)';
		
		if($store_id)
			$where .= ' AND p.object_id = '.$store_id;
		
		if(!in_array(1, explode(',', $user['group_ids'])) AND !in_array(2, explode(',', $user['group_ids'])))
			$where .= ' AND (p.object_id = '.$user['store_id'].' OR p.object_id = 0)';
		
		$tables = 'FROM `'.DB_PREFIX.'_purchases` p LEFT JOIN `'.DB_PREFIX.'_objects` o ON o.id = p.object_id LEFT JOIN `'.DB_PREFIX.'_issues` iss ON p.issue_id = iss.id LEFT JOIN `'.DB_PREFIX.'_invoices` inv ON inv.issue_id = iss.id';
		
		// Each purchases
		foreach(db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS p.* '.$tables.$where.$order_by.' LIMIT '.($page*$count).', '.$count, 1
		) as $row){
			$class = '';
			
			if (!$row['recived_id']){
				if ($row['del'] == 1){
					$class = 'p_red';
				} else {
					if ($row['confirm_id']) {
						$class = 'p_green';
					} else {
						$class = 'p_yellow';
					}
				}
			}
				
			if($row['rma'] > 0)
				$class = 'p_orange';
			
			tpl_set('buy/item2', [
				'id' => $row['id'],
				'photo' => $row['photo'],
				'date' => convert_date($row['create_date'], true),
				'class' => $class,
				'name' => $query ? preg_replace('/('.preg_quote($query, '/').')/i', "<b class=\"ss\">$1</b>", $row['sale_name'] ?: $row['name']) : $row['sale_name'] ?: $row['name'],
				'status' => ($row['recived_id'] ? 'Received' : ($row['confirmed'] ? 'Confirmed' : 'Pending')),
				'comment' => $row['comment'],
				'purchase-currency' => $config['currency'][$row['purchase_currency']]['symbol'],
				'quantity' => $row['quantity'],
				'total' => number_format($row['total'], 2, '.', ''),
				'price' => $row['price'],
				'confirmed' => $row['rma'] < 2 ? (
					$row['confirmed'] ? 'paid' : 'unpaid'
				) : (
					$row['rma_status'] == 'open' ? 'unpaid' : 'paid'
				),
			], [
				'photo' => $row['photo'],
				'notdel' => !$row['del'],
				'rma_request' => $row['rma'] == 1,
				'comment' => $row['comment'],
				'comments' => $row['rma'] != 0,
				'edit' => $user['edit_purchase'],
				'add' => $user['add_purchase'],
				'delete' => $user['delete_purchase'],
				'confirm' => $row['confirm_id'] > 0,
				'received' => $row['recived_id'],
				'can_confirm' => $user['confirm_purchase'],
				'in-store' => (!$row['customer_id'] AND !$row['issue_id']),
				'rma' => $row['rma'] == 2
			], 'orders');
			$i++;
		}
		
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		
		$total = db_multi_query('SELECT SUM(p.total) as sum, SUM(p.quantity) as q '.$tables.$where);
		
		$left_count = intval(($res_count-($page*$count)-$i));
		
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['orders'],
				'total' => number_format($total['sum'], 2, '.', ''),
				'total_q' => $total['q']
			]));
		}
		
		tpl_set('buy/main2', [
			'uid' => $user['id'],
			'query' => $query,
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'orders' => $tpl_content['orders'],
			'total' => number_format($total['sum'], 2, '.', ''),
			'total-q' => $total['q']
		], [
			'owner' => in_to_array(1, $user['group_ids'])
		], 'content');
	}
} else {
	tpl_set('forbidden', [
		'text' => $lang['Forbidden'],
	], [], 'content');
}