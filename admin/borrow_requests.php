<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$pageTitle       = 'Borrow Requests';
$pageDescription = 'Admin panel for managing member borrow requests.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $_SESSION['flash_error'] = 'Your session expired. Please try again.';
        redirect('borrow_requests.php');
    }

    $record_id = filter_input(INPUT_POST, 'record_id', FILTER_VALIDATE_INT);
    $action    = $_POST['action'] ?? '';

    if ($record_id && in_array($action, ['approve', 'reject', 'return'], true)) {
        $stmt = $pdo->prepare("SELECT * FROM borrow_records WHERE id = :id");
        $stmt->execute([':id' => $record_id]);
        $record = $stmt->fetch();

        if ($record) {
            $pdo->beginTransaction();
            try {
                if ($action === 'approve' && $record['status'] === 'requested') {
                    $pdo->prepare("UPDATE borrow_records SET status='approved', due_date = DATE_ADD(CURDATE(), INTERVAL 14 DAY) WHERE id = :id")
                        ->execute([':id' => $record_id]);
                    $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = :bid AND available_copies > 0")
                        ->execute([':bid' => $record['book_id']]);
                    $_SESSION['flash_success'] = 'Request approved. Due in 14 days.';
                } elseif ($action === 'reject' && $record['status'] === 'requested') {
                    $pdo->prepare("UPDATE borrow_records SET status='rejected' WHERE id = :id")
                        ->execute([':id' => $record_id]);
                    $_SESSION['flash_success'] = 'Request rejected.';
                } elseif ($action === 'return' && $record['status'] === 'approved') {
                    $pdo->prepare("UPDATE borrow_records SET status='returned', return_date = CURDATE() WHERE id = :id")
                        ->execute([':id' => $record_id]);
                    $pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = :bid")
                        ->execute([':bid' => $record['book_id']]);
                    $_SESSION['flash_success'] = 'Book marked as returned.';
                }
                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log('Borrow action failed: ' . $e->getMessage());
                $_SESSION['flash_error'] = 'Something went wrong. Please try again.';
            }
        }
    }
    redirect('borrow_requests.php');
}

$records = $pdo->query(
    "SELECT br.id, br.status, br.request_date, br.due_date, br.return_date,
            b.title AS book_title, u.full_name, u.email
     FROM borrow_records br
     JOIN books b ON b.id = br.book_id
     JOIN users u ON u.id = br.user_id
     ORDER BY FIELD(br.status,'requested','approved','returned','rejected'), br.request_date DESC"
)->fetchAll();

include '../includes/header.php';
?>
<div class="container">
    <h1>Borrow Requests</h1>

    <table>
        <caption class="sr-only">All member borrow requests</caption>
        <thead>
            <tr>
                <th scope="col">Member</th><th scope="col">Book</th><th scope="col">Requested</th>
                <th scope="col">Due</th><th scope="col">Status</th><th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $r): ?>
            <tr>
                <td><?= clean($r['full_name']) ?><br><small><?= clean($r['email']) ?></small></td>
                <td><?= clean($r['book_title']) ?></td>
                <td><?= date('d M Y', strtotime($r['request_date'])) ?></td>
                <td><?= $r['due_date'] ? date('d M Y', strtotime($r['due_date'])) : '—' ?></td>
                <td><span class="badge badge-<?= clean($r['status']) ?>"><?= clean(ucfirst($r['status'])) ?></span></td>
                <td>
                    <?php if ($r['status'] === 'requested'): ?>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="record_id" value="<?= (int)$r['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn">Approve</button>
                        </form>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="record_id" value="<?= (int)$r['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-secondary">Reject</button>
                        </form>
                    <?php elseif ($r['status'] === 'approved'): ?>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="record_id" value="<?= (int)$r['id'] ?>">
                            <input type="hidden" name="action" value="return">
                            <button type="submit" class="btn">Mark Returned</button>
                        </form>
                    <?php else: ?>
                        &mdash;
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($records)): ?>
            <tr><td colspan="6">No borrow requests yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
