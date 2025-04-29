<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

// Set page title
$pageTitle = "Sign Up - Sports2You";
include 'includes/header.php';

// Include database connection
require_once 'includes/db_connection.php';

// Initialize variables
$error = "";
$success = "";

// Process signup form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $universityName = "University of Texas at Dallas"; // Fixed to UTD
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($firstName) || empty($lastName) || empty($age) || empty($email) || empty($phoneNumber)) {
        $error = "All required fields must be filled";
    } else if ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } else if ($age < 18) {
        $error = "You must be at least 18 years old";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM Player WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT * FROM Player WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Email already exists";
            } else {
                // Check if phone number already exists
                $stmt = $conn->prepare("SELECT * FROM Player WHERE phone_number = ?");
                $stmt->bind_param("s", $phoneNumber);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error = "Phone number already exists";
                } else {
                    // Create new user
                    $stmt = $conn->prepare("INSERT INTO Player (username, password, first_name, last_name, age, email, phone_number, university_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssisss", $username, $password, $firstName, $lastName, $age, $email, $phoneNumber, $universityName);
                    
                    if ($stmt->execute()) {
                        $success = "Account created successfully. Please log in.";
                        
                        // Redirect to login page after 2 seconds
                        header("refresh:2;url=login.php");
                    } else {
                        $error = "Failed to create account: " . $stmt->error;
                    }
                }
            }
        }
        
        $stmt->close();
    }
}

// Close database connection
$conn->close();
?>

<main class="container">
    <h1 class="page-title">Create an Account</h1>

    <div class="auth-container">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="signup.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" min="18" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="phoneNumber">Phone Number (XXX-XXX-XXXX)</label>
                <input type="tel" id="phoneNumber" name="phoneNumber" pattern="\d{3}-\d{3}-\d{4}" class="form-control" placeholder="123-456-7890" required>
            </div>
            
            <div class="form-group">
                <label for="universityName">University</label>
                <input type="text" id="universityName" name="universityName" class="form-control" value="University of Texas at Dallas" readonly>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" minlength="6" class="form-control" required>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </div>
            
            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
