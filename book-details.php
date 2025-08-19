<?php

session_start();
include 'includes/db.php';
include 'includes/header.php';


if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}


$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$stmt = $conn->prepare("
    SELECT 
        b.id, b.title, b.author, b.genre, b.description, b.cover_image, 
        u.username as owner_name, b.user_id as owner_id 
    FROM books b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.id = ? AND b.availability = 1
");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();


if (!$book) {
  echo "<main class='page-main-content'><div class='container'><p class='alert alert-danger'>Book not found or is currently unavailable.</p></div></main>";
} else {
  ?>

  <main class="book-details-section">
    <div class="container book-details-container">

      <div class="book-image-wrapper">

        <?php

        if (!empty($book['cover_image'])) {

          $image_path = '/Book Exchange/uploads/covers/' . htmlspecialchars($book['cover_image']);
        } else {

          $image_path = 'https://via.placeholder.com/400x600.png?text=' . urlencode($book['title']);
        }
        ?>
        <img src="<?php echo $image_path; ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>"
          class="book-details-image">
      </div>

      <div class="book-info-wrapper">
        <h2 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h2>
        <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
        <p class="book-genre">Genre: <?php echo htmlspecialchars($book['genre']); ?></p>
        <p class="book-description">
          <?php echo nl2br(htmlspecialchars($book['description'])); ?>
        </p>

        <div class="owner-contact-section">
          <p><strong>Owner:</strong> <a
              href="profile.php?id=<?php echo $book['owner_id']; ?>"><?php echo htmlspecialchars($book['owner_name']); ?></a>
          </p>
          <p><strong>Status:</strong> Available for Exchange</p>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px; align-items: center;">
          <?php
          // Check if the current user is the owner of the book
          if ($_SESSION['user_id'] != $book['owner_id']) {


            $req_stmt = $conn->prepare("SELECT id FROM exchanges WHERE book_id = ? AND requester_id = ? AND status = 'pending'");
            $req_stmt->bind_param("ii", $book_id, $_SESSION['user_id']);
            $req_stmt->execute();
            $req_result = $req_stmt->get_result();

            if ($req_result->num_rows > 0) {
              echo '<p class="alert alert-info" style="margin:0;">Exchange request sent.</p>';
            } else {
              ?>
              <form action="handle_exchange_request.php" method="post" style="margin:0;">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                <input type="hidden" name="owner_id" value="<?php echo $book['owner_id']; ?>">
                <button type="submit" name="request_exchange" class="btn btn-primary">Request Exchange</button>
              </form>
              <?php
            }
            $req_stmt->close();


            ?>
            <a href="conversation.php?partner_id=<?php echo $book['owner_id']; ?>" class="btn btn-secondary">Message
              Owner</a>

            <?php
          } else {
            echo '<p class="alert alert-secondary" style="margin:0;">This is one of your listed books.</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </main>

  <?php
}
$stmt->close();
include 'includes/footer.php';
?>