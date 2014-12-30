if (!"WebSocket" in window)
  {
    alert("websockets not supported");
  }

alert("creating socket");
var ws = new WebSocket("ws://localhost:10000/inc/updateServer.php");
    ws.onopen = function()
     {
        alert("created, sending msg");
        // Web Socket is connected, send data using send()
        ws.send("Message to send");
        alert("Message is sent...");
     };
    ws.onmessage = function (evt) 
     { 
        var received_msg = evt.data;
        alert("Message is received: "+received_msg);
     };
    ws.onerror = function(error){
        console.log('Error detected: ' + error);
     };
    ws.onclose = function()
     { 
        // websocket is closed.
        alert("Connection is closed..."); 
     };