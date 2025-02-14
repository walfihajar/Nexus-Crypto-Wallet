<?php 
namespace App\Controllers;
use App\Libraries\Controller;
use App\Models\User;

class Auth extends Controller{
    private $userModel;

    public function __construct(){
        $this->userModel = new User();
    }

    // hadi hiya l lpage dyal login 😁
    public function login(){
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            if(!isset($_SESSION['verified']) || !$_SESSION['verified']) {
                redirect('auth/verify');  // Redirect unverified users to verify page
            }
            redirect('');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Init data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];
            
            error_log("Login attempt - Email: " . $data['email']);
            
            // Validate Email
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }
            
            // Validate Password
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check for user/email
            $user_exists = $this->userModel->findUserByEmail($data['email']);
            if(!$user_exists) {
                $data['email_err'] = 'No user found';
                error_log("User not found with email: " . $data['email']);
            }

            // Make sure errors are empty
            if(empty($data['email_err']) && empty($data['password_err'])) {
                // Attempt to log in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                error_log("Login attempt result: " . print_r($loggedInUser, true));

                if($loggedInUser) {
                    if(!$loggedInUser->verified) {
                        // Create a partial session for unverified users
                        $_SESSION['user_id'] = $loggedInUser->id;
                        $_SESSION['user_email'] = $loggedInUser->email;
                        $_SESSION['verified'] = false;
                        
                        // Send new verification code
                        $this->userModel->sendVerificationEmail($loggedInUser->id);
                        redirect('auth/verify');
                    } else {
                        // Create full session for verified users
                        $this->createUserSession($loggedInUser);
                        $_SESSION['verified'] = true;
                        redirect('');
                    }
                } else {
                    $data['password_err'] = 'Password incorrect';
                    error_log("Login failed - incorrect password");
                    $this->view('auth/login', $data);
                }
            } else {
                error_log("Login validation errors: " . print_r($data, true));
                $this->view('auth/login', $data);
            }
        } else {
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];
        }

        // Load view
        $this->view('auth/login', $data);
    }
}