<?php
    class Request{
        protected $param=[];
        public function __construct()
        {
            if($_SERVER['REQUEST_METHOD']=='GET'){
                $this->param=$_GET;
            }else if($_SERVER['REQUEST_METHOD']=='POST'){
                $this->param=$_GET;
            }
        }
        public function get($get){
            if(isset($_GET[$get])) return $_GET[$get];
            return null;
        }
        public function post($post){
            if(isset($_POST[$post])) return $_POST[$post];
            return null;
        }
        public function all(){
            return $this->param;
        }
        public function url(){
            $url=explode('?',$_SERVER['REQUEST_URI']);
            return str_replace('/php/pzframe/public/',"",$url[0]); 
        }
        public function fullpath(){
            return str_replace('/php/pzframe/public/',"",$_SERVER['REQUEST_URI']); 
        }
        public function method(){
            return strtolower($_SERVER['REQUEST_METHOD']);
        }
    }
?>