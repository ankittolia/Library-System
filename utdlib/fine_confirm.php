<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
 <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"> </script>
 <script type="text/javascript">

  function loadhist(cardno){              
	$.ajax({    // creating ajax request
        type: "POST",
        url: "displayhist.php", 
		data:{'card_no':cardno},
        dataType: "html",   //expect html to be returned                
        success: function(response){                    
            $("#responsecontainer").html(response); 
          
        }
    });
}

</script>
</head>


<?php   
	session_start();
	if(isset($_SESSION["searchfine"])){
	unset($_SESSION["searchfine"]);
	}

	if(isset($_POST['chkfine'])){
	$_SESSION['chkfine'] = $_POST['chkfine'];
	
	$fine_details=explode("|",$_SESSION['chkfine']);
	$card_no=$fine_details[0];
	$_SESSION['card_no']=$card_no;

	$loan_present_fine=0;
	$outbooks=0;
	$con=mysqli_connect("127.0.0.1","root","","library");
	// Check connection
	if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	$fetchq = "SELECT bl.loan_id,bl.card_no, bl.date_in, b.book_id, b.title,bl.due_date 
				from book_loans bl inner join borrower bor on 
				bl.card_no=bor.card_no inner join book b on bl.book_id=b.book_id
				where bl.card_no ='$card_no'";
	$result = mysqli_query($con,$fetchq);
	//$_SESSION["result"]=$result;
	$outcount=1;
	while($row = mysqli_fetch_array($result))
    {
	//Check for any books not yet submitted to library
	if($row['date_in']=='0000-00-00' or $row['date_in']==null){
		if($outbooks==0){
		echo "<br><br><center><font color=red><h3>Can't make Payment until the below books are returned!</h3></font></center>
				<table align=center><tr><td class='nobg'>Borrower Name:<td class='nobg'><b>".$fine_details[1];
		echo "<tr><td colspan=2 class='nobg'>Outstanding Books:";
		}
		$outbooks=1;
		echo "<tr><td colspan=2><b>".$outcount.".<i> ".$row['title']."(".$row['book_id'].")</b></i>";
		$outcount++;
		}
		
	}

	// If all books are checked in
	if($outbooks!=1){
	$total_fine=0;
	/*mysqli_data_seek($result, 0);
	while($row_loan = mysqli_fetch_array($result))
    {
	$loanid=$row_loan['loan_id'];
	$fetchloan = "SELECT * from fines where loan_id='$loanid'";
	$resultloan = mysqli_query($con,$fetchloan);
	$fine_row_count = mysqli_num_rows($resultloan);
	if($fine_row_count==0){
		 $late_days=(strtotime($row_loan['date_in']) - strtotime($row_loan['due_date'])) / (60 * 60 * 24);
		 $fine_amt= $late_days*0.25;
		 $sqlins= "INSERT INTO fines(loan_id,fine_amt,paid) VALUES 
				('$loanid','$fine_amt',0)";	
		 $query = mysqli_query($con,$sqlins) or die("Error inserting fine data!");
		
	}
	}*/
	
	echo "<table border=1>";	
	mysqli_data_seek($result, 0);
	$fine_paid=1;
	// Display the total fine for payment not made
	while($row = mysqli_fetch_array($result))
    {
	$loanid=$row['loan_id'];
	$fetchfine = "SELECT * from fines where loan_id='$loanid'";
	$resultfine = mysqli_query($con,$fetchfine);
	$row_fine = mysqli_fetch_assoc($resultfine);
	if($row_fine['paid']==0){
	$total_fine = $total_fine + $row_fine['fine_amt'];
		$fine_paid=0;
		}
	}
	if($fine_paid==0){
	$_SESSION['total_fine'] = $total_fine;
	mysqli_data_seek($result, 0);
	//$borrower_rec = mysqli_fetch_assoc($result); //Fetches the first record of a given card no.
	echo "<form name='payment' id='payment' method=post><br><h2 align=center style=color:'#09C'><i>Confirm Payment</h2>";
	echo "<table align=center cellspacing='4'><tr><th>Card No<th>Name<th>Total Fine Due";
	echo "<tr><td class='bg'>".$fine_details[0]."<td class='bg'>".$fine_details[1]."<td class='bg'> &#36; ".$total_fine;
	echo "<tr><tr><td colspan=3 class='nobg'><input type=submit id='confirmpay' name='confirmpay' value='Confirm Payment'>";
	echo "&nbsp;&nbsp;&nbsp;<input type=button id='finehistbtn' onClick=loadhist(".$_SESSION['card_no'].") name='finehistbtn' 
			value='Show Fine History'></table>";

	echo "<br><div id=responsecontainer align=center></div>";
	}
	else{
	echo "<br><font color='red'><h2 align=center>No such Pending Payment Record Exists!</h2></font>";
		}
	}
	}
	
	
	if(isset($_POST['confirmpay'])){	
	$con=mysqli_connect("127.0.0.1","root","","library");
	// Check connection
	if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$card_no=$_SESSION['card_no'];
	
	$fetchq = "SELECT bl.loan_id,bl.card_no,concat(bor.fname,' ',bor.lname) as name, bl.date_in, b.book_id, b.title,bl.due_date 
				from book_loans bl inner join borrower bor on 
				bl.card_no=bor.card_no inner join book b on bl.book_id=b.book_id where 
				bl.card_no ='$card_no'";
	//echo $fetchq;
	$result = mysqli_query($con,$fetchq);
	$name="";
	while($row = mysqli_fetch_assoc($result))
		{
		$loanid=$row['loan_id'];
		$name=$row['name'];
		$fineupd = "update fines set paid=1 where loan_id='$loanid'";
		$fineupdq = mysqli_query($con,$fineupd);
		}
	echo "<table align=center><tr><th>Card No<th>Name<th>Amount Paid";
	echo "<tr><td class='bg'>".$card_no."<td class='bg'>".$name."<td class='bg'><b>".$_SESSION['total_fine'];

	echo "</table><br><font color='green'><h2 align=center>Payment Made Successfully!</h2></font>";
	}
	
	if(!isset($_SESSION["searchfine"])){
		echo "<b><i><p align=center><a href='pay_fine.php'>&lt;&lt;Go to Search Fine</a></p>";
		$_SESSION["searchfine"]=1;
	}

	if(!(empty($con)))
	mysqli_close($con);

?>