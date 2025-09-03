<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Platform Manager</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --panel: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --primary: #4f46e5;
      --secondary: #7c3aed;
      --border: #e5e7eb;
      --green: #059669;
      --red: #dc2626;
      --yellow: #d97706;
      --blue: #2563eb;
      --orange: #ea580c;
      --purple: #9333ea;
      --pink: #db2777;
      --success: #10b981;
      --warning: #f59e0b;
      --error: #ef4444;
      --info: #3b82f6;
      --shadow: 0 10px 25px rgba(0,0,0,0.1);
      --shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
    }

    * {
      box-sizing: border-box;
      transition: all 0.3s ease;
    }

    body {
      margin: 0;
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
      animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 20px;
      box-shadow: var(--shadow);
      margin-bottom: 24px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card:hover {
      box-shadow: var(--shadow-hover);
      transform: translateY(-2px);
    }

    .card-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      font-weight: 700;
      font-size: 18px;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      color: var(--text);
    }

    .card-body {
      padding: 24px;
    }

    .row {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .col {
      flex: 1;
      min-width: 220px;
    }

    label {
      display: block;
      font-size: 14px;
      color: var(--muted);
      margin-bottom: 8px;
      font-weight: 500;
    }

    input[type="text"], input[type="number"], input[type="email"], select, textarea {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--border);
      border-radius: 12px;
      outline: none;
      font: inherit;
      background: #fff;
      transition: all 0.3s ease;
      font-size: 14px;
    }

    input:focus, select:focus, textarea:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    textarea {
      min-height: 100px;
      resize: vertical;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 14px;
      transition: all 0.3s ease;
      text-decoration: none;
      position: relative;
      overflow: hidden;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .btn:hover::before {
      left: 100%;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: #fff;
      box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
    }

    .btn-secondary {
      background: linear-gradient(135deg, var(--blue) 0%, #1d4ed8 100%);
      color: #fff;
      box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }

    .btn-success {
      background: linear-gradient(135deg, var(--green) 0%, #047857 100%);
      color: #fff;
      box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
    }

    .btn-muted {
      background: #f3f4f6;
      color: #374151;
      border: 1px solid var(--border);
    }

    .btn-muted:hover {
      background: #e5e7eb;
    }

    .btn-danger {
      background: linear-gradient(135deg, var(--red) 0%, #b91c1c 100%);
      color: #fff;
      box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    }

    .btn-warning {
      background: linear-gradient(135deg, var(--orange) 0%, #c2410c 100%);
      color: #fff;
      box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
    }

    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none !important;
    }

    .toolbar {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 16px;
      text-align: left;
      border-bottom: 1px solid var(--border);
      font-size: 14px;
    }

    thead th {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      color: #374151;
      font-weight: 600;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    tbody tr {
      transition: all 0.3s ease;
    }

    tbody tr:hover {
      background: #f8fafc;
      transform: scale(1.01);
    }

    .badge {
      font-size: 12px;
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 600;
      display: inline-block;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-green { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); color: #065f46; }
    .badge-yellow { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); color: #92400e; }
    .badge-grey { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151; }
    .badge-red { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); color: #991b1b; }
    .badge-blue { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #1e40af; }
    .badge-purple { background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); color: #6b21a8; }

    .status-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 8px;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
      100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .dot-green { background: var(--success); }
    .dot-grey { background: var(--muted); }
    .dot-yellow { background: var(--warning); }
    .dot-red { background: var(--error); }
    .dot-blue { background: var(--info); }

    .pagination {
      display: flex;
      align-items: center;
      gap: 12px;
      justify-content: center;
      margin-top: 20px;
    }

    .message {
      display: none;
      margin: 16px 0;
      padding: 16px 20px;
      border-radius: 12px;
      border: 1px solid;
      font-weight: 500;
      animation: slideIn 0.4s ease-out;
    }

    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .message.success {
      background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
      color: #065f46;
      border-color: #a7f3d0;
    }

    .message.error {
      background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
      color: #991b1b;
      border-color: #fecaca;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      backdrop-filter: blur(4px);
    }

    .loader {
      background: var(--panel);
      padding: 24px 32px;
      border-radius: 16px;
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      animation: bounce 1s infinite;
    }

    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
      40% { transform: translateY(-10px); }
      60% { transform: translateY(-5px); }
    }

    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      background: var(--panel);
      padding: 24px;
      border-radius: 16px;
      box-shadow: var(--shadow);
    }

    .brand {
      font-weight: 800;
      font-size: 24px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .muted {
      color: var(--muted);
      font-size: 14px;
    }

    .switch {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    .tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 24px;
      border-bottom: 2px solid var(--border);
      background: var(--panel);
      padding: 8px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .tab {
      padding: 16px 24px;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      font-weight: 600;
      border-radius: 8px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .tab::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(79, 70, 229, 0.1), transparent);
      transition: left 0.5s;
    }

    .tab:hover::before {
      left: 100%;
    }

    .tab.active {
      border-bottom-color: var(--primary);
      color: var(--primary);
      background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
      box-shadow: 0 4px 15px rgba(79, 70, 229, 0.2);
    }

    .tab-content {
      display: none;
      animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .tab-content.active {
      display: block;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }

    .stat-card {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 24px;
      text-align: center;
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--blue));
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-hover);
    }

    .stat-value {
      font-size: 32px;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 8px;
      display: block;
    }

    .stat-label {
      font-size: 16px;
      color: var(--muted);
      font-weight: 500;
    }

    .modal {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.6);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      backdrop-filter: blur(8px);
    }

    .modal-content {
      background: var(--panel);
      border-radius: 20px;
      padding: 32px;
      max-width: 600px;
      width: 90%;
      max-height: 80vh;
      overflow-y: auto;
      box-shadow: 0 25px 50px rgba(0,0,0,0.25);
      animation: modalSlideIn 0.4s ease-out;
    }

    @keyframes modalSlideIn {
      from { opacity: 0; transform: scale(0.9) translateY(-20px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-header {
      margin-bottom: 20px;
      font-weight: 700;
      font-size: 20px;
      color: var(--text);
    }

    .modal-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      margin-top: 24px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      margin-bottom: 8px;
    }

    .search-box {
      position: relative;
      margin-bottom: 20px;
    }

    .search-box input {
      padding-left: 40px;
    }

    .search-box::before {
      content: '🔍';
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: 16px;
    }

    .filter-row {
      display: flex;
      gap: 16px;
      align-items: end;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .filter-row .col {
      min-width: 150px;
    }

    @media (max-width: 768px) {
      .container { padding: 16px; }
      .tabs { flex-direction: column; }
      .tab { padding: 12px 16px; }
      .stats-grid { grid-template-columns: 1fr; }
      .row { flex-direction: column; }
      .filter-row { flex-direction: column; align-items: stretch; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <div class="brand">🚀 Admin Dashboard</div>
        <div class="muted">Complete platform management system</div>
      </div>
      <div class="toolbar">
        <a class="btn btn-muted" href="/frontend/user/dashboard.php">👤 User Dashboard</a>
        <button id="logoutBtn" class="btn btn-danger">🚪 Log Out</button>
      </div>
    </div>

    <div id="msg" class="message"></div>

    <div class="tabs">
      <div class="tab active" data-tab="tasks">📋 Tasks</div>
      <div class="tab" data-tab="users">👥 Users</div>
      <div class="tab" data-tab="withdrawals">💰 Withdrawals</div>
      <div class="tab" data-tab="statistics">📊 Statistics</div>
      <div class="tab" data-tab="broadcast">📢 Broadcast</div>
    </div>

    <!-- Tasks Tab -->
    <div id="tasks" class="tab-content active">
      <div class="card">
        <div class="card-header">✨ Create New Task</div>
        <div class="card-body">
          <form id="createForm">
            <div class="row">
              <div class="col">
                <label>📝 Title</label>
                <input type="text" name="title" required placeholder="e.g., Watch TikTok video (30 sec)">
              </div>
              <div class="col">
                <label>🏷️ Category</label>
                <select name="category" required>
                  <option value="tiktok">🎵 TikTok</option>
                  <option value="youtube">📺 YouTube</option>
                  <option value="whatsapp">💬 WhatsApp</option>
                  <option value="facebook">📘 Facebook Ads</option>
                  <option value="instagram">📷 Instagram Ads</option>
                  <option value="ads">📢 Ads</option>
                  <option value="blogs">📝 Blogs</option>
                  <option value="trivia">🧠 Trivia</option>
                </select>
              </div>
              <div class="col">
                <label>💰 Reward Price</label>
                <input type="number" step="0.01" min="0" name="price" required placeholder="e.g., 0.50">
              </div>
              <div class="col">
                <label>🏦 Reward Wallet</label>
                <select name="reward_wallet">
                  <option value="main">🏠 Main</option>
                  <option value="tiktok">🎵 TikTok</option>
                  <option value="youtube">📺 YouTube</option>
                  <option value="whatsapp">💬 WhatsApp</option>
                  <option value="facebook">📘 Facebook</option>
                  <option value="instagram">📷 Instagram</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col" style="flex: 1 1 100%;">
                <label>📖 Instructions</label>
                <textarea name="instructions" placeholder="Describe what the user must do to complete the task."></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <label>🔗 Target URL (optional)</label>
                <input type="text" name="target_url" placeholder="https://...">
              </div>
              <div class="col">
                <label>🖼️ Image URL (optional)</label>
                <input type="text" name="image_url" placeholder="https://.../image.jpg">
              </div>
            </div>
            <div class="row" style="align-items:center; justify-content: space-between;">
              <label class="switch">
                <input id="activeInput" type="checkbox" checked>
                ✅ Active
              </label>
              <button class="btn btn-primary" type="submit">
                ✨ Create Task
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">📋 Task Management</div>
        <div class="card-body">
          <div class="filter-row">
            <div class="col">
              <label>🏷️ Filter: Category</label>
              <select id="fCategory">
                <option value="">All Categories</option>
                <option value="tiktok">🎵 TikTok</option>
                <option value="youtube">📺 YouTube</option>
                <option value="whatsapp">💬 WhatsApp</option>
                <option value="facebook">📘 Facebook Ads</option>
                <option value="instagram">📷 Instagram Ads</option>
                <option value="ads">📢 Ads</option>
                <option value="blogs">📝 Blogs</option>
                <option value="trivia">🧠 Trivia</option>
              </select>
            </div>
            <div class="col">
              <label>📊 Filter: Status</label>
              <select id="fActive">
                <option value="">All Status</option>
                <option value="true">✅ Active</option>
                <option value="false">❌ Inactive</option>
              </select>
            </div>
            <div class="col" style="min-width:auto;">
              <button id="applyFilters" class="btn btn-secondary">🔍 Apply Filters</button>
            </div>
            <div class="col" style="flex:1 1 auto; text-align:right; min-width:auto;">
              <div class="pagination">
                <button id="prevBtn" class="btn btn-muted">⬅️ Prev</button>
                <span class="muted">Page <span id="page">1</span></span>
                <button id="nextBtn" class="btn btn-muted">Next ➡️</button>
              </div>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th>📝 Title</th>
                  <th>🏷️ Category</th>
                  <th>💰 Price</th>
                  <th>🏦 Wallet</th>
                  <th>📊 Status</th>
                  <th>📅 Created</th>
                  <th style="width: 250px;">⚙️ Actions</th>
                </tr>
              </thead>
              <tbody id="taskRows"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Tab -->
    <div id="users" class="tab-content">
      <div class="card">
        <div class="card-header">👥 User Management</div>
        <div class="card-body">
          <div class="search-box">
            <input type="text" id="userSearch" placeholder="Search users by name or email...">
          </div>
          <div class="filter-row">
            <div class="col">
              <label>📊 Filter: Status</label>
              <select id="userStatusFilter">
                <option value="">All Users</option>
                <option value="active">✅ Active</option>
                <option value="inactive">❌ Inactive</option>
              </select>
            </div>
            <div class="col" style="flex:1 1 auto; text-align:right; min-width:auto;">
              <div class="pagination">
                <button id="userPrevBtn" class="btn btn-muted">⬅️ Prev</button>
                <span class="muted">Page <span id="userPage">1</span></span>
                <button id="userNextBtn" class="btn btn-muted">Next ➡️</button>
              </div>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th>👤 Name</th>
                  <th>📧 Email</th>
                  <th>📞 Phone</th>
                  <th>💰 Balance</th>
                  <th>📊 Status</th>
                  <th>🌍 Country</th>
                  <th>📅 Joined</th>
                  <th style="width: 200px;">⚙️ Actions</th>
                </tr>
              </thead>
              <tbody id="userRows"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Withdrawals Tab -->
    <div id="withdrawals" class="tab-content">
      <div class="card">
        <div class="card-header">💰 Withdrawal Management</div>
        <div class="card-body">
          <div class="filter-row">
            <div class="col">
              <label>📊 Filter: Status</label>
              <select id="withdrawalStatusFilter">
                <option value="">All Status</option>
                <option value="pending">⏳ Pending</option>
                <option value="approved">✅ Approved</option>
                <option value="rejected">❌ Rejected</option>
              </select>
            </div>
            <div class="col" style="flex:1 1 auto; text-align:right; min-width:auto;">
              <div class="pagination">
                <button id="withdrawalPrevBtn" class="btn btn-muted">⬅️ Prev</button>
                <span class="muted">Page <span id="withdrawalPage">1</span></span>
                <button id="withdrawalNextBtn" class="btn btn-muted">Next ➡️</button>
              </div>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th>👤 User</th>
                  <th>💰 Amount</th>
                  <th>🏦 Method</th>
                  <th>📊 Status</th>
                  <th>📅 Requested</th>
                  <th style="width: 200px;">⚙️ Actions</th>
                </tr>
              </thead>
              <tbody id="withdrawalRows"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Tab -->
    <div id="statistics" class="tab-content">
      <div class="stats-grid" id="statsGrid">
        <!-- Stats will be loaded here -->
      </div>

      <div class="card">
        <div class="card-header">📊 Detailed Statistics</div>
        <div class="card-body">
          <div id="detailedStats">
            <!-- Detailed stats will be loaded here -->
          </div>
        </div>
      </div>
    </div>

    <!-- Broadcast Tab -->
    <div id="broadcast" class="tab-content">
      <div class="card">
        <div class="card-header">📢 Broadcast Notifications</div>
        <div class="card-body">
          <form id="notificationForm">
            <div class="row">
              <div class="col">
                <label>📝 Title</label>
                <input type="text" name="notificationTitle" required placeholder="Notification title">
              </div>
              <div class="col">
                <label>📊 Type</label>
                <select name="notificationType">
                  <option value="info">ℹ️ Info</option>
                  <option value="success">✅ Success</option>
                  <option value="warning">⚠️ Warning</option>
                  <option value="error">❌ Error</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col" style="flex: 1 1 100%;">
                <label>💬 Message</label>
                <textarea name="notificationMessage" required placeholder="Notification message"></textarea>
              </div>
            </div>
            <div class="row" style="justify-content: flex-end;">
              <button class="btn btn-primary" type="submit">
                📢 Send Notification
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">📧 Broadcast Email</div>
        <div class="card-body">
          <form id="emailForm">
            <div class="row">
              <div class="col">
                <label>📧 Subject</label>
                <input type="text" name="emailSubject" required placeholder="Email subject">
              </div>
              <div class="col">
                <label>🌍 Filter by Country (optional)</label>
                <input type="text" name="emailCountry" placeholder="e.g., Kenya, Uganda">
              </div>
            </div>
            <div class="row">
              <div class="col">
                <label>💱 Filter by Currency (optional)</label>
                <input type="text" name="emailCurrency" placeholder="e.g., KES, USD">
              </div>
              <div class="col">
                <label>📊 Filter by Status (optional)</label>
                <select name="emailStatus">
                  <option value="">All Users</option>
                  <option value="active">✅ Active</option>
                  <option value="inactive">❌ Inactive</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col" style="flex: 1 1 100%;">
                <label>💬 Message</label>
                <textarea name="emailMessage" required placeholder="Email message content"></textarea>
              </div>
            </div>
            <div class="row" style="justify-content: flex-end;">
              <button class="btn btn-success" type="submit">
                📧 Send Email
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modals -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">✏️ Edit Task</div>
      <form id="editForm">
        <div class="form-group">
          <label>📝 Title</label>
          <input type="text" name="editTitle" required>
        </div>
        <div class="form-group">
          <label>🏷️ Category</label>
          <select name="editCategory" required>
            <option value="tiktok">🎵 TikTok</option>
            <option value="youtube">📺 YouTube</option>
            <option value="whatsapp">💬 WhatsApp</option>
            <option value="facebook">📘 Facebook Ads</option>
            <option value="instagram">📷 Instagram Ads</option>
            <option value="ads">📢 Ads</option>
            <option value="blogs">📝 Blogs</option>
            <option value="trivia">🧠 Trivia</option>
          </select>
        </div>
        <div class="form-group">
          <label>💰 Price</label>
          <input type="number" step="0.01" min="0" name="editPrice" required>
        </div>
        <div class="form-group">
          <label>🏦 Reward Wallet</label>
          <select name="editWallet">
            <option value="main">🏠 Main</option>
            <option value="tiktok">🎵 TikTok</option>
            <option value="youtube">📺 YouTube</option>
            <option value="whatsapp">💬 WhatsApp</option>
            <option value="facebook">📘 Facebook</option>
            <option value="instagram">📷 Instagram</option>
          </select>
        </div>
        <div class="form-group">
          <label>📖 Instructions</label>
          <textarea name="editInstructions"></textarea>
        </div>
        <div class="form-group">
          <label>🔗 Target URL</label>
          <input type="text" name="editTargetUrl">
        </div>
        <div class="form-group">
          <label>🖼️ Image URL</label>
          <input type="text" name="editImageUrl">
        </div>
        <div class="form-group">
          <label class="switch">
            <input type="checkbox" name="editActive" checked>
            ✅ Active
          </label>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-muted" onclick="closeModal()">❌ Cancel</button>
          <button type="submit" class="btn btn-primary">💾 Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <div id="overlay" class="overlay">
    <div class="loader">⏳ Loading...</div>
  </div>

  <script>
    const API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
    const token = localStorage.getItem('token');
    if (!token) {
      window.location.href = '/frontend/auth/login.php';
    }

    const msg = document.getElementById('msg');
    const overlay = document.getElementById('overlay');

    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');

        // Load tab content
        switch(tab.dataset.tab) {
          case 'users': loadUsers(1); break;
          case 'withdrawals': loadWithdrawals(1); break;
          case 'statistics': loadStatistics(); break;
        }
      });
    });

    function showMsg(text, type) {
      msg.textContent = text;
      msg.className = 'message ' + (type || 'success');
      msg.style.display = 'block';
      setTimeout(() => { msg.style.display = 'none'; }, 4000);
    }

    function showLoading(b) { overlay.style.display = b ? 'flex' : 'none'; }

    async function api(path, options = {}) {
      const headers = Object.assign({ 'Authorization': `Bearer ${token}` }, options.headers || {});
      const res = await fetch(path.startsWith('http') ? path : `${API_BASE}${path}`, Object.assign({}, options, { headers }));
      let json = null;
      try { json = await res.json(); } catch {}
      if (!res.ok || (json && json.status === 'error')) {
        const message = (json && json.message) || ('HTTP ' + res.status);
        throw new Error(message);
      }
      return json;
    }

    // Tasks functionality
    const createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(createForm);
      const body = {
        title: fd.get('title'),
        category: fd.get('category'),
        price: parseFloat(fd.get('price') || '0'),
        reward_wallet: fd.get('reward_wallet') || 'main',
        instructions: fd.get('instructions') || '',
        target_url: fd.get('target_url') || '',
        image_url: fd.get('image_url') || '',
        active: document.getElementById('activeInput').checked
      };
      try {
        showLoading(true);
        await api('/tasks', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
        showMsg('✨ Task created successfully!', 'success');
        createForm.reset();
        document.getElementById('activeInput').checked = true;
        await loadTasks(1);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to create task'), 'error');
      } finally { showLoading(false); }
    });

    // Tasks list
    const taskRows = document.getElementById('taskRows');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageEl = document.getElementById('page');
    const fCategory = document.getElementById('fCategory');
    const fActive = document.getElementById('fActive');
    const applyFilters = document.getElementById('applyFilters');

    let currentTaskPage = 1;
    const taskLimit = 10;
    let totalTaskPages = 1;

    async function loadTasks(page) {
      try {
        showLoading(true);
        const params = new URLSearchParams();
        params.set('page', page);
        params.set('limit', taskLimit);
        if (fCategory.value) params.set('category', fCategory.value);
        if (fActive.value !== '') params.set('active', fActive.value);
        const data = await api('/tasks/admin?' + params.toString());
        const items = data.data.items || [];
        const total = data.data.total || 0;
        totalTaskPages = Math.max(1, Math.ceil(total / taskLimit));

        taskRows.innerHTML = '';
        for (const t of items) {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${t.title}</td>
            <td><span class="badge badge-grey">${t.category}</span></td>
            <td>${t.price}</td>
            <td>${t.reward_wallet}</td>
            <td>${t.active ? '<span class="status-dot dot-green"></span>Active' : '<span class="status-dot dot-grey"></span>Inactive'}</td>
            <td>${t.created_at}</td>
            <td>
              <button class="btn btn-muted" data-edit="${t.id}">✏️ Edit</button>
              <button class="btn btn-danger" data-delete="${t.id}">🗑️ Delete</button>
              <button class="btn btn-muted" data-toggle="${t.id}">${t.active ? '🔽 Deactivate' : '🔼 Activate'}</button>
            </td>`;
          taskRows.appendChild(tr);
        }

        // Bind actions
        taskRows.querySelectorAll('[data-edit]').forEach(btn => btn.addEventListener('click', () => onEditTask(btn.getAttribute('data-edit'))));
        taskRows.querySelectorAll('[data-delete]').forEach(btn => btn.addEventListener('click', () => onDeleteTask(btn.getAttribute('data-delete'))));
        taskRows.querySelectorAll('[data-toggle]').forEach(btn => btn.addEventListener('click', () => onToggleTask(btn.getAttribute('data-toggle'))));

        currentTaskPage = page;
        pageEl.textContent = String(currentTaskPage);
        prevBtn.disabled = currentTaskPage <= 1;
        nextBtn.disabled = currentTaskPage >= totalTaskPages;
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to load tasks'), 'error');
      } finally { showLoading(false); }
    }

    prevBtn.addEventListener('click', () => { if (currentTaskPage > 1) loadTasks(currentTaskPage - 1); });
    nextBtn.addEventListener('click', () => { if (currentTaskPage < totalTaskPages) loadTasks(currentTaskPage + 1); });
    applyFilters.addEventListener('click', () => loadTasks(1));

    async function onEditTask(id) {
      try {
        const data = await api(`/tasks/${id}`);
        const task = data.data;
        document.getElementById('editForm').elements.editTitle.value = task.title;
        document.getElementById('editForm').elements.editCategory.value = task.category;
        document.getElementById('editForm').elements.editPrice.value = task.price;
        document.getElementById('editForm').elements.editWallet.value = task.reward_wallet;
        document.getElementById('editForm').elements.editInstructions.value = task.instructions || '';
        document.getElementById('editForm').elements.editTargetUrl.value = task.target_url || '';
        document.getElementById('editForm').elements.editImageUrl.value = task.image_url || '';
        document.getElementById('editForm').elements.editActive.checked = task.active;
        document.getElementById('editModal').style.display = 'flex';
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to load task'), 'error');
      }
    }

    async function onDeleteTask(id) {
      if (!confirm('🗑️ Are you sure you want to delete this task?')) return;
      try {
        showLoading(true);
        await api(`/tasks/${id}`, { method: 'DELETE' });
        showMsg('✅ Task deleted successfully!', 'success');
        await loadTasks(currentTaskPage);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to delete task'), 'error');
      } finally { showLoading(false); }
    }

    async function onToggleTask(id) {
      try {
        const row = Array.from(taskRows.querySelectorAll('button[data-toggle]')).find(b => b.getAttribute('data-toggle') === id).closest('tr');
        const isActive = row.querySelector('td:nth-child(5)').textContent.trim().startsWith('Active');
        showLoading(true);
        await api(`/tasks/${id}`, { method: 'PATCH', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ active: !isActive }) });
        showMsg(!isActive ? '✅ Task activated!' : '🔽 Task deactivated!', 'success');
        await loadTasks(currentTaskPage);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to toggle task'), 'error');
      } finally { showLoading(false); }
    }

    // Edit form submission
    document.getElementById('editForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.target;
      const fd = new FormData(form);
      const body = {
        title: fd.get('editTitle'),
        category: fd.get('editCategory'),
        price: parseFloat(fd.get('editPrice') || '0'),
        reward_wallet: fd.get('editWallet') || 'main',
        instructions: fd.get('editInstructions') || '',
        target_url: fd.get('editTargetUrl') || '',
        image_url: fd.get('editImageUrl') || '',
        active: form.elements.editActive.checked
      };
      try {
        showLoading(true);
        await api(`/tasks/${currentEditId}`, { method: 'PATCH', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
        showMsg('✅ Task updated successfully!', 'success');
        closeModal();
        await loadTasks(currentTaskPage);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to update task'), 'error');
      } finally { showLoading(false); }
    });

    let currentEditId = null;

    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    // Users functionality
    const userRows = document.getElementById('userRows');
    const userPrevBtn = document.getElementById('userPrevBtn');
    const userNextBtn = document.getElementById('userNextBtn');
    const userPageEl = document.getElementById('userPage');
    const userSearch = document.getElementById('userSearch');
    const userStatusFilter = document.getElementById('userStatusFilter');

    let currentUserPage = 1;
    const userLimit = 10;
    let totalUserPages = 1;

    async function loadUsers(page) {
      try {
        showLoading(true);
        const params = new URLSearchParams();
        params.set('page', page);
        params.set('limit', userLimit);
        if (userStatusFilter.value) params.set('status', userStatusFilter.value);
        if (userSearch.value.trim()) params.set('search', userSearch.value.trim());
        const data = await api('/admin/users?' + params.toString());
        const items = data.data.items || [];
        const total = data.data.total || 0;
        totalUserPages = Math.max(1, Math.ceil(total / userLimit));

        userRows.innerHTML = '';
        for (const u of items) {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${u.name}</td>
            <td>${u.email}</td>
            <td>${u.phone || '-'}</td>
            <td>${u.balance.toFixed(2)}</td>
            <td>${u.is_active ? '<span class="status-dot dot-green"></span>Active' : '<span class="status-dot dot-red"></span>Inactive'}</td>
            <td>${u.country || '-'}</td>
            <td>${u.created_at}</td>
            <td>
              ${u.is_active ?
                `<button class="btn btn-warning" data-suspend="${u.id}">Suspend</button>` :
                `<button class="btn btn-success" data-activate="${u.id}">Activate</button>`}
            </td>`;
          userRows.appendChild(tr);
        }

        // Bind actions
        userRows.querySelectorAll('[data-suspend]').forEach(btn => btn.addEventListener('click', () => onSuspendUser(btn.getAttribute('data-suspend'))));
        userRows.querySelectorAll('[data-activate]').forEach(btn => btn.addEventListener('click', () => onActivateUser(btn.getAttribute('data-activate'))));

        currentUserPage = page;
        userPageEl.textContent = String(currentUserPage);
        userPrevBtn.disabled = currentUserPage <= 1;
        userNextBtn.disabled = currentUserPage >= totalUserPages;
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to load users'), 'error');
      } finally { showLoading(false); }
    }

    userPrevBtn.addEventListener('click', () => { if (currentUserPage > 1) loadUsers(currentUserPage - 1); });
    userNextBtn.addEventListener('click', () => { if (currentUserPage < totalUserPages) loadUsers(currentUserPage + 1); });
    userSearch.addEventListener('input', () => loadUsers(1));
    userStatusFilter.addEventListener('change', () => loadUsers(1));

    async function onSuspendUser(id) {
      if (!confirm('⚠️ Are you sure you want to suspend this user?')) return;
      try {
        showLoading(true);
        await api(`/admin/users/${id}/suspend`, { method: 'POST' });
        showMsg('✅ User suspended successfully!', 'success');
        await loadUsers(currentUserPage);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to suspend user'), 'error');
      } finally { showLoading(false); }
    }

    async function onActivateUser(id) {
      if (!confirm('✅ Are you sure you want to activate this user?')) return;
      try {
        showLoading(true);
        await api(`/admin/users/${id}/activate`, { method: 'POST' });
        showMsg('✅ User activated successfully!', 'success');
        await loadUsers(currentUserPage);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to activate user'), 'error');
      } finally { showLoading(false); }
    }

    // Withdrawals functionality
    const withdrawalRows = document.getElementById('withdrawalRows');
    const withdrawalPrevBtn = document.getElementById('withdrawalPrevBtn');
    const withdrawalNextBtn = document.getElementById('withdrawalNextBtn');
    const withdrawalPageEl = document.getElementById('withdrawalPage');
    const withdrawalStatusFilter = document.getElementById('withdrawalStatusFilter');

    let currentWithdrawalPage = 1;
    const withdrawalLimit = 10;
    let totalWithdrawalPages = 1;

    async function loadWithdrawals(page) {
      try {
        showLoading(true);
        const params = new URLSearchParams();
        params.set('page', page);
        params.set('limit', withdrawalLimit);
        if (withdrawalStatusFilter.value) params.set('status', withdrawalStatusFilter.value);
        const data = await api('/admin/withdrawals?' + params.toString());
        const items = data.data.items || [];
        const total = data.data.total || 0;
        totalWithdrawalPages = Math.max(1, Math.ceil(total / withdrawalLimit));

        withdrawalRows.innerHTML = '';
        for (const w of items) {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${w.user_name}</td>
            <td>${w.amount.toFixed(2)}</td>
            <td>${w.method}</td>
            <td>${w.status === 'pending' ? '<span class="status-dot dot-yellow"></span>Pending' : w.status === 'approved' ? '<span class="status-dot dot-green"></span>Approved' : '<span class="status-dot dot-red"></span>Rejected'}</td>
            <td>${w.created_at}</td>
            <td>
              ${w.status === 'pending' ? `
                <button class="btn btn-success" data-approve="${w.id}">Approve</button>
                <button class="btn btn-danger" data-reject="${w.id}">Reject</button>
              ` : ''}
            </td>`;
          withdrawalRows.appendChild(tr);
        }

        // Bind actions
        withdrawalRows.querySelectorAll('[data-approve]').forEach(btn => btn.addEventListener('click', () => onApproveWithdrawal(btn.getAttribute('data-approve'))));
        withdrawalRows.querySelectorAll('[data-reject]').forEach(btn => btn.addEventListener('click', () => onRejectWithdrawal(btn.getAttribute('data-reject'))));

        currentWithdrawalPage = page;
        withdrawalPageEl.textContent = String(currentWithdrawalPage);
        withdrawalPrevBtn.disabled = currentWithdrawalPage <= 1;
        withdrawalNextBtn.disabled = currentWithdrawalPage >= totalWithdrawalPages;
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to load withdrawals'), 'error');
      } finally { showLoading(false); }
    }

    withdrawalPrevBtn.addEventListener('click', () => { if (currentWithdrawalPage > 1) loadWithdrawals(currentWithdrawalPage - 1); });
    withdrawalNextBtn.addEventListener('click', () => { if (currentWithdrawalPage < totalWithdrawalPages) loadWithdrawals(currentWithdrawalPage + 1); });
    withdrawalStatusFilter.addEventListener('change', () => loadWithdrawals(1));

    async function onApproveWithdrawal(id) {
      if (!confirm('✅ Approve this withdrawal request?')) return;
      try {
        showLoading(true);
        await api(`/admin/withdrawals/${id}/approve`, { method: 'POST' });
        showMsg('✅ Withdrawal approved!', 'success');
        await loadWithdrawals(currentWithdrawalPage);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to approve withdrawal'), 'error');
      } finally { showLoading(false); }
    }

    async function onRejectWithdrawal(id) {
      const reason = prompt('Please provide a reason for rejection:', 'Request rejected by admin');
      if (reason === null) return;
      try {
        showLoading(true);
        await api(`/admin/withdrawals/${id}/reject`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ reason }) });
        showMsg('✅ Withdrawal rejected!', 'success');
        await loadWithdrawals(currentWithdrawalPage);
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to reject withdrawal'), 'error');
      } finally { showLoading(false); }
    }

    // Statistics functionality
    const statsGrid = document.getElementById('statsGrid');
    const detailedStats = document.getElementById('detailedStats');

    async function loadStatistics() {
      try {
        showLoading(true);
        const data = await api('/admin/statistics');
        const stats = data.data.overview;

        statsGrid.innerHTML = `
          <div class="stat-card">
            <div class="stat-value">${stats.total_users}</div>
            <div class="stat-label">Total Users</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.active_users}</div>
            <div class="stat-label">Active Users</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.inactive_users}</div>
            <div class="stat-label">Inactive Users</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.total_deposits}</div>
            <div class="stat-label">Total Deposits</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.total_withdrawals}</div>
            <div class="stat-label">Total Withdrawals</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.pending_withdrawals}</div>
            <div class="stat-label">Pending Withdrawals</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.total_deposit_amount.toFixed(2)}</div>
            <div class="stat-label">Total Deposit Amount</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.total_withdrawal_amount.toFixed(2)}</div>
            <div class="stat-label">Total Withdrawal Amount</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">${stats.platform_balance.toFixed(2)}</div>
            <div class="stat-label">Platform Balance</div>
          </div>
        `;

        detailedStats.innerHTML = `
          <p>More detailed statistics can be added here as needed.</p>
        `;
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to load statistics'), 'error');
      } finally { showLoading(false); }
    }

    // Broadcast functionality
    const notificationForm = document.getElementById('notificationForm');
    const emailForm = document.getElementById('emailForm');

    notificationForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(notificationForm);
      const body = {
        title: fd.get('notificationTitle'),
        message: fd.get('notificationMessage'),
        type: fd.get('notificationType')
      };
      try {
        showLoading(true);
        await api('/admin/notifications/broadcast', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
        showMsg('📢 Notification broadcast sent!', 'success');
        notificationForm.reset();
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to send notification'), 'error');
      } finally { showLoading(false); }
    });

    emailForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(emailForm);
      const filter = {};
      if (fd.get('emailCountry')) filter.country = fd.get('emailCountry');
      if (fd.get('emailCurrency')) filter.currency = fd.get('emailCurrency');
      if (fd.get('emailStatus')) filter.status = fd.get('emailStatus');
      const body = {
        subject: fd.get('emailSubject'),
        message: fd.get('emailMessage'),
        filter
      };
      try {
        showLoading(true);
        await api('/admin/email/broadcast', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) });
        showMsg('📧 Email broadcast sent!', 'success');
        emailForm.reset();
      } catch (err) {
        showMsg('❌ ' + (err.message || 'Failed to send email'), 'error');
      } finally { showLoading(false); }
    });

    // Logout functionality
    document.getElementById('logoutBtn').addEventListener('click', () => {
      localStorage.removeItem('token');
      window.location.href = '/frontend/auth/login.php';
    });

    // Initial load
    loadTasks(1);
  </script>
</body>
</html>
