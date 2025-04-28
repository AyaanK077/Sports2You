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
$pageTitle = "Dashboard - Sports2You";
include 'includes/header.php';

// Include database connection
require_once 'includes/db_connection.php';

// Get user's games
$stmt = $conn->prepare("
    SELECT g.*, s.sport_name 
    FROM Game g
    JOIN Sport s ON g.sport_id = s.sport_id
    WHERE g.creator_id = ?
    ORDER BY g.game_time
");
$stmt->bind_param("i", $user['player_id']);
$stmt->execute();
$gamesResult = $stmt->get_result();
$games = $gamesResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get user's preferred sports
$stmt = $conn->prepare("
    SELECT s.* 
    FROM Preferred p
    JOIN Sport s ON p.sport_id = s.sport_id
    WHERE p.player_id = ?
");
$stmt->bind_param("i", $user['player_id']);
$stmt->execute();
$sportsResult = $stmt->get_result();
$sports = $sportsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Close database connection
$conn->close();

// Function to format date and time
function formatDateTime($dateTimeStr) {
    $date = new DateTime($dateTimeStr);
    return $date->format('D, M j, Y, g:i A');
}
?>

<main class="dashboard container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>!</h1>
        <p><?php echo $user['username'] . ' | ' . $user['email']; ?></p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-main">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">My Games</h2>
                    <a href="create_game.php" class="btn btn-primary">Create New Game</a>
                </div>
                
                <?php if (empty($games)): ?>
                    <p>You haven't created any games yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sport</th>
                                    <th>Location</th>
                                    <th>Date & Time</th>
                                    <th>Skill Level</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($games as $game): ?>
                                    <tr>
                                        <td><?php echo $game['sport_name']; ?></td>
                                        <td><?php echo $game['location']; ?></td>
                                        <td><?php echo formatDateTime($game['game_time']); ?></td>
                                        <td><?php echo $game['skill_level_required']; ?></td>
                                        <td>
                                            <a href="session.php?id=<?php echo $game['game_id']; ?>" class="btn-link">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-sidebar">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h2 class="dashboard-card-title">My Sports</h2>
                    <a href="settings.php?tab=sports" class="btn-link">Edit</a>
                </div>
                
                <?php if (empty($sports)): ?>
                    <p>You haven't added any preferred sports yet.</p>
                <?php else: ?>
                    <ul class="sports-list">
                        <?php foreach ($sports as $sport): ?>
                            <li>
                                <span class="sport-dot"></span>
                                <?php echo $sport['sport_name']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="dashboard-card">
                <h2 class="dashboard-card-title">Quick Links</h2>
                <ul class="quick-links">
                    <li><a href="availability.php">Manage My Availability</a></li>
                    <li><a href="browse.php">Browse All Games</a></li>
                    <li><a href="create_game.php">Create New Game</a></li>
                    <li><a href="settings.php">Profile Settings</a></li>
                </ul>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
