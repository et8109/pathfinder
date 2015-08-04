from sqlalchemy import create_engine, Column, Integer, String, Sequence, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, relationship, backref, exc

requires = ["mysql-python"]

HOST="localhost"
USER="root"
PASS= None #"D0_N07_$H4R3"
DATABASE="ignatymc_pathfinder2"
PORT=10000

#engine = create_engine("mysql+mysqldb://%s:%s@%s[:%s]/%s" % (USER, PASS, HOST, DATABASE, PORT), echo=True) #db info
engine = create_engine("mysql+mysqlconnector://%s@%s/%s" % (USER, HOST, DATABASE), echo=True) #db info

Session = sessionmaker(bind=engine) #db sessions factory
Base = declarative_base() #base class for table classes
session = Session()

class Zone_table(Base):
    __tablename__ = 'zones'
    zone_id = Column(Integer(3), Sequence('zone_id_seq'), primary_key=True)
    #position in the world
    x = Column(Integer(3))
    y = Column(Integer(3))
    z = Column(Integer(3))
    #paths to other zones
    up = Column(Integer(3), ForeignKey("zones.zone_id"))
    down = Column(Integer(3), ForeignKey("zones.zone_id"))
    left = Column(Integer(3), ForeignKey("zones.zone_id"))
    right = Column(Integer(3), ForeignKey("zones.zone_id"))


class Player_table(Base):
    __tablename__ = 'players'
    player_id = Column(Integer(3), Sequence('player_id_seq'), primary_key=True)
    name = Column(String(20))
    password = Column(String(20))
    zone_id = Column(Integer(3), ForeignKey("zones.zone_id"))
    health = Column(Integer(3))
    location = relationship("Zone_table", backref="Player_table") #one to many

    def moveUp(self):
        self.zone_id = self.zone.up
        return self.zone_id

    def moveDown(self):
        self.zone_id = self.zone.down
        return self.zone_id

    def moveLeft(self):
        self.zone_id = self.zone.left
        return self.zone_id

    def moveRight(self):
        self.zone_id = self.zone.right
        return self.zone_id

    @classmethod
    def check_login(klass, _name, _password):
        try:
            return session.query(Player_table.player_id).filter_by(name=_name).filter_by(password=_password).one()
        except exc.NoResultFound:
            return None

    @classmethod
    def getAll(klass):
        return session.query(User.player_id, User.name).all()

class Npc_table(Base):
    __tablename__ = 'npcs'
    prefix='n'
    npc_id = Column(Integer(3), Sequence('npc_id_seq'), primary_key=True)
    zone_id = Column(Integer(3), ForeignKey("zones.zone_id"))

class EnemyType_table(Base):
    __tablename__ = 'enemyTypes'
    prefix='e'
    type_id = Column(Integer(3), Sequence('enemytype_id_seq'), primary_key=True)
    audio = Column(String(30))

class Enemy_table(Base):
    __tablename__ = 'enemies'
    enemy_id = Column(Integer(3), Sequence('enemy_id_seq'), primary_key=True)
    type_id = Column(Integer(3), ForeignKey("enemyTypes.type_id"))
    zone_id = Column(Integer(3), ForeignKey("zones.zone_id"))
    health = Column(Integer(3))

class Ambient_table(Base):
    prefix='a'
    __tablename__ = 'ambients'
    ambient_id = Column(Integer(3), Sequence('ambient_id_seq'), primary_key=True)
    zone_id = Column(Integer(3), ForeignKey("zones.zone_id"))

class Audio_table(Base):
    __tablename__ = 'audios'
    #prefix = Column(String(1))
    #obj_id = Column(Integer(3), primary_key=True)
    audio_id = Column(String(3), primary_key=True)


def createTables():
    Base.metadata.create_all(engine) #create tables
    session = Session() #create new session
    #add rows
    session.add_all([
        Player_table(name='guest', password='guest'),
        Player_table(name='guest1', password='guest1'),
        Player_table(name='guest2', password='guest2')])
    session.commit()

def commitDatabase():
    session.commit()
