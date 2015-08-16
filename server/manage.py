import sys
from pkg.Overseer import Overseer
from pkg.interfaces.Zone import Zone
from pkg.interfaces.Path import Path
from pkg.interfaces.Enemy import *

if __name__ == "__main__":
    for line in sys.argv:
        if line == 'startserver':
            overseer = Overseer()
        if line == 'resetdatabase':
            print("Reset database?")
            ans = input()
            if ans == 'Y':
                print("resetting")
                z = Zone(zid=1)
                z.paths.append(Path(dirt=Path.up,dest=2))
                z.save()
                z = Zone(zid=2)
                z.paths.append(Path(dirt=Path.down,dest=1))
                z.save()
                print("done")
            else:
                print("aborting")
        if line == 'test':
            print("testing")
            z = Zone.fromID(1)
            z.enemies.append(Wolf(health=3))
            z.save()
            print("testing completed")
