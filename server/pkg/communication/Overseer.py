from pkg.database import database
from pkg.interfaces.Player import Player
from pkg.communication.Socket import SocketServer

import json

class Overseer():

    def __init__(self):
        self.sourceToId = {}
        self.server = SocketServer(self)
        self.server.start()
        #db connection?

    def dataRecieved(self, data, source):
        if source not in self.sourceToId:
            parsed = json.loads(data.decode("utf-8"))
            pid = Player.login(parsed["u"], parsed["p"])
            if pid:
                self.sourceToId[source] = pid
                print("pid "+str(pid)+" logged in")
            else:
                self.sendData("wrong login credentials", source)
                return;
        #get player
        player = Player(self.sourceToId[source])
        if data is b'up':
            player.up()
        elif data is b'down':
            player.down()
        elif data is b'left':
            player.left()
        elif data is b'right':
            player.right()
        else:
            pass
        database.commitDatabase()

    def sendData(self, data, dest):
        print("-->>  sending from overseer: "+data)
        self.server.send_data(data.encode(), dest)

