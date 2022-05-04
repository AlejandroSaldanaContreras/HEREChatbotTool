<?php
    require_once("database.class.php");
    Class Employee extends Database{
        var $id_emp;
        var $username;

        
        function readEmployee(){
            $this->connect();
            $datos = array();
            $result = $this->con->query("SELECT id_emp, username FROM employee");
            $datos = $result->fetchAll();  
            $this->close();
            return $datos;
        }
    }

    $employee = new Employee;
?>