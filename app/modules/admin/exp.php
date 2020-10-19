<?php
//echo $config['device_form'];
//die;
echo '<pre>';

$page = intval($_GET['page']);
$count = 20;

$sql = db_multi_query('SELECT id FROM `'.DB_PREFIX.'_invoices` WHERE conducted = 1 AND `date` >= \'2018-01-01 00:00:00\' AND paid > 0 ORDER BY `date` LIMIT '.($page*$count).', '.$count, true);

foreach($sql as $row){
	$page = 'https://yoursite.com/invoices/print/'.$row['id'].'?without_tax';
	echo $page;
	echo get_curl_page($page)['content'];
	die;
}

echo $sql ? '<script>location.href = \'/exp?page='.($page+1).'\';</script>' : 'complete';

die;

	echo '<pre>';
	$list = [];
	$total = 0;
	//print_r(db_multi_query('SELECT usr.name, usr.lastname FROM `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_invoices` inv INNER JOIN `'.DB_PREFIX.'_users` usr ON iss.customer_id = usr.id WHERE iss.object_owner > 0 LIMIT 10', true));
	//print_r(db_multi_query('SELECT inv.* FROM `'.DB_PREFIX.'_issues` iss INNER JOIN `'.DB_PREFIX.'_invoices` inv ON iss.invoice_id = inv.id AND inv.date >= \'2017-06-21 00:00:00\' AND inv.conducted = 1 INNER JOIN `'.DB_PREFIX.'_users` usr ON iss.customer_id = usr.id WHERE iss.object_owner > 0 LIMIT 10', true));
	foreach(db_multi_query('SELECT usr.name, usr.lastname, inv.date, inv.total, inv.tradein_info FROM `'.DB_PREFIX.'_invoices` inv INNER JOIN `'.DB_PREFIX.'_users` usr ON inv.customer_id = usr.id WHERE `date` >= \'2017-01-01 00:00:00\' AND `total` < 0 AND `paid` < 0 AND `pay_method` = \'cash\' ORDER BY inv.date', true) as $row){
		if($row['tradein_info']){
			$tradein_info = json_decode($row['tradein_info'], true);
			$row['tradein_info'] = array_shift($tradein_info)['name'];
			$prc = $row['total']*0.1;
			$paid = number_format(($row['total']+$prc), 2, '.', '');
			$list[] = [
				'Customer name' => $row['name'].' '.$row['lastname'],
				'Paid' => '$'.$paid,
				'Device' => $row['tradein_info'],
				'Date' => $row['date'],
			];
			$total = $total+$paid;			
		}
		//print_r($row);
	}

$fp = fopen(ROOT_DIR.'/tradein.csv', 'w');

fputcsv($fp, [
	'Customer name',
	'Paid',
	'Inventory',
	'Date',
], ';', '"');

foreach ($list as $fields) {
    fputcsv($fp, $fields, ';', '"');
}

fputcsv($fp, [
	'Total:',
	' ',
	' ',
	'$'.$total,
], ';', '"');

fclose($fp);

echo '<a href="/tradein.csv" download>download</a>';
echo $total;
die;
?>