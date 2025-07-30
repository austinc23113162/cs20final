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
    $hexString = $_POST['hexString'];
    $entryText = $_POST["entryText"];
    $date = $_POST["date"];
    $tags = $_POST["tags"];
    $tagsArr = explode(",", $tags);
    
    $sql = "SELECT entryID FROM `Entry` WHERE email='$username' AND date='$date'";
    $result = $conn->query($sql);
    //if it's a new entry, insert it
    if($result->num_rows == 0) {
        $sql = "INSERT INTO `Entry`(`entryID`, `email`, `text`, `color`, `date`) VALUES ('', '$username', '$entryText', '$hexString', '$date')";
        $conn->query($sql);

        //gets the inserted id
        $entryID = $conn->insert_id;
    }
    //if user is updating an existing entry, update it
    else {
        $row = $result->fetch_assoc();
        $entryID = $row["entryID"];
        $sql = "UPDATE `Entry` SET `text`='$entryText',`color`='$hexString' WHERE email='$username' AND date='$date'";
        $conn->query($sql);
    }
    
    //for each tag that was entered
    foreach($tagsArr as $tag) {
        //don't save empty tags
        if($tag != "") {
            $tag = trim($tag);
            $sql = "SELECT * FROM `Tags` WHERE tag='$tag'";
            $result = $conn->query($sql);
            //if it's a new tag in the table, insert it
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO `Tags`(`tagID`, `tag`) VALUES ('','$tag')";
                $conn->query($sql);
                $tagID = $conn->insert_id;
            }
            //if the tag already exists, just get the id
            else {
                $row = $result->fetch_assoc();
                $tagID = $row["tagID"];
            }
        
            //check if already exists
            $sql = "SELECT * FROM `Entry_Tags` WHERE entryID='$entryID' AND tagID='$tagID'";
            $result = $conn->query($sql);
            if($result->num_rows == 0) {
                $sql = "INSERT INTO `Entry_Tags`(`entryID`, `tagID`) VALUES ('$entryID','$tagID')";
                $conn->query($sql); 
            }  
        }    
    }

    header("Location: homepage.php");
    $conn->close();
?>
