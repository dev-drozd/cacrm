<?php
/**
 * @appointment Registration
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */

switch($route[1]){
	
	case 'confirm':
		if($data = cache_get($route[2])){
			cache_delete($route[2]);
			db_query('INSERT INTO `'.DB_PREFIX.'_users` SET
				name = \''.$data['name'].'\',
				lastname = \''.$data['lastname'].'\',
				phone = \''.$data['phone'].'\',
				'.($data['address'] ? 'address = \''.$data['address'].'\',' : '').'
				email = \''.$data['email'].'\',
				password = \''.$data['password'].'\',
				hid = \''.$data['hid'].'\',
				group_ids = 5'
			);
			$uid = intval(mysqli_insert_id($db_link));
			if($data['ref'] > 0){
				db_query('
					UPDATE `'.DB_PREFIX.'_users`
					SET ref_points = '.(int)$config['refferal_points'].', referrals = referrals+1
					WHERE id = '.$data['ref']
				);
				db_query('
					INSERT INTO `'.DB_PREFIX.'_user_referrals` SET
					for_invited = '.$data['ref'].',
					from_invited = '.$uid
				);
			}
			$_SESSION['cart'] = $data['cart'];
			$_SESSION['uid'][0] = $uid;
			$_SESSION['uid'][1] = $data['hid'];
			$ctime = time()+(3600*24*7);
			setcookie('uid', $uid, $ctime, '/', $_SERVER['HTTP_HOST'], null, true);
			setcookie('hid', $data['hid'], $ctime, '/', $_SERVER['HTTP_HOST'], null, true);
			header('Location: /account');
			die;
		} else
			$tpl_content['content'] = '<div class="ctnr loginPage"><div class="noLogin">Link is outdated.</div></div>';
	break;
	
	default:
	is_ajax() or die('hacking');
	$data = [];
	$data['name'] = text_filter($_POST['name'], 25, false);
	$data['lastname'] = text_filter($_POST['lastname'], 25, false);
	$data['email'] = text_filter($_POST['email'], 50, false);
	$data['phone'] = text_filter($_POST['phone'], 25, false);
	$data['address'] = text_filter($_POST['address'], 255, false);
	$data['password'] = md5(md5(text_filter($_POST['password'], 32, false)));
	$data['hid'] = md5(md5($data['password']).md5($_SERVER['REMOTE_ADDR']).time());
	$data['cart'] = $_SESSION['cart'];
	if(isset($_SESSION['ref']) AND $_SESSION['ref'] > 0){
		$data['ref'] = $_SESSION['ref'];
	}
	
	// Check forms
	preg_match("/^[A-Za-za-яА-ЯїЇіІЄєЎўҐґ']+$/iu", $data['name']) or die('Name_not_valid');
	preg_match("/^[A-Za-za-яА-ЯїЇіІЄєЎўҐґ']+$/iu", $data['lastname']) or die('Lastname_not_valid');
	preg_match("/^[0-9-(-+]+$/", $data['phone']) or die('phone_not_valid');
	if($_POST['password'] != $_POST['password2']){
		echo 'err_password';
		die;
	}
	$e = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_users` WHERE email = \''.$data['email'].'\'');
	if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL) OR $e['count'] > 0){
		echo 'err_email';
		die;			
	}
	
	// Create secret key
	$key = md5(
		md5($data['email']).md5(uniqid(rand(),1)).md5($_POST['password'])
	);
	
	cache_set($key, $data);
	
	// Headers
	$headers  = 'MIME-Version: 1.0'."\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	$headers .= 'To: '.$data['email']. "\r\n";
	$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
	$headers .= 'Reply-To: '.$data['email']. "\r\n";
	$headers .= 'X-Mailer: PHP/ '.phpversion();
	
	mail($data['email'], 'Confirmation of registration on the Your Company', '<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Confirmation of registration on the Your Company</title>
	</head>
	<body style="background: #f6f6f6; text-align: center;">
		<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
			<div style="margin: -20px -20px 0; padding: 20px;">
				<a href="https://yoursite.com/">
					<img src="https://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
				</a>
			</div>
			<div style="padding: 0 30px 30px;">
				<p>You was successfully registered on <br><b>Your Company inc</b>. To confirm Your account, please follow the link or copy it to the browser address line:</p>
				<p><a href="https://yoursite.com/reg/confirm/'.$key.'">Click here</a></p>
				<p>Use the details below for entrance to the site:</p>
				<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
					Login: '.$data['email'].'<br>
					Password: '.$_POST['password'].'
				</div>
			</div>
		</div>
	</body>
	</html>', $headers);
	echo 'OK';
	die;
}
?>