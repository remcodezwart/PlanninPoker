<?php
	require "logic/logic.php";

	if (LoginCHek($conn)) {
		$Chambers = GetChambers($conn);
		require "templates/header.php";
		if (isset($_POST['ChamberName'])) {
			createChamber($conn);
		}
		require "templates/create_Chamber.php";
		require "templates/footer.php";
	}else{
		header('Location: index.php');
	}

?>