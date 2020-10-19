<?php
/**
 * @appointment Email feedback
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]){
	
	case 'feedback':
		is_ajax() or die('Hacking attempt!');
		if($ratting = (int)$_POST['ratting']){
			if($info = db_multi_query('SELECT staff_ids FROM `'.DB_PREFIX.'_chat_im` WHERE feedback = 0 AND id = '.$chat_id)){
				foreach(explode(',', $info['staff_ids']) as $staff_id){
					db_query('INSERT INTO `'.DB_PREFIX.'_feedback` SET
						type = 4,
						staff_id = \''.$staff_id.'\',
						customer_id = \''.$chat_id.'\',
						ratting = '.$ratting
					);
					if ($rate == 5 && ($points = floatval($config['user_points']['feedback']['chat_points']))){
						db_query(
							'UPDATE `'.DB_PREFIX.'_users`
								SET points = points+'.$points.'
							WHERE id = '.$staff_id
						);
					}
				}
				db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET feedback = '.$ratting.' WHERE id = '.$chat_id);
			} else
				echo 'ERR';
		}
		die;
	break;
	
	case 'message':
		is_ajax() or die('Hacking attempt!');
		
		$res = [];
		$name = trim($_POST['name'] ?? '');
		$email = trim($_POST['email'] ?? '');
		$phone = trim($_POST['phone'] ?? '');
		$im = (int)$_POST['im'];
		$msg = $_POST['msg'] ?? '';
		
		if($msg || filter_var($email, FILTER_VALIDATE_EMAIL)){
			
			$auth = false;
			
			if(!isset($chat_id)){
				
				$ua = parse_ua();
				
				db_query('INSERT INTO `'.DB_PREFIX.'_chat_im` SET 
					email = \''.db_escape_string($email).'\',
					phone = \''.db_escape_string($phone).'\',
					name = \''.db_escape_string($name).'\',
					uagent = \''.db_escape_string($_SERVER['HTTP_USER_AGENT']).'\',
					os = \''.db_escape_string($ua['OS_NAME'].' '.$ua['OS_VERSION']).'\',
					browser = \''.db_escape_string($ua['BR_NAME'].' '.$ua['BR_VERSION']).'\',
					device_type = \''.db_escape_string($ua['HW_TYPE']).'\',
					ip = \''.db_escape_string($ua['ip']).'\',
					token = \''.$_SESSION['guest_uniqid'].'\''
				);
				
				$chat_id = (int)mysqli_insert_id($db_link);
				
				if(explode('@', $email)[1] == 'gmail.com' && ($info = get_ava_by_email($email))){
					
					if($info['ava']){
						
						$dir = ROOT_DIR.'/uploads/images/guests/';
						
						// Is not dir
						if(!is_dir($dir.$chat_id)){
							@mkdir($dir.$chat_id, 0777);
							@chmod($dir.$chat_id, 0777);
						}
						
						$dir = $dir.$chat_id.'/';
						
						// New name
						$rename = uniqid('', true).'.jpg';
						
						// Upload image
						file_put_contents($dir.$rename, file_get_contents($info['ava']));
							
						$img = new Imagick($dir.$rename);
						
						// 1920
						if($img->getImageWidth() > 1920){
							$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
						}
						
						auto_rotate_image($img);
						
						$img->stripImage();
						
						$img->writeImage($dir.$rename);
						
						// 300x300
						$img->cropThumbnailImage(300, 300);
						$img->writeImage($dir.'preview_'.$rename);
						
						// 94x94
						$img->cropThumbnailImage(94, 94);
						$img->writeImage($dir.'thumb_'.$rename);
						$img->destroy();
						
						db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET '.(
							$name ? '' : 'name = \''.db_escape_string(trim($info['name'] ?? '')).'\','
						).' image = \''.$rename.'\' WHERE id = '.$chat_id);
					}
				}
				
				$_SESSION['chat_token'] = $_SESSION['guest_uniqid'].$chat_id;
				setcookie('chat_token', $_SESSION['guest_uniqid'].$chat_id, time()+(3600*24), '/', 'yoursite.com', null, true);
				
				if($email){
					$msg = $email.(
						$phone ? ', ('.$phone.')' : ''
					).' started a chat from page: '.($_SERVER['HTTP_REFERER'] ?? '/');
					$auth = true;
				}
			} else {
				$info = db_multi_query('SELECT name, email, image, staff_ids FROM `'.DB_PREFIX.'_chat_im` WHERE id = '.$chat_id);
			}
			
			if($msg){
				db_query('INSERT INTO `'.DB_PREFIX.'_chat_messages` SET im_id = '.$chat_id.', message = \''.db_escape_string($msg).'\', date = \''.date('Y-m-d H:i:s').'\'');
				db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET last_msg = \''.text_filter($msg, 255).'\', count = count+1 WHERE id = '.$chat_id);
			}
			
			$msg_id = (int)mysqli_insert_id($db_link);
			
			//$res['msg'] = '<div class="dialog-message me" data-id="'.$msg_id.'">
			//	<div class="dialog-img">
			//	</div>
			//	<div class="dialog-combi">
			//		<div class="dm-title">
			//			<span class="name">Me</span>
			//			<i class="name">'.date("H:i:s").'</i>
			//		</div>
			//		<div class="dm-content">'.(
			//			$email ? '<a href="mailto:'.$email.'">'.$email.'</a> started a chat...' : $msg
			//		).'
			//		</div>
			//	</div>
			//</div>';
			
			
			$res['msg'] = '<div class="cd-block" data-id="'.$msg_id.'">
				<div class="cd-image">
					<span class="fa fa-user-secret"></span>
				</div>
				<div class="cd-single">
					<label>Me:</label>
					<p>'.($email ? '<a href="mailto:'.$email.'">'.$email.'</a> started a chat from page: '.($_SERVER['HTTP_REFERER'] ?? '/') : $msg).'</p>
				</div>
			</div>';
			
			if($info['name']){
				$exp = explode(' ', $info['name']);
				$name = $exp[0];
				$lastname = $exp[1];
			}
			
			send_push('chat_guest', [
				'type' => 'chat_guest_msg',
				'id' => $msg_id,
				'block' => $auth,
				'staff_ids' => $info['staff_ids'],
				'uid' => 0,
				'chanel_id' => $chat_id ?? $_SESSION['guest_uniqid'],
				'msg' => $msg,
				'name' => $name ?? 'Guest',
				'lastname' => $lastname ?? '#'.$chat_id,
				'date' => date("H:i:s"),
				'arguments' => [
					'chat_support' => get_token('chat_support1')
				]
			]);
			
/* 			sendPush($info['staff_ids'], ($name ?? 'Guest').' '.($lastname ?? '#'.$chat_id), substr($msg, 0, 50),[
				'type' => 'chat_guest_msg',
				'id' => $msg_id,
				'block' => $auth,
				'staff_ids' => $info['staff_ids'],
				'uid' => 0,
				'chanel_id' => $chat_id ?? $_SESSION['guest_uniqid'],
				'message' => $msg,
				'name' => $name ?? 'Guest',
				'lastname' => $lastname ?? '#'.$chat_id,
				'date' => date("H:i:s"),
				'arguments' => [
					'chat_support' => get_token('chat_support1')
				]
			],'chat_guest'); */
			
			sendPush2($info['staff_id'], 'New message from the site', substr($msg, 0, 50), [
				'type' => 'chat_msg',
				'msg' => $msg,
				'im_id' => $chat_id,
				'email' => $email ?: $info['name'] ?: $info['email']
			]);
			
			sPush($info['staff_id'], 'New message from the site', substr($msg, 0, 50), [
				'type' => 'chat_msg',
				'msg' => $msg,
				'id' => $chat_id,
				'email' => $email ?: $info['name'] ?: $info['email']
			]);
			
			if($auth){
				//$res['msg'] = '<div class="dialog-chat cnt">
				//	<div class="dialog">
				//		'.$res['msg'].'
				//	</div>
				//	<div class="dialog-form">
				//		<textarea id="chat-message" placeholder="Enter your message..." onkeypress="if(event.keyCode == 13){Chat.msg(); return false;}"></textarea>
				//		<span class=" fa fa-chevron-right" onclick="Chat.msg();"></span>
				//	</div>
				//</div>';
				$res['msg'] = '<div class="chat-dialog">
					'.$res['msg'].'
				</div>
				<form class="chat-message" onsubmit="Chat.msg(event);">
					<textarea name="message" onkeypress="Chat.textarea(event)" onmousedown="Chat.mobScroll()" required></textarea>
					<button class="btn">Send</button>
				</form>';
			}
			
		} else
			$res['err'] = 1;
		
		echo json_encode($res);
		
	break;
	
	case 'iframe':
		die(tpl_set('chat/main', [
			'guest-id' => $_SESSION['guest_uniqid']
		]));
	break;
	
	case null:
		is_ajax() or die('Hacking attempt!');
		if(isset($chat_id)){
			$messages = '';
			$staff = [];
			$visitor = [];
			foreach(db_multi_query('
				SELECT m.*, u.name, u.lastname, u.image, c.name as vname FROM `'.DB_PREFIX.'_chat_im` c 
					INNER JOIN `'.DB_PREFIX.'_chat_messages` m 
						ON c.id = m.im_id 
					LEFT JOIN `'.DB_PREFIX.'_users` u 
						ON u.id = m.staff_id 
					WHERE c.id = '.$chat_id.'					
					AND c.token = \''.db_escape_string($chat_token).'\'', true
			) as $msg){
				//$messages .= '<div class="dialog-message'.(
				//	$msg['staff_id'] == 0 ? ' me' : ''
				//).'" data-id="'.$msg['id'].'">
				//	'.($msg['staff_id'] ? '<div class="dialog-img">
				//		'.(
				//			$msg['image'] ? '<img src="/uploads/images/users/'.$msg['staff_id'].'/'.$msg['image'].'" class="imImg">' : '<span class="fa fa-user-secret imImg"></span>'
				//		).'
				//	</div>' : '').'
				//	<div class="dialog-combi">
				//		<div class="dm-title">
				//			<span class="name">'.($msg['staff_id'] > 0 ? $msg['name'] : 'Me').'</span>
				//			<i class="name">'.$msg['date'].'</i>
				//		</div>
				//		<div class="dm-content">'.$msg['message'].'
				//		</div>
				//	</div>
				//</div>';
				if($msg['staff_id'] > 0){
					$staff['name'] = $msg['name'];
					$staff['image'] = $msg['image'] ? '/uploads/images/users/'.$msg['staff_id'].'/thumb_'.$msg['image'] : '';
				} else if($msg['vname'] && !$visitor['name']){
					$visitor['name'] = $msg['vname'];
				}
				$messages .= '<div class="cd-block">
					<div class="cd-image">'.(
						($msg['staff_id'] AND $msg['image']) ? '<img src="/uploads/images/users/'.$msg['staff_id'].'/thumb_'.$msg['image'].'" class="imImg">' : '<span class="fa fa-user-secret imImg"></span>'
					).'</div>
					<div class="cd-single">
						<label>'.($msg['staff_id'] > 0 ? $msg['name'] : 'Me').':</label>
						<p>'.$msg['message'].'</p>
					</div>
				</div>';
			}
			
			//$res['cnt'] = '<div class="dialog-chat cnt">
			//	<div class="dialog">
			//		'.$messages.'
			//	</div>
			//	<div class="dialog-form">
			//		<textarea id="chat-message" placeholder="Enter your message..." onkeypress="if(event.keyCode == 13){Chat.msg(); return false;}"></textarea>
			//		<span class=" fa fa-chevron-right" onclick="Chat.msg();"></span>
			//	</div>
			//</div>';
			$res['name'] = $visitor['name'] ? 'Hello, '.$visitor['name'] : 'Welcome!';
			$res['cnt'] = '<div class="chat-manager">
				'.($staff['image'] ? '<div><img src="'.$staff['image'].'"></div>' : '').'
				<div>Hello, my name is '.$staff['name'].', and I will answer any of your questions.</div>
			</div>
			<div class="chat-dialog">
				'.$messages.'
			</div>
			<form class="chat-message" onsubmit="Chat.msg(event);">
				<textarea name="message" onkeypress="Chat.textarea(event)" onmousedown="Chat.mobScroll()" required></textarea>
				<button class="btn">Send</button>
			</form>';
			$res['step'] = 3;
		} else {
			
/* (int)db_multi_query('
				SELECT COUNT(t.id) as cnt 
				FROM `'.DB_PREFIX.'_timer` t 
				INNER JOIN `'.DB_PREFIX.'_users` u 
					ON t.user_id = u.id 
				INNER JOIN `'.DB_PREFIX.'_groups` g 
					ON FIND_IN_SET(g.group_id, u.group_ids) 
					AND g.chat_support = 1 
				WHERE event = \'start\' 
					AND date >= CURRENT_DATE()
				')['cnt'] > 0 */
			if(true){
					$random = db_multi_query('SELECT id, name, lastname, image FROM `'.DB_PREFIX.'_users` WHERE del = 0 AND FIND_IN_SET(4, group_ids) AND image != \'\' ORDER BY RAND()');
					//$res['cnt'] = '<div class="welcome-chat cnt">
					//	<div class="dialog-message random-message">
					//		<div class="dialog-img">
					//			'.(
					//				$random['image'] ? '<img src="/uploads/images/users/'.$random['id'].'/'.$random['image'].'" class="imImg">' : '<span class="fa fa-user-secret imImg"></span>'
					//			).'
					//		</div>
					//		<div class="dialog-combi">
					//			<div class="dm-title">
					//				<span class="name">'.$random['name'].' '.$random['lastname'].'</span>
					//			</div>
					//			<div class="dm-content">Hi welcome to Your Company how may I be of assistance?
					//			</div>
					//		</div>
					//	</div>
					//	<input type="email" id="chat_email" placeholder="Enter email and click Start..." onkeypress="if(event.keyCode == 13){Chat.auth(); return false;}">
					//	<span class="fa fa-paper-plane chat-start" onclick="Chat.auth();"></span>
					//</div>';
					
					$res['name'] = 'Welcome!';
					
					$res['cnt'] = '<div class="chat-manager">
						<div><img src="/uploads/images/users/'.$random['id'].'/thumb_'.$random['image'].'"></div>
						<div>Hello, my name is '.$random['name'].', and I will answer any of your questions.</div>
					</div>
					<form class="chat-welcome" onsubmit="Chat.auth(event);">
						<input type="text" name="name" placeholder="Enter your name" required>
						<input type="email" name="email" placeholder="Enter your email" required>
						<input type="tel" name="phone" placeholder="Enter your phone">
						<button class="btn">Start dialog</button>
					</form>';
					/*						<div class="flRight">
							<button type="button" class="btn btnChat" onclick="Chat.auth();">Start</button>
						</div>*/
					if(!$user OR ($user && in_to_array(5, $user['group_ids']))){
/* 						send_push('chat_guest', [
							'type' => 'chat_guest_visit',
							'id' => $_SESSION['guest_uniqid'],
							'name' => 'Computer',
							'lastname' => 'Answers',
							'arguments' => [
								'chat_support' => get_token('chat_support1')
							]
						]); */
					}
					$res['step'] = 2;
				} else {
					$res['cnt'] = '<div class="email-chat cnt">
						<p>Let us know if you need any help! We respond super fast :)</p>
						<div class="chatForm">
							<span class="fa fa-at"></span>
							<input type="email" name="email" placeholder="Enter your email...">
						</div>
						<div class="chatForm">
							<span class="fa fa-envelope"></span>
							<textarea name="message" onkeypress="Chat.textarea(event)" onmousedown="Chat.mobScroll()" placeholder="Enter your message..."></textarea>
						</div>
						<div class="flRight">
							<button type="button" class="btn btnChat">Start</button>
						</div>
					</div>';
					$res['step'] = 1;
				}
		}
		echo json_encode($res);
	break;
}
die;
?>