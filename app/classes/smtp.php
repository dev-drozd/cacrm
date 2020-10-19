<?php
/**
 * @appointment Main functions
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2018
 * @link        https://yoursite.com
 * This code is copyrighted
*/
 
class Mail {
	
	public static $timeout = 30,
				  $debug = false,
				  $from = 'Your Company <info@yoursite.com>',
				  $host = 'smtp.gmail.com',
				  $port = 465,
				  $login = 'dev.drozd@gmail.com',
				  $password = 'ponyolzctcllvkxh',
				  $secure = 'ssl',
				  $type = 'php',
				  $ctype = 'text/html',
				  $charset = 'utf-8',
				  $unsub = '',
				  $error = [0,''],
				  $headers = [
					  'MIME-Version: 1.0'
				  ];

	private static $lnk, $logs = [];
	
	public static function send($a, $b, $c, $d = null){
		//echo $c;
		//die;
		if(!$d && $type == 'smtp') $d = self::$login;
		else if(!$d)
			$d = self::$from;
		self::$headers[] = 'Content-type: '.self::$ctype.'; charset='.self::$charset;
		if($unsub){
			self::$headers[] = 'List-Unsubscribe: '.self::$unsub;
			self::$headers[] = 'Precedence: bulk';
		}
		self::$headers[] = 'To: <'. $a .'>';
		if($d) self::$headers[] = 'From: '. $d;
		if(self::$type == 'smtp'){
			self::log('Подключение к '.self::$host);
			self::$lnk = fsockopen((
				self::$secure == 'ssl' ? 'ssl://' : ''
			).self::$host, self::$port, self::$error[0], self::$error[1], self::$timeout);
			self::log(self::$lnk ? 'Cоединение установлено' : 'Не удалось подключиться к '. self::$host);
			self::get();
			self::post('EHLO '.self::$host);
			if(self::$secure == 'tls'){
				self::post('STARTTLS');
				stream_socket_enable_crypto(self::$lnk, TRUE, STREAM_CRYPTO_METHOD_TLS_CLIENT);
				self::post('EHLO '.self::$host);
			}
			self::$headers[] = 'Subject:'. $b ."\r\n\r\n";
			self::$headers[] = $c."\r\n.\r\n";
			self::post(
				'AUTH LOGIN',
				base64_encode(self::$login),
				base64_encode(self::$password),
				'MAIL FROM:'.$d,
				'RCPT TO:<'.$a.'>',
				'DATA',
				implode("\r\n", self::$headers),
				'QUIT'
			);
			self::log('Соединение закрыто');
			fclose(self::$lnk);
		} else {
			if(mail($a, $b, $c, implode("\r\n", self::$headers)))
				self::log('Успешно отправлено');
			else
				self::log('Не отправлено');
		}
	}
	
	public static function sendTpl($a, $b, $c, $d = null){
		self::send($a, $b, '<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>'.$b.'</title>
			</head>
			<body style="border: 0;margin: 0;background: #EEF5F9;text-align: center;padding: 130px 0px;">
				<div style="box-sizing: border-box;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;width: 100%;max-width: 700px;background: #ffffff;padding: 20px;font-family: monospace;font-size: 14px;line-height: 24px;color: #828282;text-align: center;margin: auto;border-radius: 4px;box-shadow: 2px 2px 2px rgba(0,0,0,0.1), -1px 0 2px rgba(0,0,0,0.05);">
					<div style="margin: -20px -20px 0; padding: 20px;">
						<a href="https://yoursite.com">
							<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 40%;margin: 25px 0;">
						</a>
					</div>
					<div style="display: flex;height: 5px;">
						<span style="width: 100%;background: #57c1e8;"></span>
						<span style="width: 100%;background: #ff5000;"></span>
						<span style="width: 100%;background: #b6bd00;"></span>
						<span style="width: 100%;background: #57c1e8;"></span>
						<span style="width: 100%;background: #ff5000;"></span>
						<span style="width: 100%;background: #b6bd00;"></span>
					</div>
					<div style="margin-top: 15px;padding: 80px 40px;border: 1px solid #f9f9f9;border-radius: 4px;">
						'.$c.'
					</div>
					<div style="text-align: center; font-size: 11px;border-top: 1px solid #f9f9f9;">
						<p>'.date('Y').' © Your Company. All Rights Reserved</p>
						<a href="https://yoursite.com/account/unsubscribe/'.base64_encode(trim($a).'-'.md5(trim($a).SALT)).'" style="text-decoration: none; color: #2593bb;">MANAGE NOTIFICATIONS</a>
					</div>
				</div>
			</body>
		</html>', $d);
	}
	
	private static function post(){
		foreach(func_get_args() as $a){
			self::log('Отправленые данные: '. $a);
			fputs(self::$lnk, $a . "\r\n");
			self::get();
		}
	}
	
	private static function get(){
		$res = '';
		while ($str = fgets(self::$lnk, 1024)){
			$res .= $str;
			if(substr($str, 3, 1) === ' ') break;
		}
		self::log('Ответ: '. $res);
	}
	
	private static function log($a){
		if(self::$debug == true) echo "<code>$a</code><br />";
		self::$logs[] = [
			'time' => time(),
			'message' => $a
		];
	}
}
?>