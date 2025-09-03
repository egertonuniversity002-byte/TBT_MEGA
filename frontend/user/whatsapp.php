<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Tasks | Matrix Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4e9; }
        .task-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.07); margin-bottom: 1rem; }
        .claim-btn { background: #38a169; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="max-w-4xl mx-auto p-6">
        <div class="flex items-center gap-3 mb-6">
            <i class="fab fa-whatsapp text-3xl text-green-600"></i>
            <h1 class="text-3xl font-bold">WhatsApp Tasks</h1>
        </div>
        <p class="text-gray-600 mb-6">Complete WhatsApp tasks and earn rewards.</p>

        <div id="tasksContainer">
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading tasks...</p>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = window.API_BASE || 'https://official-paypal.onrender.com';
        const token = localStorage.getItem('token');

        if (!token) {
            window.location.href = '../auth/login.php';
        }

        async function loadTasks() {
            try {
                const response = await fetch(`${API_BASE}/tasks?category=whatsapp&page=1&limit=10`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    const tasks = data.data.items || [];

                    if (tasks.length === 0) {
                        document.getElementById('tasksContainer').innerHTML = `
                            <div class="text-center py-8">
                                <i class="fab fa-whatsapp text-6xl text-gray-300 mb-4"></i>
                                <h3 class="text-xl font-semibold mb-2">No Tasks Available</h3>
                                <p class="text-gray-600">Check back later for new WhatsApp tasks.</p>
                            </div>
                        `;
                        return;
                    }

                    document.getElementById('tasksContainer').innerHTML = tasks.map(task => `
                        <div class="task-card">
                            <h3 class="text-lg font-semibold mb-2">${task.title}</h3>
                            <p class="text-green-600 font-medium mb-3">Reward: ${task.priceDisplay}</p>
                            ${task.instructions ? `<p class="text-gray-600 mb-3">${task.instructions}</p>` : ''}
                            <div class="flex justify-between items-center">
                                <span class="text-sm bg-gray-100 px-3 py-1 rounded-full">Wallet: ${task.rewardWallet}</span>
                                <button class="claim-btn" onclick="claimTask('${task.id}', this)">
                                    <i class="fas fa-check mr-2"></i>Claim Reward
                                </button>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                document.getElementById('tasksContainer').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-6xl text-red-300 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Error Loading Tasks</h3>
                        <p class="text-gray-600">Please try again later.</p>
                    </div>
                `;
            }
        }

        async function claimTask(taskId, button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Claiming...';

            try {
                const response = await fetch(`${API_BASE}/tasks/claim`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ task_id: taskId })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    button.innerHTML = '<i class="fas fa-check mr-2"></i>Claimed!';
                    button.style.background = '#48bb78';
                    alert('Reward credited successfully!');
                    setTimeout(() => loadTasks(), 2000);
                } else {
                    throw new Error(data.message || 'Failed to claim reward');
                }
            } catch (error) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Claim Reward';
                alert('Failed to claim reward: ' + error.message);
            }
        }

        loadTasks();
    </script>
</body>
</html>
