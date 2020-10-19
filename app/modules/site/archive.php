<?php
$id = (int)$route[1];
if($row = db_multi_query('SELECT a.*, CONCAT(u.name, \' \', u.lastname) as author_name FROM `'.DB_PREFIX.'_work_archive` a INNER JOIN `'.DB_PREFIX.'_users` u ON a.staff_id = u.id WHERE a.id = '.$id.(
	$user['archive_job_view'] ? '' : ' AND confirm = 1'
))){
	$meta['title'] = $row['title'];
	$meta['description'] = $row['description'];
	$meta['keywords'] = $row['keywords'];
	$services = '';
	if($row['services']){
		foreach(json_decode($row['services'], 1) as $service){
			$services .= '<li>'.$service.'</li>';
		}
	}
	$row['content'] = '<p>Hello everyone, my name is '.$row['author_name'].' and I work at Your Company, today a client brought us an '.$row['device'].' with the "'.$row['description'].'" problem and I will solve this problem as follows:</p>'.
	$row['content'].(
		$row['services'] ? '<p>We have provided the following services to the client <ol>'.$services.'</ol></p>' : ''
	).'<div style="text-align: center;"><button class="btn btn-quote" onclick="this_quote();" onmousedown="gtag_report_conversion()"><h2 style="color: #fff;">Get quote</h2></button></div>';
	tpl_set('archive', [
		'id' => $row['id'],
		'image' => $row['image'],
		'issue' => $row['description'],
		'title' => $row['title'],
		'content' => $row['content']
	], [], 'content');
} else {
	is_ajax() or http_response_code(404);
	$meta['title'] = 'Page not found';
	$tpl_content['content'] = '<div class="err404 ctnr"><h1>404</h1><p>Page not found</p></div>';
}
?>