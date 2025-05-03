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

    public function getAllUsers(): array
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->get(PDO::FETCH_OBJ);
    }

    public function getUserById($id)
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('id', '=', $id)
            ->first();
    }

    public function getUserByEmail($email)
    {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('email', '=', $email)
            ->first();
    }

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
}