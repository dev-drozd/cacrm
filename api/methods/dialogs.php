<?php

auth_app();

//file_put_contents(ROOT_DIR.'/log.txt', var_export($_REQUEST, true));

switch($sub_method){
	
	case 'del':
		if(is_token()){
			$id = (int)$_POST['id'];
			db_query('UPDATE `'.DB_PREFIX.'_chat_messages` SET del = '.$user['id'].' WHERE id = '.$id);
		}
	break;
	
	case 'reply_templates':
		//$res['templates'] = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_reply_templates` WHERE message LIKE \'%'.db_escape_string($_POST['q']).'%\' LIMIT 10', true);
	break;
	
	case 'send':
		if(is_token()){
			db_query('
				INSERT INTO `'.DB_PREFIX.'_chat_messages` SET message = \''.db_escape_string($_REQUEST['msg']).'\', im_id = '.intval($_REQUEST['im_id']).', staff_id = '.intval($user['id'])
			);
			$msg_id = (int)mysqli_insert_id($db_link);
			if($im = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_chat_im` WHERE id = '.intval($_REQUEST['im_id']))){
				send_push($im['token'], [
					'type' => 'chat_message',
					'message' => '<div class="cd-block" data-id="'.$msg_id.'">
						<div class="cd-image">
							'.(
								$user['image'] ? '<img src="/uploads/images/users/'.$user['id'].'/'.$user['image'].'" class="imImg">' : '<span class="fa fa-user-secret imImg"></span>'
							).'
						</div>
						<div class="cd-single">
							<label>'.$user['first_name'].':</label>
							<p>'.outputMsg(stripcslashes($_REQUEST['msg'])).'</p>
						</div>
					</div>'
				]);
				db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET last_msg = \'CA: '.text_filter($_REQUEST['msg'], 244).'\' WHERE id = '.intval($_REQUEST['im_id']));
			}
			//db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET count = 0 WHERE id = '.$im_id);
			$res['err'] = 0;
		} else
			$res['err'] = 'Bad access token';
	break;
	
	case 'viewed':
		$im_id = (int)$_POST['id'];
		db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET count = 0 WHERE id = '.$im_id);
	break;
	
	case 'messages':
		$im_id = (int)$_REQUEST['id'];
		$res['messages'] = array_reverse(db_multi_query('SELECT m.*, IF(m.staff_id, CONCAT(u.name, \' \', u.lastname), \'\') as name, m.message as text FROM `'.DB_PREFIX.'_chat_messages` m LEFT JOIN `'.DB_PREFIX.'_users` u ON m.staff_id = u.id WHERE m.im_id = '.$im_id.' AND m.del = 0 ORDER BY m.id DESC LIMIT 0, 10', true, function($a, $b){
			$a['text'] = outputMsg($a['text']);
			//$a['text'] = $a['text'];
			return [$b, $a];
		}));
		db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET count = 0 WHERE id = '.$im_id);
	break;
	
	case null:
		is_token();
		$res['dialogs'] = db_multi_query('SELECT *, IF(name != \'\', name, email) as email, IF(SUBSTR(REPLACE(last_msg, \'\r\n\', \' \'), 1, 30) = REPLACE(last_msg, \'\r\n\', \' \'), REPLACE(last_msg, \'\r\n\', \' \'), CONCAT(SUBSTR(REPLACE(last_msg, \'\r\n\', \' \'), 1, 30), \'...\')) as last_msg, date as sortdate, IF(date < CURDATE(), DATE_FORMAT(date, "%c/%d/%y"), DATE_FORMAT(date, "%h:%i")) as date FROM `'.DB_PREFIX.'_chat_im` WHERE email != \'\' ORDER BY count = 0, `sortdate` DESC LIMIT 50', true);
	break;
}
?>