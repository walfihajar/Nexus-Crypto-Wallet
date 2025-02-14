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
    // w hadi hiya dyal register 👍
    public function register() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Init data
            $data = [
                'firstname' => trim($_POST['firstname']),
                'lastname' => trim($_POST['lastname']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'birth_date' => trim($_POST['birth_date']),
                'errors' => []
            ];

            // Validate data
            if(empty($data['firstname'])) {
                $data['errors']['firstname'] = 'Please enter your first name';
            }

            if(empty($data['lastname'])) {
                $data['errors']['lastname'] = 'Please enter your last name';
            }

            if(empty($data['email'])) {
                $data['errors']['email'] = 'Please enter your email';
            } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = 'Please enter a valid email';
            }

            if(empty($data['password'])) {
                $data['errors']['password'] = 'Please enter a password';
            } elseif(strlen($data['password']) < 6) {
                $data['errors']['password'] = 'Password must be at least 6 characters';
            }

            if($data['password'] != $data['confirm_password']) {
                $data['errors']['confirm_password'] = 'Passwords do not match';
            }

            if(empty($data['birth_date'])) {
                $data['errors']['birth_date'] = 'Please enter your birth date';
            }

            // Make sure no errors
            if(empty($data['errors'])) {
                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                try {
                    // Register User
                    $result = $this->userModel->signup($data);
                    error_log("Signup result: " . print_r($result, true));

                    if($result) {
                        flash('register_success', 'You are registered. Please check your email for verification.');
                        redirect('auth/login');
                    } else {
                        flash('register_error', 'Something went wrong. Please try again.');
                        $this->view('auth/register', $data);
                    }
                } catch (\Exception $e) {
                    error_log("Registration error: " . $e->getMessage());
                    flash('register_error', 'Registration failed. Please try again.');
                    $this->view('auth/register', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/register', $data);
            }
        } else {
            // Init data
            $data = [
                'firstname' => '',
                'lastname' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'birth_date' => '',
                'errors' => []
            ];

            // Load view
            $this->view('auth/register', $data);
        }
    }

    public function verify() {
        // Only allow access if user is logged in but not verified
        if(!isset($_SESSION['user_id']) || (isset($_SESSION['verified']) && $_SESSION['verified'])) {
            redirect('');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $code = trim($_POST['code']);
            $userId = $_SESSION['user_id'];
            
            if($this->userModel->verifyEmail($userId, $code)) {
                // Update session to mark user as verified
                $_SESSION['verified'] = true;
                
                // Get full user data
                $user = $this->userModel->getUserById($userId);
                $this->createUserSession($user);
                
                flash('login_success', 'Email verified successfully!');
                redirect('');
            } else {
                flash('verify_error', 'Invalid verification code', 'bg-red-900/50 text-red-400 p-3 rounded-md text-sm');
                redirect('auth/verify');
            }
        }

        $this->view('auth/verify', ['email' => $_SESSION['user_email']]);
    }

    public function resendVerification() {
        if(!isset($_SESSION['user_id']) || (isset($_SESSION['verified']) && $_SESSION['verified'])) {
            redirect('');
        }

        if($this->userModel->sendVerificationEmail($_SESSION['user_id'])) {
            flash('verify_success', 'Verification code sent successfully');
        } else {
            flash('verify_error', 'Failed to send verification code');
        }

        redirect('auth/verify');
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('auth/login');
    }
}