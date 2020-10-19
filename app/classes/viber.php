<?php
/**
 * @appointment Viber class
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2019
 * @link        https://yoursite.com
 * This code is copyrighted
*/

class Viber {
	
    private $url_api = "https://chatapi.viber.com/pa/";

    private $token = "";

    public function message_post
    (
        $from,
        array $sender,
        $text
    )
    {
        $data['from']   = $from;
        $data['sender'] = $sender;
        $data['type']   = 'text';
        $data['text']   = $text;
        return $this->call_api('post', $data);
    }

    private function call_api($method, $data)
    {
        $url = $this->url_api.$method;

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\nX-Viber-Auth-Token: ".$this->token."\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        return json_decode($response);
    }
}
$Viber = new Viber();
$Viber->message_post(
    '01234567890A=',
    [
        'name' => 'Test Sender', /
        'avatar' => 'http://avatar.example.com'
    ],
    'Test'
);

?>