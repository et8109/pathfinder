from .Table import Table
query = Table.query

class Npcs(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE npcs (
            id int(3) NOT NULL AUTO_INCREMENT,
            zoneid int(3),
            PRIMARY KEY (id),
            FOREIGN KEY (zoneid) REFERENCES zones(id)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO npcs (zoneid) 
                values (%s)
                """,
                (1)
                )
