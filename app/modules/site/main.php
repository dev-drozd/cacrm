<?php
/**
 * @appointment Main
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

//$meta['title'] = 'Control panel';

function childCategory($id, $menu) {
	foreach(array_keys(array_column($menu, 'parent_id'), $id) as $key) {
		if ($html) $html .= ',';
		$html .= $menu[$key]['id'].(
			count(array_keys(array_column($menu, 'parent_id'), $menu[$key]['id'])) ? ','.childCategory($menu[$key]['id'], $menu) : '');
	}
	return $html;
}

function products($ids, $category_ids = []) {
	$aids = childCategory($ids, $category_ids);
	if ($prs = db_multi_query('SELECT i.id, i.pathname, 
		IF(i.name = \'\', i.model, i.name) as name, 
		i.price, 
		i.images,
		o.name as object,
		c.name as category,
		ic.name as icategory,
		i.descr,
		m.name as model_name
	FROM `'.DB_PREFIX.'_inventory` i
	LEFT JOIN `'.DB_PREFIX.'_objects` o
		ON o.id = i.object_id
	LEFT JOIN `'.DB_PREFIX.'_store_categories` c
		ON c.id = i.store_category_id
	LEFT JOIN `'.DB_PREFIX.'_inventory_categories` ic
		ON ic.id = i.category_id
	LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
		ON m.id = i.model_id
	WHERE i.store_category_id IN ('.$ids.($aids ? ','.$aids : '').') AND i.type = \'stock\' AND i.del = 0 AND publish = 1
	ORDER BY `id` DESC
	LIMIT 0,2', true)) {
		foreach($prs as $row){
			$images = explode('|', $row['images']);
			tpl_set('category/item', [
				'id' => $row['id'],
				'uri' => $row['pathname'] ? urlencode($row['pathname']) : $row['id'],
				'name' => $row['icategory'].' '.$row['model_name'].' '.$row['name'],
				'category' => $row['category'],
				'object' => $row['object'] ?: '<br>',
				'price' => $row['price'],
				'descr' => text_filter($row['descr'], 150, false).'...',
				'images' => $images[0] ? '<img src="/uploads/images/inventory/'.$row['id'].'/preview_'.$images[0].'?v=2">' : '<span class="fa fa-image"></span>'
			], [
				'images' => count($images) > 0
			], 'inventories');
		}
	}
	return count($prs);
}

$catids = db_multi_query('
			SELECT
				id,
				parent_id,
				name
			FROM `'.DB_PREFIX.'_store_categories`
		', true);

$categories = '';
$cid = 0;
$sub = '';
$sub_ids = '';
$cids = [];

db_multi_query('SELECT i.store_category_id as id, c.parent_id FROM `'.DB_PREFIX.'_inventory` i INNER JOIN `'.DB_PREFIX.'_store_categories` c ON i.store_category_id = c.id WHERE i.store_category_id > 0 AND i.type = \'stock\' AND i.del = 0 GROUP BY i.store_category_id', true, null, function($a) use(&$cids){
	$cids[] = $a['id'];
	if($a['parent_id'])
		$cids[] = $a['parent_id'];
	return [0,0];
});
if ($cats = db_multi_query('
SELECT 
	c.id, 
	c.name,
	s.id as sid, 
	s.name as sname
FROM '.DB_PREFIX.'_store_categories c
LEFT JOIN '.DB_PREFIX.'_store_categories s
	ON s.parent_id = c.id
WHERE c.id IN('.implode(',', $cids).') AND c.parent_id = 0
ORDER BY c.id DESC', true)) {
	foreach($cats as $k => $cat) {
		if ($cid != $cat['id']) {
			if($cid != 0)
				products($cid, $catids);
			
				$categories .= ($cid ? ($sub ?: '').'</span></div><div class="products flex">'.$tpl_content['inventories'].'</div>' : '').'<div class="cat-title"><span>'.$cat['name'];

			$cid = $cat['id'];
			$sub = '';
			$sub_ids = '';
			$tpl_content['inventories'] = '';
		}
		
		if ($cat['sid']) {
			$sub .= '<a href="/category/'.$cat['sid'].'" onclick="Page.get(this.href); return false;">'.$cat['sname'].'</a>';
			$sub_ids .= (mb_strlen($sub_ids) > 0 ? ',' : '').$cat['sid'];
		}
	}
	products($cid, $catids);
	$categories .= ($sub ?: '').'</span></div><div class="products flex">'.$tpl_content['inventories'].'</div>';
}

$blog = '';
$i = 0;
foreach($posts = db_multi_query('
	SELECT *,
		DAY(date) as day, 
		MONTHNAME(date) as month, 
		YEAR(date) as year 
	FROM `'.DB_PREFIX.'_store_blog` ORDER BY `date` DESC LIMIT 0,9
', true) as $post) {
	if ($i % 3 == 0) {
		if ($i != 0)
			$blog .= '</div>';
		$blog .= '<div class="slItem">';
	}
	$blog .= '<div class="blog dClear">
				<div class="date">
					<div class="day">'.$post['day'].'</div>
					'.substr($post['month'], 0, 3).' '.$post['year'].'
				</div>
				<div class="nmContent">
					<a href="/blog/'.($post['pathname'] ?: $post['id']).'" class="mbTitle" onclick="Page.get(this.href);return false;">'.$post['name'].'</a>
					<p>'.text_filter(str_replace("\n", ' ', $post['content']), 155, '<br>').' <a href="/blog/'.($post['pathname'] ?: $post['id']).'" onclick="Page.get(this.href);return false;">Read more</a></p>
				</div>
			</div>';
	$i++;
}
$blog .= '</div>';

$slider = '';
//if ($user['id'] != 17) {
foreach($slides = db_multi_query('
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
					\'\'))) as link_name,
		i.descr,
		i.images,
		s.image
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
	ORDER BY `id` DESC
', true) as $slide) {
	$link = '';
	if ($slide['link_type'] != 'none') {
		switch ($slide['link_type']) {
			case 'custom':
				$link = $slide['link_id']; 
			break;
			
			case 'blog':
				$link = '/blog/view/'.$slide['link_id'];
			break;
			
			case 'category':
			case 'item':
				$link = '/'.$slide['link_type'].'/'.$slide['link_id'];
			break;
		}
	}
	$slider .= '<div class="slItem"'.(
		$slide['background'] ? ' style="background-image: url(/uploads/images/slider/'.$slide['id'].'/'.$slide['background'].');"' : ''
	).''.(
		($slide['link_type'] AND $slide['link_id']) ? ' onclick="Page.get(\''.$link.'\');"' : ''
	).'>
			<div class="slText">
				<div>'.(($slide['link_type'] == 'item' AND text_filter($slide['descr'], 500, false)) ? text_filter($slide['descr'], 500, false) : $slide['content']).'</div>
			</div>
		</div>';
		
	/*
		$slider .= '<div class="slItem image_'.($slide['image_align'] ?: 'left').'"'.(
		($slide['link_type'] AND $slide['link_id']) ? ' onclick="Page.get(\''.$link.'\');"' : ''
	).'>
		'.($slide['background'] ? '<img src="/uploads/images/slider/'.$slide['id'].'/'.$slide['background'].'">' : '').'
				<div class="ctnr">
				'.(($slide['link_type'] == 'item' AND $slide['images']) ? '<div class="slImage">
						<img src="/uploads/images/inventory/'.$slide['link_id'].'/'.explode(',', $slide['images'])[0].'">
					</div>' : ($slide['image'] ? '<div class="slImage">
						<img src="/uploads/images/slider/'.$slide['id'].'/'.$slide['image'].'">
					</div>' : '')).'
					<div class="slText">
						<h3>'.($slide['link_type'] == 'item' ? $slide['link_name'] : $slide['title']).'</h3>
						<div>'.(($slide['link_type'] == 'item' AND text_filter($slide['descr'], 500, false)) ? text_filter($slide['descr'], 500, false) : $slide['content']).'</div>
					</div>
				</div>
			</div>';
	*/
}
//}


$mpost = db_multi_query('
	SELECT 	content
	FROM `'.DB_PREFIX.'_pages`
	WHERE main_page = 1 AND confirm > 0
	ORDER BY `date` DESC LIMIT 0,1');
	
$services = '';
foreach($service = db_multi_query('
	SELECT * FROM `'.DB_PREFIX.'_store_services` WHERE image != \'\' ORDER BY RAND() DESC LIMIT 0,6
', true) as $s) {
/* 	$services .= '<div class="service dClear">
		<div class="icon '.($s['icon'] ? 'fa fa-'.$s['icon'] : '').'">
		</div>
		<div class="serContent">
			<h2>'.$s['name'].'</h2>
			<div>'.$s['content'].'</div>
			<div class="serPrice">'.$config['currency'][$s['currency'] ?: 'USD']['symbol'].' '.number_format(floatval($s['price']), 2, '.', '').'</div>
			'.($s['blog_id'] ? '<div class="more"><a href="/blog/view/'.$s['blog_id'].'" onclick="Page.get(this.href); return false;">Read more</a></div>' : '').'
		</div>
	</div>'; */
	$services .= '<div class="r-item flex">
		<a href="/repairs/iphone-repair/" onclick="Page.get(this.href); return false;">
			<img src="/uploads/images/services/'.$s['id'].'/thumb_'.$s['image'].'">
		</a>
		<div>
			<a href="'.($s['blog_id'] ? '/blog/view/'.$s['blog_id'] : '/services/'.$s['id']).'" onclick="Page.get(this.href); return false;">'.$s['name'].'</a>
			from '.$config['currency'][$s['currency'] ?: 'USD']['symbol'].' '.number_format(floatval($s['price']), 2, '.', '').'<br>
			1-2 days
		</div>
	</div>';
}

tpl_set('main', [
	'main' => $main,
	'blog' => $blog,
	'slider' => $slider,
	'main-page' => str_replace("\n", '<br>', $mpost['content']),
	'services' => $services,
	'categories' => $categories
], [
	'services' => $services
], 'content');
?>