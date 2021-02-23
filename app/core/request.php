<?php
    class Request{
        public function get($get){
            if(isset($_GET[$get])) return $_GET[$get];
            return null;
        }
        public function post($post){
            if(isset($_POST[$post])) return $_POST[$post];
            return null;
        }
        public function url(){
            return str_replace('/pzframe/public/',"",$_SERVER['REQUEST_URI']); 
        }
        public function method(){
            return strtolower($_SERVER['REQUEST_METHOD']);
        }
    }
?>