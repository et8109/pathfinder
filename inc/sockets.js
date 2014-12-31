var Server;
function send( text ) {
  Server.send( 'message', text );
}
log('Connecting...');
Server = new FancyWebSocket('ws://127.0.0.1:9300');
 
//Let the user know we're connected
  Server.bind('open', function() {
  log( "Connected." );
  send("hello server!");
  log("msg sent");
});
//OH NOES! Disconnection occurred.
Server.bind('close', function( data ) {
  log( "Disconnected. "+data );
});
//Log any messages sent from server
Server.bind('message', function( payload ) {
  log( payload );
});
Server.connect();