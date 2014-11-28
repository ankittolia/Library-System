
<?php   
	session_start();
?>
	<html>
	<head>
	
 <meta http-Equiv="Cache-Control" Content="no-cache"/>
    <meta http-Equiv="Pragma" Content="no-cache"/>
    <meta http-Equiv="Expires" Content="0"/>


	<link rel="stylesheet" type="text/css" href="main.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script type="text/javascript">
	function validateForm()
	{
	var regbkid = new RegExp("^(([0-9a-zA-Z]+-)*[0-9a-zA-Z]){1,10}$");
	var regtitle = new RegExp("^[a-zA-Z0-9 ,:-]{2,65}$");
	var regauthor = new RegExp("^[a-zA-Z .]{2,35}$");
	var isValid=new Boolean();
	
	var chkbk=regbkid.test(document.getElementById("bookid").value);	
	var chktitle=regtitle.test(document.getElementById("title").value);
	var chkauthor=regauthor.test(document.getElementById("author").value);
	
	if(chkbk | chktitle | chkauthor)
	return true;
	else{
	document.getElementById("bookid").style.border = "1px solid #F70A26";
	document.getElementById("title").style.border = "1px solid #F70A26";
	document.getElementById("author").style.border = "1px solid #F70A26";
	return false;
	}
	}

	function loadCheckout(){
	var count=0;
	var chkArr=[];
	var form = document.getElementById('search_chkout');
	var inputs = form.getElementsByTagName('input');
	var is_checked = false;
	for(var x = 0; x < inputs.length; x++) {
    if(inputs[x].type == 'checkbox') {
        is_checked = inputs[x].checked;
        if(is_checked){
		count++;
		if(count>3){
		alert('Please select atmost three books to checkout');
		return false;
	  }
		chkArr[x]=inputs[x].value;
		//alert(chkArr[x]);
		var res=chkArr[x].split("|");
		if(res[5]==0){
		alert(res[1]+"(by "+res[2]+")"+"\n"+"at Branch Id: "+res[3]+" is not available");
		return false;
		}
	}	
	}
	}
	if(count>0){
	parent.scrollTo(0,0);
	return true;
	}
	alert('Please select atleast one book to checkout');
	return false;
	}
	</script>
	</head>
	<body>
	<form name="searchBook" method="post" onSubmit="return validateForm();">
	 <h2 align=left>Search a Book by:</h2> 
	<table border=0 cellspacing="3">
	<tr><td valign=top><font size=4px>Book ID: <td><input type="text" id="bookid" name="bookid" maxlength=10 style="border: 1px solid #878b87" 
	placeholder="Please enter only character or digits (Min: 1 char)" value="<?php echo (isset($_POST['bookid']) && isset($_SESSION['bookid_exists'])) ? $_POST['bookid'] : '' ?>"/><br> <b><i>OR<tr><tr><tr>

	<tr><td valign=top><font size=4px>Title: <td><input type="text" id="title" name="title" style="border: 1px solid #878b87"
	placeholder="Please enter only character or digits (Special Chars Allowed: ,:-)" value="<?php echo (isset($_POST['title']) && isset($_SESSION['bookid_exists'])) ? $_POST['title'] : '' ?>"/> <br> <b><i>OR<tr><tr><tr>

	<tr><td valign=top><font size=4px>Author: <td><input type="text" name="author" id="author" maxlength=35 style="border: 1px solid #878b87"
	placeholder="Please enter only characters (Min: 2 chars)" value="<?php echo (isset($_POST['author']) && isset($_SESSION['bookid_exists'])) ? $_POST['author'] : '' ?>"> <tr><tr><tr>

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
	// Check connection
	if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$bookid = mysqli_real_escape_string($con,$_POST['bookid']); 
	$title = mysqli_real_escape_string($con,$_POST['title']); 
	$author = mysqli_real_escape_string($con,$_POST['author']);
	
	$fetchq = " SELECT books.book_id, books.title, books.author_name, books.branch_id, books.no_of_copies as No_Of_Copies, 
				IFNULL(books.no_of_copies - bl.checkin , books.no_of_copies) as No_Of_Available_Copies 
				FROM (SELECT b.title, b.book_id, GROUP_CONCAT(ba.author_name) as author_name, bc.branch_id, bc.no_of_copies 
				FROM book b inner join book_authors ba on b.book_id = ba.book_id inner join book_copies bc 
				ON bc.book_id = b.book_id GROUP BY b.book_id , bc.branch_id) AS books LEFT JOIN 
				(SELECT book_id, branch_id, COUNT(*) as checkin FROM book_loans 
				WHERE (book_loans.date_in IS NULL OR book_loans.date_in='0000-00-00') 
				GROUP BY book_loans.book_id , book_loans.branch_id) as bl ON (bl.book_id = books.book_id AND bl.branch_id = books.branch_id) 
				WHERE books.book_id	LIKE '%$bookid%' AND books.title LIKE '%$title%' AND books.author_name LIKE '%$author%'";

	$result = mysqli_query($con,$fetchq);
	
	$num_rows = mysqli_num_rows($result);
    if($num_rows<1){
	echo "<font color='red' ><h2 align=center>No Such Book Found!</h2></font>";
	}
	else{
	//session_unset();
	
	echo "<form name='search_chkout' id='search_chkout' action='chkout.php' onSubmit='return loadCheckout();' method='post'><br><br>
			<table><tr><td class='nobg'><th>Book Id<th>Title<th>Author<th>Branch Id<th>No. of copies<th>No. of available copies</tr>";
	
	while($row = mysqli_fetch_array($result))
    {
      echo '<tr><td><input type=checkbox id=booklist[] name=booklist[] value="'.$row["book_id"].'|'.$row["title"].'|'.$row["author_name"].'|'.$row["branch_id"].
			'|'.$row["No_Of_Copies"].'|'.$row["No_Of_Available_Copies"].'">';
      echo "<td class='bg'>" . $row['book_id'];
      echo "<td class='bg'>" . $row['title'];
      echo "<td class='bg'>" . $row['author_name'];
	  echo "<td class='bg'>" . $row['branch_id'];
	  echo "<td class='bg'>" . $row['No_Of_Copies'];
	  echo "<td class='bg'>" . $row['No_Of_Available_Copies'];
      echo "</tr>";
    }
	echo "<input type=hidden name='searchchkout' id='searchchkout' value='no'>";
	echo "<tr><td colspan=7 align='center'><input type=submit name='btncheckout' value='Proceed to Checkout' ></tr></table></form>";
	
	mysqli_close($con);
	}
	}
	?>
