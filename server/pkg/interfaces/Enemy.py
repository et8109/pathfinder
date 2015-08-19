import dexml
from dexml import fields
import pkg.interfaces.Player

class Enemy(dexml.Model):
    health = fields.Integer()
    audio = fields.String()

    def attack(pid):
        Player.fromID(pid).damage(1)

class Wolf(Enemy):
    audio = "Chomp.mp3"
