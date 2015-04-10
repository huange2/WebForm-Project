<?php

    /* Name:Erica Huang
     * Date: 2-28-2015
     * File: adminEFForm.php
     * Purpose: Provide a admin page for internship evaluation form
                Users can edit form data, admin data and print out submitted forms.
     */

    //Start Session
    session_save_path('tmp'); session_start();

    //Restrict access to admin page via session
    if(!isset($_SESSION['EFlog']) || ($_SESSION['EFlog'] != 'in') ) {
         echo "Restricted area you need to log in first! <a href='EFlogin.php'> Back to login page </a>";
         exit();
    }

    //Error messages
    $msg= "";

    //Used to execute queries and return what happened
    function execute_sql($sql, $conn) {
        if($conn->query($sql)=== TRUE) {
            $msg = "Successful";
         }
         else {
             $msg = "ERROR: Invalid values not found in db.";
         }
        return $msg;
    }

    include 'config.php';
    //Establish DB connection
    $conn = new mysqli($servername, $username, $password, $username);

    if($conn->connect_error) {
        echo "connection error";
    }
    
    // Process edit form attribute panel
    if(isset($_POST['formtype'])) {
        $attr = $_POST['attribute'];
        $new = $_POST['new'];
        if($new != "") {
            switch($attr) {
                case "Add new Ratings":
                    $attr = "INSERT INTO ratings_desc (rating_data) VALUES('$new')";
                    break;
                case "Add new Selects":
                    $attr = "INSERT INTO selects_desc (select_data) VALUES('$new')";
                    break;
                case "Add new Questions":
                    $attr = "INSERT INTO questions_desc (question_data) VALUES('$new')";
                    break;
                case "Delete Ratings":
                    $attr = "DELETE FROM ratings_desc WHERE rating_data='$new'";
                    break;
                case "Delete Selects":
                    $attr = "DELETE FROM selects_desc WHERE select_data='$new'";
                    break;
                case "Delete Questions":
                    $attr = "DELETE FROM questions_desc WHERE question_data='$new'";
                    break;
                default:
                    $msg = "ERROR: Hacked?";
                    break;
             }
             $msg = execute_sql($attr, $conn);
         }
         else {
             $msg = "ERROR: You did not enter a value for a field.";
         }
    }

    // Process edit admin attribute panel
    else if(isset($_POST['formtype1'])) {
        $attr = $_POST['attribute'];
        $new = $_POST['new'];
        $sql = "";
        if($new != "") {
           if($attr == "Change Admin name") {
               $sql = "UPDATE EFadmins SET EFname= '$new' WHERE EFadminID ='1'";
           }
           else if($attr == "Change Admin password") {
               $sql = "UPDATE EFadmins SET EFpassword= '$new' WHERE EFadminID ='1'";
           }
           $msg = execute_sql($sql, $conn);
       }
       else {
           $msg = "ERROR: Please fill out all fields";
       }
    }
    else if(isset($_POST['formtype2'])) {
        $id = $_POST['formtype2'];
        $sql = "DELETE FROM EFapplicants WHERE EFuserid=$id";
        $sql2 = "DELETE FROM ratings WHERE EFuserid=$id";
        $sql3 = "DELETE FROM selects WHERE EFuserid=$id";
        $sql4 = "DELETE FROM questions WHERE EFuserid=$id";
           $msg = execute_sql($sql, $conn);
           $msg = execute_sql($sql2, $conn);
           $msg = execute_sql($sql3, $conn);
           $msg = execute_sql($sql4, $conn);
    }

    // Create array of db data
    function create_arrays ($sql, $conn) {
        $sql = "SELECT EFfirstname, EFlastname, EFsupfname, EFsuplname, EFuserid FROM EFapplicants";
        $counter = 0;
        $data;
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[$counter] = array($row['EFfirstname']." ".$row['EFlastname'], $row['EFsupfname']." ".$row['EFsuplname'],$row['EFuserid'] );
                $counter++;
            }
        }
        return $data;
    }

    //Create table to display data
    function create_table($conn) {
        $sql = "SELECT EFfirstname, EFlastname, EFsupfname, EFsuplname, EFuserid FROM EFapplicants";
        $data = create_arrays($sql, $conn, $count);
        $table = '<div class="table-responsive"><table class="table">'."\n".'<thead><tr><th>Student Name</th><th>Supervisor Name</th><th>Actions</th></tr></thead><tbody>'."\n";
        $leng = count($data);
        for ($i = 0; $i < $leng; $i++) {
            $subLen = count($data[$i]);
            $table .= "<tr>";
            for($j=0; $j < $subLen-1; $j++) {
                $table .= "<td>".$data[$i][$j]."</td>";
            }
            $table .= "<td><a style='font-size:14px;text-decoration:none;' href='http://sw.cs.wwu.edu/~huange2/cs300/EFForm/convert.php?id=".$data[$i][$j]."' target='_blank'><input type='button' value='Print View'></a></td><td><form action='adminEFForm.php' method='POST'><input type='hidden' name='formtype2' value='".$data[$i][$j]."'><input type='submit' value='Delete Form'></form></td></tr>\n"; 
        }
        return $table;
    }
        
    //HTML elements panel contents  
    $table = create_table($conn);
    $editForm = <<< EOT
<div class="container">
  <h2> Edit Form information:</h2><br>
 <p>Change/Update Questions, Ratings, and Select description</p>
  <form action="adminEFForm.php" method="POST" class="form-horizontal" role="form">
    <input type="hidden" name="formtype" id="formtype" value='form1'>
    <div class="form-group">
      <label class="control-label col-sm-2" for="attribute">Attribute to edit:</label>
          <div class="col-sm-10"> 
            <select id="attribute" name="attribute">
                <option>Add new Ratings</option>
                <option>Add new Questions</option>
                <option>Add new Selects</option>
                <option>Delete Ratings</option>
                <option>Delete Questions</option>
                <option>Delete Selects</option>
             </select>
         </div>
    </div>
      <p>Enter new value to add or old value to delete.</p>
    <div class="form-group">
      <label class="control-label col-sm-2" for="new">Value to delete/add:</label> 
      <div class="col-sm-9">
        <input type="text" class="form-control" id="new" name="new" placeholder="Enter New Value">
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-9">
        <input id="edit1" type="submit" class="btn btn-default" value="Edit">
      </div>
    </div>
  </form>
</div>
EOT;

$editAdmin = <<< EOT
<div class="container">
  <h2> Edit Admin Info:</h2><br>
 <p>Change/Update admin name and password</p>
  <form action="adminEFForm.php" method="POST" class="form-horizontal" role="form">
    <input type="hidden" name="formtype1" id="formtype" value='form2'>
    <div class="form-group">
      <label class="control-label col-sm-2" for="attribute">Task:</label>
          <div class="col-sm-9"> 
            <select id="attribute" name="attribute">
                <option>Change Admin password</option>
                <option>Change Admin name</option>
             </select>
         </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="new">NewValue:</label> 
      <div class="col-sm-9">
        <input type="text" class="form-control" id="new" name="new" placeholder="Enter Updated Value">
      </div>
    </div>
    <div class="form-group">        
      <div class="col-sm-offset-2 col-sm-9">
        <input id="edit1" type="submit" class="btn btn-default" value="Edit">
      </div>
    </div>
  </form>
</div>
EOT;

    //Produce html page
    include 'page.php';
    $adminPage = createPage("CSCI 403 Form Admin Page", $editForm, $editAdmin, $table, 'Edit Evaluation form', 'Edit Admin Info', 'Form Submitted');

   $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
	    Admin Page
        </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/webform.css">
    </head>
    <body>
        <div class="container"><p id="errorBox"> <?php echo "$msg"; ?></p></div>
        <?php echo $adminPage; ?>
    </body>
</html>
