<html>
<?php   
	session_start();
?>
	
	<head>
	
 <meta http-Equiv="Cache-Control" Content="no-cache"/>
    <meta http-Equiv="Pragma" Content="no-cache"/>
    <meta http-Equiv="Expires" Content="0"/>
	
	<link rel="stylesheet" type="text/css" href="main.css">
	<script type="text/javascript">
	function validateForm()
	{

	var regname = new RegExp("^[a-zA-Z]{2,25}$");
	var regaddress = new RegExp("^[a-zA-Z0-9 ,/:.'#-]{5,75}$");
	var regcity = new RegExp("^[a-zA-Z ]{4,30}$");
	var regstate = new RegExp("^[a-zA-Z ]{2,30}$");
	var regzip = new RegExp("^(([0-9]+-)*[0-9]){4,10}$");
	var regcountry = new RegExp("^[a-zA-Z ]{2,30}$");
	var regphone = new RegExp("^(([0-9]+-)*[0-9]){5,21}$");
	var isValid=new Boolean();

	if(!(regname.test(document.getElementById("fname").value))) {
	document.getElementById("fname").style.border = "1px solid #F70A26";
	isValid=false;
	}
	else
	 document.getElementById("fname").style.border="1px solid #00FF00";

	if(!(regname.test(document.getElementById("lname").value))) {
	  document.getElementById("lname").style.border="1px solid #F70A26";
	  isValid=false;
	}
	else
	 document.getElementById("lname").style.border="1px solid #00FF00";
	 
	if(!(regaddress.test(document.getElementById("address").value))) {
	  document.getElementById("address").style.border="1px solid #F70A26";
	  isValid=false;
	}
	else
	 document.getElementById("address").style.border="1px solid #00FF00";


	if(!(regcity.test(document.getElementById("city").value))) {
	document.getElementById("city").style.border = "1px solid #F70A26";
	isValid=false;
	}
	else
	 document.getElementById("city").style.border="1px solid #00FF00";

	if(!(regstate.test(document.getElementById("state").value))) {
	document.getElementById("state").style.border = "1px solid #F70A26";
	isValid=false;
	}
	else
	 document.getElementById("state").style.border="1px solid #00FF00";

	if(!(regzip.test(document.getElementById("zip").value))) {
	document.getElementById("zip").style.border = "1px solid #F70A26";
	isValid=false;
	}
	else
	 document.getElementById("zip").style.border="1px solid #00FF00";

	if(!(regcountry.test(document.getElementById("country").value))) {
	document.getElementById("country").style.border = "1px solid #F70A26";
	isValid=false;
	}
	else
	 document.getElementById("country").style.border="1px solid #00FF00";
	
	if((document.getElementById("phone").value)!=""){
	if(!(regphone.test(document.getElementById("phone").value))) {
	 document.getElementById("phone").style.border="1px solid #F70A26";
	 isValid=false;
	}
	else
	document.getElementById("phone").style.border="1px solid #00FF00";
	}


	if(!isValid)
	return false;
	else
	return true;
	}
	</script>
	</head>
	<body>
	<form name="addborrower" method="post" onSubmit="return validateForm();">
	 <h2>Add a Borrower</h2> 
	<table border=0>
	<tr><td><font size=4px>First Name <font color=red>*</font> <td><input type="text" id="fname" name="fname" maxlength="25" 
	placeholder="Please enter only characters (Min: 2 chars)"	style="border: 1px solid #878b87" value="<?php echo (isset($_POST['fname']) && isset($_SESSION['borrower_exists'])) ? $_POST['fname'] : '' ?>"/><tr><tr><tr>

	<tr><td><font size=4px>Last Name <font color=red>*</font> <td><input type="text" id="lname" name="lname" maxlength="25" 
	placeholder="Please enter only characters (Min: 2 chars)" style="border: 1px solid #878b87" value="<?php echo (isset($_POST['lname']) && isset($_SESSION['borrower_exists'])) ? $_POST['lname'] : '' ?>"/><tr><tr><tr>

	<tr><td><font size=4px>Street Address <font color=red>*</font> <td><input type="text" name="address" id="address" 
	placeholder="Please enter only alphanumerics(Special Chars Allowed: ,/:.'# -)" maxlength="75" style="border: 1px solid #878b87"
	value="<?php echo (isset($_POST['address']) && isset($_SESSION['borrower_exists'])) ? $_POST['address'] : '' ?>"><tr><tr><tr>

	<tr><td><font size=4px>City <font color=red>*</font> <td><input type="text" name="city" id="city" maxlength="30" style="border: 1px solid #878b87"
	placeholder="Please enter only characters" value="<?php echo (isset($_POST['city']) && isset($_SESSION['borrower_exists'])) ? $_POST['city'] : '' ?>"><tr><tr><tr>

	<tr><td><font size=4px>State <font color=red>*</font> <td><input type="text" name="state" id="state" maxlength="30" style="border: 1px solid #878b87"
	placeholder="Please enter only characters" value="<?php echo (isset($_POST['state']) && isset($_SESSION['borrower_exists'])) ? $_POST['state'] : '' ?>"><tr><tr><tr>

    <tr><td><font size=4px>Zip Code <font color=red>*</font> <td><input type="text" name="zip" id="zip" maxlength="10" style="border: 1px solid #878b87"
	placeholder="Please enter only digits" value="<?php echo (isset($_POST['zip']) && isset($_SESSION['borrower_exists'])) ? $_POST['zip'] : '' ?>"><tr><tr><tr>

	<tr><td><font size=4px>Country <font color=red>*</font> <td><input type="text" name="country" id="country" maxlength="30" style="border: 1px solid #878b87"
	placeholder="Please enter only characters" value="<?php echo (isset($_POST['country']) && isset($_SESSION['borrower_exists'])) ? $_POST['country'] : '' ?>"><tr><tr><tr>

	<tr><td><font size=4px>Phone No <td><input type="text" name="phone" id="phone" maxlength="21" style="border: 1px solid #878b87"
	placeholder="Please enter only digits" value="<?php echo (isset($_POST['phone']) && isset($_SESSION['borrower_exists'])) ? $_POST['phone'] : '' ?>"/><tr><tr><tr>
	<tr><tr><tr><tr>
	<tr><td><td><input type="submit" id="submit" name="submit" value="Add Borrower">
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
	$fname = mysqli_real_escape_string($con,$_POST['fname']); 
	$lname = mysqli_real_escape_string($con,$_POST['lname']); 
	$address = mysqli_real_escape_string($con,$_POST['address']).", ".mysqli_real_escape_string($con,$_POST['city']).", ".
	mysqli_real_escape_string($con,$_POST['state'])." ".mysqli_real_escape_string($con,$_POST['zip']).", ".mysqli_real_escape_string($con,$_POST['country']);
	$phone = mysqli_real_escape_string($con,$_POST['phone']);
	$query = mysqli_query($con,"SELECT * FROM borrower WHERE fname='$fname' and lname='$lname' and address='$address'");
	$num_rows = mysqli_num_rows($query);
    if($num_rows>0){
	echo "<font color='red' ><h2 align=center>Borrower Data Already Exists!</h2></font>";
	$_SESSION["borrower_exists"] = "1";
	}
	else{
	session_unset(); 
	$digits = 4;
	$card_no = rand(pow(10, $digits-1), pow(10, $digits)-1);
	$sql="INSERT INTO borrower (card_no,fname,lname,address,phone)
	VALUES ('$card_no','$fname','$lname','$address','$phone')";
	
	if (!mysqli_query($con,$sql)) 
	echo "<font color='red' ><h2 align=center>Error Adding Borrower into Database!</h2></font>";
	else
	echo "<font color='green' ><h2 align=center>Borrower Added Successfully!</h2><h3 align=center>Card No: ".$card_no."</h3></font>";
	mysqli_close($con);
	}
	}
	?>
