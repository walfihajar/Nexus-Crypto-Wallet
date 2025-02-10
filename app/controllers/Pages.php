<?php 
namespace App\Controllers;
use App\Libraries\Controller;

class Pages extends Controller  {
    // hadi hiya l index page
    public function index(){
        $data = [
            'title' => 'Welcome to ' . SITENAME,
            'description' => 'Simple trading platform built with PHP MVC'
        ];
        
        $this->view('pages/index', $data);
    }
}