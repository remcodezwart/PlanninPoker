<?php
	require "logic/logic.php";
	if (LoginCHek($conn)) {
		require "templates/header.php";
		require "templates/delete_chambers_confirm.php";
		require "templates/footer.php";
		if (isset($_POST['delete'])) {
			deleteChamber($conn);
		}
	}else{
		header('Location: index.php');
	}





?>