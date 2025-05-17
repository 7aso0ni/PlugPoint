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
     * Get users by role ID
     * 
     * @param int $roleId The role ID to filter by
     * @param int|null $limit Optional limit for pagination
     * @param int $offset Optional offset for pagination
     * @return array Array of users with the specified role
     */
    public function getUsersByRole(int $roleId, $limit = null, $offset = 0): array
    {
        $query = $this->table(self::TABLE_NAME)
            ->select()
            ->where('role_id', '=', $roleId);

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
     * Search users by name, email or phone
     * 
     * @param string $search Search term
     * @param int $limit Number of users per page
     * @param int $offset Offset for pagination
     * @return array Array of matching users
     */
    public function searchUsers($search, $limit = null, $offset = null)
    {
        // Log search term for debugging
        error_log("UserModel searchUsers - Search term: " . $search);

        // Prepare search term for LIKE query
        $searchTerm = '%' . $search . '%';

        $sql = "SELECT * FROM users WHERE 
                name LIKE :search OR 
                email LIKE :search OR 
                phone LIKE :search 
                ORDER BY id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);

        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Log result count for debugging
        error_log("UserModel searchUsers - Results found: " . count($result));

        return $result;
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