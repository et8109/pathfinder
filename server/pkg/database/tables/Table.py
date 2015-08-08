from pkg.database.core.dbcore import DBCore

class Table:
    initialized = False
    conn = None

    @staticmethod 
    def query(stmt, data=None, single=False):
        return DBCore.query(stmt, data, Table.conn, single)

    @staticmethod
    def setDb():
        if(Table.initialized):
            raise dbException("cannot initialize db twice")
        Table.conn = DBCore.get_conn()
        Table.initialized = True

Table.setDb()
