from .Table import Table, Table.query as query

class Audio(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE audio (
            objid int(3) NOT NULL,
            table varchar(20) NOT NULL,
            audioid int(3) NOT NULL,
            PRIMARY KEY (objid, table, audioid)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO audio (objid, table, audioid) 
                values (%s, %s, %s)
                """,
                (1, 1, 1)
                )
