-- Drop tables if they exist
DROP TABLE IF EXISTS Preferred;
DROP TABLE IF EXISTS Availability;
DROP TABLE IF EXISTS Game;
DROP TABLE IF EXISTS Player;
DROP TABLE IF EXISTS Sport;

-- Create Sport table
CREATE TABLE Sport (
    sport_id INT AUTO_INCREMENT PRIMARY KEY,
    sport_name VARCHAR(50) NOT NULL
);

-- Create Player table
CREATE TABLE Player (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL UNIQUE,
    university_name VARCHAR(100),
    profile_picture VARCHAR(255) DEFAULT NULL
);

-- Create Game table
CREATE TABLE Game (
    game_id INT AUTO_INCREMENT PRIMARY KEY,
    sport_id INT NOT NULL,
    skill_level_required VARCHAR(20) NOT NULL,
    location VARCHAR(100) NOT NULL,
    game_time DATETIME NOT NULL,
    creator_id INT NOT NULL,
    FOREIGN KEY (sport_id) REFERENCES Sport(sport_id),
    FOREIGN KEY (creator_id) REFERENCES Player(player_id)
);

-- Create Availability table
CREATE TABLE Availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    day_of_week VARCHAR(10) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (player_id) REFERENCES Player(player_id)
);

-- Create Preferred table
CREATE TABLE Preferred (
    player_id INT NOT NULL,
    sport_id INT NOT NULL,
    PRIMARY KEY (player_id, sport_id),
    FOREIGN KEY (player_id) REFERENCES Player(player_id),
    FOREIGN KEY (sport_id) REFERENCES Sport(sport_id)
);
