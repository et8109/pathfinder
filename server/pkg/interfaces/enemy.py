import dexml
from dexml import fields

from pkg.interfaces.common import Enemy

class Wolf(Enemy):
    attackAudio = "Chomp.mp3"
    maxHealth = 3
    power = 1
    health = maxHealth
