from .Table import Table, Table.prepVar as prepVar, Table.query as query

class Ambients(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE ambients (
            id int(3),
            zonex int(3),
            zoney int(3),
            PRIMARY KEY (zonex, zoney, id)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO ambients (id, zonex, zoney) 
                values (0 ,     1,     1)"""
                )
