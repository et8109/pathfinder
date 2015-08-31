from enum import Enum
import abc
import dexml
from dexml import fields

from pkg.database.database import Database
import pkg.Overseer
from pkg.cache.cache import Cache

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
    _zone = None

    def getZone(self):
        if self._zone == None:
            self._zone = Zone.fromID(self.zid)
        return self._zone

    def changeZone(self, zid):
        self._zone = None
        self.zid = zid

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
       self.getZone().playAudio(self.attackAudio)

class Zone(dexml.Model, Loadable):

    zid = fields.Integer()
    paths = fields.List('Path')
    enemies = fields.List('Enemy')
    npcs = fields.List('Npc')
    players = fields.List('Player')

    @staticmethod
    def _getKey(zid):
        return "z"+str(zid)

    @staticmethod
    def fromID(zid):
        key = Zone._getKey(zid)
        z = None
        try:
            z = Cache.get(key)
        except KeyError:
            z = Zone.parse(Database.getZoneXML(zid))
        Cache.set(key, z)
        return z

    def save(self):
        Database.saveZone(self.zid, self.render())
        Cache.set(Zone._getKey(self.zid), self)

    def getDest(self, dirt):
        for p in self.paths:
            print(str(dirt.value) + " -- " + str(p.dirt))
            if p.dirt == dirt.value:
                return p.dest
        return None

    def playAudio(self, audio):
        for p in self.players:
            self.sendAudio(audio, p)

    def onLeave(self, player):
        '''when a player leaves the zone'''
        self.players.remove(player)

    #called when a player enters the scene
    def onEnter(self, player):
        if player in self.players:
            pass #TODO throw exception, but it messes something up
        else:
            self.players.append(player)
        '''for e in self.enemies:
            player.attack(e)'''

class Player(Fightable, Loadable):

    pid = fields.Integer()
    uname = fields.String()
    password = fields.String()

    @staticmethod
    def _getKey(pid):
        return "p"+str(pid)

    @staticmethod
    def fromID(pid):
        key = Player._getKey(pid)
        p = None
        try:
            p = Cache.get(key)
        except KeyError:
            p = Player.parse(Database.getPlayerXML(pid))
        Cache.set(key, p)
        return p

    def save(self):
        Database.savePlayer(self.pid, self.render())
        Cache.set(Player._getKey(self.pid), self)

    @staticmethod
    def login(uname, password):
        pid = Database.login(uname, password)
        player = Player.fromID(pid)
        player.getZone().onEnter(player)
        print("--->>> "+str(len(player.getZone().players)))
        return pid

    def logout(self):
        self.getZone().onLeave(self)

    def swipe(self, dirt):
        destID = self.getZone().getDest(dirt)
        if destID == None:
            return
        self.moveZone(destID)

    def moveZone(self, destID):
        self.getZone().onLeave(self)
        self.changeZone(destID)
        self.getZone().onEnter(self)

class Enemy(Fightable):
    attackAudio = fields.String()
    health = fields.Integer()
    maxHealth = None

class Npc(dexml.Model):
    audio = fields.String()

class Path(dexml.Model):
    dirt = fields.Integer()
    dest = fields.Integer()
