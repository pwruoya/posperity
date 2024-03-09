<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
</head>

<body>
    <header>
        <h1>Assignment home page</h1>
        <nav class="nav">
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="services.html">Services</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <form class="find" action="index.php" method="get">
                <label for="admission_number">Adm No:</label>
                <input type="text" id="admission_number" name="admission_number" required>
                <button type="submit" name="get">Search</button>
            </form>
        </nav>
    </header>

    <div id="maindiv">
        <div class="main-content" id="div1">
            <div class="main-content" id="div2">
                <?php
                session_start();
                if (isset($_SESSION['username'])) {
                    // Display user details if logged in
                    echo "<h2>Welcome, " . $_SESSION['username'] . "</h2>";
                    echo "<div class='profile-image'><img src='assets\profile.png' alt='Profile Image'></div>";
                } else {
                    // Display login button if not logged in
                    echo "<a href='login.php' class='button'>Login</a>";
                }
                ?>
            </div>
            <?php
            // Database connection
            $servername = "localhost";
            $dbusername = "root";
            $dbpassword = "";
            $database = "ds_userdb";

            $conn = new mysqli($servername, $dbusername, $dbpassword, $database);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if the search button was clicked
            if (isset($_GET["get"])) {
                // Sanitize the input to prevent SQL injection
                $admission_number = $_GET["admission_number"];

                // Prepare the SQL statement with a parameter placeholder
                $sql = "SELECT `UID`, `Username`, `email`, `password`, `Name`, `mobile`, `address` FROM `users` WHERE `Username` LIKE ? OR `email` LIKE ? OR `UID` LIKE ?";
                $stmt = $conn->prepare($sql);

                // Bind the parameters to the statement
                $searchTermWithWildcards = '%' . $admission_number . '%';
                $stmt->bind_param("sss", $searchTermWithWildcards, $searchTermWithWildcards, $searchTermWithWildcards);



                // Execute the query
                $stmt->execute();

                // Get the result
                $result = $stmt->get_result();
                // echo $result->num_rows;
                if ($result->num_rows > 0) {
                    echo "<h2>Student Details</h2>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<p><strong>Name:</strong> " . $row["Name"] . "</p>";
                        echo "<p><strong>Admission Number:</strong> " . $row["UID"] . "</p>";
                        echo "<p><strong>Mobile Number:</strong> " . $row["mobile"] . "</p>";
                        echo "<hr>"; // Optional: Add a horizontal line between each student's details
                    }
                } else {
                    echo "No student found with admission number: " . $admission_number;
                }


                // Close statement
                $stmt->close();
            }
            // Close connection
            $conn->close();
            ?>

        </div>
        <div class="main-content" id="div3">
            <?php
            // Database connection
            $servername = "localhost";
            $dbusername = "root";
            $dbpassword = "";
            $database = "ds_userdb";

            $conn = new mysqli($servername, $dbusername, $dbpassword, $database);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to fetch names and admission numbers
            $sql = "SELECT `UID`, `Name` FROM `users`";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<h2>Members</h2>";
                echo "<table class='student-table'>";
                echo "<tr><th>Name</th><th>Admission Number</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["Name"] . "</td>";
                    echo "<td>" . $row["UID"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No students found";
            }

            // Close connection
            $conn->close();
            ?>
        </div>
    </div>
    <footer>
        <p>&copy; Distributed systems assignment</p>
    </footer>
</body>

</html>