<?php
/**
 * @appointment Main functions
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2018
 * @link        https://yoursite.com
 * This code is copyrighted
*/


defined('ENGINE') or ('hacking attempt!');

class Imap {
	
	private static $link,
	               $host = 'localhost',
	               $port = 143,
				   $usr = 'sms@yoursite.com',
				   $pass = '2Z8a4M2q',
				   $charset,
				   $date,
				   $subject,
				   $htmlmsg,
				   $plainmsg,
				   $attachments;
				   
	public static function connect(){
		if(!self::$link) self::$link = imap_open('{'.self::$host.':'.self::$port.'/novalidate-cert}INBOX', self::$usr, self::$pass);
		return self::$link;
	}

	public static function query($a,$b = false){
		$i = imap_search(self::connect(), $a);
		if($b) foreach($i as $r){$b($r);}
		return $i;
	}

	public static function get_file($a){
		$b = explode('|', base64_decode($a));
		self::query($b[0]);
		$f = self::get_msg($b[1])[1][$b[2]];
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $b[2]);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . mb_strlen($f));
		echo $f;
		die;
	}

	public static function get_msg($a){
		self::$htmlmsg = self::$plainmsg = self::$charset = '';
		self::$attachments = [];
		$h = imap_header(self::connect(),$a);
		$s = imap_fetchstructure(self::connect(),$a);
		if (!$s->parts)
			self::get_part($a,$s,0);
		else {
			foreach ($s->parts as $partno0=>$p)
				self::get_part($a,$p,$partno0+1);
		}
		return [
			'from' => imap_utf8($h->fromaddress),
			'date' => date('Y-m-d h:i:s', strtotime($h->date)),
			'msg' => self::$htmlmsg ?: self::$plainmsg,
			'attachments' => self::$attachments,
		];
	}
	
	private static function get_part($a,$b,$c){
		$d = $c ? imap_fetchbody(self::connect(), $a, $c, 2) : imap_body(self::connect(), $a);
		if($b->encoding==4) $d = quoted_printable_decode($d);
		else if($b->encoding==3) $d = base64_decode($d);

		$p = [];
		if ($b->parameters)
			foreach ($b->parameters as $x)
				$p[strtolower($x->attribute)] = $x->value;
		if ($b->dparameters)
			foreach ($b->dparameters as $x)
				$p[strtolower($x->attribute)] = $x->value;

		if ($p['filename'] || $p['name'])
			self::$attachments[imap_utf8(($p['filename']) ? $p['filename'] : $p['name'])] = $d;
		
		if ($b->type == 0 && $d) {
			if (strtolower($b->subtype) == 'plain')
				self::$plainmsg .= trim($d) ."\n\n";
			else
				self::$htmlmsg .= $d ."<br><br>";
			self::$charset = $p['charset']; 
		} elseif ($b->type==2 && $d) {
			self::$plainmsg .= $d."\n\n";
		}
		if ($b->parts) {
			foreach ($b->parts as $c2 => $b2)
				self::get_part($a,$b2,$c.'.'.($c2+1));
		}
	}

	public static function close(){
		imap_close(self::$link);
	}
}
?>