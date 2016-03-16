<?php	
		require "logic/logic.php";
	if (LoginCHek($conn)){
		if (isset($_POST['subject'])) {
			UpdateChamber($pdo);
		}
		$chamber = UserChamber($conn,$pdo);
		require "templates/header.php";
		require "templates/chamber.php";
		require "templates/footer.php";
	}
	else{
		header('Location: index.php');
	}

?>