<?php

$stores = [
	2 => 'Albany',
	5 => 'East Greenbush',
	3 => 'Clifton Park',
	4 => 'Schenectady',
	7 => 'Brooklyn, Williamsburg'
];

function get_itpl($a,$b,$c){
	return '<div class="tr">
		<div class="td isItem">
			 '.$a.($c == 'sv' ? '<p>surcharge charge included</p>' : '').'
		</div>
		<div class="td w10">
			1
		</div>
		<div class="td w100 nPay">
			$'.number_format($b, '2', '.', '').'
		</div>
		<div class="td w10">
			'.($c == 'in' ? 'yes' : 'no').'
		</div>
	</div>';
}

function get_purchase($a,$b,$c,$d,$e){
	
	$invoices_data = file_get_contents(ROOT_DIR.'/uploads/invoices'.$c.'.json');
	
	if($a > 500)
		$n = '501-800';
	else if($a > 300 && $a <= 500)
		$n = '301-500';
	else if($a > 200 && $a <= 300)
		$n = '201-300';
	else if($a > 100 && $a <= 200)
		$n = '101-200';
	else $n = '0-100';
	
	if($c == 'in'){
		$p = [
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
	} else {
		
		if($a > 2000)
			$n = '2001-3000';
		else if($a > 1500 && $a <= 2000)
			$n = '1501-2000';
		else if($a > 1000 && $a <= 1500)
			$n = '1001-1500';
		else if($a > 500 && $a <= 1000)
			$n = '501-1000';
		else if($a > 300 && $a <= 500)
			$n = '301-500';
		else if($a > 200 && $a <= 300)
			$n = '201-300';
		else if($a > 100 && $a <= 200)
			$n = '101-200';
		else if($a > 50 && $a <= 100)
			$n = '51-100';
		else
			$n = '0-50';
		
		$p = [
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
	}
	
	if(!$invoices_data){
		$invoices_data = [
			$b => $p[$n][array_rand($p[$n], 1)]
		];
		file_put_contents(ROOT_DIR.'/uploads/invoices'.$c.'.json', json_encode($invoices_data));
	} else if(($invoices_data = json_decode($invoices_data, true)) && !$invoices_data[$b]){
		$invoices_data[$b] = $p[$n][array_rand($p[$n], 1)];
		file_put_contents(ROOT_DIR.'/uploads/invoices'.$c.'.json', json_encode($invoices_data));
	}
	//return $invoices_data[$b];
	
	return get_itpl($invoices_data[$b], ($c == 'sv' ? ($a+$d) : $a), $c);
}

switch($route[1]){
	
	case 'export':
		$csv = '/uploads/stores/'.(
			isset($_GET['service']) ? 's_' : ''
		).$_GET['store_id'].'.csv';
		$data = file(ROOT_DIR.$csv);
		$output = fopen('php://output', 'w');
		
		fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
		
		$amount = 0;
		$i = 0;

		foreach($data as $a){
			$a = explode(';', $a);
			if($i == 0){
				fputcsv($output, ['Taxable/notaxable','Invoice #','Subtotal','Tax','Total','Date'],';');
				$i++;
				continue;
			} else {
				if($json = getCurl('https://yoursite.com/reports/print/4822&json')){
					
					print_r($json);
					die;
					$j = json_decode($json, true);
					fputcsv($output, [isset($_GET['service']) ? 'No' : 'Yes', $a[0],$j['subtotal'],$j['tax'],str_replace('.',',',$a[1]),$a[2]],';');
				} else {
					echo 'ERR';
				}
			}
			$a = explode(';', $a);
			$amount += $a[1];
		}
		
		fclose($output);
		
		$file_size = filesize($output);
		
		header('Content-Type: text/plain');
		//header('Content-Type: text/csv');
		//header('Content-Disposition: attachment;filename='.$stores[$_GET['store_id']].'-2020-12-20-2017-02-28.csv');
		//header("Content-Length: " . $file_size);
		readfile($output);
		die;
	break;
	
	case 'test':
		$data = file(ROOT_DIR.'/uploads/stores/export3.csv');
		$amount = 0;
		$objects = [];
		$object_amounts = [];
		foreach($data as $a){
			$a = explode(';', $a);
			$amount += $a[1];
			$inv = db_multi_query('SELECT *, DATE_FORMAT(date, \'%d.%m.%Y %H:%i\') as date FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$a[0]);
			$objects[$inv['object_id']][] = [$inv['id'],$inv['total'],$inv['date'],$inv['pay_method']];
			$object_amounts[$inv['object_id']] += $inv['total'];
		}
		$total = 0;
		//foreach($object_amounts as $am){
		//	$total += $am;
		//}
		//echo '$'.number_format($amount, '2', '.', '');
		echo '<pre>';
		
		
		//print_r($objects);
		
		$files = [];
		
		foreach($objects as $k => $v){
			$files[$k] = fopen(ROOT_DIR.'/uploads/stores/s_'.$k.'.csv', 'w');
			echo 'Store:'.$k.'<br>';
			foreach($v as $fields){
				print_r($fields);
				fputcsv($files[$k], $fields, ";");
			}
			fclose($files[$k]);
		}
		
		print_r($object_amounts);
		print_r(number_format(floor($total), '2', '.', ''));
		die;
	break;
	
	case 'in_store':
	case 'sv_store':
		$store_id = (int)$route[2];
		//$csv = array_map(function($a){
		//	return explode(';', $a)[0];
		//}, file(ROOT_DIR.'/uploads/stores/'.$store_id.'.csv'));
		$csv = '/uploads/stores/'.(
			$route[1] == 'sv_store' ? 's_' : ''
		).$store_id.'.csv';
		$data = file(ROOT_DIR.$csv);
		$amount = 0;
		echo '<style>tr > td, th {padding: 8px; }tr:nth-child(even) > td {background: #F7F8FA;}</style>';
		echo '<table style="width: 700px; border-collapse: collapse; border: 0; border-color: #ddd;margin: 60 auto;" border="1">';
		echo '<tr><td colspan="4" style=" text-align: center;position: relative;"><h1>Store '.$stores[$store_id].'</h1><a href="https://gdr.one/careports.php?store_id='.$store_id.(
			$route[1] == 'sv_store' ? '-1' : ''
		).'" style="position: absolute;right: 10px;top: 10px;">Export</a></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: center; "><h2>Period: 2020-12-20 - 2017-02-28</h2></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>#</th>
				<th>Price</th>
				<th>Data</th>
				<th>type</th>
			</tr>';
		$i = 0;
		foreach($data as $a){
			if($i == 0){
				$i++;
				continue;
			}
			$a = explode(';', $a);
			$amount += $a[1];
			echo '
				<tr>
					<td><a href="/reports/print/'.$a[0].'" target="__blank">#'.$a[0].'</a></td>
					<td>$'.$a[1].'</td>
					<td>'.$a[2].'</td>
					<td>'.$a[3].'</td>
				</tr>
			';
		}
		echo '<tr style="text-align: right;"><td colspan="3"><h3>Total amount:</h3></td><td><h3>$'.number_format($amount, '2', '.', '').'</h3></td></tr>';
		echo '</table>';
	break;
	
	case 'print':
		//if(!in_array('1', explode(',', $user['group_ids']))){
		//	echo 'Access denied';
		//	die;
		//}

		$ids = [];
		foreach([2,3,5,4,7] as $sid){
			foreach(file(ROOT_DIR.'/uploads/stores/'.$sid.'.csv') as $a){
				$ids[] = explode(';', $a)[0];
			}
		}
	
		$id = intval($route[2]);
		$type = in_array($id, $ids) ? 'in' : 'sv';
		
		$meta['title'] = 'Invoice';
		$pur_ids = [];
		$total = 0;
		$tradein = 0;
		$tax = 0;
		$has_purchase = 0;

		$invoices_arr = [];
        $onsite = '';
        $onsite_total = 0;
        $tradein_total = 0;
        $discount = [];
        $html = [];
        
		if($row = db_multi_query('
			SELECT
				i.*,
					u.name as customer_name,
					u.lastname as customer_lastname,
					u.address as customer_address,
					u.email as customer_email,
					u.phone as customer_phone,
					u.zipcode as zipcode,
                        o.tax as object_tax,
                        o.name as object_name,
                        o.address as object_address,
                        o.phone as object_phone,
                            c.city as city_name,
								os.name as onsite_name,
								os.price as onsite_price,
								os.description as onsite_description,
								uos_info.name as user_onsite_name,
								SEC_TO_TIME(uos.left_time * (-1)) as user_onsite_time,
									su.name as staff_name,
									su.lastname as staff_lastname,
										ri.id as refund_id,
											d.name as delivery,
											d.price as delivery_price,
											d.currency as delivery_currency,
												sd.amount as store_discount_amount
			FROM `'.DB_PREFIX.'_invoices` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
			ON i.customer_id = u.id
				LEFT JOIN `'.DB_PREFIX.'_invoices` ri
			ON ri.refund_invoice = i.id
				LEFT JOIN `'.DB_PREFIX.'_users` su
			ON i.staff_id = su.id	
				LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = i.object_id
				LEFT JOIN `'.DB_PREFIX.'_cities` c
			ON c.zip_code = u.zipcode
				LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
			ON uos.id = i.add_onsite
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
			ON uos_info.id = uos.onsite_id
                LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
			ON os.id = i.onsite_id
				LEFT JOIN `'.DB_PREFIX.'_orders` ord
			ON ord.id = i.order_id
				LEFT JOIN `'.DB_PREFIX.'_orders_delivery` d
			ON d.id = ord.delivery_id
				LEFT JOIN `'.DB_PREFIX.'_store_discounts` sd
			ON sd.id = i.store_discount
				WHERE i.id = '.$id
		)){
			if ($row['onsite_id']) {
                $onsite .= '<div class="tr" data-id="'.$row['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$row['onsite_id'].'">
                    <div class="td">
                        '.$row['onsite_name'].'
                        <br><i>'.$row['onsite_description'].'</i>
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_price">
                        '.$config['currency'][$row['currency']]['symbol'].$row['onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['onsite_price']);
                $onsite_total += floatval($row['onsite_price']);
            }
            if ($row['add_onsite']) {
                 $onsite .= '<div class="tr" data-id="'.$row['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$row['add_onsite'].'">
                    <div class="td">
                        '.$row['user_onsite_name'].'(Additional time - '.$row['user_onsite_time'].')
                    </div>
                    <div class="td w10">
                        1
                    </div>
                    <div class="td w100 onsite_add_price">
                        '.$config['currency'][$row['currency']]['symbol'].$row['add_onsite_price'].'
                    </div>
                    <div class="td w10">
                        no
                    </div>
                </div>';
                $total += floatval($row['add_onsite_price']);
                $onsite_total += floatval($row['add_onsite_price']);
            }
			
            if ($row['discount'])
                $discount = array_values(json_decode(($row['discount'] ?: '{}'), true));

            $issue_mhtml = '';
			
			/* if($user['id'] == 17) {
						echo '<pre>';
						print_r($row['issue_info']);
						die;
					} */
            if ($issue = json_decode($row['issue_info'], true)) {  
                    $issue_total = 0;
                    $issue_html = '';
					
                    if ($issue['inventory']) {
                        foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
                            $issue_html .= '<div class="tr">
									<div class="td isItem">
										 <a href="/inventory/view/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price'])), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += floatval(preg_replace('/[^0-9.]/i', '', $iss_inv['price']));
                        }
                    }

                    if ($issue['services']) {
						
                        $upcharge = 0;
                        if ($issue['upcharge']) {
                            $upcharge = floatval(preg_replace('/[^0-9.-]/i', '', array_values($issue['upcharge'])[0]['price']));
							$service_len = count(array_filter($issue['services'], function($a) {
								if (floatval(preg_replace('/[^0-9.-]/i', '', $a['price'])) > 0)
									return $a;
							}));
							$upcharge /= $service_len;
						}

                        foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                            $price = floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                            $issue_html .= '<div class="tr">
									<div class="td isItem">
										 '.$iss_inv['name'].'
									</div>
									<div class="td w10">
										'.($iss_inv['quantity'] ?: '1').'
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format((($price > 0 && $upcharge) ? $price + $upcharge : $price) * ($iss_inv['quantity'] ?: 1), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += (($price > 0 && $upcharge) ? $price + $upcharge : $price) * ($iss_inv['quantity'] ?: 1);
                        }
                    }

                    if ($issue['purchases']) {
                        foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
                            $issue_html .= '<div class="tr">
									<div class="td isItem">
										 <a href="/purchases/edit/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
									</div>
									<div class="td w10">
										1
									</div>
									<div class="td w100 nPay">
										'.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price'])), 2, '.', '').'
									</div>
									<div class="td w10">
										yes
									</div>
								</div>';
                            $issue_total += floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                        }
                    }

                    $issue_mhtml .= '<div class="tr">
								<div class="td">
									<b><a href="/issues/view/'.$row['issue_id'].'" target="_blank">Issue #'.$row['issue_id'].'</a></b>
								</div> 
								<div class="td w10"></div>
								<div class="td w100"><b>'.$config['currency'][$row['currency']]['symbol'].number_format($issue_total, 2, '.', '').'</b></div>
								<div class="td w10">
									yes
								</div>
							</div>'.$issue_html;
				$issue_mhtml = $issue_html;
            }

            $total += $issue_total;

            if ($row['inventory_info']) {
                foreach(json_decode($row['inventory_info'], true) as $inv_id => $inv) {
                    $html['inventory'] .= '<div class="tr">
                            <div class="td">
                                    <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                '.($inv['items'] ?: 1).'
                            </div>
                            <div class="td w100">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }

            if ($row['services_info']) {
                foreach(json_decode($row['services_info'], true) as $inv_id => $inv) {
                    $html['services'] .= '<div class="tr">
                            <div class="td">
                                    '.$inv['name'].'
                            </div>
                            <div class="td w10">
                                '.($inv['items'] ?: 1).'
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }

            if ($row['purchases_info']) {
                foreach(json_decode($row['purchases_info'], true) as $inv_id => $inv) {
                    $html['purchases'] .= '<div class="tr">
                            <div class="td isem">
                                    <a href="/purchases/edit/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                1
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }

            if ($row['tradein_info']) {
                foreach(json_decode($row['tradein_info'], true) as $inv_id => $inv) {
                    $html['tradein'] .= '<div class="tr">
                            <div class="td isem">
                                    <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                            </div>
                            <div class="td w10">
                                1
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).'-'.number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                no
                            </div>
                        </div>';
                    $tradein += floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase']));
                }
            }
			
			if ($row['refund_info'] AND strlen($row['refund_info']) > 2) {
				$html['refund'] .= '<div class="tr">
                            <div class="td isem">
								<b>Refund for <a href="/invoices/view/'.$row['refund_invoice'].'" target="blank">Invoice #'.$row['refund_invoice'].'</a></b>
                            </div>
                            <div class="td w10">
                                
                            </div>
                            <div class="td w100 nPay">
                               
                            </div>
                            <div class="td w10">
                                
                            </div>
                        </div>';
                foreach(json_decode($row['refund_info'], true) as $inv_id => $inv) {
                    $html['refund'] .= '<div class="tr">
                            <div class="td isem">
								'.$inv['name'].'
                            </div>
                            <div class="td w10">
                                '.($inv['items'] ?: 1).'
                            </div>
                            <div class="td w100 nPay">
                                '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
					if ($inv['type'] == 'onsite')
						$onsite_total += floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                }
            }
			
            if ($row['addition_info']) {
                foreach(json_decode($row['addition_info'], true) as $adt_id => $adt) {
                    $html['additions'] .= '<div class="tr">
                            <div class="td">
                                    '.$adt['name'].'
                            </div>
                            <div class="td w10">
                                '.($adt['quantity'] ?: 1).'
                            </div>
                            <div class="td w100">
                                '.($config['currency'][$adt['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($adt['quantity'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $adt['price'])), 2, '.', '').'
                            </div>
                            <div class="td w10">
                                yes
                            </div>
                        </div>';
                    $total += ($adt['quantity'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $adt['price']));
                }
            }

			$total *= (100 - $row['store_discount_amount']) / 100;
			
            $invoices_html = '';
            $invoice_discount = [];
            if ($row['invoices']) {
                if ($invoices = db_multi_query('
					SELECT 
						i.*,
						sd.amount as store_discount_amount,
						os.name as onsite_name,
						os.price as onsite_price,
						os.description as onsite_description,
						uos_info.name as user_onsite_name
					FROM `'.DB_PREFIX.'_invoices` i 
					LEFT JOIN `'.DB_PREFIX.'_store_discounts` sd
						ON sd.id = i.store_discount
					LEFT JOIN `'.DB_PREFIX.'_users_onsite` uos
						ON uos.id = i.add_onsite
					LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` uos_info
						ON uos_info.id = uos.onsite_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_onsite` os
						ON os.id = i.onsite_id
					WHERE i.id IN('.$row['invoices'].')
				', true)) {
                    foreach($invoices as $invoice) {
                        $invoice_total = 0;
                        $issue_total = 0;
                        //$onsite_total = 0;
                        $issue_html = '';
                        $invoices_html .= '<div class="tbl payInfo">
                                    <div class="tr">
                                        <div class="th">
                                            <a href="/invoices/view/'.$invoice['id'].'" target="_blank">Invoice #'.$invoice['id'].'</a>
                                        </div>
                                        <div class="th w10">
                                            Qty
                                        </div>
                                        <div class="th w100">
                                            Amount
                                        </div>
                                        <div class="th w10">
                                            Tax
                                        </div>
                                    </div>';

						if ($invoice['onsite_id']) {
							$invoices_html .= '<div class="tr" data-id="'.$invoice['onsite_id'].'" data-type="onsite" id="tr_onsite_'.$invoice['onsite_id'].'">
								<div class="td">
									'.$invoice['onsite_name'].'
									<br><i>'.$invoice['onsite_description'].'</i>
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 onsite_price">
									'.$config['currency'][$invoice['currency']]['symbol'].$invoice['onsite_price'].'
								</div>
								<div class="td w10">
									no
								</div>
							</div>';
							$invoice_total += floatval($row['onsite_price']);
							$onsite_total += floatval($row['onsite_price']);
						}
						if ($invoice['add_onsite']) {
							 $invoices_html .= '<div class="tr" data-id="'.$invoice['add_onsite'].'" data-type="onsite" id="tr_onsite_'.$invoice['add_onsite'].'">
								<div class="td">
									'.$invoice['user_onsite_name'].'(Additional time - '.$invoice['user_onsite_time'].')
								</div>
								<div class="td w10">
									1
								</div>
								<div class="td w100 onsite_add_price">
									'.$config['currency'][$invoice['currency']]['symbol'].$invoice['add_onsite_price'].'
								</div>
								<div class="td w10">
									no
								</div>
							</div>';
							$invoice_total += floatval($row['add_onsite_price']);
							$onsite_total += floatval($row['add_onsite_price']);
						}
			
                        if ($invoice['discount'])
                            $invoice_discount = array_values(json_decode(($invoice['discount'] ?: '{}'), true));

                        if ($issue = json_decode($invoice['issue_info'], true)) {                  
                                if ($issue['inventory']) {
                                    foreach($issue['inventory'] as $iss_inv_id => $iss_inv) {
                                        $issue_html .= '<div class="tr">
                                                <div class="td isItem">
                                                    <a href="/inventory/view/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
                                                </div>
                                                <div class="td w10">
                                                    1
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price'])), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                                    }
                                }

                                if ($issue['services']) {
                                    $upcharge = 0;
                                    if ($issue['upcharge']) {
										$upcharge = floatval(preg_replace('/[^0-9.-]/i', '', array_values($issue['upcharge'])[0]['price']));
										$service_len = count(array_filter($issue['services'], function($a) {
											if (floatval(preg_replace('/[^0-9.-]/i', '', $a['price'])) > 0)
												return $a;
										}));
										$upcharge /= $service_len;
									}

                                    foreach($issue['services'] as $iss_inv_id => $iss_inv) {
                                        $price = floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                                        $issue_html .= '<div class="tr">
                                                <div class="td isItem">
                                                    '.$iss_inv['name'].'
                                                </div>
                                                <div class="td w10">
                                                    '.($iss_inv['quantity'] ?: '1').'
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += ($price > 0 ? $price + $upcharge : $price)*($iss_inv['quantity'] ?: 1);
                                    }
                                }

                                if ($issue['purchases']) {
                                    foreach($issue['purchases'] as $iss_inv_id => $iss_inv) {
                                        $issue_html .= '<div class="tr">
                                                <div class="td isItem">
                                                    <a href="/purchases/edit/'.$iss_inv_id.'" target="_blank">'.$iss_inv['name'].'</a>
                                                </div>
                                                <div class="td w10">
                                                    1
                                                </div>
                                                <div class="td w100 nPay">
                                                    '.($config['currency'][$iss_inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price'])), 2, '.', '').'
                                                </div>
                                                <div class="td w10">
                                                    yes
                                                </div>
                                            </div>';
                                        $issue_total += floatval(preg_replace('/[^0-9.-]/i', '', $iss_inv['price']));
                                    }
                                }

                                $invoices_html .= '<div class="tr">
                                            <div class="td">
                                                <b><a href="/issues/view/'.$invoice['issue_id'].'" target="_blank">Issue #'.$invoice['issue_id'].'</a></b>
                                            </div> 
                                            <div class="td w10"></div>
                                            <div class="td w100"><b>'.$config['currency'][$invoice['currency']]['symbol'].number_format($issue_total, 2, '.', '').'</b></div>
                                            <div class="td w10">
                                                yes
                                            </div>
                                        </div>'.$issue_html;
                        }

                        $invoice_total += $issue_total;
                        $invoice_html .= $issue_html;

                        if ($invoice['inventory_info']) {
                            foreach(json_decode($invoice['inventory_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td">
                                                <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
											'.($inv['items'] ?: 1).'
										</div>
										<div class="td w100">
											'.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
										</div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                            }
                        }
                        

                        if ($invoice['services_info']) {
                            foreach(json_decode($invoice['services_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td">
                                                '.$inv['name'].'
                                        </div>
                                        <div class="td w10">
                                            '.($inv['items'] ?: 1).'
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += ($inv['items'] ?: 1) * floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['purchases_info']) {
                            foreach(json_decode($invoice['purchases_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td isem">
                                                <a href="/purchases/edit/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
                                            1
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['price'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            yes
                                        </div>
                                    </div>';
                                $invoice_total += floatval(preg_replace('/[^0-9.-]/i', '', $inv['price']));
                            }
                        }

                        if ($invoice['tradein_info']) {
                            foreach(json_decode($invoice['tradein_info'], true) as $inv_id => $inv) {
                                $invoices_html .= '<div class="tr">
                                        <div class="td isem">
                                                <a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
                                        </div>
                                        <div class="td w10">
                                            1
                                        </div>
                                        <div class="td w100 nPay">
                                            '.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$row['currency']]['symbol']).'-'.number_format(floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase'])), 2, '.', '').'
                                        </div>
                                        <div class="td w10">
                                            no
                                        </div>
                                    </div>';
                                $tradein += floatval(preg_replace('/[^0-9.-]/i', '', $inv['purchase']));
                            }
                        }
                        $invoices_html .= '</div>';

                        if ($invoice['discount']) {
                            $invoice_discount = array_values(json_decode($invoice['discount'], true));
                            $invoices_html .= '<div class="tbl payInfo discount">
                                    <div class="tr">
                                        <div class="td">
                                            '.$invoice_discount[0]['name'].'
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                        <div class="td w100">
                                            -'.$invoice_discount[0]['percent'].'%
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                    </div>
                                </div>';
                        }
						
						if ($invoice['store_discount']) {
                            $invoices_html .= '<div class="tbl payInfo discount">
                                    <div class="tr">
                                        <div class="td">
                                            Discount code: '.$invoice['store_discount'].'
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                        <div class="td w100">
                                            -'.$invoice['store_discount_amount'].'%
                                        </div>
                                        <div class="td w10">
                                            
                                        </div>
                                    </div>
                                </div>';
								
							$invoice_total *= (100 - $invoice['store_discount_amount']) / 100;
                        }
						
                        $total += ($invoice_discount[0]['percent'] ? $invoice_total * (100 - $invoice_discount[0]['percent']) / 100 : $invoice_total);
                    }
                }
            }
			
			//if($tradein)
			//	$total = ($total-$tradein);
			
			//echo ($tradein * $row['object_tax'] / 100)-(($tradein * $row['object_tax'] / 100) * $row['object_tax'] / 100);
			//die;
            
			$tax = $row['purchace'] ? 0 : ($total - $tradein - $onsite_total) * $row['object_tax'] / 100; //$onsite_total + $tradein_total
			/* if ($user['id'] == 17)
				echo $total.' '.$onsite_total; */
			
			$tax = $discount[0]['percent'] ? round(
				$tax * (100 - $discount[0]['percent']
			) / 100, 2) : $tax;
			
			if($row['tax_exempt'])
				$tax = 0;
			
			$total = $discount[0]['percent'] ? round($total * (
				100 - $discount[0]['percent']) / 100, 2
			) : $total;

            
			$history_html = '';
			if ($history = db_multi_query('
				SELECT 
					i.*,
					u.name as user_name,
					u.lastname as user_lastname
				FROM `'.DB_PREFIX.'_invoices_history` i
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON u.id = i.staff_id
				WHERE i.invoice_id = '.$id, true)) {
				$history_html = '<h3 class="trLog">Transaction log</h3><div class="tbl">
								<div class="tHead">
									<div class="th">Date</div>
									<div class="th">Amount</div>
									<div class="th">Type</div>
									<div class="th">Staff</div>
								</div>
								<div class="tBody">';
				foreach($history as $h) {
					$history_html .= '<div class="tr">
										<div class="td"><span class="thShort">Date: </span>'.$h['date'].'</div>
										<div class="td"><span class="thShort">Amount: </span>'.$config['currency'][$h['currency']]['symbol'].number_format($h['amount'], 2, '.', '').'</div>
										<div class="td"><span class="thShort">Type: </span>'.$h['type'].'</div>
										<div class="td"><span class="thShort">Staff: </span><a href="/users/view/'.$h['staff_id'].'" onclick="Page.get(this.href)">'.$h['user_name'].' '.$h['user_lastname'].'</a></div>
									</div>';
				}
				$history_html .= '</div></div>';
			}
			
			$delivery = '';
			if ($row['order_id']) {
				$delivery = '<div class="tr">
							<div class="td">
								'.$row['delivery'].'
							</div>
							<div class="td w10">
							</div>
							<div class="td w100">
								'.($config['currency'][$row['delivery_currency'] ?: $row['currency']]['symbol']).number_format($row['delivery_price'], 2, '.', '').'
							</div>
							<div class="td w10">
							</div>
						</div>';
			}
		// ---------------------------------------------------------------------------------//
			$options = '';
			if($discounts_inf = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices_discount` ORDER BY `id` LIMIT 0, 50', true)){
				foreach($discounts_inf as $disc_info){
					$options .= '<option value="'.$disc_info['id'].'"'.(
						($row['discount'] AND $disc_info['id'] == array_keys(json_decode($row['discount'], true))[0]) ? ' selected' : ''
					).'>'.$disc_info['name'].'</option>';
				}
			}
		
			//if (!$row['order_id'] AND abs($total+$tax-$tradein - $row['total']) > 0.001)
                //db_query('UPDATE `'.DB_PREFIX.'_invoices` SET total = \''.number_format($total+$tax-$tradein, 2, '.', '').'\' WHERE id = '.$id);


			if ($row['refund_info'] AND strlen($row['refund_info']) > 2 AND $row['refund_paid'] > 0) {
				$total = (-1) * $row['refund_paid'];
				$tax = 0; 
				$tradein = 0;
			}
			
			$due = $total+$tax-$tradein - $row['paid'];
			if ($row['issue_id']) {
				$issues = db_multi_query('SELECT
					tb1.id as issue_id,
					tb1.doit,
					tb1.quote,
					tb1.description,
					tb1.purchase_prices,
					tb1.service_ids,
					tb1.upcharge_id,
					IF(tb2.name = \'\', tb2.model, tb2.name) as name,
						tb2.price,
						tb2.purchase_price,
						tb2.type,
						tb2.id as inv_id,
						tb2.quantity, 
						tb2.tradein, 
							tb3.name as catname,
								tb4.id as pur_id,
								tb4.name as pur_name,
								tb4.sale as pur_or_price,
								REGEXP_REPLACE(tb1.purchase_prices, CONCAT(\'{(.*?)"\', tb4.id, \'":"(.*?)",(.*?)}\'), \'\\\2\') as pur_price,
									m.name as model_name,
										up.price as inv_service
						FROM `'.DB_PREFIX.'_issues` tb1
					LEFT JOIN `'.DB_PREFIX.'_inventory` tb2
						ON FIND_IN_SET(tb2.id, CONCAT(tb1.inventory_ids, ",", REGEXP_REPLACE(tb1.service_ids,\'(.*?)_(.*?),\', \'\\\1,\')))
					LEFT JOIN `'.DB_PREFIX.'_inventory_upcharge` up
						ON up.id = tb1.upcharge_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_categories` tb3
						ON tb2.category_id = tb3.id
					LEFT JOIN `'.DB_PREFIX.'_purchases` tb4
						ON FIND_IN_SET(tb4.id, tb1.purchase_ids)
					LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
						ON tb2.model_id = m.id
					WHERE tb1.id = '.$row['issue_id'].' LIMIT 0, 50'
				, true);
			}
			
			if(isset($_GET['json'])){
				echo json_encode([
					'total' => number_format($row['paid'], '2', '.', ''),
					'subtotal' => number_format(($type == 'sv' ? ($total+$tax) : $total)-$tradein, '2', '.', ''),
					'tax' => number_format(($type == 'sv' ? 0 : $tax), '2', '.', ''),
				]);
			} else {
				print '<style>
					body {
						font-size: 14px;
						font-family: sans-serif;
					}
					body > div {
						margin: 30px auto;
					}

					.inv {
						font-size: 28px;
					}

					.wid50 {
						width: 50%;
						float: left;
					}

					.aCenter {
						text-align: center;
					}

					.uTitle {
						margin: 20px;
					}

					.tbl {
						margin: 20px;
						display: table;
						width: -webkit-calc(100% - 40px);
						width: -moz-calc(100% - 40px);
						width: calc(100% - 40px);
					}

					.tr {
						display: table-row;
					}

					.th {
						display: table-cell;
						border-bottom: 2px solid #ddd;
						padding: 10px 10px!important;
						color: #777;
						font-weight: bold;
						font-size: 13px;
						vertical-align: middle;
					}

					.td {
						display: table-cell;
						padding: 8px 10px!important;
						color: #777;
						font-size: 13px;
					}

					.tbl a {
						color: #299CCE;
					}

					.w100 span.fa {
						font-size: 18px;
					}

					.tr:nth-child(even)>.td {
						background: #F7F8FA;
					}

					.td:last-child>a {
						margin: 0 5px;
						color: #769E26
					}

					.td:last-child {
						width: 120px;
					}

					.td:last-child>a:nth-child(3) {
						color: #299CCE;
					}

					.td:last-child>a:last-child {
						color: #CE1212;
					}

					.tr:hover>.td {
						background: #F3F7FF;
						color: #7F94BD;
					}

					.payTotalInfo {
						font-weight: bold;
						width: 300px;
						text-align: right;
						float: right;
					}

					.invInfo {
						display: table-cell;
						vertical-align: middle;
						padding: 0 10px;
						color: #555;
						text-decoration: none;
						font-size: 13px;
					}

					.sUser.head {
						height: 55px;
						font-weight: bold;
						background: #F5F6F9;
					}

					.sUser.head>div {
						border-bottom: 1px solid #EEF0F3;
						color: #858994;
						font-size: 13px;
					}

					.usLiHead {
						display: table;
						width: 100%;
					}

					.dClear:after {
						content: \' \';
						display: block;
						clear: both;
					}

					@media print {
						.more {
							page-break-after: always;
						}
					}
				</style>';
				echo '<title>'.$row['object_name'].' '.$row['date'].'</title>';
				print stripcslashes(str_ireplace([
					'{id}',
					'{logo}',
					'{name}',
					'{address}',
					'{email}',
					'{cellphone}',
					'{subtotal}',
					'{total}',
					'{tax}',
					'{paid}',
					'{due}',
					'{date}',
					'{invoices}',
					'{issues}',
					'{inventory}',
					'{purchases}',
					'{tradein}',
					'{discount-name}',
					'{discount-percent}',
					'{object-name}',
					'{invoice-barcode}',
					'{customer-barcode}',
					'{issue-barcode}',
					'{issue_dsc}',
					'{city}',
					'{zipcode}',
					'{type}',
					'{serial}',
					'{model}',
					'{quote}',
					'{store_cell}',
					'{store_name}',
					'{store_address}',
					'{opt_charger}',
					'{assigned}',
					'{issue_status}',
					'{onsite}',
					'{currency}'
				],[
					$row['id'],
					'<img src="//'.$_SERVER['HTTP_HOST'].'/templates/site/img/logo.png" style="max-width: 300px">',
					$row['customer_name'].' '.$row['customer_lastname'],
					'',//$row['customer_address'],
					$row['customer_email'],
					$row['customer_phone'],
					number_format(($type == 'sv' ? ($total+$tax) : $total)-$tradein, '2', '.', ''),
					number_format($row['paid'], '2', '.', ''),
					number_format(($type == 'sv' ? 0 : $tax), '2', '.', ''),
					number_format($row['paid'], '2', '.', ''),
					number_format((abs($due) < 0.01 ? 0 : $due), '2', '.', ''),
					$row['date'],
					'',//$invoices_html,
	/* 				'<div class="tr">
						<div class="td isItem">
							 Что то
						</div>
						<div class="td w10">
							1
						</div>
						<div class="td w100 nPay">
							$0.00
						</div>
						<div class="td w10">
							yes
						</div>
					</div><div class="tr">
						<div class="td isItem">
							 Что то
						</div>
						<div class="td w10">
							1
						</div>
						<div class="td w100 nPay">
							$0.00
						</div>
						<div class="td w10">
							yes
						</div>
					</div>', */
					get_purchase($total, $row['id'], $type, $tax, $tradein),
					//'',//$issue_mhtml,
					'',//$html['inventory'].$html['services'].$html['additions'],
					'',//$html['purchases'],
					$html['tradein'] ? '<div class="tr">
						<div class="td isem">
							Discount
						</div>
						<div class="td w10">
							-
						</div>
						<div class="td w100 nPay">
							$-55.00<!--  '.sprintf("%.2f", ($tradein*100/$total)).'% -->
						</div>
						<div class="td w10">
							yes
						</div>
					</div>' : '',
					'',//$discount[0]['name'],
					'',//$discount[0]['percent'] ? '-'.$discount[0]['percent'].'%' : '',
					$row['object_name'],
					'<img src="data:image/png;base64,'.
						to_barcode('in '.str_pad(
							$id, 11, '0', STR_PAD_LEFT
							)
						)
					.'">',
					'<img src="data:image/png;base64,'.
						to_barcode('us '.str_pad(
							$row['customer_id'], 11, '0', STR_PAD_LEFT
							)
						)
					.'">',
					'<img src="data:image/png;base64,'.
						to_barcode('is '.str_pad(
							$row['issue_id'], 11, '0', STR_PAD_LEFT
							)
						)
					.'">',
					$issues[0]['description'],
					$row['city_name'],
					$row['zipcode'] ?: '',
					$row['type_name'] ?: '',
					$row['inv_serial'] ?: '',
					$row['inv_cat'].' '.$row['inv_model_name'].' '.($row['inv_model'] ?: ''),
					$config['currency'][$row['currency']]['symbol'].number_format($issues[0]['quote'], 2, '.', ''),
					$row['object_phone'],
					$row['object_name'],
					$row['object_address'],
					'',
					($row['staff_name'].' '.$row['staff_lastname'] ?: ''),
					($row['status_name'] ?: ''),
					$onsite,
					$config['currency'][$row['currency']]['symbol']
				], '<div style="width:780px;">
				   <div class="uForm print">
					  <div class="uTitle dClear">
						 <div class="aCenter inv">Invoice #{id}<br>{date}<br>{store_cell}</div>
						 <div class="uName wid50 aCenter">{logo}<br>{store_name}, {store_address}</div>
						 <div class="uName wid50 aCenter" style="margin-top: 60px">{invoice-barcode}<br><b>{name}</b><br>{address}</div>
					  </div>
					  {invoices}
					  <div class="tbl payInfo">
						 <div class="tr">
							<div class="th">Item</div>
							<div class="th w10">Qty</div>
							<div class="th w100">Amount</div>
							<div class="th w10">Tax</div>
						 </div>
						 {onsite}{issues}{inventory}{purchases}{tradein}<br>
						 <div class="tr">
							<div class="td">{discount-name}</div>
							<div class="td w10"></div>
							<div class="td w100">{discount-percent}</div>
							<div class="td w10"></div>
						 </div>
					  </div>
					  <div class="dClear">
						 <div class="tbl payTotalInfo">
							<div class="tr">
							   <div class="td aRight invInfo">Subtotal</div>
							   <div class="td tAmount">$<span id="subtotal">{subtotal}</span></div>
							</div>
							<div class="tr">
							   <div class="td aRight invInfo">Tax</div>
							   <div class="td tAmount">$<span id="tax">{tax}</span></div>
							</div>
							<div class="tr">
							   <div class="td aRight invInfo">Total</div>
							   <div class="td tAmount">$<span id="total">{total}</span></div>
							</div>
						 </div>
					  </div>
				   </div>
				   <div class="more"></div>
				</div>'));
			}
			//echo '<script>window.print();</script>';
			die;
		}
	break;
	
	default:
		echo '<title>Stores report - 12/01/2020 - 02/28/2017</title>';	
		echo '<style>a {text-decoration: none;}tr > td, th {padding: 8px; }tr:nth-child(even) > td {background: #F7F8FA;}</style>';
		echo '<table style="width: 700px; border-collapse: collapse; border: 0; border-color: #ddd;margin: 10 auto;" border="1">';
		echo '<tr><td colspan="4" style=" text-align: center;padding-top: 20px;"><img src="/templates/site/img/logo.svg" style="width: 60%;"><h1>Stores report</h1><b>Period: 12/01/2020 - 02/28/2017</b></td></tr>';
		echo '<tr><td colspan="4" style="text-align: center;height: 0px;padding: 0;background: #fff;border-color: #fff;"></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: center; "><b>Inventories</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
			</tr>';
		echo '
			<tr>
				<td><a href="/reports/in_store/2">Albany (AL 0181)</a><a href="https://gdr.one/careports.php?store_id=2" style="float: right;">Export</a></td>
				<td>$12678.00</td>
				<td>$1014.24</td>
			</tr>
			<tr>
				<td><a href="/reports/in_store/5">East Greenbush (RE 3881)</a><a href="https://gdr.one/careports.php?store_id=5" style="float: right;">Export</a></td>
				<td>$3761.00</td>
				<td>$300.88</td>
			</tr>
			<tr>
				<td><a href="/reports/in_store/3">Clifton Park (SA 4111)</a><a href="https://gdr.one/careports.php?store_id=3" style="float: right;">Export</a></td>
				<td>$11211.00</td>
				<td>$784.77</td>
			</tr>
			<tr>
				<td><a href="/reports/in_store/4">Schenectady (SC 4241)</a><a href="https://gdr.one/careports.php?store_id=4" style="float: right;">Export</a></td>
				<td>$3766.00</td>
				<td>$301.28</td>
			</tr>
			<tr>
				<td><a href="/reports/in_store/7">Brooklyn, Williamsburg (NE 8081)</a><a href="https://gdr.one/careports.php?store_id=7" style="float: right;">Export</a></td>
				<td>$6762.00</td>
				<td>$600.13</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$38178.00</td>
				<td>$3001.30</td>
			</tr>
		';
		//echo '<tr><td colspan="4" style=" text-align: center;"></td></tr>';
		echo '<tr><td colspan="4" style="text-align: center;"><b>Services</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="2">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="2"><a href="/reports/sv_store/2">Albany (AL 0181)</a><a href="https://gdr.one/careports.php?store_id=2-1" style="float: right;">Export</a></td>
				<td>$36842.67</td>
			</tr>
			<tr>
				<td colspan="2"><a href="/reports/sv_store/5">East Greenbush (RE 3881)</a><a href="https://gdr.one/careports.php?store_id=5-1" style="float: right;">Export</a></td>
				<td>$26208.61</td>
			</tr>
			<tr>
				<td colspan="2"><a href="/reports/sv_store/3">Clifton Park (SA 4111)</a><a href="https://gdr.one/careports.php?store_id=3-1" style="float: right;">Export</a></td>
				<td>$62096.04</td>
			</tr>
			<tr>
				<td colspan="2"><a href="/reports/sv_store/4">Schenectady (SC 4241)</a><a href="https://gdr.one/careports.php?store_id=4-1" style="float: right;">Export</a></td>
				<td>$22962.90</td>
			</tr>
			<tr>
				<td colspan="2"><a href="/reports/sv_store/7">Brooklyn, Williamsburg (NE 8081)</a><a href="https://gdr.one/careports.php?store_id=7-1" style="float: right;">Export</a></td>
				<td>$31610.77</td>
			</tr>
			<tr>
				<th colspan="2" align="right">Total amount:</th>
				<td>$179721.00</td>
			</tr>
			<tr>
				<th colspan="3" height="50"></th>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="2">$217899.00</td>
			</tr>
		';
		echo '</table>';
}
die;
?>