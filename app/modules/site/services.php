<?php
/**
 * @appointment Services
 * @author      Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

if($id = (int)$route[1]){
	if($row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_services` WHERE id = '.$id)){
		
		if($row['pathname']){
			header('Location: /'.$row['pathname'], true, 301);
			die;
		}
		
		$meta['title'] = $row['title'];
		$meta['keywords'] = $row['keywords'];
		$meta['description'] = $row['description'];
		$meta['canonical'] = $service['canonical'];

		tpl_set('services/view', [
			'id' => $row['id'],
			'header' => $row['name'],
			'image' => $row['image'],
			'content' => replacePageTags($row['content'])
		], [
			'image' => $row['image'],
			'edit' => $user['edit_services']
		], 'content');
	} else {
		tpl_set('nopage', [
			'text' => 'No services'
		], [], 'content');
	}
} else if ($services = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_services`', true)) {
	
	$meta['title'] = $config['title_services']['en'];
	$meta['description'] = $config['description_services']['en'];
	$meta['keywords'] = $config['keywords_services']['en'];
	
	foreach($services as $s) {
		tpl_set('services/item', [
			'id' => $s['id'],
			'name' => $s['name'],
			'content' => replacePageTags($s['content']),
			'price' => number_format(floatval($s['price']), 2, '.', ''),
			'icon' => $s['icon'],
			'image' => $s['image'],
			'currency' => $config['currency'][$s['currency'] ?: 'USD']['symbol']
		], [
			'icon' => $s['icon'],
			'image' => $s['image']
		], 'services');
	}

	$meta['title'] = 'Services';
	tpl_set('services/main', [
		'services' => $tpl_content['services']
	], [], 'content');

} else {
	tpl_set('nopage', [
		'text' => 'No services'
	], [], 'content');
}
?>