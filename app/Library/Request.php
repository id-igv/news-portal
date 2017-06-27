<?php
    namespace Library;
    
    class Request {
        private $get;
        private $post;
        private $server;
        
        public function __construct() {
            $this->get = $_GET;
            $this->post = $_POST;
            $this->server = $_SERVER;
        }
        
        public function get($key, $default = null) {
            return isset($this->get[$key]) ? $this->get[$key] : $default;
        }
        
        public function isGet() {
            return (bool)$this->get;
        }
        
        public function post($key, $default = null) {
            return isset($this->post[$key]) ? $this->post[$key] : $default;
        }
        
        public function isPost() {
            return (bool)$this->post;
        }
        
        public function server($key, $default = null) {
            return isset($this->server[$key]) ? $this->server[$key] : $default;
        }
    }
?>