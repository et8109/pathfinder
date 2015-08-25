from enum import Enum
import abc
import dexml
from dexml import fields

from pkg.database.database import Database
import pkg.Overseer

def sendAudio(audio, player):
    Overseer.Overseer.send_data(audio, player.pid)

class Dirt(Enum):
    up = 0
    down = 1
    left = 2
    right = 3

class Placeable(dexml.Model):
    '''anything that has a specific location'''
    __metaclass__ = abc.ABCMeta

    zid = fields.Integer()
    zone = None

    def getZone(self):
        if self.zone == None:
            self.zone = Zone.fromID(self.zid)
        return self.zone

class Loadable:
    '''anything loaded and saved from the database'''
    __metaclass__ = abc.ABCMeta

    @abc.abstractmethod
    def save(self):
        '''saves the object to the database'''
        return

    @abc.abstractmethod
    def fromID(self):
        '''gets object data from database and returns initialized class'''
        return

class Fightable(Placeable):

    health = fields.Integer()
    power = fields.Integer()
    attackAudio = fields.String()

    def _calcDamage(self):
        return self.power

    def _takeDamage(self, dmg):
        self.health -= dmg
        if self.health <= 0:
            self._die()

    @abc.abstractmethod
    def _die(self):
        return

    def attack(self, target):
       self._takeDamage(target._calcDamage())
       target._takeDamage(self._calcDamage())
       Zone.fromID(self.zid).playAudio(self.attackAudio)

class Zone(dexml.Model, Loadable):

    zid = fields.Integer()
    paths = fields.List('Path')
    enemies = fields.List('Enemy')
    npcs = fields.List('Npc')
    players = fields.List('Player')

    @staticmethod
    def fromID(zid):
        return Zone.parse(Database.getZoneXML(zid))

    def save(self):
        Database.saveZone(self.zid, self.render())

    def getDest(self, dirt):
        for p in self.paths:
            print(str(dirt.value) + " -- " + str(p.dirt))
            if p.dirt == dirt.value:
                return p.dest
        return None

    def playAudio(self, audio):
        for p in self.players:
            self.sendAudio(audio, p)

    #called when a player enters the scene
    def onEnter(self, player):
        for e in self.enemies:
            player.attack(e)

class Player(Fightable, Loadable):

    pid = fields.Integer()
    uname = fields.String()
    password = fields.String()

    @staticmethod
    def fromID(pid):
        return Player.parse(Database.getPlayerXML(pid))

    def save(self):
        Database.savePlayer(self.pid, self.render())

    @staticmethod
    def login(uname, password):
        return Database.login(uname, password)

    def swipe(self, dirt):
        print("swiping")
        print(dirt)
        destID = self.getZone().getDest(dirt)
        print(destID)
        if destID == None:
            return
        print("moving zone")
        self.zid = destID
        self.zone = Zone.fromID(destID)
        self.zone.onEnter(self)

class Enemy(Fightable):
    attackAudio = fields.String()
    health = fields.Integer()
    maxHealth = None

class Npc(dexml.Model):
    audio = fields.String()

class Path(dexml.Model):
    dirt = fields.Integer()
    dest = fields.Integer()
