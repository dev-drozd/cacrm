<?php
/**
 * @appointment Tablet feedback
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
*/

$store_id = (int)array_search(
	$_SERVER['REMOTE_ADDR'], $config['object_ips']
);

//if($_SERVER['REMOTE_ADDR'] == '134.249.157.109'){
	//$store_id = 2;
//}

if($_SERVER['REMOTE_ADDR'] == '79.164.88.156'){
	$store_id = 4;
}

switch($route[1]){
	
	case 'all_models':
		$type = (int)$_GET['type'];
		echo json_encode(db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_inventory_models` WHERE '.(
			$type ? 'category_id = '.$type : 'category_id < 0'
		).' ORDER BY `name` ASC LIMIT 1000', true, false, function($a){
			return [$a['id'], $a['name']];
		}));
		die;
	break;
	
	case 'send_acception':

		$first_name = trim(text_filter($_POST['first_name'], 25, false) ?: text_filter($_POST['cname'], 25, false));
		$last_name = trim(text_filter($_POST['last_name'], 25, false));
		$sex = text_filter($_POST['sex'], 20, false);
		$phone = text_filter($_POST['phone'], 255, false);
		$email = text_filter($_POST['email'], 50, false);
		$date = explode('-', text_filter($_POST['date'], null, false));
		$zipcode = text_filter($_POST['zipcode'], 255, false);
		$state = text_filter($_POST['state'], 255, false);
		$city = text_filter($_POST['city'], 255, false);
		$address = text_filter($_POST['address'], 255, false);
		$password = md5(md5(trim($_POST['password'])));

		
		db_query('INSERT INTO `'.DB_PREFIX.'_users` SET
			name = \''.$first_name.'\',
			lastname = \''.$last_name.'\',
			sex = \''.$sex.'\',
			birthday = \''.intval($date[2]).'.'.intval($date[0]).'.'.intval($date[1]).'\',
			phone = \''.$phone.'\',
			sms = \''.$phone.'\',
			address = \''.$address.'\',
			country = \'US\',
			state = \''.$state.'\',
			city = \''.$city.'\',
			zipcode = \''.$zipcode.'\',
			group_ids = 5,
			email = \''.$email.'\''
		);
		
		$user_id = intval(mysqli_insert_id($db_link));
		
		$type = (int)$_POST['type'];
		$brand = (int)$_POST['brand'];
		$model = (int)($_POST['brand']);
		
		db_query('INSERT INTO `'.DB_PREFIX.'_customer_acceptions` SET
			customer_id = \''.$user_id.'\',
			type = \''.$type.'\',
			brand = \''.$brand.'\',
			model = \''.$model.'\'
		');
		
		$aid = intval(mysqli_insert_id($db_link));
		
		db_query('
			INSERT INTO `'.DB_PREFIX.'_notifications` SET 
				type = \'new_acception\', 
				customer_id = '.$user_id.', 
				link = \'/inventory/step/?acception='.$aid.'&user='.$user_id.'&type='.$type.'&brand='.$brand.'&model='.$model.'\'., 
				store_id = '.$store_id.', 
				id = '.$aid
		);
		
		if(isset($_FILES['image']) && !$_FILES['image']['error']){
			// Upload max file size
			$max_size = 10;
			
			// path
			$dir = ROOT_DIR.'/uploads/images/users/';
			
			// Is not dir
			if(!is_dir($dir.$user_id)){
				@mkdir($dir.$user_id, 0777);
				@chmod($dir.$user_id, 0777);
			}
			
			$dir = $dir.$user_id.'/';
			
			// temp file
			$tmp = $_FILES['image']['tmp_name'];
			
			$type = mb_strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
			
			// Check
			if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime'])){
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
				
				db_query('UPDATE `'.DB_PREFIX.'_users` SET image = \''.$rename.'\' WHERE id = '.$user_id);
			}
		}
		header('Location: /tablet?token=jY6ncdCilUH8puQ8xB');
		die;
	break;
	
	case 'send_feedback':
		if($issue_id = (int)$_POST['issue_id']){
			
			$rate = (int)$_POST['rate'];
			$staff_id = (int)$_POST['staff_id'];
			$customer_id = (int)$_POST['customer_id'];
			
			// Send feedback
			db_query('INSERT INTO `'.DB_PREFIX.'_feedback` SET
				type = 3,
				issue_id = '.$issue_id.',
				staff_id = \''.$staff_id.'\',
				customer_id = \''.$customer_id.'\',
				ratting = '.$rate
			);
				
			// ++ points
			if ($rate == 5) {
				$points = floatval($config['user_points']['feedback']['sms_points']);
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$staff_id.',
					issue_id = '.$issue_id.',
					action = \'feedback\',
					point = \''.$points.'\''
				);	
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$staff_id
				);
			}
		}
		die;
	break;
	
	case 'acception':
/* 		if(!isset($_GET['token']) OR $_GET['token'] !== 'jY6ncdCilUH8puQ8xB'){
			if(!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] !== $config['tablet_user'] || $_SERVER['PHP_AUTH_PW'] !== $config['tablet_password']){
				header('WWW-Authenticate: Basic realm="FORBIDEN"');
				header('HTTP/1.0 401 Unauthorized');
				die('Access denied');
			}	
		} */
		$states_options = '<option>State</option>';
		db_multi_query('
			SELECT code, name
			FROM `'.DB_PREFIX.'_states`
			WHERE country = \'US\'', true, false, function($a) use(&$states_options){
				$states_options .= '<option value="'.$a['code'].'">'.$a['name'].'</option>';
		});
		tpl_set('tablet/customer', [
			'states' => $states_options
		], [], 'content');
	break;
	
	case null:
/* 		if(!isset($_GET['token']) OR $_GET['token'] !== 'jY6ncdCilUH8puQ8xB'){
			if(!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] !== $config['tablet_user'] || $_SERVER['PHP_AUTH_PW'] !== $config['tablet_password']){
				header('WWW-Authenticate: Basic realm="FORBIDEN"');
				header('HTTP/1.0 401 Unauthorized');
				die('Access denied');
			}	
		} */
		tpl_set('tablet/main', [], [], 'content');
	break;
}

if($_SERVER['REQUEST_METHOD'] === 'GET'){
	if(!$store_id){
		echo '<h1>IP '.$_SERVER['REMOTE_ADDR'].' does not belong to one of the stores</h1>';
		die;
	}
	echo tpl_set('tablet/index', [
		'store-id' => $store_id,
		'content' => tpl_get('content'),
		'token' => md5($store_id.$config['tablet_user'].$config['tablet_password'])
	], []);	
} else
	echo tpl_get('content');
die;

//if($store_id > 0){
	//if(!isset($_GET['token']) OR $_GET['token'] !== 'jY6ncdCilUH8puQ8xB'){
	//	if(!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] !== $config['tablet_user'] || $_SERVER['PHP_AUTH_PW'] !== $config['tablet_password']){
	//		header('WWW-Authenticate: Basic realm="FORBIDEN"');
	//		header('HTTP/1.0 401 Unauthorized');
	//		die('Access denied');
	//	}	
	//}
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		echo '<pre>';
		print_r($_POST);
		print_r($_FILES);
		
		die;
		
		if($issue_id = (int)$_POST['issue_id']){
			
			$rate = (int)$_POST['rate'];
			$staff_id = (int)$_POST['staff_id'];
			$customer_id = (int)$_POST['customer_id'];
			
			// Send feedback
			db_query('INSERT INTO `'.DB_PREFIX.'_feedback` SET
				type = 3,
				issue_id = '.$issue_id.',
				staff_id = \''.$staff_id.'\',
				customer_id = \''.$customer_id.'\',
				ratting = '.$rate
			);
				
			// ++ points
			if ($rate == 5) {
				$points = floatval($config['user_points']['feedback']['sms_points']);
				db_query('INSERT INTO `'.DB_PREFIX.'_inventory_status_history` SET
					staff_id = '.$staff_id.',
					issue_id = '.$issue_id.',
					action = \'feedback\',
					point = \''.$points.'\''
				);	
				db_query(
					'UPDATE `'.DB_PREFIX.'_users`
						SET points = points+'.$points.'
					WHERE id = '.$staff_id
				);
			}
		}
	} else
		echo tpl_set('tablet/customer', [
			'store-id' => $store_id,
			'token' => md5($store_id.$config['tablet_user'].$config['tablet_password'])
		], []);	
		die;
		echo tpl_set('tablet', [
			'store-id' => $store_id,
			'token' => md5($store_id.$config['tablet_user'].$config['tablet_password'])
		], []);	
//} else
//	header('Location: /');
die;
?>