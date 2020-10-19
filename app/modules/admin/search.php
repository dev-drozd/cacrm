<?php
/**
 * @appointment Search admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
*/

defined('ENGINE') or ('hacking attempt!');

$query = text_filter($_POST['q'], 255);

$tpl = '';

switch($_POST['type']){
	
	case 'us':
		$susers = [];
		if ($users = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
				u.id, u.name, u.lastname, u.phone, u.image
			FROM `'.DB_PREFIX.'_users` u
			WHERE CONCAT(
				u.name, \' \', u.lastname
			) LIKE \'%'.$query.'%\' OR u.email LIKE \'%'.$query.'%\' OR REGEXP_REPLACE(u.phone, \' \', \'\') LIKE \'%'.$query.'%\'
			'.(
				$query ? '' : 'ORDER BY u.id DESC'
			).' LIMIT 0, 10', true
		)) {
			$appointments = db_multi_query('SELECT id, customer_id FROM `'.DB_PREFIX.'_users_appointments` WHERE customer_id IN ('.implode(',', array_column($users, 'id')).') AND confirmed = 0', true);
			$ausers = array_column($appointments, 'customer_id');
			foreach($users as $row){
				if (in_array($row['id'], $ausers)) {
					$aps = array_column(array_filter($appointments, function($a) use(&$row) {
						if ($a['customer_id'] == $row['id'])
							return $a;
					}), 'id');
					sort($aps);
					$tpl = '<tr>
							<td><a href="/users/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">
								'.($row['image'] ? '<img src="/uploads/images/users/'.$row['id'].'/thumb_'.$row['image'].'" class="miniRound">' :
									'<span class="fa fa-user-secret miniRound"></span>'
							).$row['name'].' '.$row['lastname'].'</a><span class="hnt hntTop" data-title="Confirm appointment"><span class="btn-notif notif-confirm fa fa-check" onclick="user.confirmApp(this, '.$aps[count($aps) - 1].', 1);"></span></span></td><td>'.$row['phone'].'</td>
						</tr>'.$tpl;
				} else {
					$tpl .= '<tr>
						<td><a href="/users/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">
							'.($row['image'] ? '<img src="/uploads/images/users/'.$row['id'].'/thumb_'.$row['image'].'" class="miniRound">' :
								'<span class="fa fa-user-secret miniRound"></span>'
						).$row['name'].' '.$row['lastname'].'</a></td><td>'.$row['phone'].'</td>
					</tr>';	
				}
			}
		} else {
			$tpl .= '<tr><td class="colspan="2"><a href="/users/add" onclick="Page.get(this.href); return false;" class="btn btnSubmit new_user">New user</a></td></tr>';
		}
		$type = 'users';
	break;
	
	case 'ob':
		foreach(db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS id, name, descr, phone, image FROM
			`'.DB_PREFIX.'_objects`
			WHERE name LIKE \'%'.$query.'%\' OR descr LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY id DESC'
			).' LIMIT 0, 10', true
		) as $row){
			$tpl .= '<tr>
				<td><a href="/objects/edit/'.$row['id'].'" onclick="Page.get(this.href); return false;">
					'.($row['image'] ? '<img src="/uploads/images/stores/'.$row['id'].'/thumb_'.$row['image'].'" class="miniRound">' :
						'<span class="fa fa-user-secret miniRound"></span>'
				).$row['name'].' '.$row['lastname'].'</a></td><td>'.$row['phone'].'</td>
			</tr>';	
		}
		$type = 'objects';
	break;
	
	case 'pu':
		foreach(db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS id, name, comment, price, photo
			FROM `'.DB_PREFIX.'_purchases`
			WHERE name LIKE \'%'.$query.'%\' OR comment LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY id DESC'
			).' LIMIT 0, 10', true
		) as $row){
			$tpl .= '<tr>
				<td><a href="/purchases/edit/'.$row['id'].'" onclick="Page.get(this.href); return false;">
					'.($row['photo'] ? '<img src="/uploads/images/'.$row['id'].'/thumb_'.$row['photo'].'" class="miniRound">' :
						'<span class="fa fa-user-secret miniRound"></span>'
				).$row['name'].'</a></td><td>'.$row['price'].'</td>
			</tr>';
		}
		$type = 'purchases';
	break;
	
	case 'is':
		foreach(db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS i.id, i.description, u.phone 
			FROM `'.DB_PREFIX.'_issues` i
			LEFT JOIN `'.DB_PREFIX.'_inventory` d
				ON d.id = i.inventory_id
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON d.customer_id = u.id
			WHERE i.description LIKE \'%'.$query.'%\' OR i.id = '.intval($query).' '.(
				$query ? '' : 'ORDER BY i.id DESC'
			).' LIMIT 0, 10', true
		) as $row){
			$tpl .= '<tr>
				<td><a href="/issues/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">#'.$row['id'].'</a></td><td>'.$row['phone'].'</td>
			</tr>';
		}
		$type = 'issues';
	break;
	
	case 'de':
		foreach(db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS id, IF(
				name = \'\', model, name
			) as name, serial
			FROM `'.DB_PREFIX.'_inventory`
			WHERE model LIKE \'%'.$query.'%\' OR barcode LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY id DESC'
			).' LIMIT 0, 10', true
		) as $row){
			$tpl .= '<tr>
				<td><a href="/inventory/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">
					'.$row['name'].'</a></td><td>'.$row['serial'].'</td>
			</tr>';
		}
		$type = 'inventory';
	break;
	
	case 'in':
		foreach(db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				i.id, i.total, i.tax, u.name, u.lastname 
			FROM `'.DB_PREFIX.'_invoices` i
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON i.customer_id = u.id
			WHERE CONCAT(
				u.name, u.lastname
			) LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY i.id DESC'
			).' LIMIT 0, 10', true
		) as $row){
			$tpl .= '<tr>
				<td><a href="/invoices/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">#'.
					$row['id'].' '.$row['name'].' '.$row['lastname'].'</a></td><td>$'.number_format(
						($row['total'] + $row['tax']), 2, '.', ' '
					).'</td>
			</tr>';
		}
		$type = 'invoices';
	break;
	
	case null;
	
	
		// Users
		if ($users = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
				u.id, u.name, u.lastname, u.phone, u.image
			FROM `'.DB_PREFIX.'_users` u
			WHERE CONCAT(
				u.name, \' \', u.lastname
			) LIKE \'%'.$query.'%\' OR u.email LIKE \'%'.$query.'%\' OR REGEXP_REPLACE(u.phone, \' \', \'\') LIKE \'%'.$query.'%\'
			'.(
				$query ? '' : 'ORDER BY u.id DESC'
			).' LIMIT 0, 3', true
		)) {
			
			$count = (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0];
				
			$appointments = db_multi_query('SELECT id, customer_id FROM `'.DB_PREFIX.'_users_appointments` WHERE customer_id IN ('.implode(',', array_column($users, 'id')).') AND confirmed = 0', true);
			$ausers = array_column($appointments, 'customer_id');
			foreach($users as $row){
				$phones = '';
				foreach(explode(',', $row['phone']) as $phone){
					$phones .= '<a href="tel:'.$phone.'">'.$phone.'</a>';
				}
				if (in_array($row['id'], $ausers)) {
					$aps = array_column(array_filter($appointments, function($a) use(&$row) {
						if ($a['customer_id'] == $row['id'])
							return $a;
					}), 'id');
					sort($aps);
					$tpl = '<tr>
							<td><a href="/users/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">
								'.($row['image'] ? '<img src="/uploads/images/users/'.$row['id'].'/thumb_'.$row['image'].'" class="miniRound">' :
									'<span class="fa fa-user-secret miniRound"></span>'
							).$row['name'].' '.$row['lastname'].'</a><span class="hnt hntTop" data-title="Confirm appointment"><span class="btn-notif notif-confirm fa fa-check" onclick="user.confirmApp(this, '.$aps[count($aps) - 1].', 1);"></span></span></td><td>'.$phones.'</td>
						</tr>'.$tpl;
				} else {
					$tpl .= '<tr>
						<td><a href="/users/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">
							'.($row['image'] ? '<img src="/uploads/images/users/'.$row['id'].'/thumb_'.$row['image'].'" class="miniRound">' :
								'<span class="fa fa-user-secret miniRound"></span>'
						).$row['name'].' '.$row['lastname'].'</a></td><td>'.$phones.'</td>
					</tr>';	
				}
			}
			if($count)
				$tpl = '<tr><td colspan="2" class="shead"><b>Users ('.$count.')</b>'.(
					$count > 3 ? '<a href="/users?q='.$query.'" onclick="Page.get(this.href); return false;">All users</a>' : ''
				).'</td></tr>'.$tpl;
		} else {
			$tpl .= '<tr><td class="colspan="2"><a href="/users/add" onclick="Page.get(this.href); return false;" class="btn btnSubmit new_user">New user</a></td></tr>';
		}

		unset($users);
		unset($appointments);
		unset($ausers);

		// Stores
		$stores = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS id, name, descr, phone, image FROM
			`'.DB_PREFIX.'_objects`
			WHERE name LIKE \'%'.$query.'%\' OR descr LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY id DESC'
			).' LIMIT 0, 3', true
		);
		
		if($count = (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0])
			$tpl .= '<tr><td colspan="2" class="shead"><b>Stores ('.$count.')</b>'.(
				$count > 3 ? '<a href="/objects?q='.$query.'" onclick="Page.get(this.href); return false;">All stores</a>' : ''
			).'</td></tr>';
		
		foreach($stores as $row){
			$tpl .= '<tr>
				<td><a href="/objects/edit/'.$row['id'].'" onclick="Page.get(this.href); return false;">
					'.($row['image'] ? '<img src="/uploads/images/stores/'.$row['id'].'/thumb_'.$row['image'].'" class="miniRound">' :
						'<span class="fa fa-user-secret miniRound"></span>'
				).$row['name'].' '.$row['lastname'].'</a></td><td>'.$row['phone'].'</td>
			</tr>';	
		}

		unset($stores);
		
		// Purchases
		$purchases = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS id, name, comment, price, photo
			FROM `'.DB_PREFIX.'_purchases`
			WHERE name LIKE \'%'.$query.'%\' OR comment LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY id DESC'
			).' LIMIT 0, 3', true
		);

		if($count = (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0])
			$tpl .= '<tr><td colspan="2" class="shead"><b>Purchases ('.$count.')</b>'.(
				$count > 3 ? '<a href="/purchases?q='.$query.'" onclick="Page.get(this.href); return false;">All purchases</a>' : ''
			).'</td></tr>';

		foreach($purchases as $row){
			$tpl .= '<tr>
				<td title="'.$row['name'].'"><a href="/purchases/edit/'.$row['id'].'" onclick="Page.get(this.href); return false;">
					'.($row['photo'] ? '<img src="/uploads/images/'.$row['id'].'/thumb_'.$row['photo'].'" class="miniRound">' :
						'<span class="fa fa-user-secret miniRound"></span>'
				).$row['name'].'</a></td><td>'.$row['price'].'</td>
			</tr>';
		}

		unset($purchases);

		// Issues
		$issues = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS i.id, i.description, u.phone 
			FROM `'.DB_PREFIX.'_issues` i
			LEFT JOIN `'.DB_PREFIX.'_inventory` d
				ON d.id = i.inventory_id
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON d.customer_id = u.id
			WHERE i.description LIKE \'%'.$query.'%\' OR i.id = '.intval($query).' '.(
				$query ? '' : 'ORDER BY i.id DESC'
			).' LIMIT 0, 3', true
		);

		if($count = (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0])
			$tpl .= '<tr><td colspan="2" class="shead"><b>Issues ('.$count.')</b>'.(
				$count > 3 ? '<a href="/issues?q='.$query.'" onclick="Page.get(this.href); return false;">All issues</a>' : ''
			).'</td></tr>';

		foreach($issues as $row){
			$phones = '';
			foreach(explode(',', $row['phone']) as $phone){
				$phones .= '<a href="tel:'.$phone.'">'.$phone.'</a>';
			}
			$tpl .= '<tr>
				<td><a href="/issues/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">#'.$row['id'].'</a></td><td>'.$phones.'</td>
			</tr>';
		}

		unset($issues);

		// Inventories
		$inventories = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS id, IF(
				name = \'\', model, name
			) as name, serial
			FROM `'.DB_PREFIX.'_inventory`
			WHERE model LIKE \'%'.$query.'%\' OR barcode LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY id DESC'
			).' LIMIT 0, 3', true
		);

		if($count = (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0])
			$tpl .= '<tr><td colspan="2" class="shead"><b>Inventories ('.$count.')</b>'.(
				$count > 3 ? '<a href="/inventory?q='.$query.'" onclick="Page.get(this.href); return false;">All inventories</a>' : ''
			).'</td></tr>';

		foreach($inventories as $row){
			$tpl .= '<tr>
				<td><a href="/inventory/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">
					'.$row['name'].'</a></td><td>'.$row['serial'].'</td>
			</tr>';
		}

		unset($inventories);

		$invoices = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				i.id, i.total, i.tax, u.name, u.lastname 
			FROM `'.DB_PREFIX.'_invoices` i
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON i.customer_id = u.id
			WHERE CONCAT(
				u.name, u.lastname
			) LIKE \'%'.$query.'%\''.(
				$query ? '' : 'ORDER BY i.id DESC'
			).' LIMIT 0, 3', true
		);

		if($count = (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0])
			$tpl .= '<tr><td colspan="2" class="shead"><b>Invoices ('.$count.')</b>'.(
				$count > 3 ? '<a href="/invoices?q='.$query.'" onclick="Page.get(this.href); return false;">All invoices</a>' : ''
			).'</td></tr>';

		foreach($invoices as $row){
			$tpl .= '<tr>
				<td><a href="/invoices/view/'.$row['id'].'" onclick="Page.get(this.href); return false;">#'.
					$row['id'].' '.$row['name'].' '.$row['lastname'].'</a></td><td>$'.number_format(
						($row['total'] + $row['tax']), 2, '.', ' '
					).'</td>
			</tr>';
		}

		unset($invoices);
		
	break;
}

$count = (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0];

exit(json_encode([
	'content' => $tpl ? '<table>
		<tbody>
		'.$tpl.'
		</tbody>
	</table>' : ''
]));
?>