<?php

defined('ENGINE') or ('hacking attempt!');
include APP_DIR.'/classes/imap.php';

/* send_sms2('+15183302082', 'Тест quote');
die;



if($_GET['file']){
	imap::get_file($_GET['file']);
	die;
	
function getUrlMimeType($a) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    return $finfo->buffer($a);
}
	echo finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), imap::get_file($_GET['file']));
	die;
}

echo '<style>blockquote {
    border-left: solid 2px #000000;
    margin: 4px 2px;
    padding-left: 6px;
}
a {
    color: #4477bb;
    text-decoration: none;
    cursor: pointer;
}</style>'; */

imap::query('FROM "5183302082@"', function($a){
	$data = imap::get_msg($a);
	$msg = $data['msg'];
	$attach = '';
	foreach($data['attachments'] as $k => $v){
		$url = 'FROM "dev.drozd@gmail.com"|'.$a.'|'.$k.'';
		$mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $v);
		$attach .= '<a href="/u?file='.base64_encode($url).'" download>'.$k.'</a><br>';
	}
	echo '<div>From: '.$data['from'].'<br />'.$msg.'<br />'.$attach.'<br />Date:'.$data['date'].'</div><hr>';
});
imap::close();
die;

die;

if($users = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_new` WHERE state = \'NY\' LIMIT '.(intval($_GET['page'])*100).', 100', true)){
	foreach($users as $user){
		if(is_numeric($user['city'])) continue;
		$data = json_decode(file_get_contents('http://api.vk.com/method/database.getCities?v=5.5&country_id=9&need_all=0&region_id=5060716&q='.$user['city']), true);
		if($data['response']['count'] > 0){
			foreach($data['response']['items'] as $row){
				if($row['region'] == 'New York'){
					db_query('UPDATE `'.DB_PREFIX.'_new` SET city = '.$row['id'].', state = 5060716 WHERE id = '.$user['id']);
					break;
				}
			}
		}
	}
}
echo 'OK';
//echo '<pre>';
//print_r(json_decode($data, true)['response']['items']);
//print_r($users);
//die; //5060716
?>