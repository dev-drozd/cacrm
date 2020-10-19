<?php
//auth_app();

//is_token();

$sql = '';
$store = [];
$data = [
	'ids' => [],
	'data' => []
];

foreach(db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects`', true) as $a){
	$store[$a['id']] = $a['name'];
	$sql .= ($sql ? ', ' : '').'COUNT(IF(status_id = 30 AND object_owner = '.$a['id'].', 1, NULL)) as `'.$a['id'].'`';
}

foreach(db_multi_query('SELECT '.$sql.' FROM `'.DB_PREFIX.'_issues` WHERE `date` BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()') as $k => $v){
	$data['ids'][] = $store[$k];
	$data['data'][] = $v;
}

$res['jobs'] = $data;
?>