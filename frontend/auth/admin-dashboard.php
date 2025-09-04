<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Platform Manager</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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

    /* Enhanced Feedback System */
    .feedback-container {
      position: fixed;
      top: 80px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 350px;
    }
    
    .feedback-toast {
      padding: 16px 20px;
      border-radius: 12px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      display: flex;
      align-items: center;
      animation: toastIn 0.5s ease forwards, toastOut 0.5s ease forwards 2.5s;
      transform: translateX(100%);
      opacity: 0;
      position: relative;
      overflow: hidden;
    }
    
    .feedback-toast.success {
      background: #f0fdf4;
      color: #166534;
      border-left: 4px solid #22c55e;
    }
    
    .feedback-toast.error {
      background: #fef2f2;
      color: #991b1b;
      border-left: 4px solid #ef4444;
    }
    
    .feedback-toast.warning {
      background: #fffbeb;
      color: #92400e;
      border-left: 4px solid #f59e0b;
    }
    
    .feedback-toast.info {
      background: #eff6ff;
      color: #1e40af;
      border-left: 4px solid #3b82f6;
    }
    
    .feedback-toast-icon {
      margin-right: 12px;
      font-size: 20px;
      flex-shrink: 0;
    }
    
    .feedback-toast-content {
      flex: 1;
    }
    
    .feedback-toast-title {
      font-weight: 600;
      margin-bottom: 4px;
    }
    
    .feedback-toast-message {
      font-size: 14px;
      opacity: 0.9;
    }
    
    .feedback-toast-close {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      opacity: 0.7;
      margin-left: 10px;
      color: inherit;
      flex-shrink: 0;
    }
    
    .feedback-toast-close:hover {
      opacity: 1;
    }
    
    .feedback-toast-progress {
      position: absolute;
      bottom: 0;
      left: 0;
      height: 3px;
      width: 100%;
      background: rgba(0, 0, 0, 0.1);
    }
    
    .feedback-toast-progress-bar {
      height: 100%;
      animation: progressBar 3s linear forwards;
    }
    
    .success .feedback-toast-progress-bar {
      background: #22c55e;
    }
    
    .error .feedback-toast-progress-bar {
      background: #ef4444;
    }
    
    .warning .feedback-toast-progress-bar {
      background: #f59e0b;
    }
    
    .info .feedback-toast-progress-bar {
      background: #3b82f6;
    }
    
    @keyframes toastIn {
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes toastOut {
      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }
    
    @keyframes progressBar {
      from {
        width: 100%;
      }
      to {
        width: 0%;
      }
    }

    @media (max-width: 768px) {
      .container { padding: 16px; }
      .tabs { flex-direction: column; }
      .tab { padding: 12px 16px; }
      .stats-grid { grid-template-columns: 1fr; }
      .row { flex-direction: column; }
      .filter-row { flex-direction: column; align-items: stretch; }
      .feedback-container {
        top: 70px;
        right: 10px;
        left: 10px;
        max-width: none;
      }
    }
  </style>
</head>
<body>
  <!-- Feedback Toast Container -->
  <div class="feedback-container" id="feedbackContainer"></div>

  <div class="container">
    <div class="header">
      <div>
        <div class="brand"><i class="fa-solid fa-rocket"></i> Admin Dashboard</div>
        <div class="muted">Complete platform management system</div>
      </div>
      <div class="toolbar">
        <a class="btn btn-muted" href="/frontend/user/dashboard.php"><i class="fa-solid fa-user"></i> User Dashboard</a>
        <button id="logoutBtn" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
      </div>
    </div>

    <div id="msg" class="message"></div>

    <div class="tabs">
      <div class="tab active" data-tab="tasks"><i class="fa-solid fa-list-check"></i> Tasks</div>
      <div class="tab" data-tab="users"><i class="fa-solid fa-users"></i> Users</div>
      <div class="tab" data-tab="withdrawals"><i class="fa-solid fa-money-bill-transfer"></i> Withdrawals</div>
      <div class="tab" data-tab="statistics"><i class="fa-solid fa-chart-simple"></i> Statistics</div>
      <div class="tab" data-tab="broadcast"><i class="fa-solid fa-bullhorn"></i> Broadcast</div>
    </div>

    <!-- Tasks Tab -->
    <div id="tasks" class="tab-content active">
      <div class="card">
        <div class="card-header"><i class="fa-solid fa-plus"></i> Create New Task</div>
        <div class="card-body">
          <form id="createForm">
            <div class="row">
              <div class="col">
                <label><i class="fa-solid fa-heading"></i> Title</label>
                <input type="text" name="title" required placeholder="e.g., Watch TikTok video (30 sec)">
              </div>
              <div class="col">
                <label><i class="fa-solid fa-tag"></i> Category</label>
                <select name="category" required>
                  <option value="tiktok"><i class="fa-brands fa-tiktok"></i> TikTok</option>
                  <option value="youtube"><i class="fa-brands fa-youtube"></i> YouTube</option>
                  <option value="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</option>
                  <option value="facebook"><i class="fa-brands fa-facebook"></i> Facebook Ads</option>
                  <option value="instagram"><i class="fa-brands fa-instagram"></i> Instagram Ads</option>
                  <option value="ads"><i class="fa-solid fa-ad"></i> Ads</option>
                  <option value="blogs"><i class="fa-solid fa-blog"></i> Blogs</option>
                  <option value="trivia"><i class="fa-solid fa-question"></i> Trivia</option>
                </select>
              </div>
              <div class="col">
                <label><i class="fa-solid fa-coins"></i> Reward Price</label>
                <input type="number" step="0.01" min="0" name="price" required placeholder="e.g., 0.50">
              </div>
              <div class="col">
                <label><i class="fa-solid fa-wallet"></i> Reward Wallet</label>
                <select name="reward_wallet">
                  <option value="main"><i class="fa-solid fa-house"></i> Main</option>
                  <option value="tiktok"><i class="fa-brands fa-tiktok"></i> TikTok</option>
                  <option value="youtube"><i class="fa-brands fa-youtube"></i> YouTube</option>
                  <option value="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</option>
                  <option value="facebook"><i class="fa-brands fa-facebook"></i> Facebook</option>
                  <option value="instagram"><i class="fa-brands fa-instagram"></i> Instagram</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col" style="flex: 1 1 100%;">
                <label><i class="fa-solid fa-book"></i> Instructions</label>
                <textarea name="instructions" placeholder="Describe what the user must do to complete the task."></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <label><i class="fa-solid fa-link"></i> Target URL (optional)</label>
                <input type="text" name="target_url" placeholder="https://...">
              </div>
              <div class="col">
                <label><i class="fa-solid fa-image"></i> Image URL (optional)</label>
                <input type="text" name="image_url" placeholder="https://.../image.jpg">
              </div>
            </div>
            <div class="row" style="align-items:center; justify-content: space-between;">
              <label class="switch">
                <input id="activeInput" type="checkbox" checked>
                <i class="fa-solid fa-toggle-on"></i> Active
              </label>
              <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-plus"></i> Create Task
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><i class="fa-solid fa-gears"></i> Task Management</div>
        <div class="card-body">
          <div class="filter-row">
            <div class="col">
              <label><i class="fa-solid fa-filter"></i> Filter: Category</label>
              <select id="fCategory">
                <option value="">All Categories</option>
                <option value="tiktok"><i class="fa-brands fa-tiktok"></i> TikTok</option>
                <option value="youtube"><i class="fa-brands fa-youtube"></i> YouTube</option>
                <option value="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</option>
                <option value="facebook"><i class="fa-brands fa-facebook"></i> Facebook Ads</option>
                <option value="instagram"><i class="fa-brands fa-instagram"></i> Instagram Ads</option>
                <option value="ads"><i class="fa-solid fa-ad"></i> Ads</option>
                <option value="blogs"><i class="fa-solid fa-blog"></i> Blogs</option>
                <option value="trivia"><i class="fa-solid fa-question"></i> Trivia</option>
              </select>
            </div>
            <div class="col">
              <label><i class="fa-solid fa-filter"></i> Filter: Status</label>
              <select id="fActive">
                <option value="">All Status</option>
                <option value="true"><i class="fa-solid fa-toggle-on"></i> Active</option>
                <option value="false"><i class="fa-solid fa-toggle-off"></i> Inactive</option>
              </select>
            </div>
            <div class="col" style="min-width:auto;">
              <button id="applyFilters" class="btn btn-secondary"><i class="fa-solid fa-filter"></i> Apply Filters</button>
            </div>
            <div class="col" style="flex:1 1 auto; text-align:right; min-width:auto;">
              <div class="pagination">
                <button id="prevBtn" class="btn btn-muted"><i class="fa-solid fa-chevron-left"></i> Prev</button>
                <span class="muted">Page <span id="page">1</span></span>
                <button id="nextBtn" class="btn btn-muted">Next <i class="fa-solid fa-chevron-right"></i></button>
              </div>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th><i class="fa-solid fa-heading"></i> Title</th>
                  <th><i class="fa-solid fa-tag"></i> Category</th>
                  <th><i class="fa-solid fa-coins"></i> Price</th>
                  <th><i class="fa-solid fa-wallet"></i> Wallet</th>
                  <th><i class="fa-solid fa-signal"></i> Status</th>
                  <th><i class="fa-solid fa-calendar"></i> Created</th>
                  <th style="width: 250px;"><i class="fa-solid fa-gear"></i> Actions</th>
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
        <div class="card-header"><i class="fa-solid fa-users-gear"></i> User Management</div>
        <div class="card-body">
          <div class="search-box">
            <input type="text" id="userSearch" placeholder="Search users by name or email...">
          </div>
          <div class="filter-row">
            <div class="col">
              <label><i class="fa-solid fa-filter"></i> Filter: Status</label>
              <select id="userStatusFilter">
                <option value="">All Users</option>
                <option value="active"><i class="fa-solid fa-toggle-on"></i> Active</option>
                <option value="inactive"><i class="fa-solid fa-toggle-off"></i> Inactive</option>
              </select>
            </div>
            <div class="col" style="flex:1 1 auto; text-align:right; min-width:auto;">
              <div class="pagination">
                <button id="userPrevBtn" class="btn btn-muted"><i class="fa-solid fa-chevron-left"></i> Prev</button>
                <span class="muted">Page <span id="userPage">1</span></span>
                <button id="userNextBtn" class="btn btn-muted">Next <i class="fa-solid fa-chevron-right"></i></button>
              </div>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th><i class="fa-solid fa-user"></i> Name</th>
                  <th><i class="fa-solid fa-envelope"></i> Email</th>
                  <th><i class="fa-solid fa-phone"></i> Phone</th>
                  <th><i class="fa-solid fa-coins"></i> Balance</th>
                  <th><i class="fa-solid fa-signal"></i> Status</th>
                  <th><i class="fa-solid fa-earth-africa"></i> Country</th>
                  <th><i class="fa-solid fa-calendar"></i> Joined</th>
                  <th style="width: 200px;"><i class="fa-solid fa-gear"></i> Actions</th>
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
        <div class="card-header"><i class="fa-solid fa-money-bill-transfer"></i> Withdrawal Management</div>
        <div class="card-body">
          <div class="filter-row">
            <div class="col">
              <label><i class="fa-solid fa-filter"></i> Filter: Status</label>
              <select id="withdrawalStatusFilter">
                <option value="">All Status</option>
                <option value="pending"><i class="fa-solid fa-clock"></i> Pending</option>
                <option value="approved"><i class="fa-solid fa-check"></i> Approved</option>
                <option value="rejected"><i class="fa-solid fa-xmark"></i> Rejected</option>
              </select>
            </div>
            <div class="col" style="flex:1 1 auto; text-align:right; min-width:auto;">
              <div class="pagination">
                <button id="withdrawalPrevBtn" class="btn btn-muted"><i class="fa-solid fa-chevron-left"></i> Prev</button>
                <span class="muted">Page <span id="withdrawalPage">1</span></span>
                <button id="withdrawalNextBtn" class="btn btn-muted">Next <i class="fa-solid fa-chevron-right"></i></button>
              </div>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th><i class="fa-solid fa-user"></i> User</th>
                  <th><i class="fa-solid fa-money-bill"></i> Amount</th>
                  <th><i class="fa-solid fa-wallet"></i> Wallet</th>
                  <th><i class="fa-solid fa-hashtag"></i> Address</th>
                  <th><i class="fa-solid fa-signal"></i> Status</th>
                  <th><i class="fa-solid fa-calendar"></i> Requested</th>
                  <th style="width: 200px;"><i class="fa-solid fa-gear"></i> Actions</th>
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
      <div class="stats-grid">
        <div class="stat-card">
          <span class="stat-value" id="totalUsers">0</span>
          <span class="stat-label"><i class="fa-solid fa-users"></i> Total Users</span>
        </div>
        <div class="stat-card">
          <span class="stat-value" id="activeUsers">0</span>
          <span class="stat-label"><i class="fa-solid fa-user-check"></i> Active Users</span>
        </div>
        <div class="stat-card">
          <span class="stat-value" id="totalTasks">0</span>
          <span class="stat-label"><i class="fa-solid fa-list-check"></i> Total Tasks</span>
        </div>
        <div class="stat-card">
          <span class="stat-value" id="activeTasks">0</span>
          <span class="stat-label"><i class="fa-solid fa-toggle-on"></i> Active Tasks</span>
        </div>
        <div class="stat-card">
          <span class="stat-value" id="totalWithdrawals">0</span>
          <span class="stat-label"><i class="fa-solid fa-money-bill-transfer"></i> Total Withdrawals</span>
        </div>
        <div class="stat-card">
          <span class="stat-value" id="pendingWithdrawals">0</span>
          <span class="stat-label"><i class="fa-solid fa-clock"></i> Pending Withdrawals</span>
        </div>
        <div class="stat-card">
          <span class="stat-value" id="totalPayout">0</span>
          <span class="stat-label"><i class="fa-solid fa-money-bill-wave"></i> Total Payout</span>
        </div>
        <div class="stat-card">
          <span class="stat-value" id="platformProfit">0</span>
          <span class="stat-label"><i class="fa-solid fa-chart-line"></i> Platform Profit</span>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><i class="fa-solid fa-chart-line"></i> Daily Registrations (Last 30 Days)</div>
        <div class="card-body">
          <div id="registrationChart" style="height: 300px; width: 100%;"></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><i class="fa-solid fa-chart-pie"></i> Task Categories Distribution</div>
        <div class="card-body">
          <div id="categoryChart" style="height: 300px; width: 100%;"></div>
        </div>
      </div>
    </div>

    <!-- Broadcast Tab -->
    <div id="broadcast" class="tab-content">
      <div class="card">
        <div class="card-header"><i class="fa-solid fa-bullhorn"></i> Send Broadcast Message</div>
        <div class="card-body">
          <form id="broadcastForm">
            <div class="form-group">
              <label><i class="fa-solid fa-heading"></i> Message Title</label>
              <input type="text" name="title" placeholder="Important announcement..." required>
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-message"></i> Message Content</label>
              <textarea name="content" placeholder="Write your message here..." rows="5" required></textarea>
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-users"></i> Target Audience</label>
              <select name="target">
                <option value="all">All Users</option>
                <option value="active">Active Users Only</option>
                <option value="inactive">Inactive Users Only</option>
                <option value="withdrawals">Users with Withdrawal Requests</option>
              </select>
            </div>
            <div class="form-group">
              <label><i class="fa-solid fa-paper-plane"></i> Message Type</label>
              <select name="type">
                <option value="info">Information</option>
                <option value="warning">Warning</option>
                <option value="important">Important Announcement</option>
                <option value="update">System Update</option>
              </select>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Send Broadcast</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><i class="fa-solid fa-clock-rotate-left"></i> Broadcast History</div>
        <div class="card-body">
          <div class="filter-row">
            <div class="col" style="flex:1 1 auto; text-align:right; min-width:auto;">
              <div class="pagination">
                <button id="broadcastPrevBtn" class="btn btn-muted"><i class="fa-solid fa-chevron-left"></i> Prev</button>
                <span class="muted">Page <span id="broadcastPage">1</span></span>
                <button id="broadcastNextBtn" class="btn btn-muted">Next <i class="fa-solid fa-chevron-right"></i></button>
              </div>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr>
                  <th><i class="fa-solid fa-heading"></i> Title</th>
                  <th><i class="fa-solid fa-users"></i> Target</th>
                  <th><i class="fa-solid fa-message"></i> Type</th>
                  <th><i class="fa-solid fa-user"></i> Sent By</th>
                  <th><i class="fa-solid fa-calendar"></i> Date</th>
                  <th><i class="fa-solid fa-eye"></i> Views</th>
                </tr>
              </thead>
              <tbody id="broadcastRows"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Task Modal -->
  <div id="editTaskModal" class="modal">
    <div class="modal-content">
      <div class="modal-header"><i class="fa-solid fa-pen-to-square"></i> Edit Task</div>
      <form id="editTaskForm">
        <input type="hidden" name="id" id="editTaskId">
        <div class="form-group">
          <label><i class="fa-solid fa-heading"></i> Title</label>
          <input type="text" name="title" id="editTaskTitle" required>
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-tag"></i> Category</label>
          <select name="category" id="editTaskCategory" required>
            <option value="tiktok"><i class="fa-brands fa-tiktok"></i> TikTok</option>
            <option value="youtube"><i class="fa-brands fa-youtube"></i> YouTube</option>
            <option value="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</option>
            <option value="facebook"><i class="fa-brands fa-facebook"></i> Facebook Ads</option>
            <option value="instagram"><i class="fa-brands fa-instagram"></i> Instagram Ads</option>
            <option value="ads"><i class="fa-solid fa-ad"></i> Ads</option>
            <option value="blogs"><i class="fa-solid fa-blog"></i> Blogs</option>
            <option value="trivia"><i class="fa-solid fa-question"></i> Trivia</option>
          </select>
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-coins"></i> Reward Price</label>
          <input type="number" step="0.01" min="0" name="price" id="editTaskPrice" required>
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-wallet"></i> Reward Wallet</label>
          <select name="reward_wallet" id="editTaskWallet">
            <option value="main"><i class="fa-solid fa-house"></i> Main</option>
            <option value="tiktok"><i class="fa-brands fa-tiktok"></i> TikTok</option>
            <option value="youtube"><i class="fa-brands fa-youtube"></i> YouTube</option>
            <option value="whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</option>
            <option value="facebook"><i class="fa-brands fa-facebook"></i> Facebook</option>
            <option value="instagram"><i class="fa-brands fa-instagram"></i> Instagram</option>
          </select>
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-book"></i> Instructions</label>
          <textarea name="instructions" id="editTaskInstructions"></textarea>
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-link"></i> Target URL (optional)</label>
          <input type="text" name="target_url" id="editTaskTargetUrl">
        </div>
        <div class="form-group">
          <label><i class="fa-solid fa-image"></i> Image URL (optional)</label>
          <input type="text" name="image_url" id="editTaskImageUrl">
        </div>
        <div class="form-group">
          <label class="switch">
            <input type="checkbox" name="active" id="editTaskActive">
            <i class="fa-solid fa-toggle-on"></i> Active
          </label>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-muted" onclick="closeModal('editTaskModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="loadingOverlay" class="overlay">
    <div class="loader">
      <i class="fa-solid fa-spinner fa-spin"></i> Processing...
    </div>
  </div>

  <script>
    // Global variables
    let currentPage = 1;
    let currentTab = 'tasks';
    let tasks = [];
    let users = [];
    let withdrawals = [];
    let broadcasts = [];

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize tabs
      const tabs = document.querySelectorAll('.tab');
      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          const tabName = tab.getAttribute('data-tab');
          switchTab(tabName);
        });
      });

      // Initialize buttons
      document.getElementById('logoutBtn').addEventListener('click', logout);
      document.getElementById('createForm').addEventListener('submit', createTask);
      document.getElementById('editTaskForm').addEventListener('submit', updateTask);
      document.getElementById('broadcastForm').addEventListener('submit', sendBroadcast);
      document.getElementById('applyFilters').addEventListener('click', loadTasks);
      
      // Pagination
      document.getElementById('prevBtn').addEventListener('click', () => changePage(-1));
      document.getElementById('nextBtn').addEventListener('click', () => changePage(1));
      document.getElementById('userPrevBtn').addEventListener('click', () => changeUserPage(-1));
      document.getElementById('userNextBtn').addEventListener('click', () => changeUserPage(1));
      document.getElementById('withdrawalPrevBtn').addEventListener('click', () => changeWithdrawalPage(-1));
      document.getElementById('withdrawalNextBtn').addEventListener('click', () => changeWithdrawalPage(1));
      document.getElementById('broadcastPrevBtn').addEventListener('click', () => changeBroadcastPage(-1));
      document.getElementById('broadcastNextBtn').addEventListener('click', () => changeBroadcastPage(1));
      
      // Search and filters
      document.getElementById('userSearch').addEventListener('input', debounce(loadUsers, 300));
      document.getElementById('userStatusFilter').addEventListener('change', loadUsers);
      document.getElementById('withdrawalStatusFilter').addEventListener('change', loadWithdrawals);
      
      // Load initial data
      loadTasks();
      loadUsers();
      loadWithdrawals();
      loadStatistics();
      loadBroadcasts();
    });

    // Tab switching
    function switchTab(tabName) {
      // Update active tab
      document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.getAttribute('data-tab') === tabName) {
          tab.classList.add('active');
        }
      });
      
      // Show active content
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
        if (content.id === tabName) {
          content.classList.add('active');
        }
      });
      
      currentTab = tabName;
      
      // Load data if needed
      if (tabName === 'statistics') {
        loadStatistics();
      } else if (tabName === 'broadcast') {
        loadBroadcasts();
      }
    }

    // Load tasks from server
    function loadTasks() {
      showLoading();
      const category = document.getElementById('fCategory').value;
      const active = document.getElementById('fActive').value;
      
      // In a real app, this would be an API call
      setTimeout(() => {
        // Simulate API response
        tasks = [
          { id: 1, title: 'Watch TikTok Video (30 sec)', category: 'tiktok', price: 0.50, wallet: 'main', active: true, created_at: '2023-10-15', instructions: 'Watch the video for at least 30 seconds and like it.', target_url: 'https://tiktok.com/@user/video/123', image_url: 'https://example.com/image1.jpg' },
          { id: 2, title: 'Subscribe to YouTube Channel', category: 'youtube', price: 1.20, wallet: 'youtube', active: true, created_at: '2023-10-14', instructions: 'Subscribe to the channel and watch at least one video.', target_url: 'https://youtube.com/channel/abc', image_url: 'https://example.com/image2.jpg' },
          { id: 3, title: 'Share WhatsApp Status', category: 'whatsapp', price: 0.80, wallet: 'whatsapp', active: false, created_at: '2023-10-13', instructions: 'Share our status on your WhatsApp for 24 hours.', target_url: '', image_url: '' },
          { id: 4, title: 'Facebook Ad Engagement', category: 'facebook', price: 0.75, wallet: 'main', active: true, created_at: '2023-10-12', instructions: 'Like and comment on our Facebook ad post.', target_url: 'https://facebook.com/post/123', image_url: 'https://example.com/image3.jpg' },
          { id: 5, title: 'Instagram Story View', category: 'instagram', price: 0.60, wallet: 'instagram', active: true, created_at: '2023-10-11', instructions: 'View our Instagram story for full duration.', target_url: 'https://instagram.com/story/123', image_url: '' },
        ];
        
        // Apply filters
        let filteredTasks = tasks;
        if (category) {
          filteredTasks = filteredTasks.filter(task => task.category === category);
        }
        if (active !== '') {
          const isActive = active === 'true';
          filteredTasks = filteredTasks.filter(task => task.active === isActive);
        }
        
        renderTasks(filteredTasks);
        hideLoading();
        showFeedback('Tasks loaded successfully', 'success');
      }, 800);
    }

    // Render tasks to the table
    function renderTasks(tasks) {
      const tbody = document.getElementById('taskRows');
      tbody.innerHTML = '';
      
      if (tasks.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No tasks found</td></tr>`;
        return;
      }
      
      tasks.forEach(task => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${task.title}</td>
          <td><span class="badge badge-${getCategoryBadge(task.category)}">${getCategoryIcon(task.category)} ${task.category}</span></td>
          <td>$${task.price.toFixed(2)}</td>
          <td><span class="badge badge-blue">${task.wallet}</span></td>
          <td><span class="status-dot ${task.active ? 'dot-green' : 'dot-grey'}"></span> ${task.active ? 'Active' : 'Inactive'}</td>
          <td>${task.created_at}</td>
          <td>
            <button class="btn btn-secondary" onclick="editTask(${task.id})"><i class="fa-solid fa-pen"></i> Edit</button>
            <button class="btn ${task.active ? 'btn-warning' : 'btn-success'}" onclick="toggleTask(${task.id}, ${!task.active})">
              <i class="fa-solid fa-toggle-${task.active ? 'on' : 'off'}"></i> ${task.active ? 'Deactivate' : 'Activate'}
            </button>
            <button class="btn btn-danger" onclick="deleteTask(${task.id})"><i class="fa-solid fa-trash"></i> Delete</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    // Create a new task
    function createTask(e) {
      e.preventDefault();
      showLoading();
      
      const formData = new FormData(e.target);
      const active = document.getElementById('activeInput').checked;
      
      // In a real app, this would be an API call
      setTimeout(() => {
        const newTask = {
          id: tasks.length + 1,
          title: formData.get('title'),
          category: formData.get('category'),
          price: parseFloat(formData.get('price')),
          wallet: formData.get('reward_wallet'),
          active: active,
          instructions: formData.get('instructions'),
          target_url: formData.get('target_url'),
          image_url: formData.get('image_url'),
          created_at: new Date().toISOString().split('T')[0]
        };
        
        tasks.unshift(newTask);
        renderTasks(tasks);
        e.target.reset();
        hideLoading();
        showFeedback('Task created successfully', 'success');
      }, 1000);
    }

    // Edit task - open modal
    function editTask(id) {
      const task = tasks.find(t => t.id === id);
      if (!task) return;
      
      document.getElementById('editTaskId').value = task.id;
      document.getElementById('editTaskTitle').value = task.title;
      document.getElementById('editTaskCategory').value = task.category;
      document.getElementById('editTaskPrice').value = task.price;
      document.getElementById('editTaskWallet').value = task.wallet;
      document.getElementById('editTaskInstructions').value = task.instructions;
      document.getElementById('editTaskTargetUrl').value = task.target_url;
      document.getElementById('editTaskImageUrl').value = task.image_url;
      document.getElementById('editTaskActive').checked = task.active;
      
      openModal('editTaskModal');
    }

    // Update task
    function updateTask(e) {
      e.preventDefault();
      showLoading();
      
      const formData = new FormData(e.target);
      const id = parseInt(formData.get('id'));
      
      // In a real app, this would be an API call
      setTimeout(() => {
        const taskIndex = tasks.findIndex(t => t.id === id);
        if (taskIndex !== -1) {
          tasks[taskIndex] = {
            ...tasks[taskIndex],
            title: formData.get('title'),
            category: formData.get('category'),
            price: parseFloat(formData.get('price')),
            wallet: formData.get('reward_wallet'),
            instructions: formData.get('instructions'),
            target_url: formData.get('target_url'),
            image_url: formData.get('image_url'),
            active: document.getElementById('editTaskActive').checked
          };
          
          renderTasks(tasks);
          closeModal('editTaskModal');
          hideLoading();
          showFeedback('Task updated successfully', 'success');
        }
      }, 800);
    }

    // Toggle task status
    function toggleTask(id, active) {
      showLoading();
      
      // In a real app, this would be an API call
      setTimeout(() => {
        const taskIndex = tasks.findIndex(t => t.id === id);
        if (taskIndex !== -1) {
          tasks[taskIndex].active = active;
          renderTasks(tasks);
          hideLoading();
          showFeedback(`Task ${active ? 'activated' : 'deactivated'} successfully`, 'success');
        }
      }, 800);
    }

    // Delete task
    function deleteTask(id) {
      if (!confirm('Are you sure you want to delete this task?')) return;
      
      showLoading();
      
      // In a real app, this would be an API call
      setTimeout(() => {
        tasks = tasks.filter(t => t.id !== id);
        renderTasks(tasks);
        hideLoading();
        showFeedback('Task deleted successfully', 'success');
      }, 800);
    }

    // Load users from server
    function loadUsers() {
      showLoading();
      const search = document.getElementById('userSearch').value;
      const status = document.getElementById('userStatusFilter').value;
      
      // In a real app, this would be an API call
      setTimeout(() => {
        // Simulate API response
        users = [
          { id: 1, name: 'John Doe', email: 'john@example.com', phone: '+1234567890', balance: 25.50, status: 'active', country: 'United States', joined: '2023-09-15' },
          { id: 2, name: 'Jane Smith', email: 'jane@example.com', phone: '+0987654321', balance: 12.75, status: 'active', country: 'Canada', joined: '2023-09-20' },
          { id: 3, name: 'Robert Johnson', email: 'robert@example.com', phone: '+1122334455', balance: 8.20, status: 'inactive', country: 'United Kingdom', joined: '2023-10-01' },
          { id: 4, name: 'Maria Garcia', email: 'maria@example.com', phone: '+5566778899', balance: 42.30, status: 'active', country: 'Spain', joined: '2023-10-05' },
          { id: 5, name: 'Ahmed Hassan', email: 'ahmed@example.com', phone: '+9988776655', balance: 3.50, status: 'inactive', country: 'Egypt', joined: '2023-10-10' },
        ];
        
        // Apply filters
        let filteredUsers = users;
        if (search) {
          const searchLower = search.toLowerCase();
          filteredUsers = filteredUsers.filter(user => 
            user.name.toLowerCase().includes(searchLower) || 
            user.email.toLowerCase().includes(searchLower)
          );
        }
        if (status) {
          filteredUsers = filteredUsers.filter(user => user.status === status);
        }
        
        renderUsers(filteredUsers);
        hideLoading();
      }, 800);
    }

    // Render users to the table
    function renderUsers(users) {
      const tbody = document.getElementById('userRows');
      tbody.innerHTML = '';
      
      if (users.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;">No users found</td></tr>`;
        return;
      }
      
      users.forEach(user => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${user.name}</td>
          <td>${user.email}</td>
          <td>${user.phone}</td>
          <td>$${user.balance.toFixed(2)}</td>
          <td><span class="badge ${user.status === 'active' ? 'badge-green' : 'badge-red'}">${user.status}</span></td>
          <td>${user.country}</td>
          <td>${user.joined}</td>
          <td>
            <button class="btn btn-secondary" onclick="viewUser(${user.id})"><i class="fa-solid fa-eye"></i> View</button>
            <button class="btn ${user.status === 'active' ? 'btn-warning' : 'btn-success'}" onclick="toggleUserStatus(${user.id}, '${user.status === 'active' ? 'inactive' : 'active'}')">
              <i class="fa-solid fa-user-${user.status === 'active' ? 'slash' : 'check'}"></i> ${user.status === 'active' ? 'Deactivate' : 'Activate'}
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    // View user details
    function viewUser(id) {
      const user = users.find(u => u.id === id);
      if (!user) return;
      
      alert(`User Details:\nName: ${user.name}\nEmail: ${user.email}\nPhone: ${user.phone}\nBalance: $${user.balance.toFixed(2)}\nStatus: ${user.status}\nCountry: ${user.country}\nJoined: ${user.joined}`);
    }

    // Toggle user status
    function toggleUserStatus(id, status) {
      showLoading();
      
      // In a real app, this would be an API call
      setTimeout(() => {
        const userIndex = users.findIndex(u => u.id === id);
        if (userIndex !== -1) {
          users[userIndex].status = status;
          renderUsers(users);
          hideLoading();
          showFeedback(`User ${status === 'active' ? 'activated' : 'deactivated'} successfully`, 'success');
        }
      }, 800);
    }

    // Load withdrawals from server
    function loadWithdrawals() {
      showLoading();
      const status = document.getElementById('withdrawalStatusFilter').value;
      
      // In a real app, this would be an API call
      setTimeout(() => {
        // Simulate API response
        withdrawals = [
          { id: 1, user: 'John Doe', amount: 20.00, wallet: 'PayPal', address: 'john@example.com', status: 'pending', requested: '2023-10-15' },
          { id: 2, user: 'Jane Smith', amount: 10.50, wallet: 'Bitcoin', address: '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa', status: 'approved', requested: '2023-10-14' },
          { id: 3, user: 'Robert Johnson', amount: 5.00, wallet: 'Ethereum', address: '0x742d35Cc6634C0532925a3b844Bc454e4438f44e', status: 'rejected', requested: '2023-10-13' },
          { id: 4, user: 'Maria Garcia', amount: 30.00, wallet: 'Bank Transfer', address: 'XXXX-XXXX-XXXX-1234', status: 'pending', requested: '2023-10-12' },
          { id: 5, user: 'Ahmed Hassan', amount: 15.75, wallet: 'Skrill', address: 'ahmed@example.com', status: 'pending', requested: '2023-10-11' },
        ];
        
        // Apply filters
        let filteredWithdrawals = withdrawals;
        if (status) {
          filteredWithdrawals = filteredWithdrawals.filter(withdrawal => withdrawal.status === status);
        }
        
        renderWithdrawals(filteredWithdrawals);
        hideLoading();
      }, 800);
    }

    // Render withdrawals to the table
    function renderWithdrawals(withdrawals) {
      const tbody = document.getElementById('withdrawalRows');
      tbody.innerHTML = '';
      
      if (withdrawals.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No withdrawals found</td></tr>`;
        return;
      }
      
      withdrawals.forEach(withdrawal => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${withdrawal.user}</td>
          <td>$${withdrawal.amount.toFixed(2)}</td>
          <td>${withdrawal.wallet}</td>
          <td>${withdrawal.address}</td>
          <td><span class="badge ${getWithdrawalStatusBadge(withdrawal.status)}">${withdrawal.status}</span></td>
          <td>${withdrawal.requested}</td>
          <td>
            ${withdrawal.status === 'pending' ? `
              <button class="btn btn-success" onclick="updateWithdrawal(${withdrawal.id}, 'approved')"><i class="fa-solid fa-check"></i> Approve</button>
              <button class="btn btn-danger" onclick="updateWithdrawal(${withdrawal.id}, 'rejected')"><i class="fa-solid fa-xmark"></i> Reject</button>
            ` : `
              <button class="btn btn-secondary" onclick="viewWithdrawal(${withdrawal.id})"><i class="fa-solid fa-eye"></i> View</button>
            `}
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    // Update withdrawal status
    function updateWithdrawal(id, status) {
      showLoading();
      
      // In a real app, this would be an API call
      setTimeout(() => {
        const withdrawalIndex = withdrawals.findIndex(w => w.id === id);
        if (withdrawalIndex !== -1) {
          withdrawals[withdrawalIndex].status = status;
          renderWithdrawals(withdrawals);
          hideLoading();
          showFeedback(`Withdrawal ${status} successfully`, 'success');
        }
      }, 800);
    }

    // View withdrawal details
    function viewWithdrawal(id) {
      const withdrawal = withdrawals.find(w => w.id === id);
      if (!withdrawal) return;
      
      alert(`Withdrawal Details:\nUser: ${withdrawal.user}\nAmount: $${withdrawal.amount.toFixed(2)}\nWallet: ${withdrawal.wallet}\nAddress: ${withdrawal.address}\nStatus: ${withdrawal.status}\nRequested: ${withdrawal.requested}`);
    }

    // Load statistics
    function loadStatistics() {
      showLoading();
      
      // In a real app, this would be an API call
      setTimeout(() => {
        // Simulate API response
        document.getElementById('totalUsers').textContent = '1,254';
        document.getElementById('activeUsers').textContent = '892';
        document.getElementById('totalTasks').textContent = '156';
        document.getElementById('activeTasks').textContent = '128';
        document.getElementById('totalWithdrawals').textContent = '342';
        document.getElementById('pendingWithdrawals').textContent = '23';
        document.getElementById('totalPayout').textContent = '$5,342.75';
        document.getElementById('platformProfit').textContent = '$1,245.30';
        
        // In a real app, we would render charts here
        hideLoading();
      }, 1000);
    }

    // Load broadcasts
    function loadBroadcasts() {
      showLoading();
      
      // In a real app, this would be an API call
      setTimeout(() => {
        // Simulate API response
        broadcasts = [
          { id: 1, title: 'System Maintenance', target: 'all', type: 'info', sent_by: 'Admin', date: '2023-10-15', views: '1,254' },
          { id: 2, title: 'New Tasks Available', target: 'active', type: 'update', sent_by: 'Admin', date: '2023-10-14', views: '892' },
          { id: 3, title: 'Withdrawal Processing Delay', target: 'withdrawals', type: 'warning', sent_by: 'Admin', date: '2023-10-13', views: '342' },
          { id: 4, title: 'New Feature Announcement', target: 'all', type: 'important', sent_by: 'Admin', date: '2023-10-12', views: '1,102' },
        ];
        
        renderBroadcasts(broadcasts);
        hideLoading();
      }, 800);
    }

    // Render broadcasts to the table
    function renderBroadcasts(broadcasts) {
      const tbody = document.getElementById('broadcastRows');
      tbody.innerHTML = '';
      
      if (broadcasts.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">No broadcasts found</td></tr>`;
        return;
      }
      
      broadcasts.forEach(broadcast => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${broadcast.title}</td>
          <td><span class="badge badge-blue">${broadcast.target}</span></td>
          <td><span class="badge ${getBroadcastTypeBadge(broadcast.type)}">${broadcast.type}</span></td>
          <td>${broadcast.sent_by}</td>
          <td>${broadcast.date}</td>
          <td>${broadcast.views}</td>
        `;
        tbody.appendChild(row);
      });
    }

    // Send broadcast message
    function sendBroadcast(e) {
      e.preventDefault();
      showLoading();
      
      const formData = new FormData(e.target);
      
      // In a real app, this would be an API call
      setTimeout(() => {
        const newBroadcast = {
          id: broadcasts.length + 1,
          title: formData.get('title'),
          target: formData.get('target'),
          type: formData.get('type'),
          sent_by: 'Admin',
          date: new Date().toISOString().split('T')[0],
          views: '0'
        };
        
        broadcasts.unshift(newBroadcast);
        renderBroadcasts(broadcasts);
        e.target.reset();
        hideLoading();
        showFeedback('Broadcast sent successfully', 'success');
      }, 1000);
    }

    // Helper functions
    function getCategoryIcon(category) {
      const icons = {
        'tiktok': 'fa-brands fa-tiktok',
        'youtube': 'fa-brands fa-youtube',
        'whatsapp': 'fa-brands fa-whatsapp',
        'facebook': 'fa-brands fa-facebook',
        'instagram': 'fa-brands fa-instagram',
        'ads': 'fa-solid fa-ad',
        'blogs': 'fa-solid fa-blog',
        'trivia': 'fa-solid fa-question'
      };
      return `<i class="${icons[category] || 'fa-solid fa-tasks'}"></i>`;
    }

    function getCategoryBadge(category) {
      const badges = {
        'tiktok': 'purple',
        'youtube': 'red',
        'whatsapp': 'green',
        'facebook': 'blue',
        'instagram': 'pink',
        'ads': 'yellow',
        'blogs': 'blue',
        'trivia': 'grey'
      };
      return badges[category] || 'grey';
    }

    function getWithdrawalStatusBadge(status) {
      const badges = {
        'pending': 'yellow',
        'approved': 'green',
        'rejected': 'red'
      };
      return badges[status] || 'grey';
    }

    function getBroadcastTypeBadge(type) {
      const badges = {
        'info': 'blue',
        'warning': 'yellow',
        'important': 'red',
        'update': 'green'
      };
      return badges[type] || 'grey';
    }

    function changePage(delta) {
      currentPage += delta;
      if (currentPage < 1) currentPage = 1;
      document.getElementById('page').textContent = currentPage;
      loadTasks();
    }

    function changeUserPage(delta) {
      currentPage += delta;
      if (currentPage < 1) currentPage = 1;
      document.getElementById('userPage').textContent = currentPage;
      loadUsers();
    }

    function changeWithdrawalPage(delta) {
      currentPage += delta;
      if (currentPage < 1) currentPage = 1;
      document.getElementById('withdrawalPage').textContent = currentPage;
      loadWithdrawals();
    }

    function changeBroadcastPage(delta) {
      currentPage += delta;
      if (currentPage < 1) currentPage = 1;
      document.getElementById('broadcastPage').textContent = currentPage;
      loadBroadcasts();
    }

    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function showLoading() {
      document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoading() {
      document.getElementById('loadingOverlay').style.display = 'none';
    }

    function logout() {
      if (confirm('Are you sure you want to log out?')) {
        showLoading();
        // In a real app, this would call the logout API
        setTimeout(() => {
          window.location.href = '/frontend/auth/login.php';
        }, 800);
      }
    }

    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    // Enhanced Feedback System
    function showFeedback(message, type = 'info', title = null) {
      const container = document.getElementById('feedbackContainer');
      
      // Set title based on type if not provided
      if (!title) {
        switch (type) {
          case 'success': title = 'Success'; break;
          case 'error': title = 'Error'; break;
          case 'warning': title = 'Warning'; break;
          default: title = 'Information';
        }
      }
      
      // Set icon based on type
      let icon;
      switch (type) {
        case 'success': icon = 'fa-circle-check'; break;
        case 'error': icon = 'fa-circle-exclamation'; break;
        case 'warning': icon = 'fa-triangle-exclamation'; break;
        default: icon = 'fa-circle-info';
      }
      
      const toast = document.createElement('div');
      toast.className = `feedback-toast ${type}`;
      toast.innerHTML = `
        <div class="feedback-toast-icon"><i class="fa-solid ${icon}"></i></div>
        <div class="feedback-toast-content">
          <div class="feedback-toast-title">${title}</div>
          <div class="feedback-toast-message">${message}</div>
        </div>
        <button class="feedback-toast-close" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        <div class="feedback-toast-progress"><div class="feedback-toast-progress-bar"></div></div>
      `;
      
      container.appendChild(toast);
      
      // Remove toast after animation completes
      setTimeout(() => {
        if (toast.parentElement) {
          toast.remove();
        }
      }, 3000);
    }
  </script>
</body>
</html>