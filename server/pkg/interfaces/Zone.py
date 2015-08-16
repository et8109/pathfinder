import dexml
from dexml import fields
from pkg.database.database import Database
from .Path import Path
from .Enemy import *

class Zone(dexml.Model):
    XMLdir = "pkg/database/zoneXML/"

    zid = fields.Integer()
    paths = fields.List(Path)
    enemies = fields.List(Enemy)

    @staticmethod
    def fromID(zid):
        return Zone.parse(Database.getZoneXML(zid))

    def save(self):
        Database.saveZone(self.zid, self.render())

    #called when a player enters the scene
    def onEnter(self, player):
        Overseer.send_data(
                "Chomp.mp3", 
                player.pid)
