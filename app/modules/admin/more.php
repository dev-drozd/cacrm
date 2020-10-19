<?php

echo '<pre>';

/* foreach(db_multi_query('
	SELECT * FROM `'.DB_PREFIX.'_objects` WHERE 1
',1) as $store){
	$id = $store['id'];
	unset($store['id']);
	$s = 0;
	$fields = implode(',', array_keys($store)).'';
	$values = [];
	for($i = 2; $i <= 1000; $i++){
		$item = $store;
		unset($item['real_id']);
		$item['name'] .= '-'.$i;
		$values[] = '('.$id.', '.implode(',', array_map(function($a){
			return '\''.db_escape_string($a).'\'';
		}, array_values($item))).')';
		if($s >= 22){
			db_query('INSERT INTO `'.DB_PREFIX.'_objects` ('.$fields.') VALUES '.implode(', ', $values));
			$values = [];
			$s = 0;
		}
		$s++;
	}
} */

foreach(db_multi_query('
	SELECT id, real_id, image FROM `'.DB_PREFIX.'_objects` WHERE real_id > 0
',1) as $row){
	// path
	$dir = ROOT_DIR.'/uploads/images/stores/';
	
	// Is not dir
	if(!is_dir($dir.$row['id'])){
		@mkdir($dir.$row['id'], 0777);
		@chmod($dir.$row['id'], 0777);
	}
	
	$dir2 = $dir.$row['id'].'/';
	
	//copy($dir.$row['real_id'].'/'.$row['image'], $dir2.$row['image']);
	copy($dir.$row['real_id'].'/thumb_'.$row['image'], $dir2.'thumb_'.$row['image']);
}

echo 'OK';
die;
?>