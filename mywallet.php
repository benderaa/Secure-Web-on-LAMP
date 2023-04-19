<!DOCTYPE html>
<html>
<head>
  <title>My Wallet</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>My Wallet</h1>
    <nav>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Transactions</a></li>
        <li><a href="#">Settings</a></li>
        <li><a href="#">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h2>My Balance</h2>
    <p>$100.00</p>

    <h2>Deposit</h2>
    <form method="post" action="deposit.php">
      <label for="amount">Amount:</label>
      <input type="number" id="amount" name="amount" required><br><br>
      <input type="submit" value="Deposit">
    </form>

    <h2>Withdraw</h2>
    <form method="post" action="withdraw.php">
      <label for="amount">Amount:</label>
      <input type="number" id="amount" name="amount" required><br><br>
      <input type="submit" value="Withdraw">
    </form>
  </main>

  <footer>
    <p>&copy; 2023 My Wallet</p>
  </footer>
</body>
</html>
