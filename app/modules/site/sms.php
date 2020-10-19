<?php
/**
 * @appointment Sms feedback
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

if(strlen($_GET['tel']) >= 10 AND $_GET['text']){
	
	// Get rating
	$rating = preg_match('|\d+|', $_GET['text']) ? (
		strpos($_GET['text'], '5') !== false ? 5 : (
			strpos($_GET['text'], '4') !== false ? 4 : (
				strpos($_GET['text'], '3') !== false ? 3 : (
					strpos($_GET['text'], '2') !== false ? 2 : (
						strpos($_GET['text'], '1') !== false ? 1 : (
							strpos($_GET['text'], '0') !== false ? 1 : 5
						)
					)
				)
			)
		)
	) : 0;
	
		
	// Edit number
	$phone = '';
	$p = str_split(strrev(trim($_GET['tel'])));
	for($i = 0; $i < count($p); $i++){
		if($i == 10){
			$phone .= $p[$i].'+';
			break;
		}
		$phone .= $p[$i];
		if($i == 6 OR $i == 9) $phone .= ' ';
	}
	
	// Get issue
	if($issue = db_multi_query('
		SELECT DISTINCT
			u.id as uid, iss.id, iss.staff_id
		FROM `'.DB_PREFIX.'_users` u
		LEFT JOIN `'.DB_PREFIX.'_inventory` inv
			ON u.id = inv.customer_id
		LEFT JOIN `'.DB_PREFIX.'_issues` iss
			ON iss.inventory_id = inv.id
		WHERE sms LIKE \'%'.strrev($phone).'%\' ORDER BY iss.id DESC LIMIT 1'
	)){
		// Send feedback
		db_query('INSERT INTO `'.DB_PREFIX.'_feedback` SET
			type = 1,
			issue_id = '.$issue['id'].',
			staff_id = \''.$issue['staff_id'].'\',
			customer_id = \''.$issue['uid'].'\',
			comment = \''.(!$rating ? db_escape_string($_GET['text']) : '').'\',
			ratting = '.$rating
		);
			
		// ++ points
		if ($rating == 5) {
			$points = floatval($config['user_points']['feedback']['sms_points']);
			db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
				staff_id = '.$issue['staff_id'].',
				issue_id = '.$issue['id'].',
				action = \'feedback\',
				point = \''.$points.'\''
			);	
			db_query(
				'UPDATE `'.DB_PREFIX.'_users`
					SET points = points+'.$points.'
				WHERE id = '.$issue['staff_id']
			);
		}
		echo 'OK';
	}
	
	$_GET['date'] = date("d.m.Y H:i:s");
	set_log('feedback', var_export($_GET, true));
}
die;
?>