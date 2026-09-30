<?php
require 'config.php';

if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail   = strtolower(trim($_POST['gmail'] ?? ''));
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $gmail)) {
        $error = 'Please enter a valid Gmail address (example@gmail.com).';
    } elseif (strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE gmail = ?');
        $stmt->bind_param('s', $gmail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'That Gmail is already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $conn->prepare('INSERT INTO users (gmail, password) VALUES (?, ?)');
            $ins->bind_param('ss', $gmail, $hash);
            $ins->execute();
            $_SESSION['flash'] = ['msg' => 'Account created! You can now log in.', 'type' => 'success'];
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up | AssetVault</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body login-bg">
  <div class="auth-box">
    <img src="assets/logo.png" alt="AssetVault Logo" class="auth-logo" onerror="this.style.display='none'">
    <h1 class="auth-title">ASSETVAULT</h1>
    <p class="auth-sub">Create your account</p>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" autocomplete="off">
      <label>Gmail</label>
      <input type="email" name="gmail" placeholder="yourname@gmail.com" value="<?= e($_POST['gmail'] ?? '') ?>" required>

      <label>Password</label>
      <div class="password-wrap">
        <input type="password" name="password" id="password" placeholder="At least 6 characters" required>
        <span class="toggle-eye" onclick="togglePassword('password', 'eyeIcon1')" id="eyeIcon1">👁️</span>
      </div>

      <label>Confirm Password</label>
      <div class="password-wrap">
        <input type="password" name="confirm" id="confirm" placeholder="Repeat password" required>
        <span class="toggle-eye" onclick="togglePassword('confirm', 'eyeIcon2')" id="eyeIcon2">👁️</span>
      </div>

      <button type="submit" class="btn btn-gold btn-block">Sign Up</button>
    </form>

    <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
  </div>

  <script>
    function togglePassword(inputId, eyeId) {
      const input = document.getElementById(inputId);
      const eye = document.getElementById(eyeId);
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