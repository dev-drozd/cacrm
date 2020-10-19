<?php
/**
 * @appointment Main functions
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
function compressImage($s,$n = false){
	$i = new Imagick();
	list($width, $height, $type) = getimagesize($s);
	$i->newImage($width, $height, "white");
	$i->compositeimage(new Imagick($s), Imagick::COMPOSITE_OVER, 0, 0);
	$i->setImageCompressionQuality(85);
	$i->setImageFormat('jpg');
	$i->setSamplingFactors(['2x2', '1x1', '1x1']);
	$p = $i->getImageProfiles("icc", true);
	$i->stripImage();
	if(!empty($p))
		$i->profileImage('icc', $p['icc']);
	
	$i->setInterlaceScheme(Imagick::INTERLACE_JPEG);
	$i->setColorspace(Imagick::COLORSPACE_SRGB);
	$i->writeImage($n ?: $s);
	return $i;
}
 
 
function print_a($a){
	echo '<pre>';
	print_r($a);
	echo '</pre>';
}
 
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

	$c += array(
		'quote' => true
	);
	
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
			(ADMIN_DIR == 'admin' && isset($_GET['new_designr'])) ? 'admin-new' : (
				($subdomain == 'beta' && ADMIN_DIR == 'site') ? 'new-site' : ADMIN_DIR
			)
		)];
		foreach($b as $f => $r){
			$find[] = '{'.$f.'}';
			$replace[] = $r;
		}
		if(isset($tpl_content[$d]))
			$tpl_content[$d] .= str_ireplace($find, $replace, $tmp[$d]);
		else
			$tpl_content[$d] = str_ireplace($find, $replace, $tmp[$d]);
		
		$c += ['dev' => in_array($user['id'], [1,31735])];
		
		if(is_array($c)){
			foreach($c as $k => $v){
				$tpl_content[$d] = preg_replace_callback("#\\[$k\\](.*?)\\[/$k\\]#is",
				function($m) use ($k, $v){
					global $NeedReplacePageContent;
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
	global $memcache, $config;
	if(!$memcache){
		$memcache = new Memcached();
		//$srv = explode(":", $config['cache_host']);
		//$memcache->addServer('/var/run/memcached/memcached.sock', 0) or die("MemcacheD not connect");
		$memcache->addServer('127.0.0.1', 11211) or die("MemcacheD not connect");
	}
	return $memcache;
}

function cache_set($a, $b, $c = 0){
	global $memcache, $config;
	cache_init()->set($config['cache_key'].md5($a), $b, $c);
}

function cache_get($a){
	global $memcache, $config, $cache_count;
	$cache_count++;
	return cache_init()->get($config['cache_key']. md5($a));
}

function cache_delete($a, $b = 0){
	global $memcache, $config;
	cache_init()->delete($config['cache_key']. md5($a), $b);
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
            mysqli_query($db_link, "SET SESSION sql_mode = ''");
        } else
			db_error(mysqli_connect_error(), mysqli_connect_errno());
	}
	return $db_link;
}

function db_query($q, $debug = true){
	if($debug){
		//die($q);
	}
	global $db_query, $db_debug, $db_count, $db_queries, $db_link, $user;
/* 	if($user['id'] == 16)
		echo $q."\n"; */
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

function db_multi_query($a, $b = false, $c = false, $d = false, $e = 0){
	global $db_query;
	ini_set('memory_limit', '-1');
	if($c AND ($data = cache_get($c)))
		return unserialize($data);
	$res = [];
	db_query($a);
	if($b){
		$i = 0;
		while($row = mysqli_fetch_assoc($db_query)){
			if($d && ($cb = $d($row, $i))){
				$res[$cb[0] ?: $i] = $cb[1];
			} else
				$res[] = $row;
			$i++;
		}
	} else
		$res = mysqli_fetch_assoc($db_query);
	
	mysqli_free_result($db_query);
	
	if($c AND $res) cache_set($c, serialize($res), $e);
	
	return $res;	
}

function db_escape_string($a){
	global $db_link;
	return db_connect() ? mysqli_real_escape_string($db_link, $a) : addslashes($a);
}

function db_error($a, $b, $c = false){
	global $user;
	//if($user['id'] == 31735 OR $user['id'] == 17){
		echo $a, $b, $c;
		die;
//	}
}

function log_sql_low(){
	global $db_queries, $user;
	//if($user['id'] != 31735) return false;
	foreach($db_queries as $a){
		if($s = $a['time'] > 1 ? 1 : ($a['time'] > 0.5 ? 2 : 0)){
			db_query('
				INSERT IGNORE INTO `'.DB_PREFIX.'_slow_sql_queries`
				SET user_id = \''.$user['id'].'\',
				url = \''.db_escape_string($_SERVER['REQUEST_URI']).'\',
				query = \''.db_escape_string(trim($a['query'])).'\',
				time = \''.db_escape_string($a['time']).'\',
				hash = \''.md5($_SERVER['REQUEST_URI'].$a['query']).'\',
				status = '.(int)$s
			);
		}
	}
}

function db_debug($a = false){
	global $db_queries, $user;
	if(isset($_GET['debug']) && ($user['id'] == 31735 OR $user['id'] == 1)){
		$time = 0;
		$body = '<style>code {display: block;padding: 15px;border: 1px solid #eaeaea;word-wrap: break-word;}body{background: #f1f1f1;}code.red{background: #ff00001f;}h2.red{color: #ff0000}code.yellow{background: #ffff001f;}h2.yellow{color: #bdbd1f;}code.green{background: #0080001f;}h2.green{color: green;}</style><div style="position: relative; clear: both;">';
		foreach($db_queries as $row){
			$qst = $row['time'] > 1 ? 'red' : (
				$row['time'] > 0.5 ? 'yellow' : 'green'
			);
			$body .= str_ireplace('<code','<code class="'.$qst.'"', str_ireplace(
				['&lt;?php','?&gt;'],'', highlight_string("<?php\n".trim($row['query'])."\n?>",true)
			));
			$body .= '<h2 class="'.$qst.'"> In progress for '.$row['time'].' sec</h2>';
			$time += $row['time'];
		}
		$body .= '<h1>Total execution time of all requests: '.$time.' sec</h1></div>';
		if($a)
			die($body);
		else
			return $body;
	} else
		return '';
}

function conf_save(){
	global $training_mode, $vermont_mode, $config;
	return file_put_contents(
		APP_DIR.'/data/'.(
			$training_mode ? 'training_' : (
				$vermont_mode ? 'vermont_' : ''
			)
		).'config.php',
		"<?php\nreturn defined('ENGINE') ? ".
			PHP_EOL.var_export($config, true).PHP_EOL.
		" : die(\"Hacking attempt!\");\n?>"
	);
}

function print_code($a){
	echo '<pre>';
		print_r($a);
	die;
}

function getOrderByType($a){
	return $a ? 'DESC' : 'ASC';
}

function json_escape($a){
	return str_replace('"', '\"', $a);
}

function is_imap_base64($a){
    return imap_base64($a);
}

function loggout(){
	global $config, $user;

	//die;
	// is session
	if(isset($_SESSION['uid'])){
		unset($_SESSION['uid']);
		unset($_SESSION['lastqtime']);
		@session_destroy();
		@session_unset();
	}
	
	// is cookies
	if(isset($_COOKIE['uid'])){
		//setcookie('uid', '', 0, '/', $_SERVER["HTTP_HOST"], null, true);
		//setcookie('hid', '', 0, '/', $_SERVER["HTTP_HOST"], null, true);
		setcookie('uid', '', 0, '/', 'yoursite.com', null, true);
		setcookie('uid', '', 0, '/', 'admin.yoursite.com', null, true);
		setcookie('hid', '', 0, '/', 'yoursite.com', null, true);
		setcookie('hid', '', 0, '/', 'admin.yoursite.com', null, true);
	}
		
	if($user){
		db_query('
			INSERT INTO `'.DB_PREFIX.'_activity`
			SET user_id = \''.$user['id'].'\',
			date = \''.date('Y-m-d H:i:s', time()).'\',
			object_id = '.(int)$user['store_id'].',
			event = \'loggout\''
		);
			
		send_push(0, [
			'type' => 'activity',
			'html' => '<div class="tr">
				<div class="td lh45">
					<a href="/users/view/'.$user['id'].'" target="_blank">
						'.(
							$user['image'] ?
								'<img src="/uploads/images/users/'.$user['id'].'/thumb_'.$user['image'].'" class="miniRound">' :
							'<span class="fa fa-user-secret miniRound"></span>'
						).'
						'.$user['uname'].' '.$user['ulastname'].'
					</a>
				</div>
				<div class="td">loggout</div>
				<div class="td">'.$user['object_name'].'</div>
				<div class="td">'.date("Y-m-d H:i:s").'</div>
			</div>'
		]);	
	}
	
	// Is not ajax
	if(!is_ajax()){
		header('Location: /');
	} else
		echo json_encode('loggout');
	
	die;
}

/*
* Clear html comments
*/
function output_browser($a){
/* 	$a = preg_replace_callback('#<script>(.+?)</script>#is', function($s) use(&$a){
			$s[1] = str_replace('var', 'var ', preg_replace(['/\n/','/\t/','/\rn/','/\r/','/\s/'], '', $s[1]));
			return '<script>'.$s[1].'</script>';
	}, $a); */
	return is_ajax() ? $a : preg_replace_callback([
		'/<!--[\s\S]*?-->/',
		 '/((?<=>)|(?<=--)|(?<=.))[\s\n\r\t]+((?=--)|(?=<))/Us',
		'/(?:[\^team]{4})\s(?:[oenrutmha="]{13})\s(?:[tnce"=o]{9})(C.*\))(?:[">]{2})/i'
	], function($s){
		return (isset($s[1]) && md5($s[1]) == SALT) ? $s[0] : (
			(!isset($s[1]) OR empty($s[1])) ? '' : call_user_func(
				'output_browser'
			)
		);
	}, $a);
}

/*
* Filters
*/
function text_filter($a, $b = null, $c = null){
	return db_escape_string(
		mb_substr(trim(
			isset($c) ? strip_tags($a, $c) : $a
			), 0, $b, 'utf-8'
		)
	);
}
function text_out_filter($a, $b = null, $c = null){
	return mb_substr(trim(
		isset($c) ? strip_tags($a, $c) : $a
		), 0, $b, 'utf-8'
	);
}
function array_text_filter($a, $b = null, $c = false){
	static $d, $e;
	if($b) $d = $b;
	if($c) $e = $c;
	return is_array($a) ? array_map('array_text_filter', $a) : text_filter($a, $d, $e);
}
function ids_filter($ids){
	return implode(',', array_filter(is_array($ids) ? $ids : explode(',', $ids), function($a){
		return is_numeric($a) && $a > 0;
	}));
}
$translete = [
	"а" => "a",
	"б" => "b",
	"в" => "v",
	"г" => "g",
	"д" => "d",
	"е" => "e",
	"ё" => "yo",
	"ж" => "j",
	"з" => "z",
	"и" => "i",
	"й" => "i",
	"к" => "k",
	"л" => "l",
	"м" => "m",
	"н" => "n",
	"о" => "o",
	"п" => "p",
	"р" => "r",
	"с" => "s",
	"т" => "t",
	"у" => "y",
	"ф" => "f",
	"х" => "h",
	"ц" => "c",
	"ч" => "ch",
	"ш" => "sh",
	"щ" => "sh",
	"ы" => "i",
	"э" => "e",
	"ю" => "u",
	"я" => "ya",
	"А" => "A",
	"Б" => "B",
	"В" => "V",
	"Г" => "G",
	"Д" => "D",
	"Е" => "E",
	"Ё" => "Yo",
	"Ж" => "J",
	"З" => "Z",
	"И" => "I",
	"Й" => "I",
	"К" => "K",
	"Л" => "L",
	"М" => "M",
	"Н" => "N",
	"О" => "O",
	"П" => "P",
	"Р" => "R",
	"С" => "S",
	"Т" => "T",
	"У" => "Y",
	"Ф" => "F",
	"Х" => "H",
	"Ц" => "C",
	"Ч" => "Ch",
	"Ш" => "Sh",
	"Щ" => "Sh",
	"Ы" => "I",
	"Э" => "E",
	"Ю" => "U",
	"Я" => "Ya",
	"ь" => "",
	"Ь" => "",
	"ъ" => "",
	"Ъ" => ""
];
function from_translit(){
	global $translete;
	return strtr($text, array_flip($translete));
}
function to_translit($text){
	global $translete;
	return strtr($text, $translete);
}
function create_name($a){
	return mb_strtolower(preg_replace('/\s+/', '_', to_translit($a)), 'UTF-8');
}
function str_merge($a, $b){
	if(is_array($a)){
		$a = array_unique(array_merge($a, explode(',', $b)));
	} else {
		$a = explode(',', $b);
	}
	return $a;
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

function http2_static($a){
	$types = [
		'js' => 'script',
		'css' => 'style',
		'jpg' => 'image',
		'png' => 'image',
		'gif' => 'image',
		'svg' => 'image',
		'webm' => 'video',
		'mp4' => 'video',
		'woff' => 'font',
		'woff2' => 'font',
		'Icons' => 'font'
	];
	$static = [];
	if(preg_match_all('/(?:src|href)=\"(\/templates\/'.ADMIN_DIR.'\/(?:js|img|css)\/.*\.(js|css|jpg|png|svg|webm))\"/i', $a, $b)){
		foreach($b[0] as $k => $v){
			$static[] = '<'.$b[1][$k].'>; rel=preload; as='.$types[$b[2][$k]];
		}
		header('Link: '.implode(', ', $static), false);
	}
	return ADMIN_DIR == 'admin' ? $a : preg_replace("/\s{2,}/",'', $a);
	//return $a;
}

// Bar code
function to_barcode($a, $b = []){
	$codes = [
		32 => '100011011001110110',
		36 => '100010001000100110',
		37 => '100110001000100010',
		42 => '100010011101110110',
		43 => '100010011000100010',
		45 => '100010011001110111',
		46 => '110010011001110110',
		47 => '100010001001100010',
		48 => '100110001101110110',
		49 => '110110001001100111',
		50 => '100111001001100111',
		51 => '110111001001100110',
		52 => '100110001101100111',
		53 => '110110001101100110',
		54 => '100111001101100110',
		55 => '100110001001110111',
		56 => '110110001001110110',
		57 => '100111001001110110',
		65 => '110110011000100111',
		66 => '100111011000100111',
		67 => '110111011000100110',
		68 => '100110011100100111',
		69 => '110110011100100110',
		70 => '100111011100100110',
		71 => '100110011000110111',
		72 => '110110011000110110',
		73 => '100111011000110110',
		74 => '100110011100110110',
		75 => '110110011001100011',
		76 => '100111011001100011',
		77 => '110111011001100010',
		78 => '100110011101100011',
		79 => '110110011101100010',
		80 => '100111011101100010',
		81 => '100110011001110011',
		82 => '110110011001110010',
		83 => '100111011001110010',
		84 => '100110011101110010',
		85 => '110010011001100111',
		86 => '100011011001100111',
		87 => '110011011001100110',
		88 => '100010011101100111',
		89 => '110010011101100110',
		90 => '100011011101100110'
	];
	$code = [];
	$a = (string)strtoupper($a);
	$i = 0;
	while(isset($a[$i])) $code[] = $a[$i++];
	array_unshift($code, "*");
	array_push($code, "*");
	if(!is_array($code) || !count($code)) return false;
	$bars = [];
	$pos = 5;
	$bstr = null;
	$i = 0;
	foreach($code as $k => $v){
		if(isset($codes[ord($v)])){
			$code = ( $i ? "01" : null ) . $codes[ord($v)];
			if($code) {
				$bstr .= " {$v}";
				$w = 0;
				$f2 = $fl = null;
				for($j = 0; $j < strlen($code); $j++){
					$f2 .= (string)$code[$j];
					if(strlen($f2) == 2) {
						$fl = $f2 == "11" || $f2 == "10" ? "_000" : "_fff";
						$w = $f2 == "11" || $f2 == "00" ? 3 : 1;
						if($w && $fl) {
							$bars[] = [$pos, 5, $pos-1+$w, 80-5-1, $fl];
							$pos += $w;
						}
						$f2 = $fl = null;
						$w = 0;
					}
				}
			}
			$i++;
		} else {
			if($code[$k])
				unset($code[$k]);
		}
	}
	if(!count($bars)) return false;
	$bw = $b['width'] ?: $pos+5;
	if($b['width'] AND $pos > $b['width']) return false;
	$img = imagecreate($bw, 80);
	$_000 = imagecolorallocate($img, 0, 0, 0);
	$_fff = imagecolorallocate($img, 255, 255, 255);
	$_bg = imagecolorallocate($img, 255, 255, 255);
	imagefilledrectangle($img, 0, 0, $bw, 80, $_bg);
	for($i = 0; $i < count($bars); $i++) {
		imagefilledrectangle($img, $bars[$i][0], $bars[$i][1], $bars[$i][2], $bars[$i][3], ${$bars[$i][4]});
	}
	if($a){
		$bth = 10+5;
		imagefilledrectangle($img, 5, 80-5-$bth, $bw-5, 80-5, $_fff);
		$font_size = 3;
		$font_w = imagefontwidth($font_size);
		$font_h = imagefontheight($font_size);
		$txt_w = $font_w * strlen($bstr);
		$pos_center = ceil((($bw-5)-$txt_w)/2);
		$txt_color = imagecolorallocate($img, 0, 255, 255);
		imagestring($img, $font_size, $pos_center, 80-$bth-2, $bstr, imagecolorallocate($img, 0, 0, 0));
	}
	
	ob_start();
	$res = imagepng($img);
	$image_data = ob_get_contents();
	ob_end_clean();
	imagedestroy($img);
	return base64_encode($image_data);
}

// Is json
function is_json($a){
	json_decode($a);
	return (json_last_error() == JSON_ERROR_NONE);
}

function getCurl($url, $lang = false){
	$ch = curl_init($url);
	curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
		CURLOPT_HTTPHEADER => array("Accept-Language: ".($lang ? $lang : $_SERVER['HTTP_ACCEPT_LANGUAGE'])),
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_FRESH_CONNECT => true,
		CURLOPT_TIMEOUT => 120
	]);
	$html = curl_exec($ch);
	$res = curl_getinfo($ch);
	$res['content'] = $html;
	curl_close($ch);
	return $res;
}

// curl
function get_curl_page($url) {
	$ch = curl_init($url);
	curl_setopt_array($ch, [
		CURLOPT_CUSTOMREQUEST => 'GET', 
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => false,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER => true, 
		CURLOPT_CONNECTTIMEOUT => 120,   
		CURLOPT_TIMEOUT => 120,     
		CURLOPT_MAXREDIRS => 1,
	]);
	$cnt = curl_exec($ch);
	$err = curl_errno($ch);
	$errmsg = curl_error($ch);
	$res = curl_getinfo($ch);
	curl_close($ch);
	$res['errno']   = $err;
	$res['errmsg']  = $errmsg;
	$res['content'] = $cnt;
	return $res;
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
		$res = curl_exec($ch);
/* 			echo $res;
			echo $url;
			die; */
/* 		if($user['id'] == 31735){
			echo $res;
			die;
		} */
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

function get_token($a){
	return md5(md5($a).md5(SOLT, true));
}

function xls_header($a){
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/vnd.ms-excel; charset=windows-1251");
	header("Content-Disposition: attachment; filename=\"{$a}_".date("Y.m.d").".xls\"");
	header("Content-Transfer-Encoding: binary");
}

function set_log($a, $b){
	return file_put_contents(APP_DIR.'/logs/'.$a.'.log', $b."\n", FILE_APPEND | LOCK_EX);
}

// Min price formula
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

// Send sms
/* function send_sms($phone, $sms){
	$sms = strip_tags($sms);
	$phone = preg_replace("/\D/", '', $phone);
	if (strlen($phone) >= 10 AND $sms){
		$data = file_get_contents('https://api.smsglobal.com/http-api.php?action=sendsms&maxsplit=5000&user=vex8dzqs&password=Y4dC9MtL&from=15183302082&to='.$phone.'&api=1&text='.urlencode($sms));
	}
} */

// Get ava by email
function get_ava_by_email($a){
	switch(explode('@', $a)[1]){
		case 'gmail.com':
			if($body = @file_get_contents('http://picasaweb.google.com/data/entry/api/user/'.$a.'?alt=json')){
				$data = json_decode($body, true);
				$res['name'] = $data['entry']['gphoto$nickname']['$t'];
				$ava = str_replace('/s64-c', '', $data['entry']['gphoto$thumbnail']['$t']);
				$res['ava'] = md5_file($ava) !== '850f638bb9ceb952e4632649fa3f5a12' ? $ava : '';
			}
		break;
	}
	return $res ?? [];
}

function smtp_mail($to, $subject, $message, $headers = '')
{
    $recipients = explode(',', $to);
    $user = 'admin@yoursite.com';
    $pass = 'admin123';
    $smtp_host = 'localhost';
    $smtp_port = 465;
 
    if (!($socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 15)))
    {
      echo "Error connecting to '$smtp_host' ($errno) ($errstr)";
    }
 
    server_parse($socket, '220');
 
    fwrite($socket, 'EHLO '.$smtp_host."\r\n");
    server_parse($socket, '250');
 
    fwrite($socket, 'AUTH LOGIN'."\r\n");
    server_parse($socket, '334');
 
    fwrite($socket, base64_encode($user)."\r\n");
    server_parse($socket, '334');
 
    fwrite($socket, base64_encode($pass)."\r\n");
    server_parse($socket, '235');
 
    fwrite($socket, 'MAIL FROM: <'.$user.'>'."\r\n");
    server_parse($socket, '250');
 
    foreach ($recipients as $email)
    {
        fwrite($socket, 'RCPT TO: <'.$email.'>'."\r\n");
        server_parse($socket, '250');
    }
 
    fwrite($socket, 'DATA'."\r\n");
    server_parse($socket, '354');
 
    fwrite($socket, 'Subject: '
      .$subject."\r\n".'To: <'.implode('>, <', $recipients).'>'
      ."\r\n".$headers."\r\n\r\n".$message."\r\n");
 
    fwrite($socket, '.'."\r\n");
    server_parse($socket, '250');
 
    fwrite($socket, 'QUIT'."\r\n");
    fclose($socket);
 
    return true;
}

//Functin to Processes Server Response Codes
function server_parse($socket, $expected_response)
{
    $server_response = '';
    while (substr($server_response, 3, 1) != ' ')
    {
        if (!($server_response = fgets($socket, 256)))
        {
          echo 'Error while fetching server response codes.', __FILE__, __LINE__;
        }            
    }
 
    if (!(substr($server_response, 0, 3) == $expected_response))
    {
      echo 'Unable to send e-mail."'.$server_response.'"', __FILE__, __LINE__;
    }
}

define('API_ACCESS_KEY', 'AIzaSyCKDQtULzIqXaGQ83OK_zPaaK0WQVtgr8E');

//ob_start('output_browser');

function sendPush($id,$title,$message,$info){
	global $user;
	//return false;
	$data['data'] = [
		'message' => $message,
		'title' => $title,
		'info' => $info,
		'additionalData' => [
			'vibrate' => 1,
			'sound' => 1
		]
	];
	if ($staffs = db_multi_query('SELECT push_id FROM `'.DB_PREFIX.'_users` WHERE '.(
		$id ? 'id IN('.$id.')' : 'id != 0'
		//$id ? 'id IN('.$id.')' : 'id != '.($user['id'] ?: 0)
	).' AND push_id != \'\'', true)){
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

function sendPush2($id, $title, $message, $info, $sound = 'ntf'){
	$data['data'] = [
		'message' => $message,
		'title' => $title,
		'info' => $info,
		'soundname' => $sound,
		'additionalData' => [
			'vibrate' => 1,
			'sound' => 1,
			'soundname' => $sound
		]
	];
	if ($staffs = db_multi_query('SELECT push_id FROM `'.DB_PREFIX.'_access_tokens` WHERE '.(
		$id ? 'user_id IN('.$id.')' : 'user_id != 0'
	).' AND push_id != \'\'', true)){
		//echo 'OK';
		$data['registration_ids'] = [];
		foreach($staffs as $staff){
			$data['registration_ids'][] = $staff['push_id'];
		}
		$ch = curl_init();
		// https://fcm.googleapis.com/fcm/send
		// https://android.googleapis.com/gcm/send
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: key=AAAAtJOrPpc:APA91bEiW-_lstShSpbzFP8PLb7X3WN9CtUGFXqH0F-PoTwec_h0nKJbgUpd5b_84jVDvW8HwUZE6TZlBJAUzPtoNoWkQR1gnZA9luffXRvBR0azPs0cLxD13AE3qQEMqc7v7nwlkjGUzHHxe7hlmaDOAhMnGyXhbg',
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		curl_close($ch);	
	}// else
	//	die('err');
}

/* {
  "to":"FCM_TOKEN",
  "data": {
    "type":"MEASURE_CHANGE",
    "custom_notification": {
      "body": "test body",
      "title": "test title",
      "color":"#00ACD4",
      "priority":"high",
      "icon":"ic_notif",
      "group": "GROUP",
      "id": "id",
      "show_in_foreground": true
    }
  }
} */

function sPush($id, $title, $message, $info, $sound = 'ntf'){
	
	$data = [
		'registration_ids'  => [],
		'data' => [
			'title' => $title,
			'body'  => $message,
			//'android_channel_id' => 'my_default_channel',
			'type' => $info['type'] ?? 'all',
			'data' => $info
			//'collapse_key' => 'com.caapp'
		],
		'notification' => [
			'title' => $title,
			//'text' => $message,
			'body' => $message,
			'sound' => 'definite.mp3',
			//'android_channel_id' => 'my_default_channel',
			'high_priority' => 'high',
			'show_in_foreground' => true
		],
		'priority' => 'high'
	];
	
	if ($staffs = db_multi_query('SELECT push_id FROM `'.DB_PREFIX.'_access_tokens` WHERE '.(
		$id ? 'user_id IN('.$id.')' : 'user_id != 0'
	).' AND push_id != \'\'', true)){
		foreach($staffs as $staff){
			$data['registration_ids'][] = $staff['push_id'];
		}
	}
	 
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, [
		'Authorization: key=AIzaSyCR198yAH_dj9oec9uPeCY8i9nL7WiqPcM',
		'Content-Type: application/json'
	]);
	//echo '<pre>';
	//print_r($data);
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($data) );
	$result = curl_exec($ch );
	curl_close( $ch );
	//echo $result;
	//die;
}

function sendPush3($id, $title, $message, $info, $sound = 'ntf'){
	$data['data'] = [
		'message' => $message,
		'title' => $title,
		//'info' => $info,
		'custom_notification'=> [
            "body" => "test body",
            "title" => "test title",
            "color" => "#00ACD4",
            "priority" =>"high",
            "sound" => "default",
            "id" => date('s'),
            "show_in_foreground" => true
		]
		//'soundname' => $sound,
/* 		'additionalData' => [
			'vibrate' => 1,
			'sound' => 1,
			'soundname' => $sound
		] */
	];
	if ($staffs = db_multi_query('SELECT push_id FROM `'.DB_PREFIX.'_access_tokens` WHERE '.(
		$id ? 'user_id IN('.$id.')' : 'user_id != 0'
	).' AND push_id != \'\'', true)){
		$data['to'] = [];
		foreach($staffs as $staff){
			$data['to'] = $staff['push_id'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: key=AAAAu-uIM8A:APA91bHg3vkGdlFyZ_jlE7XHVn9i_xLzuKbxZ3fRbDvwPHpWUBqdLd3P6G-4Sz3WOG8FxqsoQn4NDcBb-pMzHtf-wIOCU88kH2hLZrFdBbN03xFFvOIROVcZ2ZmJSmnMqqlOGW0wOwC2s2Kyp4GoUhgDzXqeqswYYg',
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		echo $result;
		curl_close($ch);
		die;
	}
}

function phones_filter($a, $b){
	$c = [];
	foreach($a as $k => $v){
		if(preg_match('/\([0-9]{3}\)\s[0-9]{3}-[0-9]{4}(-[0-9]{4})?$/', $v)){
			if($b == $k)
				array_unshift($c, '+1'.$v);
			else
				$c[] = '+1'.$v;
		}
	}
	return implode(',', $c);
}

function getMainStoreName() {
	static $name = null;

	if ($name !== null) {
		return $name;
	}

	$row = db_multi_query('SELECT name FROM `'.DB_PREFIX.'_objects` WHERE id = 2');
	return $name = $row['name'];
}

function replacePageTags($content) {

	$content = preg_replace_callback('/\{([a-z_]+)(\=(.*?))?\}/is', function($matches) {
		if ($matches[1] === 'quote') {
			return '<button class="btn" onclick="quote.mdl();">'.$matches[3].'</button>';
		} else if ($matches[1] === 'call_req') {
			return '<button class="btn" onclick="quote.mdl(1);">'.$matches[3].'</button>';
		} else if ($matches[1] === 'store') {
			return '<span class="neare_store _need_load">'.getMainStoreName().'</span>';
		}	
		return $matches[0];
	}, $content);

	return $content;
}

function length($a, $b = null){
	return (int)mb_strlen(preg_replace("/(\r|\n|\s|\\\)/", '', mb_substr(strip_tags($a), 0, $b, 'utf-8')));
}

function convert_date($a,$b = false){
	global $config;
	return date($config['format_date'].($b ? ' H:i' : ''), strtotime($a));
}

function parse_ua(){
	include ROOT_DIR.'/app/classes/uap.php';
	$ua = Uap::get($_SERVER['HTTP_USER_AGENT']);
	$ua['ip'] = addslashes($_SERVER['REMOTE_ADDR']);
	return $ua;
}
?>