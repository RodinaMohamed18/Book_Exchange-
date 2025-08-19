<?php

session_start();
include 'includes/db.php';
include 'includes/header.php';


$sql = "SELECT id, title, author, genre, cover_image FROM books WHERE availability = 1";
$params = [];
$types = '';

if (!empty($_GET['search'])) {
  $searchTerm = '%' . $_GET['search'] . '%';
  $sql .= " AND (title LIKE ? OR author LIKE ?)";
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $types .= 'ss';
}
if (!empty($_GET['genre'])) {
  $genre = $_GET['genre'];
  $sql .= " AND genre = ?";
  $params[] = $genre;
  $types .= 's';
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books_result = $stmt->get_result();
?>


<div class="generic-page-header">
  <div class="container">
    <h1>Browse All Books</h1>
  </div>
</div>
<main class="page-main-content">
  <div class="container">
    <div class="browse-layout">
      <aside class="browse-sidebar">
        <h3>Filter & Refine</h3>
        <form class="filter-form" action="book-listings.php" method="GET">
          <div class="filter-group">
            <label for="search-term">Title or Author</label>
            <input type="text" id="search-term" name="search" class="form-control" placeholder="e.g., The Great Gatsby"
              value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
          </div>
          <div class="filter-group">
            <label for="genre-filter">Genre</label>
            <select id="genre-filter" name="genre" class="form-control">
              <option value="">All Genres</option>
              <?php
              $genres_result = $conn->query("SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != '' ORDER BY genre");
              while ($genre_row = $genres_result->fetch_assoc()):
                $selected = ($_GET['genre'] ?? '') === $genre_row['genre'] ? 'selected' : '';
                ?>
                <option <?php echo $selected; ?>><?php echo htmlspecialchars($genre_row['genre']); ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <button type="submit" class="btn" style="width: 100%;">Apply Filters</button>
          <a href="book-listings.php" style="display: block; text-align: center; margin-top: 10px;">Clear Filters</a>
        </form>
      </aside>
      <div class="browse-main-content">
        <div class="book-list">
          <?php if ($books_result->num_rows > 0): ?>
            <?php while ($book = $books_result->fetch_assoc()): ?>
              <article class="book-card">
                <a href="book-details.php?id=<?php echo $book['id']; ?>">


                  <?php
                  if (!empty($book['cover_image'])) {

                    $image_path = '/Book Exchange/uploads/covers/' . htmlspecialchars($book['cover_image']);
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
            <p class="no-results">No books found matching your criteria. Try clearing the filters.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<?php

$stmt->close();
include 'includes/footer.php';
?>