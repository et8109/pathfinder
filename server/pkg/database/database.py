
class Database:
    zone_dir = "pkg/database/zoneXML/"
    player_dir = "pkg/database/playerXML/"

    @staticmethod
    def getZoneXML(zid):
        f = open(Database.zone_dir+str(zid)+".xml")
        xml = f.read()
        f.close()
        return xml

    @staticmethod
    def saveZone(zid, xml):
        f = open(Database.zone_dir+str(zid)+".xml", 'w') 
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
        f = open(Database.player_dir+str(pid)+".xml", 'w')
        f.write(xml)
        f.close()
