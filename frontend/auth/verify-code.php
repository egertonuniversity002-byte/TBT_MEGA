<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Reset Code | Matrix Platform</title>
  <link rel="stylesheet" href="auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
  <!-- Loader -->
  <div class="loader-overlay" id="loader">
    <div class="meta-logo">
      <svg viewBox="0 0 100 60" xmlns="http://www.w3.org/2000/svg">
        <path d="M10,30 C20,10 40,10 50,30 C60,50 80,50 90,30"
              stroke="#38bdf8" stroke-width="6" stroke-linecap="round"/>
      </svg>
    </div>
    <p>Verifying code...</p>
  </div>

  <div class="auth-container">
    <h2><i class="fa-solid fa-shield-halved"></i> Verify Reset Code</h2>

    <div id="messageBox" class="message"></div>

    <form id="verifyForm">
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="input-group">
        <i class="fa-solid fa-key"></i>
        <input type="text" name="resetCode" placeholder="Enter reset code" required>
      </div>

      <button type="submit" class="btn">Verify Code</button>
    </form>
  </div>

  <script src="auth.js"></script>
  <script>
    document.getElementById("verifyForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const loader = document.getElementById("loader");
      const msgBox = document.getElementById("messageBox");
      loader.style.display = "flex";

      const form = e.target;
      const payload = {
        email: form.email.value.trim(),
        resetCode: form.resetCode.value.trim()
      };

      try {
        const res = await fetch(`${(window.API_BASE||'https://official-paypal.onrender.com')}/verify-reset-code`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const result = await res.json();
        loader.style.display = "none";

        if (result.status === 'success' && result.reset_token) {
          // Store reset token for the reset password page
          sessionStorage.setItem('resetToken', result.reset_token);
          window.location.href = 'reset_password.php';
        } else {
          msgBox.innerText = "❌ " + (result.message || 'Invalid or expired code');
          msgBox.className = 'message error';
          msgBox.style.display = 'block';
        }
      } catch (err) {
        loader.style.display = "none";
        msgBox.innerText = "⚠️ Network error. Please try again.";
        msgBox.className = 'message error';
        msgBox.style.display = 'block';
      }
    });
  </script>
</body>
</html>
