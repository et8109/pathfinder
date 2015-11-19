import dexml
from dexml import fields

from pkg.interfaces.common import Npc, Audio

class Man(Npc):
    class meta:
        tagname = "Npc"

    audio= Audio(name="wc.mp3", length=3)
    name="Man"
    #talkAudio = "wc.mp3"
