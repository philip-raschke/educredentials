from flask import Flask, jsonify
import threading

app = Flask(__name__)
invitation_data_store = {}  # Ein einfacher Store für die Einladungsdaten




@app.route('/')
def get_invitation():
    invitation_data = jsonify(invitation_data_store.get('invitation', {}))
    return invitation_data

def store_invitation_data(data):
    invitation_data_store['invitation'] = data

def run_api_server():
    app.run(host='localhost', port=5001)
    


def start_api_server(invitation_data):
    store_invitation_data(invitation_data)
    threading.Thread(target=run_api_server).start()

# Wenn diese Datei direkt ausgeführt wird, starten Sie die API im Hauptthread
if __name__ == '__main__':
    threading.Thread(target=run_api_server).start()
