import xml.etree.ElementTree as ET
from .Path import Path

class Zone():
    XMLdir = "pkg/database/zoneXML/"

    def __init__(self, XMLtree):
        #xml
        self.tree = XMLtree
        self.root = self.tree.getroot()
        #data
        self.zid = self.root.get("zid")
        #children
        self.enemies = {}
        self.paths = []
        for child in self.root:
            if child.tag == 'path':
                self.paths.append(Path(child))
            '''if child.tag == 'enemy':
                self.enemies[child.eid] = Enemy(child)'''

    @staticmethod
    def from_id(zid):
        return Zone(ET.parse(Zone.XMLdir+str(zid)+".xml"))

    @staticmethod
    def reset(zid, up, down, left, right):
        root = ET.Element('zone', {
            'zid': str(zid)
            })
        if up != None:
            root.append(Path.create('up',up))
        if down != None:
            root.append(Path.create('down',down))
        if right != None:
            root.append(Path.create('right',right)) 
        if left != None:
            root.append(Path.create('left',left))
        tree = ET.ElementTree(root)
        #print(ET.tostring(tree))
        tree.write(Zone.XMLdir+str(zid)+".xml")

    #called when a player enters the scene
    def onEnter(self, player):
        Overseer.send_data(
                "Chomp.mp3", 
                player.pid)
        '''from pkg.Overseer import Overseer
        for aud in Ambients_table.get_in_zone(self.zid):
            Overseer.send_data(
                aud["name"], 
                player.pid)'''
