<?php

namespace App\Libraries;

use App\Libraries\Database;

class Controller {
    protected $db;

    public function __construct() {
        // 3yt 3La db
        $this->db = new Database;
    }

    // charger model
    public function model($model){
      // lassa9 model file
      require_once '../app/models/' . $model . '.php';

      // Instatiate model
      $className = 'App\\Models\\' . $model;
      return new $className();
    }

    // Load view
    public function view($view, $data = []){
      // Check for view file
      if(file_exists('../app/views/' . $view . '.php')){
        require_once '../app/views/' . $view . '.php';
      } else {
        // View does not exist
        die('View does not exist');
      }
    }
  }