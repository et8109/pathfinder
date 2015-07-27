#install_twisted_rector must be called before importing the reactor
from kivy.support import install_twisted_reactor
install_twisted_reactor()

from kivy.app import App
from kivy.uix.gridlayout import GridLayout
from kivy.uix.label import Label
from kivy.uix.textinput import TextInput
from kivy.uix.button import Button

from twisted.internet import reactor, protocol

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





class LoginScreen(GridLayout):
    connection = None

    def Login(self, instance):
        self.message.text = "logging in"
        self.send_message("loginuserpass")

    def __init__(self, **kwargs):
        super(LoginScreen, self).__init__(**kwargs)
        self.cols = 2
        self.add_widget(Label(text='User Name'))
        self.username = TextInput(multiline=False)
        self.add_widget(self.username)
        self.add_widget(Label(text='password'))
        self.password = TextInput(password=True, multiline=False)
        self.add_widget(self.password)
        self.submit = Button(text='Login', font_size=14)
        self.submit.bind(on_press=self.Login)
        self.add_widget(self.submit)
        self.message = Label(text='...')
        self.add_widget(self.message)
        #make connection
        self.connect_to_server()
        
    def connect_to_server(self):
        reactor.connectTCP('localhost', 10000, EchoFactory(self))

    def on_connection(self, connection):
        self.print_message("connected succesfully!")
        self.connection = connection

    def send_message(self, msg):
        if msg and self.connection:
            self.connection.write(str(msg))

    def print_message(self, msg):
        self.message.text += msg + "\n"

class MyApp(App):

    def build(self):
        return LoginScreen()
        #return Label(text='Welcome')

if __name__ == '__main__':
    MyApp().run()
