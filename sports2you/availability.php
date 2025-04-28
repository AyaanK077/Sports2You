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
$pageTitle = "My Availability - Sports2You";
include 'includes/header.php';

// Include database connection
require_once 'includes/db_connection.php';

// Initialize variables
$error = "";
$success = "";

// Process add/update availability form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_availability'])) {
        // Add new availability
        $dayAvailability = $_POST['dayAvailability'];
        $startAvailability = $_POST['startAvailability'];
        $endAvailability = $_POST['endAvailability'];
        
        // Validate input
        if (empty($dayAvailability) || empty($startAvailability) || empty($endAvailability)) {
            $error = "All fields are required";
        } else if ($startAvailability >= $endAvailability) {
            $error = "End time must be after start time";
        } else {
            // Insert new availability
            $stmt = $conn->prepare("INSERT INTO Player_Availability (player_id, day_availability, start_availability, end_availability) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user['player_id'], $dayAvailability, $startAvailability, $endAvailability);
            
            if ($stmt->execute()) {
                $success = "Availability added successfully";
            } else {
                $error = "Failed to add availability: " . $stmt->error;
            }
            
            $stmt->close();
        }
    } else if (isset($_POST['update_availability'])) {
        // Update existing availability
        $availabilityId = $_POST['availabilityId'];
        $dayAvailability = $_POST['dayAvailability'];
        $startAvailability = $_POST['startAvailability'];
        $endAvailability = $_POST['endAvailability'];
        
        // Validate input
        if (empty($dayAvailability) || empty($startAvailability) || empty($endAvailability)) {
            $error = "All fields are required";
        } else if ($startAvailability >= $endAvailability) {
            $error = "End time must be after start time";
        } else {
            // Update availability
            $stmt = $conn->prepare("UPDATE Player_Availability SET day_availability = ?, start_availability = ?, end_availability = ? WHERE availability_id = ? AND player_id = ?");
            $stmt->bind_param("sssii", $dayAvailability, $startAvailability, $endAvailability, $availabilityId, $user['player_id']);
            
            if ($stmt->execute()) {
                $success = "Availability updated successfully";
            } else {
                $error = "Failed to update availability: " . $stmt->error;
            }
            
            $stmt->close();
        }
    } else if (isset($_POST['delete_availability'])) {
        // Delete availability
        $availabilityId = $_POST['availabilityId'];
        
        // Delete availability
        $stmt = $conn->prepare("DELETE FROM Player_Availability WHERE availability_id = ? AND player_id = ?");
        $stmt->bind_param("ii", $availabilityId, $user['player_id']);
        
        if ($stmt->execute()) {
            $success = "Availability deleted successfully";
        } else {
            $error = "Failed to delete availability: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Get user's availabilities
$stmt = $conn->prepare("SELECT * FROM Player_Availability WHERE player_id = ? ORDER BY day_availability, start_availability");
$stmt->bind_param("i", $user['player_id']);
$stmt->execute();
$availabilitiesResult = $stmt->get_result();
$availabilities = $availabilitiesResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Close database connection
$conn->close();

// Function to format time
function formatTime($timeStr) {
    $time = DateTime::createFromFormat('H:i:s', $timeStr);
    return $time ? $time->format('g:i A') : '';
}
?>

<main class="container">
    <h1 class="page-title">Manage Your Availability</h1>

    <div class="dashboard-card">
        <h2 class="dashboard-card-title">
            <?php echo isset($_GET['edit']) ? 'Edit Availability' : 'Add New Availability'; ?>
        </h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php
        // Get availability to edit
        $editAvailability = null;
        if (isset($_GET['edit'])) {
            foreach ($availabilities as $availability) {
                if ($availability['availability_id'] == $_GET['edit']) {
                    $editAvailability = $availability;
                    break;
                }
            }
        }
        ?>
        
        <form method="post" action="availability.php">
            <?php if ($editAvailability): ?>
                <input type="hidden" name="availabilityId" value="<?php echo $editAvailability['availability_id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="dayAvailability">Date</label>
                    <input type="date" id="dayAvailability" name="dayAvailability" class="form-control" value="<?php echo $editAvailability ? substr($editAvailability['day_availability'], 0, 10) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="startAvailability">Start Time</label>
                    <input type="time" id="startAvailability" name="startAvailability" class="form-control" value="<?php echo $editAvailability ? substr($editAvailability['start_availability'], 0, 5) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="endAvailability">End Time</label>
                    <input type="time" id="endAvailability" name="endAvailability" class="form-control" value="<?php echo $editAvailability ? substr($editAvailability['end_availability'], 0, 5) : ''; ?>" required>
                </div>
            </div>
            
            <div class="form-buttons">
                <?php if ($editAvailability): ?>
                    <a href="availability.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update_availability" class="btn btn-primary">Update Availability</button>
                <?php else: ?>
                    <button type="submit" name="add_availability" class="btn btn-primary">Add Availability</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="dashboard-card">
        <h2 class="dashboard-card-title">Your Availabilities</h2>
        
        <?php if (empty($availabilities)): ?>
            <p>You haven't added any availabilities yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availabilities as $availability): ?>
                            <tr>
                                <td><?php echo date('D, M j, Y', strtotime($availability['day_availability'])); ?></td>
                                <td><?php echo formatTime($availability['start_availability']); ?></td>
                                <td><?php echo formatTime($availability['end_availability']); ?></td>
                                <td>
                                    <a href="availability.php?edit=<?php echo $availability['availability_id']; ?>" class="btn-link">Edit</a>
                                    <form method="post" action="availability.php" style="display: inline;">
                                        <input type="hidden" name="availabilityId" value="<?php echo $availability['availability_id']; ?>">
                                        <button type="submit" name="delete_availability" class="btn-link" onclick="return confirm('Are you sure you want to delete this availability?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
