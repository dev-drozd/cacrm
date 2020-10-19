<?php
die;
include APP_DIR.'/classes/genvoices.php';

$periods = [
	'09/01/2014-11/30/2014' => 140000,
	'12/01/2014-02/28/2015' => 147651,
	'09/01/2015-11/30/2015' => 187412,
	'12/01/2015-02/29/2020' => 167893,
	'03/01/2020-05/31/2020' => 150330,
	'06/01/2020-08/31/2020' => 127138,
	'09/01/2020-11/30/2020' => 179844
];

$data = [];

$store_amount = [];

foreach($periods as $period => $amount){
	$period = explode('-', $period);
	Genvoices::period($period[0],$period[1]);
	foreach(Genvoices::generate($amount, 'services') as $invoice){
		$store_id = array_rand(Genvoices::$stores);
		$data[$store_id.'-'.strtotime($period[0]).'-'.strtotime($period[1])][] = $invoice;
		$store_amount[$store_id.'-'.$period[0].'-'.$period[1]] += $invoice['total'];
	}
}

foreach($data as $filename => $row){
	$arr = json_decode(file_get_contents(ROOT_DIR.'/uploads/invoices/'.$filename.'.json'), true);
	$invoices = [];
	foreach($row as $k => $a){
		$invoices[$k] = $a['date'];
	}
	array_multisort($invoices, SORT_STRING, $row);
	$arr['services'] = $row;
	file_put_contents(ROOT_DIR.'/uploads/invoices/'.$filename.'.json', json_encode($arr));
}

echo '<pre>';

print_r($store_amount);
//print_r($data);
die;
?>