<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Check if game ID is provided
if (!isset($_GET['id'])) {
    header("Location: browse.php");
    exit();
}

// Get user data
$user = $_SESSION['user'];
$gameId = $_GET['id'];

// Set page title
$pageTitle = "Game Details - Sports2You";
include 'includes/header.php';

// Include database connection
require_once 'includes/db_connection.php';

// Initialize variables
$error = "";
$success = "";

// Process join game form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['join_game'])) {
        $availabilityId = $_POST['availabilityId'];
        
        // Validate input
        if (empty($availabilityId)) {
            $error = "Please select an availability";
        } else {
            // Get game details
            $stmt = $conn->prepare("SELECT * FROM Game WHERE game_id = ?");
            $stmt->bind_param("i", $gameId);
            $stmt->execute();
            $gameResult = $stmt->get_result();
            $game = $gameResult->fetch_assoc();
            $stmt->close();
            
            // Get availability details
            $stmt = $conn->prepare("SELECT * FROM Player_Availability WHERE availability_id = ?");
            $stmt->bind_param("i", $availabilityId);
            $stmt->execute();
            $availabilityResult = $stmt->get_result();
            $availability = $availabilityResult->fetch_assoc();
            $stmt->close();
            
            // Check if availability matches game time
            $gameDate = date('Y-m-d', strtotime($game['game_time']));
            $availabilityDate = date('Y-m-d', strtotime($availability['day_availability']));
            $gameStartTime = date('H:i:s', strtotime($game['game_time']));
            $gameEndTime = !empty($game['end_time']) ? date('H:i:s', strtotime($game['end_time'])) : date('H:i:s', strtotime($game['game_time']) + 3600); // Default to 1 hour if no end time
            
            if ($gameDate != $availabilityDate) {
                $error = "Your availability date does not match the game date";
            } else if ($gameStartTime < $availability['start_availability'] || $gameEndTime > $availability['end_availability']) {
                $error = "Your availability time does not cover the entire game time";
            } else {
                // Check if already joined
                $stmt = $conn->prepare("SELECT * FROM All_Available WHERE game_id = ? AND availability_id = ?");
                $stmt->bind_param("ii", $gameId, $availabilityId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error = "You have already joined this game with this availability";
                } else {
                    // Add player to game
                    $stmt = $conn->prepare("INSERT INTO All_Available (game_id, availability_id) VALUES (?, ?)");
                    $stmt->bind_param("ii", $gameId, $availabilityId);
                    
                    if ($stmt->execute()) {
                        $success = "Successfully joined the game!";
                    } else {
                        $error = "Failed to join game: " . $stmt->error;
                    }
                }
                
                $stmt->close();
            }
        }
    } else if (isset($_POST['delete_game'])) {
        // Delete game
        $stmt = $conn->prepare("DELETE FROM Game WHERE game_id = ? AND creator_id = ?");
        $stmt->bind_param("ii", $gameId, $user['player_id']);
        
        if ($stmt->execute()) {
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to delete game: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Get game details
$stmt = $conn->prepare("
    SELECT g.*, s.sport_name, p.first_name, p.last_name 
    FROM Game g
    JOIN Sport s ON g.sport_id = s.sport_id
    JOIN Player p ON g.creator_id = p.player_id
    WHERE g.game_id = ?
");
$stmt->bind_param("i", $gameId);
$stmt->execute();
$gameResult = $stmt->get_result();

if ($gameResult->num_rows === 0) {
    $error = "Game not found";
} else {
    $game = $gameResult->fetch_assoc();
    $isCreator = ($game['creator_id'] == $user['player_id']);
}
$stmt->close();

// Get players for this game
$players = array();
if (isset($game)) {
    $stmt = $conn->prepare("
        SELECT DISTINCT p.* 
        FROM All_Available aa
        JOIN Player_Availability pa ON aa.availability_id = pa.availability_id
        JOIN Player p ON pa.player_id = p.player_id
        WHERE aa.game_id = ?
    ");
    $stmt->bind_param("i", $gameId);
    $stmt->execute();
    $playersResult = $stmt->get_result();
    $players = $playersResult->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Get user's availabilities
$availabilities = array();
if (isset($game) && !$isCreator) {
    // Get game date
    $gameDate = date('Y-m-d', strtotime($game['game_time']));
    
    // Get user's availabilities for the game date
    $stmt = $conn->prepare("
        SELECT * FROM Player_Availability 
        WHERE player_id = ? AND DATE(day_availability) = ?
        ORDER BY start_availability
    ");
    $stmt->bind_param("is", $user['player_id'], $gameDate);
    $stmt->execute();
    $availabilitiesResult = $stmt->get_result();
    $availabilities = $availabilitiesResult->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Close database connection
$conn->close();

// Function to format date and time
function formatDateTime($dateTimeStr) {
    $date = new DateTime($dateTimeStr);
    return $date->format('l, F j, Y, g:i A');
}

// Function to format date
function formatDate($dateStr) {
    $date = new DateTime($dateStr);
    return $date->format('D, M j, Y');
}

// Function to format time
function formatTime($timeStr) {
    $time = DateTime::createFromFormat('H:i:s', $timeStr);
    return $time ? $time->format('g:i A') : '';
}
?>

<main class="container">
    <?php if (!empty($error) && !isset($game)): ?>
        <div class="alert alert-danger">
            <p><?php echo $error; ?></p>
            <a href="browse.php" class="btn btn-primary">Back to Browse</a>
        </div>
    <?php elseif (isset($game)): ?>
        <div class="dashboard-card">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="session-header">
                <div>
                    <h1 class="page-title"><?php echo $game['sport_name']; ?> Session</h1>
                    <p>Created by <?php echo $game['first_name'] . ' ' . $game['last_name']; ?></p>
                </div>
                
                <?php if ($isCreator): ?>
                    <div>
                        <form method="post" action="session.php?id=<?php echo $gameId; ?>" onsubmit="return confirm('Are you sure you want to delete this game? This action cannot be undone.');">
                            <button type="submit" name="delete_game" class="btn btn-danger">Delete Game</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="session-details">
                <div class="session-info">
                    <h2>Details</h2>
                    <ul class="details-list">
                        <li>
                            <span>Date & Time:</span> 
                            <?php echo formatDateTime($game['game_time']); ?>
                            <?php if (!empty($game['end_time'])): ?>
                                <br><span></span> to <?php echo formatDateTime($game['end_time']); ?>
                            <?php endif; ?>
                        </li>
                        <li><span>Location:</span> <?php echo $game['location']; ?></li>
                        <li><span>Skill Level:</span> <?php echo $game['skill_level_required']; ?></li>
                    </ul>
                </div>
                
                <div class="session-players">
                    <h2>Players</h2>
                    
                    <?php if (empty($players)): ?>
                        <p>No players have joined yet.</p>
                    <?php else: ?>
                        <ul class="players-list">
                            <?php foreach ($players as $player): ?>
                                <li>
                                    <span class="player-dot"></span>
                                    <?php echo $player['first_name'] . ' ' . $player['last_name']; ?>
                                    <?php if ($player['player_id'] == $game['creator_id']): ?>
                                        <span class="creator-badge">Creator</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!$isCreator): ?>
            <div class="dashboard-card">
                <h2 class="dashboard-card-title">Join This Game</h2>
                
                <?php if (empty($availabilities)): ?>
                    <div>
                        <p>You need to add an availability for <?php echo date('l, F j, Y', strtotime($game['game_time'])); ?> that covers the game time (<?php echo date('g:i A', strtotime($game['game_time'])); ?> to <?php echo !empty($game['end_time']) ? date('g:i A', strtotime($game['end_time'])) : date('g:i A', strtotime($game['game_time']) + 3600); ?>).</p>
                        <a href="availability.php" class="btn btn-primary">Add Availability</a>
                    </div>
                <?php else: ?>
                    <form method="post" action="session.php?id=<?php echo $gameId; ?>">
                        <p>Select one of your availabilities that covers the game time:</p>
                        
                        <div class="form-group">
                            <select name="availabilityId" class="form-control" required>
                                <option value="">Select an availability</option>
                                <?php 
                                $gameStartTime = date('H:i:s', strtotime($game['game_time']));
                                $gameEndTime = !empty($game['end_time']) ? date('H:i:s', strtotime($game['end_time'])) : date('H:i:s', strtotime($game['game_time']) + 3600);
                                
                                foreach ($availabilities as $availability): 
                                    // Only show availabilities that cover the game time
                                    if ($availability['start_availability'] <= $gameStartTime && $availability['end_availability'] >= $gameEndTime):
                                ?>
                                    <option value="<?php echo $availability['availability_id']; ?>">
                                        <?php echo formatTime($availability['start_availability']); ?> to 
                                        <?php echo formatTime($availability['end_availability']); ?>
                                    </option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </select>
                        </div>
                        
                        <button type="submit" name="join_game" class="btn btn-primary">Join Game</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
