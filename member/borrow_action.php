<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login();

if (is_admin()) {
    redirect('../dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../catalogue.php');
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    $_SESSION['flash_error'] = 'Your session expired. Please try again.';
    redirect('../catalogue.php');
}

$book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
if (!$book_id) {
    $_SESSION['flash_error'] = 'Invalid book selected.';
    redirect('../catalogue.php');
}

// Check the book has an available copy (server-side re-check, not just trusting the UI)
$stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = :id");
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch();

if (!$book || (int)$book['available_copies'] < 1) {
    $_SESSION['flash_error'] = 'Sorry, that book is no longer available.';
    redirect('../catalogue.php');
}

// Prevent duplicate active requests for the same book by the same user
$dupe = $pdo->prepare(
    "SELECT id FROM borrow_records WHERE user_id = :uid AND book_id = :bid AND status IN ('requested','approved')"
);
$dupe->execute([':uid' => $_SESSION['user_id'], ':bid' => $book_id]);
if ($dupe->fetch()) {
    $_SESSION['flash_error'] = 'You already have an active request for this book.';
    redirect('../catalogue.php');
}

$insert = $pdo->prepare(
    "INSERT INTO borrow_records (user_id, book_id, request_date, status) VALUES (:uid, :bid, CURDATE(), 'requested')"
);
$insert->execute([':uid' => $_SESSION['user_id'], ':bid' => $book_id]);

$_SESSION['flash_success'] = 'Your borrow request has been submitted. An admin will approve it shortly.';
redirect('../member/my_books.php');
