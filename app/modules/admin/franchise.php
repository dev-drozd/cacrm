<?php
/**
 * @appointment Franchise admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

function compilePhone($a = '', $b = false){
	return '<div class="phone" data-code="+1">
		<input type="tel" name="phone[]" placeholder="(XXX) XXX-XXXX" class="sfWrap" value="'.$a.'" oninput="Phones.format(event);" onkeyup="Phones.format(event);" onblur="Phones.blur(this);" required>
		<input type="radio" name="sms" value="'.($b == 1 ? '1" checked' : ($b == 2 ? '0"' : '0" checked')).'>
	</div>';
}

switch($route[1]){
	
	/*
	* Delete franchise
	*/
	case 'del':
		is_ajax() or die('hacking!');
		$id = intval($_POST['id']);
		
		if(db_multi_query('SELECT id FROM `'.DB_PREFIX.'_franchises` WHERE del = 0 AND id = '.$id)){
			db_query('UPDATE `'.DB_PREFIX.'_franchises` SET del = 1 WHERE id = '.$id);
			echo 'OK';
		} else
			echo 'ERR';
		die;
	break;
	
	/*
	* Send franchise
	*/
	case 'send':
	
		$id = intval($_POST['id']);
		$owner_id = (int)$_POST['owner_id'];
		$res = [];
		
		$name = text_filter($_POST['name'], 100, false);
		$phones = str_replace(
			['(',')', '-'], '', phones_filter($_POST['phone'], $_POST['sms'])
		);
		$address = text_filter($_POST['address'], 255, false);
		$email = text_filter($_POST['email'], 50, false);
		$website = text_filter($_POST['website'], 255, false);
		$ip = text_filter($_POST['ip'], 50, false);
		
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_franchises` SET
					owner_id = '.$owner_id.',
					name = \''.$name.'\',
					address = \''.$address.'\',
					phone = \''.$phones.'\',
					sms = \''.explode(',', $phones)[0].'\',
					email = \''.$email.'\',
					website = \''.$website.'\',
					ip = '.($ip ? 'INET_ATON(\''.$ip.'\')' : 'NULL').'
					'.(
				$id ? 'WHERE id = '.$id : ''
			));
			
			$id = $id ? $id : intval(
				mysqli_insert_id($db_link)
			);
			
			if($owner_id){
				db_query('UPDATE `'.DB_PREFIX.'_users` SET franchise_id = '.$id.' WHERE id = '.$owner_id);
			}
			
			// Is file upload
			if($_FILES['image']){
				
				// Upload max file size
				$max_size = 10;
				
				// path
				$dir = ROOT_DIR.'/uploads/images/franchises/';
				
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
					
					db_query('UPDATE `'.DB_PREFIX.'_franchises` SET image = \''.$rename.'\' WHERE id = '.$id);
				}
			}
			$res['err'] = 0;
			$res['id'] = $id;
		} else
			$res['err'] = 'err_email';
		
		echo json_encode($res);
		die;
	break;
	
	/*
	* Add or edit franchise
	*/
	case 'view':
		$id = (int)$route[2];
		if($row = db_multi_query('SELECT f.*, INET_NTOA(f.ip) as ip, CONCAT(u.name, \' \', u.lastname) as owner_name FROM `'.DB_PREFIX.'_franchises` f LEFT JOIN `'.DB_PREFIX.'_users` u ON f.owner_id = u.id WHERE f.id = '.$id)){
			
			$phones = '';
			if($row['phone']){
				foreach(explode(',', $row['phone']) as $phone){
					$phones .= '<a href="tel:'.$phone.'">'.$phone.'</a><br>';
				}
			}
			
			tpl_set('franchise/view', [
				'id' => $id,
				'owner-id' => $row['owner_id'],
				'owner-name' => $row['owner_name'],
				'name' => $row['name'],
				'email' => $row['email'],
				'phones' => $phones,
				'address' => $row['address'],
				'amount' => $row['amount'],
				'website' => $row['website'],
				'ip' => $row['ip'],
				'date' => convert_date($row['date'], true),
				'contract' => $row['contract'],
				'image' => $row['image'],
				'send' => $id ? 'Edit' : 'Add'
			], [
				'deleted' => $row['del'],
				'image' => $row['image'],
				'edit' => $route[1] == 'edit',
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		} else {
			tpl_set('forbidden', [
				'text' => 'Franchise is not exists'
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		}
	break;
	
	/*
	* Add or edit franchise
	*/
	case 'add':
	case 'edit':
		$id = intval($route[2]);
		$row = [];

		if ($id) {
			if ($row = db_multi_query('SELECT f.*, INET_NTOA(f.ip) as ip, CONCAT(u.name, \' \', u.lastname) as owner_name FROM `'.DB_PREFIX.'_franchises` f LEFT JOIN `'.DB_PREFIX.'_users` u ON f.owner_id = u.id WHERE f.id = '.$id)) {
				
			} else {
				tpl_set('forbidden', [
					'text' => 'Client is not exists'
				], [
					'costum-group' => in_array(1, explode(
						',', $user['group_ids']
						)
					)
				], 'content');
			}
		}
		
		$phones = '';
		
		if($row['phone']){
			foreach(explode(',', $row['phone']) as $phone){
				$phones .= compilePhone($phone, ($phone == $row['sms'] ? 1 : false));
			}
		} else
			$phones = compilePhone();
		
		tpl_set('franchise/form', [
			'title' => ($id ? 'Edit ' : 'Add ').'franchise',
			'id' => $id,
			'owner-id' => $row['owner_id'] ?? 0,
			'owner-name' => $row['owner_name'],
			'name' => $row['name'],
			'email' => $row['email'],
			'phones' => $phones,
			'address' => $row['address'],
			'amount' => $row['amount'],
			'website' => $row['website'],
			'ip' => $row['ip'],
			'contract' => $row['contract'],
			'image' => $row['image'],
			'send' => $id ? 'Edit' : 'Add'
		], [
			'deleted' => $row['del'],
			'image' => $row['image'],
			'edit' => $route[1] == 'edit',
			'costum-group' => in_array(1, explode(
				',', $user['group_ids']
				)
			)
		], 'content');
	break;
	
	/*
	* All franchises
	*/
	case null:
		$meta['title'] = 'Franchise';
		
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 20;
		
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
				* 
			FROM `'.DB_PREFIX.'_franchises` WHERE 1 '.(
				$query ? ' AND `name` LIKE \'%'.$query.'%\' ' : ''
			).' ORDER BY `id` DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			
			$phones = '';
			if($row['phone']){
				foreach(explode(',', $row['phone']) as $phone){
					$phones .= '<a href="tel:'.$phone.'">'.$phone.'</a><br>';
				}
			}
			
			foreach($sql as $row){
				tpl_set('franchise/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'phone' => $phones,
					'image' => $row['image'],
					'email' => $row['email']
				], [
					'deleted' => $row['del'],
					'image' => $row['image']
				], 'items');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-$i));
	
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['items'],
			]));
		}
		
		tpl_set('franchise/main', [
			'items' => $tpl_content['items'] ?: '',
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
		], [
			'costum-group' => in_array(1, explode(
				',', $user['group_ids']
				)
			)
		], 'content');
	break;
}

?>