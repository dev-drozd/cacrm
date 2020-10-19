<?php
/**
 * @appointment Xls admin panel
 * @author      Alexandr Drozd && Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

$header = '<?xml version="1.0"?>
		<?mso-application progid="Excel.Sheet"?>
		<ss:Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">
			<ss:Styles>
				<ss:Style ss:ID="header">
					<ss:Interior ss:Color="#6da62c" ss:Pattern="Solid"/>
					<ss:Font ss:Bold="1" ss:Size="10"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
					</ss:Borders>
				</ss:Style>
				<Style ss:ID="wrapText">
					<ss:Interior ss:Color="#f6f6f6" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
						<ss:Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top" ss:WrapText="1"/>
				</Style>
				<Style ss:ID="wrapTextNb">
					<ss:Interior ss:Color="#f6f6f6" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top" ss:WrapText="1"/>
				</Style>
				<ss:Style ss:ID="title">
					<ss:Font ss:Bold="1" ss:Size="12"/>
					<ss:Alignment ss:Horizontal="Center"/>
				</ss:Style>
				<ss:Style ss:ID="isImportant">
					<ss:Interior ss:Color="#f0935a" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="isNew">
					<ss:Interior ss:Color="#ed5555" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="isFinished">
					<Alignment ss:Vertical="Top"/>
					<ss:Interior ss:Color="#a3e179" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
				</ss:Style>
				<ss:Style ss:ID="isAll">
					<ss:Interior ss:Color="#ffd966" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="def">
					<ss:Interior ss:Color="#f6f6f6" ss:Pattern="Solid"/>
					<ss:Borders>
						<ss:Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#757575"/>
					</ss:Borders>
					<Alignment ss:Vertical="Top"/>
				</ss:Style>
				<ss:Style ss:ID="col">
					<ss:Font ss:Size="10"/>
				</ss:Style>
			</ss:Styles> ';
$footer = '</ss:Workbook>';
 
switch($route[1]){
	case 'unpaid_invoices':
		$invoices = db_multi_query('
			SELECT 
				i.id,
				i.date,
				i.issue_id,
				i.paid,
				TRUNCATE(i.total + i.tax, 1) as total_paid,
				i.total,
				i.tax,
				i.discount,
				i.customer_id,
				i.object_id,
				u.name,
				u.lastname,
				u.phone,
				o.name as oname
			FROM `'.DB_PREFIX.'_invoices` i 
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id = i.customer_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = i.object_id
			WHERE i.conducted = 0 AND i.paid = 0 AND i.object_id != 0 AND o.name != \'\'
			ORDER BY i.object_id, i.id DESC', true);

		$cntn = $header;
		xls_header('unpaid_invoices');
		
		if ($invoices) {
			$oid = 0;
			$object_total = 0;
			foreach($invoices as $inv) {
				if ($oid != $inv['object_id']) {
					if ($oid != 0) {
						$cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="header"></ss:Cell>
							<ss:Cell ss:StyleID="header"></ss:Cell>
							<ss:Cell ss:StyleID="header"></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Undefined:</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">$'.number_format($object_total, 2, '.', '').'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"></ss:Cell>
						</ss:Row>
					</ss:Table></ss:Worksheet>';
					}
					$cntn .= '<ss:Worksheet ss:Name="'.$inv['oname'].'">
							<ss:Table>
						<ss:Column ss:StyleID="col" ss:Width="50"></ss:Column>
						<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
						<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
						<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
						<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
						<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
						<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
						<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Row>
						<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">ID</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Customer</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Phone</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Unpaid</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Issue</ss:Data></ss:Cell>
					</ss:Row>';
					$oid = $inv['object_id'];
					$object_total = 0;
				}
				
				$style = 'def';
				
				if ($row['discount'])
					$discount = array_values(json_decode(($row['discount'] ?: '{}'), true));
				
				$total = ($discount[0]['percent'] ? $inv['total_paid'] * (100 - $discount[0]['percent']) / 100 : $inv['total_paid']);
				$object_total += $total;
				
				if (number_format($total, 2, '.', '') != number_format($inv['paid'], 2, '.', '')) {
					$cntn .= '<ss:Row>
						<ss:Cell ss:StyleID="isImportant" ss:HRef="https://admin.yoursite.com/invoices/view/'.$inv['id'].'"><ss:Data ss:Type="Number">'.$inv['id'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="isFinished"'.(
							$inv['customer_id'] ? ' ss:HRef="https://admin.yoursite.com/users/view/'.$inv['customer_id'].'"' : ''
						).'><ss:Data ss:Type="String">'.(
							$inv['customer_id'] ? $inv['name'].' '.$inv['lastname'] : 'Quick sell'
						).'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$inv['phone'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$inv['date'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($total, 2, '.', '').'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.($inv['issue_id'] ? 'isAll' : 'isNew').'"'.(
							$inv['issue_id'] ? ' ss:HRef="https://admin.yoursite.com/issues/view/'.$inv['issue_id'].'"' : ''
						).'><ss:Data ss:Type="String">'.($inv['issue_id'] ? 'Issue #'.$inv['issue_id'] : 'Purchased').'</ss:Data></ss:Cell>
					</ss:Row>';
				}
			}
		}
		
		$cntn .= '<ss:Row>
					<ss:Cell ss:StyleID="header"></ss:Cell>
					<ss:Cell ss:StyleID="header"></ss:Cell>
					<ss:Cell ss:StyleID="header"></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Undefined:</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">$'.number_format($object_total, 2, '.', '').'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"></ss:Cell>
				</ss:Row>
			</ss:Table>
		</ss:Worksheet>'.$footer;
		echo $cntn;
	break;
	
	case 'issues':
		$query = text_filter($_GET['query'], 255, false);
		$object = text_filter($_GET['object'], 50, false);
		$date_start = text_filter($_GET['date_start'], 30, true);
		$date_finish = text_filter($_GET['date_finish'], 30, true);
		$page = intval($_GET['page']);
		$staff = intval($_GET['staff']);
		$status = intval($_GET['status']);
		$cstatus = intval($_GET['current_status']);
		$count = 5000;
	
		$head = false;
		$cntn  = $header.'<ss:Worksheet ss:Name="WorksheetName">
				<ss:Table>
			<ss:Column ss:StyleID="col" ss:Width="50"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="150"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
		<ss:Row>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">ID</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Customer</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Total</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Device Type</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Assigned</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Location</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Current Status</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Status</ss:Data></ss:Cell>
		</ss:Row>';

		xls_header('issues');
		db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
			iss.id as ID,
			iss.important as important,
			inv.status_id as inv_status_id,
			inv.customer_id as uID,
			inv.location_count as location_count,
			iss.staff_id as sID,
			iss.total as Total,
			CONCAT(u.name, \' \', u.lastname) as User,
			DATE(iss.date) as Date,
			t.name as DeviceType,
			CONCAT(m.name, \' \', m.lastname) as Assigned,
			l.name as Location,
			s.name as Status,
			st.name as selected_status,
			o.name as object_name			
		FROM `'.DB_PREFIX.'_issues_changelog` cl
		LEFT JOIN `'.DB_PREFIX.'_issues` iss
			ON iss.id = cl.issue_id
		LEFT JOIN `'.DB_PREFIX.'_inventory` inv
			ON inv.id = iss.inventory_id
		LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
			ON inv.category_id = c.id
		LEFT JOIN `'.DB_PREFIX.'_inventory_types` t
			ON inv.type_id = t.id
		LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
			ON inv.status_id = s.id
		LEFT JOIN `'.DB_PREFIX.'_inventory_status` st
			ON '.($status ?: 0).' = st.id
		LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
			ON inv.location_id = l.id 
		LEFT JOIN `'.DB_PREFIX.'_users` m
			ON m.id = iss.staff_id
		LEFT JOIN `'.DB_PREFIX.'_objects` o
			ON o.id = inv.object_id
		LEFT JOIN `'.DB_PREFIX.'_users` u
			ON u.id = inv.customer_id
			WHERE 1 '.(
			$object ? ' AND inv.object_id = \''.$object.'\'' : ''
		).(
			$staff ? ' AND iss.staff_id = \''.$staff.'\'' : ''
		).(
			$cstatus > 0 ? 'AND inv.status_id = '.$cstatus : ($status > 0 ? ($status == 11 ? 'AND cl.changes = \'New issue\'' : 'AND cl.changes_id = '.$status. ' AND cl.changes = \'status\' ') : '')
		).(
			$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
		).(
			($date_start AND $date_finish) ? ' AND cl.date >= CAST(\''.$date_start.'\' AS DATE) AND cl.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
		).' GROUP BY cl.issue_id, DATE(cl.date) ORDER BY iss.id DESC LIMIT '.($page*$count).', '.$count, true, false, function($a) use(&$head, &$cntn){
			/* if(!$head){
				print implode("\t", array_keys($a))."\r\n";
				$head = true;
			} print implode("\t", array_values($a))."\r\n"; */
			$style = 'isAll';
			$status = $a['Status'];
			if (strtolower($a['inv_status_id']) == 2) {
				$style = 'isFinished';
				$status = 'Finished';
			} else if (strtolower($a['inv_status_id']) == 11) {
				$style = 'isNew';
				$status = 'New';
			} else if ($a['important']) {
				$style = 'isImportant';
			}
			$cntn .= '<ss:Row>
				<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="Number">'.$a['ID'].'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'"'.(
					$a['uID'] ? ' ss:HRef="http://admin.yoursite.com/users/view/'.$a['uID'].'"' : ''
				).'><ss:Data ss:Type="String">'.(
					$a['uID'] ? $a['User'] : $a['object_name']
				).'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Date'].'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['Total'], 2, '.', '').'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['DeviceType'].'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'" ss:HRef="http://admin.yoursite.com/users/view/'.$a['sID'].'"><ss:Data ss:Type="String">'.$a['Assigned'].'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Location'].' '.$a['location_count'].'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Status'].'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.($status > 0 ? $a['selected_status'] : $a['Status']).$a['selected_status'].'</ss:Data></ss:Cell>
			</ss:Row>';
		});
		$cntn .= '</ss:Table>
		</ss:Worksheet>'.$footer;
		echo $cntn;
	break;
	
	case 'activity':
		$query = text_filter($_GET['query'], 255, false);
		$event = text_filter($_GET['event'], 50, false);
		$date_start = text_filter($_GET['date_start'], 30, true);
		$date_finish = text_filter($_GET['date_finish'], 30, true);
		$page = intval($_GET['page']);
		$staff = intval($_GET['staff']);
		$count = 100;
		
		$head = false;
		$cntn = $header.'<ss:Worksheet ss:Name="WorksheetName">
				<ss:Table>
			<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="300"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="150"></ss:Column>
		<ss:Row>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">User</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Event</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
		</ss:Row>';
		xls_header('activity');
		db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
						u.id as uID, 
						CONCAT(u.name, \' \', u.lastname) as User, 
						a.date as Date, 
						a.event as Event, 
						a.event_id as event_id, 
						SEC_TO_TIME(t.seconds) as Time FROM
			`'.DB_PREFIX.'_activity` a
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON a.user_id = u.id 
				LEFT JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = a.user_id AND t.date >= CURDATE()
				WHERE 1 '.(
				$event ? ' AND a.event = \''.$event.'\'' : ''
			).(
				$staff ? 'AND a.user_id = '.$staff.' ' : ''
			).(
				$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND a.date >= CAST(\''.$date_start.'\' AS DATE) AND a.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'ORDER BY a.id DESC LIMIT '.($page*$count).', '.$count, true, false, function($a) use(&$head, &$cntn){
				/* if(!$head){
					print implode("\t", array_keys($a))."\r\n";
					$head = true;
				} print implode("\t", array_values($a))."\r\n"; */
				$style = 'def';
				switch($a['Event']){
					case 'stop working time':
						$event = 'stop working time ('.$a['Time'].')';
						$style = 'isNew';
					break;	

					case 'start working time':
						$style = 'isFinished';
					break;						
					
					case 'add_purchase':
						$event = 'Add new purchase';
						$href = 'http://admin.yoursite.com/purchases/edit/'.$a['event_id'];
						$style = 'def';
					break;
					
					default:
						$event = str_replace('_', ' ', $a['Event']);
						$style = 'def';
					break;
				}
				$cntn .= '<ss:Row>
						<ss:Cell ss:StyleID="'.$style.'" ss:HRef="http://admin.yoursite.com/users/view/'.$a['uID'].'"><ss:Data ss:Type="String">'.$a['User'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"'.(
							$href ? ' ss:HRef="'.$href.'"' : ''
						).'><ss:Data ss:Type="String">'.($event ?: $a['Event']).'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Date'].'</ss:Data></ss:Cell>
					</ss:Row>';
			});
			$cntn .= '</ss:Table>
		</ss:Worksheet>'.$footer;
			echo $cntn;
	break;
	
	case 'timer':
		$query = text_filter($_GET['query'], 255, false);
		$object = intval($_GET['object']);
		$date_start = text_filter($_GET['date_start'], 30, true);
		$date_finish = text_filter($_GET['date_finish'], 30, true);
		$salary_check = intval($_GET['salary']);
		$tax = intval($_GET['tax']);
		$hours = intval($_GET['hours']);
		$staff = intval($_GET['user']);
		$page = intval($_GET['page']);
		$user_time = intval($_GET['user_time']);
		$group = intval($_GET['group']);
		$count = 100;
		$now_date = null;
		
		$head = false;
		//xls_header('timer');
		$style = 'def';
		if ($date_start AND $date_finish OR $user_time) {
			$total = 0;
			$id = 0;
			$pay = 0;
			$salary = 0;
			$salary_user = 0;
			$salary_user_fee = 0;
			$week_salary = 0;
			$week_salary_fee = 0;
			$week_total = 0;
			
			$empty = '<ss:Row>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						'.($hours ? '<ss:Cell></ss:Cell>' : '').'
						'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell></ss:Cell>' : '') : '').'
					</ss:Row>';
			$cntn = $header.'<ss:Worksheet ss:Name="WorksheetName">
					<ss:Table>
				<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				'.($hours ? '<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>' : '').'
				'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>' : '') : '');
			$sql = db_multi_query('SELECT SQL_CALC_FOUND_ROWS
					u.id as UserID,
					CONCAT(u.name, \' \', u.lastname) as Staff,
					u.pay,
					DATE(t.date) as Date,
					TIME(t.date) as PunchIn,
					TIME(t.break_start) as BreakStart,
					TIME(t.break_finish) as BreakEnd,
					TIME(t.control_point) as PunchOut,				
					SEC_TO_TIME(t.seconds) as WorkingTime,
					t.seconds as total,
					t.per_hour,
					t.flag,
					u.pay,
					o.name as Object,
					o.salary_tax as salary
				FROM `'.DB_PREFIX.'_timer` t
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON t.user_id = u.id 	
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id	
				WHERE t.hide = 0 AND t.event = \'stop\''.(
				$object ? ' AND o.id = \''.$object.'\'' : ''
			).(
				$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				$staff ? 'AND t.user_id = '.$staff.' ' : ''
			).(
				($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'ORDER BY '.(
				($date_start AND $date_finish) ? 't.user_id DESC, DATE(t.date)' : 't.date'
			).' DESC', true, false, function($a) use(&$week_salary, &$week_salary_fee, &$week_total, &$head, &$id, &$total, &$cntn, &$style, &$empty, &$pay, &$salary, &$user, &$group, &$salary_user, &$salary_user_fee, &$salary_check, &$tax, &$hours, &$now_date){
				/* if ($id != $a['UserID']) {
					if ($id) {
						$s = $total % 60;
						$m = ($total % 3600 - $s) / 60;
						$h = ($total % 86400 - $s - $m * 60) / 3600;
						print "\t\t\t\t\t\t\tTotal\t".$h.':'.$m.':'.$s."\r\n\r\n";
						print implode("\t", array_keys($a))."\r\n";
					} else {
						print implode("\t", array_keys($a))."\r\n";
					}
					$id = $a['UserID'];
					$total = 0;
				} 
				$total += strtotime($a['WorkingTime']);
				print implode("\t", array_values($a))."\r\n"; */
				if (!$now_date)
					$now_date = $a['Date'];
				if(isset($group) && $group && $now_date){
					$sd = date_create($a['Date']);
					$sd2 = date_create($now_date);
					$diff = (int)date_diff($sd, $sd2)->days;
					if($diff > ($group == 1 ? 7 : 14)){
						$s = $week_total % 60;
						$m = ($week_total % 3600 - $s) / 60;
						$h = ($week_total - $s - $m * 60) / 3600;
						$cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							'.($hours ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
							'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
						</ss:Row>';
						$week_total = 0;
						$week_salary = 0;
						$week_salary_fee = 0;
						$now_date = $a['Date'];
					}
				}
					
				if ($id != $a['UserID']) {
					if ($id) {
						$s = $week_total % 60;
						$m = ($week_total % 3600 - $s) / 60;
						$h = ($week_total - $s - $m * 60) / 3600;
						$cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							'.($hours ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
							'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
						</ss:Row>';
						$week_total = 0;
						$week_salary = 0;
						$week_salary_fee = 0;
						
						$s = $total % 60;
						$m = ($total % 3600 - $s) / 60;
						$h = ($total - $s - $m * 60) / 3600;
						$cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="isFinished"></ss:Cell>
							<ss:Cell ss:StyleID="isFinished"></ss:Cell>
							<ss:Cell ss:StyleID="isFinished"></ss:Cell>
							<ss:Cell ss:StyleID="isFinished"></ss:Cell>
							<ss:Cell ss:StyleID="isFinished"></ss:Cell>
							<ss:Cell ss:StyleID="isFinished"></ss:Cell>
							'.($hours ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
							'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($salary_user, 2, '.', '').($tax ? '/$'.number_format($salary_user_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
						</ss:Row>'.$empty.$empty;//'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($total/3600*$pay, 2, '.', '').($salary ? '/$'.number_format($total/3600*$pay*((100 + $salary)/100), 2, '.', '') : '').'</ss:Data></ss:Cell>' : '').'
					} 
					$cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="header" ss:HRef="http://admin.yoursite.com/users/view/'.$a['uID'].'"><ss:Data ss:Type="String">'.$a['Staff'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch In</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break Start</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break End</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch Out</ss:Data></ss:Cell>
							'.($hours ? '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Working Time</ss:Data></ss:Cell>' : '').'
							'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Salary</ss:Data></ss:Cell>' : '') : '').'
						</ss:Row>';
					$id = $a['UserID'];
					$total = 0;
					$pay = $a['pay'];
					$salary = $a['salary'];
					$salary_user = 0;
					$salary_user_fee = 0;
				} 
				$total += $a['total'];
				$salary_user += $a['flag'] ? $a['total']/3600*$a['per_hour'] : $a['total']/3600*$a['pay'];
				$salary_user_fee += $a['flag'] ? $a['total']/3600*$a['per_hour']*1.2 : $a['total']/3600*$a['pay']*((100 + $a['salary'])/100);
				$week_total += $a['total'];
				$week_salary += $a['flag'] ? $a['total']/3600*$a['per_hour'] : $a['total']/3600*$a['pay'];
				$week_salary_fee += $a['flag'] ? $a['total']/3600*$a['per_hour']*1.2 : $a['total']/3600*$a['pay']*((100 + $a['salary'])/100);
				$cntn .= '<ss:Row>
					<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Object'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Date'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchIn'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakStart'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakEnd'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchOut'].'</ss:Data></ss:Cell>
					'.($hours ? '<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['WorkingTime'].'</ss:Data></ss:Cell>' : '').'
					'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
						($a['flag'] ?
							'<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['per_hour'], 2, '.', '').($tax ? '/$'.number_format($a['total']/3600*$a['per_hour']*1.2, 2, '.', '') : '').'</ss:Data></ss:Cell>' :
							'<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($tax ?  ($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '') : '').'</ss:Data></ss:Cell>'
						) : '') : '').'
				</ss:Row>';
			}); 
			echo '<pre>';
			print_r($sql);
			die;
			//'<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '').'</ss:Data></ss:Cell>' : '').'
			/* $s = $total % 60;
			$m = ($total % 3600 - $s) / 60;
			$h = ($total % 86400 - $s - $m * 60) / 3600;
			print "\t\t\t\t\t\t\tTotal\t".$h.':'.$m.':'.$s."\r\n"; */
			$s = $total % 60;
			$m = ($total % 3600 - $s) / 60;
			$h = ($total - $s - $m * 60) / 3600;
			$cntn .= '<ss:Row>
					<ss:Cell ss:StyleID="isFinished"></ss:Cell>
					<ss:Cell ss:StyleID="isFinished"></ss:Cell>
					<ss:Cell ss:StyleID="isFinished"></ss:Cell>
					<ss:Cell ss:StyleID="isFinished"></ss:Cell>
					<ss:Cell ss:StyleID="isFinished"></ss:Cell>
					<ss:Cell ss:StyleID="isFinished"></ss:Cell>
					'.($hours ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
					'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($salary_user, 2, '.', '').($tax ? '/$'.number_format($salary_user_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
				</ss:Row>
			</ss:Table>
		</ss:Worksheet>'.$footer; //'.((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($total/3600*$pay, 2, '.', '').($sqlsalary ? '/$'.number_format($total/3600*$pay*((100 + $sqlsalary)/100), 2, '.', '') : '').'</ss:Data></ss:Cell>' : '').'
			echo $cntn;
		} else {
			$cntn = $header.'<ss:Worksheet ss:Name="WorksheetName">
					<ss:Table>
				<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				'.($hours ? '<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>' : '').'
				'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>' : '') : '').'
			<ss:Row>
				<ss:Cell ss:StyleID="header" ss:HRef="http://admin.yoursite.com/users/view/'.$a['uID'].'"><ss:Data ss:Type="String">'.$a['Staff'].'</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch In</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break Start</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break End</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch Out</ss:Data></ss:Cell>
				'.($hours ? '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Working Time</ss:Data></ss:Cell>' : '').'
				'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Salary</ss:Data></ss:Cell>' : '') : '').'
			</ss:Row>';
			db_multi_query('SELECT SQL_CALC_FOUND_ROWS
					CONCAT(u.name, \' \', u.lastname) as Staff,
					u.pay,
					DATE(t.date) as Date,
					DATE(t.user_id) as uID,
					TIME(t.date) as PunchIn,
					TIME(t.break_start) as BreakStart,
					TIME(t.break_finish) as BreakEnd,
					TIME(t.control_point) as PunchOut,				
					SEC_TO_TIME(t.seconds) as WorkingTime,
					t.per_hour,
					t.flag,
					u.pay as pay,
					t.seconds as total,
					o.salary_tax as salary
				FROM `'.DB_PREFIX.'_timer` t
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON t.user_id = u.id 	
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id	
				WHERE t.hide = 0 AND t.event = \'stop\''.(
				$object ? ' AND o.id = \''.$object.'\'' : ''
			).(
				$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'ORDER BY '.(
				($date_start AND $date_finish) ? 't.user_id DESC, DATE(t.date)' : 't.date'
			).' DESC', true, false, function($a) use(&$week_salary, &$week_salary_fee, &$week_total, &$head, &$cntn, &$user, &$salary_check, &$tax, &$hours, &$group, &$now_date){
				if (!$now_date)
					$now_date = $a['Date'];
				if(isset($group) && $group){
					$sd = date_create($a['Date']);
					$sd2 = date_create($now_date);
					$diff = (int)date_diff($sd, $sd2)->days;
					if($diff > ($group == 1 ? 7 : 14)) {
						$s = $week_total % 60;
						$m = ($week_total % 3600 - $s) / 60;
						$h = ($week_total - $s - $m * 60) / 3600;
						$cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							'.($hours ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
							'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
						</ss:Row>';
						$week_total = 0;
						$week_salary = 0;
						$week_salary_fee = 0;
						if (!$now_date)
							$now_date = $a['Date'];
					}
					//echo $_SESSION['now_date'].' '.intval($_SESSION['line']).' '.$diff.'<br>';
				}
				
				$week_total += $a['total'];
				$week_salary += $a['flag'] ? $a['total']/3600*$a['per_hour'] : $a['total']/3600*$a['pay'];
				$week_salary_fee += $a['flag'] ? $a['total']/3600*$a['per_hour']*1.2 : $a['total']/3600*$a['pay']*((100 + $a['salary'])/100);
				$style = 'def';
				$cntn .= '<ss:Row>
						<ss:Cell ss:StyleID="'.$style.'" ss:HRef="http://admin.yoursite.com/users/view/'.$a['uID'].'"><ss:Data ss:Type="String">'.$a['Staff'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Date'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchIn'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakStart'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakEnd'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchOut'].'</ss:Data></ss:Cell>
						'.($hours ? '<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['WorkingTime'].'</ss:Data></ss:Cell>' : '').'
						'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
							($row['flag'] ? 
								'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['per_hour'], 2, '.', '').($tax ? '/$'.number_format($a['total']/3600*$a['per_hour']*1.2, 2, '.', '') : '').'</ss:Data></ss:Cell>' :
								'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($tax ? ($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '') : '').'</ss:Data></ss:Cell>'
							)
						//'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '').'</ss:Data></ss:Cell>'	
						: '') : '').'
					</ss:Row>';
			});
			$s = $week_total % 60;
			$m = ($week_total % 3600 - $s) / 60;
			$h = ($week_total - $s - $m * 60) / 3600;
			$cntn .= '<ss:Row>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				'.($hours ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
				'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
			</ss:Row>';
			
			$cntn .= '</ss:Table>
		</ss:Worksheet>'.$footer;
			echo $cntn;
			
		}
		
	break;
	
	case 'timer2':
		$query = text_filter($_GET['query'], 255, false);
		$object = intval($_GET['object']);
		$date_start = text_filter($_GET['date_start'], 30, true);
		$date_finish = text_filter($_GET['date_finish'], 30, true);
		$salary_check = intval($_GET['salary']);
		$tax = intval($_GET['tax']);
		$hours = intval($_GET['hours']);
		$staff = intval($_GET['user']);
		$page = intval($_GET['page']);
		$user_time = intval($_GET['user_time']);
		$group = intval($_GET['group']);
		$count = 100;
		$now_date = null;
		$arr = [];
		$usr_arr = [];
		
		$head = false;
		xls_header('timer');
		$style = 'def';

			$cntn = $header.'<ss:Worksheet ss:Name="WorksheetName">
					<ss:Table>
				<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				'.($hours ? '<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>' : '').'
				'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>' : '') : '').'
			';
			$empty = '<ss:Row>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						<ss:Cell></ss:Cell>
						'.($hours ? '<ss:Cell></ss:Cell>' : '').'
						'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell></ss:Cell>' : '') : '').'
					</ss:Row>';
			db_multi_query('SELECT SQL_CALC_FOUND_ROWS
					CONCAT(u.name, \' \', u.lastname) as Staff,
					u.pay,
					DATE(t.date) as Date,
					t.user_id as uID,
					TIME(t.date) as PunchIn,
					TIME(t.break_start) as BreakStart,
					TIME(t.break_finish) as BreakEnd,
					TIME(t.control_point) as PunchOut,				
					SEC_TO_TIME(t.seconds) as WorkingTime,
					t.per_hour,
					t.flag,
					u.pay as pay,
					t.seconds as total,
					o.salary_tax as salary
				FROM `'.DB_PREFIX.'_timer` t
				INNER JOIN `'.DB_PREFIX.'_users` u
					ON t.user_id = u.id 	
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON o.id = t.object_id	
				WHERE t.hide = 0 AND t.flag = 1 AND t.object_id != 7 AND t.event = \'stop\' AND t.user_id NOT IN(23059,23973,26022,96,24838,28,1,13004,26494) '.(
				$object ? ' AND o.id = \''.$object.'\'' : ''
			).(
				$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'ORDER BY '.(
				($date_start AND $date_finish) ? 't.date' : 't.date'
			).' ASC', true, false, function($a) use(&$empty, &$usr_arr, &$week_salary, &$week_salary_fee, &$week_total, &$head, &$cntn, &$user, &$salary_check, &$tax, &$hours, &$group, &$now_date, &$arr){
				
				if (!$now_date)
					$now_date = $a['Date'];
				if(isset($group) && $group){
					$sd = date_create($a['Date']);
					$sd2 = date_create($now_date);
					$diff = (int)date_diff($sd, $sd2)->days;
					if(($diff > ($group == 1 ? 6 : 13)) OR ($group == 2 AND ($a['Date'] == '2020-11-20' OR $a['Date'] == '2020-12-04' OR $a['Date'] == '2020-12-05') AND ($now_date != '2020-11-20' AND $now_date != '2020-12-04' AND $now_date != '2020-12-05'))) {
						$s = $week_total % 60;
						$m = ($week_total % 3600 - $s) / 60;
						$h = ($week_total - $s - $m * 60) / 3600;
						/* $cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							<ss:Cell ss:StyleID="def"></ss:Cell>
							'.($hours ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
							'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
						</ss:Row>'; */
						$week_total = 0;
						$week_salary = 0;
						$week_salary_fee = 0;
						$arr[] = $usr_arr;
						$usr_arr = [];
						$now_date = $a['Date'];
					}
					//echo $_SESSION['now_date'].' '.intval($_SESSION['line']).' '.$diff.'<br>';
				}
				
				$week_total += $a['total'];
				$week_salary += $a['flag'] ? $a['total']/3600*$a['per_hour'] : $a['total']/3600*$a['pay'];
				$week_salary_fee += $a['flag'] ? $a['total']/3600*$a['per_hour']*1.2 : $a['total']/3600*$a['pay']*((100 + $a['salary'])/100);
				$style = 'def';
				/* $cntn .= '<ss:Row>
						<ss:Cell ss:StyleID="'.$style.'" ss:HRef="http://admin.yoursite.com/users/view/'.$a['uID'].'"><ss:Data ss:Type="String">'.$a['Staff'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Date'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchIn'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakStart'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakEnd'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchOut'].'</ss:Data></ss:Cell>
						'.($hours ? '<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['WorkingTime'].'</ss:Data></ss:Cell>' : '').'
						'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
							($a['flag'] ? 
								'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['per_hour'], 2, '.', '').($tax ? '/$'.number_format($a['total']/3600*$a['per_hour']*1.2, 2, '.', '') : '').'</ss:Data></ss:Cell>' :
								'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($tax ? ($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '') : '').'</ss:Data></ss:Cell>'
							)
						//'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '').'</ss:Data></ss:Cell>'	
						: '') : '').'
					</ss:Row>'; */
				if (!$usr_arr[$a['uID']])
					$usr_arr[$a['uID']] = [];
				$usr_arr[$a['uID']][] = [
					'name' => $a['Staff'],
					'row' => '<ss:Row>
							<ss:Cell ss:StyleID="'.$style.'"></ss:Cell>
							<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['Date'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchIn'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakStart'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['BreakEnd'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['PunchOut'].'</ss:Data></ss:Cell>
							'.($hours ? '<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['WorkingTime'].'</ss:Data></ss:Cell>' : '').'
							'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? 
								($a['flag'] ? 
									'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['per_hour'], 2, '.', '').($tax ? '/$'.number_format($a['total']/3600*$a['per_hour']*1.2, 2, '.', '') : '').'</ss:Data></ss:Cell>' :
									'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($tax ? ($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '') : '').'</ss:Data></ss:Cell>'
								)
							//'<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">$'.number_format($a['total']/3600*$a['pay'], 2, '.', '').($a['salary'] ? '/$'.number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '').'</ss:Data></ss:Cell>'	
							: '') : '').'
						</ss:Row>',
					'hours' => $a['total'],
					'total' => [
						($a['flag'] ? number_format($a['total']/3600*$a['per_hour'], 2, '.', '') : number_format($a['total']/3600*$a['pay'], 2, '.', '')),
						($a['flag'] ? number_format($a['total']/3600*$a['per_hour']*1.2, 2, '.', '') : ($a['salary'] ? number_format($a['total']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : 0))
					]
				];
			});
			
			$arr[] = $usr_arr;
			
			echo '<pre>';
			print_r($arr);
			die;
			foreach($arr as $k => $a) {
				if ($k != 0) 
					$cntn .= $empty;
				$cntn .= '<ss:Row>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Week №'.($k + 1).'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch In</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break Start</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Break End</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch Out</ss:Data></ss:Cell>
					'.($hours ? '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Working Time</ss:Data></ss:Cell>' : '').'
					'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Salary</ss:Data></ss:Cell>' : '') : '').'
				</ss:Row>';
				
				foreach($a as $uid => $u) {
					$total = 0;
					$total_tax = 0;
					$total_hours = 0;
					foreach($u as $iid => $info) {
						if (!$iid) {
							$cntn .= '<ss:Row>
								<ss:Cell ss:StyleID="isFinished" ss:HRef="http://admin.yoursite.com/users/view/'.$uid.'"><ss:Data ss:Type="String">'.$info['name'].'</ss:Data></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								<ss:Cell ss:StyleID="isFinished"></ss:Cell>
								'.($hours ? '<ss:Cell ss:StyleID="isFinished"></ss:Cell>' : '').'
								'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="isFinished"></ss:Cell>' : '') : '').'
							</ss:Row>';
						}
						$cntn .= $info['row'];
						$total_hours +=  $info['hours'];
						$total +=  $info['total'][0];
						$total_tax +=  $info['total'][1];
					}
					$s = $total_hours % 60;
					$m = ($total_hours % 3600 - $s) / 60;
					$h = ($total_hours - $s - $m * 60) / 3600;
					$cntn .= '<ss:Row>
						<ss:Cell ss:StyleID="isFinished"></ss:Cell>
						<ss:Cell ss:StyleID="isFinished"></ss:Cell>
						<ss:Cell ss:StyleID="isFinished"></ss:Cell>
						<ss:Cell ss:StyleID="isFinished"></ss:Cell>
						<ss:Cell ss:StyleID="isFinished"></ss:Cell>
						<ss:Cell ss:StyleID="isFinished"></ss:Cell>
						'.($hours ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
						'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format($total, 2, '.', ' ').($tax ? '/$'.number_format($total_tax, 2, '.', ' ') : '').'</ss:Data></ss:Cell>' : '') : '').'
					</ss:Row>'.$empty;
				}
			}
			/* $s = $week_total % 60;
			$m = ($week_total % 3600 - $s) / 60;
			$h = ($week_total - $s - $m * 60) / 3600;
			$cntn .= '<ss:Row>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				<ss:Cell ss:StyleID="def"></ss:Cell>
				'.($hours ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>' : '').'
				'.($salary_check ? ((in_array(1, explode(',', $user['group_ids'])) OR in_array(2, explode(',', $user['group_ids']))) ? '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.number_format($week_salary, 2, '.', '').($tax ? '/$'.number_format($week_salary_fee, 2, '.', '') : '').'</ss:Data></ss:Cell>' : '') : '').'
			</ss:Row>'; */
			
			$cntn .= '</ss:Table>
		</ss:Worksheet>'.$footer;
			echo $cntn;
			

		
	break;
	
	case 'object':
		xls_header('object_report');
		$ctnr = $header;
		
		$objects = ids_filter($_GET['objects']);
		$date_start = text_filter($_GET['date_start'], 30, true);
		$date_finish = text_filter($_GET['date_finish'], 30, true);
		
		$stores = '<ss:Row><ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Total</ss:Data></ss:Cell><ss:Cell ss:StyleID="header"></ss:Cell></ss:Row>';
		$sum = db_multi_query('SELECT
							o.name,
							o.id as id,
							COUNT(ih.amount) as count,
							SUM(ih.amount) as total, 
							i.object_id,
							SUM(c.amount) as cash,
							SUM(cr.amount) as credit,
							SUM(ch.amount) as tcheck
						FROM `'.DB_PREFIX.'_invoices` i
						LEFT JOIN `'.DB_PREFIX.'_invoices_history` ih
							ON ih.invoice_id = i.id '.(
							($date_start AND $date_finish) ? 
								' AND ih.date >= CAST(\''.$date_start.'\' AS DATE) AND ih.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						LEFT JOIN `'.DB_PREFIX.'_invoices_history` c
							ON c.id = ih.id AND c.type = \'cash\'
						LEFT JOIN `'.DB_PREFIX.'_invoices_history` cr
							ON cr.id = ih.id AND cr.type = \'credit\'
						LEFT JOIN `'.DB_PREFIX.'_invoices_history` ch
							ON ch.id = ih.id AND ch.type = \'check\'
						LEFT JOIN `'.DB_PREFIX.'_objects` o
							ON o.id = i.object_id
						WHERE 1 '.($objects ? 'AND i.object_id IN ('.$objects.')' : '').'
						'.(
							($date_start AND $date_finish) ? 
								' AND i.date >= CAST(\''.$date_start.'\' AS DATE) AND i.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						GROUP BY i.object_id ORDER BY i.object_id', true);
		$lack = db_multi_query('SELECT
							object_id,
							SUM(lack) as lack
						FROM `'.DB_PREFIX.'_cash`
						WHERE 1 '.($objects ? 'AND object_id IN ('.$objects.')' : '').'
							AND type = \'cash\'
						'.(
							($date_start AND $date_finish) ? 
								  ' AND date >= CAST(\''.$date_start.'\' AS DATE) AND date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
						).'
						GROUP BY object_id ORDER BY object_id', true);
				
		// Working time
		$timers = db_multi_query('
			SELECT DISTINCT
				t.*,
				o.salary_tax,
				SEC_TO_TIME(t.seconds) as seconds,
				t.seconds as seconnds2,
				u.name,
				u.lastname,
				u.image,
				u.pay,
				o.id as object_id
			FROM `'.DB_PREFIX.'_timer` t
			INNER JOIN `'.DB_PREFIX.'_users` u
				ON t.user_id = u.id 	
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = t.object_id
			WHERE t.event = \'stop\''.(
					$objects ? 'AND t.object_id IN ('.$objects.')' : ''
				).'
			'.(
				($date_start AND $date_finish) ? 
					  ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'
			ORDER BY t.object_id, t.user_id, t.date DESC LIMIT 0, 15',
			true);	
			
		$timer = [];
		$oid = '';
		foreach($timers as $item) {
			if ($oid != $item['object_id']) {
				$timer[$item['object_id']] = '';
				$oid = $item['object_id'];
			}
			$timer[$item['object_id']] .= '<ss:Row>
					<ss:Cell ss:StyleID="def" ss:HRef="http://admin.yoursite.com/users/view/'.$item['user_id'].'"><ss:Data ss:Type="String">'.$item['name'].' '.$item['lastname'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$item['date'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$item['control_point'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$item['seconds'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.(number_format($item['seconnds2']/3600*$item['pay'], 2, '.', '').($item['salary_tax'] ? ' / '.'$'.number_format($item['seconnds2']/3600*$item['pay']+((($item['seconnds2']/3600*$item['pay'])/100)*$item['salary_tax']), 2, '.', '') : '')).'</ss:Data></ss:Cell>
				</ss:Row>';
		}
		
		
		// Issue status changed
		$istatus_title = '<ss:Row>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">User</ss:Data></ss:Cell>';
		foreach($status = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_inventory_status`', true) as $st) {
			$istatus_title .= '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">'.$st['name'].'</ss:Data></ss:Cell>';
		}
		$istatus_title .= '</ss:Row>';
		$istatus_none = '<ss:Row><ss:Cell ss:StyleID="def" ss:MergeAcross="'.count($status).'"><ss:Data ss:Type="String">No info</ss:Data></ss:Cell></ss:Row>'; 
		$status = array_column($status, 'name', 'id');
		
		$istatus = [];
		$user_id = 0;
		$users = [];
		$statuses = db_multi_query('SELECT
						CONCAT(u.name, \' \', u.lastname) as name,
						sh.staff_id as user_id,
						s.name as status,
						sh.status_id as status_id,
						sh.object_id as object_id,
						COUNT(sh.status_id) as count_status
					FROM `'.DB_PREFIX.'_inventory_status_history` sh
					LEFT JOIN `'.DB_PREFIX.'_users` u
						ON u.id = sh.staff_id
					LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
						ON s.id = sh.status_id
					WHERE 1 '.(
						$objects ? 'AND sh.object_id IN ('.$objects.')' : ''
					).(
						($date_start AND $date_finish) ? 
							  ' AND sh.date >= CAST(\''.$date_start.'\' AS DATE) AND sh.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
					).'
					GROUP BY sh.object_id, sh.staff_id, sh.status_id ORDER BY sh.staff_id, sh.status_id', true);
					
		foreach($statuses as $row) {
			$users[$row['object_id']][$row['user_id']]['statuses'][$row['status_id']] = $row['count_status'];
			$users[$row['object_id']][$row['user_id']]['name'] = $row['name'];
		}
		foreach($users as $oid => $obj) {
			$istatus[$oid] = '';
			foreach($obj as $usr) {
				$istatus[$oid] .= '<ss:Row>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$usr['name'].'</ss:Data></ss:Cell>';
				foreach($status as $i => $st) {
					$istatus[$oid] .= '<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.($usr['statuses'][$i] ? $usr['statuses'][$i] : '0').'</ss:Data></ss:Cell>';
				}
				$istatus[$oid] .= '</ss:Row>';
			}
		}
		
		// Transactions
		$otran = [];
		$tran = db_multi_query('SELECT 
									i.id, 
									i.date, 
									i.customer_id,
									i.pay_method,
									i.total,
									i.object_id,
									u.name,
									u.lastname
								FROM `'.DB_PREFIX.'_invoices` i
								LEFT JOIN `'.DB_PREFIX.'_users` u
									ON u.id = i.customer_id
								WHERE 1 '.(
									$objects ? 'AND i.object_id IN ('.$objects.')' : ''
								).'
								AND i.conducted = 1
								'.(
									($date_start AND $date_finish) ? 
										  ' AND i.date >= CAST(\''.$date_start.'\' AS DATE) AND i.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
								).'
								ORDER BY i.object_id, i.date', true);
		$oid = 0;
		foreach($tran as $i => $row){
			if ($oid != $row['object_id']) {
				$otran[$row['object_id']] = '';
				$oid = $row['object_id'];
			}
			$otran[$row['object_id']] .= '<ss:Row>
										<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['name'].' '.$row['lastname'].'</ss:Data></ss:Cell>
										<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['id'].'</ss:Data></ss:Cell>
										<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['date'].'</ss:Data></ss:Cell>
										<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['pay_method'].'</ss:Data></ss:Cell>
										<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">$'.number_format($row['total'], 2, '.', '').'</ss:Data></ss:Cell>
									</ss:Row>';
		}
		
		$ostats = [];
		$stats = db_multi_query('SELECT 
									i.id, 
									i.date, 
									c.changes_id,
									d.customer_id,
									u.name,
									u.lastname,
									s.name as status_name,
									m.name as model_name,
									b.name as brand_name,
									d.model as model,
									c.object_id,
									o.name as object_name
								FROM `'.DB_PREFIX.'_issues_changelog` c
								LEFT JOIN `'.DB_PREFIX.'_issues` i
									ON i.id = c.issue_id
								LEFT JOIN `'.DB_PREFIX.'_inventory` d
									ON d.id = i.inventory_id
								LEFT JOIN `'.DB_PREFIX.'_users` u
									ON u.id = d.customer_id
								LEFT JOIN `'.DB_PREFIX.'_objects` o
									ON o.id = d.object_id
								LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
									ON s.id = d.status_id
								LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
									ON m.id = d.model_id
								LEFT JOIN `'.DB_PREFIX.'_inventory_categories` b
									ON b.id = d.category_id
								WHERE 1 '.(
									$objects ? 'AND c.object_id IN ('.$objects.')' : ''
								).(
									($date_start AND $date_finish) ? 
										  ' AND c.date >= CAST(\''.$date_start.'\' AS DATE) AND c.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
								).' 
								AND c.changes = \'status\'
								ORDER BY c.object_id, s.id, c.date', true);
		if ($stats) {			
			$oid = 0;
			foreach($stats as $i => $row){
				if ($oid != $row['object_id']) {
					$ostats[$row['object_id']] = [];
					$oid = $row['object_id'];
				}
				if (!$ostats[$row['object_id']][$row['changes_id']])
					$ostats[$row['object_id']][$row['changes_id']] = '<ss:Row>
							<ss:Cell ss:StyleID="col" ss:MergeAcross="3"></ss:Cell>
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="title" ss:MergeAcross="3"><ss:Data ss:Type="String">'.$status[$row['changes_id']].'</ss:Data></ss:Cell>
						</ss:Row>
						<ss:Row>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Customer</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Model</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Status</ss:Data></ss:Cell>
						</ss:Row>';
					
				$ostats[$row['object_id']][$row['changes_id']] .= '<ss:Row>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.($row['name'] ? $row['name'].' '.$row['lastname'] : $row['object_name']).'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['date'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['brand'].' '.$row['model_name'].' '.$row['model'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['status_name'].'</ss:Data></ss:Cell>
					</ss:Row>';
			}
		}

		$total = 0;
		foreach($sum as $i => $row){
			$total += $row['total'];
			$stores .= '<ss:Row>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$row['name'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.(floatval($row['total']) < 0 ? 'isNew' : 'isFinished').'"><ss:Data ss:Type="String">$'.number_format($row['total'], 2, '.', '').'</ss:Data></ss:Cell>
					</ss:Row>';
					
			$ctnr .= '<ss:Worksheet ss:Name="'.$row['name'].'">
				<ss:Table>
					<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Row>
					<ss:Cell ss:StyleID="isAll"><ss:Data ss:Type="String">'.$row['name'].'</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="isAll"></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">Transactions</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="isImportant"><ss:Data ss:Type="String">'.$row['count'].'</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">Total</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.(floatval($row['total']) < 0 ? 'isNew' : 'isFinished').'"><ss:Data ss:Type="String">$'.number_format($row['total'], 2, '.', '').'</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">Cash</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.(floatval($row['cash']) < 0 ? 'isNew' : 'isFinished').'"><ss:Data ss:Type="String">$'.number_format($row['cash'], 2, '.', '').'</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">Cash adjustments</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.(floatval($lack[$i]['lack']) < 0 ? 'isNew' : 'isFinished').'"><ss:Data ss:Type="String">$'.number_format($lack[$i]['lack'], 2, '.', '').'</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">Credit</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.(floatval($row['credit']) < 0 ? 'isNew' : 'isFinished').'"><ss:Data ss:Type="String">$'.number_format($row['credit'], 2, '.', '').'</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">Check</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="'.(floatval($row['tcheck']) < 0 ? 'isNew' : 'isFinished').'"><ss:Data ss:Type="String">$'.number_format($row['tcheck'], 2, '.', '').'</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="col"></ss:Cell>
					<ss:Cell ss:StyleID="col"></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="title" ss:MergeAcross="4"><ss:Data ss:Type="String">Working time</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Staff</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch in</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Punch out</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Working time</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Salary</ss:Data></ss:Cell>
				</ss:Row>'.($timer[$row['object_id']] ?: '<ss:Row>
					<ss:Cell ss:StyleID="def" ss:MergeAcross="4"><ss:Data ss:Type="String">No info</ss:Data></ss:Cell>
				</ss:Row>').'
				<ss:Row>
					<ss:Cell ss:StyleID="col"></ss:Cell>
					<ss:Cell ss:StyleID="col"></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="title" ss:MergeAcross="'.count($status).'"><ss:Data ss:Type="String">Issue status changes</ss:Data></ss:Cell>
				</ss:Row>'.$istatus_title.($istatus[$row['object_id']] ?: $istatus_none).'
				<ss:Row>
					<ss:Cell ss:StyleID="col"></ss:Cell>
					<ss:Cell ss:StyleID="col"></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="title" ss:MergeAcross="4"><ss:Data ss:Type="String">Transactions</ss:Data></ss:Cell>
				</ss:Row>
				<ss:Row>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Customer</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">ID</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Pay method</ss:Data></ss:Cell>
					<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Total</ss:Data></ss:Cell>
				</ss:Row>'.($otran[$row['object_id']] ?: '<ss:Row>
					<ss:Cell ss:StyleID="def" ss:MergeAcross="4"><ss:Data ss:Type="String">No info</ss:Data></ss:Cell>
				</ss:Row>').'
				<ss:Row>
					<ss:Cell ss:StyleID="col"></ss:Cell>
					<ss:Cell ss:StyleID="col"></ss:Cell>
				</ss:Row>';
			if ($ostats[$row['object_id']]) {
				foreach($ostats[$row['object_id']] as $st) {
					$ctnr .= $st;
				}
			}
			$ctnr .= '</ss:Table>
			</ss:Worksheet>';
		}
		
		$stores .= '<ss:Row>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">All stores</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="'.(floatval($total) < 0 ? 'isNew' : 'isFinished').'"><ss:Data ss:Type="String">$'.number_format($total, 2, '.', '').'</ss:Data></ss:Cell>
					</ss:Row>';
		
		$ctnr .= '<ss:Worksheet ss:Name="Total">
				<ss:Table>
					<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
					<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>'.$stores.'</ss:Table>
		</ss:Worksheet>'.$footer;
		echo $ctnr;
	break;
	
	case 'organizer':
		$week = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		$object = intval($_GET['object']);
		$group = intval($_GET['group']);
		$year = intval($_GET['year']);
		$month = intval($_GET['month']);
		$count = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$date_start = text_filter($_GET['date_start'], null, false);
		$date_end = text_filter($_GET['date_end'], null, false);
		xls_header('organizer_'.$year.'_'.$month);
		
		$objects = db_multi_query('SELECT id, name FROM `'.DB_PREFIX.'_objects` ORDER BY id', true);
		$ctnr = $header.'<ss:Worksheet ss:Name="Month">
					<ss:Table>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>';
		foreach($objects as $o) {
			$ctnr .= '<ss:Column ss:StyleID="col" ss:Width="150"></ss:Column>';
		}
		
		$ctnr .= '<ss:Row><ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Day</ss:Data></ss:Cell>';
		foreach($objects as $o) {
			$ctnr .= '<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">'.$o['name'].'</ss:Data></ss:Cell>';
		}
		$ctnr .= '</ss:Row>';
		
		$info = db_multi_query('SELECT o.*, u.name, u.lastname, u.image
								FROM `'.DB_PREFIX.'_organizer` o
								LEFT JOIN `'.DB_PREFIX.'_users` u
									ON u.id = o.staff
								LEFT JOIN `'.DB_PREFIX.'_objects` s
									ON s.id IN(REGEXP_REPLACE(o.time, \'(.*?){(.*?),"object":"(.*?)"}(.*?).?\', \'\\\3,\'))
								WHERE '.(
									($date_start AND $date_end) ? 'o.date_start <= \''.$date_end.'\' 
										AND IF(o.date_end, o.date_end >= \''.$date_start.'\', 1)' : 'o.date_start <= \''.$year.'-'.(
											$month < 10 ? '0'.$month : $month
										).'-'.$count.'\' 
										AND IF(o.date_end, o.date_end >= \''.$year.'-'.(
											$month < 10 ? '0'.$month : $month
										).'-1\', 1)'
									).(
										$group ? ' AND FIND_IN_SET('.$group.', u.group_ids)' : ''
								).(
									$object ? ' AND s.id = '.$object : ''
								).' ORDER BY s.id', true);
		$result = [];
		if ($info) {
			foreach($info as $s) {
				if ($s['time']) {
					foreach(json_decode($s['time'], true) as $i => $t) {
						if (!$result[$i]) $result[$i] == [];
						if (!$result[$i][$t['object']]) $result[$i][$t['object']] == [];
						$result[$i][$t['object']][] = [
							'user_id' => $s['staff'],
							'user_name' => $s['name'].' '.$s['lastname'],
							'start' => $t['start'],
							'end' => $t['end']
						];
					}
				}
			}
		}
		ksort($result);

		foreach($result as $d => $wDay) {
			$ctnr .= '<ss:Row>
					<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">'.$week[$d].'</ss:Data></ss:Cell>';
					
			foreach($objects as $o) {
				$ctnr .= '<ss:Cell ss:StyleID="wrapText"><ss:Data ss:Type="String">';
				if ($wDay[$o['id']]) {
					$c = 0;
					foreach($wDay[$o['id']] as $oUsr) {
						$ctnr .= ($c == 1 ? '&#10;' : '').$oUsr['user_name'].' ('.$oUsr['start'].' - '.$oUsr['end'].')';
						$c = 1;
					}
				}
				$ctnr .= '</ss:Data></ss:Cell>';
			}
			$ctnr .= '</ss:Row>';
		}
		
		$ctnr .= '</ss:Table>
		</ss:Worksheet>'.$footer;
		echo $ctnr;
	break;
	
	case 'salary':
		$query = text_filter($_GET['query'], null, false);
		$object = intval($_GET['object']);
		$staff = intval($_GET['staff']);
		$date_start = text_filter($_GET['date_start'], null, false);
		$date_finish = text_filter($_GET['date_end'], null, false);
		$page = intval($_GET['page']);
		$count = 100;
		xls_header('salary_'.date('m', time()).'_'.date('Y', time()));
		
		$o = db_multi_query('
			SELECT SUM(o.paid) as paid,
			o.staff_id,
			o.object_id
			FROM `'.DB_PREFIX.'_users_onsite_changelog` o
			LEFT JOIN `'.DB_PREFIX.'_users` u
				ON u.id = o.staff_id
			WHERE '.(
				($date_start AND $date_finish) ? ' o.date >= CAST(\''.$date_start.'\' AS DATE) AND o.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' o.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
			).'
			GROUP BY o.object_id, o.staff_id LIMIT '.($page*$count).', '.$count, true);
		
		$cntn = $header.'<ss:Worksheet ss:Name="WorksheetName">
					<ss:Table>
				<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="120"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
				<ss:Column ss:StyleID="col" ss:Width="100"></ss:Column>
			<ss:Row>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Staff</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Store</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Last salary</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Working time</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Salary</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">On site Salary</ss:Data></ss:Cell>
				<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Total</ss:Data></ss:Cell>
			</ss:Row>';
			db_multi_query('SELECT DISTINCT SQL_CALC_FOUND_ROWS 
				t.id,
				t.user_id,
				SUM(t.seconds) as seconds,
				u.name,
				u.lastname,
				u.image,
				u.pay,
				DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY) as last_salary,
				o.id as object_id,
				o.name as object,
				o.salary_tax as salary,
				o.points_equal as points_equal
			FROM `'.DB_PREFIX.'_users` u
			INNER JOIN `'.DB_PREFIX.'_timer` t
				ON t.user_id = u.id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = t.object_id
			WHERE t.event = \'stop\''.(
			$object ? ' AND o.id = \''.$object.'\'' : ' AND o.id > 0'
		).(
			$staff ? ' AND u.id = '.$staff.' ' : ''
		).(
			($date_start AND $date_finish) ? ' AND t.date >= CAST(\''.$date_start.'\' AS DATE) AND t.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ' AND t.date > DATE_SUB(\''.date('Y-m-d', time()).'\', INTERVAL 14 DAY)'
		).(
			$query ? ' AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
		).' GROUP BY t.object_id, t.user_id ORDER BY o.id LIMIT '.($page*$count).', '.$count, true, false, function($a) use(&$cntn, &$o){
			$user_id = $a['user_id'];
			$object_id = $a['object_id'];
			$o_user = array_values(array_filter($o, function($v) use(&$user_id, &$object_id) {
				if ($v['staff_id'] == $user_id AND $v['object_id'] == $object_id)
					return $v;
			}, ARRAY_FILTER_USE_BOTH));
				
			$total = $a['seconds'];
			$s = $total % 60;
			$m = ($total % 3600 - $s) / 60;
			$h = ($total - $s - $m * 60) / 3600;
			if ($a['seconds']) {
			$cntn .= '<ss:Row>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$a['name'].' '.$a['lastname'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$a['object'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$a['last_salary'].'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="def"><ss:Data ss:Type="String">'.$h.':'.$m.':'.$s.'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="isAll"><ss:Data ss:Type="String">$'.number_format($a['seconds']/3600*$a['pay'], 2, '.', '').($a['salary'] ? '/$'.number_format($a['seconds']/3600*$a['pay']*((100 + $a['salary'])/100), 2, '.', '') : '').'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="isAll"><ss:Data ss:Type="String">$'.number_format($o_user[0]['paid'], 2, '.', '').' / $'.number_format($o_user[0]['paid']*((100 + $a['salary'])/100), 2, '.', '').'</ss:Data></ss:Cell>
						<ss:Cell ss:StyleID="isFinished"><ss:Data ss:Type="String">$'.number_format(($a['seconds']/3600*$a['pay']*((100 + $a['salary'])/100) + $o_user[0]['paid']*((100 + $a['salary'])/100)), 2, '.', '').'</ss:Data></ss:Cell>
					</ss:Row>';
			}
		});
		$cntn .= '</ss:Table>
			</ss:Worksheet>'.$footer;
		echo $cntn;
	break;
	
	case 'camera':
		$query = text_filter($_GET['query'], 255, false);
		$event = text_filter($_GET['event'], 50, false);
		$date_start = text_filter($_GET['date_start'], 30, true);
		$date_finish = text_filter($_GET['date_finish'], 30, true);
		$page = intval($_GET['page']);
		$staff = intval($_GET['staff']);
		$object = intval($_GET['object']);
		$count = 100;
		$id = 0;
		$comments = '';
		
		$head = false;
		$cntn = $header.'<ss:Worksheet ss:Name="WorksheetName">
				<ss:Table>
			<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="200"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="150"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="150"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="150"></ss:Column>
			<ss:Column ss:StyleID="col" ss:Width="300"></ss:Column>
		<ss:Row>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">User</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Event</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Date</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Store</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Status</ss:Data></ss:Cell>
			<ss:Cell ss:StyleID="header"><ss:Data ss:Type="String">Comments</ss:Data></ss:Cell>
		</ss:Row>';
		xls_header('camera');
		db_multi_query('SELECT SQL_CALC_FOUND_ROWS 
						u.id as uID, 
						CONCAT(u.name, \' \', u.lastname) as User, 
						a.id,
						a.date as Date, 
						a.event as Event, 
						a.event_id as event_id, 
						a.camera,
						a.camera_event,
						a.end_time,
						SEC_TO_TIME(t.seconds) as Time,
						c.text,
						s.name,
						o.name as object
				FROM `'.DB_PREFIX.'_activity` a
				LEFT JOIN `'.DB_PREFIX.'_users` u
					ON a.user_id = u.id 
				LEFT JOIN `'.DB_PREFIX.'_objects` o
					ON a.object_id = o.id 
				LEFT JOIN `'.DB_PREFIX.'_timer` t
					ON t.user_id = a.user_id AND t.date >= CURDATE()
				LEFT JOIN `'.DB_PREFIX.'_camera_comments` c
					ON c.camera_id = a.id
				LEFT JOIN `'.DB_PREFIX.'_camera_status` s
					ON s.id = a.status_id
				WHERE (c.text IS NOT NULL OR a.camera = 1 OR a.status_id > 0) AND IF(a.camera, 1, u.id) '.(
				$event ? ' AND a.event = \''.$event.'\'' : ''
			).(
				$staff ? 'AND a.user_id = '.$staff.' ' : ''
			).(
				$object ? 'AND a.object_id = '.$object.' ' : ''
			).(
				$query ? 'AND MATCH(u.name, u.lastname) AGAINST (\'*'.$query.'*\' IN BOOLEAN MODE) ' : ''
			).(
				($date_start AND $date_finish) ? ' AND a.date >= CAST(\''.$date_start.'\' AS DATE) AND a.date <= CAST(\''.$date_finish.' 23:59:59\' AS DATETIME)' : ''
			).'
			ORDER BY a.object_id, a.id DESC LIMIT '.($page*$count).', '.$count, true, false, function($a) use(&$head, &$cntn, &$comments, &$id){
				$style = 'def';
				$href = '';
				switch($a['Event']){
					case 'stop working time':
						$event = 'stop working time ('.$a['Time'].')';
						$style = 'isNew';
					break;	
					
					case 'pause working time':
						$event = $lang['pauseTime'];
						$style = 'isFinished';
					break;	

					case 'start working time':
						$style = 'isFinished';
					break;						
					
					case 'add_purchase':
						$event = 'Add new purchase';
						$href = 'http://admin.yoursite.com/purchases/edit/'.$a['event_id'];
						$style = 'def';
					break;
					
					default:
						$event = $a['camera'] ? $a['camera_event'] : str_replace('_', ' ', $a['Event']);
						$style = 'def';
					break;
				}
				if ($id != $a['id']) {
					if ($id > 0) {
						$cntn .= $comments.'</ss:Data></ss:Cell>
						</ss:Row>';
					}
					$cntn .= '<ss:Row>
							<ss:Cell ss:StyleID="'.$style.'" ss:HRef="http://admin.yoursite.com/users/view/'.$a['uID'].'"><ss:Data ss:Type="String">'.$a['User'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="wrapTextNb"'.(
								$href ? ' ss:HRef="'.$href.'"' : ''
							).'><ss:Data ss:Type="String">'.$event.'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.($a['Date'].((strtotime($a['end_time']) - strtotime('TODAY')) ? ' - '.$a['end_time'] : '')).'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="'.$style.'"><ss:Data ss:Type="String">'.$a['object'].'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="'.($a['name'] ? 'isImportant' : $style).'"><ss:Data ss:Type="String">'.($a['name'] ?: '').'</ss:Data></ss:Cell>
							<ss:Cell ss:StyleID="wrapTextNb"><ss:Data ss:Type="String">';
					$comments = '';
					$id = $a['id'];
				}
				if ($comments)
					$comments .= '&#10;';
				$comments .= $a['text'];
			});
			$cntn .= $comments.'</ss:Data></ss:Cell>
						</ss:Row>
					</ss:Table>
		</ss:Worksheet>'.$footer;
		echo $cntn;
	break;
}
die;
 ?>