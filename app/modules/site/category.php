<?php
/**
 * @appointment Category
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
function childCategory($id, $menu) {
	foreach(array_keys(array_column($menu, 'parent_id'), $id) as $key) {
		if ($html) $html .= ',';
		$html .= $menu[$key]['id'].(
			count(array_keys(array_column($menu, 'parent_id'), $menu[$key]['id'])) ? ','.childCategory($menu[$key]['id'], $menu) : '');
	}
	return $html;
}
 
switch($route[1]){
	/*
	*  View category
	*/
	default:
	case 'service':
	case 'stock':
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 20;
		
		$category = db_multi_query('
			SELECT
				id,
				parent_id,
				name,
				description,
				keywords
			FROM `'.DB_PREFIX.'_store_categories`
		',true);
		
		$ids = childCategory($route[1], $category);
		
		if($sql = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS
				i.id, 
				i.pathname, 
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
			WHERE (i.commerce = 1 OR (i.images != \'\' AND i.descr != \'\')) AND i.del = 0 AND i.publish = 1 AND i.type = \'stock\' '.(
				$query ? 'AND i.name LIKE \'%'.$query.'%\' ' : ''
			).(
				$route[1] ? 'AND i.store_category_id IN ('.$route[1].($ids ? ','.$ids : '').') ' : ''
			).'ORDER BY i.id LIMIT '.($page*$count).', '.$count, true
		)){
			$i = 0;
			foreach($sql as $row){
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
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			
		} else {
			tpl_set('nopage', [
				'text' => 'There are no Inventory'
			], [], 'inventories');
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['inventories'],
			]));
		}
		
		$categories = '';
		$cid = 0;
		$sub = '';
		if ($cats = db_multi_query('
		SELECT 
			c.id, 
			c.name,
			s.id as sid, 
			s.name as sname
		FROM '.DB_PREFIX.'_store_categories c
		LEFT JOIN '.DB_PREFIX.'_store_categories s
			ON s.parent_id = c.id
		WHERE c.parent_id = 0', true)) {
			foreach($cats as $cat) {
				if ($cid != $cat['id']) {
					$categories .= ($cid ? ($sub ? '<ul>'.$sub.'</ul>' : '').'</li>' : '').'<li><a href="/category/'.$cat['id'].'" onclick="Page.get(this.href); return false;">'.$cat['name'].'</a>';

					$cid = $cat['id'];
					$sub = '';
				}
				
				if ($cat['sid'])
					$sub .= '<li><a href="/category/'.$cat['sid'].'" onclick="Page.get(this.href); return false;">'.$cat['sname'].'</a></li>';
			}
			$categories .= ($cid ? ($sub ? '<ul>'.$sub.'</ul>' : '').'</li>' : '');
		}
		
		$catname = ($route[1] == 'stock' ? 'Stock' : array_column($category, 'name', 'id')[$route[1]]);
		$description = array_column($category, 'description', 'id')[$route[1]];
		$keywords = array_column($category, 'keywords', 'id')[$route[1]];

		$meta['title'] = $catname ?: $config['title_store']['en'];
		$meta['description'] = $description ?: $config['description_store']['en'];
		$meta['keywords'] = $keywords ?: $config['keywords_store']['en'];
		
		tpl_set('category/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'inventory' => $tpl_content['inventories'],
			'category-name' => $catname,
			'categories' => $categories
		], [
			'catname' => $catname
		], 'content');
	break;
}
?>