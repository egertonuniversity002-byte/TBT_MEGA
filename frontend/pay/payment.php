<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PesaPal Payment | Matrix Platform</title>
  <link rel="stylesheet" href="../auth/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    /* Payment container */
    .payment-container {
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
    .payment-container h1 {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-size: 1.6rem;
      color: #111827;
      margin-bottom: 1.5rem;
    }
    .payment-container h1 i {
      padding: 8px;
      border: 2px solid #39ff14; /* neon green border */
      border-radius: 50%;
      color: #39ff14;             /* neon green icon */
      font-size: 28px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .payment-container h1 i:hover {
      transform: scale(1.2);
      box-shadow: 0 0 10px #39ff14, 0 0 20px #39ff14;
    }

    /* Payment form */
    .payment-form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 1rem;
    }

    .payment-form .input-group {
      display: flex;
      align-items: center;
      background: #f3f4f6;
      border-radius: 10px;
      padding: 0.6rem;
      transition: border 0.3s;
    }
    .payment-form .input-group i {
      margin-right: 10px;
      font-size: 18px;
      color: #38bdf8; /* default blue icon */
      transition: transform 0.3s, color 0.3s;
    }
    .payment-form .input-group input {
      border: none;
      outline: none;
      background: transparent;
      width: 100%;
      font-size: 0.95rem;
    }
    .payment-form .input-group i:hover { transform: scale(1.2); }

    /* Button */
    .payment-form button {
      padding: 0.8rem;
      background: #38bdf8;
      color: #fff;
      font-size: 1rem;
      font-weight: 500;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .payment-form button:hover { background: #0284c7; }

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

    /* Amount selection */
    .amount-selection {
      margin-bottom: 1rem;
    }
    .amount-buttons {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
      margin-bottom: 0.5rem;
    }
    .amount-btn {
      padding: 0.75rem;
      background: #f3f4f6;
      border: 2px solid #d1d5db;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s;
      color: #374151;
    }
    .amount-btn:hover {
      background: #e5e7eb;
      border-color: #38bdf8;
    }
    .amount-btn.selected {
      background: #38bdf8;
      border-color: #38bdf8;
      color: #fff;
    }
    .discount-info {
      text-align: center;
      font-weight: 500;
    }

    /* Fixed amount display styles */
    .fixed-amount-display {
      background: #f9fafb;
      border-radius: 10px;
      padding: 1rem;
      border: 2px solid #e5e7eb;
      margin-bottom: 0.5rem;
    }
    .amount-display {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      margin-bottom: 0.5rem;
    }
    .original-amount {
      text-decoration: line-through;
      color: #9ca3af;
      font-size: 1.1rem;
    }
    .discounted-amount {
      font-size: 1.3rem;
      font-weight: bold;
      color: #059669;
    }
    .currency-conversion {
      font-style: italic;
    }

    /* Responsive */
    @media screen and (max-width: 480px) {
      .payment-container {
        margin: 20px;
        padding: 1.5rem;
      }
      .payment-container h1 { font-size: 1.4rem; }
      .payment-container h1 i { font-size: 24px; padding: 6px; }
      .amount-buttons {
        grid-template-columns: repeat(2, 1fr);
      }
      .amount-display {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body>
<script>
  // Optionally set API_BASE globally for production
  window.API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
</script>
  <!-- Loader -->
  <div class="loader-overlay" id="loader">
    <div class="meta-logo">
      <svg viewBox="0 0 100 60" xmlns="http://www.w3.org/2000/svg">
        <path class="infinity" d="M10,30 C20,10 40,10 50,30 C60,50 80,50 90,30" stroke="#38bdf8" stroke-width="6" stroke-linecap="round"/>
      </svg>
    </div>
    <p>Processing your payment...</p>
  </div>

  <div class="payment-container">
    <h1><i class="fa-solid fa-money-bill-wave"></i> PesaPal Payment</h1>
<!-- Hero animated text above payment form -->
<div class="hero-text">
  <h2>Welcome to <span class="highlight">Matrix Platform</span></h2>
  <p id="animatedFeatures"></p>
</div>

<style>
/* Hero text styling */
.hero-text {
  text-align: center;
  margin-bottom: 2rem;
}

.hero-text h2 {
  font-size: 1.6rem;
  color: #111827;
  margin-bottom: 0.5rem;
}

.hero-text .highlight {
  color: #39ff14; /* neon green */
  border-bottom: 2px solid #39ff14;
  padding-bottom: 2px;
}

.hero-text p {
  font-size: 1rem;
  color: #374151;
  min-height: 1.5em; /* reserve space to prevent flicker */
  font-weight: 500;
}

/* Typing cursor animation */
.cursor {
  display: inline-block;
  width: 1px;
  background-color: #39ff14;
  animation: blinkCaret 0.7s infinite;
}
@keyframes blinkCaret {
  50% { border-color: transparent; }
}

.footer-rotator {
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 24px;
  color: #38bdf8; /* neon blue */
  animation: rotate 2s linear infinite;
  z-index: 100;
}

@keyframes rotate {
  0% { transform: translateX(-50%) rotate(0deg); }
  100% { transform: translateX(-50%) rotate(360deg); }
}

/* Live system indicator */
.live-system {
  position: fixed;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  width: 50px;
  height: 50px;
  background-color: #39ff14; /* solid neon green */
  border-radius: 50%;
  box-shadow: 0 0 15px #39ff14, 0 0 30px #39ff14, 0 0 45px #bb2c08ff;
  animation: pulseGlow 1.8s ease-in-out infinite;
  z-index: 100;
}

/* Pulse glow animation */
@keyframes pulseGlow {
  0% {
    transform: translateX(-50%) scale(1);
    box-shadow: 0 0 15px #39ff14, 0 0 30px #39ff14, 0 0 45px #39ff14;
  }
  50% {
    transform: translateX(-50%) scale(1.2);
    box-shadow: 0 0 25px #39ff14, 0 0 50px #39ff14, 0 0 75px #39ff14;
  }
  100% {
    transform: translateX(-50%) scale(1);
    box-shadow: 0 0 15px #39ff14, 0 0 30px #39ff14, 0 0 45px #39ff14;
  }
}

/* Responsive adjustments */
@media screen and (max-width:480px){
  .live-system {
    width: 40px;
    height: 40px;
    bottom: 20px;
  }
}



</style>

<script>
// Array of platform features to cycle through
const features = [
  "💼 Manage tasks and affiliates seamlessly.",
  "🚀 Track referrals and earnings in real-time.",
  "🔒 Secure transactions and user data.",
  "📈 Grow your network effortlessly."
];

let index = 0;
const animatedElement = document.getElementById("animatedFeatures");

function typeFeature(text, i = 0) {
  if (i < text.length) {
    animatedElement.innerHTML = text.slice(0, i + 1) + '<span class="cursor">|</span>';
    setTimeout(() => typeFeature(text, i + 1), 50);
  } else {
    setTimeout(() => eraseFeature(text), 2000);
  }
}

function eraseFeature(text) {
  let i = text.length;
  function erase() {
    if (i >= 0) {
      animatedElement.innerHTML = text.slice(0, i) + '<span class="cursor">|</span>';
      i--;
      setTimeout(erase, 30);
    } else {
      index = (index + 1) % features.length;
      typeFeature(features[index]);
    }
  }
  erase();
}

// Start the animation
typeFeature(features[index]);
</script>

    <!-- Styled message box -->
    <div id="messageBox" class="message"></div>

    <form class="payment-form" id="paymentForm">
      <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="name" placeholder="Full Name" required>
      </div>
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="email" placeholder="Email Address" required>
      </div>
      <div class="input-group">
        <i class="fa-solid fa-phone"></i>
        <input type="tel" name="phone" placeholder="Phone Number" required>
      </div>
      <!-- Fixed Amount Display -->
      <div class="amount-selection">
        <label style="font-weight: 600; color: #374151; margin-bottom: 0.5rem; display: block;">Payment Amount:</label>
        <div class="fixed-amount-display">
          <div class="amount-display">
            <span class="original-amount" id="originalAmount">KSh 350</span>
            <span class="discounted-amount" id="discountedAmount">KSh 300</span>
          </div>
          <div class="discount-info" id="discountInfo">🎉 Special Discount: Save KSh 50!</div>
        </div>
        <div class="currency-conversion" id="currencyConversion" style="margin-top: 0.5rem; font-size: 0.85rem; color: #6b7280;"></div>
      </div>
      <input type="hidden" name="amount" id="amount" value="">
      <button type="submit" id="payBtn" disabled>Pay Now</button>
    </form>
  </div>

  <script>
    // Fixed amount and discount
    const fixedAmountKES = 350;
    const visualDiscountKES = 50;
    const discountedAmountKES = fixedAmountKES - visualDiscountKES;

    // Load user's currency from profile for display and conversion
    const API_BASE = window.API_BASE || "http://172.17.106.84:5000";
    const token = localStorage.getItem('token');

    // Elements
    const loader = document.getElementById("loader");
    const msgBox = document.getElementById("messageBox");
    const form = document.getElementById("paymentForm");
    const originalAmountEl = document.getElementById('originalAmount');
    const discountedAmountEl = document.getElementById('discountedAmount');
    const currencyConversionEl = document.getElementById('currencyConversion');
    const payBtn = document.getElementById('payBtn');
    const amountInput = document.getElementById('amount');

    // Set fixed amount values on page load
    originalAmountEl.textContent = `KSh ${fixedAmountKES}`;
    discountedAmountEl.textContent = `KSh ${discountedAmountKES}`;
    amountInput.value = discountedAmountKES.toString();
    payBtn.disabled = false;

    // Function to convert KES to user's preferred currency
    async function convertCurrency(amountKES, targetCurrency) {
      if (targetCurrency === 'KES') {
        return { convertedAmount: amountKES, symbol: 'KSh' };
      }
      try {
        const res = await fetch(`https://api.exchangerate-api.com/v4/latest/KES`);
        if (!res.ok) return null;
        const data = await res.json();
        const rate = data.rates[targetCurrency];
        if (!rate) return null;
        const convertedAmount = (amountKES * rate).toFixed(2);
        const symbolMap = { USD: '$', GBP: '£', UGX: 'USh', TZS: 'TSh' };
        const symbol = symbolMap[targetCurrency] || targetCurrency;
        return { convertedAmount, symbol };
      } catch {
        return null;
      }
    }

    // Load currency and update display
    async function loadCurrency() {
      if (!token) return;
      try {
        const res = await fetch(`${API_BASE}/profile`, { headers: { 'Authorization': `Bearer ${token}` } });
        if (!res.ok) return;
        const data = await res.json();
        if (data.status === 'success' && data.data && data.data.currency) {
          const currency = data.data.currency;
          const conversion = await convertCurrency(discountedAmountKES, currency);
          if (conversion) {
            currencyConversionEl.textContent = `Equivalent to ${conversion.symbol} ${conversion.convertedAmount} (${currency})`;
          }
        }
      } catch {}
    }
    loadCurrency();

    // Hide amount buttons container if present
    const amountButtonsContainer = document.querySelector('.amount-buttons');
    if (amountButtonsContainer) {
      amountButtonsContainer.style.display = 'none';
    }

    // Handle callback if redirected from PesaPal
    const urlParams = new URLSearchParams(window.location.search);
    const orderTrackingId = urlParams.get('OrderTrackingId');
    const orderMerchantReference = urlParams.get('OrderMerchantReference');

    if (orderTrackingId && orderMerchantReference) {
      // Hide form and show callback processing
      document.querySelector('.payment-container').style.display = 'none';
      loader.style.display = 'flex';
      loader.querySelector('p').textContent = 'Verifying payment...';

      // Query backend for payment status
      fetch(`${API_BASE}/payment-callback?OrderTrackingId=${orderTrackingId}&OrderMerchantReference=${orderMerchantReference}`, {
        headers: { 'Authorization': token ? `Bearer ${token}` : "" }
      })
      .then(res => res.json())
      .then(result => {
        loader.style.display = 'none';
        document.querySelector('.payment-container').style.display = 'block';
        if (result.status === 'success') {
          showMessage("✅ Payment successful! Redirecting to dashboard...", "success");
          // Redirect to dashboard after short delay
          setTimeout(() => {
            window.location.href = "../user/dashboard.php";
          }, 2000);
        } else {
          showMessage("❌ Payment failed or pending. Please contact support if needed.", "error");
        }
        // Clear URL params
        window.history.replaceState({}, document.title, window.location.pathname);
      })
      .catch(err => {
        loader.style.display = 'none';
        document.querySelector('.payment-container').style.display = 'block';
        showMessage("⚠️ Error verifying payment. Please check your account.", "error");
      });
    }

    // Handle payment form submission
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const amount = parseFloat(amountInput.value);
      if (!amount || amount <= 0) {
        showMessage("❌ Invalid amount.", "error");
        return;
      }

      loader.style.display = "flex";
      loader.querySelector('p').textContent = 'Processing your payment...';

      try {
        const res = await fetch(`${API_BASE}/pesapal`, {
          method: "POST",
          headers: { "Content-Type": "application/json", "Authorization": token ? `Bearer ${token}` : "" },
          body: JSON.stringify({ amount: amount.toString(), phone: form.phone.value.trim() })
        });

        const result = await res.json();
        loader.style.display = "none";

        if(result.status === "success") {
          showMessage("✅ Payment initiated successfully! Redirecting to PesaPal...", "success");
          setTimeout(() => {
            window.location.href = result.checkout_url;
          }, 1500);
        } else if (res.status === 401) {
          showMessage("❌ Please login to proceed with payment.", "error");
          setTimeout(() => window.location.href = "../auth/login.php", 1200);
        } else {
          showMessage("❌ " + (result.message || "Payment failed. Try again."), "error");
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
<!-- Live system button -->
<div class="live-system"></div>


</body>
</html>
