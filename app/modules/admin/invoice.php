<?php

if ($_REQUEST['date']){
	$id = (int)$_REQUEST['id'];
	$sid = str_pad($id ?? 0, 8, '0', STR_PAD_LEFT);
	$total = (int)$_REQUEST['total'];
	$hours = number_format(($total/45), '1', '.', '');
	$total = number_format($total, '2', '.', '');
	$date = convert_date($_REQUEST['date'], true);
	$barcode = to_barcode('in '.str_pad($id, 11, '0', STR_PAD_LEFT));
?>
<html>
   <head>
      <meta charset="utf-8">
      <title>ACT OF WORKS DONE No. IN-<?=$sid?></title>
      <style>
         @font-face {
         font-display: auto;
         font-family: 'Source Sans Pro';
         font-style: normal;
         font-weight: 700;
         src: local('Source Sans Pro Bold'), local('SourceSansPro-Bold'), url(//fonts.gstatic.com/s/sourcesanspro/v11/6xKydSBYKcSV-LCoeQqfX1RYOo3ig4vwlxdu.woff2) format('woff2');
         unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
         }
         #print {
			position: relative;
			background: #fff;
			border: 1px solid #e7e7e7;
			padding: 15px;
			max-width: 800px;
			margin: 100px auto;
         }
		.shtamp {
			display: block;
			position: absolute;
			bottom: 15px;
			left: 53px;
			width: 164px;
		}
         button, .button {
         border: 0;
         height: 40px;
         line-height: 1.4;
         padding-left: 25px;
         padding-right: 25px;
         color: #fff;
         background-color: #4eb980;
         border-radius: 4px;
         cursor: pointer;
         margin-right: 15px;
         float: right;
         margin: 15px;
         }
         @media print {
         body * {
         visibility: hidden;
         }
         #print,
         #print * {
         visibility: visible;
         }
         #print {
         position: absolute;
         left: 0;
         top: 0;
         padding: 0;
         margin: 0;
         border: none;
         }
         }
         h1 {
         text-transform: uppercase;
         font-family: 'Source Sans Pro', sans-serif;
         }
      </style>
      <link type="text/css" rel="stylesheet" charset="UTF-8" href="https://translate.googleapis.com/translate_static/css/translateelement.css">
   </head>
   <body style="margin: 0;color: #6a818c;">
      <header style="position: fixed; top: 0px; width: 100%;background: #fff; border-bottom: 1px solid #e7e7e7; box-shadow: 0 1px 3px #eaedf4;z-index: 1;">
         <button onclick="window.print();">Print</button>
      </header>
      <main id="print">
         <header style="position: relative;padding: 3em;text-align: center;background: #fdfdfd;margin-bottom: 1em;">
            <img src="https://chart.googleapis.com/chart?cht=qr&amp;chl=<?=$id?>&amp;chs=50x50&amp;chld=L|0" style="position: absolute;right: 0;top: 0;">
            <img src="https://gdr.one/templates/ws/images/logo-white.svg" width="150">
            <h1>Global development room</h1>
            <hr>
         </header>
         <h2 align="center">ACT OF WORKS DONE No. IN-<?=$sid?></h2>
         <table border="1" bordercolor="gray" width="100%" cellspacing="0" style="border-collapse: collapse;" cellpadding="10">
            <thead>
               <tr>
                  <th colspan="1">Executor:</th>
                  <th colspan="5" align="right">Global Development Room, LLC</th>
               </tr>
               <tr>
                  <th colspan="1">Customer:</th>
                  <th colspan="1" align="right">Pavel Zaichenko (Your Company) <br>admin@yoursite.com <br>+1 518 330 2082</th>
                  <th colspan="1">Project:</th>
                  <th colspan="3" align="right">Your Company</th>
               </tr>
               <tr>
                  <th colspan="1">Date of:</th>
                  <th colspan="5" align="right"><?=$date?></th>
               </tr>
               <tr>
                  <th colspan="6" style="border-right-color: transparent;border-left-color: transparent;"></th>
               </tr>
            </thead>
            <thead>
               <tr>
                  <th>
                     No.
                  </th>
                  <th>
                     Service
                  </th>
                  <th>
                     Qty
                  </th>
                  <th>
                     Units
                  </th>
                  <th>
                     Amount
                  </th>
                  <th>
                     Total
                  </th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>1</td>
                  <td style="width: 100%;"><?=$_REQUEST['service']?></td>
                  <td><?=$hours?></td>
                  <td>hour</td>
                  <td>45</td>
                  <td style="text-align: center;"><?=$total?></td>
               </tr>
            </tbody>
            <thead>
               <tr>
                  <th colspan="4"></th>
                  <th>Total:</th>
                  <th>$<?=$total?></th>
               </tr>
               <tr>
                  <th colspan="4"></th>
                  <th>Paid:</th>
                  <th>$<?=$total?></th>
               </tr>
               <tr>
                  <th colspan="4"></th>
                  <th>Due:</th>
                  <th>$0</th>
               </tr>
            </thead>
         </table>
		 <p align="right">https://gdr.one</p>
		 <img src="https://gdr.one/templates/cp/img/shtamp.png" class="shtamp">
      </main>
   </body>
</html>

<?php
} else {
?>
<h1>Create GDR invoice</h1>
<form action="/invoice" method="get">
	<p>
		<label>Invoice id:</label>
		<input type="number" value="127" name="id">
	</p>
	<p>
		<label>Invoice date:</label>
		<input type="date" name="date">
	</p>
	<p>
		<label>Invoice amount:</label>
		<input type="number" value="-7500" name="total">
	</p>
	<p>
		<label>Service:</label>
		<textarea name="service">Web, and application development</textarea>
	</p>
	<button type="submit">Create invoice</button>
</form>
<?php
}
die;
?>