<?php
require 'config.php';

if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail = strtolower(trim($_POST['gmail'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, gmail, password FROM users WHERE gmail = ?');
    $stmt->bind_param('s', $gmail);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['gmail']   = $user['gmail'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid Gmail or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | AssetVault</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body login-bg">
  <div class="auth-box">
    <img src="assets/logo.png" alt="AssetVault Logo" class="auth-logo" onerror="this.style.display='none'">
    <h1 class="auth-title">ASSETVAULT</h1>
    <p class="auth-sub">Sign in to continue</p>

    <?php
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert alert-' . e($f['type']) . '">' . e($f['msg']) . '</div>';
        unset($_SESSION['flash']);
    }
    if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form method="post">
      <label>Gmail</label>
      <input type="email" name="gmail" placeholder="yourname@gmail.com" value="<?= e($_POST['gmail'] ?? '') ?>" required>

      <label>Password</label>
      <div class="password-wrap">
        <input type="password" name="password" id="password" placeholder="Your password" required>
        <span class="toggle-eye" onclick="togglePassword()" id="eyeIcon">👁️</span>
      </div>

      <button type="submit" class="btn btn-gold btn-block">Login</button>
    </form>

    <p class="auth-link">No account yet? <a href="signup.php">Sign up</a></p>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const eye = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        eye.textContent = '🙈';
      } else {
        input.type = 'password';
        eye.textContent = '👁️';
      }
    }
  </script>
</body>
</html>