from .core.dbcore import DBCore
from .tables import *

if __name__ == "__main__":
  DBCore.resetdb()
  for t in dir(tables):
    t.create()
    t.init()
