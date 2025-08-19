<?php
session_start();
include 'includes/db.php';


if (!isset($_SESSION['user_id'])) {

    header('Location: login.php');
    exit();
}


$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];


if (isset($_POST['request_exchange'])) {
    $book_id = intval($_POST['book_id']);
    $owner_id = intval($_POST['owner_id']);


    $book_stmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
    $book_stmt->bind_param("i", $book_id);
    $book_stmt->execute();
    $book_title_result = $book_stmt->get_result()->fetch_assoc();
    $book_title = $book_title_result ? $book_title_result['title'] : 'a book';
    $book_stmt->close();

    $stmt = $conn->prepare("INSERT INTO exchanges (book_id, requester_id, owner_id, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iii", $book_id, $current_user_id, $owner_id);

    if ($stmt->execute()) {

        $message = htmlspecialchars($current_username) . " has requested your book: '" . htmlspecialchars($book_title) . "'.";
        $link = "my-books.php";
        $notify_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
        $notify_stmt->bind_param("iss", $owner_id, $message, $link);
        $notify_stmt->execute();
        $notify_stmt->close();


        header('Location: book-details.php?id=' . $book_id . '&success=requested');
    } else {

        header('Location: book-details.php?id=' . $book_id . '&error=dberror');
    }
    $stmt->close();
    exit();
}


if (isset($_POST['manage_request'])) {
    $exchange_id = intval($_POST['exchange_id']);
    $action = $_POST['action'];
    $book_id = intval($_POST['book_id']);


    $verify_stmt = $conn->prepare("SELECT owner_id, requester_id FROM exchanges WHERE id = ?");
    $verify_stmt->bind_param("i", $exchange_id);
    $verify_stmt->execute();
    $exchange = $verify_stmt->get_result()->fetch_assoc();


    if ($exchange && $exchange['owner_id'] == $current_user_id) {
        $new_status = ($action == 'approve') ? 'approved' : 'rejected';
        $requester_id = $exchange['requester_id'];


        $update_stmt = $conn->prepare("UPDATE exchanges SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_status, $exchange_id);
        $update_stmt->execute();
        $update_stmt->close();


        if ($new_status == 'approved') {
            $book_update_stmt = $conn->prepare("UPDATE books SET availability = 0 WHERE id = ?");
            $book_update_stmt->bind_param("i", $book_id);
            $book_update_stmt->execute();
            $book_update_stmt->close();
        }


        $book_title_stmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
        $book_title_stmt->bind_param("i", $book_id);
        $book_title_stmt->execute();
        $book_title = $book_title_stmt->get_result()->fetch_assoc()['title'];

        $message = "Your request for '" . htmlspecialchars($book_title) . "' has been " . $new_status . ".";
        $link = "my-books.php";
        $notify_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
        $notify_stmt->bind_param("iss", $requester_id, $message, $link);
        $notify_stmt->execute();
        $notify_stmt->close();
    }

    header('Location: my-books.php?managed=true');
    exit();
}


if (isset($_POST['complete_exchange'])) {
    $exchange_id = intval($_POST['exchange_id']);


    $verify_stmt = $conn->prepare("SELECT requester_id, owner_id, book_id FROM exchanges WHERE id = ? AND status = 'approved'");
    $verify_stmt->bind_param("i", $exchange_id);
    $verify_stmt->execute();
    $exchange = $verify_stmt->get_result()->fetch_assoc();


    if ($exchange && $exchange['requester_id'] == $current_user_id) {

        $update_stmt = $conn->prepare("UPDATE exchanges SET status = 'completed' WHERE id = ?");
        $update_stmt->bind_param("i", $exchange_id);
        $update_stmt->execute();
        $update_stmt->close();


        $owner_id = $exchange['owner_id'];
        $book_id = $exchange['book_id'];

        $book_title_stmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
        $book_title_stmt->bind_param("i", $book_id);
        $book_title_stmt->execute();
        $book_title = $book_title_stmt->get_result()->fetch_assoc()['title'];

        $message = "The exchange for '" . htmlspecialchars($book_title) . "' with " . htmlspecialchars($current_username) . " has been marked as complete.";
        $link = "profile.php?id=" . $owner_id;
        $notify_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
        $notify_stmt->bind_param("iss", $owner_id, $message, $link);
        $notify_stmt->execute();
        $notify_stmt->close();
    }

    header('Location: my-books.php?status=completed');
    exit();
}


header('Location: index.php');
exit();
?>