<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
</head>

<?php
$con=mysqli_connect("127.0.0.1","root","","library");
if (mysqli_connect_errno())
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	
$tdyDate = date("Y-m-d");
$fetchq = "select * from book_loans where date_in='0000-00-00' or date_in is null or date_in>due_date";
$result = mysqli_query($con,$fetchq);
$num_rows = mysqli_num_rows($result);

    if($num_rows>0){
	while($row = mysqli_fetch_array($result))
    {
	$ins_fine=1;
	if($row['date_in']=='0000-00-00' or $row['date_in']==null){
	if(strtotime($tdyDate)>strtotime($row['due_date'])){
	$late_days=(strtotime($tdyDate) - strtotime($row['due_date'])) / (60 * 60 * 24);
	$fine_amt= $late_days*0.25;
	}
	else
	$ins_fine=0;
	}
	else{
	$late_days=(strtotime($row['date_in']) - strtotime($row['due_date'])) / (60 * 60 * 24);
	$fine_amt= $late_days*0.25;
	}
	$loan_id=$row['loan_id'];
	$fineq = "select paid from fines where loan_id='$loan_id'";
	$fine_result = mysqli_query($con,$fineq);
	$fine_rows = mysqli_num_rows($fine_result);
	
	if($fine_rows>0){
	$row_fine = mysqli_fetch_assoc($fine_result);
	if($row_fine['paid']==0){
	$fineupd = "update fines set fine_amt=$fine_amt where loan_id='$loan_id'";
	$fineupdq = mysqli_query($con,$fineupd);
	}
	}
	else{
	if($ins_fine==0){}
	else{
	 $sqlins= "INSERT INTO fines(loan_id,fine_amt,paid) VALUES 
				('$loan_id','$fine_amt',0)";	
      $query = mysqli_query($con,$sqlins);
		}
	 }
	}
	echo "<form><br><br><h2 align=center>Fine Data Updated Successfully!</h2></form>";
 }
else{
	echo "<font color='red'><h2 align=center>No such Late Book Records Exist!</h2></font>";
	}
if(!(empty($con)))
	mysqli_close($con);
	
?>