<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Matrix Platform</title>
  <link rel="stylesheet" href="auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <script src="../config.js"></script>
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
    .message.info { background: #dbeafe; color: #1e40af; border: 1px solid #3b82f6; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(-5px);} to{opacity:1; transform:translateY(0);} }
    
    /* Enhanced loader styles */
    .loader-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.95);
      z-index: 1000;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
    
    .meta-logo {
      width: 80px;
      height: 80px;
      margin-bottom: 20px;
    }
    
    .infinity {
      fill: none;
      stroke-dasharray: 100;
      stroke-dashoffset: 100;
      animation: draw 1.5s infinite ease-in-out;
    }
    
    @keyframes draw {
      to {
        stroke-dashoffset: 0;
      }
    }
    
    .loader-overlay p {
      margin-top: 15px;
      font-size: 16px;
      color: #4b5563;
    }
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
    <p id="loaderText">Logging you in...</p>
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
      <p style="margin-top:1rem; font-size:0.9rem;">Don't have an account? <a href="register.php" style="color:#38bdf8;">Register</a></p>
    </form>
  </div>

  <script>
    const loader = document.getElementById("loader");
    const loaderText = document.getElementById("loaderText");
    const msgBox = document.getElementById("messageBox");

    // Check if user is already logged in
    document.addEventListener('DOMContentLoaded', () => {
      const token = localStorage.getItem('authToken');
      if (token) {
        showMessage("You're already logged in. Redirecting to dashboard...", "info");
        setTimeout(() => {
          window.location.href = "/user/dashboard.php";
        }, 2000);
      }
    });

    // 📌 Handle login form submit
    document.getElementById("loginForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      loader.style.display = "flex";
      loaderText.textContent = "Logging you in...";

      const formData = {
        email: document.getElementById("email").value.trim(),
        password: document.getElementById("password").value.trim(),
      };

      // Basic validation
      if (!formData.email || !formData.password) {
        loader.style.display = "none";
        showMessage("Please fill in all fields", "error");
        return;
      }

      try {
        const API_BASE = window.API_BASE || "https://tbt-mega.onrender.com";
        const res = await fetch(`${API_BASE}/login`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(formData)
        });

        const result = await res.json();
        loader.style.display = "none";

        if (res.ok && result.access_token) {
          // Save token for authenticated requests
          localStorage.setItem('authToken', result.access_token);
          
          // Get user profile to determine role
          try {
            loader.style.display = "flex";
            loaderText.textContent = "Loading your profile...";
            
            const profileRes = await fetch(`${API_BASE}/profile`, {
              method: "GET",
              headers: {
                "Authorization": `Bearer ${result.access_token}`,
                "Content-Type": "application/json"
              }
            });
            
            if (profileRes.ok) {
              const profileData = await profileRes.json();
              
              // Store user data
              localStorage.setItem('userData', JSON.stringify(profileData));
              
              showMessage("✅ Login successful! Redirecting...", "success");
              
              // Determine redirect based on user role or other criteria
              let redirectUrl = "/user/dashboard.php";
              
              // Check if user has admin privileges (you might have a different way to determine this)
              const adminEmails = ["admin@example.com", "superadmin@example.com"]; // Add your admin emails
              if (adminEmails.includes(formData.email.toLowerCase())) {
                redirectUrl = "/admin/dashboard.php";
              }
              
              setTimeout(() => {
                window.location.href = redirectUrl;
              }, 1500);
            } else {
              throw new Error("Failed to fetch user profile");
            }
          } catch (profileError) {
            console.error("Profile fetch error:", profileError);
            showMessage("✅ Login successful! Loading dashboard...", "success");
            setTimeout(() => {
              window.location.href = "/user/dashboard.php";
            }, 1500);
          }
        } else {
          // Handle different error cases
          if (result.detail) {
            if (typeof result.detail === 'string') {
              showMessage("❌ " + result.detail, "error");
            } else if (Array.isArray(result.detail)) {
              showMessage("❌ " + result.detail[0].msg, "error");
            } else {
              showMessage("❌ Invalid credentials", "error");
            }
          } else if (result.message) {
            showMessage("❌ " + result.message, "error");
          } else {
            showMessage("❌ Invalid credentials", "error");
          }
        }
      } catch (err) {
        loader.style.display = "none";
        console.error("Login error:", err);
        
        if (err.name === 'TypeError' && err.message.includes('Failed to fetch')) {
          showMessage("⚠️ Network error. Please check your connection and try again.", "error");
        } else {
          showMessage("⚠️ An unexpected error occurred. Please try again.", "error");
        }
      }
    });

    // 📌 Show styled messages
    function showMessage(text, type) {
      msgBox.innerText = text;
      msgBox.className = "message " + type;
      msgBox.style.display = "block";
      
      // Auto-hide success messages after delay, keep errors visible until user action
      if (type === 'success' || type === 'info') {
        setTimeout(() => {
          msgBox.style.display = "none";
        }, 4000);
      }
    }

    // Handle page visibility change (if user switches tabs during login)
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible' && loader.style.display === 'flex') {
        // Page became visible again, check if we need to update loader text
        loaderText.textContent = "Still processing...";
      }
    });
  </script>
</body>
</html>