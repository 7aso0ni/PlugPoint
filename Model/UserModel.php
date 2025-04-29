<?php

namespace Model;

use PDO;

require_once 'BaseModel.php';

class UserModel extends BaseModel
{
    const TABLE_NAME = "users";

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

    public function createUser(array $userData)
    {
        return $this->table(self::TABLE_NAME)->insert($userData);
    }
}
