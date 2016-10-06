<html>
<body>
<pre>
<?php
session_start();

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
	echo $_SESSION['password'];

	if (!isset($message)){
		echo '<fieldset> <legend> Admin Verification </legend>
		<form method="post"> Please Enter Your Password for Verification: <p>';
		echo '        <input type="password" name="adminpassword" > <br>
		<input type="submit" name="verification" value="Verify">
		</form></fieldset>';

		
		
	} 

}


if (isset($message)){
	echo '<font color="brown">'.$message. '</font>';
}

// Password should be generated here at random. 

// Email and ID number should never be repeated..

// When Button is clicked, if all inputs
// are correct, the system is to Ask for admin password to verify it is the admin, 
// if yes, the ID number and password generated will be emailed to the user registered
// and the link for log in will be provided to them. If it is not the admin, the 
// creation of the account will fail.

// Email needed to be dlsu email or any email of the checker, whatever he/she has
// given to the admin?
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
		require_once('../mysql_connect.php');

		// MySQL should be modified that under employees table there is a
		// status where it should be known if admin or checker
		// temporary query
		/* ID NUMBER of Admin is currently hardcoded, in the future,
		who ever is logged on, ID number should be retained and compared
		here. */
		$adminquery = "select employeesid, username, password from employees";
		$adminresult = mysqli_query($dbc,$adminquery);
		$num_rows=$adminresult->num_rows;

		if (!empty($num_rows)){
			$check = 0;
			while ($row=mysqli_fetch_array($adminresult,MYSQL_ASSOC)){
				//correct password here would be 'mela123'
				if ("{$row['employeesid']}" == '11442972'  && $_SESSION['adminpassword'] == "{$row['password']}"){
					$accountresult = mysqli_query($dbc,$adminquery);
					$check = 2;

					while ($row=mysqli_fetch_array($accountresult,MYSQL_ASSOC)){
						if ($_SESSION['userid'] == "{$row['employeesid']}" || $_SESSION['email'] == "{$row['username']}"){
							$message.="<p>Account has already been registered!</p>";
						} 
					}
					
				} else if ($check != 2){
					$check = 1;
				}
			}

			if ($check == 1){
				$message.="<p> Incorrect Password! </p>";
			}

			if (!isset($message)){
				$registerquery = "insert into employees(employeesid,username,name,password,deptid) VALUES
				('{$_SESSION['userid']}','{$_SESSION['email']}','{$_SESSION['name']}','{$_SESSION['password']}','1')";
				$registerresult=mysqli_query($dbc,$registerquery);
				$message.="<p>Account has been successfully created!</p>";
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