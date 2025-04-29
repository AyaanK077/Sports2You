<?php
// Include database connection
require_once 'db_connection.php';

// Get all games with sport and creator info
$sql = "
    SELECT g.*, s.sport_name, p.first_name, p.last_name 
    FROM Game g
    JOIN Sport s ON g.sport_id = s.sport_id
    JOIN Player p ON g.creator_id = p.player_id
    ORDER BY g.game_time
";
$result = $conn->query($sql);

// Fetch all games
$games = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $games[] = $row;
    }
}

// Close database connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($games);
?>
