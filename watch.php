<!DOCTYPE html>
<html>
	<head>
		<title>Watching <?=ucfirst($_GET['screen'])?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<link rel="stylesheet" type="text/css" href="/include/css/main.css" />
		
		<script src="/include/js/libraries/jquery.js"></script>
		<script src="http://64.208.137.136:17778/socket.io/socket.io.js"></script>
		<script src="/include/js/main.js"></script>
	</head>
	<body>
		<div id="frame">
			<div id="frame-url">
				<iframe id="frame-url-content"></iframe>
			</div>
			
			<div id="frame-shade"></div>
			
			<div id="frame-message">
				<div id="frame-message-content">HELLO WORLD</div>
			</div>
		</div>
	</body>
</html>
