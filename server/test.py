import unittest

from pkg.Overseer import Overseer
import pkg.interfaces.common as common
from pkg.interfaces.common import Player, Zone, Path, Dirt
from pkg.interfaces.enemy import Wolf
from pkg.interfaces.database import Database
from pkg.cache.cache import Cache

def getTestPlayerReset():
    Database.resetPlayer("test", "test")
    pid = Player.getPid("test", "test")
    return Player.fromID(pid)

class testGeneral(unittest.TestCase):

    def testLoginLogout(self):
        SimpleMap.setup()
        Database.resetPlayer("test", "test")
        pid = Player.getPid("test", "test")
        p = Player.fromID(pid)
        assert pid == p.pid
        p.login()
        assert p in p.getZone().players
        p.logout()
        assert p not in p.getZone().players
        #make sure you cant do stuff while logged out
        try:
            p.swipe(Dirt.up)
            self.fail("swiped while not lgoged in")
        except common.PlayerNotLoggedInException:
            pass

    def testWalking(self):
        SimpleMap.setup()#??
        p = getTestPlayerReset()
        p.login()
        z = p.getZone()
        assert len(z.players) == 1
        #no path
        p.swipe(Dirt.down)
        assert p in z.players
        #path exists
        p.swipe(Dirt.up)
        assert p not in z.players
        newz = p.getZone()
        assert p in newz.players
        p.logout()
        assert p not in newz.players

    def testZoneSaving(self):
        z = Zone.fromID(1)
        z.save()

    def testEnemy(self):
        SimpleMap.setup()
        w = Wolf(zid=1)
        z1 = Zone.fromID(1)
        z1.onEnter(w)
        assert len(z1.enemies) == 1
        w._die()
        assert len(z1.enemies) == 0

    def testRetreat(self):
        SimpleMap.setup()
        w = Wolf(zid=1)
        z1 = Zone.fromID(1)
        z1.onEnter(w)
        z1.save()
        Cache.clear()
        z = Zone.fromID(1)
        w = z.enemies[0]
        w._retreat()
        assert w.getZone().zid == 2
        assert not z.enemies
        z.save()

class Map():
    pass

class SimpleMap(Map):

    @staticmethod
    def setup():
        z1 = Zone(zid=1)
        z2 = Zone(zid=2)
        z1.paths.append(Path(dirt=Dirt.up.value,dest=2))
        z1.save()
        z2.save()

if __name__ == "__main__":
    Overseer.testing = True
    unittest.main()
