<?php
/**
 * @appointment Email feedback
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$id = (int)$_GET['id'];
$rate = (int)$_GET['rate'];

if(isset($_COOKIE['fb_'.$id]) && $_SERVER['REQUEST_TIME'] < $_COOKIE['fb_'.$id]+3600){
	header('Location: /');
	die;
}

if(($sql = db_multi_query('
	SELECT u.id, u.name, u.lastname, i.staff_id, o.google_map_id 
	FROM `'.DB_PREFIX.'_issues` i 
		INNER JOIN `'.DB_PREFIX.'_users` u ON i.customer_id = u.id 
		LEFT JOIN `'.DB_PREFIX.'_objects` o ON i.object_owner = o.id 
	WHERE i.id = '.$id)) && md5($id.$sql['id']) === $_GET['hash']){
		
	setcookie('fb_'.$id, $_SERVER['REQUEST_TIME'], $_SERVER['REQUEST_TIME']+(3600*24*7), '/', 'yoursite.com', null, true);
	
	
	// Send feedback
	db_query('INSERT INTO `'.DB_PREFIX.'_feedback` SET
		type = 2,
		issue_id = '.$id.',
		staff_id = \''.$sql['staff_id'].'\',
		customer_id = \''.$sql['id'].'\',
		ratting = '.$rate
	);
		
	// ++ points
	if ($rate == 5) {
		$points = floatval($config['user_points']['feedback']['email_points']);
		db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
			staff_id = '.$sql['staff_id'].',
			issue_id = '.$id.',
			action = \'feedback\',
			point = \''.$points.'\''
		);	
		db_query(
			'UPDATE `'.DB_PREFIX.'_users`
				SET points = points+'.$points.'
			WHERE id = '.$sql['staff_id']
		);
		header('Location: https://search.google.com/local/writereview?placeid='.$sql['google_map_id']);
		die;
	}
	echo tpl_set('feedback', [
		'name' => $sql['name'],
		'lastname' => $sql['lastname'],
	]);
} else
	header('Location: /');

die;
?>