var Server;
function send( text ) {
  Server.send( 'message', text );
}
Server = new FancyWebSocket('ws://127.0.0.1:9300');
 
//Let the user know we're connected
  Server.bind('open', function() {
  send("END_SERVER");
});
Server.connect();