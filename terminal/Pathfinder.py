import socket
import sys

print ("hello")
username = input('Username: ')
password = input('Password: ')

print("creating socket")
# Create a TCP/IP socket
sock = socket.create_connection(('localhost', 10000))

try:
    # Send data
    message = 'this is the message.'
    sock.sendall(message.encode())
    
    while True:
        data = sock.recv(1024)
        print('received "%s"' % data.decode())

finally:
    print("closing socket")
    sock.close()
