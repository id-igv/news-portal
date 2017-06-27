<?php
    namespace Library;
    
    class Password {
        private $pwd;
        private $hashedpwd;
        
        public function __construct($pwd) {
            $this->pwd = $pwd;
            $this->hashedpwd = password_hash($pwd, PASSWORD_BCRYPT);
        }
        
        public function __toString() {
            return $this->hashedpwd;
        }
        
        public static function compare($pwd, $hash) {
            return password_verify($pwd, $hash);
        }
    }