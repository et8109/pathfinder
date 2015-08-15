import sys
from pkg.Overseer import Overseer
from pkg.interfaces.Zone import Zone

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
                z.save()
                print("done")
            else:
                print("aborting")
        if line == 'test':
            print("testing")
            z = Zone.from_id(1)
            print("testing completed")
