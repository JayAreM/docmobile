<!DOCTYPE html>
<!-- Coding By CodingNepal - codingnepalweb.com -->
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Navigation Bar with Search Box</title>
    <link rel="stylesheet" href="style.css" />
    <!-- Unicons CSS -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
   <script src="script.js" defer></script>
  </head>
  <body>
    <nav class="nav">
      <i class="uil uil-bars navOpenBtn"></i>
      <a href="#" class="logo">DT Receiver</a>
      <ul class="nav-links">
        <i class="uil uil-times navCloseBtn"></i>
        <li><a href="#">Home</a></li>
        <li><a href="#">QR Auto Receive</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Contact Us</a></li>
      </ul>
      <i class="uil uil-search search-icon" id="searchIcon"></i>
      <div class="search-box">
        <i class="uil uil-search search-icon"></i>
        <input type="text" placeholder="Search Tracking Number..." />
      </div>
    </nav>
  </body>
</html>