<?php

session_start();
include 'includes/db.php';
include 'includes/header.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';


$my_books_stmt = $conn->prepare("SELECT id, title, author, availability, cover_image FROM books WHERE user_id = ? ORDER BY created_at DESC");
$my_books_stmt->bind_param("i", $user_id);
$my_books_stmt->execute();
$my_books_result = $my_books_stmt->get_result();
?>


<div class="generic-page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($username); ?>'s Dashboard</h1>
    </div>
</div>

<main class="page-main-content">
    <div class="container my-books-container">

        <div style="text-align: center; margin-bottom: 40px;">
            <a href="add-book.php" class="btn">+ Add New Book</a>
        </div>

        <hr>
        <h3 class="section-title">My Book Listings & Incoming Requests</h3>


        <div class="book-list my-books-list">
            <?php if ($my_books_result->num_rows > 0): ?>
                <?php while ($book = $my_books_result->fetch_assoc()): ?>
                    <article class="book-card">
                        <?php
                        if (!empty($book['cover_image'])) {
                            $image_path = '/Book Exchange/uploads/covers/' . htmlspecialchars($book['cover_image']);
                        } else {
                            $image_path = 'https://via.placeholder.com/200x300.png?text=' . urlencode($book['title']);
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>" />

                        <div class="book-info">
                            <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>

                            <div class="status-section">
                                <?php
                                $requests_stmt = $conn->prepare("SELECT e.id, u.username as requester_name FROM exchanges e JOIN users u ON e.requester_id = u.id WHERE e.book_id = ? AND e.status = 'pending'");
                                $requests_stmt->bind_param("i", $book['id']);
                                $requests_stmt->execute();
                                $requests_result = $requests_stmt->get_result();

                                if ($requests_result->num_rows > 0) {
                                    echo '<p><strong>Pending Requests:</strong></p><ul class="list-unstyled">';
                                    while ($request = $requests_result->fetch_assoc()) {
                                        ?>
                                        <li>
                                            From <strong><?php echo htmlspecialchars($request['requester_name']); ?></strong>
                                            <form action="handle_exchange_request.php" method="post" class="mt-2">
                                                <input type="hidden" name="exchange_id" value="<?php echo $request['id']; ?>">
                                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                                <input type="hidden" name="manage_request" value="1">
                                                <button type="submit" name="action" value="approve"
                                                    class="btn btn-success btn-sm">Approve</button>
                                                <button type="submit" name="action" value="reject"
                                                    class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        </li>
                                        <?php
                                    }
                                    echo '</ul>';
                                } else {
                                    $status_color = $book['availability'] ? 'green' : 'red';
                                    $status_text = $book['availability'] ? 'Available' : 'Unavailable';
                                    echo "<p>Status: <span style='color: {$status_color}; font-weight: bold;'>{$status_text}</span></p>";
                                }
                                $requests_stmt->close();
                                ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You haven't listed any books yet. Click the button above to add one!</p>
            <?php endif; ?>
        </div>

        <hr style="margin-top: 40px; margin-bottom: 40px;">

        <h3 class="section-title">My Outgoing Exchange Requests</h3>
        <div class="outgoing-requests-list">
            <ul class="list-group">
                <?php

                $outgoing_stmt = $conn->prepare("SELECT e.id as exchange_id, e.status, b.title, u.username as owner_name FROM exchanges e JOIN books b ON e.book_id = b.id JOIN users u ON e.owner_id = u.id WHERE e.requester_id = ? ORDER BY e.created_at DESC");
                $outgoing_stmt->bind_param("i", $user_id);
                $outgoing_stmt->execute();
                $outgoing_result = $outgoing_stmt->get_result();

                if ($outgoing_result->num_rows > 0):
                    while ($req = $outgoing_result->fetch_assoc()):
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                Request for <strong><?php echo htmlspecialchars($req['title']); ?></strong> (from
                                <?php echo htmlspecialchars($req['owner_name']); ?>)
                                <br><small>Status: <span
                                        class="badge badge-info"><?php echo htmlspecialchars(ucfirst($req['status'])); ?></span></small>
                            </div>
                            <div>
                                <?php if ($req['status'] == 'approved'): ?>
                                    <form action="handle_exchange_request.php" method="post" style="display: inline-block;">
                                        <input type="hidden" name="exchange_id" value="<?php echo $req['exchange_id']; ?>">
                                        <button type="submit" name="complete_exchange" class="btn btn-primary btn-sm">Mark as
                                            Completed</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($req['status'] == 'completed'):
                                    $review_check_stmt = $conn->prepare("SELECT id FROM ratings WHERE exchange_id = ? AND reviewer_id = ?");
                                    $review_check_stmt->bind_param("ii", $req['exchange_id'], $user_id);
                                    $review_check_stmt->execute();
                                    if ($review_check_stmt->get_result()->num_rows == 0):
                                        ?>
                                        <a href="leave_review.php?exchange_id=<?php echo $req['exchange_id']; ?>"
                                            class="btn btn-warning btn-sm">Leave a Review</a>
                                    <?php
                                    endif;
                                    $review_check_stmt->close();
                                endif; ?>
                            </div>
                        </li>
                        <?php
                    endwhile;
                else:
                    echo "<li class='list-group-item'>You have not made any exchange requests.</li>";
                endif;
                $outgoing_stmt->close();
                ?>
            </ul>
        </div>
    </div>
</main>

<?php
$my_books_stmt->close();
include 'includes/footer.php';
?>