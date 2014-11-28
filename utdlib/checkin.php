
<?php   
	session_start();
?>
	<html>
	<head>
	<link rel="stylesheet" type="text/css" href="main.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script type="text/javascript">
	function validateForm()
	{
	var regbkid = new RegExp("^(([0-9a-zA-Z]+-)*[0-9a-zA-Z]){1,10}$");
	var regcard = new RegExp("^[0-9]{2,10}$");
	var regborrower = new RegExp("^[a-zA-Z]{2,35}$");
	var isValid=new Boolean();
	
	var chkbk=regbkid.test(document.getElementById("bookid").value);	
	var chkcardno=regcard.test(document.getElementById("cardno").value);
	var chkborrowerfn=regborrower.test(document.getElementById("borrowerfn").value);
	var chkborrowerln=regborrower.test(document.getElementById("borrowerln").value);
	
	if(chkbk | chkcardno | chkborrowerfn | chkborrowerln)
	return true;
	else{
	document.getElementById("bookid").style.border = "1px solid #F70A26";
	document.getElementById("cardno").style.border = "1px solid #F70A26";
	document.getElementById("borrowerfn").style.border = "1px solid #F70A26";
	document.getElementById("borrowerln").style.border = "1px solid #F70A26";
	return false;
	}
	}

	function loadCheckout(){
	var count=0;
	var chkArr=[];
	var form = document.getElementById('action_chkin');
	var inputs = form.getElementsByTagName('input');
	var is_checked = false;
	for(var x = 0; x < inputs.length; x++) {
    if(inputs[x].type == 'checkbox') {
        is_checked = inputs[x].checked;
        if(is_checked){
		count++;
		if(count>3){
		alert('Please select atmost three books to checkin');
		return false;
				}
			}
		}
	}
	if(count>0){
	parent.scrollTo(0,0);
	return true;
	}
	alert('Please select atleast one book to checkin');
	return false;
	}
	</script>
	</head>
	<body>
	<form name="searchBook" method="post" onSubmit="return validateForm();">
	 <h2 align=left>Search a Book to Check-In by:</h2> 
	<table border=0>
	<tr><td valign=top><font size=4px>Book ID: <td><input type="text" id="bookid" name="bookid" maxlength=10 style="border: 1px solid #878b87" 
	placeholder="Please enter only character or digits (Min: 1 char)" value="<?php echo (isset($_POST['bookid']) && isset($_SESSION['bookid_exists'])) ? $_POST['bookid'] : '' ?>"/><br>  <b><i>OR<tr><tr><tr>

	<tr><td valign=top><font size=4px>Card No: <td><input type="text" id="cardno" name="cardno" maxlength=10 style="border: 1px solid #878b87" maxlength=10
	placeholder="Please enter only digits (Min: 2 digits)" value="<?php echo (isset($_POST['cardno']) && isset($_SESSION['bookid_exists'])) ? $_POST['cardno'] : '' ?>"/><br>  <b><i>OR<tr><tr><tr>

	<tr><td valign=top><font size=4px>First Name: <td><input type="text" name="borrowerfn" id="borrowerfn" maxlength=35 style="border: 1px solid #878b87"
	placeholder="Please enter only characters (Min: 2 chars)" value="<?php echo (isset($_POST['borrowerfn']) && isset($_SESSION['bookid_exists'])) ? $_POST['borrowerfn'] : '' ?>"><br>  <b><i>OR<tr><tr><tr>
	
	<tr><td valign=top><font size=4px>Last Name: <td><input type="text" name="borrowerln" id="borrowerln" maxlength=35 style="border: 1px solid #878b87"
	placeholder="Please enter only characters (Min: 2 chars)" value="<?php echo (isset($_POST['borrowerln']) && isset($_SESSION['bookid_exists'])) ? $_POST['borrowerln'] : '' ?>">  <tr><tr><tr>

	<tr><tr><tr><tr>
	<tr><td><td><input type="submit" id="submit" name="submit" value="Search Book">
	</table>
	</form>
	</body>
	</html>

	<?php   
	$_SESSION["bookid_exists"] = "1";
	
	if(isset($_POST['submit'])){
	$con=mysqli_connect("127.0.0.1","root","","library");
	if (mysqli_connect_errno())
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	
	$bookid = mysqli_real_escape_string($con,$_POST['bookid']); 
	$card_no = mysqli_real_escape_string($con,$_POST['cardno']); 
	$borrowerfn = mysqli_real_escape_string($con,$_POST['borrowerfn']);
	$borrowerln = mysqli_real_escape_string($con,$_POST['borrowerln']);

	$fetchq = "select concat(bor.fname,' ',bor.lname) as Name,bl.book_id,bl.card_no,bl.branch_id,bl.date_out,bl.due_date from book_loans bl
				inner join borrower bor on bl.card_no=bor.card_no where bl.book_id LIKE '%$bookid%' and 
				bl.card_no LIKE '%$card_no%' and bor.fname LIKE '%$borrowerfn%' and bor.lname LIKE '%$borrowerln%' and
				(bl.date_in='0000-00-00' or bl.date_in is null) order by bor.fname";
	$result = mysqli_query($con,$fetchq);
	
	$num_rows = mysqli_num_rows($result);
    if($num_rows<1){
	echo "<br><br><br><br><font color='red'><h2 align=center>No Such Pending Check In Record Exists!</h2></font>";
	}
	else{
		
	echo "<br><br><br><br><br><form name='action_chkin' id='action_chkin' onSubmit='return loadCheckout();' method='post'>
			<table align=center><tr><td class='nobg'><th>Borrower Name<th>Book Id<th>Card No<th>Branch Id<th>Date Out<th>Due Date</tr>";
	
	while($row = mysqli_fetch_array($result))
    {
      echo '<tr><td><input type=checkbox id=chkbooklist[] name=chkbooklist[] value="'.$row["Name"].'|'.$row["book_id"].'|'.
			$row["card_no"].'|'.$row["branch_id"].'|'.$row["date_out"].'|'.$row["due_date"].'">';
      echo "<td class='bg'>" . $row['Name'];
      echo "<td class='bg'>" . $row['book_id'];
      echo "<td class='bg'>" . $row['card_no'];
	  echo "<td class='bg'>" . $row['branch_id'];
	  echo "<td class='bg'>" . $row['date_out'];
	  echo "<td class='bg'>" . $row['due_date'];
	  echo "</tr>";
    }
	
	echo "<tr><td colspan=7 align='center'><input type=submit name='btncheckin' value='Check-in Book(s)'></tr></table></form>";

	}
	}

	if(isset($_POST['btncheckin'])){
	
	if(isset($_POST['chkbooklist'])){
	$con=mysqli_connect("127.0.0.1","root","","library");
	if (mysqli_connect_errno())
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	
	$booksChkin=$_POST['chkbooklist'];
	$bookChkCount= count($booksChkin);
	}
	
	$tdyDate = date("Y-m-d");
	$book=(array) null;
	for($i=0; $i < $bookChkCount; $i++)
    {
	  $book=explode("|",$booksChkin[$i]);
	  $sqlupd= "update book_loans SET date_in='$tdyDate' where card_no='$book[2]' and book_id='$book[1]' and branch_id='$book[3]'";	
      $query = mysqli_query($con,$sqlupd);
	}
	
	?>
	<br><br><br><br><table align=center>
	<tr><th>Book Id<th>Card No<th>Branch Id<th>Check-In Date<tr><tr><tr>
	<?php
	for($i=0; $i < $bookChkCount; $i++)
    { 
	  $book=explode("|",$booksChkin[$i]);	
	  echo "<tr><td class='bg'>" . $book[1];
      echo "<td class='bg'>" . $book[2];
      echo "<td class='bg'>" . $book[3];
	  echo "<td class='bg'>" . $tdyDate;
	  echo "</tr>";
    }
	echo "</table>";
	echo "<font color='green'><h2 align=center>Book(s) Checked In Successfully</h2></font>";
	}
	if(!(empty($con)))
	mysqli_close($con);
	
?>