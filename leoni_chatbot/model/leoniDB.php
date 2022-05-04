<?php

class leoniDB extends DBConnection {

    public function __construct() {
        parent::__construct();
    }

    public function employeeInfo($email) {
       	$statement = $this->_db->prepare("SELECT * FROM employee where email=?");  
        $statement->bindParam(1, $email);
	    $statement->execute();
        $routesbyday = $statement->fetchAll();
		return $routesbyday;
    }	
    
    public function saveRequest($request) {
        $statement = $this->_db->prepare("INSERT INTO request(peoplesoft_id, id_emp, role, absence_type, start_date, 
                                            end_date, num_days, date_sent, status_request, approval_from) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $statement->bindParam(1, $request['peoplesoft_id']);
        $statement->bindParam(2, $request['id_emp']);
        $statement->bindParam(3, $request['role']);
        $statement->bindParam(4, $request['absence_type']);
        $statement->bindParam(5, $request['start_date']);
        $statement->bindParam(6, $request['end_date']);
        $statement->bindParam(7, $request['num_days']);
        $statement->bindParam(8, $request['date_sent']);
        $statement->bindParam(9, $request['status_request']);
        $statement->bindParam(10, $request['approval_from']);
        $statement->execute();
    }
    
    public function getRequestById($id) {
        $datos = array();
        $stmt = $this->_db->prepare("SELECT * FROM request WHERE peoplesoft_id = ?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $datos = $stmt->fetchAll();
        if(isset($datos[0])){
            return true;
        }else{
            return false;
        }
    }

    public function readQuestions(){
        $datos = array();
        $result = $this->_db->query("SELECT * FROM question");
        $datos = $result->fetchAll();
        return $datos;
    }

    function ReadOneAnswerById($id_answer){
        $datos = array();        
        if($stmt = $this->_db->prepare("SELECT * FROM answer WHERE id_answer = ?")){
        $stmt->bindParam(1, $id_answer);
        $stmt->execute();
        $datos = $stmt->fetchAll();
        return $datos[0];
        }
    }
    
    function readEmpInfo($id_emp){
        $datos = array();
        if($stmt = $this->_db->prepare("SELECT booking_manager, first_name, last_name, email FROM employee WHERE id_emp = ?")){
            $stmt->bindParam(1, $id_emp);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            return $datos[0];
        }
    }


    function managerEmail($id_emp){
        $datos = array();
        if($stmt = $this->_db->prepare("SELECT email FROM employee WHERE id_emp = ?")){
            $stmt->bindParam(1, $id_emp);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            return $datos[0];
        }
    }
	
    public function PTO_approved($PTO_id_request, $PTO_comments){
		try {
			$statement = $this->_db->prepare("UPDATE request SET status_request='Approved', date_approved=CURDATE(), comments='".$PTO_comments."' WHERE id_request=$PTO_id_request");
			$statement->execute();
			$statement2 = $this->_db->prepare("CALL updateDays(".$PTO_id_request.")");
			$statement2->execute();
		} catch(Exception $ex) {
            echo "Exception " . $ex;
        }
    }
    public function PTO_denied($PTO_id_request, $PTO_comments){
		try {
			$statement = $this->_db->prepare("UPDATE request SET status_request='Denied', date_approved=CURDATE(), comments='".$PTO_comments."' WHERE id_request=$PTO_id_request");
			$statement->execute();
		} catch(Exception $ex) {
            echo "Exception " . $ex;
        }
    }
    
    public function PTO_dates($idEmp,$PTO_date){
		$statement = $this->_db->prepare("CALL math($idEmp,'$PTO_date')");
		$statement->execute();
		$result = $statement->fetchAll();
        return $result[0];
	}
	

    function getIdRequest ($peoplesoft_id){
        $datos = array();
        if($stmt = $this->_db->prepare("SELECT id_request FROM request WHERE peoplesoft_id = ?")){
            $stmt->bindParam(1, $peoplesoft_id);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            return $datos[0];
        }
    }

    function readHolidays(){
		$datos = array();
        $result = $this->_db->query("SELECT rest_date FROM holidays");
        $datos = $result->fetchAll();
        return $datos;
	}
	
	
	function EmpRequest($peoplesoft_id){
		$datos = array();
		if($stmt = $this->_db->prepare("SELECT id_emp, absence_type FROM request WHERE peoplesoft_id = ?")){
			$stmt->bindParam(1, $peoplesoft_id);
			$stmt->execute();
            $datos = $stmt->fetchAll(); 
			return $datos[0];
		}
		
	}

    public function PTO_lineMail($lineId,$repId,$idEmp){
        if($repId!='0'){
        $statement = $this->_db->prepare("SELECT (SELECT CONCAT(first_name,' ',last_name) from employee WHERE id_emp=".$idEmp.") AS report, A.email, CONCAT(A.first_name,' ',A.last_name) as name, COUNT(B.id_request) AS amount FROM employee A INNER JOIN request B ON A.id_emp=B.approval_from WHERE A.id_emp=".$repId." AND B.status_request='Submitted'");
        } else{
        $statement = $this->_db->prepare("SELECT (SELECT CONCAT(first_name,' ',last_name) from employee WHERE id_emp=".$idEmp.") AS report, A.email, CONCAT(A.first_name,' ',A.last_name) as name, COUNT(B.id_request) AS amount FROM employee A INNER JOIN request B ON A.id_emp=B.approval_from WHERE A.id_emp=".$lineId." AND B.status_request='Submitted'");
        }
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    public function Level($id_emp){
        $datos = array();
        if($stmt = $this->_db->prepare("SELECT level FROM employee WHERE id_emp = ?")){
			$stmt->bindParam(1, $id_emp);
			$stmt->execute();
            $datos = $stmt->fetchAll(); 
			return $datos[0];
		}
    }    


    public function PTO_empInfo($id_emp){
        $result = array();
        $statement = $this->_db->prepare("CALL employeePTOInfo(".$id_emp.")");
        $statement->execute();
        $result = $statement->fetchAll();
        return $result[0];
    }


    public function PTO_diffDays($PTO_sD,$PTO_eD){
        $statement = $this->_db->prepare("CALL diffDays('".$PTO_sD."','".$PTO_eD."')");
		$statement->execute();
		$result = $statement->fetchAll();
        return $result[0];
    }


    public function PTO_anniversary($idEmp){
        $statement = $this->_db->prepare("SELECT TIMESTAMPDIFF(YEAR,hiring_date,CURDATE()) AS seniority FROM employee WHERE id_emp=".$idEmp);
        $statement->execute();
		$result = $statement->fetchAll();
        return $result[0];
    }
	
	  public function PTO_findR($idEmp){
        $statement = $this->_db->prepare("SELECT * FROM request WHERE absence_type='10th Anniversary' AND status_request IN('Approved','Submitted') AND id_emp=".$idEmp);
        $statement->execute();
		$result = $statement->fetchAll();
        return $result;
    }
	

}