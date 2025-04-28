from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import SessionLocal
from models import Player, Game, Sport, PlayerAvailability
from pydantic import BaseModel
from typing import List
from datetime import datetime

router = APIRouter()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# -------------------------------
# 1. Register a New Player
# -------------------------------
class PlayerCreate(BaseModel):
    first_name: str
    last_name: str
    age: int
    username: str
    phone_number: str
    password: str
    email: str
    university_name: str = None

@router.post("/players/", response_model=dict)
def create_player(player: PlayerCreate, db: Session = Depends(get_db)):
    # Check if username or email already exists
    existing_user = db.query(Player).filter(
        (Player.username == player.username) | (Player.email == player.email)
    ).first()
    if existing_user:
        raise HTTPException(status_code=400, detail="Username or Email already registered")

    new_player = Player(
        first_name=player.first_name,
        last_name=player.last_name,
        age=player.age,
        username=player.username,
        phone_number=player.phone_number,
        password=player.password,  # NOTE: Ideally, you should hash this password!
        email=player.email,
        university_name=player.university_name
    )
    db.add(new_player)
    db.commit()
    db.refresh(new_player)
    return {"message": "Player created successfully", "player_id": new_player.player_id}

# -------------------------------
# 2. Login Player
# -------------------------------
class PlayerLogin(BaseModel):
    username: str
    password: str

@router.post("/login/", response_model=dict)
def login(player: PlayerLogin, db: Session = Depends(get_db)):
    db_player = db.query(Player).filter(Player.username == player.username).first()
    if not db_player or db_player.password != player.password:
        raise HTTPException(status_code=401, detail="Invalid username or password")
    return {"message": "Login successful", "player_id": db_player.player_id}

# -------------------------------
# 3. View All Games
# -------------------------------
class GameOut(BaseModel):
    game_id: int
    location: str
    game_time: datetime
    skill_level_required: str = None
    sport_id: int
    creator_id: int

    class Config:
        orm_mode = True

@router.get("/games/", response_model=List[GameOut])
def get_games(db: Session = Depends(get_db)):
    games = db.query(Game).all()
    return games

# -------------------------------
# 4. Create a New Game
# -------------------------------
class GameCreate(BaseModel):
    location: str
    game_time: datetime
    skill_level_required: str = None
    sport_id: int
    creator_id: int

@router.post("/games/", response_model=dict)
def create_game(game: GameCreate, db: Session = Depends(get_db)):
    new_game = Game(
        location=game.location,
        game_time=game.game_time,
        skill_level_required=game.skill_level_required,
        sport_id=game.sport_id,
        creator_id=game.creator_id
    )
    db.add(new_game)
    db.commit()
    db.refresh(new_game)
    return {"message": "Game created successfully", "game_id": new_game.game_id}

# -------------------------------
# 5. View Available Players (Player Availability)
# -------------------------------
class PlayerAvailabilityOut(BaseModel):
    availability_id: int
    player_id: int
    day_availability: datetime
    start_availability: datetime
    end_availability: datetime

    class Config:
        orm_mode = True

@router.get("/available_players/", response_model=List[PlayerAvailabilityOut])
def get_available_players(db: Session = Depends(get_db)):
    availabilities = db.query(PlayerAvailability).all()
    return availabilities
