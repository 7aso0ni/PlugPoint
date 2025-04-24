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

    public function getUserByEmail($email) {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('email', '=', $email)
            ->first();
    }

    public function doesUserExist($email): bool {
        return $this->table(self::TABLE_NAME)
            ->select()
            ->where('email', '=', $email)
            ->exists();
    }

    public function createUser(array $userData)
    {
        return $this->table(self::TABLE_NAME)->insert($userData);
    }
}
