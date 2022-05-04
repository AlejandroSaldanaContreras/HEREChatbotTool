<?php
    require_once("database.class.php");
    Class Question extends Database{
        var $id_question;
        var $question;
        var $date_created;
        var $date_modified;
        var $owner;
        var $id_answer;

        //? GETTERS
        function getIdQuestion(){return $this->id_question;}
        function getQuestion(){return $this->question;}
        function getDateCreated(){return $this->date_created;}
        function getDateModified(){return $this->date_modified;}
        function getOwner(){return $this->owner;}
        function getIdAnswer(){return $this->id_answer;}

        //? SETTERS
        function setIdQuestion($id_question){return $this->id_question = $id_question;}
        function setQuestion($question){return $this->question = $question;}
        function setDateCreated($date_created){return $this->date_created = $date_created;}
        function setDateModified($date_modified){return $this->date_modified = $date_modified;}
        function setOwner($owner){return $this->owner = $owner;}
        function setIdAnswer($id_answer){return $this->id_answer = $id_answer;}


        function createQuestion(){
            $this-> connect();
                if ($stmt = $this->con->prepare("INSERT INTO question(question, date_created, date_modified, owner, id_answer) VALUES (?,?,?,?,?)")) {
                    $question = $this->getQuestion();
                    $date_created = $this->getDateCreated();
                    $date_modified = $this->getDateModified();
                    $owner = $this->getOwner();
                    $id_answer = $this->getIdAnswer();
                    $stmt->bindParam(1, $question);
                    $stmt->bindParam(2, $date_created);
                    $stmt->bindParam(3, $date_modified);
                    $stmt->bindParam(4, $owner);
                    $stmt->bindParam(5, $id_answer);
                    $stmt->execute();
                    
                }
            
            
            $this->close();
        }
        
        function deleteQuestion(){
            $this->connect();
            if ($stmt = $this->con->prepare("DELETE FROM question WHERE id_question=?")) {
                $id_question = $this->getIdQuestion();
                $stmt->bindParam(1,$id_question);
                $stmt->execute();
            }    
            $this->close();
            
        }

        function modifyQuestion(){
            $this->connect();
            $sql = "";
            $sql = "UPDATE question SET question=?, date_created=?, date_modified=?, owner=?, id_answer=? WHERE id_question=?";
                    if ($stmt = $this->con->prepare($sql)){
                        $datos=[
                            'id_question' => $this->getIdQuestion(),
                            'question' => $this->getQuestion(),
                            'date_created' => $this->getDateCreated(),
                            'date_modified' => $this->getDateModified(),
                            'owner' => $this->getOwner(),
                            'id_answer' => $this->getIdAnswer(),
                            
                        ];
                        
                       
                        $stmt->bindParam(1, $datos['question']);
                        $stmt->bindParam(2, $datos['date_created']);
                        $stmt->bindParam(3, $datos['date_modified']);
                        $stmt->bindParam(4, $datos['owner']);
                        $stmt->bindParam(5, $datos['id_answer']);
                        $stmt->bindParam(6, $datos['id_question']);
                        
                        $stmt->execute();
                    }
                    
                    $this->close();
            }

            function readQuestions(){
                $this->connect();
                $datos = array();
                $result = $this->con->query("SELECT * FROM question");
                $datos = $result->fetchAll();  
                $this->close();
                return $datos;
            }

            function ReadOneQuestion(){
                $this-> connect();
                $datos = array();
                
                if($stmt = $this->con->prepare("SELECT * FROM question WHERE id_question = ?")){
                $id_question = $this->getIdQuestion();
                $stmt->bindParam(1, $id_question);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                return $datos[0];
                //$stmt->close();
                }
                $this->close();
            }

    }
        $question = new Question;


?>