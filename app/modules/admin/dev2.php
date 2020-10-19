<?php

compressImage(ROOT_DIR.'/4.png');
echo '<img src="/4.png?t='.time().'">  590.66 KB TO 42.81 KB ';
echo '<img src="/min_3.jpg?t='.time().'">';

die;

$usr = [
	'id' => 26494,
	'name' => 'Ota',
	'lastname' => 'Greven'
];

$arr = [300, 350, 400, 450, 500, 550, 700];
$arr2 = [
	300 => 30,
	350 => 35,
	400 => 40,
	450 => 40,
	500 => 40,
	550 => 40,
	600 => 40,
	650 => 40,
	700 => 40
];

function time_format($t) {
	$hs = intval($t);
	$ms = ($t - intval($t))*60;
	return ($hs < 10 ? '0'.$hs : $hs).':'.($ms < 10 ? '0'.$ms : $ms).':00';
	
}

$start_date = new DateTime('14.01.2014');
$end_date = new DateTime('10.09.2020');

$date = clone $start_date;

while ($date <= $end_date) {
	$date_began = $date->format('Y-m-d');
	$date->modify('+6 day');
	$date_end = $date->format('Y-m-d');
	$amount = $arr[array_rand($arr, 1)];
	$hours = $arr2[$amount];
	$per_hour = $amount/$hours;
	db_query('INSERT INTO `CA_payroll` SET user_id = '.$usr['id'].', per_hour = \''.$per_hour.'\', 	amount = \''.$amount.'\', first_name = \''.$usr['name'].'\', last_name = \''.$usr['lastname'].'\', date_end = \''.db_escape_string($date_end).'\', date_began = \''.db_escape_string($date_began).'\', total_hours = \''.time_format($hours).'\', hash = MD5(\''.$usr['name'].$usr['lastname'].$hours.$date_began.$date_end.time().'\')');
	$date->modify('+1 day');
}

echo 'Complete: ';

die;
$page = (int)$_GET['page'];
$location = false;
$users = db_multi_query('SELECT SQL_CALC_FOUND_ROWS * FROM `CA_tmp_data` WHERE 1 LIMIT '.($page*50).', 50', true);
$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
$i = 0;
foreach($users as $row) {
	if(!$row['hours']) continue;
	$name = explode(' ', str_replace('*', '', $row['name']));
	if($user = db_multi_query('SELECT id FROM `CA_users` WHERE name = \''.db_escape_string($name[0]).'\' AND lastname = \''.db_escape_string($name[1]).'\''))
		$id = $user['id'];
	else {
		db_query('INSERT INTO `'.DB_PREFIX.'_users` SET 
			email = \''.db_escape_string($row['email']).'\', 
			name = \''.db_escape_string($name[0]).'\',
			lastname = \''.db_escape_string($name[1]).'\',
			group_ids = 4,
			del = 1
		');
		$id = intval(mysqli_insert_id($db_link));
	}
	echo 'id: '.$id.'<br>';
	$date_end = date('Y-m-d', strtotime($row['date']));
	$date_began = date('Y-m-d', strtotime('-6 days', strtotime($row['date'])));
	db_query('INSERT INTO `CA_payroll` SET user_id = '.$id.', first_name = \''.db_escape_string($name[0]).'\', last_name = \''.db_escape_string($name[1]).'\', date_end = \''.db_escape_string($date_end).'\', date_began = \''.db_escape_string($date_began).'\', total_hours = \''.time_format(str_replace(',','.', $row['hours'])).'\', hash = MD5(\''.$name[0].$name[1].$row['hours'].$date_began.$date_end.time().'\')');
	$i++;
	$location = true;
}
echo $location ? 'Осталось: '.($res_count-$i).' <script>setTimeout(function(){location.href = \'/dev2?page='.($page+1).'\';}, 300);</script>' : 'Готово';
die;
?>