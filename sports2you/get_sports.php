<?php
// Include database connection
require_once 'db_connection.php';

// Get all sports
$sql = "SELECT * FROM Sport ORDER BY sport_name";
$result = $conn->query($sql);

// Fetch all sports
$sports = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sports[] = $row;
    }
}

// Close database connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($sports);
?>
