<html>
	<head>
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<?php
$con=mysqli_connect("127.0.0.1","root","","library");
	// Check connection
	if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	//echo $_POST["card_no"];
	$card_no = $_POST["card_no"];

	$finehist = "SELECT bl.loan_id,bl.book_id,bl.due_date,bl.date_in,f.fine_amt
				from book_loans bl inner join fines f on 
				bl.loan_id=f.loan_id where bl.card_no=".$card_no." and paid='1'";
	
	$resultfinehist = mysqli_query($con,$finehist);
	$result_rows_fine = mysqli_num_rows($resultfinehist);
	if($result_rows_fine<1)
	echo "<h3>No Fine History Exists<br></h3>";
	else{
	echo "<h3>Previously Paid Fines:<br></h3>";
	echo "<table align=center><tr><th>Loan ID<th>Book ID<th>Due Date<th>Date In<th>Amount Paid";
	
	while($row_fine_hist = mysqli_fetch_array($resultfinehist))
		{
			//echo $row_fine_hist['loan_id'];
			echo "<tr><td class='bg'>".$row_fine_hist['loan_id']."<td class='bg'>".$row_fine_hist['book_id']."<td class='bg'>".$row_fine_hist['due_date'].
						"<td class='bg'>".$row_fine_hist['date_in']."<td class='bg'> &#36; ".$row_fine_hist['fine_amt'];
		}
		}

?>