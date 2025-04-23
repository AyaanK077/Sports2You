from flask import Flask, send_from_directory

app = Flask(__name__, static_folder='src')

@app.route('/')
def homepage():
    return send_from_directory('src', 'HomePage.html')

@app.route('/<path:filename>')
def serve_static(filename):
    return send_from_directory('src', filename)

if __name__ == '__main__':
    app.run(debug=True)
