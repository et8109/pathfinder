from pkg.database.database import *
from pkg.interfaces import *

class Overseer():
    sourceToId = {}

    def __init__(self):
        pass
        #db connection?

    def dataRevieved(self, data, source):
        if sourceToId[source] is None:
            sourceToId[source] = data
            print("got data")
        #get player
        player = Player(sourceToId[source])
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
        commitDatabase()

    def sendData(self, data):
        outgoing[s].put(data)
