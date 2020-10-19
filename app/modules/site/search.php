<?php
/**
 * @appointment Services
 * @author      Drozd Alexandr
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

if($query = $_REQUEST['query']){
	
	$sql = db_multi_query('SELECT id, pathname, name, content, \'page\' as type FROM `'.DB_PREFIX.'_pages` WHERE MATCH(`name`) AGAINST(\'*'.db_escape_string(trim($query)).'*\' IN BOOLEAN MODE) LIMIT 0, 50', true);
	
	$sql += db_multi_query('SELECT id, pathname, name, content, \'blog\' as type FROM `'.DB_PREFIX.'_store_blog` WHERE MATCH(`name`) AGAINST(\'*'.db_escape_string(trim($query)).'*\' IN BOOLEAN MODE) LIMIT 0, 50', true);
	
	foreach($sql as $row){
		tpl_set('search/result', [
			'pathname' => $row['pathname'],
			'name' => preg_replace('/('.preg_quote(trim($query)).')/siu', "<font color=orange>$1</font>", $row['name']),
			'content' => text_out_filter(str_replace("\n", ' ', $row['content']), 255, '<br>')
		], [], 'results');
	}
	
	tpl_set('search/main', [
		'results' => $tpl_content['results']
	], [], 'content');
	
} else {
	is_ajax() or http_response_code(404);
	$meta['title'] = 'Page not found';
	$tpl_content['content'] = '<div class="err404 ctnr"><h1>404</h1><p>Page not found</p></div>';
}
?>