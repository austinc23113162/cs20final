<?php
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

    $username = $_SESSION["username"];

    $query = "SELECT paid FROM `Login` WHERE email='$username'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $has_paid = $row['paid'];
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" href="style.css">
        <link href='https://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css2?family=Calistoga' rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@jaames/iro@5"></script>

        <title>Headspace - Home</title>
    </head>

    <body>
        <script>
            window.onload = function() {
                const today = new Date();
                document.getElementById("entryDate").innerHTML = getDateByCal(today.getDate());
            } //end window.onload
        </script>
        <main>
    
            <div class="l-calendar">

                <header>
                    <div style="
                            background-color: #FFFEFC;
                            padding: 15px; 
                            width: 220px;
                            filter: drop-shadow(2px 2px 4px #7b70708b); 
                            display: flex;
                            align-items: center;
                            rotate: -2deg;">

                        <img src="logo.png" style="height: 45px; padding-right: 20px;"/>
                        <h1 style="margin: 0px; font-size: 32px;"><hl>Headspace</hl></h1>
                    </div>
                </header>
                
                <div class="cal-nav">
                    <button id="backBtn"><<</button>
                    <span id="monthLabel">April - Your Moods</span>
                    <button id="nextBtn">>></button>
                </div>


                <div class="cal-bg"></div>
                
                <p id="instructions">Click on a day in the calendar to edit old entries or insert new ones!</p>

            </div>  <!-- l-calendar -->

            <div class="r-write">
                
                <header class="nav" style="
                            background-color: #FFFEFC;
                            display: flex;
                            padding: 40px 0px 10px;
                            z-index: 1;">
                    <!-- Insert rest of nav buttons here -->


                    <ul>
                        <li> Hi, <?php echo $username; ?>!<br></li>
                        <li><a href="<?php echo $has_paid ? 'stats.php' : 'payment.html'; ?>"><?php echo $has_paid ? "Statistics" : "Unlock Premium"; ?></a></li>
                        <li><a href="login.php">Log Out</a></li>
                    </ul>
                
                </header>

                <div style="filter: drop-shadow(2px -2px 4px #7b70708b); padding: 30px 0px 20vh 0px; background-color: #f5f3ee;">

                    <div class="title">
                        <h2><hl id="entryDate"></hl> - </h2>
                    </div>

                    <form class="entry" method='post' action='process_entry.php' id="entryForm" onsubmit='return validate()'>
                    <div class="entry" style="filter: none; padding: 5px;">
                        <div class="l-entry">
                            <div class="colorPicker" id="defaultPicker">
                                <label>Pick a Mood Color:</label>
                            </div>
                            <div id="values"></div>
        
                            <script>
                                var defaultPicker = new iro.ColorPicker("#defaultPicker", {
                                    width: 200,
                                    color: "rgb(255, 0, 0)",
                                    borderWidth: 1,
                                    borderColor: "#fff",
                                });
        
                                // update the "selected color" values whenever the color changes
                                var values = document.getElementById("values");
                                defaultPicker.on(["color:init", "color:change"], function(color){
                                    // show the current chosen color
                                    hex = color.hexString;
                                    
                                    // values.innerHTML = "Hex: " + hex;
                                    values.innerHTML = `
                                        <span>Hex: ${hex}</span>
                                        <div style="
                                            width: 20px; 
                                            height: 20px; 
                                            border: 1px solid #ccc; 
                                            background-color: ${hex}; 
                                            border-radius: 4px;
                                        "></div>
                                    `;
                                });
                            </script>
        
                            <div class="tags">
                                <label for="tags">Tags:</label>
                                <input type="tfext" id="tags" name="tags" placeholder="e.g. happy, anxious"/><br />
                                <small>Use commas to separate tags</small>
                            </div>
                        </div>

                        <div class="r-entry">
                            <label for="entryText">Today's Entry:</label>
                            <textarea id="entryText" name="entryText" rows="15" required></textarea>
                            <input type="hidden" id="hexString" name="hexString">
                            <input type="hidden" id="date" name="date">
                            

                            <button type="submit" class="entry-button">Save Entry</button>
                        </div>

                    </div>  <!-- entry -->
                    </form>
                </div>  <!-- input fields -->
            </div>  <!-- r-write -->
        </body>
    </main>
                
        

    <script>
        //content holder
        const week = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        const months = ["January", "Feburary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var colors = [
            "none", "none", "none", "none", "none", "none", "none",
            "none", "none", "none", "none", "none", "none", "none",
            "none", "none", "none", "none", "none", "none", "none",
            "none", "none", "none", "none", "none", "none", "none",
            "none", "none", "none", "none", "none", "none", "none",
            "none", "none", "none", "none", "none", "none", "#7b7070"
        ];

        // get today
        const day = new Date();
        var year = day.getFullYear();
        var month = day.getMonth();

        var firstDay = new Date(year, month, 1);
        var startDay = firstDay.getDay();
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        
        //DOM
        const labelElement = document.getElementById("monthLabel");
        const nextBtn = document.getElementById("nextBtn");
        const backBtn = document.getElementById("backBtn");

        if (labelElement) {
            labelElement.textContent = `[${months[month]}] - Your Moods`;
        }

        nextBtn.addEventListener("click", function () {
                    getOtherMonth(true);
                });
        backBtn.addEventListener("click", function () {
                    getOtherMonth(false);
                });
    
        //div creation
        const container = document.createElement("div");
        container.className = "cal-container";
    
        function makeCalGrid() {
            container.innerHTML = '';
            
            for (let i = 0; i < 7; i++) {
                const cell = document.createElement("div");
                cell.className = "cell-label";
                
                const p = document.createElement("p");
                p.textContent = week[i];

                cell.appendChild(p);    
                container.appendChild(cell);
            }

            for (let i = 0; i < 35; i++) {
                const cell = document.createElement("button");
                cell.className = "cell";
                cell.id = "cell" + i;

                const inner = document.createElement("div");
                inner.className = "cell-inner";

                const p = document.createElement("p");

                //set date
                const dayNum = i - startDay + 1;
                if (i >= startDay && dayNum <= daysInMonth) {
                    p.textContent = dayNum;

                    // add get day click function
                    cell.addEventListener("click", function () {
                        displayEntry(dayNum);
                    });
                } else {
                    p.textContent = "";
                }

                inner.appendChild(p);
                cell.appendChild(inner);

                const color = colors[i];
                if (color && color !== "none") {
                    cell.style.backgroundColor = color;
                }

                container.appendChild(cell);
            }
            
        }
            
        document.querySelector(".cal-bg").appendChild(container);

        function getDateByCal(day) {
            let outMonth = month + 1;
            const mm = outMonth.toString().padStart(2, '0');
            const dd = day.toString().padStart(2, '0');

            const formatted = `${mm}/${dd}/${year}`;
            return formatted;
        }

        function getOtherMonth(isNext) {
            if (isNext) {
                month++;
            } else {
                month--;
            }

            if (month < 0) {
                month = 11;
                year--;
            } else if (month > 11) {
                month = 0;
                year++;
            }

            firstDay = new Date(year, month, 1);
            startDay = firstDay.getDay();
            daysInMonth = new Date(year, month + 1, 0).getDate();

            if (labelElement) {
                labelElement.textContent = `[${months[month]}] - Your Moods`;
            }
            getCalColors();
            makeCalGrid();
        }

        function validate() {
            document.getElementById('hexString').value = hex;

            document.getElementById("date").value = document.getElementById("entryDate").innerHTML;
            return true;
        }
        
        function displayEntry(day) {
            date = getDateByCal(day);
            document.getElementById("entryDate").innerHTML = date;
            
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    responseArr = JSON.parse(this.responseText)
                    if(responseArr.length != 0){
                        document.getElementById("entryText").innerHTML = responseArr[0];
                        document.getElementById("tags").value = responseArr[2];
                        let values = document.getElementById("values");
                        values.innerHTML = `
                                        <span>Hex: ${responseArr[1]}</span>
                                        <div style="
                                            width: 20px; 
                                            height: 20px; 
                                            border: 1px solid #ccc; 
                                            background-color: ${responseArr[1]}; 
                                            border-radius: 4px;
                                        "></div>
                                    `;
                    }
                    else {
                        document.getElementById("entryText").innerHTML = ""
                        document.getElementById("tags").value = "";
                    }
                }
            };
            xhttp.open("POST", "get_entry.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
            xhttp.send("displayDate=" + date);
            makeCalGrid();
        }

        function getCalColors() {

            date = getDateByCal(1);
            var unprocessedColors = [];

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {

                if (this.readyState == 4 && this.status == 200) {
                let responseArr = JSON.parse(this.responseText);
                console.log(responseArr);

                if (responseArr.length != 0) {
                    unprocessedColors = responseArr;
                } else {
                    unprocessedColors = [];
                }

                let currUnprocessed = 0;
                for (let i = 0; i < colors.length; i++) {
                    let dayNum = i - startDay + 1;

                    if (i >= startDay && dayNum <= daysInMonth) {
                        if (currUnprocessed < unprocessedColors.length) {
                            if (unprocessedColors[currUnprocessed] != "") {

                                colors[i] = unprocessedColors[currUnprocessed];

                            } else {
                                colors[i] = "none";
                            }
                            currUnprocessed++;
                        } else {
                            colors[i] = "none";
                        }
                    } else {
                        colors[i] = "none";
                    }
                }
            }
            makeCalGrid();
        };
            xhttp.open("POST", "get_colors.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
            xhttp.send("displayDate=" + date);
        }
        makeCalGrid();
        
        $(document).ready(function() {
            setTimeout(function() {
                getCalColors();
            }, 300);
        });
    </script>
    


</html>