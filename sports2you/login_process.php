<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $response['success'] = false;
        $response['message'] = 'Username and password are required';
    } else {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM Player WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (in a real app, you would use password_verify with hashed passwords)
            if ($password === $user['password']) {
                // Password is correct
                $response['success'] = true;
                $response['message'] = 'Login successful';
                
                // Remove password from user data before sending to client
                unset($user['password']);
                $response['user'] = $user;
            } else {
                // Password is incorrect
                $response['success'] = false;
                $response['message'] = 'Invalid username or password';
            }
        } else {
            // User not found
            $response['success'] = false;
            $response['message'] = 'Invalid username or password';
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
