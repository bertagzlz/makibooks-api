<?php
class Database{
    
    // CHANGE THE DB INFO ACCORDING TO YOUR DATABASE

    //private $db_host = 'awseb-e-ckdjb8rum3-stack-awsebrdsdatabase-rupg1aujpysx.cbldhoc4glmk.eu-west-3.rds.amazonaws.com';
    //private $db_name = 'ebdb'; // elastic bean

    private $db_host = 'www.db4free.net'; //'mysqldb';
    private $db_name = 'makibooks';

    private $db_username = 'joserod';
    private $db_password = 'Ac640100jr';
    
    public function dbConnection(){
        
        try{
            $conn = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name,$this->db_username,$this->db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e){
            echo "Connection error ".$e->getMessage(); 
            exit;
        }
          
    }
}
