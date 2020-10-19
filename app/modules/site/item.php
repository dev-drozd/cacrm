<?php
/**
 * @appointment Item
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
 
switch($route[1]){
	/*
	*  View item
	*/
	default:
		$meta['title'] = 'Item';
		if($row = db_multi_query('
				SELECT
					i.*,
					t.options as opts,
					c.name as category_name,
					sc.name as store_category_name,
					o.name as os_name
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_inventory_types`
					t ON i.type_id = t.id
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
					ON c.id = i.category_id
				LEFT JOIN `'.DB_PREFIX.'_inventory_os` o
					ON o.id = i.os_id
				LEFT JOIN `'.DB_PREFIX.'_store_categories` sc
					ON sc.id = i.store_category_id
				WHERE '.(
					!is_numeric($route[1]) ? 'i.pathname = \''.db_escape_string(urldecode($route[1])).'\'' : 'i.id = '.$route[1]
				).' AND del = 0 AND publish = 1'
		)){
			$id = $row['id'];
			
			if(is_numeric($route[1]) && $row['pathname']){
				header('Location: /item/'.$row['pathname'], true, 301);
				die;
			}
			
			
			$options = '';
			if($row['options']){
				$opts = json_decode($row['opts'], true);
				foreach(json_decode($row['options'], true) as $n => $v){
					if(!$v) continue;
					if(is_array($v)){
						$vlue = [];
						foreach($v as $f){
							$vlue[] = $opts[$n]['sOpts'][$f];
						}
						$vlue = implode(', ', $vlue);
					} else {
						$vlue = is_array($opts[$n]['sOpts']) ? $opts[$n]['sOpts'][$v] : $v;
					}
					$options .= '<div><span>'.$opts[$n]['name'].''.(
						$row['type'] == 'stock' ? ':</span> '.$vlue : '</span>'
					).'</div>';
				}
			}
			
			
			$meta['title'] = $row['stitle'] ?: ($row['name'] ?: ($row['category_name'].' '.$row['model']));
			$meta['description'] = $row['description'] ?: $row['descr'];
			$meta['keywords'] = $row['keywords'];
			$meta['canonical'] = 'item/'.$row['canonical'];
			
			
			$images = explode('|', $row['images']);
			if($images[0])
				$meta['image'] = '/uploads/images/inventory/'.$id.'/preview_'.$images[0];
			$img = '';
			if ($images) {
				foreach($images as $k => $image) {
					if ($image)
						$img .= '<img src="/uploads/images/inventory/'.$id.'/preview_'.$image.'?v=2" onclick="showPhoto.change(this);"'.(!$k ? ' class="active"' : '').'>';
				}
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
			
			tpl_set('item', [
				'id' => $id,
				'name' => $row['type'] == 'stock' ? $row['category_name'].' '.$row['model'] : $row['name'],
				'model' => $row['model'],
				'os_name' => $row['os_name'],
				'ver_os' => $row['ver_os'],
				'price' => $row['price'],
				'category-id' => $row['category_id'],
				'category' => $row['category_name'],
				'store-category-id' => $row['store_category_id'],
				'store-category' => $row['store_category_name'],
				'descr' => $row['descr'],
				'images' => $img,	
				'img' => $images[0] ?? '',	
				'options' => $options,
				'categories' => $categories
			], [
				'stock' => ($row['type'] == 'stock'),
				'catname' => $row['store_category_name'],
				'edit' => ($user['edit_stock'] or $user['add_stock']),
				'img' => $row['images'],
				'model' => $row['model'],
				'os_name' => $row['os_name']
			], 'content');
		} else {
			http_response_code(404);
			tpl_set('nopage', [
			], [
			], 'content');
		}
	break;
}
?>