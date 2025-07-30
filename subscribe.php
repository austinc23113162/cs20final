<?php
    session_start();
    //establish connection info (get these info from siteground)
    $server = "35.212.93.90";// your server
    $userid = "udotiyabra2mv"; // your user id (name)
    $pw = "cs20finalpassword"; // your pw
    $db= "dbndrvh93shrhj"; // your database name
            
    // Create connection
    $conn = new mysqli($server, $userid, $pw);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //select the database
    $conn->select_db($db);
    
    $username = $_SESSION["username"];

    $sql = "UPDATE `Login` SET `paid` = true where email = '$username'";
    $result = $conn->query($sql);
    
    header("Location: stats.php");
?>