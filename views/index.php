<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/css/headers.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>HERE Chatbot Tool</title>
  </head>


  <body>

    <!-- MENU -->
    <div class="row">
    <div class="col col-lg-12">
      <header class="p-3 mb-3 border-bottom bg-dark text-white">
        <div class="container">
          <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a id="logo" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/question.php" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
              <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/images/HERE_Logo_2016_NEG_sRGB.png"style="width:40px"/>
            </a>
            </div>
            </div>
      </header>
    </div>
    </div>

  <!-- IMAGEN SUPERIOR -->
  <div class="row" >
    <div class="col col-lg-12">
      <div class="card bg-dark text-white" style="margin-top:-20px">
        <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/images/cover.png" class="card-img" alt="...">
      </div>
    </div>
  </div>

  <!-- RENGLÓN INFO -->
  <div class="row" style="margin-top:10px; margin-right:15px; margin-left:15px;">
    <div class="col col-lg-5">
      <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/images/display.jpg"  class="card-img" alt="...">
    </div>
     <div class="col col-lg-7">
      <h3>About Our Chatbot</h3>
      <p style="font-size:large; text-align: justify; text-justify: inter-word;">This tool will help you with some administrive tasks like manage PTO requests and give answer to frequently asked questions that employees have.
       For PTO each employee can send their request, it will be notified to their manager who will accept or reject all from the same Webex chat
       In this page you can add new questions and answers or modify the already existing for the Here Chatbot
       The table below shows the quetions with their answers that you can currently use.<br>
       Search the question that you want, modify it, delete it or add a new one </p>
    </div>
  </div>

  <div class="row" style="margin-top:75px; margin-right:15px; margin-left:15px; margin-bottom:10px;">
    <div class="col col-lg-8">
    <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/question.php?action=form" style="color: #333b47; font-weight: bold;  background: linear-gradient(to right, #ad77fd, #5ddad7); border-radius: 12px;" ; class="btn" type="button"> Add Question </a>
    </div>
  </div>

 <div class="row" style="margin-top:10px; margin-right:15px; margin-left:15px; margin-bottom:75px;">
    <div class="col col-lg-12">
      <table id="myTable" class="table table-hover display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No.</th>
                <th>Question</th>
                <th>Answer</th>
                <th>Date Created</th>
                <th>Date Modified</th>
                <th>Owner</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>

          <?php
            $i = 1;
            foreach ($data as $resultado => $row) {

          ?>
              <tr>
                <th scope="row"><?php echo $i; ?></th>
                <td><?php echo utf8_encode($row["question"]); ?></td>  
                <?php 
                  for($j=0; $j<count($data_answer); $j++){
                    if($row['id_answer'] == $data_answer[$j]['id_answer']){
                ?>
                    <td id="appadd"> <?php echo utf8_encode($data_answer[$j]['answer']); ?> </td>
                <?php
                    }
                  }  
                ?>
                <td id="appadd"><?php echo $row["date_created"]; ?></td>
                <td id="appadd"><?php echo $row["date_modified"]; ?></td>
                <?php 
                  for($j=0; $j<count($data_employee); $j++){
                    if($row['owner'] == $data_employee[$j]['id_emp']){
                ?>
                    <td id="appadd"> <?php echo $data_employee[$j]['username']; ?> </td>
                <?php
                    }
                  }  
                ?>
                <td id="appadd" class="edit_button"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/question.php?action=form&id_question=<?php echo $row["id_question"] ?>&id_answer=<?php echo $row["id_answer"]?>"><span class="material-icons" style = "color:#6D6A69;">edit</span></a></td>
                <td id="appadd" class="delete_button"><a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/question.php?action=delete&id_question=<?php echo $row["id_question"] ?>"><span class="material-icons" style = "color:#6D6A69;">delete</span></a></td>
              </tr>
         
          <?php
            $i++;  
                        }
          ?>   
        </tbody>
    </table>
     </div>
  </div>
  
  <!-- MODAL TERMS OF USE -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Terms of use</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="d-flex justify-content-around">
         <div class="modal-body">
            The use of this portal is exclusive to internal users of Mexico. The information provided by the user or the portal will be used strictly for the purposes established in the tools, in the stipulated confidentiality policies and local laws
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>



  <!-- FOOTER -->
  <footer class="footer-distributed" style="position: float; width:100%; right: 0; bottom: 0; left: 0; padding: 1rem; background-color: #101521; text-align: center; z-index: 10;">
    <div class="footer-center">
				<center>				
				  <p class="footer-company-name">
            Here © 2021 |
            <a href="https://legal.here.com/en-gb/privacy/cookies" target="_blank">Cookie Policy</a>
            <a> | </a>
            <a href="https://in.here.com/sites/company/policies/GlobalPolicies/IT,%20Privacy,%20Security/HERE%20Employment%20Privacy%20Policy.pdf" target="_blank">Privacy Policy</a> 
            <a> | </a>
            <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/views/supplement.php" style="cursor: pointer;" onclick="privacy_sup();">Privacy Supplement</a>
            <a > | </a>
            <a href="#myModal" data-toggle="modal" style="cursor: pointer;">Terms of Use</a>
          </p>
				</center>
        <a>
			  </a>
      </div>
  </footer>
  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" ></script>
    <script rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.0/font/bootstrap-icons.css" integrity="sha384-7ynz3n3tAGNUYFZD3cWe5PDcE36xj85vyFkawcF6tIwxvIecqKvfwLiaFdizhPpN" crossorigin="anonymous"></script>
    <script>
    $(document).ready( function () {
    $('#myTable').DataTable({
        "scrollX": true
    });
    } );
    </script>

  </body>
</html>
