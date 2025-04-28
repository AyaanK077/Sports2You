<?php
$pageTitle = "Sports2You - Find Local Sports Games";
include 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Find Sports Games on Campus</h1>
                <p>Connect with fellow students, create or join games, and play your favorite sports.</p>
                <div class="hero-buttons">
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                    <a href="login.php" class="btn btn-secondary">Log In</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="images/logo.png" alt="Sports2You Logo">
            </div>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title">How It Works</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-number">1</div>
                <h3>Create an Account</h3>
                <p>Sign up with your university email and set your sports preferences.</p>
            </div>
            <div class="feature-card">
                <div class="feature-number">2</div>
                <h3>Set Your Availability</h3>
                <p>Let others know when you're free to play.</p>
            </div>
            <div class="feature-card">
                <div class="feature-number">3</div>
                <h3>Join or Create Games</h3>
                <p>Find existing games or create your own and invite others.</p>
            </div>
        </div>
    </div>
</section>

<section class="sports">
    <div class="container">
        <h2 class="section-title">Popular Sports</h2>
        <div class="sports-grid">
            <div class="sport-card">
                <h3>Basketball</h3>
            </div>
            <div class="sport-card">
                <h3>Soccer</h3>
            </div>
            <div class="sport-card">
                <h3>Tennis</h3>
            </div>
            <div class="sport-card">
                <h3>Volleyball</h3>
            </div>
            <div class="sport-card">
                <h3>Football</h3>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
