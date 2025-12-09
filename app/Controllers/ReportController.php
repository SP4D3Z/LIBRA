<?php
namespace App\Controllers;

use App\Models\BookModel;
use App\Models\BorrowModel;

class ReportController {
    private $pdo;
    private $bookModel;
    private $borrowModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->bookModel = new BookModel($pdo);
        $this->borrowModel = new BorrowModel($pdo);
    }

    public function getInventoryReport() {
        // Simple queries directly in controller
        $total_books = $this->pdo->query("SELECT COUNT(*) as count FROM books WHERE is_archived = 0")->fetchColumn();
        $available_books = $this->pdo->query("SELECT COALESCE(SUM(available_copies), 0) as total FROM books WHERE is_archived = 0")->fetchColumn();
        $borrowed_books = $this->pdo->query("SELECT COUNT(*) as count FROM borrowing_transactions WHERE status = 'active'")->fetchColumn();
        
        $categories = $this->pdo->query("
            SELECT c.category_name, COUNT(b.book_id) as count 
            FROM categories c 
            LEFT JOIN books b ON c.category_id = b.category_id AND b.is_archived = 0
            GROUP BY c.category_id, c.category_name
            ORDER BY c.category_name
        ")->fetchAll();
        
        return [
            'total_books' => $total_books ?: 0,
            'available_books' => $available_books ?: 0,
            'borrowed_books' => $borrowed_books ?: 0,
            'by_category' => $categories
        ];
    }

    public function getBorrowingReport($start_date = null, $end_date = null) {
        $where = "";
        $params = [];
        
        if ($start_date && $end_date) {
            $where = "WHERE bt.borrowed_date BETWEEN ? AND ?";
            $params = [$start_date, $end_date];
        } elseif ($start_date) {
            $where = "WHERE bt.borrowed_date >= ?";
            $params = [$start_date];
        } elseif ($end_date) {
            $where = "WHERE bt.borrowed_date <= ?";
            $params = [$end_date];
        }
        
        $sql = "SELECT 
                    DATE(bt.borrowed_date) as date,
                    COUNT(*) as count,
                    u.user_type
                FROM borrowing_transactions bt
                JOIN users u ON bt.user_id = u.user_id
                {$where}
                GROUP BY DATE(bt.borrowed_date), u.user_type
                ORDER BY date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getPopularBooksReport($limit = 10) {
        $sql = "SELECT 
                    b.book_id,
                    b.title,
                    b.author,
                    COUNT(bt.transaction_id) as borrow_count,
                    b.available_copies
                FROM books b
                LEFT JOIN borrowing_transactions bt ON b.book_id = bt.book_id
                WHERE b.is_archived = 0
                GROUP BY b.book_id, b.title, b.author, b.available_copies
                ORDER BY borrow_count DESC
                LIMIT ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getUserBorrowingStats() {
        $sql = "SELECT 
                    u.user_type,
                    COUNT(DISTINCT u.user_id) as user_count,
                    COUNT(bt.transaction_id) as total_borrowed,
                    AVG(DATEDIFF(COALESCE(bt.returned_date, CURDATE()), bt.borrowed_date)) as avg_borrow_days,
                    SUM(CASE WHEN bt.status = 'active' AND bt.due_date < CURDATE() THEN 1 ELSE 0 END) as overdue_count
                FROM users u
                LEFT JOIN borrowing_transactions bt ON u.user_id = bt.user_id
                WHERE u.user_type IN ('student', 'teacher')
                GROUP BY u.user_type";
        
        return $this->pdo->query($sql)->fetchAll();
    }
}