<?php
require 'config.php';
require_login();

$buildings = $conn->query('SELECT id, name FROM buildings ORDER BY id');

$images = [
    'Main Building'       => 'assets/main-building.jpg',
    'Melloville Building' => 'assets/meloville-building.jpg',
    'Carbag Building'     => 'assets/carbarg-building.jpg',
];

// "keith@gmail.com" -> "Keith"
$namePart = strstr($_SESSION['gmail'], '@', true);
$displayName = ucwords(str_replace(['.', '_', '-'], ' ', $namePart));

page_top('Dashboard', null, 'home', 'dash-bg');
?>
<section class="welcome-hero">
  <div>
    <p class="welcome-sub">Welcome back,</p>
    <h1 class="welcome-name"><?= e($displayName) ?>!</h1>
    <p class="muted">Select a building to manage its floors, rooms and items.</p>
  </div>
</section>

<div class="grid grid-3">
  <?php while ($b = $buildings->fetch_assoc()):
      $img = $images[$b['name']] ?? null;
  ?>
    <a class="bcard" href="building.php?id=<?= (int)$b['id'] ?>">
      <div class="bcard-photo">
        <?php if ($img): ?>
          <img src="<?= e($img) ?>" alt="<?= e($b['name']) ?>" onerror="this.style.display='none';">
        <?php endif; ?>
      </div>
      <div class="bcard-footer">
        <h3><?= e($b['name']) ?></h3>
        <span class="bcard-hint">View floors &rarr;</span>
      </div>
    </a>
  <?php endwhile; ?>
</div>
<?php page_bottom(); ?>