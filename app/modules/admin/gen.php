<?php

$store_id = intval($_GET['store_id']);

//if($store_id != 2 && $route[1] == 'get')
	//die('Access denied');

$stores = [
	2 => 'Albany',
	5 => 'East Greenbush',
	3 => 'Clifton Park',
	4 => 'Schenectady',
	7 => 'Brooklyn, Williamsburg'
];

switch($route[1]){
	
	case 'get':
		$csv = array_map(function($a){
			return explode(';', $a)[0];
		}, file(ROOT_DIR.'/uploads/stores/'.$store_id.'.csv'));
		$data = file(ROOT_DIR.'/uploads/stores/'.$store_id.'.csv');
		$amount = 0;
		echo '<style>tr > td, th {padding: 8px; }tr:nth-child(even) > td {background: #F7F8FA;}</style>';
		echo '<table style="width: 700px; border-collapse: collapse; border: 0; border-color: #ddd;margin: 60 auto;" border="1">';
		echo '<tr><td colspan="4" style=" text-align: center; "><h1>Store '.$stores[$store_id].'</h1></td></tr>';
		echo '<tr><td colspan="4" style=" text-align: center; "><h2>Period: 2020-12-20 - 2017-02-28</h2></td></tr>';
		echo '<tr style="background: #f7f7f7; text-align: center;">
				<th>#</th>
				<th>Price</th>
				<th>Data</th>
				<th>type</th>
			</tr>';
		foreach($data as $a){
			$a = explode(';', $a);
			$amount += $a[1];
			echo '
				<tr>
					<td><a href="/gen/print/'.$a[0].'">#'.$a[0].'</a></td>
					<td>$'.$a[1].'</td>
					<td>'.$a[2].'</td>
					<td>'.$a[3].'</td>
				</tr>
			';
			//echo '<li><a href="/gen/print/'.$a[0].'">#'.$a[0].' <font color="green">$'.$a[1].'</font> '.$a[2].'</a> '.$a[3].'</li>';
		}
		echo '<tr style="text-align: right;"><td colspan="3"><h3>Total amount:</h3></td><td><h3>$'.number_format($amount, '2', '.', '').'</h3></td></tr>';
		echo '</table>';
		//echo implode(',',$csv);
	break;
	
	case 'print':
		$id = intval($route[2]);
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
            
			$tax = $row['purchace'] ? 0 : ($total - $onsite_total) * $row['object_tax'] / 100; //$onsite_total + $tradein_total
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
			print '<style>
				body {
					font-size: 14px;
					font-family: sans-serif;
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
				$row['customer_address'],
				$row['customer_email'],
				$row['customer_phone'],
				number_format($total, '2', '.', ''),
				number_format(($total+$tax), '2', '.', ''),
				number_format($tax, '2', '.', ''),
				number_format($row['paid'], '2', '.', ''),
				number_format((abs($due) < 0.01 ? 0 : $due), '2', '.', ''),
				$row['date'],
				$invoices_html,
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
				$issue_mhtml,
				'',//$html['inventory'].$html['services'].$html['additions'],
				$html['purchases'],
				$html['tradein'],
				$discount[0]['name'],
				$discount[0]['percent'] ? '-'.$discount[0]['percent'].'%' : '',
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
			], $config['device_form']));
			die;
		}
	break;
	default:
	$store_id = intval($_GET['store_id']);

	$st_pr = [
		2 => 12678,
		5 => 3761,
		3 => 11211,
		4 => 3766,
		8 => 6762
	];

	$start_date = '2020-12-01';
	$end_date = '2017-02-28';
	$ids = [];
	foreach([2,3,5,4,7] as $sid){
		foreach(file(ROOT_DIR.'/uploads/stores/'.$sid.'.csv') as $a){
			$ids[] = explode(';', $a)[0];
		}
	}
	$sql = 'SELECT 
			id,
			REPLACE(total, \'.\',\',\') as total,
			DATE_FORMAT(date, \'%d.%m.%Y %H:%i\') as date,
			pay_method FROM `'.DB_PREFIX.'_invoices`
		WHERE object_id IN(2,5,3,4,7) AND (pay_method = \'cash\' OR pay_method = \'credit\') AND date >= \''.$start_date.'\' AND date <= \''.$end_date.'\' AND total > 0 AND conducted = 1 AND id NOT IN('.implode(',', $ids).') ORDER BY pay_method DESC';
	$invoices = db_multi_query($sql,1);
	//echo $sql;
	$file = fopen(ROOT_DIR.'/uploads/export.csv', 'w');
	$total1 = 0;
	$total = 0;
	$two = 0;
	foreach ($invoices as $fields){
		$total += str_replace(',','.',$fields['total']);
		fputcsv($file, $fields, ";");
		if($total > 160000 && !$two){
			fclose($file);
			$file = fopen(ROOT_DIR.'/uploads/export2.csv', 'w');
			$two = 1;
			$total1 = $total;
			$total = 0;
		}
	}
	 
	fclose($file);

	echo 'Total: $'.$total1.'<br>Total2: $'.$total.'<br><a href="/uploads/export.csv" download>Download</a> - <a href="/uploads/export2.csv" download>Download2</a>';

	die;
}

die;
$store_id = intval($_GET['store_id']);

$st_pr = [
	2 => 12678,
	5 => 3761,
	3 => 11211,
	4 => 3766,
	8 => 6762
];

$start_date = '2020-12-20';
$end_date = '2017-02-28';

/* $invoices = db_multi_query('SELECT 
		h.invoice_id,
		REPLACE(h.amount, \'.\',\',\') as amount,
		DATE_FORMAT(h.date, \'%d.%m.%Y %H:%i\') as date,
		h.type
	FROM `'.DB_PREFIX.'_invoices_history` h
	LEFT JOIN `'.DB_PREFIX.'_invoices` i
		ON h.invoice_id = i.id
	WHERE i.object_id = '.$store_id.' 
	AND (h.type = \'cash\' OR h.type = \'credit\')
	AND h.date >= \''.$start_date.'\' AND h.date <= \''.$end_date.'\' AND h.amount > 0 ORDER BY h.type DESC',1
); */

$invoices = db_multi_query('SELECT 
		id,
		REPLACE(total, \'.\',\',\') as total,
		DATE_FORMAT(date, \'%d.%m.%Y %H:%i\') as date,
		pay_method FROM `'.DB_PREFIX.'_invoices`
	WHERE object_id = '.$store_id.' AND (pay_method = \'cash\' OR pay_method = \'credit\') AND date >= \''.$start_date.'\' AND date <= \''.$end_date.'\' AND total > 0 AND conducted = 1 AND total < 500 ORDER BY pay_method DESC',1
);

//echo '<pre>';
//print_r($invoices);
//die;

//header('Content-Type: text/csv; charset=utf-8');
$file = fopen(ROOT_DIR.'/uploads/export.csv', 'w');
foreach ($invoices as $fields){
    fputcsv($file, $fields, ";");
}
 
fclose($file);

echo '<a href="/uploads/export.csv" download>Download</a>';

die;

$prc = $st_pr[$store_id]/100;

$cp = rand(4,6);

$cprc = $prc*$cp;

$data = [
	'total' => $st_pr[$store_id],
	'credit_amount' => 0,
	'cash_amount' => 0,
	$cp.'%' => $cprc,
	(100-$cp).'%' => $st_pr[$store_id]-$cprc,
	//'credit_invoices' => [],
	//'cash_invoices' => []
];

$data2 = [
	'credit_invoices' => [],
	'cash_invoices' => []
]; 

echo '<pre>';

//print_r($invoices);

shuffle($invoices);

$crhids = [];
$criids = [];

$chhids = [];
$chiids = [];

$count = count($invoices);

foreach($invoices as $invoice){
	if($data[$invoice['type'].'_amount']+$invoice['amount'] <= (
		$invoice['type'] == 'credit' ? $data[(100-$cp).'%'] : $data[$cp.'%']
	)){
		if($invoice['type'] == 'credit'){
			$data['credit_amount'] += $invoice['amount'];
			//$data2['credit_invoices'][] = $invoice;
			$crhids[] = $invoice['id'];
			$criids[] = $invoice['invoice_id'];
		} else {
			$data['cash_amount'] += $invoice['amount'];
			//$data2['cash_invoices'][] = $invoice;
			$chhids[] = $invoice['id'];
			$chiids[] = $invoice['invoice_id'];
		}
	} //else
		//break;
}
$result = $data['credit_amount']+$data['cash_amount'];
$data['result'] = $result;

if($data['result'] == $data['total']){
	echo '<p>Credit hystory ids: '.implode(',',$crhids).'</p>';
	echo '<p>Credit invoice ids: '.implode(',',$criids).'</p>';
	echo '<p>Cash hystory ids: '.implode(',',$chhids).'</p>';
	echo '<p>Cash invoices ids: '.implode(',',$chiids).'</p>';
} else
	echo '<script>setTimeout(\'location.reload()\', 1)</script>';

echo "<h1>Период с $start_date по $end_date</h1>";
echo "<h2>Магазин #$store_id</h2>";
echo "<h3>Нужная сумма $ $st_pr[$store_id]</h3>";
echo "<h3>Результат комбинаций $ $result</h3>";
echo "<h3>Количество инвойсов $count</h3>";
echo '<pre style="background: #222; color: green; padding: 10px;">';
print_r($data);
//print_r($data2);
die;
?>