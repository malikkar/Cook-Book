<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About us</title>

  <!-- style -->
  <link rel="stylesheet" href="/assets/css/stylemain.css" />

  <!-- icons -->
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
        rel="stylesheet" />
</head>
<body>

<!-- Navigation Bar -->
<div class="topnav">
    <div class="logo">
        <img src="/food/logo.png" alt="Cook Book.">
    </div>
    <div>
        <a href="/">Home</a>
        <?php if (is_logged_in()): ?>
            <a href="/profile.php">Profile</a>
        <?php else: ?>
            <a href="/signin.php">Login</a>
        <?php endif; ?>
        <a href="/aboutus.php">About us</a>
    </div>
</div>

<div class="container">
  <h1>About Us</h1>
  <p>Our cookbook website is dedicated to providing you with the best recipes from around the world. We believe that cooking is not just about following a recipe; it's about sharing stories, traditions, and experiences.</p>
  <p>Our team of passionate chefs and food enthusiasts work tirelessly to curate a collection of delicious recipes that cater to all tastes and dietary needs. Whether you're a seasoned cook or just starting out, you'll find something here to inspire your next meal.</p>
  <p>Join us on our culinary journey and let's cook up some amazing dishes together!</p><br />

  <img src="\food\family.jpg" alt="cooking" />
</div>
<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
?>