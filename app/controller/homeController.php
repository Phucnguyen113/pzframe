<?php
    class homeController{
        public function index(Request $request,$no1,$no2){
            echo $no1+$no2;
        }
        public function postEdit(Request $request){
            echo 123;
            echo '<pre>';
            print_r($request);
        }
    }

?>