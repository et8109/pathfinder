from ._Table import Table

class Ambients(Table):

    @classmethod
    def create(cls):
        cls.db.query("""CREATE TABLE ambients (
            id int(3),
            zonex int(3),
            zoney int(3),
            PRIMARY KEY (zonex, zoney, id)
            )"""
            )

    @classmethod
    def init(cls):
        cls.db.query(
                """INSERT INTO ambients (id, zonex, zoney) 
                values (0 ,     1,     1)"""
                )

    @classmethod
    def getInZone(cls, zonex, zoney):
        zonex = prepVar(zonex)
        zoney = prepVar(zoney)
        rows = cls.db.query("select id from ambients where zonex="+zonex+" and zoney="+zoney)
        for r in rows:
            r['audios'] = Audio.getUrls('a'+r['id'])
        return rows
