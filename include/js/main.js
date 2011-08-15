/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function () {
	var socket = io.connect('64.208.137.136:17778');

	socket.on('connect', function () {
		socket.emit('register', window.location.pathname.split('/')[2]);
		
		socket.on('url', function (data) {
			_displayUrl(data);	
	
			console.log("url: %s", data);
		});
		
		socket.on('message', function (data) {
			_displayMessage(data);
			
			console.log("message: %s", data);
		});
	});
	
	socket.on('disconnect', function () {
		socket = io.connect('64.208.137.136:17778');
	});
});

var _displayUrl = function(args) {
	var _$frameUrlContent = $('#frame-url-content');
	_$frameUrlContent.attr('src', args[1]);
};

var _messageTimeoutID;
var _displayMessage = function(args) {
	var _duration = args[2] || 10000;
	var _shade = $('#frame-shade');
	var _frameMessage = $('#frame-message');
	var _frameMessageContent = $('#frame-message-content');
	
	if (_messageTimeoutID) {
		clearTimeout(_messageTimeoutID);
		_messageTimeoutID = null;
	}
	
	_shade.fadeIn('slow', function() {
		_frameMessageContent.text(args[1]);
		
		_frameMessage.fadeIn('slow', function() {
			
			_messageTimeoutID = setTimeout(function() {
				_frameMessage.fadeOut('slow', function() {
					_shade.fadeOut('slow', function() {
						_frameMessageContent.text('');
					});
				});
			}, _duration);
		});
	});
};