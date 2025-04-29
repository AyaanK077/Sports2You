<?php
// Include database connection
require_once 'db_connection.php';

// Initialize response array
$response = array();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $universityName = $_POST['universityName'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($age) || empty($username) || 
        empty($email) || empty($phoneNumber) || empty($password)) {
        $response['success'] = false;
        $response['message'] = 'All required fields must be filled';
    } else if ($age < 18) {
        $response['success'] = false;
        $response['message'] = 'You must be at least 18 years old';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM Player WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['success'] = false;
            $response['message'] = 'Username already exists';
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT * FROM Player WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $response['success'] = false;
                $response['message'] = 'Email already exists';
            } else {
                // Check if phone number already exists
                $stmt = $conn->prepare("SELECT * FROM Player WHERE phone_number = ?");
                $stmt->bind_param("s", $phoneNumber);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $response['success'] = false;
                    $response['message'] = 'Phone number already exists';
                } else {
                    // Insert new user
                    $stmt = $conn->prepare("INSERT INTO Player (first_name, last_name, age, username, phone_number, password, email, university_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssisssss", $firstName, $lastName, $age, $username, $phoneNumber, $password, $email, $universityName);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'Registration successful';
                        $response['player_id'] = $conn->insert_id;
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Registration failed: ' . $stmt->error;
                    }
                }
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
