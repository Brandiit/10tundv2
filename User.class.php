<?php 
class User {
    
    private $connection;
    
    //see fn käivitub kui tekitame uue instantsi
    // new User()
    function __construct($mysqli){
        
        // $this on see klass e User 
        // -> connection on klassi muutuja
        $this->connection = $mysqli;
        
    }
    
    function logInUser($email, $hash){
        
        $response = new StdClass();
        
        $stmt = $this->connection->prepare("SELECT id FROM user_sample WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->bind_result($id);
        $stmt->execute();
        
        // kas selline email on
        if(!$stmt->fetch()){
            
            // ei ole
            $error = new StdClass();
            $error->id = 0;
            $error->message = "Sellist emaili ei ole";
            
            $response->error = $error;
            
            //lõpetan
            return $response;
            
        }
        
        //**************************** 
        //******* OLULINE ************
        //****************************
        // paneme eelmise käsu kinni
        $stmt->close();
        
        $stmt = $this->connection->prepare("SELECT id, email FROM user_sample WHERE email=? AND password=?");
        //echo $this->connection->error;
        $stmt->bind_param("ss", $email, $hash);
        $stmt->bind_result($id_from_db, $email_from_db);
        $stmt->execute();
        if($stmt->fetch()){
            
            //selline kasutaja olemas
            $success = new StdClass();
            $success->message = "Sai edukalt sisse logitud";
            
            $user = new StdClass();
            $user->id = $id_from_db;
            $user->email = $email_from_db;
            
            $success->user = $user;
            
            $response->success = $success;
                       
        }else{
            // vale parool
            $error = new StdClass();
            $error->id = 1;
            $error->message = "Vale parool";
            
            $response->error = $error;
        }
        $stmt->close();
        
        return $response;
             
    }
    
    function createUser($create_email, $hash){
        
        //objekt kus tagastame errori(id, message) või success'i (message)
        $response = new StdClass();
        
        $stmt = $this->connection->prepare("SELECT id FROM user_sample WHERE email = ?");
        $stmt->bind_param("s", $create_email);
        $stmt->bind_result($id);
        $stmt->execute();
        
        // kas saime rea andmeid
        if($stmt->fetch()){
            
            // email on juba olemas
            $error = new StdClass();
            $error->id = 0;
            $error->message = "Email on juba kasutusel";
            
            $response->error = $error;
            
            // pärast return käsku, fn'i enam edasi ei vaadata
            return $response;
            
        }
        
        //siia olen jõudnud siis kui emaili ei olnud
        $stmt = $this->connection->prepare("INSERT INTO user_sample (email, password) VALUES (?,?)");
        $stmt->bind_param("ss", $create_email, $hash);
        if($stmt->execute()){
            // sisestamine õnnestus
            $success = new StdClass();
            $success->message = "Kasutaja edukalt loodud";
            
            $response->success = $success;
            
        }else{
            //ei õnnestunud
            $error = new StdClass();
            $error->id = 1;
            $error->message = "Midagi läks katki";
            
            $response->error = $error;
        }
        $stmt->close();
        
        return $response;
        
    }
    
    
    
    
} ?>