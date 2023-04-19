<?php
session_start();

// Generate a new CSRF token and save it in the session
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate the CSRF token to prevent cross-site request forgery
  if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    // Invalid CSRF token
    // Redirect the user back to the Sign Up form with an error message
    header("Location: sign-up.php?error=csrf");
    exit();
  }

  $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
  $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
  $confirm_password = filter_var($_POST["confirm-password"], FILTER_SANITIZE_STRING);
  $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

  // Validate username
  if (empty($username) || !preg_match("/^[a-zA-Z0-9]+$/", $username)) {
    // Username is not valid
    // Redirect the user back to the Sign Up form with an error message
    header("Location: sign-up.php?error=username");
    exit();
  }

  // Validate password
  $password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
  if (!preg_match($password_regex, $password) || strpos($password, $username) !== false || $password !== $confirm_password) {
    // Password does not meet the requirements
    // Redirect the user back to the Sign Up form with an error message
    header("Location: sign-up.php?error=password");
    exit();
  }

  // Validate email
  if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Email address is not valid
    // Redirect the user back to the Sign Up form with an error message
    header("Location: sign-up.php?error=email");
    exit();
  }

  // Save the valid form data to the database using prepared statements
  $host = "localhost";
  $username = "your_username";
  $password = "your_password";
  $dbname = "your_database_name";
  $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

  try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $email]);

    // Display a success message to the user
    echo "Sign Up successful!";
  } catch (PDOException $e) {
    // Something went wrong with the database connection
    // Redirect the user back to the Sign Up form with an error message
    header("Location: sign-up.php?error=database");
    exit();
  }
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My Website</title>
	<link rel="stylesheet" href="style.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=IM+Fell+English:ital@1&display=swap" rel="stylesheet">
</head>
<body>
	<header>
		<h1>Welcome to My Website</h1>
		<nav>
			<ul>
				<li><a href="#">Home</a></li>
				<li><a href="#">Browse Games</a></li>
				<li><a href="#">Sign In</a></li>
				<li><a href="#">Sign Up</a></li>
			</ul>
		</nav>
	</header>
	
<main>
  <h2>Sign Up</h2>
  <?php
  // Check for any error messages from the previous form submission
  if (isset($_GET["error"])) {
    $error_message = "An error occurred. Please try again later.";

    switch ($_GET["error"]) {
      case "csrf":
        $error_message = "Invalid CSRF token. Please try again.";
        break;
      case "username":
        $error_message = "Invalid username. Please enter a valid username (alphanumeric characters only).";
        break;
      case "password":
        $error_message = "Invalid password. Please enter a valid password that is at least 8 characters long and includes at least one uppercase letter, one lowercase letter, one number, and one special character.";
        break;
      case "confirm":
        $error_message = "Passwords do not match. Please try again.";
        break;
      case "email":
        $error_message = "Invalid email address. Please enter a valid email address.";
        break;
      case "database":
        $error_message = "Something went wrong. Please try again later.";
        break;
    }

    // Display the error message to the user
    echo "<p class='error-message'>$error_message</p>";
  }
  ?>
  <p>Please enter your login information to sign up</p>
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <label for="confirm-password">Confirm Password:</label>
    <input type="password" id="confirm-password" name="confirm-password" required><br><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>
    <input type="submit" value="Sign Up">
  </form>
</main>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('form').submit(function(event) {
      var password = $('#password').val();
      var confirm_password = $('#confirm-password').val();
      var email = $('#email').val();
      var username = $('#username').val();
      var password_regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
      var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!password_regex.test(password) || password.indexOf(username) !== -1) {
        event.preventDefault();
        $('#password-error').text('Please enter a valid password (minimum 8 characters, including at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character, and do not use part of your username).');
      } else {
        $('#password-error').text('');
      }
      if (password !== confirm_password) {
        event.preventDefault();
        $('#confirm-password-error').text('Passwords do not match.');
      } else {
        $('#confirm-password-error').text('');
      }
      if (!email_regex.test(email)) {
        event.preventDefault();
        $('#email-error').text('Please enter a valid email address.');
      } else {
        $('#email-error').text('');
      }
    });
  });
</script>
	<footer>
		<p>&copy; 2023 My Website</p>
	</footer>
	
	<script src="script.js"></script>
</body>
</html>

