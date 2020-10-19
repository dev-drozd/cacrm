<?php
/**
 * @appointment Sell admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
 switch($route[1]){
	 
	/*
	*  Add sell
	*/
	case 'add_sell':
		is_ajax() or die('Hacking attempt!');
		die;
	break;
	
	/*
	*  All inventary
	*/
	case null:
	case 'service':
	case 'stock':
		$meta['title'] = 'Inventory';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		$count = 20;
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name, commerce FROM `'.DB_PREFIX.'_inventory` WHERE 1 '.(
			$query ? 'AND name LIKE \'%'.$query.'%\' ' : ''
		).(in_array($route[1], [
			'stock', 'service'
		]) ? 'AND type = \''.$route[1].'\' ' : '').' AND customer_id = 0 ORDER BY `id` LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('sell/item', [
					'id' => $row['id'],
					'commerce' => $row['commerce'],
					'name' => $row['name']
				], [], 'inventories');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
			tpl_set('noContent', [
				'text' => 'There are no Inventory'
			], false, 'inventories');
		}
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['inventories'],
			]));
		}
		tpl_set('sell/main', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'inventory' => $tpl_content['inventories']
		], [], 'content');
	break;
 }
 ?>