<?php
/**
 * @appointment Parser
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        https://yoursite.com
 * This code is copyrighted
*/
 
defined('ENGINE') or ('hacking attempt!');

//is_ajax() or die('hacking');

$lnk = $_GET['lnk'] ?? $_POST['lnk'];

$url = parse_url($lnk);

$item = [
	'err' => '',
	'host' => $url['host']
];

preg_match('/(www.)?(ebay|amazon).(com|net)/i', $url['host'], $m);

$responce = getCurl($lnk);

/* if(!is_ajax()){
	echo '<pre>';
	echo print_r($responce['content']);
	die;	
} */

if(strpos($responce['content_type'], 'text/html') !== false){
	
	libxml_use_internal_errors(true);
	
	$node = new DOMDocument();
	
	$text = $node->loadHTML(
		'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'.
			trim($responce['content'])
	);
	
	$finder = new DomXPath($node);
	$descr = @$finder->query('//*/meta[@name="description"]')->item(0);
	$item['description'] = $descr ? ($descr->getAttribute('content') ?? '') : '';
	
	switch($m[2]){
		
		case 'ebay':
			$item['price'] = preg_replace("/(?:.*\s)?(.*)/i", "$1",
				str_replace(
					'\t', '', 
					$node->getElementById('mm-saleDscPrc')->nodeValue ?? 
					$node->getElementById('prcIsum')->nodeValue ?? ''
				)
			);
			$item['sale_price'] = $item['price'] ? number_format(min_price(floatval(preg_replace("/[^0-9.]/i", '', $item['price'])), ($user['store_id'] ?? 2)), 2, '.', '') : 0;
			$item['title'] = trim(str_replace("Details about  &nbsp;", '', htmlentities(
				$node->getElementById('itemTitle')->nodeValue, null, 'utf-8'
			))) ?? '';
			$item['code'] = $node->getElementById('descItemNumber')->nodeValue ?? '';
			
			$item['image'] = str_replace(
				's-l300', 's-l500', $node->getElementById('icImg')->getAttribute('src')
			) ?? '';	
			
			$item['estimated'] = $finder->query(
				'//*[@class="vi-acc-del-range"]'
			)->item(0)->nodeValue ?? '';
		break;
		
		case 'amazon':
			if(!is_ajax()){
				echo $responce['content'];
				die;	
			}
			$price = floatval(preg_replace("/[^0-9.\s]/i", '', ($node->getElementById('priceblock_saleprice')->nodeValue ?: $node->getElementById('usedBuySection')->childNodes[1]->childNodes[1]->childNodes[3]->nodeValue) ?? 
			$node->getElementById('priceblock_ourprice')->nodeValue ?? ''));
			$item['price'] = '$ '.number_format($price, 2, '.', '');
			$item['sale_price'] = $item['price'] ? number_format(min_price($price, ($user['store_id'] ?? 2)), 2, '.', '') : 0;
			$item['title'] = $node->getElementById('productTitle')->nodeValue ?? '';
			$item['code'] = preg_replace("/(?:.*)\/dp\/([A-Za-z0-9_]+)(?:.*)?/i", "$1", $lnk);
			$item['image'] = ($node->getElementById(
				'landingImage'
			)->getAttribute('data-old-hires') ?: $node->getElementById(
				'landingImage'
			)->getAttribute('src')) ?? '';
		break;
		
		default:
			if(!is_ajax()){
				echo $responce['content'];
				die;
				$dom  = new DOMDocument();
				libxml_use_internal_errors( 1 );
				$dom->loadHTML($responce['content']);
				$xpath = new DOMXpath($dom);

				$script = $xpath->query('//script[@type="application/ld+json"]');
				$json = trim($script->item(0)->nodeValue);

				$data = json_decode($json,1);
				
				print_r($data);
				
//https://www.skymobileny.com/product-page/iphone-6s-plus-back-camera-replacement
				die;	
			}
			$item['title'] = $node->getElementsByTagName('title')->item(0)->nodeValue ?? 
			$node->getElementsByTagName('h1')->item(0)->nodeValue;
			$ogg_image = $finder->query('//*/meta[@property="og:image"]')->item(0);
			$item['image'] = $ogg_image > 0 ? ($ogg_image->getAttribute('content') ?? '') : '';
			if($currency = @$finder->query('//*/*[@itemprop="priceCurrency"]')->item(0)){
				$currency = $currency->nodeValue ?: @$currency->getAttribute('content');
			}
			if($price = @$finder->query('//*/*[@itemprop="price"]')->item(0)){
				$price = $price->nodeValue ?: $price->getAttribute('content');
				$item['price'] = $currency.' '.number_format($price, 2, '.', '');
				$item['sale_price'] = number_format(min_price($price, ($user['store_id'] ?? 2)), 2, '.', '');
			}
	}
	
} else
	$item['err'] = 'no_responce';

	echo json_encode($item);

die;
?>