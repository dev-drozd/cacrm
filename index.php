<?php
/**
 * @appointment Home page
 * @copyright   Copyright Your Company 2018
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

if(strpos('www.', $_SERVER['HTTP_HOST']) !== false){
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: https://yoursite.com');
	exit;
}

if($_SERVER['REQUEST_URI'] == "/index.php") {
	header("Location: /", true, 301);
	exit;
}

ini_set('session.cookie_domain', 'yoursite.com');
ini_set('opcache.enable', 0);

ob_start(function($a){
	//log_sql_low();
	//return http2_static($a);
	return $a;
});

// Timer start
$start = microtime(true);

// Get memory
$memory = memory_get_peak_usage();


// Session start
session_start();

//$_REQUEST['display_errors'] = 1;

// Disable notice
if(isset($_REQUEST['display_errors'])){
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1);
} else {
	error_reporting(E_ERROR);
	ini_set('display_errors', 0);
}

//http_response_code(502);
//die;

// Set headers
header('Content-type: text/html; charset=utf-8');

// Set internal character encoding
mb_internal_encoding('UTF-8');

// Route
$route = isset($_GET['uri']) ? explode(
	'/', str_replace('\\', '',
		trim($_GET['uri'], '/')
	)
) : [];

$subdomain = preg_replace(
	'/(www)?.?yoursite.com/i', '', $_SERVER['HTTP_HOST']
);

$training_mode = false;
$dev_mode = false;
$vermont_mode = false;

if($subdomain == 'training'){
	$subdomain = 'admin';
	$training_mode = true;
}

if($subdomain == 'vermont'){
	$subdomain = 'admin';
	$vermont_mode = true;
}

if($subdomain == 'dev'){
	$subdomain = 'admin';
	$dev_mode = true;
}

if($subdomain == 'crm'){
	$subdomain = 'admin';
	exit('Site was deleted or not found');
}

if($subdomain == 'erp'){
	$subdomain = 'admin';
}

if(isset($_GET['ref']) AND $_GET['ref'] > 0){
	$_SESSION['ref'] = $_GET['ref'];
}

// Vars
$logged = false;
$user = [];
$meta = [];

// Constants
define('ENGINE', true);
define('SOLT', 'f5afgafw5f%F%F^Uqfsa');
define('SALT', 'f3f99974621464c6330fb525543694cb');
define('ROOT_DIR', __DIR__);
define('APP_DIR', ROOT_DIR.'/app');
define('CLIENT_IP', !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (
	!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
));

// Init application
include APP_DIR.'/init.php';

if(!is_ajax() && preg_replace('/(www)?.?yoursite.com/i', '', $_SERVER['HTTP_HOST']) == 'admin'){
	echo '<h1 align="center" style="margin: 5em;">The system is available at a new address: <a href="https://crm.yoursite.com/">crm.yoursite.com</a></h1><meta http-equiv="refresh" content="5;URL=https://crm.yoursite.com/" />';
	die;
}

//if(ADMIN_PANEL AND CLIENT_IP != '134.249.157.109'){
//	echo '<h1 align="center" style="background: #f2fdff;padding: 50px;margin-top: calc(50vh - 140px);border: 1px solid #d1f8ff;color: #333;">Technical work is taking place, please contact the administration for more information or try again later.</h1>';
//	die;
//}

$tpl_content['content'] .= db_debug();

if (ADMIN_PANEL AND $_SESSION['jlogin']) {
	$dep_purchases = [];
	if ($not_deposit = db_multi_query('SELECT DISTINCT
			p.id,
			p.customer_id,
			p.issue_id,
			iinv.conducted as iss_conducted,
			iinv.paid as iss_paid,
			iinv.id as iinv_id,
			inv.conducted as inv_conducted,
			inv.id as inv_id,
			inv.paid as inv_paid
		FROM `'.DB_PREFIX.'_purchases` p
		LEFT JOIN `'.DB_PREFIX.'_issues` iss
			ON p.issue_id = iss.id
		LEFT JOIN `'.DB_PREFIX.'_invoices` iinv 
			ON iinv.issue_id = iss.id
		LEFT JOIN `'.DB_PREFIX.'_invoices` inv
			ON (p.invoice_id = inv.id)
		WHERE p.del = 0 AND p.create_id = '.$user['id'].' AND p.customer_id > 0 AND ((!inv.id AND !iinv.id) OR (IF(inv.id, inv.paid < p.price, IF(iinv.id, inv.paid < p.price, 1))))
	', true)) {
		foreach($not_deposit as $k => $p) {
			$dep_purchases[$k] = $p;
		}
	}
	unset($_SESSION['jlogin']);
}

if (!ADMIN_PANEL) {
	/* if ($user['id'] == 17) {
		echo '<pre>';
		print_r($_SERVER);
		die;
	} */
	$h = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_visitors` WHERE ip = \''.CLIENT_IP.'\' AND DATE(date) = \''.date('Y-m-d', time()).'\'');
	db_query('INSERT INTO `'.DB_PREFIX.'_visitors` SET
		ip = \''.CLIENT_IP.'\',
		user_agent = \''.$_SERVER['HTTP_USER_AGENT'].'\',
		user_lang = \''.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'\',
		type = \''.($h ? '0' : '1').'\'
	');
	if (!$h) {
		send_push(0, [
			'type' => 'new_visitor'
		]);
	}
}

$quotes = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_quote_requests` WHERE sent_id = 0');
$bugs = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_bugs` WHERE user_id = '.intval($user['id']).' AND notify = 1');
//$bugs = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_bugs` WHERE status = \'opened\'');

$cpu = (memory_get_peak_usage()-$memory)/(1024*1024);
$total_cpu = $cpu*25000;

// Output
if(is_ajax()){
	header('Content-Type: text/x-json');
	$output = json_encode([
		'title' => $meta['title'] ?? $config['title']['en'],
		'points' => $user['points'],
		'quotes_count' => $quotes['count'] ?: '',
		'bugs_count' => $bugs['count'] ?: '',
		'notify_count' => $notify['count'] ?: '',
		'new_msg' => $user['new_msg'],
		'description' => $meta['description'] ?? $config['description']['en'],
		'keywords' => $meta['keywords'] ?? $config['keywords']['en'],
		'add-tags' => $meta['add-tags'] ?: '',
		'cpu' => round($cpu, 2),
		'cpu_total' => round($total_cpu, 2),
		'time_script' => sprintf('%f', microtime(true)-$start),
		'compile_tpl' => $tpl_count,
		'query_cache' => $cache_count,
		'query_db' => $db_count,
		'content' => $tpl_content['content'],
		'purchases' => (ADMIN_PANEL AND $not_deposit) ? $dep_purchases : []
	], JSON_UNESCAPED_UNICODE);
	header('Content-Length: '.strlen($output));
	print($output);
	
} else {
	$groups = '';
	if($logged){
		$privileges = in_to_array($user['group_ids'], [1]) ? false : ids_filter(implode(',', $user['add_users']));
		foreach(db_multi_query('SELECT `group_id`, `name` FROM `'.DB_PREFIX.'_groups`'.(
			$privileges ? ' WHERE group_id IN('.$privileges.')' : ''
		).' ORDER BY `group_id`', true) as $group){
			$groups .= '<li><a href="/users/'.$group['group_id'].'" onclick="Page.get(this.href);return false;">'.$group['name'].'</a></li>';
		}	
	}
	$items = '';
	$subtotal = 0;
	if ($subdomain != 'admin') {
		if ($_SESSION['cart']['count'] > 0) {
			$count = $_SESSION['cart']['count'] || 0;
			foreach (db_multi_query('
				SELECT 
					i.id, 
					i.name, 
					i.model,
					i.price,
					i.category_id,
					c.name as category_name 
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c 
					ON c.id = i.category_id
				WHERE i.id IN('.substr($_SESSION['cart']['items'], 0, -1).')
			', true) as $item) {
				$items .= '<li id="miniCart_'.$item['id'].'"><a href="/item/'.$item['id'].'" onclick="Page.get(this.href); return false;">'.(
					$item['name'] ? $item['name'] : $item['category_name'].' '.$item['model']
				).'</a><span class="cartPrice">$'.$item['price'].'</span></li>';
				$subtotal += $item['price'];
			}
		}
		$cids = [];

		db_multi_query('SELECT i.store_category_id as id, c.parent_id FROM `'.DB_PREFIX.'_inventory` i INNER JOIN `'.DB_PREFIX.'_store_categories` c ON i.store_category_id = c.id WHERE i.store_category_id > 0 AND i.type = \'stock\' AND i.del = 0 GROUP BY i.store_category_id', true, false, function($a) use(&$cids){
			$cids[] = $a['id'];
			if($a['parent_id'])
				$cids[] = $a['parent_id'];
			return [0,0];
		});
		$nav = db_multi_query('SELECT n.*, IF(act_type = \'blog\', IF(b.pathname = \'\', CONCAT(\'blog/\', b.id), b.pathname), IF(act_type = \'page\', IF(p.pathname = \'\', CONCAT(\'page/\', p.id), p.pathname), n.url)) as pathname FROM `'.DB_PREFIX.'_navigation` n LEFT JOIN `'.DB_PREFIX.'_pages` p ON n.nav_id = p.id LEFT JOIN `'.DB_PREFIX.'_store_blog` b ON n.nav_id = b.id ORDER BY n.parent_id, n.sort', true);
		$menus = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_categories` WHERE id IN('.implode(',', $cids).') ORDER BY `parent_id`, `sort`', true);
		$pages = db_multi_query('SELECT id, pathname, IF(site_name != \'\', site_name, name) as name, parent_id FROM `'.DB_PREFIX.'_pages` WHERE main = 1 AND confirm > 0 ORDER BY `parent_id`, `sort`', true);
		$pages_list = showMenu(0, $pages, '', '');
		$list = showMenu(0, $menus, '/category', '');
		
		$menu2 = '<ul><li><span class="fa fa-times" onclick="$(\'.hdr-nav > ul\').slideUp();"></span></li>'.genNav(0, $nav).'</ul>';
		
		$menu = '<ul><li><span class="fa fa-times" onclick="$(\'.hdr-nav > ul\').slideUp();"></span></li>
			<li class="dd"><a href="/category" onclick="Page.get(this.href); return false;">Shop</a><ul>'.$list.'</ul></li>'.$pages_list.'
			<li>
				<a href="/blog" onclick="Page.get(this.href); return false;">Blog</a>	
			</li>
			<li>
				<a href="/services" onclick="Page.get(this.href); return false;">Services</a>	
			</li>
		</ul>';
		
		$obj_loc = '';
		if ($objs = db_multi_query('SELECT id, map, phone, address, name, google_map_id FROM '.DB_PREFIX.'_objects WHERE close = 0', true)) {
			$obj_loc = '';
			foreach($objs as $o) {
				$c = explode(',', $o['map']);
				if(!$c[0] or !$c[1]) continue;
				/*$obj_loc[] = array(
					'lat' => $c[0],
					'lng' => $c[1],
					'phone' => $o['phone'],
					'address' => $o['address'],
					'name' => $o['address']
				);*/
				$obj_loc .= ($obj_loc ? ',' : '').'{id: '.$o['id'].',lat: '.$c[0].',lng: '.$c[1].',phone: "'.$o['phone'].'",address:"'.$o['address'].'",name:"'.$o['name'].'",map_id:"'.$o['google_map_id'].'"}';
			}
			//$obj_loc = json_encode($obj_loc);
		}
	} else {
		
	}
	
	$objects_ip = array_flip($config['object_ips']);
	if(ADMIN_PANEL && $logged){
		$ims = '';
		if($g = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_im` WHERE (from_uid = '.$user['id'].' OR FIND_IN_SET('.$user['id'].', for_uids)) AND name != \'\' ORDER BY `id` DESC LIMIT 0, 20', true)){
			foreach($g as $row){
				$ims .= '<li class="mini-chat-person" data-id="G-'.$row['id'].'" onclick="Chat.open(this);">
					<span class="fa fa-users miniRound"></span>
					 '.$row['name'].'
				</li>';
			}
		}
		foreach(db_multi_query('
			SELECT u.id, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_im` i 
				INNER JOIN `'.DB_PREFIX.'_users` u 
				ON u.id = IF(for_uid = '.$user['id'].', from_uid, for_uid)
			WHERE (for_uid = '.$user['id'].' OR from_uid = '.$user['id'].') ORDER BY i.date DESC LIMIT 0, 20',
		true) as $itm){
			$ims .= '<li class="mini-chat-person" data-id="'.$itm['id'].'" onclick="Chat.open(this);">
				'.($itm['image'] ? '<img src="/uploads/images/users/'.$itm['id'].'/thumb_'.$itm['image'].'" class="imImg">' : '<span class="fa fa-user-secret miniRound"></span>').'
				 '.$itm['name'].' '.$itm['lastname'].'
			</li>';
		}
	}
	
	
	if(isset($_GET['tauth'])){
		echo '<pre>';
		print_r($user);
		die;
	}
	
	if(ADMIN_PANEL && $user['id'] > 0){
		$tabs = '';
		foreach(db_multi_query(
			'SELECT * FROM `'.DB_PREFIX.'_tabs` WHERE user_id = '.$user['id'].' ORDER BY id ASC', 1
		) as $tab){
			$tabs .= '<a href="'.$tab['link'].'" onclick="Page.get(this.href); return false;">'.$tab['title'].'<i class="fa fa-times" onclick="tab.remove(this)"></i></a>';
		}
		unset($tab);
	}
	
	$google_url = 'https://accounts.google.com/o/oauth2/auth';

	$google_params = [
		'redirect_uri'  => 'https://crm.yoursite.com?login=true',
		'response_type' => 'code',
		'client_id'     => '1040991714737-pgb2p5765fo2ahk34ui9jrog33cogetl.apps.googleusercontent.com',
		'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
	];
					
	echo tpl_set(ADMIN_PANEL ? (!$logged ? 'login' : 'index') : (
		($config['offline'] AND $user['group_id'] != 1) ? 'offline' : 'index'
	), [
		'uid' => $user['id'],
		'ims' => $ims,
		'google-lnk' => $google_url.'?'.urldecode(http_build_query($google_params)),
		'tabs' => $tabs,
		'new-quotes' => $quotes['count'] ? '<span class="newMsg">'.$quotes['count'].'</span>' : '',
		'new-bugs' => $bugs['count'] ? '<span class="newMsg">'.$bugs['count'].'</span>' : '',
		'or:image' =>  $meta['image'] ?? '/img/logo.png',
		'guest-id' => $_SESSION['guest_uniqid'],
		'group-ids' => $user['group_ids'],
		'notify-count' => $notify['count'] ?: '',
		'price-formula' => json_encode($config['price_formula']),
		'currencies' => json_encode($config['currency']),
		'quick-sell' => $config['quick_sell'],
		'user-token' =>get_token($user['id']),
		'long-polling' => json_encode([
			'confirm_purchase' => get_token($user['confirm_purchase']),
			'chat_support' => get_token('chat_support'.$user['chat_support']),
			'all' => get_token(1)
		], JSON_UNESCAPED_UNICODE),
		'uava' => $user['image'],
		'points' => $user['points'],
		'uname' => $user['uname'],
		'timer' => $user['timer_id'] ? (in_array($user['event'], ['pause', 'stop']) ? intval($user['seconds']) : (time() - strtotime($user['control_point']) + intval($user['seconds']))) : 0,
		'timer-id' => $user['timer_id'],
		'ulastname' => $user['ulastname'],
		'menu' => $menu,
		'menu2' => $menu2,
		'lang' => $lang_code,
		'new-msg' => $user['new_msg'] ? '<span class="newMsg">'.$user['new_msg'].'</span>' : '',
		'groups' => $groups,
		'this-year' => date('Y'),
		'title' => $meta['title'] ?? $config['title']['en'],
		'description' => str_ireplace('"', '″', $meta['description'] ?? $config['description']['en']),
		'keywords' => $meta['keywords'] ?? $config['keywords']['en'],
		'canonical' => /* $meta['canonical'] ? "\n\t<link rel=\"canonical\" href=\"https://yoursite.com/".$meta['canonical']."\">\n\t" : '' */'',
		'add-tags' => $meta['add-tags'],
		'db-queries' => $db_queries,
		'cart-count' => $_SESSION['cart']['count'] ?? 0,
		'cart-items' => $items ?? '',
		'cart-subtotal' => $subtotal,
		'cpu' => round($cpu, 2),
		'cpu-total' => round($total_cpu, 2),
		'time-script' => sprintf('%f', microtime(true)-$start),
		'compile-tpl' => $tpl_count,
		'query-cache' => $cache_count,
		'query-db' => $db_count,
		'content' => $tpl_content['content'],
		'e-order' => $counters['e_order'],
		'store-name' => $user['object_name'],
		'obj-loc' => $obj_loc,
		'year' => date('Y'),
		'query' => htmlspecialchars($_GET['query']),
		'purchases' => (ADMIN_PANEL AND $not_deposit) ? json_encode($dep_purchases) : []
	], [
		'settings' => $user['settings'],
		'keywords' => $meta['keywords'],
		'description' => $meta['description'],
		'quotes' => $quotes['count'] > 0,
		'login' => $user,
		'from-login' => isset($_GET['from_login']),
		'logout-user' => ($_SESSION['uid'][2] ?? $_COOKIE['ohid']),
		'gpst' => strpos($_SERVER['HTTP_USER_AGENT'], "Google Page Speed" ) !== false OR isset($_GET['test']),
		'training' => $training_mode,
		'notify' => $notify['count'],
		'nocart' => !$_SESSION['cart']['count'],
		'ava' => $user['image'],
		'timer' => $user['event'] == 'start',
		'print' => $route[2] != 'print',
		'owner' => strrpos($user['group_ids'], '1') !== false,
		'timer-enable' => $user['timer'] == 1,
		'object' => $user['edit_object'],
		'show' => (in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids'])) OR $objects_ip[$_SERVER['REMOTE_ADDR']] != 0 OR !$user['stores_check_ip']),
		'users' => $user['users'],
		'im' => $user['im'],
		'store' => $user['store'],
		'auth-store' => $user['object_name'],
		'service' => $user['service'],
		'purchase' => $user['purchase'],
		'commerce' => $user['commerce'],
		'invoces' => $user['invoces'],
		'issues' => $user['issues_show_all'],
		'cash' => $user['cash'],
		'organizer' => $user['organizer'],
		'salary' => $user['salary'],
		'feedback' => $user['feedback'],
		'analytics' => $user['analytics'],
		'camera' => $user['camera'],
		'e-order' => $counters['e_order']
	]);	
}

function genNav($id, $menu) {
	$html = (!$id ? '' : '<ul>');
	foreach(array_keys(array_column($menu, 'parent_id'), $id) as $key) {
		$href = filter_var($menu[$key]['pathname'], FILTER_VALIDATE_URL) ? $menu[$key]['pathname'] : (
			$menu[$key]['pathname'] ? '/'.$menu[$key]['pathname'] : '#'
		);
		$attr = filter_var($menu[$key]['pathname'], FILTER_VALIDATE_URL) ? ' target="_blank"' : (
			$menu[$key]['pathname'] ? ' onclick="Page.get(this.href); return false;"' : ''
		);
		if (count(array_keys(array_column($menu, 'parent_id'), $menu[$key]['id'])))
			$html .= '<li class="dd"><a href="'.($href != '//' ? $href : '#').'"'.$attr.'>'.$menu[$key]['name'].'</a>'.genNav($menu[$key]['id'], $menu).'</li>';
		else 
			$html .= '<li><a href="'.$href.'"'.$attr.'>'.$menu[$key]['name'].'</a></li>';
	}
	return $html.(!$id ? '' : '</ul>');
}

function showMenu($id, $menu, $t = '', $p = '') {
	$html = (!$id ? '' : '<ul>');
	foreach(array_keys(array_column($menu, 'parent_id'), $id) as $key) {
		 if (count(array_keys(array_column($menu, 'parent_id'), $menu[$key]['id']))){
			 $href = $t.'/'.($menu[$key]['pathname'] ?: $menu[$key]['id']);
		 	$html .= '<li class="dd"><a href="'.($href != '//' ? $href : '#').'" onclick="Page.get(this.href);return false;">'.$menu[$key]['name'].'</a>'.showMenu($menu[$key]['id'], $menu, $t, $p).'</li>';
		} else 
			$html .= '<li><a href="'.$t.'/'.($menu[$key]['pathname'] ?: $menu[$key]['id']).'" onclick="Page.get(this.href);return false;">'.$menu[$key]['name'].'</a></li>';
	}
	return $html.(!$id ? '' : '</ul>');
}
