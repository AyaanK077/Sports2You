<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if game ID is provided
if (isset($_GET['gameId'])) {
    $gameId = $_GET['gameId'];
    
    // Get game details
    $stmt = $conn->prepare("
        SELECT g.*, s.sport_name, p.first_name, p.last_name 
        FROM Game g
        JOIN Sport s ON g.sport_id = s.sport_id
        JOIN Player p ON g.creator_id = p.player_id
        WHERE g.game_id = ?
    ");
    $stmt->bind_param("i", $gameId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['error'] = 'Game not found';
    } else {
        $game = $result->fetch_assoc();
        $response['game'] = $game;
        
        // Get players for this game
        $stmt = $conn->prepare("
            SELECT p.* 
            FROM All_Available aa
            JOIN Player_Availability pa ON aa.availability_id = pa.availability_id
            JOIN Player p ON pa.player_id = p.player_id
            WHERE aa.game_id = ?
        ");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $players = array();
        while ($row = $result->fetch_assoc()) {
            // Remove password from player data
            unset($row['password']);
            $players[] = $row;
        }
        
        $response['players'] = $players;
    }
    
    $stmt->close();
} else {
    $response['error'] = 'Game ID is required';
}

// Close database connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
