import dexml
from dexml import fields

class Path(dexml.Model):
    up = 0
    down = 1
    left = 2
    right = 3
    #need to get fields.Choice() working
    dirt = fields.Integer()
    dest = fields.Integer()
