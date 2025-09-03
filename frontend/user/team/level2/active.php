<?php
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Team Level 2 - Active</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#f7f8fa; margin:0; }
    .container { max-width: 900px; margin: 24px auto; background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size:14px; }
    thead th { background:#f9fafb; }
    .muted { color:#6b7280; font-size:13px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Level 2 - Active Referrals</h2>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody id="rows"></tbody>
      </table>
    </div>
  </div>
<script>
  const API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
  const token = localStorage.getItem('token');
  const rows = document.getElementById('rows');
  async function load() {
    try {
      const res = await fetch(`${API_BASE}/team/list?level=2&status=active`, { headers: { 'Authorization': `Bearer ${token}` }});
      const j = await res.json();
      if (j.status !== 'success') return;
      rows.innerHTML = '';
      (j.data.items || []).forEach(u => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${u.name||''}</td><td>${u.email||''}</td><td>${u.status||''}</td><td>${u.joined||''}</td>`;
        rows.appendChild(tr);
      });
    } catch(e) {}
  }
  load();
</script>
</body>
</html>