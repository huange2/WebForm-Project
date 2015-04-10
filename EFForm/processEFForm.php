<?php

    /* Name:Erica Huang
     * Date: 1-28-2015
     * File: processForm.php
     * Purpose: Process the internship evaluation forms and store in db
     */

    //Clean user input in case of mysql injection
    function clean($value, $connection) {
        if( isset($value) && $value != ""){
            $value = mysqli_real_escape_string($connection, trim($value));
        }
        else {
            $value = " ";
        }
        return $value;
    }

    //Store common user input in array
    function store_data ($count, $name, $connection) {
        $data;
        for ($index = 0; $index < $count; $index++) {
            $temp = $name.$index;
            $data[$index] = $_POST[$temp];
            $temp2 = $data[$index];
        }
        return $data;
    }

    //Insert common user in put in array
    function insert_sql($name, $userID, $count, $array, $conn) {
        for ($index=0; $index < $count; $index++) {
            $temp = $array[$index];
            $sql = "INSERT INTO $name VALUES('$userID', '$index', '$temp')";
            if($conn->query($sql) === FALSE) {
                echo " ERROR: ".$conn->error;
                break;
            }
        }
    }

    //Establish database connection if valid
    include 'config.php';

    $conn = new mysqli($servername, $username, $password, $username);
 
    if($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //Obtain user input
    $valid = false;
    $success = false;
    $studentNameF = clean($_POST['studentF'], $conn);
    $studentNameL = clean($_POST['studentL'], $conn);
    $companyName = clean($_POST['company'], $conn);
    $supervisorNameF = clean($_POST['supervisorF'], $conn); 
    $supervisorNameL = clean($_POST['supervisorL'], $conn); 
    $phone = clean($_POST['phone'], $conn);
    $grade = $_POST['grade'];
    $rating = $_POST['ratingCnt'];
    $select = $_POST['selectCnt'];
    $question = $_POST['questionCnt'];
    $ratingData = store_data($rating, "rows", $conn);
    $selectData = store_data($select, "sel", $conn);
    $questionData = store_data($question, "opt", $conn);

    //Server side validation of user input
    if( $studentNameF != " " && $studentNameL != " " && $companyName != " " && 
         $supervisorNameF != " " && $supervisorNameL != " " && $phone != " " ) {
        if(preg_match("/^\d{10}$|^(\(\d{3}\)\s*)?\d{3}[\s-]?\d{4}$/",$phone) == 1){ 
            $valid= true;
        }
    }

    if($valid === TRUE) {

       //Insert data into db
       $basicInfo = "INSERT INTO EFapplicants VALUES ('', '$studentNameF', '$studentNameL', '$companyName', 
                                       '$supervisorNameF', '$supervisorNameL', '$phone', '$grade')";

       if($conn->query($basicInfo) === TRUE) {  
           $userID = $conn->insert_id;
           $ratingInfo = insert_sql("ratings", $userID, $rating, $ratingData, $conn);
           $selectInfo = insert_sql("selects", $userID, $select, $selectData, $conn);
           $questionInfo = insert_sql("questions", $userID, $question, $questionData, $conn);
           $success = true;
       }
       else {
           echo "ERROR: " . $conn->error;
           $success = false;
       } 
    } 
     
    $conn->close();

?>
       <!DOCTYPE html>
       <html lang="en">
            <head>
                  <title>Processed Form </title>
                  <meta charset="UTF-8">
                  <link rel="stylesheet" type="text/css" href="css/webform.css">
            </head>
            <body>
            <div class="main">
                <div class="header">
                  <?php if( $success) echo "<h1> Your form has been processed. </h1>
                                            <h3> Thank you for submitting!</h3>";?>
                  <?php if(!$success) echo "<h1> There was an error while processing </h1>";?>
                 </div>
                 <div class="processed">
                 <a href="http://yorktown.cbe.wwu.edu/ISC/" style="float:left;" >
                      <div > 
                          <p> HOME</p>
                          <img src="images/home168.png" alt="homepage">
                      </div>
                 </a>
                 <a href="http://sw.cs.wwu.edu/~huange2/cs300/EFForm/EFForm.php" style="float:right;" <?php if($success) echo "hidden" ?>>
                      <div > 
                          <p> RESET</p>
                          <img src="images/recycle27.png" alt="reset form">
                      </div>
                 </a>
                 </div><br><br>
             </div>
            </body>
      </html>
