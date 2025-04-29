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
        SELECT s.* 
        FROM Preferred p
        JOIN Sport s ON p.sport_id = s.sport_id
        WHERE p.player_id = ?
    ");
    $stmt->bind_param("i", $playerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all sports
    $sports = array();
    while ($row = $result->fetch_assoc()) {
        $sports[] = $row;
    }
    
    $stmt->close();
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($sports);
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
