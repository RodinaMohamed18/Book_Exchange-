<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];
    $cover_image_name = null;


    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $upload_dir = 'uploads/covers/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['cover_image']['tmp_name']);

        if (in_array($file_type, $allowed_types)) {

            $file_extension = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $cover_image_name = uniqid('book_', true) . '.' . $file_extension;
            $upload_path = $upload_dir . $cover_image_name;

            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path)) {
                $error_message = "Failed to save uploaded file.";
                $cover_image_name = null;
            }
        } else {
            $error_message = "Invalid file type. Please upload a JPG, PNG, or GIF.";
        }
    }

    if (empty($error_message) && (!empty($title) && !empty($author))) {

        $stmt = $conn->prepare("INSERT INTO books (user_id, title, author, genre, description, cover_image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user_id, $title, $author, $genre, $description, $cover_image_name);

        if ($stmt->execute()) {
            header("Location: my-books.php?added=true");
            exit();
        } else {
            $error_message = "Database error. Please try again.";
        }
        $stmt->close();
    } elseif (empty($error_message)) {
        $error_message = "Title and Author are required.";
    }
}

include 'includes/header.php';
?>

<main class="add-book-section">
    <div class="container" style="max-width: 800px;">
        <h2 class="section-title">Add a New Book for Exchange</h2>
        <form class="add-book-form" action="add-book.php" method="post" enctype="multipart/form-data">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div class="form-group"><label>Book Title</label><input type="text" name="title" class="form-control"
                    required></div>
            <div class="form-group"><label>Author</label><input type="text" name="author" class="form-control" required>
            </div>
            <div class="form-group"><label>Genre</label><input type="text" name="genre" class="form-control"></div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-control"
                    rows="4"></textarea></div>
            <div class="form-group">
                <label for="cover_image">Book Cover Image (Optional)</label>
                <input type="file" id="cover_image" name="cover_image" class="form-control">
            </div>
            <button type="submit" class="btn">Add Book</button>
        </form>
    </div>
</main>

<?php
include 'includes/footer.php';
?>