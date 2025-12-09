<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\BookController;

require_login();

$pdo = Database::connect();
$controller = new BookController($pdo);
$currentUser = user();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$controller->handleArchive($action, $id);
$msg = $controller->handleAddBook($_POST, $currentUser);

$books = $controller->getBooks();
$categories = $controller->getCategories();

include VIEWS_PATH . '/header.php';
?>
<style>
form {
  background: #f8fafc;
  border-radius: 1rem;
  padding: 1.5rem 1.2rem;
  box-shadow: 0 2px 12px rgba(56,189,248,0.07);
}
</style>
<div class="row">
  <div class="col-md-8">
    <h3><i class="bi bi-book-half text-primary"></i> Books</h3>
    <?php if(!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <table class="table table-bordered">
      <thead><tr><th>Title</th><th>Author</th><th>Copies</th><th>Available</th><th>Category</th><th>Archived</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach($books as $b): ?>
          <tr>
            <td><?php echo htmlspecialchars($b['title']); ?></td>
            <td><?php echo htmlspecialchars($b['author']); ?></td>
            <td><?php echo $b['total_copies']; ?></td>
            <td><?php echo $b['available_copies']; ?></td>
            <td><?php echo htmlspecialchars($b['category_name']); ?></td>
            <td><?php echo $b['is_archived'] ? 'Yes' : 'No'; ?></td>
            <td>
              <a class="btn btn-sm btn-outline-primary" href="books.php?action=archive&id=<?php echo $b['book_id']; ?>"><?php echo $b['is_archived'] ? 'Unarchive' : 'Archive'; ?></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="col-md-4">
    <h4>Add Book</h4>
    <form method="post">
      <input type="hidden" name="add_book" value="1">
      <div class="mb-2"><input class="form-control" name="isbn" placeholder="ISBN"></div>
      <div class="mb-2"><input class="form-control" name="title" placeholder="Title" required></div>
      <div class="mb-2"><input class="form-control" name="author" placeholder="Author" required></div>
      <div class="mb-2"><input class="form-control" name="publisher" placeholder="Publisher"></div>
      <div class="mb-2"><input class="form-control" name="publication_year" placeholder="Year"></div>
      <div class="mb-2"><input class="form-control" name="edition" placeholder="Edition"></div>
      <div class="mb-2">
        <select name="category_id" class="form-control">
          <option value="">--Select Category--</option>
          <?php foreach($categories as $c): ?>
            <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2"><input class="form-control" name="total_copies" placeholder="Total copies" type="number" min="1" value="1"></div>
      <div class="mb-2"><input class="form-control" name="price" placeholder="Price" value="0.00"></div>
      <div class="mb-2"><input class="form-control" name="location" placeholder="Shelf/Location"></div>
      <div class="mb-2"><textarea class="form-control" name="description" placeholder="Description"></textarea></div>
      <button class="btn btn-primary">Add Book</button>
    </form>
  </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>