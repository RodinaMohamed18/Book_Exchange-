<footer class="site-footer">
    <div class="container footer-container">
        <div class="footer-about">
            <h3 class="footer-logo">Book Exchange</h3>
            <p>A community-driven platform to share and discover new books.</p>
        </div>
        <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="book-listings.php">Browse Books</a></li>
                <li><a href="add-book.php">Add Your Book</a></li>
                <li><a href="#">About</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h3>Explore</h3>
            <ul>
                <li><a href="#">Genres</a></li>
                <li><a href="#">Most Exchanged</a></li>
                <li><a href="#">Community Picks</a></li>
                <li><a href="#">Featured</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h3>Help</h3>
            <ul>
                <li><a href="#">How It Works</a></li>
                <li><a href="#">Delivery & Returns</a></li>
                <li><a href="#">FAQs</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container bottom-container">
            <p>&copy; <?php echo date("Y"); ?> Book Exchange. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const changePhotoButton = document.getElementById('change-photo-btn');
        const photoUploadForm = document.getElementById('photo-upload-form');
        const photoInput = document.getElementById('profile-image-input');

        if (changePhotoButton && photoInput && photoUploadForm) {
            changePhotoButton.addEventListener('click', function () {
                photoInput.click();
            });
            photoInput.addEventListener('change', function () {
                if (photoInput.files.length > 0) {
                    photoUploadForm.submit();
                }
            });
        }
    });
</script>

</body>

</html>