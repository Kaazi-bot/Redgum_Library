<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle       = 'Login';
$pageDescription = 'Log in to your Redgum Community Library member account.';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['general'] = 'Your session expired. Please try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $errors['general'] = 'Please enter both your email and password.';
        } else {
            $stmt = $pdo->prepare("SELECT id, full_name, email, password_hash, role FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            // Same generic message whether the email exists or not (avoid user enumeration)
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors['general'] = 'Incorrect email or password.';
            } else {
                session_regenerate_id(true); // prevent session fixation
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                $_SESSION['flash_success'] = 'Welcome back, ' . $user['full_name'] . '!';
                redirect('dashboard.php');
            }
        }
    }
}

include 'includes/header.php';
?>
<div class="container">
    <h1>Member Login</h1>

    <?php if (!empty($errors['general'])): ?>
        <p class="field-error"><?= clean($errors['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="login.php" class="stack" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="form-group">
            <label for="email">Email Address <span class="required-mark">*</span></label>
            <input type="email" id="email" name="email" value="<?= clean($email) ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password <span class="required-mark">*</span></label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <p>New here? <a href="register.php">Create a free account</a>.</p>
    <p style="font-size:0.85rem;color:#666;">Demo admin login: admin@redgum.local / Admin@123</p>
</div>
<?php include 'includes/footer.php'; ?>
