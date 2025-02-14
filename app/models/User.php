<?php
namespace App\Models;
use App\Libraries\Database;
class User {
    protected $db;

    public function __construct() {
        $this->db = new Database;
    }
}