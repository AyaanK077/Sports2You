<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $gameId = $_POST['gameId'];
    $availabilityId = $_POST['availabilityId'];
    
    // Validate input
    if (empty($gameId) || empty($availabilityId)) {
        $response['success'] = false;
        $response['message'] = 'Game ID and Availability ID are required';
    } else {
        // Check if already joined
        $stmt = $conn->prepare("
            SELECT * FROM All_Available 
            WHERE game_id = ? AND availability_id = ?
        ");
        $stmt->bind_param("ii", $gameId, $availabilityId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['success'] = false;
            $response['message'] = 'You have already joined this game with this availability';
        } else {
            // Add player to game
            $stmt = $conn->prepare("
                INSERT INTO All_Available (game_id, availability_id) 
                VALUES (?, ?)
            ");
            $stmt->bind_param("ii", $gameId, $availabilityId);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Successfully joined the game';
            } else {
                $response['success'] = false;
                $response['message'] = 'Failed to join game: ' . $stmt->error;
            }
        }
        
        $stmt->close();
    }
} else {
    // Invalid request method
    $response['success'] = false;
    $response['message'] = 'Invalid request method';
}

// Close database connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
