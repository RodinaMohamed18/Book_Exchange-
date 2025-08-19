<?php

session_start();
include 'includes/db.php';

$error_message = '';

if (isset($_SESSION['user_id'])) {
    header("Location: my-books.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error_message = "Both email and password are required.";
    } else {

        $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();


        if ($user && password_verify($password, $user["password_hash"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];


            header("Location: my-books.php");
            exit;
        } else {

            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    }
}


include 'includes/header.php';
?>


<main class="page-main-content">
    <div class="container">
        <div class="auth-section">
            <h2 class="auth-title">Login</h2>


            <form action="login.php" method="POST" class="auth-form">


                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"
                        style="color: red; background: #ffdddd; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>


                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success"
                        style="color: green; background: #ddffdd; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                        Registration successful! Please log in.
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email"
                        required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Login</button>


                <p class="auth-text">Don't have an account? <a href="register.php">Sign up</a></p>
            </form>
        </div>
    </div>
</main>

<?php

include 'includes/footer.php';
?>