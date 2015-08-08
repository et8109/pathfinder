from .Table import Table
query = Table.query

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
            )"""
            )

    @staticmethod
    def init():
        query(
                """INSERT INTO players (id, username, password, zoneid, health) 
                values (1, "guest", "guest", 1, 5)
                """,
                None
                )
                
    @staticmethod
    def from_id(pid):
        return query("select id, health, zoneid from players where id=%s",(pid,), single=True)
        
    @staticmethod
    def save(pid, health, zoneid):
        print("saving: "+str(zoneid)+", "+str(health)+", "+str(pid))
        query("update players set zoneid=%s, health=%s where id=%s",(zoneid, health, pid))

    @staticmethod
    def login(username, password):
        result = query("select id from players where username=%s and password=%s", (username, password), single=True)
        if result:
            return result[0]
        return None
