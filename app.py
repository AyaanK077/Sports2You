from sqlalchemy.orm import sessionmaker
from backend.models import Base, Player, PlayerAvailability, Game
from backend.database import engine
from datetime import datetime
from flask import Flask, render_template, request, redirect, session
import os

app = Flask(__name__)
app.secret_key = 'supersecretkey'  # Needed for sessions

SessionLocal = sessionmaker(bind=engine)
Base.metadata.create_all(bind=engine)

# --------- Serve Home Page ---------
@app.route('/')
def homepage():
    return render_template('HomePage.html')

# --------- Sign Up Route ---------------
@app.route('/signup', methods=['GET', 'POST'])
def signup():
    db = SessionLocal()
    if request.method == 'POST':
        try:
            first_name = request.form.get('first_name')
            last_name = "N/A"  # since your form doesn't ask
            age = int(request.form.get('age'))
            username = request.form.get('username')
            phone_number = request.form.get('phone_number')
            password = request.form.get('password')
            email = request.form.get('email')
            university_name = request.form.get('university_name')

            # Create player
            new_player = Player(
                first_name=first_name,
                last_name=last_name,
                age=age,
                username=username,
                phone_number=phone_number,
                password=password,
                email=email,
                university_name=university_name
            )
            db.add(new_player)
            db.commit()
            return redirect('/login')  # After signup, redirect to login page
        except Exception as e:
            return f"Error during sign up: {e}"
        finally:
            db.close()
    else:
        return render_template('SignUp.html')  # If user visits signup page

# --------- Login Route ---------------
@app.route('/login', methods=['GET', 'POST'])
def login():
    db = SessionLocal()
    if request.method == 'POST':
        try:
            username = request.form.get('username')
            password = request.form.get('password')

            player = db.query(Player).filter(Player.username == username).first()

            if player and player.password == password:
                session['player_id'] = player.player_id
                session['username'] = player.username
                return redirect('/dashboard')
            else:
                return "Invalid login. Try again."
        except Exception as e:
            return f"Error during login: {e}"
        finally:
            db.close()
    else:
        return render_template('Login.html')  # If user visits login page

# --------- Dashboard Page ---------------
@app.route('/dashboard')
def dashboard():
    if 'player_id' not in session:
        return redirect('/login')
    return render_template('Dashboard.html', username=session.get('username'))

# --------- Create Game Route ---------------
@app.route('/create_game', methods=['POST'])
def create_game():
    db = SessionLocal()
    try:
        location = request.form.get('location')
        game_time_str = request.form.get('game_time')
        game_time = datetime.strptime(game_time_str, '%Y-%m-%dT%H:%M')
        sport_id = 1  # For now (later: map sport name -> sport_id)
        creator_id = session.get('player_id')  # Use the logged-in user's ID

        new_game = Game(
            location=location,
            game_time=game_time,
            sport_id=sport_id,
            creator_id=creator_id
        )
        db.add(new_game)
        db.commit()
        return redirect('/dashboard')
    except Exception as e:
        return f"Error creating game: {e}"
    finally:
        db.close()

# --------- Set Availability Route ---------------
@app.route('/set_availability', methods=['POST'])
def set_availability():
    db = SessionLocal()
    try:
        day = request.form.get('day')
        start = request.form.get('start')
        end = request.form.get('end')

        player_id = session.get('player_id')

        if not player_id:
            return redirect('/login')

        new_availability = PlayerAvailability(
            player_id=player_id,
            day_availability=datetime.strptime(day, '%Y-%m-%d').date(),
            start_availability=datetime.strptime(start, '%H:%M').time(),
            end_availability=datetime.strptime(end, '%H:%M').time()
        )
        db.add(new_availability)
        db.commit()
        return redirect('/dashboard')
    except Exception as e:
        return f"Error setting availability: {e}"
    finally:
        db.close()

# --------- Join Game View Page ---------------
@app.route('/join_game')
def join_game():
    db = SessionLocal()
    try:
        games = db.query(Game).all()
        return render_template('JoinGame.html', games=games)
    finally:
        db.close()

# --------- Join a Specific Game ---------------
@app.route('/join_specific_game', methods=['POST'])
def join_specific_game():
    game_id = request.form.get('game_id')
    player_id = session.get('player_id')

    if not player_id:
        return redirect('/login')

    # Later: Save player joining game logic here
    return f"Player {player_id} joined Game {game_id} successfully!"

# --------- Logout Route ---------------
@app.route('/logout')
def logout():
    session.clear()
    return redirect('/')

if __name__ == '__main__':
    app.run(debug=True)
