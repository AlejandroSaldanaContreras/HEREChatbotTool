<?php
    require_once("database.class.php");
    Class Answer extends Database{
        var $id_answer;
        var $date_created;
        var $date_modified;
        var $action;
        var $answer;

        //? GETTERS
        function getIdAnswer(){return $this->id_answer;}
        function getDateCreated(){return $this->date_created;}
        function getDateModified(){return $this->date_created;}
        function getAction(){return $this->action;}
        function getAnswer(){return $this->answer;}



        //? SETTERS
        function setIdAnswer($id_answer){return $this->id_answer = $id_answer;}
        function setDateCreated($date_created){return $this->date_created = $date_created;}
        function setDateModified($date_modified){return $this->date_modified = $date_modified;}
        function setAction($action){return $this->action = $action;}
        function setAnswer($answer){return $this->answer = $answer;}

        function createAnswer(){
            $this-> connect();
                if ($stmt = $this->con->prepare("INSERT INTO answer(date_created, date_modified, action, answer) VALUES (?,?,?,?)")) {
                    $date_created = $this->getDateCreated();
                    $date_modified = $this->getDateModified();
                    $action = $this->getAction();
                    $answer = $this->getAnswer();
                    $stmt->bindParam(1, $date_created);
                    $stmt->bindParam(2, $date_modified);
                    $stmt->bindParam(3, $action);
                    $stmt->bindParam(4, $answer);
                    $stmt->execute();
                    
                }
            
            
            $this->close();
        }

        function deleteAnswer(){
            $this->connect();
            if ($stmt = $this->con->prepare("DELETE FROM answer WHERE id_answer=?")) {
                $id_answer = $this->getIdAnswer();
                $stmt->bindParam(1,$id_answer);
                $stmt->execute();
            }    
            $this->close();
            
        }

        function ReadOneAnswer(){
            $this-> connect();
            $datos = array();
            
            if($stmt = $this->con->prepare("SELECT * FROM answer WHERE answer = ?")){
            $answer = $this->getAnswer();
            $stmt->bindParam(1, $answer);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            return $datos[0];
            //$stmt->close();
            }
            $this->close();
        }

        function ReadOneAnswerById(){
            $this-> connect();
            $datos = array();
            
            if($stmt = $this->con->prepare("SELECT * FROM answer WHERE id_answer = ?")){
            $id_answer = $this->getIdAnswer();
            $stmt->bindParam(1, $id_answer);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            return $datos[0];
            }
            $this->close();
        }

        function modifyAnswer(){
            $this->connect();
            $sql = "";
            $sql = "UPDATE answer SET date_created=?, date_modified=?, action=?, answer=? WHERE id_answer=?";
                    if ($stmt = $this->con->prepare($sql)){
                        $datos=[
                            'date_created' => $this->getDateCreated(),
                            'date_modified' => $this->getDateModified(),
                            'action' => $this->getAction(),
                            'answer' => $this->getAnswer(),
                            'id_answer' => $this->getIdAnswer(),
                            
                        ];
                        
                        
                        $stmt->bindParam(1, $datos['date_created']);
                        $stmt->bindParam(2, $datos['date_modified']);
                        $stmt->bindParam(3, $datos['action']);
                        $stmt->bindParam(4, $datos['answer']);
                        $stmt->bindParam(5, $datos['id_answer']);
                        
                        $stmt->execute();
                    }
                    
                    $this->close();
            }

            function readAnswer(){
                $this->connect();
                $datos = array();
                $result = $this->con->query("SELECT * FROM answer");
                $datos = $result->fetchAll();  
                $this->close();
                return $datos;
            }


        

    }
    
    $answer = new Answer;
?>