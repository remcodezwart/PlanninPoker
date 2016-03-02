<?php
		require "logic/logic.php";
	if (LoginCHek($conn)) {
		require "templates/header.php";
		require "templates/delete_Confirm.php";
		require "templates/footer.php";
		if (isset($_POST['delete'])) {
			delete($conn);
		}
	}else{
		header('Location: index.php');
	}


?>