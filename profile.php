<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : ($_SESSION['user_id'] ?? 0);
if ($profile_user_id === 0) { header('Location: index.php'); exit(); }
$is_own_profile = ($profile_user_id === ($_SESSION['user_id'] ?? null));


$user_stmt = $conn->prepare("SELECT username, email, full_name, address, phone, profile_pic, created_at FROM users WHERE id = ?");
$user_stmt->bind_param("i", $profile_user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<main class='page-main-content'><div class='container'>User not found.</div></main>";
    include 'includes/footer.php';
    exit();
}


$ratings_query = $conn->prepare("SELECT rating, review, u.username as reviewer_name FROM ratings r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewed_user_id = ? ORDER BY r.created_at DESC");
$ratings_query->bind_param("i", $profile_user_id);
$ratings_query->execute();
$reviews = $ratings_query->get_result()->fetch_all(MYSQLI_ASSOC);
$num_ratings = count($reviews);
$avg_rating = 0;
if ($num_ratings > 0) {
    $total_rating = 0;
    foreach ($reviews as $review) { $total_rating += $review['rating']; }
    $avg_rating = round($total_rating / $num_ratings, 1);
}


$history_stmt = $conn->prepare("SELECT b.title AS book_title, e.status, e.created_at, requester.username AS requester_name, owner.username AS owner_name FROM exchanges e JOIN books b ON e.book_id = b.id JOIN users requester ON e.requester_id = requester.id JOIN users owner ON e.owner_id = owner.id WHERE e.requester_id = ? OR e.owner_id = ? ORDER BY e.created_at DESC");
$history_stmt->bind_param("ii", $profile_user_id, $profile_user_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<div class="generic-page-header">
    <div class="container"><h1><?php echo $is_own_profile ? "My Profile" : htmlspecialchars($user['username']) . "'s Profile"; ?></h1></div>
</div>

<main class="page-main-content">
    <div class="container">
        <div class="profile-grid">
            <aside class="profile-card">
                <div class="profile-avatar">
                    <?php
                        $profile_pic_src = 'https://via.placeholder.com/150';
                        if (!empty($user['profile_pic'])) {
                            
                            $profile_pic_src = '/Book Exchange/uploads/profiles/' . htmlspecialchars($user['profile_pic']) . '?v=' . time();
                        }
                    ?>
                    <img src="<?php echo $profile_pic_src; ?>" alt="Profile Picture">
                    
                    <?php if ($is_own_profile): ?>
                        <button id="change-photo-btn" class="btn btn-secondary">Change Photo</button>
                    <?php endif; ?>
                </div>

                <form action="upload-profile-photo.php" method="post" enctype="multipart/form-data" id="photo-upload-form" style="display: none;">
                    <input type="file" name="profile_image" id="profile-image-input" accept="image/*">
                </form>

                
                <div class="profile-info">
                    <h2 class="user-name"><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h2>
                    <p class="user-email"><?php echo htmlspecialchars($user['email']); ?></p>
                    
                    <?php if ($is_own_profile): ?>
                        <div class="user-contact-details">
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <p class="user-join-date">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                    
                    
                    <?php if ($is_own_profile): ?>
                        <a href="edit-profile.php" class="btn">Edit Profile</a>
                    <?php endif; ?>
                </div>
            </aside>
            
            <div class="profile-activity">
               
                <section class="user-rating-section">
                    <h3>User Rating</h3>
                    <div class="rating-display">
                        <div class="rating-stars"><?php echo str_repeat('★', floor($avg_rating)) . str_repeat('☆', 5 - floor($avg_rating)); ?></div>
                        <p class="rating-text"><?php echo $avg_rating; ?> average based on <?php echo $num_ratings; ?> reviews</p>
                    </div>
                </section>
                
                <section class="exchange-history-section">
                    <h3>Exchange History</h3>
                    <table class="history-table">
                        <thead><tr><th>Book Title</th><th>Partner</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($history_result->num_rows > 0): ?>
                                <?php while ($row = $history_result->fetch_assoc()): ?>
                                  <tr>
                                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                                    <td><?php echo htmlspecialchars(($row['requester_name'] === $user['username']) ? $row['owner_name'] : $row['requester_name']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                  </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No exchange history yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>

                <section class="user-reviews-section" style="margin-top: 30px;">
                    <h3>Reviews Received</h3>
                    <div class="reviews-list">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px;">
                                    <strong>Rating: <?php echo str_repeat('★', $review['rating']); ?></strong> by <strong><?php echo htmlspecialchars($review['reviewer_name']); ?></strong>
                                    <?php if (!empty($review['review'])): ?><p style="margin-top: 5px;"><em>"<?php echo nl2br(htmlspecialchars($review['review'])); ?>"</em></p><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>This user has not received any reviews yet.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<?php 
$user_stmt->close();
$ratings_query->close();
$history_stmt->close();
include 'includes/footer.php'; 
?>