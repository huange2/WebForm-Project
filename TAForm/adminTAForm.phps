<?php

    /* Name:Erica Huang
     * Date: 2-28-2015
     * File: adminTAForm.php
     * Purpose: Allow admins to edit content and view applicants
   */

    //Start Session
    session_save_path('tmp'); session_start();

    //Restrict access to admin page via session
    if(!isset($_SESSION['TAlog']) || ($_SESSION['TAlog'] != 'in') ) {
         echo "Restricted area you need to log in first! <a href='TAlogin.php'> Back to login page </a>";
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

    // Create array of db data
    function create_arrays ($sql, $conn) {
        $sql = "SELECT TAfirstname, TAlastname, date, student_type, TAuserid FROM TAapplicants";
        $counter = 0;
        $data;
        $ref = array("g"=>"Graduate", "ug"=>"Undergraduate");
        $result = $conn->query($sql);
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $temp = $row['student_type'];
                $data[$counter] = array($row['TAfirstname']." ".$row['TAlastname'], $row['date'], $ref[$temp], $row['TAuserid'] );
                $counter++;
            }
        }
        return $data;
    }

    //Create table to display data
    function create_table($conn) {
        $data = create_arrays($sql, $conn);
        $table = '<div class="table-responsive"><table class="table">'."\n".'<thead><tr><th>Student Name</th><th>Date Submitted</th><th>Actions</th></tr></thead><tbody>'."\n";
        $leng = count($data);
        for ($i = 0; $i < $leng; $i++) {
            $subLen = count($data[$i]);
            $table .= "<tr>";
            for($j=0; $j < $subLen-1; $j++) {
                $table .= "<td>".$data[$i][$j]."</td>";
            }
            $table .= "<td><a style='font-size:14px;text-decoration:none;' href='http://sw.cs.wwu.edu/~huange2/cs300/TAForm/convertTA.php?id=".$data[$i][$j]."&type=".$data[$i][$j-1]."' target='_blank'><input type='button' value='Print View'></a></td><td><form action='adminTAForm.php' method='POST'><input type='hidden' name='formtype2' value='".$data[$i][$j]."'><input type='submit' value='Delete Form'></form></td></tr>\n"; 
        }
        return $table;
    }
    // Process edit form attribute panel
    if(isset($_POST['formtype'])) {
        $attr = $_POST['attribute'];
        $new = $_POST['new'];
        if($new != "") {
            switch($attr) {
                case "Edit Agreement Statement":
                    $attr = "UPDATE agreement SET agreement_info='$new' WHERE agreement_id='1'";
                    break;
                case "Add Classes Offered":
                    $attr = "INSERT INTO classes (class_name) VALUES('$new')";
                    break;
                case "Add Faculty":
                    $attr = "INSERT INTO sponsors (sponsor_name) VALUES('$new')";
                    break;
                case "Delete Classes Offered":
                    $attr = "DELETE FROM classes WHERE class_name='$new'";
                    break;
                case "Delete Faculty":
                    $attr = "DELETE FROM sponsors WHERE select_data='$new'";
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
               $sql = "UPDATE TAadmins SET TAname= '$new' WHERE TAname ='1'";
           }
           else if($attr == "Change Admin password") {
               $sql = "UPDATE TAadmins SET TApassword= '$new' WHERE TAname ='1'";
           }
           $msg = execute_sql($sql, $conn);
       }
       else {
           $msg = "ERROR: Please fill out all fields";
       }
    }
    else if(isset($_POST['formtype2'])) {
        $id = $_POST['formtype2'];
        $sql = "DELETE FROM TAapplicants WHERE TAuserid='$id'";
        $sql2 = "DELETE FROM class_prefs WHERE TAuserid='$id'";
        $sql3 = "DELETE FROM time_prefs WHERE TAuserid='$id'";
           $msg = execute_sql($sql, $conn);
           $msg = execute_sql($sql2, $conn);
           $msg = execute_sql($sql3, $conn);
    }
    $table = create_table($conn);
    $editForm = <<< EOT
<div class="container">
  <h2> Edit Form information:</h2><br>
 <p>Change/Update classes offered, agreement statement or faculty list</p>
  <form action="adminTAForm.php" method="POST" class="form-horizontal" role="form">
    <input type="hidden" name="formtype" id="formtype" value='form1'>
    <div class="form-group">
      <label class="control-label col-sm-2" for="attribute">Attribute to edit:</label>
          <div class="col-sm-9"> 
            <select id="attribute" name="attribute">
                <option>Edit Agreement Statement</option>
                <option>Add Classes Offered</option>
                <option>Add Faculty</option>
                <option>Delete Classes Offered</option>
                <option>Delete Faculty</option>
             </select>
         </div>
    </div>
      <p>You can delete/add multiple values by putting whitespace between each value.</p>
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
  <form action="adminTAForm.php" method="POST" class="form-horizontal" role="form">
    <input type="hidden" name="formtype1" id="formtype" value='form2'>
    <div class="form-group">
      <label class="control-label col-sm-2" for="attribute">Task:</label>
          <div class="col-sm-10"> 
            <select id="attribute" name="attribute">
                <option>Change Admin password</option>
                <option>Change Admin name</option>
             </select>
         </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="new">NewValue:</label> 
      <div class="col-sm-9">
        <input type="text" class="form-control" id="new" name="new" placeholder="Enter Updated Value if applicable">
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

    include 'page.php';
    $adminPage = createPage("TA Form Admin Page",$editForm, $editAdmin, $table, 'Edit TA forms', 'Edit Admin Info', 'Print Application');

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
