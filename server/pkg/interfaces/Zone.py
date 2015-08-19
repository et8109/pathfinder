import dexml
from dexml import fields
from pkg.database.database import Database

import pkg.interfaces.Path as Path
import pkg.interfaces.Enemy as Enemy
import pkg.interfaces.Npc as Npc

class Zone(dexml.Model):
    XMLdir = "pkg/database/zoneXML/"

    zid = fields.Integer()
    paths = fields.List(Path.Path)
    enemies = fields.List(Enemy.Enemy)
    npcs = fields.List(Npc.Npc)

    @staticmethod
    def fromID(zid):
        return Zone.parse(Database.getZoneXML(zid))

    def save(self):
        Database.saveZone(self.zid, self.render())

    #called when a player enters the scene
    def onEnter(self, player):
        for e in self.enemies:
            Overseer.send_data(
                e.audio, 
                player.pid)
        for n in self.npcs:
           Overseer.send_data(
                n.audio,
                player.pid)
 
