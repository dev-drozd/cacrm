<?php
/**
 * @appointment Store admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

require_once 'functions/store.php';

function delivery($id, $lid = 0){
	
	if($post = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_blog` WHERE id = '.$id)){
		
		// tmp
		$to = '';
		$to_header = '';
		
		// All users
		foreach(db_multi_query('SELECT email FROM `'.DB_PREFIX.'_users` WHERE '.implode(
			' OR ', array_map(function($a){
					return 'FIND_IN_SET('.intval($a).', `group_ids`)';
				}, $_POST['groups']
			)
		).(
			$lid ? ' AND `id` > '.$lid : ''
		).' ORDER BY `id` LIMIT 50', true) as $row){
			if($to){
				$to .= ', '.$row['email'];
				$to_header .= ', '.$row['name'].' <'.$row['email'].'>';
			} else {
				$to = $row['email'];
				$to_header = $row['name'].' <'.$row['email'].'>';
			}
			$lid = $row['id'];
		}

		// Headers
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		$headers .= 'To: '.$to_header. "\r\n";
		$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
		
		// Send
		mail($to, 'New post', tpl_set('store/blog/letter', [
			'title' => $post['name'],
			'datetime' => $post['date'],
			'content' => $post['content'],
			'url' => '/post/'.$id
		], []), $headers);
	}
	
	return json_encode([
		'id' => $id,
		'last_id' => $lid,
	]);
}

if($user['commerce'] > 0){
 
	switch($route[1]){
		/*
		* Service categories
		*/
		case 'service_categories':	
			
			$meta['title'] = $lang['allCategories'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					tb1.id, tb1.name, tb2.name as pname, tb1.parent_id
				FROM `'.DB_PREFIX.'_service_categories` tb1
				LEFT JOIN `'.DB_PREFIX.'_service_categories` tb2
					ON tb1.parent_id = tb2.id	'.(
				$query ? 'WHERE (tb1.name LIKE \'%'.$query.'%\' OR tb2.name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY tb1.id DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('store/service_categories/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'parent-name' => $row['pname'] ?? '',
						'parent-json' => json_encode($row['parent_id'] ? [
							$row['parent_id'] => ['name' => $row['pname']]
						] : [])
					], [
						'edit-services' => $user['edit_services'],
						'del-services' => $user['del_services']
					], 'categories');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noCategories']
				], [], 'categories');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['categories'],
				]));
			}
			tpl_set('store/service_categories/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'invCategories' => $tpl_content['categories']
			], [
				'e-order' => $counters['e_order'],
				'owner' => in_array(1, explode(',', $user['group_ids'])),
				'add-services' => $user['add_services'],
				'pages' => ($user['add_pages'] or $user['edit_pages']),
				'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
				'services' => ($user['add_services'] or $user['edit_services'])
			], 'content');
		break;
		
		/*
		* Select pages
		*/
		case 'allPages':
		
			$s = (int)$_GET['s'];
			
			$query = text_filter($_REQUEST['q'] ?? $_REQUEST['query'], 100, false);
			
			$pages = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS id, name
					FROM `'.DB_PREFIX.'_pages` '.(
					$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : 'ORDER BY `id`'.(
						$s ? ' = '.$s : ''
					).' DESC '
				).'LIMIT 20', true
			);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			die(json_encode([
				'list' => $pages,
				'count' => $res_count,
			]));
		break;
		
		/*
		* Select nav
		*/
		case 'allNav':
		
			$query = text_filter($_REQUEST['q'] ?? $_REQUEST['query'], 100, false);
			
			$s = (int)$_GET['s'];
			$id = (int)$_POST['id'];
			
			$pages = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS id, name
					FROM `'.DB_PREFIX.'_navigation` WHERE 1 '.(
					$query ? 'AND (name LIKE \'%'.$query.'%\') ' : 'ORDER BY `id`'.(
						$s ? ' = '.$s : ''
					).' DESC '
				).(
					$id ? 'AND id != '.$id : ''
				).'LIMIT 20', true
			);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
			die(json_encode([
				'list' => $pages,
				'count' => $res_count,
			]));
		break;
		
		case 'delNav':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query('DELETE FROM `'.DB_PREFIX.'_navigation` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		break;
		
		/*
		* Nav method sort
		*/
		case 'stNavPriority':
			is_ajax() or die('Hacking attempt!');
			$i = 1;
			foreach($_POST as $row){
				db_query('UPDATE `'.DB_PREFIX.'_navigation` SET sort = '.$i.' WHERE id = '.$row['id']);
				$i++;	
			}
			die;
		break;
		
		/*
		* Select nav items
		*/
		case 'nav':
		
			$parent = (int)$route[2];
			switch($route[2]){
				
				case 'send':
				
					$id = (int)$_POST['id'];
					$parent_id = (int)$_POST['parent_id'];
					$name = text_filter($_POST['name'], 255, false);
					
					if(in_array($_POST['act_type'], ['empty','url','page','blog'])){
						$nav_id = (int)$_POST[$_POST['act_type'].'_id'];
						db_query((
							$id ? 'UPDATE' : 'INSERT INTO'
						).' `'.DB_PREFIX.'_navigation` SET
								name = \''.$name.'\',
								url = \''.db_escape_string($_POST['url']).'\',
								act_type = \''.$_POST['act_type'].'\',
								parent_id = '.$parent_id.',
								nav_id = '.$nav_id.''.(
							$id ? ' WHERE id = '.$id : ''
						));
						
						echo 'OK';
					}
					die;
				break;
				
				case 'add': case 'edit':
					$id = (int)$route[3];
					$row = [];
					if($route[2] == 'edit' && $id > 0) $row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_navigation` WHERE id = '.$id);
					tpl_set('store/nav/form', [
						'id' => $row['id'],
						'name' => $row['name'],
						'url' => $row['url'],
						'nav-id' => $row['nav_id'],
						'parent-id' =>  ($route[2] == 'add' && $id) ? $id : $row['parent_id'],
						'act-type' => $row['act_type'],
						'types' => str_ireplace('value="'.$row['act_type'].'"','value="'.$row['act_type'].'" selected','
							<option value="empty">Empty</option>
							<option value="url">Link</option>
							<option value="page">Page</option>
							<option value="blog">Blog</option>'
						)
					], [
						'e-order' => $counters['e_order'],
						'parent-id' =>  ($route[2] == 'add' && $id) ? $id : $row['parent_id'],
						'act-blog' => $row['act_type'] == 'blog',
						'act-page' => $row['act_type'] == 'page',
						'act-url' => $row['act_type'] == 'url',
						'select' => ($row['act_type'] && $row['act_type'] != 'url')
					], 'content');
				break;
				
				case null:
				case $parent > 0:
				
					$meta['title'] = 'All items';
					$query = text_filter($_POST['query'], 255, false);
					$page = (int)$_POST['page'];
					$count = 10;
					
					if($sql = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS
							id, name, act_type
						FROM `'.DB_PREFIX.'_navigation` WHERE parent_id = '.$parent.' '.(
						$query ? 'WHERE (`name` LIKE \'%'.$query.'%\') ' : 'ORDER BY `sort` ASC '
					).'LIMIT '.($page*$count).', '.$count, true)){
						$i = 0;
						foreach($sql as $row){
							tpl_set('store/nav/item', [
								'id' => $row['id'],
								'name' => $row['name'],
								'action-type' => $row['act_type']
							], [], 'items');
							$i++;
						}
						$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					} else {
						tpl_set('noContent', [
							'text' => 'Items not found'
						], [], 'items');
					}
					
					$left_count = intval(($res_count-($page*$count)-$i));
					
					$nav_arr = [];
					$nav = '';
					
					if($_POST){
						exit(json_encode([
							'res_count' => $res_count,
							'left_count' => $left_count,
							'content' => $tpl_content['items'],
						]));
					} elseif($parent){
						function get_cat($a){
							global $nav_arr;
							if(!$a) return;
							if($row = db_multi_query('SELECT id, name, parent_id FROM `'.DB_PREFIX.'_navigation` WHERE id = '.$a)){
								$nav_arr[] = $row;
								get_cat($row['parent_id']);
							}
						}
						get_cat($parent);
						foreach(array_reverse($nav_arr) as $item){
							$nav .= ' / <a href="/store/nav/'.$item['id'].'" onclick="Page.get(this.href); return false;">'.$item['name'].'</a>';
						}
					}
					
					tpl_set('store/nav/main', [
						'res_count' => $res_count,
						'more' => $left_count ? '' : ' hdn',
						'nav' => $nav,
						'e-order' => $counters['e_order'],
						'items' => $tpl_content['items'],
						'parent' => $parent
					], [
						'e-order' => $counters['e_order'],
						'nav' => $nav,
						'parent' => $parent
					], 'content');
			}
		break;
		
		/*
		* Select service categories
		*/
		case 'allServiceCategories':
			$id = intval($_POST['id']);
			$lId = intval($_POST['lId']);
			$nIds = ids_filter($_POST['nIds']);
			$query = text_filter($_POST['query'], 100, false);
			$categories = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS tb1.id, CONCAT(
				IFNULL(tb2.name, \'\'), IF(
					tb2.name IS NOT NULL, \' <span class="fa fa-angle-right"></span> \', \'\'
				), tb1.name
				) as name
					FROM `'.DB_PREFIX.'_service_categories` tb1
					LEFT JOIN `'.DB_PREFIX.'_service_categories` tb2
					ON tb1.parent_id = tb2.id
					WHERE 1'.(
					$lId ? ' AND tb1.id < '.$lId : ''
				).(
					$query ? ' AND (tb1.name LIKE \''.$query.'%\' OR tb2.name LIKE \''.$query.'%\')' : ''
				).($nIds ? ' AND tb1.id NOT IN('.$nIds.')' : '').(
					$id ? ' AND tb1.id != '.$id.' AND tb1.parent_id != '.$id : ''
				).' ORDER BY tb1.name ASC LIMIT 20', true
			);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $categories,
				'count' => $res_count,
			]));
		break;
	
		/*
		* Delete service category
		*/
		case 'delServiceCategory':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['delete_inventory_categories']){
				db_query('DELETE FROM `'.DB_PREFIX.'_service_categories` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('ERR');
		break;
		
			
		/*
		*  Send service category
		*/
		case 'sendServiceCategory':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if(($id AND $user['edit_services']) OR (!$id AND $user['add_services'])){
				db_query(($id ? 'UPDATE' : 'INSERT INTO').' `'.DB_PREFIX.'_service_categories` SET
					name = \''.text_filter($_POST['name'], 50, false).'\',
					description = \''.text_filter($_POST['description'], 255, false).'\',
					keywords = \''.text_filter($_POST['keywords'], null, false).'\',
					parent_id = '.intval($_POST['parent']).(
						$id ? ' WHERE id = '.$id : ''
				));
				echo $id ? $id : intval(mysqli_insert_id($db_link));
			} else
				echo 'ERR';
			die;
		break;
		
		/*
		* Send discount
		*/
		case 'send_discount':
			is_ajax() or die('Hacking attempt!');
			$amount = floatval($_POST['amount']);
			$customer = floatval($_POST['customer']);
			$code = substr(md5(uniqid().time()), 0, 10);
			
			if (!$amount OR !$customer)
				die('no_info');
			
			db_query('INSERT INTO `'.DB_PREFIX.'_store_discounts` SET
				id = \''.$code.'\',
				amount = \''.$amount.'\',
				date_exp = \''.date('Y-m-d', strtotime('next month')).'\',
				customer_id = '.$customer
			);
			die('OK');
		break;
		
		/*
		* Delete discount
		*/
		case 'del_discount':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$d = db_multi_query('SELECT used FROM `'.DB_PREFIX.'_store_discounts` WHERE id = '.$id);
			
			if ($d['used'])
				die('ERR');
			
			db_query('DELETE FROM `'.DB_PREFIX.'_store_discounts` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		break;
	
		/*
		* All discounts
		*/
		case 'discounts': 
			$meta['title'] = 'Discounts';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
				d.*,
				u.id as user_id,
				CONCAT(u.name, \' \', u.lastname) as user_name
			FROM `'.DB_PREFIX.'_store_discounts` d
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id = d.customer_id
			'.(
				$query ? 'WHERE d.id LIKE \'%'.$query.'%\' ' : ''
			).'ORDER BY d.id LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('store/discounts/item', [
						'id' => $row['id'],
						'amount' => $row['amount'],
						'date' => $row['date_exp'],
						'user-name' => $row['user_name'],
						'user-id' => $row['user_id']
					], [
						'customer' => $row['customer_id'],
						'used' => $row['used']
					], 'discounts');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['discounts'],
				]));
			}
			tpl_set('store/discounts/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'discounts' => $tpl_content['discounts'],
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order'],
				'pages' => ($user['add_pages'] or $user['edit_pages']),
				'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
				'services' => ($user['add_services'] or $user['edit_services']),
				'owner' => in_array(1, explode(',', $user['group_ids']))
			], 'content');
		break;
	
		/*
		*  Send post
		*/
		case 'send_service':
			is_ajax() or die('Hacking attempt!');
			
			$id = intval($_POST['id']);
			
			if(($id AND $user['edit_services']) OR (!$id AND $user['add_services'])){
				
				$pathname = trim($_POST['pathname']);
				$old_pathname = trim($_POST['old_pathname'] ?? '');
				
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_store_services` SET
						name =\''.text_filter($_POST['name'], 255, false).'\',
						'.(!$user['approve_services'] ? '
						confirm = 0,
						' : '').'
						pathname = '.(
							$pathname ? '\''.text_filter($pathname, 255, false).'\'' : 'NULL'
						).',
						canonical =\''.text_filter($_POST['canonical'], 255, false).'\',
						title =\''.text_filter($_POST['title'], 255, false).'\',
						description =\''.text_filter($_POST['description'], 255, false).'\',
						keywords =\''.text_filter($_POST['keywords'], 255, false).'\',
						category_id =\''.intval($_POST['category']).'\',
						blog_id =\''.intval($_POST['blog']).'\',
						content =\''.text_filter($_POST['content']).'\',
						price =\''.floatval($_POST['price']).'\',
						currency =\''.(text_filter($_POST['currency'], 25, false) ?: 'USD').'\',
						declined = 0,
						declined_comment = \'\',
						icon =\''.text_filter($_POST['icon'], 255, false).'\''.(
							$id ? ' WHERE id = '.$id : ''
					)
				);
				$id = $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
				// Is file upload
				if($_FILES){
					
					// Upload max file size
					$max_size = 10;
					
					// path
					$dir = ROOT_DIR.'/uploads/images/services/';
					
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
					if ($_FILES['image']) {
						if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array($type, ['jpeg', 'jpg', 'png', 'gif'])){
							echo 'err_image_type';
							die;
						}
						if($_FILES['image'] AND $_FILES['image']['size'] >= 1024*$max_size*1024){
							echo 'err_file_size';
							die;
						}
						
					}
					
					// New name
					$rename = uniqid('', true).'.'.$type;
					
					// Upload image
					if(move_uploaded_file($tmp, $dir.$rename)){
						
						$img = new Imagick($dir.$rename);
						
						// 1920
						if($img->getImageWidth() > 500){
							$img->resizeImage(500, 0, imagick::FILTER_LANCZOS, 0.9);
							auto_rotate_image($img);
							$img->stripImage();
							$img->writeImage($dir.$rename);
						}
						
						$img->cropThumbnailImage(307, 218);
						auto_rotate_image($img);
						$img->writeImage($dir.'thumb_mws_'.$rename);
						$img->destroy();
						
						$img = new Imagick($dir.$rename);
						$img->cropThumbnailImage(204, 137);
						auto_rotate_image($img);
						$img->stripImage();
						$img->writeImage($dir.'thumb_iws_'.$rename);
						$img->destroy();
						
						// 94x94
						$img = new Imagick($dir.$rename);
						$img->cropThumbnailImage(94, 94);
						auto_rotate_image($img);
						$img->stripImage();
						$img->writeImage($dir.'thumb_'.$rename);
						$img->destroy();
						
						db_query('UPDATE `'.DB_PREFIX.'_store_services` SET image = \''.$rename.'\' WHERE id = '.$id);
					}
				}
				if($old_pathname){
					db_query('INSERT INTO `'.DB_PREFIX.'_redirect` SET url_from = \''.db_escape_string($old_pathname).'\', url_to = \''.db_escape_string($pathname).'\'');
				}
				echo $id;
			} else
				echo 'ERR';
			die;
		break;
		
		case 'services_image2':
		foreach(db_multi_query('SELECT id, image FROM `'.DB_PREFIX.'_store_services` WHERE image != \'\'', true) as $row){
			$dir = ROOT_DIR.'/uploads/images/services/';
			$dir = $dir.$row['id'].'/';
			$png = str_ireplace('.jpg','.png', $row['image']);
			if(file_exists($dir.$png)){
				$row['image'] = $png;
			}
			$image_types = getimagesize($dir.$row['image']);
			$old_name = $dir.$row['image'];
			$img = compressImage($dir.$row['image'], $dir.(
				$image_types[2] === IMAGETYPE_PNG ? str_ireplace('.png','.jpg',$row['image']) : $row['image']
			));
			if($image_types[2] === IMAGETYPE_PNG){
				$row['image'] = str_ireplace('.png','.jpg',$row['image']);
				@unlink($old_name);
			}
			
			$img->cropThumbnailImage(307, 218);
			$img->writeImage($dir.'thumb_mws_'.$row['image']);
			$img->destroy();
			
			$img = new Imagick($dir.$row['image']);
			$img->cropThumbnailImage(204, 137);
			$img->writeImage($dir.'thumb_iws_'.$row['image']);
			$img->destroy();
			
			$img = new Imagick($dir.$row['image']);
			$img->cropThumbnailImage(94, 94);
			$img->writeImage($dir.'thumb_'.$row['image']);
			$img->destroy();
			
			echo '<p><img src="/uploads/images/services/'.$row['id'].'/thumb_iws_'.$row['image'].'?v='.time().'">'.$row['id'].'</p>';
		}
		echo 'Complete';
		die;
		break;
		
		/*
		*  Services
		*/
		case 'services':
			
			if($route[2] == 'add' OR (
				$route[2] == 'edit' AND intval($route[3])
			)){
				$id = intval($route[3]);
				$type = $id ? 'Edit' : 'Add';
				$row = [];
				$meta['title'] = $type.' service';
				if ($id) {
					$row = db_multi_query('
						SELECT 
							s.*,
							c.name as catname,
							b.name as bname
						FROM `'.DB_PREFIX.'_store_services` s
						LEFT JOIN `'.DB_PREFIX.'_service_categories` c
							ON c.id = s.category_id
						LEFT JOIN `'.DB_PREFIX.'_store_blog` b
							ON b.id = s.blog_id
						WHERE s.id = '.$id
					);
				}

				$currency = '';
				foreach($config['currency'] as $k => $c) {
					$currency .= '<option value="'.$k.'"'.($k == $row['currency'] ? ' selected' : '').'>'.$k.' ('.$c['symbol'].')</option>';
				}
				
				tpl_set('store/services/form', [
					'id' => $id,
					'title' => $type.' service',
					'name' => htmlspecialchars($row['name'], ENT_QUOTES),
					'pathname' => $row['pathname'],
					'canonical' => $row['canonical'],
					'uri' => $row['pathname'] ?: 'services/'.$row['id'],
					'content' => htmlspecialchars($row['content'], ENT_QUOTES),
					'title' => htmlspecialchars($row['title'], ENT_QUOTES),
					'description' => htmlspecialchars($row['description'], ENT_QUOTES),
					'keywords' => htmlspecialchars($row['keywords'], ENT_QUOTES),
					'price' => floatval($row['price']),
					'currency' => $currency,
					'icon' => $row['icon'] ?: '',
					'send' => isset($route[3]) ? 'Save' : 'Send',
					'e-order' => $counters['e_order'],
					'image' => $row['image'],
					'category-id' => json_encode($row['category_id'] ? [
						$row['category_id'] => [
							'name' => $row['catname']
						]
					] : []),
					'blog-id' => json_encode($row['blog_id'] ? [
						$row['blog_id'] => [
							'name' => $row['bname']
						]
					] : []),
				], [
					'e-order' => $counters['e_order'],
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'add' => ($route[2] == 'add'),
					'edit' => $row,
					'image' => $row['image'],
					'icon' => $row['icon'],
					'confirm' => storeCanConfirmPost($row),
					'can-decline' => storeCanDeclinePost($row)
				], 'content');
			} else {
				$meta['title'] = 'Services';
				$query = text_filter($_POST['query'], 255, false);
				$page = intval($_POST['page']);
				
				$long_title = (int)$_REQUEST['long_title'];
				$long_description = (int)$_REQUEST['long_description'];
				$title = (int)$_REQUEST['title'];
				$description = (int)$_REQUEST['description'];
				$keywords = (int)$_REQUEST['keywords'];
				$canonical = (int)$_REQUEST['canonical'];
				$image = (int)$_REQUEST['image'];
				
				$count = 10;
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						* FROM `'.DB_PREFIX.'_store_services` WHERE 1 '.(
					$query ? 'AND `name` LIKE \'%'.$query.'%\' ' : ''
				).(
					$long_title ? 'AND CHAR_LENGTH(title) > 60 ' : ''
				).(
					$long_description ? 'AND CHAR_LENGTH(description) > 300 ' : ''
				).(
					$title ? 'AND title = \'\' ' : ''
				).(
					$description ? 'AND description = \'\' ' : ''
				).(
					$keywords ? 'AND keywords = \'\' ' : ''
				).(
					$canonical ? 'AND canonical = \'\' ' : ''
				).(
					$image ? 'AND image = \'\' ' : ''
				).'ORDER BY confirm ASC, id DESC LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('store/services/item', [
							'id' => $row['id'],
							'name' => $row['name'],
							'image' => $row['image'],
							'status' => storeGetPostStatusText($row)
						], [
							'confirm' => $row['confirm'],
							'edit-services' => $user['edit_services'],
							'del-services' => $user['del_services'],
							'image' => $row['image'],
							'status' => storeGetPostStatus($row)
						], 'services');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				} else {
					tpl_set('noContent', [
						'text' => 'There are no services'
					], [], 'services');
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['services'],
					]));
				}
				tpl_set('store/services/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'services' => $tpl_content['services'],
					'e-order' => $counters['e_order']
				], [
					'e-order' => $counters['e_order'],
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'add-services' => $user['add_services'],
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'store' => $route[1] == 'store'
				], 'content');
			}
		break;
		
		/*
		* Delete service
		*/
		case 'del_service':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['del_services']){
				db_query('DELETE FROM `'.DB_PREFIX.'_store_services` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('ERR');
		break;
		
		/*
		* Update order status
		*/
		case 'update_order_status':
			is_ajax() or die('Hacking attempt!');
			$status = intval($_POST['status']);
			if ($status) {
				$order = db_multi_query('SELECT status_id FROM `'.DB_PREFIX.'_orders` WHERE id = '.intval($_POST['id']));
				if ($order['status_id'] != $status) {
					db_query('UPDATE `'.DB_PREFIX.'_orders` SET status_id = '.$status.' WHERE id = '.intval($_POST['id']));
					if ($order['status_id'] == 1)
						db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'e_order\'');	
				}
				die('OK');
			}
		break;
		
		/*
		* Get order statuses
		*/
		case 'get_order_status':
			is_ajax() or die('Hacking attempt!');
			$status = '';
			if ($statuses = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_orders_status`', true)) {
				foreach($statuses as $s) {
					$status .= '<option value="'.$s['id'].'"'.($s['id'] == intval($_POST['status']) ? ' selected' : '').'>'.$s['name'].'</option>';
				}
			}
			echo $status;
			die;
		break;
		
		/*
		* Del order
		*/
		case 'delOrder':
			is_ajax() or die('Hacking attempt!');
			$order = db_multi_query('SELECT status_id FROM `'.DB_PREFIX.'_orders` WHERE id = '.intval($_POST['id']));
			if ($order['status_id'] == 1)
				db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count - 1 WHERE name = \'e_order\'');	
			db_query('UPDATE `'.DB_PREFIX.'_orders` SET del = 1 WHERE id = '.intval($_POST['id']));
			db_query('DELETE FROM `'.DB_PREFIX.'_invoices` WHERE order_id = '.intval($_POST['id']));
			die('OK');
		break;
		

		/*
		* Orders
		*/
		case 'orders':
			if (intval($route[2])) {
				$id = intval($route[2]);
				$meta['title'] = 'Order #'.$id;
				$products = '';
				
				if ($row = db_multi_query('SELECT
						o.*,
						u.id as uid,
						CONCAT(u.name, \' \', u.lastname) as uname,
						u.phone as uphone,
						u.address as uaddress,
						s.name as status,
						s.color,
						s.alt_color,
						d.name as delivery,
						d.price as delivery_price,
						d.currency as delivery_currency,
						p.name as payment,
						c.name as ucountry_name,
						st.name as ustate_name,
						ct.city as ucity_name,
						oc.name as country_name,
						ost.name as state_name,
						oct.city as city_name,
						i.id as invoice
					FROM `'.DB_PREFIX.'_orders` o 
					LEFT JOIN `'.DB_PREFIX.'_users` u 
						ON u.id = o.customer_id
					LEFT JOIN `'.DB_PREFIX.'_orders_status` s 
						ON s.id = o.status_id
					LEFT JOIN `'.DB_PREFIX.'_orders_delivery` d 
						ON d.id = o.delivery_id
					LEFT JOIN `'.DB_PREFIX.'_orders_payment` p 
						ON p.id = o.payment_id
					LEFT JOIN `'.DB_PREFIX.'_countries` c
						ON u.country = c.code
					LEFT JOIN `'.DB_PREFIX.'_states` st
						ON u.state = st.code
					LEFT JOIN `'.DB_PREFIX.'_cities` ct
						ON u.zipcode = ct.zip_code
					LEFT JOIN `'.DB_PREFIX.'_countries` oc
						ON o.country = oc.code
					LEFT JOIN `'.DB_PREFIX.'_states` ost
						ON o.state = ost.code
					LEFT JOIN `'.DB_PREFIX.'_cities` oct
						ON o.zipcode = oct.zip_code
					LEFT JOIN `'.DB_PREFIX.'_invoices` i 
						ON i.order_id = o.id
					WHERE o.del = 0 AND o.id = '.$id)
				) {
					if ($row['product_info']) {
					foreach(json_decode($row['product_info'], true) as $inv_id => $inv) {
						$products .= '<div class="tr">
								<div class="td">
										<a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
								</div>
								<div class="td w100">
									'.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format($inv['price'], 2, '.', '').'
								</div>
								<div class="td w10">
									yes
								</div>
							</div>';
						}
					}
					
					$delivery = '<div class="tr">
								<div class="td">
									'.$row['delivery'].'
								</div>
								<div class="td w100">
									'.($config['currency'][$row['delivery_currency'] ?: $row['currency']]['symbol']).number_format($row['delivery_price'], 2, '.', '').'
								</div>
								<div class="td w10">
								</div>
							</div>';
					
					tpl_set('store/orders/view', [
						'id' => $row['id'],
						'total' => number_format($row['total'], 2, '.', ''),
						'tax' => number_format($row['tax'], 2, '.', ''),
						'subtotal' => number_format($row['total'] - $row['tax'], 2, '.', ''),
						'currency' => $config['currency'][$row['currency']]['symbol'],
						'uid' => $row['uid'],
						'uname' => $row['uname'],
						'uphone' => $row['uphone'],
						'uaddress' => ($row['ucountry_name'] ? $row['ucountry_name'].' ' : '').($row['ustate_name'] ? $row['ustate_name'].' ' : '').($row['ucity_name'] ? $row['ucity_name'].' ' : '').$row['uaddress'],
						'daddress' => ($row['country_name'] ? $row['country_name'].' ' : '').($row['state_name'] ? $row['state_name'].' ' : '').($row['city_name'] ? $row['city_name'].' ' : '').$row['address'],
						'date' => $row['date'],
						'status' => $row['status'],
						'status-id' => $row['status_id'],
						'delivery' => $row['delivery'],
						'payment' => $row['payment'],
						'products' => $products,
						'delivery-line' => $row['delivery_price'] ? $delivery : '',
						'invoice' => $row['invoice'],
						'note' => $row['note'],
						'e-order' => $counters['e_order']
					], [
						'e-order' => $counters['e_order'],
						'owner' => in_array(1, explode(',', $user['group_ids'])),
						'pages' => ($user['add_pages'] or $user['edit_pages']),
						'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
						'services' => ($user['add_services'] or $user['edit_services']),
						'note' => $row['note']
					], 'content');
				} else {
					tpl_set('noContent', [
						'text' => 'Order is deleted or just not created'
					], [
						'owner' => in_array(1, explode(',', $user['group_ids'])),
						'pages' => ($user['add_pages'] or $user['edit_pages']),
						'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
						'services' => ($user['add_services'] or $user['edit_services'])
					], 'content');
				}
			} else {
				$meta['title'] = $lang['Orders'];
				$query = text_filter($_POST['query'], 255, false);
				$page = intval($_POST['page']);
				$count = 10;
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						o.*,
						DATE(o.date) as date,
						TIME(o.date) as time,
						u.id as uid,
						CONCAT(u.name, \' \', u.lastname) as uname,
						u.phone as uphone,
						s.name as status,
						s.color,
						s.alt_color,
						d.name as delivery,
						p.name as payment
					FROM `'.DB_PREFIX.'_orders` o 
					LEFT JOIN `'.DB_PREFIX.'_users` u 
						ON u.id = o.customer_id
					LEFT JOIN `'.DB_PREFIX.'_orders_status` s 
						ON s.id = o.status_id
					LEFT JOIN `'.DB_PREFIX.'_orders_delivery` d 
						ON d.id = o.delivery_id
					LEFT JOIN `'.DB_PREFIX.'_orders_payment` p 
						ON p.id = o.payment_id
					WHERE o.del = 0 '.(
						$query ? 'AND (id LIKE \'%'.$query.'%\' OR CONCAT(u.name, \' \', u.lastname) LIKE \'%'.$query.'%\' OR CONCAT(u.lastname, \' \', u.name) LIKE \'%'.$query.'%\') ' : ''
					).'ORDER BY o.id DESC LIMIT '.($page*$count).', '.$count
				, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('store/orders/item', [
							'id' => $row['id'],
							'total' => number_format($row['total'], 2, '.', ''),
							'tax' => number_format($row['tax'], 2, '.', ''),
							'currency' => $config['currency'][$row['currency']]['symbol'],
							'uid' => $row['uid'],
							'uname' => $row['uname'],
							'uphone' => $row['uphone'],
							'date' => date('m.d.y', strtotime($row['date'])).'<br>'.$row['time'],
							'status' => $row['status'],
							'delivery' => $row['delivery'],
							'payment' => $row['payment'],
							'color' => ($i%2 == 0 ? $row['color'] : ($row['alt_color'] ?: $row['color']))
						], [
							'color' => ($i%2 == 0 ? $row['color'] : ($row['alt_color'] ?: $row['color']))
						], 'orders');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				} else {
					tpl_set('noContent', [
						'text' => $lang['noOrders']
					], [
						'owner' => in_array(1, explode(',', $user['group_ids'])),
						'pages' => ($user['add_pages'] or $user['edit_pages']),
						'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
						'services' => ($user['add_services'] or $user['edit_services'])
					], 'orders');
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['orders'],
					]));
				}
				tpl_set('store/orders/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'orders' => $tpl_content['orders'],
					'e-order' => $counters['e_order']
				], [
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'e-order' => $counters['e_order'],
				], 'content');
			}
		break;
		
		/*
		* Payment Methods
		*/
		case 'payment':
			$meta['title'] = $lang['PaymentMethods'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					*
				FROM `'.DB_PREFIX.'_orders_payment` '.(
				$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY `sort` ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('store/payment/item', [
						'id' => $row['id'],
						'name' => $row['name'],
					], [
					], 'payment');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noPayment']
				], [], 'payment');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['payment'],
				]));
			}
			tpl_set('store/payment/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'payment' => $tpl_content['payment'],
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order'],
			], 'content');
		break;
		
		/*
		*  Add/edit Payment Method
		*/
		case 'sendPayment':
			is_ajax() or die('Hacking attempt!');
				$id = intval($_POST['id']);
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_orders_payment` SET
						name =\''.text_filter($_POST['name'], 50, false).'\''.(
					$id ? ' WHERE id = '.$id : ''
				));
				echo $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
			die;
		break;
		
		/*
		*  Del Payment Method
		*/
		case 'delPayment':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query('DELETE FROM `'.DB_PREFIX.'_orders_payment` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		break;


		/*
		* Payment method sort
		*/
		case 'stPaymentPriority':
			is_ajax() or die('Hacking attempt!');
			$i = 1;
			foreach($_POST as $row){
				db_query('UPDATE `'.DB_PREFIX.'_orders_payment` SET sort = '.$i.' WHERE id = '.$row['id']);
				$i++;	
			}
			die;
		break;

		/*
		* Delivery Methods
		*/
		case 'delivery':
			$meta['title'] = $lang['DeliveryMethods'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					*
				FROM `'.DB_PREFIX.'_orders_delivery` '.(
				$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY `sort` ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('store/delivery/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'price' => floatval($row['price']),
						'currency' => $row['currency'],
						'currency-symbol' => $config['currency'][$row['currency']]['symbol'],
					], [
					], 'delivery');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noDelivery']
				], [], 'delivery');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['delivery'],
				]));
			}
			tpl_set('store/delivery/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'delivery' => $tpl_content['delivery'],
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order'],
			], 'content');
		break;
		
		/*
		*  Add/edit Delivery Method
		*/
		case 'sendDelivery':
			is_ajax() or die('Hacking attempt!');
				$id = intval($_POST['id']);
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_orders_delivery` SET
						name =\''.text_filter($_POST['name'], 50, false).'\',
						price =\''.floatval($_POST['price']).'\',
						currency =\''.(text_filter($_POST['currency'], 25, false) ?: 'USD').'\''.(
					$id ? ' WHERE id = '.$id : ''
				));
				echo $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
			die;
		break;
		
		/*
		*  Del Delivery Method
		*/
		case 'delDelivery':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query('DELETE FROM `'.DB_PREFIX.'_orders_delivery` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		break;


		/*
		* Delivery method sort
		*/
		case 'stDeliveryPriority':
			is_ajax() or die('Hacking attempt!');
			$i = 1;
			foreach($_POST as $row){
				db_query('UPDATE `'.DB_PREFIX.'_orders_delivery` SET sort = '.$i.' WHERE id = '.$row['id']);
				$i++;	
			}
			die;
		break;

		/*
		* Order statuses
		*/
		case 'order_statuses':
			$meta['title'] = $lang['allStatuses'];
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					*
				FROM `'.DB_PREFIX.'_orders_status` '.(
				$query ? 'WHERE (name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY `sort` ASC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('store/statuses/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'form' => intval($row['form']),
						'color' => $row['color'],
						'alt_color' => $row['alt_color']
					], [
						'default' => $row['id'] == 1
					], 'status');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			} else {
				tpl_set('noContent', [
					'text' => $lang['noStatuses']
				], [], 'status');
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $tpl_content['status'],
				]));
			}
			tpl_set('store/statuses/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'status' => $tpl_content['status'],
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order'],
			], 'content');
		break;
		
		/*
		*  Add/edit status
		*/
		case 'sendOrderStatus':
			is_ajax() or die('Hacking attempt!');
				$id = intval($_POST['id']);
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_orders_status` SET
						name =\''.text_filter($_POST['name'], 50, false).'\',
						color =\''.text_filter($_POST['color'], 9, false).'\',
						alt_color =\''.text_filter($_POST['alt_color'], 9, false).'\',
						form =\''.intval($_POST['form']).'\''.(
					$id ? ' WHERE id = '.$id : ''
				));
				echo $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
			die;
		break;
		
		/*
		*  Del status
		*/
		case 'delOrderStatus':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query('DELETE FROM `'.DB_PREFIX.'_orders_status` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		break;
		
		/*
		* Status sort
		*/
		case 'stPriority':
			is_ajax() or die('Hacking attempt!');
			$i = 1;
			foreach($_POST as $row){
				db_query('UPDATE `'.DB_PREFIX.'_orders_status` SET sort = '.$i.' WHERE id = '.$row['id']);
				$i++;	
			}
			die;
		break;
	
		/*
		*  Slider
		*/
		case 'slider':
			if ($route[2] == 'add' OR ($route[2] == 'edit' AND intval($route[3]))) {
				if ($user['edit_slider'] AND $route[2] == 'edit' OR $user['add_slider'] AND $route[2] == 'add') {
					$id = intval($route[3]);
					$type = $id ? 'Edit' : 'Add';
					$row = [];
					$link_id = [];
					$meta['title'] = $type.' '.'post';
					if($id) $row = db_multi_query('
						SELECT 
							s.*,
							IF (s.link_type = \'category\', 
								c.name,
								IF (s.link_type = \'blog\',
									b.name,
									IF (s.link_type = \'item\',
										IF (i.type = \'stock\',
											CONCAT(IFNULL(it.name, \'\'), \' \', IFNULL(ib.name, \'\'), \' \', IFNULL(im.name, \'\'), \' \', IFNULL(i.model, \'\')),
											i.name),
										\'\'))) as link_name
						FROM `'.DB_PREFIX.'_store_slider` s
						LEFT JOIN `'.DB_PREFIX.'_store_categories` c
							ON c.id = s.link_id
						LEFT JOIN `'.DB_PREFIX.'_store_blog` b
							ON b.id = s.link_id
						LEFT JOIN `'.DB_PREFIX.'_inventory` i
							ON i.id = s.link_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_types` it
							ON it.id = i.type_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_categories` ib
							ON ib.id = i.category_id
						LEFT JOIN `'.DB_PREFIX.'_inventory_models` im
							ON im.id = i.model_id
						WHERE s.id = '.$id);

					tpl_set('store/slider/form', [
						'id' => $id,
						'title' => htmlspecialchars($type.' '.'slide', ENT_QUOTES),
						'name' => htmlspecialchars($row['title'], ENT_QUOTES),
						'content' => htmlspecialchars($row['content'], ENT_QUOTES),
						'image' => $row['image'],
						'back' => $row['background'],
						'align' => $row['image_align'],
						'link-type' => $row['link_type'],
						'link-id' => ($row['link_type'] == 'none' ? '' : ($row['link_type'] == 'custom' ? $row['link_id'] : 
							json_encode($row['link_id'] ? [
								$row['link_id'] => [
									'name' => $row['link_name']
								]
							] : [])
						)),
						'send' => isset($route[3]) ? 'Save' : 'Send',
						'e-order' => $counters['e_order']
					], [
						'e-order' => $counters['e_order'],
						'image' => $row['image'],
						'owner' => in_array(1, explode(',', $user['group_ids'])),
						'pages' => ($user['add_pages'] or $user['edit_pages']),
						'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
						'services' => ($user['add_services'] or $user['edit_services']),
						'back' => $row['background'],
						'edit' => $route[2] == 'edit',
						'link-type' => ($row['link_type'] != 'none' AND $row['link_type'] != 'custom')
					], 'content');
				} else {
					tpl_set('forbidden', [
						'text' => 'You have no access to this page'
					], [
						'owner' => in_array(1, explode(',', $user['group_ids'])),
						'pages' => ($user['add_pages'] or $user['edit_pages']),
						'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
						'services' => ($user['add_services'] or $user['edit_services'])
					], 'content');
				}
			} else {
				$meta['title'] = 'Slider';
				$query = text_filter($_POST['query'], 255, false);
				$page = intval($_POST['page']);
				$count = 10;
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						*
					FROM `'.DB_PREFIX.'_store_slider`
					WHERE 1'.(
					$query ? ' AND title LIKE \'%'.$query.'%\' ' : ''
				).' ORDER BY `id` LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('store/slider/item', [
							'id' => $row['id'],
							'title' => $row['title'],
							'image' => $row['background']
						], [
							'image' => $row['background'],
							'edit' => $user['edit_slider'],
							'delete' => $user['del_slider'],
							'deny' => ($user['edit_slider'] AND $user['del_slider'])
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
						'store' => $tpl_content['items'],
					]));
				}
				tpl_set('store/slider/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'items' => $tpl_content['items'] ?: '<div class="noContent">No slides</div>',
					'e-order' => $counters['e_order']
				], [
					'e-order' => $counters['e_order'],
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'add' => $user['add_slider']
				], 'content');
			}
		break;
		
		/*
		*  Send slide
		*/
		case 'send_slide':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$sql = '';
			
			if ($user['add_slider'] AND !$id OR $user['edit_slider'] AND $id) {
				
				if ($_POST['del_image']) 
					$sql .= ', image = \'\'';
				
				if ($_POST['del_background']) 
					$sql .= ', background = \'\'';
				
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_store_slider` SET
						title =\''.text_filter($_POST['name'], 50, false).'\',
						image_align =\''.text_filter($_POST['image_align'], 50, false).'\',
						link_type =\''.text_filter($_POST['link_type'], 50, false).'\',
						link_id =\''.text_filter($_POST['link_id'], 255, false).'\',
						content =\''.text_filter($_POST['content']).'\''.$sql.(
							$id ? ' WHERE id = '.$id : ''
					)
				);
				$id = $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
				
				// Is file upload
				if($_FILES){
					
					// Upload max file size
					$max_size = 10;
					
					// path
					$dir = ROOT_DIR.'/uploads/images/slider/';
					
					// Is not dir
					if(!is_dir($dir.$id)){
						@mkdir($dir.$id, 0777);
						@chmod($dir.$id, 0777);
					}
					
					$dir = $dir.$id.'/';
					
					// temp file
					$tmp = $_FILES['image']['tmp_name'];
					$tmp2 = $_FILES['background']['tmp_name'];
					
					$type = mb_strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
					$type2 = mb_strtolower(pathinfo($_FILES['background']['name'], PATHINFO_EXTENSION));
					
					// Check
					if ($_FILES['image']) {
						if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array($type, ['jpeg', 'jpg', 'png', 'gif'])){
							echo 'err_image_type';
							die;
						}
						if($_FILES['image'] AND $_FILES['image']['size'] >= 1024*$max_size*1024){
							echo 'err_file_size';
							die;
						}
						
					}
					
					if ($_FILES['background']) {
						if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp2)['mime']) OR !in_array($type2, ['jpeg', 'jpg', 'png', 'gif'])){
								echo 'err_image_type2';
								die;
							}
						if($_FILES['background']['size'] >= 1024*$max_size*1024){
							echo 'err_file_size2';
							die;
						}
					}
					
					// New name
					$rename = uniqid('', true).'.'.$type;
					$rename2 = uniqid('', true).'.'.$type2;
					
					// Upload image
					if(move_uploaded_file($tmp, $dir.$rename)){
						
						$img = new Imagick($dir.$rename);
						
						// 1920
						if($img->getImageWidth() > 1200){
							$img->resizeImage(1200, 0, imagick::FILTER_LANCZOS, 0.9);
							auto_rotate_image($img);
							$img->stripImage();
							$img->writeImage($dir.$rename);
						}
						
						// 300x300
						$img->cropThumbnailImage(300, 300);
						auto_rotate_image($img);
						$img->stripImage();
						$img->writeImage($dir.'preview_'.$rename);
						
						// 94x94
						$img->cropThumbnailImage(94, 94);
						auto_rotate_image($img);
						$img->stripImage();
						$img->writeImage($dir.'thumb_'.$rename);
						$img->destroy();
						
						db_query('UPDATE `'.DB_PREFIX.'_store_slider` SET image = \''.$rename.'\' WHERE id = '.$id);
					}
					
					if(move_uploaded_file($tmp2, $dir.$rename2)){
						
						$img = new Imagick($dir.$rename2);
						
						// 1920
						if($img->getImageWidth() > 1200){
							$img->resizeImage(1200, 0, imagick::FILTER_LANCZOS, 0.9);
							auto_rotate_image($img);
							$img->stripImage();
							$img->writeImage($dir.$rename2);
						}
						
						// 300x300
						$img->cropThumbnailImage(300, 300);
						auto_rotate_image($img);
						$img->stripImage();
						$img->writeImage($dir.'preview_'.$rename2);
						
						// 94x94
						$img->cropThumbnailImage(94, 94);
						auto_rotate_image($img);
						$img->stripImage();
						$img->writeImage($dir.'thumb_'.$rename2);
						$img->destroy();
						
						db_query('UPDATE `'.DB_PREFIX.'_store_slider` SET background = \''.$rename2.'\' WHERE id = '.$id);
					}
				}
				
				echo $id;
				die;
			} else 
				die('no_access');
		break;
		
		/*
		* Delete slide
		*/
		case 'del_slide':
			$id = intval($_POST['id']);
			if($user['del_slider']){
				db_query('DELETE FROM `'.DB_PREFIX.'_store_slider` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('ERR');
		break;
		
		/*
		*  Send post
		*/
		case 'send_post':
			is_ajax() or die('Hacking attempt!');
			
			$id = intval($_POST['id']);
			
			if(($id AND $user['edit_blogs']) OR (!$id AND $user['add_blogs'])){
				
				$name = text_filter($_POST['name'], 255, false);
				$title = text_filter($_POST['title'], 50, false);
				$description = text_filter($_POST['description'], 255, false);
				$keywords = text_filter($_POST['keywords'], 255, false);
				$content = text_filter($_POST['content']);
				$canonical = text_filter($_POST['canonical']);
				
				if(!$user['approve_blogs']){
					$new_symbols = length($_POST['name'], 255)+length($_POST['title'], 50)+length($_POST['description'], 255)+length($_POST['keywords'], 255)+length($_POST['content']);
					
					if($id){
						$row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_blog` WHERE id = '.$id);
						$old_symbols = length($row['name'])+length($row['title'])+length($row['description'])+length($row['keywords'])+length($row['content']);
					}
				}
				
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_store_blog` SET
						name =\''.$name.'\',
						'.(!$user['approve_blogs'] ? '
						confirm = 0,
						symbols = IF(symbols < 0, symbols+'.intval($new_symbols-intval($old_symbols)).', '.intval($new_symbols-intval($old_symbols)).'),
						' : '').'
						pathname =\''.text_filter($_POST['pathname'], 255, false).'\',
						category_id = \''.ids_filter($_POST['category']).'\',
						date = \''.date('Y-m-d H:i:s', time()).'\',
						title =\''.$title.'\',
						main =\''.intval($_POST['main']).'\',
						description =\''.$description.'\',
						keywords =\''.$keywords.'\',
						canonical =\''.$canonical.'\',
						add_tags =\''.$_POST['addTags'].'\',
						declined = 0,
						declined_comment = \'\',
						content =\''.$content.'\''.(
							$id ? ' WHERE id = '.$id : ''
					)
				);
				$id = $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
				
				// Is file upload
				if($_FILES){
					
					// Upload max file size
					$max_size = 25;
					
					// path
					$dir = ROOT_DIR.'/uploads/images/blogs/';
					
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
					if ($_FILES['image']) {
						if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array($type, ['jpeg', 'jpg', 'png', 'gif'])){
							echo json_encode([
								'err' => 'err_image_type'
							]);
							die;
						}
						if($_FILES['image'] AND $_FILES['image']['size'] >= 1024*$max_size*1024){
							echo json_encode([
								'err' => 'err_file_size'
							]);
							die;
						}
						
					}
					
					// New name
					$uname = uniqid('', true);
					$rename = $uname.'.jpg';
					
					// Upload image
					if(move_uploaded_file($tmp, $dir.$uname.'.'.$type)){
						
						$img = compressImage($dir.$uname.'.'.$type, $dir.$rename);
						
						auto_rotate_image($img);
						
						// 1920
						if($img->getImageWidth() > 1024){
							$img->resizeImage(1024, 0, imagick::FILTER_LANCZOS, 0.9);
							$img->writeImage($dir.$rename);
						}
						
						$img->cropThumbnailImage(309, 200);
						auto_rotate_image($img);
						$img->writeImage($dir.'thumb_iws_'.$rename);
						$img->destroy();
						
						$img = new Imagick($dir.$rename);
						$img->cropThumbnailImage(94, 94);
						$img->writeImage($dir.'thumb_'.$rename);
						$img->destroy();
						
						db_query('UPDATE `'.DB_PREFIX.'_store_blog` SET image = \''.$rename.'\' WHERE id = '.$id);
					
					}
				}
					
				echo json_encode([
					'id' => $id,
					'delivery' => (isset($_POST['groups']) && !empty($_POST['groups'])) ? delivery($id) : ''
				]);
			} else
				echo 'ERR';
			die;
		break;
		
		/*
		*  Send page
		*/
		case 'send_page':
		
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			$pathname = trim($_POST['pathname']);
			$old_pathname = trim($_POST['old_pathname'] ?? '');
			
			if(($id AND $user['edit_pages']) OR (!$id AND $user['add_pages'])){
				
				$name = text_filter($_POST['name'], 50, false);
				$title = text_filter($_POST['title'], 50, false);
				$description = text_filter($_POST['description'], 255, false);
				$keywords = text_filter($_POST['keywords'], 255, false);
				$content = text_filter($_POST['content']);
				
				//$content = html_entity_decode($content);
				
				//$dom = new DOMDocument();
				//@$dom->loadHTML(mb_convert_encoding($_POST['content'], 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
				//if($images = $dom->getElementsByTagName('img')){
				//	for ($i = 0; $i < $images->length; $i++){
				//		$image = $images->item($i);
				//		$src = $image->getAttribute('src');
				//		if(preg_match('/^(http?s:\/\/(?!.*?yoursite)|www|data).*/i', $src)){
				//		
				//			foreach ($image->attributes as $attr) {
				//				if(!in_array($attr->name, ['src', 'width', 'height']))
				//					$image->removeAttribute($attr->name);
				//			}
				//			
				//			if(($img = file_get_contents($src)) && ($ext = get_image_ext($src))){
				//				
				//				$ext = $ext ?: 'jpg';
				//				
				//				$dir = ROOT_DIR.'/uploads/images/editor/';
				//				
				//				$rename = uniqid('', true).'.'.$ext;
				//
				//				if(file_put_contents($dir.$rename, $img)){
				//					
				//					$img = new Imagick($dir.$rename);
				//					
				//					if($img->getImageWidth() > 1200){
				//						$img->resizeImage(1200, 0, imagick::FILTER_LANCZOS, 0.9);
				//						auto_rotate_image($img);
				//						$img->stripImage();
				//						$img->writeImage($dir.$rename);
				//					}
				//					
				//					$img->cropThumbnailImage(200, 200);
				//					$img->stripImage();
				//					$img->writeImage($dir.'preview_'.$rename);
				//					
				//					$img->cropThumbnailImage(94, 94);
				//					$img->stripImage();
				//					$img->writeImage($dir.'thumb_'.$rename);
				//					$img->destroy();
				//					
				//					$image->setAttribute('src', '/uploads/images/editor/'.$rename);
				//				}
				//			}
				//		}
				//	}
				//}
				//$content = html_entity_decode($dom->saveHTML());
				
				if(!$user['approve_pages']){
					$new_symbols = length($_POST['name'], 50)+length($_POST['title'], 50)+length($_POST['description'], 255)+length($_POST['keywords'], 255)+length($_POST['content']);
					if($id){
						$row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id);
						$old_symbols = length($row['name'])+length($row['title'])+length($row['description'])+length($row['keywords'])+length($row['content']);
					}
				}
				
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_pages` SET
						name =\''.text_filter($_POST['name'], 50, false).'\',
						'.(!$user['approve_pages'] ? '
						confirm = 0,
						symbols = IF(symbols < 0, symbols+'.intval($new_symbols-intval($old_symbols)).', '.intval($new_symbols-intval($old_symbols)).'),
						' : '
						confirm = 1,
						').'
						pathname =\''.text_filter($pathname, 255, false).'\',
						canonical =\''.text_filter($_POST['canonical'], 255, false).'\',
						main_page = '.intval($_POST['main_page']).',
						title =\''.text_filter($_POST['title'], 50, false).'\',
						description =\''.text_filter($_POST['description'], 255, false).'\',
						keywords =\''.text_filter($_POST['keywords'], 255, false).'\',
						declined = 0,
						declined_comment = \'\',
						content =\''.$content.'\''.(
							$id ? ' WHERE id = '.$id : ''
					)
				);
				$id = $id ? $id : intval(
					mysqli_insert_id($db_link)
				);
				
				// Is file upload
				if($_FILES){
					
					// Upload max file size
					$max_size = 25;
					
					// path
					$dir = ROOT_DIR.'/uploads/images/pages/';
					
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
					if ($_FILES['image']) {
						if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array($type, ['jpeg', 'jpg', 'png', 'gif'])){
							echo json_encode([
								'err' => 'err_image_type'
							]);
							die;
						}
						if($_FILES['image'] AND $_FILES['image']['size'] >= 1024*$max_size*1024){
							echo json_encode([
								'err' => 'err_file_size'
							]);
							die;
						}
						
					}
					
					// New name
					$uname = uniqid('', true);
					$rename = $uname.'.jpg';
					
					// Upload image
					if(move_uploaded_file($tmp, $dir.$uname.'.'.$type)){
						
						$img = compressImage($dir.$uname.'.'.$type, $dir.$rename);
						
						auto_rotate_image($img);
						
						// 1920
						if($img->getImageWidth() > 1024){
							$img->resizeImage(1024, 0, imagick::FILTER_LANCZOS, 0.9);
							$img->writeImage($dir.$rename);
						}
						
						$img->cropThumbnailImage(309, 200);
						auto_rotate_image($img);
						$img->writeImage($dir.'thumb_iws_'.$rename);
						$img->destroy();
						
						$img = new Imagick($dir.$rename);
						$img->cropThumbnailImage(94, 94);
						$img->writeImage($dir.'thumb_'.$rename);
						$img->destroy();
						
						db_query('UPDATE `'.DB_PREFIX.'_pages` SET image = \''.$rename.'\' WHERE id = '.$id);
					
					}
				}
				
				if($old_pathname){
					db_query('INSERT INTO `'.DB_PREFIX.'_redirect` SET url_from = \''.db_escape_string($old_pathname).'\', url_to = \''.db_escape_string($pathname).'\'');
				}
				
				echo json_encode([
					'id' => $id
				]);
			} else
				echo 'ERR';
			die;
		break;
		
		/*
		* Delete store
		*/
		case 'del':
			$id = intval($_POST['id']);
			if($user['delete_store']){
				db_query('DELETE FROM `'.DB_PREFIX.'_store` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('ERR');
		break;
		
		/*
		*  Add/edit store
		*/
		case 'add':
			case 'edit':
			$id = intval($route[2]);
			$row = [];
			$type = $id ? 'Edit' : 'Add';
			if($id){
				$row = db_multi_query('
					SELECT
						tb1.*, tb2.name as inname
					FROM `'.DB_PREFIX.'_store` tb1
					INNER JOIN `'.DB_PREFIX.'_inventory` tb2
					ON tb1.inventory_id = tb2.id
					WHERE tb1.id = '.$id
				);
			}
			$meta['title'] = $id ? 'Edit store' : 'Add store';
			if($route[1] == 'add' OR (
				$route[1] == 'edit' AND $id
			)){
				tpl_set('store/form', [
					'id' => $id,
					'title' => $type.' store',
					'name' => $row['name'],
					'descr' => $row['description'],
					'device' => json_encode(
						[$row['inventory_id'] => [
						'name' => $row['inname']
					]]),
					'send' => ($id ? 'edit' : 'add'),
					'e-order' => $counters['e_order']
				], [
					'e-order' => $counters['e_order'],
					'edit' => $id
				], 'content');
			}
		break;
		
		/*
		*  Send store
		*/
		case 'send': 
			is_ajax() or die('Hacking attempt!');
			
			// Filters
			$id = intval($_POST['id']);
			$name = text_filter($_POST['name'], 25, false);
			$description = text_filter($_POST['descr'], 255, false);
			$inventory_id = intval($_POST['inventory']);
			
			// SQL SET
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_store` SET
					name = \''.$name.'\',
					description = \''.$description.'\',
					inventory_id = \''.$inventory_id.'\''.(
				$id ? 'WHERE id = '.$id : ''
			));
			
			echo $id = $id ? $id : intval(mysqli_insert_id($db_link));
			die;
		break;
		
		/*
		*  Categories
		*/
		case 'categories':
			$meta['title'] = 'Categories';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			$left_count = 0;

			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					tb1.id, tb1.name, tb2.name as pname, tb1.parent_id
				FROM `'.DB_PREFIX.'_store_categories` tb1
				LEFT JOIN `'.DB_PREFIX.'_store_categories` tb2
				ON tb1.parent_id = tb2.id '.(
				$query ? 'WHERE (tb1.name LIKE \'%'.$query.'%\' OR tb2.name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY tb1.sort, tb1.id DESC', true)){
				/*$i = 0;
				foreach($sql as $row){
					tpl_set('store/categories/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'parent-name' => $row['pname'] ?? '',
						'parent-json' => json_encode($row['parent_id'] ? [
							$row['parent_id'] => ['name' => $row['pname']]
						] : [])
					], [], 'categories');
					$i++;
				}*/
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				
				$list = showCategory(0, $sql, '');
			} else {
				tpl_set('noContent', [
					'text' => 'There are no categories',
					'e-order' => $counters['e_order']
				], [
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'e-order' => $counters['e_order'],
				], 'categories');
			}
			$left_count = intval(($res_count-($page*$count)-count($sql)));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $list
				]));
			}
			tpl_set($route[1] == '' ?  : 'store/categories/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'invCategories' => $list,
				'owner' => in_array(1, explode(',', $user['group_ids'])),
				'pages' => ($user['add_pages'] or $user['edit_pages']),
				'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
				'services' => ($user['add_services'] or $user['edit_services']),
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order'],
				'owner' => in_array(1, explode(',', $user['group_ids'])),
				'pages' => ($user['add_pages'] or $user['edit_pages']),
				'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
				'services' => ($user['add_services'] or $user['edit_services']),
				'store' => ($route[1] == 'store')
			], 'content');
		break;
		
		/*
		* Delete category
		*/
		case 'delCategory':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['delete_inventory_categories']){
				db_query('DELETE FROM `'.DB_PREFIX.'_store_categories` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('ERR');
		break;
		
		
		/*
		* Select categories
		*/
		case 'allCategories':
			$id = intval($_POST['id']);
			$lId = intval($_POST['lId']);
			$nIds = ids_filter($_POST['nIds']);
			$query = text_filter($_POST['query'], 100, false);
			$current = [];
			$categories = [];
			db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS tb1.id, CONCAT(
				IFNULL(tb2.name, \'\'), IF(
					tb2.name IS NOT NULL, \' <span class="fa fa-angle-right"></span> \', \'\'
				), tb1.name
				) as name, tb1.description, tb1.keywords, tb1.canonical
					FROM `'.DB_PREFIX.'_store_categories` tb1
					LEFT JOIN `'.DB_PREFIX.'_store_categories` tb2
					ON tb1.parent_id = tb2.id
					WHERE 1'.(
					$lId ? ' AND tb1.id < '.$lId : ''
				).(
					$query ? ' AND (tb1.name LIKE \'%'.$query.'%\' OR tb2.name LIKE \'%'.$query.'%\')' : ''
				).(
					in_array($_POST['type'], ['service', 'inventory']) ? ' AND tb1.type = \''.$_POST['type'].'\' ' : ''
				).($nIds ? ' AND tb1.id NOT IN('.$nIds.')' : '').(
					$id ? ' AND tb1.parent_id != '.$id : ''
				).' ORDER BY tb1.id DESC LIMIT 50', true,
			false, function($a) use(&$id, &$current, &$categories){
				if($id && $a['id'] == $id)
					$current = $a;
				else
					$categories[] = $a;
			});
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $categories,
				'current' => $current,
				'count' => $res_count,
			]));
		break;
		
		/*
		*  Send category
		*/
		case 'sendCategory':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query(($id ? 'UPDATE' : 'INSERT INTO').' `'.DB_PREFIX.'_store_categories` SET
				name = \''.text_filter($_POST['name'], 50, false).'\',
				description = \''.text_filter($_POST['description'], 255, false).'\',
				keywords = \''.text_filter($_POST['keywords'], null, false).'\',
				parent_id = '.intval($_POST['parent']).(
					$id ? ' WHERE id = '.$id : ''
			));
			echo $id ? $id : intval(mysqli_insert_id($db_link));
			die;
		break;
		
		/*
		*  Categories
		*/
		case 'blog_categories':
			$meta['title'] = 'Categories';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			$left_count = 0;

			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					tb1.id, tb1.name, tb2.name as pname, tb1.parent_id, tb1.pathname
				FROM `'.DB_PREFIX.'_store_blog_categories` tb1
				LEFT JOIN `'.DB_PREFIX.'_store_blog_categories` tb2
				ON tb1.parent_id = tb2.id '.(
				$query ? 'WHERE (tb1.name LIKE \'%'.$query.'%\' OR tb2.name LIKE \'%'.$query.'%\') ' : ''
			).'ORDER BY tb1.sort, tb1.id DESC', true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('store/blog_categories/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'pathname' => $row['pathname'],
						'parent-name' => $row['pname'] ?? '',
						'parent-json' => json_encode($row['parent_id'] ? [
							$row['parent_id'] => ['name' => $row['pname']]
						] : [])
					], [
						'edit-blog' => $user['edit_blogs'],
						'del-blog' => $user['del_blogs']
					], 'categories');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				
				//$list = showCategory(0, $sql, 'Blog');
			} else {
				tpl_set('noContent', [
					'text' => 'There are no categories',
					'e-order' => $counters['e_order']
				], [
					'e-order' => $counters['e_order'],
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'add-blog' => $user['add_blogs'],
					'owner' => in_array(1, explode(',', $user['group_ids']))
				], 'categories');
			}
			$left_count = intval(($res_count-($page*$count)-count($sql)));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'content' => $list
				]));
			}
			tpl_set($route[1] == '' ?  : 'store/blog_categories/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'blog-categories' => $tpl_content['categories'],
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order'],
				'owner' => in_array(1, explode(',', $user['group_ids'])),
				'add-blog' => $user['add_blogs'],
				'pages' => ($user['add_pages'] or $user['edit_pages']),
				'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
				'services' => ($user['add_services'] or $user['edit_services']),
				'store' => ($route[1] == 'store')
			], 'content');
		break;
		
		/*
		* Delete category
		*/
		case 'delBlogCategory':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			db_query('DELETE FROM `'.DB_PREFIX.'_store_blog_categories` WHERE id = '.$id);
			if(mysqli_affected_rows($db_link)){
				exit('OK');
			} else
				exit('ERR');
		break;
		
		
		/*
		* Select categories
		*/
		case 'allBlogCategories':
			$id = intval($_POST['id']);
			$lId = intval($_POST['lId']);
			$nIds = ids_filter($_POST['nIds']);
			$query = text_filter($_POST['query'], 100, false);
			$categories = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS tb1.id, CONCAT(
				IFNULL(tb2.name, \'\'), IF(
					tb2.name IS NOT NULL, \' <span class="fa fa-angle-right"></span> \', \'\'
				), tb1.name
				) as name
					FROM `'.DB_PREFIX.'_store_blog_categories` tb1
					LEFT JOIN `'.DB_PREFIX.'_store_blog_categories` tb2
					ON tb1.parent_id = tb2.id
					WHERE 1'.(
					$lId ? ' AND tb1.id < '.$lId : ''
				).(
					$query ? ' AND (tb1.name LIKE \'%'.$query.'%\' OR tb2.name LIKE \'%'.$query.'%\')' : ''
				).($nIds ? ' AND tb1.id NOT IN('.$nIds.')' : '').(
					$id ? ' AND tb1.id != '.$id.' AND tb1.parent_id != '.$id : ''
				).' ORDER BY tb1.id DESC LIMIT 20', true
			);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $categories,
				'count' => $res_count,
			]));
		break;
		
		/*
		*  Send category
		*/
		case 'sendBlogCategory':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if(($id AND $user['edit_blogs']) OR (!$id AND $user['add_blogs'])){
				db_query(($id ? 'UPDATE' : 'INSERT INTO').' `'.DB_PREFIX.'_store_blog_categories` SET
					name = \''.text_filter($_POST['name'], 50, false).'\',
					description = \''.text_filter($_POST['description'], 255, false).'\',
					keywords = \''.text_filter($_POST['keywords'], null, false).'\',
					pathname = \''.text_filter($_POST['pathname'], 255, false).'\',
					parent_id = '.intval($_POST['parent']).(
						$id ? ' WHERE id = '.$id : ''
				));
				echo $id ? $id : intval(mysqli_insert_id($db_link));
			} else
				echo 'ERR';
			die;
		break;
		
		/*
		*  Blogging
		*/
		case 'blog':
			
			if($route[2] == 'add' OR (
				$route[2] == 'edit' AND intval($route[3])
			)){
				$id = intval($route[3]);
				$type = $id ? 'Edit' : 'Add';
				$row = [];
				$meta['title'] = $type.' '.'post';
				if($id) $row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_blog` WHERE id = '.$id);
				$groups = db_multi_query('SELECT `group_id`, `name` FROM `'.DB_PREFIX.'_groups` ORDER BY `group_id`', true);
				$options = '';
				foreach($groups as $group){
					$options .= '<option value="'.$group['group_id'].'"'.(
						$group['group_id'] == $u['group_id'] ? ' selected' : ''
					).'>'.$group['name'].'</option>';
				}
				$cats = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_store_blog_categories` ORDER BY id', true);
				$categories = '<option value="0">Not selected</option>';
				$cids = explode(',', $row['category_id']);
				foreach($cats as $c){
					$categories .= '<option value="'.$c['id'].'"'.(
						in_array($c['id'], $cids) ? ' selected' : ''
					).'>'.$c['name'].'</option>';
				}
				tpl_set('store/blog/form', [
					'id' => $id,
					'image' => $row['image'],
					'title' => htmlspecialchars($type.' '.'post', ENT_QUOTES),
					'name' => htmlspecialchars($row['name'], ENT_QUOTES),
					'pathname' => htmlspecialchars($row['pathname'], ENT_QUOTES),
					'content' => htmlspecialchars($row['content'], ENT_QUOTES),
					'stitle' => htmlspecialchars($row['title'], ENT_QUOTES),
					'description' => htmlspecialchars($row['description'], ENT_QUOTES),
					'keywords' => htmlspecialchars($row['keywords'], ENT_QUOTES),
					'add-tags' => htmlspecialchars($row['add_tags'], ENT_QUOTES),
					'uri' => 'blog/'.($row['pathname'] ?: $row['id']),
					'groups' => $options,
					'canonical' => $row['canonical'],
					'categories' => $categories,
					'send' => isset($route[3]) ? 'Save' : 'Send',
					'e-order' => $counters['e_order']
				], [
					'e-order' => $counters['e_order'],
					'edit' => $row,
					'image' => $row['image'],
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'add' => ($route[2] == 'add'),
					'main' => ($row['main'] == 1),
					'confirm' => storeCanConfirmPost($row),
					'can-decline' => storeCanDeclinePost($row)
				], 'content');
			} else {
				$meta['title'] = 'Posts';
				$query = text_filter($_POST['query'], 255, false);
				$page = intval($_POST['page']);
				
				$long_title = (int)$_REQUEST['long_title'];
				$long_description = (int)$_REQUEST['long_description'];
				$title = (int)$_REQUEST['title'];
				$description = (int)$_REQUEST['description'];
				$keywords = (int)$_REQUEST['keywords'];
				
				$count = 10;
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						* FROM `'.DB_PREFIX.'_store_blog` WHERE 1 '.(
					$query ? 'AND name LIKE \'%'.$query.'%\' ' : ''
				).(
					$long_title ? 'AND CHAR_LENGTH(title) > 60 ' : ''
				).(
					$long_description ? 'AND CHAR_LENGTH(description) > 300 ' : ''
				).(
					$title ? 'AND title = \'\' ' : ''
				).(
					$description ? 'AND description = \'\' ' : ''
				).(
					$keywords ? 'AND keywords = \'\' ' : ''
				).'ORDER BY confirm ASC, date DESC LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('store/blog/item', [
							'id' => $row['id'],
							'name' => $row['name'],
							'image' => $row['image'],
							'symbols' => $row['symbols'],
							'status' => storeGetPostStatusText($row)
						], [
							'confirm' => $row['confirm'],
							'symbols' => ($row['symbols'] && !$row['confirm']),
							'edit-blog' => $user['edit_blogs'],
							'del-blog' => $user['del_blogs'],
							'image' => $row['image'],
							'status' => storeGetPostStatus($row)
						], 'posts');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				} else {
					tpl_set('noContent', [
						'text' => 'There are no posts'
					], [], 'posts');
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['posts'],
					]));
				}
				tpl_set('store/blog/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'posts' => $tpl_content['posts'],
					'e-order' => $counters['e_order']
				], [
					'e-order' => $counters['e_order'],
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'add-blog' => $user['add_blogs'],
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'store' => $route[1] == 'store'
				], 'content');
			}
		break;
		
		case 'reparse':
		$page = (int)$_GET['page'];
			$pages = db_multi_query('SELECT id, pathname, content FROM `'.DB_PREFIX.'_pages` WHERE content LIKE \'%wpsl-wrap%\' AND content IS NOT NULL ORDER BY id ASC LIMIT '.($page*5).', 1', true);
			foreach($pages as $page){
				$dom = new DOMDocument();
				@$dom->loadHTML($page['content']);
				$wpsl = $dom->getElementById('wpsl-wrap');
				$div = $dom->createElement('div', '');
				$attr = $dom->createAttribute('id');
				$attr->value = 'widget-locations';
				$div->appendChild($attr);
				$wpsl->parentNode->replaceChild($div, $wpsl);
				//$parent = $wpsl->parentNode;
				//$parent->parentNode->removeChild($wpsl);
function get_inner_html( $node ) { 
    $innerHTML= ''; 
    $children = $node->childNodes; 
    foreach ($children as $child) { 
        $innerHTML .= $child->ownerDocument->saveXML( $child ); 
    } 

    return $innerHTML; 
}
				//$wpsl->parentNode->removeChild($wpsl);
				//echo $page['content'];
				echo get_inner_html($dom);
				break;
			}
		die;
		break;
		
		case 'parser':
		die;
			$page = (int)$_GET['page'];
			$content = '';
			$pages = db_multi_query('SELECT id, pathname FROM `'.DB_PREFIX.'_pages` WHERE content IS NULL ORDER BY id LIMIT '.($page*5).', 1', true);
			foreach($pages as $page){
				$content = file_get_contents('http://www.yoursite.com/'.$page['pathname']);
				$dom = new DOMDocument();
				@$dom->loadHTML($content);

				if($title = $dom->getElementsByTagName('title')){
					$title = str_ireplace(' | Your Company', '', $title->item(0)->nodeValue);
				}
				
				$description = '';
				
				$keywords = '';
				
				$metas = $dom->getElementsByTagName('meta');

				for ($i = 0; $i < $metas->length; $i++){
					$meta = $metas->item($i);
					if($meta->getAttribute('name') == 'description')
						$description = $meta->getAttribute('content');
					if($meta->getAttribute('name') == 'keywords')
						$keywords = $meta->getAttribute('content');
				}
				
				if($article = $dom->getElementsByTagName('article')){
					if($images = $dom->getElementsByTagName('img')){
						for ($i = 0; $i < $images->length; $i++){
							$image = $images->item($i);
							$src = $image->getAttribute('src');
							$image->removeAttribute('srcset');
							if($img = file_get_contents($src)){
								$ext = strrchr($src, '.');
								$nname = uniqid().$ext;
								if(file_put_contents(ROOT_DIR.'/uploads/pages/'.$nname, $img)){
									$image->setAttribute('src', '/uploads/pages/'.$nname);
								}
							}
						}
					}
					$doc = $article->item(0)->ownerDocument;

					$html = '';
					foreach ($article->item(0)->childNodes as $node) {
						$html .= $doc->saveHTML($node);
					}
					
					$article = $html;
				}
	
				//db_query('UPDATE `'.DB_PREFIX.'_pages` SET
				//	title = \''.text_filter($title, 255, false).'\',
				//	description = \''.text_filter($description, 16000, false).'\',
				//	keywords = \''.text_filter($keywords, 16000, false).'\'
				//	WHERE id = '.$page['id']
				//);
				db_query('UPDATE `'.DB_PREFIX.'_pages` SET
					content = \''.db_escape_string($article).'\'
					WHERE id = '.$page['id']
				);
			}
			echo $pages ? '<script>location.reload();</script>' : 'OK';
			die;
		break;
		
		case 'page_approve':
			db_query('UPDATE `'.DB_PREFIX.'_pages` SET confirm = '.$user['id'].' WHERE id = '.intval($_POST['id']));
			echo 'OK';
			die;
		break;
		
		case 'blog_approve':
			db_query('UPDATE `'.DB_PREFIX.'_store_blog` SET confirm = '.$user['id'].' WHERE id = '.intval($_POST['id']));
			echo 'OK';
			die;
		break;
		
		case 'service_approve':
			db_query('UPDATE `'.DB_PREFIX.'_store_services` SET confirm = '.$user['id'].' WHERE id = '.intval($_POST['id']));
			echo 'OK';
			die;
		break;

		case 'service_decline':
			$post_id = intval($_POST['id']);
			$comment = text_filter($_POST['comment']);
			$type = $_POST['type'];

			$table_name = '_store_services';
			if ($type === 'blog') {
				$table_name = '_store_blog';
			} else if ($type === 'pages') {
				$table_name = '_pages';
			}

			db_query("UPDATE ".DB_PREFIX."{$table_name} SET declined = 1, declined_comment = '{$comment}' WHERE id = {$post_id}");
			echo 'OK';
			die;
		break;
		
		/*
		*  Move to service
		*/
		case 'move_to_service':
			$id = (int)$_POST['id'];
			if($page = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id)){
				db_query('INSERT INTO `'.DB_PREFIX.'_store_services` SET
					name =\''.db_escape_string($page['name']).'\',
					pathname = '.(
						$page['pathname'] ? '\''.db_escape_string($page['pathname']).'\'' : 'NULL'
					).',
					canonical =\''.db_escape_string($page['canonical']).'\',
					title =\''.db_escape_string($page['title']).'\',
					description =\''.db_escape_string($page['description']).'\',
					keywords =\''.db_escape_string($page['keywords']).'\',
					category_id = 0,
					blog_id = 0,
					content =\''.db_escape_string($page['content']).'\',
					price =\''.floatval(0).'\',
					currency =\'USD\',
					declined = 0'
				);
				if($new_id = intval(mysqli_insert_id($db_link))){
					db_query('DELETE FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id);
					echo $new_id;
				}
			} else
				echo 'Not page';
			die;
		break;
		
		/*
		*  Pages
		*/
		case 'pages':
			
			if($route[2] == 'add' OR (
				$route[2] == 'edit' AND intval($route[3])
			)){
				$id = intval($route[3]);
				$type = $id ? 'Edit' : 'Add';
				$row = [];
				$meta['title'] = $type.' '.'post';
				
				if($id) $row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id);

				tpl_set('store/pages/form', [
					'id' => $id,
					'title' => $type.' '.'page',
					'pathname' => $row['pathname'],
					'canonical' => $row['canonical'],
					'uri' => $row['pathname'] ?: 'page/'.$row['id'],
					'name' => htmlspecialchars($row['name'], ENT_QUOTES),
					'content' => htmlspecialchars($row['content'], ENT_QUOTES),
					'stitle' => htmlspecialchars($row['title'], ENT_QUOTES),
					'description' => htmlspecialchars($row['description'], ENT_QUOTES),
					'keywords' => htmlspecialchars($row['keywords'], ENT_QUOTES),
					'e-order' => $counters['e_order'],
					'image' => $row['image'],
					'send' => isset($route[3]) ? 'Save' : 'Send'
				], [
					'e-order' => $counters['e_order'],
					'image' => $row['image'],
					'add' => ($route[2] == 'add'),
					'edit' => $row,
					'confirm' => (!$row['confirm'] AND $user['approve_pages']),
					'main_page' => $row['main_page'],
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services'])
				], 'content');
			} else {
				$meta['title'] = 'Pages';
				$query = text_filter($_POST['query'], 255, false);
				$page = (int)$_POST['page'];
				
				$long_title = (int)$_REQUEST['long_title'];
				$long_description = (int)$_REQUEST['long_description'];
				$title = (int)$_REQUEST['title'];
				$description = (int)$_REQUEST['description'];
				$keywords = (int)$_REQUEST['keywords'];
				$canonical = (int)$_REQUEST['canonical'];
				$navigation = (int)$_REQUEST['navigation'];
				
				$count = 10;
				if($sql = db_multi_query('
					SELECT SQL_CALC_FOUND_ROWS
						p.*, n.name as nav_name, n.id as nid FROM `'.DB_PREFIX.'_pages` p LEFT JOIN `'.DB_PREFIX.'_navigation` n ON n.nav_id = p.id AND n.act_type = \'page\' WHERE 1 '.(
					$query ? 'AND p.title LIKE \'%'.$query.'%\' ' : ''
				).(
					$long_title ? 'AND CHAR_LENGTH(p.title) > 60 ' : ''
				).(
					$long_description ? 'AND CHAR_LENGTH(p.description) > 300 ' : ''
				).(
					$title ? 'AND p.title = \'\' ' : ''
				).(
					$description ? 'AND p.description = \'\' ' : ''
				).(
					$keywords ? 'AND p.keywords = \'\' ' : ''
				).(
					$canonical ? 'AND p.canonical = \'\' ' : ''
				).(
					$navigation ? 'AND n.name != \'\' ' : ''
				).'ORDER BY p.confirm ASC, p.date DESC LIMIT '.($page*$count).', '.$count, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('store/pages/item', [
							'id' => $row['id'],
							'nav-id' => $row['nid'],
							'name' => $row['name'],
							'nav-name' => $row['nav_name'],
							'pathname' => $row['pathname'],
							'uri' => $row['pathname'] ?: 'page/'.$row['id'],
							'symbols' => $row['symbols'],
							'image' => $row['image'],
							'status' => storeGetPostStatusText($row)
						], [
							'nav' => $row['nav_name'],
							'image' => $row['image'],
							'confirm' => $row['confirm'],
							'edit-page' => $user['edit_pages'],
							'del-page' => $user['del_pages'],
							'symbols' => ($row['symbols'] && !$row['confirm']),
							'status' => storeGetPostStatus($row)
						], 'pages');
						$i++;
					}
					$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
				} else {
					tpl_set('noContent', [
						'text' => 'There are no pages'
					], [], 'pages');
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['pages'],
					]));
				}
				tpl_set('store/pages/main', [
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'pages' => $tpl_content['pages'],
					'e-order' => $counters['e_order']
				], [
					'e-order' => $counters['e_order'],
					'owner' => in_array(1, explode(',', $user['group_ids'])),
					'add-page' => $user['add_pages'],
					'pages' => ($user['add_pages'] or $user['edit_pages']),
					'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
					'services' => ($user['add_services'] or $user['edit_services']),
					'store' => $route[1] == 'store'
				], 'content');
			}
		break;
		
		/*
		* All blogs
		*/
		case 'allBlogs':
			$id = intval($_POST['id']);
			$s = (int)$_GET['s'];
			$lId = intval($_POST['lId']);
			$nIds = ids_filter($_POST['nIds']);
			$query = text_filter($_POST['query'], 100, false);
			$blogs = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS id, name
					FROM `'.DB_PREFIX.'_store_blog`
					WHERE 1'.(
					$lId ? ' AND id < '.$lId : ''
				).(
					$query ? ' AND name LIKE \'%'.$query.'%\'' : ' ORDER BY `id`'.(
						$s ? ' = '.$s : ''
					).' DESC'
				).($nIds ? ' AND id NOT IN('.$nIds.')' : '').(
					$id ? ' AND id != '.$id : ''
				).' LIMIT 20', true
			);
			
			// Get count
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $blogs,
				'count' => $res_count,
			]));
		break;
		
		/*
		* Delete post
		*/
		case 'del_post':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['del_blogs']){
				db_query('DELETE FROM `'.DB_PREFIX.'_store_blog` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('ERR');
		break;
		
		/*
		* Delete page
		*/
		case 'del_page':
			is_ajax() or die('Hacking attempt!');
			$id = intval($_POST['id']);
			if($user['del_pages']){
				db_query('DELETE FROM `'.DB_PREFIX.'_pages` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			} else
				exit('ERR');
		break;
		
		/*
		* All store
		*/
		case 'introducing':
			$meta['title'] = 'Introducing';
			tpl_set('store/introducing', [
				'intTitle' => $config['intTitle'],
				'title' => 'Introducing',
				'intContent' => stripcslashes($config['intContent']),
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order']
			], 'content');
		break;

		/*
		* Save store introducing
		*/
		case 'save_introducing':
			is_ajax() or die('hacking!');

			$config['intTitle'] = addcslashes($_POST['title'], '\'');
			$config['intContent'] = addcslashes($_POST['content'], '\'');

			if(conf_save())
				echo 'OK';
			else
				echo 'ERR';
			die;
		break;
		
		/*
		* Category priority
		*/
		case 'cat_priority':
			is_ajax() or die('hacking!');
			$parent = '';
			$sort = '';
			if (count($_POST)) {
				foreach($_POST as $k => $row){
					print_r($row['parent']);
					$parent .= 'WHEN '.$k.' THEN '.$row['parent'].' ';
					$sort .= 'WHEN '.$k.' THEN '.$row['sort'].' ';
				}
				db_query('
					UPDATE `'.DB_PREFIX.'_store_categories` 
					SET 
						parent_id = CASE id '.$parent.' ELSE parent_id END, 
						sort = CASE id '.$sort.' ELSE sort END
					WHERE id IN ('.implode(',', array_keys($_POST)).')
				');
			}
			die;
		break;
		
		/*
		* Category priority
		*/
		case 'blog_cat_priority':
			is_ajax() or die('hacking!');
			$parent = '';
			$sort = '';
			if (count($_POST)) {
				foreach($_POST as $k => $row){
					print_r($row['parent']);
					$parent .= 'WHEN '.$k.' THEN '.$row['parent'].' ';
					$sort .= 'WHEN '.$k.' THEN '.$row['sort'].' ';
				}
				db_query('
					UPDATE `'.DB_PREFIX.'_store_blog_categories` 
					SET 
						parent_id = CASE id '.$parent.' ELSE parent_id END, 
						sort = CASE id '.$sort.' ELSE sort END
					WHERE id IN ('.implode(',', array_keys($_POST)).')
				');
			}
			die;
		break;


		/*
		* All store
		*/
		default:
			$meta['title'] = 'store';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			
			$title = (int)$_REQUEST['title'];
			$description = (int)$_REQUEST['description'];
			$keywords = (int)$_REQUEST['keywords'];
			$canonical = (int)$_REQUEST['canonical'];
			
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					i.*, c.name as category_name
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
				ON c.id = i.category_id
				WHERE i.del = 0 AND (i.commerce = 1 OR (i.images != \'\' AND i.descr != \'\')) '.(
				$query ? ' AND c.name LIKE \'%'.$query.'%\' ' : ''
			).(
				$title ? ' AND NULLIF(i.stitle, \' \') IS NULL ' : ''
			).(
				$description ? ' AND NULLIF(i.description, \' \') IS NULL ' : ''
			).(
				$keywords ? ' AND NULLIF(i.keywords, \' \') IS NULL ' : ''
			).(
				$canonical ? ' AND NULLIF(i.canonical, \' \') IS NULL ' : ''
			).' ORDER BY `id` DESC LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('store/item', [
						'id' => $row['id'],
						'name' => $row['name'],
						'model' => $row['model'],
						'price' => $row['price'],
						'category' => $row['category_name'],
						'descr' => $row['descr']
					], [], 'items');
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
			tpl_set('store/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'items' => $tpl_content['items'],
				'e-order' => $counters['e_order']
			], [
				'e-order' => $counters['e_order'],
				'edit' => $user['edit_store'],
				'owner' => in_array(1, explode(',', $user['group_ids'])),
				'pages' => ($user['add_pages'] or $user['edit_pages']),
				'blogs' => ($user['add_blogs'] or $user['edit_blogs']),
				'services' => ($user['add_services'] or $user['edit_services']),
				'add' => $user['add_store']
			], 'content');
		
	}
} else {
	tpl_set('forbidden', [
		'text' => $lang['Forbidden'],
	], [], 'content');
}

function showCategory($id, $menu, $t) {
	$html = '<ol>';
	foreach(array_keys(array_column($menu, 'parent_id'), $id) as $key) {
		$html .= '<li data-id="'.$menu[$key]['id'].'">
			<div class="uMore">
				<span class="fa fa-ellipsis-h" onclick="$(this).next().toggle(0);"></span>
				<ul>
					<li><a href=\'javascript:store.add'.($t ?? '').'Category('.$menu[$key]['id'].', "'.$menu[$key]['name'].'")\'><span class="fa fa-pencil"></span> Edit Category</a></li>
					<li><a href="javascript:store.delCategory('.$menu[$key]['id'].')"><span class="fa fa-times"></span> Del Category</a></li>
				</ul>
			</div>
			<a href=\'javascript:store.add'.($t ?? '').'Category('.$menu[$key]['id'].', "'.$menu[$key]['name'].'")\'>'.$menu[$key]['name'].'</a>'.(
			(count(array_keys(array_column($menu, 'parent_id'), $menu[$key]['id']))) ? showCategory($menu[$key]['id'], $menu, $t) : '<ol></ol>').'
		</li>';
	}
	return $html.'</ol>';
}
?>