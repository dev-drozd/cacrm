<?php
/**
 * @appointment Feedbacks
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2018
 * @link        https://yoursite.com
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]){
	
	/*
	* Feedback report by day
	*/
	case 'report_by_day':
		//is_ajax() or die('Hacking attempt!');
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', strtotime(' - 7 days'));
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
			$stars = [];
			$labels = [];
			$count = 0;
			
			if ($user['manager_report'] AND $user['store_id'])
				$object_id = $user['store_id'];
			
			$feebacks_array = [];
			
			if ($feedbacks = array_column(db_multi_query('
				SELECT
					SUM(f.ratting)/COUNT(f.ratting) as middle,
					'.(
						($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
					).'(f.date) as date
				FROM `'.DB_PREFIX.'_feedback` f
				LEFT JOIN `'.DB_PREFIX.'_issues` i
					ON i.id = f.issue_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = i.object_owner
				WHERE 1 '.(
					($date_start AND $date_finish) ? ' AND f.date >= CAST(\''.$date_start.' 00:00:00\' AS DATETIME) AND f.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
				).(
					$object_id ? ' AND i.object_owner = '.$object_id : ''
				).'
				GROUP BY '.(
						($_POST['date_start'] AND $date_start == $date_finish) ? 'HOUR' : 'DATE'
					).'(f.date)
			', true), 'middle', 'date')) {

				if ($date_start == $date_finish) {
					$count = 23;
					$step = 1;

					foreach($feedbacks as $k => $v){
						$feebacks_array[] = [$k, number_format($v, 2, '.', '')];
					}

				} else {
					$count = (strtotime($date_finish) - strtotime($date_start)) / 24 / 3600;
					$step = round($count / 15) ? round($count / 15) : 1;
					$date_i = date ("Y-m-d", strtotime($date_start));
					
					for($i = 0; $i <= $count ; $i++) {
						if ($feedbacks[$date_i]) 
							$feebacks_array[] = [$i, floatval(number_format($feedbacks[$date_i], 2, '.', '')), 'Date: '.$date_i.'; Middle value: '.number_format($feedbacks[$date_i], 2, '.', '')];
						
						if (!($i % $step)) {
							$labels[] = date ("d-m", strtotime($date_i));
						}
						$date_i = date ("Y-m-d", strtotime("+1 day", strtotime($date_i)));
					}	
				}
			}

			die(json_encode([
				'data' => array_values($feebacks_array),
				'labels' => $labels,
				'step' => $step,
				'last' => $count
			]));
		}
	break;
	
	/*
	* Report feedbacks
	*/
	case 'report':
		is_ajax() or die('Hacking attempt!');
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$date_start = $_POST['date_start'] ? text_filter($_POST['date_start'], 30, true) : date('Y-m-d', strtotime(' - 7 days'));
			$date_finish = $_POST['date_finish'] ? text_filter($_POST['date_finish'], 30, true) : date('Y-m-d', time());
			$stars = [];
			$labels = [];
			$count = 0;
			
			if ($user['manager_report'] AND $user['store_id'])
				$object_id = $user['store_id'];
			
			for($i = 0; $i < 5; $i++) {
				$stars[$i] = [$i, 0, ''];
				$labels[$i] = [$i + 1];
			}
			if ($feedbacks = db_multi_query('
				SELECT
					SUM(IF(f.ratting = 1, 1, 0)) AS r1,
					SUM(IF(f.ratting = 2, 1, 0)) AS r2,
					SUM(IF(f.ratting = 3, 1, 0)) AS r3,
					SUM(IF(f.ratting = 4, 1, 0)) AS r4,
					SUM(IF(f.ratting = 5, 1, 0)) AS r5,
					IF(i.object_owner, i.object_owner, 0) as object,
					o.name
				FROM `'.DB_PREFIX.'_feedback` f
				LEFT JOIN `'.DB_PREFIX.'_issues` i
					ON i.id = f.issue_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = i.object_owner
				WHERE 1 '.(
					($date_start AND $date_finish) ? ' AND date(f.date) >= date(\''.$date_start.'\') AND date(f.date) <= date(\''.$date_finish.'\')' : ''
				).(
					$object_id ? ' AND i.object_owner = '.$object_id : ''
				).'
				GROUP BY object
			', true)) {

				foreach($feedbacks as $k => $f) {
					$stars[0][1] += $f['r1'];
					$stars[1][1] += $f['r2'];
					$stars[2][1] += $f['r3'];
					$stars[3][1] += $f['r4'];
					$stars[4][1] += $f['r5'];
					
					tpl_set('analytics/feedback/store', [
						'name' => $f['object'] > 0  ? $f['name'] : $lang['NoStore'],
						'object' => $f['object'],
						'date_start' => $date_start,
						'date_finish' => $date_finish,
						'r1' => $f['r1'],
						'r2' => $f['r2'],
						'r3' => $f['r3'],
						'r4' => $f['r4'],
						'r5' => $f['r5']
					], [], 'stores');
				}
			}
			
			foreach($stars as $k => $s) {
				if ($s[1] > 0)
					$count ++;
				else {
					unset($stars[$k]);
				}
			}

			die(json_encode([
				'data' => array_values($stars),
				'labels' => $labels,
				'step' => 1,
				'last' => 5,
				'stores' => $tpl_content['stores']
			]));
		}
	break;
	
	/*
	* Analytics feedbacks
	*/
	case 'analytics':
		if (in_to_array(1, $user['group_ids']) OR in_to_array(2, $user['group_ids']) OR ($user['manager_report'] AND $user['store_id'])) {
			$meta['title'] = $lang['FeedbackAnalytics'];
			tpl_set('feedbacks/analytics/main', [
			], [
			], 'content');
		}
	break;
	
	case 'report_details':
		$meta['title'] = 'Reporort details';
		$page = (int)$_REQUEST['page'];
		$send_staff = ((int)$_REQUEST['send_staff']) ?: ((int)$_REQUEST['all']);
		$date_start = text_filter($_REQUEST['date_start'], 30, true);
		$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
		$count = 20;
		
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS u.name as cname, u.lastname as clastname, u.phone, u.image as cava, s.name as sname, s.lastname as slastname, s.image as sava, f.ratting, f.comment, f.date as fb_date, f.id, f.customer_id, f.date, f.send_staff_id FROM
			`'.DB_PREFIX.'_feedback` f 
			INNER JOIN `'.DB_PREFIX.'_users` u ON f.customer_id = u.id 
			INNER JOIN `'.DB_PREFIX.'_users` s ON f.send_staff_id = s.id
			WHERE 1'.(
				($date_start AND $date_finish) ? ' AND f.date >= CAST(\''.$date_start.'\' AS DATE) AND f.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).(
				$send_staff ? ' AND f.send_staff_id = '.$send_staff : ''
			).' ORDER BY f.ratting DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				
				$star = '';
				
				if($row['ratting'] > 0){
					for($j = 1; $j <= 5; $j++){
						$star .= '<span class="fa fa-star rStart mini r_'.$row['ratting'].(
							$j <= $row['ratting'] ? ' active' : ''
						).'" ratting="'.$j.'"></span>';
					}
				}
				
				$phones = '';
				foreach(explode(',', $row['phone']) as $phone){
					$phones .= '<a href="tel:'.$phone.'">'.$phone.'</a><br />';
				}
				
				tpl_set('feedbacks/details/item', [
					'id' => $row['id'],
					'date' => convert_date($row['date'], true),
					'customer-id' => $row['customer_id'],
					'customer_name' => $row['cname'].' '.$row['clastname'],
					'staff-id' => $row['send_staff_id'],
					'staff_name' => $row['sname'].' '.$row['slastname'],
					'phone' => $phones,
					'cava' => $row['cava'],
					'sava' => $row['sava'],
					'comment' => wordwrap($row['comment'], 35, "<br />", true),
					'ratting' => $row['ratting'],
					'star' => $star 
				], [
					'cava' => $row['cava'],
					'sava' => $row['sava'],
					'nfb' => !$row['fb_id']
				], 'feedbacks');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['feedbacks'],
			]));
		}
		tpl_set('feedbacks/details/main', [
			'query' => '',
			'date-start' => $date_start,
			'date-finish' => $date_finish,
			'staff-id' => $send_staff,
			'res-count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'feedbacks' => $tpl_content['feedbacks']
		], [
		], 'content');
	break;

	/*
	* Feedbacks of job
	*/
	case 'jobs':
	
		$meta['title'] = 'Feedbacks of job';
		$query = text_filter($_REQUEST['query'], 255, false);
		$page = (int)$_REQUEST['page'];
		$object = (int)$_REQUEST['object'];
		$ratting = (int)$_REQUEST['rating'];
		$type = (int)$_REQUEST['type'];
		$send_staff = (int)$_REQUEST['send_staff'];
		$date_start = text_filter($_REQUEST['date_start'], 30, true);
		$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
		$count = 10;
		
		$q = '
			SELECT SQL_CALC_FOUND_ROWS
				i.id,
				i.date,
				i.customer_id,
				u.name as cname, 
				u.lastname as clastname,
				u.phone,
				u.image as cava,
				IF(f.ratting, f.ratting, i.fb_ratting) as ratting,
				f.comment,
				f.date as fb_date,
				IF(i.fb_ratting, \'custom\', f.id) as fb_id
			FROM `'.DB_PREFIX.'_issues` i
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON i.customer_id = u.id
				'.(
					$type === 2 ? 'INNER' : 'LEFT'
				).' JOIN `'.DB_PREFIX.'_feedback` f
					ON i.id = f.issue_id
			WHERE i.finished = 1 AND u.incphone = 0'.(
				$type === 1 ? ' AND f.id IS NULL' : ''
			).(
				$query ? ' AND CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' ' : ''
			).(
				$object ? ' AND i.object_owner = '.$object : ''
			).(
				($date_start AND $date_finish) ? ' AND i.date >= CAST(\''.$date_start.'\' AS DATE) AND i.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).(
				isset($_REQUEST['rating']) ? ' AND (f.ratting = '.$ratting.' OR i.fb_ratting = '.$ratting.') ' : ''
			).(
				$send_staff ? ' AND f.send_staff_id = '.$send_staff : ''
			).' ORDER BY '.(
				$type === 1 ? 'i.date' : (
					$type === 2 ? 'f.date' : 'i.date'
				)
			).' DESC LIMIT '.($page*$count).', '.$count;
		
		if($sql = db_multi_query($q, true)){
			$i = 0;
			foreach($sql as $row){
				
				$star = '';
				
				if($row['ratting'] > 0){
					for($j = 1; $j <= 5; $j++){
						$star .= '<span class="fa fa-star rStart mini r_'.$row['ratting'].(
							$j <= $row['ratting'] ? ' active' : ''
						).'" ratting="'.$j.'"></span>';
					}
				}
				
				$phones = '';
				foreach(explode(',', $row['phone']) as $phone){
					$phones .= '<a href="tel:'.$phone.'">'.$phone.'</a><br />';
				}
				
				tpl_set('feedbacks/jobs/item', [
					'id' => $row['id'],
					'fb-id' => $row['fb_id'] ?: 0,
					'date' => $row['date'],
					'fb-date' => $row['fb_date'] ?: '-',
					'customer-id' => $row['customer_id'],
					'customer_name' => $row['cname'].' '.$row['clastname'],
					'phone' => $phones,
					'cava' => $row['cava'],
					'comment' => wordwrap($row['comment'], 35, "<br />", true),
					'ratting' => $row['ratting'],
					'star' => $star 
				], [
					'cava' => $row['cava'],
					'fb' => $row['fb_id'] != 'custom',
					'nfb' => !$row['fb_id']
				], 'feedbacks');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['feedbacks'],
			]));
		}
		tpl_set('feedbacks/jobs/main', [
			'query' => '',
			'res-count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'feedbacks' => $tpl_content['feedbacks']
		], [
		], 'content');
	break;

	/*
	* Random feedbacks
	*/
	case 'random':
		$meta['title'] = 'Random feedbacks';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 10;
		
		if($sql = db_multi_query('
			SELECT DISTINCT SQL_CALC_FOUND_ROWS
				f.*,
				u.id, 
				u.name as cname, 
				u.lastname as clastname,
				u.phone,
				u.image as cava 
			FROM `'.DB_PREFIX.'_users` u 
			LEFT JOIN `'.DB_PREFIX.'_feedback_random` f
				ON f.id = u.id
			LEFT JOIN `'.DB_PREFIX.'_issues` i
				ON i.customer_id = u.id
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON inv.customer_id = u.id
			WHERE i.id IS NULL AND inv.id IS NULL AND f.id IS NULL '.(
				$query ? 'AND CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' ' : ''
			).'ORDER BY u.id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				$star = '';
				for($j = 1; $j <= 5; $j++){
					$star .= '<span class="fa fa-star rStart mini r_'.$row['ratting'].(
						$j <= $row['ratting'] ? ' active' : ''
					).'" ratting="'.$j.'"></span>';
				}
					
				tpl_set('feedbacks/random/item', [
					'id' => $row['id'],
					'customer_name' => $row['cname'].' '.$row['clastname'],
					'phone' => $row['phone'],
					'cava' => $row['cava'],
					'comment' => $row['comment'],
					'ratting' => $row['ratting'],
					'star' => $star 
				], [
					'cava' => $row['cava']
				], 'feedbacks');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['feedbacks'],
			]));
		}
		tpl_set('feedbacks/random/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'feedbacks' => $tpl_content['feedbacks']
		], [
		], 'content');
	break;
	
	/*
	* All feedbacks
	*/
	case null:
		$meta['title'] = 'Feedbacks';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$staff = intval($_POST['staff'] ? $_POST['staff'] : $_GET['staff']);
		$rating = intval($_REQUEST['rating']);
		$type = intval($_POST['type']);
		$create = intval($_POST['create']);
		$date_start = text_filter($_REQUEST['date_start'], 30, true);
		$date_finish = text_filter($_REQUEST['date_finish'], 30, true);
		$object = intval($_REQUEST['object']);
		$count = 10;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				f.*,
				DATE(f.date) as date,
				TIME(f.date) as time,
				u.name, 
				u.lastname,
				u.image as ava,
				c.name as cname, 
				c.lastname as clastname,
				c.phone,
				c.image as cava,
				ss.name as ss_name, 
				ss.lastname as ss_lastname,
				IF(cm.name != \'\', cm.name, cm.email) as chat_name,
				cm.image as cava
			FROM `'.DB_PREFIX.'_feedback` f 
			LEFT JOIN `'.DB_PREFIX.'_users` u 
				ON u.id = f.staff_id 
			LEFT JOIN `'.DB_PREFIX.'_issues` i
				ON i.id = f.issue_id 
			LEFT JOIN `'.DB_PREFIX.'_users` c 
				ON c.id = f.customer_id 
			LEFT JOIN `'.DB_PREFIX.'_users` ss 
				ON ss.id = f.send_staff_id
			LEFT JOIN `'.DB_PREFIX.'_chat_im` cm 
				ON cm.id = f.customer_id 
			WHERE f.date > CURRENT_DATE - INTERVAL 6 MONTH '.(
				$query ? 'AND (CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' OR CONCAT(c.name, \' \', c.lastname) LIKE \'%'.$query.'%\' OR f.issue_id = \''.$query.'\') ' : ''
			).(
				$staff ? 'AND f.staff_id = '.$staff.' ' : ''
			).(
				$create ? 'AND f.send_staff_id = '.$create.' ' : ''
			).(
				$type > 0 ? 'AND f.type = '.$type.' ' : ''
			).(
				$rating ? 'AND f.ratting = '.$rating.' ' : ''
			).(
				$object ? 'AND i.object_owner = '.$object.' ' : ''
			).(
				($date_start AND $date_finish) ? ' AND f.date >= CAST(\''.$date_start.'\' AS DATE) AND f.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'ORDER BY f.type DESC, f.ratting ASC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				$star = '';
				for($j = 1; $j <= 5; $j++){
					$star .= '<span class="fa fa-star rStart mini r_'.$row['ratting'].(
						$j <= $row['ratting'] ? ' active' : ''
					).'" ratting="'.$j.'"></span>';
				}
					
				tpl_set('feedbacks/item', [
					'id' => $row['id'],
					'staff_id' => $row['staff_id'],
					'staff_name' => $row['name'].' '.$row['lastname'],
					'send_staff_id' => $row['send_staff_id'],
					'send_staff_name' => $row['ss_name'].' '.$row['ss_lastname'],
					'customer_id' => $row['customer_id'],
					'customer_name' => $row['type'] == 4 ? $row['chat_name'] : $row['cname'].' '.$row['clastname'],
					'phone' => $row['type'] != 4 ? $row['phone'] : '',
					'issue' => $row['issue_id'],
					'ava' => $row['ava'],
					'cava' => $row['cava'],
					'date' => $row['date'],
					'time' => $row['time'],
					'comment' => $row['comment'],
					'ratting' => $row['ratting'],
					'star' => $star
				], [
					'ava' => $row['ava'],
					'cava' => $row['cava'],
					'sms' => $row['type'] == 1,
					'email' => $row['type'] == 2,
					'tablet' => $row['type'] == 3,
					'chat' => $row['type'] == 4,
					'custom' => $row['type'] == 0,
					'staff' => $row['staff_id'] == $user['id']
				], 'feedbacks');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['feedbacks'],
			]));
		}
		tpl_set('feedbacks/main', [
			'res_count' => $res_count,
			'query' => '',
			'more' => $left_count ? '' : ' hdn',
			'feedbacks' => $tpl_content['feedbacks']
		], [
		], 'content');
	break;
}