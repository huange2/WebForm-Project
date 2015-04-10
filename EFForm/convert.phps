<?php
    /* Name:Erica Huang
     * Date: 2-28-2015
     * File: convert.php
     * Purpose: Pdf versions of forms submitted created with fpdf.
     *          Credit to: www.fpdf.org
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
    if(!isset($_SESSION['EFlog']) || ($_SESSION['EFlog'] != 'in') ) {
         echo "Restricted area you need to log in first! <a href='EFlogin.php'> Back to login page </a>";
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

       $ratingINFO = execute_sql($conn, "SELECT rating_info, EFuserid FROM ratings WHERE EFuserid ='$id'");
       $selectINFO = execute_sql($conn, "SELECT select_info, EFuserid FROM selects WHERE EFuserid = '$id'");
       $questionINFO = execute_sql($conn, "SELECT question_info, EFuserid FROM questions WHERE EFuserid = '$id'");
       $userINFO = execute_sql($conn, "SELECT EFfirstname, EFlastname, EFsupfname, EFsuplname, EFphone, EFgrade, EFuserid FROM EFapplicants WHERE EFuserid='$id'");
       $ratingDESC = execute_sql($conn, "SELECT rating_data FROM ratings_desc");
       $selectDESC = execute_sql($conn, "SELECT select_data FROM selects_desc");
       $questionDESC = execute_sql($conn, "SELECT question_data FROM questions_desc");
       $basicCnt = count($userINFO[0]);
       $ratingCnt = count($ratingINFO);
       $selectCnt = count($selectINFO);
       $questionCnt = count($questionINFO);

       $conn->close();

       class PDF extends FPDF {
           //Page header
           function Header() {
               //Logo
               $this->Image('images/wwulogo.png',10,8,33);
               //Arial bold 15
               $this->SetFont('Arial','B',15);
               //Move to the right
               $this->Cell(80);
               //Title
               $this->Cell(30,10,'CSCI 403 INTERNSHIP EVALUATION FORM',0,0,'C');
               //Line break
               $this->Ln(35);
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
       $pdf->Cell(0,5,"           STUDENT NAME:       ".$userINFO[0][0]." ".$userINFO[0][1],0,1);
       $pdf->Ln(5);
       $pdf->Cell(0,5,"      SUPERVISOR NAME:      ".$userINFO[0][2]." ".$userINFO[0][3],0,1);
       $pdf->Ln(5);  
       $pdf->Cell(0,5,"    SUPERVISOR PHONE:      ".$userINFO[0][4],0,1);
       $pdf->Ln(5);
       $pdf->Cell(0,5,"RECOMMENDED GRADE:     ".$userINFO[0][5],0,1);
       $pdf->SetFont('Arial', 'B', 10);
       $pdf->Ln(5);
       $pdf->Cell(0,10,"RATINGS: ",0,1);
       $ratingHeader = array("  " , "Excellent", "Good", "Fair" , "Poor");
       $selectHeader = array("  " , "N/A", "Excellent", "VeryGood", "Average", "Mediocre" , "Poor");
       $pdf->SetFont('Arial', '', 10);
       $pdf->table($ratingHeader, $ratingINFO, $ratingDESC, $ratingCnt, 80);
       $pdf->SetFont('Arial', 'B', 10);  
       $pdf->Ln(5);    
       $pdf->Cell(0,10,"SELECTS: ",0,1);  
       $pdf->SetFont('Arial', '', 10);
       $pdf->table($selectHeader, $selectINFO, $selectDESC, $selectCnt, 45);
       $pdf->SetFont('Arial', 'B', 10);
       $pdf->Ln(5);
       $pdf->Cell(0,10,"QUESTIONS: ",0,1);
       for($i=0;$i<$questionCnt;$i++) {
          $j = $i+1;
          $pdf->SetFont('Arial', 'B', 10);
          $pdf->MultiCell(0,5,$j.".  ".$questionDESC[$i][0]."  ",0,1);
          $pdf->SetFont('Arial', '', 10);
          $pdf->MultiCell(0,5,"     Answer:  ".$questionINFO[$i][0],0,1);
          $pdf->Ln(5);
       }
       $pdf->Output(); 
    }
?>
