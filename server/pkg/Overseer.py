from pkg.communication.Socket import SocketServer
from pkg.interfaces.common import Player

import json

class Overseer():

    instance = None
    sourceToId = {}
    idToSource = {}

    def __init__(self):
        Overseer.instance = self
        self.server = SocketServer(self)
        self.server.start()
        #db connection?

    @staticmethod
    def add_conn_hash(source, pid):
        print("adding id: "+str(pid)+" to "+str(source))
        Overseer.sourceToId[source] = pid
        Overseer.idToSource[pid] = source

    @staticmethod
    def send_data(data, pid):
        Overseer.instance.sendData(data, Overseer.idToSource[pid])

    def sendData(self, data, dest):
        self.server.send_data(data.encode(), dest)

    def login(self, data, source):
        parsed = json.loads(data.decode("utf-8"))
        pid = Player.login(parsed["u"], parsed["p"])
        if pid:
            Overseer.add_conn_hash(source, pid)
            self.sendData("OK", source)
        else:
            self.sendData("wrong login credentials", source)

    def dataRecieved(self, data, source):
        #if not logged in
        if source not in Overseer.sourceToId:
            self.login(data, source)
            return
        #get player
        player = Player.from_id(Overseer.sourceToId[source])
        if data == b'up':
            player.up()
        elif data == b'down':
            player.down()
        elif data == b'left':
            player.left()
        elif data == b'right':
            player.right()
        else:
            pass
        player.save()#save to db
