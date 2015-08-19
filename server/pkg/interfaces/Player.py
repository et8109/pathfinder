import dexml
from dexml import fields
from pkg.interfaces.Zone import Zone
from pkg.database.database import Database

class Player(dexml.Model):

    pid = fields.Integer()
    health = fields.Integer()
    zid = fields.Integer()
    uname = fields.String()
    password = fields.String()

    @staticmethod
    def from_id(pid):
        return Player.parse(Database.getPlayerXML(pid))

    def save(self):
        Database.savePlayer(self.pid, self.render())

    @staticmethod
    def login(uname, password):
        return Database.login(uname, password)
        
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

    def damage(self, amount):
        self.health -= amount
