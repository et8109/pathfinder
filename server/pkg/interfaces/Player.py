from pkg.database.tables.players import Players as Player_table
from pkg.interfaces.Zone import Zone

class Player():

    def __init__(self, pid, health, zone):
        self.pid = pid
        self.health = health
        self.zone = zone

    @staticmethod
    def from_id(pid):
        player = Player_table.from_id(pid)
        return Player(pid = player[0], 
            health = player[1], 
            zone = Zone.from_id(player[2]))

    @staticmethod
    def login(uname, password):
        return Player_table.login(uname, password)
        
    def save(self):
        Player_table.save(self.pid, self.health, self.zone.zid)

    def up(self):
        self.zone = Zone.from_id(self.zone.up)
        self.zone.onEnter(self)
        
    def down(self):
        self.zone = Zone.from_id(self.zone.down)
        self.zone.onEnter(self)
        
    def left(self):
        self.zone = Zone.from_id(self.zone.left)
        self.zone.onEnter(self)
        
    def right(self):
        self.zone = Zone.from_id(self.zone.right)
        self.zone.onEnter(self)
