from pkg.communication.Overseer import *
from pkg.database.database import Zone_table

class Zone():

    def __init__(self, zid):
        self.zid = zid

    @staticmethod
    def from_id(zid):
        #zone = Zone_table.from_id(zid)
        return Zone(zid)

    #called when a player enters the scene
    def onEnter(self, player):
        print("--> in zone")
        Overseer.Overseer.sendData("entered zone", player.pid) 
