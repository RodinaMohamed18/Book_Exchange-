<?php
session_start();

include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewer_id = $_SESSION['user_id'];
    $exchange_id = intval($_POST['exchange_id']);
    $reviewed_user_id = intval($_POST['reviewed_user_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    if ($rating < 1 || $rating > 5) {
        header('Location: leave_review.php?exchange_id=' . $exchange_id . '&error=invalidrating');
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO ratings (exchange_id, reviewer_id, reviewed_user_id, rating, review) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $exchange_id, $reviewer_id, $reviewed_user_id, $rating, $review);
    
    if ($stmt->execute()) {
       
        header('Location: profile.php?id=' . $reviewed_user_id . '&review=success');
    } else {
        header('Location: leave_review.php?exchange_id=' . $exchange_id . '&error=dberror');
    }
    $stmt->close();
} else {
    header('Location: index.php');
}
$conn->close();
?>