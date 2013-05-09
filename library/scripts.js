<?php
header('Content-type: text/javascript');

$ques = array(
	'module/jquery-1.8.0.js',
	'module/require/require.min.js',
	'module/Device.min.js',
	'module/jquery/tapevent.min.js',
	'module/jquery/easing.js',
	// 'module/html5shiv.js',
	// 'module/fix/modernizr.js',
);

foreach ($ques as $q) {
	readfile($q);
	echo "\n\n";
}