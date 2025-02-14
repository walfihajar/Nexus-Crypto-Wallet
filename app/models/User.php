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

    
    protected function emailExists(string $email): bool {
        $this->db->query("SELECT id FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return (bool) $this->db->single();
    }

    protected function generateVerificationCode(): string {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    protected function generateNexusId(): string {
        do {
            $time = hrtime(true);
            $random = bin2hex(random_bytes(4));
            $nexus_id = sprintf("NX%s%s", 
                substr(dechex($time), -8),
                $random
            );
            
            $this->db->query("SELECT 1 FROM users WHERE nexus_id = :nexus_id");
            $this->db->bind(':nexus_id', $nexus_id);
            
            try {
                $exists = $this->db->single();
            } catch (\PDOException $e) {
                continue;
            }
        } while ($exists);

        return $nexus_id;
    }

    protected function storeVerificationCode(int $userId, string $code): bool {
        try {
            $this->db->query("UPDATE verification_codes 
                             SET expires_at = NOW() - INTERVAL '1 minute'
                             WHERE user_id = :user_id 
                             AND type = 'email_verification'
                             AND expires_at > NOW()");
            $this->db->bind(':user_id', $userId);
            $this->db->execute();

            $this->db->query("INSERT INTO verification_codes 
                             (user_id, code, type, expires_at) 
                             VALUES 
                             (:user_id, :code, 'email_verification', NOW() + INTERVAL '15 minutes')");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':code', $code);
            
            return $this->db->execute();
        } catch (\Exception $e) {
            error_log("Error storing verification code: " . $e->getMessage());
            return false;
        }
    }

    protected function verifyCode(int $userId, string $code): bool {
        try {
            $this->db->query("SELECT id FROM verification_codes 
                             WHERE user_id = :user_id 
                             AND code = :code 
                             AND type = 'email_verification'
                             AND expires_at > NOW()");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':code', $code);
            
            return (bool) $this->db->single();
        } catch (\Exception $e) {
            error_log("Error verifying code: " . $e->getMessage());
            return false;
        }
    }

    protected function markUserAsVerified(int $userId): bool {
        try {
            $this->db->query("UPDATE users SET verified = true WHERE id = :id");
            $this->db->bind(':id', $userId);
            return $this->db->execute();
        } catch (\Exception $e) {
            error_log("Error marking user as verified: " . $e->getMessage());
            return false;
        }
    }

    public function signup(array $data) {
        try {
            $this->db->beginTransaction();
            
            $hashedPassword = $data['password'];
            error_log("Using password hash: " . $hashedPassword);

            $nexus_id = $this->generateNexusId();
            
            $this->db->query("INSERT INTO users (nexus_id, firstname, lastname, email, password, birth_date) 
                             VALUES (:nexus_id, :firstname, :lastname, :email, :password, :birth_date)");
            
            $this->db->bind(':nexus_id', $nexus_id);
            $this->db->bind(':firstname', $data['firstname']);
            $this->db->bind(':lastname', $data['lastname']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':password', $hashedPassword);
            $this->db->bind(':birth_date', $data['birth_date']);

            if(!$this->db->execute()) {
                throw new \Exception("Failed to create user");
            }

            $userId = $this->db->lastInsertId();

            // Hna automatiquement 3ndo wallet USDT
            $this->db->query("INSERT INTO wallets (user_id, crypto_id, balance) 
                             SELECT :user_id, id, 0 
                             FROM cryptocurrencies 
                             WHERE symbol = 'USDT'");
            $this->db->bind(':user_id', $userId);

            if(!$this->db->execute()) {
                throw new \Exception("Failed to create wallet");
            }

            $this->db->commit();
            return $userId;
        } catch(\Exception $e) {
            $this->db->rollBack();
            error_log("Signup Error: " . $e->getMessage());
            return false;
        }
    }

    public function login(string $email, string $password) {
        error_log("Attempting login for email: " . $email);
        error_log("Provided password (raw): " . $password);

        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        
        $user = $this->db->single();
        error_log("User found: " . ($user ? 'Yes' : 'No'));
        
        if($user) {
            error_log("Stored hash: " . $user->password);
            
            // Use the verifyPassword method from Auth class
            if($this->verifyPassword($password, $user->password)) {
                error_log("Password verified successfully");
                
                if($user->verified == false) {
                    error_log("User not verified, sending verification email");
                    $this->sendVerificationEmail($user->id);
                    unset($user->password);
                    return $user;
                }
                
                unset($user->password);
                return $user;
            } else {
                error_log("Password verification failed");
                return false;
            }
        }

        return false;
    }

    public function sendVerificationEmail(int $userId): bool {
        try {
            // Generate new verification code
            $code = $this->generateVerificationCode();
            
            // Store the code in database
            $this->db->query("INSERT INTO verification_codes (user_id, code, type, expires_at) 
                             VALUES (:user_id, :code, 'email_verification', NOW() + INTERVAL '1 hour')");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':code', $code);
            
            if(!$this->db->execute()) {
                error_log("Failed to store verification code");
                return false;
            }

            // Get user email and name
            $this->db->query("SELECT email, firstname FROM users WHERE id = :id");
            $this->db->bind(':id', $userId);
            $user = $this->db->single();
            
            if(!$user) {
                error_log("User not found for verification email");
                return false;
            }

            // Send email
            $emailService = new \App\Services\EmailService();
            return $emailService->sendVerificationEmail($user->email, $user->firstname, $code);
        } catch (\Exception $e) {
            error_log("Error in sendVerificationEmail: " . $e->getMessage());
            return false;
        }
    }

    public function verifyEmail(int $userId, string $code): bool {
        try {
            $this->db->query("SELECT * FROM verification_codes 
                             WHERE user_id = :user_id 
                             AND code = :code 
                             AND type = 'email_verification' 
                             AND expires_at > NOW() 
                             ORDER BY created_at DESC 
                             LIMIT 1");
            
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':code', $code);
            
            $verification = $this->db->single();
            
            if($verification) {
                // Mark user as verified
                $this->db->query("UPDATE users SET verified = true WHERE id = :id");
                $this->db->bind(':id', $userId);
                
                if($this->db->execute()) {
                    // Delete used verification code
                    $this->db->query("DELETE FROM verification_codes WHERE id = :id");
                    $this->db->bind(':id', $verification->id);
                    $this->db->execute();
                    
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error in verifyEmail: " . $e->getMessage());
            return false;
        }
    }

    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function findUserByEmailOrNexusId($identifier) {
        $this->db->query("SELECT * FROM users WHERE email = :identifier OR nexus_id = :identifier");
        $this->db->bind(':identifier', $identifier);
        return $this->db->single();
    }     
}