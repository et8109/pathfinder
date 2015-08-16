import dexml
from dexml import fields

class Enemy(dexml.Model):
    health = fields.Integer()
    audio = fields.String()


class Wolf(Enemy):
    audio = "Chomp.mp3"
