<?php
// Expects: $user array, $books array
?>
<form method="post">
  <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
  <div class="mb-3">
    <label class="form-label">Select Book</label>
    <select name="book_id" class="form-control" required>
      <?php foreach($books as $b): ?>
        <option value="<?php echo $b['book_id']; ?>">
          <?php echo htmlspecialchars($b['title']); ?> (Available: <?php echo $b['available_copies']; ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <button class="btn btn-primary w-100" name="borrow_book">Borrow</button>
</form>