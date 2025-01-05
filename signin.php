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
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<div class="container">
    <header>Sign In</header>
    <form id="signinForm" action="signin_process.php" method="POST">
            <!-- email -->
            <div class="field email-field">
                <div class="input-field">
                    <input type="email" placeholder="Enter your email" class="email" name="email" required />
                </div>
                <span class="error email-error">
                    <i class="bx bx-error-circle error-icon"></i>
                    <p class="error-text">Please, enter a valid email</p>
                </span>
            </div>

            <!-- password -->
            <div class="field enter-password">
                <div class="input-field">
                    <input type="password" placeholder="Enter your password" class="password" name="password" required />
                    <i class="bx bx-hide show-hide"></i>
                </div>
                <span class="error password-error">
                    <i class="bx bx-error-circle error-icon"></i>
                    <p class="error-text">Wrong password</p>
                </span>
            </div>

            <!-- submit button -->
            <div class="input-field button">
                <input type="submit" value="Sign In" />
            </div>

            <!-- if account doesn't exist -->
            <div class="ifHaveAccount">
                <br /><p>Don't have an account? <a href="signup.php">SignUp</a></p>
            </div>
        </form>
</div>
<!-- JavaScript -->
<script src="/assets/js/signin.js"></script>
</body>
</html>
