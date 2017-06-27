<?php
    namespace Model;
    
    use \Library\Manager;
    use \Model\Entity\User;
    use \Model\Form\LoginForm;
    use \Model\Form\RegistrationForm;
    use \Library\Password;
    
    class ProfileManager extends Manager {
        
        public function exists(LoginForm $userLoginData) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=:email");
            $stmt->execute([
                ':email' => $userLoginData->get_email()
                ]);
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$res) {
                return false;
            }
            
            if (!Password::compare($userLoginData->get_pwd(), $res['pwd'])) {
                return false;
            }
            
            $user = new User();
            $user->id = $res['id'];
            $user->email = $res['email'];
            $user->username = $res['username'];
            $user->role_id = $res['role_id'];
            
            return $user;
        }
        
        public function getRole($id) {
            $stmt = $this->pdo->prepare("SELECT (name) FROM roles WHERE id=:id");
            $stmt->execute([
                ':id' => $id
                ]);
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$res) {
                return false;
            }
            return $res['name'];
        }
        
        public function addUserToDB(RegistrationForm $userRegData) {
            $pwd = new Password($userRegData->get_pwd());
            try {
                $stmt = $this->pdo->prepare("INSERT INTO users (username, email, pwd) VALUES (:username, :email, :pwd)");
                return $stmt->execute([
                            ':email' => $userRegData->get_email(),
                            ':username' => $userRegData->get_username(),
                            ':pwd' => $pwd
                        ]);
                        
            } catch(\PDOException $e) {
                return false;
            }
        }
        
        public function increaseUserBalance($amount, $steam_id = null) {
            if ($amount == 0) {
                return true;
            }
            
            $user = $this->userData;
            
            if ($steam_id != null) {
                $user = $this->exists($steam_id, true);
            }
            
            $userBalance = $user->total_balance;
            $userBalance += $amount;
            
            $stmt = $this->pdo->prepare("UPDATE users SET total_balance=:newbalance WHERE steam_id=:steamid");
            return $stmt->execute([
                ':steamid' => $steam_id,
                ':newbalance' => $userBalance
                ]);
        }
    }
    
?>