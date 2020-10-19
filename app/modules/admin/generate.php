<?php

//error_reporting(E_ALL|E_STRICT);

ini_set('display_errors', true);

ini_set('memory_limit', '8056M');

set_time_limit(0);
ignore_user_abort(true);

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

$users = [[
	'name' => 'Ota',
	'lastname' => 'Greven'
], [
	'name' => 'Ota',
	'lastname' => 'Greven'
], [
	'name' => 'Ota',
	'lastname' => 'Greven'
]];

$amounts = [
	300,
	350,
	400,
	450,
	500,
	550,
	700
];
$amounts_hours = [
	300 => 30,
	350 => 35,
	400 => 30,
	450 => 25,
	500 => 30,
	550 => 25,
	600 => 40,
	650 => 30,
	700 => 40
];

$data = [];
$hours = [4,6,6,6,7,7,7,8,8,8,8,8,8,8,8,8,8];
$minutes = [0,0,0,0,0,0,0,0.25,0.5,0.5,0.75];
$shours_long = [10,10,10,10,10,11,11];
$shours_short = [10,10,10,11,11,11,12,12,12,13];

$staffs = ['Shayne Williams', 'Ota Greven'];

echo '<pre>';

function shuffle_hours($total_hours, $days, $start, $end, $uid, $per_hour) {
	global $working_time, $hours, $minutes, $th, $exist, $shours_long, $shours_short, $i, $staffs;
	if ($days AND $total_hours) {
		$date = rand_date($start, $end, $exist);
		if ($total_hours <= 9) {
			
			$old_lady = [1.5, 2, 2.25, 2.5, 2.75, 3];
			$tg = $old_lady[array_rand($old_lady)];
			//$tg = rand(2, 3);
			
			$time1 = $total_hours-$tg;
			$time2 = $tg;
			
			$sdate1 = rand_start($time1 < 7 ? $shours_short : $shours_long, $minutes);
			$break1 = $time1 > 4 ? rand_break($sdate1, $time1) : [];

			$sdate2 = rand_start($time2 < 7 ? $shours_short : $shours_long, $minutes);
			$break2 = $time2 > 4 ? rand_break($sdate2, $time2) : [];
			
			$working_time[] = [
				'name' => $staffs[array_rand($staffs)],
				'per_hour' => $per_hour,
				'date' => $date, 
				'working-time' => $time1, 
				'start-time' => $sdate1,
				'break' => $break1,
				'end-time' => $sdate1 + $time1 + ($break1 ? $break1['long'] : 0)
			];
			$working_time[] = [
				'name' => 'Old lady',
				'per_hour' => $per_hour,
				'date' => $date, 
				'working-time' => $time2, 
				'start-time' => $sdate2,
				'break' => $break2,
				'end-time' => $sdate2 + $time2 + ($break2 ? $break2['long'] : 0)
			];
			
			return false;
		} else {
			
			$old_lady = [1.5, 2, 2.25, 2.5, 2.75, 3];
			$tg = $old_lady[array_rand($old_lady)];
			//$tg = rand(2, 3);
			shuffle($hours);
			shuffle($minutes);
			$time1 = ($hours[0] + $minutes[0])-$tg;
			$time2 = $tg;
			$exist[] = $date;
			
			$sdate1 = rand_start($time1 < 5 ? $shours_short : $shours_long, $minutes);
			$break1 = $time1 > 4 ? rand_break($sdate1, $time1) : [];
			
			$sdate2 = rand_start($time2 < 5 ? $shours_short : $shours_long, $minutes);
			$break2 = $time2 > 4 ? rand_break($sdate2, $time2) : [];
			$working_time[] = [
				'name' => $staffs[array_rand($staffs)],
				'per_hour' => $per_hour,
				'date' => $date, 
				'working-time' => $time1, 
				'start-time' => $sdate1,
				'break' => $break1,
				'end-time' => $sdate1 + $time1 + ($break1 ? $break1['long'] : 0)
			];
			$working_time[] = [
				'name' => 'Old lady',
				'per_hour' => $per_hour,
				'date' => $date, 
				'working-time' => $time2, 
				'start-time' => $sdate2,
				'break' => $break2,
				'end-time' => $sdate2 + $time2 + ($break2 ? $break2['long'] : 0)
			];
			$total_hours -= $time1+$time2;
			$days -= 1;
			$i++;
			shuffle_hours($total_hours, $days, $start, $end, $uid, $per_hour);
		}
	} elseif (!$total_hours) {
		return false;
	} elseif ($days < $i AND $total_hours > 0) {
		$working_time = [];
		$exist = [];
		shuffle_hours($th[0], $th[1], $start, $end, $uid, $per_hour);
	}
}

function rand_date($s, $f, &$e) {
	$d = date('Y-m-d', mt_rand(strtotime($s), strtotime($f)));
	return in_array($d, $e) ? rand_date($s, $f, $e) : $d;
}

function rand_start($hours, $minutes) {
	shuffle($hours);
	shuffle($minutes);
	return $hours[0] + $minutes[0];
}

function rand_break($s, $t) {
	global $minutes;
	//$break = rand(1,10);
	//if ($break > 5) {
		shuffle($minutes);
		$bs = rand($t > 6 ? 3 : 1,intval($t) - 2) + $minutes[0];
		shuffle($minutes);
		$bl = rand_long(rand(0,1), $minutes[0]);
		
		return [
			'start' => $s + $bs,
			'end' => $s + $bs + $bl,
			'long' => $bl
		];
	//} else
	//	return [];
}

function rand_long($h, $m) {
	global $minutes;
	shuffle($minutes);
	return $h + $m ?: rand_long(rand(0,1), $minutes[0]);
}

function time_format($t) {
	$hs = intval($t);
	$ms = ($t - intval($t))*60;
	return ($hs < 10 ? '0'.$hs : $hs).':'.($ms < 10 ? '0'.$ms : $ms).':00';
	
}

$start_date = new DateTime('14.01.2014');
//$end_date = new DateTime('10.09.2020');
$end_date = new DateTime('15.03.2017');
$otto_date = new DateTime('10.09.2020');

$date = clone $start_date;


$xls_data = '';


//echo '<pre>';
// Создаем периоды
$week = 0;
while ($date <= $end_date) {
	$date_began = $date->format('Y-m-d');
	$date->modify('+6 day');
	$date_end = $date->format('Y-m-d');
	$amount = $amounts[array_rand($amounts, 1)];
	$hourss = $amounts_hours[$amount];
	$per_hour = $amount/$hourss;
	$working_time = [];
	$exist = [];
	$th = [time_format($hourss), 5];
	$i = 0;
	$first = 0;
	$week ++;
	
	if(strtotime($date_end) >= strtotime($otto_date->format('Y-m-d'))){
		unset($staffs[1]);
	}
	shuffle_hours(time_format($hourss*3600), 5, $date_began, $date_end, 1, $per_hour);
	print_r($working_time);
	die;
	if($working_time) {
		$users = [];
		foreach($working_time as $k => $wt) {
			/* echo 'date: '.$wt['date'].' '.time_format($wt['start-time']).'<br>';
			echo 'control-point: '.$wt['date'].' '.time_format($wt['end-time']).'<br>';
			echo 'break-start: '.($wt['break'] ? $wt['date'].' '.time_format($wt['break']['start']) : '').'<br>';
			echo 'break-end: '.($wt['break'] ? $wt['date'].' '.time_format($wt['break']['end']) : '').'<br>';
			echo 'senconds: '.($wt['working-time']*3600).'<br>';
			echo 'per_hour: '.$wt['per_hour'].'<br>'.'<br>'; */

				
			if (!$users[$wt['name']])
				$users[$wt['name']] = ['html' => '', 'total' => 0];
			$users[$wt['name']]['html'] .= '<ss:Row>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$wt['date'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.time_format($wt['start-time']).'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.time_format($wt['break']['start']).'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.time_format($wt['break']['end']).'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.time_format($wt['end-time']).'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.time_format($wt['working-time']).'</ss:Data></ss:Cell>
						</ss:Row>';
			$users[$wt['name']]['total'] += $wt['working-time'];
			 
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
					</ss:Row>' : '').'
					<ss:Row>
						<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.$k.'</ss:Data></ss:Cell>
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
						<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.time_format($u['total']).'</ss:Data></ss:Cell>
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