import os
import tables

class PlayerInfo(tables.IsDescription):
    uname = tables.StringCol(20)
    pword = tables.StringCol(20)
    pid = tables.Int32Col()

class Database:
    zone_dir = "pkg/database/zoneXML/"
    player_dir = "pkg/database/playerXML/"
    tableName = "pathfinder.h5"

    @staticmethod
    def getZoneXML(zid):
        f = open(Database.zone_dir+str(zid)+".xml")
        xml = f.read()
        f.close()
        return xml

    @staticmethod
    def saveZone(zid, xml):
        f = open(Database.zone_dir+str(zid)+".xml", "w") 
        f.write(xml)
        f.close()

    @staticmethod
    def getPlayerXML(pid):
        f = open(Database.player_dir+str(pid)+".xml")
        xml = f.read()
        f.close()
        return xml

    @staticmethod
    def savePlayer(pid, xml):
        f = open(Database.player_dir+str(pid)+".xml", "w")
        f.write(xml)
        f.close()

    @staticmethod
    def numPlayers():
        DIR = "./pkg/database/playerXML"
        return len([name for name in os.listdir(DIR) if os.path.isfile(os.path.join(DIR, name))])

    @staticmethod
    def login(uname, pword):
        DIR = "./pkg/database/"
        tableFile = tables.open_file(DIR+Database.tableName, mode = "a")
        table = tableFile.root.info.playerIds
        qry = "(uname == b'{}') & (pword == b'{}')".format(uname, pword)
        pid = [ x['pid'] for x in table.where(qry) ]
        tableFile.close()
        if len(pid) == 1:
            return pid[0]
        else:
            return None
 
    @staticmethod
    def clearAll():
        #clear all xml files
        dbdir = "./pkg/database"
        mycwd = os.getcwd()
        os.chdir(dbdir)
        for f in os.listdir("zoneXML"):
            os.remove("zoneXML/"+f)
        for f in os.listdir("playerXML"):
            os.remove("playerXML/"+f)
        #clear uname to id table
        tableFile = tables.open_file(Database.tableName, mode = "w", title = "Other Info")
        group = tableFile.create_group("/", 'info', 'Player information')
        table = tableFile.create_table(group, 'playerIds', PlayerInfo, "Player Ids")
        os.chdir(mycwd)
        tableFile.close()

    @staticmethod
    def addPlayerToTable(uname, pword, pid):
        #add to id table
        DIR = "./pkg/database/"
        tableFile = tables.open_file(DIR+Database.tableName, mode = "a")
        table = tableFile.root.info.playerIds
        pinfo = table.row
        pinfo['uname'] = uname
        pinfo['pword'] = pword
        pinfo['pid'] = pid
        pinfo.append()
        table.flush()
        tableFile.close()
