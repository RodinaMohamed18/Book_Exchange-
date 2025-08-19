<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="generic-page-header">
    <div class="container">
        <h1>Notifications</h1>
    </div>
</div>

<main class="page-main-content">
    <div class="container" style="max-width: 900px;">
        <div class="notifications-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $unread_class = $row['is_read'] == 0 ? 'notification-unread' : '';
                    ?>

                    <a href="<?php echo htmlspecialchars($row['link']); ?>"
                        class="notification-item <?php echo $unread_class; ?>">
                        <div class="notification-content">
                            <p class="notification-message"><?php echo htmlspecialchars($row['message']); ?></p>
                            <small
                                class="notification-timestamp"><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></small>
                        </div>
                    </a>
                    <?php
                }
            } else {
                echo "<div class='no-notifications'><p>You have no notifications yet.</p></div>";
            }
            ?>
        </div>
    </div>
</main>

<?php

$update_stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
$update_stmt->close();

$stmt->close();
include 'includes/footer.php';
?>