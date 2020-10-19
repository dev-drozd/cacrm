<?php
/**
 * @appointment Home page
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
*/

header("Access-Control-Allow-Origin: *");

error_reporting(E_ERROR);

define('ENGINE', true);
define('SALT', '46dXW4=J;Nt*cUSX+8');
define('ROOT_DIR', __DIR__);
define('SITE_DIR', str_replace('/api', '', __DIR__));
define('METHOD_DIR', ROOT_DIR.'/methods');

include SITE_DIR.'/app/data/db.php';

include ROOT_DIR.'/functions.php';

header('Content-type: text/html; charset=utf-8');

mb_internal_encoding('UTF-8');

$res = [
	'err' => 0
];

$user = [];

$app = [];

$config = include SITE_DIR.'/app/data/config.php';

$exp_method = explode('-', str_replace(
	'\\', '',trim($_GET['method'], '/')
));

$method = $exp_method[0];

$sub_method = $exp_method[1];

unset($exp_method);

$method_file = METHOD_DIR.'/'.$method.'.php';

if(file_exists($method_file))
	include $method_file;
else {
	$res = [
		'err' => 'Unknown method'
	];
}

$res['server_time'] = time();

header('Content-Type: application/json');

echo json_encode(
	$res, 
	JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK
);
?>