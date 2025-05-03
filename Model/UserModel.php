<?php

namespace Model;

use PDO;

require_once 'BaseModel.php';

class UserModel extends BaseModel
{
    const TABLE_NAME = "users";

    /**
     * Hash a password using PHP's password_hash function
     * 
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public function hashPassword($password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify if a plain text password matches the stored hash
     * 
     * @param string $password Plain text password to verify
     * @param string $hash Stored hash to check against
     * @return bool True if password matches, false otherwise
     */
    public function verifyPassword($password, $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Get all users
     */
    public function getAllUsers($limit = null, $offset = 0): array
    {
        $query = $this->table(self::TABLE_NAME)->select();

        if ($limit !== null) {
            $query->limit($limit, $offset);
        }

        return $query->get();
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('id', '=', $id)
            ->first();
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('email', '=', $email)
            ->first();
    }

    /**
     * Check if a user exists with the given email
     */
    public function doesUserExist($email): bool
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('email', '=', $email)
            ->exists();
    }

    /**
     * Update user data based on current email
     *
     * @param string $currentEmail The current email used to identify the user
     * @param array $userData New user data to update
     * @return bool|object Result of update operation
     */
    public function updateUserData($userData, $currentEmail)
    {
        // If password is being updated, hash it first
        if (isset($userData['password'])) {
            $userData['password'] = $this->hashPassword($userData['password']);
        }

        return $this->table(self::TABLE_NAME)
            ->where("email", "=", $currentEmail)
            ->update($userData);
    }

    /**
     * Check if an email already exists in the database
     *
     * @param string $email Email to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExists($email)
    {
        $result = $this->table(self::TABLE_NAME)
            ->where("email", "=", $email)
            ->select("id")
            ->get();

        return !empty($result);
    }

    /**
     * Create a new user with hashed password
     * 
     * @param array $userData User data including plain text password
     * @return mixed Result of insert operation
     */
    public function createUser(array $userData)
    {
        // Hash the password before storing
        $userData['password'] = $this->hashPassword($userData['password']);

        return $this->table(self::TABLE_NAME)->insert($userData);
    }

    /**
     * Get total number of users
     */
    public function getTotalUsers(): int
    {
        $result = $this->table(self::TABLE_NAME)
            ->select('COUNT(*) as count')
            ->first();

        return $result['count'] ?? 0;
    }

    /**
     * Update user
     */
    public function updateUser($userId, array $userData)
    {
        // Hash password if it's being updated
        if (isset($userData['password'])) {
            $userData['password'] = $this->hashPassword($userData['password']);
        }

        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $userId)
            ->update($userData);
    }

    /**
     * Delete user
     */
    public function deleteUser($userId)
    {
        return $this->table(self::TABLE_NAME)
            ->where('id', '=', $userId)
            ->delete();
    }

    /**
     * Search users by name or email
     */
    public function searchUsers($search, $limit = 10, $offset = 0): array
    {
        // If using ORM approach isn't possible for OR conditions, use direct SQL
        $sql = "SELECT * FROM " . self::TABLE_NAME . " 
                WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?
                LIMIT ? OFFSET ?";

        $params = [
            "%$search%",
            "%$search%",
            "%$search%",
            $limit,
            $offset
        ];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total search results count
     */
    public function getTotalSearchResults($search): int
    {
        $sql = "SELECT COUNT(*) as count FROM " . self::TABLE_NAME . " 
                WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";

        $params = [
            "%$search%",
            "%$search%",
            "%$search%"
        ];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] ?? 0;
    }

    /**
     * Get user growth statistics
     */
    public function getUserGrowthStats($months = 6): array
    {
        $stats = [];

        for ($i = 0; $i < $months; $i++) {
            $monthDate = date('Y-m', strtotime("-$i months"));
            $monthStart = $monthDate . '-01 00:00:00';
            $monthEnd = date('Y-m-t 23:59:59', strtotime($monthStart));

            // Count users registered this month
            $sql = "SELECT COUNT(*) as count FROM " . self::TABLE_NAME . " 
                    WHERE created_at >= ? AND created_at <= ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$monthStart, $monthEnd]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stats[] = [
                'month' => date('M Y', strtotime($monthDate)),
                'new_users' => $result['count'] ?? 0
            ];
        }

        // Reverse to get chronological order
        return array_reverse($stats);
    }
}