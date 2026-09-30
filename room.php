<?php
require 'config.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    'SELECT r.id, r.room_number, f.id AS floor_id, f.floor_number, b.name AS building_name
     FROM rooms r
     JOIN floors f ON f.id = r.floor_id
     JOIN buildings b ON b.id = f.building_id
     WHERE r.id = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
if (!$room) { header('Location: dashboard.php'); exit; }

$stmt = $conn->prepare('SELECT id, name, description, photo FROM items WHERE room_id = ? ORDER BY id DESC');
$stmt->bind_param('i', $id);
$stmt->execute();
$items = $stmt->get_result();

page_top('Room ' . $room['room_number'], 'floor.php?id=' . $room['floor_id']);
?>
<section class="welcome">
  <h1><?= e($room['building_name']) ?>
      <span class="gold">/ Floor <?= (int)$room['floor_number'] ?> / Room <?= (int)$room['room_number'] ?></span></h1>
</section>

<section class="panel">
  <h2>Add New Item</h2>
  <form action="add_item.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="room_id" value="<?= (int)$room['id'] ?>">

    <label>Item Name</label>
    <input type="text" name="name" maxlength="150" placeholder="e.g. Office Chair" required>

    <label>Description</label>
    <textarea name="description" rows="3" placeholder="Describe the item..."></textarea>

    <label>Photo / Picture</label>
    <input type="file" name="photo" accept="image/*">

    <button type="submit" class="btn btn-gold">Add Item</button>
  </form>
</section>

<h2 class="section-title">Items in this room</h2>

<?php if ($items->num_rows === 0): ?>
  <p class="muted">No items yet. Add your first item above.</p>
<?php else: ?>
<div class="grid grid-items">
  <?php while ($it = $items->fetch_assoc()): ?>
    <article class="item">
      <?php if ($it['photo']): ?>
        <img src="uploads/<?= e($it['photo']) ?>" alt="<?= e($it['name']) ?>">
      <?php else: ?>
        <div class="no-photo">No photo</div>
      <?php endif; ?>
      <div class="item-body">
        <h3><?= e($it['name']) ?></h3>
        <p><?= nl2br(e($it['description'])) ?></p>
        <form action="add_item.php" method="post" onsubmit="return confirm('Delete this item?');">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="room_id" value="<?= (int)$room['id'] ?>">
          <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </article>
  <?php endwhile; ?>
</div>
<?php endif; ?>
<?php page_bottom(); ?>