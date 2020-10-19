<?php
/**
 * @appointment Organizer admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
 if($user['organizer'] > 0){
	 switch($route[1]){
		/*
		* Shedule swap
		*/
		case 'swap':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$staff = intval($_POST['staff']);
			$date = text_filter($_POST['date'], 10, false);
			$time_start = text_filter($_POST['time_start'], 5, false);
			$time_end = text_filter($_POST['time_end'], 5, false);
			
			if (!$staff OR !$date OR !$time_start OR !$time_end)
				die('no_info');
			
			if ($org = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_organizer` WHERE id = '.$id)) {
			
				$w = date('w', strtotime($date));
				$objs = json_decode($org['time'], true);
				$time = (strtotime($time_end) - strtotime($time_start));
				
				// staff hours limit
				$week_hours = $time;
				if ($exist = db_multi_query('
					SELECT 
						SUM(staff_time) as staff_time
					FROM `'.DB_PREFIX.'_organizer` 
					WHERE IF(date_end, date_end >= \''.$date.'\', 1) AND IF(\''.$date.'\', date_start <= \''.$date.'\', 1) AND staff = '.$staff
				)) {
					$week_hours += $exist['staff_time'];
				}
				
				$user_info = db_multi_query('
					SELECT 
						group_ids
					FROM `'.DB_PREFIX.'_users` 
					WHERE id = '.$staff
				);
				$gr = min(explode(',', $user_info['group_ids']));
				
				$group_info = db_multi_query('
					SELECT 
						week_hours
					FROM `'.DB_PREFIX.'_groups` 
					WHERE group_id = '.$gr
				);

				if (floatval($group_info['week_hours']) > 0 AND floatval($group_info['week_hours']) < ($week_hours - (strtotime($org['time'][$w]['end']) - strtotime($org['time'][$w]['start'])))/3600)
					die('no_hours');
				
				// store hours limit
				$objects = db_multi_query('
					SELECT 
						SUM(REGEXP_REPLACE(object_time, \'{(.*?)("2":([0-9.]+))(.*?)}\', \'\\\3\')) as store_'.$objs[$w]['object'].'
					FROM `'.DB_PREFIX.'_organizer` 
					WHERE (FIND_IN_SET('.$objs[$w]['object'].', REGEXP_REPLACE(REGEXP_REPLACE(object_time, \'(?:"([0-9]+)":([0-9]+)(,?))\', \'\\\1\\\3\'), \'([{}])\', \'\'))) AND IF(date_end, date_end >= \''.$date.'\', 1) AND IF(\''.$date.'\', date_start <= \''.$date.'\', 1)
				');
			
			
				
				if ($object_hours = db_multi_query('
					SELECT 
						id,
						name,
						week_hours
					FROM `'.DB_PREFIX.'_objects` 
					WHERE id = '.$objs[$w]['object']
				)) {
					if ($object_hours['week_hours'] AND $object_hours['week_hours'] < ($objects['store_'.$object_hours['id']] - ($object_hours['id'] == $org['time'][$w]['object'] ? (strtotime($org['time'][$w]['end']) - strtotime($org['time'][$w]['start'])) : 0) + $time) / 3600)
						die('store_hours_'.$object_hours['name']);
				}
			
				db_query('UPDATE `'.DB_PREFIX.'_organizer` SET swap = CONCAT(swap, \''.$date.',\') WHERE id = '.$id);
				
				db_query('INSERT INTO `'.DB_PREFIX.'_organizer` SET
					staff = \''.$staff.'\',
					date_start = \''.$date.'\',
					date_end = \''.$date.'\',
					time = \''.text_filter(json_encode([
						$w => [
							'start' => $time_start,
							'end' => $time_end,
							'object' => $objs[$w]['object']
						]
					], JSON_FORCE_OBJECT), null, true).'\',
					staff_time = '.$time.', 
					object_time = \''.json_encode([
						$objs[$w]['object'] => $time
					], JSON_FORCE_OBJECT).'\''
				);
				
				die('OK');
			}
			die;
		break;
		
		/*
		* Days
		*/
		case 'days':
			is_ajax() or die('Hacking attempt!');
			$week = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
			$id = intval($_POST['id']);
			$month = intval($_POST['month']);
			$year = intval($_POST['year']);
			$object = intval($_POST['object']);
			$group = intval($_POST['group']);
			$count = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$date_start = text_filter($_POST['date_start'], null, false);
			$date_end = text_filter($_POST['date_end'], null, false);
			$month_week = [];
			
			$objects = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects` ORDER BY id', true);
			$ctnr = '<div class="tbl">
						<div class="tr">
							<div class="th">Store</div>';
			foreach($week as $a) {
				$ctnr .= '<div class="th">'.$a.'</div>';
			}
			$ctnr .= '</div>';
			
			$info = db_multi_query('SELECT o.*, u.name, u.lastname, u.image, s.name as store_name
									FROM `'.DB_PREFIX.'_organizer` o
									LEFT JOIN `'.DB_PREFIX.'_users` u
										ON u.id = o.staff
									LEFT JOIN `'.DB_PREFIX.'_objects` s
										ON s.id IN(REGEXP_REPLACE(o.time, \'(.*?){(.*?),"object":"(.*?)"}(.*?).?\', \'\\\3,\'))
									WHERE o.date_start <= \''.$year.'-'.(
											$month < 10 ? '0'.$month : $month
										).'-'.$count.'\' 
										AND IF(o.date_end, o.date_end >= \''.$year.'-'.(
											$month < 10 ? '0'.$month : $month
										).'-1\', 1)'
									.(
										$group ? ' AND FIND_IN_SET('.$group.', u.group_ids)' : ''
									).(
										$object ? ' AND s.id = '.$object : ''
									), true);
			$org = [];
			for($i = 0; $i <= $count; $i++) {
				$dow = jddayofweek (cal_to_jd(CAL_GREGORIAN, $month, $i, $year));
				$org[$i] = array_filter($info, function($v) use(&$i, &$month, &$year, &$dow) {
					if (strtotime($v['date_start']) <= strtotime($year.'-'.$month.'-'.$i) AND ($v['date_end'] !== '0000-00-00' ? strtotime($v['date_end']) >= strtotime($year.'-'.$month.'-'.$i) : 1) AND json_decode($v['time'], true)[$dow] AND !in_array($year.'-'.$month.'-'.$i, explode(',', $v['swap']))) {
						return $v;
					}
				});
				if ($i)
					$month_week[$i] = $dow;
			}
			
			$info2 = db_multi_query('SELECT o.*, u.name, u.lastname, u.image, s.name as store
									FROM `'.DB_PREFIX.'_organizer` o
									LEFT JOIN `'.DB_PREFIX.'_users` u
										ON u.id = o.staff
									LEFT JOIN `'.DB_PREFIX.'_objects` s
										ON s.id IN(REGEXP_REPLACE(o.time, \'(.*?){(.*?),"object":"(.*?)"}(.*?).?\', \'\\\3,\'))
									WHERE '.(
										($date_start AND $date_end) ? 'o.date_start <= \''.$date_end.'\' 
										AND IF(o.date_end, o.date_end >= \''.$date_start.'\', 1)' : 'o.date_start <= \''.$year.'-'.(
											$month < 10 ? '0'.$month : $month
										).'-'.$count.'\' 
										AND IF(o.date_end, o.date_end >= \''.$year.'-'.(
											$month < 10 ? '0'.$month : $month
										).'-1\', 1)'
									).(
										$group ? ' AND FIND_IN_SET('.$group.', u.group_ids)' : ''
									).(
										$object ? ' AND s.id = '.$object : ''
									), true);
								
			$result = [];
			if ($info2) {
				foreach($info2 as $s) {
					if ($s['time']) {
						foreach(json_decode($s['time'], true) as $i => $t) {
							if ($t['object']) {
								if (!$result[$t['object']]) $result[$t['object']] == [];
								if (!$result[$t['object']][$i]) $result[$t['object']][$i] == [];
								$result[$t['object']][$i][] = [
									'user_id' => $s['staff'],
									'user_name' => $s['name'].' '.$s['lastname'],
									's_name' => substr($s['name'], 0, 1).'.'.$s['lastname'],
									'start' => $t['start'],
									'end' => $t['end'],
									'date_start' => $s['date_start'],
									'date_end' => $s['date_end']
								];
							}
						}
					}
				}
			}
			
			$sort_objects = array_column($objects, 'name', 'id');
			foreach($result as $o => $wDay) {
				$users = [];
				$week_html = '';
						
				foreach($week as $k => $w) {
					$week_html .= '<div class="td">';
					if ($wDay[$k]) {
						foreach($wDay[$k] as $oUsr) {
							$working_days = '';
							$i = 0;
							foreach(array_keys(array_filter($month_week, function($a, $key) use(&$k, &$oUsr, &$year, &$month, &$date_start, &$date_end) {
								if ($a == $k AND strtotime($date_start ?: $oUsr['date_start']) <= strtotime($year.'-'.$month.'-'.$key) AND (strtotime($date_end ?: $oUsr['date_end']) >= 1 ? strtotime($date_end ?: $oUsr['date_end']) >= strtotime($year.'-'.$month.'-'.$key) : 1))
									return $key;
							}, ARRAY_FILTER_USE_BOTH)) as $day) {
								if ($working_days)
									$working_days .= '<br>';
								$working_days .= $year.'-'.(
									$day < 10 ? '0'.$day : $day
								).'-'.(
									$month < 10 ? '0'.$month : $month
								);
								$i ++;
							}
							$time = (strtotime($oUsr['end']) - strtotime($oUsr['start'])) / 3600;

							if (!$users[$oUsr['user_id']]) {
								$users[$oUsr['user_id']] = [
									'time' => 0,
									'name' => $oUsr['s_name']
								];
							}
							$users[$oUsr['user_id']]['time'] += $time*$i;
							$week_html .= '<div class="suOrg"><a href="/users/view/'.$oUsr['user_id'].'" onclick="Page.get(this.href); return false;">'.$oUsr['user_name'].'</a>
								<br><b>'.$oUsr['start'].' - '.$oUsr['end'].'</b><br>
								'.$working_days.'
								</div>';
						}
					}
					$week_html .= '</div>';
				}
				$users_time = '';
				if ($users) {
					foreach($users as $u) {
						$users_time .= ($users_time ? '<br>' : '').$u['name'].' - '.floor($u['time']).((($u['time'] - floor($u['time'])) * 60) ? ':'.(($u['time'] - floor($u['time'])) * 60) : '').'h';
					}
				}
				$ctnr .= '<div class="tr">
						<div class="td"><b>'.$sort_objects[$o].'</b><br>'.$users_time.'</div>'.$week_html.'</div>';
			}
			
			
			print_r(json_encode([
				'days' => $org,
				'table' => $ctnr,
				'objects' => array_column($objects, 'name', 'id'),
				'date_filter' => ($date_start AND $date_end),
				'is_admin' => $user['organizer_add'] == 1
			]));
			die;
		break;
		
		/*
		* Delete user
		*/
		case 'del':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			
			if ($user['organizer_del']) {
				db_query('DELETE FROM `'.DB_PREFIX.'_organizer` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				die('no_acc');
		break;
		
		/*
		* Is edit
		*/
		case 'is_edit':
			print_r(json_encode([
				'edit' => $user['organizer_edit'] == 1 ? 'OK' : '',
				'del' => $user['organizer_del'] == 1 ? 'OK' : ''
			]));
			die;
		break;
		
		/*
		* Send
		*/
		case 'send':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$staff = intval($_POST['staff']);
			$date_start = text_filter($_POST['date_start'], null, true);
			$date_end = text_filter($_POST['date_end'], null, true);
			$staff_time = 0;
			$week_hours = 0;
			$object_time = [];
			
			if ($id AND $user['organizer_edit'] OR !$id AND $user['organizer_add']) {
				$week = $_POST['week'];
				if (is_array($week)) {
					foreach($week as $w) {
						$full = (strtotime($w['end']) - strtotime($w['start']));
						$week_hours += $full;
						
						if (!$object_time[$w['object']])
							$object_time[$w['object']] = 0;
						$object_time[$w['object']] += $full;
					}
					$staff_time = $week_hours;
				}
				
				// user hours limit
				if ($exist = db_multi_query('
					SELECT 
						SUM(staff_time) as staff_time
					FROM `'.DB_PREFIX.'_organizer` 
					WHERE IF(date_end, date_end >= \''.$date_start.'\', 1) AND IF(\''.$date_end.'\', date_start <= \''.$date_end.'\', 1) AND staff = '.$staff.(
						$id ? ' AND id != '.$id : ''
					)
				)) {
					$week_hours += $exist['staff_time'];
				}
				
				$user_info = db_multi_query('
					SELECT 
						group_ids
					FROM `'.DB_PREFIX.'_users` 
					WHERE id = '.$staff
				);
				$gr = min(explode(',', $user_info['group_ids']));
				
				$group_info = db_multi_query('
					SELECT 
						week_hours
					FROM `'.DB_PREFIX.'_groups` 
					WHERE group_id = '.$gr
				);

				if (floatval($group_info['week_hours']) > 0 AND floatval($group_info['week_hours']) < $week_hours/3600)
					die('no_hours');
				// user hours limit
				
				// store hours limit				
				$o_where = '';
				$o_field = '';
				foreach(array_keys($object_time) as $v) {
					if (intval($v)) {
						if ($o_where) 
							$o_where .= ' OR ';
						$o_where .= 'FIND_IN_SET('.$v.', REGEXP_REPLACE(REGEXP_REPLACE(object_time, \'(?:"([0-9]+)":([0-9]+)(,?))\', \'\\\1\\\3\'), \'([{}])\', \'\'))';
						
						if ($o_field) 
							$o_field .= ', ';
						$o_field .= 'SUM(REGEXP_REPLACE(object_time, \'{(.*?)("2":([0-9.]+))(.*?)}\', \'\\\3\')) as store_'.$v;
					}
				}
				$objects = db_multi_query('
					SELECT 
						'.$o_field.'
					FROM `'.DB_PREFIX.'_organizer` 
					WHERE ('.$o_where.') AND IF(date_end, date_end >= \''.$date_start.'\', 1) AND IF(\''.$date_end.'\', date_start <= \''.$date_end.'\', 1)
				'.(
					$id ? ' AND id != '.$id : ''
				));
				
				if ($object_hours = db_multi_query('
					SELECT 
						id,
						name,
						week_hours
					FROM `'.DB_PREFIX.'_objects` 
					WHERE id IN ('.implode(',', array_keys($object_time)).')', true
				)) {
					foreach($object_hours as $oh) {
						if ($oh['week_hours'] AND $oh['week_hours'] < ($objects['store_'.$oh['id']] + $object_time[$oh['id']]) / 3600)
							die('store_hours_'.$oh['name']);
					}
				}
				// store hours limit
			
				db_query((
							$id ? 'UPDATE' : 'INSERT INTO'
						).' `'.DB_PREFIX.'_organizer` SET
						staff = \''.$staff.'\',
						date_start = \''.$date_start.'\',
						date_end = \''.$date_end.'\',
						time = \''.($_POST['week'] ? text_filter(json_encode($_POST['week'], JSON_FORCE_OBJECT), null, true) : '').'\',
						staff_time = '.$staff_time.', 
						object_time = \''.json_encode($object_time, JSON_FORCE_OBJECT).'\'
						'.(
							$id ? ' WHERE id = '.$id : ''
						)
					);
			
				die('OK');
			} else {
				die('ERR');
			}
		break;
		 
		default:
			$meta['title'] = 'Organizer';

			$options = '<option value="0">Not selected</option>';
			foreach(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_groups`', true) as $g) {
				$options .= '<option value="'.$g['group_id'].'">'.$g['name'].'</option>';
			}
			
			$select_options = '<option value="0">Not selected</option>';
			foreach(db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects`', true) as $o) {
				$select_options .= '<option value="'.$o['id'].'">'.$o['name'].'</option>';
			}
			
			tpl_set('organizer/main', [
				'groups' => $options,
				'select-options' => $select_options
			], [
			], 'content');
		break;
	}
} else {
	tpl_set('forbidden', [
		'text' => 'You have no access to this page',
	], [], 'content');
}