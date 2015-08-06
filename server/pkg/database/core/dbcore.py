#!/usr/bin/python
import pymysql

class DBCore:
    HOST = "localhost"
    USER = "root"
    PASSWD = None
    DATABASE = "ignatymc_pathfinder2"

    def get_conn():
        conn = pymysql.connect(
                self.HOST,
                self.USER,
                self.PASSWD,
                self.DATABASE
                )
        return conn.cursor()

   '''@staticmethod
    def escapeString():
        return string.encode('string-escape')'''

    @staticmethod
    def query(stmt, data, conn, single=False):
        results = conn.execute(stmt, data).fetchall()
        if single and len(results) > 1:
            raise dbException("More than 1 row returned")
        return results

    @staticmethod
    def lastQueryNumRows(conn):
        return conn.rowcount

    @staticmethod
    def resetdb():
        print("clearing db")
        conn = DBCore.get_conn()
        DBCore.query("DROP DATABASE IF EXISTS "+self.DATABASE, conn)
        DBCore.query("CREATE DATABASE "+self.DATABASE, conn)
        DBCore.query("USE "+self.DATABASE, conn)

class dbException(Exception):
    CODE_COULD_NOT_CONNECT = 0
    msg = None

    def __init__(self):
        super().__init__()
