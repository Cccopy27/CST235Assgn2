<?php
	session_start();
	// connect to database
	$conn = mysqli_connect('localhost', 'kuro7799', 'wenhao0627', 'noteboard');//(hostname, username, password, database name)
	
	// check connection
	if (!$conn) {
	 	echo 'Connection error: ' . mysqli_connect_error();		// error if there is an error in connection
    }

	// get value from session
	$userID = $_SESSION['userID'];
    
    // initilize values
    $title = $note = '';							// initialize to empty string
    $error = array('title' => '', 'note' => '');    // initailize error message (if any)

    // if form is submitted
    if(isset($_POST['submit'])){
		
		// make sure the length of title is less than 255 characters, error if more than 255 characters
		$title = $_POST['title'];
		if(strlen($_POST['title']) > 255){			
			$error['title'] = 'Title must be less than 255 characters';
		}

		// make sure the length of note is less than 255 characters, error if more than 255 characters
		$note = $_POST['note'];
		if(strlen($_POST['note']) > 255){
			$error['note'] = 'Note must be less than 255 characters';
        }
        
        // if there is no error
		if(!array_filter($error)){

			// escape sql characters
			$title = mysqli_real_escape_string($conn, $_POST['title']);
			$note = mysqli_real_escape_string($conn, $_POST['note']);

			// sql command
			$sql = "INSERT INTO board(title,note,userid) VALUES('$title','$note','$userID')";

			// check for any errors
			if(mysqli_query($conn, $sql)){
                header("Refresh:0");            			// reload page
			} else {
				echo 'Query Error: '. mysqli_error($conn);	// error 
			}
			
		}

    } 
    
    // Show all notes
    // write query for all notes
	$sql = "SELECT title, note, id FROM board WHERE userid = '$userID' ORDER BY id DESC ";    // show notes from new to old

	// make query and get result
	$result = mysqli_query($conn, $sql);	            // (reference to get the data, the command)

	// fetch the resulting rows
	$notes = mysqli_fetch_all($result, MYSQLI_ASSOC);	// (result, return as associative array)
    
	// free result from memory
	mysqli_free_result($result);			

    // Delete notes
    if(isset($_POST['delete'])) {       // If delete is pressed

        // escape sql characters
		$delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);

        // sql command
		$sql = "DELETE FROM board WHERE id = $delete_id";

        // save to database and check for any errors
		if(mysqli_query($conn, $sql)) {
            header("Refresh:0");
		} else {
			echo 'Query Error: '. mysqli_error($conn);
		}

	}

	// close connection
    mysqli_close($conn);
    
?>


<html>

	<?php include("templates/header.php"); ?>

	<form class="logout" action="/logout.php" method="POST">
		<input type="submit" value="Logout">	
	</form>

	

    <!-- form to input notes -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

		<div class="write">

			<input type="text" name="title" value="<?php echo htmlspecialchars($title) ?>" required placeholder="Title">
			<div><?php echo $error['title']; ?></div>
	
		
			<textarea name="note" cols="40" rows="10" placeholder="Please enter your note" value="<?php echo htmlspecialchars($note) ?>" required></textarea>
			<div><?php echo $error['note']; ?></div>
		
		
			<button name="submit" value="Submit" class="save_note">Save notes</button>
			</div>

		
    </form>

    <div class="container">

        <!-- show all notes -->
		<?php foreach($notes as $note): ?>

			<div class="grid_item">
					
					<h3><?php echo htmlspecialchars($note['title']); ?><br></br></h3>
				
				
					<p><?php echo htmlspecialchars($note['note']); ?><br></br></p>
				
			
            <!-- delete note -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($note['id']); ?>">
				<button name="delete" value="Delete" class="delete_note">Delete </button>

			</form>
			</div>

		<?php endforeach; ?>

    </div>

    <?php include("templates/footer.php"); ?>
</html>
</body>
</html>
