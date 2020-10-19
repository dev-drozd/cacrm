<?php
/**
 * @appointment Settings admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

if($user['settings']){
 
	function groupOptions(){
		$groups = db_multi_query('SELECT `group_id`, `name`, `pay`, `timer`, `week_hours`, `rating` FROM `'.DB_PREFIX.'_groups` WHERE group_id NOT IN(1) ORDER BY `group_id` DESC', true);
		$options = '';
		foreach($groups as $group){
			$options .= '<option value="'.$group['group_id'].'">'.$group['name'].'</option>';
			tpl_set('settings/group', [
				'id' => $group['group_id'],
				'name' => $group['name'],
				'name' => $group['name'],
				'pay' => $group['pay'],
				'timer' => $group['timer'],
				'rating' => $group['rating'],
				'week_hours' => floatval($group['week_hours'])
			], [
				'custom' => ($group['group_id'] > 5)
			], 'groups');
		}
		return $options;
	}
	 
	switch($route[1]){
		
		/*
		* Delete franchise
		*/
		case 'delFranchise':
			is_ajax() or die('hacking!');
			$id = intval($_POST['id']);
			
			if(db_multi_query('SELECT id FROM `'.DB_PREFIX.'_franchise` WHERE del = 0 AND id = '.$id)){
				db_query('UPDATE `'.DB_PREFIX.'_franchise` SET del = 1 WHERE id = '.$id);
				echo 'OK';
			} else
				echo 'ERR';
			die;
		break;
		
		/*
		* Send franchise
		*/
		case 'sendFranchise':
			is_ajax() or die('hacking!');
			$id = intval($_POST['id']);
			
			$name = text_filter($_POST['name'], 500, false);
			$address = text_filter($_POST['address'], 500, false);
			$timezone = text_filter($_POST['timezone'], 50, false);
			$phone = text_filter($_POST['phone'], 255, false);
			$sms = text_filter($_POST['sms'], 15, false);
			$email = text_filter($_POST['email'], 100, false);
			$website = text_filter($_POST['website'], 100, false);
			$ip = text_filter($_POST['ip'], 16, false);
			$contract = intval($_POST['ip']);
			$amount = floatval($_POST['amount']);
			
			preg_match("/^[0-9-(-+,\s]+$/", $phone) or die('phone_not_valid');
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
				die('err_email');
			
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_franchise` SET
					name = \''.$name.'\',
					address = \''.$address.'\',
					phone = \''.$phone.'\',
					sms = \''.$sms.'\',
					email = \''.$email.'\',
					timezone = \''.$timezone.'\',
					website = \''.$website.'\',
					contract = \''.$contract.'\',
					amount = \''.$amount.'\',
					ip = INET_ATON(\''.$ip.'\')
					'.(
				$id ? 'WHERE id = '.$id : ''
			));
			
			$id = $id ? $id : intval(
				mysqli_insert_id($db_link)
			);
			
			echo $id;
			die;
		break;
		
		/*
		* Franchise
		*/
		case 'franchise':
			switch ($route[2]) {
				case 'add':
				case 'edit':
					$id = intval($route[3]);
					$fr = [];

					if ($id) {
						if ($fr = db_multi_query('SELECT *, INET_NTOA(ip) as ip FROM `'.DB_PREFIX.'_franchise` WHERE id = '.$id)) {
							
						} else {
							tpl_set('forbidden', [
								'text' => 'Client is not exists'
							], [
								'costum-group' => in_array(1, explode(
									',', $user['group_ids']
									)
								)
							], 'content');
						}
					}
					
					$timezones = '';
					$timezones_array = include APP_DIR.'/data/timezones.php';
					foreach($timezones_array as $v => $n){
						$timezones .= '<option value="'.$v.'"'.(
							$fr['timezone'] == $v ? 'selected' : ''
						).'>'.$n.'</option>';
					}
					
					$phones = '';
					$i = 0;
					foreach(explode(',', $fr['phone']) as $ph) {
                        $n = explode(' ', $ph);
                        $phones .= '<div class="sPhone">
                                    <span class="fa fa-times rd'.($i == 0 ? ' hide' : '').'" onclick="'.($i == 0 ? '' : '$(this).parent().remove();').'"></span>
                                    <select name="phoneCode">
                                        <option value="+1"'.(($n[0] == '+1' OR !$n[0]) ? ' selected' : '').'>+1</option>
                                        <option value="+3"'.($n[0] == '+3' ? ' selected' : '').'>+3</option>
                                    </select>
                                    <span class="wr">(</span>
                                    <input type="number" name="code" onkeyup="phones.next(this, 3);" value="'.$n[1].'" max="">
                                    <span class="wr">)</span>
                                    <input type="number" name="part1" onkeyup="phones.next(this, 7);" value="'.$n[2].'">
                                    <input type="number" name="part2" value="'.$n[3].'">
                                    <input type="checkbox" name="sms" onchange="phones.onePhone(this);" '.(trim($ph) === $u['sms'] ? ' checked' : '').'>
                                </div>';
						$i ++;
                    }
					
					tpl_set('settings/franchise/form', [
						'title' => ($id ? 'Edit ' : 'Add ').'client',
						'id' => $id,
						'name' => $fr['name'],
						'email' => $fr['email'],
						'phone' => $phones,
						'address' => $fr['address'],
						'amount' => $fr['amount'],
						'website' => $fr['website'],
						'ip' => $fr['ip'],
						'contract' => $fr['contract'],
						'timezones' => $timezones,
						'send' => $id ? 'Edit' : 'Add'
					], [
						'deleted' => $fr['del'],
						'edit' => $route[2] == 'edit',
						'costum-group' => in_array(1, explode(
							',', $user['group_ids']
							)
						)
					], 'content');
				break;
				
				case null:
					$meta['title'] = 'Franchise';
					
					$query = text_filter($_POST['query'], 255, false);
					$page = intval($_POST['page']);
					$count = 20;
					
					if($sql = db_multi_query('
						SELECT SQL_CALC_FOUND_ROWS 
							* 
						FROM `'.DB_PREFIX.'_franchise` WHERE 1 '.(
							$query ? ' AND `name` LIKE \'%'.$query.'%\' OR email LIKE \'%'.$query.'%\' OR REGEXP_REPLACE(phone, \' \', \'\') LIKE \'%'.$query.'%\' ' : ''
						).' ORDER BY `id` DESC LIMIT '.($page*$count).', '.$count, true)){
						$i = 0;
						foreach($sql as $row){
							tpl_set('settings/franchise/item', [
								'id' => $row['id'],
								'name' => $row['name'],
								'phone' => $row['phone'],
								'email' => $row['email']
							], [
								'deleted' => $row['del'],
							], 'items');
							$i++;
						}
						$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
					}
					$left_count = intval(($res_count-($page*$count)-$i));
				
					if($_POST){
						exit(json_encode([
							'res_count' => $res_count,
							'left_count' => $left_count,
							'content' => $tpl_content['items'],
						]));
					}
					
					tpl_set('settings/franchise/main', [
						'items' => $tpl_content['items'] ?: '',
						'res_count' => $res_count,
						'more' => $left_count ? '' : ' hdn',
					], [
						'costum-group' => in_array(1, explode(
							',', $user['group_ids']
							)
						)
					], 'content');
				break;
			}
		break;
		
		/*
		* Emails
		*/
		case 'emails':
			$meta['title'] = 'Emails';
			$items = '';
			foreach($config['emails'] as $v) {
				$items .= '<div class="iGroup optGroup">
					<div class="sSide">
						<label>Login</label>
						<input type="text" name="login" value="'.$v['login'].'">
					</div>
					<div class="sSide">
						<label>Password</label>
						<input type="password" name="password" value="'.$v['password'].'">
					</div>
					<div class="sSide rSide">
						<label>Default</label>
						<input type="checkbox" name="default" '.($v['default'] ? ' checked' : '').' onchange="Settings.defaultEmail(this);">
					</div>
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</div>';
			}
			tpl_set('settings/emails', [
				'items' => $items
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;
		
		/*
		* Currency
		*/
		case 'currency':
			$meta['title'] = 'Currency';
			$items = '';
			foreach($config['currency'] as $k => $v){
				$items .= '<div class="iGroup optGroup refPoints">
					<div class="sSide">
						<label>Name</label>
						<input type="text" name="name" value="'.$k.'">
					</div>
					<div class="sSide">
						<label>Symbol</label>
						<input type="text" name="symbol" value="'.$v['symbol'].'">
					</div>
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</div>';
			}
			tpl_set('settings/currency', [
				'items' => $items
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;
		
		/*
		* Send emails
		*/
		case 'send_emails':
			$arr = [];
			$def = 0;
			foreach($_POST as $row){
				$arr[] = [
					'login' => text_filter($row['login'], 50, false),
					'password' => text_filter($row['password'], 50, false),
					'default' => intval($row['default'])
				];
				if (intval($row['default']))
					$def ++;
			}
			if ($def > 1)
				die('def');
			else if (!$def)
				die('no_def');
			$config['emails'] = $arr;
			conf_save();
			echo 'OK';
			die;
		break;
		
		
		/*
		* Send currency
		*/
		case 'send_currency':
			$arr = [];
			foreach($_POST as $row){
				$arr[text_filter($row['name'], 25, false)] = [
					'symbol' => text_filter($row['symbol'], 10)
				];
			}
			$config['currency'] = $arr;
			conf_save();
			echo 'OK';
			die;
		break;
		
		/*
		* Send points
		*/
		case 'send_ref_points':
			$arr = [];
			foreach($_POST as $row){
				$arr[(int)$row['min_ref']] = (int)$row['points'];
			}
			$config['referral_points'] = $arr;
			conf_save();
			echo 'OK';
			die;
		break;
		
		case 'test':
			echo min_price2(1000);
			die;
		break;

		/*
		* Send price formula
		*/
		case 'send_price_formula':
			$arr = [];
			foreach($_POST as $row){
				$arr[(float)$row['min_price']] = (float)$row['mark_up'];
			}
			$config['price_formula'] = $arr;
			conf_save();
			echo 'OK';
			die;
		break;
		
		/*
		* Refferal points
		*/
		case 'price_formula':
			$meta['title'] = 'Price formula';
			$items = '';
			foreach($config['price_formula'] as $k => $v){
				$items .= '<div class="iGroup optGroup refPoints">
					<div class="sSide">
						<label>'.$lang['min_price'].'</label>
						<input type="number" name="min_price" value="'.$k.'" step="0.001">
					</div>
					<div class="sSide">
						<label>'.$lang['mark_up'].'</label>
						<input type="number" name="mark-up" value="'.$v.'" step="0.001">
					</div>
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</div>';
			}
			tpl_set('settings/price_formula', [
				'items' => $items
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;

		/*
		* Refferal points
		*/
		case 'refferal_points':
			$meta['title'] = 'Refferal points';
			$items = '';
			foreach($config['referral_points'] as $k => $v){
				$items .= '<div class="iGroup optGroup refPoints">
					<div class="sSide">
						<label>Minial refferals</label>
						<input name="min_ref" value="'.$k.'">
					</div>
					<div class="sSide">
						<label>Points</label>
						<input name="points" value="'.$v.'">
					</div>
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</div>';
			}
			tpl_set('settings/refferal_points', [
				'items' => $items
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;
		
		/*
		* Send discounts
		*/
		case 'send_ref_discounts':
			$arr = [];
			foreach($_POST as $row){
				$arr[(int)$row['ref']] = (int)$row['discount'];
			}
			$config['referral_discounts'] = $arr;
			conf_save();
			echo 'OK';
			die;
		break;
		
		/*
		* Refferal discounts
		*/
		case 'refferal_discounts':
			$meta['title'] = 'Refferal discounts';
			$items = '';
			foreach($config['referral_discounts'] as $k => $v){
				$items .= '<div class="iGroup optGroup refPoints">
					<div class="sSide">
						<label>Minial refferals</label>
						<input name="ref" value="'.$k.'">
					</div>
					<div class="sSide">
						<label>Discount %</label>
						<input name="discount" value="'.$v.'">
					</div>
					<span class="fa fa-times" onclick="$(this).parent().remove();"></span>
				</div>';
			}
			tpl_set('settings/refferal_discounts', [
				'items' => $items
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;
		
		/*
		* Get all groups
		*/
		case 'allGroups':
			$id = intval($_POST['id']);
			$lId = intval($_POST['lId']);
			$nIds = ids_filter($_POST['nIds']);

			$groups = db_multi_query('SELECT SQL_CALC_FOUND_ROWS group_id as id, name FROM `'.DB_PREFIX.'_groups` WHERE 1'.(
				$lId ? ' AND group_id < '.$lId : ''
			).($nIds ? ' AND group_id NOT IN('.$nIds.')' : '').' ORDER BY `group_id` DESC LIMIT 20', true);
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $groups,
				'count' => $res_count,
			]));
		break;
		
		/*
		* Get all forms
		*/
		case 'allForms':
			$id = intval($_POST['id']);
			$lId = intval($_POST['lId']);
			$nIds = ids_filter($_POST['nIds']);
			$query = text_filter($_POST['query'], 100, false);

			$forms = db_multi_query('SELECT SQL_CALC_FOUND_ROWS id, name FROM `'.DB_PREFIX.'_forms` WHERE 1'.(
				$lId ? ' AND id < '.$lId : ''
			).(
				$query ? ' AND MATCH(`name`) AGAINST (\'*'.$query.'*\', IN BOOLEAN MODE)': ''
			).($nIds ? ' AND id NOT IN('.$nIds.')' : '').' ORDER BY `id` DESC LIMIT 20', true);
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			die(json_encode([
				'list' => $forms,
				'count' => $res_count,
			]));
		break;
		
		/*
		*  Delete group
		*/
		case 'del_group':
			is_ajax() or die('hacking!');
			$id = intval($_POST['id']);
			if($id > 5){
				db_query('DELETE FROM `'.DB_PREFIX.'_groups` WHERE group_id = '.$id);
				echo 'OK';
			} else
				echo 'ERR';
			die;
		break;

		/*
		*  To git
		*/
		case 'to_git':
			is_ajax() or die('hacking!');
			if($_POST['comment']){
				echo shell_exec(
					'sh '.APP_DIR.'/system/git.sh '.escapeshellcmd(
						$_POST['comment']
					)
				);
				echo 'OK';
			}
			die;
		break;
		
		/*
		*  Server restart
		*/
		case 'restart':
			is_ajax() or die('hacking!');
			echo shell_exec('sh '.APP_DIR.'/system/restart.sh');
			die;
		break;
		
		/*
		*  Create group
		*/
		case 'add_group':
			is_ajax() or die('hacking!');
			$id = intval($_POST['id']);
			$name = text_filter($_POST['name'], 50, false);
			if($name AND in_array(1, explode(',', $user['group_ids']))){
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_groups` SET 
					name = \''.$name.'\',
					pay = \''.intval($_POST['pay']).'\',
					timer = \''.intval($_POST['timer']).'\',
					rating = \''.intval($_POST['rating']).'\',
					week_hours = \''.floatval($_POST['week_hours']).'\''.(
					$id ? ' WHERE group_id = '.$id : ''
				));
				echo 'OK';
			} else
				echo 'ERR';
			die;
		break;
		
		/*
		*  Save privileges
		*/
		case 'privileges':
			is_ajax() or die('hacking!');
			if(in_array(1, explode(',', $user['group_ids']))){
				if($id = intval($_POST['group'])){
					echo json_encode(array_map(function($a){
						return is_numeric($a) ? $a : explode(',', $a);
					}, db_multi_query('SELECT * FROM `'.DB_PREFIX.'_groups` WHERE group_id = '.$id, false, 'settings/group_previlegies_'.$id)));
				} else {
					echo tpl_set('settings/privileges', [
						'group-option' => groupOptions()
					]);
				}
			} else
				echo 'ERR';
			die;
		break;

		/*
		*  Get module lang
		*/
		case 'getLangModule':
			is_ajax() or die('hacking!');
			
			$lang_dir = ROOT_DIR.'/language/';
			
			$module = str_replace(
				'\\', '', trim($_POST['module'], '/')
			);
			
			in_array($_POST['type'], ['admin', 'site']) or die('Error type!');
			
			in_array($_POST['lang'], $config['lang_list']) or die('Error lang!');
			
			$path = $lang_dir.$_POST['lang'].'/'.$_POST['type'].'/'.$module;
			
			$phrases_file = $module == 'javascript' ? json_decode(file_get_contents($path.'.json'), true) : include $path.'.php';
			
			$phrases = '';
			
			foreach($phrases_file as $k => $v){
				$phrases .= '<div class="iGroup">
					<label>'.$k.'</label>
					<input type="text" name="'.$k.'" value="'.$v.'">
				</div>';
			}
			echo $phrases;
			die;
		break;
		
		/*
		*  Get lang
		*/
		case 'saveLang':
			is_ajax() or die('hacking!');
			
			$lang_dir = ROOT_DIR.'/language/';
			
			$exp = explode('_', $_POST['module']);
			
			unset($_POST['module']);
			
			$module = str_replace(
				'\\', '', trim($exp[1], '/')
			);
			
			in_array($exp[0], ['admin', 'site']) or die('Error type!');
			
			in_array($_POST['lang'], $config['lang_list']) or die('Error lang!');
			
			$path = $lang_dir.$_POST['lang'].'/'.$exp[0].'/'.$module.'.'.(
				$module == 'javascript' ? 'json' : 'php'
			);
			
			if($file = file_get_contents($path)){
				unset($_POST['lang']);
				$phrases = [];
				foreach($_POST as $k => $v){
					$phrases[$k] = $v;
				}
				if(file_put_contents($path, $module == 'javascript' ? json_encode(str_replace("'", "\'", $phrases), JSON_UNESCAPED_UNICODE) : "<?php\nreturn defined('ENGINE') ? ".
						PHP_EOL.var_export($phrases, true).PHP_EOL.
					" : die(\"Hacking attempt!\");\n?>"))
					echo 'OK';
				else
					echo 'ERR';
			}
			die;
		break;
		
		/*
		*  Get lang
		*/
		case 'getLang':
			is_ajax() or die('hacking!');
			
				$lang_dir = ROOT_DIR.'/language/';
				
				in_array($_POST['lang'], $config['lang_list']) or die('Error lang!');
				
				$modules = '<optgroup label="Admin panel">';
				
				foreach(scandir($lang_dir.$_POST['lang'].'/admin/') as $i => $item){
					if(file_exists($item)) continue;
					if($i == 2) $module = $item;
					$item = str_replace(['.php', '.json'], '', $item);
					$modules .= '<option value=\'admin_'.$item.'\''.(
						$i == 2 ? ' selected' : ''
					).'>'.ucfirst($item).'</option>';
				}
				
				$modules .= '</optgroup><optgroup label="Site">';
				
				foreach(scandir($lang_dir.$_POST['lang'].'/site/') as $item){
					if(!is_file($item)) continue;
					$item = str_replace(['.php', '.json'], '', $item);
					$modules .= '<option value=\'site_'.$item.'\'>'.ucfirst($item).'</option>';
				}
				
				echo $modules.'</optgroup>';
			die;
		break;
		
		/*
		*  languages
		*/
		case 'langs':
			$meta['title'] = 'Languages';
			
			$lang_dir = ROOT_DIR.'/language/';
			
			$module = '';
			$languages = '';
			
			foreach(scandir($lang_dir) as $item){
				if(is_dir($item)) continue;
					$languages .= '<option value=\''.$item.'\''.($item == $config['lang'] ? ' selected' : '').'>'.$item.'</option>';
			}
			
			$modules = '<optgroup label="Admin panel">';
			
			foreach(scandir($lang_dir.$config['lang'].'/admin/') as $i => $item){
				if(file_exists($item)) continue;
				if($i == 2) $module = $item;
				$item = str_replace(['.php', '.json'], '', $item);
				$modules .= '<option value=\'admin_'.$item.'\''.($i == 2 ? ' selected' : '').'>'.ucfirst($item).'</option>';
			}
			
			$modules .= '</optgroup><optgroup label="Site">';
			foreach(scandir($lang_dir.$config['lang'].'/site/') as $item){
				if(!is_file($lang_dir.$config['lang'].'/site/'.$item)) continue;
				$item = str_replace(['.php', '.json'], '', $item);
				$modules .= '<option value=\'site_'.$item.'\'>'.ucfirst($item).'</option>';
			}
			
			$modules .= '</optgroup>';
			$phrases_file = include $lang_dir.$config['lang'].'/admin/'.$module;
			$phrases = '';
			
			foreach($phrases_file as $k => $v){
				$phrases .= '<div class="iGroup">
					<label>'.$k.'</label>
					<input type="text" name="'.$k.'" value="'.$v.'">
				</div>';
			}
			
			tpl_set('settings/langs', [
				'languages' => $languages,
				'modules' => $modules,
				'phrases' => $phrases
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;	

		/*
		*  Save privileges
		*/
		case 'groups':
			is_ajax() or die('hacking!');
			if(in_array(1, explode(',', $user['group_ids']))){
				groupOptions();
				echo tpl_set('settings/groups', [
					'groups' => $tpl_content['groups']
				]);	
			}
			die;
		break;
		
		/*
		*  Save sms
		*/
		case 'save_sms':
			is_ajax() or die('hacking!');
			if(in_array(1, explode(',', $user['group_ids']))){
				$config['sms']['send_interval'] = floatval($_POST['sms_send_interval']);
				$config['sms']['send_limit'] = intval($_POST['sms_send_limit']);
				$config['sms']['to_day']['date'] = date('d.m.y');
				conf_save();
				echo 'OK';
				die;
			}
			die;
		break;
		
		/*
		*  Sms page
		*/
		case 'sms':
			//if(in_array(1, explode(',', $user['group_ids']))){
				$meta['title'] = 'Settings/SMS';
				tpl_set('settings/sms', [
					'sms-send-limit' => $config['sms']['send_limit'],
					'sms-send-interval' => $config['sms']['send_interval'],
					'sms-sended-today' => $config['sms']['to_day']['count']
				], [
					'costum-group' => in_array(1, explode(
						',', $user['group_ids']
						)
					)
				], 'content');	
		break;

		/*
		*  Phpinfo
		*/
		case 'phpinfo':
			ob_start();
			phpinfo();
			$info = ob_get_contents();
			ob_end_clean();
			$meta['title'] = 'php info';
			tpl_set('settings/phpinfo', [
				'phpinfo' => preg_replace(
					"/src=\"(.*)\" alt=\"PHP logo\"/i",
					"src=\"//php.net/images/logo.php\" alt=\"PHP logo\" height=\"50\"",
					str_replace(
					['934px', 'href="http://www.php.net/"'],
					['100%', 'href="http://www.php.net/" target="_blank"'],
					$info
				))
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;
		
		/*
		*  Delete form
		*/
		case 'del_form':
			is_ajax() or die('hacking!');
				$id = intval($_POST['id']);
				if ($id == 16 OR $id == 17) {
					die('no_del');
				} else {
					if($user['delete_object']){
						db_query('DELETE FROM `'.DB_PREFIX.'_forms` WHERE id = '.$id);
						if(mysqli_affected_rows($db_link)){
							exit('OK');
						} else
							exit('ERR');
					} else
						exit('ERR');
				}
			die;
		break;

		/*
		*  Save form
		*/
		case 'save_form':
			is_ajax() or die('hacking!');
			$id = intval($_POST['id']);
			$name = text_filter($_POST['name'], 25, false);
			if(in_to_array($_POST['type'], ['issue', 'user', 'device', 'transfer', 'sms', 'order']) AND $name){
				db_query((
					$id ? 'UPDATE' : 'INSERT INTO'
				).' `'.DB_PREFIX.'_forms` SET
						name = \''.$name.'\',
						content = \''.text_filter($_POST['content']).'\',
						types = \''.implode(
							',', array_text_filter(
								$_POST['type'], 8
							, false
						)).'\''.(
					$id ? 'WHERE id = '.$id : ''
				));
				echo $id ?: intval(mysqli_insert_id($db_link));
			} else
				echo 'ERR';
			die;
		break;
		
		/*
		*  Forms
		*/
		case 'forms':
			if ($route[2] == 'add' OR ($route[2] == 'edit' AND (
					$id = intval($route[3])
					)
				)
			){
				$title = ucfirst($route[2]).' form';
				$meta['title'] = $title;
				
				if($id){
					$row = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_forms` WHERE id = '.$id);
				} else
					$row = [];
				
				$types = explode(',', $row['types']);
				$options = '';
				
				foreach(['issue','user','device','transfer', 'sms', 'order'] as $option){
					$options .= '<option value="'.$option.'"'.(
						in_array($option, $types) ? ' selected' : ''
					).'>'.ucfirst($option).'</option>';
				}
				
				tpl_set('settings/forms/form', [
					'id' => intval($id),
					'title' => $title,
					'name' => $row['name'],
					'options' => $options,
					'content' => $row['content'],
					'send' => $route[2]
				], [
					'costum-group' => in_array(1, explode(
						',', $user['group_ids']
						)
					)
				], 'content');
			} else {
				$meta['title'] = 'Forms';
				$query = text_filter($_POST['query'], 255, false);
				$page = intval($_POST['page']);
				$count = 20;
				if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_forms` '.(
					$query ? 'WHERE name LIKE \'%'.$query.'%\' ' : ''
				).'ORDER BY `id` DESC LIMIT '.($page*$count).', '.$count
				, true)){
					$i = 0;
					foreach($sql as $row){
						tpl_set('settings/forms/item', [
							'id' => $row['id'],
							'name' => $row['name'],
							'type' => $row['type']
						], [
							'edit' => $user['edit_object'],
							'add' => $user['add_object']
						], 'forms');
						$i++;
					}
					$res_count = intval(
						mysqli_fetch_array(
							db_query('SELECT FOUND_ROWS()')
						)[0]
					);
				} else {
					tpl_set('noContent', [
						'text' => $lang['noForm']
					], false, 'objects');
				}
				$left_count = intval(($res_count-($page*$count)-$i));
				
				if($_POST){
					exit(json_encode([
						'res_count' => $res_count,
						'left_count' => $left_count,
						'content' => $tpl_content['objects'],
					]));
				}
				
				tpl_set('settings/forms/main', [
					'uid' => $user['id'],
					'res_count' => $res_count,
					'more' => $left_count ? '' : ' hdn',
					'forms' => $tpl_content['forms']
				], [
					'costum-group' => in_array(1, explode(
						',', $user['group_ids']
						)
					),
					'add' => $user['add_objects']
				], 'content');
			}
		break;
		
		
		/*
		*  Send referral
		*/
		case 'send_refferal':
			is_ajax() or die('hacking!');
			$id = intval($_POST['id']);
			$name = text_filter($_POST['name'], 150, false);
			db_query((
				$id ? 'UPDATE' : 'INSERT INTO'
			).' `'.DB_PREFIX.'_referrals` SET
					name = \''.$name.'\''.(
				$id ? 'WHERE id = '.$id : ''
			));
			echo 'OK';
			die;
		break;
		
		/*
		*  Del referral
		*/
		case 'del_refferal':
			is_ajax() or die('hacking!');
				$id = intval($_POST['id']);
				db_query('DELETE FROM `'.DB_PREFIX.'_referrals` WHERE id = '.$id);
				if(mysqli_affected_rows($db_link)){
					exit('OK');
				} else
					exit('ERR');
			die;
		break;

		/*
		*  Referrals
		*/
		case 'referrals':
			$meta['title'] = 'Referrals';
			$query = text_filter($_POST['query'], 255, false);
			$page = intval($_POST['page']);
			$count = 10;
			if($sql = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS * FROM `'.DB_PREFIX.'_referrals` WHERE 1 '.(
				$query ? ' AND name LIKE \'%'.$query.'%\' ' : ''
			).' ORDER BY `id` LIMIT '.($page*$count).', '.$count, true)){
				$i = 0;
				foreach($sql as $row){
					tpl_set('settings/referrals/item', [
						'id' => $row['id'],
						'name' => $row['name']
					], [], 'referrals');
					$i++;
				}
				$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
			}
			$left_count = intval(($res_count-($page*$count)-$i));
			if($_POST){
				exit(json_encode([
					'res_count' => $res_count,
					'left_count' => $left_count,
					'referrals' => $tpl_content['referrals'],
					'content' => $tpl_content['referrals']
				]));
			}
			tpl_set('settings/referrals/main', [
				'res_count' => $res_count,
				'more' => $left_count ? '' : ' hdn',
				'referrals' => $tpl_content['referrals']
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				),
				'edit' => $user['edit_store'],
				'add' => $user['add_store']
			], 'content');
		break;

		/*
		*  Save points
		*/
		case 'save_points':
		is_ajax() or die('hacking!');
		$key = key($_POST);
		$type = explode('-', $key)[0];
		if (in_array($type, ['camera_statuses', 'user_suspention'])) {
			$sql = '';
			$ids = '';
			foreach($_POST as $k => $v) {
				$id = explode('-', $k)[1];
				$sql .= 'WHEN '.$id.' THEN '.$v.' ';
				if ($ids) $ids .= ',';
				$ids .= $id;
			}
			if ($sql) {
				switch($type) {
					case 'camera_statuses':
						db_query('UPDATE `'.DB_PREFIX.'_camera_status` SET points = CASE id '.$sql.' ELSE points END WHERE id IN ('.$ids.')');
					break;
					
					case 'user_suspention':
						db_query('UPDATE `'.DB_PREFIX.'_users_writeup` SET points = CASE id '.$sql.' ELSE points END WHERE id IN ('.$ids.')');
					break;
				}
			}
			echo 'OK';
		} elseif (isset($config['user_points'][$key])){
			$config['user_points'][$key]['points'] = (float)$_POST[$key];
			if (count($_POST) > 1) {
				foreach($_POST as $k => $v) {
					$key = explode('-', $k);
					if ($key[1])
						$config['user_points'][$key[0]][$key[1]] = (float)$_POST[$k];
				}
			}
			conf_save();
			echo 'OK';
		}
		die;
		break;		
		
		/*
		*  Points
		*/
		case 'points':
			if ($route[2] == 'edit'){
				if(isset($config['user_points'][$route[3]])){
					$meta['title'] = 'Edit Points '.ucfirst(implode(' ', explode('_', $route[3])));
					$fields = '';
					if ($route[3] == 'camera_statuses') {
						if ($sts = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_camera_status`', true)) {
							foreach($sts as $s) {
								$fields .= '<div class="iGroup">
									<label class="upper">'.$s['name'].'</label>
									<input name="camera_statuses-'.$s['id'].'" type="number" value="'.$s['points'].'" step="0.001">
								</div>';
							}
						}
					} elseif ($route[3] == 'user_suspention') {
						if ($sts = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users_writeup`', true)) {
							foreach($sts as $s) {
								$fields .= '<div class="iGroup">
									<label class="upper">'.$s['name'].'</label>
									<input name="user_suspention-'.$s['id'].'" type="number" value="'.$s['points'].'" step="0.001">
								</div>';
							}
						}
					} elseif (count($config['user_points'][$route[3]]) > 2) {
						foreach($config['user_points'][$route[3]] as $k => $field) {
							if (!in_array($k, ['icon', 'points'])) {
								$fields .= '<div class="iGroup">
									<label class="upper">'.str_replace('_', ' ', $k).($route[3] == 'trade_in' ? ', %' : '').'</label>
									<input name="'.$route[3].'-'.$k.'" type="number" value="'.$field.'" step="0.001">
								</div>';
							}
						}
					}
					tpl_set('settings/points/form', [
						'name' => $route[3],
						'points' => (float)$config['user_points'][$route[3]]['points'],
						'fields' => $fields,
						'title' => ucfirst(str_replace('_', ' ', $route[3]))
					], [
						'costum-group' => in_array(1, explode(
							',', $user['group_ids']
							)
						),
						'edit' => $user['edit_store'],
						'add' => $user['add_store'],
						'trade_in' => $route[3] == 'trade_in',
						'camera' => $route[3] == 'camera_statuses',
						'ecommerce' => $route[3] == 'ecommerce',
						'user_suspention' => $route[3] == 'user_suspention'
					], 'content');	
				}
			} else {
				$meta['title'] = 'Points';
				foreach($config['user_points'] as $name => $opts){
					tpl_set('settings/points/item', [
						'id' => $name,
						'name' => ucfirst(implode(' ', explode('_', $name))),
						'icon' => $opts['icon'],
						'point' => $opts['points'].' '.($opts['points'] > 1 ? 'points' : 'point')
					], [], 'items');
				}
				tpl_set('settings/points/main', [
					'items' => $tpl_content['items'],
				], [
					'costum-group' => in_array(1, explode(
						',', $user['group_ids']
						)
					),
					'edit' => $user['edit_store'],
					'add' => $user['add_store']
				], 'content');
			}
		break;	
		
		/*
		*  Save settings
		*/
		case 'save':
			is_ajax() or die('hacking!');
			$now_lang = $config['lang'];
			foreach($_POST as $name => $value){
				if(preg_match('/(title|description|keywords)_*./i', $name)){
					$exp = explode('_', $name);
					$lang = end($exp);
					$name = str_ireplace('_'.$lang, '', $name);
					if(!is_array($config[$name])) $config[$name] = [];
						$config[$name][$lang] = addslashes($value);
				} else if($name == 'offline')
					$config['offline'] = intval($_POST['offline']);
				else if($name == 'privileges'){
					$group_id = intval(key($_POST[$name]));
					$sql = '';
					foreach($_POST[$name][$group_id] as $name => $value){
						$pre = $sql ? ', ' : '';
						if(is_array($value)){
							$sql .= $pre.$name.' = \''.implode(',', array_map('intval', $value)).'\'';
						} else {
							$sql .= $pre.$name.' = '.intval($value);
						}
						cache_delete('settings/group_previlegies_'.$group_id);
					}
					db_query('UPDATE `'.DB_PREFIX.'_groups` SET '.$sql.' WHERE group_id = '.$group_id);
				} else
					$config[$name] = addslashes($value);
			}
			if(conf_save()){
				if($_POST['lang'] != $now_lang){
					echo 'RELOAD';
				} else
					echo 'OK';
			} else
				echo 'ERR';
			die;
		break;
		
		case 'send_to_app':
			$id = (int)$_POST['id'];
			$title = text_filter($_POST['title'], 255, false);
			$message = text_filter($_POST['message'], 2500, false);
			sPush($id, $title, $message, [
				'type' => 'alert',
				'msg' => $message,
				'id' => uniqid()
			]);
			die('OK');
		break;
		
		case 'app_log':
			//echo '<pre>';
			foreach(db_multi_query('SELECT * FROM `'.DB_PREFIX.'_users` WHERE last_visit_app > NOW() - INTERVAL 14 DAY ORDER BY last_visit_app DESC LIMIT 100', true) as $row){
				tpl_set('settings/applog/item', [
					'id' => $row['id'],
					'name' => $row['name'],
					'lastname' => $row['lastname'],
					'last-visit' => $row['last_visit_app'],
					'ava' => $row['image']
				], [
					'ava' => $row['image'],
					'android' => true,
					'ios' => $row['id'] == 31735 ? true : false,
					'deleted' => $row['del']
				], 'users');
			}
			tpl_set('settings/applog/main', [
				'logs' => $tpl_content['users']
			], [
				'costum-group' => in_array(1, explode(
					',', $user['group_ids']
					)
				)
			], 'content');
		break;
		
		/*
		*  Setting page
		*/
		case null:
			$meta['title'] = $lang['Settings'];
				$groups = db_multi_query('SELECT `group_id`, `name`, `timer`, `pay`, `week_hours` FROM `'.DB_PREFIX.'_groups` WHERE group_id NOT IN(1,5) ORDER BY `group_id` DESC', true, 'settings/groups_list');
				$options = '';
				foreach($groups as $group){
					$options .= '<option value="'.$group['group_id'].'">'.$group['name'].'</option>';
					tpl_set('settings/group', [
						'id' => $group['group_id'],
						'name' => $group['name'],
						'pay' => $group['pay'],
						'timer' => $group['timer'],
						'week_hours' => floatval($group['week_hours']),
					], [
						'custom' => ($group['group_id'] > 5)
					], 'groups');
				}
				
				$order_form = '';
				if ($forms = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_forms` WHERE \'order\' IN (types)', true)) {
					foreach($forms as $form) {
						$order_form .= '<option value="'.$form['id'].'"'.($config['order_form'] == $form['id'] ? ' selected' : '').'>'.$form['name'].'</option>';
					}
				}
					
				tpl_set('settings/main', [
					'lang' => $config['lang'],
					'max-life-time' => $config['maxlifetime'],
					'sitemap' => file_get_contents(ROOT_DIR.'/sitemap.xml'),
					'sitemap-lastdate' => date("F d Y H:i:s.", filemtime(ROOT_DIR.'/sitemap.xml')),
					'options' => $options,
					'admin-uri' => $config['admin_uri'],
					'format-date' => $config['format_date'],
					'quick-sell' => $config['quick_sell'],
					'min-purchase' => $config['min_purchase'],
					'issue-min-total' => $config['issue_min_total'],
					'title' => $config['title']['en'],
					'keywords' => $config['keywords']['en'],
					'description' => $config['description']['en'],
					'title-services' => $config['title_services']['en'],
					'keywords-services' => $config['keywords_services']['en'],
					'description-services' => $config['description_services']['en'],
					'title-self-services' => $config['title_self_services']['en'],
					'keywords-self-services' => $config['keywords_self_services']['en'],
					'description-self-services' => $config['description_self_services']['en'],
					'title-blog' => $config['title_blog']['en'],
					'keywords-blog' => $config['keywords_blog']['en'],
					'description-blog' => $config['description_blog']['en'],
					'title-store' => $config['title_store']['en'],
					'keywords-store' => $config['keywords_store']['en'],
					'description-store' => $config['description_store']['en'],
					'offline' => $config['offline'] ? 'checked' : '',
					'offline-msg' => $config['offline_msg'],
					'cache-host' => $config['cache_host'],
					'cache-key' => $config['cache_key'],
					'cache-sel' => $config['cache_sel'],
					'camera-period' => $config['camera_period'],
					'timezone' => $config['timezone'],
					'ebay-devID' => $config['ebay_devID'],
					'ebay-appID' => $config['ebay_appID'],
					'ebay-certID' => $config['ebay_certID'],
					'ebay-token' => $config['ebay_token'],
					'tablet-user' => $config['tablet_user'],
					'tablet-password' => $config['tablet_password'],
					'order-form' => $order_form,
					'min-forfeit' => $config['min_forfeit'],
					'min-lack' => $config['min_lack'],
					'max-lack' => $config['max_lack']
				], [
					'costum-group' => in_array(1, explode(
						',', $user['group_ids']
						)
					)
				], 'content');	
		break;
	}
} else {
	tpl_set('forbidden', [
		'text' => 'Forbidden',
	], [], 'content');
	$meta['title'] = $lang['Settings'];
}
?>