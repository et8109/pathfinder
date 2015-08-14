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
                Zone.reset(1, 2,None,None,None)
                Zone.reset(2, None,1,None,None)
                print("done")
            else:
                print("aborting")
        if line == 'test':
            print("testing")
            z = Zone.from_id(1)
            print("testing completed")
