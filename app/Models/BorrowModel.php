<?php
namespace App\Models;

use PDO;

class BorrowModel {
    protected $pdo;
    public function __construct($pdo){
        $this->pdo = $pdo;
    }
    public function getAllBorrowings() {
        $sql = "SELECT bt.*, u.first_name, u.last_name, b.title 
                FROM borrowing_transactions bt 
                JOIN users u ON bt.user_id=u.user_id 
                JOIN books b ON bt.book_id=b.book_id 
                ORDER BY bt.borrowed_date DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getByUser($userId) {
        $sql = "SELECT b.* /*, add any joined columns like book.title if needed */
                FROM borrows b
                WHERE b.user_id = :uid
                ORDER BY b.borrowed_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        return $this->pdo->query("SELECT user_id, first_name, last_name, user_type FROM users ORDER BY first_name")->fetchAll();
    }

    public function getAllBooks() {
        return $this->pdo->query("SELECT book_id, title, available_copies FROM books WHERE is_archived = 0 ORDER BY title")->fetchAll();
    }

    public function getBookAvailability($book_id) {
        $stmt = $this->pdo->prepare("SELECT available_copies FROM books WHERE book_id = ? FOR UPDATE");
        $stmt->execute([$book_id]);
        return $stmt->fetch();
    }

    public function getUserType($user_id) {
        $stmt = $this->pdo->prepare("SELECT user_type FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getStudentBorrowCount($user_id, $sem_start, $sem_end) {
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM borrowing_transactions WHERE user_id = ? AND borrowed_date BETWEEN ? AND ? AND status = 'active'");
    $stmt->execute([$user_id, $sem_start, $sem_end]);
    return $stmt->fetchColumn();
}

// ADD THIS METHOD FOR SEMESTER DATES
public function getCurrentSemester() {
    $month = date('n'); // Current month (1-12)
    
    if ($month >= 6 && $month <= 10) {
        // First Semester: June 1 - October 31
        return [
            'name' => 'First Semester',
            'start' => date('Y') . '-06-01',
            'end' => date('Y') . '-10-31'
        ];
    } elseif ($month == 11 || $month == 12 || $month <= 3) {
        // Second Semester: November 1 - March 31
        $year = $month <= 3 ? date('Y') - 1 : date('Y');
        return [
            'name' => 'Second Semester', 
            'start' => $year . '-11-01',
            'end' => ($year + 1) . '-03-31'
        ];
    } else {
        // Summer: April 1 - May 31
        return [
            'name' => 'Summer',
            'start' => date('Y') . '-04-01',
            'end' => date('Y') . '-05-31'
        ];
    }
}

    public function borrowBook($user_id, $book_id, $borrowed, $due, $staff_id) {
        $stmt = $this->pdo->prepare("INSERT INTO borrowing_transactions (user_id,book_id,borrowed_date,due_date,staff_id_borrowed,status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$user_id, $book_id, $borrowed, $due, $staff_id, 'active']);
        $this->pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?")->execute([$book_id]);
    }

    public function returnBook($transaction_id, $staff_id) {
        $this->pdo->prepare("UPDATE borrowing_transactions SET returned_date = ?, status = 'returned', staff_id_returned = ? WHERE transaction_id = ?")
            ->execute([date('Y-m-d'), $staff_id, $transaction_id]);
        $bk = $this->pdo->prepare("SELECT book_id FROM borrowing_transactions WHERE transaction_id = ?");
        $bk->execute([$transaction_id]);
        $bookid = $bk->fetchColumn();
        if($bookid){
            $this->pdo->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE book_id = ?")->execute([$bookid]);
        }
    }
    // Check for overdue books
public function getOverdueBooksCount($user_id) {
    $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM borrowing_transactions WHERE user_id = ? AND status = 'active' AND due_date < CURDATE()");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

// Check unpaid penalties
public function getUnpaidPenalties($user_id) {
    $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM penalties WHERE user_id = ? AND is_paid = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

// Get user's active borrowings
public function getUserActiveBorrowings($user_id) {
    $sql = "SELECT bt.*, b.title, b.author 
            FROM borrowing_transactions bt 
            JOIN books b ON bt.book_id = b.book_id 
            WHERE bt.user_id = ? AND bt.status = 'active' 
            ORDER BY bt.due_date";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
}