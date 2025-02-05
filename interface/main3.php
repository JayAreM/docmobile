<?php
if (!isset($_SESSION)) {
    session_start();
}

defined('ROOTER') ? NULL : define("ROOTER", "../");
require_once(ROOTER . 'javascript/ajaxFunction.php');
require_once(ROOTER . 'includes/database.php');

	if(!isset($_SESSION['employeeNumber'])){
		$link = "<script>window.open('../../dtr/interface/login.php','_self')</script>";
		echo $link;
	}

	// Check privilege and redirect if privilege is 6 and 7 go to project cleansing
	if (isset($_SESSION['privy'] ) && ($_SESSION['privy'] == '6' )) {

		$link = "<script> window.open('../../inv/interface/main.php', '_self')</script>";
		echo $link;
	}


    $officeCode = $_SESSION['officeCode'];

    $employeeNumber = $_SESSION['employeeNumber'];

    $sql = "SELECT COUNT(*) AS TotalCount
        FROM (
            SELECT * 
            FROM citydoc2023.voucherhistory 
            WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
            UNION ALL
            SELECT * 
            FROM citydoc2023.voucherhistory 
            WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
            UNION ALL
            SELECT * 
            FROM citydoc2024.voucherhistory 
            WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
            UNION ALL
            SELECT * 
            FROM citydoc2025.voucherhistory 
            WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
        ) AS TotalReceived";

    $result = $database->query($sql);
   
    $year = date('Y');

    $data = $database->fetch_array($result);

    $sql1 = "SELECT  *, DATEDIFF(CURRENT_DATE, STR_TO_DATE(DateModified, '%Y-%m-%d %H:%i')) AS DaysAgo , Status
            FROM 
            citydoc2025.voucherhistory where ModifiedBy = '$employeeNumber' and DateModified like '%$year%'";
    $result1 = $database->query($sql1);
    $data1 = $database->fetch_array($result1);

    // echo $sql1 ;

    // echo $data['TotalCount'];

?>
<?php include('navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DT Receiver</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    /* General Styles */
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #ffffff, #f4f7fb);
        color: #333;
        height: 90vh;
    }

/* Scale-up effect for cards */
@keyframes scaleUp {
    0% {
        opacity: 0;
        transform: scale(0.9);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

    /* Content Container */
    .content-container {
        padding: 20px;
        text-align: center;
        transition: margin-left 0.3s;
        width: 80%;
        margin: 10px auto;
        animation: scaleUp 0.8s ease-out forwards;
    }

    .content-container h1 {
        font-size: 2em;
        color: #333;
        margin-bottom: 10px;
    }

    .welcome-message {
        font-size: 1em;
        color: #555;
        margin-bottom: 20px;
        text-align:left;
        font-family: "Poppins", sans-serif;
    }

    .menu-icon {
        display: none;
        font-size: 30px;
    }

    /* Mobile Styles */
    @media (max-width: 1024px) {
    .card {
        flex: 1 1 calc(50% - 20px);
        }
    }

    @media (max-width: 768px) {

        .content-container {
            padding: 15px;
        }

        .sidebar.open {
            left: 0;
        }

        .dashboard-cards {
            display: block;
        }

        .card {
            flex: 1 1 100%;
        }
    }

    /* Dashboard Card Styles */
    .dashboard-cards {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-top: 20px;
        gap: 20px;
    }

    .card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        flex: 1 1 calc(33.333% - 20px);
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        min-width: 250px;
        /* pointer-events:none; */
         
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .card-icon {
        font-size: 2em;
        color: #1c7ed6;
        margin-bottom: 10px;
    }

    .card-content h2 {
        font-size: 1.2em;
        margin: 10px 0;
    }

    .card-content-recent {
    overflow: auto;
    height: 200px;
    scrollbar-width: thin; /* For Firefox */
    scrollbar-color: #aaa #f1f1f1; /* Thumb & track color */
    }

    /* Custom scrollbar for Webkit browsers (Chrome, Safari) */
    .card-content-recent::-webkit-scrollbar {
        width: 6px; /* Thin scrollbar */
    }

    .card-content-recent::-webkit-scrollbar-thumb {
        background: #aaa; /* Scroll thumb color */
        border-radius: 10px; /* Rounded edges */
    }

    .card-content-recent::-webkit-scrollbar-thumb:hover {
        background: #888; /* Slightly darker on hover */
    }

    .card-content-recent::-webkit-scrollbar-track {
        background: #f1f1f1; /* Track color */
        border-radius: 10px;
    }


    .card-content p {
        color: #777;
        h
    }

    /* Latest Activities Styles */
    .latest-activities {
        margin-top: 20px;
        text-align: left;
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .latest-activities h2 {
        font-size: 1.5em;
        margin-bottom: 10px;
        color: #333;
    }

    .latest-activities ul {
        list-style-type: none;
        padding: 0;
    }

    .latest-activities ul li {
        font-size: 1em;
        padding: 10px;
        border-bottom: 1px solid #eee;
        color: #555;
        transition: background 0.3s;
    }

    .latest-activities ul li:hover {
        background-color: #f1f8ff;
    }

    a {
        cursor: pointer;
    }

    .dropdown {
            position: relative;
        }

        .dropdown-btn {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            color: #888;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 30px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            overflow: hidden;
            display: none;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu div {
            padding: 10px 15px;
            font-size: 14px;
            color: #555;
            cursor: pointer;
        }

        .dropdown-menu div:hover {
            background-color: #f4f4f4;
        }

        .activity-list {
            margin-top: 20px;
            height:0px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .activity-item:last-child {
            margin-bottom: 0;
        }

        .activity-time {
            font-size: 14px;
            color: #888;
            width: 120px;
            text-align: right;
            margin-right: 15px;
        }

        .activity-marker {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .activity-text {
            font-size: 14px;
            color: #555;
        }

        .activity-text strong {
            font-weight: bold;
            color: #333;
        }

        .marker-green { background-color: #28a745; }
        .marker-red { background-color: #dc3545; }
        .marker-blue { background-color: #007bff; }
        .marker-yellow { background-color: #ffc107; }
        .marker-gray { background-color: #6c757d; }

        .cardactivity {
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 400px;
            padding: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ececec;
            padding-bottom: 10px;
        }

        .card-header h2 {
            font-size: 18px;
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .dropdown-option{
            cursor:pointer;
        }

    .card-recent{
        height:auto;
    }

</style>
</head>
<body>

    <!-- Content -->
    <div class="content-container">
        <div class="welcome-message">
            Welcome, <?php echo $_SESSION['fullName'];?>! 
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-icon">
                    <i class="fa-solid fa-calendar-week"></i>
                </div>
                <div class="card-content">
                    <h2>Total Received</h2>
                    <p id="total-count">0</p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="card-content">
                    <h2>Currency Conversion</h2>
                    <p id="currency-conversion">Fetching exchange rate...</p>
                </div>
            </div>
        </div>

        <!-- Latest Activities Section -->
        <!-- <div class="latest-activities">
            <h2>Latest Activities</h2>
            <ul>
                <li><strong>User1</strong> completed task #3</li>
                <li><strong>User2</strong> uploaded a new document</li>
                <li><strong>User3</strong> finished a review</li>
                <li><strong>User4</strong> started a new project</li>
            </ul>
        </div> -->

        <div class="card-recent" style="margin-top:40px;">
            <div class="card-header">
                    <h2 id="activity-title">Recent Activity | <span id="time-period">Today</span></h2>
                    <div class="dropdown" >
                        <button class="dropdown-btn" onclick="toggleDropdown(event);" style="pointer-events: auto;">...</button>
                        <div class="dropdown-menu">
                            <div class="dropdown-option" style="pointer-events: auto;" onclick="updateTimePeriod('Today', event)">Today</div>
                            <div class="dropdown-option" style="pointer-events: auto;" onclick="updateTimePeriod('This Month', event)">This Month</div>
                            <div class="dropdown-option" style="pointer-events: auto;" onclick="updateTimePeriod('This Year', event)">This Year</div>
                        </div>
                    </div>
                </div>
            <div class="card-content-recent">
               

                <div class="activity-list">
                    <?php
                    while ($data1 = $database->fetch_array($result1)) {
                        $daysAgo = $data1['DaysAgo'];
                        $datemodified = $data1['DateModified'];
                        $status = $data1['Status'];
                        
                        // Generate the time ago text
                        if ($daysAgo == 0) {
                            $currentDateTime = date("Y-m-d h:i A");

                            // Convert current date/time and the DateModified to timestamps
                            $currentTimestamp = strtotime($currentDateTime);
                            $datemodified = $data1['DateModified']; // Assuming it's in 'Y-m-d H:i:s' format
                            $modifiedTimestamp = strtotime($datemodified);
                            // Calculate the time difference
                            $timeDifference = $currentTimestamp - $modifiedTimestamp;
                            
                            if ($timeDifference < 60) {
                                // Less than 60 seconds
                                $timeAgoText = $timeDifference . ' seconds ago';
                            } elseif ($timeDifference < 3600) {
                                // Less than 60 minutes (but more than 60 seconds)
                                $minutes = floor($timeDifference / 60);
                                $timeAgoText = $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
                            } elseif ($timeDifference < 86400) {
                                // Less than 24 hours (but more than 1 hour)
                                $hours = floor($timeDifference / 3600);
                                $timeAgoText = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
                            } elseif ($timeDifference < 2592000) {
                                // Less than 30 days (but more than 24 hours)
                                $days = floor($timeDifference / 86400);
                                $timeAgoText = $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
                            } elseif ($timeDifference < 31536000) {
                                // Less than a year (but more than 30 days)
                                $months = floor($timeDifference / 2592000);
                                $timeAgoText = $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
                            } else {
                                // More than a year
                                $years = floor($timeDifference / 31536000);
                                $timeAgoText = $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
                            }
                            

                            $activityDate = date("Y-m-d"); // Today's date
                        } elseif ($daysAgo == 1) {
                            $timeAgoText = 'Yesterday';
                            $activityDate = date("Y-m-d", strtotime("-1 day")); // Yesterday's date
                        } else {
                            $timeAgoText = "$daysAgo days ago";
                            $activityDate = date("Y-m-d", strtotime("-$daysAgo days")); // Calculate date based on daysAgo
                        }

                        // Optionally, you can map the status to different colors or markers
                        $markerClass = 'marker-gray'; // Default
                        if (strpos(strtolower($status), 'completed') !== false) {
                            $markerClass = 'marker-green';
                        } elseif (strpos(strtolower($status), 'pending') !== false) {
                            $markerClass = 'marker-yellow';
                        } elseif (strpos(strtolower($status), 'received') !== false) {
                            $markerClass = 'marker-blue';
                        }

                        // Output the activity item HTML dynamically with the data-date attribute
                        echo '<div class="activity-item" data-date="' . htmlspecialchars($activityDate) . '">
                                <div class="activity-time">' . htmlspecialchars($timeAgoText) . '</div>
                                <div class="activity-marker ' . htmlspecialchars($markerClass) . '"></div>
                                <div class="activity-text">' . htmlspecialchars($status) . '</div>
                            </div>';
                    }
                    ?>
                </div>

            </div>
        </div>

    </div>
</body>
</html>


<script>

        document.querySelector('.dropdown-btn').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('active');
        });




    // Fetch Currency Exchange Rate from USD to PHP
        async function fetchExchangeRate() {
            try {
                const apiKey = '116e86e6c94f9e2c9492dccf';  // Replace with your API key from ExchangeRate-API or Fixer.io
                const response = await fetch(`https://v6.exchangerate-api.com/v6/${apiKey}/latest/USD`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                const rate = data.conversion_rates.PHP;  // Fetch PHP rate from the response

                // Display the conversion result
                document.getElementById('currency-conversion').textContent = 
                    `1 USD = ${rate.toFixed(2)} PHP`;
            } catch (error) {
                document.getElementById('currency-conversion').textContent = 'Error';
                console.error('Currency API Error:', error);
            }
        }


        // Call APIs on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetchExchangeRate() ;
        });

        function animateCountUp(elementId, start, end, duration) {
            const element = document.getElementById(elementId);
            const increment = (end - start) / (duration / 16); // 16ms per frame for 60fps
            let currentValue = start;

            const animate = () => {
                currentValue += increment;
                if (currentValue >= end) {
                    element.textContent = end; // Ensure it ends with the exact number
                } else {
                    element.textContent = Math.round(currentValue);
                    requestAnimationFrame(animate);
                }
            };

            animate();
        }

        // Fetch data and animate the counter
        document.addEventListener('DOMContentLoaded', () => {
            const totalCount = <?php echo $data['TotalCount']; ?>; // PHP data
            animateCountUp('total-count', 0, totalCount, 1000); 
        });

        function toggleDropdown(event) {
            const dropdownMenu = document.querySelector(".dropdown-menu");
            const dropdown = document.querySelector(".dropdown");

            // Prevent the click from propagating to the document (to avoid closing the dropdown immediately)
            event.stopPropagation();

            // Toggle the dropdown visibility
            dropdownMenu.style.display = (dropdownMenu.style.display === "block" ? "none" : "block");

            // Add the outside click listener to close the dropdown
            document.addEventListener("click", closeDropdownOnOutsideClick);

            function closeDropdownOnOutsideClick(event) {
                if (!dropdown.contains(event.target)) {
                    dropdownMenu.style.display = "none"; // Hide the dropdown if click is outside
                    // Remove the outside click listener
                    document.removeEventListener("click", closeDropdownOnOutsideClick);
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Set the default to "Today" when the page loads
            updateTimePeriod('Today', event);
        });

        function toggleDropdown(event) {
            const dropdownMenu = document.querySelector(".dropdown-menu");
            const dropdown = document.querySelector(".dropdown");

            // Prevent the click from propagating to the document (to avoid closing the dropdown immediately)
            event.stopPropagation();

            // Toggle the dropdown visibility
            dropdownMenu.style.display = (dropdownMenu.style.display === "block" ? "none" : "block");

            // Add the outside click listener to close the dropdown
            document.addEventListener("click", closeDropdownOnOutsideClick);

            function closeDropdownOnOutsideClick(event) {
                if (!dropdown.contains(event.target)) {
                    dropdownMenu.style.display = "none"; // Hide the dropdown if click is outside
                    // Remove the outside click listener
                    document.removeEventListener("click", closeDropdownOnOutsideClick);
                }
            }
        }

        function updateTimePeriod(period, event) {
            // Get the current date
            const currentDate = new Date();
            let displayText = '';
            let filterDate = '';

            // Format the time period based on the selected option
            if (period === 'Today') {
                // Show today's date in the format "YYYY-MM-DD"
                displayText = currentDate.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
                filterDate = currentDate.toISOString().split('T')[0]; // "YYYY-MM-DD"
            } else if (period === 'This Month') {
                // Show the current month in the format "Month Year"
                displayText = currentDate.toLocaleDateString(undefined, { year: 'numeric', month: 'long' });
                filterDate = currentDate.getFullYear() + '-' + (currentDate.getMonth() + 1).toString().padStart(2, '0'); // "YYYY-MM"
            } else if (period === 'This Year') {
                // Show the current year
                displayText = currentDate.getFullYear();
                filterDate = currentDate.getFullYear().toString(); // "YYYY"
            }

            // Update the time period text
            const timePeriodElement = document.getElementById("time-period");
            timePeriodElement.textContent = period;

            // Filter the activity items based on the selected time period
            const activityItems = document.querySelectorAll('.activity-item');

            activityItems.forEach(function(item) {
                const activityDate = item.getAttribute('data-date');

                // Compare based on the selected period
                if (period === 'Today') {
                    if (activityDate === filterDate) {
                        item.style.display = 'flex'; // Show today's activities
                    } else {
                        item.style.display = 'none'; // Hide activities not from today
                    }
                } else if (period === 'This Month') {
                    if (activityDate.startsWith(filterDate)) {
                        item.style.display = 'flex'; // Show activities from this month
                    } else {
                        item.style.display = 'none'; // Hide activities not from this month
                    }
                } else if (period === 'This Year') {
                    if (activityDate.startsWith(filterDate)) {
                        item.style.display = 'flex'; // Show activities from this year
                    } else {
                        item.style.display = 'none'; // Hide activities not from this year
                    }
                }
            });

            // Close the dropdown
            const dropdownMenu = document.querySelector(".dropdown-menu");
            dropdownMenu.style.display = "none"; // Hide the dropdown

            // Stop further propagation of click event (so it doesn't trigger the document listener)
            event.stopPropagation();
        }








    </script>

