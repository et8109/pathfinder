'''
    Simple socket server using threads
'''
 
import socket
import sys
import hashlib
import struct
import base64
from _thread import *

GUID = b"258EAFA5-E914-47DA-95CA-C5AB0DC85B11"
handshake_shell = (
  '''HTTP/1.1 101 Switching Protocols
     Upgrade: websocket
     Connection: Upgrade
     WebSocket-Origin: http://localhost:10000
     Sec-WebSocket-Accept: %(acceptstring)s
     '''
    )

#sends handshake response to client to verify identity
def handshake(conn):
    print('Handshaking...')
    data = conn.recv(1024)
    #headers = parse_headers(data)
    for line in data.splitlines():
        if b'Sec-WebSocket-Key:' in line:
            key = line.split(b': ')[1]
            
            # Append the standard GUID and get digest
            combined = key + GUID
            response = ((base64.b64encode(hashlib.sha1(combined).digest())).decode('utf8')).strip()
                
            # Replace the placeholder in the handshake response
            shake = handshake_shell % { 'acceptstring' : response }

    conn.send(shake.encode())
    return True 

#Function for handling connections. This will be used to create threads
def clientthread(conn):
    #handshake(conn)
    #Sending message to connected client
    msg = ("welcome to the server.")
    conn.send(msg.encode())
    print("sent welcome message")

    #infinite loop so that function do not terminate and thread do not end.
    while True:

        #Receiving from client
        data = conn.recv(1024)
        reply = ('OK...').encode() + data
        if not data:
            break

        conn.sendall(reply)
    
    #came out of loop
    conn.sendall(("Goodbye").encode());
    print("closing");
    conn.close()

def startListening(): 
    HOST = ''   # Symbolic name meaning all available interfaces
    PORT = 10000 # Arbitrary non-privileged port
 
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    print('Socket created')
 
    #Bind socket to local host and port
    try:
        s.bind((HOST, PORT))
    except socket.error as msg:
        print ('Bind failed. Error Code: '+str(msg[0])+' Message: '+msg[1])
        sys.exit()
     
    print ('Socket bind complete')
 
    #Start listening on socket
    s.listen(10)
    print ('Socket now listening')
 
    #now keep talking with the client
    while 1:
        #wait to accept a connection - blocking call
        conn, addr = s.accept()
        print ('Connected with '+addr[0]+':'+str(addr[1]))
     
        #start new thread takes 1st argument as a function name to be run, second is the tuple of arguments to the function.
        start_new_thread(clientthread ,(conn,))

    print("done")
    s.close()
