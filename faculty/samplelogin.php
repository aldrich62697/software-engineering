<html>
<body>
<?php
session_start();


if (isset($_POST['login'])){
	$message=NULL;

	if (empty($_POST['email'])){
		$_SESSION['email']=NULL;
		$message.='<p>You forgot to enter your email! </p>';
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
		$_SESSION['email']=$_POST['email']; 
	} else {
		$message.='<p> Email is invalid! </p>';
		$_SESSION['email']=FALSE;
	}

	if (empty($_POST['password'])){
		$_SESSION['password']=NULL;
		$message.='<p>You forgot to enter your password! </p>';
	} else {
		$_SESSION['password']=$_POST['password'];
	}

	// query for calling faculty table to check if email 
	// and password matches
	require_once('../mysql_connect.php');
	$loginquery="select username, password from employees";
	$result=mysqli_query($dbc,$loginquery);
	$num_rows=$result->num_rows;

	if (!empty($num_rows)){

		while ($row=mysqli_fetch_array($result,MYSQL_ASSOC)){
			if ($_SESSION['email'] == "{$row['username']}" && $_SESSION['password'] == "{$row['password']}") 
				header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/attendancetracker/pages/index.php");
			
		}
		$message.='Email and password do not match, please try again';
		
	}
		
	

}
if (isset($message)){
	echo '<font color="green">'.$message. '</font>';
}
?>

<fieldset> <legend> Faculty Login </legend>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p>
Email: <input type="text" name="email"> <br>
Password: <input type="password" name="password"> 
</p>
<input type="submit" name="login" value="Log-in">
</form>	
</fieldset>
</body>
</html>