from database.core.dbcore import DBCore
from database.tables.ambients import Ambients

if __name__ == "__main__":
    print("resetting db")
    db = DBCore()
    db.resetdb()
    print("creating tables")
    Ambients.create()
    Ambients.init()
    print("done db reset")
