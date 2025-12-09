<?php
namespace App\Controllers;

use App\Models\BorrowModel;

class BorrowController {
    protected $model;
    public function __construct($pdo){
        $this->model = new BorrowModel($pdo);
    }

    public function handleBorrow($post, $currentUser) {
    $err = '';
    $msg = '';
    
    if(isset($post['borrow_book'])) {
        $book_id = (int)$post['book_id'];
        $user_id = (int)$post['user_id'];
        $book = $this->model->getBookAvailability($book_id);
        
        // Check book availability
        if(!$book || $book['available_copies'] <= 0){
            $err = "Book not available.";
            return [$err, $msg];
        }
        
        $user_type = $this->model->getUserType($user_id);
        
        // STUDENT: Check semester limit (3 books)
        if($user_type === 'student') {
            $semester = $this->model->getCurrentSemester();
            $borrowed_count = $this->model->getStudentBorrowCount($user_id, $semester['start'], $semester['end']);
            
            if($borrowed_count >= 3){
                $err = "Students can only borrow up to 3 books per semester. You have borrowed {$borrowed_count} books this semester.";
                return [$err, $msg];
            }
            $days = 30; // 30 days for students
        } 
        // TEACHER: Unlimited books, longer period
        elseif($user_type === 'teacher') {
            $days = 180; // 180 days for teachers (full semester)
        }
        // STAFF & LIBRARIAN: Standard period
        else {
            $days = 30;
        }
        
        // Check if user has overdue books
        $overdue_count = $this->model->getOverdueBooksCount($user_id);
        if($overdue_count > 0) {
            $err = "Cannot borrow books. You have {$overdue_count} overdue book(s). Please return them first.";
            return [$err, $msg];
        }
        
        // Check if user has unpaid penalties
        $unpaid_penalties = $this->model->getUnpaidPenalties($user_id);
        if($unpaid_penalties > 0) {
            $err = "Cannot borrow books. You have unpaid penalties of ₱" . number_format($unpaid_penalties, 2) . ". Please clear them first.";
            return [$err, $msg];
        }
        
        $borrowed = date('Y-m-d');
        $due = date('Y-m-d', strtotime("+$days days"));
        $this->model->borrowBook($user_id, $book_id, $borrowed, $due, $currentUser['user_id']);
        $msg = "Book borrowed successfully. Due: $due";
        
        // Add role-specific message
        if($user_type === 'student') {
            $remaining = 3 - ($borrowed_count + 1);
            $msg .= "<br>Remaining books you can borrow this semester: {$remaining}";
        }
    }
    return [$err, $msg];
}

    public function handleReturn($get, $currentUser) {
        if(isset($get['return']) && isset($get['id'])){
            $tid = (int)$get['id'];
            $this->model->returnBook($tid, $currentUser['user_id']);
            header('Location: borrow.php');
            exit;
        }
    }
    
    public function listUserBorrows() {
        if(empty($_SESSION['user'])) return [];
        $userId = $_SESSION['user']['user_id']; // Fixed: changed 'id' to 'user_id'
        return $this->model->getByUser($userId);
    }

    public function getBorrowings() {
        return $this->model->getAllBorrowings();
    }

    public function getUsers() {
        return $this->model->getAllUsers();
    }

    public function getBooks() {
        return $this->model->getAllBooks();
    }
}   