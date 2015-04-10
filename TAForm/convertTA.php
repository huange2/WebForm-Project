<?php
    /* Name:Erica Huang
     * Date: 2-28-2015
     * File: convertTA.php
     * Purpose: Pdf generator for ta forms
   */
    require('pdfconvertor/fpdf.php');
    //Start Session
    session_save_path('tmp'); session_start();

    function execute_sql($conn, $sql) {
        $data=array();
        $counter = 0;
        $result=$conn->query($sql);
        if($result->num_rows > 0) {
            while($row= $result->fetch_array(MYSQLI_NUM)){
                $data[$counter] = $row;
                $counter++;
            }
        }
        return $data;
    }
    if(!isset($_SESSION['TAlog']) || ($_SESSION['TAlog'] != 'in') ) {
         echo "Restricted area you need to log in first! <a href='TAlogin.php'> Back to login page </a>";
         exit();
    }
    if(isset($_GET['id'])) {
       $id = $_GET['id'];
       include 'config.php';
       //Establish DB connection
       $conn = new mysqli($servername, $username, $password, $username);

       if($conn->connect_error) {  
           echo "connection error";
       }

       $timeINFO = execute_sql($conn, "SELECT 8am, 9am, 10am, 11am, 12pm, 1pm, 2pm, 3pm, 4pm, 5pm, TAuserid FROM time_prefs WHERE TAuserid = '$id'");
       $userINFO = execute_sql($conn, "SELECT TAfirstname, TAlastname, sponsor_id, TAemail, TAphone, TAuserid FROM TAapplicants WHERE TAuserid='$id'");
       $spon_num = $userINFO[0][2];
       $sponsorINFO = execute_sql($conn, "SELECT sponsor_name, sponsor_id FROM sponsors WHERE sponsor_id = '$spon_num'");
       if($_GET['type'] == "Undergraduate") {
           $classDESC = execute_sql($conn, "SELECT class_name FROM classes");
           $classINFO = execute_sql($conn, "SELECT class_choice, class_id, TAuserid FROM class_prefs WHERE TAuserid ='$id'");
           $classCnt = count($classINFO);
       }
       $basicCnt = count($userINFO[0]);
       $timeCnt = count($timeINFO[0]);

       $conn->close();
  
       $temp=count($timeINFO);

       class PDF extends FPDF {
           //Page headerhttp://sw.cs.wwu.edu/~huange2/cs300/adminTAForm.php
           function Header() {
               //Logo
               $this->Image('images/wwulogo.png',10,8,33);
               //Arial bold 15
               $this->SetFont('Arial','B',15);
               //Move to the right
               $this->Cell(80);
               //Title
               $this->Cell(30,10,'TA APPLICATION FORM',0,0,'C');
               //Line break
               $this->Ln(35);
           }
           function table2($header, $data, $dataDesc, $count, $width) {
               // Header
               $leng =count($header);
               $this->Cell($width,7,$header[$i],1);
               for($i=1;$i<=$leng-1;$i++) 
                   $this->Cell(20,7,$header[$i],1);
               $this->Ln();
               // Data
               for($i=0;$i<$count;$i++) {
                   $this->Cell($width,6,$dataDesc[$i],1);
                   for($j=1;$j<=$leng-1;$j++) {
                       if(preg_match("/^M/", $header[$j])==1 && preg_match("/^M/", $data[0][$i])==1) {
                            $this->Cell(20, 6, "X", 1);
                       }
                       else if(preg_match("/^T/", $header[$j])==1 && preg_match("/T/", $data[0][$i])==1) {
                            $this->Cell(20, 6, "X", 1);
                       }
                       else if(preg_match("/^W/", $header[$j])==1 && preg_match("/W/", $data[0][$i])==1) {
                            $this->Cell(20, 6, "X", 1);
                       }
                       else if(preg_match("/^R/", $header[$j])==1 && preg_match("/R/", $data[0][$i])==1) {
                            $this->Cell(20, 6, "X", 1);
                       }
                       else if(preg_match("/^F/", $header[$j])==1 && preg_match("/F/", $data[0][$i])==1) {
                            $this->Cell(20, 6, "X", 1);
                       }
                       else {
                           $this->Cell(20,6,"   ",1);
                       }
                   }
                   $this->Ln();
               }
           }
           function table($header, $data, $dataDesc, $count, $width) {
               // Header
               $leng =count($header);
               $this->Cell($width,7,$header[$i],1);
               for($i=1;$i<=$leng-1;$i++) 
                   $this->Cell(20,7,$header[$i],1);
               $this->Ln();
               // Data
               for($i=0;$i<$count;$i++) {
                   $this->Cell($width,6,$dataDesc[$i][0],1);
                   for($j=1;$j<=$leng-1;$j++) {
                       if( $header[$j] === $data[$i][0]) {
                            $this->Cell(20, 6, "X", 1);
                       }
                       else {
                           $this->Cell(20,6,"   ",1);
                       }
                   }
                   $this->Ln();
               }
           }
           //Page footer
           function Footer() {
               //Position at 1.5 cm from bottom
               $this->SetY(-15);
               //Arial italic 8
               $this->SetFont('Arial','I',8);
               //Page number
               $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
           }
       }
    
       $pdf=new PDF();
       $pdf->AliasNbPages();
       $pdf->AddPage();
       $pdf->SetFont('Arial', 'B', 10);
       $pdf->Cell(0,10,"BASIC INFO",0,1);
       $pdf->SetFont('Arial', '', 10);
       $pdf->Cell(0,5,"STUDENT NAME:       ".$userINFO[0][0]." ".$userINFO[0][1],0,1);
       $pdf->Ln(2);
       $pdf->Cell(0,5,"SPONSOR NAME:      ".$sponsorINFO[0][0],0,1);
       $pdf->Ln(2);
       $pdf->Cell(0,5,"EMAIL:                       ".$userINFO[0][3],0,1);
       $pdf->Ln(2);
       $pdf->Cell(0,5,"PHONE:                      ".$userINFO[0][4],0,1);
       $pdf->Ln(2);
       $pdf->Cell(0,5,"WESTERN ID:            ".$userINFO[0][5],0,1);
       $pdf->Ln(2);  
       $pdf->Cell(0,5,"STUDENT TYPE:        ".$_GET['type'],0,1);
       $pdf->SetFont('Arial', 'B', 10);
       $pdf->Ln(2);
       $timeHeader = array("  " , "Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
       $timeDESC = array( "8am-8:50", "9am-9:50", "10am-10:50", "11am-11:50", "12pm-12:50", "1pm-1:50", "2pm-2:50", "3pm-3:50", "4pm-4:50", "5pm-5:50");
       if($_GET['type'] === "Undergraduate") {
           $classHeader = array("  " , "1st", "2nd", "3rd" , "NoWay");
           $pdf->Cell(0,10,"CLASS PREFERENCE: ",0,1);
           $pdf->SetFont('Arial', '', 10);
           $pdf->table($classHeader, $classINFO, $classDESC, $classCnt, 40);
           $pdf->SetFont('Arial', 'B', 10);  
           $pdf->Ln(5);    
       }
       $pdf->Cell(0,10,"TIME UNAVAILABLE: ",0,1);  
       $pdf->SetFont('Arial', '', 10);
       $pdf->table2($timeHeader, $timeINFO, $timeDESC, 10, 25);
       $pdf->SetFont('Arial', 'B', 10);

       $pdf->Output(); 
    }
?>
