<?php
    class DB{
        protected $table;
        protected $params=[];
        protected $sql;
        protected $sqlTable='';
        protected $sqlSelect='select ';
        protected $select;
        protected $sqlWhere=[];
        protected $where=[];
        protected $sqlOrWhere=[];
        protected $orWhere=[];
        protected $sqlSkip='';
        protected $sqlTake='';
        protected $connect;
        public function __construct()
        {   
            $this->connect=new PDO('mysql:host=localhost;dbname=duan1;charset=utf8','root',"");
            $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
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
        public function where($column,$if='=',$value=''){
            if(is_callable($column)){
                $column($this);
                return $this;
            }
            if(is_array($column)){
                foreach ($column as $columnKey => $columnValue) {
                    if(count($columnValue)!==3) die('lỗi nè');
                    list($sub_column,$subIf,$subValue)=$columnValue;
                    $subIf=$this->prepareParam($subIf);
                    $sub_column=$this->prepareParam($sub_column);
                    if(!empty($this->sqlWhere)){
                        $this->sqlWhere[]="AND $sub_column $subIf ? ";
                        $keysqlWhere=array_keys($this->sqlWhere);
                        $keyLast=end($keysqlWhere);
                        $this->where[$keyLast]['value']=$subValue;
                    }else{
                        $this->sqlWhere[]=" WHERE $sub_column $subIf ? ";
                        $this->where[0]['value']=$subValue;
                    }
                    
                }
                return $this;
            }
            $if=$this->prepareParam($if);
            $column=$this->prepareParam($column);
            if(!empty($this->sqlWhere)){
                $this->sqlWhere[]="AND $column $if ? ";
                $keysqlWhere=array_keys($this->sqlWhere);
                $keyLast=end($keysqlWhere);
                $this->where[$keyLast]['value']=$value;
            }else{
                $this->sqlWhere[]=" WHERE $column $if ? ";
                $this->where[0]['value']=$value;
            }
           return $this;
        }
        public function orWhere($column,$if='=',$value=''){
            if(is_callable($column)){
                $column($this);
                return $this;
            }
            if(is_array($column)){
                foreach ($column as $columnKey => $columnValue) {
                    if(count($columnValue)!==3) die('lỗi nè');
                    list($sub_column,$subIf,$subValue)=$columnValue;
                    $subIf=$this->prepareParam($subIf);
                    $sub_column=$this->prepareParam($sub_column);
                    if(!empty($this->sqlOrWhere)){
                        $this->sqlOrWhere[]="OR $sub_column $subIf ? ";
                        $keysqlOrWhere=array_keys($this->sqlWhere);
                        $keyLast=end($keysqlOrWhere);
                        $this->orWhere[$keyLast]['value']=$subValue;
                    }else{
                        $this->sqlOrWhere[]=" OR $sub_column $subIf ? ";
                        $this->orWhere[0]['value']=$subValue;
                    }
                    
                }
                return $this;
            }
            $if=$this->prepareParam($if);
            $column=$this->prepareParam($column);
            if(!empty($this->sqlOrWhere)){
                $this->sqlOrWhere[]="OR $column $if ? ";
                $keysqlOrWhere=array_keys($this->sqlOrWhere);
                $keyLast=end($keysqlOrWhere);
                $this->orWhere[$keyLast]['value']=$value;
            }else{
                $this->sqlOrWhere[]="OR $column $if ? ";
                $this->orWhere[0]['value']=$value;
            }
            return $this;
        }
        public function skip($skip){
            if(!is_numeric($skip)) die('skip không phải là số');
            $skip=$this->prepareParam($skip);
            $this->sqlSkip=" offset $skip";
            $this->skip=$skip;
            return $this;
        }
        public function take($take){
            if(!is_numeric($take)) die('take không phải là số');
            $take=$this->prepareParam($take);
            $this->sqlTake=" limit $take";
            $this->take=$take;
            return $this;
        }
        public function prepareParam($param){
            $param=str_replace("'","",$param);
            $param=str_replace('"',"",$param);
            $param=str_replace('-',"",$param);
            return $param;
        }
        public function mapSql(){
            if($this->sqlSelect!="select "){
                $this->sql=$this->sqlSelect;
            }
            if($this->sqlTable!=""){
                $this->sql.=" ".$this->sqlTable;
               
            }
            if(!empty($this->sqlWhere)){
                $whereText='';
                foreach ($this->sqlWhere as $key => $value) {
                    $whereText.=$value;
                    $this->params[]=$this->where[$key]['value'];
                }
                $this->sql.=$whereText;
            }
            if(!empty($this->sqlOrWhere)){
                $orWhereText='';
                foreach ($this->sqlOrWhere as $key => $value) {
                    $orWhereText.=$value;
                    $this->params[]=$this->orWhere[$key]['value'];
                }
                $this->sql.=$orWhereText;
            }
            if($this->sqlTake!=''){
                $this->sql.=" ".$this->sqlTake;
            }
            if($this->sqlSkip!=''){
                $this->sql.=" ".$this->sqlSkip;
            }
           
            $prepare=$this->connect->prepare($this->sql);
            $prepare->execute($this->params);
            $data=$prepare->fetchAll();
            echo '<pre>';
            print_r($data);
        }
    }
?>