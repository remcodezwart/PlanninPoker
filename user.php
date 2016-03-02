<?php
	require "logic/logic.php";

	if (LoginCHek($conn)) {
		$Chambers = GetChambers($conn);
		require "templates/header.php";
		require "templates/userLogin.php";
		require "templates/footer.php";
	}else{
		header('Location: index.php');
	}



?>