import dexml
from dexml import fields

from pkg.interfaces.common import Enemy, Audio

class Wolf(Enemy):
    class meta:
        tagname = "Enemy"

    etype="Wolf"
    attackAudio = Audio(name="Chomp.mp3", length=3)
    deathAudio = Audio(name="ed.mp3", length=3)
    retreatAudio = Audio(name="Birds.mp3", length=3)
    maxHealth = 3
    power = 1
    health = maxHealth
    size = 4
