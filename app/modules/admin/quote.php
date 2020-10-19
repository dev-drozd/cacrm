<?php

defined('ENGINE') or ('hacking attempt!');


switch($route[1]){
	
	case 'send':
	
		$id = (int)$_POST['id'];
		if($quote = db_multi_query('SELECT q.*, o.address as store_address, o.phone as store_phone, CONCAT(TIME_FORMAT(o.work_time_start, "%H:%i"), \'-\', TIME_FORMAT(o.work_time_end, "%H:%i")) as store_hours, o.map as store_loc FROM `'.DB_PREFIX.'_quote_requests` q LEFT JOIN `'.DB_PREFIX.'_objects` o ON q.store_id = o.id WHERE q.id = '.$id)){
			
			$form = db_multi_query('SELECT content FROM `'.DB_PREFIX.'_forms` WHERE id = 21');
			
			$text = str_ireplace([
				'{staff-name}',
				'{customer-question}',
				'{staff-answer}',
				'{store-location}',
				'{store-hours}',
				'{store-phone}'
			], [
				$user['first_name'].' '.$user['last_name'],
				$quote['issue'],
				'<blockquote style="padding: 15px;background: #fbfbfb;border-radius: 4px;">'.$_POST['text'].'</blockquote>',
				'<a href="https://maps.google.com/?q=loc:'.$quote['store_loc'].'">'.$quote['store_address'].'</a>',
				$quote['store_hours'],
				'<a href="tel:+1 '.$quote['store_phone'].'">'.$quote['store_phone'].'</a>'
			], $form['content']);
			
			if(isset($_POST['email']) && $_POST['email']){
				
				include APP_DIR.'/classes/smtp.php';
				
				Mail::sendTpl($quote['email'], 'Reply to your request #'.$id, $text);
				
			}
			
			if(isset($_POST['sms']) && $_POST['sms']){
				send_sms($quote['phone'], strip_tags($text));
			}
			
			db_query(
				'UPDATE `'.DB_PREFIX.'_quote_requests` SET
					sent_id = '.$user['id'].',
					reply_text = \''.db_escape_string($_POST['text']).'\',
					send_date = Now() 
				WHERE id = '.$id
			);
		}
		die;
	break;
	
	case 'create_app':
		$quote_id = intval($_POST['id']);
		if($first_name = text_filter($_POST['name'], 100, false)){
			$last_name = text_filter($_POST['lastname'], 100, false);
			$email = text_filter($_POST['email'], 100, false);
			$phone = text_filter($_POST['phone'], 17, false);
			
			$password = substr(md5(uniqid()), 0, 6);
		
			db_query('INSERT INTO `'.DB_PREFIX.'_users` SET
				name = \''.$first_name.'\',
				lastname = \''.$last_name.'\',
				phone = \''.$phone.'\',
				sms = \''.$phone.'\',
				email = \''.$email.'\',
				password = \''.$password.'\',
				group_ids = \'5\'
			');
			
			// Headers
			$headers  = 'MIME-Version: 1.0'."\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$headers .= 'To: '.$email. "\r\n";
			$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";
			
			$message = '<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>New user on Your Company</title>
			</head>
			<body style="background: #f6f6f6; text-align: center;">
				<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 600px; max-width: 100%; background: #ffffff; border: 1px solid #ddd; padding: 20px; font-family: monospace; font-size: 14px; line-height: 24px; color: #828282; text-align: center; margin: 30px auto;">
					<div style="margin: -20px -20px 0; padding: 20px;">
						<a href="http://yoursite.com/">
							<img src="http://yoursite.com/templates/site/img/logo.png" style="width: 60%; margin: 25px 0;">
						</a>
					</div>
					<div style="padding: 0 30px 30px;">
						<p>You successfully registered on <br><b>Your Company inc</b>.</p>
						<p>Please use the details below for login to website:</p>
						<div style="box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; width: 300px; background: #f1f8fb; padding: 30px; color: #768b94; text-align: left; max-width: 100%; margin: 30px auto 0;">
							Link: <a href="http://yoursite.com/" style="color: #0e92d4;">Click here</a><br>
							Login: '.$email.'<br>
							Password: '.$password.'
						</div>
					</div>
				</div>
			</body>
			</html>';
			
			$user_id = intval(mysqli_insert_id($db_link));
		} else
			$user_id = intval($_POST['customer_id']);
			
		db_query('UPDATE `'.DB_PREFIX.'_quote_requests` SET flag = 2, staff_id = '.$user['id'].','.(
			$user_id ? 'customer_id = '.$user_id.',' : ''
		).' app_date = Now() WHERE id = '.$quote_id);
		
		echo $user_id;
		die;
	break;
	
	case 'edit':
		if($id = intval($route[2])){
			$row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_quote_requests` WHERE id = '.$id);
			tpl_set('quote/form', [
				'id' => $row['id'],
				'title' => 'Quote request #'.$row['id'],
				'name' => $row['first_name'],
				'lastname' => $row['last_name'],
				'email' => $row['email'],
				'phone' => $row['phone'],
				'issue' => $row['issue'],
				'customer-id' => $row['customer_id']
			], [
				'customer' => $row['customer_id']
			], 'content');
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE id = '.$id);
		}
	break;
	
	case 'view':
		if($id = intval($route[2])){
			$row = db_multi_query(
				'SELECT q.*,
					us.name as answ_name,
					us.lastname as answ_lastname,
					uc.image as customer_image,
					if(uc.name is not null, uc.name, q.first_name) as first_name,
					if(uc.lastname is not null, uc.lastname, q.last_name) as last_name,
					o.name as store
				FROM 
					`'.DB_PREFIX.'_quote_requests` q LEFT JOIN  
					`'.DB_PREFIX.'_users` uc ON q.customer_id = uc.id LEFT JOIN
					`'.DB_PREFIX.'_users` us ON q.sent_id = us.id LEFT JOIN
					`'.DB_PREFIX.'_objects` o ON q.store_id = o.id
				WHERE q.id = '.$id
			);
			$phone = trim(str_ireplace(['+1',' '],'', $row['phone']));
			$sms = '';
			include APP_DIR.'/classes/imap.php';
			imap::query('FROM "'.$phone.'@"', function($a) use(&$sms, $row){
				$data = imap::get_msg($a);
				$sms .= '<div class="answered">
					<a href="#" onclick="Page.get(this.href); return false;">'.$row['first_name'].' '.$row['last_name'].'</a>
					<p data-date="'.$data['date'].'">'.$data['msg'].'</p>
				</div>';
			});
			imap::close();
			tpl_set('quote/view', [
				'id' => $row['id'],
				'image' => $row['customer_image'],
				'pathname' => $row['pathname'],
				'ip' => $row['ip'],
				'send-id' => $row['sent_id'],
				'sms' => $sms,
				'title' => 'Quote request #'.$row['id'],
				'store-id' => $row['store_id'],
				'store' => $row['store'],
				'company' => $row['company'],
				'name' => $row['first_name'],
				'lastname' => $row['last_name'],
				'email' => $row['email'],
				'phone' => $row['phone'],
				'whatsapp' => preg_replace("/\D/", '', $row['phone']),
				'date' => convert_date($row['date'], true),
				'send-date' => $row['send_date'],
				'issue' => $row['issue'],
				'reply-text' => $row['reply_text'],
				'answ-name' => $row['answ_name'],
				'answ-lastname' => $row['answ_lastname'],
				'customer-id' => $row['customer_id']
			], [
				'company' => $row['company'],
				'ava' => $row['customer_image'],
				'pathname' => $row['pathname'],
				'ip' => $row['ip'],
				'store' => $row['store_id'],
				'customer' => $row['customer_id'],
				'answered' => $row['sent_id']
			], 'content');
			db_query('DELETE FROM `'.DB_PREFIX.'_notifications` WHERE id = '.$id);
		}
	break;
	
	case 'del':
		$id = (int)$_POST['id'];
		db_query('DELETE FROM `'.DB_PREFIX.'_quote_requests` WHERE id = '.$id);
	break;
	
	case null:
	
        $meta['title'] = 'Quotes';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		
		$count = 20;
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
			q.*, if(uc.name is not null, uc.name, q.first_name) as first_name,
			if(uc.lastname is not null, uc.lastname, q.last_name) as last_name
		FROM `'.DB_PREFIX.'_quote_requests` q LEFT JOIN `'.DB_PREFIX.'_users` uc ON q.customer_id = uc.id WHERE flag != 0 '.(
			$query ? 'AND MATCH (first_name, last_name, q.phone, q.email, q.issue) AGAINST (\''.$query.'\' IN BOOLEAN MODE) ' : ''
		).'ORDER BY q.sent_id ASC, q.date DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('quote/item', [
					'id' => $row['id'],
					'date' => convert_date($row['date'], true),
					'name' => $row['first_name'],
					'lastname' => $row['last_name'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'color' => $row['sent_id'] > 0 ? '#d8f5a2' : (
						$row['view_id'] > 0 ? '' : '#b9c4ce'
					),
                    'issue' => text_out_filter($row['issue'], 70),
                    'customer-id' => $row['customer_id']
                ],[
                    'customer' => $row['customer_id']
				], 'quote');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
           // tpl_set('noContent', [
            //    'text' => $lang['noFaqs']
            //], false, 'quote');
        }
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['quote'],
			]));
		}
		tpl_set('quote/main', [
			'title' => $meta['title'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'quotes' => $tpl_content['quote']
		], [
			'add' => $user['faq_add']
		], 'content');
	break;
}

?>