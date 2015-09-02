import unittest

from pkg.Overseer import Overseer
from pkg.interfaces.common import Player, Zone, Path, Dirt
from pkg.interfaces.enemy import Wolf
from pkg.interfaces.database import Database

class testGeneral(unittest.TestCase):

    def testPlayerStart(self):
        pid = Player.login("guest", "guest")
        Player.startPlayer(pid)
        p = Player.fromID(pid)
        print(len(p.getZone().players))
        assert len(p.getZone().players) == 1
        p.logout()

    def testWalking(self):
        pid = Player.login("guest", "guest")
        Player.startPlayer(pid)
        p = Player.fromID(pid)
        p.swipe(Dirt.up)
        p.logout()

    def testZoneSaving(self):
        z = Zone.fromID(1)
        z.save()

    def testEnemy(self):
        z = Zone.fromID(2)
        assert len(z.enemies) == 1

if __name__ == "__main__":
    Overseer.testing = True
    unittest.main()
