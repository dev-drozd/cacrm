<?php
$_POST['id'] = 2;

$_POST['type'] = 'cash';

$date = db_multi_query('SELECT 
					c.*
					FROM `'.DB_PREFIX.'_cash` c
					WHERE '.($_POST['type'] == 'credit' ? 'c.action = \'open\' AND c.type= \'credit\'' : 'c.action = \'close\'').' AND c.object_id = '.intval($_POST['id']).' ORDER BY c.id DESC LIMIT 0, 1');
	
echo '<pre>';	
print_r($date);
$sql = db_multi_query('SELECT 
							SUM(h.amount) as amount,
							h.date,
							h.currency
						FROM `'.DB_PREFIX.'_invoices_history` h
						LEFT JOIN `'.DB_PREFIX.'_invoices` i
							ON h.invoice_id = i.id
						WHERE i.object_id = '.intval($_POST['id']).' 
						AND h.type = \'cash\' 
						AND h.date >= \''.($date['date'] ?: 0).'\'');
print_r($sql);

die;

$exitst_amount = ($date ? (289 - $date['out_cash'] + 248.6) : 0);

$sql['amount'] = 94.29;

echo number_format(floatval($sql['amount']) + floatval($exitst_amount), 2, '.', '');

die;

$page = (int)$_GET['page'];
if($sql = db_multi_query('
	SELECT SQL_CALC_FOUND_ROWS
		id, images
	FROM `'.DB_PREFIX.'_inventory`
	WHERE images != \'\' ORDER BY id LIMIT '.$page.', 50', true
)){
	foreach($sql as $row){
		$path = ROOT_DIR.'/uploads/images/inventory/'.$row['id'].'/';
		foreach(explode('|', $row['images']) as $img){
			if(!$img) continue;
			$obj = new Imagick($path.$img);
			
			$obj->cropThumbnailImage(300, 300);
			$obj->stripImage();
			$obj->writeImage($path.'preview_'.$img);
			
			$obj->cropThumbnailImage(94, 94);
			$obj->stripImage();
			$obj->writeImage($path.'thumb_'.$img);
			$obj->destroy();
		}
	}
	$page++;
}
if(intval($_GET['page']) != $page)
	echo '<script>location.replace(\'/abs?page='.$page.'\');</script>';
die;