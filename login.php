<?php
session_start();
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = htmlspecialchars($_POST["username"]);
  $password = htmlspecialchars($_POST["password"]);

  // Validate the CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = "Invalid CSRF token";
  } else {
    // Validate the username and password
    if (empty($username)) {
      $error = "Username is required";
    } elseif (empty($password)) {
      $error = "Password is required";
    } else {
      // Use prepared statements to retrieve the user from the database
      $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
      $stmt->execute([$username]);

      // Get the first row from the result set
      $user = $stmt->fetch();

      // Verify the password
      if ($user && password_verify($password, $user['password'])) {
        // Password is correct, do something with the user data
      } else {
        // Password is incorrect, display an error message
        $error = "Invalid username or password";
      }
    }
  }

  // Regenerate the CSRF token
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    <h2>Sign In</h2>
    <?php if (isset($error)): ?>
      <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="process-form.php">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username"><br><br>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password"><br><br>
      <input type="submit" value="Sign In">
    </form>
  </main>

  <footer>
    <p>&copy; 2023 My Website</p>
  </footer>
  
  <script src="script.js"></script>
</body>
</html>
 