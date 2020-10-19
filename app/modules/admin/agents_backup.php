<?php
	defined('ENGINE') or ('hacking attempt!');
	die;
	switch($route[1]) {
		case 'stores':
			switch($route[2]) {
				case 'del':
					is_ajax() or die('Hacking attempt!');
					db_query('UPDATE `'.DB_PREFIX.'_partner_stores` SET del = 1 WHERE id = '.intval($_POST['id']));
					die('OK');
				break;
				
				case 'send':
					is_ajax() or die('Hacking attempt!');
		
					// Filters
					$id = intval($_POST['id']);
					$name = text_filter($_POST['name'], 255, false);
					$address = text_filter($_POST['address'], 255, false);
					$phone = text_filter($_POST['phone']);
					$descr = text_filter($_POST['desc'], 255, false);

					// Check time
					preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $_POST['work_time_start']) or die('Err time format');
					preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $_POST['work_time_end']) or die('Err time format');
					
					// Check phone
					preg_match("/^[0-9-(-+\s]+$/", $phone) or die('phone_not_valid');
						
					db_query((
						$id ? 'UPDATE' : 'INSERT INTO'
					).' `'.DB_PREFIX.'_partner_stores` SET
						name = \''.$name.'\',
						address = \''.$address.'\',
						phone = \''.$phone.'\',
						descr = \''.$descr.'\',
						work_time_start = \''.$_POST['work_time_start'].'\',
						work_time_end = \''.$_POST['work_time_end'].'\'
					'.(
						$id ? 'WHERE id = '.$id : ''
					));
						
					$id = $id ? $id : intval(
						mysqli_insert_id($db_link)
					);

					echo $id;
					die;
				break;
				
				case 'add':
				case 'edit':
					$id = intval($route[3]);
					$meta['title'] = 'Partner store';
	
					if($id)
						$u = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_partner_stores` WHERE id = '.$id);
					
					
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
					
					tpl_set('agents/stores/form', [
						'title' => ($id ? 'Edit' : 'Add').' store',
						'id' => $id,
						'send' => ($id ? 'edit' : 'add'),
						'name' => $u['name'],
						'address' => $u['address'],
						'phone' => $phones,
						'descr' => $u['descr'],
						'work-time-start' => $u['work_time_start'],
						'work-time-end' => $u['work_time_end']
					], [
						'edit' => $id,
						'deleted' => $u['del'],
					], 'content');
				break;
				
				case null:
					$meta['title'] = 'Partner stores';
					$query = text_filter($_POST['query'], 255, false);
					$page = intval($_POST['page']);
					$count = 10;
					if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_partner_stores` '.(
						$query ? 'WHERE name LIKE \'%'.$query.'%\' OR descr LIKE \'%'.$query.'%\' ' : ''
					).'ORDER BY `id` LIMIT '.($page*$count).', '.$count, true)){
						$i = 0;
						foreach($sql as $row){
							tpl_set('agents/stores/item', [
								'id' => $row['id'],
								'name' => $row['name'],
								'descr' => $row['descr'],
								'address' => $row['address'],
								'phone' => $row['phone']
							], [
								'deleted' => $row['del'],
							], 'stores');
							$i++;
						}
						
						// Get count
						$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					} else {
						tpl_set('noContent', [
							'text' => 'No stores found'
						], [], 'stores');
					}
					$left_count = intval(($res_count-($page*$count)-$i));
					if($_POST){
						exit(json_encode([
							'res_count' => $res_count,
							'left_count' => $left_count,
							'content' => $tpl_content['stores'],
						]));
					}
					tpl_set('agents/stores/main', [
						'uid' => $user['id'],
						'res_count' => $res_count,
						'more' => $left_count ? '' : ' hdn',
						'stores' => $tpl_content['stores']
					], [
					], 'content');
				break;
			}
		break;
		
		case 'appointments':
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 20;
			$aid = intval($route[2]);
			
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS 
					ap.id,
					ap.date,
					ap.note,
					u.id as uid,
					u.name,
					u.lastname,
					u.phone,
					a.id as aid,
					a.first_name,
					a.last_name,
					o.name as object
				FROM `'.DB_PREFIX.'_users_appointments` ap
				LEFT JOIN `'.DB_PREFIX.'_users` u 
					ON u.id = ap.customer_id
				LEFT JOIN `'.DB_PREFIX.'_agents` a
					ON a.id = ap.agent_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = ap.object_id
				WHERE ap.agent_id != 0 '.(
					$query ? ' AND CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' OR u.email LIKE \'%'.$query.'%\' OR REGEXP_REPLACE(u.phone, \' \', \'\') LIKE \'%'.$query.'%\' ' : ''
				).(
					$aid ? ' AND ap.aget_id = '.$aid : ''
				).($query ? '' : ' ORDER BY ap.id DESC').' LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('agents/appointments/item', [
						'id' => $row['id'],
						'date' => $row['date'],
						'note' => $row['note'],
						'uid' => $row['uid'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'phone' => $row['phone'],
						'object' => $row['object'],
						'aid' => $row['aid'],
						'first_name' => $row['first_name'],
						'last_name' => $row['last_name']
					], [
						'deleted' => $row['del'],
					], 'appointments');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'All appointments';
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['appointments'],
				]));
			}
			tpl_set('agents/appointments/main', [
				'uid' => $user['id'],
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'appointments' => $tpl_content['appointments'],
				'first_name' => $aid ? $sql[0]['first_name'] : '',
				'last_name' => $aid ? $sql[0]['last_name'] : '',
			], [
				'aid' => ($aid AND $sql[0]['first_name'])
			], 'content');
		break;
		
		case 'customers':
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 20;
			$aid = intval($route[2]);
			
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS 
					u.*,
					a.id as aid,
					a.first_name,
					a.last_name
				FROM `'.DB_PREFIX.'_users` u 
				LEFT JOIN `'.DB_PREFIX.'_agents` a
					ON a.id = u.agent_id
				WHERE u.agent_id != 0 '.(
					$query ? ' AND CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' OR u.email LIKE \'%'.$query.'%\' OR REGEXP_REPLACE(u.phone, \' \', \'\') LIKE \'%'.$query.'%\' ' : ''
				).(
					$aid ? ' AND u.agent_id = '.$aid : ''
				).($query ? '' : ' ORDER BY u.id DESC').' LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('agents/customers/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'phone' => $row['phone'],
						'reg_date' => $row['reg_date'],
						'aid' => $row['aid'],
						'first_name' => $row['first_name'],
						'last_name' => $row['last_name']
					], [
						'deleted' => $row['del'],
					], 'customers');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'All customers';
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['customers'],
				]));
			}
			tpl_set('agents/customers/main', [
				'uid' => $user['id'],
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'customers' => $tpl_content['customers'] ?: '<div class="noContent">No customers found</div>',
				'first_name' => $aid ? $sql[0]['first_name'] : '',
				'last_name' => $aid ? $sql[0]['last_name'] : '',
			], [
				'aid' => ($aid AND $sql[0]['first_name'])
			], 'content');
		break;
		
		case null:
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 20;
			
			if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_agents` WHERE 1 '.(
					$query ? ' AND CONCAT(`first_name`, \' \', `last_name`) LIKE \'%'.$query.'%\' OR email LIKE \'%'.$query.'%\' OR REGEXP_REPLACE(phone, \' \', \'\') LIKE \'%'.$query.'%\' ' : ''
				).($query ? '' : ' ORDER BY `id` DESC').' LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('agents/item', [
						'id' => $row['id'],
						'name' => $row['first_name'],
						'lastname' => $row['last_name'],
						'phone' => $row['phone'],
						'email' => $row['email'],
						'balance' => $row['balance'],
						'payment' => $row['payment'],
						'payment_account' => $row['payment_account']
					], [
						'payment' => ($row['payment'] AND $row['payment'] != 'none'),
						'deleted' => $row['del'],
					], 'agents');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = 'All agents';
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['agents'],
				]));
			}
			tpl_set('agents/main', [
				'uid' => $user['id'],
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'agents' => $tpl_content['agents']
			], [
			], 'content');
	
		break;
	}
?>