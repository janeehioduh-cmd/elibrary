<?php

class Member {

    private $conn;
    private $table = "members";

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Check if email already exists
    public function emailExists($email) {

        $sql = "SELECT id FROM members WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->rowCount() > 0;
    }

    // Register a new member with optional profile picture
    public function register($name, $email, $phone, $password, $profileImage = null) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO members
                (name, email, phone, password, profile_image)
                VALUES
                (:name, :email, :phone, :password, :profile_image)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':password' => $hashedPassword,
            ':profile_image' => $profileImage
        ]);
    }

    // Login member
    public function login($email, $password) {

        $sql = "SELECT * FROM members WHERE email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member && password_verify($password, $member['password'])) {
            return $member;
        }

        return false;
    }

    // Get member by ID
    public function getMemberById($id) {

        $sql = "SELECT * FROM members WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all members
    public function getAllMembers() {

        $sql = "SELECT * FROM members ORDER BY date_joined DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Suspend member
    public function suspendMember($id, $reason) {

        $sql = "UPDATE members
                SET status = 'suspended',
                    suspension_reason = :reason
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':reason' => $reason,
            ':id' => $id
        ]);
    }

    // Reactivate member
    public function reactivateMember($id) {

        $sql = "UPDATE members
                SET status = 'active',
                    suspension_reason = NULL
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    // Update member profile picture
    public function updateProfileImage($id, $profileImage) {

        $sql = "UPDATE members
                SET profile_image = :profile_image
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':profile_image' => $profileImage,
            ':id' => $id
        ]);
    }

}

?>