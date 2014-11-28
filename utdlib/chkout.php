<?php
	
	session_start();
	if(isset($_POST['booklist'])){
	$_SESSION['booklist'] = $_POST['booklist'];
	$_SESSION['book_count'] = count($_SESSION['booklist']);
	}
	
	if(isset($_POST["searchchkout"]) && $_POST["searchchkout"]=="yes"){
	//echo $_POST["searchchkout"];
	$_SESSION["searchchkout"]=$_POST["searchchkout"];
	if(isset($_SESSION["alreadyshown"]))
	unset($_SESSION["alreadyshown"]);
	}
	
	if(isset($_POST["searchchkout"]) && $_POST["searchchkout"]=="no"){
	//echo $_POST["searchchkout"];
	unset($_SESSION["searchchkout"]);
	if(isset($_SESSION["alreadyshown"]))
	unset($_SESSION["alreadyshown"]);
	}
	
	if(isset($_POST["cardno"])){
	$cardno = $_POST["cardno"]; 
	$_SESSION["cardno"] = $cardno;
   }
	else{
	$_SESSION["cardno"] = "";
	}
?>
<html>
<head>
	
 <meta http-Equiv="Cache-Control" Content="no-cache"/>
    <meta http-Equiv="Pragma" Content="no-cache"/>
    <meta http-Equiv="Expires" Content="0"/>

	
	<link rel="stylesheet" type="text/css" href="main.css">
	<script type="text/javascript">
	function validateForm()
	{
	var regcard = new RegExp("^[0-9]{4,10}$");
	var isValid=new Boolean();

	if(!(regcard.test(document.getElementById("cardno").value))) {
	document.getElementById("cardno").style.border = "1px solid #F70A26";
	return false;
	}
	else
	return true;
	}
	</script>
	</head>
	<body>
	<form name="chkout" method="post" onSubmit="return validateForm();">
	<?php
	if(isset($_POST['booklist'])){
	?>
	<h2>Checkout the below Book(s):</h2>
	<?php
	}
	if(!(isset($_POST['booklist'])) && isset($_SESSION['cardno'])){
	?>
	Card No:&nbsp;&nbsp;&nbsp;<td><input type="text" id="cardno" name="cardno" id="cardno" maxlength="10" 
		 value ="<?php echo $_SESSION['cardno'];?>" readonly style="border: 1px solid #878b87"/><br><br><br>
	<?php
	}
	else{
	?>
	Card No:&nbsp;&nbsp;&nbsp;<td><input type="text" id="cardno" name="cardno" id="cardno" maxlength="10" 
		placeholder="Please enter only digits (Min: 4 digits)" style="border: 1px solid #878b87"/><br><br><br>	
	<?php
	}	
	?>
	<table>
	<tr><th>Book Id <th>Title<th>Author<th>Branch Id<th>No of Copies<th>No of Available Copies<tr><tr><tr>
	<?php
	for($i=0; $i < $_SESSION['book_count']; $i++)
    {
	  $book=explode("|",$_SESSION['booklist'][$i]);
      echo "<td class='bg'>" . $book[0];
      echo "<td class='bg'>" . $book[1];
      echo "<td class='bg'>" . $book[2];
	  echo "<td class='bg'>" . $book[3];
	  echo "<td class='bg'>" . $book[4];
	  echo "<td class='bg'>" . $book[5];
      echo "</tr>";
    }
	
	?>
	<tr><tr><tr><tr>
	<?php
	if(isset($_POST['booklist'])){
	?>
	<tr><td colspan=6 align=center><input type="submit" id="submit" name="submit" value="Checkout Book(s)">
	<?php
	}
	if(!(isset($_SESSION["alreadyshown"]))){
	if(isset($_SESSION["searchchkout"])){
		echo "<br><i><b><p align=center><a href='search_chkout.php'>&lt;&lt;Go to Search Book</a></p>";
		$_SESSION["alreadyshown"]=1;
	}
	else{
		echo "<br><i><b><p align=center><a href='search_book.php'>&lt;&lt;Go to Search Book</a></p>";
		$_SESSION["alreadyshown"]=1;
	}
	}
	?>
	</table>
	</body>
	</html>

	<?php   
	
	if(isset($_POST['submit'])){
	$con=mysqli_connect("127.0.0.1","root","","library");
	// Check connection
	if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
		 
	$card_no=$_SESSION["cardno"];
	$chkborrower = mysqli_query($con,"select * from borrower where card_no=$card_no;");
	$chk_num_rows = mysqli_num_rows($chkborrower);

	if($chk_num_rows==0){
	echo "<font color='red' ><h2 align=center>Borrower Data Doesn't Exist.</h2></font>";
	if(isset($_SESSION["searchchkout"])){
		echo "<p align=center><a href='search_chkout.php'>&lt;&lt;Go to Search Book</a></p>";
	}
	else
		echo "<p align=center><a href='search_book.php'>&lt;&lt;Go to Search Book</a></p>";
	}
	else{
	$sqlfine= mysqli_query($con,"select count(*) from book_loans bl inner join fines f on bl.loan_id = f.loan_id
			where bl.card_no=$card_no and f.paid=0"); 
	$rowfine = mysqli_fetch_row($sqlfine);
	if($rowfine[0]>0){
	echo "<font color='red'><h2 align=center>Borrower has a pending payment.</h2></font>";
	if(isset($_SESSION["searchchkout"]))
		echo "<p align=center><a href='search_chkout.php'>&lt;&lt;Go to Search Book</a></p>";
	else
		echo "<p align=center><a href='search_book.php'>&lt;&lt;Go to Search Book</a></p>";
	}
	else{
	$sqlq= mysqli_query($con,"select count(*) from book_loans where card_no=$card_no and (date_in = '0000-00-00' 
			or date_in is null);");
	$row = mysqli_fetch_row($sqlq);
	
	if(($row[0]+$_SESSION['book_count'])>3){
	echo "<font color='red' ><h2 align=center>Borrower Book Loan Limit Exceeded! Only 3 Book Loans Permitted.</h2></font>";
	if(isset($_SESSION["searchchkout"]))
		echo "<p align=center><a href='search_chkout.php'>&lt;&lt;Go to Search Book</a></p>";
	else
		echo "<p align=center><a href='search_book.php'>&lt;&lt;Go to Search Book</a></p>";
	}
	else{
	$tdyDate = date("Y-m-d");
	$dueDate = date('Y-m-d', strtotime($tdyDate. ' + 14 days'));
	//echo $tdyDate." ".$dueDate;

	for($i=0; $i < $_SESSION['book_count']; $i++)
    {
	  $book=explode("|",$_SESSION['booklist'][$i]);
	  
	  $sqlins= "INSERT INTO book_loans(book_id,branch_id,card_no,date_out,due_date) VALUES 
				('$book[0]','$book[3]','$card_no','$tdyDate','$dueDate')";	
      $query = mysqli_query($con,$sqlins);
	}
	echo "<font color='green'><h2 align=center>Book(s) Checked Out Successfully!</h2></font>";
	if(isset($_SESSION["searchchkout"]))
		echo "<p align=center><a href='search_chkout.php'>&lt;&lt;Go to Search Book</a></p>";
	else
		echo "<p align=center><a href='search_book.php'>&lt;&lt;Go to Search Book</a></p>";
	}
	}
	}
	mysqli_close($con);
	}
	
	?>
