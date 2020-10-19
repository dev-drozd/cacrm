<?php

// Дата начала
$start_date = '2014-09-01';

// Дата конца
$end_date = '2014-11-30';

// Сумма услуг
$summ_services = 52301;
$summ_purchases = 140000;

// Магазины
$stores = [
	2 => 'Albany',
	5 => 'East Greenbush',
	3 => 'Clifton Park',
	4 => 'Schenectady',
	7 => 'Brooklyn, Williamsburg'
];

// Виды дивайсов и их стоимость
$purchases = [
	'0-100' => [
		'A1375 Battery For Apple Macbook Air 11',
		'OEM LCD Display Touch iPhone',
		'Galaxy S5 SM-G900T Battery',
		'LED LCD Screen N156BGE-EA1',
		'Dell Inspiron 15 3558 Laptop Motherboard',
		'Battery For HP Envy HSTNN-LB6J',
		'Toshiba DC Jack',
		'Apple MacBook Pro A1286',
		'HP ENVY TOUCHSMART M7  Palmrest',
		'Easy To Shop 2840mAh 3.8V Internal Battery',
		'Toshiba Satellite C55T-B Rear LCD'
	],
	'101-200' => [
		'Samsung Galxay S5 LCD',
		'TOSHIBA Satellite C55D-A C55D-A5304 AMD Laptop Motherboard',
		'MSI ATX Motherboard Motherboards Z370-A PRO',
		'Corsair Vengeance 16GB (2x8GB) DDR4 3000',
		'HP Pavilion G7-2000 Motherboard',
		'Dell Inspiron 15 3558 Laptop Motherboard',
		'MacBook Air SSD for Sayeed Sadat',
		'Samsung Galaxy S7 G930 screen'
	],
	'201-300' => [
		'Silicon-Power-240GB SSD',
		'Samsung Note 5 LCD',
		'Intel Core i7-7800X Processor',
		'MSI Pro Series Intel X299 LGA 2066 DDR4 USB 3.1 SLI ATX Motherboard',
		'Apple MacBook Pro 13',
		'Full Assembly for Manny Choy q504ua'
	],
	'301-500' => [
		'Apple iPhone 6  64GB MainBoard',
		'NEW Dell Alienware 17 18 Video Graphics Card WV6W6 Nvidia GeForce',
		'Asus Q551L Motherboard',
		'WD Blue 250GB PC SSD - SATA 6',
		'WD Blue 250GB Internal SSD'
	],
	'501-800' => [
		'Motherboard for A1708'
	]
];


// Виды услуг и их стоимость
$services_ammount = [50,70,80,90,100,150,200,250,400,450,500,550,600,650,700,750,800,850,900,950,1000,1500,2000];
$services = [
	'0-50' => [
		'Replace blown Caps',
		'In-depth Diagnostics',
		'Remote Support initial 30min',
		'Flash BIOS (Any Device)',
		'Charging port replacement'
	],
	'51-100' => [
		'iPhone software reinstall',
		'Corrupted files repair',
		'Repair motherboard',
		'Cable Management',
		'Screen repair',
		'One Hour onsite technical support',
		'Expedited Shipping',
		'Systems analysis',
		'Windows maintenance',
		'Tablet Screen repair',
		'Printer Cleaning (Full Cleaning)',
		'1 Hour Consultation In Store',
		'System tune up',
		'Software support services',
		'Basic data transfer',
		'Hardware reset',
		'Light tune up',
		'Installation of customer provided program',
		'Thermal paste application',
		'Board level rewiring',
		'Game installation',
		'Software clone',
		'Basic residential computer consultation',
		'malware removal',
		'Malware protection configuration',
		'Spyware removal',
		'Dust clean up',
		'System over heating repair',
		'Specialty os install',
		'Linx os configuration',
		'Linux customization',
		'Linux upgrade'
	],
	'101-200' => [
		'Mac keyboard repair',  
		'Basic Data recovery',
		'Data back up and Transfer',
		'2 Hour On-Site technical support (more of this)',
		'Hard Drive Reinstall with OS',
		'Consulting',
		'Instruction',
		'Windows 10 installing',
		'Reactivate Office/Windows',
		'Update software',
		'Macbook Screen Assembly Replacement',
		'Basic water damage repair',
		'Update software',
		'Advanced hardware reset',
		'Advanced tune up',
		'Charging port repair',
		'Advanced GPU reflow',
		'Advanced software clone',
		'Basic Digital foresics',
		'Head phone jack repair',
		'Repair audio problems',
		'Repair video problems',
		'Install customer provided hardware',
		'Computer consulting',
		'Reattach lcd glass assembly',
		'Virus removal',
		'Virus removal and security',
		'advanced malware and spyware removal',
		'Advanced malware, spyware, and virus removal',
		'Trojan horse removal and support',
		'Advanced compromised security support',
		'Repair computer video issues',
		'Repair computer sound issues'
	],
	'201-300' => [
		'Reflow motherboard (Laptop)',
		'Data recovery',
		'3 hour onsite technical support( more of this)',
		'Advanced Data Recovery',
		'Software Training',
		'Windows troubleshooting',
		'Virtual Machine Configuration',
		'Board Level Solder Repair',
		'Advanced Water damage repair',
		'Digital forensics',
		'Engineering and technical support',
		'Sql database restore',
		'Deploy provided business software',
		'off site databackup',
		'business computer consulting',
		'custom building customer provided system hardware',
		'tune up and virus removal',
		'Advanced system tune up',
		'System virtualization'
	],
	'301-500' => [
		'Advanced Data recovery',
		'Macbook motherboard Repair',
		'Spectrascan Repair',
		'Asus motherboard reflow',
		'Firewall programming',
		'Security consulting',
		'Hard Drive repair with backup files',
		'Advanced digital foresnsics',
		'Advanced business computer consulting'
	],
	'501-1000' => [
		'10 Hour Consultation'
	],
	'1001-1500' => [
		'10 Block Hours On site'
	],
	'1501-2000' => [
		'20 Block Hours On site'
	],
	'2001-3000' => [
		'20 Block Hours On site and consultation'
	]
];

$begin = new DateTime($start_date);
$end = new DateTime($end_date);
$end = $end->modify('+1 day'); 

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval ,$end);

// Массив для сбора дней
$days = [];

// Отфильтровываем выходные дни
foreach($daterange as $date){
	$day = $date->format("Y-m-d");
	if(date('N', strtotime($day)) != 7){
		$days[] = $day;
	}
}

// Деленные дни
$delday = 6;

// сумма за 6 дней
$delday_summ_service = round($summ_services/count($days)*$delday);
$delday_summ_purchases = round($summ_purchases/count($days)*$delday);

$invoice_services = [];
$invoice_purchases = [];

// Статус цикла инвойсов
$break = 2;

// Генерация инвойсов на сумм за $delday_summy дней
while($break > 0){
	
}

echo round($summ/count($days)*6);
die;

$ammount = 0;
$data = [];
while(true){
	$k = array_rand($days);
	if($k > 0 OR $k === 0){
		$am = $services_ammount[array_rand($services_ammount)];
		$ammount += $am;
		$data[$days[$k]] = $am;
		//echo $days[$k]."<br>";
		unset($days[$k]);
	} else
		break;
}
echo '<pre>';
echo $ammount;
print_r($data);

	echo 'OK';
die;
?>