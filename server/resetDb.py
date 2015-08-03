from pkg.database.database import createTables

if __name__ == "__main__":
    print("creating tables")
    createTables()
    print("done db reset")
