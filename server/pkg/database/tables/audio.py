from .Table import Table
query = Table.query

class Audio(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE audio (
            objid int(3) NOT NULL,
            table varchar(20) NOT NULL,
            audioid int(3) NOT NULL,
            name varchar(20) NOT NULL,
            PRIMARY KEY (objid, table, audioid)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO audio (objid, table, audioid, name) 
                values (%s, %s, %s, %s)
                """,
                (1, 'a', 0, "Chomp.mp3")
                )
