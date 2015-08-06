from .Table import Table, Table.prepVar as prepVar, Table.query as query

class Players(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE players (
            id int(3),
            username varchar(20),
            password varchar(20),
            zoneid int(3),
            health int(3),
            PRIMARY KEY (id)
            FOREIGN KEY (zoneid) REFERENCES zones(id)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO players (id, username, password, zoneid, health) 
                values (1, 'guest', 'guest', 1, 5)
                """
                )
