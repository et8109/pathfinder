class Player(database.Player_table):

    def __init__(self, id):
        pass

    def up(self):
        zoneid = self.moveUp()
        Zone(zoneid).onEnter(self)
        
    def down(self):
        self.moveDown()
        
    def left(self):
        self.moveLeft()
        
    def right(self):
        self.moveRight()
