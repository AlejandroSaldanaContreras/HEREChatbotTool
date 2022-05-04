<?php
    session_start();
    class Database{
        var $host;
        var $port;
        var $user;
        var $password;
        var $db;
        var $con;


        function connect(){
            $this->host = "localhost";
            $this->user= "prueba";
            $this->password = "here2021";
            $this->db = "prueba"; 
            $this->con = new PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->user, $this->password);
        }

        function close(){   
            $this->con = null;
        }

    }
?>