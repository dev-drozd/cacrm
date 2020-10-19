<?php
/**
 * @appointment Blog
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

if($route[1] && $route[1] != 'category'){
	$meta['title'] = 'Blog post';
	if($row = db_multi_query('
	SELECT 
		*, 
		DAY(date) as day, 
		DAYNAME(date) as dayname, 
		MONTHNAME(date) as month, 
		YEAR(date) as year, 
		TIME(date) as time 
	FROM `'.DB_PREFIX.'_store_blog` WHERE '.(
		!is_numeric($route[1]) ? 'pathname = \''.db_escape_string($route[1]).'\'' : 'id = '.intval($route[1])
	))){
		if(is_numeric($route[1]) && $row['pathname']){
			header('Location: /blog/'.$row['pathname'], true, 301);
			die;
		}
			
		$meta['title'] = $row['name'];
		$meta['keywords'] = $row['keywords'];
		$meta['description'] = $row['description'];
		$meta['add-tags'] = $row['add_tags'];
		$meta['canonical'] = $row['canonical'];
		
		$cats = db_multi_query('SELECT id, name, pathname FROM `'.DB_PREFIX.'_store_blog_categories` ORDER BY id', true);
		$catname = [];
		$categories = '';
		foreach($cats as $c){
			if(in_array($c['id'], explode(',', $row['category_id']))){
				$catname[] = '<span typeof="v:Breadcrumb"><a property="v:title" rel="v:url" href="/blog/category/'.(
					$c['pathname'] ?: $c['id']
				).'" onclick="Page.get(this.href); return false;">'.$c['name'].'</a></span>';
			}
			$categories .= '<li><a href="/blog/category/'.($c['pathname'] ?: $c['id']).'" onclick="Page.get(this.href); return false;">'.$c['name'].'</a></li>';
		}
		
		$lp = db_multi_query('SELECT id, name, date, pathname FROM `'.DB_PREFIX.'_store_blog` ORDER BY id DESC LIMIT 0, 5', true);
		$posts = '';
		foreach($lp as $p){
			$posts .= '<li><a href="/blog/'.($p['pathname'] ?: ''.$p['id']).'" onclick="Page.get(this.href); return false;">'.$p['name'].'<i>'.$p['date'].'</i></a></li>';
		}
		
		tpl_set('blog/single', [
			'id' => $row['id'],
			'name' => $row['name'],
			'content' => replacePageTags(str_replace("\n", ' ', $row['content'])),
			'day' => $row['day'],
			'dayname' => $row['dayname'],
			'month' => $row['month'],
			'year' => $row['year'],
			'time' => $row['time'],
			'categories' => $categories,
			'posts' => $posts,
			'catname' => implode(', ', $catname),
			'pathname' => $row['pathname']
		], [
			'categories' => $categories,
			'catname' => $catname,
			'posts' => $posts,
			'edit' => $user['edit_blogs'],
			'pathname' => $row['pathname']
		], 'content');	
	} else {
		is_ajax() or http_response_code(404);
		$meta['title'] = 'Page not found';
		$tpl_content['content'] = '<div class="err404 ctnr"><h1>404</h1><p>Page not found</p></div>';
	}
} else {
	$query = text_filter($_POST['query'], 255, false);
	$page = intval($_POST['page']);
	$count = 10;
	$category_id = intval($route[2]);
	$cat_path = text_filter($route[2], 255, false);
	
	if (!$category_id AND $cat_path AND $c = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_store_blog_categories` WHERE pathname = \''.$cat_path.'\''))
		$category_id = $c['id'];

	$ids = [];
	if($sql = db_multi_query('
		SELECT SQL_CALC_FOUND_ROWS
			*, DAY(date) as day, MONTHNAME(date) as month, YEAR(date) as year, pathname FROM `'.DB_PREFIX.'_store_blog` WHERE 1'.(
		$query ? ' AND name LIKE \'%'.$query.'%\' ' : ''
	).(
		$category_id ? ' AND  FIND_IN_SET('.$category_id.', category_id)' : ''
	).' ORDER BY id DESC LIMIT '.($page*$count).', '.$count, true, false, function($a) use(&$ids){
		$ids[] = $a['category_id'];
	})){
		$i = 0;
		$categories = db_multi_query('SELECT id, name, IF(pathname != \'\', pathname, id) as pathname FROM `'.DB_PREFIX.'_store_blog_categories` WHERE id IN('.implode(
			',', array_unique(
				explode(',', implode(',', $ids))
			)
		).')', true, false, function($a){
			return [$a['id'], $a];
		});
		foreach($sql as $row){
			preg_match('/<img\s(?:.*)src="\/uploads\/images\/editor\/([a-zA-Z0-9_.]+)"(?:.*)>/isU', $row['content'], $images);
			$cats = '';
			foreach(explode(',', $row['category_id']) as $cat_id){
				$cats .='<a href="/blog/category/'.$categories[$cat_id]['pathname'].'" onclick="Page.get(this.href); return false;">'.$categories[$cat_id]['name'].'</a>';
			}
			
			
			if($images[0] && $images[1]){
				$src = ROOT_DIR.'/uploads/images/editor/'.$images[1];
				// path
				$dir = ROOT_DIR.'/uploads/images/blogs/';
				
				// Is not dir
				if(!is_dir($dir.$row['id'])){
					@mkdir($dir.$row['id'], 0777);
					@chmod($dir.$row['id'], 0777);
				}
				
				$dir = $dir.$row['id'].'/';
				
				$image_types = getimagesize($src);
				$imagename = $image_types[2] === IMAGETYPE_PNG ? str_ireplace('.png','.jpg',$images[1]) : $images[1];
				$img = compressImage($src, $dir.$imagename);
				
				$img->cropThumbnailImage(309, 200);
				$img->writeImage($dir.'thumb_iws_'.$imagename);
				$img->destroy();
				
				$img = new Imagick($dir.$imagename);
				$img->cropThumbnailImage(94, 94);
				$img->writeImage($dir.'thumb_'.$imagename);
				$img->destroy();
				
				db_query('UPDATE `'.DB_PREFIX.'_store_blog` SET image = \''.$imagename.'\' WHERE id = '.$row['id']);
			}
			
			
			tpl_set('blog/item', [
				'id' => $row['id'],
				'pathname' => $row['pathname'],
				'name' => $row['name'],
				'categories' => $cats,
				'image' => '/uploads/images/editor/preview_'.$images[1],
				'content' => replacePageTags(text_filter(str_replace("\n", ' ', $row['content']), 255, '<br>')),
				'date' => $row['date'],
				'day' => $row['day'],
				'month' => substr($row['month'], 0, 3),
				
				'year' => $row['year']
			], [
				'image' => $images[0],
				'pathname' => $row['pathname']
			], 'posts');
			$i++;
		}
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
	}
	$left_count = intval(($res_count-($page*$count)-$i));
	
	if($_POST){
		exit(json_encode([
			'res_count' => $res_count,
			'left_count' => $left_count,
			'content' => $tpl_content['posts'],
		]));
	}
	$cur_cat = [];
	$cats = db_multi_query('SELECT id, name, description, keywords, pathname FROM `'.DB_PREFIX.'_store_blog_categories` ORDER BY id', true);
	$categories = '';
	foreach($cats as $c){
		$categories .= '<li><a href="/blog/category/'.($c['pathname'] ?: $c['id']).'" onclick="Page.get(this.href); return false;">'.$c['name'].'</a></li>';
		if ($category_id AND $c['id'] == $category_id)
			$cur_cat = $c;
	}
	
	$meta['title'] = 'Blog - '.($cur_cat['name'] ?: $config['title_blog']['en']);
	$meta['description'] = $cur_cat['description'] ?: $config['description_blog']['en'];
	$meta['keywords'] = $cur_cat['keywords'] ?: $config['keywords_blog']['en'];
	
	tpl_set('blog/main', [
		'posts' => $tpl_content['posts'] ?? '<div class="noContent">No posts</div>',
		'categories' => $categories,
		'catname' => $cur_cat['name']
	], [
		'categories' => $categories,
		'catname' => $cur_cat['name']
	], 'content');
}
?>