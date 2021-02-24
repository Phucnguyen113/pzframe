<?php
    class DB{
        protected $table;
        protected $params;
        protected $sql;
        protected $sqlTable='';
        protected $sqlSelect='select ';
        protected $sqlWhere='';
        public function table($table){
            $this->table=$table;
            $this->sqlTable="from $this->table";
            return $this;
        }
        public function select($arraySelect){
            foreach ($arraySelect as $key => $value) {
                $this->sqlSelect.=$value.",";
            }
            $this->sqlSelect=rtrim($this->sqlSelect,',');
            return $this;
        }
        public function where($column,$if,$value){
           if($this->sqlWhere!=""){
                $this->sqlWhere.="AND $column $if $value ";
           }else{
                $this->sqlWhere.=" WHERE $column $if $value ";
           }
           return $this;
        }
        public function mapSql(){
            if($this->sqlSelect!="select "){
                $this->sql=$this->sqlSelect;
            }
            if($this->sqlTable!=""){
                $this->sql.=" ".$this->sqlTable;
            }
            if($this->sqlWhere!=""){
                $this->sql.=$this->sqlWhere;
            }
            echo $this->sql;
        }
    }
?>