<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | Matrix Platform</title>
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
    <p>Resetting password...</p>
  </div>

  <div class="auth-container">
    <h2><i class="fa-solid fa-lock"></i> Reset Password</h2>

    <div id="messageBox" class="message"></div>

    <form id="resetForm">
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="newPassword" placeholder="Enter new password" required>
      </div>

      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" name="confirmPassword" placeholder="Confirm new password" required>
      </div>

      <button type="submit" class="btn">Reset Password</button>
    </form>
  </div>

  <script src="auth.js"></script>
  <script>
    document.getElementById('resetForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const loader = document.getElementById('loader');
      const msgBox = document.getElementById('messageBox');
      loader.style.display = 'flex';

      const form = e.target;
      const newPassword = form.newPassword.value.trim();
      const confirmPassword = form.confirmPassword.value.trim();
      if (newPassword !== confirmPassword) {
        loader.style.display = 'none';
        msgBox.innerText = '❌ Passwords do not match';
        msgBox.className = 'message error';
        msgBox.style.display = 'block';
        return;
      }

      const resetToken = sessionStorage.getItem('resetToken');
      if (!resetToken) {
        loader.style.display = 'none';
        msgBox.innerText = '❌ Missing reset token. Please verify your code again.';
        msgBox.className = 'message error';
        msgBox.style.display = 'block';
        return;
      }

      try {
        const res = await fetch(`${(window.API_BASE||'https://official-paypal.onrender.com')}/reset-password`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ newPassword, resetToken })
        });
        const result = await res.json();
        loader.style.display = 'none';

        if (result.status === 'success') {
          sessionStorage.removeItem('resetToken');
          msgBox.innerText = '✅ ' + result.message;
          msgBox.className = 'message success';
          msgBox.style.display = 'block';
          setTimeout(() => window.location.href = 'login.php', 1500);
        } else {
          msgBox.innerText = '❌ ' + (result.message || 'Unable to reset');
          msgBox.className = 'message error';
          msgBox.style.display = 'block';
        }
      } catch (err) {
        loader.style.display = 'none';
        msgBox.innerText = '⚠️ Network error. Please try again.';
        msgBox.className = 'message error';
        msgBox.style.display = 'block';
      }
    });
  </script>
</body>
</html>
