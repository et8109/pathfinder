#install_twisted_rector must be called before importing the reactor
from kivy.support import install_twisted_reactor
install_twisted_reactor()

from kivy.app import App
from kivy.lang import Builder
from kivy.uix.screenmanager import ScreenManager, Screen
from kivy.uix.gridlayout import GridLayout
from kivy.uix.label import Label
from kivy.uix.textinput import TextInput
from kivy.uix.button import Button

from twisted.internet import reactor, protocol

import json

Builder.load_file('./main.kv')

connection = None
disconnect_pointer = None

def send_message(msg):
     if msg and connection:
        connection.write(str(msg))

class LoginScreen(Screen):

    def on_enter(self):
        global connection
        if connection is None:
            print("no connection, establishing")
            self.connect()

    def connect(self):
        self.print_message("connecting to server")
        disconnect_pointer = reactor.connectTCP('localhost', 10000, EchoFactory(self))

    def on_connection(self, _connection):
        global connection
        self.print_message("connected succesfully!")
        connection = _connection
        print(connection)

    def login(self, uname, password):
        msg = {"u":uname,
               "p":password}
        send_message(json.dumps(msg))

    def get_message(self, msg):
        self.print_message(msg)

    def print_message(self, msg):
        self.ids.message.text += msg + "\n"
        
    
class MainScreen(Screen):
    fromx = None
    fromy = None

    def disconnect(self):
        if disconnect_pointer:
            disconnect_pointer.disconnect()

    def on_enter(self):
        self.reset_message()
        self.print_message("Welcome, "+username)

    def reset_message(self):
        self.ids.message.text =""

    def get_message(self, msg):
        self.print_message(msg)

    def print_message(self, msg):
        self.ids.message.text += msg + "\n"

    def on_touch_down(self, touch):
        if touch.x < 20 and touch.y < 20:
            self.disconnect()
            sm.current = "Login"
        else:
            self.fromx = touch.x
            self.fromy = touch.y

    def on_touch_up(self, touch):
        xdiff = self.fromx-touch.x
        ydiff = self.fromy-touch.y
        walkdir = None
        maxVar = 30
        minDist = 50
        if(-maxVar < xdiff < maxVar):
            if(ydiff > minDist):
                walkdir ="down"
            elif(ydiff < -minDist):
                walkdir ="up"
        elif(-maxVar < ydiff < maxVar):
            if(xdiff > minDist):
                walkdir = "left"
            elif(xdiff < -minDist):
                walkdir = "right"
        if walkdir:
            self.send_message(walkdir)
 
class EchoClient(protocol.Protocol):
    def connectionMade(self):
        self.factory.app.on_connection(self.transport)

    def dataReceived(self, data):
        self.factory.app.get_message(data)

class EchoFactory(protocol.ClientFactory):
    protocol = EchoClient
    def __init__(self, app):
        self.app = app

    def clientConnectionLost(self, conn, reason):
        self.app.print_message("connection lost")

    def clientConnectionFailed(self, conn, reason):
        self.app.print_message("connection failed")


sm = ScreenManager()
sm.add_widget(LoginScreen(name='Login'))
sm.add_widget(MainScreen(name='Main'))

class MyApp(App):

    def build(self):
        return sm

if __name__ == '__main__':
    MyApp().run()
