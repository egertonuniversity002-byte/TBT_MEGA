<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Withdrawal Request | Matrix Platform</title>
  <link rel="stylesheet" href="../auth/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    /* Payout container */
    .payout-container {
      background: #fff;
      padding: 2rem;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      max-width: 500px;
      margin: 50px auto;
      text-align: center;
      animation: fadeSlide 0.6s ease-in-out;
    }

    /* Heading */
    .payout-container h1 {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-size: 1.6rem;
      color: #111827;
      margin-bottom: 1.5rem;
    }
    .payout-container h1 i {
      padding: 8px;
      border: 2px solid #ff6b35; /* orange border for withdrawal */
      border-radius: 50%;
      color: #ff6b35;
      font-size: 28px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .payout-container h1 i:hover {
      transform: scale(1.2);
      box-shadow: 0 0 10px #ff6b35, 0 0 20px #ff6b35;
    }

    /* Payout form */
    .payout-form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 1rem;
    }

    .payout-form .input-group {
      display: flex;
      align-items: center;
      background: #f3f4f6;
      border-radius: 10px;
      padding: 0.6rem;
      transition: border 0.3s;
    }
    .payout-form .input-group i {
      margin-right: 10px;
      font-size: 18px;
      color: #ff6b35;
      transition: transform 0.3s, color 0.3s;
    }
    .payout-form .input-group input, .payout-form .input-group select {
      border: none;
      outline: none;
      background: transparent;
      width: 100%;
      font-size: 0.95rem;
    }
    .payout-form .input-group i:hover { transform: scale(1.2); }

    /* Button */
    .payout-form button {
      padding: 0.8rem;
      background: #ff6b35;
      color: #fff;
      font-size: 1rem;
      font-weight: 500;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .payout-form button:hover { background: #e55a2b; }

    /* Loader overlay */
    .loader-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(255,255,255,0.9);
      justify-content: center;
      align-items: center;
      flex-direction: column;
      z-index: 1000;
    }
    .loader-overlay .meta-logo svg {
      width: 80px; height: 60px;
      animation: pulse 2s infinite;
    }
    .loader-overlay p {
      margin-top: 12px;
      color: #374151;
      font-size: 0.95rem;
      text-align: center;
    }

    /* Styled message box */
    .message {
      display: none;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 0.9rem;
      text-align: left;
      animation: fadeIn 0.4s ease-in-out;
    }
    .message.success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .message.error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }

    /* Animations */
    @keyframes fadeSlide {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    /* Responsive */
    @media screen and (max-width: 480px) {
      .payout-container {
        margin: 20px;
        padding: 1.5rem;
      }
      .payout-container h1 { font-size: 1.4rem; }
      .payout-container h1 i { font-size: 24px; padding: 6px; }
    }
  </style>
</head>
<body>
<script>
  // Set API_BASE globally
  window.API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
</script>
  <!-- Loader -->
  <div class="loader-overlay" id="loader">
    <div class="meta-logo">
      <svg viewBox="0 0 100 60" xmlns="http://www.w3.org/2000/svg">
        <path class="infinity" d="M10,30 C20,10 40,10 50,30 C60,50 80,50 90,30" stroke="#ff6b35" stroke-width="6" stroke-linecap="round"/>
      </svg>
    </div>
    <p>Processing your withdrawal request...</p>
  </div>

  <div class="payout-container">
    <h1><i class="fa-solid fa-money-bill-wave"></i> Withdrawal Request</h1>

    <!-- Styled message box -->
    <div id="messageBox" class="message"></div>

    <form class="payout-form" id="payoutForm">
      <div class="input-group">
        <i class="fa-solid fa-dollar-sign"></i>
        <input type="number" name="amount" placeholder="Amount (KSH)" min="1" step="0.01" required>
      </div>
      <div class="input-group">
        <i class="fa-solid fa-wallet"></i>
        <select name="wallet" required>
          <option value="main">Main Wallet</option>
          <option value="tiktok">TikTok Wallet</option>
          <option value="youtube">YouTube Wallet</option>
          <option value="whatsapp">WhatsApp Wallet</option>
          <option value="facebook">Facebook Wallet</option>
          <option value="instagram">Instagram Wallet</option>
        </select>
      </div>
      <button type="submit" id="withdrawBtn">Request Withdrawal</button>
    </form>
  </div>

  <script>
    const API_BASE = window.API_BASE || "https://official-paypal.onrender.com";
    const token = localStorage.getItem('token');

    if (!token) {
      window.location.href = '../auth/login.php';
    }

    // Elements
    const loader = document.getElementById("loader");
    const msgBox = document.getElementById("messageBox");
    const form = document.getElementById("payoutForm");

    // Handle payout form submission
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const amount = parseFloat(form.amount.value);
      const wallet = form.wallet.value;

      if (!amount || amount <= 0) {
        showMessage("❌ Invalid amount.", "error");
        return;
      }

      loader.style.display = "flex";

      try {
        const res = await fetch(`${API_BASE}/withdrawals`, {
          method: "POST",
          headers: { "Content-Type": "application/json", "Authorization": `Bearer ${token}` },
          body: JSON.stringify({ amount, wallet })
        });

        const result = await res.json();
        loader.style.display = "none";

        if (result.status === "success") {
          showMessage("✅ Withdrawal request submitted successfully! You will be notified once approved.", "success");
          setTimeout(() => {
            window.location.href = "../user/dashboard.php";
          }, 2000);
        } else {
          showMessage("❌ " + (result.message || "Withdrawal request failed."), "error");
        }
      } catch (err) {
        loader.style.display = "none";
        showMessage("⚠️ Network error. Please try again.", "error");
      }
    });

    // Show styled messages
    function showMessage(text, type) {
      msgBox.innerText = text;
      msgBox.className = "message " + type;
      msgBox.style.display = "block";
      setTimeout(() => msgBox.style.display = "none", 5000);
    }
  </script>
</body>
</html>
