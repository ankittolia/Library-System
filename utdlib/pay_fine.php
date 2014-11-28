
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
	
	var regcard = new RegExp("^[0-9]{2,10}$");
	var regborrower = new RegExp("^[a-zA-Z]{2,35}$");
	
	var isValid=new Boolean();
	
	var chkcardno=regcard.test(document.getElementById("cardno").value);
	var chkborrowerfn=regborrower.test(document.getElementById("borrowerfn").value);
	var chkborrowerln=regborrower.test(document.getElementById("borrowerln").value);

	
	if(chkcardno | chkborrowerfn | chkborrowerln)
	return true;
	else{
	document.getElementById("cardno").style.border = "1px solid #F70A26";
	document.getElementById("borrowerfn").style.border = "1px solid #F70A26";
	document.getElementById("borrowerln").style.border = "1px solid #F70A26";
	return false;
	}
	}

	function loadFine(){
	
   	var form = document.getElementById('search_fine_borrower');
	var inputs = form.getElementsByTagName('input');
	var is_checked = false;
	for(var x = 0; x < inputs.length; x++) {
    if(inputs[x].type == 'radio') {
        is_checked = inputs[x].checked;
        if(is_checked){
		parent.scrollTo(0,0);
		return true;
		}
	}
	}
	alert('Please select a borrower to make payment');
	return false;
	}
	
	</script>
	</head>
	<body>
	<form name="payfine" method="post" onSubmit="return validateForm();">
	 <h2 align=left>Search a Fine Record by:</h2> 
	<table border=0>
	
	<tr><td valign=top><font size=4px>Card No: <td><input type="text" id="cardno" name="cardno" style="border: 1px solid #878b87" maxlength=10
	placeholder="Please enter only digits (Min: 2 digits)"  value="<?php echo (isset($_POST['cardno']) && isset($_SESSION['borrower_exists'])) ? $_POST['cardno'] : '' ?>"/><br><b><i>OR<tr><tr><tr>
	
	<tr><td valign=top><font size=4px>First Name: <td><input type="text" name="borrowerfn" id="borrowerfn" maxlength=35 style="border: 1px solid #878b87"
	placeholder="Please enter only characters (Min: 2 chars)"  value="<?php echo (isset($_POST['borrowerfn']) && isset($_SESSION['bookid_exists'])) ? $_POST['borrowerfn'] : '' ?>"><br>  <b><i>OR<tr><tr><tr>
	
	<tr><td valign=top><font size=4px>Last Name: <td><input type="text" name="borrowerln" id="borrowerln" maxlength=35 style="border: 1px solid #878b87"
	placeholder="Please enter only characters (Min: 2 chars)"  value="<?php echo (isset($_POST['borrowerln']) && isset($_SESSION['bookid_exists'])) ? $_POST['borrowerln'] : '' ?>">  <tr><tr><tr>

	<tr><tr><tr><tr>
	<tr><td><td><input type="submit" id="submit" name="submit" value="Show Borrower Record">
	</table>
	</form>
	</body>
	</html>

	<?php   
	$_SESSION["borrower_exists"] = "1";
	if(isset($_POST['submit'])){
	$con=mysqli_connect("127.0.0.1","root","","library");
	// Check connection
	if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$card_no = mysqli_real_escape_string($con,$_POST['cardno']); 
	$borrowerfn = mysqli_real_escape_string($con,$_POST['borrowerfn']);
	$borrowerln = mysqli_real_escape_string($con,$_POST['borrowerln']);
	
	$_SESSION['card_no'] = $card_no;
	
	$fetchq = "SELECT bl.loan_id,bl.card_no,concat(bor.fname,' ',bor.lname) as name, bl.date_in, b.book_id, b.title,bl.due_date 
				from book_loans bl inner join borrower bor on 
				bl.card_no=bor.card_no inner join book b on bl.book_id=b.book_id inner join fines f on bl.loan_id=f.loan_id
				where bl.card_no LIKE '%$card_no%' and bor.fname LIKE '%$borrowerfn%' AND bor.lname LIKE '%$borrowerln%' and 
				(bl.date_in='0000-00-00' or date_in is null or date_in>due_date) and paid =0";

	//echo $fetchq;
	$result = mysqli_query($con,$fetchq);
	$_SESSION["result"]=$result;
	$num_rows = mysqli_num_rows($result);
	if($num_rows<1)
		echo "<br><br><br><center><font color='red'><h2>No such Late Fine Record Exists!</h2>
				Please update the fine data to reflect the most recent changes.</font></center>";
	else
	{
	echo "<br><br><br><form name='search_fine_borrower' id='search_fine_borrower' action='fine_confirm.php' onSubmit='return loadFine();'
			method='post'><table align=center><tr><td class='nobg'><th>Card No<th>Borrower Name<th>Payment Pending</tr>";
	$alreadychkd="";
	$i=0;
	while($row = mysqli_fetch_array($result))
    {
	$chkcard = $row['card_no'];
	if($alreadychkd!=$chkcard){
		echo '<tr><td class="nobg"><input type=radio id=chkfine name=chkfine value="'.$row["card_no"].'|'.$row["name"].'">';
		echo "<td class='bg'>" . $row['card_no'];
		echo "<td class='bg'>" . $row['name'];	
		echo "<td class='bg'>Yes";	
	}
	$alreadychkd = $chkcard;
	}
	
	echo "<tr><tr><tr><td colspan=4 align='center'><input type=submit name='btnFineConfirm' value='Proceed to Payment' >
		</tr></table></form>";
	

	}
}

	?>
