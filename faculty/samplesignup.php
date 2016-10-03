<html>
<body>
<?php 
session_start();

if (isset($_POST['signup'])){
    $message=NULL;

	if (empty($_POST['facultyid'])){
		$_SESSION['facultyid']=FALSE;
		$message.='<p>You forgot to enter your ID Number!';
	} else {
		$_SESSION['facultyid']=$_POST['facultyid']; 
	}

	if (empty($_POST['name'])){
		$_SESSION['name']=FALSE;
		$message.='<p>You forgot to enter your Name!';
	} else {
		$_SESSION['name']=$_POST['name']; 
	}

	if (empty($_POST['email'])){
		$_SESSION['email']=FALSE;
		$message.='<p>You forgot to enter your Email!';
	} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
		$_SESSION['email']=$_POST['email']; 
	} else {
		$message.='<p> Email is invalid! </p>';
		$_SESSION['email']=FALSE;
	}

	if (empty($_POST['gender'])){
		$_SESSION['gender']=FALSE;
		$message.='<p>You forgot to select Gender';
	} else {
		$_SESSION['gender']=$_POST['gender']; 
	}

	if (empty($_POST['birth'])){
		$_SESSION['birth']=FALSE;
		$message.='<p>You forgot to select your birth date';
	} else {
		$_SESSION['birth']=$_POST['birth']; 
	}


	if (empty($_POST['initialp'])){
		$_SESSION['initialp']=FALSE;
		$message.='<p>You forgot to enter your Password!';
	} elseif (strlen($_POST["initialp"]) < 8){
		echo strlen($_POST["initialp"]);
		$message.='<p>Your password must contain at least 8 Characters!</p>';
	} 
	if(!preg_match("#[0-9]+#",$_POST["initialp"])) {
        $message.= "<p>Your password must contain at least 1 Number!</p>";
    } 
    if(!preg_match("#[A-Z]+#",$_POST["initialp"])) {
        $message.= "<p>Your password must contain at least 1 Capital Letter!</p>";
    } else {
		$_SESSION['initialp']=$_POST['initialp']; 
	}

	if (empty($_POST['finalp'])){
		$_SESSION['initialp']=FALSE;
		$message.='<p>You did not re-type your password!';
	} elseif ($_POST['finalp'] != $_POST['initialp']){
		$message.='<p> Passwords do not match!';
	}else {
		$_SESSION['finalp']=$_POST['finalp']; 
	}

	// Hard Coded
	if (empty($_POST['security'])){
		$_SESSION['security']=FALSE;
		$message.='<p>You did not type any security code!';
	} elseif ($_POST['security'] != 1234){
		$message.='<p> Security code is invalid!';
		// 3 Tries before locked?
	}else {
		$_SESSION['security']=$_POST['security']; 
	}	
	//Security Code Check I'm not sure how to code it, Should probably be checked in database 

	if (!isset($message)){
		//Username = Email & Employeesid = ID number
		require_once('../mysql_connect.php');

		$facultyquery="select employeesid, username from employees";
		$facultyresult=mysqli_query($dbc,$facultyquery);
		$num_rows=$facultyresult->num_rows;
		//Checks if the id number and the email has been registered
		if (!empty($num_rows)){
			while ($row=mysqli_fetch_array($facultyresult,MYSQL_ASSOC)){
				if ($_SESSION['facultyid'] == "{$row['employeesid']}" || $_SESSION['email'] == "{$row['username']}"){
					$message.="<p>Account has already been registered!</p>";
				}

			}
			if (!isset($message)){
				$query="insert into employees(employeesid,username,name,password,deptid,birthday) VALUES 
				('{$_SESSION['facultyid']}','{$_SESSION['email']}','{$_SESSION['name']}','{$_SESSION['finalp']}','1','{$_SESSION['birth']}')";
				$result=mysqli_query($dbc,$query);
				$message="<p> You have successfully signed-up!</p> Proceed to <a href='samplelogin.php'> Log-in </a> ";
				}

		} else {
			$query="insert into employees(employeesid,username,name,password,deptid,birthday) VALUES 
			('{$_SESSION['facultyid']}','{$_SESSION['email']}','{$_SESSION['name']}','{$_SESSION['finalp']}','1','{$_SESSION['birth']}')";
			$result=mysqli_query($dbc,$query);
			$message="<p> You have successfully signed-up!</p> Proceed to <a href='samplelogin.php'> Log-in </a> "; 
		}

		
	}
	
}


if (isset($message)){
 echo '<font color="green">'.$message. '</font>';
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<fieldset> <legend> Faculty Sign-up </legend>
<p>
ID Number: <input type"number" name="facultyid"> 
Fullname: <input type="text" name="name"> <br>
Email: <input type="text" name="email"> <br>
Gender: <select name="gender">
<option value="">--Select--</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
</select> 
Birthdate: <input type="date" name="birth"> <br>
Password: <input type="password" name="initialp">
Re-type Password: <input type="password" name="finalp"><br>
<font size=1>Password must be more than 8 characters, has at least
one number and one capital letter </font> <br><br>

Security Code: <input type"number" name="security"> <br>
<font size=1>Please type 4 digit code sent in the email</font>
</p>
<input type="submit" name="signup" value="Sign-up">
</fieldset>
</form>

</body>
</html>