<?php

switch($route[1]){
	
	case 'customers':
        $meta['title'] = 'Customer acceptions';
		$query = text_filter($_POST['query'], 255, false);
		$page = intval($_POST['page']);
		
		$count = 20;
		if($sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS ca.*, u.id as uid, u.name, u.lastname, u.phone, u.email FROM `'.DB_PREFIX.'_customer_acceptions` ca LEFT JOIN `'.DB_PREFIX.'_users` u ON ca.customer_id = u.id WHERE ca.flag != 0 '.(
			$query ? 'AND MATCH (u.name, u.lastname) AGAINST (\''.$query.'\' IN BOOLEAN MODE) ' : ''
		).'ORDER BY date DESC LIMIT '.($page*$count).', '.$count, true)){
			$i = 0;
			foreach($sql as $row){
				tpl_set('tablet/customers/item', [
					'id' => $row['uid'],
					'type-id' => $row['type'],
					'brand-id' => $row['brand'],
					'model-id' => $row['model'],
					'first-name' => $row['name'],
					'last-name' => $row['lastname'],
                    'phone' => $row['phone'],
                    'email' => $row['email']
                ],[], 'customers');
				$i++;
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		} else {
           // tpl_set('noContent', [
            //    'text' => $lang['noFaqs']
            //], false, 'quote');
        }
		$left_count = intval(($res_count-($page*$count)-$i));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['customers'],
			]));
		}
		tpl_set('tablet/main', [
			'title' => $meta['title'],
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'customers' => $tpl_content['customers']
		], [], 'content');
	break;
}
?>