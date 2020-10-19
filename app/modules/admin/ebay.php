<?php

defined('ENGINE') or ('hacking attempt!');

	error_reporting(E_ALL);

	$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>
<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
<RequesterCredentials>
<eBayAuthToken>AgAAAA**AQAAAA**aAAAAA**jstVVw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GgDZOLpQSdj6x9nY+seQ**1tEDAA**AAMAAA**KSNy9441RXuMyfQJMYm7k4ZefcEyBH4YzfGlB94NVUTBhPDwQHZ1o/RppkGNF58E2Vig77w2W8z5REZLwSEG4VT1TFIrY8pVXEQ/tnU6y/Ts4CHUrYMtTQBcK/0YEDt2N3Ab5cqyafzUmWaukXye6Wd+xqFyXtYrLQ93h00lqMXdjBGP/fp8jaIJJgfFCW5yHrCtOP1vrfYKF5gebyD3lw3WbNq6Ml8CJ7ITdKxiFRDkcVWTxnrNLkIkQpUP9P8XQrjRew4mnvUGObC5Cp+/GEKQZhJTT1RDyYF8s6f0xY6Ds6N3a/kNrY5+vEVXnyJy//TcWxuRopKYKMsfuW88oUgnUmiA4WqhkdF0SdggcnIQToVJwpfHPALxEW1iTg517MeoXig62xbnjrlD50SpOrL7UQ+n/90Nj84rauN+henOlQWGPWR+lvlWV//KVc6XFcuJgKdpfG+p11jTyc0sYXplhiblmirCa2KzqdgIPz1Akqc3F5zvu7yqImk0TlRyhgb1FBE34Pw8DtL7/0Cjco31TgDJL97wLKmQUi73CY9lFgO6e4Z4aWrg5OxZ8ZhcurC0b7qUcsSwAhrbMLt/eBDjh6oxE2MwhUTF+QXElUPgyuSX4AOmFQCxvvjNMbEhfRsiIAPPH76KsK4StqJ9zCe1IvItQKaCOyg8LkSoS5QQ02toRvv50R8JvnETBdbf/4Zng4oIWKdFhsFcIA8DMAOVWTV5t+af7jCkLE+SGV/bWUZFDmrm0pnlnILGSfI4</eBayAuthToken>
</RequesterCredentials>
<CreateTimeFrom>2007-12-01T20:34:44.000Z</CreateTimeFrom>
  <CreateTimeTo>2020-06-06T20:34:44.000Z</CreateTimeTo>
  <OrderRole>Buyer</OrderRole>
</GetOrdersRequest>​';
	
	$connection = curl_init();
	curl_setopt_array($connection, [
		CURLOPT_URL => 'https://api.sandbox.ebay.com/ws/api.dll',
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_HTTPHEADER => [
			'X-EBAY-API-COMPATIBILITY-LEVEL:949',
			'X-EBAY-API-DEV-NAME:a6c5bfb0-8e95-415b-99b6-7193ebbb5be5',
			'X-EBAY-API-APP-NAME:Alexandr-Computer-SBX-d8a129233-af00af65',
			'X-EBAY-API-CERT-NAME:SBX-8a1292338fbd-53c3-4f63-bf44-a361',
			'X-EBAY-API-SITEID:0',
			'X-EBAY-API-CALL-NAME:GetOrders'
		],
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $requestXmlBody,
		CURLOPT_RETURNTRANSFER => 1
	]);
	$response = curl_exec($connection);
	$err = curl_errno($connection);
	$errmsg = curl_error($connection);
	$res = curl_getinfo($connection);
	curl_close($connection);
	
	$responseDoc = new DomDocument();
	$responseDoc->loadXML($response);
	
	$orderArray = $responseDoc->getElementsByTagName('OrderArray')[0]->getElementsByTagName('Order');
	foreach ($orderArray as $order) {
		echo  $order->getElementsByTagName('OrderID')[0]->nodeValue . ' - ' . $order->getElementsByTagName('OrderStatus')[0]->nodeValue . '<br>';
	}
	
	echo '<pre>';
	print_r($res);
	echo '</pre>';
	
?>