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
    inset: 0;
    background: rgba(243, 244, 233, 0.95);
    justify-content: center;
    align-items: center;
    z-index: 1000;
    flex-direction: column;
    color: #1a202c;
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
  }
</style>
</head>
<body>

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
      <button class="text-white p-2 rounded hover:bg-white/10" aria-label="Notifications">
        <i class="fa-solid fa-bell text-orange-200"></i>
      </button>
      <div class="relative">
        <button id="profileBtn" class="flex items-center gap-2 text-white p-1 rounded hover:bg-white/10">
          <span id="avatarCircle" class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">U</span>
          <i class="fa-solid fa-chevron-down text-white/80 text-xs"></i>
        </button>
        <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden">
          <button id="openAvatarModal" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50">Update Picture</button>
          <a href="#" data-page="profile" class="block px-4 py-2 text-sm hover:bg-gray-50">Profile</a>
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
    <a href="../pay/payout.php" data-page="withdraw_balance">Withdraw Balance</a>
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
         <div class="submenu-btn">Level 1
           <div class="submenu"><a href="#" data-page="team_level">Active</a></div>
           <div class="submenu"><a href="#" data-page="team_level_inactive">Inactive</a></div>
         </div>
                  <div class="submenu-btn">Level 2
           <div class="submenu"><a href="#" data-page="team_level">Active</a></div>
           <div class="submenu"><a href="#" data-page="team_level_inactive">Inactive</a></div>
         </div>
                  <div class="submenu-btn">Level 3
                    
           <div class="submenu"><a href="#" data-page="team_level">Active</a></div>
           <div class="submenu"><a href="#" data-page="team_level_inactive">Inactive</a></div>
         </div>
          <a href="#" data-page="team_level2">Level 2</a>
          <a href="#" data-page="team_level3">Level 3</a>
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
    <a href="#" data-page="logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Log Out</span></a>
  </div>

  <!-- Main Content Area -->
  <div class="main-content" id="mainContent">
    <!-- Dashboard base content here when no overlay is open -->
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
        const sidebar = document.getElementById('sidebar');
        const hamburger = document.getElementById('hamburger');
        const overlay = document.getElementById('overlay');
        const mainContent = document.getElementById('mainContent');
        const loader = document.getElementById('loader');

        // This function will simulate fetching dashboard data from a server
    const API_BASE = window.API_BASE;

    function fetchDashboardData() {
        return new Promise(async (resolve) => {
            try {
                const token = localStorage.getItem('token');
                const res = await fetch(`${API_BASE}/dashboard`, { headers: { 'Authorization': `Bearer ${token}` }});
                const data = await res.json();
                if (data.status === 'success') return resolve(data.data);
            } catch (e) {}
            // fallback demo data
            setTimeout(() => {
                const defaultData = {
                    user: {
                        name: 'User',
                        todayEarnings: '0',
                        totalEarnings: '0',
                        earningsGrowth: '+0%',
                        balance: '0',
                        withdrawn: '0',
                        affiliateEarnings: '0',
                        agentBonus: '0',
                        adsEarnings: '0',
                        tiktokEarnings: '0',
                        youtubeEarnings: '0',
                        triviaEarnings: '0',
                        blogEarnings: '0',
                        invested: '0',
                        profit: '0',
                        currency: 'USD',
                        currencySymbol: '$',
                        affiliateLink: '#'
                    },
                    timetable: []
                };
                resolve(defaultData);
            }, 800);
        });
    }
        
        // This is a generic "coming soon" page generator
        function renderComingSoon(pageName) {
            const displayTitle = pageName.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            return `
                <div class="p-8 bg-white rounded-3xl shadow-lg text-center">
                    <i class="fa-solid fa-hourglass-half text-6xl text-gray-500 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">${displayTitle}</h2>
                    <p class="text-gray-600">This feature is coming soon!</p>
                </div>
            `;
        }

        // This map contains simulated HTML content for different pages
        const pageContentMap = {
            'deposit': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Deposit</h2>
                    <p class="text-gray-600">Enter amount and proceed to PesaPal checkout.</p>
                    <div class="mt-6">
                        <label for="depositAmount" class="block text-sm font-medium text-gray-700">Amount (KSH)</label>
                        <input type="number" id="depositAmount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="1000">
                    </div>
                    <button id="depositBtn" class="mt-4 bg-green-600 text-white rounded-full px-8 py-3 shadow-md hover:bg-green-700 active:scale-95 transition-all">Proceed to Deposit</button>
                    <p id="depositMsg" class="mt-3 text-sm text-red-600 hidden"></p>
                </div>
            `,
            'pay_for_client': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Pay For Client</h2>
                    <p class="text-gray-600">Transfer funds to a client's account.</p>
                    <div class="mt-6 space-y-4">
                        <div>
                            <label for="clientUsername" class="block text-sm font-medium text-gray-700">Client Username</label>
                            <input type="text" id="clientUsername" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="transferAmount" class="block text-sm font-medium text-gray-700">Amount (KSH)</label>
                            <input type="number" id="transferAmount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                    </div>
                    <button class="mt-4 bg-indigo-600 text-white rounded-full px-8 py-3 shadow-md hover:bg-indigo-700 active:scale-95 transition-all">Transfer Funds</button>
                </div>
            `,
            'withdrawal_history': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Withdrawal History</h2>
                    <p class="text-gray-600">A log of all your previous withdrawal transactions.</p>
                    <div class="overflow-x-auto mt-6">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600">
                                    <th class="py-3 px-4 rounded-tl-lg">Date</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 rounded-tr-lg">Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be filled from API -->
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <button id="wdPrev" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Prev</button>
                        <span class="text-sm text-gray-600">Page <span id="wdPage">1</span> of <span id="wdPages">1</span></span>
                        <button id="wdNext" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Next</button>
                    </div>
                </div>
            `,
            'profile': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">User Profile</h2>
                    <p class="text-gray-600">View and edit your personal information.</p>
                    <div class="mt-6 space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Full Name</span>
                            <span class="text-lg text-gray-800 font-semibold">Lizbeth</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Email Address</span>
                            <span class="text-lg text-gray-800 font-semibold">lizbeth@example.com</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Phone Number</span>
                            <span class="text-lg text-gray-800 font-semibold">+254 712 345 678</span>
                        </div>
                    </div>
                    <button class="mt-6 bg-indigo-600 text-white rounded-full px-8 py-3 shadow-md hover:bg-indigo-700 active:scale-95 transition-all">Edit Profile</button>
                </div>
            `,
            'contact_us': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Contact Us</h2>
                    <p class="text-gray-600 mb-6">Have a question or need support? Reach out to us!</p>
                    <div class="space-y-4">
                        <p><i class="fas fa-envelope mr-2 text-indigo-600"></i> Email: <a href="mailto:support@matrix.com" class="text-indigo-600 hover:underline">support@matrix.com</a></p>
                        <p><i class="fas fa-phone mr-2 text-indigo-600"></i> Phone: +254 7xx xxx xxx</p>
                    </div>
                </div>
            `,
            // ALL other pages now use the generic "Coming Soon" renderer
            'buy_voucher': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                  <h2 class="text-2xl font-bold text-gray-800 mb-3">Buy Voucher</h2>
                  <p class="text-gray-600">Purchase a voucher to activate account or services.</p>
                  <div class="mt-5 grid md:grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Amount (KSH)</label>
                      <input id="bvAmount" type="number" placeholder="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-700">Quantity</label>
                      <input id="bvQty" type="number" min="1" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                  </div>
                  <button id="bvBtn" class="mt-4 bg-green-600 text-white rounded-full px-8 py-3 shadow-md hover:bg-green-700 active:scale-95 transition-all">Buy Now</button>
                  <div id="bvMsg" class="mt-3 hidden text-sm"></div>
                </div>
              `,
            'voucher_history': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                  <h2 class="text-2xl font-bold text-gray-800 mb-3">Voucher History</h2>
                  <p class="text-gray-600">Your previously purchased vouchers.</p>
                  <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-left table-auto">
                      <thead>
                        <tr class="bg-gray-100 text-gray-600">
                          <th class="py-3 px-4 rounded-tl-lg">Date</th>
                          <th class="py-3 px-4">Amount</th>
                          <th class="py-3 px-4">Code</th>
                          <th class="py-3 px-4 rounded-tr-lg">Status</th>
                        </tr>
                      </thead>
                      <tbody id="vhBody"></tbody>
                    </table>
                  </div>
                  <div class="mt-4 flex items-center justify-between">
                    <button id="vhPrev" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Prev</button>
                    <span class="text-sm text-gray-600">Page <span id="vhPage">1</span></span>
                    <button id="vhNext" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Next</button>
                  </div>
                </div>
              `,
            'withdraw_balance': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Withdraw</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount (KSH)</label>
                            <input type="number" id="wdAmount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Wallet</label>
                            <select id="wdWallet" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="main">Main</option>
                                <option value="tiktok">tiktok</option>
                                <option value="youtube">youtube</option>
                                <option value="whatsapp">whatsapp</option>
                                <option value="facebook">facebook</option>
                                <option value="instagram">instagram</option>
                            </select>
                        </div>
                    </div>
                    <button id="wdBtn" class="mt-4 bg-emerald-600 text-white rounded-full px-8 py-3 shadow-md hover:bg-emerald-700 active:scale-95 transition-all">Request Withdrawal</button>
                    <p id="wdMsg" class="mt-3 text-sm text-red-600 hidden"></p>
                </div>
            `,
            'withdraw_youtube': renderComingSoon('withdraw_youtube'),
            'withdraw_trivia': renderComingSoon('withdraw_trivia'),
            'withdraw_games': renderComingSoon('withdraw_games'),
            'withdraw_tiktok': renderComingSoon('withdraw_tiktok'),
            'whatsapp': `
    <div class="p-8 bg-white rounded-3xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">WhatsApp Tasks</h2>
        <p class="text-gray-600">Share content and earn rewards to your designated wallet.</p>
        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left table-auto">
                <thead>
                    <tr class="bg-gray-100 text-gray-600">
                        <th class="py-3 px-4 rounded-tl-lg">Task</th>
                        <th class="py-3 px-4">Reward</th>
                        <th class="py-3 px-4">Wallet</th>
                        <th class="py-3 px-4 rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody id="waTasksBody"></tbody>
            </table>
            <div class="mt-4 flex items-center justify-between">
                <button id="waPrev" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Prev</button>
                <span class="text-sm text-gray-600">Page <span id="waPage">1</span></span>
                <button id="waNext" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Next</button>
            </div>
        </div>
    </div>
`,
            'team_level1': renderComingSoon('team_level1'),
            'team_level2': renderComingSoon('team_level2'),
            'team_level3': renderComingSoon('team_level3'),
            'team_all_active': renderComingSoon('team_all_active'),
            'team_all_inactive': renderComingSoon('team_all_inactive'),
            'spin_and_win': renderComingSoon('spin_and_win'),
            'fixed_bets': renderComingSoon('fixed_bets'),
            'chess': renderComingSoon('chess'),
            'checkers': renderComingSoon('checkers'),
            'aviator_game': renderComingSoon('aviator_game'),
            'soccer': renderComingSoon('soccer'),
            'play_aviator': renderComingSoon('play_aviator'),
            'aviator_revshare': renderComingSoon('aviator_revshare'),
            'buy_aviator': renderComingSoon('buy_aviator'),
            'easy_duka': renderComingSoon('easy_duka'),
            'watch_movie': renderComingSoon('watch_movie'),
            'facebook_ads': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Facebook Ads Tasks</h2>
                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600">
                                    <th class="py-3 px-4 rounded-tl-lg">Task</th>
                                    <th class="py-3 px-4">Reward</th>
                                    <th class="py-3 px-4">Wallet</th>
                                    <th class="py-3 px-4 rounded-tr-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody id="fbTasksBody"></tbody>
                        </table>
                        <div class="mt-4 flex items-center justify-between">
                            <button id="fbPrev" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Prev</button>
                            <span class="text-sm text-gray-600">Page <span id="fbPage">1</span></span>
                            <button id="fbNext" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Next</button>
                        </div>
                    </div>
                </div>
            `,
            'instagram_ads': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Instagram Ads Tasks</h2>
                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600">
                                    <th class="py-3 px-4 rounded-tl-lg">Task</th>
                                    <th class="py-3 px-4">Reward</th>
                                    <th class="py-3 px-4">Wallet</th>
                                    <th class="py-3 px-4 rounded-tr-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody id="igTasksBody"></tbody>
                        </table>
                        <div class="mt-4 flex items-center justify-between">
                            <button id="igPrev" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Prev</button>
                            <span class="text-sm text-gray-600">Page <span id="igPage">1</span></span>
                            <button id="igNext" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Next</button>
                        </div>
                    </div>
                </div>
            `,
            'post_articles': renderComingSoon('post_articles'),
            'all_articles': renderComingSoon('all_articles'),
            'forex_lessons': renderComingSoon('forex_lessons'),
            'claim_followers': renderComingSoon('claim_followers'),
            'tiktok_earn': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">TikTok Earn</h2>
                    <p class="text-gray-600">Complete tasks and earn rewards to your designated wallet.</p>
                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600">
                                    <th class="py-3 px-4 rounded-tl-lg">Task</th>
                                    <th class="py-3 px-4">Reward</th>
                                    <th class="py-3 px-4">Wallet</th>
                                    <th class="py-3 px-4 rounded-tr-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody id="ttTasksBody"></tbody>
                        </table>
                        <div class="mt-4 flex items-center justify-between">
                            <button id="ttPrev" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Prev</button>
                            <span class="text-sm text-gray-600">Page <span id="ttPage">1</span></span>
                            <button id="ttNext" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Next</button>
                        </div>
                    </div>
                </div>
            `,
            'tiktok_followers': renderComingSoon('tiktok_followers'),
            'youtube_earn': `
                <div class="p-8 bg-white rounded-3xl shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">YouTube Earn</h2>
                    <p class="text-gray-600">Complete tasks and earn rewards to your designated wallet.</p>
                    <div class="mt-6 overflow-x-auto">
                        <table class="w-full text-left table-auto">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600">
                                    <th class="py-3 px-4 rounded-tl-lg">Task</th>
                                    <th class="py-3 px-4">Reward</th>
                                    <th class="py-3 px-4">Wallet</th>
                                    <th class="py-3 px-4 rounded-tr-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody id="ytTasksBody"></tbody>
                        </table>
                        <div class="mt-4 flex items-center justify-between">
                            <button id="ytPrev" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Prev</button>
                            <span class="text-sm text-gray-600">Page <span id="ytPage">1</span></span>
                            <button id="ytNext" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:opacity-50">Next</button>
                        </div>
                    </div>
                </div>
            `,
            'trivia': renderComingSoon('trivia'),
            'trivia_history': renderComingSoon('trivia_history'),
            'business_books': renderComingSoon('business_books'),
            'buy_airtime': renderComingSoon('buy_airtime'),
            'logout': renderComingSoon('logout')
        };

        // Function to update dashboard earnings with data
        function updateDashboardEarnings(data) {
            const symbol = data.user.currencySymbol || data.user.currency || '';
            const fmt = (v) => {
                const num = Number(v ?? 0);
                return `${symbol} ${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            };
            document.getElementById('userName').textContent = data.user.name;
            document.getElementById('todayEarnings').textContent = fmt(data.user.todayEarnings);
            document.getElementById('totalEarnings').textContent = fmt(data.user.totalEarnings);
            document.getElementById('earningsGrowth').textContent = data.user.earningsGrowth;
            document.getElementById('currentBalance').textContent = fmt(data.user.balance);
            document.getElementById('totalWithdrawn').textContent = fmt(data.user.withdrawn);
            document.getElementById('affiliateEarnings').textContent = fmt(data.user.affiliateEarnings);
            document.getElementById('agentBonus').textContent = fmt(data.user.agentBonus);
            document.getElementById('adsEarnings').textContent = fmt(data.user.adsEarnings);
            document.getElementById('tiktokEarnings').textContent = fmt(data.user.tiktokEarnings);
            document.getElementById('youtubeEarnings').textContent = fmt(data.user.youtubeEarnings);
            document.getElementById('triviaEarnings').textContent = fmt(data.user.triviaEarnings);
            document.getElementById('blogEarnings').textContent = fmt(data.user.blogEarnings);
            document.getElementById('investedAmount').textContent = fmt(data.user.invested);
            document.getElementById('profitAmount').textContent = fmt(data.user.profit);
            document.getElementById('affiliateLink').value = data.user.affiliateLink;
        }

        // This function renders the main dashboard view
        function renderDashboard(data) {
            // Get the main content area and reset its content
            const mainContentDiv = document.getElementById('mainContent');
            mainContentDiv.innerHTML = `
                <div class="p-4 sm:p-8">
                  <!-- Welcome Section -->
                  <div class="bg-emerald-600 rounded-3xl shadow-xl p-8 sm:p-12 md:p-16 mb-8 text-white transform transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl">
                      <div class="flex items-center justify-between flex-wrap gap-4">
                          <div>
                              <h1 class="text-3xl sm:text-4xl font-bold">Welcome, <span id="userName" class="text-green-200">User</span>!</h1>
                              <p class="mt-1 opacity-90 text-sm sm:text-base">Boost your online impact with <span class="font-bold text-green-100">MULAPAL'S 10+ digital tools!</span></p>
                          </div>
                          <div class="flex items-center space-x-4">
                              <div class="text-right">
                                  <p class="text-sm opacity-80">Today's Earnings</p>
                                  <p class="text-3xl sm:text-4xl font-bold text-white" id="todayEarnings">0</p>
                              </div>
                          </div>
                      </div>
                  </div>
                  
                  <!-- Earnings Cards Section -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                      <!-- Total Earnings Card -->
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 col-span-1 sm:col-span-2 lg:col-span-1 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">TOTAL EARNINGS</h3>
                              <i class="fas fa-wallet text-xl text-blue-500 opacity-75"></i>
                          </div>
                          <p class="text-4xl sm:text-5xl font-extrabold text-blue-600" id="totalEarnings">0</p>
                          <p class="text-sm mt-1 text-gray-500" id="earningsGrowth">+0% since yesterday</p>
                      </div>
                  
                      <!-- Balance Card -->
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Balance</h3>
                              <i class="fas fa-coins text-xl text-green-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold text-green-600" id="currentBalance">0</p>
                          <p class="text-sm text-gray-500 mt-2">Your available balance for withdrawal.</p>
                      </div>
                  
                      <!-- Withdrawn Card -->
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Withdrawn</h3>
                              <i class="fas fa-exchange-alt text-xl text-orange-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold text-orange-600" id="totalWithdrawn">0</p>
                          <p class="text-sm text-gray-500 mt-2">Total amount withdrawn to date.</p>
                      </div>
                  
                      <!-- Affiliate Earnings Card -->
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Affiliate Earnings</h3>
                              <i class="fas fa-users text-xl text-yellow-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold text-yellow-600" id="affiliateEarnings">0</p>
                          <p class="text-sm text-gray-500 mt-2">Earnings from your team referrals.</p>
                      </div>
                  
                      <!-- Other Earnings Cards Grid -->
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Agent Bonus</h3>
                              <i class="fas fa-award text-xl text-cyan-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold text-cyan-600" id="agentBonus">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Bonus for reaching your agent goals.</p>
                      </div>
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Ads Earnings</h3>
                              <i class="fas fa-ad text-xl text-cyan-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="adsEarnings">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Earnings from ad views and clicks.</p>
                      </div>
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">TikTok Earnings</h3>
                              <i class="fab fa-tiktok text-xl text-cyan-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="tiktokEarnings">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Earnings from TikTok activities.</p>
                      </div>
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">YouTube Earnings</h3>
                              <i class="fab fa-youtube text-xl text-cyan-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="youtubeEarnings">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Earnings from YouTube activities.</p>
                      </div>
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Trivia Earnings</h3>
                              <i class="fas fa-question-circle text-xl text-cyan-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="triviaEarnings">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Earnings from trivia challenges.</p>
                      </div>
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Blog Earnings</h3>
                              <i class="fas fa-blog text-xl text-cyan-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold mt-2 text-cyan-600" id="blogEarnings">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Earnings from blog activities.</p>
                      </div>
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Invested</h3>
                              <i class="fas fa-chart-line text-xl text-purple-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold mt-2 text-purple-600" id="investedAmount">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Total amount invested in the platform.</p>
                      </div>
                      <div class="bg-white rounded-3xl shadow-lg px-6 py-4 sm:px-8 sm:py-6 md:px-10 md:py-8 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl flex flex-col justify-between">
                          <div class="flex items-center justify-between mb-2">
                              <h3 class="text-lg font-semibold text-gray-800">Profit</h3>
                              <i class="fas fa-trophy text-xl text-emerald-500 opacity-75"></i>
                          </div>
                          <p class="text-3xl sm:text-4xl font-bold mt-2 text-emerald-600" id="profitAmount">KSH 0</p>
                          <p class="text-sm text-gray-500 mt-2">Total profit earned from investments.</p>
                      </div>
                  </div>
                  
                  <!-- Affiliate Link Section -->
                  <div class="bg-white rounded-3xl shadow-lg p-6 sm:p-8 mb-8 transform transition-all duration-300 hover:scale-[1.01] hover:shadow-xl">
                      <h3 class="text-xl font-bold text-gray-800 mb-4">Your Affiliate Link</h3>
                      <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                          <input type="text" id="affiliateLink" class="flex-grow p-3 border border-gray-300 rounded-lg text-sm" readonly value="https://mulapal.com/register.php?ref=xyz">
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
                                  <!-- Timetable rows will be inserted here -->
                              </tbody>
                          </table>
                      </div>
                  </div>
                </div>
            `;
            
            // Populate the dashboard with data
            const symbol = data.user.currencySymbol || data.user.currency || '';
            const fmt = (v) => {
                const num = Number(v ?? 0);
                return `${symbol} ${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            };
            document.getElementById('userName').textContent = data.user.name;
            document.getElementById('todayEarnings').textContent = fmt(data.user.todayEarnings);
            document.getElementById('totalEarnings').textContent = fmt(data.user.totalEarnings);
            document.getElementById('earningsGrowth').textContent = data.user.earningsGrowth;
            document.getElementById('currentBalance').textContent = fmt(data.user.balance);
            document.getElementById('totalWithdrawn').textContent = fmt(data.user.withdrawn);
            document.getElementById('affiliateEarnings').textContent = fmt(data.user.affiliateEarnings);
            document.getElementById('agentBonus').textContent = fmt(data.user.agentBonus);
            document.getElementById('adsEarnings').textContent = fmt(data.user.adsEarnings);
            document.getElementById('tiktokEarnings').textContent = fmt(data.user.tiktokEarnings);
            document.getElementById('youtubeEarnings').textContent = fmt(data.user.youtubeEarnings);
            document.getElementById('triviaEarnings').textContent = fmt(data.user.triviaEarnings);
            document.getElementById('blogEarnings').textContent = fmt(data.user.blogEarnings);
            document.getElementById('investedAmount').textContent = fmt(data.user.invested);
            document.getElementById('profitAmount').textContent = fmt(data.user.profit);
            document.getElementById('affiliateLink').value = data.user.affiliateLink;
            
            // Populate the timetable
            const timetableBody = document.getElementById('timetableBody');
            timetableBody.innerHTML = '';
            data.timetable.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'border-b last:border-0 border-gray-200 hover:bg-gray-50';
                row.innerHTML = `
                    <td class="py-3 px-4 font-medium">${item.name}</td>
                    <td class="py-3 px-4">${item.day1}</td>
                    <td class="py-3 px-4">${item.day2}</td>
                    <td class="py-3 px-4">${item.day3}</td>
                    <td class="py-3 px-4">${item.day4}</td>
                `;
                timetableBody.appendChild(row);
            });
            
            // Add event listener for the copy button
            document.getElementById('copyLinkBtn').addEventListener('click', () => {
                const affiliateLink = document.getElementById('affiliateLink');
                affiliateLink.select();
                document.execCommand('copy');

                // Enhanced feedback message
                const originalText = document.getElementById('copyLinkBtn').innerHTML;
                document.getElementById('copyLinkBtn').innerHTML = '<i class="fas fa-check mr-2"></i> Copied!';
                document.getElementById('copyLinkBtn').classList.remove('bg-blue-600', 'hover:bg-blue-700');
                document.getElementById('copyLinkBtn').classList.add('bg-green-600', 'hover:bg-green-700');

                // Show helpful tooltip
                const tooltip = document.createElement('div');
                tooltip.className = 'fixed bg-gray-800 text-white text-sm px-3 py-2 rounded-lg shadow-lg z-50';
                tooltip.textContent = 'Share this link with friends to earn commissions!';
                tooltip.style.left = (document.getElementById('copyLinkBtn').getBoundingClientRect().left) + 'px';
                tooltip.style.top = (document.getElementById('copyLinkBtn').getBoundingClientRect().bottom + 5) + 'px';
                document.body.appendChild(tooltip);

                setTimeout(() => {
                    document.getElementById('copyLinkBtn').innerHTML = originalText;
                    document.getElementById('copyLinkBtn').classList.remove('bg-green-600', 'hover:bg-green-700');
                    document.getElementById('copyLinkBtn').classList.add('bg-blue-600', 'hover:bg-blue-700');
                    if (document.body.contains(tooltip)) {
                        document.body.removeChild(tooltip);
                    }
                }, 3000);
            });
        }

        // Cache currency symbol
        let currencySymbolCache = null;
        async function getCurrencySymbol() {
            if (currencySymbolCache) return currencySymbolCache;
            try {
                const API_BASE = window.API_BASE;
                const token = localStorage.getItem('token');
                // Prefer dashboard (has currencySymbol), fallback to profile
                let res = await fetch(`${API_BASE}/dashboard`, { headers: { 'Authorization': `Bearer ${token}` }});
                if (res.ok) {
                    const data = await res.json();
                    if (data?.status === 'success') {
                        currencySymbolCache = data.data?.user?.currencySymbol || data.data?.user?.currency || '';
                        return currencySymbolCache;
                    }
                }
                res = await fetch(`${API_BASE}/profile`, { headers: { 'Authorization': `Bearer ${token}` }});
                if (res.ok) {
                    const data = await res.json();
                    const map = { KES: 'KSh', UGX: 'USh', TZS: 'TSh', USD: '$', GBP: '£' };
                    const code = data?.data?.currency || '';
                    currencySymbolCache = map[code] || code;
                    return currencySymbolCache;
                }
            } catch {}
            return '';
        }

        function applyCurrencySymbols(symbol) {
            try {
                // Replace labels like "Amount (KSH)" -> "Amount (<symbol>)"
                document.querySelectorAll('label').forEach(lb => {
                    lb.textContent = lb.textContent.replace(/Amount \(KSH\)/g, `Amount (${symbol})`);
                });
                // Replace table cells starting with "KSH " to "<symbol> <amount>"
                document.querySelectorAll('td').forEach(td => {
                    td.textContent = td.textContent.replace(/^KSH\s*([\d,.]+)/g, (m, amt) => {
                        const num = Number((amt || '0').toString().replace(/,/g, ''));
                        return `${symbol} ${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                    });
                });
            } catch {}
        }

        // Function to load page content
        function loadPage(pageId) {
            // Show loader
            loader.style.display = 'flex';
            
            // Simulate loading time
            setTimeout(async () => {
                // Hide loader
                loader.style.display = 'none';
                
                // If it's the dashboard, fetch data and render it
                if (pageId === 'dashboard') {
                    // center animated loader shown already; keeps experience consistent
                    fetchDashboardData().then(data => {
                        renderDashboard(data);
                    });
                } else if (pageContentMap[pageId]) {
                    // Otherwise, show the page in overlay with blur background
                    const title = pageId.split('_').map(w => w.charAt(0).toUpperCase()+w.slice(1)).join(' ');
                    openOverlay(title, pageContentMap[pageId]);

                    // Tasks pages loader (TikTok/YouTube/WhatsApp/Facebook/Instagram)
                    if (['tiktok_earn','youtube_earn','whatsapp','facebook_ads','instagram_ads'].includes(pageId)) {
                        (async () => {
                            const token = localStorage.getItem('token');
                            const map = {
                                tiktok_earn: { cat: 'tiktok', tbody: 'ttTasksBody', prev: 'ttPrev', next: 'ttNext', page: 'ttPage' },
                                youtube_earn: { cat: 'youtube', tbody: 'ytTasksBody', prev: 'ytPrev', next: 'ytNext', page: 'ytPage' },
                                whatsapp: { cat: 'whatsapp', tbody: 'waTasksBody', prev: 'waPrev', next: 'waNext', page: 'waPage' },
                                facebook_ads: { cat: 'facebook', tbody: 'fbTasksBody', prev: 'fbPrev', next: 'fbNext', page: 'fbPage' },
                                instagram_ads: { cat: 'instagram', tbody: 'igTasksBody', prev: 'igPrev', next: 'igNext', page: 'igPage' }
                            };
                            const cfg = map[pageId];
                            const tbody = document.getElementById(cfg.tbody);
                            const prevBtn = document.getElementById(cfg.prev);
                            const nextBtn = document.getElementById(cfg.next);
                            const pageEl = document.getElementById(cfg.page);
                            let page = 1; const limit = 10;

                            async function load(p) {
                                const res = await fetch(`${API_BASE}/tasks?category=${cfg.cat}&page=${p}&limit=${limit}`, { headers: { 'Authorization': `Bearer ${token}` }});
                                if (!res.ok) return;
                                const json = await res.json();
                                if (json.status !== 'success') return;
                                const items = json.data.items || [];
                                const total = json.data.total || 0;
                                const pages = Math.max(1, Math.ceil(total / limit));
                                tbody.innerHTML = '';
                                items.forEach(t => {
                                    const tr = document.createElement('tr');
                                    tr.className = 'border-b last:border-0 border-gray-200';
                                    tr.innerHTML = `
                                        <td class=\"py-3 px-4\">${t.title}</td>
                                        <td class=\"py-3 px-4\">${t.priceDisplay}</td>
                                        <td class=\"py-3 px-4\">${t.rewardWallet}</td>
                                        <td class=\"py-3 px-4\"><button data-task-id=\"${t.id}\" class=\"claim-btn bg-green-600 text-white rounded-full px-4 py-2 hover:bg-green-700\">Claim</button></td>
                                    `;
                                    tbody.appendChild(tr);
                                });
                                page = p;
                                pageEl.textContent = String(page);
                                prevBtn.disabled = page <= 1;
                                nextBtn.disabled = page >= pages;
                                // Bind claim handlers
                                tbody.querySelectorAll('.claim-btn').forEach(btn => {
                                    btn.addEventListener('click', async () => {
                                        const id = btn.getAttribute('data-task-id');
                                        btn.disabled = true;
                                        try {
                                            const res = await fetch(`${API_BASE}/tasks/claim`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` }, body: JSON.stringify({ task_id: id }) });
                                            const j = await res.json();
                                            alert(j.message || (res.ok ? 'Reward credited' : 'Failed'));
                                            if (res.ok) {
                                                fetchDashboardData().then(data => updateDashboardEarnings(data));
                                            }
                                        } catch {}
                                        btn.disabled = false;
                                    });
                                });
                            }

                            prevBtn.addEventListener('click', () => { if (page > 1) load(page - 1); });
                            nextBtn.addEventListener('click', () => load(page + 1));
                            await load(1);
                        })();
                    }

                    // If withdrawal history, try to fetch real data
                    if (pageId === 'withdrawal_history') {
                        try {
                            const API_BASE = window.API_BASE || 'http://localhost:5000';
                            const token = localStorage.getItem('token');

                            let page = 1; const limit = 10;
                            const tbody = document.querySelector('table tbody');
                            const pageEl = document.getElementById('wdPage');
                            const pagesEl = document.getElementById('wdPages');
                            const prevBtn = document.getElementById('wdPrev');
                            const nextBtn = document.getElementById('wdNext');

                            async function load(p) {
                                const res = await fetch(`${API_BASE}/withdrawals?page=${p}&limit=${limit}`, { headers: { 'Authorization': `Bearer ${token}` }});
                                if (!res.ok) return;
                                const json = await res.json();
                                if (json.status !== 'success') return;
                                const items = json.data.items || [];
                                const total = json.data.total || 0;
                                const pages = Math.max(1, Math.ceil(total / limit));

                                tbody.innerHTML = '';
                                items.forEach(it => {
                                    const tr = document.createElement('tr');
                                    tr.className = 'border-b last:border-0 border-gray-200';
                                    tr.innerHTML = `
                                        <td class="py-3 px-4">${it.createdAt}</td>
                                        <td class="py-3 px-4">${it.amountDisplay}</td>
                                        <td class="py-3 px-4"><span class="${it.status === 'completed' ? 'bg-green-100 text-green-800' : it.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'} text-xs font-semibold px-2.5 py-0.5 rounded-full">${it.status.charAt(0).toUpperCase() + it.status.slice(1)}</span></td>
                                        <td class="py-3 px-4">${it.method || ''}</td>
                                    `;
                                    tbody.appendChild(tr);
                                });

                                page = p;
                                pageEl.textContent = String(page);
                                pagesEl.textContent = String(pages);
                                prevBtn.disabled = page <= 1;
                                nextBtn.disabled = page >= pages;
                            }

                            prevBtn.addEventListener('click', () => { if (page > 1) load(page - 1); });
                            nextBtn.addEventListener('click', () => load(page + 1));

                            await load(1);
                        } catch (e) { /* ignore */ }
                    }

                    const symbol = await getCurrencySymbol();
                    applyCurrencySymbols(symbol);
                } else {
                    // If page not found, show a 404 message
                    mainContent.innerHTML = `
                        <div class="p-8 bg-white rounded-3xl shadow-lg text-center">
                            <i class="fa-solid fa-triangle-exclamation text-6xl text-yellow-500 mb-4"></i>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Page Not Found</h2>
                            <p class="text-gray-600">The requested page could not be found.</p>
                        </div>
                    `;
                }
            }, 800);
        }

        // Initialize the dashboard
        loadPage('dashboard');

        // Overlay helpers
        const pageOverlay = document.getElementById('pageOverlay');
        const overlayContent = document.getElementById('overlayContent');
        const overlayTitle = document.getElementById('overlayTitle');
        const overlayClose = document.getElementById('overlayClose');
        const overlayBackdrop = document.getElementById('pageOverlayBackdrop');

        function openOverlay(title, html){
            overlayTitle.textContent = title;
            overlayContent.innerHTML = html;
            pageOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeOverlay(){
            pageOverlay.classList.add('hidden');
            overlayContent.innerHTML = '';
            document.body.style.overflow = '';
        }
        overlayClose.addEventListener('click', closeOverlay);
        overlayBackdrop.addEventListener('click', closeOverlay);

        // Wire actions for dynamic pages after content is swapped in
        function wireDepositActions(){
            const btn = document.getElementById('depositBtn');
            const amountEl = document.getElementById('depositAmount');
            const msg = document.getElementById('depositMsg');
            if (!btn) return;
            btn.addEventListener('click', async () => {
                const amount = parseFloat((amountEl?.value||'0'));
                if (!amount || amount <= 0){
                    msg.classList.remove('hidden');
                    msg.classList.remove('text-green-700', 'text-yellow-700');
                    msg.classList.add('text-red-600');
                    msg.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i> Please enter a valid amount greater than 0';
                    return;
                }

                // Show loading state
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-gray-600', 'cursor-not-allowed');

                try{
                    const API_BASE = window.API_BASE || 'http://localhost:5000';
                    const token = localStorage.getItem('token');
                    const res = await fetch(`${API_BASE}/payments/deposit/initiate`, { method:'POST', headers:{ 'Content-Type':'application/json', 'Authorization': `Bearer ${token}` }, body: JSON.stringify({ amount }) });
                    const json = await res.json();

                    if (json.status !== 'success'){
                        msg.classList.remove('hidden');
                        msg.classList.remove('text-green-700', 'text-yellow-700');
                        msg.classList.add('text-red-600');
                        msg.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${json.message || 'Failed to initiate payment. Please try again.'}`;
                        return;
                    }

                    const url = json.data.redirect_url || json.data.redirectUrl || json.data.redirect_url_mobile || json.data.payment_url || '';
                    if (url) {
                        msg.classList.remove('hidden');
                        msg.classList.remove('text-red-600', 'text-yellow-700');
                        msg.classList.add('text-green-700');
                        msg.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Redirecting to payment gateway...';
                        setTimeout(() => window.location.href = url, 1000);
                    } else {
                        msg.classList.remove('hidden');
                        msg.classList.remove('text-green-700', 'text-red-600');
                        msg.classList.add('text-yellow-700');
                        msg.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Payment initiated but redirect URL not available. Please contact support.';
                    }
                }catch(e){
                    msg.classList.remove('hidden');
                    msg.classList.remove('text-green-700', 'text-yellow-700');
                    msg.classList.add('text-red-600');
                    msg.innerHTML = '<i class="fas fa-wifi-slash mr-1"></i> Network error. Please check your connection and try again.';
                } finally {
                    // Reset button state
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-gray-600', 'cursor-not-allowed');
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');
                }
            });
        }

        function wireWithdrawActions(){
            const btn = document.getElementById('wdBtn');
            const amountEl = document.getElementById('wdAmount');
            const walletEl = document.getElementById('wdWallet');
            const msg = document.getElementById('wdMsg');
            if (!btn) return;
            btn.addEventListener('click', async () => {
                const amount = parseFloat((amountEl?.value||'0'));
                const wallet = walletEl?.value || 'main';
                if (!amount || amount <= 0){ msg.classList.remove('hidden'); msg.textContent = 'Enter a valid amount'; return; }
                try{
                    const API_BASE = window.API_BASE || 'http://localhost:5000';
                    const token = localStorage.getItem('token');
                    const res = await fetch(`${API_BASE}/withdrawals`, { method:'POST', headers:{ 'Content-Type':'application/json', 'Authorization': `Bearer ${token}` }, body: JSON.stringify({ amount, wallet }) });
                    const json = await res.json();
                    if (json.status !== 'success'){ msg.classList.remove('hidden'); msg.textContent = json.message || 'Failed to request withdrawal'; return; }
                    msg.classList.remove('hidden'); msg.classList.remove('text-red-600'); msg.classList.add('text-green-700');
                    msg.textContent = 'Withdrawal request submitted';
                }catch(e){ msg.classList.remove('hidden'); msg.textContent = 'Network error'; }
            });
        }

        // Hook into page rendering to wire forms
        const origLoadPage = loadPage;
        loadPage = function(pageId){
            origLoadPage(pageId);
            setTimeout(() => {
                if (pageId === 'deposit') wireDepositActions();
                if (pageId === 'withdraw_balance') wireWithdrawActions();
            }, 50);
        };

        // Voucher handlers
        function showMsg(el, text, type){
            el.classList.remove('hidden');
            el.textContent = text;
            el.classList.remove('text-red-600','text-green-700','text-yellow-700');
            if (type==='error') el.classList.add('text-red-600');
            else if (type==='success') el.classList.add('text-green-700');
            else el.classList.add('text-yellow-700');
        }

        function wireVoucherBuy(){
            const btn = document.getElementById('bvBtn');
            const amt = document.getElementById('bvAmount');
            const qty = document.getElementById('bvQty');
            const msg = document.getElementById('bvMsg');
            if (!btn) return;
            btn.addEventListener('click', async () => {
                const amount = parseFloat(amt?.value||'0');
                const quantity = Math.max(1, parseInt(qty?.value||'1', 10));
                if (!amount || amount <= 0){ showMsg(msg, 'Enter a valid amount', 'error'); return; }
                try{
                    const API_BASE = window.API_BASE || 'http://localhost:5000';
                    const token = localStorage.getItem('token');
                    // If backend not ready, fail gracefully
                    const res = await fetch(`${API_BASE}/vouchers/buy`, { method:'POST', headers:{ 'Content-Type':'application/json', 'Authorization': `Bearer ${token}` }, body: JSON.stringify({ amount, quantity }) });
                    const json = await res.json();
                    if (json.status !== 'success') { showMsg(msg, json.message || 'Failed to buy voucher', 'error'); return; }
                    showMsg(msg, 'Voucher purchase created. Check history for codes.', 'success');
                }catch(e){ showMsg(msg, 'Network error', 'error'); }
            });
        }

        function wireVoucherHistory(){
            const prevBtn = document.getElementById('vhPrev');
            const nextBtn = document.getElementById('vhNext');
            const tbody = document.getElementById('vhBody');
            const pageEl = document.getElementById('vhPage');
            if (!tbody) return;
            let page = 1; const limit = 10;
            async function load(p){
                try{
                    const API_BASE = window.API_BASE || 'http://localhost:5000';
                    const token = localStorage.getItem('token');
                    const res = await fetch(`${API_BASE}/vouchers/history?page=${p}&limit=${limit}`, { headers:{ 'Authorization': `Bearer ${token}` }});
                    if (!res.ok) return;
                    const json = await res.json();
                    if (json.status !== 'success') return;
                    const items = json.data.items || [];
                    tbody.innerHTML = '';
                    items.forEach(v => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b last:border-0 border-gray-200';
                        tr.innerHTML = `
                            <td class="py-3 px-4">${v.createdAt||''}</td>
                            <td class="py-3 px-4">${v.amountDisplay||''}</td>
                            <td class="py-3 px-4">${v.code||''}</td>
                            <td class="py-3 px-4">${v.status||''}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                    page = p; pageEl.textContent = String(page);
                }catch{}
            }
            prevBtn?.addEventListener('click', () => { if (page>1) load(page-1); });
            nextBtn?.addEventListener('click', () => load(page+1));
            load(1);
        }

        // Extend wiring to new pages
        const _origLoadPage2 = loadPage;
        loadPage = function(pageId){
            _origLoadPage2(pageId);
            setTimeout(() => {
                if (pageId === 'buy_voucher') wireVoucherBuy();
                if (pageId === 'voucher_history') wireVoucherHistory();
            }, 70);
        };

        // Toggle sidebar on mobile
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('visible');
        });

        // Close sidebar when clicking on overlay
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');
        });

        // Handle submenu toggles
        document.querySelectorAll('.submenu-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const submenu = btn.nextElementSibling;
                submenu.classList.toggle('open');
                
                // Rotate the chevron icon
                const chevron = btn.querySelector('.fa-chevron-down');
                chevron.classList.toggle('rotate-180');
            });
        });

        // Handle navigation clicks
        document.querySelectorAll('.sidebar a[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                const pageId = link.getAttribute('data-page');
                const href = link.getAttribute('href');

                // If link has external href (not just #), allow default navigation
                if (href && href !== '#' && !href.startsWith('#')) {
                    // Allow default navigation for external links
                    return;
                }

                // Prevent default for overlay pages
                e.preventDefault();

                // Update active state
                document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
                link.classList.add('active');

                // Close sidebar on mobile after selection
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('visible');
                }

                // Load the page content (overlay for non-dashboard)
                if (pageId === 'dashboard') {
                    closeOverlay();
                }
                loadPage(pageId);
            });
        });

        // Add rotate-180 class for chevron animation
        const style = document.createElement('style');
        style.textContent = `
            .rotate-180 {
                transform: rotate(180deg);
                transition: transform 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    });
</script>
</body>
</html>