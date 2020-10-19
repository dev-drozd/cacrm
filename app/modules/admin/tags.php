<?php
/**
 * @appointment Tags admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$res = ['status' => 'NO'];

$id = (int)$route[2];

switch($route[1]){
	
	case 'profit_job':
		list($issue_id, $staff_id) = explode('-', $route[2]);
		if($statuses = db_multi_query(
			'SELECT st.id, st.name FROM `'.DB_PREFIX.'_issues_changelog` iss INNER JOIN `'.DB_PREFIX.'_inventory_status` st ON iss.changes_id = st.id WHERE `issue_id` = '.intval($issue_id).' AND `user` = '.intval($staff_id).' AND `changes` = \'status\''
		, true)){
			$res['name'] = [];
			foreach($statuses as $status){
				$res['name'][] = $status['name'];
			}
			$res['title'] = 'Established statuses';
			$res['descr'] = implode(', ', $res['name']);
			$res['status'] = 'OK';
		}
	break;
	
	case 'purchase':
		case 'rma':
		if($res = db_multi_query('SELECT id, if(
				sale_name, sale_name, name
			) as title, if(
				photo, CONCAT(\'/uploads/images/\', id, \'/thumb_\', photo), \'\'
			) as photo, if(
				total, total, price
			) as price
			FROM `'.DB_PREFIX.'_purchases`
			WHERE id = '.$id
			)
		) $res['status'] = 'OK';
	break;
	
	case 'issue':
		if($res = db_multi_query('
			SELECT id, description as descr, total as price
			FROM `'.DB_PREFIX.'_issues`
			WHERE id = '.$id
			)
		) $res['status'] = 'OK';
	break;
	
	case 'bug':
		if($res = db_multi_query('
			SELECT id, title, content as descr, date
			FROM `'.DB_PREFIX.'_bugs`
			WHERE id = '.$id
			)
		) $res['status'] = 'OK';
	break;
	
	case 'camera':
		if($res = db_multi_query('
			SELECT a.id, s.name as title, a.camera_event, a.camera, a.event, a.date, t.seconds, a.event_id
			FROM `'.DB_PREFIX.'_activity` a
				LEFT JOIN `'.DB_PREFIX.'_camera_status` s 
				ON a.status_id = s.id
				LEFT JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = a.user_id AND t.date >= CURDATE()
			WHERE a.id = '.$id
			)
		) {
			$event = $res['event'];
			
			if(!$res['camera_event']){
				
				switch($res['event']){
					
					case 'stop working time':
						$event = $lang['stopTime'].' '.$res['seconds'];
					break;	

					case 'start working time':
						$event = $lang['startTime'];
					break;		

					case 'pause working time':
						$event = $lang['pauseTime'];
					break;					
					
					case 'add_purchase':
						$event = $lang['addNew'].' <a href="/purchases/edit/'.$res['event_id'].'" target="_blank">'.$lang['purchase'].'</a>';
					break;
					
					case 'new user':
						$event = $lang['addNew'].' <a href="/users/view/'.$res['event_id'].'" target="_blank">'.$lang['user'].'</a>';
					break;
					
					case 'new inventory':
						$event = $lang['addNew'].' <a href="/inventory/view/'.$res['event_id'].'" target="_blank">'.$lang['inventory'].'</a>';
					break;
					
					case 'new service':
						$event = $lang['addNew'].' <a href="/inventory/edit/'.$res['event_id'].'" target="_blank">'.$lang['service'].'</a>';
					break;
					
					case 'make transaction':
						$event = '<a href="/invoices/view/'.$res['event_id'].'" target="_blank">'.$lang['makeTran'].'</a>';
					break;
					
					default:
						$event = $res['camera'] ? $res['camera_event'] : $res['event'];
					break;
				}
			}
			$res['event'] = $res['camera_event'] ?: $event;
			$res['status'] = 'OK';
		}
	break;
	
	case 'stock':
		if($res = db_multi_query('
			SELECT i.id, CONCAT(
				IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model
			) as title,
				descr, price as price,
				i.images
			FROM `'.DB_PREFIX.'_inventory` i LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
				ON i.type_id = t.id
			LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
				ON c.id = i.category_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
				ON m.id = i.model_id
			WHERE i.id = '.$id
			)
		) $res['status'] = 'OK';
	break;
	
	case 'invoice':
		if($res = db_multi_query('
			SELECT i.date, i.total, CONCAT(u.name, \' \', u.lastname) as title, if(
				u.image, CONCAT(\'/uploads/images/users/\', u.id, \'/thumb_\', u.image), \'\'
			) as photo, total as price FROM `'.DB_PREFIX.'_invoices` i LEFT JOIN `'.DB_PREFIX.'_users` u ON i.customer_id = u.id WHERE i.id = '.$id
			)
		) $res['status'] = 'OK';
	break;
	
	case 'cash':
		if($res = db_multi_query('
			SELECT ch.date, ob.name as store, ch.amount, ch.lack FROM `'.DB_PREFIX.'_cash` ch LEFT JOIN `'.DB_PREFIX.'_objects` ob ON ch.object_id = ob.id WHERE ch.id = '.$id
			)
		) $res['status'] = 'OK';
	break;

	case 'user':
		if ($res = db_multi_query('
			SELECT 
				CONCAT(name, \' \', lastname) as title,
				if(
					image, CONCAT(\'/uploads/images/users/\', id, \'/thumb_\', image), \'\'
				) as photo 
			FROM `'.DB_PREFIX.'_users` 
			WHERE id = '.$id
		)) $res['status'] = 'OK';
	break;
}

echo json_encode($res);

die;
?>