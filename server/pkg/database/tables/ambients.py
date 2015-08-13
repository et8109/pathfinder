from .Table import Table
query = Table.query

class Ambients(Table):
    prefix='a'

    @staticmethod
    def create():
        query("""CREATE TABLE ambients (
            id int(3),
            zoneid int(3),
            PRIMARY KEY (zonex, zoney, id)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO ambients (id, zoneid) 
                values (0, 1)"""
                )

    @staticmethod
    def get_in_zone(zid):
        return query("select aud.name from ambients amb, audio aud where amb.zoneid=%s, aud.objid=amb.id, aud.table=%s, aud.id=0",(zid,Ambients.prefix))
