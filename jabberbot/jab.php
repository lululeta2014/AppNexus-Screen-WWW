<?php

//Connect to XMPP
include 'XMPPHP/XMPP.php';
$conn = new XMPPHP_XMPP('01.xmpp.nym1.appnexus.net', 5222, 'screen', 'PASSWORD', 'xmpphp', 'appnexus.com', $printlog=true,$loglevel=XMPPHP_Log::LEVEL_INFO);
$conn->autoSubscribe();

$handle = fsockopen('64.208.137.136', 17779);

function sendInstructions($conn, $pl) {
	$user = substr($pl['from'],0,strpos($pl['from'],"@appnexus.com"));
	
	$response = "Get that junk out of here. Here are valid methods:\n
		1. To stream a link to a screen
				@adserver-tv url REPLACE_WITH_URL
		2. To send a message to a screen
				@services-tv message REPLACE_WITH_MESSAGE
				@services-tv message -t 10000 REPLACE_WITH_MESSAGE
					note: t flag represents duration in milliseconds

		Remember: your screen can be found at 64.208.137.136:17777/watch/$user";
	
	$conn->message($pl['from'], $response, $pl['type']);
}

try {
    $conn->connect();
    while(!$conn->isDisconnected()) {
    	$payloads = $conn->processUntil(array('message', 'end_stream', 'session_start'));
    	foreach($payloads as $event) {
    		switch($event[0]) {
    			case 'session_start':
    			    print "Session Start\n";
    				$conn->presence();
					
					break;
				case 'message': 
					if (!isset($event[1])) {
						break;
					}
					
					$pl = $event[1];
					$message = $pl['body'];
					preg_match("/@([A-Za-z0-9]+) ([A-Za-z]+)( -t ([0-9]+))?(.+)/", $message, $matches);
					
					if (!isset($matches[1])) {
						sendInstructions($conn, $pl);
						break;
					}
					
					$target = $matches[1];
					$action = $matches[2];
					$duration = $matches[4];
					$data = $matches[5];
					
					if ($action === 'message') {
						$action = 'sendMessage';
					} else if($action === 'url') {
						if (strpos($data, 'youtube.com')) {
							$action = 'sendYoutube';
						} else {
							$action = 'sendUrl';
						}
						$data = strip_tags($data);
					} else {
						sendInstructions($conn, $pl);
						break;
					}
					
					$params = array(
						'name' => $action,
						'args' => array(
							$target,
							$data,
							$duration,
						)
					);
					
					$user = substr($pl['from'],0,strpos($pl['from'],"@appnexus.com"));
					
					if (fwrite($handle, json_encode($params))) {
						$conn->message($pl['from'], 'Sent your request!', $pl['type']);
						$conn->message("$target@appnexus.com", "Hey, $user just updated your screen. Go here: http://64.208.137.136:17777/watch/$target", "chat");
					} else {
						$conn->message($pl['from'], 'Something went wrong... :-(', $pl['type']);
					}

					break;
    		}
    	}
    }
} catch(XMPPHP_Exception $e) {
    die($e->getMessage());
}
?>