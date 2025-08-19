<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: messages.php');
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message_body = isset($_POST['message_body']) ? trim($_POST['message_body']) : '';
$book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : null;

if ($receiver_id <= 0 || empty($message_body)) {
    header('Location: conversation.php?partner_id=' . $receiver_id . '&error=empty');
    exit();
}

if ($sender_id === $receiver_id) {
    header('Location: messages.php?error=self');
    exit();
}

$stmt_insert = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, book_id, message) VALUES (?, ?, ?, ?)");
$stmt_insert->bind_param("iiis", $sender_id, $receiver_id, $book_id, $message_body);

if ($stmt_insert->execute()) {
    $sender_username = $_SESSION['username'] ?? 'Someone';
    $notification_message = "You have a new message from " . htmlspecialchars($sender_username);
    $link = "conversation.php?partner_id=" . $sender_id;

    $notify_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
    $notify_stmt->bind_param("iss", $receiver_id, $notification_message, $link);
    $notify_stmt->execute();
    $notify_stmt->close();
}

$stmt_insert->close();
$conn->close();

header('Location: conversation.php?partner_id=' . $receiver_id);
exit();
