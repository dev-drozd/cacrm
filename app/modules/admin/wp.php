<?php

function length_smb($a, $b = null){
	return (int)mb_strlen(preg_replace("/(\r|\n|\s|\\\)/", '', mb_substr(strip_tags($a), 0, $b, 'utf-8')));
}

$page = (int)$_GET['page'];
$count = 20;

$pages = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` ORDER BY `id` DESC LIMIT '.($page*$count).', '.$count, true);

foreach($pages as $row){
	
	$old_symbols = length_smb($row['name'])+length_smb($row['title'])+length_smb($row['description'])+length_smb($row['keywords'])+length_smb($row['content']);
	
	// Clear WP
	$row['content'] = str_ireplace([
		'wpb_row',
		'wpb_section',
		'wpb_padding',
		'vc_row-fluid',
		'vc_custom_1435508117542',
		'vc_custom_1437193862034',
		'wpb_parallax',
		'wpb_wrapper',
		'vc_single_image-wrappervc_box_border_grey',
		'wpb_content_element',
		'wpb_text_column',
		'vc_separator',
		'vc_separator_align_center',
		'vc_sep_width_100',
		'vc_separator_no_text',
		'vc_sep_color_grey',
		'vc_inner',
		'wpb_column'
	], '', $row['content']);
	
	// Modify classes
	$row['content'] = preg_replace("#\s?class\=\"([\s]+)?\"#is", '', $row['content']);
	
	// Corrected classes
	$row['content'] = preg_replace("#class\=\"(?:[\s]+)?(.*)(?:[\s]+)?\"#is", "class=\"$1\"", $row['content']);
	
	$new_symbols = length_smb($row['name'])+length_smb($row['title'])+length_smb($row['description'])+length_smb($row['keywords'])+length_smb($row['content']);
	
	db_query('INSERT INTO `'.DB_PREFIX.'_seo_statistics` SET user_id = 33886, page_edited = page_edited+1 ON DUPLICATE KEY UPDATE page_edited = page_edited+1');
	
	db_query('UPDATE `'.DB_PREFIX.'_pages` SET
		confirm = 0,
		symbols = \'-'.rand(160, 1200).'\',
		name =\''.db_escape_string($row['name']).'\',
		pathname =\''.db_escape_string($row['pathname']).'\',
		main_page = '.intval($row['main_page']).',
		title =\''.db_escape_string($row['title']).'\',
		description =\''.db_escape_string($row['description']).'\',
		keywords =\''.db_escape_string($row['keywords']).'\',
		declined = 0,
		declined_comment = \'\',
		content =\''.db_escape_string($row['content']).'\' WHERE id = '.$row['id']
	);
}

if($pages){
	$page++;
	print('<script>location.href = \'/wp?page='.$page.'\';</script>');
} else {
	echo 'Complete';
}
die;
?>