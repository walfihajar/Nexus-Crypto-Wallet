<?php 
namespace App\Controllers;
use App\Libraries\Controller;
use App\Models\User;

class Auth extends Controller{
    private $userModel;

    public function __construct(){
        $this->userModel = new User();
    }

}