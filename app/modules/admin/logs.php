<?php
/**
 * @appointment Logs admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$objects_ip = array_flip($config['object_ips']);
 
switch($route[1]){
	
	case 'sql_low':
		$page = intval($_POST['page']);
		$count = 20;
		if($sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS s.*, u.id as uid, u.name, u.lastname, u.image FROM
		`'.DB_PREFIX.'_slow_sql_queries` s
			INNER JOIN `'.DB_PREFIX.'_users` u
				ON s.user_id = u.id 
			WHERE status > 0 ORDER BY s.time DESC LIMIT '.($page*$count).', '.$count, true
		)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('logs/sql_low_item', [
					'id' => $row['id'],
					'uid' => $row['uid'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'ava' => $row['image'],
					'date' => $row['date'],
					'time' => number_format($row['time'], 2, '.', ''),
					'url' => $row['url'],
					'style' => $row['status'] == 1 ? 'ff00001f' : ($row['status'] == 2 ? 'fffef0' : '0080001f'),
					'query' => str_ireplace(
						['&lt;?php','?&gt;'],'', highlight_string("<?php\n".trim($row['query'])."\n?>",true)
					)
				], [
					'ava' => $row['image']
				], 'logs');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			$left_count = intval(($res_count-($page*$count)-$i));
		}
		$meta['title'] = 'SQL low';
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['logs'],
			]));
		}
		tpl_set('logs/sql_low_main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'logs' => $tpl_content['logs']
		], [], 'content');
	break;
	
	default:
		$query = text_filter($_POST['query'], 255, false);
		$event = text_filter($_POST['event'], 50, false);
		$staff = intval($_POST['staff']);
		$object = intval($_POST['object']);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$page = intval($_POST['page']);
		$count = 20;
		

		if($sql = db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS a.*, u.id as uid, u.name, u.lastname, u.image, t.seconds as hours FROM
		`'.DB_PREFIX.'_activity` a
			INNER JOIN `'.DB_PREFIX.'_users` u
				ON a.user_id = u.id 
			LEFT JOIN `'.DB_PREFIX.'_timer` t
				ON t.user_id = a.user_id AND t.date >= CURDATE()
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON FIND_IN_SET(a.user_id, o.managers) OR FIND_IN_SET(a.user_id, o.staff)
			WHERE camera = 0 '.(
			$event ? ' AND a.event = \''.$event.'\' ' : ''
		).(
			(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
				' AND (FIND_IN_SET('.$user['id'].', o.staff) OR FIND_IN_SET('.$user['id'].', o.managers) OR (o.id = '.($objects_ip[$user['oip']] ?: 0).' OR u.id = '.$user['id'].'))' : ''
		).(
			$query ? 'AND MATCH(u.name, u.lastname, u.email) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
		).(
			$staff ? 'AND a.user_id = '.$staff.' ' : ''
		).(
			$object ? 'AND a.object_id = '.$object.' ' : ''
		).(
			($date_start AND $date_finish) ? ' AND a.date >= CAST(\''.$date_start.'\' AS DATE) AND a.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
		).'ORDER BY a.id DESC LIMIT '.($page*$count).', '.$count, true
		)){

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
						$event = $lang['addNew'].' <a href="/purchases/edit/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">'.$lang['purchase'].'</a>';
					break;
					
					case 'new_invoice':
						$event = 'Create new invoice: <a href="/invoices/view/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
					break;
					
					case 'new_job':
						$event = 'Create new job: <a href="/issues/view/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
					break;
					
					case 'remove_job':
						$event = 'Remove job: <a href="/issues/view/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
						$class = 'stop';
					break;
					
					case 'replied_to_chat':
						$event = 'Replied to chat: <a href="/im/support/'.$row['event_id'].'" onclick="Page.get(this.href); return false;">#'.$row['event_id'].'</a>';
					break;
					
				}
				tpl_set('logs/item', [
					'id' => $row['id'],
					'uid' => $row['uid'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'ava' => $row['image'],
					'date' => $row['date'],
					'event' => $event,
					'class' => $class
				], [
					'ava' => $row['image']
				], 'logs');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}

		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = $lang['All'].' logs';
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['logs'],
			]));
		}
		tpl_set('logs/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'logs' => $tpl_content['logs']
		], [], 'content');
}