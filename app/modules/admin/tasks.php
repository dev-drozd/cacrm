<?php

defined('ENGINE') or ('hacking attempt!');
 
switch($route[1]){
	case 'complite':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		
		$task = db_multi_query('SELECT complited FROM `'.DB_PREFIX.'_tasks` WHERE id = '.$id);
		
		if ($task['complited'])
			die('complited');
		
		db_query('UPDATE `'.DB_PREFIX.'_tasks` SET complited = 1 WHERE id = '.$id);
		
		die('OK');
	break;
	
	case 'send':
		is_ajax() or die('Hacking attempt!');
		
		$object_id = intval($_POST['object_id']);
		$user_id = intval($_POST['user_id']);
		$type = intval($_POST['type']);
		$date = text_filter($_POST['date'], 12, false);
		$time = text_filter($_POST['time'], 12, false);
		$note = trim(text_filter($_POST['note']));
		
		//if (!$object_id AND !$user_id)
			//die('no_object_user');
		
		if ($type == 3 AND !$date)
			die('no_date');
		
		if ($type == 3 AND $date AND strtotime($date) < strtotime(date('Y-m-d')))
			die('date');
		
		if (!mb_strlen($note))
			die('no_note');
		
		db_query('INSERT INTO `'.DB_PREFIX.'_tasks` SET
			create_id = '.$user['id'].',
			create_date = \''.date('Y-m-d H:i:s').'\',
			object_id = '.$object_id.',
			user_id = '.$user_id.',
			type = '.$type.',
			'.($date ? 'date = \''.$date.'\',' : '').'
			'.($time ? 'time = \''.$time.'\',' : '').'
			note = \''.$note.'\',
			visible = '.(($type == 3 OR $type == 0) ? '1' : '0').'
		');
		
		die('OK');
	break;
	
	case null:
		$query = text_filter($_POST['query'], 255, false);
		$event = text_filter($_POST['event'], 50, false);
		$staff = intval($_POST['staff']);
		$create = intval($_POST['create']);
		$object = intval($_POST['object']);
		$date_start = text_filter($_POST['date_start'], 30, true);
		$date_finish = text_filter($_POST['date_finish'], 30, true);
		$page = intval($_POST['page']);
		$count = 20;
		

		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
			t.*, 
			u.id as uid, 
			u.name, 
			u.lastname, 
			c.id as cid, 
			c.name as cname, 
			c.lastname as clastname, 
			c.image as cimage,
			o.name as object
		FROM `'.DB_PREFIX.'_tasks` t
		LEFT JOIN `'.DB_PREFIX.'_users` u
			ON t.user_id = u.id 
		LEFT JOIN `'.DB_PREFIX.'_users` c
			ON t.create_id = c.id
		LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = t.object_id
		WHERE 1 '.(
			(in_array(4, explode(',', $user['group_ids'])) AND !in_array(1, explode(',', $user['group_ids']))) ? 
				' AND t.object_id = '.$user['store_id'].' AND (t.user_id = 0 OR t.user_id = '.$user['id'].') ' : ''
		).(
			$query ? 'AND t.note LIKE \'%'.$query.'%\' ' : ''
		).(
			$create ? 'AND t.create_id = '.$create.' ' : ''
		).(
			$staff ? 'AND t.user_id = '.$staff.' ' : ''
		).(
			$object ? 'AND t.object_id = '.$object.' ' : ''
		).(
			($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
		).'ORDER BY t.id DESC LIMIT '.($page*$count).', '.$count, true
		)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('tasks/item', [
					'id' => $row['id'],
					'uid' => $row['uid'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'cid' => $row['cid'],
					'cname' => $row['cname'],
					'clastname' => $row['clastname'],
					'cava' => $row['cimage'],
					'date' => $row['date'],
					'time' => $row['time'],
					'type' => $row['type'] == 3 ? 'time' : ($row['type'] == 2 ? 'Cash opened' : 'Cash closed'),
					'note' => $row['note'],
					'object' => $row['object'],
					'class' => $row['complited'] ? 'dicline_more' : 'dicline'
				], [
					'cava' => $row['cimage'],
					'time' => $row['type'] == 3,
					'object' => $row['object_id'],
					'user' => $row['user_id'],
					'both' => ($row['object_id'] && $row['user_id'])
				], 'tasks');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}

		$left_count = intval(($res_count-($page*$count)-$i));
		$meta['title'] = 'Tasks';
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['tasks'],
			]));
		}
		tpl_set('tasks/main', [
			'uid' => $user['id'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'tasks' => $tpl_content['tasks']
		], [], 'content');
	break;
}
?>