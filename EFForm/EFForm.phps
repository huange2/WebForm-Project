<?php

    /* Name:Erica Huang
     * Date: 1-28-2015
     * File: EFForm.php
     * Purpose: Provide a internship evaluation form
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
    
    //Database information
    $instructions = "Please feel free to complete or ignore the following questions. There is no need to be very detailed, but if you could single out the most particular characteristics of your intern, it would help grading their work. Thank you!";
 
    $rateText = fetch_data($conn, "SELECT rating_data FROM ratings_desc", "rating_data");
    $selectText = fetch_data($conn, "SELECT select_data FROM selects_desc", "select_data");
    $questionText = fetch_data($conn, "SELECT question_data FROM questions_desc", "question_data");

    //Create the list of select options
    function createSelect($array) {
        $length = count($array);
        $selections = <<< EOD
    <option value="N/A">N/A</option>
    <option value="Excellent">Excellent</option>
    <option value="VeryGood">Very Good</option>
    <option value="Average">Average</option>
    <option value="Mediocre">Mediocre</option>
    <option value="Poor">Poor</option>
</select>
</li>
EOD;
        for($counter=0; $counter < $length; $counter++) {
            echo "<li>".$array[$counter]."<br>\n";
            echo "<select name='sel".$counter."'>\n";
            echo "$selections";
        }
    }

    //Create the list of questions options
    function createText($array) {
        $length= count($array);
        $increment = 4;
        for($counter=0; $counter < $length; $counter+=2) {
            $index = $counter;
            $index = $index + 1;
            echo "<fieldset id='step".$increment."' >\n";
            echo "<p><b>Please feel free to complete or ignore the following questions. There is no need to be very detailed, but if you could single out the most particular characteristics of your intern, it would help grading their work. Thank you!</b></p>\n";
            echo "<ol start='".$index ."'>\n";
            echo "<li><b>".$array[$counter]."</b><br><br>\n";
            echo "<textarea name='opt".$counter."' value='' rows='10' cols='50'></textarea><br><br>\n";
            echo "</li><li><b>".$array[$counter+1]."</b><br><br><textarea name='opt".$index;
            echo "' value='' rows='10' cols='50'></textarea><br><br>\n";
            echo "</li></ol></fieldset>\n";
            $increment++;
        }
    }

    //Create a table of ratings
    function createTable($array, $type, $id, $choices) { 
        $labelId = 0;
        $ratingDesc = array("Excellent", "Good", "Fair", "Poor");
        $length = count($array);
        for($counter=0; $counter < $length; $counter++) {
            echo "<tr>";
            echo "<td>" . $array[$counter] . "</td>\n";
            for($i=0; $i < $choices; $i++){
                $temp = $ratingDesc[$i];
                echo " <td class='customBox'><input type=".'"'.$type.'" '."id=".'"'.$labelId.$id.'"'." name=".'"'.$id.$counter.'"'." value=".'"'.$temp.'"'."><label for="."'".$labelId.$id."'>"."</label></td>\n";
                $labelId++;
            }
        }
    }
    $conn->close();
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
	    Internship Evaluation Form
        </title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="css/webform.css">
    </head>
    <body onload="init()" >
        <div class="main">
        <div class="header">
            <h1>CSCI 403 Internship Evaluation Form</h1>
            <h3> <b>Please double check your form before submitting.</b></h3><br>
        </div>
        <form id= "evalForm" action="processEFForm.php" method="POST">
            <fieldset id="step1">
                <div> <h2> Please enter basic Information: </h2></div>
                <div><label><b>First Name of Student:</b> </label><input type="text" name="studentF" id="studentF" value=""></div>
                <div><label><b>Last Name of Student:</b></label> <input type="text" name="studentL" id="studentL" value="" ></div>
                <div><label><b>Name of Company:</b></label> <input type="text" name="company" id="company" value=""></div>
                <div><label><b>First Name of Supervisor: </b></label><input type="text" name="supervisorF" id="supervisorF" value=""></div>
                <div><label><b>Last Name of Supervisor:</b></label> <input type="text" name="supervisorL" id="supervisorL" value=""></div>
                <div><label><b>Supervisor's phone/fax number:</b></label> <input type="text" name="phone" id="phone" value=""></div>
                <p id="errorBox"> The phone number must be 10 digits and in one of the following format: xxxxxxxxxx, (xxx) xxx-xxxx, xxx-xxx-xxxx</p>
                <p id="nameError"> Please fill all the fields.</p>
            </fieldset>
            <fieldset id="step2">
                <table>
		    <tr>
		        <th>Please, rate your intern</th>
		        <th>Excellent</th>
		        <th>Good</th>
    		        <th>Fair</th>
		        <th>Poor</th>
                    </tr>
                    <?php echo createTable($rateText, "radio", "rows", 4); ?>
            </table>
            </fieldset>
            <fieldset id="step3">
                <h3><b>Information on this evaluation will be used to help the ISC student intern improve professional performance and will serve as a component of the student's grade. To make the internship as useful as possible, please make specific suggestions about qualities that need to be emphasized and ways that the intern can improve.</b></h3>
                <h4> I. The following scale helps us keep track of the intern performance.</h4>
                <ol><?php echo createSelect($selectText, "select_info"); ?></ol>
            </fieldset>
             <?php echo createText($questionText, "question_info"); ?>
            <fieldset id="lastStep">
                <h3><b> Grade Recommendations:</b></h3>
                <select name="grade">
                     <option value="N/A">No Idea</option>
                     <option value="A">A</option>
                     <option value="A-">A-</option>
                     <option value="B+">B+</option>
                     <option value="B">B</option>
                     <option value="B-">B-</option>
                     <option value="C+">C+</option>
                     <option value="C">C</option>
                     <option value="C-">C-</option>
                     <option value="D+">D+</option>
                     <option value="D">D</option>
                     <option value="D-">D-</option>
                     <option value="F">F</option>
                 </select><br><br>
                 <p> Thank you for your time! Please hit the "submit" button when ready.</p>
            </fieldset>
            <br>
            <input type="hidden" name="ratingCnt" value="<?php echo count($rateText); ?>">
            <input type="hidden" name="questionCnt" value="<?php echo count($questionText); ?>">
            <input type="hidden" name="selectCnt" value="<?php echo count($selectText); ?>">
            <input id="prevB" type="button" value="Prev" onclick="prev()">
            <input id="nextB" type="button" value="Next" onclick="next()">
            <input id="submitB" type="submit" value="Submit">
      <a href="http://yorktown.cbe.wwu.edu/ISC/">
          <div id="home"> 
               <p>HOME</p>
               <img src="images/home168.png" alt="homepage">
          </div>
      </a>
      <a href="http://sw.cs.wwu.edu/~huange2/cs300/EFForm/EFForm.php">
          <div id="home"> 
               <p>RESET</p>
               <img src="images/recycle27.png" alt="resetform">
          </div>
      </a><br><br><br><br>
         </form>
      </div>

    </body>
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script language="javascript" type="text/javascript" src="js/form.js"></script>
</html>
