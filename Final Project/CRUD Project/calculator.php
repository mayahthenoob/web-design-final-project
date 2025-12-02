<?php
session_start();
// Standardized Database Credentials for InfinityFree (for session consistency)
$host = "sql300.infinityfree.com";
$db   = "if0_40502206_flavorful";
$user = "if0_40502206";
$pass = "noelbest2025";

// PDO Options (included but not used for calculator logic)
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    // We don't connect here as it's not strictly necessary for a calculator, 
    // but the structure is ready if needed.
    // $pdo = new PDO($dsn, $user, $pass, $options); 
} catch (PDOException $e) {
    error_log("DB connection check failed: " . $e->getMessage());
}

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: calculator.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>A Simple Calculator</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Global Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background-color: #fff; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
    
    /* Header Styles (Consistent) */
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; }
    header .logo { font-size: 24px; font-weight: 700; text-decoration: none; color: #111; flex-shrink: 0; }
    nav { display: flex; gap: 20px; align-items: center; }
    nav a { text-decoration: none; color: #111; font-weight: 600; padding: 5px 10px; border-radius: 4px; transition: background-color 0.3s; }
    nav a:hover { background-color: #f0f0f0; }

    .profile-icon { display: flex; align-items: center; }
    .profile-btn {
        background-color: #f59e0b;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
    }
    .profile-icon button {
        margin-left: 10px; 
        padding: 5px 10px; 
        background-color: #e53e3e; 
        color: white; 
        border: none; 
        border-radius: 4px; 
        cursor: pointer; 
        font-size: 14px;
        transition: background-color 0.3s;
    }
    .profile-icon button:hover { background-color: #c53030; }

    /* Main Calculator Section */
    section {
      text-align: center;
      padding: 40px 20px;
      flex-grow: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }

    #calculator {
      border: 4px solid #f59e0b;
      border-radius: 12px;
      display: inline-block;
      padding: 20px;
      background: #eee;
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
      max-width: 300px; /* Limit calculator size */
      width: 100%;
    }

    #display {
      margin-bottom: 20px;
      width: 100%; /* Make display fluid within the container */
      height: 60px;
      border: 2px solid #333;
      border-radius: 8px;
      font-size: 2em;
      text-align: right;
      padding: 10px;
      box-sizing: border-box;
      background: white;
      color: #111;
    }
    
    #memory {
        display: none; /* Keep memory hidden from UI */
    }

    .keypad {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
    }

    .keypad input[type="button"] {
      padding: 15px;
      font-size: 1.2em;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.1s;
      font-weight: 600;
    }

    /* Functional keys (Operators, Clear, etc) */
    .keypad input[type="button"][value="C"],
    .keypad input[type="button"][value="+/-"],
    .keypad input[type="button"][value="%"],
    .keypad input[type="button"][value="/"],
    .keypad input[type="button"][value="*"],
    .keypad input[type="button"][value="-"],
    .keypad input[type="button"][value="+"] {
      background-color: #d97706; /* Orange color */
      color: white;
    }

    /* Equals key */
    .keypad input[type="button"][value="="] {
      background-color: #10b981; /* Green color */
      color: white;
      grid-column: span 2; /* Make it wider */
    }

    /* Number keys */
    .keypad input[type="button"]:not([value="="]):not([value="C"]):not([value="+/-"]):not([value="%"]):not([value="/"]):not([value="*"]):not([value="-"]):not([value="+"]) {
      background-color: #fff;
      color: #333;
    }

    .keypad input[type="button"]:active {
      transform: scale(0.98);
    }
    
    /* Footer Styles */
    footer {
        text-align: center;
        padding: 20px;
        background: #111;
        color: #eee;
        margin-top: auto;
    }
    
    /* Message Modal (for replacing alert()) */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none; /* Hidden by default */
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        text-align: center;
        max-width: 300px;
    }
    .modal-content button {
        background-color: #f59e0b;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        margin-top: 15px;
        cursor: pointer;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
        nav { width: 100%; justify-content: space-between; margin-top: 10px; }
    }
  </style>
</head>
<body>
  <header>
    <a href="index.php" class="logo">Flavorful</a>
    <nav>
      <a href="about.php">About</a>
      <a href="prices.php">Prices</a>
      <a href="socials.php">Socials</a>
      <a href="workers.php">Workers</a>
      <a href="buy-now.php">Order</a>

      <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
      <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>

      <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
        <a href="buy-now.php" class="profile-btn" title="View Order Options"> 
          <span><?= htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
        </a>
        <form method="POST" style="display:inline;">
          <button type="submit" name="logout">Logout</button>
        </form>
      </div>
    </nav>
  </header>

  <section>
    <h1>A Simple Calculator</h1>
    <div id="calculator">
      <input type="text" id="display" name="display" readonly>
      <input type="hidden" id="memory" name="memory">

      <div class="keypad">
        <input type="button" id="clear" name="clear" value="C" onclick="clearMe()">
        <input type="button" id="sign" name="sign" value="+/-" onclick="switchSigns()">
        <input type="button" id="percent" name="percent" value="%" onclick="percentMe()">
        <input type="button" id="divide" name="divide" value="/" onclick="displayMe(this.value)">

        <input type="button" id="seven" name="seven" value="7" onclick="displayMe(this.value)">
        <input type="button" id="eight" name="eight" value="8" onclick="displayMe(this.value)">
        <input type="button" id="nine" name="nine" value="9" onclick="displayMe(this.value)">
        <input type="button" id="multiply" name="multiply" value="*" onclick="displayMe(this.value)">

        <input type="button" id="four" name="four" value="4" onclick="displayMe(this.value)">
        <input type="button" id="five" name="five" value="5" onclick="displayMe(this.value)">
        <input type="button" id="six" name="six" value="6" onclick="displayMe(this.value)">
        <input type="button" id="subtract" name="subtract" value="-" onclick="displayMe(this.value)">

        <input type="button" id="one" name="one" value="1" onclick="displayMe(this.value)">
        <input type="button" id="two" name="two" value="2" onclick="displayMe(this.value)">
        <input type="button" id="three" name="three" value="3" onclick="displayMe(this.value)">
        <input type="button" id="add" name="add" value="+" onclick="displayMe(this.value)">

        <!-- Replaced 'M' (Memory) with '0' for a standard layout -->
        <input type="button" id="zero" name="zero" value="0" onclick="displayMe(this.value)"> 
        <input type="button" id="decimal" name="decimal" value="." onclick="displayMe(this.value)">
        <input type="button" id="equals" name="equals" value="=" onclick="calculate()">
      </div>
    </div>
  </section>

  <footer>
    Copyright &copy; 2025 Flavorful
  </footer>
  
  <!-- Message Modal -->
  <div id="messageModal" class="modal">
    <div class="modal-content">
        <p id="modalMessage"></p>
        <button onclick="closeModal()">OK</button>
    </div>
  </div>

  <script> 
    let currentMemory = '';

    function showModal(message) {
        document.getElementById('modalMessage').innerText = message;
        document.getElementById('messageModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('messageModal').style.display = 'none';
    }

    function displayMe(inVal) {
      document.getElementById('display').value += inVal;
    }

    function calculate() {
      const display = document.getElementById('display');
      try {
        // Using Function() for safe evaluation (better than eval())
        display.value = Function("return " + display.value)(); 
      } catch (e) {
        showModal('Invalid expression');
        display.value = ''; // Clear display on error
      }
    }

    function clearMe() {
      document.getElementById('display').value = '';
      currentMemory = '';
      // document.getElementById('memory').value = ''; // Since memory is hidden
    }

    function switchSigns() {
      const display = document.getElementById('display');
      if (display.value && !isNaN(parseFloat(display.value))) {
        display.value = parseFloat(display.value) * -1;
      }
    }

    function percentMe() {
      const display = document.getElementById('display');
      if (display.value && !isNaN(parseFloat(display.value))) {
        display.value = parseFloat(display.value) / 100;
      }
    }

  </script>
</body>
</html>