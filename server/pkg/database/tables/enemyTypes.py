from .Table import Table, Table.query as query

class EnemyTypes(Table):

    @staticmethod
    def create():
        query("""CREATE TABLE enemyTypes (
            id int(3) NOT NULL,
            PRIMARY KEY (id)
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO enemyTypes (id) 
                values (%s)
                """,
                (1)
                )