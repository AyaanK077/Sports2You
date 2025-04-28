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
$pageTitle = "Create Game - Sports2You";
include 'includes/header.php';

// Include database connection
require_once 'includes/db_connection.php';

// Initialize variables
$error = "";
$success = "";

// Define location options
$locationOptions = [
    'Activity Center Indoor Courts',
    'Aux Gym',
    'Rec Center West',
    'Activity Center Outside Courts',
    'Activity Center Tennis Courts',
    'Soccer Fields',
    'Activity Center Volleyball Courts',
    'Residence Hall Courts'
];

// Process create game form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sportId = $_POST['sportId'];
    $skillLevel = $_POST['skillLevel'];
    $location = $_POST['location'];
    $gameDate = $_POST['gameDate'];
    $gameTime = $_POST['gameTime'];
    
    // Validate input
    if (empty($sportId) || empty($skillLevel) || empty($location) || empty($gameDate) || empty($gameTime)) {
        $error = "All fields are required";
    } else {
        // Combine date and time
        $gameDateTime = $gameDate . ' ' . $gameTime . ':00';
        
        // Validate future date
        $gameTimestamp = strtotime($gameDateTime);
        $currentTimestamp = time();
        
        if ($gameTimestamp <= $currentTimestamp) {
            $error = "Game time must be in the future";
        } else {
            // Create new game
            $stmt = $conn->prepare("INSERT INTO Game (sport_id, skill_level_required, location, game_time, creator_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isssi", $sportId, $skillLevel, $location, $gameDateTime, $user['player_id']);
            
            if ($stmt->execute()) {
                $gameId = $stmt->insert_id;
                $success = "Game created successfully";
                
                // Redirect to game details page
                header("Location: session.php?id=" . $gameId);
                exit();
            } else {
                $error = "Failed to create game: " . $stmt->error;
            }
            
            $stmt->close();
        }
    }
}

// Get all sports
$stmt = $conn->prepare("SELECT * FROM Sport ORDER BY sport_name");
$stmt->execute();
$sportsResult = $stmt->get_result();
$sports = $sportsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Close database connection
$conn->close();
?>

<main class="container">
    <h1 class="page-title">Create a New Game</h1>

    <div class="dashboard-card">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="create_game.php">
            <div class="form-group">
                <label for="sportId">Sport</label>
                <select id="sportId" name="sportId" class="form-control" required>
                    <option value="">Select a Sport</option>
                    <?php foreach ($sports as $sport): ?>
                        <option value="<?php echo $sport['sport_id']; ?>"><?php echo $sport['sport_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="skillLevel">Skill Level Required</label>
                <select id="skillLevel" name="skillLevel" class="form-control" required>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <select id="location" name="location" class="form-control" required>
                    <option value="">Select a Location</option>
                    <?php foreach ($locationOptions as $locationOption): ?>
                        <option value="<?php echo $locationOption; ?>"><?php echo $locationOption; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="gameDate">Date</label>
                    <input type="date" id="gameDate" name="gameDate" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="gameTime">Time</label>
                    <input type="time" id="gameTime" name="gameTime" class="form-control" required>
                </div>
            </div>
            
            <div class="form-buttons">
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Game</button>
            </div>
        </form>
    </div>
</main>

<script>
    // Set default date to tomorrow
    window.addEventListener('DOMContentLoaded', function() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        const year = tomorrow.getFullYear();
        const month = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const day = String(tomorrow.getDate()).padStart(2, '0');
        
        document.getElementById('gameDate').value = `${year}-${month}-${day}`;
    });
</script>

<?php include 'includes/footer.php'; ?>
