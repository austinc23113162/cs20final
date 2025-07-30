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
    $date = $_POST["displayDate"];
    $startDate = date('Y-m-d', strtotime($date));
    $endDate = date('Y-m-d', strtotime("$startDate +35 days"));
    $return = [];

    $sql = "SELECT ent.date, ent.color FROM (Entry ent left join Entry_Tags on ent.entryID = Entry_Tags.entryID) left join Tags on Entry_Tags.tagID = Tags.tagID WHERE ent.email='$username'
    AND STR_TO_DATE(ent.date, '%m/%d/%Y') >= '$startDate'
    AND STR_TO_DATE(ent.date, '%m/%d/%Y') < '$endDate'";
    
    $result = $conn->query($sql);
    
    $colorMap = [];
    if ($result && $result->num_rows > 0) {
        
        $i = 0;
        while(($row = $result->fetch_assoc())) {
            $time = date('m/d/Y', strtotime($row["date"]));
            $colorMap[$time] = $row["color"];
            $i = $i + 1;
        }
    }

    for ($i = 0; $i < 35; $i++) {
        $currDate = date('m/d/Y', strtotime("$date +$i days"));
        if (true) {
            $return[$i] = $colorMap[$currDate];
        } else {
            $return[$i] = "";
        }
    }

    $jsonArray = json_encode($return);
    echo $jsonArray;
?>