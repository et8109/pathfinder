import sys, os
from pkg.Overseer import Overseer
from pkg.interfaces.common import Player, Zone, Path
from pkg.interfaces.enemy import Wolf

if __name__ == "__main__":
    for line in sys.argv:
        if line == 'startserver':
            overseer = Overseer()
        if line == 'resetdatabase':
            print("Reset database?")
            ans = input()
            if ans == 'Y':
                print("deleting")
                dbdir = "./pkg/database"
                mycwd = os.getcwd()
                os.chdir(dbdir)
                for f in os.listdir("zoneXML"):
                    os.remove("zoneXML/"+f)
                for f in os.listdir("playerXML"):
                    os.remove("playerXML/"+f)
                os.chdir(mycwd)
                print("resetting")
                z1 = Zone(zid=1)
                #print(str(z1.zid))
                z1.paths.append(
                    Path(dirt=Path.up,dest=2))
                z1.save()
                z2 = Zone(zid=2)
                z2.paths.append(Path(dirt=Path.down,dest=1))
                z2.enemies.append(Wolf())
                z2.save()
                p = Player(pid=1, health=5, zid=1, uname="guest",password="guest")
                p.save()
                print("done")
            else:
                print("aborting")
        if line == 'test':
            print("testing")
            z = Zone.fromID(1)
            z.enemies.append(Wolf(health=3))
            z.save()
            print("testing completed")
