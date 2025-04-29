-- Insert sports
INSERT INTO Sport (sport_name) VALUES 
('Soccer'),
('Basketball'),
('Football'),
('Volleyball'),
('Tennis'),
('Spikeball'),
('Pickleball'),
('Badminton');

-- Insert sample players
INSERT INTO Player (username, password, first_name, last_name, age, email, phone_number, university_name) VALUES
('john_doe', 'password123', 'John', 'Doe', 21, 'john.doe@example.com', '123-456-7890', 'University of Texas at Dallas'),
('jane_smith', 'password456', 'Jane', 'Smith', 22, 'jane.smith@example.com', '234-567-8901', 'University of Texas at Dallas');

-- Insert sample games
INSERT INTO Game (sport_id, skill_level_required, location, game_time, creator_id) VALUES
(1, 'Intermediate', 'Soccer Fields', '2023-12-15 14:00:00', 1),
(2, 'Beginner', 'Activity Center Indoor Courts', '2023-12-16 16:00:00', 2);

-- Insert sample availabilities
INSERT INTO Availability (player_id, day_of_week, start_time, end_time) VALUES
(1, 'Monday', '14:00:00', '18:00:00'),
(1, 'Wednesday', '16:00:00', '20:00:00'),
(2, 'Tuesday', '15:00:00', '19:00:00'),
(2, 'Thursday', '17:00:00', '21:00:00');

-- Insert sample preferred sports
INSERT INTO Preferred (player_id, sport_id) VALUES
(1, 1),
(1, 3),
(2, 2),
(2, 4);
