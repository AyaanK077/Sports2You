from sqlalchemy import Column, Integer, String, ForeignKey, Date, Time, DateTime
from sqlalchemy.orm import relationship
from backend.database import Base

class Player(Base):
    __tablename__ = "Player"
    player_id = Column(Integer, primary_key=True, index=True)
    first_name = Column(String(50), nullable=False)
    last_name = Column(String(50), nullable=False)
    age = Column(Integer, nullable=False)
    username = Column(String(50), unique=True, nullable=False)
    phone_number = Column(String(20), unique=True, nullable=False)
    password = Column(String(255), nullable=False)
    email = Column(String(100), unique=True, nullable=False)
    university_name = Column(String(100))

class Sport(Base):
    __tablename__ = "Sport"
    sport_id = Column(Integer, primary_key=True, index=True)
    sport_name = Column(String(50), nullable=False)
    min_players = Column(Integer, nullable=False)

class PlayerAvailability(Base):
    __tablename__ = "Player_Availability"
    availability_id = Column(Integer, primary_key=True, index=True)
    player_id = Column(Integer, ForeignKey("Player.player_id", ondelete="CASCADE"), nullable=False)
    day_availability = Column(Date, nullable=False)
    start_availability = Column(Time, nullable=False)
    end_availability = Column(Time, nullable=False)

class Game(Base):
    __tablename__ = "Game"
    game_id = Column(Integer, primary_key=True, index=True)
    skill_level_required = Column(String(15))
    location = Column(String(100), nullable=False)
    game_time = Column(DateTime, nullable=False)
    creator_id = Column(Integer, ForeignKey("Player.player_id", ondelete="CASCADE"), nullable=False)
    sport_id = Column(Integer, ForeignKey("Sport.sport_id", ondelete="CASCADE"), nullable=False)

class AllAvailable(Base):
    __tablename__ = "All_Available"
    availability_id = Column(Integer, ForeignKey("Player_Availability.availability_id", ondelete="CASCADE"), primary_key=True)
    game_id = Column(Integer, ForeignKey("Game.game_id", ondelete="CASCADE"), primary_key=True)

class Preferred(Base):
    __tablename__ = "Preferred"
    player_id = Column(Integer, ForeignKey("Player.player_id", ondelete="CASCADE"), primary_key=True)
    sport_id = Column(Integer, ForeignKey("Sport.sport_id", ondelete="CASCADE"), primary_key=True)
