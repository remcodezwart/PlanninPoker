<?php
	require "config/config.php";
	function CreateUser($conn,$user,$password){
		$stmt = $conn->prepare("SELECT * FROM users WHERE name=?"); 
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$result = $stmt->get_result();
		$Login = $result->fetch_all(MYSQLI_ASSOC);
		if ($Login != null) {
			$error = "<p>gebruikersnaam al in gebruik</p>";
			return $error;
		}else{
		$stmt = $conn->prepare("INSERT INTO users (name, password) VALUES (?,?)"); 
		$stmt->bind_param("ss", $user,$password);
		$stmt->execute();
		header('Location: index.php');
		}
	}
	function login($conn,$user,$password){
		$stmt = $conn->prepare("SELECT * FROM users WHERE name=?"); 
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$result = $stmt->get_result();
		$Login = $result->fetch_all(MYSQLI_ASSOC);
		if ($Login == null) {
				return false;
			}
		foreach($Login as $chek){
			if ($chek['Banned'] == "Ja") {
				return false;
			}
			if ($password != $chek['password'] || $user != $chek['name']) {
				return false;
			}
		}
		return true;
	}
	function setSession($user,$conn,$login){
		$token = (rand(1,9999999));
		$token = (string)$token;
		$expiring = time() + 600;
		setcookie("Token", $token, $expiring);
		setcookie("User", $user, $expiring);
		$stmt = $conn->prepare("UPDATE users SET  token=?, expiry=? WHERE name=? ");
		$stmt->bind_param("sss", $token, $expiring, $user);
		$stmt->execute();
		if ($login == "") {
			header('Location: user.php');
		}
	}
	function LoginCHek($conn){
		$query = "SELECT * FROM users ";
		$result = $conn->query($query);
		$Chek = $result->fetch_all(MYSQLI_ASSOC);

		if(count($_COOKIE) <= 0){
			return false;
		}
		if(!isset($_COOKIE["User"])){
			return false;
		}
		if (!isset($_COOKIE["Token"])) {
			return false;
		}
		foreach($Chek as $cheking){
			$now = time();
			if($_COOKIE["User"] != $cheking['name'] && $_COOKIE["Token"] != $cheking['token'] && $now < $cheking['expiry'] && $cheking['Banned'] != "nee") {	
				return false;
			}
		}
		$login = "Logged in";
		$user = $_COOKIE["User"];
		setSession($user,$conn,$login);
		return true;
	}
	function delete($conn){
		$user = $_COOKIE["User"];
		$Delete = "Ja";
		$zero = 0;
		$stmt = $conn->prepare("UPDATE users SET Banned=?, expiry=?, token=? WHERE name=? ");
		$stmt->bind_param("ssss", $Delete,$zero,$zero,$user);
		$stmt->execute();
		header('Location: index.php');
	}
	function GetChambers($conn){
		$query = "SELECT * FROM chambers ";
		$result = $conn->query($query);
		$Chambers = $result->fetch_all(MYSQLI_ASSOC);
		return $Chambers;
	}





?>