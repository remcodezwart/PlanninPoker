<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "planningpoker";

// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);

$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Check connection
