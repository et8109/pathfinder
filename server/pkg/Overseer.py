from pkg.communication.Socket import SocketServer
from pkg.interfaces.common import Player, Dirt
from pkg.interfaces.database import Database

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
        pid = Database.playerLogin(parsed["u"], parsed["p"])
        if pid != None:
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
        player = Player.fromID(Overseer.sourceToId[source])
        if data == b'up':
            player.swipe(Dirt.up)
        elif data == b'down':
            player.swipe(Dirt.down)
        elif data == b'left':
            player.swipe(Dirt.left)
        elif data == b'right':
            player.swipe(Dirt.right)
        else:
            pass
        player.save()#save to db
