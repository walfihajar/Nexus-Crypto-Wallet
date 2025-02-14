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

}