<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once 'includes/db.php';

$notification_count = 0;
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $stmt = $conn->prepare("SELECT COUNT(id) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  $notification_count = $result['unread_count'];
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Book Exchange</title>
  <link href="style.css" rel="stylesheet" />
  <style>
    .notification-link.unread {
      color: red !important;

      padding: 6px 12px;
      border-radius: 6px;
    }

    .notification-badge {
      background: white;
      color: red;
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 50%;
      margin-left: 4px;
      font-weight: bold;
    }
  </style>
</head>

<body>


  <header class="site-header">
    <div class="container header-container">
      <h1 class="logo"><a href="index.php">Book Exchange</a></h1>
      <nav class="main-nav">
        <ul class="nav-links">
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="book-listings.php">Browse</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="add-book.php">Add Book</a></li>
            <li><a href="my-books.php">My Books</a></li>
          <?php endif; ?>
        </ul>
      </nav>
      <div class="header-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="notifications.php" class="notification-link <?php echo ($notification_count > 0) ? 'unread' : ''; ?>">
            Notifications
            <?php if ($notification_count > 0): ?>
              <span class="notification-badge"><?php echo $notification_count; ?></span>
            <?php endif; ?>
          </a>
          <a href="messages.php" class="header-icon">Messages</a>
          <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="header-icon">Profile</a>
          <div class="auth-buttons">
            <a href="logout.php" class="btn btn-secondary">Logout</a>
          </div>
        <?php else: ?>
          <div class="auth-buttons">
            <a href="login.php" class="btn btn-secondary">Login</a>
            <a href="register.php" class="btn">Sign Up</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </header>