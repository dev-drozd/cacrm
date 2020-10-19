<?php
/**
 * @appointment Contacts
 * @author      Victoria Shovkovych
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]) {
	
	case 'send_feedback':
		is_ajax() or die('Hacking attempt!');
		
		$name = text_filter($_POST['name'], 255, false);
		$email = text_filter($_POST['email'], 75, false);
		$mes = text_filter($_POST['mes'], 1000, false);
		$to = 'admin@yoursite.com';

		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			die('err_email');			
		
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		$headers .= 'To: '.$to. "\r\n";
		$headers .= 'From: '.$name.' <'.$email.'>' . "\r\n";

		// Send
		mail($to, 'Feedback from website', '<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Feedback from website</title>
		</head>
		<body style="background: #f6f6f6; text-align: center;">
			<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
				<div style="margin: -20px -20px 0; padding: 20px;">
					<a href="http://yoursite.com/">
						<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
					</a>
				</div>
				'.$name.' <'.$email.'>
				<div style="padding: 0 30px 30px;">
					'.$mes.'
				</div>
			</div>
		</body>
		</html>', $headers);
		
		die('OK');
	break;
	
    default:
        $meta['title'] = 'Contacts';
        $ohtml = '';
        if ($objects = db_multi_query('SELECT name, address, phone, map, email, DATE_FORMAT(work_time_start, \'%k:%i\') as work_time_start, DATE_FORMAT(work_time_end, \'%k:%i\') as work_time_end FROM `'.DB_PREFIX.'_objects` WHERE close = 0', true)) {
            foreach($objects as $o) {
                $coords = explode(',', $o['map']);
                $ohtml .= '<div class="map-item" data-lat="'.$coords[0].'" data-lng="'.$coords[1].'" data-email="'.$o['email'].'" onclick="setMarker(this);">
                    <h3>'.$o['name'].'</h3>
                    <div class="address">
                        '.$o['address'].'
                    </div>
					<p>Work time: '.$o['work_time_start'].'-'.$o['work_time_end'].'</p>
                    <div class="phone">'.$o['phone'].'</div>
                </div>';
            }
        }
        tpl_set('contacts', [
            'objects' => $ohtml
        ], [], 'content');
    break;
}

?>