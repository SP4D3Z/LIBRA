<?php
namespace App\Models;

use PDO;

class BookModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function addBook($data, $user_id) {
        $stmt = $this->pdo->prepare("INSERT INTO books (isbn,title,author,publisher,publication_year,edition,category_id,total_copies,available_copies,price,location,description,condition_status,added_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['isbn'], $data['title'], $data['author'], $data['publisher'] ?: null,
            $data['publication_year'] ?: null, $data['edition'] ?: null, $data['category_id'] ?: null,
            $data['total_copies'] ?: 1, $data['total_copies'] ?: 1, $data['price'] ?: 0.00,
            $data['location'] ?: null, $data['description'] ?: null, $data['condition_status'] ?? 'excellent',
            $user_id
        ]);
    }

    public function toggleArchive($book_id) {
        $this->pdo->prepare("UPDATE books SET is_archived = NOT is_archived WHERE book_id = ?")->execute([$book_id]);
    }

    public function getBooks() {
        return $this->pdo->query("SELECT b.*, c.category_name FROM books b LEFT JOIN categories c ON b.category_id=c.category_id ORDER BY b.title")->fetchAll();
    }

    public function getCategories() {
        return $this->pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();
    }
    // Add these methods to your existing BookModel.php class:

public function getTotalBookCount() {
    $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM books WHERE is_archived = 0");
    return $stmt->fetchColumn();
}

public function getAvailableBookCount() {
    $stmt = $this->pdo->query("SELECT SUM(available_copies) as available FROM books WHERE is_archived = 0");
    return $stmt->fetchColumn() ?: 0;
}

public function getBorrowedBookCount() {
    $stmt = $this->pdo->query("SELECT COUNT(DISTINCT bt.book_id) as borrowed FROM borrowing_transactions bt JOIN books b ON bt.book_id = b.book_id WHERE bt.status = 'active' AND b.is_archived = 0");
    return $stmt->fetchColumn() ?: 0;
}

public function getBooksByCategory() {
    $sql = "SELECT 
                c.category_id,
                c.category_name,
                COUNT(b.book_id) as total_books,
                SUM(b.available_copies) as available_copies,
                SUM(b.total_copies - b.available_copies) as borrowed_copies
            FROM categories c
            LEFT JOIN books b ON c.category_id = b.category_id AND b.is_archived = 0
            GROUP BY c.category_id, c.category_name
            ORDER BY c.category_name";
    
    return $this->pdo->query($sql)->fetchAll();
}

public function getInventorySummary() {
    return [
        'total_books' => $this->getTotalBookCount(),
        'total_copies' => $this->getTotalCopiesCount(),
        'available_copies' => $this->getAvailableBookCount(),
        'borrowed_copies' => $this->getBorrowedCopiesCount(),
        'by_category' => $this->getBooksByCategory()
    ];
}

public function getTotalCopiesCount() {
    $stmt = $this->pdo->query("SELECT SUM(total_copies) as total FROM books WHERE is_archived = 0");
    return $stmt->fetchColumn() ?: 0;
}

public function getBorrowedCopiesCount() {
    $stmt = $this->pdo->query("SELECT SUM(total_copies - available_copies) as borrowed FROM books WHERE is_archived = 0");
    return $stmt->fetchColumn() ?: 0;
}
}