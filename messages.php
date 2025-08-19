<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        u.id as partner_id,
        u.username as partner_name,
        m.message, 
        m.created_at
    FROM messages m
    JOIN users u ON u.id = IF(m.sender_id = ?, m.receiver_id, m.sender_id)
    WHERE m.id IN (
        SELECT MAX(id)
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
    )
    ORDER BY m.created_at DESC
");
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$conversations = $stmt->get_result();
?>


<div class="generic-page-header">
  <div class="container">
    <h1>Messages</h1>
  </div>
</div>


<main class="page-main-content">
  <div class="container">
    <div class="messages-wrapper">
      <aside class="contacts-list">
        <?php if ($conversations->num_rows > 0): ?>
          <?php while ($convo = $conversations->fetch_assoc()): ?>
            <div class="contact">
              <a href="conversation.php?partner_id=<?php echo $convo['partner_id']; ?>">
                <span class="contact-name"><?php echo htmlspecialchars($convo['partner_name']); ?></span>
                <span class="last-message"><?php echo htmlspecialchars(substr($convo['message'], 0, 80)) . '...'; ?></span>
              </a>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>You have no messages yet. You can start a conversation from a book’s detail page.</p>
        <?php endif; ?>
      </aside>
      <section class="chat-area">
        <div class="message-list">
          <p>Select a conversation to start chatting</p>
        </div>
        <form class="message-input-form">
          <input type="text" placeholder="Type your message..." class="input-text" disabled>
          <button type="submit" class="btn btn-send" disabled>Send</button>
        </form>
      </section>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>