<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$exchange_id = isset($_GET['exchange_id']) ? intval($_GET['exchange_id']) : 0;

$stmt = $conn->prepare("SELECT requester_id, owner_id FROM exchanges WHERE id = ? AND status = 'completed'");
$stmt->bind_param("i", $exchange_id);
$stmt->execute();
$exchange = $stmt->get_result()->fetch_assoc();

if (!$exchange || ($user_id != $exchange['requester_id'] && $user_id != $exchange['owner_id'])) {
    echo "<main class='page-main-content'><div class='container'><p>Invalid request.</p></div></main>";
    include 'includes/footer.php';
    exit();
}

$reviewed_user_id = ($user_id == $exchange['requester_id']) ? $exchange['owner_id'] : $exchange['requester_id'];
?>

<div class="generic-page-header"><div class="container"><h1>Leave a Review</h1></div></div>

<main class="page-main-content">
    <div class="container" style="max-width: 600px;">
        <div class="auth-section">
            <form action="submit_review.php" method="post">
                <input type="hidden" name="exchange_id" value="<?php echo $exchange_id; ?>">
                <input type="hidden" name="reviewed_user_id" value="<?php echo $reviewed_user_id; ?>">
                <div class="form-group">
                    <label for="rating">Your Rating (1-5 Stars):</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="5">★★★★★ (Excellent)</option>
                        <option value="4">★★★★☆ (Very Good)</option>
                        <option value="3">★★★☆☆ (Good)</option>
                        <option value="2">★★☆☆☆ (Fair)</option>
                        <option value="1">★☆☆☆☆ (Poor)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="review">Your Review (optional):</label>
                    <textarea name="review" id="review" class="form-control" rows="5" placeholder="Share your experience..."></textarea>
                </div>
                <button type="submit" class="btn">Submit Review</button>
            </form>
        </div>
    </div>
</main>

<?php 
$stmt->close();
include 'includes/footer.php'; 
?>