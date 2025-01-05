<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>

  <!-- style -->
  <link rel="stylesheet" href="/assets/css/style.css" />

  <!-- icons -->
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
        rel="stylesheet" />

</head>
<body>
<div class="container">
  <header>Login</header>
  <form action="register_process.php" method="POST" id="registrationForm">
        <!-- name -->
        <div class="field name-field">
            <div class="input-field">
                <input type="text" name="name" placeholder="Enter your name" class="name" required />
            </div>
            <span class="error name-error">
                <i class="bx bx-error-circle error-icon"></i>
                <p class="error-text">Please enter a valid name</p>
            </span>
        </div>

        <!-- surname -->
        <div class="field surname-field">
            <div class="input-field">
                <input type="text" name="surname" placeholder="Enter your surname" class="surname" required />
            </div>
            <span class="error surname-error">
                <i class="bx bx-error-circle error-icon"></i>
                <p class="error-text">Please enter a valid surname</p>
            </span>
        </div>

        <!-- email -->
        <div class="field email-field">
            <div class="input-field">
                <input type="email" name="email" placeholder="Enter your email" class="email" required />
            </div>
            <span class="error email-error">
                <i class="bx bx-error-circle error-icon"></i>
                <p class="error-text">Please enter a valid email</p>
            </span>
        </div>

        <!-- password -->
        <div class="field create-password">
            <div class="input-field">
                <input type="password" name="password" placeholder="Create password" class="password" required />
                <i class="bx bx-hide show-hide"></i>
            </div>
            <span class="error password-error">
                <i class="bx bx-error-circle error-icon"></i>
                <p class="error-text">
                    Please enter at least 8 characters with number, symbol, small and capital letter.
                </p>
            </span>
        </div>

        <!-- confirm password -->
        <div class="field confirm-password">
            <div class="input-field">
                <input type="password" name="cPassword" placeholder="Confirm password" class="cPassword" required />
                <i class="bx bx-hide show-hide"></i>
            </div>
            <span class="error cPassword-error">
                <i class="bx bx-error-circle error-icon"></i>
                <p class="error-text">Passwords don't match</p>
            </span>
        </div>

        <!-- role -->
        <div class="field role-field">
            <p>Choose your role:</p>
            <div class="choose">
                <input type="radio" id="reader" name="role" value="reader" required />
                <label for="reader">Reader</label>
            </div>
            <div class="choose">
                <input type="radio" id="author" name="role" value="author" />
                <label for="author">Editor</label>
            </div>
        </div>

        <!-- submit button -->
        <div class="input-field button">
            <input type="submit" value="Register" />
        </div>

        <!-- if account exists -->
        <div class="ifHaveAccount">
            <br /><p>Already have an account? <a href="signin.php">Sign In</a></p>
        </div>
    </form>
</div>
<!-- JavaScript -->
 <script src="/assets/js/register.js"></script>
</body>
</html>
