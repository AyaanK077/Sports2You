<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $playerId = $_POST['playerId'];
    $dayAvailability = $_POST['dayAvailability'];
    $startAvailability = $_POST['startAvailability'];
    $endAvailability = $_POST['endAvailability'];
    
    // Validate input
    if (empty($playerId) || empty($dayAvailability) || empty($startAvailability) || empty($endAvailability)) {
        $response['success'] = false;
        $response['message'] = 'All fields are required';
    } else if ($startAvailability >= $endAvailability) {
        $response['success'] = false;
        $response['message'] = 'End time must be after start time';
    } else {
        // Insert new availability
        $stmt = $conn->prepare("
            INSERT INTO Player_Availability (player_id, day_availability, start_availability, end_availability) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $playerId, $dayAvailability, $startAvailability, $endAvailability);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Availability added successfully';
            $response['availability_id'] = $conn->insert_id;
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to add availability: ' . $stmt->error;
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
