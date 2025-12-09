<?php
// Expects: $borrows array
if (!isset($borrows) || empty($borrows)) {
    echo '<div class="alert alert-info">No borrowing records found.</div>';
    return;
}
?>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>User</th>
      <th>Book</th>
      <th>Borrowed</th>
      <th>Due</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($borrows as $row): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td><?php echo $row['borrowed_date']; ?></td>
        <td class="<?php echo (isset($row['due_date']) && strtotime($row['due_date']) < time() && $row['status'] === 'active') ? 'text-danger fw-bold' : ''; ?>">
          <?php echo $row['due_date']; ?>
          <?php if (isset($row['due_date']) && strtotime($row['due_date']) < time() && $row['status'] === 'active'): ?>
            <span class="badge bg-danger">Overdue</span>
          <?php endif; ?>
        </td>
        <td>
          <span class="badge bg-<?php echo $row['status'] === 'active' ? 'primary' : ($row['status'] === 'returned' ? 'success' : 'secondary'); ?>">
            <?php echo ucfirst($row['status']); ?>
          </span>
        </td>
        <td>
          <?php if($row['status'] === 'active'): ?>
            <a class="btn btn-sm btn-primary" href="borrow.php?return=1&id=<?php echo $row['transaction_id']; ?>">Return</a>
          <?php else: ?>
            <span class="text-muted">-</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>