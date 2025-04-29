<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if player ID is provided
if (isset($_GET['playerId'])) {
    $playerId = $_GET['playerId'];
    
    // Prepare SQL statement
    $stmt = $conn->prepare("
        SELECT g.*, s.sport_name 
        FROM Game g
        JOIN Sport s ON g.sport_id = s.sport_id
        WHERE g.creator_id = ?
        ORDER BY g.game_time
    ");
    $stmt->bind_param("i", $playerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all games
    $games = array();
    while ($row = $result->fetch_assoc()) {
        $games[] = $row;
    }
    
    $stmt->close();
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($games);
} else {
    // Player ID not provided
    $response['success'] = false;
    $response['message'] = 'Player ID is required';
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Close database connection
$conn->close();
?>
