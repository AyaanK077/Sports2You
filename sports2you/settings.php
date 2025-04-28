<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user = $_SESSION['user'];

// Set page title
$pageTitle = "Settings - Sports2You";
include 'includes/header.php';

// Include database connection
require_once 'includes/db_connection.php';

// Get active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

// Initialize variables
$error = "";
$success = "";

// Process profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $universityName = "University of Texas at Dallas"; // Fixed to UTD
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($age) || empty($email) || empty($phoneNumber)) {
        $error = "All required fields must be filled";
    } else if ($age < 18) {
        $error = "You must be at least 18 years old";
    } else {
        // Check if email already exists (excluding current user)
        $stmt = $conn->prepare("SELECT * FROM Player WHERE email = ? AND player_id != ?");
        $stmt->bind_param("si", $email, $user['player_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Check if phone number already exists (excluding current user)
            $stmt = $conn->prepare("SELECT * FROM Player WHERE phone_number = ? AND player_id != ?");
            $stmt->bind_param("si", $phoneNumber, $user['player_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Phone number already exists";
            } else {
                // Update user profile
                $stmt = $conn->prepare("UPDATE Player SET first_name = ?, last_name = ?, age = ?, email = ?, phone_number = ?, university_name = ? WHERE player_id = ?");
                $stmt->bind_param("ssisssi", $firstName, $lastName, $age, $email, $phoneNumber, $universityName, $user['player_id']);
                
                if ($stmt->execute()) {
                    // Update session data
                    $stmt = $conn->prepare("SELECT * FROM Player WHERE player_id = ?");
                    $stmt->bind_param("i", $user['player_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $_SESSION['user'] = $result->fetch_assoc();
                    $user = $_SESSION['user'];
                    
                    $success = "Profile updated successfully";
                } else {
                    $error = "Failed to update profile: " . $stmt->error;
                }
            }
        }
        
        $stmt->close();
    }
}

// Process password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All password fields are required";
    } else if ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match";
    } else if ($currentPassword !== $user['password']) {
        $error = "Current password is incorrect";
    } else {
        // Update password
        $stmt = $conn->prepare("UPDATE Player SET password = ? WHERE player_id = ?");
        $stmt->bind_param("si", $newPassword, $user['player_id']);
        
        if ($stmt->execute()) {
            // Update session data
            $user['password'] = $newPassword;
            $_SESSION['user'] = $user;
            
            $success = "Password updated successfully";
        } else {
            $error = "Failed to update password: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Process profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_picture'])) {
    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Get file info
        $fileName = $_FILES['profilePicture']['name'];
        $fileType = $_FILES['profilePicture']['type'];
        $fileTmpName = $_FILES['profilePicture']['tmp_name'];
        $fileError = $_FILES['profilePicture']['error'];
        $fileSize = $_FILES['profilePicture']['size'];
        
        // Generate unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = 'profile_' . $user['player_id'] . '_' . time() . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $newFileName;
        
        // Check if file is an image
        $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Only JPG, PNG, and GIF files are allowed";
        } else if ($fileSize > 5000000) { // 5MB max
            $error = "File is too large (max 5MB)";
        } else if (move_uploaded_file($fileTmpName, $targetFilePath)) {
            // Update profile picture in database
            $stmt = $conn->prepare("UPDATE Player SET profile_picture = ? WHERE player_id = ?");
            $stmt->bind_param("si", $newFileName, $user['player_id']);
            
            if ($stmt->execute()) {
                // Update session data
                $user['profile_picture'] = $newFileName;
                $_SESSION['user'] = $user;
                
                $success = "Profile picture updated successfully";
            } else {
                $error = "Failed to update profile picture in database: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $error = "Failed to upload profile picture";
        }
    } else {
        $error = "Please select a file to upload";
    }
}

// Process sports preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_sports'])) {
    // Get selected sports
    $selectedSports = isset($_POST['sports']) ? $_POST['sports'] : array();
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Delete all current preferences
        $stmt = $conn->prepare("DELETE FROM Preferred WHERE player_id = ?");
        $stmt->bind_param("i", $user['player_id']);
        $stmt->execute();
        
        // Add new preferences
        if (!empty($selectedSports)) {
            $stmt = $conn->prepare("INSERT INTO Preferred (player_id, sport_id) VALUES (?, ?)");
            
            foreach ($selectedSports as $sportId) {
                $stmt->bind_param("ii", $user['player_id'], $sportId);
                $stmt->execute();
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        $success = "Sports preferences updated successfully";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error = "Failed to update sports preferences: " . $e->getMessage();
    }
}

// Get all sports
$stmt = $conn->prepare("SELECT * FROM Sport ORDER BY sport_name");
$stmt->execute();
$allSportsResult = $stmt->get_result();
$allSports = $allSportsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get user's preferred sports
$stmt = $conn->prepare("SELECT sport_id FROM Preferred WHERE player_id = ?");
$stmt->bind_param("i", $user['player_id']);
$stmt->execute();
$preferredSportsResult = $stmt->get_result();
$preferredSports = array();
while ($row = $preferredSportsResult->fetch_assoc()) {
    $preferredSports[] = $row['sport_id'];
}
$stmt->close();

// Close database connection
$conn->close();
?>

<main class="container">
    <h1 class="page-title">Settings</h1>

    <div class="settings-container">
        <div class="settings-sidebar">
            <ul class="settings-menu">
                <li><a href="settings.php?tab=profile" class="<?php echo $activeTab === 'profile' ? 'active' : ''; ?>">Profile</a></li>
                <li><a href="settings.php?tab=password" class="<?php echo $activeTab === 'password' ? 'active' : ''; ?>">Password</a></li>
                <li><a href="settings.php?tab=picture" class="<?php echo $activeTab === 'picture' ? 'active' : ''; ?>">Profile Picture</a></li>
                <li><a href="settings.php?tab=sports" class="<?php echo $activeTab === 'sports' ? 'active' : ''; ?>">Sports Preferences</a></li>
            </ul>
        </div>

        <div class="settings-content">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($activeTab === 'profile'): ?>
                <div class="settings-section">
                    <h2 class="settings-section-title">Profile Information</h2>
                    <form method="post" action="settings.php?tab=profile">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="firstName" class="form-control" value="<?php echo $user['first_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="lastName" class="form-control" value="<?php echo $user['last_name']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" id="age" name="age" min="18" class="form-control" value="<?php echo $user['age']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phoneNumber">Phone Number (XXX-XXX-XXXX)</label>
                            <input type="tel" id="phoneNumber" name="phoneNumber" pattern="\d{3}-\d{3}-\d{4}" class="form-control" value="<?php echo $user['phone_number']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="universityName">University Name</label>
                            <input type="text" id="universityName" name="universityName" class="form-control" value="University of Texas at Dallas" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                            <small class="form-text">Username cannot be changed.</small>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            <?php elseif ($activeTab === 'password'): ?>
                <div class="settings-section">
                    <h2 class="settings-section-title">Change Password</h2>
                    <form method="post" action="settings.php?tab=password">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword" minlength="6" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" minlength="6" class="form-control" required>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" name="update_password" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            <?php elseif ($activeTab === 'picture'): ?>
                <div class="settings-section">
                    <h2 class="settings-section-title">Profile Picture</h2>
                    <form method="post" action="settings.php?tab=picture" enctype="multipart/form-data">
                        <div class="profile-picture-container">
                            <img src="<?php echo file_exists('uploads/' . $user['profile_picture']) ? 'uploads/' . $user['profile_picture'] : 'images/default-profile.png'; ?>" alt="Profile Picture" class="profile-picture-preview">
                            
                            <div class="form-group">
                                <label for="profilePicture">Upload New Picture</label>
                                <input type="file" id="profilePicture" name="profilePicture" class="form-control" accept="image/*">
                                <small class="form-text">Max file size: 5MB. Supported formats: JPG, PNG, GIF.</small>
                            </div>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" name="upload_picture" class="btn btn-primary">Upload Picture</button>
                        </div>
                    </form>
                </div>
            <?php elseif ($activeTab === 'sports'): ?>
                <div class="settings-section">
                    <h2 class="settings-section-title">Sports Preferences</h2>
                    <form method="post" action="settings.php?tab=sports">
                        <p>Select the sports you're interested in:</p>
                        
                        <div class="sports-checkboxes">
                            <?php foreach ($allSports as $sport): ?>
                                <div class="sport-checkbox">
                                    <input type="checkbox" id="sport_<?php echo $sport['sport_id']; ?>" name="sports[]" value="<?php echo $sport['sport_id']; ?>" <?php echo in_array($sport['sport_id'], $preferredSports) ? 'checked' : ''; ?>>
                                    <label for="sport_<?php echo $sport['sport_id']; ?>"><?php echo $sport['sport_name']; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" name="update_sports" class="btn btn-primary">Save Preferences</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

## Answers to Your Questions

### Do I need to re-download/upload the code after changes?

Yes, whenever you make changes to the code, you need to update the files on your web server. Here's how:

1. If you're editing files directly on the server (e.g., using a text editor on the server), you don't need to re-upload.
2. If you're editing files locally (e.g., on your computer), you need to upload the changed files to the server.

### Can I run this in VS Code instead of XAMPP?

VS Code is just a text editor - you still need a web server with PHP and MySQL to run the application. However, you can use VS Code as your editor while running XAMPP. Here's how to set it up:

1. Install VS Code
2. Install the PHP extension for VS Code
3. Open your project folder in VS Code
4. Edit your files in VS Code
5. Run XAMPP to serve your website

Alternatively, you can use VS Code's "Live Server" extension, but it won't process PHP files - it only serves static HTML/CSS/JS.

### How do I see the admin view of the database?

To see and manage your database directly:

1. Start XAMPP and make sure MySQL is running
2. Open your web browser and go to `http://localhost/phpmyadmin/`
3. Log in (default username is "root" with no password)
4. Select the "sports2you" database from the left sidebar
5. You'll see all your tables listed (Player, Game, Sport, etc.)
6. Click on any table to view its contents
7. You can directly see all data, including:
   - User accounts in the "Player" table
   - Created games in the "Game" table
   - Sports preferences in the "Preferred" table

This is the "admin view" of your database where you can see all stored data and make direct changes if needed.

### Additional Tips

1. **Testing the Application**: After making changes, always test thoroughly to ensure everything works as expected.
2. **Database Backup**: Regularly back up your database using phpMyAdmin's Export feature.
3. **Error Logging**: If something doesn't work, check the PHP error logs in XAMPP (click on "Logs" in the XAMPP Control Panel).

Let me know if you need any clarification or have additional questions!
