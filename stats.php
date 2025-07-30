<?php
    /*----------------------------------------------------------------*
    |               Connecting to database
    *----------------------------------------------------------------*/

    session_start();
    $server = "35.212.93.90";   // server
    $userid = "udotiyabra2mv";  // user id (name)
    $pw = "cs20finalpassword";  // pw
    $db= "dbndrvh93shrhj";      // database name
            
    // Create connection
    $conn = new mysqli($server, $userid, $pw);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->select_db($db);

    if (!isset($_SESSION["username"])) {
            echo "Please log in.";
            exit();
    }
    
    $username = $_SESSION["username"];

    /*----------------------------------------------------------------*
    |               Collecting all entries into array
    *----------------------------------------------------------------*/

    $sql = "SELECT * FROM `Entry` WHERE email='$username'";
    $result = $conn->query($sql);

    $entries = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $entries[] = $row;
        }
    }
    
    $num_entries = count($entries);

    /*----------------------------------------------------------------*
    |               Getting top 3 most used tags
    *----------------------------------------------------------------*/

    $tag_counts = [];

    $sql = "SELECT Tags.tag FROM (Entry ent left join Entry_Tags on ent.entryID = Entry_Tags.entryID) left join Tags on Entry_Tags.tagID = Tags.tagID WHERE ent.email='$username'";  
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
        $curr_tag = $row['tag'];
        if($curr_tag != null) {
            if(!isset($tag_counts[$curr_tag])) {
                $tag_counts[$curr_tag] = 0;
            }
            $tag_counts[$curr_tag]++;
        }
    }

    // Sort tags with highest and trim the array to only hold the top 3
    arsort($tag_counts);
    $top_tags = array_slice($tag_counts, 0, 3, true);

    $conn->close();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" href="style.css">
        <link href='https://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css2?family=Calistoga' rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@jaames/iro@5"></script>


        <style type="text/css">
            * {box-sizing:border-box}

            /* Slideshow container */
            .slideshow-container {
                max-width: 1000px;
                position: relative;
                margin: auto;
            }

            /* Hide the images by default */
            .mySlides {
                display: none;
            }

            /* Next & previous buttons */
            .prev, .next {
                cursor: pointer;
                position: absolute;
                top: 45%;
                width: auto;
                margin-top: -22px;
                padding: 20px;
                color: #85c0c5;
                font-weight: bold;
                font-size: 30px;
                transition: 0.6s ease;
                border-radius: 0 3px 3px 0;
                user-select: none;
            }

            .next {
                right: 0;
            }

            /* On hover, add a black background color with a little bit see-through */
            .prev:hover, .next:hover {
                color: rgba(0, 0, 0, 0.8);
            }

            /* The dots/bullets/indicators */
            .dot {
                cursor: pointer;
                height: 15px;
                width: 15px;
                margin: 0 2px;
                background-color: #ffffff;
                filter: drop-shadow(0px -4px 8px #7b707053);

                border-radius: 50%;
                display: inline-block;
                transition: background-color 0.6s ease;
            }

            .active, .dot:hover {
                background-color: #717171;
            }

            .slide {
                display: flex;
                align-items: center;

                height: 550px;
                padding: 30px;
                border-radius: 20px;
                background-color: #FFFEFC;
                filter: drop-shadow(0px -4px 8px #7b707053);
                text-align: center;
                overflow: auto;
            }

            .slidetext{
                width: 60%;
                margin: auto;

            }

            /* Fading animation */
            .fade {
            animation-name: fade;
            animation-duration: 1.5s;
            }

            @keyframes fade {
            from {opacity: .4}
            to {opacity: 1}
            }


            .searchform {
                border-radius: 8px;
                filter: drop-shadow(0px -4px 8px #7b707053);
            }

            @media (max-width: 900px) {
                #big_nav {
                    display: none; 
                }

                #small_nav {
                    display: flex; 
                    justify-content: center;
                    padding: 0;
                    margin: 0;
                }

                #small_nav ul li {
                    font-size: 16px !important;
                }
            }

            @media (min-width: 901px) {
                #big_nav {
                    display: flex;
                }

                #small_nav {
                    display: none; 
                }
            }

        </style>

        <title>Headspace - Stats</title>
    </head>

    <body>
        <header style="display: flex;">
            <div style="
                    background-color: #FFFEFC;
                    padding: 15px; 
                    width: 350px;
                    height: 80px;
                    filter: drop-shadow(2px 2px 4px #7b70708b); 
                    display: flex; 
                    rotate: -2deg;">

                <img src="logo.png" style="height: 50px; padding-right: 20px;"/>
                <h1 style="margin: 0px; font-size: 36px;"><hl>Statistics</hl></h1>
            </div>
            <div class="nav" id="big_nav">
                <ul>
                    <li><a href="homepage.php">Back to Diary</a></li>
                    <li><a href="login.php">Log Out</a></li>
                </ul>
            </div>

        </header>

        <!-- Slideshow container -->
        <div class="slideshow-container">

            <!-- Full-width images with number and caption text -->
            <div class="mySlides fade">
                <div class="slide"><div class="slidetext">                 You've made <?php echo $num_entries; ?> entries in Headspace. <br><br>
                    Click through the slideshow to see your stats!
                    </div> 
                </div>
            </div>
        
            <div class="mySlides fade">
                <div class="slide">
                    <div class="slidetext">
                        Your most used tags were: <br><br>

                        <?php 
                            $i = 1;
                            foreach ($top_tags as $tag => $count) {
                                echo "$i. \"$tag\", in $count entries <br>";
                                $i++;
                            } 
                        ?>
                        <br />
                        Notice a pattern? Think about how each of these things are affecting your life and mental health. 
                    </div> 
                </div>
            </div>
        
            <div class="mySlides fade">
                <div class="slide">
                    <div class="slidetext">
                        <label for="tag">Search your entries by tag:</label><br><br>
                        <input type="text" id="tag" name="tag" placeholder="Enter a tag..." required>
                        <button type="button" id="search">Search</button>
                        <h3 id="result"></h3>
                        <div id="display_result"></div>
                    </div> 
                </div>
            </div>
        
            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
        <br>
        
        <!-- Make the dots/circles respond on click -->
        <div style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
        <br><br>
        <div class="nav" id="small_nav">
                <ul>
                    <li><a href="homepage.php">Back to Diary</a></li>
                    <li><a href="login.php">Log Out</a></li>
                </ul>
        </div>
  

        <script>
            window.onload = function() {
                search = document.getElementById("search");
                search.addEventListener("click", function () {
                    filter();
                });
            }

            function filter() {
                filter_tag = document.getElementById("tag").value;
                document.getElementById("result").innerHTML = "Results for tag: '" + filter_tag + "'";

                                  
                        
                var result = "";
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        responseArr = JSON.parse(this.responseText)

                        for(let row of responseArr) {
                            result += "<div class='searchform'><strong>Date: </strong>" + row["date"] + "<br /><strong>Text: </strong>" + row["text"] + "</div><br />";
                        }
                    }
                    
                    if(result != "") {
                        $('#display_result').html(result);
                    }
                    else {
                        $('#display_result').html("<p>No entries found with that tag.</p>");
                    }
                };

                xhttp.open("POST", "filter_tags.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
                xhttp.send("filter=" + filter_tag);
            }


            let slideIndex = 1;
            showSlides(slideIndex);

            // Next/previous controls
            function plusSlides(n) {
            showSlides(slideIndex += n);
            }

            // Thumbnail image controls
            function currentSlide(n) {
            showSlides(slideIndex = n);
            }

            function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySlides");
            let dots = document.getElementsByClassName("dot");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            }

        </script>

    </body>

</html>