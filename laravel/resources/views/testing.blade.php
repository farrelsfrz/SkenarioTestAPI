<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    @php
        $response_status = [
            ['1', 'Success'],
            ['2', 'Review'],
            ['3', 'Bugs'],
            ['4', 'Failed'],
            ['5', 'Pending']
        ];
    @endphp

    <div id="app" class="user-dashboard">
        <header class="dashboard-header">
            <button id="toggleMenu" onclick="toggleMenu()" style="background: none; border: none; color: white; cursor: pointer;">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-title">Testing</div>
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
            <div class="main-content">
                <table class="table" style="margin-top: 10px;" id="stepTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;" onclick="sortTable(0)">No <i class="sort-icon fas fa-sort"></i></th>
                            <th style="display: none" onclick="sortTable(1)">Test Steps ID <i class="sort-icon fas fa-sort"></i></th>
                            <th style="display: none" onclick="sortTable(2)">Test Case ID <i class="sort-icon fas fa-sort"></i></th>
                            <th onclick="sortTable(3)">Nama Menu <i class="sort-icon fas fa-sort"></i></th>
                            <th onclick="sortTable(4)">Expected Result <i class="sort-icon fas fa-sort"></i></th>
                            <th onclick="sortTable(5)">Actual Result <i class="sort-icon fas fa-sort"></i></th>
                            <th style="width: 110px;" onclick="sortTable(6)">Status <i class="sort-icon fas fa-sort"></i></th>
                            <th style="width: 110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="stepTableBody">
                        <!-- Baris akan diisi dengan JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addMenuModal" style="display:none;">
        <div class="modal-content">
            <span onclick="closeModal()" style="cursor:pointer; float: right; font-size: 20px;">&times;</span>
            <h2 style="text-align: center;">Menu</h2>
            <div class="modal-form">
                <label>Test Case ID:</label>
                <input type="text" id="testCaseId" class="modal-input" readonly/>
                <label>Nama Menu:</label>
                <input type="text" id="menuName" class="modal-input" readonly/>
                <label>Expected Result:</label>
                <input type="text" id="expectedResult" class="modal-input" readonly/>
                <label>Actual Result:</label>
                <input type="text" id="actualResult" class="modal-input" />
                <label>Status:</label>
                <select id="status" class="modal-input">
                    @foreach ($response_status as $key)
                        <option value="{{ $key[0] }}">{{ $key[1] }}</option>
                    @endforeach
                </select>
                <button id="saveButton" onclick="saveStep()" class="modal-button">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        let editingStepId = null; 

        document.addEventListener('DOMContentLoaded', function() {
            fetchData();
            // setInterval(fetchData, 5000); 
        });

        function fetchData() {
            axios.get('http://10.25.200.21:8000/test_steps')
                .then(response => {
                    const testSteps = response.data;
                    console.log('Data test steps:', testSteps);
                    const tableBody = document.getElementById('stepTableBody');
                    tableBody.innerHTML = '';

                    axios.get('http://10.25.200.21:8000/test_cases')
                        .then(testCasesResponse => {
                            const testCases = testCasesResponse.data;
                            console.log('Data test cases:', testCases);

                            const testCaseMap = {};
                            testCases.forEach(testCase => {
                                testCaseMap[testCase.id] = testCase.title; 
                            });
                            console.log('testCaseMap:', testCaseMap);

                            testSteps.forEach((step, index) => {
                                console.log('Test Step ID:', step.test_cases_id);
                                console.log('Corresponding Title:', testCaseMap[step.test_cases_id]);
                                const newRow = document.createElement('tr');
                                newRow.innerHTML = `
                                    <td>${index + 1}</td>
                                    <td style="display: none">${step.id}</td>
                                    <td style="display: none">${step.test_cases_id}</td>
                                    <td>${step.title}</td>
                                    <td>${step.expected_result}</td>
                                    <td>${step.actual_result}</td>
                                    <td>${step.status}</td>
                                    <td>
                                        <button class="btn btn-warning" onclick="editStep(${step.id})">Edit</button>
                                    </td>
                                `;
                                tableBody.appendChild(newRow);
                            });
                        })
                        .catch(error => {
                            console.error('Gagal mengambil data test cases:', error);
                        });
                })
                .catch(error => {
                    console.error('Gagal mengambil data test steps:', error);
                });
        }

        function editStep(id) {
            axios.get(`http://10.25.200.21:8000/test_steps/${id}`)
                .then(response => {
                    const step = response.data;
                    document.getElementById('testCaseId').value = step.test_cases_id;
                    document.getElementById('menuName').value = step.title; 
                    document.getElementById('expectedResult').value = step.expected_result;
                    document.getElementById('actualResult').value = step.actual_result;
                    document.getElementById('status').value = step.status;

                    editingStepId = step.id; 
                    document.getElementById('addMenuModal').style.display = 'block'; 
                })
                .catch(error => {
                    console.error('Error fetching step data:', error);
                    alert('Gagal mengambil data langkah: ' + (error.response ? error.response.data.message : 'Kesalahan tidak diketahui'));
                });
        }

        function saveStep() {
            const data = {
                test_cases_id: document.getElementById('testCaseId').value,
                title: document.getElementById('menuName').value, 
                expected_result: document.getElementById('expectedResult').value,
                actual_result: document.getElementById('actualResult').value,
                status: parseInt(document.getElementById('status').value), 
            };

            axios.put(`http://10.25.200.21:8000/test_steps/${editingStepId}`, data)
                .then(response => {
                    fetchData(); 
                    closeModal(); 
                    updateSkenario(); 
                    updateDashboard(); 
                })
                .catch(error => {
                    console.error('Error updating data:', error);
                    alert('Gagal memperbarui data: ' + (error.response ? error.response.data.message : 'Kesalahan tidak diketahui'));
                });
        }

        function closeModal() {
            document.getElementById('addMenuModal').style.display = 'none';
        }

        function toggleMenu() {
            const menuItems = document.getElementById('menuItems');
            menuItems.style.display = menuItems.style.display === 'none' ? 'block' : 'none';
        }

        function sortTable(columnIndex) {
            const table = document.getElementById("stepTable");
            const rows = Array.from(table.rows).slice(1); 
            const isAscending = table.getAttribute('data-sort-order') === 'asc';

            rows.sort((a, b) => {
                const cellA = a.cells[columnIndex].innerText.toLowerCase();
                const cellB = b.cells[columnIndex].innerText.toLowerCase();

                const isNumeric = !isNaN(cellA) && !isNaN(cellB);

                if (isNumeric) {
                    return isAscending ? parseFloat(cellA) - parseFloat(cellB) : parseFloat(cellB) - parseFloat(cellA);
                } else {
                    return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                }
            });

            rows.forEach(row => table.appendChild(row));
            table.setAttribute('data-sort-order', isAscending ? 'desc' : 'asc'); 
        }

        function updateSkenario() {
            axios.get('http://10.25.200.21:8000/test_cases')
                .then(response => {
                    console.log('Memperbarui status di Skenario');
                })
                .catch(error => {
                    console.error('Gagal memperbarui status di Skenario:', error);
                });
        }

        function updateDashboard() {
            console.log('updateDashboard function called');
        }
        
function search() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    console.log('Searching for:', input); 
    const rows = document.querySelectorAll('#stepTableBody tr'); 
    const matchedRows = [];
    const unmatchedRows = [];

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        let rowContainsMatch = false; 

        cells.forEach(cell => {
            if (cell.textContent.toLowerCase().includes(input)) {
                rowContainsMatch = true; 
            }
        });

        if (rowContainsMatch) {
            matchedRows.push(row); 
        } else {
            unmatchedRows.push(row); 
        }
    });

    const stepTableBody = document.getElementById('stepTableBody');

    console.log('stepTableBody:', stepTableBody);

    if (stepTableBody) {
        stepTableBody.innerHTML = ''; 
        matchedRows.forEach(row => stepTableBody.appendChild(row));
        
        if (input === '') {
            fetchData(); 
        }
    } else {
        console.error('stepTableBody tidak ditemukan');
    }
}
    </script>
</body>
</html>