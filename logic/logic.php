<?php

	require "config/config.php";
	function CreateUser($conn,$user,$password)
	{
		$stmt = $conn->prepare("SELECT * FROM users WHERE name=?"); 
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$result = $stmt->get_result();
		$Login = $result->fetch_all(MYSQLI_ASSOC);
		if ($Login != null) {
			$error = "<p>gebruikersnaam al in gebruik</p>";//makes it so that if a username is alreadey in use it can not register
			return $error;
		}else{
		$stmt = $conn->prepare("INSERT INTO users (name, password) VALUES (?,?)"); 
		$stmt->bind_param("ss", $user,$password);
		$stmt->execute();
		header('Location: index.php');
		}
	}
	function login($conn,$user,$password)
	{
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
	function setSession($user,$conn,$login)
	{
		$token = (rand(1,9999999));
		$token = (string)$token;
		$expiring = time() + 1800 ;//600
		setcookie("Token", $token, $expiring);
		setcookie("User", $user, $expiring);
		$stmt = $conn->prepare("UPDATE users SET  token=?, expiry=? WHERE name=? ");
		$stmt->bind_param("sss", $token, $expiring, $user);
		$stmt->execute();
		if ($login == "") {
			header('Location: user.php');
		}
	}
	function LoginCHek($conn)
	{
		$query = "SELECT * FROM users ";
		$result = $conn->query($query);
		$Chek = $result->fetch_all(MYSQLI_ASSOC);
		if (count($_COOKIE) <= 0) {
			return false;
		}
		if (!isset($_COOKIE["User"])) {
			return false;
		}
		if (!isset($_COOKIE["Token"])) {
			return false;
		}
		foreach($Chek as $cheking){
			$now = time();
			if($_COOKIE["User"] == $cheking['name'] && $_COOKIE["Token"] == $cheking['token'] && $now > $cheking['expiry'] && $cheking['Banned'] == "Nee") ;
			{	
					$login = "Logged in";
					$user = $_COOKIE["User"];
					setSession($user,$conn,$login);
					return true;
			}
		}
	}
	function delete($conn)
	{
		$user = $_COOKIE["User"];
		$Delete = "Ja";
		$zero = 0;
		$stmt = $conn->prepare("UPDATE users SET Banned=?, expiry=?, token=? WHERE name=? ");
		$stmt->bind_param("ssss", $Delete,$zero,$zero,$user);
		$stmt->execute();
		header('Location: index.php');
	}
	function GetChambers($conn)
	{
		$query = "SELECT * FROM chambers ";
		$result = $conn->query($query);
		$Chambers = $result->fetch_all(MYSQLI_ASSOC);
		return $Chambers;
	}
	function createChamber($conn)
	{
		if ($_POST['ChamberName'] == null || $_POST['Onderwerp'] == null || $_POST['feature1'] == null) {
			echo "<p>De eerste 3 velden moeten ingevuld zijn.</p>";
		} else {
			$user = $_COOKIE["User"];
			$name = $_POST['ChamberName'];
			$subject = $_POST['Onderwerp'];
			$feature1 = $_POST['feature1'];
			$feature2 = $_POST['feature2'];
			$feature3 = $_POST['feature3'];
			$feature4 = $_POST['feature4'];
			$feature5 = $_POST['feature5'];
			$feature6 = $_POST['feature6'];
			$features = array($feature1,$feature2,$feature3,$feature4,$feature5,$feature6);
			$stmt = $conn->prepare("INSERT INTO chambers (Name,subject,owner) VALUES(?,?,?)");
			$stmt->bind_param("sss", $name,$subject,$user);
			$stmt->execute();
			$stmt = $conn->prepare("INSERT INTO features (feature,chamber_id) VALUES(?,?)");
			$last_id = $conn->insert_id;
		for ($result = 0;$result <= 6; $result++)
			{
				if ($features[$result] != null) {
					$stmt->bind_param("ss", $features[$result],$last_id);
					$stmt->execute();
				}
			}
		header('location: user.php');
		}
	}
	function deleteChamber($conn)
	{
		$id = $_GET['id'];
		$user = $_COOKIE["User"];

		$stmt = $conn->prepare("SELECT * FROM chambers WHERE id=?"); 
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$Chambers = $result->fetch_all(MYSQLI_ASSOC);
		foreach ($Chambers as $chek) {
			$owner = $chek['owner'];
		}
		if ($Chambers != null && $user == $owner) {
			$stmt = $conn->prepare("DELETE FROM chambers WHERE id=?"); 
			$stmt->bind_param("s", $id);
			$stmt->execute();
			$stmt = $conn->prepare("DELETE FROM features WHERE chamber_id=?"); 
			$stmt->bind_param("s", $id);
			$stmt->execute();
			header('Location: user.php');
		}else{
			header('Location: user.php');
		}
	}
	function UserChamber($conn,$pdo)
	{
		if (isset($_GET['chamber'])) {
			$id = $_GET['chamber'];
		}else{
			header('location:user.php');
			die();
		}

		$user = $_COOKIE["User"];
		$stmt = $conn->prepare("SELECT * FROM users WHERE name=?");
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$result = $stmt->get_result();
		$User = $result->fetch_all(MYSQLI_ASSOC);
		foreach($User as $user){
			$userid = $user['id'];
		}

		$stmt = $pdo->prepare("SELECT * FROM chamberusers WHERE chambers_id=:id AND users_id=:chamberId"); 
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);      
		$stmt->bindParam(':chamberId',$userid,PDO::PARAM_INT);      
		$stmt->execute();
		$Chambers = $stmt->fetchAll();

		$now = time();
		if ($Chambers != null) {
				$stmt = $pdo->prepare("UPDATE chamberusers SET 	expiry=:expiry WHERE users_id=:user");
				$stmt->bindParam(':expiry',$now,PDO::PARAM_INT);      
				$stmt->bindParam(':user',$userid,PDO::PARAM_INT);
				$stmt->execute();
		}else{
			foreach($User as $UserId){
				$stmt = $pdo->prepare("INSERT INTO chamberusers (users_id,chambers_id,expiry) VALUES (:id,:chamberId,:expiry);");
				$stmt->bindParam(':id',$userid,PDO::PARAM_INT);      
				$stmt->bindParam(':chamberId',$id,PDO::PARAM_INT);      
				$stmt->bindParam(':expiry',$now,PDO::PARAM_INT);      
				$stmt->execute();
			}
		}
		$stmt = $conn->prepare("SELECT features.feature, features.chamber_id, chambers.id, chambers.Name, chambers.subject, chambers.owner FROM chambers INNER JOIN features ON chambers.id=features.chamber_id WHERE chambers.id=?");
		$stmt->bind_param("s", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		$chamber = $result->fetch_all(MYSQLI_ASSOC);
		if ($chamber != null) {
			return  $chamber;
		}
		else{
			header('location:user.php');
		}
	}
	function UpdateChamber($pdo)
	{
		$idChamber = $_GET['chamber'];
		$user = $_COOKIE['User'];

		$stmt = $pdo->prepare("SELECT * FROM chambers WHERE id=:id"); 
		$stmt->bindParam(':id',$idChamber,PDO::PARAM_INT);      
		$stmt->execute();
		$Chambers = $stmt->fetchAll();

		foreach ($Chambers as $chek) {
			$owner = $chek['owner'];
		}
		if ($Chambers != null && $user == $owner) {
			$stmt = null;
			$stmt = $pdo->prepare("UPDATE chambers SET subject=:subject  WHERE id=:id");
			$stmt->bindParam(':subject', $_POST['subject'], PDO::PARAM_STR, 12);       
			$stmt->bindParam(':id', $idChamber, PDO::PARAM_INT); 
			$stmt->execute(); 
		}
	}
	function Answer($pdo)
	{
		$answer = $_POST['answer'];
		$Chamberid = $_POST['chamber'];
		$user = $_COOKIE['User'];

		$stmt = $pdo->prepare("SELECT * FROM users WHERE name=:name");
		$stmt->bindParam(':name', $user, PDO::PARAM_STR);       
		$stmt->execute(); 
		$answers = $stmt->fetchAll();
		foreach($User as $user){
			$userid = $	$answers['id'];
		}

		$stmt = $pdo->prepare("SELECT * FROM answer WHERE users_id=:id AND WHERE feature_id=:featureid"); 
		$stmt->bindParam(':id',$userid,PDO::PARAM_INT);      
		$stmt->bindParam(':featureid',$featureid,PDO::PARAM_INT);      
		$stmt->execute();
		$answers = $stmt->fetchAll();

		if ($answers != null) {
			$stmt = $pdo->prepare("UPDATE answer SET answer=:answer WHERE users_id=:userid"); 
			$stmt->bindParam(':answer',$answer,PDO::PARAM_STR);      
			$stmt->bindParam(':userid',$userid,PDO::PARAM_INT);      
			$stmt->execute();
		}else{
			$stmt = $pdo->prepare("INSERT INTO (users_id,feature_id,answer ) VALUES (:user,:featureid,:answer)"); 
			$stmt->bindParam(':user',$userid,PDO::PARAM_INT);  
			$stmt->bindParam(':featureid',$featureid,PDO::PARAM_INT);      
			$stmt->bindParam(':answer',$answer,PDO::PARAM_STR);          
			$stmt->execute();
		}
	}
?>