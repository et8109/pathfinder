import sys, os
#from pkg.database.database import Database
from pkg.Overseer import Overseer
from pkg.interfaces.common import Player, Zone, Path
from pkg.interfaces.enemy import Wolf
from pkg.interfaces.database import Database

if __name__ == "__main__":
    for line in sys.argv:
        if line == 'startserver':
            overseer = Overseer()
        if line == 'resetdatabase':
            print("Reset database?")
            ans = input()
            if ans == 'Y':
                print("resetting")
                Database.reset()
                print("done")
            else:
                print("aborting")
        if line == 'test':
            print("testing")
            print("testing completed")
