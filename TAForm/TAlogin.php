<?php

    /* Name:Erica Huang
     * Date: 2-28-2015
     * File: TAlogin.php
     * Purpose: Provide login page for admins
     */

   // Start session
   session_save_path('tmp'); session_start();

   $_SESSION['TAlog'] = "out";

   //User hit submit on login form
   if(isset($_POST['Name'])) {

      include 'config.php';
      //Establish db connection
      $conn = new mysqli($servername, $username, $password, $username);

      //Prepare statement to prevent mysql injection
      $stmt = $conn->prepare("SELECT TAname, TApassword FROM TAadmins WHERE TAname=? and TApassword=?");
      $stmt->bind_param('ss', trim($_POST['Name']), trim($_POST['pwd']));
      $stmt->execute();
      $stmt->bind_result($result1, $result2);
      $stmt->fetch();

      //Validate username and password
      if($result1 === trim($_POST['Name']) && $result2=== trim($_POST['pwd'])) {
          $_SESSION['TAlog'] = "in";
          $conn->close();
          header('location:adminTAForm.php');
      }
      else {
        $conn->close();
        header('location: TAlogin.php?error=Invalid id and password');
      }
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>TA admin Form</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/webform.css">
</head>
<body >
<br><br>
<div id="form1" class="main" <?php echo $login; ?>>
   <div class="header">
    <h1>WWU CS TA Admin Login Page</h1>
  </div>
  <form class="form-horizontal" action="TAlogin.php" method="POST" role="form">
    <h2> Log In Here:</h2><br>
    <input type="hidden" name="formtype" id="formtype" value='form1'>
    <div class="form-group">
      <label class="control-label col-sm-2" for="Name">Id:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="Name" id="Name" placeholder="Enter Id" required>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="pwd">Password:</label>
      <div class="col-sm-10">          
        <input type="password" class="form-control" name="pwd" id="pwd" placeholder="Enter password" required>
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default" >Submit</button>
        <p id="errorBox"><?php echo $_GET['error'];?></p>
      </div>
    </div>
      <a href="http://yorktown.cbe.wwu.edu/ISC/">
          <div id="home"> 
               <p>HOME</p>
               <img src="images/home168.png" alt="homepage">
          </div>
      </a>
      <a href="http://sw.cs.wwu.edu/~huange2/cs300/TAForm/TAForm.php">
          <div id="home"> 
               <p>RESET</p>
               <img src="images/recycle27.png" alt="resetform">
          </div>
      </a><br><br><br><br>
  </form>

</div>
</body>
</html>
