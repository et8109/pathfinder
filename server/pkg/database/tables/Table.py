from ..core.dbcore import DBCore

class Table:
    initialized = False
    conn = None

    @staticmethod 
    def query(stmt, data=None, single=False):
        return Table.conn.query(stmt, data, conn, single)

    '''@staticmethod
    def prepVar(cls, var):
        return cls.conn.escapeString(var)'''

    @staticmethod
    def setDb():
        if(Table.initialized):
            raise dbException("cannot initialize db twice")
        Table.conn = DBCore.get_conn()
        Table.initialized = True

Table.setDb()
