from sqlalchemy import create_engine, Column, Integer, String
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

HOST="localhost"
USER="root"
PASS= None
DATABASE="ignatymc_pathfinder2"
PORT=10000

engine = create_engine("mysql+mysqldb://%s:%s@%s[:%s]/%s" % (USER, PASS, HOST, DATABASE, PORT), echo=True) #db info
Session = sessionmaker(bind=engine) #db sessions factory
Base = declarative_base() #base class for table classes

class Zone(Base):
    __tablename__ = 'zones'
    id = Column(Integer(3), Sequence('zone_id_seq'), primary_key=True)
    #position in the world
    x = Column(Integer(3))
    y = Column(Integer(3))
    z = Column(Integer(3))
    #paths to other zones
    up = Column(Integer(3), ForeignKey("Zone.id"))
    down = Column(Integer(3), ForeignKey("Zone.id"))
    left = Column(Integer(3), ForeignKey("Zone.id"))
    right = Column(Integer(3), ForeignKey("Zone.id"))


class Player(Base):
    __tablename__ = 'players'
    id = Column(Integer(3), Sequence('player_id_seq'), primary_key=True)
    name = Column(String(20))
    password = Column(String(20))
    zone_id = Column(Integer(3), ForeignKey("Zone.id"))
    health = Column(Integer(3))

    def login(self, name, password):
        return session.query(Player.id).filter(Player.name=name, Player.password=password).one()

    @classmethod
    def getAll(klass):
        return session.query(User.id, User.name).all()

class Npc(Base):
    __tablename__ = 'npcs'
    prefix='n'
    id = Column(Integer(3), Sequence('npc_id_seq'), primary_key=True)
    zone_id = Column(Integer(3), ForeignKey("Zone.id"))

class EnemyType(Base):
    __tablename__ = 'enemyTypes'
    prefix='e'
    id = Column(Integer(3), Sequence('enemytype_id_seq'), primary_key=True)
    audio = Column(String(30))

class Enemy(Base):
    __tablename__ = 'enemies'
    id = Column(Integer(3), Sequence('enemy_id_seq'), primary_key=True)
    type = Column(Integer(3), ForeignKey("EnemyType.id"))
    zone_id = Column(Integer(3), ForeignKey("Zone.id"))
    health = Column(Integer(3))

class Ambient(Base):
    prefix='a'
    __tablename__ = 'ambients'
    id = Column(Integer(3), Sequence('ambient_id_seq'), primary_key=True)
    zone_id = Column(Integer(3), ForeignKey("Zone.id"))

class Audio(Base):
    __tablename__ = 'audios'
    prefix = Column(String(1), primary_key=True)
    obj_id = Column(Integer(3), primary_key=True)
    id = Column(Integer(3), primary_key=True)


def createTables():
    Base.metadata.create_all(engine) #create tables
    session = Session() #create new session
    #add rows
    session.add_all([
        User(name='guest', password='guest'),
        User(name='guest1', password='guest1'),
        User(name='guest2', password='guest2')])
    session.commit()
