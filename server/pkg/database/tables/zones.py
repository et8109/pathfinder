from .Table import Table
query = Table.query

class Zones(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE zones (
            id int(3) NOT NULL,
            x int(3) NOT NULL,
            y int(3) NOT NULL,
            z int(3),
            upid int(3),
            downid int(3),
            leftid int(3),
            rightid int(3),
            PRIMARY KEY (id)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO zones (id,x,y,z,upid, downid, leftid, rightid) 
                values
                (1, 1,1,1, 2,2,2,2),
                (2, 2,2,1, 1,1,1,1)
                """
                )
    @staticmethod
    def from_id(zid):
        return query("select id, x,y,z, upid,downid,leftid,rightid from zones where id=%s",(zid,),single=True)
