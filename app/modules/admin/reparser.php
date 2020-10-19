<?php

defined('ENGINE') or ('hacking attempt!');


function json_decode_fix($a){
	$b = json_decode($a, true);
	if(json_last_error()){
		$b = json_decode(preg_replace_callback('/\"\:\"(.*)\"[,}]/U', function($a){
				return str_replace($a[1], str_replace('"', '\"', $a[1]), $a[0]);
		}, $a), true);
	}
	return $b;
}


switch($route[1]){
	
	case 'jobs-inventory-purchase':
	
		echo '<pre>';
		$jobs = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, inventory_info, purchase_info, reparse FROM `'.DB_PREFIX.'_issues` WHERE reparse = 0 AND (LENGTH(purchase_info) > 2 OR LENGTH(inventory_info) > 2) ORDER BY `id` DESC LIMIT 0, 20', 1);
		
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		
		$values = [];
		
		foreach($jobs as $job){
			
			$purchase_info = [];
			
			if($job_purchases = json_decode_fix($job['purchase_info'])){
			
				$purchases = db_multi_query('SELECT id, price FROM `'.DB_PREFIX.'_purchases` WHERE id IN('.implode(',', array_keys($job_purchases)).')', true, false, function($a){
					return [$a['id'], $a];
				});
				
				foreach($job_purchases as $id => $row){
					$row['cost_price'] = $purchases[$id]['price'];
					$purchase_info[$id] = $row;
				}
			}
			
			$inventory_info = [];
			
			if($job_inventories = json_decode_fix($job['inventory_info'])){
			
				$inventories = db_multi_query('SELECT id, purchase_price FROM `'.DB_PREFIX.'_inventory` WHERE id IN('.implode(',', array_keys($job_inventories)).')', true, false, function($a){
					return [$a['id'], $a];
				});
				
				foreach($job_inventories as $id => $row){
					$row['cost_price'] = $inventories[$id]['purchase_price'];
					$inventory_info[$id] = $row;
				}
			}
			
			$str = '('.$job['id'];
			$str .= $inventory_info ? ',\''.db_escape_string(json_encode($inventory_info)).'\'' : ',\'{}\'';
			$str .= $purchase_info ? ',\''.db_escape_string(json_encode($purchase_info)).'\'' : ',\'{}\'';
			$str .= ',1)';
			
			$values[] .= $str;
		}
		
		if($jobs){
			$sql = 'INSERT INTO `'.DB_PREFIX.'_issues` (id,inventory_info,purchase_info,reparse) VALUES '.implode(',', $values).' ON DUPLICATE KEY UPDATE inventory_info = VALUES(inventory_info), purchase_info = VALUES(purchase_info), reparse = VALUES(reparse);';
			//print_r($jobs);
			//echo $sql;
			db_query($sql);
			echo '<br />Осталось перепарсить: '.$res_count.' jobs<script>setTimeout(function(){location.reload()}, 100);</script>';
		} else
			echo 'Готово';
	break;
}
die;
?>