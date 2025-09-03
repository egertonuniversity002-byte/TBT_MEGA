<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Matrix Platform</title>
  <link rel="stylesheet" href="auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    .message {
      display: none;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 0.9rem;
      animation: fadeIn 0.4s ease-in-out;
    }
    .message.success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .message.error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(-5px);} to{opacity:1; transform:translateY(0);} }
  </style>
</head>
<body>
  <!-- Loader -->
  <div class="loader-overlay" id="loader">
    <div class="meta-logo">
      <svg viewBox="0 0 100 60" xmlns="http://www.w3.org/2000/svg">
        <path class="infinity" d="M10,30 C20,10 40,10 50,30 C60,50 80,50 90,30" stroke="#38bdf8" stroke-width="6" stroke-linecap="round"/>
      </svg>
    </div>
    <p>Logging you in...</p>
  </div>

  <div class="auth-container">
    <h2><i class="fa-solid fa-right-to-bracket"></i> Login</h2>

    <!-- Styled Feedback -->
    <div id="messageBox" class="message"></div>

    <form id="loginForm">
      <!-- Email -->
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" id="email" name="email" placeholder="Email Address" required>
      </div>

      <!-- Password -->
      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" id="password" name="password" placeholder="Password" required>
      </div>

      <button type="submit" class="btn">Login</button>
      <!-- Forgot password link -->
      <p class="forgot">
        <a href="reset_password.php">Forgot Password?</a>
      </p>
      <p style="margin-top:1rem; font-size:0.9rem;">Don’t have an account? <a href="register.php" style="color:#38bdf8;">Register</a></p>
    </form>
  </div>

  <script>
    const loader = document.getElementById("loader");
    const msgBox = document.getElementById("messageBox");

    // 📌 Handle login form submit
    document.getElementById("loginForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      loader.style.display = "flex";

      const formData = {
        email: document.getElementById("email").value.trim(),
        password: document.getElementById("password").value.trim(),
      };

      try {
        const API_BASE = window.API_BASE || "http://172.17.106.84:5000";
        const res = await fetch(`${API_BASE}/login`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(formData)
        });

        const result = await res.json();
        loader.style.display = "none";

        if (result.status === "success") {
          // Save token for authenticated requests (payments, dashboard)
          localStorage.setItem('token', result.token);
          showMessage("✅ " + result.message, "success");
          // Check user role and redirect accordingly
          const redirectUrl = result.role === 'admin' ? "../auth/admin-dashboard.php" : "../user/dashboard.php";
          setTimeout(() => window.location.href = redirectUrl, 1500);
        } else {
          showMessage("❌ " + (result.message || "Invalid credentials"), "error");
        }
      } catch (err) {
        loader.style.display = "none";
        showMessage("⚠️ Network error. Please try again.", "error");
      }
    });

    // 📌 Show styled messages
    function showMessage(text, type) {
      msgBox.innerText = text;
      msgBox.className = "message " + type;
      msgBox.style.display = "block";
      setTimeout(() => msgBox.style.display = "none", 4000);
    }
  </script>
</body>
</html>
