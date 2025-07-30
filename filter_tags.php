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
    $filter = $_POST["filter"];
    $return = [];

    $sql = "SELECT ent.text, ent.date FROM (Entry ent left join Entry_Tags on ent.entryID = Entry_Tags.entryID) left join Tags on Entry_Tags.tagID = Tags.tagID WHERE ent.email='$username' AND Tags.tag='$filter'";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        $return[] = $row;
    }

    $jsonArray = json_encode($return);
    echo $jsonArray;
?>