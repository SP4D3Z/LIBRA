<?php
// Expects: $borrows array
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
        <td><?php echo $row['due_date']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
          <?php if($row['status'] === 'active'): ?>
            <a class="btn btn-sm btn-primary" href="borrow.php?return=1&id=<?php echo $row['transaction_id']; ?>">Return</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>