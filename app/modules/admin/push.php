<?php
//$store_id = 2;
send_push(md5($user['store_id'].$config['tablet_user'].$config['tablet_password']), [
	'type' => 'acception'
]);
echo 'OK';
die;
?>