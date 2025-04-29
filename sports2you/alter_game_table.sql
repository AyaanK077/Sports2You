-- Add end_time column to Game table if it doesn't exist
ALTER TABLE Game ADD COLUMN IF NOT EXISTS end_time DATETIME AFTER game_time;

-- If the column already exists but with a different name, you might need to use this instead:
-- ALTER TABLE Game CHANGE game_end_time end_time DATETIME;
