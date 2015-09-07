from enum import Enum
import abc
import dexml
from dexml import fields

from pkg.database.database import Database
#from pkg.Overseer import Overseer
from pkg.cache.cache import Cache

def sendToPlayer(audio, player):
    from pkg.Overseer import Overseer
    Overseer.sendToPlayer(audio, player.pid)

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
    deathAudio = fields.String()

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
        print("---- attacking")
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
            if p.dirt == dirt.value:
                return p.dest
        return None

    def playAudio(self, audio):
        for p in self.players:
            sendToPlayer(audio, p)

    def onPlayerDead(self, player):
        self.players.remove(player)
        self.save()

    def onPlayerLeave(self, player):
        '''when a player leaves the zone'''
        self.players.remove(player)
        self.save()

    #called when a player enters the scene
    def onPlayerEnter(self, player):
        print("zone "+str(self.zid))
        print("eneimes: "+str(self.enemies))
        print("players: "+str(self.players))
        if player in self.players:
            raise Exception("player already in zone") #TODO throw exception, but it messes something up
            return
        else:
            self.players.append(player)
        for e in self.enemies:
            player.attack(e)
        self.save()

    def removeEnemy(self, enemy):
        self.enemies.remove(enemy)

    def addEnemy(self, enemy):
        self.enemies.append(enemy)
        return enemy

class Player(Fightable, Loadable):

    pid = fields.Integer()
    uname = fields.String()
    password = fields.String()
    maxHealth = 3
    loggedIn = False

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
    def getPid(uname, password):
        return Database.getPid(uname, password)

    def login(self):
        self.getZone().onPlayerEnter(self)
        self.loggedIn = True

    def logout(self):
        self.getZone().onPlayerLeave(self)
        self.loggedIn = False

    def swipe(self, dirt):
        if not self.loggedIn:
            raise PlayerNotLoggedInException
        print("swipe! current zone: "+str(self.getZone().zid))
        destID = self.getZone().getDest(dirt)
        if destID == None:
            print("no dest")
            return
        print("moving to: "+str(destID))
        self.moveZone(destID)
        self.save()

    def moveZone(self, destID):
        self.getZone().onPlayerLeave(self)
        self.changeZone(destID)
        self.getZone().onPlayerEnter(self)

    def _die(self):
        sendToPlayer("Dead.mp3", self)
        self.getZone().onPlayerDead(self)
        self.changeZone(1)
        self.health = Player.maxHealth
        self.getZone().onPlayerEnter(self)

class Enemy(Fightable):
    maxHealth = None

    def _die(self):
        self.getZone().playAudio(self.deathAudio)
        self.getZone().removeEnemy(self)
        #Zone.fromID(2).addEnemy(self.__class__(zid=2))

class Npc(dexml.Model):
    audio = fields.String()

class Path(dexml.Model):
    dirt = fields.Integer()
    dest = fields.Integer()


##############################################
#exceptions
##############################################
class InterfaceException(Exception):
    pass

class PlayerNotLoggedInException(InterfaceException):
    pass
