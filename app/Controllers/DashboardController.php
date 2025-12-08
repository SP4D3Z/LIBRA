<?php
namespace App\Controllers;

use App\Models\BorrowModel;
use App\Models\PenaltyModel;

class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }

    public function getDashboardStats($user_id, $user_type) {
        $stats = [];
        
        if (in_array($user_type, ['student', 'teacher'])) {
            // For students and teachers
            $borrowModel = new BorrowModel($this->pdo);
            $penaltyModel = new PenaltyModel($this->pdo);
            
            $stats['active_borrowings'] = count($borrowModel->getUserActiveBorrowings($user_id));
            $stats['unpaid_penalties'] = $borrowModel->getUnpaidPenalties($user_id);
            $stats['overdue_books'] = $borrowModel->getOverdueBooksCount($user_id);
            
            if ($user_type === 'student') {
                $semester = $borrowModel->getCurrentSemester();
                $stats['semester_borrowed'] = $borrowModel->getStudentBorrowCount($user_id, $semester['start'], $semester['end']);
                $stats['semester_limit'] = 3;
            }
        }
        
        return $stats;
    }
}