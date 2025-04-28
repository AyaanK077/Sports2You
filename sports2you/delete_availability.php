<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'];
    
    // Validate input
    if (empty($id)) {
        $response['success'] = false;
        $response['message'] = 'Availability ID is required';
    } else {
        // Delete availability
        $stmt = $conn->prepare("DELETE FROM Player_Availability WHERE availability_id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Availability deleted successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete availability: ' . $stmt->error;
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
