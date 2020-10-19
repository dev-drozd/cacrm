<?php

/* $users = '';

foreach(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users` WHERE 1 LIMIT 0,50', true) as $row){
	$users .= '<li data-value="'.$row['id'].'">'.$row['name'].' '.$row['lastname'].'</li>';
}

tpl_set('sl', [
	'users' => $users
], [], 'content'); */

		sendPush2('1,31735', 'New order from the site',
					 'Someone made an order from the site '.date("Y-m-d H:i:s"),
		[
			'type' => 'alert',
			'id' => 'order-site-'.uniqid(),
		]);
		die;

echo '<pre>';


//print_r(db_multi_query('SELECT store_category_id FROM `'.DB_PREFIX.'_inventory` WHERE store_category_id > 0 AND type = \'stock\' AND del = 0 GROUP BY store_category_id', true));

$ids = [];

db_multi_query('SELECT i.store_category_id as id, c.parent_id FROM `'.DB_PREFIX.'_inventory` i INNER JOIN `'.DB_PREFIX.'_store_categories` c ON i.store_category_id = c.id WHERE i.store_category_id > 0 AND i.type = \'stock\' AND i.del = 0 GROUP BY i.store_category_id', true, false, function($a) use(&$ids){
	$ids[] = $a['id'];
	if($a['parent_id'])
		$ids[] = $a['parent_id'];
	return [0,0];
});
echo implode(',', $ids);
echo '<br>';
print_r(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_categories` WHERE id IN('.implode(',', $ids).') ORDER BY `parent_id`, `sort`', true));
die;