<html>
<body>
<pre>
<?php
session_start();
require_once('../mysql_connect.php');
$_SESSION['useremail'] = $_SESSION['user'];
//echo $_SESSION['useremail'];

if (isset($_POST['creation'])){
	$message=NULL;

	if (empty($_POST['userid'])){
		$_SESSION['userid']=NULL;
		$message.='<p>You forgot to input the ID Number!</p>';
	} else {
		$_SESSION['userid'] = $_POST['userid'];
	}

	if (empty($_POST['name'])){
		$_SESSION['name']=NULL;
		$message.='<p>You forgot to enter the Name!';
	} else {
		$_SESSION['name']=$_POST['name']; 
	}

	if (empty($_POST['email'])){
		$_SESSION['email']=NULL;
		$message.='<p>You forgot to enter the Email!';
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
		$_SESSION['email']=$_POST['email']; 
	} else {
		$message.='<p> Email is invalid! </p>';
		$_SESSION['email']=NULL;
	}

	$_SESSION['password']= generatePassword();
	//echo $_SESSION['password'];

	if (!isset($message)){
		$adminquery = "select employeesid, username from employees";
		$adminresult = mysqli_query($dbc,$adminquery);
		$num_rows=$adminresult->num_rows;

		if (!empty($num_rows)){
			while ($row=mysqli_fetch_array($adminresult,MYSQL_ASSOC)){
				if ($_SESSION['userid'] == "{$row['employeesid']}" || $_SESSION['email'] == "{$row['username']}"){
					$message.="<p>Account has already been registered!</p>";
				} 
			}
			if (!isset($message)){
				echo '<fieldset> <legend> Admin Verification </legend>
				<form method="post"> Please Enter Your Password for Verification: <p>';
				echo '        <input type="password" name="adminpassword" > <br>
				<input type="submit" name="verification" value="Verify">
				</form></fieldset>';
			}
		}	
	} 

}
if (isset($message)){
	echo '<font color="brown">'.$message. '</font>';
}

?>

<?php
if (isset($_POST['verification'])){
		$message = NULL;

		if (empty($_POST['adminpassword'])){
			$_SESSION['adminpassword']=FALSE;
			$message.='<p>You forgot to enter your password!';
		} else {
			$_SESSION['adminpassword']=$_POST['adminpassword']; 
		}
		

		$admincheck = "select username, password from employees";
		$checkresult = mysqli_query($dbc,$admincheck);
		$num_rows=$checkresult->num_rows;

		if (!empty($num_rows)){
			while ($row=mysqli_fetch_array($checkresult,MYSQL_ASSOC)){
				//type the password in an account you have logged in
				if ($_SESSION['useremail'] == "{$row['username']}" && $_SESSION['adminpassword'] == "{$row['password']}"){
					$registerquery = "insert into employees(employeesid,username,name,password,deptid) VALUES
					('{$_SESSION['userid']}','{$_SESSION['email']}','{$_SESSION['name']}','{$_SESSION['password']}','1')";
					$registerresult=mysqli_query($dbc,$registerquery);
					$message.="<p>Account has been successfully created!</p>";
				}
			}
			if (!isset($message)){
				$message.="<p>Password is incorrect!";
			}		
		}
		if (isset($message)){
			echo '<font color="brown">'.$message. '</font>';
		}
}
?>

<fieldset> <legend> Create Account for Checker </legend>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<align="right">  ID Number:</align> <input type="text" name="userid" value="<?php if (isset($_POST['userid'])) echo $_POST['userid']; ?>" size="20"> <br>
<align="right">   Fullname:</align> <input type="text" name="name" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>" size="20"> <br>
<align="right">      Email:</align> <input type="text" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" size="20"> <br>
<align="right">                  <input type="submit" name="creation" value="Create"> </align>


</form>
</fieldset>

<?php
function generatePassword() {
    $combination = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); 
    $alphaLength = strlen($combination) - 1; 
    for ($i = 0; $i < 8; $i++) {
        $j = rand(0, $alphaLength);
        $pass[] = $combination[$j];
    }
    return implode($pass); //password generated
}
?>
</pre>
</body>
</html>