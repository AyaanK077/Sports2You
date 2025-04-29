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
                <img src="images/logo.png" alt="Sports2You Logo" class="logo-large">
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

<section class="about-us">
    <div class="container">
        <h2 class="section-title">About Us</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Sports2You was created to solve a common problem faced by college students at the University of Texas at Dallas: finding people to play sports with on campus.</p>
                <p>Our platform makes it easy to connect with fellow students who share your passion for sports. Whether you're looking for a pickup basketball game, a tennis partner, or a volleyball team, Sports2You helps you find the right match.</p>
                <p>This project was developed as part of a Database Systems class by a dedicated team of students who wanted to create a practical solution to enhance campus life and promote physical activity among students.</p>
                <h3>Our Team</h3>
                <p>Sports2You was created by a group of passionate students who wanted to make it easier for everyone to stay active and connected through sports.</p>
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
