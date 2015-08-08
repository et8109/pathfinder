from .Table import Table
query = Table.query

class Enemy(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE enemies (
            id int(3) NOT NULL AUTO_INCREMENT,
            type int(3) NOT NULL,
            zoneid int(3),
            PRIMARY KEY (id),
            FOREIGN KEY (zoneid) REFERENCES zones(id)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO enemies (zoneid, type) 
                values (%s, %s)
                """,
                (1, 1)
                )
