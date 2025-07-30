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

    $errorMessage = "";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $_SESSION["username"] = $username;

        $sql = "SELECT * FROM `Login` WHERE email='$username'";
        $result = $conn->query($sql);

        if($result->num_rows > 0) {
            $errorMessage = "Email is already registered";
        }
        else {
            $sql = "INSERT INTO `Login`(`email`, `password`) VALUES ('$username','$password')";
            $conn->query($sql);
            header("Location: login.php");
        } 
    }
    $conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href='https://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet'>


    <link href='https://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet'>

    <style>
        body {
            font-family: 'Inconsolata';
            font-size: 22px;
            text-align: center;
            margin: 0;
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f5f3ee;
        }
        
        form {
            background-color: #ffffff;
            filter: drop-shadow(0px -4px 8px #7b707053);
            width: 300px;
            padding: 20px;
            padding-bottom: 50px;
            border-radius: 20px;
            font-size: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: auto;
        }

        input, button {
            font-size: 18px;
            font-family: 'Inconsolata';
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #969696;
            border-radius: 12px;
            width: 200px;
        }
        
        button:hover {
            background-color: #8f8a828a;
        }

        div.error-message {
            margin-bottom: 5px;
            min-height: 25px;
        }

        h2 {
            margin-bottom: 12px;
        }

    </style>
</head>
<body>    
    <h1>Headspace</h1>

    <form name="signup" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <h2>Signup</h2>
        <div class="error-message">
            <?php
                if(isset($errorMessage)) {
                    echo $errorMessage;
                }
            ?>
        </div>
        <input type="email" id="username" name="username" placeholder="Email" autocomplete="email" required>
        
        <input type="password" id="password" name="password" placeholder="Password" autocomplete="new-password" required>

        <button type="submit" class="signup-button">Signup</button>
    </form>
</body>
</html>
