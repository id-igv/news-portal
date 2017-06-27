<?php
    namespace Model\Form;
    
    use \Libarary\Request;
    
    class RegistrationForm {
        private $email;
        private $username;
        private $pwd;
        private $pwdConfirm;
        
        public function __construct(\Library\Request $request) {
            $this->email = $request->post('email');
            $this->username = $request->post('username');
            $this->pwd = $request->post('pwd');
            $this->pwdConfirm = $request->post('pwdConfirm');
        }
        
        public function isValid() {
            return !is_null($this->email) && !is_null($this->username) &&
                !is_null($this->pwd) && !is_null($this->pwdConfirm);
        }
        
        public function get_email() {
            return $this->email;
        }
        public function get_username() {
            return $this->username;
        }
        
        public function get_pwd() {
            return $this->pwd;
        }
    }
?>