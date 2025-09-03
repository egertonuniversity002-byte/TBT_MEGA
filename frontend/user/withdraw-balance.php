<?php
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Withdraw</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f7f8fa; margin:0; }
    .container { max-width: 520px; margin: 40px auto; background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:20px; }
    label { display:block; margin-bottom:8px; color:#374151; font-size:14px; }
    input, select { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:16px; }
    button { margin-top:14px; width:100%; background:#059669; color:#fff; border:none; border-radius:999px; padding:12px 16px; font-weight:600; cursor:pointer; }
    .msg { margin-top:12px; padding:10px; border-radius:10px; display:none; }
    .msg.error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
    .msg.success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Withdraw Balance</h2>
    <label>Amount</label>
    <input id="amount" type="number" step="0.01" min="1" placeholder="500" />
    <label style="margin-top:8px;">From Wallet</label>
    <select id="wallet">
      <option value="main">Main</option>
      <option value="tiktok">tiktok</option>
      <option value="youtube">youtube</option>
      <option value="whatsapp">whatsapp</option>
      <option value="facebook">facebook</option>
      <option value="instagram">instagram</option>
    </select>
    <button id="reqBtn">Request Withdrawal</button>
    <div id="msg" class="msg"></div>
  </div>
<script>
  const API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
  const token = localStorage.getItem('token');
  const msg = document.getElementById('msg');
  function showMsg(text, type){ msg.textContent=text; msg.className='msg '+type; msg.style.display='block'; }

  document.getElementById('reqBtn').addEventListener('click', async () => {
    const amount = parseFloat(document.getElementById('amount').value || '0');
    const wallet = document.getElementById('wallet').value;
    if (!amount || amount <= 0) { return showMsg('Enter a valid amount', 'error'); }
    try {
      const res = await fetch(`${API_BASE}/withdrawals`, { method:'POST', headers:{ 'Content-Type':'application/json', 'Authorization': `Bearer ${token}` }, body: JSON.stringify({ amount, wallet }) });
      const json = await res.json();
      if (json.status !== 'success') return showMsg(json.message || 'Failed to request withdrawal', 'error');
      showMsg('Withdrawal request submitted', 'success');
    } catch (e) { showMsg('Network error', 'error'); }
  });
</script>
</body>
</html>