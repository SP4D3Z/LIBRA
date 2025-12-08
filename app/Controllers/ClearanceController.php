<?php
namespace App\Controllers;

use App\Models\BorrowModel;
use App\Models\PenaltyModel;
use App\Models\UserModel;

class ClearanceController {
    private $borrowModel;
    private $penaltyModel;
    private $userModel;

    public function __construct($pdo) {
        $this->borrowModel = new BorrowModel($pdo);
        $this->penaltyModel = new PenaltyModel($pdo);
        $this->userModel = new UserModel($pdo);
    }

    public function checkClearanceStatus($user_id) {
        $status = [
            'eligible' => true,
            'issues' => [],
            'user_info' => $this->getUserInfo($user_id)
        ];

        // Check for active borrowings
        $active_borrowings = $this->borrowModel->getUserActiveBorrowings($user_id);
        if (!empty($active_borrowings)) {
            $status['eligible'] = false;
            $status['issues'][] = 'Has ' . count($active_borrowings) . ' active borrowed book(s)';
            $status['active_borrowings'] = $active_borrowings;
        }

        // Check for unpaid penalties
        $unpaid_penalties = $this->borrowModel->getUnpaidPenalties($user_id);
        if ($unpaid_penalties > 0) {
            $status['eligible'] = false;
            $status['issues'][] = 'Has unpaid penalties: ₱' . number_format($unpaid_penalties, 2);
            $status['unpaid_penalties'] = $unpaid_penalties;
        }

        return $status;
    }

    public function processClearance($user_id, $staff_id) {
        $status = $this->checkClearanceStatus($user_id);
        
        if ($status['eligible']) {
            // Mark user as cleared (you might want to add this to your users table)
            // For now, we'll just return success
            return [
                'success' => true, 
                'message' => 'Clearance approved for ' . $status['user_info']['name']
            ];
        } else {
            return [
                'success' => false, 
                'message' => 'Clearance denied for ' . $status['user_info']['name'] . ': ' . implode(', ', $status['issues'])
            ];
        }
    }

    public function getUsersForClearance() {
        // Get all students and teachers who might need clearance
        $sql = "SELECT user_id, username, first_name, last_name, user_type 
                FROM users 
                WHERE user_type IN ('student', 'teacher') 
                AND is_active = 1 
                ORDER BY user_type, first_name";
        return $this->userModel->executeQuery($sql)->fetchAll();
    }

    private function getUserInfo($user_id) {
        $sql = "SELECT user_id, username, first_name, last_name, user_type 
                FROM users 
                WHERE user_id = ?";
        $stmt = $this->userModel->executeQuery($sql, [$user_id]);
        $user = $stmt->fetch();
        
        return [
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'type' => $user['user_type'],
            'username' => $user['username']
        ];
    }
}