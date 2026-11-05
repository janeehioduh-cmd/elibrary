<?php

class Borrowing {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Submit a borrowing request
    public function requestBook($book_id, $member_id, $pickup_date, $terms_agreed) {

        // Check membership status
        $sql = "SELECT status FROM members WHERE id = :member_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':member_id' => $member_id]);

        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$member || $member['status'] !== 'active') {
            return false;
        }

        // Check book availability
        $sql = "SELECT is_available FROM books WHERE id = :book_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);

        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book || !$book['is_available']) {
            return false;
        }

        // Check terms agreement
        if (!$terms_agreed) {
            return false;
        }

        // Save request
        $sql = "INSERT INTO borrowings
                (book_id, member_id, preferred_pickup_date, terms_agreed, status)
                VALUES
                (:book_id, :member_id, :pickup_date, :terms_agreed, 'pending')";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':book_id' => $book_id,
            ':member_id' => $member_id,
            ':pickup_date' => $pickup_date,
            ':terms_agreed' => $terms_agreed
        ]);
    }

    // Approve borrowing request
    public function approveRequest($borrowing_id) {

        $this->conn->beginTransaction();

        try {

            $sql = "SELECT borrowings.*, members.status AS member_status,
                           books.is_available
                    FROM borrowings
                    JOIN members ON borrowings.member_id = members.id
                    JOIN books ON borrowings.book_id = books.id
                    WHERE borrowings.id = :id
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $borrowing_id]);

            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request ||
                $request['status'] !== 'pending' ||
                $request['member_status'] !== 'active' ||
                !$request['is_available']) {

                $this->conn->rollBack();
                return false;
            }

            // Set due date to 14 days after borrowing
            $borrow_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+14 days'));

            // Update borrowing
            $sql = "UPDATE borrowings
                    SET borrow_date = :borrow_date,
                        due_date = :due_date,
                        status = 'borrowed'
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':borrow_date' => $borrow_date,
                ':due_date' => $due_date,
                ':id' => $borrowing_id
            ]);

            // Mark book unavailable
            $sql = "UPDATE books
                    SET is_available = 0
                    WHERE id = :book_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':book_id' => $request['book_id']]);

            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            $this->conn->rollBack();
            return false;

        }
    }

    // Record book return
    public function returnBook($borrowing_id) {

        $this->conn->beginTransaction();

        try {

            $sql = "SELECT book_id, status
                    FROM borrowings
                    WHERE id = :id
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $borrowing_id]);

            $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$borrowing || $borrowing['status'] !== 'borrowed') {
                $this->conn->rollBack();
                return false;
            }

            // Update borrowing record
            $sql = "UPDATE borrowings
                    SET return_date = CURDATE(),
                        status = 'returned'
                    WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $borrowing_id]);

            // Make book available again
            $sql = "UPDATE books
                    SET is_available = 1
                    WHERE id = :book_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':book_id' => $borrowing['book_id']]);

            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            $this->conn->rollBack();
            return false;

        }
    }

    // Get member borrowing records
    public function getMemberBorrowings($member_id) {

        $sql = "SELECT borrowings.*, books.title, books.author
                FROM borrowings
                JOIN books ON borrowings.book_id = books.id
                WHERE borrowings.member_id = :member_id
                ORDER BY borrowings.id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':member_id' => $member_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all borrowing requests
    public function getAllBorrowings() {

        $sql = "SELECT borrowings.*, books.title,
                       members.name AS member_name
                FROM borrowings
                JOIN books ON borrowings.book_id = books.id
                JOIN members ON borrowings.member_id = members.id
                ORDER BY borrowings.id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function 
rejectRequest($borrowing_id) {

    $sql = "UPDATE borrowings
            SET status = 'rejected'
            WHERE id = :id AND status = 'pending'";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute([':id' => $borrowing_id]);

    return $stmt->rowCount() > 0;
}

}
