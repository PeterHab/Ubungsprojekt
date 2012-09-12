<?php
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
    }

    
}
?>