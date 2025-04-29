<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

// Set page title
$pageTitle = "Login - Sports2You";
include 'includes/header.php';

// Initialize variables
$error = "";
$success = "";

// Check for registration success message
if (isset($_GET['registered']) && $_GET['registered'] == 'true') {
    $success = "Registration successful! Please log in.";
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database connection
    require_once 'includes/db_connection.php';
    
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
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
                // Password is correct, create session
                $_SESSION['user'] = $user;
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Password is incorrect
                $error = "Invalid username or password";
            }
        } else {
            // User not found
            $error = "Invalid username or password";
        }
        
        $stmt->close();
    }
    
    // Close database connection
    $conn->close();
}
?>

<main class="container">
    <div class="form-container">
        <h2 class="form-title">Sign in to your account</h2>
        <p class="text-center">Or <a href="signup.php">create a new account</a></p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Sign in</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
