<?php
require_once("../inc/constants.php");

// prevent the server from timing out
set_time_limit(60);

// include the web sockets server script (the server is started at the far bottom of this file)
require 'class.PHPWebSocket.php';
//include the cache, which the server requests for data
include '../inc/cache.php';

$tmpStr = "before changing.";

function tmpThreadFunc(){
	global $tmpStr;
	$tmpStr = "after thread";
}

class Updater extends Thread{
	private $clientID;
	public $result;
	private $joined;
	//private $server;
	function __construct($clientID){
		$this->clientID = $clientID;
		//$this->server = $server;
		//$this->result = "str from thread before run";
		$this->result = "result before";
		$this->joined = false;
		$this->done = false;
	}
	public function run(){
		/*$this->return = cacheUpdatePlayer($this->clientID, $this->message);
		$this->server->wsSend($this->clientID, $this->return);*/
		//$this->server->wsSend($this->clientID, "inside thread run function");
		//tmpThreadFunc();
		//wsSendMessage($this->clientID, "message from thread.");
		/*
		if( $this->result = "str from thread after run" ){
			return true;
		}
		return false;
		*/
		$this->result = "done running thread";
		$this->done = true;
		
		$this->synchronized(function($self){
			//$self->wait();
			//$self->result = "done w/e sync, after";
			$self->done = true;
		}, $this);
	}
	
	public function getResponse(){
		if(!$this->joined) {
			$this->joined = true;
			$this->join();
		}
		return $this->result;
	}
	
	public static function update($clientID){
		$thread = new Updater($clientID);
		$thread->start();
		/*if($thread->start()){
			return $thread->result;
		} else{
			return "could not update";
		}*/
		global $Server;
		wsSendMessage($clientID, "before sync");
		//sync 1: do action
		$thread->synchronized(function($self){
			$done = $self->done;
			$tmpStr = $self->result;
			wsSendMessage($self->clientID, "inside sync. done = $done");
			wsSendMessage($self->clientID, "inside sync. result = $tmpStr");
			if (!$self->done) {
				$self->notify();
			} else {
				//nothing
			}
		}, $thread);
		//sync 2: get result
		$thread->synchronized(function($self){
			$done = $self->done;
			$tmpStr = $self->result;
			wsSendMessage($self->clientID, "inside sync. done = $done");
			wsSendMessage($self->clientID, "inside sync. result = $tmpStr");
			if (!$self->done) {
				$self->notify();
			} else {
				//nothing
			}
		}, $thread);
		
	}
}


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
		//$threadMsg = Updater::update($clientID);
		//global $tmpStr;
		//wsSendMessage($clientID, $threadMsg);
		Updater::update($clientID);
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