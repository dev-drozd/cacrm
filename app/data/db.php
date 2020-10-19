<?php

define(DB_HOST, '127.0.0.1');
define(DB_USER, 'yoursite_site');
define(DB_NAME, $training_mode ? 'training_yoursite' : (
	$dev_mode ? 'dev_yoursite' : (
		$vermont_mode ? 'vermont.yoursite' : 'yoursite_site'
	)
));
define(DB_PASS, '4FCNZwxRUaBAzK89DfRzSdEcEsEkf5LhvNCt');
define(DB_PORT, '3310');
define(DB_CHARSET, 'utf8mb4');
define(DB_PREFIX, 'CA');
