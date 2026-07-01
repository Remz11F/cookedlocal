<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Inquiries</title>
  <style>
    /* Body of Website */
    body {
      font-family: Arial, sans-serif;
      background: #fff9f4;
      padding: 40px;
    }
    /* Header of Website */
    h2 {
      color: #f35920;
      text-align: center;
    }
    /* Back Button */
    .back-button {
      display: block;
      width: fit-content;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #fe964f;
      color: white;
      text-align: center;
      text-decoration: none;
      font-weight: bold;
      border-radius: 6px;
      transition: background 0.3s;
    }
    /* Changes button colour when hovering on it */
    .back-button:hover {
      background-color: #f35920;
    }
    /* Inquiry section struture */
    .inquiry {
      background: #fff;
      padding: 20px;
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    /* Inquiry heading */
    .inquiry h4 {
      margin: 0 0 5px;
    }
    /* Inquiry paragraph */
    .inquiry p {
      margin: 4px 0;
    }
  </style>
</head>
<body>
  <h2>Submitted Inquiries</h2>
  <a href="admin.html" class="back-button">Admin Page</a>

  <?php
  // Database connection with details (username, password and database)
  $conn = new mysqli("localhost", "root", "", "cookedlocal");
  // Checks the connection of the database
  if ($conn->connect_error) {
    // Error message if connection does fail
    die("Connection Failed");
  }

  // SQL select statment to select inquiries table 
  $result = $conn->query("SELECT * FROM inquiries ORDER BY id DESC");
  // Checks if there are any results within the table
  if ($result->num_rows > 0) {
    // Loop through each row
    while ($row = $result->fetch_assoc()) {
      // html code is displayed (name, email, message) and containts the infomration from database
      echo "<div class='inquiry'>
              <h4>" . htmlspecialchars($row['subject']) . "</h4>
              <p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>
              <p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>
              <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($row['message'])) . "</p>
            </div>";
    }
  } else {
    // If there is no inquiries display a message 
    echo "<p style='text-align:center;'>There are currently no inquiries</p>";
  }
  $conn->close();
  ?>

</body>
</html>
