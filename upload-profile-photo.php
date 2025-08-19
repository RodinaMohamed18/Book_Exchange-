<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $upload_dir = 'uploads/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $file_type = mime_content_type($_FILES['profile_image']['tmp_name']);

    if (in_array($file_type, $allowed_types)) {
        $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $profile_image_name = uniqid('user_', true) . '.' . $file_extension;
        $upload_path = $upload_dir . $profile_image_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {

            $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $profile_image_name, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}
header('Location: profile.php');
exit();
?>