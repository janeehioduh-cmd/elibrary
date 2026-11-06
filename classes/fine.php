<?php

class Fine {

    private $conn;
    private $finePerDay = 500;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Save or update overdue fines
    public function updateOverdueFines() {

        $sql = "SELECT id, member_id, due_date
                FROM borrowings
                WHERE status = 'borrowed'
                AND due_date < CURDATE()";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $overdueBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($overdueBooks as $book) {

            $dueDate = new DateTime($book['due_date']);
            $today = new DateTime(date('Y-m-d'));

            $daysOverdue = $dueDate->diff($today)->days;

            $amount = $daysOverdue * $this->finePerDay;

            $insert = "INSERT INTO fines
                       (borrowing_id, member_id, amount)
                       VALUES (:borrowing_id, :member_id, :amount)
                       ON DUPLICATE KEY UPDATE
                       amount = IF(status = 'unpaid', VALUES(amount), amount)";

            $save = $this->conn->prepare($insert);

            $save->execute([
                ':borrowing_id' => $book['id'],
                ':member_id' => $book['member_id'],
                ':amount' => $amount
            ]);
        }

        return true;
    }

    // Get all fines
    public function getAllFines() {

        $sql = "SELECT fines.*,
                       members.name AS member_name,
                       books.title
                FROM fines
                JOIN borrowings
                    ON fines.borrowing_id = borrowings.id
                JOIN members
                    ON fines.member_id = members.id
                JOIN books
                    ON borrowings.book_id = books.id
                ORDER BY fines.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Record fine payment
    public function markFineAsPaid($id) {

        $sql = "UPDATE fines
                SET status = 'paid',
                    paid_at = NOW()
                WHERE id = :id
                AND status = 'unpaid'";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;

    }
    // Get a member's total unpaid fines
public function getMemberOutstandingFine($member_id) {

    $sql = "SELECT COALESCE(SUM(amount), 0) AS total
            FROM fines
            WHERE member_id = :member_id
            AND status = 'unpaid'";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute([
        ':member_id' => $member_id
    ]);

    return (float) $stmt->fetchColumn();
}
// Get a member's fine history
public function getMemberFines($member_id) {

    $sql = "SELECT fines.*,
                   books.title
            FROM fines
            JOIN borrowings
                ON fines.borrowing_id = borrowings.id
            JOIN books
                ON borrowings.book_id = books.id
            WHERE fines.member_id = :member_id
            ORDER BY fines.created_at DESC";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute([
        ':member_id' => $member_id
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}