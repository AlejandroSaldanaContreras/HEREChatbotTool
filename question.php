<?php
    include("class/question.class.php");
    include("class/answer.class.php");
    include("class/employee.class.php");
    $action = (isset($_GET["action"]))?$_GET["action"]: null;
        switch($action){
            case 'form':
                $id_question = (isset($_GET["id_question"]))?$_GET["id_question"]: null;
                $id_answer = (isset($_GET["id_answer"]))?$_GET["id_answer"]: null;

                $data_question = [
                    "question" =>"",
                    "date_created"=>"",
                    "date_modified"=>"",
                    "owner"=>"",
                    "id_answer"=>""
                ];

                $data_answer = [
                    "answer" =>"",
                    "date_created" =>"",
                    "date_modified"=>"",
                    "action" => ""
                ];

                $answers = $answer->readAnswer();
                if(isset($_GET["message"]))
                {
                    $message = "You can not save a question with no answer or with two answers, please try again";
                }
                else
                {
                    $message = "";
                }
               
                if (is_numeric($id_question))
                {
                    
                    $question ->setIdQuestion($id_question);
                    $data_question = $question->readOneQuestion();
                    $answer ->setIdAnswer($data_question["id_answer"]);
                    $data_answer = $answer->readOneAnswerById();
                    $script = "question.php?action=modify";
                    include("views/form.php");
                }
                else 
                {
                    $script="question.php?action=new"; 
                    include("views/form.php");
                }
                break;
         
            case 'new':                
                if( (!strlen(trim($_POST['answer'])) && !empty($_POST['answer_selected'])) || (empty($_POST['answer_selected']) && strlen(trim($_POST['answer']))) ) 
                {
                    $date = date('Y-m-d');
                    $datos_answer = [
                        "answer" => (strlen(trim($_POST['answer'])))?$_POST['answer']:$_POST["answer_selected"],
                        "date_created"=>$date,
                        "date_modified"=>"",
                        "action" => "",
                    ];
                    $answer->setAnswer($datos_answer['answer']);
                    $answer->setDateCreated($datos_answer['date_created']);
                    if($answer->ReadOneAnswer()){
                        $actual_answer = $answer->ReadOneAnswer();
                        $id_answer = $actual_answer['id_answer'];
                    }else{
                        $answer->CreateAnswer();
                        $actual_answer = $answer->ReadOneAnswer();
                        $id_answer = $actual_answer['id_answer'];
                    }
                    
                    
                    $datos_question = [
                        "question" => (isset($_POST["question"]))?$_POST["question"]:"some question",
                        "date_created"=> $date,
                        "date_modified"=>"",
                        "owner"=> (isset($_POST["id_emp"]))?$_POST["id_emp"]:null,
                        "id_answer"=> $id_answer,
                    ];

                    $question->setQuestion($datos_question['question']);
                    $question->setDateCreated($datos_question['date_created']);
                    $question->setOwner($datos_question['owner']);
                    $question->setIdAnswer($datos_question['id_answer']);
                    $question->createQuestion();
                    header("Location:question.php");
                    
                }
                elseif ((!strlen(trim($_POST['answer'])) && empty($_POST['answer_selected'])) || (strlen(trim($_POST['answer'])) && !empty($_POST['answer_selected'])))
                {
                    header("Location:question.php?action=form&message=1");

                }
                break;

            case 'modify':
                if( (!strlen(trim($_POST['answer'])) && !empty($_POST['answer_selected'])) || (empty($_POST['answer_selected']) && strlen(trim($_POST['answer']))) )
                {
                    $date = date('Y-m-d');
                    $datos_answer = [
                        "id_answer" => ($_POST["id_answer"]),
                        "answer" => (strlen(trim($_POST['answer'])))?$_POST['answer']:$_POST["answer_selected"],
                        "date_created"=> (isset($_POST["date_created_answer"]))?$_POST["date_created_answer"]:"some answer",
                        "date_modified"=>$date,
                        "action" => "",
                    ];
                    $answer->setIdAnswer($datos_answer['id_answer']);
                    $answer->setAnswer($datos_answer['answer']);
                    $answer->setDateCreated($datos_answer['date_created']);
                    $answer->setDateModified($datos_answer['date_modified']);
                    if($answer->ReadOneAnswer()){
                        $actual_answer = $answer->ReadOneAnswer();
                        $id_answer = $actual_answer['id_answer'];
                    }else{
                        $answer->ModifyAnswer();
                        $actual_answer = $answer->ReadOneAnswer();
                        $id_answer = $actual_answer['id_answer'];
                    }

                    $datos_question = [
                        "id_question"  => ($_POST["id_question"]),
                        "question" => (isset($_POST["question"]))?$_POST["question"]:"some question",
                        "date_created"=> (isset($_POST["date_created_question"]))?$_POST["date_created_question"]:"some answer",
                        "date_modified"=>$date,
                        "owner"=> (isset($_POST["id_emp"]))?$_POST["id_emp"]:null,
                        "id_answer"=> $id_answer,
                    ];
                    $question->setIdQuestion($datos_question['id_question']);
                    $question->setQuestion($datos_question['question']);
                    $question->setDateCreated($datos_question['date_created']);
                    $question->setDateModified($datos_question['date_modified']);
                    $question->setOwner($datos_question['owner']);
                    $question->setIdAnswer($datos_question['id_answer']);
                    $question->modifyQuestion();
                    header("Location:question.php");
                    break;
                }
                elseif ((!strlen(trim($_POST['answer'])) && empty($_POST['answer_selected'])) || (strlen(trim($_POST['answer'])) && !empty($_POST['answer_selected'])))
                {
					header("Location:question.php?action=form&message=1");
                }
				break;

            case 'delete':
                $id_question = (isset($_GET["id_question"]))?$_GET["id_question"]: null;
                if (is_numeric($id_question))
                    {
                        $question ->setIdQuestion($id_question);
                        $question ->deleteQuestion();
                    }
                    header("Location:question.php");
                    break;
            
            case 'show':
                default:
                    $data_employee = $employee->readEmployee();
                    $data_answer = $answer->readAnswer();
                    $data = $question->readQuestions();
                    include("views/index.php");
                    break; 
                    
    }

?>