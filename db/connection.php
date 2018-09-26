<?php
class DatabaseConnection{
    public $pdo = null;
    public function openConn(){
        try{
            $host = 'localhost';//'sg1-ls2.a2hosting.com'; //
            $db   = 'dealahos_wp601';
            $user = 'root';//'dealahos_user'; //
            $pass = '';//'kirito127';
            $charset = 'utf8mb4';
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";    
            $opt  = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false
                    );
            $this->pdo = new PDO($dsn, $user, $pass, $opt);
        } catch(PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    public function closeConn(){
        $this->pdo = null;
    }
}




?>