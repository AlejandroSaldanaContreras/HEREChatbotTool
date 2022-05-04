<?php
require_once 'model/leoniRequests.php';
$leoniRequests=new leoniRequests();

require_once 'model/leoniDB.php';
$leoniDB=new leoniDB();


$getMessagesURL="https://webexapis.com/v1/messages/";
$postMessagesURL="https://webexapis.com/v1/messages";
$getCardInfo = "https://webexapis.com/v1/attachment/actions/";
$getPeopleInfo = "https://webexapis.com/v1/people/";
$deleteMessage = "https://webexapis.com/v1/messages/";
$urllocal="https://saldanacbots.loca.lt";
$filesWebex="";
$htmlWebex="";
$id_question="";


$method = $_SERVER['REQUEST_METHOD'];
if($method == 'POST'){
	
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);
	$resource = $json->resource;
	$email = $json->data->personEmail;
	$personId = $json->data->personId;
	$messageid = $json->data->id;
	$messageid2 = $json->data->messageId;
	$getEmail = $leoniRequests->getRequests($getPeopleInfo, $personId);
	$getCard = $leoniRequests->getRequests($getCardInfo, $messageid);
	$getMessage = $leoniRequests->getRequests($getMessagesURL,$messageid);
	$id_used = "";
	$employee = $leoniDB->employeeInfo($email);
	$id_emp = $employee[0]['id_emp'];



	//Create a log file to know if the chatbot has a connection to the code and save the repsonse   
	$log_time = date('Y-m-d h:i:sa');
	$log_msg = $requestBody;
	$log_filename = "log";
	if (!file_exists($log_filename)) {
			mkdir($log_filename, 0777, true);
	}
	$log_file_data = $log_filename.'/access_log_' . date('d-M-Y') . '.log';
	file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
	
	//Change all the input text by the user to lowercase 
	//$messagelowercase=strtolower($getMessage['text']);
	//$str = preg_replace('/\s+/', ' ',$messagelowercase);
	//$message = preg_replace('/[^A-Za-z0-9 .,áéíóúüÁÉÍÓÚÜñÑ]/', '', $str);	
	
	$messageraw = $getMessage['text'];
	$message = preg_replace('/\s+/', '',$messageraw);
	$message = strtolower($message);


	
	switch ($resource) {
				
		case 'messages':
			$data_questions = $leoniDB -> readQuestions();
			$index = 1;

			foreach($data_questions as $question){
				$preg = $question['question'];
				$string = preg_replace('/\s+/', '', $preg);
				$string = strtolower($string);
				$base = 10;
				$messageint = intval($message, $base);
				
				
				if(strcmp($string, $message) == 0 || $messageint == $index){
					$id_answer = $question['id_answer'];
					$answer = $leoniDB->ReadOneAnswerById($id_answer);
					$speech = json_encode($answer);
					$speech = $answer['answer'];
					$speech_minus = preg_replace('/\s+/', '', $speech);
					$menu = "Menu";
					$card = "CardPTO";
					$ptoyear = "available_cur_year";
					$ptoavailable = "actual_available";
					$ptotaken = "taken_year";
					$ptoexpire = "days_to_expire";
					$pto_info = "pto_info";
					$sick = "sick_leave";
					$calculator = "calculatePTO";
					
							
					switch($speech_minus){
						case $menu:
							$speech = replyMenu($data_questions);
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						case $sick:				
							$level = $leoniDB->Level($id_emp);
							$level = $level['level'];

							if($level >=2){
								$speech = "sick leave";
								replyCardSick($speech,$email,$filesWebex,$htmlWebex,$postMessagesURL);
								die();
							}else{
								$speech = "Only line managers can request Sick Leave PTOs, please contact your manager";
								reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
								die();
							}
							break;
						case $card:
							$speech = "card";
							replyCardPTO($speech,$email,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						case $ptoyear:
							$ptoinfo = $leoniDB->PTO_empInfo(strval($id_emp));	
							$speech = "Your PTOs available on the current year are ".$ptoinfo['dayspyear'];
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						case $ptoavailable:
							$ptoinfo = $leoniDB->PTO_empInfo(strval($id_emp));
							$speech = "Your PTOs available at this moment are ".$ptoinfo['actual_days'];
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						case $ptotaken:
							$ptoinfo = $leoniDB->PTO_empInfo(strval($id_emp));
							$speech = "You have taken ".$ptoinfo['taken']." PTOs";
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						case $ptoexpire:
							$ptoinfo = $leoniDB->PTO_empInfo(strval($id_emp));
							$speech = "You have ".$ptoinfo['expired']." PTOs next to expire";
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						case $pto_info: 
							$speech = ptoinfo($id_emp);
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						case $calculator:
							$speech = "card";
							replyCardCalculator($speech,$email,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;
						default:
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
							break;		
					}
				}
				$index ++;
			}
			$speech = "I'm sorry, I can't understand that question. Send the message 'Help' for more information. ";
			reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
			die();
			break;


		case 'attachmentActions':
			$type = $getCard['inputs']['decision'];
			switch($type){
				case 'Accept':
				case 'Reject':
					//PL INFORMATION
				$comment = $getCard['inputs']['comment'];
				$decision = $getCard['inputs']['decision'];
				$id = $getCard['inputs']['id_request'];
				$days = $getCard['inputs']['num_days'];
				$emp_email = $getEmail['emails'][0];
				$employee = $leoniDB->employeeInfo($emp_email);
				$id_pl = $employee[0]['id_emp'];
				$date_approved = date('Y-m-d');
				
				//EMPLOYEE INFORMATION
				$result = $leoniDB->EmpRequest($id);
				$id_emp = $result['id_emp'];
				$pto_abs = $result['absence_type'];
				$id_emp = strval($id_emp);
				$empemail = $leoniDB->managerEmail($id_emp);
				$empemail = $empemail['email'];
				$employee_info = $leoniDB->employeeInfo($empemail);
				$emp_name = $employee_info[0]['first_name']." ".$employee_info[0]['last_name'];
				$id_request=$leoniDB->getIdRequest($id);
				$id_request=$id_request['id_request'];

				if(strcmp($decision, "Accept") == 0){
					$status_request = "Approved";
					$leoniDB->PTO_approved($id_request, $comment);
					mailAccept($empemail, $pto_abs, $emp_name);
					
				}else{
					$status_request = "Denied";
					$leoniDB->PTO_denied($id_request, $comment);
					mailDenied($empemail, $pto_abs, $emp_name, $comment);
				}

				$speech = "PTO request updated, thank you.";
				replyParent($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL, $messageid2);
				$leoniRequests->deleteRequests($deleteMessage, $messageid2);

				
				$speech = "Confirmation";
				replyCardConfirmation($speech,$empemail,$filesWebex,$htmlWebex,$postMessagesURL,$id,$status_request,$comment);
				die();
				break;

				case 'pto_calculator': //PTO CALCULATOR
					$emp_email = $getEmail['emails'][0];
					$employee = $leoniDB->employeeInfo($emp_email);
					$id_emp = $employee[0]['id_emp'];
					$start_date = $getCard['inputs']['start_date'];
					$days = $leoniDB->PTO_dates($id_emp, $start_date);
					$days = $days['actual'];	
					$speech = "You will have ".$days." PTOs.";	
					reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
				break;

				default:
				$emp_email = $getEmail['emails'][0];
				$employee = $leoniDB->employeeInfo($emp_email);

				$start_date = $getCard['inputs']['start_date'];
				$end_date = $getCard['inputs']['end_date'];
				$absence_type = $getCard['inputs']['absence_type'];
				$peoplesoft_id = $getCard['inputs']['time_id'];
				$id_emp = $employee[0]['id_emp'];
				$role = $employee[0]['user_lvl'];
				$manager = $employee[0]['booking_manager'];
				

				$num_days = $leoniDB->PTO_diffDays($start_date, $end_date);
				$num_days = intval($num_days['cant_days']);

				
				$date_sent = date('Y-m-d');
				$status_request="Submitted";

				$request = [
					"peoplesoft_id" => $peoplesoft_id,
					"id_emp" => $id_emp,
					"role" => $role,
					"absence_type" => $absence_type,
					"start_date" => $start_date,
					"end_date" => $end_date,
					"num_days" => $num_days,
					"date_sent" => $date_sent,
					"status_request" => $status_request,
					"approval_from" => $manager
				];
				
				//DATES
				if($start_date > $end_date){
					$speech = "Please review the dates of your PTO and try again";
					reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
					die();
				}


				//SWITCH ABSENCE TYPE
				switch($absence_type){
					case 'Sick Leave':
						$id_emp_sick = $getCard['inputs']['id_emp'];
						$request['id_emp'] = $id_emp_sick;
						$request['approval_from'] = $id_emp;
						$request['status_request'] = "Approved";
						$leoniDB->saveRequest($request);
						$leoniRequests->deleteRequests($deleteMessage, $messageid2);
						

						$employee_sick = $leoniDB->readEmpInfo($id_emp_sick);
						$emp_email = $employee_sick['email'];
						$pto_abs = "Sick Leave";
						$employee = $employee_sick['first_name']." ".$employee_sick['last_name'];
						mailAccept($emp_email, $pto_abs, $employee);

						$speech = 'The Sick Leave PTO from '.$employee.' with start date '.$start_date.' to end date '.$end_date.' was saved';
						reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
						break;
					
					//END SICK LEAVE

					default:
						foreach($request as $value){
							if(empty($value)){
								$speech = "PTO Information is not complete, please review it and try again";
								reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
								die();
							}
						}
						
						$id_used = $leoniDB->getRequestById($request['peoplesoft_id']);
						$peoplesoft_id = reportID($peoplesoft_id);
						if ($peoplesoft_id <= 600000){
							$speech = "This Time Report ID is incorrect, please try again";
							reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
							die();
						}else{
							if($id_used == true){
								$speech = "This Time Report ID has already been used, please try again";
								reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
								die();
							}else{
								$today = date("Y-m-d");
								if($start_date < $today){
									$speech = "You can not request a PTO with past date, please try again";
									reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
									die();
								}
								if($start_date > $end_date){
									$speech = "Please review the dates of your PTO and try again";
									reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
									die();
								}
								
								$speech = noweekends($start_date, $end_date);
								if($speech == "You can not take PTOs for weekends"){
									reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
									die();
								}
								
								$weeks = week($start_date, $end_date);				
								if($weeks['semana1'] <> $weeks['semana2']){
									$speech = "You can not use same Time Report ID in different week, please try again";
									reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
									die();
								}
								
								$available = $leoniDB->PTO_empInfo($id_emp);
								$available = $available['actual_days'];

								if($num_days > $available){
									$speech = "I'm sorry, you don't have enough PTOs available, verify your dates";
									reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
									die();
								}
								
								if($absence_type == "10th Anniversary") {
									$PTO_anny = $leoniDB->PTO_anniversary($id_emp);
									$PTO_anny = $PTO_anny['seniority'];
									$PTO_used = $leoniDB->PTO_findR($id_emp);
		
									if (empty($PTO_used) && $PTO_anny>9 && $PTO_anny<11 && $num_days===5){
										
									}elseif(sizeof($PTO_used)>0){
										$speech = "You already made use of this request. Please, upload it as PTO. Otherwise, contact the TEG for support.";
										reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
										die();
									} elseif($PTO_anny<10){
										$speech = "You do not yet meet the necessary requirements to upload this request. Please try again. Otherwise, contact the TEG for support";
										reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
										die();
									} elseif($PTO_anny>10){
										$speech = "The period to use this type of request has expired. Please, upload it as PTO. Otherwise, contact the TEG for support";
										reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
										die();
									} elseif($num_days<5 || $num_days>5){
										$speech = "Your request could not be processed. You must upload 5 days. Please, try again.";
										reply($speech,$personId,$filesWebex,$htmlWebex,$postMessagesURL);
										die();
									}
								}
		
								$leoniDB->saveRequest($request);
								$leoniRequests->deleteRequests($deleteMessage, $messageid2);



								//MENSAJE PTO ENVIADO
								replyCardSent($speech, $emp_email, $filesWebex, $htmlWebex, $postMessagesURL, $request["start_date"], $request["end_date"], $request["peoplesoft_id"]);

								
								$first_name_emp = $employee[0]['first_name'];
								$last_name_emp = $employee[0]['last_name'];
								$emp_full_name = $first_name_emp." ".$last_name_emp;
								
								
								$pl_info = $leoniDB->readEmpInfo($manager);
								$pl_mail = $pl_info['email'];
								$pl_name = $pl_info['first_name'];
								$pl_last_name = $pl_info['last_name'];
								$pl_full_name = $pl_name ." ".$pl_last_name;
								$repl = 0;


								$PTO_linemail = $leoniDB->PTO_lineMail($manager, $repl, $id_emp);
								$pto_abs = $PTO_linemail[0]['amount'];
								mailPl($pl_mail, $pl_full_name, $emp_full_name, $pto_abs);



								//PL NOTIFICACION TEXTO & EMAIL
								$pl_notification = "_______________________________________________________________________________________________________________________________________________________________________________\n".$first_name_emp." ".$last_name_emp." has made a PTO request\nFrom ".$start_date." to ".$end_date."\nTotal days: ".$num_days."\nAbsence Type: ".$absence_type."\nDate Sent: ".$date_sent;
								replyEmail($pl_notification,$pl_mail,$filesWebex,$htmlWebex,$postMessagesURL);
								$cardspeech = "speech de card";
								$actual_days = strval($request['num_days']);
								replyCardPL($cardspeech, $pl_mail, $filesWebex, $htmlWebex, $postMessagesURL,$request['peoplesoft_id'], $actual_days);
							}
						}
						break; 
					}		

				break;
			}
	}	
}


//REPLY POR EMAIL

function reply($speech,$personId,$filesWebex,$htmlWebex,$url){	
require_once 'model/leoniRequests.php';
$leoniRequests=new leoniRequests();
	
	if($personId!='myherebot@webex.bot'){
		if($filesWebex!=""){
			$arr['files'] =  array($filesWebex);
		}
	
		if($htmlWebex!=""){
			$arr['html'] =  $htmlWebex;
		}
	
	
		$arr['toPersonId'] =$personId;	
		$arr['text'] =  $speech ;
		
		$leoniRequests->postRequests($url,$arr);
	}	
}

function replyParent($speech,$personId,$filesWebex,$htmlWebex,$url, $parentid){	
require_once 'model/leoniRequests.php';
$leoniRequests=new leoniRequests();
	
	if($personId!='myherebot@webex.bot'){
		if($filesWebex!=""){
			$arr['files'] =  array($filesWebex);
		}
	
		if($htmlWebex!=""){
			$arr['html'] =  $htmlWebex;
		}
	
	
		$arr['toPersonId'] =$personId;	
		$arr['text'] =  $speech ;
		$arr['parentId'] = $parentid;
		
		$leoniRequests->postRequests($url,$arr);
	}	
}



function replyEmail($speech,$email,$filesWebex,$htmlWebex,$url){
	require_once 'model/leoniRequests.php';
	$leoniRequests=new leoniRequests();
	
	if($email!='myherebot@webex.bot'){
	if($filesWebex!=""){
	$arr['files'] = array($filesWebex);
	}
	
	if($htmlWebex!=""){
	$arr['html'] = $htmlWebex;
	}
	
	
	$arr['toPersonEmail'] =$email;
	$arr['text'] = $speech ;
	
	$leoniRequests->postRequests($url,$arr);
	}
}

function replyCardConfirmation($speech,$email,$filesWebex,$htmlWebex,$url, $id, $status, $comments){
	require_once 'model/leoniRequests.php';
	$leoniRequests=new leoniRequests();
	
	if($email!='myherebot@webex.bot'){
		if($filesWebex!=""){
		$arr['files'] = array($filesWebex);
		}
		
		if($htmlWebex!=""){
		$arr['html'] = $htmlWebex;
		}
		
		
		$arr['toPersonEmail'] =$email;
		$arr['text'] = $speech ;
		
		
		$card = array (
			0 => 
			array (
				'contentType' => 'application/vnd.microsoft.card.adaptive',
				'content' => 
				array (
					'type' => 'AdaptiveCard',
					'$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
					'version' => '1.2',
					'body' => 
					array (
						0 => 
						array (
							'type' => 'ColumnSet',
							'columns' => 
							array (
								0 => 
								array (
									'type' => 'Column',
									'width' => 'stretch',
									'items' => 
									array (
										0 => 
										array (
											'type' => 'TextBlock',
											'text' => 'Your PTO request has been updated.',
											'wrap' => true,
											'size' => 'Large',
											'weight' => 'Bolder',
											'color' => 'Accent',
										),
									),
								),
								1 => 
								array (
									'type' => 'Column',
									'width' => 'auto',
									'items' => 
									array (
										0 => 
										array (
											'type' => 'Image',
											'separator' => true,
											'id' => 'icon',
											'url' => 'http://testchatbotprueba1-951056636.us-east-1.elb.amazonaws.com/chatbotproject/leoni/img/logo.png',
											'size' => 'Medium',
										),
									),
								),
							),
						),
						1 => 
						array (
							'type' => 'ColumnSet',
							'columns' => 
							array (
								0 => 
								array (
									'type' => 'Column',
									'width' => 'stretch',
									'items' => 
									array (
										0 => 
										array (
											'type' => 'RichTextBlock',
											'inlines' => 
											array (
												0 => 
												array (
													'type' => 'TextRun',
													'text' => 'Your PTO with Time Report ID '.$id. " was updated by your manager.\n".'Response: '.$status."\nComments: ".$comments,
												),
											),
											'id' => 'pto_info',
										),
									),
								),
							),
						),
					),
				),
			),
		);



		$arr['attachments'] = $card;
		$leoniRequests->postRequests($url,$arr);
		}
}


function replyCardSick($speech,$email,$filesWebex,$htmlWebex,$url){
	require_once 'model/leoniRequests.php';
	$leoniRequests=new leoniRequests();
	
	if($email!='myherebot@webex.bot'){
		if($filesWebex!=""){
		$arr['files'] = array($filesWebex);
		}
		
		if($htmlWebex!=""){
		$arr['html'] = $htmlWebex;
		}
		
		
		$arr['toPersonEmail'] =$email;
		$arr['text'] = $speech ;
		
		$card = array(
			"contentType" => "application/vnd.microsoft.card.adaptive",
			"content" => array(
				"type" => "AdaptiveCard",
				"$schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
				"version" => "1.2",
				"body" => array(
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "auto",
								"items" => array(
									array(
										"type" => "Image",
										"url" => "http://testchatbotprueba1-951056636.us-east-1.elb.amazonaws.com/chatbotproject/leoni/img/logo.png",
										"size" => "Medium",
										"id" => "icon"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "Register a Sick Leave",
										"wrap" => true,
										"size" => "Large",
										"weight" => "Bolder",
										"color" => "Light"
									),
									array(
										"type" => "TextBlock",
										"text" => "Please fill in the fields to register your Sick Leave.",
										"wrap" => true
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "Start Date",
										"wrap" => true,
										"horizontalAlignment" => "Center",
										"separator" => true,
										"fontType" => "Default",
										"size" => "Medium",
										"isSubtle" => true,
										"spacing" => "None"
									)
								),
								"horizontalAlignment" => "Center"
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.Date",
										"id" => "start_date"
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "End Date",
										"wrap" => true,
										"horizontalAlignment" => "Center",
										"size" => "Medium"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.Date",
										"id" => "end_date"
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "ID Employee",
										"wrap" => true,
										"size" => "Medium",
										"horizontalAlignment" => "Center"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.Text",
										"placeholder" => "Placeholder text",
										"id" => "id_emp"
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.Text",
										"wrap" => true,
										"id" => "absence_type",
										"isVisible" => false,
										"value" => "Sick Leave"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "ActionSet",
										"actions" => array(
											array(
												"type" => "Action.Submit",
												"title" => "Send",
												"style" => "positive",
												"id" => "send_action"
											)
										)
									)
								),
								"horizontalAlignment" => "Right"
							)
						)
					)
				)
			)
		);
		$arr['attachments'] = $card;
		$leoniRequests->postRequests($url,$arr);
		}
}


function replyCardSent($speech,$email,$filesWebex,$htmlWebex,$url, $start_date, $end_date, $peoplesoft_id){
	require_once 'model/leoniRequests.php';
	$leoniRequests=new leoniRequests();
	
	if($email!='myherebot@webex.bot'){
		if($filesWebex!=""){
		$arr['files'] = array($filesWebex);
		}
		
		if($htmlWebex!=""){
		$arr['html'] = $htmlWebex;
		}
		
		
		$arr['toPersonEmail'] =$email;
		$arr['text'] = $speech ;
		
		
		$card = array (
  0 => 
  array (
    'contentType' => 'application/vnd.microsoft.card.adaptive',
    'content' => 
    array (
      'type' => 'AdaptiveCard',
      '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
      'version' => '1.2',
      'body' => 
      array (
        0 => 
        array (
          'type' => 'ColumnSet',
          'columns' => 
          array (
            0 => 
            array (
              'type' => 'Column',
              'width' => 'auto',
              'items' => 
              array (
                0 => 
                array (
                  'type' => 'Image',
                  'size' => 'Medium',
                  'url' => 'http://testchatbotprueba1-951056636.us-east-1.elb.amazonaws.com/chatbotproject/leoni/img/logo.png',
                  'id' => 'icon',
                ),
              ),
            ),
            1 => 
            array (
              'type' => 'Column',
              'width' => 'stretch',
              'items' => 
              array (
                0 => 
                array (
                  'type' => 'TextBlock',
                  'text' => 'PTO Request Sent',
                  'wrap' => true,
                  'size' => 'Large',
                  'weight' => 'Bolder',
                  'color' => 'Light',
                  'id' => 'title',
                ),
                1 => 
                array (
                  'type' => 'TextBlock',
                  'text' => 'PTO Request Information:',
                  'wrap' => true,
                  'id' => 'subtitle',
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'type' => 'ColumnSet',
          'columns' => 
          array (
            0 => 
            array (
              'type' => 'Column',
              'width' => 'stretch',
              'items' => 
              array (
                0 => 
                array (
                  'type' => 'RichTextBlock',
                  'inlines' => 
                  array (
                    0 => 
                    array (
                      'type' => 'TextRun',
                      'text' => 'Your PTO with start date '.$start_date.' to end date '.$end_date.' and Time Report ID '.$peoplesoft_id.' was sent, your request is under review.',
                    ),
                  ),
                  'id' => 'pto_info',
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);
		
		$arr['attachments'] = $card;
		$leoniRequests->postRequests($url,$arr);
		}
}


function replyCardCalculator($speech,$email,$filesWebex,$htmlWebex,$url){
	require_once 'model/leoniRequests.php';
	$leoniRequests=new leoniRequests();
	
	if($email!='myherebot@webex.bot'){
		if($filesWebex!=""){
		$arr['files'] = array($filesWebex);
		}
		
		if($htmlWebex!=""){
		$arr['html'] = $htmlWebex;
		}
		
		
		$arr['toPersonEmail'] =$email;
		$arr['text'] = $speech ;

		$card = [
			"contentType" => "application/vnd.microsoft.card.adaptive", 
			"content" => [
				  "type" => "AdaptiveCard", 
				  "$schema" => "http://adaptivecards.io/schemas/adaptive-card.json", 
				  "version" => "1.2", 
				  "body" => [
					 [
						"type" => "ColumnSet", 
						"columns" => [
						   [
							  "type" => "Column", 
							  "width" => "auto", 
							  "items" => [
								 [
									"type" => "Image", 
									"url" => "http://testchatbotprueba1-951056636.us-east-1.elb.amazonaws.com/chatbotproject/leoni/img/logo.png", 
									"size" => "Medium", 
									"id" => "icon" 
								 ] 
							  ] 
						   ], 
						   [
									   "type" => "Column", 
									   "width" => "stretch", 
									   "items" => [
										  [
											 "type" => "TextBlock", 
											 "text" => "PTO Calculator", 
											 "wrap" => true, 
											 "size" => "Large", 
											 "weight" => "Bolder", 
											 "color" => "Light" 
										  ], 
										  [
												"type" => "TextBlock", 
												"text" => "Calculate how many PTOs you will have at a certain date.", 
												"wrap" => true 
											 ] 
									   ] 
									] 
						] 
					 ], 
					 [
												   "type" => "ColumnSet", 
												   "columns" => [
													  [
														 "type" => "Column", 
														 "width" => "stretch", 
														 "items" => [
															[
															   "type" => "TextBlock", 
															   "text" => "Date", 
															   "wrap" => true, 
															   "horizontalAlignment" => "Center", 
															   "separator" => true, 
															   "fontType" => "Default", 
															   "size" => "Medium", 
															   "isSubtle" => true, 
															   "spacing" => "None" 
															] 
														 ], 
														 "horizontalAlignment" => "Center" 
													  ], 
													  [
																  "type" => "Column", 
																  "width" => "stretch", 
																  "items" => [
																	 [
																		"type" => "Input.Date", 
																		"id" => "start_date" 
																	 ] 
																  ] 
															   ] 
												   ] 
												], 
					 [
																		   "type" => "ColumnSet", 
																		   "columns" => [
																			  [
																				 "type" => "Column", 
																				 "width" => "stretch", 
																				 "items" => [
																					[
																					   "type" => "Input.Text", 
																					   "placeholder" => "Placeholder text", 
																					   "id" => "decision", 
																					   "value" => "pto_calculator", 
																					   "isVisible" => false 
																					] 
																				 ] 
																			  ], 
																			  [
																						  "type" => "Column", 
																						  "width" => "stretch" 
																					   ] 
																		   ] 
																		], 
					 [
																							 "type" => "ColumnSet", 
																							 "columns" => [
																								[
																								   "type" => "Column", 
																								   "width" => "stretch" 
																								], 
																								[
																									  "type" => "Column", 
																									  "width" => "stretch", 
																									  "items" => [
																										 [
																											"type" => "ActionSet", 
																											"actions" => [
																											   [
																												  "type" => "Action.Submit", 
																												  "title" => "Send", 
																												  "style" => "positive", 
																												  "id" => "send_action" 
																											   ] 
																											] 
																										 ] 
																									  ], 
																									  "horizontalAlignment" => "Right" 
																								   ] 
																							 ] 
																						  ] 
				  ] 
			   ] 
		 ];


		$arr['attachments'] = $card;
		$leoniRequests->postRequests($url,$arr);
		}
		
}





function replyCardPL($speech,$email,$filesWebex,$htmlWebex,$url,$idpto, $actual_days){
	require_once 'model/leoniRequests.php';
	$leoniRequests=new leoniRequests();

	if($email!='myherebot@webex.bot'){
		if($filesWebex!=""){
		$arr['files'] = array($filesWebex);
		}
		
		if($htmlWebex!=""){
		$arr['html'] = $htmlWebex;
		}
		
		
		$arr['toPersonEmail'] =$email;
		$arr['text'] = $speech ;
		
		$card = $arrayVar = [
"contentType" => "application/vnd.microsoft.card.adaptive",
"content" => [
"type" => "AdaptiveCard",
"$schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
"version" => "1.2",
"body" => [
[
"type" => "ColumnSet",
"columns" => [
[
"type" => "Column",
"width" => "auto",
"items" => [
[
"type" => "Image",
"url" => 'http://testchatbotprueba1-951056636.us-east-1.elb.amazonaws.com/chatbotproject/leoni/img/logo.png',
"size" => "Medium",
"id" => "icon",
],
],
],
[
"type" => "Column",
"width" => "stretch",
"items" => [
[
"type" => "TextBlock",
"text" => "PTO Request",
"wrap" => true,
"size" => "Large",
"weight" => "Bolder",
"color" => "Light",
"id" => "pto_pl",
],
[
"type" => "TextBlock",
"text" =>
"Please accept or reject this PTO request.",
"wrap" => true,
],
],
],
],
],
[
"type" => "ColumnSet",
"columns" => [
[
"type" => "Column",
"width" => "stretch",
"items" => [
[
"type" => "ColumnSet",
"columns" => [
[
"type" => "Column",
"width" => "stretch",
"items" => [
[
"type" => "Input.Text",
"placeholder" => "Comments",
"isMultiline" => true,
"id" => "comment",
],
],
],
],
],
[
"type" => "Input.ChoiceSet",
"choices" => [
["title" => "Accept", "value" => "Accept"],
["title" => "Reject", "value" => "Reject"],
],
"placeholder" =>
"-- Please select an option --",
"id" => "decision",
],
],
],
],
],
[
"type" => "ColumnSet",
"columns" => [
[
"type" => "Column",
"width" => "stretch",
"items" => [
[
"type" => "Input.Text",
"placeholder" => "Placeholder text",
"value" => $idpto,
"id" => "id_request",
"isVisible" => false,
],
],
],
[
"type" => "Column",
"width" => "stretch",
"items" => [
[
"type" => "ActionSet",
"actions" => [
[
"type" => "Action.Submit",
"title" => "Send",
"id" => "pto",
"style" => "positive",
],
],
"horizontalAlignment" => "Center",
],
],
],
],
],
[
"type" => "Input.Text",
"placeholder" => "Placeholder text",

"id" => "num_days",
"value"=> $actual_days,
"isVisible" => false,
],
],
],
];
		

		
		$arr['attachments'] = $card;
		$leoniRequests->postRequests($url,$arr);
		}
}

function replyCardPTO($speech,$email,$filesWebex,$htmlWebex,$url){
	require_once 'model/leoniRequests.php';
	$leoniRequests=new leoniRequests();

	if($email!='myherebot@webex.bot'){
		if($filesWebex!=""){
		$arr['files'] = array($filesWebex);
		}
		
		if($htmlWebex!=""){
		$arr['html'] = $htmlWebex;
		}
		
		
		$arr['toPersonEmail'] =$email;
		$arr['text'] = $speech ;

		
		$card = array(
			"contentType" => "application/vnd.microsoft.card.adaptive",
			"content" => array(
				"type" => "AdaptiveCard",
				"$schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
				"version" => "1.2",
				"body" => array(
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "auto",
								"items" => array(
									array(
										"type" => "Image",
										"url" => "http://testchatbotprueba1-951056636.us-east-1.elb.amazonaws.com/chatbotproject/leoni/img/logo.png",
										"size" => "Medium",
										"id" => "icon"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "Register a new PTO",
										"wrap" => true,
										"size" => "Large",
										"weight" => "Bolder",
										"color" => "Light"
									),
									array(
										"type" => "TextBlock",
										"text" => "Please fill in the fields to register your PTO request.",
										"wrap" => true
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "Start Date",
										"wrap" => true,
										"horizontalAlignment" => "Center",
										"separator" => true,
										"fontType" => "Default",
										"size" => "Medium",
										"isSubtle" => true,
										"spacing" => "None"
									)
								),
								"horizontalAlignment" => "Center"
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.Date",
										"id" => "start_date"
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "End Date",
										"wrap" => true,
										"horizontalAlignment" => "Center",
										"size" => "Medium"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.Date",
										"id" => "end_date"
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "Absence Type",
										"wrap" => true,
										"size" => "Medium",
										"horizontalAlignment" => "Center"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.ChoiceSet",
										"choices" => array(
											array(
												"title" => "Maternity",
												"value" => "Maternity"
											),
											array(
												"title" => "PTO",
												"value" => "PTO"
											),
											array(
												"title" => "10th Anniversary",
												"value" => "10th Anniversary"
											),
											array(
												"title" => "Paternity",
												"value" => "Paternity"
											),
										),
										"placeholder" => "Type",
										"style" => "expanded",
										"isVisible" => false,
										"wrap" => true,
										"separator" => true,
										"id" => "absence_type"
									)
								)
							)
						)
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "TextBlock",
										"text" => "Time Report ID",
										"wrap" => true,
										"horizontalAlignment" => "Center",
										"size" => "Medium"
									)
								)
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "Input.Number",
										"placeholder" => "ID",
										"id" => "time_id"
									),
									array(
										"type" => "TextBlock",
										"text" => "Please send the last digits of your Time report ID without the 0\'s.",
										"wrap" => true,
										"horizontalAlignment" => "Center",
										"fontType" => "Default",
										"size" => "Small",
										"weight" => "Lighter",
										"isSubtle" => true
									)
								)
							)
						)
					),
					array(
						"type" => "TextBlock",
						"text" => "Your request will be sent to your manager and you will be notified when accepted",
						"wrap" => true,
						"horizontalAlignment" => "Center",
						"fontType" => "Default",
						"weight" => "Lighter",
						"color" => "Light"
					),
					array(
						"type" => "ColumnSet",
						"columns" => array(
							array(
								"type" => "Column",
								"width" => "stretch"
							),
							array(
								"type" => "Column",
								"width" => "stretch",
								"items" => array(
									array(
										"type" => "ActionSet",
										"actions" => array(
											array(
												"type" => "Action.Submit",
												"title" => "Send",
												"style" => "positive",
												"id" => "send_action"
											)
										)
									)
								),
								"horizontalAlignment" => "Right"
							)
						)
					)
				)
			)
		);

		
		$arr['attachments'] = $card;
		$leoniRequests->postRequests($url,$arr);
		}
}


function replyMenu($questions){
	
	$speech = "Hi, these are the questions I can answer for you, be sure to send them as they are described in this menu or by list number, thanks :)\n\n";
	$index = 1;
	
	foreach($questions as $question){
		$speech = $speech.$index." - ".$question['question']."\n";
		$index ++;
	}

	return $speech;
}

function week($start_date, $end_date){
	$dia1   = substr($start_date,8,2);
	$mes1 = substr($start_date,5,2);
	$anio1 = substr($start_date,0,4); 
	
	$dia2   = substr($end_date,8,2);
	$mes2 = substr($end_date,5,2);
	$anio2 = substr($end_date,0,4); 
	
	$semana1 = date('W',  mktime(0,0,0,$mes1,$dia1,$anio1));  
	$semana2 = date('W',  mktime(0,0,0,$mes2,$dia2,$anio2));
	
	$semanas = ["semana1" => $semana1, "semana2" => $semana2];
	
	return $semanas;
}


function reportID($time_report_id){
	
	for($i=0; $i<=strlen($time_report_id); $i++){
		$digit = substr($time_report_id, 0, 1);
		if($digit == '0'){
			$time_report_id = substr($time_report_id, 1);
		}else{
			return $time_report_id;
		}
	}
}



function ptoinfo($id_emp){

	$leoniDB=new leoniDB();
	$ptoinfo = array();
	$ptoinfo = $leoniDB->PTO_empInfo($id_emp);
	$year = $ptoinfo['dayspyear'];
	$current = $ptoinfo['actual_days'];
	$taken= $ptoinfo['taken'];
	$expire = $ptoinfo['expired'];
	
	$answer = "This is your complete PTOs information:\n"
			  ."Your PTOs available per current year are ".$year."\n"
			  ."Your PTOs available at this moment are ".$current."\n"
			  ."You have taken ".$taken." PTOs this year"."\n"
			  ."You have ".$expire." PTOs next to expire";
			  

	return $answer;
}

function noweekends($start, $end){
	
	$start_day = date('l', strtotime($start));
	$end_day = date('l', strtotime( $end));
	if($start_day == "Saturday" || $start_day == "Sunday" || $end_day == "Saturday" || $end_day == "Sunday"){
		$speech = "You can not take PTOs for weekends";
		return $speech;
	}
	return ".";
}


function mailPl ($email, $pl, $employee, $pto_abs){
	ini_set('SMTP','smtp.in.here.com');
	ini_set('smtp_port',25);
	ini_set('sendmail_from','leon.notifications@here.com');

	$dt = new datetime("now", new datetimezone('America/Mexico_City'));
    $datenow = gmdate("F d, Y", (time() + $dt->getOffset()));
	
    // Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: timereport@here.com' . "\r\n";
	$headers .= 'BCc:'.$email;

			$to = $email;
			$mess = '<p style="font-family:FiraGo;color:gray">Time Report | '.$datenow.'</p>
			<h1 style="font-family:FiraGo Light;color: #00afaa; display:inline; text-align: right;"><i>Absence Notification</i></h1>
			<div style="background-color: #cdced0; width: 100%; height: 15px; color:#cdced0">.</div><br>
			<p style="font-family:FiraGo Light;"><b>Dear '.$pl.',</b></p>
			<p style="font-family:FiraGo Light;"><b>'.$employee.' has sent a request and you have '.$pto_abs.' requests pending approval.
			</b></p>
			<p style="font-family:FiraGo Light;"><b>Please click <a href="https://leontools.teg.aws.in.here.com/">here</a> to approve request.</b></p>';

			$mess.='<br>
                        <hr>
                        <br><br>
                        <p style="font-family_FiraGo Light;font-size:1em"><i>Do not reply to this email. The sender is not enabled to receive messages.</i></p>';


	// headers of the email
			$subject = utf8_decode('Notification Time Report - Absence Notification');
			mail($to, $subject, $mess, $headers);
			//unset($headers);
		
	}

	function mailAccept ($email, $PTO_abs, $employee){
		ini_set('SMTP','smtp.in.here.com');
		ini_set('smtp_port',25);
		ini_set('sendmail_from','leon.notifications@here.com');
	
		$dt = new datetime("now", new datetimezone('America/Mexico_City'));
		$datenow = gmdate("F d, Y", (time() + $dt->getOffset()));
		
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	
		// More headers
		$headers .= 'From: timereport@here.com' . "\r\n";
		$headers .= 'BCc:'.$email;
	
				$to = $email;
				$mess = '<p style="font-family:FiraGo;color:gray">Time Report | '.$datenow.'</p>
				<h1 style="font-family:FiraGo Light;color: #00afaa; display:inline; text-align: right;"><i>Absence Notification</i></h1>
				<div style="background-color: #cdced0; width: 100%; height: 15px; color:#cdced0">.</div><br>
				<p style="font-family:FiraGo Light;"><b>Dear '.$employee.',</b></p>
				<p style="font-family:FiraGo Light;"><b>Your line manager has approved your '.$PTO_abs.' request. Please click <a href="https://leontools.teg.aws.in.here.com/">here</a> to verify it.
				</b></p>';
	
				$mess.='<br>
				<hr>
				<br><br>
				<p style="font-family_FiraGo Light;font-size:1em"><i>Do not reply to this email. The sender is not enabled to receive messages.</i></p>';
	
	
		// headers of the email
				$subject = utf8_decode('Notification Time Report - Absence Notification');
				mail($to, $subject, $mess, $headers);
				//unset($headers);
			
		}

	function mailDenied ($email, $PTO_abs, $employee, $comments) {
		ini_set('SMTP','smtp.in.here.com');
		ini_set('smtp_port',25);
		ini_set('sendmail_from','leon.notifications@here.com');

		$dt = new datetime("now", new datetimezone('America/Mexico_City'));
		$datenow = gmdate("F d, Y", (time() + $dt->getOffset()));

		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		$headers .= 'From: timereport@here.com' . "\r\n";
		$headers .= 'BCc:'.$email;

		$to = $email;
		$mess = '<p style="font-family:FiraGo;color:gray">Time Report | '.$datenow.'</p>
                        <h1 style="font-family:FiraGo Light;color: #00afaa; display:inline; text-align: right;"><i>Absence Notification</i></h1>
                        <div style="background-color: #cdced0; width: 100%; height: 15px; color:#cdced0">.</div><br>
                        <p style="font-family:FiraGo Light;"><b>Dear '.$employee.',</b></p>
                        <p style="font-family:FiraGo Light;"><b>
                        Your Line Manager has denied your '.$PTO_abs.' request for the following reason '.$comments.'. Please click <a href="https://leontools.teg.aws.in.here.com/">here</a> to verify it.
                        </b></p>';
		$mess.='<br>
				<hr>
				<br><br>
				<p style="font-family_FiraGo Light;font-size:1em"><i>Do not reply to this email. The sender is not enabled to receive messages.</i></p>';
						
		$subject = utf8_decode('Notification Time Report - Absence Notification');
		mail($to, $subject, $mess, $headers);
}