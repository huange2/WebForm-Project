<?php 
     /* Name:Erica Huang
     * Date: 1-28-2015
     * File: processTAForm.php
     * Purpose: Process the internship evaluation forms
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
    // Use to fetch data out of db and stick in array
    function fetch_data($connection, $sql, $value) {
        $data;
        $counter = 0;
        $result = $connection->query($sql);
        if($result->num_rows > 0) {
            while($row=$result->fetch_assoc()) {
                $data[$counter] = $row["$value"];
                $counter++;
            }
        }
        else {
            echo 'ERROR';
        }
        return $data;
    }

     function insert_arrays ($name, $userId, $value, $count, $conn) {
         $data_array = array();
         $data = "";
         for($i=0; $i <$count; $i++) {
             $temp = $_POST[$value.$i];
             foreach((array) $temp as $selected) {
                $data .= $selected;
             }
             $data_array[$i] = $data;
             $data = "";
         }
         $sql = "INSERT INTO $name VALUES('$userId', '".$data_array[0]."', '".$data_array[1]."', '".$data_array[2]."','".$data_array[3]."','".$data_array[4]."','".$data_array[5]."','".$data_array[6]."','".$data_array[7]."','".$data_array[8]."','".$data_array[9]."')";
         if($conn->query($sql) === FALSE) {
             echo " ERROR: ".$conn->error;
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
    $studentNameF = clean($_POST['studentF'],$conn);
    $studentNameL = clean($_POST['studentL'], $conn);
    $studentN = clean($_POST['studentN'], $conn);
    $email = clean($_POST['email'], $conn);
    $phone = clean($_POST['phone'], $conn);
    $spon = $_POST['sponsor'];
    $sponsor = fetch_data($conn, "SELECT sponsor_id FROM sponsors WHERE sponsor_name='$spon'", "sponsor_id");
    $form = $_POST['formType'];
    if( $studentNameF != " " && $studentNameL != " " && $studentN != " " && 
         $email != " " ) {
        if(preg_match("/^\d{10}$|^(\(\d{3}\)\s*)?\d{3}[\s-]?\d{4}$/",$phone) == 1){ 
            if(preg_match("/^[W]\d{8}$/",$studentN) == 1) {
                if(preg_match("/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/",$email) == 1) {
                    $valid = true;
                }
            }
        }
    }   
    if($valid === TRUE) {
       if($_POST['formType'] == "UTA") {
           $classes = store_data($_POST['classCnt'], "row", $conn); 
           //Insert data into db
           $basicInfo = "INSERT INTO TAapplicants VALUES ('$studentN', '$studentNameF', '$studentNameL', '$email', 
                                       '$phone', '$sponsor[0]', '1', 'ug', CURRENT_TIMESTAMP)";     
           if($conn->query($basicInfo) === TRUE) {  
               $classPref = insert_sql("class_prefs", $studentN, $_POST['classCnt'], $classes, $conn);
               $timePref = insert_arrays("time_prefs", $studentN, "checkRow", 10, $conn);
               $success = true;
           } 
           else {
               $success = false;
           } 
       }
       else if($_POST['formType'] === "GTA") {
           //Insert data into db
           $basicInfo = "INSERT INTO TAapplicants VALUES ('$studentN', '$studentNameF', '$studentNameL', '$email', 
                                       '$phone', '$sponsor[0]', '1', 'g', CURRENT_TIMESTAMP)";     
           if($conn->query($basicInfo) === TRUE) {  
               $timePref = insert_arrays("time_prefs", $studentN, "checkRow", 10, $conn);
               $success = true;
           } 
           else {
               $success = false;
           } 
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
                                            <h3> Thank you for submitting!</h3>"; ?>
                  <?php if(!$success) echo "<h1> There was an error while processing </h1>"; ?>
                 </div>
                 <div class="processed">
                 <a href="http://www.wwu.edu" style="float:left;" >
                      <div > 
                          <p> HOME</p>
                          <img src="images/home168.png" alt="homepage">
                      </div>
                 </a>
                 <a href="http://sw.cs.wwu.edu/~huange2/cs300/TAForm/<?php if($form === 'UTA') echo 'underGradTAForm.php'; else echo 'gradTAForm.php'; ?>" style="float:right;" <?php if($success) echo "hidden"; ?>>
                      <div > 
                          <p> RESET</p>
                          <img src="images/recycle27.png" alt="reset form">
                      </div>
                 </a>
                 </div><br><br>
             </div>
            </body>
      </html>
