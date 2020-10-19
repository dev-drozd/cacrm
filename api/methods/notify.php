<?php
send_push('59ec03bb0cf60', [
	'type' => 'new_msg',
	'msg' => 'test'
]);
die;
function sendPush2($id, $title, $message, $info){
	$data['data'] = [
		'message' => $message,
		'title' => $title,
		'info' => $info,
		'additionalData' => [
			'vibrate' => 1,
			'sound' => 1
		]
	];
	if ($staffs = db_multi_query('SELECT push_id FROM `'.DB_PREFIX.'_access_tokens` WHERE '.(
		$id ? 'user_id IN('.$id.')' : 'user_id != 0'
	).' AND push_id != \'\'', true)){
		echo 'OK';
		$data['registration_ids'] = [];
		foreach($staffs as $staff){
			$data['registration_ids'][] = $staff['push_id'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: key=AAAAu-uIM8A:APA91bFkq6iQQWZODpWrcS9HL1WxMVqNJtCVlAVCZP_iSYSs2yc4Q6YNS51tPpRXCvjV78XWCB2MHzeJ9FJf8q-z_7hTOBufFikVJ7a5_Bi643mmzFKwIi5f2t_q3ZkiDGmO5_-jZP0L',
			'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		curl_close($ch);
		echo $result;	
	} else
		die('err');
}
sendPush2(3, 'Test message', 'The test', 'GGG');
?>