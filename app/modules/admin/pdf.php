<?php

defined('ENGINE') or ('hacking attempt!');

switch($route[1]) {
	case 'test':
		require('app/pdf_html.php');

		$pdf=new createPDF('
			<table>
			<tr>
			<td>Name</td>
			<td>Test</td>
			</tr>
			<tr>
			<td>Name</td>
			<td>Test</td>
			</tr>
			</table>
		', 'title', 'title', 'title',  time());
		$pdf->run();
	break;
	
	case 'ubarcode':
		require('app/pdf.php');
		$id = $route[2];
		$row = db_multi_query('
			SELECT 
				id, name, lastname, phone, email, zipcode
			FROM `'.DB_PREFIX.'_users` 
			WHERE id = '.$id);
		
		$pdf = new Pdf('L', 'mm', array(27,90));
		$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
		$pdf->Text(2,5,"Your Company");
		$pdf->Image('data:image/png;base64,'.
						to_barcode('us '.str_pad(
							$id, 6, '0', STR_PAD_LEFT
							)
						),1,7,45,0,'PNG');
		$pdf->SetFont('Arial','B',8);
		$pdf->Text(49,5,$row['name'].' '.$row['lastname']);
		$pdf->SetFont('Arial','',8);
		$pdf->Text(49,13,$row['phone']);
		$pdf->Text(49,17,$row['email']);
		$pdf->Text(49,21,$row['zipcode']);
		$pdf->Output(); 
	break; 
	
	case 'dbarcode':
		require('app/pdf.php');
		$id = $route[2];
		$barcode = db_multi_query('SELECT barcode FROM `'.DB_PREFIX.'_inventory` WHERE id = '.$id);
		$len = (9 - strlen($barcode['barcode'])) >= 0 ? (9 - strlen($barcode['barcode'])) : 0;
		$zero = '';
		for($i = 0; $i < $len; $i++) {
			$zero .= '0';
		}
		$width = 5.5 * ($barcode['barcode'] ? (strlen($barcode['barcode']) < 9 ? 9 : strlen($barcode['barcode'])) : 9);
		if ($width > 86) 
			$width = 86;

		$pdf = new Pdf('L', 'mm', array(27,90));
		$pdf->AddPage();
		$pdf->Image('data:image/png;base64,'.
						to_barcode($barcode['barcode'] ? $zero.$barcode['barcode'] : 'de '.str_pad(
							$id, 7, '0', STR_PAD_LEFT
							)
						),((90 - $width) / 2),2,$width,0,'PNG');
		$pdf->Output(); 
	break; 
	
	case 'barcode':
		require('app/pdf.php');
		$issue_id = $route[2];
		
		$row = db_multi_query('
			SELECT 
				iss.description,
				iss.inventory_ids,
				iss.service_ids,
				iss.date, 
				iss.staff_id, 
				iss.quote, 
				i.price, IF(
					i.name = \'\', i.model, i.name
				) as inventory_name,
				i.id as device_id,
				i.customer_id,
				i.barcode,
				i.charger as charger,
				i.serial as serial,
				t.options as opts,
				u.name as customer_name,
				u.lastname as customer_lastname,
				u.phone as customer_phone,
				u.email as customer_email,
				u.address as customer_address,
				u.image as customer_image,
				st.name as staff_name,
				st.lastname as staff_lastname,
				o.id as object_id,
				o.name as object_name,
				o.phone as object_phone,
				o.tax as object_tax,
				o.address as object_address,
				s.name as status_name,
				l.name as location_name,
				c.name as category_name,
				m.name as model_name,
				t.name as type_name,
				inv.id as invoice,
				os.name as os_name,
				ct.name as country_name,
				cty.city as city_name,
				u.zipcode as zipcode
			FROM `'.DB_PREFIX.'_issues` iss 
			INNER JOIN `'.DB_PREFIX.'_inventory` 
				i ON iss.inventory_id = i.id 
			LEFT JOIN `'.DB_PREFIX.'_inventory_types`
				t ON i.type_id = t.id
			LEFT JOIN `'.DB_PREFIX.'_users`
				u ON u.id = i.customer_id
			LEFT JOIN `'.DB_PREFIX.'_users`
				st ON st.id = iss.staff_id
			LEFT JOIN `'.DB_PREFIX.'_objects` o
				ON o.id = i.object_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_status` s
				ON s.id = i.status_id
			LEFT JOIN `'.DB_PREFIX.'_objects_locations` l
				ON l.id = i.location_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_categories` c
				ON c.id = i.category_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_os` os
				ON os.id = i.os_id
			LEFT JOIN `'.DB_PREFIX.'_inventory_models` m
				ON m.id = i.model_id
			LEFT JOIN `'.DB_PREFIX.'_invoices` inv
				ON i.id = inv.issue_id
			LEFT JOIN `'.DB_PREFIX.'_countries` ct
				ON ct.code = u.country
			LEFT JOIN `'.DB_PREFIX.'_cities` cty
				ON cty.zip_code = u.zipcode
			WHERE iss.id = '.$issue_id
		);

		$pdf = new Pdf('L', 'mm', array(27,90));
		$pdf->AddPage();
		$pdf->SetFont('Arial','',8);
		$pdf->Text(2,5,"Your Company");
		$pdf->Text(2,8,"Free Estimates/Diagnosis");
		$pdf->Text(2,11,$row['object_phone']);
		$pdf->Image('data:image/png;base64,'.
						to_barcode('is '.str_pad(
							$issue_id, 6, '0', STR_PAD_LEFT
							)
						),1,8,45,0,'PNG');
		$pdf->SetFont('Arial','',8);
		$pdf->Text(49,5,'Intake: '.$row['date']);
		$pdf->Text(49,9,'Customer Name: ');
		$pdf->Text(49,13,$row['customer_name'].' '.$row['customer_lastname']);
		$pdf->Text(49,17,$row['customer_email']);
		$pdf->Text(49,21,'Cell: '.$row['customer_phone']);
		$pdf->Text(49,25,'Has Charger: '.($row['charger'] ? 'yes' : '-'));
		$pdf->Output(); 
	break;
	
	case 'pdflib':
		try {
			$p = new PDFlib();

			/*  open new PDF file; insert a file name to create the PDF on disk */
			if ($p->begin_document("", "") == 0) {
				die("Error: " . $p->get_errmsg());
			}

			$p->set_info("Creator", "hello.php");
			$p->set_info("Author", "Rainer Schaaf");
			$p->set_info("Title", "Hello world (PHP)!");

			$p->begin_page_ext(595, 842, "");

			$font = $p->load_font("Helvetica-Bold", "winansi", "");

			$p->setfont($font, 24.0);
			$p->set_text_pos(50, 700);
			$p->show("Hello world!");
			$p->continue_text("(says PHP)");
			$p->end_page_ext("");

			$p->end_document("");

			$buf = $p->get_buffer();
			$len = strlen($buf);

			header("Content-type: application/pdf");
			header("Content-Length: $len");
			header("Content-Disposition: inline; filename=hello.pdf");
			print $buf;
		}
		catch (PDFlibException $e) {
			die("PDFlib exception occurred in hello sample:\n" .
			"[" . $e->get_errnum() . "] " . $e->get_apiname() . ": " .
			$e->get_errmsg() . "\n");
			
		}
		catch (Exception $e) {
			die($e);
		}
		$p = 0;
	break;
}
?>