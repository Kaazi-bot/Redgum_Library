<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login();

if (is_admin()) {
    redirect('../admin/borrow_requests.php');
}

$pageTitle       = 'My Books';
$pageDescription = 'View your Redgum Community Library borrow requests and their status.';

$stmt = $pdo->prepare(
    "SELECT br.id, br.status, br.request_date, br.due_date, br.return_date,
            b.title, b.author
     FROM borrow_records br
     JOIN books b ON b.id = br.book_id
     WHERE br.user_id = :uid
     ORDER BY br.request_date DESC"
);
$stmt->execute([':uid' => $_SESSION['user_id']]);
$records = $stmt->fetchAll();

include '../includes/header.php';
?>
<div class="container">
    <h1>My Borrowed Books</h1>

    <table>
        <caption class="sr-only">Your borrow request history</caption>
        <thead>
            <tr>
                <th scope="col">Book</th>
                <th scope="col">Author</th>
                <th scope="col">Requested</th>
                <th scope="col">Due</th>
                <th scope="col">Returned</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $r): ?>
            <tr>
                <td><?= clean($r['title']) ?></td>
                <td><?= clean($r['author']) ?></td>
                <td><?= date('d M Y', strtotime($r['request_date'])) ?></td>
                <td><?= $r['due_date'] ? date('d M Y', strtotime($r['due_date'])) : '—' ?></td>
                <td><?= $r['return_date'] ? date('d M Y', strtotime($r['return_date'])) : '—' ?></td>
                <td><span class="badge badge-<?= clean($r['status']) ?>"><?= clean(ucfirst($r['status'])) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($records)): ?>
            <tr><td colspan="6">You have not requested any books yet. <a href="../catalogue.php">Browse the catalogue</a>.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
