import xml.etree.ElementTree as ET

class Path:
    def __init__(self, XMLroot):
        self.root = XMLroot
        self.direction = self.root.find("dir")
        self.dest = self.root.find("dest")

    def toXML(self):
        return self.root

    @staticmethod
    def create(direction, dest):
        root = ET.Element('path')
        ET.SubElement(root,'dir').text = str(direction)
        ET.SubElement(root,'dest').text = str(dest)
        return root
