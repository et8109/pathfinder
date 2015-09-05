from pkg.communication.Socket import SocketServer
from pkg.interfaces.common import Player, Dirt

import json

class Overseer():
    '''Processes input from clients, sends data back, and sends data to interfaces'''

    sourceToId = {}
    idToSource = {}
    server = None
    testing = False

    #@staticmethod
    def startServer(self):
        Overseer.server = SocketServer(self)
        Overseer.server.start()

    @staticmethod
    def add_conn_hash(source, pid):
        Overseer.sourceToId[source] = pid
        Overseer.idToSource[pid] = source

    @staticmethod
    def sendToPlayer(data, pid):
        print("sending: "+str(data))
        try:
            Overseer._sendData(data, Overseer.idToSource[pid])
        except KeyError:
            if not Overseer.testing:
                raise KeyError("Player id not found in id->source")

    @staticmethod
    def _sendData(data, source):
        if Overseer.server:
            data = json.dumps({"data": data})
            Overseer.server.send_data(data.encode(), source)

    @staticmethod
    def login(data, source):
        from pkg.database.database import PlayerNotFoundException
        parsed = json.loads(data.decode("utf-8"))
        try:
            pid = Player.login(parsed["u"], parsed["p"])
            Overseer.add_conn_hash(source, pid)
            Overseer._sendData("OK", source)
            Player.startPlayer(pid)
        except PlayerNotFoundException:
            Overseer._sendData("wrong login credentials", source)

    @staticmethod
    def dataRecieved(data, source):
        #if not logged in
        if source not in Overseer.sourceToId:
            Overseer.login(data, source)
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
