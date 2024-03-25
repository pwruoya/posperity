<?php
session_start();
// Database connection parameters
include 'dbconfig.php';

include "redisconnect.php";


// Query to fetch data grouped by year, month, week, and day
$query = "SELECT YEAR(`Timestamp`) AS year, MONTH(`Timestamp`) AS month, WEEK(`Timestamp`, 1) AS week, DAY(`Timestamp`) AS day, SUM(selling_price) AS total FROM `sale` WHERE merchant_id = ? GROUP BY year, month, week, day";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $redis->get('merchantid'));
$stmt->execute();
$result = $stmt->get_result();

// Fetch data into an array
$yearlyTotals = [];
$monthlyTotals = [];
$weeklyTotals = []; // Store weekly totals for each year and month
$dailyTotals = []; // Store daily totals for each year, month, and week
$grandTotal = 0;

while ($row = $result->fetch_assoc()) {
    $year = $row['year'];
    $month = $row['month'];
    $week = $row['week'];
    $day = $row['day'];
    $total = $row['total'];

    // Calculate grand total
    $grandTotal += $total;

    // Store yearly totals
    if (!isset($yearlyTotals[$year])) {
        $yearlyTotals[$year] = 0;
    }
    $yearlyTotals[$year] += $total;

    // Store monthly totals
    if (!isset($monthlyTotals[$year][$month])) {
        $monthlyTotals[$year][$month] = 0;
    }
    $monthlyTotals[$year][$month] += $total;

    // Store weekly totals
    if (!isset($weeklyTotals[$year][$month][$week])) {
        $weeklyTotals[$year][$month][$week] = 0;
    }
    $weeklyTotals[$year][$month][$week] += $total;

    // Store daily totals
    if (!isset($dailyTotals[$year][$month][$week][$day])) {
        $dailyTotals[$year][$month][$week][$day] = 0;
    }
    $dailyTotals[$year][$month][$week][$day] += $total;
}
// $yearlyTotals = array_reverse($yearlyTotals);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details</title>
    <link rel="stylesheet" href="home.css">
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
    <script src="title.js"></script>
</head>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        color: black;
    }

    th {
        background-color: #f2f2f2;
        color: black;
    }

    .trans_button,
    .mtrans_button,
    .wtrans_button,
    .dtrans_button {
        border-left: none;
        border-right: none;
        border-bottom: none;
        background-color: transparent;
        padding: 2px;
        display: flex;
        justify-content: space-between;
    }

    .trans_button div {
        font-size: 1.5vw;
    }

    .trans_button {
        min-width: 100%;
    }

    .mtrans_button {
        margin-left: 2%;
        min-width: 98%;
    }

    .wtrans_button {
        margin-left: 4%;
        min-width: 96%;
    }

    .dtrans_button {
        margin-left: 6%;
        min-width: 94%;
    }

    .trans_button div,
    .mtrans_button div {
        font-size: 1.5vw;
        color: rgb(50, 67, 103);
    }

    @media screen and (max-width: 650px) {
        #maindiv {
            flex: 1;
            background-color: rgba(255, 255, 255, 0.7);
        }

        .main-content {
            flex: 1;
            margin-top: 0;
            background-color: transparent;
        }

        .trans_button div,
        .mtrans_button div {
            font-size: 4.5vw;
        }

    }

    .elevate {
        height: fit-content;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Add box shadow for elevation */
        padding: 2px;
        margin: 5px;
    }
</style>

<body>
    <header>
        <h1>
            <?php
            if ($redis->exists('merchantname')) {
                echo "{$redis->get('merchantname')} transactions";
            }
            ?>
        </h1>
        <div class="head">

            <div class="menu">
                <a onclick="toggleMenu()"><i class="fa-solid fa-bars"></i></a>
                <div id="hide" class="navbar-toggle">
                    <a class="bar" href="index.php">Home</a>
                    <a class="bar" href="makeSale.php">Make Sale</a>
                    <a class="bar" href="inventory.php">Inventory</a>
                    <a class="bar" href="transactions.php">Transactions</a>
                    <a class="bar" href="about.html">About</a>
                    <a class="bar" href="services.html">Services</a>
                    <a class="bar" href="logout.php">Log out</a>
                </div>
            </div>
            <nav class="nav" id="navbarLinks">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="makeSale.php">Make Sale</a></li>
                    <li><a href="inventory.php">Inventory</a></li>
                    <li><a href="transactions.php">Transactions</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="services.html">Services</a></li>
                    <li><a href="logout.php"><i class="fa-regular fa-user" style="color: #ffffff;"></i> log out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="maindiv">
        <div class="main-content" id="div2">
            <div class="elevate">
                <h2>Grand Total</h2>
                <h3><?php echo 'Ksh ' . number_format($grandTotal, 2); ?></h3>
                <div id="day"></div>
            </div>
            <div class="trans_button">
                <div>
                    Year
                </div>
                <div>
                    Sub-total
                </div>
            </div><br>
            <?php

            // Display the results
            foreach ($yearlyTotals as $year => $yearTotal) {
                echo '<button class="trans_button year-button">';
                echo '<div>' . $year . '</div>';
                echo '<div>' . number_format($yearTotal, 2) . '</div>';
                echo '</button>';
                echo '<div class="monthly-transactions" style="display:none;" >';

                foreach ($monthlyTotals[$year] as $month => $monthTotal) {
                    $timestamp = mktime(0, 0, 0, $month, 1, $year); // Get timestamp for the month
                    echo '<button class="mtrans_button" data-date="' . date('Y-m', $timestamp) . '">';
                    echo '<div>' . date('F', $timestamp) . '</div>';
                    echo '<div>' . number_format($monthTotal, 2) . '</div>';
                    echo '</button>';

                    echo '<div class="weekly-transactions" style="display:none;">';
                    foreach ($weeklyTotals[$year][$month] as $week => $weekTotal) {
                        echo '<button class="wtrans_button" data-date="Week ' . $week . '">';
                        echo '<div>Week ' . $week . '</div>';
                        echo '<div>' . number_format($weekTotal, 2) . '</div>';
                        echo '</button>';

                        echo '<div class="daily-transactions" style="display:none;">';
                        foreach ($dailyTotals[$year][$month][$week] as $day => $dayTotal) {
                            echo '<button class="dtrans_button" data-date="' . date('Y-m-d', mktime(0, 0, 0, $month, $day, $year)) . '">';
                            echo '<div>' . date('d', mktime(0, 0, 0, $month, $day, $year)) . '</div>';
                            echo '<div>' . number_format($dayTotal, 2) . '</div>';
                            echo '</button>';
                        }
                        echo '</div>'; // End of daily-transactions
                    }
                    echo '</div>'; // End of weekly-transactions
                }

                echo '</div>'; // End of monthly-transactions
            }



            ?>


        </div>

    </div>
    <footer>
        <p style="font-size: 10px;">
            &copy; 2024 posperity,all rights reserved</p>
    </footer>
</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const yearButtons = document.querySelectorAll('.year-button');
        const monthlyTransactions = document.querySelectorAll('.monthly-transactions');
        const weeklyTransactions = document.querySelectorAll('.weekly-transactions');
        const dailyTransactions = document.querySelectorAll('.daily-transactions');

        yearButtons.forEach(button => {
            button.addEventListener('click', () => {
                const monthlyTrans = button.nextElementSibling;
                const weeklyTrans = monthlyTrans.querySelectorAll('.weekly-transactions');

                // Toggle visibility of monthly transactions
                if (monthlyTrans.style.display === 'none') {
                    monthlyTrans.style.display = 'block';
                    button.style.backgroundColor = 'rgb(50, 67, 103)';
                    button.children[0].style.color = 'white';
                    button.children[1].style.color = 'white';
                } else {
                    button.children[0].style.color = '';
                    button.children[1].style.color = '';
                    monthlyTrans.style.display = 'none';
                    button.style.backgroundColor = '';
                    button.style.color = 'black';
                }

                // Hide all weekly and daily transactions initially
                weeklyTrans.forEach(weekly => {
                    weekly.style.display = 'none';
                });
                dailyTransactions.forEach(daily => {
                    daily.style.display = 'none';
                });
            });
        });

        monthlyTransactions.forEach(monthly => {
            const monthBtn = monthly.querySelectorAll('.mtrans_button');
            monthBtn.forEach(monthBtn => {
                monthBtn.addEventListener('click', () => {
                    const weeklyTrans = monthBtn.nextElementSibling;

                    // Toggle visibility of weekly transactions
                    if (weeklyTrans.style.display === 'none') {
                        weeklyTrans.style.display = 'block';
                    } else {
                        weeklyTrans.style.display = 'none';
                    }

                    // Hide all daily transactions initially
                    dailyTransactions.forEach(daily => {
                        daily.style.display = 'none';
                    });
                });
            });
        });

        weeklyTransactions.forEach(weekly => {
            const weekBtn = weekly.querySelectorAll('.wtrans_button');
            weekBtn.forEach(wBtn => {
                wBtn.addEventListener('click', () => {
                    const dailyTrans = wBtn.nextElementSibling;

                    // Toggle visibility of daily transactions
                    if (dailyTrans.style.display === 'none') {
                        dailyTrans.style.display = 'block';
                    } else {
                        dailyTrans.style.display = 'none';
                    }
                });
            });
        });

        dailyTransactions.forEach(daily => {
            const dayBtns = daily.querySelectorAll('.dtrans_button');
            dayBtns.forEach(dayBtn => {
                dayBtn.addEventListener('click', () => {
                    const date = dayBtn.getAttribute('data-date'); // Assuming you have a data-date attribute on your button
                    const url = 'daily_transactions.php?date=' + encodeURIComponent(date);
                    window.location.href = url;
                });
            });
        });


    });
</script>

</html>