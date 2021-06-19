<!DOCTYPE html>

<?php
    session_start();
    //connect to databse
	$conn = mysqli_connect('localhost', 'kuro7799', 'wenhao0627', 'noteboard');

	// check connection
    if (!$conn) {
        echo 'Connection error: ' . mysqli_connect_error();		// error if there is an error in connection
    }

	$user = $pass = '';
	$errors = array('username' => '', 'password' => '', 'incorrect' => '');


    // if form is submitted
    if(isset($_POST['submit'])){

        // check username
		if(empty($_POST['username'])){
			$errors['username'] = 'Username cannot be empty';
		}

        // check password
		if(empty($_POST['password'])){
			$errors['password'] = 'Password cannot be empty';
		}

        if(array_filter($errors)){
        }
        else{
            // safety
			$user = mysqli_real_escape_string($conn, $_POST['username']);
			$pass = mysqli_real_escape_string($conn, hash('sha512', $_POST['password']));

            // testing 
            //$user = $_POST['username'];
            //$pass = $_POST['password'];

            //sql query
            $sql = "SELECT * FROM loginform WHERE user = '".$user."' AND pass = '".$pass."' limit 1";

            // make query and get result
            $result = mysqli_query($conn, $sql);

            // if there are result
            if(mysqli_num_rows($result)==1){

                // sql query to get user id
                $sql = "SELECT ID FROM loginform WHERE user = '".$user."' AND pass = '".$pass."'";
                $result = mysqli_query($conn, $sql);

                // fetch the resulting id
                $ID = mysqli_fetch_assoc($result);
            
                // free result from memory
                mysqli_free_result($result);

                //save username to session
                $_SESSION['userID'] = $ID['ID'];

                // log
                $sql = "INSERT INTO userLog(user,login_status) VALUES('$user','S')";
                mysqli_query($conn, $sql);

                // close connection
                mysqli_close($conn);

                // redirect to next page
                header('Location: index.php');
                //exit();
            }
            else{
			    $errors['username'] = 'Wrong username or password';
                $sql = "INSERT INTO userLog(user,login_status) VALUES('$user','D')";
                mysqli_query($conn, $sql);

                // close connection
                mysqli_close($conn);

                //exit();
            }
        }
    }
?>

<html>
<head>
    <meta charset = "utf-8:">
	<title> Login Form</title>
	<link rel="stylesheet" a href="css\loginstyle.css">
	<link rel="stylesheet" a href="css\font-awesome.min.css">
</head>
<body>
	<div class="container">
	<img src="img/login.png"/>
		<form method ="POST">
			<div class="form-input">
				<input type="text" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($user) ?>"/>	
			</div>
			<div class="red-text">
                <?php echo $errors['username']; ?>
            </div>
			<div class="form-input">
				<input type="password" name="password" placeholder="Enter your password" value="<?php echo htmlspecialchars($pass) ?>"/>
			</div>
            <div class="red-text">
                <?php echo $errors['password']; ?>
            </div>
			<input type="submit" value="Login" name="submit" class="btn-login"/>
		</form>
	</div>
</body>
</html>