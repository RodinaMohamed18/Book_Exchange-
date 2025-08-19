<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];


$success_message = '';
$error_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);


    if (!empty($username) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, address = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $username, $email, $full_name, $address, $phone, $user_id);
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $success_message = "Profile details updated successfully!";
        } else {
            $error_message = "Username or email may already be in use.";
        }
        $stmt->close();
    } else {
        $error_message = "Username and Email are required.";
    }


    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    if (!empty($new_password)) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $pw_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $pw_stmt->bind_param("si", $hashed_password, $user_id);
            $pw_stmt->execute();
            $pw_stmt->close();

            $success_message .= " Password changed successfully!";
        } else {

            $error_message .= " Passwords do not match.";
        }
    }
}


$user_stmt = $conn->prepare("SELECT username, email, full_name, address, phone FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

include 'includes/header.php';
?>

<div class="generic-page-header">
    <div class="container">
        <h1>Edit Profile</h1>
    </div>
</div>

<main class="page-main-content">
    <div class="container" style="max-width: 800px;">
        <form class="edit-profile-form" action="edit-profile.php" method="post">


            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div><?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div><?php endif; ?>

            <h3 class="section-subtitle">Personal Information</h3>
            <div class="form-group"><label for="full_name">Full Name</label><input type="text" id="full_name"
                    name="full_name" class="form-control"
                    value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"></div>
            <div class="form-group"><label for="address">Address</label><input type="text" id="address" name="address"
                    class="form-control" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>"></div>
            <div class="form-group"><label for="phone">Phone Number</label><input type="tel" id="phone" name="phone"
                    class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"></div>

            <hr>
            <h3 class="section-subtitle">Account Details</h3>
            <div class="form-group"><label for="username">Username</label><input type="text" id="username"
                    name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>"
                    required></div>
            <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email"
                    class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>

            <hr>
            <h3 class="section-subtitle">Change Password</h3>
            <p><small>Leave blank if you don't want to change your password.</small></p>
            <div class="form-group"><label for="new_password">New Password</label><input type="password"
                    id="new_password" name="new_password" class="form-control"></div>
            <div class="form-group"><label for="confirm_password">Confirm New Password</label><input type="password"
                    id="confirm_password" name="confirm_password" class="form-control"></div>

            <button type="submit" class="btn">Save Changes</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</main>

<?php
$user_stmt->close();
include 'includes/footer.php';
?>