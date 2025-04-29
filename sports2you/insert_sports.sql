-- Make sure the Sport table exists
CREATE TABLE IF NOT EXISTS Sport (
    sport_id INT AUTO_INCREMENT PRIMARY KEY,
    sport_name VARCHAR(50) NOT NULL
);

-- Clear existing sports to avoid duplicates
DELETE FROM Sport;

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
