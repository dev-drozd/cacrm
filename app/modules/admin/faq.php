<?php
/**
 * @appointment FAQ 
 * @author      Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
switch($route[1]){
	/*
	*  Del question
	*/
	case 'del':
		is_ajax() or die('Hacking attempt!');
		$id = intval($_POST['id']);
		if($user['faq_del']){
			db_query('DELETE FROM `'.DB_PREFIX.'_faq` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		} else
			exit('no_acc');
	break;
	
	/*
	*  Add/edit question
	*/
	case 'add':
	case 'edit':
		$meta['title'] = $id ? $lang['EditFaq'] : $lang['AddFaq'];
		if ((!$id AND $user['faq_add']) OR ($id AND $user['faq_edit'])) {
			$id = intval($route[2]);
			$row = [];
			$type = $id ? $lang['Edit'] : $lang['Add'];
			if($id){
				$row = db_multi_query('
					SELECT
						*
					FROM `'.DB_PREFIX.'_faq`
					WHERE id = '.$id
				);
			}
			if($route[1] == 'add' OR (
				$route[1] == 'edit' AND $id
			)){
				tpl_set('faq/form', [
					'id' => $id,
					'title' => $row['title'],
					'content' => $row['content'],
					'send' => ($id ? $lang['Edit'] : $lang['Add']),
					'page-title' => $id ? $lang['EditFaq'] : $lang['AddFaq']
				], [
					'edit' => $id
				], 'content');
			}
		} else {
			tpl_set('forbidden', [
				'text' => $lang['noAcc']
			], [
			], 'content');
		}
	break;

	/*
	*  Send faq
	*/
	case 'send': 
		is_ajax() or die('Hacking attempt!');

		// Filters
		$id = intval($_POST['id']);
		
		/* $delete = '';
		if($_POST['delete']){
			foreach($_POST['delete'] as $del){
				$delete .= 'images = REPLACE(
					images, \''.$del.'|\', \'\'
				), ';
			}
		} */
		
		if ((!$id AND !$user['faq_add']) OR ($id AND !$user['faq_edit']))
			die('no_acc');
		
		// SQL SET
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_faq` SET '.$delete.'
				title = \''.text_filter($_POST['title'], 255, true).'\',
				content = \''.text_filter($_POST['content']).'\''.(
					$id ? '' : ', user_id = '.$user['id']
				).(
			$id ? ' WHERE id = '.$id : ''
		));
		
		$id = $id ? $id : intval(
			mysqli_insert_id($db_link)
		);
		
		/* // Is file upload
		if($_FILES){
			
			$images = [];
			
			// Upload max file size
			$max_size = 10;
			
			// path
			$dir = ROOT_DIR.'/uploads/images/faq/';
			
			// Is not dir
			if(!is_dir($dir.$id)){
				@mkdir($dir.$id, 0777);
				@chmod($dir.$id, 0777);
			}
			
			$dir = $dir.$id.'/';
			
			foreach($_FILES["image"]["error"] as $key => $error){
				
				// temp file
				$tmp = $_FILES['image']['tmp_name'][$key];
				
				$type = mb_strtolower(pathinfo($_FILES['image']['name'][$key], PATHINFO_EXTENSION));
				
				// Check
				if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array(
					$type, ['jpeg', 'jpg', 'png', 'gif']
				)){
					echo 'err_image_type';
					die;
				}
				if($_FILES['image']['size'][$key] >= 1024*$max_size*1024){
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
			if($images){
				db_query('UPDATE `'.DB_PREFIX.'_faq` SET 
						images = CONCAT(images, \''.implode('|', $images).'|\')
				WHERE id = '.$id);
			}
		} */
		
		
		die('OK');
	break;

    /*
	* All faqs
	*/
    default:
        $meta['title'] = $lang['allFaq'];
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		
		$count = 20;
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
                f.*,
                u.name as user_name,
                u.lastname as user_lastname
			FROM `'.DB_PREFIX.'_faq` f
			LEFT JOIN `'.DB_PREFIX.'_users` u 
				ON f.user_id = u.id
			WHERE 1 '.(
			$query ? 'AND MATCH (f.title, f.content) AGAINST (\''.$query.'\' IN BOOLEAN MODE) ' : ''
		).'ORDER BY f.id DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0; // f.title LIKE \'%'.$query.'%\' OR f.content LIKE \'%'.$query.'%\'
			foreach($sql as $row){
				tpl_set('faq/item', [
					'id' => $row['id'],
					'title' => $row['title'],
					'content' => $row['content'],
                    'date' => $row['date'],
                    'user-name' => $row['user_name'],
                    'user-lastname' => $row['user_lastname'],
                    'user-id' => $row['user_id']
                ],[
                    'can_add' => $user['faq_add'],
                    'can_edit' => $user['faq_edit'],
                    'can_del' => $user['faq_del']
				], 'faq');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
            //tpl_set('noContent', [
             //   'text' => $lang['noFaqs']
            //],false, 'faq');
        }
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['faq'],
			]));
		}
		tpl_set('faq/main', [
			'title' => $meta['title'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'faq' => $tpl_content['faq']
		], [
			'add' => $user['faq_add']
		], 'content');
    break;
}

?>