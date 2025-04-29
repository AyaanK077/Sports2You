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
        SELECT * FROM Player_Availability 
        WHERE player_id = ? 
        ORDER BY day_availability, start_availability
    ");
    $stmt->bind_param("i", $playerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch all availabilities
    $availabilities = array();
    while ($row = $result->fetch_assoc()) {
        $availabilities[] = $row;
    }
    
    $stmt->close();
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($availabilities);
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
