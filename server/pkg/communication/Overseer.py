from pkg.database import database
from pkg.interfaces import Player

class Overseer():

    def __init__(self):
        self.sourceToId = {}
        pass
        #db connection?

    def dataRecieved(self, data, source):
        if source not in self.sourceToId:
            self.sourceToId[source] = data
            print("got data")
        #get player
        player = Player.Player(self.sourceToId[source])
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

    def sendData(self, data):
        outgoing[s].put(data)
