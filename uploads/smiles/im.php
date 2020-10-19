<?php
/**
 * @appointment IM admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
function outputMsg($a){
	return preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">$1://$2</a>$3', str_replace("\n", '<br />', htmlspecialchars($a, ENT_HTML5)));
}

if($user['im'] > 0){
	switch($route[1]){
		
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
			echo 'OK';
			die;
		break;
		
		case 'send':
			is_ajax() or die('hacking');
			$id = (int)$_POST['id'];
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
						
						$type = mb_strtolower(pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION));
						
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
							
							$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
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
						if(!in_array(explode('/', $_FILES['files']['type'][$key])[1], [
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
				if($id > 0){
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
					for_uid = '.$id.',
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
				send_push($id, $output);
				$output['my'] = true;
				echo json_encode([
					'err' => 0,
					'message' => $output
				], JSON_UNESCAPED_UNICODE);
			}
			die;
		break;
		
		case 'history':
			is_ajax() or die('hacking');
			$id = intval($_POST['id']);
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
			}
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 20;
			if($messages = array_reverse(db_multi_query('
				SELECT m.*, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_messages` m
				INNER JOIN  `'.DB_PREFIX.'_users` u
					ON m.from_uid = u.id
				WHERE '.(
				$id > 0 ? ' (
					m.for_uid = '.$user['id'].' AND m.from_uid = '.$id.'
				) OR (
					m.from_uid = '.$user['id'].' AND m.for_uid = '.$id.'
				)' : 'm.for_uid = 0'
			).($query ? ' AND m.message LIKE \'*'.$query.'*\' ' : '').'
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
					tpl_set('im/message', [
						'id' => $row['id'],
						'uid' => $row['from_uid'],
						'name' => $row['name'],
						'lastname' => $row['lastname'],
						'image' => $row['image'],
						'message' => $row['del'] ? '<span class="fa fa-trash imDeleted"></span> Message deleted' : outputMsg($row['message']).$attach_images.$attach_files,
						'date' => $row['date']
					], [
						'first' => ($fid !== $row['from_uid']),
						'ndel' => $row['del'] == 0,
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
				'content' => $tpl_content['messages'] ?? '<div class="imNoCont">No chats</div>'
			]));
		break;
		
		default:
			$query = text_filter($_POST['query'], 255, false);
			$id = (int)$route[1];
			$page = intval($_POST['page']);
			$count = 20;
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
				WHERE NOT FIND_IN_SET(5, u.group_ids)
				AND u.id != '.$user['id'].'
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
						'ava' => $row['image']
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
				if($messages = array_reverse(db_multi_query('SELECT m.*, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_messages` m
					INNER JOIN  `'.DB_PREFIX.'_users` u
						ON m.from_uid = u.id
					WHERE '.(
					$id > 0 ? ' (
						m.for_uid = '.$user['id'].' AND m.from_uid = '.$id.'
					) OR (
						m.from_uid = '.$user['id'].' AND m.for_uid = '.$id.'
					)' : 'm.for_uid = 0'
				).' ORDER BY m.date DESC LIMIT 0, 20', true))){
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
											<img src="/uploads/attaches/'.$row2['for_uid'].'/thumb_'.$file.'" onclick="showPhoto(this.src);">
										</div>';
								} else {
									$attach_files .= '<li>
											<a href="/uploads/attaches/'.$row2['for_uid'].'/'.$file.'" download>
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
							'message' => $row2['del'] ? '<span class="fa fa-trash imDeleted"></span> Message deleted' : outputMsg($row2['message']).$attach_images.$attach_files,
							'date' => $row2['date']
						], [
							'first' => ($fid !== $row2['from_uid']),
							'ndel' => $row2['del'] == 0,
							'my' => ($row2['from_uid'] == $user['id']),
							'ava' => $row2['image']
						], 'messages');
						$fid = $row2['from_uid'];
					}
				}
				tpl_set('im/main', [
					'id' => $id ?: 'all',
					'dialogues' => $tpl_content['dialogues'],
					'messages' => $tpl_content['messages'] ?? '<div class="imNoCont">No chats</div>'
				], [], 'content');
				$meta['title'] = 'Dialogues';	
			}
		break;
	}
} else {
	tpl_set('forbidden', [
		'text' => $lang['Forbidden'],
	], [], 'content');
}
?>