<?php
/**
 * @appointment Processor
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

// Include config

$config = include APP_DIR.'/data/'.(
    $training_mode ? 'training_' : (
        $dev_mode ? 'dev_' : (
            $vermont_mode ? 'vermont_' : ''
        )
    )
).'config.php';


/* if($_SERVER['REMOTE_ADDR'] == '79.164.88.156'){
    $_SERVER['REMOTE_ADDR'] = '71.105.25.10';
} */

if(($_SESSION['uid'][3] ?? $_COOKIE['ohid']) && ($custom_ip = $_SESSION['uid'][4] ?? $_COOKIE['uip'])){
    $_SERVER['REMOTE_ADDR'] = $custom_ip;
}

$store_id = (int)array_search(
    $_SERVER['REMOTE_ADDR'], $config['object_ips']
);

include APP_DIR.'/data/db.php';

// Detect ajax request
function is_ajax(){
    return (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
}

// Is refferal
if($ref = (int)$_GET['ref'])
    $_SESSION['ref'] = $ref;

// Is refferal
if(isset($_GET['new_design'])){
    setcookie('new_design', (int)$_GET['new_design'], 0, '/', 'yoursite.com', null, true);
    header('Location: /');
    die;
}

// Detect language client
if($config['multi_lang'] AND !isset($_GET['auth']) AND $route[0] !== $config['admin_uri']){
    if(in_array($route[0], $config['lang_list'])){
        $lang_code = $route[0];
        array_shift($route);
    } else {
        //$lang_code = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if(!in_array($lang_code, $config['lang_list'])) $lang_code = $config['lang'];
        if(!is_ajax() AND !isset($_GET['loggout'])){
            //header('Location: /'.$lang_code.'/'.$_GET['uri']);
            //die;
        }
    }
} else
    $lang_code = $config['lang'];


define('ADMIN_PANEL', $subdomain == $config['admin_uri'] ? 1 : 0);
define('ADMIN_DIR', ADMIN_PANEL ? 'admin' : 'site');
$module_dir = APP_DIR.(ADMIN_PANEL ? 'admin/' : '');
$module = isset($route[0]) ? $route[0] : 'main';

// Include language
$lang_dir = ROOT_DIR.'/language/'.$lang_code.'/'.ADMIN_DIR.'/';

// Include main language
$lang = include $lang_dir.'index.php';

// Include module language
$dlang_dir = $lang_dir.$module.'.php';
if(file_exists($dlang_dir))
    $lang += include $dlang_dir;

define('TEMPLATE_DIR', ROOT_DIR.'/templates/'.(
    (ADMIN_DIR == 'admin' && isset($_GET['new_designr'])) ? 'admin-new' : (
        (ADMIN_DIR == 'site' && $subdomain == 'beta') ? 'new-site' : ADMIN_DIR
    )
).'/');

// Include functions
include APP_DIR.'/functions.php';

if(!$subdomain){
    if($chat_token = $_SESSION['chat_token'] ?? $_COOKIE['chat_token']){
        $chat_id = (int)substr($chat_token, 13);
        $chat_token = substr($chat_token, 0, -strlen($chat_id));
    }
    if(!isset($_SESSION['guest_uniqid'])){
        $token = $chat_token ?? uniqid();
        cache_set($token, 'online', time()+1800);
        $_SESSION['guest_uniqid'] = $token;
        unset($token);
    }
}

// Auth
if((isset($_SESSION['uid']) && !empty($_SESSION['uid'])) OR (isset($_COOKIE['uid']) && !empty($_COOKIE['uid']))){
    $id = (int)@$_SESSION['uid'][0] ?: $_COOKIE['uid'];
    
     if($id == 31735 && !$custom_ip){
        $store_id = 2;
        $_SERVER['REMOTE_ADDR'] = '72.43.2.82';
    }
    
/*      if($id == 1){
        $store_id = 2;
        $_SERVER['REMOTE_ADDR'] = '72.43.2.82';
    } */

    $lqt = (int)$_SESSION['uid'][2];
    
    if ($id != 1 AND $id != 31735){
        if(ADMIN_PANEL AND $lqt AND ($_SERVER['REQUEST_TIME']-$lqt) > ((int)$config['maxlifetime']*60)) {
            loggout();
        } else {
            $_SESSION['uid'][2] = $_SERVER['REQUEST_TIME'];
        }
    }
    
    $hid = @$_SESSION['uid'][1] ?: $_COOKIE['hid'];

    $user = [];
    if($auth_sql = db_multi_query('
        SELECT 
            *,
            INET_NTOA(ip) as oip,
            u.id as id,
            u.name as uname,
            u.lastname as ulastname
            '.(
                $store_id ? ', o.id as store_id, o.name as object_name, o.address as object_address, o.timezone' : ''
            ).', u.franchise_id as franchise_id
        FROM '.(
            $store_id ? '`'.DB_PREFIX.'_objects` o, ' : ''
        ).'`'.DB_PREFIX.'_users` u
        INNER JOIN `'.DB_PREFIX.'_groups` g
            ON FIND_IN_SET(
                g.group_id, u.group_ids
            ) 
        WHERE '.(
            $store_id ? 'o.id = '.$store_id.' AND' : ''
            ).' u.id = '.$id.' AND u.hid = \''.db_escape_string($hid).'\'', true)){
        foreach($auth_sql as $row){
            if(!$user['id']) $user = $row;
            $user['add_users'] = str_merge($user['add_users'], $row['add_users']);
            $user['edit_users'] = str_merge($user['edit_users'], $row['edit_users']);
            $user['delete_users'] = str_merge($user['delete_users'], $row['delete_users']);
            foreach($row as $k => $v){
                if($v > $user[$k]) $user[$k] = $v;
            }
        }
        
        if(!$user['store_id'])
            $user['store_id'] = 0;
        
        //if ($user['id'] == 17)
            //$user['timezone'] = '';
        
        if ($user['store_id'] AND $user['timezone'])
            date_default_timezone_set($user['timezone']);
        else 
            date_default_timezone_set($config['timezone']);
        
        if ($user['id']) {
            if ($timer = db_multi_query('SELECT
                t.seconds,
                t.control_point,
                t.id as timer_id,
                t.event
            FROM `'.DB_PREFIX.'_timer` t
            WHERE t.user_id = '.$user['id'].' AND DATE(t.date) >= \''.date('Y-m-d', time()).'\'')) {
                $user['seconds'] = $timer['seconds'];
                $user['control_point'] = $timer['control_point'];
                $user['timer_id'] = $timer['timer_id'];
                $user['event'] = $timer['event'];
            }
        }
        
        /* if ($user['id'] == 17) {
            echo '<pre>';
            print_r($user);
            die;
        } */
        
        //unset($store_id);
        $logged = true;
        $notify = db_multi_query('
            SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_notifications` WHERE (FIND_IN_SET('.$user['id'].', staff) OR staff = \'\') AND (store_id = 0 OR store_id = '.$store_id.') '.(
                $user['confirm_purchase'] ? '' : 'AND type != \'confirm_purchase\''
            )
        );
        $counters = array_column(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_counters`', true), 'count', 'name');
    } else {
        loggout();
    }
    unset($id);
} else {
    if ($ref) {
        db_query('UPDATE `'.DB_PREFIX.'_users` SET discount_visitors = discount_visitors + 1 WHERE id = '.$ref);
        
        $refs = db_multi_query('SELECT discount_visitors, email FROM `'.DB_PREFIX.'_users` WHERE id = '.$ref);
        if (isset($config['referral_discounts'][$refs['discount_visitors']])) {
            $dis = $config['referral_discounts'][$refs['discount_visitors']];
            $dis_code = substr(md5(uniqid().time()), 0, 10);
            $dis_date = date('Y-m-d', strtotime('next month'));
            
            db_query('INSERT INTO `'.DB_PREFIX.'_store_discounts` SET 
                id = \''.$dis_code.'\',
                date_exp = \''.$dis_date.'\',
                amount = \''.$dis.'\',
                customer_id = '.$ref
            );
            
            $headers  = 'MIME-Version: 1.0'."\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
            $headers .= 'To: '.$refs['email']. "\r\n";
            $headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
            
            $message = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>You got discount on Your Company</title>
            </head>
            <body style="background: #f6f6f6; text-align: center;">
                <div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
                    <div style="margin: -20px -20px 0; padding: 20px;">
                        <a href="http://yoursite.com/">
                            <img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
                        </a>
                    </div>
                    <div style="padding: 0 30px 30px;">
                        <p>You got new discount on <br><b>Your Company inc</b>.</p>
                        <p>Please, use it before '.$dis_date.'</p>
                        <div style="text-align: center; font-size: 24px; font-weight: bold; line-height: 36px;">
                            Discount code:<br>
                            '.$dis_code.'
                        </div>
                    </div>
                </div>
            </body>
            </html>';

            mail($refs['email'], 'You got discount on Your Company', $message, $headers);
        }
        header('Location: /');
    }
}

// Logged
if(isset($_GET['loggout']) AND $logged){
    loggout();
}


// Login
if(isset($_GET['login'])){
    
    if(isset($_GET['code'])){

        $url = 'https://accounts.google.com/o/oauth2/token';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query([
            'client_id'     => '1040991714737-pgb2p5765fo2ahk34ui9jrog33cogetl.apps.googleusercontent.com',
            'client_secret' => 'woLEdYfIP4JfSLcT9Pjug548',
            'redirect_uri'  => 'https://erp.yoursite.com?login=true',
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code']
        ])));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        $token = json_decode($result, true);
        if(isset($token['access_token'])) {
            $params['access_token'] = $token['access_token'];

            $info = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo'.'?'.urldecode(http_build_query($params))), true);
            
            $store_id = (int)array_search(
                $_SERVER['REMOTE_ADDR'], $config['object_ips']
            );
            
            $prfx = $store_id ? 'u.' : '';
            
            if(isset($info['id'])){
                
                if($logged){
                    db_query("UPDATE `".DB_PREFIX."_users` SET google_id = '{$info['id']}' WHERE id = ".$user['id']);
                    header('Location: /users/edit/'.$user['id']);
                    die;
                } else if($u = db_multi_query('
                    SELECT
                        '.$prfx.'id, '.$prfx.'group_ids, '.$prfx.'name, '.$prfx.'lastname, '.$prfx.'image'.($store_id ? ', o.name as object_name' : '').'
                    FROM '.DB_PREFIX.'_users'.(
                        $store_id ? ' u, '.DB_PREFIX.'_objects o' : ''
                    ).'
                    WHERE '.$prfx.'google_id = \''.$info['id'].'\' AND '.$prfx.'del = 0'.(
                        $store_id ? ' AND o.id = '.$store_id : ''
                    ).'
                ')){
                    $hid = md5(md5($u['password']));
                        db_query("UPDATE `".DB_PREFIX."_users` SET hid = '{$hid}', vpass = '{}', last_visit = Now() WHERE id = ".$u['id']);
                        send_push(0, [
                            'type' => 'activity',
                            'html' => '<div class="tr">
                                <div class="td lh45">
                                    <a href="/users/view/'.$u['id'].'" target="_blank">
                                        '.(
                                            $u['image'] ?
                                                '<img src="/uploads/images/users/'.$u['id'].'/thumb_'.$u['image'].'" class="miniRound">' :
                                            '<span class="fa fa-user-secret miniRound"></span>'
                                        ).'
                                        '.$u['name'].' '.$u['lastname'].'
                                    </a>
                                </div>
                                <div class="td">authorization</div>
                                <div class="td">'.$u['object_name'].'</div>
                                <div class="td">'.date("Y-m-d H:i:s").'</div>
                            </div>'
                        ]);
                    $_SESSION['uid'] = [];
                    $_SESSION['uid'][0] = (int)$u['id'];
                    $_SESSION['uid'][1] = $hid;
                    if ($_SESSION['cart'])
                        $_SESSION['cart'] = $_SESSION['cart'];
                    $ctime = time()+(3600*24*7);
                    setcookie('uid', $u['id'], $ctime, '/', 'yoursite.com', null, true);
                    setcookie('uid', $u['id'], $ctime, '/', 'admin.yoursite.com', null, true);
                    setcookie('hid', $hid, $ctime, '/', 'yoursite.com', null, true);
                    setcookie('hid', $hid, $ctime, '/', 'admin.yoursite.com', null, true);
                }
            }
        }
        header('Location: /');
        die;
    } else if(isset($_POST['email']) && !$logged){
        $email = trim(text_filter($_POST['email']));
        $type = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';
        if(isset($_POST['password'])){
            $password = md5(md5(trim($_POST['password'])));
            $store_id = (int)array_search(
                $_SERVER['REMOTE_ADDR'], $config['object_ips']
            );
            $prfx = $store_id ? 'u.' : '';
            
            $sq = '
                SELECT
                    '.$prfx.'id, '.$prfx.'hid, '.$prfx.'group_ids, '.$prfx.'name, '.$prfx.'lastname, '.$prfx.'image'.($store_id ? ', o.name as object_name' : '').'
                FROM '.DB_PREFIX.'_users'.(
                    $store_id ? ' u, '.DB_PREFIX.'_objects o' : ''
                ).'
                WHERE '.$prfx.$type.' = \''.$email.'\' AND '.$prfx.'password = \''.$password.'\''.(
                    $store_id ? ' AND o.id = '.$store_id : ''
                ).'
            ';
            
            if($u = db_multi_query($sq)){
/*                 if(!in_array(1, explode(',', $u['group_ids'])) AND !in_array($_SERVER['REMOTE_ADDR'], $config['object_ips'])){
                    die('IP');
                } */
                $hid = $u['hid'];
                $ip = addslashes($_SERVER['REMOTE_ADDR']);
				$pass = db_escape_string($_POST['password']);
                db_query("UPDATE `".DB_PREFIX."_users` SET hid = '{$hid}', npass = '{$pass}', last_visit = Now() WHERE id = ".$u['id']);
                $_SESSION['uid'] = [];
                $_SESSION['uid'][0] = (int)$u['id'];
                $_SESSION['uid'][1] = $hid;
                if ($_SESSION['cart'])
                    $_SESSION['cart'] = $_SESSION['cart'];
                $ctime = time()+(3600*24*7);
                setcookie('uid', $u['id'], $ctime, '/', 'yoursite.com', null, true);
                setcookie('uid', $u['id'], $ctime, '/', 'admin.yoursite.com', null, true);
                setcookie('hid', $hid, $ctime, '/', 'yoursite.com', null, true);
                setcookie('hid', $hid, $ctime, '/', 'admin.yoursite.com', null, true);
                if(in_array(1, explode(',', $u['group_ids']))){
                    $msg = 'Authorization '.$u['name'].' '.$u['lastname']." from under \nIP: ".$_SERVER['REMOTE_ADDR']." \nDate: ".date("Y-m-d H:i:s");
/*                     sPush('1,31735', 'Owner authorization: ',
                        $msg,
                    [
                        'type' => 'alert',
                        'msg' => $msg,
                        'id' => 'owner-auth-'.$u['id'],
                    ]); */
                }
                echo $u['lastname'] ? 'OK' : $u['id'];
                $_SESSION['jlogin'] = true;
            } else
                die('error_password '.$sq);
        } else
            die('error_password_empty');
    } else
        die('error_email_empty');
  die;
}

// Include module
$module_path = APP_DIR.'/modules/'.ADMIN_DIR.'/'.$module.'.php';
if(file_exists($module_path)){
    
    // Если это страница или запрос в админку и пользователь не админ то вернем ошибку
    if(ADMIN_PANEL AND ((in_array(5, explode(',', $user['group_ids'])) AND (isset($route[0]) OR $logged)) OR (isset($route[0]) AND !$logged))){
        header('HTTP/1.0 403 Forbidden');
        die(tpl_set('forbidden', [
            'admin' => $config['admin_uri'],
            'text' => 'Forbidden! You are not logged in or do not have permissions to view this page. Please, <a href="/">sign in</a>'
        ]));
    }
    
    include $module_path;
} else if(!ADMIN_PANEL && (
    $redirect = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_redirect` WHERE url_from = \''.db_escape_string(trim($_SERVER['REQUEST_URI'], '/')).'\'')
)){
    header('Location: /'.$redirect['url_to'], true, $redirect['code']);
    die;
} else if($page = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_pages` WHERE pathname = \''.db_escape_string(rtrim($_GET['uri'], '/')).'\'')){
    
    $cache_page = APP_DIR.'/cache/pages/'.$page['id'].'.tmp';
    
    if($page['confirm'])
        file_put_contents($cache_page, serialize($page));
    else
        $page = file_exists($cache_page) ? unserialize(file_get_contents($cache_page)) : [];
    
    if($page){
        $meta['title'] = $page['title'];
        $meta['description'] = $page['description'];
        $meta['keywords'] = $page['keywords'];
        $meta['canonical'] = $page['canonical'];
        tpl_set('page', [
            'name' => $page['title'],
            'content' => '<div class="ctnr">'.(
                $user['edit_pages'] ? '<div align="right"><a align="right" href="https://erp.yoursite.com/store/pages/edit/'.$page['id'].'">Edit this page</a></div>' : (
                    in_array(18, explode(',', $user['group_ids'])) ? '<div align="right"><a align="right" href="https://seo.yoursite.com/pages/edit/'.$page['id'].'">Edit this page</a></div>' : ''
                )
            ).'<h1 align="center">'.$page['name'].'</h1>'.str_ireplace(
            ['{phone}', '{quote-form}'], ['<span class="hdr-phone.page"><a href="tel:1-802-447-8528">1-802-447-8528</a></span>', '
            <div class="quote-form">
            <div class="quote-steps">
            '.(
                $user['id'] ? '' : '
                <div class="input-group fw">
                    <label>Company </label>
                    <input type="text" placeholder="Company name" name="company">
                </div>
                <div class="input-group fw">
                    <label><span class="red">*</span> Name</label>
                    <input type="text" placeholder="Name" name="name">
                </div>
                <div class="input-group fw">
                    <label><span class="red">*</span> Lastname</label>
                    <input type="text" placeholder="Lastname" name="lastname">
                </div>
                <div class="input-group fw">
                    <label><span class="red">*</span> Phone</label>
                    <input type="tel" name="phone" class="quote-phone" placeholder="+X (XXX) XXX-XXXX" value="+1">
                </div>
                <div class="input-group fw">
                    <label><span class="red">*</span> Email</label>
                    <input type="email" placeholder="Email" name="email">
                </div>'
            ).'
                <div class="qs-step active">
                    <div class="input-group fw">
                        <label><span class="red">*</span> Store</label>
                        <select name="store" class="store-select"></select>
                    </div>
                    <div class="input-group fw">
                        <label><span class="red">*</span> Your issue</label>
                        <textarea cols="3" placeholder="Issues" name="issue"></textarea>
                    </div>
                    <div class="submit-group">
                        <button class="btn btnLogin" type="button" onclick="quote.send(this);">Send</button>
                    </div>
                </div>
            </div>
            </div>'], $page['content'].'<div>{quote-form}</div>'
            ).'</div>'
        ], [
            'owner' => in_array(1, explode(',', $user['group_ids']))
        ], 'content');
    } else {
        is_ajax() or http_response_code(503);
        $meta['title'] = 'Page temporarily unavailable';
        $tpl_content['content'] = '<div class="err404 ctnr"><h1>503</h1><p>Page temporarily unavailable</p></div>';    
    }
    //$tpl_content['content'] = '<div class="page">'.$page['content'].'</div>';
} else if($service = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_store_services` WHERE pathname = \''.db_escape_string(rtrim($_GET['uri'], '/')).'\'')) {
    $meta['title'] = $service['title'];
    $meta['description'] = $service['description'];
    $meta['keywords'] = $service['keywords'];
    $meta['canonical'] = $service['canonical'];
    tpl_set('services/view', [
        'id' => $service['id'],
        'header' => $service['name'],
        'image' => $service['image'],
        'content' => replacePageTags($service['content'])
    ], [
        'image' => $service['image'],
        'edit' => $user['edit_services']
    ], 'content');
} else {
    include ROOT_DIR.'/app/classes/uap.php';
    $ua = Uap::get($_SERVER['HTTP_USER_AGENT']);
    $ip = addslashes($_SERVER['REMOTE_ADDR']);
    db_query('INSERT INTO `'.DB_PREFIX.'_404` SET 
        url = \''.db_escape_string($_SERVER['REQUEST_URI']).'\',
        referer = \''.db_escape_string($_SERVER['HTTP_REFERER']).'\',
        user_agent = \''.db_escape_string($_SERVER['HTTP_USER_AGENT']).'\',
        os = \''.db_escape_string($ua['OS_NAME'].' '.$ua['OS_VERSION']).'\',
        browser = \''.db_escape_string($ua['BR_NAME'].' '.$ua['BR_VERSION']).'\',
        hw_type = \''.db_escape_string($ua['HW_TYPE']).'\',
        ip = \''.db_escape_string($ip).'\',
        views = 1,
        token = \''.db_escape_string(md5(md5($_SERVER['REQUEST_URI']).md5($_SERVER['HTTP_REFERER']).md5($_SERVER['HTTP_USER_AGENT'])).md5($ua['OS_NAME'].$ua['OS_VERSION'].$ua['BR_NAME'].$ua['BR_VERSION'])).'\' ON DUPLICATE KEY UPDATE views = views+1'
    );
    is_ajax() or http_response_code(404);
    $meta['title'] = 'Page not found 2';
    $tpl_content['content'] = '<div class="err404 ctnr"><h1>404</h1><p>Page not found 2</p></div>';
}
?>