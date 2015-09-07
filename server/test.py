import unittest

from pkg.Overseer import Overseer
import pkg.interfaces.common as common
from pkg.interfaces.common import Player, Zone, Path, Dirt
from pkg.interfaces.enemy import Wolf
from pkg.interfaces.database import Database

def getTestPlayerReset():
    Database.resetPlayer("test", "test")
    pid = Player.getPid("test", "test")
    return Player.fromID(pid)

class testGeneral(unittest.TestCase):

    def testLoginLogout(self):
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
        z99 = Zone(zid=99)
        z99.save()
        z99 = Zone.fromID(99)
        e = z99.addEnemy(Wolf(zid=99))
        z99.save()
        assert len(z99.enemies) == 1
        e._die()
        assert len(z99.enemies) == 0
        z99.save()

if __name__ == "__main__":
    Overseer.testing = True
    unittest.main()
