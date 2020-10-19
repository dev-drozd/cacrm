<?php //96.236.16.180:
$login = "admin@yoursite.com";
$password = "2S0d1F9d";
if($auth = imap_open("{96.236.16.180:143/novalidate-cert}INBOX", $login, $password)){
	echo 'Соединение успешно<br>';
	$count = imap_num_msg($auth);
	echo 'Сообщений '.$count.'<br>';
	
	$list = imap_list($auth, "{localhost:143}", "*");
		echo '<pre>';
	print_r($list);
	die;
	$threads = imap_thread($auth);

$arr = '';
$i = null;
echo '<pre>';
foreach ($threads as $key => $val) {
  $tree = explode('.', $key);
  /* if ($tree[1] == 'num') {
    $header = imap_headerinfo($auth, $val);
    echo "<ul>\n\t<li>" . $header->fromaddress . "\n";
  } elseif ($tree[1] == 'branch') {
    echo "\t</li>\n</ul>\n";
  } */
  
  if ($tree[1] == 'num' AND empty($i)) {
	  $i = $tree[0];
	  $header = imap_headerinfo($auth, $val);
	  $arr[] = [
		'from' => $header->fromaddress,
		'subject' => $header->subject,
		'name' => $header->from[0]->personal
	  ];
  } elseif ($key == $i.'.branch') {
	  $i = null;
  }
}

print_r($arr);

	imap_close($auth);
} else {
	echo 'ошибка соединения';
}
die;
?>