<?php

include APP_DIR.'/classes/smtp.php';
Mail::$type = 'php';
Mail::send('5183302082@messaging.sprintpcs.com', 'Test sms', '<b>Test sms from KA 2</b>');
die;