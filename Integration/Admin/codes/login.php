<?php
require_once 'config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Username and password are required.';
    } else {
        $row = db_fetch_one($mysqli, 'SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1', 's', [$username]);
        if ($row && password_verify($password, $row['password_hash'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid credentials.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="assets/style.css">
  <script
  src="https://www.tuqlas.com/chatbot.js"
  data-key="tq_live_a44ec16671bf6a95088dd8aaf009222acb448c0a"
  data-api="https://www.tuqlas.com"
  defer
></script>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="login-title">Admin Login</div>
      <div class="small">Use your admin credentials to continue.</div>

      <?php if (!empty($errors)) : ?>
        <div class="error">
          <?php foreach ($errors as $error) : ?>
            <div><?php echo e($error); ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" class="form" style="border:none; padding:0; max-width:none;">
        <div class="form-row">
          <label for="username">Username</label>
          <input id="username" name="username" type="text" required value="<?php echo e($_POST['username'] ?? ''); ?>">
        </div>
        <div class="form-row">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required>
        </div>
        <div class="form-actions">
          <button class="btn" type="submit">Sign In</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
