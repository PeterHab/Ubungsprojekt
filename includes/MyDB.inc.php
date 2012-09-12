<?php
require_once("mySql.inc.php");
##################################
##      Datenbankklasse         ##
##  Thor: Peter Haberkorn       ##
##################################

class MyDB{
    private $connection;
    
    //Connect function
    public function connect($root, $db, $user, $pass){
        @$this->connection = new mysqli($root, $db, $user, $pass);
        if (mysqli_connect_errno()) throw new Exception(__METHOD__.'::'.mysqli_connect_error ());
    }
    
    //Close connection function.
    public function close(){
        if(!$this->connection){ 
            return false;
        }
        $this->connection->close();
    }
    
    public function query($sql, $return='affected', $result_mode=MYSQLI_USE_RESULT){
        if(!$this->connection){
            throw new Exception('Connection missing');
        }
        if ($result = $this->connection->query($sql, $result_mode)){
            $data = array();
            if($return=='affected'){
                $data = $this->connection->affected_rows;
            }
            elseif($return=='num'){
                $data = $result->num_rows;
            }
            elseif($return=='id'){
                $data = $this->connection->insert_id;
            }
            elseif($return=='assoc'){
                while($row= $result->fetch_assoc()){
                    $data[] = $row;
                }
            }
            elseif($return=='numeric'){
                while ($row = $result->fetch_assoc()){
                    $row = $result->fetch_array(MYSQLI_NUM);
                }
            }
            elseif($return=='fields'){
                while ($row = $result->fetch_fields()){
                    $data[] = $row;
                }  
            }
            if(is_object($result)){
                $result->close();
            }  
        }
        else{
            throw new Exception(__METHOD__.'::'.$this->connection->error.'::'.$sql);
        };
        return $data;
    }
    
    public function startTransaction($isolation_level="SERIALIZABLE"){
        if(!$this->connection){
            return false;
            $isolation_level = strtoupper($isolation_level);
            $ok = $this->query("SET TRANSACTION ISOLATION LEVEL{$isolation_level};", "bool");
            $ok = ($ok && $this->query("SET AUTOCOMMIT = 0;", "bool"));
            return($ok && $this->query("START TRANSACTION;", "bool"));
        }
    }
    
    public function commit(){
        if(!$this->connection){
            return false;
        }
        if(!$this->query("COMMIT;", "bool")){
            return false;
        }
        $this->query("SET AUTOCOMMIT=1;", "bool");
        return true;
    }
    
    public function rollback(){
        if(!$this->connection){
            return false;
        }
        if(!$this->query("ROLLBACK;","bool")){
            return false;
        }
        $this->query("SET AUTOCOMMIT=1;");
        return true;
        
    }
}
?>