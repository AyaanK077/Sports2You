<footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">Sports2You</h3>
                    <p>Connect with fellow students, create or join games, and play your favorite sports.</p>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="browse.php">Browse Games</a></li>
                            <li><a href="settings.php">Settings</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="signup.php">Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Contact</h3>
                    <p>Email: info@sports2you.com</p>
                    <p>Phone: (123) 456-7890</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Sports2You. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            document.getElementById('navLinks').classList.toggle('show');
        });
    </script>
</body>
</html>
