<?php
    namespace Model\Form;
    
    class LoginForm {
        private $email;
        private $pwd;
        
        public function __construct(\Library\Request $request) {
            $this->email = $request->post('email');
            $this->pwd = $request->post('pwd');
        }
        
        public function isValid() {
            return !is_null($this->email) && !is_null($this->pwd);
        }
        
        public function get_email() {
            return $this->email;
        }
        
        public function get_pwd() {
            return $this->pwd;
        }
    }
?>