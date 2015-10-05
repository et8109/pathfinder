import sys, os
from pkg.Overseer import Overseer
from pkg.interfaces.database import Database

def printHelp():
        print("manage commands:")
        print(" -startserver")
        print(" -resetdatabase")
        print(" -test")

if __name__ == "__main__":
    #clear terminal
    os.system('cls' if os.name == 'nt' else 'clear')
    if len(sys.argv) != 2:
        printHelp()
        exit()
    #identify command
    cmd = sys.argv[1]
    if cmd == 'startserver':
        Overseer().startServer()
    if cmd == 'resetdatabase':
        print("Reset database?")
        ans = input()
        if ans == 'Y':
            print("resetting")
            Database.reset()
            print("done")
        else:
            print("aborting")
    if cmd == 'test':
        print("testing")
        startTest()
        print("testing completed")
