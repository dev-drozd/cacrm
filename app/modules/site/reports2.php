<?php

$stores = [
	2 => [
		'name' => 'Albany',
		'address' => '818 Central Ave Albany, NY 12206',
		'phone' => '518-207-1477'
	],
	5 => [
		'name' => 'East Greenbush',
		'address' => '501 Columbia Turnpike East Greenbush, NY 12061',
		'phone' => '518-937-1477'
	],
	3 => [
		'name' => 'Clifton Park',
		'address' => '1602 Route 9 Clifton Park, NY 12065',
		'phone' => '(518)-383-0313'
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
			if($data = file_get_contents(ROOT_DIR.'/uploads/invoices/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'.json')){
				
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
			$dir = ROOT_DIR.'/uploads/invoices/';
			echo '<ol>';
			foreach(glob($dir."*.json") as $file){
				$exp = explode('-', str_replace([
					$dir,
					'.json'
				], '', $file
				));
				echo '<li><a href="/reports2/export/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'" onclick="this.style.color = \'red\';">'.$stores[$exp[0]]['name'].' ('.date('m-d-Y', $exp[1]).'-'.date('m-d-Y', $exp[2]).')</a></li>';
				echo '<br>';
			}
			echo '</ol>';
		}
		die;
	break;
	
	case 'view':
		$exp = explode('-', $route[2]);
		$index = (int)$exp[3];
		if($data = file_get_contents(ROOT_DIR.'/uploads/invoices/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'.json')){
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
					'.($prev >= 0 ? '<a href="/reports2/view/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'-'.$prev.'-'.$exp[4].'">&#8592;</a>' : '').'
					'.($next ? '<a href="/reports2/view/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'-'.$next.'-'.$exp[4].'">&#8594;</a>' : '').'
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
		if($data = file_get_contents(ROOT_DIR.'/uploads/invoices/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'.json')){
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
						<td><a href="/reports2/view/'.$exp[0].'-'.$exp[1].'-'.$exp[2].'-'.$i.'-'.$exp[3].'" target="__blank">#'.($i+1).'</a></td>
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
		echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: 09/01/2014 - 11/30/2014</b></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
				<th>Tax rate</th>
			</tr>';
			
		$start = strtotime('09/01/2014');
		$end = strtotime('11/30/2014');
		echo '
			<tr>
				<td><a href="/reports2/store/2-'.$start.'-'.$end.'-p">Albany (AL 0181)</a></td>
				<td>$21450.00</td>
				<td>$1716.00</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/5-'.$start.'-'.$end.'-p">East Greenbush (RE 3881)</a></td>
				<td>$3800.00</td>
				<td>$304.00</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/3-'.$start.'-'.$end.'-p">Clifton Park (SA 4111)</a></td>
				<td>$11347.00</td>
				<td>$794.29</td>
				<td>0.07000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/4-'.$start.'-'.$end.'-p">Schenectady (SC 4241)</a></td>
				<td>$11789.00</td>
				<td>$943.12</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/7-'.$start.'-'.$end.'-p">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$3975.00</td>
				<td>$352.78</td>
				<td>0.08875</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$52361.00</td>
				<td>$4110.19</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="3">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="3"><a href="/reports2/store/2-'.$start.'-'.$end.'-s">Albany (AL 0181)</a></td>
				<td>$26040.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/5-'.$start.'-'.$end.'-s">East Greenbush (RE 3881)</a></td>
				<td>$19160.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/3-'.$start.'-'.$end.'-s">Clifton Park (SA 4111)</a></td>
				<td>$30840.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/4-'.$start.'-'.$end.'-s">Schenectady (SC 4241)</a></td>
				<td>$32270.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/7-'.$start.'-'.$end.'-s">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$31690.00</td>
			</tr>
			<tr>
				<th colspan="3" align="right">Total amount:</th>
				<td>$140000.00</td>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="3">$192361.00</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: 12/01/2014 - 02/28/2015</b></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
				<th>Tax rate</th>
			</tr>';
		$start = strtotime('12/01/2014');
		$end = strtotime('02/28/2015');
		echo '
			<tr>
				<td><a href="/reports2/store/2-'.$start.'-'.$end.'-p">Albany (AL 0181)</a></td>
				<td>$21579.00</td>
				<td>$1726.32</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/5-'.$start.'-'.$end.'-p">East Greenbush (RE 3881)</a></td>
				<td>$6309.00</td>
				<td>$504.72</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/3-'.$start.'-'.$end.'-p">Clifton Park (SA 4111)</a></td>
				<td>$17652.00</td>
				<td>$1235.64</td>
				<td>0.07000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/4-'.$start.'-'.$end.'-p">Schenectady (SC 4241)</a></td>
				<td>$13219.00</td>
				<td>$1057.52</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/7-'.$start.'-'.$end.'-p">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$11390.00</td>
				<td>$1010.86</td>
				<td>0.08875</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$70149.00</td>
				<td>$5535.06</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="3">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="3"><a href="/reports2/store/2-'.$start.'-'.$end.'-s">Albany (AL 0181)</a></td>
				<td>$31430.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/5-'.$start.'-'.$end.'-s">East Greenbush (RE 3881)</a></td>
				<td>$19701.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/3-'.$start.'-'.$end.'-s">Clifton Park (SA 4111)</a></td>
				<td>$40410.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/4-'.$start.'-'.$end.'-s">Schenectady (SC 4241)</a></td>
				<td>$33200.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/7-'.$start.'-'.$end.'-s">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$22910.00</td>
			</tr>
			<tr>
				<th colspan="3" align="right">Total amount:</th>
				<td>$147651.00</td>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="3">$217800.00</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: 09/01/2015 - 11/30/2015</b></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
				<th>Tax rate</th>
			</tr>';
		$start = strtotime('09/01/2015');
		$end = strtotime('11/30/2015');
		echo '
			<tr>
				<td><a href="/reports2/store/2-'.$start.'-'.$end.'-p">Albany (AL 0181)</a></td>
				<td>$23781.00</td>
				<td>$1902.48</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/5-'.$start.'-'.$end.'-p">East Greenbush (RE 3881)</a></td>
				<td>$3845.00</td>
				<td>$307.60</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/3-'.$start.'-'.$end.'-p">Clifton Park (SA 4111)</a></td>
				<td>$19827.00</td>
				<td>$1387.89</td>
				<td>0.07000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/4-'.$start.'-'.$end.'-p">Schenectady (SC 4241)</a></td>
				<td>$8971.00</td>
				<td>$717.68</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/7-'.$start.'-'.$end.'-p">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$11037.00</td>
				<td>$979.53</td>
				<td>0.08875</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$67461.00</td>
				<td>$5295.18</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="3">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="3"><a href="/reports2/store/2-'.$start.'-'.$end.'-s">Albany (AL 0181)</a></td>
				<td>$42162.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/5-'.$start.'-'.$end.'-s">East Greenbush (RE 3881)</a></td>
				<td>$28690.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/3-'.$start.'-'.$end.'-s">Clifton Park (SA 4111)</a></td>
				<td>$51140.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/4-'.$start.'-'.$end.'-s">Schenectady (SC 4241)</a></td>
				<td>$32560.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/7-'.$start.'-'.$end.'-s">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$32860.00</td>
			</tr>
			<tr>
				<th colspan="3" align="right">Total amount:</th>
				<td>$187412.00</td>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="3">$254873.00</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: 12/01/2015 - 02/29/2020</b></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
				<th>Tax rate</th>
			</tr>';
		$start = strtotime('12/01/2015');
		$end = strtotime('02/29/2020');
		echo '
			<tr>
				<td><a href="/reports2/store/2-'.$start.'-'.$end.'-p">Albany (AL 0181)</a></td>
				<td>$21570.00</td>
				<td>$1725.60</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/5-'.$start.'-'.$end.'-p">East Greenbush (RE 3881)</a></td>
				<td>$5783.00</td>
				<td>$462.64</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/3-'.$start.'-'.$end.'-p">Clifton Park (SA 4111)</a></td>
				<td>$17896.00</td>
				<td>$1252.72</td>
				<td>0.07000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/4-'.$start.'-'.$end.'-p">Schenectady (SC 4241)</a></td>
				<td>$9066.00</td>
				<td>$725.28</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/7-'.$start.'-'.$end.'-p">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$13592.00</td>
				<td>$1206.29</td>
				<td>0.08875</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$67907.00</td>
				<td>$5372.53</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="3">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="3"><a href="/reports2/store/2-'.$start.'-'.$end.'-s">Albany (AL 0181)</a></td>
				<td>$44853.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/5-'.$start.'-'.$end.'-s">East Greenbush (RE 3881)</a></td>
				<td>$22360.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/3-'.$start.'-'.$end.'-s">Clifton Park (SA 4111)</a></td>
				<td>$43390.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/4-'.$start.'-'.$end.'-s">Schenectady (SC 4241)</a></td>
				<td>$33090.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/7-'.$start.'-'.$end.'-s">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$24200.00</td>
			</tr>
			<tr>
				<th colspan="3" align="right">Total amount:</th>
				<td>$167893.00</td>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="3">$235800.00</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: 03/01/2020 - 05/31/2020</b></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
				<th>Tax rate</th>
			</tr>';
		$start = strtotime('03/01/2020');
		$end = strtotime('05/31/2020');
		echo '
			<tr>
				<td><a href="/reports2/store/2-'.$start.'-'.$end.'-p">Albany (AL 0181)</a></td>
				<td>$23209.00</td>
				<td>$1856.72</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/5-'.$start.'-'.$end.'-p">East Greenbush (RE 3881)</a></td>
				<td>$5792.00</td>
				<td>$463.36</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/3-'.$start.'-'.$end.'-p">Clifton Park (SA 4111)</a></td>
				<td>$27593.00</td>
				<td>$1931.51</td>
				<td>0.07000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/4-'.$start.'-'.$end.'-p">Schenectady (SC 4241)</a></td>
				<td>$10812.00</td>
				<td>$864.96</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/7-'.$start.'-'.$end.'-p">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$9795.00</td>
				<td>$869.31</td>
				<td>0.08875</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$77201.00</td>
				<td>$5985.86</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="3">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="3"><a href="/reports2/store/2-'.$start.'-'.$end.'-s">Albany (AL 0181)</a></td>
				<td>$25040.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/5-'.$start.'-'.$end.'-s">East Greenbush (RE 3881)</a></td>
				<td>$25510.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/3-'.$start.'-'.$end.'-s">Clifton Park (SA 4111)</a></td>
				<td>$32160.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/4-'.$start.'-'.$end.'-s">Schenectady (SC 4241)</a></td>
				<td>$36600.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/7-'.$start.'-'.$end.'-s">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$31020.00</td>
			</tr>
			<tr>
				<th colspan="3" align="right">Total amount:</th>
				<td>$150330.00</td>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="3">$227531.00</td>
			</tr>
		';

		echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: 06/01/2020 - 08/31/2020</b></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
				<th>Tax rate</th>
			</tr>';
		$start = strtotime('06/01/2020');
		$end = strtotime('08/31/2020');
		echo '
			<tr>
				<td><a href="/reports2/store/2-'.$start.'-'.$end.'-p">Albany (AL 0181)</a></td>
				<td>$2214.00</td>
				<td>$1697.12</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/5-'.$start.'-'.$end.'-p">East Greenbush (RE 3881)</a></td>
				<td>$7138.00</td>
				<td>$571.04</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/3-'.$start.'-'.$end.'-p">Clifton Park (SA 4111)</a></td>
				<td>$19977.00</td>
				<td>$1398.38</td>
				<td>0.07000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/4-'.$start.'-'.$end.'-p">Schenectady (SC 4241)</a></td>
				<td>$10786.00</td>
				<td>$862.88</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/7-'.$start.'-'.$end.'-p">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$12168.00</td>
				<td>$1079.91</td>
				<td>0.08875</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$71283.00</td>
				<td>$5609.34</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="3">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="3"><a href="/reports2/store/2-'.$start.'-'.$end.'-s">Albany (AL 0181)</a></td>
				<td>$21840.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/5-'.$start.'-'.$end.'-s">East Greenbush (RE 3881)</a></td>
				<td>$20620.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/3-'.$start.'-'.$end.'-s">Clifton Park (SA 4111)</a></td>
				<td>$41810.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/4-'.$start.'-'.$end.'-s">Schenectady (SC 4241)</a></td>
				<td>$24188.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/7-'.$start.'-'.$end.'-s">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$18680.00</td>
			</tr>
			<tr>
				<th colspan="3" align="right">Total amount:</th>
				<td>$127138.00</td>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="3">$198421.00</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style=" text-align: center; " height="80"><b>PERIOD: 09/01/2020 - 11/30/2020</b></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: left; "><b>INVENTORIES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>Store name</th>
				<th>Amount</th>
				<th>Tax</th>
				<th>Tax rate</th>
			</tr>';
		$start = strtotime('09/01/2020');
		$end = strtotime('11/30/2020');
		echo '
			<tr>
				<td><a href="/reports2/store/2-'.$start.'-'.$end.'-p">Albany (AL 0181)</a></td>
				<td>$11758.00</td>
				<td>$940.64</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/5-'.$start.'-'.$end.'-p">East Greenbush (RE 3881)</a></td>
				<td>$5141.00</td>
				<td>$411.28</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/3-'.$start.'-'.$end.'-p">Clifton Park (SA 4111)</a></td>
				<td>$17855.00</td>
				<td>$1249.85</td>
				<td>0.07000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/4-'.$start.'-'.$end.'-p">Schenectady (SC 4241)</a></td>
				<td>$7882.00</td>
				<td>$630.56</td>
				<td>0.08000</td>
			</tr>
			<tr>
				<td><a href="/reports2/store/7-'.$start.'-'.$end.'-p">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$4971.00</td>
				<td>$441.18</td>
				<td>0.08875</td>
			</tr>
			<tr>
				<th align="right">Total amount:</th>
				<td>$47607.00</td>
				<td>$3673.51</td>
			</tr>
		';
		
		echo '<tr><td colspan="4" style="text-align: left;"><b>SERVICES</b></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th colspan="3">Store name</th>
				<th>Amount</th>
			</tr>';
		echo '
			<tr>
				<td colspan="3"><a href="/reports2/store/2-'.$start.'-'.$end.'-s">Albany (AL 0181)</a></td>
				<td>$28714.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/5-'.$start.'-'.$end.'-s">East Greenbush (RE 3881)</a></td>
				<td>$34720.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/3-'.$start.'-'.$end.'-s">Clifton Park (SA 4111)</a></td>
				<td>$29470.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/4-'.$start.'-'.$end.'-s">Schenectady (SC 4241)</a></td>
				<td>$35350.00</td>
			</tr>
			<tr>
				<td colspan="3"><a href="/reports2/store/7-'.$start.'-'.$end.'-s">Brooklyn, Williamsburg (NE 8081)</a></td>
				<td>$51590.00</td>
			</tr>
			<tr>
				<th colspan="3" align="right">Total amount:</th>
				<td>$179844.00</td>
			</tr>
			<tr>
				<th align="right">Gross sales:</th>
				<td colspan="3">$227451.00</td>
			</tr>
		';
		
		echo '</table>';
	break;
}
die;
?>