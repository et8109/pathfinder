from pkg.database.database import Database as DB
from pkg.interfaces.common import Player, Zone, Path
from pkg.interfaces.enemy import Wolf

class Database:
    
    @staticmethod
    def registerPlayer(newUname, newPword):
        #check player does not exist TODO
        newPid = DB.numPlayers()
        p = Player(pid=newPid, health=5, zid=1, uname=newUname,password=newPword)
        p.save()
        DB.addPlayerToTable(newUname, newPid)

    @staticmethod
    def reset():
        DB.clearAll()
        Database.registerPlayer("guest", "guest")
        z1 = Zone(zid=1)
        z1.paths.append(Path(dirt=Path.up,dest=2))
        z1.save()
        z2 = Zone(zid=2)
        z2.paths.append(Path(dirt=Path.down,dest=1))
        z2.enemies.append(Wolf())
        z2.save()  
