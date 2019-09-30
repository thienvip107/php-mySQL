<?php
    class database{
        protected $connect;
        protected $database;
        protected $resultQuery;
        public function __construct($params){
            $link = mysqli_connect($params['server'],$params['username'],$params['password'],$params['database']);

            if(!$link){
                die('Fail Connect : '.mysqli_error());
            }else{
                $this->connect = $link;
                
                $this->table = $params['table'];

            }


        }
        public function showInfo(){
            $result = array();
             $sql = mysqli_query($this->connect,"select * from `$this->table`");
             while($row= mysqli_fetch_assoc($sql)){
                  $result[] = $row;
             }
             return $result;

        }

        public function __destruct(){
            mysqli_close($this->connect);
        }

        // INSERT DATA

        public function insert($data,$type ){
            if($type == 'single'){
                $newQuery 	= $this->createInsertSQL($data);
                $query 		= "INSERT INTO `$this->table`(".$newQuery['col'].") VALUES (".$newQuery['row'].")";
                $this->query($query);
            }else{
                foreach($data as $value){
                    $newQuery = $this->createInsertSQL($value);
                    $query = "INSERT INTO `$this->table`(".$newQuery['col'].") VALUES (".$newQuery['row'].")";
                    $this->query($query);
                }
            }
        }

        public function createInsertSQL($array){
            $query = array();
            $cols='';$rows='';
            foreach ($array as $key => $value) {
                $cols .= ", `$key` ";
                 
                $rows .= ", '$value'";
            }
            $cols = substr($cols,2);
            $rows = substr($rows,2);
            $query['col']= $cols;
            $query['row']= $rows;
            return $query;

        }


        // UPDATE DATA 

        public function update($data,$where){
            $newSet = $this->createUpdateSQL($data);
            $newWhere = $this->createWhereUpdateSQL($where);
            $query = "UPDATE `$this->table` set ".$newSet." WHERE $newWhere";
            $this->query($query);
        }

        public function createUpdateSQL($data){
            $query='';
            foreach ($data as $key => $value) {
                $query .=", `$key` = '$value'";
            }
            $query = substr($query,2);
            return $query;
        }

        public function createWhereUpdateSQL($data){
            $where =array();
            foreach ($data as $key => $value) {
                
               $where[]= "`$value[0]`= '$value[1]'";
               if(isset($value[2])){
                    $where[]=$value[2];
               }
               

            }
            $where = implode(" ",$where);
            echo $where;
            return $where;
        }

        // DELETE DATA 

        public function delete($where){
            
            $newWhere = $this->createWhereDeleteSQL($where);
            $query = "delete from `$this->table` where `id` in ($newWhere)";
            $this->query($query);
            
        }

        public function createWhereDeleteSQL($data){
                $newWhere ='';
                foreach ($data as $key => $value) {
                    $newWhere .= "'".$value."', ";
                }
                $newWhere .="'0'";
                return $newWhere;

        }


        // Query
        public function query($query){
            $this->resultQuery= mysqli_query($this->connect,$query);
             
            return $this->resultQuery;
        }

        public function listRecord($resultQuery=null){
            $result = array();
            $resultQuery = ($resultQuery == null) ? $this->resultQuery : $resultQuery;
            if(mysqli_num_rows($resultQuery) > 0){
                while($row = mysqli_fetch_assoc($resultQuery)){
                    $result[] = $row;
                }
                mysqli_free_result($resultQuery);
            }

            
            
            return $result;
        }
        

        public function singleRecord($resultQuery=null){
            $result =array();
            $resultQuery = ($resultQuery ==null) ?$this->resultQuery : $resultQuery;
            if(mysqli_num_rows($resultQuery)>0){
                $result = mysqli_fetch_assoc($resultQuery);
                   
                mysqli_free_result($resultQuery);
                }
                 return $result;
            }
            
           

        }

    


    