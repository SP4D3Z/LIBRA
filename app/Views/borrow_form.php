<?php
// Expects: $user array, $books array, $allUsers array (for staff/librarians)
$canBorrowForOthers = isset($user['user_type']) && in_array($user['user_type'], ['staff', 'librarian']);
?>
<form method="post">
  <?php if($canBorrowForOthers && isset($allUsers) && !empty($allUsers)): ?>
    <!-- Staff/Librarian: Can borrow for any user -->
    <div class="mb-3">
      <label class="form-label">Select User</label>
      <select name="user_id" class="form-control" required>
        <?php foreach($allUsers as $u): ?>
          <option value="<?php echo $u['user_id']; ?>">
            <?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name'] . ' (' . $u['user_type'] . ')'); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php else: ?>
    <!-- Student/Teacher: Can only borrow for themselves -->
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
    <div class="alert alert-info mb-3">
      <i class="bi bi-person-circle"></i> 
      <strong>Borrowing as:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
    </div>
  <?php endif; ?>
  
  <!-- Book selection (same for all) -->
  <div class="mb-3">
    <label class="form-label">Select Book</label>
    <select name="book_id" class="form-control" required>
      <option value="">-- Select a Book --</option>
      <?php foreach($books as $b): ?>
        <?php if($b['available_copies'] > 0): ?>
          <option value="<?php echo $b['book_id']; ?>">
            <?php echo htmlspecialchars($b['title']); ?> 
            (Available: <?php echo $b['available_copies']; ?>)
          </option>
        <?php else: ?>
          <option value="<?php echo $b['book_id']; ?>" disabled>
            <?php echo htmlspecialchars($b['title']); ?> (Out of stock)
          </option>
        <?php endif; ?>
      <?php endforeach; ?>
    </select>
  </div>
  
  <button class="btn btn-primary w-100" name="borrow_book">
    <i class="bi bi-book-plus"></i> Borrow Book
  </button>
</form>