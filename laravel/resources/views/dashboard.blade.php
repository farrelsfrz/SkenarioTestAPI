<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>
<body>
    <div id="app" class="user-dashboard">
        <header class="dashboard-header">
            <button id="toggleMenu" onclick="toggleMenu()" style="background: none; border: none; color: white; cursor: pointer;">
                <i class="fas fa-bars"></i> 
            </button>
            <div class="header-title">Dashboard</div>
            <div class="header-search">
                <input type="text" id="searchInput" placeholder="Search..." />
                <button class="search-button" onclick="search()">🔍</button>
            </div>
        </header>
        <div class="dashboard-layout">
            <div class="sidebar" id="menuItems">
                <h2>Menu</h2>
                <ul>
                    @foreach ([
                        ['name' => 'Dashboard', 'link' => 'dashboard', 'icon' => 'fas fa-tachometer-alt'],
                        ['name' => 'Skenario', 'link' => 'skenario', 'icon' => 'fas fa-list-alt'],
                        ['name' => 'Testing', 'link' => 'testing', 'icon' => 'fas fa-vial'],
                        ['name' => 'Export', 'link' => 'export.php', 'icon' => 'fas fa-save']
                    ] as $item)
                        <li>
                            <a href="{{ $item['link'] }}" class="{{ request()->is($item['link']) ? 'active' : '' }}">
                                <i class="{{ $item['icon'] }}"></i> 
                                <strong>{{ $item['name'] }}</strong>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="logout-container">
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf   
                        <button type="submit" class="logout-button">Log out</button>
                    </form>
                </div>
            </div>
            <div class="main-content fade-in" id="mainContent">
                <div class="chart-container">
                    <canvas id="resultsChart" width="300" height="300" style="display: block;"></canvas>
                    <div class="test-results">
                        <p class="success"><span style="color: #06D001;">●</span> <span id="passed-count">0</span> Success</p>
                        <p class="review"><span style="color: #697565;">●</span> <span id="blocked-count">0</span> Review</p>
                        <p class="bugs"><span style="color: #FFEB00;">●</span> <span id="retest-count">0</span> Bugs</p>
                        <p class="failed"><span style="color: #FF0000;">●</span> <span id="failed-count">0</span> Failed</p>
                    </div>
                    <div class="overall">
                        <h3>Overall: <span id="overall-percentage">0%</span> Success</h3>
                    </div>
                </div>
                <div class="dashboard-content" id="dashboardContent">
                    <div class="dashboard-column" data-column-index="1">
                        <h3>Success</h3>
                        <ul id="successColumn" class="success-column"></ul>
                    </div>
                    <div class="dashboard-column" data-column-index="2">
                        <h3>Review</h3>
                        <ul id="reviewColumn" class="review-column"></ul>
                    </div>
                    <div class="dashboard-column" data-column-index="3">
                        <h3>Bugs</h3>
                        <ul id="bugsColumn" class="bugs-column"></ul>
                    </div>
                    <div class="dashboard-column" data-column-index="4">
                        <h3>Failed</h3>
                        <ul id="failedColumn" class="failed-column"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateDashboard() {
            console.log('updateDashboard function called');
            axios.get('http://10.25.200.21:8000/applications')
                .then(response => {
                    const applications = response.data;
                    console.log('Data dari API:', applications);

                    const successCount = applications.filter(app => app.success).length;
                    const reviewCount = applications.filter(app => app.review).length;
                    const bugsCount = applications.filter(app => app.bugs).length;
                    const failedCount = applications.filter(app => app.failed).length;

                    const ctx = document.getElementById('resultsChart').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            datasets: [{
                                label: 'Persentase',
                                data: [successCount, reviewCount, bugsCount, failedCount],
                                backgroundColor: ['#06D001', '#697565', '#FFEB00', '#FF0000'],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                datalabels: {
                                    color: '#fff',
                                    formatter: (value, context) => {
                                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(2) + '%' : '0%';
                                        return percentage;
                                    }
                                }
                            }
                        }
                    });

                    const successColumn = document.getElementById('successColumn');
                    const reviewColumn = document.getElementById('reviewColumn');
                    const bugsColumn = document.getElementById('bugsColumn');
                    const failedColumn = document.getElementById('failedColumn');

                    successColumn.innerHTML = '';
                    reviewColumn.innerHTML = '';
                    bugsColumn.innerHTML = '';
                    failedColumn.innerHTML = '';

                    applications.forEach(app => {
                        const li = document.createElement('li');
                        li.textContent = app.review || app.failed || app.success || app.bugs || 'No Title'; 
                        if (app.success) {
                            successColumn.appendChild(li);
                        } else if (app.review) {
                            reviewColumn.appendChild(li);
                        } else if (app.bugs) {
                            bugsColumn.appendChild(li);
                        } else if (app.failed) {
                            failedColumn.appendChild(li);
                        }
                    });

                    updateResults(applications);
                })
                .catch(error => {
                    console.error('Gagal mengambil data aplikasi:', error);
                });
        }

        function updateResults(applications) {
            const results = {
                passed: applications.filter(app => app.success).length,
                blocked: applications.filter(app => app.review).length,
                retest: applications.filter(app => app.bugs).length,
                failed: applications.filter(app => app.failed).length,
            };

            const total = results.passed + results.blocked + results.retest + results.failed;

            document.getElementById('passed-count').textContent = results.passed;
            document.getElementById('blocked-count').textContent = results.blocked;
            document.getElementById('retest-count').textContent = results.retest;
            document.getElementById('failed-count').textContent = results.failed;

            const overallPercentage = total > 0 ? ((results.passed / total) * 100).toFixed(2) : 0;
            document.getElementById('overall-percentage').textContent = overallPercentage + '%';
        }

        function fetchData() {
            updateDashboard(); 
        }

        function toggleMenu() {
            const menuItems = document.getElementById('menuItems');
            menuItems.style.display = menuItems.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.getElementById('mainContent');
            mainContent.classList.add('visible'); 
            fetchData(); 
        });
    </script>
</body>
</html>