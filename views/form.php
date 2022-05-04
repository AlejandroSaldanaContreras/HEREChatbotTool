<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/css/headers.css" rel="stylesheet">
    <title>Question</title>
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

     <div class="row" >
      <div class="col col-lg-12">
        <div class="card bg-dark text-white" style="margin-top:-20px" >
          <img src="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/images/header.jpg" class="card-img" alt="...">
        </div>
      </div>
    </div>

    <div class="row" style="margin-top:10px; margin-left:0px;">
      <div class="col col-lg-12 d-flex justify-content-center">
          <div class="card shadow-sm p-3 mb-5 bg-white rounded">
          <div class="card-title d-flex justify-content-center">
              <h1 class="card-title">Here Chatbot Tool</h5>
          </div>
          <div class="card-content d-flex justify-content-center">
           <p class="card-text"><?php echo is_numeric($id_question)? "In this section you can modify the questions and answers in the Chatbot tool in Webex.":"In this section you can create the questions and answers for the Chatbot tool in Webex."?></p>
          </div>
          </div>
      </div>
    </div>


    <!-- FORM -->
    <form action="<?php echo $script; ?>" method="POST">
      <div class="row" style="margin-top:10px; margin-right:15px; margin-left:15px;">
      <div class="col col-lg-6">
            <div class="mb-3">
              <label class="form-label">ID Employee</label>
              <input name="id_emp" value="<?php echo $data_question["owner"]; ?>" type="text" class="form-control" >
            </div>
          </div>
        </div>
        <div class="row" style="margin-top:10px; margin-right:15px; margin-left:15px;">
          <div class="col col-lg-6">
            <div class="mb-3">
              <label class="form-label">Question</label>
              <textarea style="height:55px;" placeholder="" class="form-control" name="question" ><?php echo $data_question["question"];?> </textarea>
            </div>
          </div>

        </div>
        <div class="row" style="margin-top:10px; margin-right:15px; margin-left:15px;">
          <div class="col col-lg-6">
              <div class="mb-3">
                <label class="form-label">Answer</label>
                <textarea style="height:55px;" class="form-control" name="answer" type="text"><?php echo (!empty($data_answer["answer"]))?$data_answer["answer"]:"" ?> </textarea>
                <small class="form-text text-muted">
                 Write a new answer for your question 
                  </small>
              </div>
            </div> 
          <div class="col col-lg-6">
            <div class="mb-3">
            <label style= "visibility: hidden" class="form-label">Hi</label>     
            <select name="answer_selected" class="form-control form-control-xl" aria-label="Default select example" style="height:55px;">
             <option selected value=""> Please select an option. </option>
              <?php foreach($answers as $ans): ?>
                <option value="<?php echo $ans["answer"]?>"> <?php echo $ans["answer"]?> </option>
              <?php endforeach;?>
                 
            </select>
            <small class="form-text text-muted">
                Or select one of the existing answers
            </small>
          </div>

        </div>
      </div>
      
      <div class="row" style="margin-top:10px; margin-right:15px; margin-left:15px;">
        <div class="col col-lg-4">
          <button type="submit" class="btn btn-primary" style="color: #333b47; font-weight: bold;  background: linear-gradient(to right, #f8b0d6, #94b2ce); border-radius: 12px;">Save</button>
          <?php if(!empty($message)): ?>
          <div class="alert alert-danger" role="alert"> <?php echo $message ?></div>
          <?php endif ?>
        </div>
        <div class="col col-lg-8">
        </div>
      </div>
      <?php if(is_numeric($id_answer)){  ?>
            <input type="hidden" name="id_answer" value="<?php echo $data_answer["id_answer"]; ?>">
            <input type="hidden" name="date_created_answer" value="<?php echo $data_answer["date_created"]; ?>">
      <?php } ?>
      <?php if(is_numeric($id_question)){  ?>
            <input type="hidden" name="id_question" value="<?php echo $data_question["id_question"]; ?>">
            <input type="hidden" name="date_created_question" value="<?php echo $data_question["date_created"]; ?>">
      <?php } ?>
      

    </form>
    
    <div class="row" style="margin-top:0px; margin-right:15px; margin-left:15px; margin-bottom:70px;">
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
  <footer class="footer-distributed" style=" position: fixed; width:100%; right: 0; bottom: 0;  left: 0; top:95 %; padding: 1rem; background-color: #101521; text-align: center;">
    <div class="footer-center">
				<center>				
					   <p class="footer-company-name">
      Here © 2021 |
      <a href="https://legal.here.com/en-gb/privacy/cookies" target="_blank">Cookie Policy</a>
      <a> | </a>
      <a href="https://in.here.com/sites/company/policies/GlobalPolicies/IT,%20Privacy,%20Security/HERE%20Employment%20Privacy%20Policy.pdf" target="_blank">Privacy Policy</a> 
      <a> | </a>
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/chatbotproject/views/supplement.php" style="cursor: pointer;" onclick="privacy_sup();">Privacy Supplement</a>
      <a> | </a>
      <a href="#myModal" data-toggle="modal" style="cursor: pointer;">Terms of Use</a>
      </p>
				</center><a>
			</a>
      </div>
  </footer>
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" ></script>
  <script rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.0/font/bootstrap-icons.css" integrity="sha384-7ynz3n3tAGNUYFZD3cWe5PDcE36xj85vyFkawcF6tIwxvIecqKvfwLiaFdizhPpN" crossorigin="anonymous"></script>
  </body>
</html>
