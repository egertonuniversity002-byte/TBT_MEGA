<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Matrix Platform</title>
  <link rel="stylesheet" href="auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css"/>
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
    <p>Creating your account...</p>
  </div>

  <div class="auth-container">
    <h2><i class="fa-solid fa-user-plus"></i> Register</h2>

    <!-- Referral Message -->
    <p id="referralMsg" style="font-size:0.9rem; color:#4b5563; margin-bottom:1rem;"></p>

    <!-- Styled Feedback -->
    <div id="messageBox" class="message"></div>

    <form id="registerForm">
      <!-- Name -->
      <div class="input-group">
        <i class="fa-solid fa-user"></i>
        <input type="text" id="name" name="name" placeholder="Full Name" required>
      </div>

      <!-- Phone -->
      <div class="input-group">
        <i class="fa-solid fa-phone"></i>
        <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
      </div>

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
     
      <!-- Terms -->
      <div style="margin:10px 0; font-size:0.85rem;">
        <input type="checkbox" id="terms" required>
        I agree to the <a href="terms.php" style="color:#38bdf8;">Terms & Conditions</a>
      </div>

      <button type="submit" class="btn">Register</button>
      <p style="margin-top:1rem; font-size:0.9rem;">Already have an account? <a href="login.php" style="color:#38bdf8;">Login</a></p>
    </form>
  </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script>
const loader = document.getElementById("loader");
const msgBox = document.getElementById("messageBox");
const referralMsg = document.getElementById("referralMsg");

// Detect referral from URL (?ref=...)
const params = new URLSearchParams(window.location.search);
if (params.has("ref")) {
  referralMsg.innerHTML = "You were invited by <b>" + params.get("ref") + "</b>";
}

// Intl Tel Input for phone with flags
const phoneInput = document.querySelector("#phone");
const iti = window.intlTelInput(phoneInput, {
  initialCountry: "ke",
  preferredCountries: ["ke","ug","tz","us","gb"],
  separateDialCode: true,
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
});

// Map ISO codes to currencies
const currencyMap = {
  ke: "KES",
  ug: "UGX",
  tz: "TZS",
  us: "USD",
  gb: "GBP"
};

// Form submit
document.getElementById("registerForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  // Make sure phone is entered
  if (!phoneInput.value.trim()) {
    showMessage("⚠️ Please enter your phone number.", "error");
    return;
  }

  loader.style.display = "flex";

  const countryData = iti.getSelectedCountryData();
  const phoneNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164); // full number with country code

  const formData = {
    name: document.getElementById("name").value.trim(),
    phone: phoneNumber,
    email: document.getElementById("email").value.trim(),
    password: document.getElementById("password").value.trim(),
    country: countryData.name,
    currency: currencyMap[countryData.iso2] || "USD",
    ref: params.get("ref") || null
  };

  console.log("Form Data:", formData); // check phone number here

  try {
    const API_BASE = window.API_BASE || " http://127.0.0.1:5000";
    const res = await fetch(`${API_BASE}/register`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData)
    });

    const result = await res.json();
    loader.style.display = "none";

    if (result.status === "success") {
      showMessage("✅ " + result.message, "success");
      setTimeout(() => window.location.href = "payment.php", 1500);
    } else {
      showMessage("❌ " + (result.message || "Registration failed"), "error");
    }
  } catch (err) {
    loader.style.display = "none";
    showMessage("⚠️ Network error. Please check your connection.", "error");
  }
});

// Show styled messages
function showMessage(text, type) {
  msgBox.innerText = text;
  msgBox.className = "message " + type;
  msgBox.style.display = "block";
  setTimeout(() => msgBox.style.display = "none", 4000);
}
</script>


</body>
</html>
