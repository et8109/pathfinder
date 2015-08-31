import unittest

from pkg.Overseer import Overseer
from pkg.interfaces.common import Player, Zone, Path, Dirt
from pkg.interfaces.enemy import Wolf
from pkg.interfaces.database import Database

class testGeneral(unittest.TestCase):

    def yolo(self):
        pass

    def testLogin(self):
        pid = Player.login("guest", "guest")
        p = Player.fromID(pid)
        print(len(p.getZone().players))
        assert len(p.getZone().players) == 1
        p.logout()

    def testWalking(self):
        pid = Player.login("guest", "guest")
        p = Player.fromID(pid)
        p.swipe(Dirt.up)
        p.logout()

    def testZoneSaving(self):
        z = Zone.fromID(1)
        z.save()

if __name__ == "__main__":
    unittest.main()
