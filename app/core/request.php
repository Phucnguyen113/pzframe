<?php
    class Request{
        protected $param=[];
        public function __construct()
        {
            if($_SERVER['REQUEST_METHOD']=='GET'){
                $this->param=$_GET;
            }else if($_SERVER['REQUEST_METHOD']=='POST'){
                $this->param=$_POST;
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
        public function url($url=''){
            $urll=explode('?',$_SERVER['REQUEST_URI']);
            // if($url!='') return str_replace('/pzframe/public/',"",$urll[0]).$url; 
            $path_dir=$this->address_dir();
            if(!preg_match('/index.php/',$urll[0])){
                $path_dir=str_replace('index.php',"",$path_dir);
            }
            return str_replace($path_dir,"",$urll[0]); 
        }
        public function fullUrl(){
            $path_dir=$this->address_dir();
            if(!preg_match('/index.php/',$_SERVER['REQUEST_URI'])){
                $path_dir=str_replace('index.php',"",$path_dir);
            }
            return trim(str_replace($path_dir,"",$_SERVER['REQUEST_URI']),"/"); 
        }
        public function method(){
            return strtolower($_SERVER['REQUEST_METHOD']);
        }
        public function root(){
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
                $url = "https://";   
            else  
                $url = "http://";   
            // Append the host(domain name, ip) to the URL.   
            $url.= $_SERVER['HTTP_HOST'];   
            
            // Append the requested resource location to the URL   
            // $url.= $_SERVER['REQUEST_URI'];    
            return $url;
        }
        public function address_dir(){
            $address = substr($_SERVER["SCRIPT_NAME"],0,strlen($_SERVER["SCRIPT_NAME"]));  
            return $address;
        }
    }
?>