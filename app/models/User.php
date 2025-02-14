<?php
namespace App\Models;
use App\Libraries\Database;
class User {
    protected $db;

    public function __construct() {
        $this->db = new Database;
    }
    protected function validateLogin(string $email, string $password): bool {
        if(empty($email) || empty($password)) {
            return false;
        }
        return true;
    }

    protected function validateSignup(array $data): bool {
        if(empty($data['firstname']) || empty($data['lastname']) || 
           empty($data['email']) || empty($data['password']) || 
           empty($data['birth_date'])) {
            return false;
        }

        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if(strlen($data['password']) < 6) {
            return false;
        }

        if($this->emailExists($data['email'])) {
            return false;
        }

        return true;
    }

    protected function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    protected function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}