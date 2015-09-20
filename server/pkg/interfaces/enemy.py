import dexml
from dexml import fields

from pkg.interfaces.common import Enemy

class Wolf(Enemy):
    class meta:
        tagname = "Enemy"

    attackAudio = "Chomp.mp3"
    deathAudio = "ed.mp3"
    retreatAudio = "Birds.mp3"
    maxHealth = 3
    power = 1
    health = maxHealth
