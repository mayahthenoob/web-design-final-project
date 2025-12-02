<?php
session_start();

$dsn = "mysql:host=sql300.infinityfree.com;dbname=dbname"; //might have to change these to what th website gives
$username = "noels";
$password = "noelbest2025"

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: workers.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Workers</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <style id="FLAVORFUL_GLOBAL_STYLE">
    /* Global Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box; 
      }
   
    body { 
        font-family: 'Inter', sans-serif;
        background-color: #fff; 
        color: #111; 
        line-height: 1.6; 
        display: flex; 
        flex-direction: column; 
        min-height: 100vh; 
      }
    
    header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 20px 60px; 
        background: #fff; 
        border-bottom: 1px solid #eee;
        position: sticky; top: 0;
        z-index: 1000; 
        flex-wrap: wrap; 
      }

    header .logo { 
        font-size: 24px; 
        font-weight: 700;
        text-decoration: none;
        color: #111;
        flex-shrink: 0; 
      }

    nav { 
        display: flex; 
        gap: 20px; 
        align-items: center;
      }

    nav a { 
        text-decoration: none;
        color: #111;
        font-weight: 600;   /*tomake changes to*/
        padding: 5px 10px; 
       border-radius: 4px;
       transition: background-color 0.3s; 
      }

    nav a:hover { 
      background-color: #f0f0f0; 
    }

    .profile-icon {
       display: flex;
        align-items: center; 
      }

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

    .profile-icon button:hover { 
      background-color: #c53030;
     }

    .workers-section {
        padding: 60px;
        text-align: center;
        flex-grow: 1;
    }
    .workers-section h2 {
        font-size: 2.5em;
        color: #d97706;
        margin-bottom: 50px;
    }
    
    .card-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .card {
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        padding: 20px;
        transition: transform 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }

    .card img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid #f59e0b;
    }

    .card h4 {
        font-size: 1.5em;
        margin-bottom: 5px;
    }

    .card span {
        display: block;
        color: #d97706;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .card p {
        font-size: 0.9em;
        color: #555;
        margin-bottom: 15px;
        min-height: 40px;
    }

    .socials a {
        color: #111;
        font-size: 1.5em;
        margin: 0 5px;
        transition: color 0.3s;
    }

    .socials a:hover {
        color: #f59e0b;
    }

    footer {
        text-align: center;
        padding: 20px;
        background: #111;
        color: #eee;
        margin-top: auto;
    }

    /* RESPONSIVENESS */  /*fix responsiveness*/
    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
        nav { width: 100%; justify-content: space-between; margin-top: 10px; }
        .workers-section {
            padding: 40px 20px;
        }
        .card-container {
            grid-template-columns: 1fr;
        }
    }
  </style>
</head>
<body>
   <header>
    <h1><a href="index.php" class="logo">Flavorful</a></h1>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="workers.php">Workers</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="prices.php">Prices</a>
        <a href="socials.php">Socials</a>
        <a href="buy-now.php">Buy Now</a>
    </nav>
    <div class="account-links">
        <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
        <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>
        
      <div class="profile-icon" style="display:none;">
        <a href="buy-now.php" class="profile-btn" title="View Order Options"> 
            <span>US</span>
        </a>
        <form method="POST" style="display:inline;">
            <button type="submit" name="logout">Logout</button>
        </form>
      </div>
    </nav>
  </header>

  <section class="workers-section">
    <h2>Meet Our Dedicated Team</h2>
    <div class="card-container">
      
      <!-- Manager -->
      <div class="card">
        <img src="Manager" alt="Manager">
        <h4>The Manager</h4>
        <span>Founder & CEO</span>
        <p>Oversees all operations, strategy, and business development for Flavorful.</p>
        <div class="socials">
          <a href="#"><i class="bx bxl-whatsapp"></i></a>
          <a href="#"><i class="bx bxl-facebook"></i></a>
          <a href="#"><i class="bx bxl-instagram"></i></a>
        </div>
      </div>

      <!-- Noel -->
      <div class="card">
        <img src="#" atl="Noel Pic">
        <h4>Noel</h4>
        <span>Lead Production</span>
        <p>Manages the daily production line and ensures quality control of every batch.</p>
        <div class="socials">
          <a href="#"><i class="bx bxl-whatsapp"></i></a>
          <a href="#"><i class="bx bxl-facebook"></i></a>
          <a href="#"><i class="bx bxl-instagram"></i></a>
        </div>
      </div>

      <!-- Allana -->
      <div class="card">
        <img src="Allana" alt="Allana"> <!--put in the extension for the images for the cards-->
        <h4>Allana</h4>
        <span>Mixer</span>
        <p>Responsible for mixing flavors and ensuring product consistency, making sure every pennacool tastes perfect.</p>
        <div class="socials">
          <a href="#"><i class="bx bxl-whatsapp"></i></a>
          <a href="#"><i class="bx bxl-facebook"></i></a>
          <a href="#"><i class="bx bxl-instagram"></i></a>
        </div>
      </div>

      <!-- Ezekiel -->
      <div class="card">
        <img src="Ezekiel" alt="Ezekiel">
        <h4>Ezekiel</h4>
        <span>Sales & Delivery</span>
        <p>Handles the bulk of sales and deliveries to supermarkets and local shops efficiently.</p>
        <div class="socials">
          <a href="#"><i class="bx bxl-whatsapp"></i></a>
          <a href="#"><i class="bx bxl-facebook"></i></a>
          <a href="#"><i class="bx bxl-instagram"></i></a>
        </div>
      </div>
      
    </div>
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>