<?php
/**
 * @appointment Forms admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
$id = intval($_GET['id']);

$logo = '<img src="//'.$_SERVER['HTTP_HOST'].'/templates/admin/img/logo.svg" style="max-width: 300px">';

switch($_GET['type']){
	/*
	* Transfer
	*/
	case 'transfer':
		$transfer_id = intval($_GET['transfer_id']);
		$inventory = '';
		
		$row = db_multi_query('
			SELECT 
				t.*,
				CONCAT(fu.name, \' \', fu.lastname) as fu_name,
				CONCAT(tu.name, \' \', tu.lastname) as tu_name,
				fu.image as fu_image,
				tu.image as tu_image,
				fu.phone as fu_phone,
				tu.phone as tu_phone,
				fs.name as fs_name,
				ts.name as ts_name,
				fs.phone as fs_phone,
				ts.phone as ts_phone,
				fs.address as fs_address,
				ts.address as ts_address,
				f.content
			FROM `'.DB_PREFIX.'_inventory_transfer` t
			LEFT JOIN `'.DB_PREFIX.'_forms` f
				ON f.id = '.$id.'
			LEFT JOIN `'.DB_PREFIX.'_users` fu
				ON fu.id = t.from_manager
			LEFT JOIN `'.DB_PREFIX.'_users` tu
				ON tu.id = t.to_manager
			LEFT JOIN `'.DB_PREFIX.'_objects` fs
				ON fs.id = t.from_store
			LEFT JOIN `'.DB_PREFIX.'_objects` ts
				ON ts.id = t.to_store
			WHERE t.id = '.$transfer_id);
			
		foreach($inv = db_multi_query('
			SELECT 
				i.price,
				i.model,
				t.name as type_name,
				c.name as category_name,
				m.name as model_name
			FROM `'.DB_PREFIX.'_inventory` i
			LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
				ON t.id = i.type_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
				ON c.id = i.category_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_models` m 
				ON m.id = i.model_id
			WHERE i.id IN ('.$row['inventory_ids'].')
		', true) as $item) {
			$inventory .= '<div style="position: relative; width: 100%; padding: 10px 20px; border-bottom: 1px solid #ddd">'.(
				($item['type_name'] ? $item['type_name'].' ' : '').
				($item['category_name'] ? $item['category_name'].' ' : '').
				($item['model_name'] ? $item['model_name'].' ' : '').
				($item['model'] ?: '')
			).'<span style="position: absolute; top: 10px; right: 10px; font-weight: bold;">$'.$item['price'].'</span></div>';
		}
		
		print str_ireplace([
				'{logo}',
				'{send_name}',
				'{send_photo}',
				'{send_cellphone}',
				'{receive_name}',
				'{receive_photo}',
				'{receive_cellphone}',
				'{send_store}',
				'{receive_store}',
				'{send_store_cellphone}',
				'{receive_store_cellphone}',
				'{send_store_address}',
				'{receive_store_address}',
				'{inventory}'
			],[
				$logo,
				$row['fu_name'],
				'<img src="//'.$_SERVER['HTTP_HOST'].'/uploads/images/users/'.$row['from_manager'].'/'.$row['fu_image'].'" style="width: 144px; height: 144px;">',
				$row['fu_phone'],
				$row['tu_name'],
				'<img src="//'.$_SERVER['HTTP_HOST'].'/uploads/images/users/'.$row['to_manager'].'/'.$row['tu_image'].'" style="width: 144px; height: 144px;">',
				$row['tu_phone'],
				$row['fs_name'],
				$row['ts_name'],
				$row['fs_phone'],
				$row['ts_phone'],
				$row['fs_address'],
				$row['ts_address'],
				$inventory
			], $row['content']
		);
		
	break;
	
	/*
	*	Device
	*/
	case 'device':
		$device_id = intval($_GET['device_id']);
		$row = db_multi_query('
			SELECT
				i.*,
				f.content,
				t.options as opts,
				u.name as customer_name,
				u.lastname as customer_lastname,
				u.image as customer_image,
				u.ver as customer_ver,
				u.phone as customer_phone,
				u.address as customer_address,
				u.email as customer_email,
				o.id as object_id,
				o.name as object_name,
				o.phone as object_phone,
				o.address as object_address,
				o.image as object_image,
				s.name as status_name,
				l.name as location_name,
				c.name as category_name,
				os.name as os_name
			FROM `'.DB_PREFIX.'_inventory` i
			LEFT JOIN `'.DB_PREFIX.'_forms` f
				ON f.id = '.$id.'
			INNER JOIN `'.DB_PREFIX.'_inventory_types`
				t ON i.type_id = t.id
			LEFT JOIN `'.DB_PREFIX.'_users`
				u ON u.id = i.customer_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = i.object_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
				ON s.id = i.status_id
			LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
				ON l.id = i.location_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
				ON c.id = i.category_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_os` os
				ON os.id = i.os_id
			WHERE i.id = '.$device_id
		);
		
		print str_ireplace([
				'{logo}',
				'{name}',
				'{photo}',
				'{address}',
				'{cellphone}',
				'{email}',
				'{device_barcode}',
				'{customer_barcode}',
				'{issue_barcode}',
				'{date}',
				'{devices}',
				'{invoices}',
				'{price}',
				'{quote_price}',
				'{tax}',
				'{total_price}',
				'{device}',
				'{issue}',
				'{services}',
				'{assigned}',
				'{issue_status}',
				'{issue_dsc}',
				'{opt_changer}',
				'{store_cell}',
				'{store_address}',
				'{store_name}'
			],[
				$logo,
				$row['name'].' '.$row['lastname'],
				'<img src="//'.$_SERVER['HTTP_HOST'].'/uploads/images/users/'.$row['customer_id'].'/'.$row['customer_image'].'" style="width: 144px; height: 144px;">',
				$row['customer_address'],
				$row['customer_phone'],
				$row['customer_email'],
				'<img src="data:image/png;base64,'.
					to_barcode($row['barcode'] ?: 'de '.str_pad(
						$device_id, 11, '0', STR_PAD_LEFT
						)
					)
				.'">',
				'<img src="data:image/png;base64,'.
					to_barcode('us '.str_pad(
						$row['customer_id'], 11, '0', STR_PAD_LEFT
						)
					)
				.'">',
				'',
				$row['cr_date'],
				$device_tbl.$tpl_content['devices'].$table_end,
				$invoice_tbl.$tpl_content['invoices'].$table_end,
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				$row['opt_changer'],
				$row['object_phone'],
				$row['object_address'],
				$row['object_name']
			], $row['content']
		);
	break;
	
	/*
	*	User
	*/
	case 'user':
		$user_id = intval($_GET['user_id']);
		$tbl_end = '</table>';
		$devices_ids = [];
		
 		$device_tbl = '<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						<tr>
							<th style="padding: 5px 10px; width: 10%; border-bottom: 2px solid #000;">ID</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['Type'].'</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['Category'].'</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['Model'].'</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['OS'].'</th>
						</tr>';
		
		$invoice_tbl = '<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						<tr>
							<th style="padding: 5px 10px; width: 10%; border-bottom: 2px solid #000;">ID</th>
							<th style="padding: 5px 10px; width: 18%; border-bottom: 2px solid #000;">'.$lang['Date'].'</th>
							<th style="padding: 5px 10px; width: 18%; border-bottom: 2px solid #000;">'.$lang['Total'].'</th>
							<th style="padding: 5px 10px; width: 18%; border-bottom: 2px solid #000;">'.$lang['Paid'].'</th>
							<th style="padding: 5px 10px; width: 18%; border-bottom: 2px solid #000;">'.$lang['Due'].'</th>
							<th style="padding: 5px 10px; width: 18%; border-bottom: 2px solid #000;">'.$lang['Status'].'</th>
						</tr>';
						
		if($row = db_multi_query('SELECT u.id as uid, u.name, u.image, u.address, u.email, u.phone, u.lastname, u.reg_date, f.content 
								FROM `'.DB_PREFIX.'_forms` f, 
								`'.DB_PREFIX.'_users` u
								WHERE f.id = '.$id.' 
									AND u.id = '.$user_id)){
			if($devices = db_multi_query('
				SELECT
					tb1.id, tb1.model,
					tb2.name as type_name,
					tb3.name as location_name,
					tb4.name as os_name,
					tb6.name as category_name
				FROM `'.DB_PREFIX.'_inventory` tb1
				INNER JOIN `'.DB_PREFIX.'_inventory_types` tb2
					ON tb1.type_id = tb2.id
				INNER JOIN `'.DB_PREFIX.'_objects_locations` tb3
					ON tb1.location_id = tb3.id
				INNER JOIN `'.DB_PREFIX.'_inventory_os` tb4
					ON tb1.os_id = tb4.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_types` tb5
					ON tb1.type_id = tb5.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb6
					ON tb1.category_id = tb6.id
				WHERE tb1.customer_id = '.$user_id, true
			)){
				foreach($devices as $item){
					foreach(db_multi_query(
						'SELECT i.*, u.name as staff_name, u.lastname FROM `'.DB_PREFIX.'_issues` i INNER JOIN `'.DB_PREFIX.'_users` u ON i.staff_id = u.id WHERE inventory_id = '.$item['id']
					, true) as $issue){
						tpl_set('/cicle/fIssue', [
							'id' => $issue['id'],
							'staff-id' => $issue['staff_id'],
							'staff-name' => $issue['staff_name'],
							'staff-lastname' => $issue['lastname'],
							'description' => $issue['description'],
							'date' => $issue['date']
						], [], 'issues');
					}
					tpl_set('/cicle/fInventory', [
						'id' => $item['id'],
						'user-name' => $row['name'],
						'user-lastname' => $row['lastname'],
						'name' => $item['name'],
						'model' => $item['model'],
						'os' => $item['os_name'],
						'category' => $item['category_name'],
						'location' => $item['location_name'],
						'issues' => $tpl_content['issues'],
						'type' => $item['type_name']
					], [], 'devices');
					unset($tpl_content['issues']);
				}
			}
			
			foreach(db_multi_query(
				'SELECT id, date, total, paid, conducted FROM `'.DB_PREFIX.'_invoices` WHERE customer_id = '.$user_id
			, true) as $invoice){
				tpl_set('/cicle/fInvoice', [
					'id' => $invoice['id'],
					'date' => $invoice['date'],
					'total' => $invoice['total'],
					'paid' => $invoice['paid'],
					'due' => $invoice['total'] - $invoice['paid'],
					'status' => $invoice['conducted'] ? $lang['Paid'] : $lang['Unpaid'],
					'date' => $invoice['date']
				], [], 'invoices');
			}
			print str_ireplace([
					'{logo}',
					'{name}',
					'{photo}',
					'{address}',
					'{cellphone}',
					'{email}',
					'{customer_barcode}',
					'{device_barcode}',
					'{issue_barcode}',
					'{date}',
					'{devices}',
					'{invoices}',
					'{con}',
					'{price}',
					'{quote_price}',
					'{tax}',
					'{total_price}',
					'{device}',
					'{issue}',
					'{services}',
					'{assigned}',
					'{issue_status}',
					'{issue_dsc}',
					'{store_cell}',
					'{store_address}',
					'{store_name}'
				],[
					$logo,
					$row['name'].' '.$row['lastname'],
					'<img src="//'.$_SERVER['HTTP_HOST'].'/uploads/images/users/'.$user_id.'/'.$row['image'].'" style="width: 144px; height: 144px;">',
					$row['address'],
					$row['phone'],
					$row['email'],
					'<img src="data:image/png;base64,'.
						to_barcode('us '.str_pad(
							$user_id, 11, '0', STR_PAD_LEFT
							)
						)
					.'">',
					'',
					'',
					$row['reg_date'],
					$device_tbl.$tpl_content['devices'].$table_end,
					$invoice_tbl.$tpl_content['invoices'].$table_end,
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					''
				], $row['content']
			);
		}
	break;
	
	/*
	*	Issue
	*/
	case 'issue':
	
		$issue_id = intval($_GET['issue_id']);
		$id = intval($_GET['id']);
		// Get issue
		$row = db_multi_query('
			SELECT 
				f.*,
				iss.description,
				iss.inventory_ids,
				iss.service_ids,
				iss.date, 
				iss.staff_id, 
				iss.quote, 
				i.price, IF(
					i.name = \'\', i.model, i.name
				) as inventory_name,
				i.id as device_id,
				i.customer_id,
				i.charger as charger,
				i.barcode,
				i.serial as serial,
				t.options as opts,
				u.name as customer_name,
				u.lastname as customer_lastname,
				u.phone as customer_phone,
				u.email as customer_email,
				u.address as customer_address,
				u.image as customer_image,
				st.name as staff_name,
				st.lastname as staff_lastname,
				o.id as object_id,
				o.name as object_name,
				o.phone as object_phone,
				o.tax as object_tax,
				o.address as object_address,
				s.name as status_name,
				l.name as location_name,
				c.name as category_name,
				m.name as model_name,
				t.name as type_name,
				inv.id as invoice,
				os.name as os_name,
				ct.name as country_name,
				cty.city as city_name,
				u.zipcode as zipcode
			FROM `'.DB_PREFIX.'_forms` f,
				 `'.DB_PREFIX.'_issues` iss 
			INNER JOIN `'.DB_PREFIX.'_inventory` 
				i ON iss.inventory_id = i.id 
			LEFT JOIN `'.DB_PREFIX.'_inventory_types`
				t ON i.type_id = t.id
			LEFT JOIN `'.DB_PREFIX.'_users`
				u ON u.id = i.customer_id
			LEFT JOIN `'.DB_PREFIX.'_users`
				st ON st.id = iss.staff_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = i.object_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
				ON s.id = i.status_id
			LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
				ON l.id = i.location_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
				ON c.id = i.category_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_os` os
				ON os.id = i.os_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
				ON m.id = i.model_id
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON i.id = inv.issue_id
			LEFT JOIN `'.DB_PREFIX.'_countries` ct
				ON ct.code = u.country
			LEFT JOIN `'.DB_PREFIX.'_cities` cty
				ON cty.zip_code = u.zipcode
			WHERE f.id = '.$id.'
			AND iss.id = '.$issue_id
		);
		
		if ($row['service_ids']) {
			foreach(explode(',', substr($row['service_ids'], 0, -1)) as $val) {
				$services_ids .= intval($val).',';
			}
		}
		
		$purchase_ids = substr($row['purchase_ids'], 0, -1);
		
		$users_ids = '';
		$users = [];
		$comments = db_multi_query('SELECT 
			i.comments
		FROM `'.DB_PREFIX.'_issues` i
		WHERE i.id = '.$id);
		if ($comments['comments'] && $comments['comments'] !== '{}') {
			foreach(json_decode(substr($comments['comments'], 0, -2).'}', true) as $c) {
				$users_ids .= $c['staff'].',';
			}
			$users_sql = db_multi_query('SELECT id, CONCAT(name, \' \', lastname) as name FROM `'.DB_PREFIX.'_users` WHERE id IN('.substr($users_ids, 0, -1).')', true);
			$users = array_column($users_sql, 'name', 'id');
		}

		// Stock and services
		if($row['inventory_ids'] OR $row['service_ids']){
			$services = '';
			$inventories = '';
			$purchases = '';
			$serv_json = [];
			foreach($iss_arr = db_multi_query(
				'SELECT 
					iss.id, 
					iss.inventory_ids, 
					iss.service_ids,
					iss.options,
					iss.comments,
					inv.id as inv_id, 
					inv.name as inv_name, 
					inv.type as inv_type, 
					inv.type_id as inv_type_id, 
					inv.options as inv_options, 
					inv.location_id as inv_location_id, 
					inv.price as inv_price,
					inv.barcode,
					inv.model as inv_model,
					inv.category_id as inv_category_id,
					c.name as inv_category_name, 
					t.name as inv_type_name, 
					t.options as opts, 
					l.name as inv_location_name,
					u.id as staff_id,
					u.name as staff_name,
					u.lastname as staff_lastname
				FROM `'.DB_PREFIX.'_issues` iss
				LEFT JOIN `'.DB_PREFIX.'_inventory` inv
					ON inv.id IN('.((
						$row['inventory_ids'] && $row['service_ids']) ? $row['inventory_ids'].substr($services_ids, 0, -1) : (
						$row['inventory_ids'] ? substr($row['inventory_ids'], 0, -1) : substr($services_ids, 0, -1)
					)).')
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
					ON inv.category_id = c.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
					ON inv.type_id = t.id
				LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
					ON inv.location_id = l.id 
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = REGEXP_REPLACE(iss.comments, CONCAT(\'(.*)(?:"\', inv.id, \'":{"staff" : "(.*?)", "comment": "(.*?)"},)(.*)\'), \'\\\2\')
				WHERE iss.id = '.$issue_id
				, true) as $issue){
					
				if ($issue['inv_type'] == 'stock') {
					tpl_set('/cicle/fIsStock', [
						'id' => $issue['inv_id'],
						'model' => $issue['inv_model'],
						'category' => $issue['inv_category_name'],
						'type' => $issue['inv_type_name'],
						'location' => $issue['inv_location_name'],
						'price' => $issue['inv_price'],
						'issue-id' => $issue['id']
					], [], 'devices');
				} else {
					
					$comments = json_decode(substr($issue['comments'], 0, -2).'}', true);
					$count = substr_count($row['service_ids'], $issue['inv_id'].'_');
					$sIDs = explode(',', substr($row['service_ids'], 0, -1));
					foreach($sIDs as $k => $v) {
						if (strrpos($v, $issue['inv_id']) === false)
							unset($sIDs[$k]);
					}
					$sIDs = array_values($sIDs);

					for ($j = 0; $j < $count; $j++) {
						$uId = $comments[$sIDs[$j]]['staff'];
						tpl_set('/cicle/fIsServices', [
							'id' => $sIDs[$j],
							'id-show' => $issue['inv_id'],
							'name' => $issue['inv_name'],
							'price' => number_format($issue['inv_price'], 2, '.', ""),
							'comment' => $comments[$sIDs[$j]]['comment'],
							'staff-id' => $uId,
							'staff-name' => $users[$uId],
							'issue-id' => $issue['id']
						], [
							'comment' => $comments[$sIDs[$j]]['comment']
						], 'services');
					
						$services .= $sIDs[$j].',';
						$opts = json_decode($issue['inv_options'], true);
						$options = [];
						if ($issue['inv_options']) {
							foreach(json_decode($issue['inv_options'], true) as $n => $v){
								$steps = json_decode(substr($issue['options'], 0, -2).'}', true);
								if(!$v) continue;
								$options[$n] = [
									'name' => $opts[$n],
									'value' => in_array($n, (explode(',', $steps[$sIDs[$j]])))
								];
							}
						}
						
						$serv_json[$sIDs[$j]] = [
							'name' => $issue['inv_name'],
							'steps' => $options
						];
					}
				} 
			}
		}
		
		
		
		$tbl_end = '</table>';
		
		$steps_html = '';
		if ($serv_json) {
			foreach($serv_json as $si => $sj) {
				$steps_html .= '<div id="sSteps_'.$si.'" class="servSteps">
									<div class="servTitle">'.$sj['name'].'</div><ul>'; 
				foreach($sj['steps'] as $ost) {
					if ($ost['value']) $steps_html .= '<li>'.$ost['name'].'</li>';
				}
				$steps_html .= '</ul></div>';
			}
		}
		
 		$device_tbl = '<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						<tr>
							<th style="padding: 5px 10px; width: 10%; border-bottom: 2px solid #000;">ID</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['Type'].'</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['Category'].'</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['Model'].'</th>
							<th style="padding: 5px 10px; width: 22.5%; border-bottom: 2px solid #000;">'.$lang['Price'].'</th>
						</tr>';
		
		$service_tbl = '<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						<tr>
							<th style="padding: 5px 10px; width: 10%; border-bottom: 2px solid #000;">ID</th>
							<th style="padding: 5px 10px; width: 45%; border-bottom: 2px solid #000;">'.$lang['Name'].'</th>
							<th style="padding: 5px 10px; width: 45%; border-bottom: 2px solid #000;">'.$lang['Price'].'</th>
						</tr>';
		
		print str_ireplace([
				'{logo}',
				'{name}',
				'{photo}',
				'{address}',
				'{price}',
				'{quote_price}',
				'{tax}',
				'{total_price}',
				'{device}',
				'{issue_barcode}',
				'{customer_barcode}',
				'{device_barcode}',
				'{intake_date}',
				'{devices}',
				'{services}',
				'{assigned}',
				'{cellphone}',
				'{email}',
				'{issue_status}',
				'{issue_dsc}',
				'{opt_charger}',
				'{device_sn}',
				'{device_mod}',
				'{device_brand}',
				'{device_typ}',
				'{country}',
				'{city}',
				'{zip}',
				'{tech_summery}',
				'{store_cell}',
				'{store_address}',
				'{store_name}',
				'{date}'
			],[
				$logo,
				$row['customer_name'].' '.$row['customer_lastname'],
				'<img src="//'.$_SERVER['HTTP_HOST'].'/uploads/images/users/'.$row['customer_id'].'/'.$row['customer_image'].'" style="width: 144px; height: 144px;">',
				$row['customer_address'],
				$row['price'],
				$row['quote'],
				'',
				'',
				$row['category_name'].' '.$row['inventory_name'],
				'<img src="data:image/png;base64,'.
					to_barcode('is '.str_pad(
						$issue_id, 11, '0', STR_PAD_LEFT
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
					to_barcode($row['barcode'] ? str_pad(
						$row['device_id'], 11, '0', STR_PAD_LEFT
						) : 'de '.str_pad(
						$row['device_id'], 11, '0', STR_PAD_LEFT
						)
					)
				.'">',
				$row['date'],
				$device_tbl.$tpl_content['devices'].$tbl_end,
				$service_tbl.$tpl_content['services'].$tbl_end,
				$row['staff_name'].' '.$row['staff_lastname'],
				$row['customer_phone'],
				$row['customer_email'],
				$row['status_name'],
				$row['description'],
				$row['charger'] ? 'yes' : 'no',
				$row['serial'],
				$row['model_name'],
				$row['category_name'],
				$row['type_name'],
				$row['country_name'],
				$row['city_name'],
				$row['zipcode'],
				$steps_html,
				$row['object_phone'],
				$row['object_address'],
				$row['object_name'],
				date('Y-m-d H:i', time())
			], $row['content']
		);
	break;
}
echo '<script>window.onload = function() {
        print();
		return false;
    }</script>';
die;
?>