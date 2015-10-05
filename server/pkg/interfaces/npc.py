import dexml
from dexml import fields

from pkg.interfaces.common import Npc

class Man(Npc):
    class meta:
        tagname = "Npc"

    audio= "wc.mp3"
    name="Man"
    #talkAudio = "wc.mp3"
