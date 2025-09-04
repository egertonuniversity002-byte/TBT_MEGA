<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Matrix Platform</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  html, body {
    height: 100%;
    margin: 0;
    font-family: 'Inter', sans-serif;
    background: #f3f4e9;
    color: #1a202c;
  }

  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  
  .app-container {
    display: flex;
    flex-grow: 1;
    height: 100%;
    min-height: 100%;
    transition: margin-left 0.3s ease-in-out;
  }

  /* Sidebar Drawer */
  .sidebar {
    width: 250px;
    background: #fdfdf5;
    display: flex;
    flex-direction: column;
    padding: 1rem 0;
    position: sticky;
    top: 0;
    height: 100%;
    overflow-y: auto;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    box-shadow: 2px 0 10px rgba(0,0,0,0.05);
  }
  
  .sidebar a {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 0.8rem 1rem;
    color: #4a5568;
    text-decoration: none;
    border-left: 4px solid transparent;
    cursor: pointer;
    transition: all 0.3s;
  }

  .sidebar .submenu-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.8rem 1rem;
    color: #4a5568;
    text-decoration: none;
    border-left: 4px solid transparent;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .sidebar a.active, .sidebar a:hover, .sidebar .submenu-btn:hover {
    color: #38a169;
    border-left: 4px solid #38a169;
    background: rgba(56, 161, 105, 0.1);
  }

  .sidebar i {
    margin-right: 10px;
    color: #64748b;
  }
  
  .sidebar a.active i, .sidebar a:hover i, .sidebar .submenu-btn:hover i {
      color: #38a169;
  }
  
  /* Section divider */
  .section-divider {
      color: #718096;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-bottom: 1px solid #e2e8f0;
      margin-bottom: 0;
      text-transform: uppercase;
      font-size: 0.85rem;
  }
  
  /* Submenu */
  .submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    padding-left: 1.5rem;
  }

  .submenu.open {
      max-height: 500px;
  }

  .submenu a {
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
  }
  
  /* Hamburger toggle for mobile */
  .hamburger {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    font-size: 28px;
    color: #38a169;
    cursor: pointer;
    z-index: 200;
  }
  
  /* Overlay for mobile drawer */
  .overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.4);
    z-index: 10;
    display: none;
    transition: opacity 0.3s ease-in-out;
    opacity: 0;
  }

  .overlay.visible {
    display: block;
    opacity: 1;
  }

  /* Main content */
  .main-content {
    flex: 1;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
  }
  
  /* Loader */
  .loader-overlay {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 200px;
    height: 200px;
    background: rgba(243, 244, 233, 0.95);
    justify-content: center;
    align-items: center;
    z-index: 1000;
    flex-direction: column;
    color: #1a202c;
    border-radius: 10px;
  }
  .loader-overlay .loader-spinner {
    width: 60px;
    height: 60px;
    border: 6px solid #e2e8f0;
    border-top: 6px solid #38a169;
    border-radius: 50%;
    animation: spin 1.2s linear infinite;
  }
  .loader-overlay p { margin-top: 12px; }

  /* Spin animation */
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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

  /* Responsive media queries */
  @media screen and (max-width: 768px) {
    .app-container {
      flex-direction: column;
      height: 100vh;
    }
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      transform: translateX(-100%);
      box-shadow: none;
      z-index: 50;
    }
    .sidebar.open {
      transform: translateX(0);
      box-shadow: 0 0 20px rgba(0,0,0,0.5);
    }
    .hamburger {
      display: block;
    }
    .main-content {
      padding: 1rem;
      padding-top: 4rem;
    }
    
    .feedback-container {
      top: 70px;
      right: 10px;
      left: 10px;
      max-width: none;
    }

    /* Mobile notifications dropdown */
    #notificationsDropdown {
      position: fixed !important;
      top: 56px !important; /* Below header */
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      max-width: none !important;
      margin-top: 0 !important;
      border-radius: 0 !important;
      max-height: calc(100vh - 56px) !important;
      overflow-y: auto !important;
      z-index: 60;
    }

    /* Mobile notifications overlay */
    .notifications-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      z-index: 55;
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease-in-out;
    }

    .notifications-overlay.visible {
      display: block;
      opacity: 1;
    }
  }
</style>
</head>
<body>

<!-- Feedback Toast Container -->
<div class="feedback-container" id="feedbackContainer"></div>

<!-- Fixed Top Header -->
<header class="topbar fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 shadow-md">
  <div class="max-w-7xl mx-auto px-3 h-14 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <button id="hamburger" class="text-white text-xl p-2 rounded hover:bg-white/10 focus:outline-none" aria-label="Open sidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="flex items-center gap-2 text-white font-semibold select-none">
        <i class="fa-solid fa-layer-group text-yellow-200"></i>
        <span>Matrix</span>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <div class="relative">
        <button id="notificationsBtn" class="text-white p-2 rounded hover:bg-white/10 relative" aria-label="Notifications">
          <i class="fa-solid fa-bell text-orange-200"></i>
          <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        </button>
        <div id="notificationsDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 hidden max-h-96 overflow-y-auto">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
            <p class="text-sm text-gray-600">You have <span id="notificationCount">0</span> unread notifications</p>
          </div>
          <div id="notificationsList" class="divide-y divide-gray-200">
            <!-- Notifications will be loaded here -->
          </div>
          <div class="p-4 border-t border-gray-200">
            <button id="markAllReadBtn" class="w-full bg-blue-600 text-white rounded-lg py-2 hover:bg-blue-700 transition-colors">Mark All as Read</button>
            <button id="viewAllNotificationsBtn" class="w-full mt-2 bg-gray-100 text-gray-700 rounded-lg py-2 hover:bg-gray-200 transition-colors">View All Notifications</button>
          </div>
        </div>
      </div>
      <div class="relative">
        <button id="languageBtn" class="flex items-center gap-2 text-white p-2 rounded hover:bg-white/10">
          <i class="fa-solid fa-globe text-white/80"></i>
          <span id="currentLanguage">EN</span>
          <i class="fa-solid fa-chevron-down text-white/80 text-xs"></i>
        </button>
        <div id="languageMenu" class="absolute right-0 mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 hidden">
          <button data-lang="en" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">English</button>
          <button data-lang="sw" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Swahili</button>
        </div>
      </div>
      <div class="relative">
        <button id="profileBtn" class="flex items-center gap-2 text-white p-1 rounded hover:bg-white/10">
          <span id="avatarCircle" class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">U</span>
          <i class="fa-solid fa-chevron-down text-white/80 text-xs"></i>
        </button>
        <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden">
          <button id="openAvatarModal" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Update Picture</button>
          <a href="#" data-page="profile" class="block px-4 py-2 text-sm hover:bg-gray-50">Profile</a>
          <a href="#" id="logoutBtn" class="block px-4 py-2 text-sm hover:bg-gray-50">Logout</a>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="overlay" id="overlay"></div>

<!-- Avatar Modal -->
<div id="avatarModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
  <div class="bg-white w-11/12 max-w-md rounded-2xl p-6 shadow-xl">
    <h3 class="text-lg font-bold mb-2">Update Profile Picture</h3>
    <p class="text-sm text-gray-500 mb-4">Choose an image (JPG/PNG). Max ~2MB.</p>
    <div class="flex items-center gap-4">
      <img id="avatarPreview" src="" alt="Preview" class="w-16 h-16 rounded-full object-cover bg-gray-100 border" />
      <input id="avatarFile" type="file" accept="image/*" class="block w-full text-sm text-gray-700" />
    </div>
    <div id="avatarMsg" class="text-sm mt-3 hidden"></div>
    <div class="mt-5 flex justify-end gap-2">
      <button id="avatarCancel" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">Cancel</button>
      <button id="avatarSave" class="px-4 py-2 rounded-full bg-green-600 text-white hover:bg-green-700">Save</button>
    </div>
  </div>
</div>

<div class="app-container" style="padding-top:56px;">
  <!-- Sidebar Drawer -->
  <div class="sidebar" id="sidebar">
    <h2 class="text-3xl font-bold text-center mb-6 text-green-500"><i class="fa-solid fa-gauge-high mr-2"></i> Matrix</h2>
    
    <!-- ACCOUNT SECTION -->
    <div class="section-divider">ACCOUNTS</div>
    <a href="#" data-page="dashboard" class="active"><i class="fa-solid fa-house"></i> <span>Dashboard</span></a>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-ticket"></i><span>Voucher</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="buy_voucher">Buy Voucher</a>
        <a href="#" data-page="voucher_history">View History</a>
      </div>
    </div>
    
    <a href="#" data-page="deposit"><i class="fa-solid fa-arrow-down"></i> <span>Deposit</span></a>
    <a href="#" data-page="pay_for_client"><i class="fa-solid fa-hand-holding-dollar"></i> <span>Pay For Client</span></a>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-money-bill-wave"></i><span>Withdraw</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="withdraw_balance">Withdraw Balance</a>
        <a href="#" data-page="withdraw_youtube">Withdraw YouTube</a>
        <a href="#" data-page="withdraw_trivia">Withdraw Trivia</a>
        <a href="#" data-page="withdraw_games">Withdraw Games</a>
        <a href="#" data-page="withdraw_tiktok">Withdraw TikTok</a>
      </div>
    </div>
    
    <a href="#" data-page="withdrawal_history"><i class="fa-solid fa-clock-rotate-left"></i> <span>Withdrawal History</span></a>
    <a href="#" data-page="whatsapp"><i class="fa-brands fa-whatsapp"></i> <span>WhatsApp</span></a>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-users"></i><span>Team</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <div class="submenu-btn">Level 1 <i class="fa-solid fa-chevron-down ml-auto"></i></div>
        <div class="submenu">
          <a href="#" data-page="team/level1/active">Active</a>
          <a href="#" data-page="team/level1/inactive">Inactive</a>
        </div>
        <div class="submenu-btn">Level 2 <i class="fa-solid fa-chevron-down ml-auto"></i></div>
        <div class="submenu">
          <a href="#" data-page="team/level2/active">Active</a>
          <a href="#" data-page="team/level2/inactive">Inactive</a>
        </div>
        <div class="submenu-btn">Level 3 <i class="fa-solid fa-chevron-down ml-auto"></i></div>
        <div class="submenu">
          <a href="#" data-page="team/level3/active">Active</a>
          <a href="#" data-page="team/level3/inactive">Inactive</a>
        </div>
        <a href="#" data-page="team_all_active">All Active</a>
        <a href="#" data-page="team_all_inactive">All Inactive</a>
      </div>
    </div>
    
    <!-- PRODUCTS SECTION -->
    <div class="section-divider">PRODUCTS</div>
    <a href="#" data-page="spin_and_win"><i class="fa-solid fa-dice"></i> <span>Spin & Win</span></a>
    <a href="#" data-page="fixed_bets"><i class="fa-solid fa-coins"></i> <span>Fixed Bets</span></a>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-gamepad"></i><span>Games</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="chess">Chess</a>
        <a href="#" data-page="checkers">Checkers</a>
        <a href="#" data-page="aviator_game">Aviator</a>
      </div>
    </div>
    
    <a href="#" data-page="soccer"><i class="fa-solid fa-futbol"></i> <span>Soccer</span></a>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-plane"></i><span>Aviator</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="play_aviator">Play Now</a>
        <a href="#" data-page="aviator_revshare">Aviator Rev Share</a>
      </div>
    </div>
    
    <a href="#" data-page="buy_aviator"><i class="fa-solid fa-cart-shopping"></i> <span>Buy Aviator</span></a>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-store"></i><span>Free Shop</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="easy_duka">EASY DUKA</a>
      </div>
    </div>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-film"></i><span>Movies</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="watch_movie">Watch Movie</a>
      </div>
    </div>
    
    <div>
      <div class="submenu-btn"><i class="fa-brands fa-facebook"></i><span>Meta Ads</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="facebook_ads">Facebook Ads</a>
        <a href="#" data-page="instagram_ads">Instagram Ads</a>
      </div>
    </div>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-newspaper"></i><span>Articles</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="post_articles">Post Articles</a>
        <a href="#" data-page="all_articles">All Articles</a>
      </div>
    </div>
    
    <a href="#" data-page="forex_lessons"><i class="fa-solid fa-chart-line"></i> <span>Forex Lessons</span></a>
    <a href="#" data-page="claim_followers"><i class="fa-solid fa-user-plus"></i> <span>Claim Followers</span></a>
    
    <div>
      <div class="submenu-btn"><i class="fa-brands fa-tiktok"></i><span>TikTok Earn</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="tiktok_earn">Earn</a>
        <a href="#" data-page="tiktok_followers">Get Followers/Likes</a>
      </div>
    </div>
    
    <div>
      <div class="submenu-btn"><i class="fa-brands fa-youtube"></i><span>YouTube Earn</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="youtube_earn">Earn</a>
      </div>
    </div>
    
    <div>
      <div class="submenu-btn"><i class="fa-solid fa-question"></i><span>Trivia Challenge</span> <i class="fa-solid fa-chevron-down ml-auto"></i></div>
      <div class="submenu">
        <a href="#" data-page="trivia">Trivia</a>
        <a href="#" data-page="trivia_history">Trivia History</a>
      </div>
    </div>
    
    <a href="#" data-page="business_books"><i class="fa-solid fa-book"></i> <span>Business Books</span></a>
    <a href="#" data-page="buy_airtime"><i class="fa-solid fa-mobile-screen-button"></i> <span>Buy Airtime</span></a>
    
    <!-- SETTINGS SECTION -->
    <div class="section-divider">SETTINGS</div>
    <a href="#" data-page="profile"><i class="fa-solid fa-user"></i> <span>Profile</span></a>
    <a href="#" data-page="contact_us"><i class="fa-solid fa-envelope"></i> <span>Contact Us</span></a>
    <a href="#" id="sidebarLogoutBtn"><i class="fa-solid fa-right-from-bracket"></i> <span>Log Out</span></a>
  </div>

  <!-- Main Content Area -->
  <div class="main-content" id="mainContent">
    <!-- Dashboard content will be loaded here -->
  </div>

  <!-- Overlay Modal for Pages -->
  <div id="pageOverlay" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 backdrop-blur-sm bg-black/40" id="pageOverlayBackdrop"></div>
    <div class="relative z-10 h-full w-full flex items-center justify-center p-4">
      <div class="bg-white w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl border border-gray-200 animate-[fadeIn_.2s_ease]">
        <div class="flex items-center justify-between sticky top-0 bg-white/90 backdrop-blur px-4 py-3 border-b rounded-t-2xl">
          <h3 id="overlayTitle" class="text-lg font-semibold text-gray-800">Loading...</h3>
          <button id="overlayClose" class="text-gray-600 hover:text-gray-900 p-2 rounded-full hover:bg-gray-100" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div id="overlayContent" class="p-4"></div>
      </div>
    </div>
  </div>
</div>

<!-- Loader -->
<div class="loader-overlay" id="loader">
  <div class="loader-spinner"></div>
  <p>Loading content...</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Enhanced feedback system
        function showFeedback(message, type = 'info', title = '', duration = 3000) {
            const container = document.getElementById('feedbackContainer');
            const toast = document.createElement('div');
            toast.className = `feedback-toast ${type}`;
            
            // Set icon based on type
            let icon = 'fa-info-circle';
            if (type === 'success') icon = 'fa-check-circle';
            if (type === 'error') icon = 'fa-exclamation-circle';
            if (type === 'warning') icon = 'fa-exclamation-triangle';
            
            // Set default title if not provided
            if (!title) {
                if (type === 'success') title = 'Success';
                if (type === 'error') title = 'Error';
                if (type === 'warning') title = 'Warning';
                if (type === 'info') title = 'Information';
            }
            
            toast.innerHTML = `
                <div class="feedback-toast-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="feedback-toast-content">
                    <div class="feedback-toast-title">${title}</div>
                    <div class="feedback-toast-message">${message}</div>
                </div>
                <button class="feedback-toast-close">
                    <i class="fas fa-times"></i>
                </button>
                <div class="feedback-toast-progress">
                    <div class="feedback-toast-progress-bar"></div>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.style.animation = 'toastIn 0.5s ease forwards';
            }, 10);
            
            // Close button functionality
            const closeBtn = toast.querySelector('.feedback-toast-close');
            closeBtn.addEventListener('click', () => {
                toast.style.animation = 'toastOut 0.5s ease forwards';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 500);
            });
            
            // Auto remove after duration
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.animation = 'toastOut 0.5s ease forwards';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.parentNode.removeChild(toast);
                        }
                    }, 500);
                }
            }, duration);
            
            return toast;
        }

        // Auto-detect language from browser
        function detectLanguage() {
            const browserLang = navigator.language || navigator.userLanguage;
            let langCode = 'en';
            
            if (browserLang.startsWith('sw')) {
                langCode = 'sw';
            }
            
            // Update UI
            document.getElementById('currentLanguage').textContent = langCode.toUpperCase();
            
            // Show notification
            if (langCode === 'sw') {
                showFeedback('Lugha imekaguliwa kiotomatiki: Kiswahili', 'info', 'Lugha');
            } else {
                showFeedback('Language auto-detected: English', 'info', 'Language');
            }
            
            return langCode;
        }

        // Load dashboard data
        function loadDashboardData() {
            const loader = document.getElementById('loader');
            const mainContent = document.getElementById('mainContent');
            
            // Show loader
            loader.style.display = 'flex';
            
            // Simulate API call
            setTimeout(() => {
                // Sample data
                const dashboardData = {
                    userName: 'John Doe',
                    todayEarnings: 350.25,
                    totalEarnings: 1250.75,
                    earningsGrowth: '+12.5%',
                    balance: 850.50,
                    withdrawn: 400.25,
                    affiliateEarnings: 225.75,
                    agentBonus: 100.00,
                    adsEarnings: 75.50,
                    tiktokEarnings: 50.25,
                    youtubeEarnings: 35.75,
                    triviaEarnings: 20.50,
                    blogEarnings: 15.25,
                    invested: 500.00,
                    profit: 125.75,
                    affiliateLink: 'https://matrixplatform.com/register?ref=12345'
                };
                
                // Render dashboard with original wallet cards
                mainContent.innerHTML = `
                    <div class="p-4 sm:p-8">
                        <!-- Welcome Section -->
                        <div class="bg-emerald-600 rounded-3xl shadow-xl p-8 sm:p-12 md:p-16 mb-8 text-white transform transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div>
                                    <h1 class="text-3xl sm:text-4xl font-bold">Welcome, <span id="userName" class="text-green-200">${dashboardData.userName}</span>!</h1>
                                    <p class="mt-1 opacity-90 text-sm sm:text-base">Boost your online impact with <span class="font-bold text-green-100">MULAPAL'S 10+ digital tools!</span></p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-sm opacity-80">Today's Earnings</p>
                                        <p class="text-3xl sm:text-4xl font-bold text-white" id="todayEarnings">$${dashboardData.todayEarnings}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Earnings Cards Section - Restored to original design -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            <!-- Total Earnings Card -->
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 col-span-1 sm:col-span-2 lg:col-span-1 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">TOTAL EARNINGS</h3>
                                    <i class="fas fa-wallet text-xl text-blue-500 opacity-75"></i>
                                </div>
                                <p class="text-4xl sm:text-5xl font-extrabold text-blue-600" id="totalEarnings">$${dashboardData.totalEarnings}</p>
                                <p class="text-sm mt-1 text-gray-500" id="earningsGrowth">${dashboardData.earningsGrowth} since yesterday</p>
                            </div>
                        
                            <!-- Balance Card -->
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Balance</h3>
                                    <i class="fas fa-coins text-xl text-green-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold text-green-600" id="currentBalance">$${dashboardData.balance}</p>
                                <p class="text-sm text-gray-500 mt-2">Your available balance for withdrawal.</p>
                            </div>
                        
                            <!-- Withdrawn Card -->
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Withdrawn</h3>
                                    <i class="fas fa-exchange-alt text-xl text-orange-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold text-orange-600" id="totalWithdrawn">$${dashboardData.withdrawn}</p>
                                <p class="text-sm text-gray-500 mt-2">Total amount withdrawn to date.</p>
                            </div>
                        
                            <!-- Affiliate Earnings Card -->
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Affiliate Earnings</h3>
                                    <i class="fas fa-users text-xl text-yellow-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold text-yellow-600" id="affiliateEarnings">$${dashboardData.affiliateEarnings}</p>
                                <p class="text-sm text-gray-500 mt-2">Earnings from your team referrals.</p>
                            </div>
                        
                            <!-- Other Earnings Cards Grid -->
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Agent Bonus</h3>
                                    <i class="fas fa-award text-xl text-cyan-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold text-cyan-600" id="agentBonus">$${dashboardData.agentBonus}</p>
                                <p class="text-sm text-gray-500 mt-2">Bonus for reaching your agent goals.</p>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Ads Earnings</h3>
                                    <i class="fas fa-ad text-xl text-cyan-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="adsEarnings">$${dashboardData.adsEarnings}</p>
                                <p class="text-sm text-gray-500 mt-2">Earnings from ad views and clicks.</p>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">TikTok Earnings</h3>
                                    <i class="fab fa-tiktok text-xl text-cyan-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="tiktokEarnings">$${dashboardData.tiktokEarnings}</p>
                                <p class="text-sm text-gray-500 mt-2">Earnings from TikTok activities.</p>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">YouTube Earnings</h3>
                                    <i class="fab fa-youtube text-xl text-cyan-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="youtubeEarnings">$${dashboardData.youtubeEarnings}</p>
                                <p class="text-sm text-gray-500 mt-2">Earnings from YouTube activities.</p>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Trivia Earnings</h3>
                                    <i class="fas fa-question-circle text-xl text-cyan-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="triviaEarnings">$${dashboardData.triviaEarnings}</p>
                                <p class="text-sm text-gray-500 mt-2">Earnings from trivia challenges.</p>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Blog Earnings</h3>
                                    <i class="fas fa-blog text-xl text-cyan-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="blogEarnings">$${dashboardData.blogEarnings}</p>
                                <p class="text-sm text-gray-500 mt-2">Earnings from blog activities.</p>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Invested</h3>
                                    <i class="fas fa-chart-line text-xl text-purple-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold mt-2 text-purple-600" id="investedAmount">$${dashboardData.invested}</p>
                                <p class="text-sm text-gray-500 mt-2">Total amount invested in the platform.</p>
                            </div>
                            <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">Profit</h3>
                                    <i class="fas fa-trophy text-xl text-emerald-500 opacity-75"></i>
                                </div>
                                <p class="text-3xl sm:text-4xl font-bold mt-2 text-emerald-600" id="profitAmount">$${dashboardData.profit}</p>
                                <p class="text-sm text-gray-500 mt-2">Total profit earned from investments.</p>
                            </div>
                        </div>
                        
                        <!-- Affiliate Link Section -->
                        <div class="bg-white rounded-3xl shadow-lg p-6 sm:p-8 mb-8 transform transition-all duration-300 hover:scale-[1.01] hover:shadow-xl">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Your Affiliate Link</h3>
                            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                                <input type="text" id="affiliateLink" class="flex-grow p-3 border border-gray-300 rounded-lg text-sm" readonly value="${dashboardData.affiliateLink}">
                                <button id="copyLinkBtn" class="bg-blue-600 text-white rounded-full px-6 py-3 shadow-md hover:bg-blue-700 active:scale-95 transition-all whitespace-nowrap">
                                    <i class="fas fa-copy mr-2"></i> Copy Link
                                </button>
                            </div>
                        </div>
                        
                        <!-- Timetable Section -->
                        <div class="bg-white rounded-3xl shadow-lg p-6 sm:p-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Weekly Earnings Timetable</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-600">
                                            <th class="py-3 px-4 rounded-tl-lg">Product</th>
                                            <th class="py-3 px-4">Day 1</th>
                                            <th class="py-3 px-4">Day 2</th>
                                            <th class="py-3 px-4">Day 3</th>
                                            <th class="py-3 px-4 rounded-tr-lg">Day 4</th>
                                        </tr>
                                    </thead>
                                    <tbody id="timetableBody">
                                        <tr class="border-b last:border-0 border-gray-200 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">TikTok Earnings</td>
                                            <td class="py-3 px-4">$12.50</td>
                                            <td class="py-3 px-4">$15.75</td>
                                            <td class="py-3 px-4">$18.25</td>
                                            <td class="py-3 px-4">$20.50</td>
                                        </tr>
                                        <tr class="border-b last:border-0 border-gray-200 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">YouTube Earnings</td>
                                            <td class="py-3 px-4">$8.75</td>
                                            <td class="py-3 px-4">$10.25</td>
                                            <td class="py-3 px-4">$12.50</td>
                                            <td class="py-3 px-4">$15.00</td>
                                        </tr>
                                        <tr class="border-b last:border-0 border-gray-200 hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">Affiliate Earnings</td>
                                            <td class="py-3 px-4">$25.00</td>
                                            <td class="py-3 px-4">$28.50</td>
                                            <td class="py-3 px-4">$32.75</td>
                                            <td class="py-3 px-4">$35.25</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add event listener for the copy button
                document.getElementById('copyLinkBtn').addEventListener('click', () => {
                    const affiliateLink = document.getElementById('affiliateLink');
                    affiliateLink.select();
                    document.execCommand('copy');
                    
                    // Show success message
                    showFeedback('Affiliate link copied to clipboard!', 'success');
                });
                
                // Hide loader
                loader.style.display = 'none';
                
                // Show success message
                showFeedback('Dashboard loaded successfully', 'success');
            }, 1500);
        }

        // Load notifications
        function loadNotifications() {
            const notifications = [
                {
                    id: 1,
                    title: 'New Reward Available',
                    message: 'You have a new reward waiting to be claimed',
                    time: '2 hours ago',
                    read: false
                },
                {
                    id: 2,
                    title: 'Deposit Successful',
                    message: 'Your deposit of $500 has been processed',
                    time: '5 hours ago',
                    read: true
                },
                {
                    id: 3,
                    title: 'Referral Bonus',
                    message: 'You earned $25 from a referral',
                    time: '1 day ago',
                    read: false
                },
                {
                    id: 4,
                    title: 'System Update',
                    message: 'New features have been added to the platform',
                    time: '2 days ago',
                    read: true
                }
            ];
            
            const unreadCount = notifications.filter(n => !n.read).length;
            
            // Update badge
            const badge = document.getElementById('notificationBadge');
            const countElement = document.getElementById('notificationCount');
            
            if (unreadCount > 0) {
                badge.classList.remove('hidden');
                badge.textContent = unreadCount;
                countElement.textContent = unreadCount;
            } else {
                badge.classList.add('hidden');
                countElement.textContent = '0';
            }
            
            // Render notifications list
            const notificationsList = document.getElementById('notificationsList');
            notificationsList.innerHTML = '';
            
            notifications.forEach(notification => {
                const notificationElement = document.createElement('div');
                notificationElement.className = `p-4 hover:bg-gray-50 cursor-pointer ${!notification.read ? 'bg-blue-50' : ''}`;
                notificationElement.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-semibold ${!notification.read ? 'text-blue-700' : 'text-gray-800'}">${notification.title}</h4>
                            <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                            <p class="text-xs text-gray-400 mt-2">${notification.time}</p>
                        </div>
                        ${!notification.read ? '<span class="w-2 h-2 bg-blue-500 rounded-full ml-2 mt-1"></span>' : ''}
                    </div>
                `;
                
                notificationElement.addEventListener('click', () => {
                    // Mark as read
                    notification.read = true;
                    loadNotifications();
                    showFeedback('Notification marked as read', 'success');
                });
                
                notificationsList.appendChild(notificationElement);
            });
        }

        // Setup event listeners
        function setupEventListeners() {
            // Mobile sidebar toggle
            document.getElementById('hamburger').addEventListener('click', () => {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('overlay');
                sidebar.classList.toggle('open');
                overlay.classList.toggle('visible');
            });
            
            // Overlay click to close sidebar
            document.getElementById('overlay').addEventListener('click', () => {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('overlay');
                sidebar.classList.remove('open');
                overlay.classList.remove('visible');
            });
            
            // Notifications dropdown
            const notificationsBtn = document.getElementById('notificationsBtn');
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            
            notificationsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationsDropdown.classList.toggle('hidden');
            });
            
            // Close notifications when clicking outside
            document.addEventListener('click', (e) => {
                if (!notificationsBtn.contains(e.target) && !notificationsDropdown.contains(e.target)) {
                    notificationsDropdown.classList.add('hidden');
                }
            });
            
            // Language dropdown
            const languageBtn = document.getElementById('languageBtn');
            const languageMenu = document.getElementById('languageMenu');
            
            languageBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                languageMenu.classList.toggle('hidden');
            });
            
            // Close language menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!languageBtn.contains(e.target) && !languageMenu.contains(e.target)) {
                    languageMenu.classList.add('hidden');
                }
            });
            
            // Language selection
            document.querySelectorAll('#languageMenu button').forEach(button => {
                button.addEventListener('click', () => {
                    const lang = button.getAttribute('data-lang');
                    document.getElementById('currentLanguage').textContent = lang.toUpperCase();
                    languageMenu.classList.add('hidden');
                    
                    if (lang === 'sw') {
                        showFeedback('Lughimekobadilishwa kuwa Kiswahili', 'success', 'Lugha');
                    } else {
                        showFeedback('Language changed to English', 'success', 'Language');
                    }
                });
            });
            
            // Profile dropdown
            const profileBtn = document.getElementById('profileBtn');
            const profileMenu = document.getElementById('profileMenu');
            
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });
            
            // Close profile menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.add('hidden');
                }
            });
            
            // Logout functionality
            const logoutBtn = document.getElementById('logoutBtn');
            const sidebarLogoutBtn = document.getElementById('sidebarLogoutBtn');
            
            const logout = () => {
                showFeedback('Logging out...', 'info');
                setTimeout(() => {
                    showFeedback('You have been logged out successfully', 'success');
                    // In a real app, this would redirect to login page
                }, 1500);
            };
            
            if (logoutBtn) logoutBtn.addEventListener('click', logout);
            if (sidebarLogoutBtn) sidebarLogoutBtn.addEventListener('click', logout);
            
            // Submenu toggle
            document.querySelectorAll('.submenu-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const submenu = btn.nextElementSibling;
                    submenu.classList.toggle('open');
                    
                    // Rotate icon
                    const icon = btn.querySelector('.fa-chevron-down');
                    icon.classList.toggle('fa-rotate-180');
                });
            });
            
            // Sidebar navigation
            document.querySelectorAll('.sidebar a[data-page]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    // Remove active class from all links
                    document.querySelectorAll('.sidebar a').forEach(a => {
                        a.classList.remove('active');
                    });
                    
                    // Add active class to clicked link
                    link.classList.add('active');
                    
                    // Close sidebar on mobile
                    if (window.innerWidth <= 768) {
                        document.getElementById('sidebar').classList.remove('open');
                        document.getElementById('overlay').classList.remove('visible');
                    }
                    
                    // Show loading message
                    const pageName = link.getAttribute('data-page').replace(/_/g, ' ');
                    showFeedback(`Loading ${pageName}...`, 'info');
                });
            });
            
            // Mark all as read button
            document.getElementById('markAllReadBtn').addEventListener('click', () => {
                showFeedback('All notifications marked as read', 'success');
                // Close dropdown
                notificationsDropdown.classList.add('hidden');
            });
            
            // View all notifications button
            document.getElementById('viewAllNotificationsBtn').addEventListener('click', () => {
                showFeedback('Opening all notifications...', 'info');
                // Close dropdown
                notificationsDropdown.classList.add('hidden');
            });
        }

        // Initialize the application
        function initApp() {
            // Auto-detect language
            detectLanguage();
            
            // Load dashboard
            loadDashboardData();
            
            // Load notifications
            loadNotifications();
            
            // Setup event listeners
            setupEventListeners();
            
            // Show welcome message
            setTimeout(() => {
                showFeedback('Welcome to Matrix Platform!', 'success', 'Welcome');
            }, 1000);
        }

        // Start the app
        initApp();
    });
</script>
</body>
</html>