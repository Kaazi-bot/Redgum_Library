<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin(); // role-based access control: admin only

$pageTitle       = 'Manage Books';
$pageDescription = 'Admin catalogue management for Redgum Community Library.';

$errors = [];
$edit_book = null;

// ---- DELETE ----
if (isset($_GET['delete'])) {
    if (!csrf_verify($_GET['csrf_token'] ?? null)) {
        $_SESSION['flash_error'] = 'Invalid request.';
    } else {
        $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
        if ($id) {
            $pdo->prepare("DELETE FROM books WHERE id = :id")->execute([':id' => $id]);
            $_SESSION['flash_success'] = 'Book deleted.';
        }
    }
    redirect('books.php');
}

// ---- Load a book for editing ----
if (isset($_GET['edit'])) {
    $id = filter_var($_GET['edit'], FILTER_VALIDATE_INT);
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $edit_book = $stmt->fetch();
}

// ---- CREATE / UPDATE ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['general'] = 'Your session expired. Please try again.';
    } else {
        $id               = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $title            = trim($_POST['title'] ?? '');
        $author           = trim($_POST['author'] ?? '');
        $isbn             = trim($_POST['isbn'] ?? '');
        $category         = trim($_POST['category'] ?? '');
        $description      = trim($_POST['description'] ?? '');
        $cover_image      = trim($_POST['cover_image'] ?? '') ?: 'default-book.jpg';
        $total_copies     = filter_var($_POST['total_copies'] ?? '', FILTER_VALIDATE_INT);
        $available_copies = filter_var($_POST['available_copies'] ?? '', FILTER_VALIDATE_INT);

        if ($title === '' || mb_strlen($title) > 200) {
            $errors['title'] = 'Please enter a valid title (max 200 characters).';
        }
        if ($author === '' || mb_strlen($author) > 150) {
            $errors['author'] = 'Please enter a valid author name.';
        }
        if ($category === '') {
            $errors['category'] = 'Please enter a category.';
        }
        if ($total_copies === false || $total_copies < 0) {
            $errors['total_copies'] = 'Total copies must be a non-negative number.';
        }
        if ($available_copies === false || $available_copies < 0) {
            $errors['available_copies'] = 'Available copies must be a non-negative number.';
        } elseif ($total_copies !== false && $available_copies > $total_copies) {
            $errors['available_copies'] = 'Available copies cannot exceed total copies.';
        }

        if (empty($errors)) {
            if ($id) {
                // UPDATE
                $stmt = $pdo->prepare(
                    "UPDATE books SET title=:title, author=:author, isbn=:isbn, category=:category,
                     description=:description, cover_image=:cover_image, total_copies=:total_copies,
                     available_copies=:available_copies WHERE id=:id"
                );
                $stmt->execute([
                    ':title' => $title, ':author' => $author, ':isbn' => $isbn, ':category' => $category,
                    ':description' => $description, ':cover_image' => $cover_image,
                    ':total_copies' => $total_copies, ':available_copies' => $available_copies, ':id' => $id,
                ]);
                $_SESSION['flash_success'] = 'Book updated successfully.';
            } else {
                // CREATE
                $stmt = $pdo->prepare(
                    "INSERT INTO books (title, author, isbn, category, description, cover_image, total_copies, available_copies)
                     VALUES (:title, :author, :isbn, :category, :description, :cover_image, :total_copies, :available_copies)"
                );
                $stmt->execute([
                    ':title' => $title, ':author' => $author, ':isbn' => $isbn, ':category' => $category,
                    ':description' => $description, ':cover_image' => $cover_image,
                    ':total_copies' => $total_copies, ':available_copies' => $available_copies,
                ]);
                $_SESSION['flash_success'] = 'Book added successfully.';
            }
            redirect('books.php');
        }
    }
}

$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();

include '../includes/header.php';
?>
<div class="container">
    <h1>Manage Book Catalogue</h1>

    <h2><?= $edit_book ? 'Edit Book' : 'Add a New Book' ?></h2>
    <?php if (!empty($errors['general'])): ?><p class="field-error"><?= clean($errors['general']) ?></p><?php endif; ?>

    <form method="post" action="books.php" class="stack" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <?php if ($edit_book): ?><input type="hidden" name="id" value="<?= (int)$edit_book['id'] ?>"><?php endif; ?>

        <div class="form-group">
            <label for="title">Title <span class="required-mark">*</span></label>
            <input type="text" id="title" name="title" required maxlength="200"
                   value="<?= clean($edit_book['title'] ?? ($_POST['title'] ?? '')) ?>">
            <?php if (!empty($errors['title'])): ?><p class="field-error"><?= clean($errors['title']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="author">Author <span class="required-mark">*</span></label>
            <input type="text" id="author" name="author" required maxlength="150"
                   value="<?= clean($edit_book['author'] ?? ($_POST['author'] ?? '')) ?>">
            <?php if (!empty($errors['author'])): ?><p class="field-error"><?= clean($errors['author']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" maxlength="20"
                   value="<?= clean($edit_book['isbn'] ?? ($_POST['isbn'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label for="category">Category <span class="required-mark">*</span></label>
            <input type="text" id="category" name="category" required maxlength="80"
                   value="<?= clean($edit_book['category'] ?? ($_POST['category'] ?? '')) ?>">
            <?php if (!empty($errors['category'])): ?><p class="field-error"><?= clean($errors['category']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= clean($edit_book['description'] ?? ($_POST['description'] ?? '')) ?></textarea>
        </div>

        <div class="form-group">
            <label for="cover_image">Cover image filename</label>
            <input type="text" id="cover_image" name="cover_image" placeholder="e.g. hobbit.jpg"
                   value="<?= clean($edit_book['cover_image'] ?? ($_POST['cover_image'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label for="total_copies">Total Copies <span class="required-mark">*</span></label>
            <input type="text" id="total_copies" name="total_copies" required inputmode="numeric"
                   value="<?= clean($edit_book['total_copies'] ?? ($_POST['total_copies'] ?? '1')) ?>">
            <?php if (!empty($errors['total_copies'])): ?><p class="field-error"><?= clean($errors['total_copies']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="available_copies">Available Copies <span class="required-mark">*</span></label>
            <input type="text" id="available_copies" name="available_copies" required inputmode="numeric"
                   value="<?= clean($edit_book['available_copies'] ?? ($_POST['available_copies'] ?? '1')) ?>">
            <?php if (!empty($errors['available_copies'])): ?><p class="field-error"><?= clean($errors['available_copies']) ?></p><?php endif; ?>
        </div>

        <button type="submit" class="btn"><?= $edit_book ? 'Update Book' : 'Add Book' ?></button>
        <?php if ($edit_book): ?><a href="books.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
    </form>

    <h2>All Books</h2>
    <table>
        <caption class="sr-only">List of all books in the catalogue</caption>
        <thead>
            <tr><th scope="col">Title</th><th scope="col">Author</th><th scope="col">Category</th>
                <th scope="col">Copies</th><th scope="col">Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($books as $b): ?>
            <tr>
                <td><?= clean($b['title']) ?></td>
                <td><?= clean($b['author']) ?></td>
                <td><?= clean($b['category']) ?></td>
                <td><?= (int)$b['available_copies'] ?> / <?= (int)$b['total_copies'] ?></td>
                <td>
                    <a href="books.php?edit=<?= (int)$b['id'] ?>">Edit</a> &middot;
                    <a href="books.php?delete=<?= (int)$b['id'] ?>&csrf_token=<?= csrf_token() ?>"
                       onclick="return confirm('Delete this book? This cannot be undone.');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
