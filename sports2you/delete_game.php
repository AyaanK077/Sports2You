<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $gameId = $_POST['gameId'];
    
    // Validate input
    if (empty($gameId)) {
        $response['success'] = false;
        $response['message'] = 'Game ID is required';
    } else {
        // Delete game
        $stmt = $conn->prepare("DELETE FROM Game WHERE game_id = ?");
        $stmt->bind_param("i", $gameId);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Game deleted successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete game: ' . $stmt->error;
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
