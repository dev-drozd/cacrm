<?php
/**
 * @appointment Cart
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
require APP_DIR .'/vendor/autoload.php';
			
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

define("AUTHORIZENET_LOG_FILE", "phplog");


 
switch($route[1]){
	
	case 'send_delivery':
		$id = intval($_POST['id']);
		
		if (!text_filter($_POST['address'], 1000, false))
			die('no_address');
		
		db_query('
			UPDATE `'.DB_PREFIX.'_orders` SET
				country = \''.text_filter($_POST['country'], 255, false).'\',
				state = \''.text_filter($_POST['state'], 255, false).'\',
				city = \''.text_filter($_POST['city'], 255, false).'\',
				zipcode = \''.text_filter($_POST['zipcode'], 255, false).'\',
				address = \''.text_filter($_POST['address'], 1000, false).'\'
			WHERE id = '.$id
		);
		
		sendPush2('1,31735', 'New order from the site',
					 'Someone made an order from the site '.date("Y-m-d H:i:s"),
		[
			'type' => 'alert',
			'id' => 'order-site-'.uniqid(),
		]);
		
		if ($user['email'] AND $config['order_form'] AND $form = db_multi_query('SELECT content FROM `'.DB_PREFIX.'_forms` WHERE id = '.$config['order_form'])) {
			$products = '';
			
			$send = db_multi_query('SELECT
				o.*,
				s.name as status,
				d.name as delivery,
				d.price as delivery_price,
				d.currency as delivery_currency,
				p.name as payment,
				c.name as ucountry_name,
				st.name as ustate_name,
				ct.city as ucity_name,
				oc.name as country_name,
				ost.name as state_name,
				oct.city as city_name,
				i.id as invoice
			FROM `'.DB_PREFIX.'_orders` o 
			LEFT JOIN `'.DB_PREFIX.'_users` u 
				ON u.id = o.customer_id
			LEFT JOIN `'.DB_PREFIX.'_orders_status` s 
				ON s.id = o.status_id
			LEFT JOIN `'.DB_PREFIX.'_orders_delivery` d 
				ON d.id = o.delivery_id
			LEFT JOIN `'.DB_PREFIX.'_orders_payment` p 
				ON p.id = o.payment_id
			LEFT JOIN `'.DB_PREFIX.'_countries` c
				ON u.country = c.code
			LEFT JOIN `'.DB_PREFIX.'_states` st
				ON u.state = st.code
			LEFT JOIN `'.DB_PREFIX.'_cities` ct
				ON u.zipcode = ct.zip_code
			LEFT JOIN `'.DB_PREFIX.'_countries` oc
				ON o.country = oc.code
			LEFT JOIN `'.DB_PREFIX.'_states` ost
				ON o.state = ost.code
			LEFT JOIN `'.DB_PREFIX.'_cities` oct
				ON o.zipcode = oct.zip_code
			LEFT JOIN `'.DB_PREFIX.'_invoices` i 
				ON i.order_id = o.id
			WHERE o.del = 0 AND o.id = '.$id);
			
			if ($send['product_info']) {
				foreach(json_decode($send['product_info'], true) as $inv_id => $inv) {
					$products .= '<tr>
						<td>
							<a href="/inventory/view/'.$inv_id.'" target="_blank">'.$inv['name'].'</a>
						</td>
						<td>
							'.($config['currency'][$inv['currency']]['symbol'] ?: $config['currency'][$send['currency']]['symbol']).number_format($inv['price'], 2, '.', '').'
						</td>
						<td>
							yes
						</td>
					</tr>';
				}
			}
			
			$delivery = '<tr>
						<td>
							'.$send['delivery'].'
						</td>
						<td>
							'.($config['currency'][$send['delivery_currency'] ?: $send['currency']]['symbol']).number_format($send['delivery_price'], 2, '.', '').'
						</td>
						<td>
						</td>
					</tr>';
			
			$form_text = str_ireplace([
				'{logo}',
				'{customer_name}',
				'{customer_lastname}',
				'{customer_phone}',
				'{customer_address}',
				'{customer_email}',
				'{subtotal}',
				'{total}',
				'{tax}',
				'{currency}',
				'{date}',
				'{products}',
				'{delivery}',
				'{payment}',
				'{delivery-name}',
				'{delivery-price}',
				'{note}'
			],[
				'<img src="//'.$_SERVER['HTTP_HOST'].'/templates/admin/img/logo.svg" style="max-width: 300px">',
				$user['uname'],
				$user['ulastname'],
				$user['phone'],
				(
					($send['country_name'] OR $send['state_name'] OR $send['city_name']) ?
					($send['country_name'] ? $send['country_name'].' ' : '').($send['state_name'] ? $send['state_name'].' ' : '').($send['city_name'] ? $send['city_name'].' ' : '') : 
					($send['ucountry_name'] ? $send['ucountry_name'].' ' : '').($send['ustate_name'] ? $send['ustate_name'].' ' : '').($send['ucity_name'] ? $send['ucity_name'].' ' : '')
				).text_filter($_POST['address'], 1000, false),
				$user['email'],
				number_format($send['total'] - $send['tax'], 2, '.', ''),
				number_format($send['total'], 2, '.', ''),
				number_format($send['tax'], 2, '.', ''),
				$config['surrency'][$send['currency']]['symbol'] ?: '$',
				$send['date'],
				$products,
				$delivery,
				$send['payment'],
				$send['delivery'],
				($config['currency'][$send['delivery_currency'] ?: $send['currency']]['symbol']).number_format($send['delivery_price'], 2, '.', ''),
				$send['note']
			], $form['content']);
			
			// Headers
			$headers  = 'MIME-Version: 1.0'."\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$headers .= 'To: '.$user['email']. "\r\n";
			$headers .= 'From: Your Company <noreply@yoursite.com>' . "\r\n";

			// Send
			mail($user['email'], 'Thank you for order on Your Company Inc', $form_text, $headers);
		}
		
		echo $send['invoice'];
		die;
	break;
	
	case 'delivery':
		$id = intval($route[2]);
		if ($id) {
			tpl_set('cart/delivery', [
				'id' => $id,
			], [
			], 'content');
		}
	break;
	
	case 'login':
		tpl_set('cart/login', [
		], [
		], 'content');
	break;
	
	/*
	* Invoice Payment
	*/
	case 'send_pay':
		is_ajax() or die('hacking');
		if ($id = intval($_POST['id'])) {
			
			$amount = floatval($_POST['amount']);
			
			// Common setup for API credentials
			$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
			$merchantAuthentication->setName($config['login_id']);
			$merchantAuthentication->setTransactionKey($config['transaction_key']);
			$refId = 'ref' . time();

			// Create the payment data for a credit card
			$creditCard = new AnetAPI\CreditCardType();
			$creditCard->setCardNumber($_POST['card-number']);
			$creditCard->setExpirationDate($_POST['month'].$_POST['exp-year']);
			$creditCard->setCardCode(intval($_POST['code']));
			$paymentOne = new AnetAPI\PaymentType();
			$paymentOne->setCreditCard($creditCard);

			$order = new AnetAPI\OrderType();
			$order->setDescription('Invoice #'.$id);

			//create a transaction
			$transactionRequestType = new AnetAPI\TransactionRequestType();
			$transactionRequestType->setTransactionType( "authCaptureTransaction"); 
			$transactionRequestType->setAmount($amount);
			$transactionRequestType->setOrder($order);
			$transactionRequestType->setPayment($paymentOne);


			$request = new AnetAPI\CreateTransactionRequest();
			$request->setMerchantAuthentication($merchantAuthentication);
			$request->setRefId($refId);
			$request->setTransactionRequest( $transactionRequestType);
			$controller = new AnetController\CreateTransactionController($request);
			$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);


			if ($response != null) {
				if($response->getMessages()->getResultCode() == 'Ok') {
					$tresponse = $response->getTransactionResponse();

					if ($tresponse != null && $tresponse->getMessages() != null) {
						db_query('UPDATE `'.DB_PREFIX.'_invoices` SET paid = total, conducted = 1, pay_method = \'merchaine\' WHERE id = '.$id);
						$invoice = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id);
						db_query('INSERT INTO `'.DB_PREFIX.'_invoices_history` SET 
							invoice_id = '.$id.', 
							amount = '.$invoice['total'].',
							currency = \''.$invoice['currency'].'\',
							type = \'merchaine\'');
						die('OK');
					} else {
						die('trans_failed');
					}
				} else
					die('card_failed');
			} else 
				die('no_response');
		}
		die;
	break;
	
	/*
	* Cart payment
	*/
	case 'pay':
		if ($id = intval($route[2])) {
			if ($invoice = db_multi_query('SELECT total, paid, customer_id, conducted FROM `'.DB_PREFIX.'_invoices` WHERE id = '.$id)) {
				if ($user['id'] != $invoice['customer_id']) {
					tpl_set('cart/error', [
						'text' => 'You have no access to do this'
					], [
					], 'content');
				} elseif ($invoice['total'] - $invoice['paid'] < 0.001 OR $invoice['conducted']) {
					tpl_set('cart/error', [
						'text' => 'Invoice is already paid'
					], [
					], 'content');
				} else {
					$years = '';
					for($i = date('Y', time()); $i <= date('Y', time()) + 10; $i++)
						$years .= '<option value="'.($i - 2000).'">'.$i.'</option>';
					tpl_set('cart/pay', [
						'id' => $id,
						'amount' => $invoice['total'] - $invoice['paid'],
						'years' => $years
					], [
					], 'content');
				}
			} else {
				tpl_set('cart/error', [
					'text' => 'Wrong invoice id'
				], [
				], 'content');
			}
		} else {
			tpl_set('cart/empty', [
			], [
			], 'content');
		}
	break;
	
	/*
	* Send order
	*/
	case 'send':
		is_ajax() or die('hacking');
		$ids = substr_replace($_SESSION['cart']['items'], '', -1);
		
		if ($ids) {
			if ($inventory = db_multi_query('
				SELECT SQL_CALC_FOUND_ROWS
					i.id, 
					REPLACE(IF(i.name = \'\', CONCAT(IFNULL(c.name, \'\'), \' \', IFNULL(t.name, \'\'), \' \', IFNULL(m.name, \'\'), \' \', i.model), i.name), \'"\', \'\') as name, 
					i.price,
					i.currency
				FROM `'.DB_PREFIX.'_inventory` i 
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c 
					ON i.category_id = c.id
				LEFT JOIN  `'.DB_PREFIX.'_inventory_models` m
					ON m.id = i.model_id
				LEFT JOIN  `'.DB_PREFIX.'_inventory_types` t
					ON t.id = i.type_id
				WHERE i.id IN('.$ids.')
			', true)) {
				$product_info = '';
				foreach($inventory as $inv) {
					if ($product_info) $product_info .= ',';
					$product_info .= '"'.$inv['id'].'":{"name":"'.$inv['name'].'","price":"'.$inv['price'].'","currency":"'.$inv['currency'].'"}';
				}
				$product_info = '{'.$product_info.'}';
				
				db_query('
					INSERT INTO `'.DB_PREFIX.'_orders` SET
						customer_id = '.$user['id'].',
						product_ids = \''.$ids.'\',
						product_info = \''.$product_info.'\',
						total = '.floatval($_POST['total']).',
						tax = '.floatval($_POST['tax']).',
						currency = \''.text_filter($_POST['currency'], 25, false).'\',
						status_id = 1,
						delivery_id = '.intval($_SESSION['cart']['delivery']['id'] ?: $_POST['delivery']).'
				');
				
				/*
					country = \''.text_filter($_POST['country'], 255, false).'\',
					state = \''.text_filter($_POST['state'], 255, false).'\',
					city = \''.text_filter($_POST['city'], 255, false).'\',
					zipcode = \''.text_filter($_POST['zipcode'], 255, false).'\',
					address = \''.text_filter($_POST['address'], 1000, false).'\',
					note = \''.text_filter($_POST['note'], 1000, false).'\'
				*/
				
				$id = intval(mysqli_insert_id($db_link));
				
				db_query('
					INSERT INTO `'.DB_PREFIX.'_invoices` SET
						customer_id = '.$user['id'].',
						inventory = \''.$ids.'\',
						inventory_info = \''.$product_info.'\',
						total = '.floatval($_POST['total']).',
						tax = '.floatval($_POST['tax']).',
						currency = \''.text_filter($_POST['currency'], 25, false).'\',
						order_id = '.intval($id)
				);
				
				$invoice_id = intval(mysqli_insert_id($db_link));
				
				db_query('UPDATE `'.DB_PREFIX.'_counters` SET count = count + 1 WHERE name = \'e_order\'');	
				
				unset($_SESSION['cart']);
				
				echo $id;
				die;
			} else
				die('no_items');
			
		}
	break;
	
	/*
	*  Change payment
	*/
	case 'payment': 
		is_ajax() or die('hacking');
		
		$_SESSION['cart']['payment'] = intval($_POST['id']);
		die;
	break;
	
	/*
	*  Change delivery
	*/
	case 'change_delivery': 
		is_ajax() or die('hacking');
		
		$_SESSION['cart']['delivery']['id'] = intval($_POST['id']);
		$_SESSION['cart']['delivery']['price'] = floatval($_POST['price']);
		$_SESSION['cart']['delivery']['currency'] = text_filter($_POST['currency'], 25, false);
		die;
	break;
	
	/*
	*  Add item
	*/
	case 'add': 
		is_ajax() or die('hacking');
		$id = intval($_POST['id']);
		
		if (in_array($id, explode(',', $_SESSION['cart']['items']))) 
			echo 'in_cart';
		else {
			$_SESSION['cart']['items'] = $_SESSION['cart']['items'].$id.',';
			$_SESSION['cart']['count'] += 1 ;
			echo 'add';
		}
		die;
	break;

	/*
	*  Del item
	*/
	case 'del': 
		is_ajax() or die('hacking');
		$id = intval($_POST['id']);

		$_SESSION['cart']['items'] = str_replace($id.',', '', $_SESSION['cart']['items']);
		$_SESSION['cart']['count'] -= 1;
		$tax = db_multi_query('SELECT 
				o.tax
			FROM `'.DB_PREFIX.'_inventory` i
			LEFT JOIN `'.DB_PREFIX.'_objects` o 
				ON o.id = i.object_id
			WHERE i.id = '.$id);
		echo $tax['tax'] ?: 0;
		die;
	break;

	/*
	*  Clear cart
	*/
	case 'clear': 
		is_ajax() or die('hacking');
		unset($_SESSION['cart']);
		die('OK');
	break;

	/*
	*  View cart
	*/
	case null:
		$meta['title'] = 'Cart';
		$subtotal = 0;
		$tax = 0;
		$currency = 'USD';
		
		if ($_SESSION['cart']['delivery']['price'])
			$subtotal = $_SESSION['cart']['delivery']['price'];

		if ($_SESSION['cart']['count']) {
			$i = 0;
			foreach(db_multi_query('
				SELECT 
					i.id, 
					i.name, 
					i.model,
					i.price,
					i.category_id,
					i.images,
					i.currency,
					i.descr,
					c.name as category_name,
					o.tax
				FROM `'.DB_PREFIX.'_inventory` i
				LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c 
					ON c.id = i.category_id
				LEFT JOIN `'.DB_PREFIX.'_objects` o 
					ON o.id = i.object_id
				WHERE i.id IN('.substr($_SESSION['cart']['items'], 0, -1).')
			', true) as $item) {
				$images = explode('|', $item['images']);
				$subtotal += $item['price'];
				$tax += $item['price'] * $item['tax'] / 100;
				
				if (!$i)
					$currency = $item['currency'];
					

				tpl_set('cart/item', [
					'id' => $item['id'],
					'name' => $item['name'] ? $item['name'] : $item['category_name'].' '.$item['model'],
					'price' => $item['price'],
					'descr' => text_filter($item['descr'], 75, false).'...',
					'currency' => $config['currency'][$item['currency']]['symbol'],
					'image' => $images[0]
				], [
					'image' => $images[0]
				], 'items');
				$i++;
			}

			$delivery = '';
			$d_price = 0;
			$d_currency = '$';
			$i = 0;
			if ($deliveryms = db_multi_query('
				SELECT
					*
				FROM `'.DB_PREFIX.'_orders_delivery`
				ORDER BY sort
			', true)) {
				foreach($deliveryms as $del) {
					if (!$i) {
						$d_price = $del['price'];
						$d_currency = $config['currency'][$del['currency']]['symbol'];
					}
					$delivery .= '<option value="'.$del['id'].'" data-price="'.$del['price'].'" data-currency="'.$del['currency'].'" data-symbol="'.$config['currency'][$del['currency']]['symbol'].'"'.((isset($_SESSION['cart']['delivery']['id']) AND $_SESSION['cart']['delivery']['id'] == $del['id']) ? ' selected' : '').'>'.$del['name'].'</option>';
					$i++;
				}
			}

			$payment = '';
			if ($paymentms = db_multi_query('
				SELECT
					*
				FROM `'.DB_PREFIX.'_orders_payment`
				ORDER BY sort
			', true)) {
				foreach($paymentms as $pay) {
					$payment .= '<option value="'.$pay['id'].'"'.((isset($_SESSION['cart']['payment']) AND $_SESSION['cart']['payment'] == $pay['id']) ? ' selected' : '').'>'.$pay['name'].'</option>';
				}
			}
					
			tpl_set('cart/main', [
				'items' => $tpl_content['items'],
				'subtotal' => number_format($subtotal, 2, '.', ''),
				'tax' => number_format($tax, 2, '.', ''),
				'total' => number_format($subtotal + $tax, 2, '.', ''),
				'delivery' => $delivery,
				'payment' => $payment,
				'd_price' => $_SESSION['cart']['delivery']['price'] ? $_SESSION['cart']['delivery']['price'] : $d_price,
				'd_currency' => $_SESSION['cart']['delivery']['currency'] ? $config['currency'][$_SESSION['cart']['delivery']['currency']]['symbol'] : $d_currency,
				'currency' => $currency,
				'scurrency' => $config['currency'][$currency]['symbol'],
				'dsubtotal' => number_format(($_SESSION['cart']['delivery']['price'] ? $subtotal - $_SESSION['cart']['delivery']['price'] : $subtotal), 2, '.', ''),
				'dtotal' => number_format(($_SESSION['cart']['delivery']['price'] ? $subtotal + $tax - $_SESSION['cart']['delivery']['price'] : $subtotal + $tax), 2, '.', ''),
			], [
				'user' => $user['id']
			], 'content');
		} else {
			tpl_set('cart/empty', [
			], [
			], 'content');
		}
		
	break;
}
?>