<?php
/**
 * @appointment Developers
 * @author      Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
echo '<pre>';

error_reporting(E_ALL|E_STRICT);

ini_set('display_errors', true);

$mm = new Memcached;
$mm->addServer('/var/run/memcached/memcached.sock', 0);
$mm->set('test', '1');
echo $mm->get('test');

die('<br>ooo');

/* if ($users = db_multi_query('
	SELECT 
		u5.*
	FROM `'.DB_PREFIX.'_users_05` u5
	WHERE system_id = 0
', true)) {
	foreach($users as $u) {
		// INSERT USERS
		
		db_query('INSERT INTO `'.DB_PREFIX.'users`
		(`group_id`, `group_ids`, `login`, `email`, `password`, `hid`, `name`, `lastname`, `image`, `phone`, `sms`, 
		`address`, `country`, `state`, `city`, `zipcode`, `ver`, `lang`, `ip`, `points`, `reg_date`, `last_visit`, 
		`sex`, `birthday`, `company`, `contact`, `referral`, `referrals`, `new_msg`, `pay`, `del`, `last_salary`, 
		`ref_points`, `discount_visitors`, `dub`) VALUES
		(0,5,\''.$u['login'].'\',\''.$u['email'].'\',\''.$u['password'].'\',\''.$u['hid'].'\',\''.$u['name'].'\',\''.$u['lastname'].'\',\''.$u['image'].'\',\''.$u['phone'].'\',\''.$u['sms'].'\',
		\''.$u['address'].'\',\''.$u['country'].'\',\''.$u['state'].'\',\''.$u['city'].'\',\''.$u['zipcode'].'\',\''.$u['ver'].'\',\''.$u['lang'].'\',\''.$u['ip'].'\',\''.$u['points'].'\',\''.$u['reg_date'].'\',\''.$u['last_visit'].'\',
		\''.$u['sex'].'\',\''.$u['birthday'].'\',\''.$u['company'].'\',\''.$u['contact'].'\',\''.$u['referral'].'\',\''.$u['referrals'].'\',\''.$u['new_msg'].'\',\''.$u['pay'].'\',\''.$u['del'].'\',\''.$u['last_salary'].'\',
		\''.$u['ref_points'].'\',\''.$u['discount_visitors'].'\',\''.$u['dub'].'\')');
		
		$id = intval(mysqli_insert_id($db_link));
		
		db_query('UPDATE `'.DB_PREFIX.'_users_05` SET system_id = '.$id.' WHERE id = '.$u['id']);
		
		// UPDATE IDS
		
		db_query('UPDATE `'.DB_PREFIX.'_inventory_05` SET customer_id = '.$id.' WHERE customer_id = '.$u['id']);
		db_query('UPDATE `'.DB_PREFIX.'_invoices_05` SET customer_id = '.$id.' WHERE customer_id = '.$u['id']);
		db_query('UPDATE `'.DB_PREFIX.'_purchases_05` SET customer_id = '.$id.' WHERE customer_id = '.$u['id']);
		db_query('UPDATE `'.DB_PREFIX.'_issues_05` SET customer_id = '.$id.' WHERE customer_id = '.$u['id']);
	}
} */


/* if ($inventory = db_multi_query('
	SELECT 
		*
	FROM `'.DB_PREFIX.'_inventory_05`
	WHERE system_id = 0
', true)) {
	foreach($inventory as $u) {
		// INSERT INVENTORY
		
		db_query('INSERT INTO `'.DB_PREFIX.'_inventory`
		(`name`, `model_id`, `os_id`, `model`, `serial`, `ver_os`, `price`, `currency`, `purchase_price`, `purchase_currency`, 
		`sale_price`, `type_id`, `owner_id`, `customer_id`, `status_id`, `store_status_id`, `category_id`, `store_category_id`, 
		`location_id`, `location_count`, `object_id`, `type`, `options`, `commerce`, `descr`, `images`, `main`, `charger`, `opt_charger`, 
		`confirmed`, `cr_user`, `cr_date`, `cr_issue`, `cn_date`, `cn_user`, `vendor_id`, `pickup`, `object_owner`, `owner_type`, `time`, 
		`quantity`, `save_data`, `save_data_comment`, `barcode`, `parts_required`, `tradein`, `del`, `warranty`, `warranty_issue`, 
		`warranty_date`, `warranty_status`, `accessories`, `is_issue`, `craglist_url`, `craglist_date`) VALUES
		(\''.$u['name'].'\',\''.$u['model_id'].'\',\''.$u['os_id'].'\',\''.$u['model'].'\',\''.$u['serial'].'\',\''.$u['ver_os'].'\',\''.$u['price'].'\',\''.$u['currency'].'\',\''.$u['purchase_price'].'\',\''.$u['purchase_currency'].'\',
		\''.$u['sale_price'].'\',\''.$u['type_id'].'\',\''.$u['owner_id'].'\',\''.$u['customer_id'].'\',\''.$u['status_id'].'\',\''.$u['store_status_id'].'\',\''.$u['category_id'].'\',\''.$u['store_category_id'].'\',
		\''.$u['location_id'].'\',\''.$u['location_count'].'\',\''.$u['object_id'].'\',\''.$u['type'].'\',\''.$u['options'].'\',\''.$u['commerce'].'\',\''.$u['descr'].'\',\''.$u['images'].'\',\''.$u['main'].'\',\''.$u['charger'].'\',\''.$u['opt_charger'].'\',
		\''.$u['confirmed'].'\',\''.$u['cr_user'].'\',\''.$u['cr_date'].'\',\''.$u['cr_issue'].'\',\''.$u['cn_date'].'\',\''.$u['cn_user'].'\',\''.$u['vendor_id'].'\',\''.$u['pickup'].'\',\''.$u['object_owner'].'\',\''.$u['owner_type'].'\',\''.$u['time'].'\',
		\''.$u['quantity'].'\',\''.$u['save_data'].'\',\''.$u['save_data_comment'].'\',\''.$u['barcode'].'\',\''.$u['parts_required'].'\',\''.$u['tradein'].'\',\''.$u['del'].'\',\''.$u['warranty'].'\',\''.$u['warranty_issue'].'\',
		\''.$u['warranty_date'].'\',\''.$u['warranty_status'].'\',\''.$u['accessories'].'\',\''.$u['is_issue'].'\',\''.$u['craglist_url'].'\',\''.$u['craglist_date'].'\')');
		
		$id = intval(mysqli_insert_id($db_link));
		
		db_query('UPDATE `'.DB_PREFIX.'_inventory_05` SET system_id = '.$id.' WHERE id = '.$u['id']);
		
		// UPDATE IDS
		
		db_query('UPDATE `'.DB_PREFIX.'_issues_05` SET inventory_id = '.$id.' WHERE inventory_id = '.$u['id']);
	}
} */


/* if ($issues = db_multi_query('
	SELECT 
		*
	FROM `'.DB_PREFIX.'_issues_05`
	WHERE system_id = 0
', true)) {
	foreach($issues as $u) {
		// INSERT ISSUES
		
		db_query('INSERT INTO `'.DB_PREFIX.'_issues`
		(`staff_id`, `intake_id`, `customer_id`, `object_owner`, `description`, `inventory_id`, `total`, 
		`currency`, `date`, `inventory_ids`, `service_ids`, `purchase_ids`, `doit`, `quote`, `options`, 
		`comments`, `purchase_prices`, `important`, `discount`, `discount_confirmed`, `discount_reason`, 
		`discount_user`, `upcharge_id`, `purchase_done`, `service_info`, `inventory_info`, `purchase_info`,
		`upcharge_info`, `assigned`, `finished`, `status_id`, `warranty`, `warranty_status`, `warranty_purchases`) VALUES
		(\''.$u['staff_id'].'\',\''.$u['intake_id'].'\',\''.$u['customer_id'].'\',\''.$u['object_owner'].'\',\''.$u['description'].'\',\''.$u['inventory_id'].'\',\''.$u['total'].'\',
		\''.$u['currency'].'\',\''.$u['date'].'\',\''.$u['inventory_ids'].'\',\''.$u['service_ids'].'\',\''.$u['purchase_ids'].'\',\''.$u['doit'].'\',\''.$u['quote'].'\',\''.$u['options'].'\',
		\''.$u['comments'].'\',\''.$u['purchase_prices'].'\',\''.$u['important'].'\',\''.$u['discount'].'\',\''.$u['discount_confirmed'].'\',\''.$u['discount_reason'].'\',
		\''.$u['discount_user'].'\',\''.$u['upcharge_id'].'\',\''.$u['purchase_done'].'\',\''.$u['service_info'].'\',\''.$u['inventory_info'].'\',\''.$u['purchase_info'].'\',
		\''.$u['upcharge_info'].'\',\''.$u['assigned'].'\',\''.$u['finished'].'\',\''.$u['status_id'].'\',\''.$u['warranty'].'\',\''.$u['warranty_status'].'\',\''.$u['warranty_purchases'].'\')');
		
		$id = intval(mysqli_insert_id($db_link));
		
		db_query('UPDATE `'.DB_PREFIX.'_issues_05` SET system_id = '.$id.' WHERE id = '.$u['id']);
		
		// UPDATE IDS
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices_05` SET issue_id = '.$id.' WHERE issue_id = '.$u['id']);
		db_query('UPDATE `'.DB_PREFIX.'_purchases_05` SET issue_id = '.$id.' WHERE issue_id = '.$u['id']);
	}
} */

/* if ($purchases = db_multi_query('
	SELECT 
		*
	FROM `'.DB_PREFIX.'_purchases_05`
	WHERE system_id = 0
', true)) {
	foreach($purchases as $u) {
		// INSERT PURCHASES
		
		db_query('INSERT INTO `'.DB_PREFIX.'_purchases`
		(`link`, `name`, `sale_name`, `price`, `purchase_currency`, `quantity`, `total`, 
		`sale`, `currency`, `estimated`, `user_status`, `status`, `photo`, `tracking`, 
		`comment`, `customer_id`, `issue_id`, `invoice_id`, `object_id`, `create_id`, 
		`create_date`, `confirm_id`, `confirm_date`, `edited_id`, `edited_date`, `del`, 
		`confirmed`, `ship_tracking`, `rma`, `rma_status`, `rma_request_staff`, `rma_request_date`, 
		`rma_confirm_staff`, `rma_confirm_date`, `rma_pickup_staff`, `rma_pickup_date`, `rma_comment`, 
		`recived_id`, `recived_date`, `transaction`, `stock_id`) VALUES
		(\''.$u['link'].'\',\''.$u['name'].'\',\''.$u['sale_name'].'\',\''.$u['price'].'\',\''.$u['purchase_currency'].'\',\''.$u['quantity'].'\',\''.$u['total'].'\',
		\''.$u['sale'].'\',\''.$u['currency'].'\',\''.$u['estimated'].'\',\''.$u['user_status'].'\',\''.$u['status'].'\',\''.$u['photo'].'\',\''.$u['tracking'].'\',
		\''.$u['comment'].'\',\''.$u['customer_id'].'\',\''.$u['issue_id'].'\',\''.$u['invoice_id'].'\',\''.$u['object_id'].'\',\''.$u['create_id'].'\',
		\''.$u['create_date'].'\',\''.$u['confirm_id'].'\',\''.$u['confirm_date'].'\',\''.$u['edited_id'].'\',\''.$u['edited_date'].'\',\''.$u['del'].'\',
		\''.$u['confirmed'].'\',\''.$u['ship_tracking'].'\',\''.$u['rma'].'\',\''.$u['rma_status'].'\',\''.$u['rma_request_staff'].'\',\''.$u['rma_request_date'].'\',
		\''.$u['rma_confirm_staff'].'\',\''.$u['rma_confirm_date'].'\',\''.$u['rma_pickup_staff'].'\',\''.$u['rma_pickup_date'].'\',\''.$u['rma_comment'].'\',
		\''.$u['recived_id'].'\',\''.$u['recived_date'].'\',\''.$u['transaction'].'\',\''.$u['stock_id'].'\')');
		
		$id = intval(mysqli_insert_id($db_link));
		
		db_query('UPDATE `'.DB_PREFIX.'_purchases_05` SET system_id = '.$id.' WHERE id = '.$u['id']);
		
		// UPDATE IDS
		
		if (intval($u['issue_id'])) {
			$issue = db_multi_query('SELECT purchase_info FROM `'.DB_PREFIX.'_issues` WHERE id = '.$u['issue_id']);
			$pi = json_decode($issue['purchase_info'], true);
			$npi = [];
			if (is_array($pi)) {
				foreach($pi as $k => $v) {
					$npi[$k == $u['id'] ? $id : $k] = $v;
				}
				db_query('UPDATE `'.DB_PREFIX.'_issues` SET purchase_info = \''.json_encode($npi).'\' WHERE id = '.$u['issue_id']);
			}
		}
		
		if (intval($u['invoice_id'])) {
			$issue = db_multi_query('SELECT purchases_info FROM `'.DB_PREFIX.'_invoices_05` WHERE id = '.$u['invoice_id']);
			$pi = json_decode($issue['purchases_info'], true);
			$npi = [];
			if (is_array($pi)) {
				foreach($pi as $k => $v) {
					$npi[$k == $u['id'] ? $id : $k] = $v;
				}
				db_query('UPDATE `'.DB_PREFIX.'_invoices_05` SET purchases_info = \''.json_encode($npi).'\' WHERE id = '.$u['invoice_id']);
			}
		}
	}
} */


/* if ($invoices = db_multi_query('
	SELECT 
		*
	FROM `'.DB_PREFIX.'_invoices_05`
	WHERE system_id = 0
', true)) {
	foreach($invoices as $u) {
		// INSERT INVOICES
		
		db_query('INSERT INTO `'.DB_PREFIX.'_invoices`
		(`customer_id`, `staff_id`, `discount_id`, `invoices`, `issue_id`, `date`, `tr_date`,
		`currency`, `total`, `paid`, `tax`, `buy_inventory`, `inventory`, `services`, `object_id`,
		`conducted`, `pay_method`, `type`, `purchace`, `purchases`, `tradein`, `onsite_id`, 
		`add_onsite`, `add_onsite_price`, `order_id`, `transaction`, `refund`, `refund_user`, 
		`discount`, `discount_confirmed`, `issue_info`, `inventory_info`, `services_info`, 
		`purchases_info`, `tradein_info`, `refund_info`, `refund_invoice`, `refund_inventory`, 
		`refund_paid`, `refund_comment`, `profit`, `estimate`, `store_discount`) VALUES
		(\''.$u['customer_id'].'\',\''.$u['staff_id'].'\',\''.$u['discount_id'].'\',\''.$u['invoices'].'\',\''.$u['issue_id'].'\',\''.$u['date'].'\',\''.$u['tr_date'].'\',
		\''.$u['currency'].'\',\''.$u['total'].'\',\''.$u['paid'].'\',\''.$u['tax'].'\',\''.$u['buy_inventory'].'\',\''.$u['inventory'].'\',\''.$u['services'].'\',\''.$u['object_id'].'\',
		\''.$u['conducted'].'\',\''.$u['pay_method'].'\',\''.$u['type'].'\',\''.$u['purchace'].'\',\''.$u['purchases'].'\',\''.$u['tradein'].'\',\''.$u['onsite_id'].'\',
		\''.$u['add_onsite'].'\',\''.$u['add_onsite_price'].'\',\''.$u['order_id'].'\',\''.$u['transaction'].'\',\''.$u['refund'].'\',\''.$u['refund_user'].'\',
		\''.$u['discount'].'\',\''.$u['discount_confirmed'].'\',\''.$u['issue_info'].'\',\''.$u['inventory_info'].'\',\''.$u['services_info'].'\',
		\''.$u['purchases_info'].'\',\''.$u['tradein_info'].'\',\''.$u['refund_info'].'\',\''.$u['refund_invoice'].'\',\''.$u['refund_inventory'].'\',
		\''.$u['refund_paid'].'\',\''.$u['refund_comment'].'\',\''.$u['profit'].'\',\''.$u['estimate'].'\',\''.$u['store_discount'].'\')');
		
		$id = intval(mysqli_insert_id($db_link));
		
		db_query('UPDATE `'.DB_PREFIX.'_invoices_05` SET system_id = '.$id.' WHERE id = '.$u['id']);
		
		// UPDATE IDS
		
		if (intval($u['issue_id'])) {
			$invoice = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_issues` WHERE id = '.$u['issue_id']);
			$issue_info = '{"total":"'.$invoice['total'].'","inventory":'.$invoice['inventory_info'].',"services":'.$invoice['service_info'].',"purchases":'.$invoice['purchase_info'].',"upcharge":'.($invoice['upcharge_info'] ?: '{}').'}';
			db_query('UPDATE `'.DB_PREFIX.'_invoices` SET issue_info = \''.$issue_info.'\' WHERE id = '.$id);
		}
		
		if ($u['purchases_info']) {
			$pi = json_decode($u['purchases_info'], true);
			if (is_array($pi) AND array_keys($pi)) {
				db_query('UPDATE `'.DB_PREFIX.'_purchases` SET invoice_id = \''.$id.'\' WHERE id IN ('.implode(',', array_keys($pi)).')');
			}
		}
	}
} */

die('OK');
 
 ?>