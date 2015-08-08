from pkg.database.core.dbcore import DBCore
from pkg.database.tables import *

if __name__ == "__main__":
  DBCore.resetdb()
  zones.Zones.create()
  zones.Zones.init()
  players.Players.create()
  players.Players.init()
