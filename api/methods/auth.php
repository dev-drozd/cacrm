<?php

auth_app();

is_token();

$ip = $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'];

if($_REQUEST['access_token'] ?? null){
	if($user = db_multi_query('SELECT u.name as first_name, u.lastname as last_name, u.image, u.id, u.login, u.email FROM `'.DB_PREFIX.'_access_tokens` t INNER JOIN `'.DB_PREFIX.'_users` u ON t.user_id = u.id WHERE token = \''.db_escape_string($_REQUEST['access_token']).'\'')){
		$user['access_token'] = $_REQUEST['access_token'];
		$res['user'] = $user;
		$sub_method = null;
		if($_REQUEST['push_id'] ?? null){
			db_query('
				INSERT INTO `'.DB_PREFIX.'_access_tokens` SET user_id = '.$user['id'].', push_id = \''.db_escape_string($_REQUEST['push_id']).'\', token = \''.db_escape_string($_REQUEST['access_token']).'\' ON DUPLICATE KEY UPDATE token = \''.db_escape_string($_REQUEST['access_token']).'\', push_id = \''.db_escape_string($_REQUEST['push_id']).'\''
			);
		}
	} else
		$res['err'] = 'Token failed';
} else {
	$login = trim($_REQUEST['login'] ?? '');
	$password = trim($_REQUEST['password'] ?? '');
	if($u = db_multi_query('
		SELECT id, password, del, name as first_name, lastname as last_name, image, login, email
		FROM `'.DB_PREFIX.'_users`
		WHERE '.(filter_var(
			$login, FILTER_VALIDATE_EMAIL
		) ? 'email' : 'login').' = \''.db_escape_string($login).'\''
	)){
	    $password = md5(md5(trim($_REQUEST['password'])));
		if($u['password'] === $password AND $u['del'] == 0){
			$access_token = get_token($u['id'].$password);
			file_put_contents(ROOT_DIR.'/log.txt', $access_token);
			db_query('
				INSERT INTO `'.DB_PREFIX.'_access_tokens` SET user_id = '.$u['id'].', push_id = \''.db_escape_string($_REQUEST['push_id']).'\', token = \''.$access_token.'\' ON DUPLICATE KEY UPDATE token = \''.$access_token.'\', push_id = \''.db_escape_string($_REQUEST['push_id']).'\''
			);
			unset($u['del']);
			unset($u['password']);
			$u['access_token'] = $access_token;
			$res['user'] = $u;
			$res['access_token'] = $access_token;
		} else
			$res['err'] = 'Wrong password';
	} else
		$res['err'] = 'No such user';
}

if(!$res['err']){
	$res['dialogs'] = (int)db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_chat_im` WHERE email != \'\' AND count > 0')['count'];
	$res['quotes'] = (int)db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_quote_requests` r LEFT JOIN `'.DB_PREFIX.'_objects` o ON r.store_id = o.id WHERE r.del = 0 AND r.sent_id = 0')['count'];
	$res['requests'] = (int)db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_orders` WHERE del = 0')['count'];
	$res['total'] = $res['dialogs']+$res['quotes']+$res['requests'];
}
?>