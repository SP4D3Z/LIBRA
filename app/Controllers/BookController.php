<?php

namespace App\Controllers;

use App\Models\BookModel;

class BookController {
    private $model;

    public function __construct($pdo) {
        $this->model = new BookModel($pdo);
    }

    public function handleAddBook($post, $currentUser) {
        $msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($post['add_book'])) {
            $this->model->addBook($post, $currentUser['user_id']);
            $msg = 'Book added';
        }
        return $msg;
    }

    public function handleArchive($action, $id) {
        if ($action === 'archive' && $id) {
            $this->model->toggleArchive($id);
            header('Location: books.php');
            exit;
        }
    }

    public function getBooks() {
        return $this->model->getBooks();
    }

    public function getCategories() {
        return $this->model->getCategories();
    }
}