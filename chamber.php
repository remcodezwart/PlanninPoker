<?php	
		require "logic/logic.php";
	if (LoginCHek($conn)){
		$chamber = UserChamber($conn);
		require "templates/header.php";
		require "templates/chamber.php";
		require "templates/footer.php";
	}else{
		header('Location: index.php');
	}


?>