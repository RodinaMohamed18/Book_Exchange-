<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$featured_books_stmt = $conn->prepare("SELECT id, title, author, cover_image FROM books WHERE availability = 1 ORDER BY created_at DESC LIMIT 4");
$featured_books_stmt->execute();
$featured_books_result = $featured_books_stmt->get_result();
?>

<main>

  <section class="hero-section">
    <div class="container hero-container">
      <div class="hero-content">
        <span class="subtitle">Start Exchanging</span>
        <h1>You're Only One Book Away From a Good Mood</h1>
        <p>Find your next favorite book. List your own books and exchange them with a community of readers.</p>
        <a href="book-listings.php" class="btn">Discover Books</a>
      </div>
      <div class="hero-books">
        <div class="book-grid-small">
          <article class="book-card-sm"><img
              src="uploads/covers/Moby Dick by Herman Melville_ The Original Classic Hardcover - A Riveting Exploration of One Man’s Obsession and Nature’s Unforgiving Power.jpeg"
              alt="Book cover" /></article>
          <article class="book-card-sm"><img src="uploads/covers/Such a beautiful story!.jpeg" alt="Book cover" />
          </article>
          <article class="book-card-sm"><img src="uploads/covers/The Catcher in the Rye.jpeg" alt="Book cover" />
          </article>
        </div>
      </div>
    </div>
  </section>


  <section class="featured-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Discover Your New Book</h2>
        <p>Explore some of the great books recently added by our community.</p>
      </div>
      <div class="book-list">
        <?php if ($featured_books_result && $featured_books_result->num_rows > 0): ?>
          <?php while ($book = $featured_books_result->fetch_assoc()): ?>
            <article class="book-card">
              <a href="book-details.php?id=<?php echo $book['id']; ?>">
                <?php
                if (!empty($book['cover_image'])) {
                  $image_path = 'uploads/covers/' . htmlspecialchars($book['cover_image']);
                } else {
                  $image_path = 'https://via.placeholder.com/300x450.png?text=' . urlencode($book['title']);
                }
                ?>
                <img src="<?php echo $image_path; ?>" alt="Cover for <?php echo htmlspecialchars($book['title']); ?>" />
              </a>
              <div class="book-info">
                <h3 class="book-title"><a
                    href="book-details.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a>
                </h3>
                <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
              </div>
            </article>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No books have been added yet. Be the first!</p>
        <?php endif; ?>
        <?php $featured_books_stmt->close(); ?>
      </div>
      <div class="section-footer">
        <a href="book-listings.php" class="btn">Discover More Books</a>
      </div>
    </div>
  </section>

  <section class="community-section">
    <div class="container community-container">
      <div class="community-content">
        <h2>Join Book Lovers Community and Get Latest Updates</h2>
        <form class="subscribe-form" action="#">
          <input type="email" placeholder="Your Email Address">
          <button type="submit" class="btn">Subscribe</button>
        </form>
      </div>
      <div class="community-image">
        <img src="uploads/covers/stack of books.jpeg" alt="Stack of books">
      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>