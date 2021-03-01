<?php
    class authMiddleware extends middleware{
        public function before(){
          echo 'before<br>';
        }
        public function after(){
            die('after');
        }
    }

?>