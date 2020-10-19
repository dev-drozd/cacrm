<?php

$stores = [
	2 => [
		'name' => 'Albany (AL 0181)',
		'address' => '818 Central Ave Albany, NY 12206',
		'phone' => '518-207-1477'
	],
	5 => [
		'name' => 'East Greenbush (RE 3881)',
		'address' => '501 Columbia Turnpike East Greenbush, NY 12061',
		'phone' => '518-937-1477'
	],
	3 => [
		'name' => 'Clifton Park',
		'address' => '1602 Route 9 Clifton Park, NY 12065',
		'phone' => '518-383-0313'
	],
	4 => [
		'name' => 'Schenectady',
		'address' => '2330 Watt St Crosstown Plaza Schenectady, NY 12304',
		'phone' => '518-346-0861'
	],
	7 => [
		'name' => 'Brooklyn, Williamsburg',
		'address' => '455 Graham Ave Brooklyn, NY 11222',
		'phone' => '718-313-0427'
	],
	8 => [
		'name' => 'Ballston Spa',
		'address' => '128 Milton Ave, Ballston Spa, NY 12020',
		'phone' => '518-512-0930'
	]
];

function convert_date2($a,$b = false){
	global $config;
	return date('j M Y'.(
		$b ? ' H:i' : ''
	), strtotime($a));
}

switch($route[1]){
	
	case 'export':
		if(isset($route[2])){
			$exp = explode('-', $route[2]);
			if($data = file_get_contents(ROOT_DIR.'/uploads/invoices2/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'.json')){
				
				header('Content-Type: application/csv; charset=UTF-8');
				header('Content-Disposition: attachment; filename="'.urlencode($stores[$exp[0]]['name']).'-('.date('m-d-Y', $exp[1]).'-'.date('m-d-Y', $exp[2]).').csv"');
				
				$types = json_decode($data, true);
				
				$output = fopen('php://output', 'w');
				fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
				fputcsv($output, ['Taxable/notaxable','Subtotal','Tax','Total','Date'],';');
				
				$tax = 0;
				$amount = 0;
				$subtotal = 0;
				
				foreach($types as $invoices){
					$i = 1;
					foreach($invoices as $invoice){
						fputcsv($output, [($invoice['tax'] > 0 ? 'Yes' : 'No'), number_format(($invoice['amount']), '2', '.', ''),number_format($invoice['tax'], '2', '.', ''),number_format($invoice['total'], '2', '.', ''),convert_date2($invoice['date'])],';');
						$tax += (float)$invoice['tax'];
						$subtotal += (float)$invoice['amount'];
						$amount += (float)$invoice['total'];
						$i++;
					}
				}
				
				fputcsv($output, ['', '','','','',''],';');
				fputcsv($output, ['Subtotal:', '','','','','$'.number_format($subtotal, '2', '.', '')],';');
				fputcsv($output, ['Tax:', '','','','','$'.number_format($tax, '2', '.', '')],';');
				fputcsv($output, ['Total amount:', '','','','','$'.number_format($amount, '2', '.', '')],';');
				fclose($output);
			}
		} else {
			$dir = ROOT_DIR.'/uploads/invoices2/';
			echo '<ol>';
			foreach(glob($dir."*.json") as $file){
				if($file == $dir.'dashboard.json')
					continue;
				$exp = explode('-', str_replace([
					$dir,
					'.json'
				], '', $file
				));
				echo '<li><a href="/reports3/export/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'" onclick="this.style.color = \'red\';">'.$stores[$exp[0]]['name'].' ('.date('m-d-Y', $exp[1]).'-'.date('m-d-Y', $exp[2]).')</a></li>';
				echo '<br>';
			}
			echo '</ol>';
		}
		die;
	break;
	
	case 'view':
		$exp = explode('-', $route[2]);
		$index = (int)$exp[3];
		if($data = file_get_contents(ROOT_DIR.'/uploads/invoices2/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'.json')){
			$type = $exp[4] == 'p' ? 'purchases' : 'services';
			$data = json_decode($data, true);
			$row = $data[$type][$exp[3]];
			$prev = $index-1;
			$next = isset($data[$type][$index+1]) ? ($index+1) : 0;
			
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
				
				header > a {
					font-size: 2em;
					text-decoration: none;
					color: #57c1e8;
				}
				
				header > a:last-child {
					float: right;
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
				($exp[3]+1)*10,
				'<img src="//'.$_SERVER['HTTP_HOST'].'/templates/site/img/logo.png" style="max-width: 300px">',
				$row['customer'],
				'',
				'',
				'',
				number_format(($type == 'sv' ? ($row['total']+$row['tax']) : $row['amount']), '2', '.', ''),
				number_format($row['total'], '2', '.', ''),
				number_format(($type == 'sv' ? 0 : $row['tax']), '2', '.', ''),
				number_format($row['total'], '2', '.', ''),
				'',//number_format((abs($due) < 0.01 ? 0 : $due), '2', '.', ''),
				$row['date'],
				'',
				'<div class="tr">
					<div class="td isItem">
						 '.$row['text'].'
					</div>
					<div class="td w10">
						1
					</div>
					<div class="td w100 nPay">
						$'.number_format($row['amount'], '2', '.', '').'
					</div>
					<div class="td w10">
						'.($exp[4] == 'p' ? 'yes' : 'no').'
					</div>
				</div>',
				'',
				'',
				'',
				'',
				'',
				$stores[$exp[0]]['name'],
				'<img src="data:image/png;base64,'.
					to_barcode('in '.str_pad(
						($exp[3]+1)*10, 11, '0', STR_PAD_LEFT
						)
					)
				.'">',
				'',
				'',
				'',
				$row['city_name'] ?: 'City name',
				$row['zipcode'] ?: 'ZIPCODE',
				$row['type_name'] ?: '',
				$row['inv_serial'] ?: '',
				$row['inv_cat'].' '.$row['inv_model_name'].' '.($row['inv_model'] ?: ''),
				$config['currency'][$row['currency']]['symbol'].number_format($issues[0]['quote'], 2, '.', ''),
				$stores[$exp[0]]['phone'],
				$stores[$exp[0]]['name'],
				$stores[$exp[0]]['address'],
				'',
				'',
				'',
				'',
				''
			], '<div style="width:780px;">
				<header>
					'.($prev >= 0 ? '<a href="/reports3/view/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'-'.$prev.'-'.$exp[4].'">&#8592;</a>' : '').'
					'.($next ? '<a href="/reports3/view/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'-'.$next.'-'.$exp[4].'">&#8594;</a>' : '').'
				</header>
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
			
		} else
			echo 'err';
	break;
	
	case 'store':
		$exp = explode('-', $route[2]);
		if($data = file_get_contents(ROOT_DIR.'/uploads/invoices2/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'.json')){
			$type = $exp[3] == 'p' ? 'purchases' : 'services';
			$data = json_decode($data, true);
			$amount = 0;
			
			echo '<style>tr > td, th {padding: 8px; }tr:nth-child(even) > td {background: #F7F8FA;}</style>';
			echo '<table style="width: 700px; border-collapse: collapse; border: 0; border-color: #ddd;margin: 60 auto;" border="1">';
			echo '<tr><td colspan="4" style=" text-align: center; "><h1>Store '.$stores[$exp[0]]['name'].'</h1></td></tr>';
			echo '<tr><td colspan="4" style=" text-align: center; "><h2>Period: '.date('Y-m-d', $exp[1]).' - '.date('Y-m-d', $exp[2]).'</h2></td></tr>';
			echo '<tr style="background: #f7f7f7; text-align: center;">
					<th>#</th>
					<th>Price</th>
					<th>Data</th>
					<th>type</th>
				</tr>';
			
			foreach($data[$type] as $i => $purchase){
				$amount += $purchase['total'];
				echo '
					<tr>
						<td><a href="/reports3/view/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'-'.$i.'-'.$exp[3].'" target="__blank">#'.($i+1).'</a></td>
						<td>$'.number_format($purchase['total'], '2', '.', '').'</td>
						<td>'.$purchase['date'].'</td>
						<td>'.$purchase['type'].'</td>
					</tr>
				';
			}
			echo '<tr style="text-align: right;"><td colspan="3"><h3>Total amount:</h3></td><td><h3>$'.number_format($amount, '2', '.', '').'</h3></td></tr>';
			echo '</table>';
			//echo '<pre>';
			//print_r($data);
		} else
			echo 'err';
	break;
	
	case null:
	
		echo '<title>Stores report - 09/01/2014 - 11/30/2014</title>';	
		echo '<style>body {margin: 0;padding: 0;background: #fff;color: #606060;width: 100%;min-height: 100vh;font-family: \'Roboto\', Arial, sans-serif;font-size: 16px;line-height: 24px;text-align: center;}a {text-decoration: none;}tr > td, th {padding: 8px; }tr:nth-child(even) > td {background: #F7F8FA;}a {color: #57c1e8;}a:hover {color: #33b7e8;text-decoration: none;}</style>';
		echo '<table style="max-width: 850px;width: 100%; border-collapse: collapse; border: 0; border-color: #ddd;margin: 20 auto;" border="1">';
		echo '<tr><td colspan="4" style=" text-align: center;padding-top: 20px;"><img src="/templates/site/img/logo.svg" style="width: 60%;"><h1>Stores report</h1></td></tr>';
		echo '<tr><td colspan="4" style="text-align: center;height: 0px;padding: 0;background: #fff;border-color: #fff;"></td></tr>';
		
		
		$dashboard = ROOT_DIR.'/uploads/invoices2/dashboard.json';
		
		$dashboard_data = json_decode(file_get_contents($dashboard), true);
		
		foreach($dashboard_data as $period => $store){
			$stores_data = [
				'p' => '',
				's' => ''
			];
			
			$stores_ammount = [
				'p' => [0,0],
				's' => [0,0]
			];
			$p = explode('-', $period);
			echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: '.date('m/d/Y', $p[0]).' - '.date('m/d/Y', $p[1]).'</b></td></tr>';
			foreach($store as $store_id => $invoices){
				foreach($invoices as $type => $data){
					$stores_data[$type] .= '<tr>
						<td'.($type == 's' ? ' colspan="3"' : '').'><a href="/reports3/store/'.$store_id.'-'.$period.'-'.$type.'">'.$stores[$store_id]['name'].'</a></td>
						<td>$'.number_format($data['total'], '2', '.', '').'</td>
						'.($type == 'p' ? '
							<td>$'.number_format($data['tax'], '2', '.', '').'</td>
							<td>'.number_format($data['rate'], '5', '.', '').'</td>
						' : '').'
					</tr>';
					$stores_ammount[$type][0] += $data['total'];
					$stores_ammount[$type][1] += ($data['total']*$data['rate']);
				}
			}
			echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
			echo '<tr style="background: #f7f7f7; text-align: center;">
					<th>Store name</th>
					<th>Amount</th>
					<th>Tax</th>
					<th>Tax rate</th>
				</tr>';
				
			echo $stores_data['p'];
			
			echo '<tr>
					<th align="right">Total amount:</th>
					<td>$'.number_format($stores_ammount['p'][0], '2', '.', '').'</td>
					<td>$'.number_format($stores_ammount['p'][1], '2', '.', '').'</td>
				</tr>';
			
			echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
			echo '<tr style="background: #f7f7f7; text-align: center;">
					<th colspan="3">Store name</th>
					<th>Amount</th>
				</tr>';
				
			echo $stores_data['s'];
			
			echo '
				<tr>
					<th colspan="3" align="right">Total amount:</th>
					<td>$'.number_format($stores_ammount['s'][0], '2', '.', '').'</td>
				</tr>
				<tr>
					<th align="right">Gross sales:</th>
					<td colspan="3">$'.number_format($stores_ammount['p'][0]+$stores_ammount['s'][0], '2', '.', '').'</td>
				</tr>
			';
		}
		
		echo '</table>';
	break;
}
die;
?>