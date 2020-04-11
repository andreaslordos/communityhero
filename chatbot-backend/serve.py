from flask import Flask, render_template
from flask_socketio import SocketIO
import requests
import json
import urllib.parse

BASE_URL = 'http://localhost:8000/api/v1/'

app = Flask(__name__)
app.config['SECRET_KEY'] = 'tHiSiSuSiNgNlPoFcOuRsE#'
socketio = SocketIO(app)
SESSION = {}

# ================ Sentiment Analysis ===================
from flair.models import TextClassifier
from flair.data import Sentence
classifier = TextClassifier.load('en-sentiment')


@app.route('/')
def sessions():
	return render_template('index.html')

def messageReceived(methods=['GET', 'POST']):
	print('message was received!!!')

@socketio.on('my event')
def handle_my_custom_event(json, methods=['GET', 'POST']):
	print('received my event: ' + str(json))

	if not "user_name" in json:
		return

	if json['user_name'] in SESSION:
		print(SESSION)
		if 'state' in SESSION[json['user_name']]:
			print('state found')
			state = SESSION[json['user_name']]['state']
			json['message'] = eval(state + '("'+json['user_name'] + '", "' + json['message'] + '")')
	else:
		json['message'] = new_session(json['user_name'])
		# TODO: cacheMessage(json['message'])


	json['user_name'] = 'bot'
	socketio.emit('my response', json, callback=messageReceived)

def new_session(user):
	SESSION[user] = {}
	r = requests.get(BASE_URL + 'user/' + user)
	print(r.status_code)
	if r.status_code==404:
		SESSION[user]['state'] = 'get_address'
		print(SESSION)
		return "Woah, you're new here! Welcome! Can you send me your address so that I can finish registering you? (I'm not that smart, so please don't include anything else in your text :D )"
	return "Welcome back, " + r.content

def get_address(user, message):
	r = requests.get('https://nominatim.openstreetmap.org/search.php?format=json&q=' + urllib.parse.quote(message)).content[1:-1]
	print(r)
	r = json.loads(r)
	SESSION[user]['state'] = 'confirm_location'
	return "So you're telling me you live here? https://www.openstreetmap.org/?mlat=" + r['lat'] + "&mlon=" + r['lon']+ " \nIt's not that I don't know, just checking if you know ;)"

def confirm_location(user, message):
	sent = Sentence(message)
	classifier.predict(sent)
	print(sent.labels)
	return "as you wish"

if __name__ == '__main__':
	socketio.run(app, debug=True)