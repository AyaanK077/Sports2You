<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'];
    $dayAvailability = $_POST['dayAvailability'];
    $startAvailability = $_POST['startAvailability'];
    $endAvailability = $_POST['endAvailability'];
    
    // Validate input
    if (empty($id) || empty($dayAvailability) || empty($startAvailability) || empty($endAvailability)) {
        $response['success'] = false;
        $response['message'] = 'All fields are required';
    } else if ($startAvailability >= $endAvailability) {
        $response['success'] = false;
        $response['message'] = 'End time must be after start time';
    } else {
        // Update availability
        $stmt = $conn->prepare("
            UPDATE Player_Availability 
            SET day_availability = ?, start_availability = ?, end_availability = ? 
            WHERE availability_id = ?
        ");
        $stmt->bind_param("sssi", $dayAvailability, $startAvailability, $endAvailability, $id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Availability updated successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to update availability: ' . $stmt->error;
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
