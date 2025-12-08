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
}