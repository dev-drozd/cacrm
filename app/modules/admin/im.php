<?php
/**
 * @appointment IM admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$demail = array_filter($config['emails'], function($a) {
	if ($a['read'] == 1)
		return $a;
});
$k = array_keys($demail)[0];
$login = $demail[$k]['login'];
$password = $demail[$k]['password'];

function outputMsg($a){
	return preg_replace(	
		[
			"~(?:https?\:\/\/|)(?:www\.|)(?:youtube\.com|youtu\.be)\/(?:embed\/|v\/|watch\?v=|)(.{11})((&|\?)*[\S]*[\s]?)?~",
			"~((?:ht|f)tps?)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~",
			"~(Purchase|RMA|Issue|Bug|Invoice|Stock|User|Camera|Cash)\s\#([0-9]+)\,?~i"
		], [
			'<div class="youtube" onmousedown="Im.getYoutube(this, \'$1\')">
				<img width="640" height="480" src="//img.youtube.com/vi/$1/sddefault.jpg">
				<span class="fa fa-youtube-play"></span>
			</div>',
			'<a href="$1://$2" target="_blank">$1://$2</a>$3',
			'<div class="tooltip" onmousemove="Im.tooltip(this, \'$1\', $2);"><a href="javascript:Im.tagLink(\'$1\', $2);">$1 #$2</a><div></div></div>'
		], str_replace(
			[
				"\n",
				":)",
				":("
			], [
				'<br />',
				'<img src="/uploads/smiles/happy.png">',
				'<img src="/uploads/smiles/sad.png">'
			], htmlspecialchars($a, ENT_HTML5)
		)
	);
}

if($user['im'] > 0){
	switch($route[1]){
		
		case 'send_group':
			is_ajax() or die('hacking');
			$ids = ids_filter($_POST['ids']);
			db_query('INSERT INTO `'.DB_PREFIX.'_im` SET
				from_uid = '.$user['id'].',
				name = \''.text_filter($_POST['name']).'\',
				for_uids = \''.$ids.'\',
				for_msg = for_msg+1'
			);
			echo json_encode([
				'id' => (int)mysqli_insert_id($db_link),
				'name' => text_out_filter($_POST['name'])
			]);
			die;
		break;

		case 'send_push':
			is_ajax() or die('hacking');
			die;
		break;
		
		case 'delTree':
			is_ajax() or die('hacking');
			
			if (!$user['email_delete'])
				die('ERR');
					
			$chat_id = text_filter($_POST['id'], 500, false);
			$info = explode(':|:', str_replace('::', '.', $chat_id));
			$subject = trim(preg_replace("/Re\:|re\:|RE\:|Fwd\:|fwd\:|FWD\:/i", '', $info[1]));
			
			/* $demail = array_filter($config['emails'], function($a) {
			if ($a['default'])
					return $a;
			});

			$login = $demail[0]['login'];
			$password = $demail[0]['password']; */
	
			if($chat_id){
				
				$imap = imap_open('{localhost:143/novalidate-cert}INBOX', $login, $password); 
				if (is_array($results = imap_search($imap, 'SUBJECT "'.$subject.'" FROM "'.$info[0].'"'))) {
					foreach($results as $r) {
						imap_delete($imap, $r);
					}
				}
				imap_expunge($imap);
				
				imap_reopen($imap, '{localhost:143/novalidate-cert}Sent'); 
				if (is_array($results2 = imap_search($imap, 'SUBJECT "'.$subject.'" TO "'.$info[0].'"'))) {
					foreach($results2 as $r) {
						imap_delete($imap, $r);
					}
				}
				
				imap_expunge($imap);
				imap_close($imap);
			
				die('OK');
			} else
				die('ERR');
		break;
		
		case 'config_emails';
			is_ajax() or die('hacking');
			
			$emails = '';
			if ($config['emails']) {
				foreach($config['emails'] as $e) {
					$emails .= '<option value="'.$e['login'].'">'.$e['login'].'</option>';
				}
			}
			echo $emails;
			die;
		break;
		
		case 'del':
			is_ajax() or die('hacking');
			$id = intval($_POST['id']);
			db_query('UPDATE `'.DB_PREFIX.'_messages` SET del = 1 WHERE id = '.$id.' AND (
				from_uid = '.$user['id'].'
			)');
			send_push(intval($_POST['sid']), [
				'type' => 'del_message',
				'id' => $id
			]);
			sendPush(intval($_POST['sid']), '', '', [
				'type' => 'del_message',
				'id' => $id
			]);
			echo 'OK';
			die;
		break;
		
		case 'undo':
			is_ajax() or die('hacking');
			$id = (int)$_POST['id'];
			if($row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_messages` 
			WHERE id = '.$id.' AND from_uid = '.$user['id'].'')){
				db_query('UPDATE `'.DB_PREFIX.'_messages` SET del = 0 WHERE id = '.$id.' AND (
					from_uid = '.$user['id'].'
				)');
				$attach_images = '';
				$attach_files = '';
				if($row['attaches']){
					$attach_images = '<div class="thumbnails">';
					$attach_files = '<ul class="files">';
					foreach(explode('|:|', $row['attaches']) as $file){
						if(preg_match("/(.*).(jpeg|jpg|png|gif)/i", $file)){
							$attach_images .= '<div class="thumb">
									<img src="/uploads/attaches/'.$row['for_uid'].'/thumb_'.$file.'" onclick="showPhoto(this.src);">
								</div>';
						} else {
							$attach_files .= '<li>
									<a href="/uploads/attaches/'.$row['for_uid'].'/'.$file.'" download>
										<span class="fa fa-file"></span> '.$file.'
									</a>
								</li>';
						}
					}	
					$attach_images .= '</div>';
					$attach_files .= '</ul>';
				}
				$res = [
					'err' => 0,
					'msg' => outputMsg($row['message']).$attach_images.$attach_files,
					'id' => $id
				];
				$data = $res;
				$data['type'] = 'undo_message';
				send_push((int)$_POST['sid'], $data);
				unset($data);
			} else
				$res = ['err' => 1];
			
			echo json_encode($res, JSON_UNESCAPED_UNICODE);
			die;
		break;
		
		case 'send':
			is_ajax() or die('hacking');
			$id = (int)$_POST['id'];
			$gid = 0;
			if(preg_match('/G\-([0-9]+)/i', $_POST['id'], $match))
				$gid = $match[1];
			
			$all = (int)$_POST['all'];
			$msg = text_filter($_POST['message'], 16000);
			$attach = '';
			if($msg OR $_FILES['images'] OR $_FILES['files']){
				
				$attaches = [];
				
				// Upload max file size
				$max_size = 50;
				
				// path
				$dir = ROOT_DIR.'/uploads/attaches/';
				
				// Is not dir
				if(!is_dir($dir.$id)){
					@mkdir($dir.$id, 0777);
					@chmod($dir.$id, 0777);
				}
				
				$dir = $dir.$id.'/';
				
				if($_FILES['images']){
					$attach .= '<div class="thumbnails">';
					
					foreach($_FILES["images"]["error"] as $key => $error){
						
						// temp file
						$tmp = $_FILES['images']['tmp_name'][$key];
						
						$type = $_FILES['images']['name'][$key] == 'blob' ? str_ireplace(
							'image/', '', $_FILES['images']['type'][$key]
						) : mb_strtolower(pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION));
						
						// Check
						if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array(
							$type, ['jpeg', 'jpg', 'png', 'gif']
						)){
							echo 'err_image_type';
							die;
						}
						if($_FILES['images']['size'][$key] >= 1024*$max_size*1024){
							echo 'err_file_size';
							die;
						}
						
						// New name
						$rename = uniqid('', true).'.'.$type;
						
						// Upload image
						if(move_uploaded_file($tmp, $dir.$rename)){
							
							$img = new Imagick($dir.$rename);
							if($img->getImageWidth() > 1920){
								$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
							}
							auto_rotate_image($img);
							$img->stripImage();
							$img->writeImage($dir.$rename);
							
							$img->cropThumbnailImage(300, 300);
							$img->stripImage();
							$img->writeImage($dir.'preview_'.$rename);
							
							$img->cropThumbnailImage(94, 94);
							$img->stripImage();
							$img->writeImage($dir.'thumb_'.$rename);
							$img->destroy();
							
							$attaches[] = $rename;
							
							$attach .= '<div class="thumb">
								<img src="/uploads/attaches/'.$id.'/thumb_'.$rename.'" onclick="showPhoto(this.src);">
							</div>';
						}
					}
					$attach .= '</div>';
				}
				if($_FILES['files']){
					$attach .= '<ul class="files">';
					foreach($_FILES["files"]["error"] as $key => $error){
						
						// temp file
						$tmp = $_FILES['files']['tmp_name'][$key];
						
						$type = mb_strtolower(pathinfo($_FILES['files']['name'][$key], PATHINFO_EXTENSION));
						
						// Check
						if(!in_array((explode('/', $_FILES['files']['type'][$key])[1] OR $type), [
							'apk',
							'png',
							'jpg',
							'gif',
							'mp3',
							'vnd.openxmlformats-officedocument.wordprocessingml.document',
							'msword',
							'vnd.ms-excel',
							'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
							'vnd.ms-powerpoint',
							'vnd.openxmlformats-officedocument.presentationml.presentation',
							'rtf',
							'pdf',
							'vnd.adobe.photoshop',
							'vnd.djvu',
							'fb2',
							'ps',
							'jpeg',
							'plain',
							'csv',
							'vnd.android.package-archive'
						])){
							echo 'err_file_type';
							die;
						}
						if($_FILES['files']['size'][$key] >= 1024*$max_size*1024){
							echo 'err_file_size';
							die;
						}
						
						// New name
						$rename = uniqid('', true).'.'.$type;
						
						// Upload image
						if(move_uploaded_file($tmp, $dir.$rename)){
							$attaches[] = $rename;
							$attach .= '<li>
									<a href="/uploads/attaches/'.$id.'/'.$rename.'" download>
										<span class="fa fa-file"></span> '.$rename.'
									</a>
								</li>';
						}
					}
					$attach .= '</ul>';
				}
				
				if($id == 0 && $all && in_array(
					1, explode(
						',', $user['group_ids']
					)
				)){
					foreach(db_multi_query('SELECT id FROM `'.DB_PREFIX.'_users` WHERE NOT FIND_IN_SET(5, group_ids) AND del = 0 AND id != '.$user['id'], true) as $row){
						$im_where = 'WHERE (
							from_uid = '.$user['id'].' OR for_uid = '.$user['id'].'
						) AND (
							from_uid = '.$row['id'].' OR for_uid = '.$row['id'].'
						)';
						if($im = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_im` '.$im_where)){
							db_query('UPDATE `'.DB_PREFIX.'_im` SET
								'.($im['from_uid'] == $user['id'] ? 'for_msg = for_msg+1' : 'from_msg = from_msg+1').' '.$im_where
							);
						} else {
							db_query('INSERT INTO `'.DB_PREFIX.'_im` SET
								from_uid = '.$user['id'].',
								for_uid = '.$row['id'].',
								for_msg = for_msg+1,
								date = \''.date('Y-m-d H:i:s', time()).'\''
							);
						}
						db_query('UPDATE `'.DB_PREFIX.'_users` SET new_msg = new_msg+1 WHERE id = '.$row['id']);
						db_query('INSERT INTO `'.DB_PREFIX.'_messages` SET
							from_uid = '.$user['id'].',
							for_uid = '.$row['id'].',
							ind = 1,
							attaches = \''.implode('|:|', $attaches).'\',
							message = \''.$msg.'\''
						);
						send_push($row['id'], [
							'type' => 'messages',
							'id' => (int)mysqli_insert_id($db_link),
							'uid' => $user['id'],
							'sid' => $user['id'],
							'image' => $user['image'],
							'name' => $user['uname'],
							'lastname' => $user['ulastname'],
							'message' => outputMsg(stripcslashes($msg)).$attach,
							'date' => date("Y-m-d H:i:s")
						]);
					}
				}
				if($id > 0 && !$gid){
					$im_where = 'WHERE (
						from_uid = '.$user['id'].' OR for_uid = '.$user['id'].'
					) AND (
						from_uid = '.$id.' OR for_uid = '.$id.'
					)';
					if($im = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_im` '.$im_where)){
						db_query('UPDATE `'.DB_PREFIX.'_im` SET
							'.($im['from_uid'] == $user['id'] ? 'for_msg = for_msg+1' : 'from_msg = from_msg+1').' '.$im_where
						);
					} else {
						db_query('INSERT INTO `'.DB_PREFIX.'_im` SET
							from_uid = '.$user['id'].',
							for_uid = '.$id.',
							for_msg = for_msg+1'
						);
					}
					db_query('UPDATE `'.DB_PREFIX.'_users` SET new_msg = new_msg+1 WHERE id = '.$id);
				}
				db_query('INSERT INTO `'.DB_PREFIX.'_messages` SET
					from_uid = '.$user['id'].',
					'.($gid ? 'sid = '.$gid : 'for_uid = '.$id).',
					viewed = '.($id > 0 ? '0' : '1').',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					attaches = \''.implode('|:|', $attaches).'\',
					message = \''.$msg.'\''
				);
				$output = [
					'type' => 'messages',
					'id' => (int)mysqli_insert_id($db_link),
					'uid' => $user['id'],
					'sid' => $id ? $user['id'] : 'all',
					'image' => $user['image'],
					'name' => $user['uname'],
					'lastname' => $user['ulastname'],
					'message' => outputMsg(stripcslashes($msg)).$attach,
					'date' => date("Y-m-d H:i:s")
				];
				if(!$gid){
					send_push($id, $output);
					sendPush($id, $user['uname'].' '.$user['ulastname'], substr($msg, 0, 50),$output);
				}
				$output['my'] = true;
				echo json_encode([
					'err' => 0,
					'message' => $output
				], JSON_UNESCAPED_UNICODE);
			}
			die;
		break;
		
		case 'viewed':
			is_ajax() or die('hacking');
				if($id = (int)$_POST['sid']){
					db_query('
						UPDATE `'.DB_PREFIX.'_messages`
						SET viewed = 1
						WHERE from_uid = '.$id.'
						AND for_uid = '.$user['id'].'
						AND viewed = 0'
					);
					send_push($id, [
						'type' => 'viewed_messages',
						'uid' => $user['id']
					]);	
					sendPush($id, '', '', [
						'type' => 'viewed_messages',
						'uid' => $user['id']
					]);
				}
			die;
		break;
		
		case 'history':
			is_ajax() or die('hacking');
			
			$id = intval($_POST['id']);
			$gid = 0;
			if(preg_match('/G\-([0-9]+)/i', $_POST['id'], $match))
				$gid = $match[1];
			
			if($id > 0){
				@db_query('UPDATE `'.DB_PREFIX.'_im` i INNER JOIN `'.DB_PREFIX.'_users` u ON u.id = IF(
					i.for_uid = '.$user['id'].', i.for_uid, i.from_uid
				) SET  u.new_msg = u.new_msg-IF(
					i.from_uid = '.$user['id'].', i.from_msg, i.for_msg
				), i.for_msg = IF(
					i.for_uid = '.$user['id'].', 0, i.for_msg
				), i.from_msg = IF(
					i.from_uid = '.$user['id'].', 0, i.from_msg
				) WHERE (
					i.from_uid = '.$user['id'].' OR i.for_uid = '.$user['id'].'
				) AND (
					i.from_uid = '.$id.' OR i.for_uid = '.$id.'
				)');
				db_query('
					UPDATE `'.DB_PREFIX.'_messages`
					SET viewed = 1
					WHERE from_uid = '.$id.'
					AND for_uid = '.$user['id'].'
					AND viewed = 0'
				);
				send_push($id, [
					'type' => 'viewed_messages',
					'uid' => $user['id']
				]);
				sendPush($id, '', '', [
					'type' => 'viewed_messages',
					'uid' => $user['id']
				]);
			}
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 20;
			$where = $id > 0 ? ' (
				m.for_uid = '.$user['id'].' AND m.from_uid = '.$id.'
			) OR (
				m.from_uid = '.$user['id'].' AND m.for_uid = '.$id.'
			)' : (
				$gid > 0 ? 'm.sid = '.$gid : 'm.for_uid = 0 AND m.sid = 0'
			);
			if($messages = array_reverse(db_multi_query('
				SELECT m.*, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_messages` m
				INNER JOIN  `'.DB_PREFIX.'_users` u
					ON m.from_uid = u.id
				WHERE '.$where.($query ? ' AND m.message LIKE \'*'.$query.'*\' ' : '').'
				ORDER BY m.date DESC LIMIT '.($page*$count).', '.$count, true))){
				$i = 0;
				$fid = 0;
				foreach($messages as $row){
					$attach_images = '';
					$attach_files = '';
					if($row['attaches']){
						$attach_images = '<div class="thumbnails">';
						$attach_files = '<ul class="files">';
						foreach(explode('|:|', $row['attaches']) as $file){
							if(preg_match("/(.*).(jpeg|jpg|png|gif)/i", $file)){
								$attach_images .= '<div class="thumb">
										<img src="/uploads/attaches/'.($row['ind'] ? 0 : $row['for_uid']).'/thumb_'.$file.'" onclick="showPhoto(this.src);">
									</div>';
							} else {
								$attach_files .= '<li>
										<a href="/uploads/attaches/'.($row['ind'] ? 0 : $row['for_uid']).'/'.$file.'" download>
											<span class="fa fa-file"></span> '.$file.'
										</a>
									</li>';
							}
						}	
						$attach_images .= '</div>';
						$attach_files .= '</ul>';
					}
					tpl_set('im/message', [
						'id' => $row['id'],
						'uid' => $row['from_uid'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'message' => $row['del'] ? '<span class="fa fa-trash imDeleted"></span> '.$lang['MessageDeleted'] : outputMsg($row['message']).$attach_images.$attach_files,
						'date' => $row['date']
					], [
						'first' => ($fid !== $row['from_uid']),
						'del' => $row['del'],
						'new' => (!$row['viewed'] && $row['from_uid'] == $user['id']),
						'my' => ($row['from_uid'] == $user['id']),
						'ava' => $row['image']
					], 'messages');
					$i++;
					$fid = $row['from_uid'];
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['messages'] ?? '<div class="imNoCont">'.$lang['NoChats'].'</div>'
			]));
		break;
		
		case 'tab':
			is_ajax() or die('hacking');
			$page = (int)$_POST['page'];
			$support = intval($_POST['support']);
			$count = 20;
			if($support == 1){
				$query = text_filter(str_replace('#', '', $_POST['query']), 255, false);
				$i = 0;
				foreach(db_multi_query('
					SELECT * FROM `'.DB_PREFIX.'_chat_im`'.(
						$query ? ' WHERE MATCH(email) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
					).' ORDER BY date DESC LIMIT '.($page*$count).', '.$count, true
				) as $im){
					tpl_set('im/user', [
						'id' => $im['id'],
						'name' => 'Guest #'.$im['id'],
						'msg' => '',
						'lastname' => $im['email'],
						'ava' => '',
						'class' => ''
					], [
						'online' => false,
						'ava' => false,
						'msg-text' => false,
						'email' => false
					], 'dialogues');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				$left_count = intval(($res_count-($page*$count)-$i));
				$res = ['im' => [
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['dialogues'],
				]];
			} elseif ($support == 2) {
				if ($user['email_receive']) {
					//require_once('app/imap.php');
					$query = text_filter(str_replace('#', '', $_POST['query']), 255, false);
					
					/* $imap_driver = new imap_driver();
					if ($imap_driver->init('localhost', 143) === false) {
						echo "init() failed: " . $imap_driver->error . "\n";
						exit;
					} else {
						if ($imap_driver->login($login, $password) === false) {
							echo "login() failed: " . $imap_driver->error . "\n";
							exit;
						} else {
							if ($imap_driver->select_folder("INBOX") === false) {
								echo "select_folder() failed: " . $imap_driver->error . "\n";
								return false;
							} else {
								$ids = $imap_driver->get_uids_by_search('SINCE ' . date('j-M-Y', time() - 60 * 60 * 24 * 10));
								if ($ids === false) {
									echo "get_uids_failed: " . $imap_driver->error . "\n";
									exit;
								} else {
									$emails = [];
									foreach($ids as $id) {
										if (($headers = $imap_driver->get_headers_from_uid($id)) === false) {
											echo "get_headers_by_uid() failed: " . $imap_driver->error . "\n";
											return false;
										} else {
											preg_match('/(.*?)<(.*@.*)>/i', $headers['from'], $email);
											if ($email[2] AND !in_array($email[2], array_keys($emails))) {
												$emails[$email[2]] = [
													$email[1],
													$headers['subject']
												];
											}
										}
									}
									
									if ($emails) {
										foreach($emails as $k => $e) {
											tpl_set('im/user', [
												'id' => str_replace('.', '::', $k.':|:'.$e[1]),
												'name' => $k,
												'msg' => '',
												'msg-text' => $e[1],
												'lastname' => ($e[0] ? ' '.$e[0] : ''),
												'ava' => '',
												'class' => 'email-user'
											], [
												'online' => false,
												'ava' => false,
												'msg-text' => true
											], 'dialogues');
										}
									}
								}
							}
						}
					} */
					
					if($auth = imap_open("{localhost:143/novalidate-cert}INBOX", $login, $password)){
						$threads = imap_thread($auth);
						$i = null;
						$new = 0;
						foreach ($threads as $key => $val) {
							$tree = explode('.', $key);
							$header = imap_headerinfo($auth, $val);
							if ($tree[1] == 'num' AND ($header->Unseen == 'U' OR $header->Recent == 'N'))
								$new ++;
							if ($tree[1] == 'num' AND empty($i)) {
							  $i = $tree[0];
							  if ($header->from[0]->mailbox) {
								  tpl_set('im/user', [
										'id' => str_replace('.', '::', $header->from[0]->mailbox.'@'.$header->from[0]->host.':|:'.$header->subject),
										'name' => $header->from[0]->mailbox.'@'.$header->from[0]->host,
										'msg' => $new > 0 ? '<span class="imCount">'.$new.'</span>' : '',
										'msg-text' => mb_convert_encoding(imap_utf8($header->subject), 'UTF-8'),
										'lastname' => mb_convert_encoding(imap_utf8($header->from[0]->personal), 'UTF-8'),
										'ava' => '',
										'class' => 'email-user'
									], [
										'online' => false,
										'ava' => false,
										'msg-text' => true,
										'email' => $user['email_delete']
									], 'dialogues');
							  }
							} elseif ($key == $i.'.branch') {
							  $i = null;
							  $new = 0;
							}
						}
						imap_close($auth);
					} else
						echo 'connection error';
					
					/* if($auth = imap_open("{localhost:143}Sent", $login, $password)){
						$threads = imap_thread($auth);
						$i = null;
						$new = 0;
						foreach ($threads as $key => $val) {
							$tree = explode('.', $key);
							$header = imap_headerinfo($auth, $val);
							if ($tree[1] == 'num' AND ($header->Unseen == 'U' OR $header->Recent == 'N'))
								$new ++;
							if ($tree[1] == 'num' AND empty($i)) {
							  $i = $tree[0];
							  if ($header->to[0]->mailbox) {
								  tpl_set('im/user', [
										'id' => str_replace('.', '::', $header->to[0]->mailbox.'@'.$header->to[0]->host.':|:'.$header->subject),
										'name' => $header->to[0]->mailbox.'@'.$header->to[0]->host,
										'msg' => $new > 0 ? '<span class="imCount">'.$new.'</span>' : '',
										'msg-text' => imap_utf8($header->subject),
										'lastname' => imap_utf8($header->to[0]->personal),
										'ava' => '',
										'class' => 'email-user'
									], [
										'online' => false,
										'ava' => false,
										'msg-text' => true
									], 'dialogues');
							  }
							} elseif ($key == $i.'.branch') {
							  $i = null;
							  $new = 0;
							}
						}
						imap_close($auth);
					} else
						echo 'connection error'; */

					$res = ['im' => [
						'res_count' => count($emails),
						'left_count' => 0,
						'new_email' => $user['email_new'],
						'content' => mb_convert_encoding($tpl_content['dialogues'], 'UTF-8'),
					]];
				} else {
					$res = ['im' => [
						'res_count' => 0,
						'left_count' => 0,
						'content' => 'You have no access to this action',
					]];
				}
			} else {
				if($users = db_multi_query('SELECT u.id, u.name, u.lastname, u.image, IF(
					i.from_uid = '.$user['id'].', IF(
						i.from_msg, i.from_msg, 0
					), IF(
						i.for_msg, i.for_msg, 0
					)
				) as msg
					FROM `'.DB_PREFIX.'_users` u LEFT JOIN `'.DB_PREFIX.'_im` i ON (
						i.for_uid = u.id AND i.from_uid = '.$user['id'].'
					) OR (
						i.from_uid = u.id AND i.for_uid = '.$user['id'].'
					)
					WHERE NOT FIND_IN_SET(5, u.group_ids) AND u.del = 0
					AND u.id != '.$user['id'].' AND u.del = 0
					'.($query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : '').'
					ORDER BY i.date DESC LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($users as $row){
						tpl_set('im/user', [
							'id' => $row['id'],
							'name' => $row['name'],
							'msg' => $row['msg'] > 0 ? '<span class="imCount">'.$row['msg'].'</span>' : '',
							'lastname' => $row['lastname'],
							'ava' => $row['image'],
							'class' => ''
						], [
							'online' => false,
							'ava' => $row['image'],
							'msg-text' => false,
							'email' => false
						], 'dialogues');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				$res = ['im' => [
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['dialogues'],
				]];
			} 
			echo json_encode($res);
			die;
		break;
		
		case 'emails':
			$page = intval($_POST['page']);
			$count = 20;
			/* $demail = array_filter($config['emails'], function($a) {
				if ($a['default'])
					return $a;
			});
			$login = $demail[0]['login'];
			$password = $demail[0]['password']; */
			
			function outputMsgMail($a){
				$a = is_imap_base64($a) ? imap_base64($a) : $a;
				return $a;
				//return trim(quoted_printable_decode($a), "\x7f..\xff\x0..\x1f");
				//return str_replace("\n", '<br />', trim(quoted_printable_decode($a), "\x7f..\xff\x0..\x1f"));
				return ''.preg_replace(	
					[
						"~(?:https?\:\/\/|)(?:www\.|)(?:youtube\.com|youtu\.be)\/(?:embed\/|v\/|watch\?v=|)(.{11})((&|\?)*[\S]*[\s]?)?~",
						"~((?:ht|f)tps?)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~",
					], [
						'<div class="youtube" onmousedown="Im.getYoutube(this, \'$1\')">
							<img width="640" height="480" src="//img.youtube.com/vi/$1/sddefault.jpg">
							<span class="fa fa-youtube-play"></span>
						</div>',
						'<a href="$1://$2" target="_blank">$1://$2</a>$3'
					], str_replace("\n", '<br />', trim(quoted_printable_decode($a), "\x7f..\xff\x0..\x1f")
					)
				).'';
			}
			switch($route[2]){
				
				case 'send':
					is_ajax() or die('hacking');
					ignore_user_abort(true);
					$id = explode(':|:', str_replace('::', '.', str_replace('%7C', '|', text_filter(urldecode($_POST['id']), 500, false))));
					$new = intval($_POST['new']);
					
					if (($new AND !$user['email_new']) OR (!$new AND !$user['email_answer'])) 
						die(json_encode(['err' => 'no_acc']));
					
					$msg = text_filter($_POST['message'], 16000);
					$subject = trim(preg_replace("/Re\:|re\:|RE\:|Fwd\:|fwd\:|FWD\:/i", '', $id[1]));
					$email = text_filter($_POST['from'], 50, false);
					/* $demail = array_filter($config['emails'], function($a) {
					if ($a['default'])
							return $a;
					});

					$login = $demail[0]['login'];
					$password = $demail[0]['password']; */
					
					//mail ($id[0], 'Re: '.$id[1], $msg, 'Content-type: text/html; charset=utf-8'."\r\n".'FROM: '.$login);

					// Headers
					$headers  = 'MIME-Version: 1.0'."\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
					$headers .= 'To: '.$id[0]. "\r\n";
					$headers .= 'From: Your Company <'.($email ?: $login).'>' . "\r\n";
										
					mail($id[0], 'Re: '.$id[1], '<!DOCTYPE html>
					<html lang="en">
					<head>
						<meta charset="UTF-8">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<title>New user on Your Company</title>
					</head>
					<body style="background: #f6f6f6; text-align: center;">
						<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
							<div style="margin: -20px -20px 0; padding: 20px;">
								<a href="http://yoursite.com/">
									<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
								</a>
							</div>
							<div style="padding: 0 30px 30px;">
								'.$msg.'
							</div>
						</div>
					</body>
					</html>', $headers);
				
					$auth = imap_open("{localhost:143/novalidate-cert}INBOX", $login, $password,NULL,1,array('DISABLE_AUTHENTICATOR' => array('GSSAPI','NTLM')));
					imap_append($auth, "{localhost:143/novalidate-cert}Sent"
					   , "From: ".$login."\r\n"
					   . "Date: ".date('Y-m-d H:i:s', time())."\r\n"
					   . "To: ".$id[0]."\r\n"
					   . "Subject: Re: ".$id[1]."\r\n"
					   . "\r\n"
					   . $msg."\r\n"
					   );
					
					echo json_encode([
						'err' => 0,
						'message' => [
							'type' => 'messages',
							'id' => '',
							'uid' => '',
							'sid' => $id[0],
							'image' => '',
							'name' => $login,
							'lastname' => '',
							'message' => outputMsg(stripcslashes($msg)),
							'my' => true,
							'date' => date("Y-m-d H:i:s")
						]
					], JSON_UNESCAPED_UNICODE);
					die;
				break;
				
				case 'history':
					if ($user['email_receive']) {
						is_ajax() or die('hacking');
						$page = intval($_POST['page']);
						$chat_id = text_filter($_POST['id'], 500, false);
						$fid = '';
						$info = explode(':|:', str_replace('::', '.', $chat_id));
						$subject = trim(preg_replace("/Re\:|re\:|RE\:|Fwd\:|fwd\:|FWD\:/i", '', $info[1]));
						$threads = [];
						
						if ($page)
							die;
										
						if($chat_id){
							
							$imap = imap_open('{localhost:143/novalidate-cert}INBOX', $login, $password); 
							$subject = trim(preg_replace("/Re\:|re\:|RE\:|Fwd\:|fwd\:|FWD\:/i", '', $info[1]));
							$threads = array(); 
							
							$results = imap_search($imap, 'SUBJECT "'.$subject.'" FROM "'.$info[0].'"');
							if(is_array($results)) { 
								$emails = imap_fetch_overview($imap, implode(',', $results)); 

								foreach ($emails as $email) { 
									$structure = imap_fetchstructure($imap, $email->msgno);
									
									$threads[strtotime($email->date)] = [];
									$threads[strtotime($email->date)]['info'] = $email; 
									$text = imap_fetchbody($imap,$email->msgno, 1); 

									switch ($structure->encoding) {
										case 0:
											$threads[strtotime($email->date)]['body'] = $text;
										break;

										case 1:
											$threads[strtotime($email->date)]['body'] = quoted_printable_decode(imap_8bit(imap_fetchbody($imap,$email->msgno, 1)));
										break;
											
										case 2:
											$threads[strtotime($email->date)]['body'] = imap_binary($text);
										break;
											
										case 3:
											$threads[strtotime($email->date)]['body'] = base64_decode(imap_fetchbody($imap,$email->msgno, 1));
										break;

										case 4:
											$threads[strtotime($email->date)]['body'] = quoted_printable_decode(imap_fetchbody($imap,$email->msgno, 1));
										break;

										case 5:
										default:
											$threads[strtotime($email->date)]['body'] = imap_qprint($text);
										break;
									}
								}    
							} 
							
							
							imap_reopen($imap, '{localhost:143/novalidate-cert}Sent'); 

							if(is_array($results = imap_search($imap, 'SUBJECT "'.$subject.'" TO "'.$info[0].'"'))) { 
								$emails = imap_fetch_overview($imap, implode(',', $results)); 

								foreach ($emails as $email) { 
									/* $threads[strtotime($email->date)]['info'] = $email; 
									$threads[strtotime($email->date)]['body'] = imap_fetchbody($imap,$email->uid,1);  */
									
									$structure = imap_fetchstructure($imap, $email->msgno);
									$threads[strtotime($email->date)]['info'] = $email; 
									$text = imap_fetchbody($imap,$email->msgno,1);

									switch ($structure->encoding) {
										case 0:
											$threads[strtotime($email->date)]['body'] = $text;
										break;

										case 1:
											$threads[strtotime($email->date)]['body'] = quoted_printable_decode(imap_8bit(imap_fetchbody($imap,$email->msgno, 1)));
										break;
											
										case 2:
											$threads[strtotime($email->date)]['body'] = imap_binary($text);
										break;
											
										case 3:
											$threads[strtotime($email->date)]['body'] = base64_decode(imap_fetchbody($imap,$email->msgno, 1));
										break;

										case 4:
											$threads[strtotime($email->date)]['body'] = quoted_printable_decode(imap_fetchbody($imap,$email->msgno, 1));
										break;

										case 5:
										default:
											$threads[strtotime($email->date)]['body'] = imap_qprint($text);
										break;
									}
								}    
							} 

							ksort($threads); 
							foreach($threads as $msg){
								tpl_set('im/message', [
									'id' => $msg['info']->msgno,
									'uid' => '',
									'name' => preg_replace('[<>]', '', imap_utf8($msg['info']->from)),
									'lastname' => '',
									'image' => '',
									'message' => outputMsgMail($msg['body']),
									'date' => date('Y-m-d H:i:s', strtotime($msg['info']->date))
								], [
									'first' => $fid != $msg['info']->from,
									'del' => false,
									'new' => (!$msg['info']->seen && $msg['info']->from == $login),
									'my' => $msg['info']->from == $login,
									'ava' => false
								], 'messages');	
								$fid = $msg['info']->from;
							}
							
							
							exit(json_encode([
								'res_count' => count($threads),
								'left_count' => 0,
								'content' => mb_convert_encoding($tpl_content['messages'] ?? '<div class="imNoCont">'.$lang['NoChats'].'</div>', 'UTF-8')
							]));
						}
					} else {
						exit(json_encode([
							'res_count' => 0,
							'left_count' => 0,
							'content' => 'You have no access to this action'
						]));
					}
				break;
				
				case 'del':
					if (!$user['email_delete'])
						die('ERR');
					
					$id = intval($_POST['id']);
					
					$mbox = imap_open("{localhost:143/novalidate-cert}Sent", $login, $password);
					imap_delete($mbox, $id);
					imap_expunge($mbox);
					imap_close($mbox);
					
					die('OK');
				break;
				
				default:
					if ($user['email_receive']) {
						$chat_id = text_filter($route[2], 500, false);
						$query = text_filter(str_replace('#', '', $_POST['query']), 255, false);
						
						if($auth = imap_open("{localhost:143/novalidate-cert}INBOX", $login, $password)){
							$threads = imap_thread($auth);
							$i = null;
							foreach ($threads as $key => $val) {
								$tree = explode('.', $key);
								if ($tree[1] == 'num' AND empty($i)) {
								  $i = $tree[0];
								  $header = imap_headerinfo($auth, $val);
								  if ($header->from[0]->mailbox) {
									  if (!$query OR ($query AND strpos($header->from[0]->mailbox.'@'.$header->from[0]->host, $query) !== false)) {
										  tpl_set('im/user', [
												'id' => str_replace('.', '::', $header->from[0]->mailbox.'@'.$header->from[0]->host.':|:'.$header->subject),
												'name' => $header->from[0]->mailbox.'@'.$header->from[0]->host,
												'msg' => '',
												'msg-text' => imap_utf8($header->subject),
												'lastname' => imap_utf8($header->from[0]->personal),
												'ava' => '',
												'class' => 'email-user'
											], [
												'online' => false,
												'ava' => false,
												'msg-text' => true,
												'email' => $user['email_delete']
											], 'dialogues');
									  }
								  }
								} elseif ($key == $i.'.branch')
								  $i = null;
							}
							imap_close($auth);
							
							if($_SERVER['REQUEST_METHOD'] == 'POST'){
								exit(json_encode([
									'res_count' => 0,
									'left_count' => 0,
									'content' => $tpl_content['dialogues'],
								]));
							}
						} else
							echo 'connection error';

						
						
						if($chat_id){
							$info = explode(':|:', str_replace('::', '.', urldecode($chat_id)));
							
							$imap = imap_open('{localhost:143/novalidate-cert}INBOX', $login, $password); 
							$subject = trim(preg_replace("/Re\:|re\:|RE\:|Fwd\:|fwd\:|FWD\:/i", '', $info[1]));
							$threads_m = array(); 
							
							$results = imap_search($imap, 'SUBJECT "'.$subject.'" FROM "'.$info[0].'"');
							if(is_array($results)) { 
								$emails = imap_fetch_overview($imap, implode(',', $results)); 
								
								foreach ($emails as $email) { 
									$structure = imap_fetchstructure($imap, $email->msgno);
									$threads_m[strtotime($email->date)]['info'] = $email; 
									$text = imap_fetchbody($imap,$email->msgno,1);

									switch ($structure->encoding) {
										case 0:
											$threads_m[strtotime($email->date)]['body'] = $text;
										break;

										case 1:
											$threads_m[strtotime($email->date)]['body'] = imap_8bit(imap_fetchbody($imap,$email->msgno, 1));
										break;
											
										case 2:
											$threads_m[strtotime($email->date)]['body'] = imap_binary($text);
										break;
											
										case 3:
											$threads_m[strtotime($email->date)]['body'] = base64_decode(imap_fetchbody($imap,$email->msgno, 1));
										break;

										case 4:
											$threads_m[strtotime($email->date)]['body'] = imap_fetchbody($imap,$email->msgno, 1);
										break;

										case 5:
										default:
											$threads_m[strtotime($email->date)]['body'] = imap_qprint($text);
										break;
									}
								}    
							} 

							imap_reopen($imap, '{localhost:143/novalidate-cert}Sent'); 

							if(is_array($results = imap_search($imap, 'SUBJECT "'.$subject.'" TO "'.$info[0].'"'))) { 
								$emails = imap_fetch_overview($imap, implode(',', $results)); 

								foreach ($emails as $email) { 
									$structure = imap_fetchstructure($imap, $email->msgno);
									$threads_m[strtotime($email->date)]['info'] = $email; 
									$text = imap_fetchbody($imap,$email->msgno,1);
									switch ($structure->encoding) {
										case 0:
											$threads_m[strtotime($email->date)]['body'] = imap_utf8($text);
										break;

										case 1:
											$threads_m[strtotime($email->date)]['body'] = imap_8bit(imap_fetchbody($imap,$email->msgno, 1));
										break;
											
										case 2:
											$threads_m[strtotime($email->date)]['body'] = imap_binary($text);
										break;
											
										case 3:
											$threads_m[strtotime($email->date)]['body'] = base64_decode(imap_fetchbody($imap,$email->msgno, 1));
										break;

										case 4:
											$threads_m[strtotime($email->date)]['body'] = imap_fetchbody($imap,$email->msgno, 1);
										break;

										case 5:
										default:
											$threads_m[strtotime($email->date)]['body'] = imap_qprint($text);
										break;
									}
								}    
							} 

							ksort($threads_m); 
							//echo '<pre>';
							foreach($threads_m as $msg){
								//print_r($msg);
								tpl_set('im/message', [
									'id' => $msg['info']->msgno,
									'uid' => '',
									'name' => preg_replace('[<>]', '', imap_utf8($msg['info']->from)),
									'lastname' => '',
									'image' => '',
									'message' => outputMsgMail($msg['body']),
									'date' => date('Y-m-d H:i:s', strtotime($msg['info']->date))
								], [
									'first' => $fid != $msg['info']->from,
									'del' => false,
									'new' => (!$msg['info']->seen && $msg['info']->from == $login),
									'my' => $msg['info']->from == $login,
									'ava' => false
								], 'messages');	
								$fid = $msg['info']->from;
							}
							//die;
						}
						
						$email_list = '';
						if ($config['emails']) {
							foreach($config['emails'] as $e) {
								$email_list .= '<option value="'.$e['login'].'"'.(
									$e['read'] ? ' selected' : ''
								).'>'.$e['login'].'</option>';
							}
						}
						tpl_set('im/main', [
							'id' => $id,
							'text' => '',
							'email-list' => $email_list,
							'dialogues' => $tpl_content['dialogues'],
							'messages' => $tpl_content['messages'] ?? '<div class="imNoCont">'.$lang['NoChats'].'</div>'
						], [
							'send-all' => false,
							'support' => false,
							'emails' => true,
							'new-email' => $user['email_new'],
							'chat-support' => $user['chat_support'],
							'email-receive' => $user['email_receive']
						], 'content');
						$meta['title'] = 'Emails';
					} else {
						tpl_set('forbidden', [
							'text' => 'You have no access to this page',
						], [], 'content');
					}
				break;
			}
		break;
		
		case 'read-email':
			is_ajax() or die('hacking');
			foreach($config['emails'] as $i => $email){
				$config['emails'][$i]['read'] = (
					$config['emails'][$i]['login'] == $_POST['email'] ? 1 : 0
				);
			}
			conf_save();
			die;
		break;
		
		case 'support':
			$page = intval($_POST['page']);
			$count = 20;
			if($route[2] == 'history'){
				is_ajax() or die('hacking');
				if($id = intval($_POST['id'])){
					$fid = 0;
					$i = 0;
					$im = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_chat_im` WHERE id = '.$id);
					$tpl_content['messages'] = $im['ip'] ? '<div class="info-chat">
						<ul>
							<li><b>Email:</b> <a href="mailto:'.$im['email'].'">'.$im['email'].'</a></li>
							<li><b>Phone:</b> <a href="tel:'.$im['phone'].'">'.$im['phone'].'</a></li>
							<li><b>IP:</b> <a href="https://www.infobyip.com/ip-'.$im['ip'].'.html" target="__blank">'.$im['ip'].'</a></li>
							<li><b>Browser:</b> '.$im['browser'].'</li>
							<li><b>OS:</b> '.$im['os'].'</li>
							<li><b>Type:</b> '.$im['device_type'].'</li>
						</ul>
					</div>' : '';
					foreach(array_reverse(db_multi_query('
					SELECT m.*, c.email, u.name, u.lastname FROM `'.DB_PREFIX.'_chat_messages` m 
						INNER JOIN `'.DB_PREFIX.'_chat_im` c 
						ON c.id = m.im_id 
						LEFT JOIN `'.DB_PREFIX.'_users` u ON m.staff_id = u.id
					WHERE m.im_id = '.$id.'
						ORDER BY m.id DESC
					LIMIT '.($page*$count).', '.$count, true
					)) as $msg){
						tpl_set('im/message', [
							'id' => $msg['id'],
							'uid' => $msg['staff_id'],
							'name' => $msg['staff_id'] ? $msg['name'] : 'Guest #'.$msg['im_id'],
							'lastname' => $msg['staff_id'] ? $msg['lastname'] : '',
							'image' => '',
							'message' => $msg['del'] ? '<span class="fa fa-trash imDeleted"></span> '.$lang['MessageDeleted'] : outputMsg($msg['message']),
							'date' => $msg['date']
						], [
							'first' => ($fid !== $msg['staff_id']),
							'del' => $msg['del'],
							'new' => (!$msg['viewed'] && $msg['staff_id']),
							'my' => $msg['staff_id'],
							'ava' => false
						], 'messages');	
						$fid = $msg['staff_id'];
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					$left_count = intval(($res_count-($page*$count)-$i));
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['messages'] ?? '<div class="imNoCont">'.$lang['NoChats'].'</div>'
					]));
				}
			} else if($route[2] == 'send'){
				$id = (int)$_POST['id'];
				$msg = text_filter($_POST['message'], 16000);
				db_query('INSERT INTO `'.DB_PREFIX.'_chat_messages` SET im_id = '.$id.', message = \''.$msg.'\', date = \''.date('Y-m-d H:i:s', time()).'\', staff_id = '.$user['id']);
				$msg_id = (int)mysqli_insert_id($db_link);
				$im = db_multi_query('SELECT token, staff_ids, email FROM `'.DB_PREFIX.'_chat_im` WHERE id = '.$id);
				
				
				
				
				if(!$im['staff_ids'] OR !in_array($user['id'], explode(',', $im['staff_ids']))){
					db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET count = 0, staff_ids = \''.($im['staff_ids'] ? $im['staff_ids'].','.$user['id'] : $user['id']).'\' WHERE id = '.$id);
					db_query('
						INSERT INTO `'.DB_PREFIX.'_activity`
						SET user_id = \''.$user['id'].'\',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						object_id = '.(int)$user['store_id'].',
						event_id = '.$id.',
						event = \'replied_to_chat\''
					);
				} else
					db_query('UPDATE `'.DB_PREFIX.'_chat_im` SET count = 0 WHERE id = '.$id);
				
				
				// nik
				//if (cache_get($im['token'])) {
				if (true) {
					$res = send_push($im['token'], [
						'type' => 'chat_message',
						'message' => '<div class="cd-block" data-id="'.$msg_id.'">
							<div class="cd-image">
								'.(
									$user['image'] ? '<img src="/uploads/images/users/'.$user['id'].'/'.$user['image'].'" class="imImg">' : '<span class="fa fa-user-secret imImg"></span>'
								).'
							</div>
							<div class="cd-single">
								<label>'.$user['uname'].':</label>
								<p>'.outputMsg(stripcslashes($msg)).'</p>
							</div>
						</div>'
					]);
/* 					if($user['id'] == 16){
						print_r($res[0]);
					} */
/* 					<div class="dialog-message" data-id="'.$msg_id.'">
							<div class="dialog-img">
								'.(
									$user['image'] ? '<img src="/uploads/images/users/'.$user['id'].'/'.$user['image'].'" class="imImg">' : '<span class="fa fa-user-secret imImg"></span>'
								).'
							</div>
							<div class="dialog-combi">
								<div class="dm-title">
									<span class="name">'.$user['uname'].'</span>
									<i class="name">'.date("H:i:s").'</i>
								</div>
								<div class="dm-content">'.outputMsg(stripcslashes($msg)).'
								</div>
							</div>
						</div> */
				} else {
					if($id = intval($_POST['id']) AND $im['email']){
						$fid = 0;
						foreach($items = array_reverse(db_multi_query('
							SELECT m.*, c.email, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_chat_messages` m 
								INNER JOIN `'.DB_PREFIX.'_chat_im` c 
								ON c.id = m.im_id 
								LEFT JOIN `'.DB_PREFIX.'_users` u ON m.staff_id = u.id
							WHERE m.im_id = '.$id.'
							ORDER BY m.id DESC
							', true
						)) as $imsg){
							if ($items[count($items) - 1]['id'] != $imsg['id']) {
								tpl_set('im/email_message', [
									'id' => $imsg['id'],
									'uid' => $imsg['staff_id'],
									'name' => $imsg['staff_id'] ? $imsg['name'] : 'Guest #'.$imsg['im_id'],
									'lastname' => $imsg['staff_id'] ? $imsg['lastname'] : '',
									'image' => $imsg['image'],
									'message' => $imsg['del'] ? '<span class="fa fa-trash imDeleted"></span> '.$lang['MessageDeleted'] : outputMsg($imsg['message']),
									'date' => $imsg['date']
								], [
									'first' => ($fid !== $imsg['staff_id']),
									'del' => $imsg['del'],
									'new' => (!$imsg['viewed'] && $imsg['staff_id']),
									'my' => $imsg['staff_id'],
									'ava' => $imsg['image'],
									'email' => false
								], 'messages');	
								$fid = $imsg['staff_id'];
							}
						}
						
						$demail = [];
						foreach($config['emails'] as $itm){
							if ($itm['default'])
								$demail = $itm;
						}
						$login = $demail['login'];
						$password = $demail['password'];
						

						// Headers
						$headers  = 'MIME-Version: 1.0'."\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
						$headers .= 'To: '.$im['email']. "\r\n";
						$headers .= 'From: Your Company <'.$login.'>' . "\r\n";
						
						$emsg = '<!DOCTYPE html>
						<html lang="en">
						<head>
							<meta charset="UTF-8">
							<meta name="viewport" content="width=device-width, initial-scale=1">
							<title>New user on Your Company</title>
						</head>
						<body style="background: #f6f6f6; text-align: center;">
							<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
								<div style="margin: -20px -20px 0; padding: 20px;">
									<a href="http://yoursite.com/">
										<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
									</a>
								</div>
								<div style="padding: 0 30px 30px;">
									<p>You had the conversation on <b>Your Company inc</b>.</p>
									<div style="margin: 20px -30px; text-align: left">
										'.$tpl_content['messages'].'
									</div>
									<div style="background: #d6ecff; margin: 10px 0 20px; padding: 10px; color: #69849a;">
										'.outputMsg($items[count($items) - 1]['message']).'
									</div>
								</div>
							</div>
						</body>
						</html>';
											
						mail($im['email'], 'Online chat answer', $emsg, $headers);
					
						$auth = imap_open("{localhost:143/novalidate-cert}INBOX", $login, $password,NULL,1,array('DISABLE_AUTHENTICATOR' => array('GSSAPI','NTLM')));
						imap_append($auth, "{localhost:143/novalidate-cert}Sent"
						   , "From: ".$login."\r\n"
						   . "Date: ".date('Y-m-d H:i:s', time())."\r\n"
						   . "To: ".$im['email']."\r\n"
						   . "Subject: Online chat answer\r\n"
						   . "\r\n"
						   . outputMsg($items[count($items) - 1]['message']) ."\r\n"
						   );
					}
				}
				send_push('chat_guest', [
					'type' => 'unblock_box',
					'id' => $id,
					'staff_id' => $user['id'],
					'name' => $user['name'],
					'lastname' => $user['lastname'],
					'arguments' => [
						'chat_support' => get_token('chat_support1')
					]
				]);
				echo json_encode([
					'err' => 0,
					'message' => [
						'type' => 'messages',
						'token' => $im['token'],
						'id' => $msg_id,
						'uid' => $user['id'],
						'sid' => $id,
						'image' => $user['image'],
						'name' => $user['uname'],
						'lastname' => $user['ulastname'],
						'message' => outputMsg(stripcslashes($msg)),
						'my' => true,
						'date' => date("Y-m-d H:i:s")
					]
				], JSON_UNESCAPED_UNICODE);
				die;
			} else if($route[2] == 'welcom'){
				$msg = text_filter($_POST['msg'], 16000);
				send_push('chat_guest', [
					'type' => 'unblock_box',
					'id' => $_POST['id'],
					'name' => $user['name'],
					'lastname' => $user['lastname'],
					'arguments' => [
						'chat_support' => get_token('chat_support1')
					]
				]);
				send_push($_POST['id'], [
					'type' => 'support_msg',
					'content' => '<div class="dialog-chat cnt">
						<div class="dialog">
							<div class="dialog-message">
								<div class="dialog-img">
								</div>
								<div class="dialog-combi">
									<div class="dm-title">
										<span class="name">'.$user['uname'].'</span>
										<i class="name">'.date("H:i:s").'</i>
									</div>
									<div class="dm-content">'.outputMsg(stripcslashes($msg)).'
									</div>
								</div>
							</div>
						</div>
						<div class="dialog-form">
							<textarea id="chat-message" placeholder="Enter your message..." onkeypress="if(event.keyCode == 13){Chat.msg(); return false;}"></textarea>
							<span class=" fa fa-chevron-right" onclick="Chat.msg();"></span>
						</div>
					</div>',
					'uid' => $user['id']
				]);
				die;
			} else {
				$id = (int)$route[2];
				$query = text_filter(str_replace('#', '', $_POST['query']), 255, false);
				$i = 0;
				foreach(db_multi_query('
					SELECT * FROM `'.DB_PREFIX.'_chat_im`'.(
						$query ? ' WHERE MATCH(email) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
					).' ORDER BY '.(
						$id ? 'id = '.$id.' DESC, ' : ''
					).'`date` DESC LIMIT '.($page*$count).', '.$count, true
				) as $im){
					tpl_set('im/user', [
						'id' => $im['id'],
						'name' => 'Guest #'.$im['id'],
						'msg' => '',
						'lastname' => $im['email'],
						'ava' => ''
					], [
						'online' => cache_get($im['token']),
						'ava' => false,
						'msg-text' => false,
						'email' => false
					], 'dialogues');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_SERVER['REQUEST_METHOD'] == 'POST'){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['dialogues'],
					]));
				}
				if($id > 0){
					$fid = 0;
					$im = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_chat_im` WHERE id = '.$id);
					$tpl_content['messages'] = $im['ip'] ? '<div class="info-chat">
						<ul>
							<li><b>Email:</b> <a href="mailto:'.$im['email'].'">'.$im['email'].'</a></li>
							<li><b>Phone:</b> <a href="tel:'.$im['phone'].'">'.$im['phone'].'</a></li>
							<li><b>IP:</b> <a href="https://www.infobyip.com/ip-'.$im['ip'].'.html" target="__blank">'.$im['ip'].'</a></li>
							<li><b>Browser:</b> '.$im['browser'].'</li>
							<li><b>OS:</b> '.$im['os'].'</li>
							<li><b>Type:</b> '.$im['device_type'].'</li>
						</ul>
					</div>' : '';
					foreach(array_reverse(db_multi_query('
					SELECT m.*, c.email, u.name, u.lastname FROM `'.DB_PREFIX.'_chat_messages` m 
						INNER JOIN `'.DB_PREFIX.'_chat_im` c 
						ON c.id = m.im_id 
						LEFT JOIN `'.DB_PREFIX.'_users` u ON m.staff_id = u.id
					WHERE m.im_id = '.$id.'
						ORDER BY m.id DESC
					LIMIT 0, 20', true
					)) as $msg){
						tpl_set('im/message', [
							'id' => $msg['id'],
							'uid' => $msg['staff_id'],
							'name' => $msg['staff_id'] ? $msg['name'] : 'Guest #'.$msg['im_id'],
							'lastname' => $msg['staff_id'] ? $msg['lastname'] : '',
							'image' => '',
							'message' => $msg['del'] ? '<span class="fa fa-trash imDeleted"></span> '.$lang['MessageDeleted'] : outputMsg($msg['message']),
							'date' => $msg['date']
						], [
							'first' => ($fid !== $msg['staff_id']),
							'del' => $msg['del'],
							'new' => (!$msg['viewed'] && $msg['staff_id']),
							'my' => $msg['staff_id'],
							'ava' => false
						], 'messages');	
						$fid = $msg['staff_id'];
					}
				}
				$email_list = '';
				if ($config['emails']) {
					foreach($config['emails'] as $e) {
						$email_list .= '<option value="'.$e['login'].'"'.(
							$e['read'] ? ' selected' : ''
						).'>'.$e['login'].'</option>';
					}
				}
				tpl_set('im/main', [
					'id' => $id,
					'text' => '',
					'email-list' => $email_list,
					'dialogues' => $tpl_content['dialogues'],
					'messages' => $tpl_content['messages'] ?? '<div class="imNoCont">'.$lang['NoChats'].'</div>'
				], [
					'send-all' => false,
					'support' => true,
					'emails' => false,
					'new-email' => $user['email_new'],
					'chat-support' => $user['chat_support'],
					'email-receive' => $user['email_receive']
				], 'content');
				$meta['title'] = 'Support';	
			}
		break;
		
		default:
			$text = isset($_GET['text']) ? htmlspecialchars($_GET['text'], ENT_HTML5).PHP_EOL : '';
			$query = text_filter($_POST['query'], 255, false);
			$id = (int)$route[1];
			$gid = 0;
			if(preg_match('/G\-([0-9]+)/i', $route[1], $match))
				$gid = $match[1];
			$page = intval($_POST['page']);
			$count = 20;
			if($id > 0){
				@db_query('UPDATE IGNORE `'.DB_PREFIX.'_im` i INNER JOIN `'.DB_PREFIX.'_users` u ON u.id = IF(
					i.for_uid = '.$user['id'].', i.for_uid, i.from_uid
				) SET  u.new_msg = u.new_msg-IF(
					i.from_uid = '.$user['id'].', i.from_msg, i.for_msg
				), i.for_msg = IF(
					i.for_uid = '.$user['id'].', 0, i.for_msg
				), i.from_msg = IF(
					i.from_uid = '.$user['id'].', 0, i.from_msg
				) WHERE (
					i.from_uid = '.$user['id'].' OR i.for_uid = '.$user['id'].'
				) AND (
					i.from_uid = '.$id.' OR i.for_uid = '.$id.'
				)');
				db_query('
					UPDATE `'.DB_PREFIX.'_messages`
					SET viewed = 1
					WHERE from_uid = '.$id.'
					AND for_uid = '.$user['id'].'
					AND viewed = 0'
				);
				send_push($id, [
					'type' => 'viewed_messages',
					'uid' => $user['id']
				]);
			}
			if($groups = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_im` WHERE (from_uid = '.$user['id'].' OR FIND_IN_SET('.$user['id'].', for_uids)) AND name != \'\' ORDER BY `id` DESC LIMIT 0, 20', true)){
				foreach($groups as $row){
					tpl_set('im/user', [
						'id' => $row['id'],
						'name' => $row['name'],
						'msg' => $row['msg'] > 0 ? '<span class="imCount">'.$row['msg'].'</span>' : '',
						'lastname' => '',
						'ava' => ''
					], [
						'online' => false,
						'ava' => '',
						'msg-text' => false,
						'email' => false
					], 'dialogues');
				}
			}
			if($users = db_multi_query('SELECT u.id, u.name, u.lastname, u.image, IF(
				i.from_uid = '.$user['id'].', IF(
					i.from_msg, i.from_msg, 0
				), IF(
					i.for_msg, i.for_msg, 0
				)
			) as msg
				FROM `'.DB_PREFIX.'_users` u LEFT JOIN `'.DB_PREFIX.'_im` i ON (
					i.for_uid = u.id AND i.from_uid = '.$user['id'].'
				) OR (
					i.from_uid = u.id AND i.for_uid = '.$user['id'].'
				)
				WHERE NOT FIND_IN_SET(5, u.group_ids) AND u.del = 0
				AND u.id != '.$user['id'].' AND u.del = 0
				'.($query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : '').'
				ORDER BY '.($id ? 'u.id = '.$id.' DESC, ' : '').'i.date DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($users as $row){
					tpl_set('im/user', [
						'id' => $row['id'],
						'name' => $row['name'],
						'msg' => $row['msg'] > 0 ? '<span class="imCount">'.$row['msg'].'</span>' : '',
						'lastname' => $row['lastname'],
						'ava' => $row['image']
					], [
						'online' => false,
						'ava' => $row['image'],
						'msg-text' => false,
						'email' => false
					], 'dialogues');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['dialogues'],
				]));
			} else {
				
				$where = $id > 0 ? ' (
					m.for_uid = '.$user['id'].' AND m.from_uid = '.$id.'
				) OR (
					m.from_uid = '.$user['id'].' AND m.for_uid = '.$id.'
				)' : (
					$gid > 0 ? 'm.sid = '.$gid : 'm.for_uid = 0 AND m.sid = 0'
				);
				
				if($messages = array_reverse(db_multi_query('SELECT m.*, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_messages` m
					INNER JOIN  `'.DB_PREFIX.'_users` u
						ON m.from_uid = u.id
					WHERE '.$where.' ORDER BY m.id DESC LIMIT 0, 20', true))){
					$fid = 0;
					foreach($messages as $row2){
						$attach_images = '';
						$attach_files = '';
						if($row2['attaches']){
							$attach_images = '<div class="thumbnails">';
							$attach_files = '<ul class="files">';
							foreach(explode('|:|', $row2['attaches']) as $file){
								if(preg_match("/(.*).(jpeg|jpg|png|gif)/i", $file)){
									$attach_images .= '<div class="thumb">
											<img src="/uploads/attaches/'.($row2['ind'] ? 0 : $row2['for_uid']).'/thumb_'.$file.'" onclick="showPhoto(this.src);">
										</div>';
								} else {
									$attach_files .= '<li>
											<a href="/uploads/attaches/'.($row2['ind'] ? 0 : $row2['for_uid']).'/'.$file.'" download>
												<span class="fa fa-file"></span> '.$file.'
											</a>
										</li>';
								}
							}	
							$attach_images .= '</div>';
							$attach_files .= '</ul>';
						}
						tpl_set('im/message', [
							'id' => $row2['id'],
							'uid' => $row2['from_uid'],
							'name' => $row2['name'],
							'lastname' => $row2['lastname'],
							'image' => $row2['image'],
							'message' => $row2['del'] ? '<span class="fa fa-trash imDeleted"></span> '.$lang['MessageDeleted'] : outputMsg($row2['message']).$attach_images.$attach_files,
							'date' => $row2['date']
						], [
							'first' => ($fid !== $row2['from_uid']),
							'del' => $row2['del'],
							'new' => (!$row2['viewed'] && $row2['from_uid'] == $user['id']),
							'my' => ($row2['from_uid'] == $user['id']),
							'ava' => $row2['image']
						], 'messages');
						$fid = $row2['from_uid'];
					}
				}
				$email_list = '';
				if ($config['emails']) {
					foreach($config['emails'] as $e) {
						$email_list .= '<option value="'.$e['login'].'"'.(
							$e['read'] ? ' selected' : ''
						).'>'.$e['login'].'</option>';
					}
				}
				tpl_set('im/main', [
					'id' => $id ?: 'all',
					'email-list' => $email_list,
					'text' => str_replace(';', ' #', $text),
					'dialogues' => $tpl_content['dialogues'],
					'messages' => $tpl_content['messages'] ?? '<div class="imNoCont">'.$lang['NoChats'].'</div>'
				], [
					'send-all' => !$id,
					'support' => false,
					'emails' => false,
					'new-email' => $user['email_new'],
					'chat-support' => $user['chat_support'],
					'email-receive' => $user['email_receive']
				], 'content');
				$meta['title'] = $lang['Dialogues'];	
			}
		break;
	}
} else {
	tpl_set('forbidden', [
		'text' => $lang['Forbidden'],
	], [], 'content');
}
?>
