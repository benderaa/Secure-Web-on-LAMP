<?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];
  
  // Connect to the database
  $servername = "localhost";
  $dbusername = "username";
  $dbpassword = "password";
  $dbname = "mydb";
  $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

  // Check if the username is valid
  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 1) {
    // Username is valid, check if password is correct
    $row = $result->fetch_assoc();
    $hashed_password = $row["password"];
    if (password_verify($password, $hashed_password)) {
      // Password is correct, redirect to home page
      header("Location: home.php");
      exit();
    } else {
      // Password is incorrect, display an error message
      echo "Invalid username or password";
    }
  } else {
    // Username is not valid, display an error message
    echo "Invalid username or password";
  }

  $stmt->close();
  $conn->close();
}
?>