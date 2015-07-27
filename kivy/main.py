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

Builder.load_file('./main.kv')

class LoginScreen(Screen):
    username = None
    pass

class MainScreen(Screen):
    username = 'originaluname'
    connection = None
    fromx = None
    fromy = None

    def on_enter(self):
        self.connect_to_server()
        self.print_message("Welcome, "+self.username)
        
    def connect_to_server(self):
        reactor.connectTCP('localhost', 10000, EchoFactory(self))

    def on_connection(self, connection):
        self.print_message("connected succesfully!")
        self.connection = connection

    def send_message(self, msg):
        if msg and self.connection:
            self.connection.write(str(msg))

    def print_message(self, msg):
        self.ids.message.text += msg + "\n"

    def on_touch_down(self, touch):
        self.fromx = touch.x
        self.fromy = touch.y
        self.print_message("touched")

    def on_touch_up(self, touch):
        xdiff = self.fromx-touch.x
        ydiff = self.fromy-touch.y
        if(-5 < xdiff < 5):
            if(ydiff > 10):
                self.print_message("down")
            elif(ydiff < -10):
                self.print_message("up")
        elif(-5 < ydiff < 5):
            if(xdiff > 10):
                self.print_message("left")
            elif(xdiff < -10):
                self.print_message("right")
 
class EchoClient(protocol.Protocol):
    def connectionMade(self):
        self.factory.app.on_connection(self.transport)

    def dataReceived(self, data):
        self.factory.app.print_message(data)

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
