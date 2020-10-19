<?php

print_a($user);
die;

switch($route[1]) {    
    
    /*
	* Send user points
	*/
	default:
		is_ajax() or die('hacking');
		$id = intval($_POST['id']);
		$type = $id ? 'edit' : 'add';
		
		// Filters
		$name = text_filter($_POST['name'], 25, false);
		$lastname = text_filter($_POST['lastname'], 25, false);
		$phone = text_filter($_POST['phone'], 25, false);
		$address = text_filter($_POST['address'], 255, false);
		$ver = text_filter($_POST['addressConf'], 10, false);
		$email = text_filter($_POST['email'], 50, false);
		$password = text_filter($_POST['password'], 32, false);
		$group_ids = ids_filter($_POST['group_id']);
		
		// Check
		preg_match("/^[A-Za-za-яА-ЯїЇіІЄєЎўҐґ']+$/iu", $name) or die('Name_not_valid');
		preg_match("/^[A-Za-za-яА-ЯїЇіІЄєЎўҐґ']+$/iu", $lastname) or die('Lastname_not_valid');
		preg_match("/^[0-9-(-+]+$/", $phone) or die('phone_not_valid');
		$e = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$email.'\''.(
			$id ? ' AND id != '.$id : ''
		));
		if(!filter_var($email, FILTER_VALIDATE_EMAIL) OR $e['count'] > 0){
			echo 'err_email';
			die;			
		}

		$sql = '';
		if($_POST['del_image']) $sql .= ' image = \'\',';
		if($password) $sql .= ' hid = \''.md5(md5($password).md5($_SERVER['REMOTE_ADDR']).time()).'\', password = \''.md5(md5($_POST['password'])).'\',';
		db_query((
			$id ? 'UPDATE' : 'INSERT INTO'
		).' `'.DB_PREFIX.'_users` SET
				name = \''.$name.'\',
				lastname = \''.$lastname.'\',
				phone = \''.$phone.'\',
				address = \''.$address.'\',
				ver = \''.$ver.'\',
				email = \''.$email.'\','.$sql.' 
				group_ids = \''.$group_ids.'\''.(
			$id ? ' WHERE id = '.$id : ''
		));
		
		$id = $id ? $id : intval(mysqli_insert_id($db_link));
		
		// Is file upload
		if($_FILES){
			
			// Upload max file size
			$max_size = 10;
			
			// path
			$dir = ROOT_DIR.'/uploads/images/';
			
			// Is not dir
			if(!is_dir($dir.$id)){
				@mkdir($dir.$id, 0777);
				@chmod($dir.$id, 0777);
			}
			
			$dir = $dir.$id.'/';
			
			// temp file
			$tmp = $_FILES['image']['tmp_name'];
			
			$type = mb_strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
			
			// Check
			if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array($type, ['jpeg', 'jpg', 'png', 'gif'])){
				echo 'err_image_type';
				die;
			}
			if($_FILES['image']['size'] >= 1024*$max_size*1024){
				echo 'err_file_size';
				die;
			}
			
			// New name
			$rename = uniqid('', true).'.'.$type;
			
			// Upload image
			if(move_uploaded_file($tmp, $dir.$rename)){
				
				$img = new Imagick($dir.$rename);
				
				// 1920
				if($img->getImageWidth() > 1920){
					$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
					auto_rotate_image($img);
					$img->stripImage();
					$img->writeImage($dir.$rename);
				}
				
				// 300x300
				$img->cropThumbnailImage(300, 300);
				$img->stripImage();
				$img->writeImage($dir.'preview_'.$rename);
				
				// 94x94
				$img->cropThumbnailImage(94, 94);
				$img->stripImage();
				$img->writeImage($dir.'thumb_'.$rename);
				$img->destroy();
				
				db_query('UPDATE `'.DB_PREFIX.'_users` SET image = \''.$rename.'\' WHERE id = '.$id);
			}
		}
		echo $id;
		die;
	break;
}
?>