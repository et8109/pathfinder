import select 
import socket 
import sys
import queue

from .Overseer import Overseer

overseer = Overseer()
host = 'localhost' 
port = 10000 
backlog = 5 
size = 1024 
print("binding socket")
server = socket.socket(socket.AF_INET, socket.SOCK_STREAM) 
server.bind((host,port)) 
server.listen(backlog) 
inputs = [server,sys.stdin]
outputs = []
outgoing = {}
running = 1
print("server now listening")
while running: 
    readable,writeable,exceptional = select.select(inputs,[],[]) 

    for s in readable: 
        if s is server: 
            # handle the server socket 
            conn, address = server.accept() 
            inputs.append(conn) 
            outgoing[conn] = queue.Queue()

        elif s is sys.stdin: 
            # handle standard input 
            junk = sys.stdin.readline() 
            running = 0 

        else: 
            # handle all other sockets 
            data = s.recv(size) 
            if data:
                print("recieved: "+str(data))
                overseer.dataRecieved(data, s)
            if s not in outputs:
                outputs.append(s)#only if data is being sent
            else: 
                s.close() 
                inputs.remove(s) 
                if s in outputs:
                    outputs.remove(s)
                del outgoing[s]

    for s in writeable:
        try:
            next_msg = outgoing[s].get_nowait()
        except Queue.Empty:
            outputs.remove(s)
        else:
            s.send(next_msg)

    for s in exceptional:
        print("Handling exeption")
        inputs.remove(s)
        if s in outputs:
            outputs.remove(s)
        s.close()
        del outgoing[s]
server.close()
