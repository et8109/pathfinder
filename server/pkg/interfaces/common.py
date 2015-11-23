from enum import Enum
import abc
import dexml
from dexml import fields

from pkg.database.database import Database
#from pkg.Overseer import Overseer
from pkg.cache.cache import Cache

def sendToPlayer(audio, player):
    '''sends audio title to given player'''
    print("sending to player: "+audio)
    if audio is None:
        raise TypeError
    from pkg.Overseer import Overseer
    Overseer.sendToPlayer(audio, player.pid)

class Dirt(Enum):
    '''Enum to represent the 4 different directions'''
    up = 0
    down = 1
    left = 2
    right = 3

class Audio(dexml.Model):
    '''Audio information'''
    name = fields.String()
    length = fields.Integer()

class Placeable(dexml.Model):
    '''Anything that has a specific zone it is in'''
    __metaclass__ = abc.ABCMeta

    zid = fields.Integer()
    _zone = None

    def getZone(self):
        if self._zone == None:
            self._zone = Zone.fromID(self.zid)
        return self._zone

    def setZone(self, zid):
        self._zone = None
        self.zid = zid

    @abc.abstractmethod
    def enter(self):
        '''enters a zone'''
        return

    @abc.abstractmethod
    def leave(self):
        '''leaves a zone'''
        return

    def walk(self, dirt):
        oldzone = self.getZone()
        path = oldzone.getPath(dirt)
        newzone = Zone.fromID(path.dest)
        if newzone:
            self.leave()
            self.enter(newzone)
            oldzone.playAudio(path.audio)
            newzone.playAudio(path.audio)

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

    def calcDamage(self):
        return self.power

    def die(self):
        self.getZone().playAudio(self.deathAudio)

    def takeDamage(self, dmg):
        self.health -= dmg
        if self.health <= 0:
            self.die()
            return
        try:
            self.retreat()
        except AttributeError:
            pass

    def attack(self, target):
        self.takeDamage(target.calcDamage())
        target.takeDamage(self.calcDamage())
        self.getZone().playAudio(self.attackAudio)

class Zone(dexml.Model, Loadable):

    zid = fields.Integer()
    paths = fields.List('Path')
    enemies = fields.List('Enemy')
    npcs = fields.List('Npc')
    players = fields.List('Player')

    @staticmethod
    def getKey(zid):
        return "z"+str(zid)

    @staticmethod
    def fromID(zid):
        key = Zone.getKey(zid)
        z = None
        try:
            z = Cache.get(key)
        except KeyError:
            z = Zone.parse(Database.getZoneXML(zid))
        Cache.set(key, z)
        #update types
        for e in z.enemies:
            e.setClass()
        '''for n in z.npcs:
            n.setClass() not needed?'''
        return z

    def save(self):
        Database.saveZone(self.zid, self.render())
        Cache.set(Zone.getKey(self.zid), self)

    def getPath(self, dirt):
        for p in self.paths:
            if p.dirt == dirt:
                return p
        raise NoPathException("no path available: zone {}, dirt {}".format(str(self.zid), str(dirt)))

    def playAudio(self, audio):
        '''Sends audio to all players in zone'''
        print("num: "+str(len(self.players)))
        for p in self.players:
            sendToPlayer(audio.name, p)

class Player(Fightable, Loadable):

    pid = fields.Integer()
    uname = fields.String()
    password = fields.String()
    maxHealth = 3
    loggedIn = False
    attackAudio = fields.Model("Audio")
    deathAudio = fields.Model("Audio")
    gold = fields.Integer()

    @staticmethod
    def getKey(pid):
        return "p"+str(pid)

    @staticmethod
    def fromID(pid):
        key = Player.getKey(pid)
        p = None
        try:
            p = Cache.get(key)
        except KeyError:
            p = Player.parse(Database.getPlayerXML(pid))
        Cache.set(key, p)
        return p

    def save(self):
        Database.savePlayer(self.pid, self.render())
        Cache.set(Player.getKey(self.pid), self)

    @staticmethod
    def getPid(uname, password):
        return Database.getPid(uname, password)

    def login(self):
        self.loggedIn = True
        self.enter(self.getZone())

    def logout(self):
        self.leave(self.getZone())
        self.loggedIn = False

    def swipe(self, dirt):
        if not self.loggedIn:
            raise PlayerNotLoggedInException
        try:
            self.walk(dirt.value)
        except NoPathException as e:
            print(e)
        self.save()

    def respawn(self):
        self.setZone(1)
        self.health = Player.maxHealth
        self.enter(self.getZone()) 

    def die(self):
        Fightable.die(self)
        self.getZone().players.remove(self)
        self.getZone().save()
        self.respawn()

    def enter(self, zone):
        if self in zone.players:
            raise Exception("player already in zone")
            return
        self.setZone(zone.zid)
        zone.players.append(self)
        for e in zone.enemies:
            self.attack(e)
        for n in zone.npcs:
            n.talk()
        zone.save()

    def leave(self):
        z = self.getZone()
        z.players.remove(self)
        z.save()


class Enemy(Fightable):
    __metaclass__ = abc.ABCMeta
    maxHealth = None

    def setClass(self):
        '''called after a zone is loaded from the xml'''
        self.__class__ = eval(self.etype)#TODO change

    def respawn(self):
        pass
        #self.enter(Zone.fromID(self.respawnZone))

    def die(self):
        Fightable.die(self)
        self.getZone().enemies.remove(self)
        self.getZone().save()
        self.respawn()

    def retreat(self):
        '''retreat after a round of combat'''
        if self.health <= 0:
            raise DeadCannotPerformActionException
        for path in self.getZone().paths:
            z = Zone.fromID(path.dest)
            if not z.enemies and not z.players:
                self.getZone().playAudio(self.__class__.retreatAudio)
                self.walk(path.dirt)
                z.playAudio(self.__class__.retreatAudio) 
                return

    def enter(self, zone):
        if self in zone.enemies:
            raise Exception("Enemy already in zone")
        zone.enemies.append(self)
        self.setZone(zone.zid)
        zone.save()

    def leave(self):
        z = self.getZone()
        z.enemies.remove(self)
        z.save()

class Npc(Placeable):
    __metaclass__ = abc.ABCMeta
    audio = fields.Model("Audio")

    def setClass(self):
        self.__class__ = eval(self.etype)#TODO change

    def talk(self):
        self.getZone().playAudio(self.audio)

    def enter(self, zone):
        if self in zone.enemies:
            raise Exception("Npc already in zone")
        zone.enemies.append(self)
        self.setZone(zone.zid)
        zone.save()

    def leave(self):
        z = self.getZone()
        z.enemies.remove()
        z.save()


class Path(dexml.Model):
    dirt = fields.Integer()
    dest = fields.Integer()
    audio = fields.Model("Audio")

##############################################
#exceptions
##############################################
class InterfaceException(Exception):
    pass

class NoPathException(InterfaceException):
    pass

class PlayerNotLoggedInException(InterfaceException):
    pass

class DeadCannotPerformActionException(InterfaceException):
    pass

from pkg.interfaces.enemy import Wolf #need to import enemy classes for trasnformation after loading from xml
