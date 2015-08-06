from pkg.database.tables import Players as Player_table
from pkg.interfaces.Zone import Zone

class Player():

    def __init__(self, pid, health, zone):
        self.pid = pid
        self.health = health
        self.zone = zone

    @staticmethod
    def from_id(pid):
        player = Player_table.from_id(pid)
        return Player(pid, 
            health = player.health, 
            zone = Zone.from_id(player.zoneid))

    @staticmethod
    def login(uname, password):
        return Player_table.check_login(uname, password)

    def up(self):
        print("-->> in player up")
        zoneid = Player_table.moveUp(self.pid)
        self.zone = Zone.from_id(zoneid)
        self.zone.onEnter(self)
        
    def down(self):
        zoneid = Player_table.moveDown(self.pid)
        self.zone = Zone.from_id(zoneid)
        self.zone.onEnter(self)
        
    def left(self):
        zoneid = Player_table.moveLeft(self.pid)
        self.zone = Zone.from_id(zoneid)
        self.zone.onEnter(self)
        
    def right(self):
        zoneid = Player_table.moveRight(self.pid)
        self.zone = Zone.from_id(zoneid)
        self.zone.onEnter(self)
