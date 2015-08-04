from pkg.database.database import *

class Player(Player_table):

    def __init__(self, id):
        pass

    @classmethod
    def login(klass, name, password):
        pid = klass.check_login(name, password)
        return pid

    def up(self):
        zoneid = self.moveUp()
        Zone(zoneid).onEnter(self)
        
    def down(self):
        self.moveDown()
        
    def left(self):
        self.moveLeft()
        
    def right(self):
        self.moveRight()
