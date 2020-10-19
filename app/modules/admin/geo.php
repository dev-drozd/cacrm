<?php
/**
 * @appointment Geo api
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
switch($route[1]){
	
	case 'countries':
		$data = db_multi_query('
			SELECT code, name
			FROM `'.DB_PREFIX.'_countries`', true, false, function($a){
				return [$a['code'], $a['name']];
		});
	break;
	
	case 'states':
		$data = db_multi_query('
			SELECT code, name
			FROM `'.DB_PREFIX.'_states`
			WHERE country = \''.text_filter($route[2], 2, false).'\'', true, false, function($a){
				return [$a['code'], $a['name']];
		});
	break;
	
	case 'cities2':
		$data = db_multi_query('
			SELECT DISTINCT zip_code, city 
			FROM `'.DB_PREFIX.'_cities` 
			WHERE state = \''.text_filter($route[2], 2, false).'\''.(
				$_POST['not_zip'] ? 'GROUP BY city' : ''
			), true, false, function($a){
				return [$a['zip_code'], $a['city']];
		});
	break;
	
	case 'cities':
		$zipCode = db_escape_string($route[3]);
		$name = [];
		$data = db_multi_query('
			SELECT zip_code, city 
			FROM `'.DB_PREFIX.'_zipcodes` 
			WHERE state = \''.text_filter($route[2], 2, false).'\' GROUP BY city'.(
				$zipCode ? ', zip_code = '.$zipCode : ''
			), true, false, function($a) use(&$name){
				if($name[1] == $a['city']){
					$name[2] = $name[0];
				}
				$name[0] = $a['zip_code'];
				$name[1] = $a['city'];
				return [$a['zip_code'], $a['city']];
		});
		if($name[2]){
			unset($data[$name[2]]);
			unset($name);
		}
	break;
	
	case 'zipcode':
		$data = db_multi_query('
			SELECT country, state 
			FROM `'.DB_PREFIX.'_cities` 
			WHERE zip_code = \''.db_escape_string($route[2]).'\''
		);
	break;
}

header("Content-Type: application/json;charset=utf-8");

exit(json_encode($data ?? []));

?>