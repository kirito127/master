<?php
include_once 'connection.php';
class DatabaseOperation extends DatabaseConnection{

        //fetch record by custom query : select * from tbl_foo
        public function fetch_query($query){
            if($query){ //if query is not empty it execute else return false
                $this->openConn();
                $stmt = $this->pdo->query($query);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);                    
            }else{return false;}
        }
}
?>