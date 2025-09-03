<?php
// Minimal deposit page: calls /payments/deposit/initiate and opens Pesapal checkout URL if provided
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Deposit</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f7f8fa; margin:0; }
    .container { max-width: 520px; margin: 40px auto; background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:20px; }
    label { display:block; margin-bottom:8px; color:#374151; font-size:14px; }
    input { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:16px; }
    button { margin-top:14px; width:100%; background:#4f46e5; color:#fff; border:none; border-radius:999px; padding:12px 16px; font-weight:600; cursor:pointer; }
    .msg { margin-top:12px; padding:10px; border-radius:10px; display:none; }
    .msg.error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
    .msg.success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Deposit</h2>
    <p>Enter amount to deposit. You'll be redirected to PesaPal to complete payment.</p>
    <label>Amount</label>
    <input id="amount" type="number" step="0.01" min="1" placeholder="1000" />
    <button id="payBtn">Proceed to Pay</button>
    <div id="msg" class="msg"></div>
  </div>
<script>
  const API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
  const token = localStorage.getItem('token');
  const msg = document.getElementById('msg');
  function showMsg(text, type){ msg.textContent=text; msg.className='msg '+type; msg.style.display='block'; }

  document.getElementById('payBtn').addEventListener('click', async () => {
    const amount = parseFloat(document.getElementById('amount').value || '0');
    if (!amount || amount <= 0) { return showMsg('Enter a valid amount', 'error'); }
    try {
      const res = await fetch(`${API_BASE}/payments/deposit/initiate`, { method:'POST', headers:{ 'Content-Type':'application/json', 'Authorization': `Bearer ${token}` }, body: JSON.stringify({ amount }) });
      const json = await res.json();
      if (json.status !== 'success') return showMsg(json.message || 'Failed to initiate payment', 'error');
      // Pesapal v3 returns redirect URL fields; try common keys
      const url = json.data.redirect_url || json.data.redirectUrl || json.data.redirect_url_mobile || json.data.payment_url || '';
      if (url) { window.location.href = url; } else { showMsg('Initiated but no redirect URL returned. Check server logs.', 'error'); }
    } catch (e) { showMsg('Network error', 'error'); }
  });
</script>
</body>
</html>