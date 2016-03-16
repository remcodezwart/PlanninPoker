<?php
	require "logic/logic.php";
	if (isset($_POST['answer'])) {
		Answer($pdo);
	}
?>