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
    .history-container { margin-top: 30px; }
    .history-title { font-weight: 700; font-size: 1.2rem; margin-bottom: 12px; color: #111827; }
    .history-list { list-style: none; padding: 0; margin: 0; }
    .history-item { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px 16px; margin-bottom: 10px; }
    .history-item .status { font-weight: 600; }
    .status-pending { color: #f59e0b; }
    .status-approved { color: #10b981; }
    .status-rejected { color: #ef4444; }
    .history-date { font-size: 0.85rem; color: #6b7280; margin-top: 4px; }
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

    <div class="history-container">
      <h3 class="history-title">Withdrawal History</h3>
      <ul id="historyList" class="history-list"></ul>
    </div>
  </div>
<script>
  const API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
  const token = localStorage.getItem('token');
  const msg = document.getElementById('msg');
  const historyList = document.getElementById('historyList');

  function showMsg(text, type){ 
    msg.textContent = text; 
    msg.className = 'msg ' + type; 
    msg.style.display = 'block'; 
  }

  async function loadWithdrawalHistory() {
    try {
      const res = await fetch(`${API_BASE}/withdrawals`, {
        method: 'GET',
        headers: { 'Authorization': `Bearer ${token}` }
      });
      const json = await res.json();
      if (json.status === 'success' && json.data && json.data.items) {
        historyList.innerHTML = '';
        if (json.data.items.length === 0) {
          historyList.innerHTML = '<li>No withdrawal history found.</li>';
          return;
        }
        json.data.items.forEach(item => {
          const li = document.createElement('li');
          li.className = 'history-item';
          const statusClass = item.status === 'pending' ? 'status-pending' :
                              item.status === 'approved' ? 'status-approved' :
                              item.status === 'rejected' ? 'status-rejected' : '';
          li.innerHTML = `
            <div>Amount: <strong>${item.amountDisplay || item.amount}</strong></div>
            <div>Status: <span class="status ${statusClass}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span></div>
            <div class="history-date">Date: ${item.createdAt || ''}</div>
          `;
          historyList.appendChild(li);
        });
      } else {
        historyList.innerHTML = '<li>Failed to load withdrawal history.</li>';
      }
    } catch (e) {
      historyList.innerHTML = '<li>Error loading withdrawal history.</li>';
    }
  }

  document.getElementById('reqBtn').addEventListener('click', async () => {
    const amount = parseFloat(document.getElementById('amount').value || '0');
    const wallet = document.getElementById('wallet').value;
    if (!amount || amount <= 0) { return showMsg('Enter a valid amount', 'error'); }
    try {
      const res = await fetch(`${API_BASE}/withdrawals`, { 
        method:'POST', 
        headers:{ 'Content-Type':'application/json', 'Authorization': `Bearer ${token}` }, 
        body: JSON.stringify({ amount, wallet }) 
      });
      const json = await res.json();
      if (json.status !== 'success') return showMsg(json.message || 'Failed to request withdrawal', 'error');
      showMsg('Withdrawal request submitted', 'success');
      loadWithdrawalHistory(); // Refresh history after new request
    } catch (e) { showMsg('Network error', 'error'); }
  });

  // Load withdrawal history on page load
  loadWithdrawalHistory();
</script>
</body>
</html>
