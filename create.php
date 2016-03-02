<?php
	require "logic/logic.php";

	require "templates/header.php";
	
	if (isset($_POST['user']) && $_POST['user'] != null){
		if (isset($_POST['password']) && $_POST['password'] != null) {
			$user = $_POST['user'] ;
			$password = $_POST['password'];
			$error = CreateUser($conn,$user,$password);
				if (isset($error)) {
					echo $error;
				}
			}
	}
	
	require "templates/create_user.php";
	require "templates/footer.php";
?>