import select 
import socket 
import sys
import queue

size = 1024

class SocketServer():

    host = 'localhost' 

    def __init__(self, overseer):
        self.overseer = overseer
        self.port = 10000
        self.backlog = 5
        self.outputs = []
        self.outgoing = {}
        self.running = 1
        print("binding socket")
        self.server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.server.bind((self.host,self.port))
        self.server.listen(self.backlog)
        self.inputs = [self.server]

    def send_data(self, data, conn):
        print("-->> prepping send")
        self.outgoing[conn].put(data)
        if conn not in self.outputs:
            self.outputs.append(conn)

    def start(self):
        print("server now listening")
        while self.running: 
            readable,writeable,exceptional = select.select(self.inputs,self.outputs,self.inputs) 

            for s in readable: 
                if s is self.server: 
                    # handle the server socket 
                    conn, address = self.server.accept() 
                    self.inputs.append(conn) 
                    #print("-->> #inputs: "+str(count(self.inputs)))
                    self.outgoing[conn] = queue.Queue()
                    print("new conn: "+str(s))

                else: 
                    # handle all other sockets 
                    data = s.recv(size) 
                    if data:
                        print("recieved: "+str(data))
                        self.overseer.dataRecieved(data, s)
                    else: 
                        print("-->> removeing input")
                        s.close() 
                        self.inputs.remove(s) 
                        if s in self.outputs:
                            self.outputs.remove(s)
                        del self.outgoing[s]

            for s in writeable:
                try:
                    print("-->> writeable: "+str(s))
                    next_msg = self.outgoing[s].get_nowait()
                except queue.Empty:
                    self.outputs.remove(s)
                else:
                    print("-->> sending!!!!")
                    s.send(next_msg)

            for s in exceptional:
                print("Handling exeption")
                self.inputs.remove(s)
                if s in self.outputs:
                    self.outputs.remove(s)
                s.close()
                del self.outgoing[s]
            print("-->> done loop")
        self.server.close()
