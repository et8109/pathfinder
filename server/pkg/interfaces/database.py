from pkg.database.database import Database as DB
from pkg.interfaces.common import Player, Zone, Path, Dirt
from pkg.interfaces.enemy import Wolf
from pkg.interfaces.npc import Man

class Database:
    
    @staticmethod
    def registerPlayer(newUname, newPword):
        #check player does not exist TODO
        newPid = DB.numPlayers()
        p = Player(health=5, zid=1, power=1, attackAudio="attack.mp3",
                deathAudio="Dead.mp3", pid=newPid, uname=newUname,password=newPword)
        p.save()
        DB.addPlayerToTable(newUname, newPword, newPid)
        return p

    @staticmethod
    def resetPlayer(_uname, _pword):
        _pid = DB.getPid(_uname, _pword)
        p = Player(health=5, zid=1, power=1, attackAudio="attack.mp3", deathAudio="Dead.mp3", pid=_pid, uname=_uname,password=_pword)
        p.save()

    @staticmethod
    def reset():
        DB.clearAll()
        Database.registerPlayer("guest", "guest")
        Database.registerPlayer("test", "test")

        #start zone
        z1 = Zone(zid=1)
        z1.paths.append(Path(dirt=Dirt.up.value,dest=2))
        z1.save()

        #up 1
        z2 = Zone(zid=2)
        z2.paths.append(Path(dirt=Dirt.down.value,dest=1))
        z2.paths.append(Path(dirt=Dirt.up.value,dest=3))
        z2.enemies.append(Wolf(zid=2))
        z2.save()

        #up 2
        z3 = Zone(zid=3)
        z3.paths.append(Path(dirt=Dirt.down.value,dest=2))
        z3.paths.append(Path(dirt=Dirt.right.value,dest=4))
        z3.save()

        #up2 r1
        z4 = Zone(zid=4)
        z4.npcs.append(Man(zid=4))
        z4.paths.append(Path(dirt=Dirt.left.value,dest=3))
        z4.save()

    @staticmethod
    def playerLogin(uname, pword):
        return DB.login(uname, pword)
