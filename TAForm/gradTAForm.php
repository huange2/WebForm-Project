<?php

    /* Name:Erica Huang
     * Date: 1-28-2015
     * File: gradTAForm.php
     * Purpose: Provide a form for graduate TA's
     */

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

    include 'config.php';

    $conn = new mysqli($servername, $username, $password, $username);
    if($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }

    $agreement= fetch_data($conn, "SELECT agreement_info FROM agreement", "agreement_info");
    $sponsors = fetch_data($conn, "SELECT sponsor_name FROM sponsors", "sponsor_name");
    $time = array("8:00 - 8:50", "9:00 - 9:50", "10:00 - 10:50", "11:00 - 11:50", "12:00 - 12:50", 
                  "1:00 - 1:50", "2:00 - 2:50", "3:00 - 3:50", "4:00 - 4:50", "5:00 - 5:50");
    $timeRef = array("M", "T", "W", "R", "F");
    function createSelect ($array, $id) {
        $select = "<select id='$id' name='$id'>\n";
        $leng = count($array);
        for($i=0; $i<$leng;$i++) {
            $select .= "<option>".$array[$i]."</option>\n";
        }
        $select .= "</select>\n";
        return $select;
    }
    //Used to create dynamic schedule table.
    function createTable($array, $type, $id, $choices, $ref) { 
        $labelId = 0;
        $length = count($array);
        for($counter=0; $counter < $length; $counter++) {
            echo "<tr>";
            echo "<td>" . $array[$counter] . "</td>\n";
            for($i=0; $i < $choices; $i++){
                echo " <td class='customBox'><input type=".'"'.$type.'" '."id=".'"'.$labelId.$id.'"'." name=".'"'.$id.$counter.'[]"'." value=".'"'.$ref[$i].'"'."><label for="."'".$labelId.$id."'>"."</label></td>\n";
                $labelId++;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
	    Graduate TA Form
        </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="css/webform.css">
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script language="javascript" type="text/javascript" src="js/TAForm.js"></script>
    </head>
    <body onload="init()">
        <div class="main">
            <div class="header">
                <h1>WWU CS Graduate TA Application Form</h1>
                <h3> <b>Please double check your form before submitting.</b></h3>
            <div class="sub-header">
                <h5>Teaching Assistants (TA) are hired to manage labs in introductory level courses.  Applicants for a TA position need to be full time student, in “good standing,” and have the backing of at least one faculty member in the department.</h5> 
                <h5>If you are interested in being considered for a TA position, please sign the agreement below and continue with this form.  Note that this form is only an application: it does not guarantee that you will be a TA.  The CS faculty will select TAs amongst all valid applicants. </h5>
             </div>
            </div>
            <form id="GTAForm" action="processTAForm.php" method="POST">
                 <fieldset id="part1">
                     <h2> AGREEMENT CONDITIONS</h2>
                     <h3><?php echo $agreement[0]; ?></h3>
                     <input type="checkbox" name="agreement1" id="agreement1" value="I agree"><label for="agreement1">I agree</label>
                     <p id="agreeError"> You must agree in order to continue!</p>
                     <input type="hidden" name="formType" value="GTA">
                 </fieldset>
                 <fieldset id="step1">
                     <div> <h2> Please enter basic Information: </h2></div>
                     <div><label><b>First Name:</b> </label><input type="text" name="studentF" id="studentF" value=""></div>
                     <div><label><b>Last Name:</b></label> <input type="text" name="studentL" id="studentL" value=""></div>
                     <div><label><b>Student Number: </b></label><input type="text" name="studentN" id="studentN" value=""></div>
                     <div><label><b>Email:</b></label> <input type="text" name="email" id="email" value=""></div>
                     <div><label><b>Phone Number:</b></label> <input type="text" name="phone" id="phone" value=""></div>
                     <div><label><b>Which faculty member do you think would be able to sponsor you for the position:</b></label><br>             <?php echo createSelect($sponsors,"sponsor"); ?></div>
                     <p id="errorBox"> The phone number must be 10 digits and in one of the following format: xxxxxxxxxx, (xxx) xxx-xxxx, xxx-xxx-xxxx</p>
                     <p id="nameError"> Please fill all the fields.</p>
                     <p id="idError"> Invalid W number must be in the format: WXXXXXXXXX (a 'W' and 9 digits)</p>
                     <p id="emailError">Invalid email must be in form username@something.com</p>
                 </fieldset>
                 <fieldset id="part3">
                     <h4>Please indicate by clicking the boxes the times you are in class.</h4>
                     <table>
                         <tr>
                              <th>Days of Week</th>
                              <th>Monday</th>
                              <th>Tuesday</th>
                              <th>Wednesday</th>
                              <th>Thursday</th>
                              <th>Friday</th>
                         </tr>
                         <?php createTable($time, "checkbox", "checkRow", 5, $timeRef); ?>
                     </table>
                 </fieldset>
                 <fieldset id="part4">
                     <p>Thank you! </p>
                 </fieldset>
                 <input id="prevB" type="button" value="Prev" onclick="prev()">
                 <input id="nextB" type="button" value="Next" onclick="next()">
                 <input id="submitB" type="submit" value="Submit">
                 <a href="http://faculty.wwu.edu/~granier/TA/Winter_2015/">
                     <div id="home"> 
                         <p>HOME</p>
                         <img src="images/home168.png" alt="homepage">
                     </div>
                 </a>
                 <a href="http://sw.cs.wwu.edu/~huange2/cs300/TAForm/gradTAForm.php">
                     <div id="home"> 
                         <p>RESET</p>
                         <img src="images/recycle27.png" alt="resetform">
                    </div>
                 </a><br><br>
            </form>
        </div>
    </body>
<?php $conn->close(); ?>
</html>
