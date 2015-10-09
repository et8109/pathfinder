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

    def _changeZone(self, zid):
        self._zone = None
        self.zid = zid

    def walk(self, dirt):
        oldzone = self.getZone()
        newzone = Zone.fromID(oldzone._getDestID(dirt))
        if newzone:
            oldzone.onLeave(self)
            self._changeZone(newzone.zid)
            newzone.onEnter(self)
            self.getZone()._playAudio("Chomp.mp3")

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

    def _calcDamage(self):
        return self.power

    def _takeDamage(self, dmg):
        self.health -= dmg
        if self.health <= 0:
            self._die()

    def _die(self):
        self.getZone().onDead(self)
        self.getZone()._playAudio(self.deathAudio)
    
    def attack(self, target):
        self._takeDamage(target._calcDamage())
        target._takeDamage(self._calcDamage())
        self.getZone()._playAudio(self.attackAudio)

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
        #update enemy types
        for e in z.enemies:
            e.setClass()
        return z

    def save(self):
        Database.saveZone(self.zid, self.render())
        Cache.set(Zone._getKey(self.zid), self)

    def _getDestID(self, dirt):
        for p in self.paths:
            if p.dirt == dirt:
                return p.dest
        raise NoPathException("no path available: zone {}, dirt {}".format(str(self.zid), str(dirt)))

    def _playAudio(self, audio):
        for p in self.players:
            sendToPlayer(audio, p)

    def onDead(self, thing):
        if isinstance(thing, Player):
            self.players.remove(thing)
        elif isinstance(thing, Enemy):
            self.enemies.remove(thing)
        else:
            raise Exception("unkown type of dead thing: "+str(klass))
        self.save()

    def onLeave(self, thing):
        '''when a something leaves the zone'''
        if isinstance(thing, Player):
            self.players.remove(thing)
        elif isinstance(thing, Enemy):
            self.enemies.remove(thing)
        else:
            raise Exception("unkonwn thing leaving zone")
        self.save()

    def onEnter(self, thing):
        if isinstance(thing, Player):
            player = thing
            if player in self.players:
                raise Exception("player already in zone") #TODO throw exception, but it messes something up
                return
            else:
                self.players.append(player)
            for e in self.enemies:
                player.attack(e)
                if e.health > 0:
                    e._retreat()
            for n in self.npcs:
                n.talk()
        elif isinstance(thing, Enemy):
            self.enemies.append(thing)
        self.save()

class Player(Fightable, Loadable):

    pid = fields.Integer()
    uname = fields.String()
    password = fields.String()
    maxHealth = 3
    loggedIn = False
    deathAudio = "Dead.mp3"
    attackAudio = fields.String()
    deathAudio = fields.String()

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
        self.loggedIn = True
        self.getZone().onEnter(self)

    def logout(self):
        self.getZone().onLeave(self)
        self.loggedIn = False

    def swipe(self, dirt):
        if not self.loggedIn:
            raise PlayerNotLoggedInException
        try:
            self.walk(dirt.value)
        except NoPathException as e:
            print(e)
        self.save()

    def _die(self):
        Fightable._die(self)
        self._respawn()

    def _respawn(self):
        self._changeZone(1)
        self.health = Player.maxHealth
        self.getZone().onEnter(self)

class Enemy(Fightable):
    __metaclass__ = abc.ABCMeta
    maxHealth = None
    #etype = None # used to know which enemy to transform into

    def setClass(self):
        '''called after a zone is loaded from the xml'''
        self.__class__ = eval(self.etype)#TODO change

    def _die(self):
        Fightable._die(self)
        #respawn
        #Zone.fromID(2).addEnemy(self.__class__(zid=2))

    def _retreat(self):
        '''retreat after a round of combat'''
        if self.health <= 0:
            raise DeadCannotPerformActionException
        for path in self.getZone().paths:
            z = Zone.fromID(path.dest)
            if not z.enemies and not z.players:
                print("retreating to: "+str(z.zid))
                self.getZone()._playAudio(self.__class__.retreatAudio)
                self.walk(path.dirt)
                z._playAudio(self.__class__.retreatAudio) 
                print("retreated to: "+str(z.zid))
                return

class Npc(Placeable):
    __metaclass__ = abc.ABCMeta
    audio = fields.String()

    def setClass(self):
        self.__class__ = eval(self.etype)#TODO change

    def talk(self):
        self.getZone()._playAudio(self.audio)

class Path(dexml.Model):
    dirt = fields.Integer()
    dest = fields.Integer()


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
