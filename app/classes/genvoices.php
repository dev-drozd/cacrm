<?php

class Genvoices {
	
	static public 
	$days = [],
	$customers = [],
	$stores = [
		2 => 'Albany',
		5 => 'East Greenbush',
		3 => 'Clifton Park',
		4 => 'Schenectady',
		7 => 'Brooklyn, Williamsburg'
	];
	
	static private 
	$purchases = [[
		[50,100],
		[150,200],
		[250,300],
		[350,400,450,500],
		[550,600,650,700,750,800]
	],[
		[
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
		[
			'Samsung Galxay S5 LCD',
			'TOSHIBA Satellite C55D-A C55D-A5304 AMD Laptop Motherboard',
			'MSI ATX Motherboard Motherboards Z370-A PRO',
			'Corsair Vengeance 16GB (2x8GB) DDR4 3000',
			'HP Pavilion G7-2000 Motherboard',
			'Dell Inspiron 15 3558 Laptop Motherboard',
			'MacBook Air SSD for Sayeed Sadat',
			'Samsung Galaxy S7 G930 screen'
		],
		[
			'Silicon-Power-240GB SSD',
			'Samsung Note 5 LCD',
			'Intel Core i7-7800X Processor',
			'MSI Pro Series Intel X299 LGA 2066 DDR4 USB 3.1 SLI ATX Motherboard',
			'Apple MacBook Pro 13',
			'Full Assembly for Manny Choy q504ua'
		],
		[
			'Apple iPhone 6  64GB MainBoard',
			'NEW Dell Alienware 17 18 Video Graphics Card WV6W6 Nvidia GeForce',
			'Asus Q551L Motherboard',
			'WD Blue 250GB PC SSD - SATA 6',
			'WD Blue 250GB Internal SSD'
		],
		[
			'Motherboard for A1708'
		]
	]], 
	$services = [[
		[40,50],
		[60,80,100],
		[150,200],
		[250,300],
		[350,400,450,500],
		[600,700,800,900,1000],
		[1300,1500],
		[1800,2000],
		[2500,2800,3000]
	], [[
			'Replace blown Caps',
			'In-depth Diagnostics',
			'Remote Support initial 30min',
			'Flash BIOS (Any Device)',
			'Charging port replacement'
		],
		[
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
		[
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
		[
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
		[
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
		[
			'10 Hour Consultation'
		],
		[
			'10 Block Hours On site'
		],
		[
			'20 Block Hours On site'
		],
		[
			'20 Block Hours On site and consultation'
		]
	]];
	
	static public function period($a,$b){
		$begin = new DateTime($a);
		$end = new DateTime($b);
		$end = $end->modify('+1 day'); 
		$interval = new DateInterval('P1D');
		$daterange = new DatePeriod($begin, $interval ,$end);
		foreach($daterange as $date){
			$day = $date->format("Y-m-d");
			if(date('N', strtotime($day)) != 7){
				self::$days[] = $day;
			}
		}
		return self::$days;
	}
	
	static public function generate($a,$b,$c = 0){
		$total_amount = 0;
		$items = [];
		$count_cash = $a*0.05;
		while(true){
			if($total_amount < $a){
				$key = array_rand(self::$$b[0]);

				$amount = self::$$b[0][$key][array_rand(self::$$b[0][$key])];
				
				if($c){
					$tax = $amount*floatval($c);
					$total = $amount+$tax;
				} else
					$total = $amount;
				
				if(($total_amount+$total) > $a){
					if($c){
						$total = $a-$total_amount;
						$tax = $total*floatval($c);
						$amount = $total-$tax;
					} else {
						$total = $a-$total_amount;
						$tax = 0;
						$amount = $total;	
					}
				}
				
				$total_amount += $total;
				if($customer_key = array_rand(self::$customers)){
					$customer_name = self::$customers[$customer_key];
					unset(self::$customers[$customer_key]);
				}
				
				if($count_cash > 0){
					$type = 'cash';
					$count_cash -= $total;
				} else
					$type = 'credit';
				
				$items[] = [
					'id' => uniqid(),
					'customer' => self::$customers[array_rand(self::$customers)],
					'amount' => $amount,
					'tax' => floatval($tax),
					'date' => self::$days[array_rand(self::$days)],
					'type' => $type,
					'total' => $total,
					'text' => self::$$b[1][$key][array_rand(self::$$b[1][$key])]
				];
			} else
				break;
		}
		return $items;
	}
}
db_multi_query('SELECT name, lastname FROM `'.DB_PREFIX.'_users` WHERE group_ids = 5 LIMIT 5000', true, false, function($a){
	Genvoices::$customers[] = $a['name'].' '.$a['lastname'];
});
?>