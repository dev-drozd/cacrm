<?php
/**
 * @appointment Minichat admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

//is_ajax() or die('hacking');

function outputMsg($a){
	return preg_replace(	
		[
			"~(?:https?\:\/\/|)(?:www\.|)(?:youtube\.com|youtu\.be)\/(?:embed\/|v\/|watch\?v=|)(.{11})((&|\?)*[\S]*[\s]?)?~",
			"~((?:ht|f)tps?)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~",
			"~(Purchase|RMA|Issue|Stock|User|Camera)\s\#([0-9]+)\,?~i"
		], [
			'<div class="youtube" onmousedown="Im.getYoutube(this, \'$1\')">
				<img width="640" height="480" src="//img.youtube.com/vi/$1/sddefault.jpg">
				<span class="fa fa-youtube-play"></span>
			</div>',
			'<a href="$1://$2" target="_blank">$1://$2</a>$3',
			'<div class="tooltip" onmousemove="Im.tooltip(this, \'$1\', $2);"><a href="javascript:Im.tagLink(\'$1\', $2);">$1 #$2</a><div></div></div>'
		], str_replace(
			[
				"\n",
				":)",
				":("
			], [
				'<br />',
				'<img src="/uploads/smiles/happy.png">',
				'<img src="/uploads/smiles/sad.png">'
			], htmlspecialchars($a, ENT_HTML5)
		)
	);
}

switch($route[1]){
	
	case 'test':
	
$header = '<?xml version="1.0"?>
		<?mso-application progid="Excel.Sheet"?>
		<ss:Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
			<ss:Styles>
				<ss:Style ss:ID="header">
					<ss:Interior ss:Color="#6da62c" ss:Pattern="Solid"/>
					<ss:Font ss:Bold="1" ss:Size="10"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
					</ss:Borders>
				</ss:Style>
				<Style ss:ID="wrapText">
					<ss:Interior ss:Color="#f6f6f6" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
						<ss:Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top" ss:WrapText="1"/>
				</Style>
				<Style ss:ID="wrapTextNb">
					<ss:Interior ss:Color="#f6f6f6" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top" ss:WrapText="1"/>
				</Style>
				<ss:Style ss:ID="title">
					<ss:Font ss:Bold="1" ss:Size="12"/>
					<ss:Alignment ss:Horizontal="Center"/>
				</ss:Style>
				<ss:Style ss:ID="isImportant">
					<ss:Interior ss:Color="#f0935a" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="isNew">
					<ss:Interior ss:Color="#ed5555" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="isFinished">
					<Alignment ss:Vertical="Top"/>
					<ss:Interior ss:Color="#a3e179" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
				</ss:Style>
				<ss:Style ss:ID="isAll">
					<ss:Interior ss:Color="#ffd966" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="def">
					<ss:Interior ss:Color="#f6f6f6" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="col">
					<ss:Font ss:Size="10"/>
				</ss:Style>
			</ss:Styles> ';
$footer = '</ss:Workbook>';

$cntn = $header.'<ss:Worksheet ss:Name="WorksheetName">
					<ss:Table>
				<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
			';
	
$empty = '<ss:Row>
			<ss:Cell></ss:Cell>
			<ss:Cell></ss:Cell>
			<ss:Cell></ss:Cell>
			<ss:Cell></ss:Cell>
			<ss:Cell></ss:Cell>
			<ss:Cell></ss:Cell>
			<ss:Cell></ss:Cell>
			<ss:Cell></ss:Cell>
		</ss:Row>';
	
		$staffs = ['Shayne Williams', 'Ota Greven'];
		
	function time_format($t) {
		$hs = intval($t);
		$ms = ($t - intval($t))*60;
		return ($hs < 10 ? '0'.$hs : $hs).':'.($ms < 10 ? '0'.$ms : $ms).':00';
		
	}
		
		function rand_divide($a, $b){
			$r = array_fill(0, $b, $a/$b);
			$n = 0;
			for($i = 0; $i < $b; $i++){
				if($n > 0){
					$r[$i] = $r[$i]-$n;
					$n = 0;
				} else if(isset($r[$i+1])){
					$n = rand($r[$i]/$b, $r[$i]/3);
					$r[$i] = $r[$i]+$n;
				}
			}
			shuffle($r);
			return $r;
		}
		
		function gen_working_time($a, $b){
			global $staffs;
			$d = new DateTime($a);
			$r = [];
			$g = 0;
			foreach($b as $h){
				$h = ceil($h);
				$sd = [9, 9.25, 9.5, 9.75, 10];
				$s = $sd[array_rand($sd)];
				$t = 0;
				$n = 'Old lady';
				if($l = $h > 6 ? rand(1,2) : 0){
					if($g >= 5 && isset($staffs[1])){
						$n = $staffs[1];
						$t = rand(2.5, 5);
						$h = $h-$t;
					} else if((isset($staffs[1]) && $g <= 10) || !isset($staffs[1]) && $g <=5){
						$t = isset($staffs[1]) ? rand(2.5, 5) : rand(2, 3);
						$g = $g+$t;
						$h = $h-$t;	
					}
				}
				$e = $s+$h+$l+t;
				$r[] = [
					'name' => $staffs[0],
					'date' => $d->format('Y-m-d'), 
					'working-time' => $h, 
					'start-time' => gmdate('H:i', $s*3600),
					'break' => $l ? [
						'start' => gmdate('H:i', ($s+$l)*3600),
						'end' => gmdate('H:i', ($s+($l*2))*3600),
						'long' => $l
					] : [],
					'end-time' => gmdate('H:i', $e*3600)
				];
				if($t){
					$r[] = [
						'name' => $n,
						'date' => $d->format('Y-m-d'), 
						'working-time' => $t, 
						'start-time' => gmdate('H:i', ($s+($l*2))*3600),
						'break' => [],
						'end-time' => gmdate('H:i', ($s+($l*2)+$t)*3600)
					];
				}
				$d->modify('+1 day');
			}
			return $r;
		}
		
		echo '<pre>';
		$omount1 = [150, 200];
		$omount2 = [250, 300];
		$omount3 = [350, 450];
		$start_date = new DateTime('14.01.2014');
		$end_date = new DateTime('15.03.2017');
		$otto_date = new DateTime('10.09.2020');
		$date = clone $start_date;
		$week = 0;
		while ($date <= $end_date) {
			$date_began = $date->format('Y-m-d');
			$date->modify('+6 day');
			$date_end = $date->format('Y-m-d');
			if(strtotime($date_end) >= strtotime($otto_date->format('Y-m-d'))){
				unset($staffs[1]);
			}
			$week ++;
			if($working_time =  gen_working_time($date_began, rand_divide(isset($staffs[1]) ? rand(45,50) : rand(35, 40), 5))){
				$users = [];
				foreach($working_time as $k => $wt) {
					if (!$users[$wt['name']])
						$users[$wt['name']] = ['html' => '', 'total' => 0];
					$users[$wt['name']]['total'] += $wt['working-time'];
					$users[$wt['name']]['html'] .= '<ss:Row>
						<ss:Cell ss:StyleID="def"></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$wt['date'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$wt['start-time'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$wt['break']['start'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$wt['break']['end'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$wt['end-time'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.time_format($wt['working-time']).'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String"></ss:Data></ss:Cell>
					</ss:Row>';
					 
				}
				$now_week = '';
				foreach($users as $k => $u) {
					$cntn .= ($now_week != $week ? '<ss:Row>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Week №'.$week.'</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch In</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break Start</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break End</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch Out</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Working Time</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Amount</ss:Data></ss:Cell>
							</ss:Row>' : '').'
							<ss:Row>
								<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.$k.'</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
							</ss:Row>'.$u['html'].'<ss:Row>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.time_format(floor($u['total'])).'</ss:Data></ss:Cell>
								'.($k == 'Ota Greven' ? '
								<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.(
									$u['total'] <= 9 ? $omount1[array_rand($omount1)] : (
										($u['total'] > 9 && $u['total'] <= 12) ? $omount2[array_rand($omount2)] : $omount3[array_rand($omount3)]
									)
								).'</ss:Data></ss:Cell>
								' : '<ss:Cell ss:StyleID="isFinished"></ss:Cell>').'
							</ss:Row>'.$empty; 
					$now_week = $week;
				}
			}
			$date->modify('+1 day');
		}
$cntn .= '</ss:Table>
		</ss:Worksheet>'.$footer;

file_put_contents(ROOT_DIR.'/test.xls', $cntn);

echo '<a href="/test.xls" download>download</a>';
		die;
	break;
	
	case 'im':
		$q = text_filter($_POST['q'], 255, false);
		$p = (int)$_POST['p'];
		
		echo json_encode([db_multi_query('
			SELECT id, name, lastname, image FROM `'.DB_PREFIX.'_users`
			WHERE NOT FIND_IN_SET(5, group_ids) '.(
				$q ? 'AND MATCH(name, lastname) AGAINST (\'*'.$q.'*\' IN BOOLEAN MODE) ' : 'ORDER BY `id` DESC'
			).' LIMIT '.($p*20).', 20',
		true), (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]]);
		
		
/* 		echo json_encode([db_multi_query('
			SELECT u.id, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_im` i 
				INNER JOIN `'.DB_PREFIX.'_users` u 
				ON u.id = IF(for_uid = '.$user['id'].', from_uid, for_uid)
			WHERE (for_uid = '.$user['id'].' OR from_uid = '.$user['id'].') '.(
				$q ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$q.'*\' IN BOOLEAN MODE) ' : ''
			).'ORDER BY i.date DESC LIMIT '.($p*20).', 20',
		true), (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]]); */
	break;
	
	case 'support':
		$q = text_filter($_POST['q'], 255, false);
		$p = (int)$_POST['p'];
		echo json_encode([db_multi_query('SELECT * FROM `'.DB_PREFIX.'_chat_im`'.(
			$q ? ' WHERE MATCH(email) AGAINST (\'*'.$q.'*\' IN BOOLEAN MODE) ' : ''
		).' ORDER BY `date` DESC LIMIT '.($p*50).', 50', true, false, function($a, $b){
			if($a['name']){
				$exp = explode(' ', $a['name']);
				$name = $exp[0];
				$lastname = $exp[1];
			}
			$a['name'] = $name ?? ($a['email'] ?: 'Guest #'.$a['id']);
			$a['lastname'] = $lastname ?? '';
			return [$b, $a];
		}), (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]]);
	break;
	
	case 'history_support':
		$id = (int)$_POST['sid'];
		echo json_encode([array_reverse(db_multi_query('
			SELECT m.*, IF(m.date = CURDATE(), DATE_FORMAT(m.date, \'%k:%i\'), DATE_FORMAT(m.date, \'%m-%d-%y %k:%i\')) as date, c.email, u.name, u.lastname FROM `'.DB_PREFIX.'_chat_messages` m 
				INNER JOIN `'.DB_PREFIX.'_chat_im` c 
				ON c.id = m.im_id 
				LEFT JOIN `'.DB_PREFIX.'_users` u ON m.staff_id = u.id
			WHERE m.im_id = '.$id.'
				ORDER BY m.id DESC
			LIMIT 0, 20', true, false, function($a, $b){
				$a['uid'] = $a['staff_id'];
				$a['name'] = $a['staff_id'] ? $a['name'] : 'Guest #'.$a['im_id'];
				$a['lastname'] = '';
				return [$b, $a];
			})), (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]]
		);
	break;
	
	case 'history':
		$id = (int)$_POST['sid'];
		
		$gid = 0;
		if(preg_match('/G\-([0-9]+)/i', $_POST['sid'], $match))
			$gid = $match[1];
			
		$p = (int)$_POST['p'];
		if($id > 0){
			@db_query('UPDATE `'.DB_PREFIX.'_im` i INNER JOIN `'.DB_PREFIX.'_users` u ON u.id = IF(
				i.for_uid = '.$user['id'].', i.for_uid, i.from_uid
			) SET  u.new_msg = u.new_msg-IF(
				i.from_uid = '.$user['id'].', i.from_msg, i.for_msg
			), i.for_msg = IF(
				i.for_uid = '.$user['id'].', 0, i.for_msg
			), i.from_msg = IF(
				i.from_uid = '.$user['id'].', 0, i.from_msg
			) WHERE (
				i.from_uid = '.$user['id'].' OR i.for_uid = '.$user['id'].'
			) AND (
				i.from_uid = '.$id.' OR i.for_uid = '.$id.'
			)');
			db_query('
				UPDATE `'.DB_PREFIX.'_messages`
				SET viewed = 1
				WHERE from_uid = '.$id.'
				AND for_uid = '.$user['id'].'
				AND viewed = 0'
			);
			send_push($id, [
				'type' => 'viewed_messages',
				'uid' => $user['id']
			]);
		}
		$where = $id > 0 ? ' (
			m.for_uid = '.$user['id'].' AND m.from_uid = '.$id.'
		) OR (
			m.from_uid = '.$user['id'].' AND m.for_uid = '.$id.'
		)' : (
			$gid > 0 ? 'm.sid = '.$gid : 'm.for_uid = 0 AND m.sid = 0'
		);
		echo json_encode([array_reverse(db_multi_query('
				SELECT m.*, IF(m.date = CURDATE(), DATE_FORMAT(m.date, \'%k:%i\'), DATE_FORMAT(m.date, \'%m-%d-%y %k:%i\')) as date, u.name, u.lastname, u.image FROM `'.DB_PREFIX.'_messages` m
				INNER JOIN  `'.DB_PREFIX.'_users` u
					ON m.from_uid = u.id
				WHERE '.$where.' ORDER BY m.id DESC LIMIT '.($p*50).', 50', true, false, function($a, $b){
				$attach_images = '';
				$attach_files = '';
				if($a['attaches']){
					$attach_images = '<div class="thumbnails">';
					$attach_files = '<ul class="files">';
					foreach(explode('|:|', $a['attaches']) as $file){
						if(preg_match("/(.*).(jpeg|jpg|png|gif)/i", $file)){
							$attach_images .= '<div class="thumb">
									<img src="/uploads/attaches/'.($a['ind'] ? 0 : $a['for_uid']).'/thumb_'.$file.'" onclick="showPhoto(this.src);">
								</div>';
						} else {
							$attach_files .= '<li>
									<a href="/uploads/attaches/'.($a['ind'] ? 0 : $a['for_uid']).'/'.$file.'" download>
										<span class="fa fa-file"></span> '.$file.'
									</a>
								</li>';
						}
					}	
					$attach_images .= '</div>';
					$attach_files .= '</ul>';
				}
				$a['message'] = $a['del'] ? '<span class="fa fa-trash imDeleted"></span> '.$lang['MessageDeleted'] : outputMsg($a['message']).$attach_images.$attach_files;
				return [$b, $a];
			})), (int)mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]]
		);
	break;
}
die;
?>