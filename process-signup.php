<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate and sanitize user input
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

  // Get the user's IP address
  $ip_address = $_SERVER['REMOTE_ADDR'];

  // Check that user input meets requirements
  if (strlen($username) < 5 || strlen($username) > 20) {
    // Username is too short or too long, display an error message
    $error_msg = "Username must be between 5 and 20 characters";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Email is not valid, display an error message
    $error_msg = "Invalid email address";
  } elseif (strlen($password) < 8) {
    // Password is too short, display an error message
    $error_msg = "Password must be at least 8 characters";
  } elseif (!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[^A-Za-z0-9]/", $password)) {
    // Password doesn't meet complexity requirements, display an error message
    $error_msg = "Password must include at least one uppercase letter, one lowercase letter, one number, and one special character";
  } else {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Use prepared statements with parameter binding
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $ip_address);
    if (!$stmt->execute()) {
      // Insertion into database failed, display an error message
      $error_msg = "An error occurred while creating your account. Please try again later.";
    } else {
      // Start user session and redirect to home page over HTTPS
      session_start();
      $_SESSION["username"] = $username;
      $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . "/home.php";
      header("Location: " . $redirect_url);
      exit();
    }
  }

  $stmt->close();
  $conn->close();

  if (isset($error_msg)) {
    // An error occurred, display the error message
    echo "<p>Error: " . $error_msg . "</p>";
  }
}
?>
