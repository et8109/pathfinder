#!/usr/bin/python
import pymysql

class DBCore:
    HOST = "localhost"
    USER = "root"
    PASSWD = None
    DATABASE = "ignatymc_pathfinder2"

    def __init__(self):
        self.con = pymysql.connect(
                self.HOST,
                self.USER,
                self.PASSWD,
                self.DATABASE
                )
        self.cursor = self.con.cursor()

    def escapeString(self, string):
        return string.encode('string-escape')

    def _query(self, sql):
        return self.cursor.execute(sql)

    def query(self, sql):
        self._query(sql)
        return self.cursor.fetchall()

    def lastQueryNumRows(self):
        return cursor.rowcount

    def resetdb(self):
        print("clearing db")
        db = DBCore()
        db.query("DROP DATABASE IF EXISTS "+self.DATABASE)
        db.query("CREATE DATABASE "+self.DATABASE)
        db.query("USE "+self.DATABASE)

class dbException(Exception):
    CODE_COULD_NOT_CONNECT = 0
    msg = None

    def __init__(self):
        super().__init__()
