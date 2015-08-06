from pkg.communication.Overseer import *
from pkg.database.tables import zones as Zone_table

class Zone():

    def __init__(self, zid, up, down, left, right):
        self.zid = zid
        self.up = up
        self.down = down
        self.left = left
        self.right = right

    @staticmethod
    def from_id(zid):
        zone = Zone_table.from_id(zid)
        return Zone(zid, zone.up, zone.down, zone.left, zone.right)

    #called when a player enters the scene
    def onEnter(self, player):
        Overseer.Overseer.sendData("entered zone", player.pid) 
