<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : 0;


$convo_stmt = $conn->prepare("
    SELECT 
        u.id as partner_id, u.username as partner_name, m.message, m.created_at
    FROM messages m
    JOIN users u ON u.id = IF(m.sender_id = ?, m.receiver_id, m.sender_id)
    WHERE m.id IN (
        SELECT MAX(id) FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        GROUP BY LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)
    ) ORDER BY m.created_at DESC
");
$convo_stmt->bind_param("iii", $user_id, $user_id, $user_id);
$convo_stmt->execute();
$conversations = $convo_stmt->get_result();

$messages = null;
$partner = null;
if ($partner_id > 0) {
    $partner_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $partner_stmt->bind_param("i", $partner_id);
    $partner_stmt->execute();
    $partner = $partner_stmt->get_result()->fetch_assoc();

    if ($partner) {
        $messages_stmt = $conn->prepare("SELECT sender_id, message, created_at FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
            ORDER BY created_at ASC");
        $messages_stmt->bind_param("iiii", $user_id, $partner_id, $partner_id, $user_id);
        $messages_stmt->execute();
        $messages = $messages_stmt->get_result();

        
        $update_read_stmt = $conn->prepare("UPDATE messages SET is_read = 1, read_at = NOW() WHERE sender_id = ? AND receiver_id = ?");
        $update_read_stmt->bind_param("ii", $partner_id, $user_id);
        $update_read_stmt->execute();
    }
}
?>

<div class="generic-page-header"><div class="container"><h1>Messages</h1></div></div>

<main class="page-main-content">
  <div class="container">
    <div class="messages-wrapper">
      
      <aside class="contacts-list">
        <?php if ($conversations->num_rows > 0): ?>
          <?php while ($convo = $conversations->fetch_assoc()): ?>
            <div class="contact <?php echo ($convo['partner_id'] == $partner_id) ? 'active' : ''; ?>">
              <a href="conversation.php?partner_id=<?php echo $convo['partner_id']; ?>">
                <span class="contact-name"><?php echo htmlspecialchars($convo['partner_name']); ?></span>
                <span class="last-message"><?php echo htmlspecialchars(substr($convo['message'], 0, 40)) . '...'; ?></span>
              </a>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="padding: 20px;">No conversations yet.</p>
        <?php endif; ?>
      </aside>

      <section class="chat-area">
        <div class="message-list">
          <?php if ($messages && $messages->num_rows > 0): ?>
            <?php while ($msg = $messages->fetch_assoc()):
              $msg_class = ($msg['sender_id'] == $user_id) ? 'message outgoing' : 'message incoming';
            ?>
              <div class="<?php echo $msg_class; ?>">
                <p class="message-text"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                <span class="message-time"><?php echo date('g:i A', strtotime($msg['created_at'])); ?></span>
              </div>
            <?php endwhile; ?>
          <?php elseif ($partner): ?>
            <p>This is the beginning of your conversation with <?php echo htmlspecialchars($partner['username']); ?>.</p>
          <?php else: ?>
            <p style="text-align: center; color: #777;">Select a conversation to start chatting.</p>
          <?php endif; ?>
        </div>

        <?php if ($partner): ?>
        
        <form action="send-message.php" method="POST" class="message-input-form">
            <input type="hidden" name="receiver_id" value="<?php echo $partner_id; ?>">
            <input type="text" name="message_body" placeholder="Type your message..." class="input-text" required>
            <button type="submit" class="btn btn-send">Send</button>
        </form>
        <?php endif; ?>
      </section>

    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>