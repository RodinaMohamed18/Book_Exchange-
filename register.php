<?php
session_start();
include 'includes/db.php';


mysqli_report(MYSQLI_REPORT_OFF);

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST["username"]);
  $email = trim($_POST["email"]);
  $password = $_POST["password"];
  $confirm_password = $_POST["confirm-password"];


  if (empty($username) || empty($email) || empty($password)) {
    $error_message = "All fields are required.";
  } elseif ($password !== $confirm_password) {
    $error_message = "Passwords do not match.";
  } else {

    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
      $error_message = "This username or email is already taken.";
    } else {

      $passCheck = $conn->prepare("SELECT password_hash FROM users");
      $passCheck->execute();
      $resultPass = $passCheck->get_result();

      $duplicatePassword = false;
      while ($row = $resultPass->fetch_assoc()) {
        if (password_verify($password, $row['password_hash'])) {
          $duplicatePassword = true;
          break;
        }
      }
      $passCheck->close();

      if ($duplicatePassword) {
        $error_message = "This password is already used by another user. Please choose a different one.";
      } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
          header("Location: login.php?registered=true");
          exit();
        } else {
          $error_message = "Registration failed. Please try again.";
        }
        $stmt->close();
      }
    }
    $check->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Book Exchange</title>
  <link rel="stylesheet" href="assets/style.css">
</head>

<body class="page-register">

  <?php include 'includes/header.php'; ?>

  <main class="auth-section">
    <div class="auth-card">
      <h2 class="auth-title">Create an Account</h2>

      <form action="register.php" method="POST" class="auth-form">

        <?php if (!empty($error_message)): ?>
          <div class="alert alert-danger">
            <?php echo $error_message; ?>
          </div>
        <?php endif; ?>

        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username"
            required>
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password"
            required>
        </div>
        <div class="form-group">
          <label for="confirm-password">Confirm Password:</label>
          <input type="password" id="confirm-password" name="confirm-password" class="form-control"
            placeholder="Re-enter your password" required>
        </div>
        <button type="submit" class="btn" style="width: 100%;">Sign Up</button>
        <p class="auth-text">Already have an account? <a href="login.php">Login</a></p>
      </form>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

</body>

</html>