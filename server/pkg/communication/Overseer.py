from ./pkg/database/database import database

class Overseer():

    def __init__(self):
        #db connection?

    def dataRevieved(self, data):
        #get player
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


