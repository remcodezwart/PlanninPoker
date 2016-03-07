<?php
	require "logic/logic.php";

	require "templates/header.php";
	if (isset($_POST['user']) && $_POST['user'] != null){
			if (isset($_POST['password']) && $_POST['password'] != null) {
			$user = $_POST['user'] ;
			$password = $_POST['password'];
			if (login($conn,$user,$password)) {
				$login = "";
				setSession($user,$conn,$login);
			}else{
				echo "<p>geen geldige combinatie van gebruikersnaam en/of wachtwoord</p>";
			}
		}
	}

	require "templates/index.php";
	require "templates/footer.php";
?>