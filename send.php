<?php
$handle = fsockopen('64.208.137.136', 17779);

if (!$handle) {
	die('Failed to connect to node server.');
}

$params = array(
	'name' => 'sendUrl',
	'args' => array(
		'test2',
		'http://reddit.com'
	)
);

if (fwrite($handle, json_encode($params))) {
	echo 'Sent!';
} else {
	echo 'Failure!';
}
fclose($handle);
?>
