#!/usr/bin/python
import pymysql

class DBCore:
    HOST = "localhost"
    USER = "root"
    PASSWD = None
    DATABASE = "ignatymc_pathfinder2"

    def get_conn():
        conn = pymysql.connect(
                DBCore.HOST,
                DBCore.USER,
                DBCore.PASSWD,
                DBCore.DATABASE
                )
        return conn.cursor()

    @staticmethod
    def query(stmt, data, conn, single=False):
        #print(stmt+" >> "+str(data))
        numrows = conn.execute(stmt, data)
        #print("numrows: "+str(numrows))
        if single and numrows > 1:
            raise dbException("Too many rows for sql single query: "+numrows)
        conn.connection.commit()
        try:
            if single: 
                return conn.fetchone() 
            else: 
                return conn.fetchall()
        except Exception:
            return None

    @staticmethod
    def lastQueryNumRows(conn):
        return conn.rowcount

    @staticmethod
    def resetdb():
        print("clearing db")
        conn = DBCore.get_conn()
        #DBCore.query("DROP DATABASE IF EXISTS "+DBCore.DATABASE,None, conn)
        #DBCore.query("CREATE DATABASE "+DBCore.DATABASE,None, conn)
        #DBCore.query("USE "+DBCore.DATABASE,None, conn)

class dbException(Exception):
    CODE_COULD_NOT_CONNECT = 0
    msg = None

    def __init__(self):
        super().__init__()
