<?php
/**
 * @appointment Timer admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        https://yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
switch($route[1]){
	
	case 'confirm':
		is_ajax() or die('Hacking attempt!');
		$id = (int)$_POST['id'];
		if($id && $user['confirm_working_time']){
			db_query('UPDATE `'.DB_PREFIX.'_timer` SET confirm = '.$user['id'].' WHERE id = '.$id);
			echo 'OK';
		} else
			echo 'PERMISSION';
		die;
	break;
	
	/*
	* Week
	*/
	case 'week':
		is_ajax() or die('Hacking attempt!');
		
		$date = new DateTime();
		$date->modify('last Monday');
		$start = $date->format('Y-m-d');
		$date->modify('+6 days');
		$end = $date->format('Y-m-d');
		
		$timer = '';
		
		foreach(db_multi_query('
			SELECT DISTINCT
				event,
				SEC_TO_TIME(seconds) as seconds,
				DAYNAME(date) as week,
				TIME(date) as start,
				TIME(break_start) as break_start,
				TIME(break_finish) as break_finish,
				TIME(control_point) as end
			FROM `'.DB_PREFIX.'_timer` 
			WHERE user_id = '.$user['id'].'
				AND date >= CAST(\''.$start.'\' AS DATE) AND date <= CAST(\''.$end.' 23:59:59\' AS DATETIME)
			ORDER BY date LIMIT 0, 7',
		true) as $item){
			$timer .= '<tr>
					<td>'.$item['week'].'</td>
					<td>'.$item['start'].'</td>
					<td>'.$item['break_start'].'</td>
					<td>'.$item['break_finish'].'</td>
					<td>'.($item['event'] == 'stop' ? $item['end'] : '').'</td>
					<td>'.$item['seconds'].'</td>
				</tr>';
		}

		print '
			<table>
				<tr>
					<th>Day</th>
					<th>Time in</th>
					<th>Break start</th>
					<th>Break end</th>
					<th>Time out</th>
					<th>Working time</th>
				</tr>
				'.$timer.'
			</table>
		';
		die;
	break;
	
	/*
	* Create time
	*/
	case 'create_time': 
		is_ajax() or die('Hacking attempt!');
		if(in_to_array('1,2', $user['group_ids'])){
			$id = (int)$_POST['id'];
			if($id AND preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $_POST['time'])){
				$field = $_POST['type'] == 'start' ? 'date' : 'control_point';
				db_query(
					'UPDATE `'.DB_PREFIX.'_timer`
						SET '.$field.' = DATE_FORMAT('.$field.', \'%Y-%m-%d '.$_POST['time'].':00\'), seconds = (
							UNIX_TIMESTAMP(control_point)-UNIX_TIMESTAMP(date)
						)
					WHERE id = '.$id
				);
				echo 'OK';
			} else
				die('ERR_TIME');
		}
		die;
	break;
	
	/*
	* Time round
	*/
	case 'getTime':
		is_ajax() or die('Hacking attempt!');
		
		$sec = 300;
		$time = time();
		$div = $time % $sec;
		$event = text_filter($_POST['event'], 10);
		$time_sec = date('H', time()) * 3600 + date('i', time()) * 60 + date('s', time());
		$start_time = 0;
		$end_time = 0;
		$start_time_date = '';
		
		/* if ($user['id'] == 17)
			$user['store_id'] = 2; */
		
		if ($_POST['side'] > 0) {
			if ($event == 'start' AND (!in_array(
				1, explode(',', $user['group_ids'])
			) AND !in_array(
				2, explode(',', $user['group_ids'])
			))) {
				
				$org = db_multi_query('
					SELECT 
						*,
						TIME_TO_SEC(REGEXP_REPLACE(time, \'{(.*?)"'.date('w', time()).'":{"start":"(.*?)","end":"(.*?)","object":"'.$user['store_id'].'"(.*?)}(.*?)}\', \'\\\2\')) as start_time,
						REGEXP_REPLACE(time, \'{(.*?)"'.date('w', time()).'":{"start":"(.*?)",end:"(.*?)","object":"'.$user['store_id'].'"(.*?)}(.*?)}\', \'\\\2\') as start_time_date,
						TIME_TO_SEC(REGEXP_REPLACE(time, \'{(.*?)"'.date('w', time()).'":{"start":"(.*?)","end":"(.*?)","object":"'.$user['store_id'].'"(.*?)}(.*?)}\', \'\\\3\')) as end_time
					FROM `'.DB_PREFIX.'_organizer` 
					WHERE staff = '.$user['id'].' AND date_start <= \''.date('Y-m-d', time()).'\' AND IF (date_end, date_end >= \''.date('Y-m-d', time()).'\', 1)
					AND time REGEXP \'{(.*?)"'.date('w', time()).'":{(.*?),"object":"'.$user['store_id'].'"(.*?)}(.*?)}\'
				');

				$arr = json_decode($org['time'], true);
				
				$start_time_date = $arr[date('w', time())]['start'];
				$start_time = explode(':', $arr[date('w', time())]['start']);
				$start_time = $start_time[0] * 3600 + $start_time[1] * 60;
				
				$end_time = explode(':', $arr[date('w', time())]['end']);
				$end_time = $end_time[0] * 3600 + $end_time[1] * 60;


				
				if (!$org){
					//die('You are not scheduled to punch in, please contact to your manager');
					$time = $time - $div + $sec;
				} else if ($start_time > $time_sec OR ($end_time ? $end_time < $time_sec : 0))
					die('Your working time begins at '.$start_time_date);
				else {
					if (abs($start_time - $time_sec) > 600)
						$time = $time - $div + $sec;
				}
				if ($org && abs($org['start_time'] - $time_sec) <= $sec)
					$time = time();
			} else {
				$time = $time - $div + $sec;
			}
		} else 
			$time = $time - $div;
		echo date('h:i', $time); 
		die;		
	break;
	
    /*
	*  Send timer
	*/
    default:
		is_ajax() or die('Hacking attempt!');
		
		$sec = 300;
		
		$event = text_filter($_POST['a'], 10, true);
		$id = intval($_POST['id']);
		$new = intval($_POST['id']);
		$time = time();
		$div = $time % $sec;
		$sql = [];
		$time_sec = date('H', time()) * 3600 + date('i', time()) * 60 + date('s', time());
		$start_time = 0;
		$end_time = 0;
		$start_time_date = '';
		
		/* if ($user['id'] == 17)
			$user['store_id'] = 2; */
					
		if($user['stores_check_ip'] AND !in_array(
			1, explode(',', $user['group_ids'])
		) AND !in_array(
			$_SERVER['REMOTE_ADDR'], $config['object_ips']
		)){
			die('IP');
		}
		
		if ($event == 'stop' AND !$id)
			die('You can not punch out before punch in');
		
		if ($event == 'start' AND !in_array(
			1, explode(',', $user['group_ids'])
		) AND !in_array(
			2, explode(',', $user['group_ids'])
		)) {
			$org = db_multi_query('
				SELECT 
					*,
					TIME_TO_SEC(REGEXP_REPLACE(time, \'{(.*?)"'.date('w', time()).'":{"start":"(.*?)",(.*?),"object":"'.$user['store_id'].'"(.*?)}(.*?)}\', \'\\\2\')) as start_time,
					REGEXP_REPLACE(time, \'{(.*?)"'.date('w', time()).'":{"start":"(.*?)",(.*?),"object":"'.$user['store_id'].'"(.*?)}(.*?)}\', \'\\\2\') as start_time_date,
					TIME_TO_SEC(REGEXP_REPLACE(time, \'{(.*?)"'.date('w', time()).'":{(.*?),"end":"(.*?)","object":"'.$user['store_id'].'"(.*?)}(.*?)}\', \'\\\3\')) as end_time
				FROM `'.DB_PREFIX.'_organizer` 
				WHERE staff = '.$user['id'].' AND date_start <= \''.date('Y-m-d', time()).'\' AND IF (date_end, date_end >= \''.date('Y-m-d', time()).'\', 1)
				AND time REGEXP \'{(.*?)"'.date('w', time()).'":{(.*?),"object":"'.$user['store_id'].'"(.*?)}(.*?)}\'
			');
			
			$arr = json_decode($org['time'], true);
				
			$start_time_date = $arr[date('w', time())]['start'];
			$start_time = explode(':', $arr[date('w', time())]['start']);
			$start_time = $start_time[0] * 3600 + $start_time[1] * 60;
			
			$end_time = explode(':', $arr[date('w', time())]['end']);
			$end_time = $end_time[0] * 3600 + $end_time[1] * 60;
				
			if (!$org){
				//die('You are not scheduled to punch in, please contact to your manager');
				$confirm = 0;
			} else if ($start_time > $time_sec OR ($end_time ? $end_time < $time_sec : 0))
				die('Your working time begins at '.$start_time_date);
		}
		
		$time = $event == 'start' ? $time - $div + $sec : $time - $div;
		
		
		if ($org && $event == 'start' AND abs($org['start_time'] - $time_sec) <= $sec)
			$time = time();
		
		if ($id){
			$sql = db_multi_query('
				SELECT * FROM `'.DB_PREFIX.'_timer`
				WHERE id = '.$id
			);
			
			$interval = ($time - strtotime(($sql['control_point']))) <= 0 ? 0 : $time - strtotime(($sql['control_point']));
			
			if ($sql['seconds'] > 0 AND $event == 'pause') 
				die('You can not use the pause twice a day');
			else if ($sql['event'] == 'stop')
				die('You can not start working time twice a day');
		} else {
			$sql = db_multi_query('
				SELECT * FROM `'.DB_PREFIX.'_timer`
				WHERE DATE(date) = CURDATE() AND user_id = '.$user['id']
			);
			if ($sql['id'] > 0) {
				die('You can not start working time twice a day');
			}
		}

		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_timer` SET 
			user_id = '.$user['id'].',
			'.(isset($confirm) ? ' confirm = 0,' : '').'
			event = \''.$event.'\''.(
			$event != 'stop' ? ($event == 'pause' ? ', break_start = \''.date('Y-m-d H:i:s', $time).'\'' : (
				$id ? ', break_finish = \''.date('Y-m-d H:i:s', $time).'\'' : ''
			)) : ''
		).', control_point = \''.date('Y-m-d H:i:s', $time).'\''.(
			$event == 'start' ? ', date = \''.date('Y-m-d H:i:s', $time).'\', object_id = '.$user['store_id'] : ''
		).(
			$event == 'pause' ? ', seconds = '.$interval : (
				($event == 'stop' AND $sql['event'] != 'pause') ? (', seconds = '.(($sql['seconds'] ?: 0) + $interval)) : ''
			)
		).(
			$id ? ' WHERE id = '.$id : ''
		));
		
		$id = $id ?: intval(
			mysqli_insert_id($db_link)
		);
		
		db_query('
			INSERT INTO 
			`'.DB_PREFIX.'_activity` SET 
				user_id = \''.$user['id'].'\', 
				event = \''.$event.' working time\',
				date = \''.date('Y-m-d H:i:s', time()).'\',
				object_id = '.$user['store_id'].'
		');
		
		if (!$new AND $user['store_id'] > 0 AND $event == 'start'){
			$points = floatval($config['user_points']['punch_in']['points']);
			db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
				staff_id = '.$user['id'].',
				action = \'punch_in\',
				object_id = '.$user['store_id'].',
				point = \''.$points.'\''
			);
			db_query(
				'UPDATE `'.DB_PREFIX.'_users`
					SET points = points+'.$points.'
				WHERE id = '.$user['id']
			);
		}
		
		
		send_push(0, [
			'type' => 'activity',
			'html' => '<div class="tr '.$event.'">
				<div class="td lh45">
					<a href="/users/view/'.$user['id'].'" target="_blank">
						'.(
							$user['image'] ?
								'<img src="/uploads/images/users/'.$user['id'].'/thumb_'.$user['image'].'" class="miniRound">' :
							'<span class="fa fa-user-secret miniRound"></span>'
						).'
						'.$user['uname'].' '.$user['ulastname'].'
					</a>
				</div>
				<div class="td">'.ucfirst($event).' working time</div>
				<div class="td">'.$user['object_name'].'</div>
				<div class="td">'.date("Y-m-d H:i:s").'</div>
			</div>'
		]);
		
		if($user['id'] == 17) $user['store_id'] = 10;
		
		$acash = 0;
		
		if (!$new) {
			$cash = db_multi_query('SELECT 
				id 
			FROM `'.DB_PREFIX.'_cash` 
			WHERE action = \'open\' AND DATE(date) = CURDATE() AND object_id = '.$user['store_id']);
			if (!$cash['id'])
				$acash = 1;
		} elseif ($event = 'stop') {
			$cash = db_multi_query('SELECT 
				c.id
			FROM `'.DB_PREFIX.'_cash` c
			WHERE c.action = \'close\' AND DATE(c.date) = CURDATE() AND c.object_id = '.$user['store_id']);
			
			$timer = db_multi_query('SELECT COUNT(t.id) as count FROM `'.DB_PREFIX.'_timer` t WHERE DATE(t.date) = CURDATE() AND t.event != \'stop\' AND t.object_id = '.$user['store_id']);
			if (!$cash AND !$timer['count'])
				$acash = 1;
		}
		
		if ($acash == 1 AND $new)
			db_query('INSERT INTO `'.DB_PREFIX.'_cash_closing` SET staff_id = '.$user['id'].', object_id = '.$user['store_id']);
		
		print_r(json_encode([
			'id' => $id,
			'cash' => $acash
		]));
		die;
    break;
}

?>