<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | Matrix Platform</title>
  <link rel="stylesheet" href="auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <!-- Loader -->
  <div class="loader-overlay" id="loader">
    <div class="meta-logo">
      <svg viewBox="0 0 100 60" xmlns="http://www.w3.org/2000/svg">
        <path class="infinity" d="M10,30 C20,10 40,10 50,30 C60,50 80,50 90,30" stroke="#38bdf8" stroke-width="6" stroke-linecap="round"/>
      </svg>
    </div>
    <p>Processing request...</p>
  </div>

  <div class="auth-container">
    <h2><i class="fa-solid fa-key"></i> Forgot Password</h2>

    <!-- Styled Feedback -->
    <div id="messageBox" class="message"></div>

    <form id="forgotForm">
      <!-- Email -->
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" placeholder="Enter your registered email" required>
      </div>

      <button type="submit" class="btn">Send Reset Link</button>
      <p style="margin-top:1rem; font-size:0.9rem;">Remembered your password? 
        <a href="login.php" style="color:#38bdf8;">Login</a>
      </p>
    </form>
  </div>

  <script src="auth.js"></script>
  <script>
    // Attach form handler
    handleForm("forgotForm", "/forgot-password", null, "Requesting reset link...");
  </script>
</body>
</html>
