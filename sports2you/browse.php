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
$pageTitle = "Browse Games - Sports2You";
include 'includes/header.php';

// Include database connection
require_once 'includes/db_connection.php';

// Define location options and sort them alphabetically
$locationOptions = [
    'Activity Center Indoor Courts',
    'Activity Center Outside Courts',
    'Activity Center Tennis Courts',
    'Activity Center Volleyball Courts',
    'Aux Gym',
    'Rec Center West',
    'Residence Hall Courts',
    'Soccer Fields'
];
sort($locationOptions);

// Get all sports
$stmt = $conn->prepare("SELECT * FROM Sport ORDER BY sport_name");
$stmt->execute();
$sportsResult = $stmt->get_result();
$sports = $sportsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get all games
$stmt = $conn->prepare("
    SELECT g.*, s.sport_name, p.first_name, p.last_name 
    FROM Game g
    JOIN Sport s ON g.sport_id = s.sport_id
    JOIN Player p ON g.creator_id = p.player_id
    ORDER BY g.game_time
");
$stmt->execute();
$gamesResult = $stmt->get_result();
$games = $gamesResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Close database connection
$conn->close();

// Function to format date and time
function formatDateTime($dateTimeStr) {
    $date = new DateTime($dateTimeStr);
    return $date->format('D, M j, Y, g:i A');
}
?>

<main class="container">
    <h1 class="page-title">Browse Games</h1>

    <div class="dashboard-card">
        <h2 class="dashboard-card-title">Filters</h2>
        
        <div class="filters-container">
            <div class="form-group">
                <label for="sportFilter">Sport</label>
                <select id="sportFilter" class="form-control">
                    <option value="">All Sports</option>
                    <?php foreach ($sports as $sport): ?>
                        <option value="<?php echo $sport['sport_id']; ?>"><?php echo $sport['sport_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="locationFilter">Location</label>
                <select id="locationFilter" class="form-control">
                    <option value="">All Locations</option>
                    <?php foreach ($locationOptions as $location): ?>
                        <option value="<?php echo $location; ?>"><?php echo $location; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="skillLevelFilter">Skill Level</label>
                <select id="skillLevelFilter" class="form-control">
                    <option value="">All Skill Levels</option>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="searchFilter">Search</label>
                <input type="text" id="searchFilter" class="form-control" placeholder="Search by creator name">
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <h2 class="dashboard-card-title">Available Games</h2>
        
        <div id="gamesContainer">
            <?php if (empty($games)): ?>
                <p>No games found.</p>
            <?php else: ?>
                <div class="game-cards">
                    <?php foreach ($games as $game): ?>
                        <div class="game-card" 
                             data-sport-id="<?php echo $game['sport_id']; ?>" 
                             data-skill-level="<?php echo $game['skill_level_required']; ?>"
                             data-location="<?php echo $game['location']; ?>">
                            <div class="game-card-header">
                                <h3><?php echo $game['sport_name']; ?></h3>
                            </div>
                            <div class="game-card-body">
                                <p class="game-card-detail"><span>When:</span> <?php echo formatDateTime($game['game_time']); ?></p>
                                <p class="game-card-detail"><span>Where:</span> <?php echo $game['location']; ?></p>
                                <p class="game-card-detail"><span>Skill Level:</span> <?php echo $game['skill_level_required']; ?></p>
                                <p class="game-card-detail"><span>Created by:</span> <?php echo $game['first_name'] . ' ' . $game['last_name']; ?></p>
                                <a href="session.php?id=<?php echo $game['game_id']; ?>" class="btn btn-primary" style="display: block; text-align: center; margin-top: 1rem;">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    // Filter games
    function filterGames() {
        const sportId = document.getElementById('sportFilter').value;
        const skillLevel = document.getElementById('skillLevelFilter').value;
        const location = document.getElementById('locationFilter').value;
        const searchQuery = document.getElementById('searchFilter').value.toLowerCase();
        
        const gameCards = document.querySelectorAll('.game-card');
        
        gameCards.forEach(card => {
            let show = true;
            
            // Filter by sport
            if (sportId && card.dataset.sportId !== sportId) {
                show = false;
            }
            
            // Filter by skill level
            if (skillLevel && card.dataset.skillLevel !== skillLevel) {
                show = false;
            }
            
            // Filter by location
            if (location && card.dataset.location !== location) {
                show = false;
            }
            
            // Filter by search query
            if (searchQuery) {
                const cardText = card.textContent.toLowerCase();
                if (!cardText.includes(searchQuery)) {
                    show = false;
                }
            }
            
            // Show or hide card
            card.style.display = show ? 'block' : 'none';
        });
        
        // Check if any cards are visible
        const visibleCards = document.querySelectorAll('.game-card[style="display: block;"]');
        const gamesContainer = document.getElementById('gamesContainer');
        
        if (visibleCards.length === 0) {
            // No cards visible, show message
            if (!document.getElementById('noGamesMessage')) {
                const message = document.createElement('p');
                message.id = 'noGamesMessage';
                message.textContent = 'No games found matching your filters.';
                gamesContainer.appendChild(message);
            }
        } else {
            // Cards visible, remove message if exists
            const message = document.getElementById('noGamesMessage');
            if (message) {
                message.remove();
            }
        }
    }

    // Add event listeners to filters
    document.getElementById('sportFilter').addEventListener('change', filterGames);
    document.getElementById('skillLevelFilter').addEventListener('change', filterGames);
    document.getElementById('locationFilter').addEventListener('change', filterGames);
    document.getElementById('searchFilter').addEventListener('input', filterGames);
</script>

<?php include 'includes/footer.php'; ?>
