from pkg.database.tables.zones import Zones as Zone_table

class Zone():

    def __init__(self, zid, x,y,z, up, down, left, right):
        self.zid = zid
        self.x = x
        self.y = y
        self.z = z
        self.up = up
        self.down = down
        self.left = left
        self.right = right

    @staticmethod
    def from_id(zid):
        zone = Zone_table.from_id(zid)
        return Zone(zone[0], zone[1], zone[2], zone[3], zone[4], zone[5], zone[6], zone[7])

    #called when a player enters the scene
    def onEnter(self, player):
        from pkg.Overseer import Overseer
        Overseer.send_data("entered zone", player.pid) 
