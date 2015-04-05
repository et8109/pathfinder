window.onerror = function(msg, url, line) {
    log("Error: "+msg+" url: "+url+" line: "+line);
};

//page setup
var loading = true;

//p2p chat
var peer;
var localStream;//audio from local device
var connections=[];

window.URL = window.URL || window.webkitURL;
navigator.getMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
var audioContext = window.AudioContext || window.webkitAudioContext;

//get audio stream from device
navigator.getMedia({
      audio: true,
      video: false
    },
    function(stream){
        localStream=stream;
        var audioTracks = localStream.getAudioTracks();
        if (audioTracks.length > 0)
            log('Using Audio device: ' + audioTracks[0].label);
    },
    function(e){
      log('getUserMedia() error: ' + e.name);
    });

/**
 *The audiocontext for the entire page.
 */
var context = new webkitAudioContext();
/**
 *The audio source with the sound for walking.
 */
var spriteObject=new node();
var question = false;
var answer = null;

var requestArray=[];//used to request audio
var decodingAudio = 0;
var npcs=[];
var enemies = [];

/**
 *The audiocontext for the entire page.
 */
var context = new webkitAudioContext();
/**
 *The audio source with the sound for walking.
 */
var spriteObject=new node();
var question = false;
var answer = null;

var requestArray=[];//used to request audio
var npcs=[];
var enemies = [];
var ambients =[];
var players=[];

var updater;
var ticker;

/**
 *repeated in db
 */
var types = {
    ambient_noise:0,
    enemy: 1,
    walk_audio: 2,
    person: 3
}

//load defualt playe rinfo


/**
 *  onload:
 *      load player and sprite defaults
 *      load current zone
 *  onmove:
 *      load new zone
 *  onupdate:
 *      read response to play/pause
 */

window.onload = function(){
    sendRequest("setup.php",
                "setup=true",
                function(response){
                    log("starting loading");
                    //load sprite and player audio
                    //spriteObject.requestBuffer(response.spriteaudioURL); TODO load sprite
                    players[response.playerID] = new node(false, response.playeraudioURL);
                    players[response.playerID].requestBuffer();
                    //loadRequestArray(requestArray);
                    //load current scene
                    moveZone('init');
                    //create peer
                    createPeer(response.peerID);
                    //start updater
                    log("client version 2");
                    log("server version "+response.version);
                    loading = false;
                }
               );
}

window.onkeypress = function(event){
    //a, left
    if(event.keyCode == 97 || event.keyCode == 65){
        moveZone('W');
        log("left");
        return;
    }
    //d, right
    if(event.keyCode == 68 || event.keyCode == 100){
        moveZone('E');
        log("right");
        return;
    }
    //w, up
    if(event.keyCode == 87 || event.keyCode == 119){
        moveZone('N');
        log("up");
        return;
    }
    //s, down
    if(event.keyCode == 83 || event.keyCode == 115){
        moveZone('S');
        log("down");
        return;
    }
 
}

function node(loop, audioURLs){
    this.loop = loop;
    this.buffers=[];//bufferend audio
    this.audioURLs = audioURLs;//string array of urls

    /**
     *adds a request to requestArray to get buffers for audio urls
     *takes comma separated urls
     */
    this.requestBuffer=function(){
        //TODO look into this
        var l = this.audioURLs.length-1;//to flip it around
        for(u in this.audioURLs){
           requestArray.push([this,this.audioURLs[l-u]]);
        }
    }
    
    this.play = function(audioNum){
        log("starting: "+this.audioURLs[audioNum]);
        this.audioSource && this.audioSource.stop();
        log(this.buffers[audioNum]);
        this.audioSource = createAudioSource(this.buffers[audioNum],false/*no panner*/);
        //this.audioSource = createAudioSource(this.buffers[audioNum],true/*panner*/,this.posx,this.posy,this.posz);
        if (this.loop){
            this.audioSource.loop = true;//for walking
        }
        this.audioSource.start();
        return true;
    }
    
    this.stop = function(){
        if(this.audioSource){
            this.audioSource.stop();
        }
        return true;
    }
}

/**
 *sends the array created by node's request buffer to the server
 *[id,url]
 */
function loadRequestArray(requestArray, play_data){
    decodingAudio = requestArray.length;
    if(requestArray.length == 0){
        //nothing to load
        update(play_data);
        return;
    }
    _sendAudioReq(requestArray, play_data);
}

function _sendAudioReq(requestArray, play_data){
    if (!requestArray.length >0) {
        return;
    }
    var info = requestArray.pop();
    request = new XMLHttpRequest();
    request.open("GET","../../server/audio/"+info[1],true/*asynchronous*/);
    request.responseType = "arraybuffer";
    request.onload = function(){
        if (request.response == null) {
            log("error loading");
        }
        //set object's buffer: http request -> buffer
        context.decodeAudioData(request.response,function(decoded){ //callback function
            info[0].buffers.push(decoded);
            decodingAudio--;
            if(requestArray.length == 0 && decodingAudio == 0){
                update(play_data);
            }
            });
        _sendAudioReq(requestArray, play_data);
    }
    request.send()
}

/**
 * Moves the player n, s, e or w.
 * use init when loading curent zone
 */
function moveZone(dir){
    sendRequest("changeZones.php",
                "dir="+dir,
                function(response){
                    var prep_data = response.prep;
                    var play_data = response.play;
                    //end last zone's ambients
                    for(a of prep_data.endAmb){
                        ambients[a.id].stop();
                    }
                    //load audio urls into nodes
                    for(amb of prep_data.amb){
                        ambients[amb.id] = new node(true, amb.audio);
                        ambients[amb.id].requestBuffer();
                    }
                    for(npc of prep_data.npcs){
                        npcs[npc.id] = new node(false, npc.audioURLs);
                        npcs[npc.id].requestBuffer();
                    }
                    for(enemy of prep_data.enemies){
                        enemies[enemy.id] = new node(false, enemy.audioURLs);
                        enemies[enemy.id].requestBuffer();
                    }
                    //TODO call peeps
                    loadRequestArray(requestArray, play_data);
                });
}


/*
                log("player found: "+data.peerid);
                if (connections[data.peerid] == null){
                    log("conn not usable. calling.");
                    connections[data.peerid] = true;*/
                    /*var conn = peer.connect(data.peerid);
                    conn.on('error', function(err){
                        log("connection error: ");
                        log(err);
                    });
                    conn.on('open', function(){
                        conn.send('hi!');
                        log("msg sent");
                    });*/
                    //new audio conn
                   /* var call = peer.call(data.peerid, localStream);
                    call.on('error', function(err){
                        log("call error: ");
                        log(err);
                    });
                    call.on('stream',function(stream){
                        log("-recieving stream: "+stream);
                        //var audioSource =
                        //connections[call.peer] =
                        createAudioSourceStream(stream,2,2,0);
                        //document.getElementById("otherAudio").setAttribute('src', URL.createObjectURL(stream));
                    });
                    //document.getElementById("playerAudio").prop('src',URL.createObjectURL(stream));
                    //var source = context.createMediaStreamSource(stream);
                }
            }
        }
        //loadRequestArray(requestArray);
    } 
}*/


/**
 *set up convolver
 */
/*
 * Again, the context handles the difficult bits
var convolver = context.createConvolver();

// Wiring
soundSource.connect(convolver);
convolver.connect(context.destination);

// load the impulse response asynchronously
var request = new XMLHttpRequest();
request.open("GET", "impulse-response.mp3", true);
request.responseType = "arraybuffer";

request.onload = function () {
  convolver.buffer = context.createBuffer(request.response, false);
  playSound();
}
request.send();*/

/**
 *starts recording, opens the button to stop, calls the param function afterwards
 */
function record(callback){
    //TODO change to use localStream!!!!!!!!!!!!!!!!!!!!!!
    var mediaStreamSource;
    navigator.getMedia(
        {audio: true},
        function(localMediaStream){
            mediaStreamSource = context.createMediaStreamSource(localMediaStream);
           // mediaStreamSource.connect(context.destination);
        },
        function(err){
            log(err);
            return;
        }
    );
    try{
        var recorder = new MediaRecorder(mediaStreamSource);
        recorder.record(/*length in ms: */2000);
        recorder.ondataavailable = function(blob){
            callback(blob);
            recorder.stop();
        }
    } catch(err){
        log(err);
        return;
    }
}

function recordedAttack(blob){
    log("recording not yet implemented");
}

/**
 *started when login request is recived
 *sends current position to db
 *reacts to recieved data
 */
function update(play_data){
    for(a of play_data.ambients){
        ambients[a.id].play(0);
    }
    for(n of play_data.npcs){
        npcs[n.id].play(n.audioType);
    }
    for(e of play_data.enemies){
        enemies[e.id].play(e.audioType);
    }
    for(p of play_data.player){
        players[p.id].play(p.audioType);
    }
}

/**
 *returns an audioSourceNode with the audioBuffer
 */
function createAudioSource(audioBuffer,hasPanner,posx,posy,posz){
    var audioSource = context.createBufferSource();
    audioSource.buffer = audioBuffer;
    if (hasPanner) {
        var panner = context.createPanner();
        panner.setPosition(posx,posy,posz);
        audioSource.connect(panner);
        panner.connect(context.destination);
    } else{
        audioSource.connect(context.destination);
    }
    return audioSource;
}

function createAudioSourceStream(audioStream,posx,posy,posz){
    var audioSource = context.createMediaStreamSource(audioStream);
    //var panner = context.createPanner();
    //panner.setPosition(posx,posy,posz);
    //audioSource.connect(panner);
    //panner.connect(context.destination);
    if(!audioSource.connect(context.destination)){
        log("could not connect remote stream to context destination");
        document.getElementById("otherAudio").setAttribute('src', URL.createObjectURL(audioStream));
    }
    log("audio source created");
}

/**
 *initialized the peer of the player
 */
function createPeer(peerID){
    peer = new Peer(peerID,{key: 'kf8l60l4w3f03sor'});
    peer.on('error', function(err){
        log("peer error: ");
        log(err);
    });
    /*peer.on('connection', function(conn) {
        conn.on('data', function(data){
          // Will print 'hi!'
          log(data);
        });
      });*/
    peer.on('call',function(call){
        log("called!");
        /*if (window.existingCall) {
            window.existingCall.close();
        }*/
        call.on('error', function(err){
        log(err.message);
        });
        call.answer(localStream);
        log("-answered");
        call.on('stream',function(stream){
            log("-recieving stream: "+stream);
            //var audioSource =
            //connections[call.peer] =
            createAudioSourceStream(stream,2,2,0);
            //document.getElementById("otherAudio").setAttribute('src', URL.createObjectURL(stream));
        });
    });
}

function stop(){
    clearInterval(updater);
    clearInterval(ticker);
    for (conn in connections){
        conn.close();
    }
}

function log(msg){
    document.getElementById("log").innerHTML+="</br>"+msg;
}

/**
 *sends a request to the given url
 *if recieved json has .error, logs it
 */
function sendRequest(url,params,returnFunction){
var request = new XMLHttpRequest();
request.open("POST","../../server/communication/"+url);
request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
request.setRequestHeader("Content-length", params.length);
request.setRequestHeader("Connection", "close");
    request.onreadystatechange = function(){
        if (this.readyState==4 && this.status==200){
            log("response: "+this.responseText);
            if (!this.responseText) {
                return;
            }
            var json = JSON.parse(this.responseText);
            if (json.error) {
                log(json.error);
            } else{
                returnFunction(json);
            }
        }
    }
request.send(params);
}
