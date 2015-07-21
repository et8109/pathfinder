from ..core.dbcore import DBCore

class Table:
    initialized = False
    db = None

    @classmethod
    def yolo(cls):
        return "yolo"

    @classmethod 
    def query(cls, string):
        return cls.db.query(string)

    @classmethod
    def prepVar(cls, var):
        return cls.db.escapeString(var)

    @classmethod
    def setDb(cls):
        if(cls.initialized):
            raise dbException("cannot initialize db twice")
        cls.db = DBCore()
        cls.initialized = True

Table.setDb()
