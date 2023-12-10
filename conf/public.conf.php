<?php

$this->config = [
	'app' => [
		'name' => 'Gardner-White Furniture',
		'controller' => [
			'uri' => '/index.php',
			'includePaths' => [],
			'logMask' => '',
			'logFile' => BASE_PATH . 'logs/public.log'
		],
		'session' => [
			'name' => 'GW_Public',
			'timeout' => '86400',
			'path' => '',
			'expire' => '525600',
			'secure' => false,
			'domain' => '.gardner-white.com'
		]
	]
];