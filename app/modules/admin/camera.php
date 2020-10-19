<?php
/**
 * @appointment Camera admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

if ($user['camera']) {
	switch($route[1]){
		/*
		* Add activity
		*/
		case 'add_activity':
			is_ajax() or die('Hacking attempt!');
			
			$status_id = intval($_POST['status']);
			$staff_id = intval($_POST['staff']);
			$object_id = intval($_POST['object']);
			
			if (!intval($_POST['staff']))
				die('no_staff');
			if (!intval($_POST['object']))
				die('no_object');
			if (!text_filter($_POST['activity']))
				die('no_activity');
			if (!intval($_POST['status']))
				die('no_status');
			if (!(text_filter($_POST['date']).' '.text_filter($_POST['time'])))
				die('no_date');
			
			db_query('INSERT INTO `'.DB_PREFIX.'_activity` SET
				user_id = '.$staff_id.',
				'.(
					count(explode(',', ids_filter($_POST['staff']))) > 1 ? 'user_ids = \''.ids_filter($_POST['staff']).'\',' : ''
				).'
				camera_event = \''.text_filter($_POST['activity']).'\',
				object_id = '.$object_id.',
				date = \''.text_filter($_POST['date']).' '.text_filter($_POST['time']).'\',
				end_time = \''.text_filter($_POST['end_time']).'\',
				status_id = '.$status_id.',
				camera = 1
			');
			
			$st = db_multi_query('SELECT points FROM `'.DB_PREFIX.'_camera_status` WHERE id = '.$status_id);
			
			if ($st['points']) {
				foreach((count(explode(',', ids_filter($_POST['staff']))) > 1 ? explode(',', ids_filter($_POST['staff'])) : [$staff_id]) as $u) {
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$u.',
						action = \'camera_status\',
						min_rate = 0,
						object_id = '.$object_id.',
						status_id = '.$status_id.',
						point = \''.$st['points'].'\''
					);	
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$st['points'].'
						WHERE id = '.$u
					);
					if (floatval($st['points']) < 0) {
						if ($wt = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_timer` WHERE DATE(date) = \''.text_filter($_POST['date']).'\' AND user_id = '.$u)) {
							db_query('UPDATE `'.DB_PREFIX.'_timer` SET seconds = seconds + '.(floatval($st['points']) * $config['min_forfeit'] * 60).' WHERE id = '.$wt['id']);
							db_query('INSERT INTO `'.DB_PREFIX.'_users_time_forfeit` SET user_id = '.$u.', forfeit = '.(floatval($st['points']) * $config['min_forfeit']*60));
						}
					}
				}
			}
			
			die('OK');
		break;
		
		
		/* 
		* All statuses
		*/
		case 'allStatus':
			is_ajax() or die('Hacking attempt!');
			$statuses = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_camera_status` ORDER BY `id` DESC', true);
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			die(json_encode([
				'list' => $statuses,
				'count' => $res_count,
			]));
		break;
		
		/*
		* Statuses
		*/
		case 'statuses':
			$meta['title'] = $lang['Statuses'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					id, name, not_priority
				FROM `'.DB_PREFIX.'_camera_status` '.(
				$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY `sort` ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('camera/statuses/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'priority' => $row['not_priority'] ? ' checked' : ''
					], [
						'default' => $row['id'] != 11 AND $row['id'] != 2,
						'nnew' => $row['id'] != 11
					], 'status');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noStatuses']
				], [], 'status');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['status'],
				]));
			}
			tpl_set('camera/statuses/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'status' => $tpl_content['status']
			], [], 'content');
		break;
		
		/*
		*  Add/edit status
		*/
		case 'send_status':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if ((!$id AND $user['add_camera_status']) OR ($id AND $user['edit_camera_status'])) {
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_camera_status` SET
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
		*  Del status
		*/
		case 'del_status':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['delete_camera_status']){
				db_query('DELETE FROM `'.DB_PREFIX.'_camera_status` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('no_acc');
		break;
		
		/*
		* Status sort
		*/
		case 'st_priority':
			is_ajax() or die('Hacking attempt!');
			$i = 1;
			foreach($_POST as $row){
				db_query('UPDATE `'.DB_PREFIX.'_camera_status` SET sort = '.$i.', not_priority = '.(
					$row['pr'] ? 1 : '0'
				).' WHERE id = '.$row['id']);
					$i++;	
			}
			die;
		break;
	
		/*
		* Send comment
		*/
		case 'send_comment': 
			is_ajax() or die('Hacking attempt!');
			
			$id = intval($_POST['id']);
			$status_id = intval($_POST['status']);
			
			if (!intval($_POST['status']))
				die('no_status');
			
			$old_st = db_multi_query('SELECT user_id, status_id, object_id FROM `'.DB_PREFIX.'_activity` WHERE id = '.$id);
			
			db_query(
				'INSERT INTO `'.DB_PREFIX.'_camera_comments` SET 
					staff_id = '.$user['id'].',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					camera_id = '.intval($_POST['id']).',
					text = \''.text_filter($_POST['text'], 1000, false).'\''
			);
			
			if ($old_st['status_id'] != $status_id) {
				db_query(
					'UPDATE `'.DB_PREFIX.'_activity` SET 
						status_id = '.$status_id.'
					WHERE id = '.$id
				);
				
				$st = db_multi_query('SELECT points FROM `'.DB_PREFIX.'_camera_status` WHERE id = '.$status_id);
				
				if ($st['points']) {
					db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
						staff_id = '.$old_st['user_id'].',
						action = \'camera_status\',
						min_rate = 0,
						date = \''.date('Y-m-d H:i:s', time()).'\',
						object_id = '.$old_st['object_id'].',
						status_id = '.$status_id.',
						point = \''.$st['points'].'\''
					);	
					db_query(
						'UPDATE `'.DB_PREFIX.'_users`
							SET points = points+'.$st['points'].'
						WHERE id = '.$old_st['user_id']
					);
				}
			}
			
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
					u.image,
					a.status_id
				FROM `'.DB_PREFIX.'_camera_comments` c
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = c.staff_id
				LEFT JOIN `'.DB_PREFIX.'_activity` a
					ON a.id = c.camera_id
				WHERE c.camera_id = '.$id.'
				ORDER BY c.id ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row) {
					tpl_set('camera/comment_item', [
						'id' => $row['id'],
						'date' => $row['date'],
						'text' => $row['text'],
						'user_id' => $row['staff_id'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'status-id' => $row['status_id']
					], [
						'image' => $row['image']
					], 'list');
					$i++;
				}
				
				// Get count
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				$left_count = intval(($res_count-($page*$count)-$i));
			}
			
			$st_list = '<option value="0">'.$lang['noStatus'].'</option>';
			if (!$page) {
				if ($statuses = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_camera_status` ORDER BY sort', true)) {
					foreach($statuses as $st) {
						$st_list .= '<option value="'.$st['id'].'"'.($st['id'] == $row['status_id'] ? ' selected' : '').'>'.$st['name'].'</option>';
					}
				}
			}
				
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['list'],
				'more' => $left_count ? '' : ' hdn',
				'statuses' => $st_list
			]));
		break;
		
		case 'main':
			$date = explode('/', $config['camera_period']);
			echo json_encode(db_multi_query('
				SELECT a.id, a.camera_event, a.user_id, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_activity` a LEFT JOIN `'.DB_PREFIX.'_users` u ON a.user_id = u.id WHERE a.camera > 0 AND camera_event != \'\' AND a.camera_event != \'.\' AND date(a.date) >= date(\''.trim($date[0]).'\') AND date(a.date) <= date(\''.trim($date[1]).'\') ORDER BY a.date DESC LIMIT 0, 50
			', 1));
			die;
		break;
		
		/*
		* All activity
		*/
		case 'updated':
		default:
			$query = text_filter($_POST['query'], 255, false);
			$event = text_filter($_POST['event'], 50, false);
			$staff = intval($_POST['staff']);
			$object = intval($_POST['object']);
			$updated = $_POST['updated'] ? intval($_POST['updated']) : ($route[1] == 'updated' ? 1 : 0);
			$date_start = text_filter($_POST['date_start'], 30, true);
			$date_finish = text_filter($_POST['date_finish'], 30, true);
			$page = intval($_POST['page']);
			$count = 20;

			if($sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
					a.*, 
					u.id as uid, 
					u.name, 
					u.lastname, 
					u.image, 
					t.seconds as hours, 
					o.name as object, 
					s.name as status_name,
					tu.id as tuid, 
					tu.name as tr_name, 
					tu.lastname as tr_lastname,
					ts.name as to_name
				FROM `'.DB_PREFIX.'_activity` a
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON a.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_users` tu
					ON a.tr_staff = tu.id
				LEFT JOIN `'.DB_PREFIX.'_camera_comments` c
					ON c.camera_id = a.id
				LEFT JOIN `'.DB_PREFIX.'_camera_status` s
					ON s.id = a.status_id
				LEFT JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = a.user_id AND t.date >= CURDATE()
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON a.object_id = o.id
				LEFT JOIN `'.DB_PREFIX.'_objects` ts
					ON a.tr_store = ts.id
				WHERE IF(a.camera, 1, u.id) '.(
				$event ? ' AND a.event = \''.$event.'\'' : ''
			).(
				(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
					' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($objects_ip[$user['oip']] ?: 0).' OR u.id = '.$user['id'].'))' : ''
			).(
				$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				$staff ? 'AND a.user_id = '.$staff.' ' : ''
			).(
				$object ? 'AND a.object_id = '.$object.' ' : ''
			).(
				$updated ? 'AND (a.camera = 1 OR a.status_id != 0 OR c.id > 0)  ' : ''
			).(
				($date_start AND $date_finish) ? ' AND a.date >= CAST(\''.$date_start.'\' AS DATE) AND a.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'ORDER BY a.date DESC LIMIT '.($page*$count).', '.$count, true
			)){
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				$user_ids = implode(',', array_filter(array_column($sql, 'user_ids')));
				if ($user_ids) {
					$mstaff = db_multi_query('
						SELECT 
							id, 
							CONCAT(name, \' \', lastname) as name, 
							image 
						FROM `'.DB_PREFIX.'_users` 
						WHERE id IN ('.$user_ids.')', true);
				} else
					$mstaff = [];
				
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
						
						case 'new transfer':
							$event = '<a href="/inventory/transfer/view/'.$row['event_id'].'" target="_blank">'.$lang['newTransfer'].'</a> '.$lang['toStore'].' '.$row['to_name'].'
								(Responsible person: <a href="/users/view/'.$row['tuid'].'" onclick="Page.get(this.href); return false;">'.$row['tr_name'].' '.$row['tr_lastname'].'</a>)';
						break;
						
						case 'confirm transfer':
						case 'confirm transfer request':
							$event = '<a href="/inventory/transfer/view/'.$row['event_id'].'" target="_blank">'.$lang['confirmTransfer'.(
								$row['event'] == 'confirm transfer' ? '' : 'Request'
							)].'</a>';
						break;
						
						default:
							$event = $row['camera'] ? $row['camera_event'] : ($lang[$row['event']] ?: $row['event']);
						break;
					}
					
					$users = '';
					if ($row['user_ids'] AND count($mstaff)) {
						if ($usrs = array_filter($mstaff, function($a) use(&$row) {
							if (in_array($a['id'], explode(',', $row['user_ids'])))
								return $a;
						})) {
							foreach($usrs as $usr) {
								if ($users) $users .= '<br>';
								$users .= '<a href="/users/view/'.$usr['id'].'" onclick="Page.get(this.href); return false;">
										'.(
											$usr['image'] ? '<img src="/uploads/images/users/'.$usr['id'].'/'.$usr['image'].'" class="miniRound">' : '<span class="fa fa-user-secret miniRound"></span>'
										).'
										'.$usr['name'].'
									</a>';
							}
						}
						
					}
						
					tpl_set('camera/item', [
						'id' => $row['id'],
						'uid' => $row['uid'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'ava' => $row['image'],
						'date' => $row['date'].((strtotime($row['end_time']) - strtotime('TODAY')) ? ' - '.$row['end_time'] : ''),
						'event' => ucfirst($event),
						'class' => $class,
						'object' => $row['object'],
						'status' => $row['status_name'],
						'users' => $users
					], [
						'ava' => $row['image'],
						'uid' => $row['uid'],
						'del' => $user['delete_camera_activity'],
						'users' => ($row['user_ids'] AND $users)
					], 'activity');
					$i++;
				}
			}
			
			$events = '';
			if ($action = db_multi_query('SELECT DISTINCT event FROM `'.DB_PREFIX.'_activity` WHERE camera = 0 AND event != \'\'', true)) {
				foreach($action as $act) {
					$events .= '<option value="'.$act['event'].'">'.ucfirst(str_replace('_', ' ', $act['event'])).'</option>';
				}
			}

			$left_count = intval(($res_count-($page*$count)-$i));
			$meta['title'] = $lang['Camera'];
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['activity'],
				]));
			}
			tpl_set('camera/main', [
				'uid' => $user['id'],
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'activity' => $tpl_content['activity'],
				'events' => $events
			], [
				'updated' => $route[1] == 'updated'
			], 'content');
		break;
	}
}
?>