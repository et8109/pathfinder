<?php
require_once("../inc/constants.php");

// prevent the server from timing out
set_time_limit(60);

// include the web sockets server script (the server is started at the far bottom of this file)
require 'class.PHPWebSocket.php';
//include the cache, which the server requests for data
include '../inc/cache.php';

// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {
	global $Server;
	set_time_limit(10);
	//$ip = long2ip( $Server->wsClients[$clientID][6] );

	// check if message length is 0
	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}
	if($message == "END_SERVER"){
		$Server->wsStopServer();
		die("server stopped");
	} else{
		wsSendMessage($clientID, "server recived: $message");
		cacheUpdatePlayer($clientID, $message);
	}
	//$Server->wsSend($clientID, "hello, this is the server.");
}

// when a client connects
function wsOnOpen($clientID)
{
	global $Server;
	//$ip = long2ip( $Server->wsClients[$clientID][6] );
	//$Server->wsSend($id, "Visitor $clientID ($ip) has joined the room.");
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	//$ip = long2ip( $Server->wsClients[$clientID][6] );
	//$Server->wsSend($id, "Visitor $clientID ($ip) has left the room.");
}

function wsSendMessage($clientID, $msg){
	global $Server;
	$Server->wsSend($clientID, $msg);
}

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
$Server->wsStartServer(constants::ipAddr, constants::portNum);

?>