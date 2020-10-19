<?php
/*
* Template
*/

// Content tpl
$tpl_content = [
	'main' => ''
];

$tpl_count = 0;

// Set tpl
function tpl_set($a, $b = [], $c = [], $d = 'main', $e = true){
	global $tpl_content, $subdomain, $tpl_count, $user;
	static $tmp = [
		'main' => ''
	];
	$tpl = TEMPLATE_DIR.$a.'.tpl';
	
	if(($e AND $tmp[$d]) OR ($tmp[$d] = file_get_contents($tpl))){
		$tpl_count++;
		
		if(strpos($tmp[$d], "{include=\"") !== false){
			$tmp[$d] = preg_replace_callback(
				"#\\{include=\"(.+?)\"\\}#is", function($m){
					$tpl = TEMPLATE_DIR.$m[1];
					if(file_exists($tpl)){
						return file_get_contents($tpl);
					} else
						return str_replace(ROOT_DIR, '', $tpl).' File is missing or not readable!';
				}
			, $tmp[$d]);
		}

		if(strpos($tmp[$d], "{lang=") !== false){
			$tmp[$d] = preg_replace_callback(
				"#\\{lang=(.+?)\\}#is", function($a){
					global $lang;
					return $lang[$a[1]];
				}
			, $tmp[$d]);
		}
		
		$find = ['{theme}'];
		$replace = ['/templates/'.(
			$subdomain == 'admin' ? 'admin' : 'site'
		)];
		foreach($b as $f => $r){
			$find[] = '{'.$f.'}';
			$replace[] = $r;
		}
		if(isset($tpl_content[$d]))
			$tpl_content[$d] .= str_ireplace($find, $replace, $tmp[$d]);
		else
			$tpl_content[$d] = str_ireplace($find, $replace, $tmp[$d]);
		
		$c += ['dev' => in_array($user['id'], [16,17])];
		
		if(is_array($c)){
			foreach($c as $k => $v){
				$tpl_content[$d] = preg_replace_callback("#\\[$k\\](.*?)\\[/$k\\]#is",
				function($m) use ($k, $v){
					$exp = strpos($m[1], "[not-$k]") !== false ? explode("[not-$k]", $m[1]) : array($m[1], '');
					return $v ? $exp[0] : $exp[1];
				}, $tpl_content[$d]);
			}
		}
		return $tpl_content[$d];
		
	}
}
function tpl_get($a){
	global $tpl_content;
	return $tpl_content[$a];
}

/*
* Memcached
*/
$memcache = false;
$cache_count = 0;

function cache_init(){
	global $memcache;
	if(!$memcache){
		$memcache = new Memcached();
		$memcache->addServer('127.0.0.1', 11211) or die("MemcacheD not connect");
	}
	return $memcache;
}

function cache_set($a, $b, $c = 0){
	global $memcache;
	cache_init()->set(md5($a), $b, $c);
}

function cache_get($a){
	global $memcache, $cache_count;
	$cache_count++;
	return cache_init()->get(md5($a));
}

function cache_delete($a, $b = 0){
	global $memcache;
	cache_init()->delete(md5($a), $b);
}

/*
* Mysqli
*/
$db_link = null;
$db_query = null;
$db_queries = [];
$db_debug = true;
$db_count = 0;

function db_connect(){
	global $db_link;
	if(!$db_link){
		if($db_link = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT)){
			mysqli_set_charset($db_link, DB_CHARSET);
		} else
			db_error(mysqli_connect_error(), mysqli_connect_errno());	
	}
	return $db_link;
}

function db_query($q, $debug = false){
	if($debug){
		die($q);
	}
	global $db_query, $db_debug, $db_count, $db_queries, $db_link;
	$timer = microtime(true);
	if($db_query = mysqli_query(db_connect(), $q)){
		if($db_debug){
			array_push($db_queries, array(
				'query' => $q,
				'time' => sprintf('%f', microtime(true)-$timer)
			));
			$db_count++;
		}
	} else
		db_error(mysqli_error($db_link), mysqli_errno($db_link), $q);
	return $db_query;
}

function db_multi_query($a, $b = false, $c = false){
	global $db_query;

	$res = [];
	db_query($a);
	if($b){
		$i = 0;
		while($row = mysqli_fetch_assoc($db_query)){
			if($c){
				$cb = $c($row, $i);
				$res[$cb[0]] = $cb[1];
			} else
				$res[] = $row;
			$i++;
		}
	} else
		$res = mysqli_fetch_assoc($db_query);
	
	mysqli_free_result($db_query);
	
	return $res;	
}

function db_escape_string($a){
	global $db_link;
	return db_connect() ? mysqli_real_escape_string($db_link, $a) : addslashes($a);
}

function db_error($a, $b, $c = ''){
	global $user;
	//if($user['id'] == 16 OR $user['id'] == 17){
		echo $a, $b, $c;
		die;
	//}
}

function send_sms($a, $b, $c = ''){
	$b = strip_tags($b);
	$a = preg_replace("/\D/", '', $a);
	if (strlen($a) >= 10 AND $b){
		if(($l = strlen($a)) > 10) $a = substr($a, $l-10);
		foreach([
			'text.wireless.alltel.com',
			'txt.att.net',
			'myboostmobile.com',
			'sms.mycricket.com',
			'messaging.sprintpcs.com',
			'tmomail.net',
			'email.uscc.net',
			'vtext.com',
			'vmobl.com'
		] as $carrier){
			mail($a.'@'.$carrier, $c, $b, [
				'MIME-Version' => '1.0',
				'Content-type' => 'text/plain; charset=utf-8',
				'From' => 'sms@yoursite.com'
			]);
		}
	}
}

function text_filter($a, $b = null, $c = null){
	return db_escape_string(
		mb_substr(trim(
			isset($c) ? strip_tags($a, $c) : $a
			), 0, $b, 'utf-8'
		)
	);
}

function ids_filter($ids){
	return implode(',', array_filter(is_array($ids) ? $ids : explode(',', $ids), function($a){
		return is_numeric($a) && $a > 0;
	}));
}

function get_token($a){
	return md5(md5($a).md5(SALT, true));
}

function send_push($id, $data){
	global $user;
	$res = [];
	$push = function($id, $data) use(&$res){
		$arguments = '';
		if(is_array($data['arguments'])){
			foreach($data['arguments'] as $k => $v){
				$arguments .= '&'.$k.'='.get_token($v);
			}
		}
		$url = 'https://crm.yoursite.com/push.sock?'.(
			$id == 0 ? 'all='.get_token(1) : 'token='.(
				is_int($id) ? get_token($id) : $id
			)
		).$arguments;
		
		echo $url;

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_HTTPHEADER => ["Content-Type: text/json"],
			CURLOPT_TIMEOUT => 1,
			CURLOPT_NOSIGNAL => 1,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0
		]);
		$res[] = curl_exec($ch);
		curl_close($ch);		
	};
	if(is_array($id)){
		foreach($id as $i){
			$push($i, $data);
		}
	} else
		$push($id, $data);
	return $res;
}

function auth_app(){
	global $res, $app;
	if($app = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_apps` WHERE secret_key = \''.db_escape_string($_REQUEST['secret_key']).'\' AND id = '.intval($_REQUEST['app_id']))){
		if($app['permission'] == '*' OR in_array($_GET['method'], implode(',', $app['permission'])))
			return false;
		else
			$res['err'] = 'Permission denied';
	} else
		$res['err'] = 'secret_key or app_id is incorrect';
	
	exit(json_encode($res));
}

function is_token(){
	global $res, $user;
	if ($user = db_multi_query('SELECT u.name as first_name, u.lastname as last_name, u.image, u.id FROM `'.DB_PREFIX.'_access_tokens` t INNER JOIN `'.DB_PREFIX.'_users` u ON t.user_id = u.id WHERE token = \''.db_escape_string($_REQUEST['access_token']).'\'')) {
		db_query('UPDATE `'.DB_PREFIX.'_users` SET last_visit_app = Now() WHERE id = '.$user['id']);
		$res['user'] = $user;
		return true;
	} else
		return false;
}

function outputMsg($a){
	return preg_replace(	
		[
			//"~(?:https?\:\/\/|)(?:www\.|)(?:youtube\.com|youtu\.be)\/(?:embed\/|v\/|watch\?v=|)(.{11})((&|\?)*[\S]*[\s]?)?~",
			//"~((?:ht|f)tps?)://[^img.youtube](.*?)(\s|\n|[,.?!](\s|\n)|$)~",
			//"~(([a-z0-9+_-]+)(.[a-z0-9+_-]+)*@([a-z0-9-]+.)+[a-z]{2,6})~",
			"/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i",
			"~\,\s\(([0-9-+--\s]+)\)\sstarted\sa\schat~",
			"~((?:ht|f)tps?)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~",
		], [
/* 			'<div class="youtube" onmousedown="app.getYoutube(this, \'$1\')">
				<img width="640" height="auto" src="https://img.youtube.com/vi/$1/sddefault.jpg">
				<span class="fa fa-youtube-play"></span>
			</div>', */
			//'Email: <a href="mailto:$1">$1</a>',
			"Email: $1<a href=\"mailto:$2@$3\">$2@$3</a>",
			'Phone: <a href="tel:$1">$1</a>Started a chat',
			'<a href="$1://$2" target="_blank">$1://$2</a>$3'
		], htmlspecialchars($a, ENT_HTML5)
	);
}

define('API_ACCESS_KEY', 'AIzaSyCKDQtULzIqXaGQ83OK_zPaaK0WQVtgr8E');

function sendPush($id,$title,$message,$info){
	global $user;
	$data['data'] = [
		'message' => $message,
		'title' => $title,
		'info' => $info,
		'additionalData' => [
			'vibrate' => 1,
			'sound' => 1,
		]
	];
	if ($staffs = db_multi_query('SELECT push_id FROM `'.DB_PREFIX.'_users` WHERE '.(
		$id ? 'id = '.$id : 'id != '.$user['id']
	), true)){
		$data['registration_ids'] = [];
		foreach($staffs as $staff){
			$data['registration_ids'][] = $staff['push_id'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: key=' . API_ACCESS_KEY,
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		curl_close($ch);
		//echo $result;	
	}
}

function auto_rotate_image($a){
    switch($a->getImageOrientation()){
        case imagick::ORIENTATION_BOTTOMRIGHT: 
            $a->rotateimage("#000", 180);
        break; 
        case imagick::ORIENTATION_RIGHTTOP: 
            $a->rotateimage("#000", 90);
        break; 
        case imagick::ORIENTATION_LEFTBOTTOM: 
            $a->rotateimage("#000", -90);
        break; 
    } 
    $a->setImageOrientation(imagick::ORIENTATION_TOPLEFT); 
}

function min_price($a, $o){
	if ($o AND $object = db_multi_query('SELECT purchase_price FROM `'.DB_PREFIX.'_objects` WHERE id = '.$o)) {
		$pp = json_decode($object['purchase_price'], true);
		ksort($pp);
		foreach($pp as $k => $v){
			if($a < $k) return $a+$a/100*$v;
		}
		return $a+$a/100*$pp[0];
	} else {
		global $config;
		ksort($config['price_formula']);
		foreach($config['price_formula'] as $k => $v){
			if($a < $k) return $a+$a/100*$v;
		}
		return $a+$a/100*$config['price_formula'][0];
	}
}

function in_to_array($a, $b){
	$r = false;
	if(!is_array($a))
		$a = explode(',', $a);
	if(!is_array($b))
		$b = explode(',', $b);
	foreach($a as $item){
		if($r) break;
		$r = in_array($item, $b);
	}
	return $r;
}
?>