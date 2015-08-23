import dexml
from dexml import fields

from pkg.interfaces.shared import Fightable

class Npc(dexml.Model):
    audio = fields.String()
