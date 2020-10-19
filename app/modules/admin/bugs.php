<?php
/**
 * @appointment Bugs admin panel
 * @author      Victoria Shovkovych & Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

function outputText($a){
	return preg_replace(	
		[
			"~(?:https?\:\/\/|)(?:www\.|)(?:youtube\.com|youtu\.be)\/(?:embed\/|v\/|watch\?v=|)(.{11})((&|\?)*[\S]*[\s]?)?~",
			"~((?:ht|f)tps?)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~"
		], [
			'<div class="youtube" onmousedown="Im.getYoutube(this, \'$1\')">
				<img width="640" height="480" src="//img.youtube.com/vi/$1/sddefault.jpg">
				<span class="fa fa-youtube-play"></span>
			</div>',
			'<a href="$1://$2" target="_blank">$1://$2</a>$3'
		], htmlspecialchars($a, ENT_HTML5)
	);
}
 
switch($route[1]){

	/*
	*  Send bug
	*/
	case 'send': 
		is_ajax() or die('Hacking attempt!');
		
		// Filters
		$id = intval($_POST['id']);
		
		$delete = '';
		if($_POST['delete']){
			foreach($_POST['delete'] as $del){
				$delete .= 'images = REPLACE(
					images, \''.$del.'|\', \'\'
				), ';
			}
		}
		
		// SQL SET
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_bugs` SET '.$delete.(
					$_POST['title'] ? 'title = \''.text_filter($_POST['title'], 255, true).'\',' : ''
				).(
					$_POST['url'] ? 'url = \''.db_escape_string($_POST['url']).'\',' : ''
				).(
					$_POST['content'] ? 'content = \''.text_filter($_POST['content']).'\',' : ''
				).(
					$_POST['comment'] ? 'comment = \''.text_filter($_POST['comment']).'\', notify = 1,' : ''
				).(
					$id ? '' : 'user_id = '.$user['id'].', '
				).(
					$_POST['status'] ? 'dev_id = '.$user['id'].',' : ''
				).'status = \''.(
					$_POST['status'] ? text_filter($_POST['status'], 12, false) : 'opened'
				).'\''.(
			$id ? ' WHERE id = '.$id : ''
		));
		
		$id = $id ? $id : intval(
			mysqli_insert_id($db_link)
		);
		
		if($_POST['comment'] AND (
			$bug = db_multi_query('SELECT user_id FROM `'.DB_PREFIX.'_bugs` WHERE id = '.$id)
		)){
			$uid = (int)$bug['user_id'];
			send_push($uid, [
				'type' => 'bugs',
				'id' => $id,
				'uid' => $user['id'],
				'sid' => $uid,
				'image' => $user['image'],
				'name' => $user['uname'],
				'lastname' => $user['ulastname'],
				'comment' => text_filter($_POST['comment'], 100, false),
				'date' => date("Y-m-d H:i:s")
			]);
		}
		
		// Is file upload
		if($_FILES){
			
			$images = [];
			
			// Upload max file size
			$max_size = 200;
			
			// path
			$dir = ROOT_DIR.'/uploads/images/bugs/';
			$dir2 = ROOT_DIR.'/uploads/videos/bugs/';
			
			// Is not dir
			if(!is_dir($dir.$id)){
				@mkdir($dir.$id, 0777);
				@chmod($dir.$id, 0777);
			}
			
			if(!is_dir($dir2.$id)){
				@mkdir($dir2.$id, 0777);
				@chmod($dir2.$id, 0777);
			}
			
			$dir = $dir.$id.'/';
			$dir2 = $dir2.$id.'/';
			
			foreach($_FILES["image"]["error"] as $key => $error){
				
				// temp file
				$tmp = $_FILES['image']['tmp_name'][$key];
				
				$type = mb_strtolower(pathinfo($_FILES['image']['name'][$key], PATHINFO_EXTENSION));
				
				if($_FILES['image']['size'][$key] >= 1024*$max_size*1024){
					echo 'err_file_size';
					die;
				}
				
				// New name
				$rename = uniqid('', true).'.'.$type;
				
				if(($type == 'mp4' OR $type == 'webm') AND ($_FILES['image']['type'][$key] == 'video/mp4' OR $_FILES['image']['type'][$key] == 'video/webm')){
					if(move_uploaded_file($tmp, $dir2.$rename))
						$images[] = $rename;
					
				} else {
					// Check
					if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array(
						$type, ['jpeg', 'jpg', 'png', 'gif']
					)){
						echo 'err_image_type';
						die;
					}
					
					// Upload image
					if(move_uploaded_file($tmp, $dir.$rename)){
						
						$img = new Imagick($dir.$rename);
						
						if($img->getImageWidth() > 1920){
							$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
							auto_rotate_image($img);
							$img->stripImage();
							$img->writeImage($dir.$rename);
						}
						
						$img->cropThumbnailImage(300, 300);
						$img->stripImage();
						$img->writeImage($dir.'preview_'.$rename);
						
						$img->cropThumbnailImage(94, 94);
						$img->stripImage();
						$img->writeImage($dir.'thumb_'.$rename);
						$img->destroy();
						
						$images[] = $rename;
					}
				}
			}
			if($images){
				db_query('UPDATE `'.DB_PREFIX.'_bugs` SET 
					'.(
						$_POST['dev'] == 1 ? 'images_comment = CONCAT(images_comment, \''.implode('|', $images).'|\'':
							'images = CONCAT(images, \''.implode('|', $images).'|\''
					).'
				) WHERE id = '.$id);
			}
		}
		
		
		exit(json_encode([
			'id' => $id,
			'date' => date('Y-m-d h:m:s'),
			'user' => '<a href="/admin/users/view/'.$user['id'].'">'.$user['uname'].' '.$user['ulastname'].'</a>',
			'images' => $images
		]));
	break;
	
    /*
	* All bugs
	*/
    case null:
		$meta['title'] = 'Bugs dashboard';
		$page = intval($_POST['page']);
		$count = 20;
		$bugs = db_multi_query('SELECT COUNT(*) as count, status FROM `'.DB_PREFIX.'_bugs` GROUP BY status', true, false, function($a){
			return [$a['status'], $a['count']];
		});
		if($users = db_multi_query(
			'SELECT COUNT(b.id) as count, u.id, u.name, u.lastname, u.image FROM 
			`'.DB_PREFIX.'_bugs` b INNER JOIN `'.DB_PREFIX.'_users` u 
			ON b.user_id = u.id GROUP BY b.user_id ORDER BY COUNT DESC LIMIT '.($page*$count).', '.$count, true
		)){
			foreach($users as $user){
				tpl_set('bugs/item_user', [
					'id' => $user['id'],
					'count' => $user['count'],
                    'name' => $user['name'],
                    'lastname' => $user['lastname'],
                    'image' => $user['image']
                ],[
					'image' => $user['image']
				], 'users');
				$i++;
			}
		}
		tpl_set('bugs/dashboard', [
			'title' => $meta['title'],
			'users' => $tpl_content['users']
		]+$bugs, [], 'content');
	break;

    /*
	* All bugs
	*/
    case 'all':
    case 'opened':
    case 'pending':
    case 'rejected':
    case 'closed':
    case 'improvement':
    case 'my':
    case 'users':
        $meta['title'] = $lang['All'].' '.($route[1] ?? $lang['improvementAnd'].' ').(
			$route[1] != 'improvement' ? ' '.$lang['bugs'] : ''
		);
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$my = ($route[1] === 'my' OR ($route[1] == 'users' AND $route[2] == $user['id']));
		
		$status = in_array(text_filter($route[1], 11, true), ['opened', 'pending', 'rejected', 'closed', 'improvement', 'final_closed']) ? text_filter($route[1], 11, true) : '';
		$count = 20;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
                b.*,
				b.id as id,
                u.name as user_name,
                u.lastname as user_lastname,
				d.name as dev_name,
                d.lastname as dev_lastname
			FROM `'.DB_PREFIX.'_bugs` b
                LEFT JOIN `'.DB_PREFIX.'_users` u 
            ON b.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_users` d 
            ON b.dev_id =  d.id WHERE '.(in_array($user['id'], [31735, 1]) ? '1 ' : 'b.user_id != 1 AND b.user_id != 31735 ').(
			$query ? 'AND b.title LIKE \'%'.$query.'%\' OR b.content LIKE \'%'.$query.'%\' ' : ''
		).(
            $status ? (($status == 'closed' OR $status == 'final_closed') ? 'AND (status = \'closed\' OR status = \'final_closed\')' : 'AND status = \''.$status.'\' ') : ' AND status != \'final_closed\' '
        ).(
			in_array($route[1], ['my','users']) ? 'AND user_id = '.(
				$route[1] === 'users' ? (int)$route[2] : $user['id']
			).' ' : ''
		).'ORDER BY '.(
			$my ? '`notify` DESC, ' : ''
		).'`id` DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				
				$images = '';
				if($row['images']){
					$images = '<div class="thumbnails">';
					foreach(explode('|', $row['images']) as $image){
						if(!$image) break;
						$images .= '<div class="thumb">
							'.(in_array(mb_strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['mp4','webm']) ? '<img src="/templates/admin/img/videothumb.jpg" video-src="/uploads/videos/bugs/'.$row['id'].'/'.$image.'" onclick="bugs.videoShow(this);">' : '<img src="/uploads/images/bugs/'.$row['id'].'/thumb_'.$image.'" onclick="showPhoto(this.src);">').'
						</div>';
					}
					$images .= '</div>';
				}
				$comimages = '';
				if($row['images_comment']){
					foreach(explode('|', $row['images_comment']) as $image){
						if(!$image) break;
						$comimages .= '<div class="thumb">
							<img src="/uploads/images/bugs/'.$row['id'].'/thumb_'.$image.'" onclick="showPhoto(this.src);">
						</div>';
					}
				}
				
				tpl_set('bugs/item', [
					'id' => $row['id'],
					'title' => $row['title'],
					'url' => $row['url'],
					'content' => '<div>'.outputText($row['content']).'</div>'.$images,
                    'status_id' => $row['status'],
                    'status' => $lang[$row['status']],
                    'date' => convert_date($row['date']),
                    'user-name' => $row['user_name'],
                    'user-lastname' => $row['user_lastname'],
                    'user-id' => $row['user_id'],
					'dev-name' => $row['dev_name'],
                    'dev-lastname' => $row['dev_lastname'],
                    'dev-id' => $row['dev_id'],
					'comment' => $row['comment'],
					'comimages' => $comimages
                ],[
                    'dev' => $user['id'] == 31735 OR $user['id'] == 1,
					'comment-new' => $row['notify'] AND $row['user_id'] == $user['id'],
					'comment' => $row['comment'] OR $row['images_comment'],
					'item' => true,
					'owner' => $row['user_id'] == $user['id'] AND $row['status'] == 'opened'
				], 'bugs');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
			$tpl_content['bugs'] = '';
/*             tpl_set('noContent', [
                'text' => $lang['ThereAreNo'].' '.(
                    $status ? $lang[$status].' ' : ''
                ).$lang['bugs']
            ],false, 'bugs'); */
        }
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['bugs'],
			]));
		}
		tpl_set('bugs/main', [
			'title' => $meta['title'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'bugs' => $tpl_content['bugs']
		], [], 'content');
    break;
	
    /*
	* View bug
	*/
    case ($id = (int)$route[1]) > 0:
		if($row = db_multi_query('SELECT b.*,
				b.id as id,
				b.notify,
                u.name as user_name,
                u.lastname as user_lastname,
				d.name as dev_name,
                d.lastname as dev_lastname 
			FROM `'.DB_PREFIX.'_bugs` b
                LEFT JOIN `'.DB_PREFIX.'_users` u 
            ON b.user_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_users` d 
            ON b.dev_id =  d.id WHERE b.id = '.$id)){
			$images = '';
			if($row['images']){
				$images = '<div class="thumbnails">';
				foreach(explode('|', $row['images']) as $image){
					if(!$image) break;
					$images .= '<div class="thumb">
						'.(in_array(mb_strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['mp4','webm']) ? '<img src="/templates/admin/img/videothumb.jpg" video-src="/uploads/videos/bugs/'.$row['id'].'/'.$image.'" onclick="bugs.videoShow(this);">' : '<img src="/uploads/images/bugs/'.$row['id'].'/thumb_'.$image.'" onclick="showPhoto(this.src);">').'
					</div>';
				}
				$images .= '</div>';
			}
			$comimages = '';
			if($row['images_comment']){
				foreach(explode('|', $row['images_comment']) as $image){
					if(!$image) break;
					$comimages .= '<div class="thumb">
						<img src="/uploads/images/bugs/'.$row['id'].'/thumb_'.$image.'" onclick="showPhoto(this.src);">
					</div>';
				}
			}
			tpl_set('bugs/item', [
				'id' => $row['id'],
				'title' => $row['title'],
				'url' => $row['url'],
				'content' => '<div>'.outputText($row['content']).'</div>'.$images,
				'status_id' => $row['status'],
				'status' => $lang[$row['status']],
				'date' => convert_date($row['date']),
				'user-name' => $row['user_name'],
				'user-lastname' => $row['user_lastname'],
				'user-id' => $row['user_id'],
				'dev-name' => $row['dev_name'],
				'dev-lastname' => $row['dev_lastname'],
				'dev-id' => $row['dev_id'],
				'comment' => $row['comment'],
				'comimages' => $comimages
			],[
				'dev' => $user['id'] == 31735 OR $user['id'] == 1,
				'comment' => $row['comment'] OR $row['images_comment'],
				'comment-new' => false,
				'item' => false,
				'owner' => $row['user_id'] == $user['id'] AND $row['status'] == 'opened'
			], 'content');
			
			if($row['notify'] AND $row['user_id'] == $user['id']){
				db_query('UPDATE `'.DB_PREFIX.'_bugs` SET notify = 0 WHERE id = '.$row['id']);
			}
		}
}

?>