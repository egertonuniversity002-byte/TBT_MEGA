<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms & Conditions | Matrix Platform</title>
  <link rel="stylesheet" href="auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    /* Terms container */
    .terms-container {
      background: #fff;
      padding: 2rem;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      max-width: 800px;
      margin: 50px auto;
      animation: fadeSlide 0.6s ease-in-out;
    }

    /* Heading */
    .terms-container h1 {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1.6rem;
      color: #111827;
      margin-bottom: 1.5rem;
    }

    .terms-container h1 i {
      padding: 8px;
      border: 2px solid #39ff14; /* Neon green border */
      border-radius: 50%;
      color: #39ff14; /* Neon green icon */
      font-size: 28px;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .terms-container h1 i:hover {
      transform: scale(1.2);
      box-shadow: 0 0 10px #39ff14, 0 0 20px #39ff14;
    }

    /* Terms content */
    .terms-content {
      max-height: 500px;
      overflow-y: auto;
      font-size: 0.95rem;
      line-height: 1.6;
      color: #374151;
    }

    .terms-content p {
      margin-bottom: 1rem;
    }

    .terms-content h2 {
      font-size: 1.2rem;
      color: #111827;
      margin-top: 1.5rem;
      margin-bottom: 0.8rem;
    }

    /* Scrollbar styling */
    .terms-content::-webkit-scrollbar {
      width: 8px;
    }
    .terms-content::-webkit-scrollbar-thumb {
      background-color: #38bdf8;
      border-radius: 10px;
    }
    .terms-content::-webkit-scrollbar-track {
      background: #f3f4f6;
      border-radius: 10px;
    }

    /* Back button */
    .back-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #38bdf8;
      color: #fff;
      border-radius: 10px;
      text-decoration: none;
      transition: background 0.3s;
    }
    .back-btn:hover { background: #0284c7; }

    /* Responsive */
    @media screen and (max-width: 480px) {
      .terms-container {
        margin: 20px;
        padding: 1.5rem;
      }
      .terms-content { max-height: 400px; }
      .terms-container h1 { font-size: 1.4rem; }
      .terms-container h1 i { font-size: 24px; padding: 6px; }
    }
  </style>
</head>
<body>
  <div class="terms-container">
    <h1><i class="fa-solid fa-file-contract"></i> Terms & Conditions</h1>
    <div class="terms-content">
      <p>Welcome to the Matrix Platform affiliate program. By registering, you agree to follow the rules outlined below:</p>

      <h2>1. Eligibility</h2>
      <p>Only individuals above 18 years of age can participate in our affiliate program. Each participant must provide accurate and complete information during registration.</p>

      <h2>2. Referral Codes</h2>
      <p>Referrals must be genuine. Any attempt to manipulate referral codes or create fake accounts may result in account termination.</p>

      <h2>3. Payments</h2>
      <p>Commissions will be calculated according to the program rules. Payments are made monthly, and any discrepancies must be reported within 7 days of the payout.</p>

      <h2>4. Account Termination</h2>
      <p>Matrix Platform reserves the right to suspend or terminate accounts that violate these terms, including fraudulent activity or abuse of the system.</p>

      <h2>5. Privacy</h2>
      <p>Your personal information will be stored securely and will only be used in accordance with our privacy policy. Sharing your login or referral information is prohibited.</p>

      <h2>6. Updates to Terms</h2>
      <p>We may update these terms from time to time. You are responsible for reviewing the terms regularly. Continued participation constitutes acceptance of any changes.</p>

      <p>By continuing, you acknowledge that you have read and agree to these Terms & Conditions.</p>
    </div>

    <a href="register.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Register</a>
  </div>
</body>
</html>
