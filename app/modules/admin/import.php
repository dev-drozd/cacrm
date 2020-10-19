<?php
/**
 * @appointment File Import
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]){
	case 'verify_purchace':
		is_ajax() or die('Hacking attempt!');
		db_query('UPDATE `'.DB_PREFIX.'_purchases` SET transaction = \''.$_POST['transaction'].'\' WHERE id = '.intval($_POST['id']));
		die('OK');
	break;
	
	case 'purchases':
		is_ajax() or die('Hacking attempt!');
		$query = text_filter($_POST['query'], 100, false);
		$purchases = db_multi_query('
			SELECT SQL_CALC_FOUND_ROWS 
				p.id, 
				REPLACE(IF(p.sale_name = \'\', p.name, p.sale_name), \'"\', \'\') as name, 
				p.price, 
				p.currency,
				p.confirm_date as object
			FROM `'.DB_PREFIX.'_purchases` p
			LEFT JOIN `'.DB_PREFIX.'_objects` o 
				ON o.id = p.object_id
			WHERE p.del = 0 AND p.status NOT IN (\'Created\', \'Rejected\') AND p.transaction = \'\' '.(
			$query ? ' AND IF(p.sale_name = \'\', p.name, p.sale_name) LIKE \'%'.$query.'%\'' : ''
		).' ORDER BY p.id DESC LIMIT 20', true);
		
		// Get count
		$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		die(json_encode([
			'list' => $purchases,
			'count' => $res_count,
		]));
	break;
	
	case 'verified':
		is_ajax() or die('Hacking attempt!');
		if ($tran = $_POST) {
			$sql = '';
			foreach($tran as $k => $v) {
				$sql .= 'WHEN '.$k.' THEN \''.$v.'\' ';
			}
			
			db_query('UPDATE `'.DB_PREFIX.'_purchases` SET transaction = CASE id '.$sql.' END WHERE id IN ('.implode(',', array_keys($tran)).')');
			echo mysqli_affected_rows($db_link);
		}
		die;
	break;
	
	case 'save_file':
		is_ajax() or die('Hacking attempt!');
		
		if ($_FILES) {
			$dir = ROOT_DIR.'/uploads/files/import/';
			if(!is_dir($dir.$id)){
				@mkdir($dir.$id, 0777);
				@chmod($dir.$id, 0777);
			}
			$dir = $dir.$id.'/';
			
			$tmp = $_FILES['file']['tmp_name'];
			$type = mb_strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
			$rename = uniqid('', true).'.'.$type;
			
			if(move_uploaded_file($tmp, $dir.$rename)){
								
				db_query('INSERT INTO `'.DB_PREFIX.'_import` SET 
					file_name = \''.$_FILES['file']['name'].'\',
					date = \''.date('Y-m-d H:i:s', time()).'\',
					deals = '.intval($_POST['deals']).',
					period = \''.text_filter($_POST['period'], 255, false).'\',
					file = \''.$rename.'\'
				');
				
				die('OK');
			}
		}
	break;
	
	case 'compare':
		is_ajax() or die('Hacking attempt!');
		$sql = 'del = 0';
		$fields = 'id, IF(sale_name != \'\', sale_name, name) as show_name, link as show_link, price as show_price, purchase_currency as show_currency';
		$purchases = [];
		$period = 0;
		foreach($_POST as $field => $compare){
			if ($field == 'date'){
				if($compare[0] && $compare[1]){
					$sql .= ' AND DATE(confirm_date) >= \''.$compare[0].'\' AND DATE(confirm_date) <= \''.$compare[1].'\'';
					$fields .= ($fields ? ',' : '').'DATE_FORMAT(confirm_date,\'%m/%d/%Y\') as confirm_date';
					$purchases = db_multi_query('SELECT COUNT(*) as count FROM `'.DB_PREFIX.'_purchases` WHERE DATE(confirm_date) >= \''.$compare[0].'\' AND DATE(confirm_date) <= \''.$compare[1].'\'');
					$period = 1;
				} else {
					continue;
				}
			} else if ($field == 'transaction') {
				continue;
			} else {
				$p = '';
				
				foreach(explode('|:|', $compare) as $d){
					if(!$d) continue;
					$p .= ($p ? ',' : '').'"'.db_escape_string($d).'"';
				}
				
				$field = db_escape_string($field);
				
				if ($field == 'confirm_date')
					$field = "DATE_FORMAT(confirm_date,'%m/%d/%Y')";
				
				$fields .= ($fields ? ', ' : '').$field.(
					$field == "DATE_FORMAT(confirm_date,'%m/%d/%Y')" ? ' as date' : ''
				);
				
				$sql .= ($sql ? ' AND' : '').' '.$field.' IN('.$p.')';
			}
		}
		if ($_POST['transaction'] AND count($_POST) > 2) {
			$fields .= ($fields ? ',' : '').'transaction';
			$p = '';
			foreach(explode('|:|', $_POST['transaction']) as $d){
				if(!$d) continue;
				$p .= ($p ? ',' : '').'"'.db_escape_string($d).'"';
			}
			$sql = '(transaction IN ('.$p.') OR ('.$sql.'))';
		}

		$query = 'SELECT '.$fields.' FROM `'.DB_PREFIX.'_purchases` WHERE '.$sql;
		//echo $query;
		$res = db_multi_query($query, true);
		echo json_encode([
			'res' => $res,
			'purchases' => $period ? $purchases['count'] : 'none'
		]);
		die;
	break;
	
	case 'upload':
		is_ajax() or die('Hacking attempt!');
		
		$select = '<select name="fields" onchange="compare();">
			<option value="0">None</option>
			<option value="link">Link</option>
			<option value="name">Name</option>
			<option value="sale_name">Sale name</option>
			<option value="price">Price</option>
			<option value="sale">Sale price</option>
			<option value="total">Total price</option>
			<option value="confirm_date">Date</option>
		</select>';
		
		$data = [
			'file_titles_row' => []
		];
		
		$table = '<table>';
		
		$id = intval($_POST['id']);
		
		if ($id)
			$file = db_multi_query('SELECT id, file FROM `'.DB_PREFIX.'_import` WHERE id = '.$id);
		
		$ext = pathinfo(($id ? $file['file'] : $_FILES['file']['name']), PATHINFO_EXTENSION);
		
		switch($ext){
			
			case 'csv':
				$f = fopen(($id ? ROOT_DIR.'/uploads/files/import/'.$file['file'] : $_FILES['file']['tmp_name']), "r");
				$line = 0;
				$date_field = '';
				while(($l = fgetcsv($f)) !== false){
					$table .= ($table == '<table>' ? '<thead><tr>' : '<tr id="line_'.$line.'" data-title="0 Matches" class="hntJS">');
					$i = 0;
					$first = 0;
					foreach ($l as $c){
						if(!$c){
							$table = '<table>';
							break;
						}
						if ($table == '<table><thead><tr>' OR $first) {
							$first = 1;
							$table .= '<th data-field="'.$c.'" data-id="'.$i.'">'.$select.'<br>'.htmlspecialchars($c).'</th>';
							$data['titles'][$i] = $c;
							if (strpos(strtolower($c), 'date') AND !$date_field) {
								$date_field = $c;
							}
						} else {
							$table .= '<td data-value="'.htmlspecialchars($c).'">'.htmlspecialchars($c).'</td>';
							$data[$data['titles'][$i]][] = $c;
						}
						$i++;
					}
					if ($table != '<table>')
						$table .= '</tr><tr id="line_p_'.$line.'"><td colspan="'.count($l).'"></td>'.($first ? '</tr></thead><tbody>' : '</tr>');
					$line++;
				}
				fclose($f);
			break;
			
			case 'xlsx':
			
				require('app/xlsx.php');
			
				$xlsx = new Xlsx($id ? ROOT_DIR.'/uploads/files/import/'.$file['file'] : $_FILES['file']['tmp_name']);
				list($num_cols, $num_rows) = $xlsx->dimension();
				
				if ($xlsx->rows()){
					$line = 0;
					$date_field = '';
					foreach($xlsx->rows() as $k => $r) {
						if ($k == 0) {
							$table .= '<thead>
								<tr>';
							for( $i=0; $i < $num_cols; $i++) {
								$table .= '<th data-field="'.$r[$i].'" data-id="'.$i.'">'.$select.'<br>'.( (!empty($r[$i])) ? $r[$i] : '&nbsp;' ).'</th>';
								if (!empty($r[$i])) 
									$data['titles'][$i] = $r[$i];
								if (strpos(strtolower($r[$i]), 'date') AND !$date_field) {
									$date_field = $r[$i];
								}
							}
							$table .= '</tr>
								</thead><tbody>';
						} else {
							$table .= '<tr id="line_'.$line.'" data-title="0 Matches" class="hntJS">';
							for($i = 0; $i < $num_cols; $i++) {
								$table .= '<td data-value="'.( (!empty($r[$i])) ? str_replace('"', '\'', $r[$i]) : '&nbsp;' ).'">'.( (!empty($r[$i])) ? str_replace('"', '\'', $r[$i]) : '&nbsp;' ).'</td>';
								$data[$data['titles'][$i]][] = (!empty($r[$i])) ? $r[$i] : '';
							}
							$table .= '</tr><tr id="line_p_'.$line.'"><td colspan="'.$num_cols.'"></td></tr>';
						}
						$line++;
					}
				}
			break;
			
			null:
				echo json_encode([
					'err' => 1
				]);
			break;
			
		}
		$table .= '</tbody></table>';
		
		$date_arr = $data[$date_field];
		usort($date_arr, function($a, $b) {
			return strtotime($a) < strtotime($b) ? -1: 1;
		});
		
		echo json_encode([
			'table' => $table,
			'data' => $data,
			'period' => $date_arr[0].' - '.$date_arr[count($date_arr) - 1],
			'deals' => count($date_arr)
		]);
		die;
	break;
	
	case 'list':
		$meta['title'] = 'Import list';
		$page = intval($_POST['page']);
		$count = 20;
		
		if ($import = db_multi_query('SELECT * FROM `'.DB_PREFIX.'_import` ORDER BY id DESC LIMIT '.($page*$count).', '.$count, true)) {
			foreach($import as $imp) {
				tpl_set('import/item', [
					'id' => $imp['id'],
					'file_name' => $imp['file_name'],
					'deals' => $imp['deals'],
					'period' => $imp['period'],
					'date' => $imp['date'],
				], [], 'import');
			}
			$res_count = intval(mysqli_fetch_array(db_query('SELECT FOUND_ROWS()'))[0]);
		}
		$left_count = intval(($res_count-($page*$count)-count($import)));
		if($_POST){
			exit(json_encode([
				'res_count' => $res_count,
				'left_count' => $left_count,
				'content' => $tpl_content['import'],
			]));
		}
		tpl_set('import/list', [
			'res_count' => $res_count,
			'more' => $left_count ? '' : ' hdn',
			'import' => $tpl_content['import']
		], [], 'content');
	break;
	
	default:
		$meta['title'] = 'Import data';
		$id = intval($route[1]);
		
		tpl_set('import/main', [
			'id' => $id
		], [
			'id' => $id
		], 'content');
	break;
}
?>