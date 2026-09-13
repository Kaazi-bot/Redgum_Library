<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle       = 'Catalogue';
$pageDescription = 'Search the Redgum Community Library book catalogue by title, author or category.';

// ---- Search / filter form (Form #1: GET, validated & sanitised) ----
$search   = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$errors = [];
if (mb_strlen($search) > 100) {
    $errors[] = 'Search text is too long.';
    $search = mb_substr($search, 0, 100);
}

$sql    = "SELECT * FROM books WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (title LIKE :search1 OR author LIKE :search2)";
    $params[':search1'] = '%' . $search . '%';
    $params[':search2'] = '%' . $search . '%';
}
if ($category !== '' && $category !== 'All') {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}
$sql .= " ORDER BY title ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM books ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';
?>
<div class="container">
    <h1>Library Catalogue</h1>

    <?php foreach ($errors as $e): ?>
        <p class="field-error"><?= clean($e) ?></p>
    <?php endforeach; ?>

    <form method="get" action="catalogue.php" class="stack" role="search" aria-label="Search catalogue">
        <div class="form-group">
            <label for="q">Search by title or author</label>
            <input type="text" id="q" name="q" value="<?= clean($search) ?>" maxlength="100" placeholder="e.g. The Hobbit">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="All">All categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= clean($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= clean($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn">Search</button>
    </form>

    <p><?= count($books) ?> book(s) found.</p>

    <div class="grid">
        <?php foreach ($books as $b): ?>
            <div class="card">
               <?php
    $cover = !empty($b['isbn'])
        ? "https://covers.openlibrary.org/b/isbn/" . urlencode($b['isbn']) . "-M.jpg"
        : "images/books/" . clean($b['cover_image']);
?>
<img src="<?= $cover ?>" alt="Cover of <?= clean($b['title']) ?>" loading="lazy"
     onerror="this.onerror=null; this.src='images/books/default-book.jpg'">
                <h3><?= clean($b['title']) ?></h3>
                <p><em>by <?= clean($b['author']) ?></em> &mdash; <?= clean($b['category']) ?></p>
                <p><?= clean($b['description']) ?></p>
                <p><strong>Available:</strong> <?= (int)$b['available_copies'] ?> / <?= (int)$b['total_copies'] ?></p>

                <?php if (is_logged_in() && !is_admin()): ?>
                    <?php if ((int)$b['available_copies'] > 0): ?>
                        <form method="post" action="member/borrow_action.php">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                            <input type="hidden" name="action" value="request">
                            <button type="submit" class="btn">Request to Borrow</button>
                        </form>
                    <?php else: ?>
                        <p><em>Currently unavailable.</em></p>
                    <?php endif; ?>
                <?php elseif (!is_logged_in()): ?>
                    <p><a href="login.php">Log in</a> to request this book.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if (empty($books)): ?>
            <p>No books matched your search.</p>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
