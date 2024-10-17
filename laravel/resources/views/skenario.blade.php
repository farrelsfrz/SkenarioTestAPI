<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skenario</title>
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
            <div class="header-title">Skenario</div>
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
                <div style="margin-bottom: 10px;">
                    <button class="btn btn-primary" onclick="addMenu()">Tambah</button>
                </div>
                <table class="table" style="margin-top: 10px;" id="menuTable">
                    <thead>
                        <tr>
                            <th style="width: 40px;" onclick="sortTable(0)">No <i class="sort-icon fas fa-sort"></i></th>
                            <th style="display: none;" onclick="sortTable(1)">ID <i class="sort-icon fas fa-sort"></i></th>
                            <th style="width: 125px;" onclick="sortTable(2)">Application ID <i class="sort-icon fas fa-sort"></i></th>
                            <th onclick="sortTable(3)">Nama Menu <i class="sort-icon fas fa-sort"></i></th>
                            <th onclick="sortTable(4)">Deskripsi <i class="sort-icon fas fa-sort"></i></th>
                            <th style="width: 110px;" onclick="sortTable(5)">Status <i class="sort-icon fas fa-sort"></i></th>
                            <th style="width: 110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="menuTableBody">
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
                <label>Application ID:</label>
                <input type="text" id="applicationId" class="modal-input" required />
                <label>Nama Menu:</label>
                <input type="text" id="menuName" class="modal-input" required />
                <label>Deskripsi:</label>
                <input type="text" id="description" class="modal-input" required />
                <label>Status:</label>
                <select id="status" class="modal-input" required>
                    @foreach ($response_status as $key)
                        <option value="{{ $key[0] }}">{{ $key[1] }}</option>
                    @endforeach
                </select>
                <button id="saveButton" onclick="saveMenu()" class="modal-button">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        let editingMenuId = null; 
        document.addEventListener('DOMContentLoaded', function() {
            fetchData();
            // setInterval(fetchData, 5000); 
        });

        function fetchData() {
            axios.get('http://10.25.200.21:8000/test_cases')
                .then(response => {
                    console.log('Data yang diterima:', response.data);
                    const testCases = response.data;
                    const tableBody = document.getElementById('menuTableBody');
                    tableBody.innerHTML = ''; 

                    testCases.sort((a, b) => a.id - b.id);

                    testCases.forEach((testCase, index) => {
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td>${index + 1}</td>
                            <td style="display: none;">${testCase.id || '-'}</td>
                            <td>${testCase.application_id || '-'}</td>
                            <td>${testCase.title || '-'}</td>
                            <td>${testCase.description || '-'}</td>
                            <td>${testCase.status || '-'}</td>
                            <td>
                                <button class="btn btn-warning" onclick="editMenu(${testCase.id})">Edit</button>
                                <button class="btn btn-danger" onclick="confirmDelete(this)">Delete</button>
                            </td>
                        `;
                        tableBody.appendChild(newRow);
                    });
                })
                .catch(error => {
                console.error('Gagal mengambil data test cases:', error);
                if (error.response) {
                    console.error('Response data:', error.response.data);
                    console.error('Response status:', error.response.status);
                }
                alert('Gagal mengambil data: ' + (error.response ? error.response.data.message : 'Kesalahan tidak diketahui'));
            });
    }

        function addMenu() {
            document.getElementById('addMenuModal').style.display = 'block'; 
            clearModalFields(); 
            editingMenuId = null; 
        }

        function editMenu(id) {
            console.log('Editing menu with ID:', id);
            axios.get(`http://10.25.200.21:8000/test_cases/${id}`)
                .then(response => {
                    const testCase = response.data;
                    document.getElementById('applicationId').value = testCase.application_id; 
                    document.getElementById('menuName').value = testCase.title;
                    document.getElementById('description').value = testCase.description; 
                    document.getElementById('status').value = testCase.status; 

                    document.getElementById('addMenuModal').style.display = 'block'; 
                    editingMenuId = id; 
                })
                .catch(error => {
                    console.error('Error fetching test case data:', error);
                    alert('Gagal mengambil data test case: ' + (error.response ? error.response.data.message : 'Kesalahan tidak diketahui'));
                });
        }

function saveMenu() {
    const applicationId = parseInt(document.getElementById('applicationId').value);
    const menuName = document.getElementById('menuName').value.trim();
    const description = document.getElementById('description').value.trim();
    const status = document.getElementById('status').value;

    if (!applicationId || !menuName || !description) {
        alert('Semua field harus diisi!');
        return;
    }

    const data = {
        application_id: applicationId,
        title: menuName,
        description: description,
        status: parseInt(status)
    };

    console.log('Data yang akan dikirim:', data);

    if (editingMenuId) {
        axios.put(`http://10.25.200.21:8000/test_cases/${editingMenuId}`, data)
            .then(response => {
                console.log('Data updated successfully:', response.data);
                fetchData(); 
                closeModal(); 
            })
            .catch(error => {
                console.error('Error updating data:', error);
                alert('Gagal memperbarui data: ' + (error.response ? error.response.data.message : 'Kesalahan tidak diketahui'));
            });
    } else {
        axios.post('http://10.25.200.21:8000/test_cases', data, {
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Data saved successfully:', response.data);
            fetchData(); // Memanggil fetchData untuk memperbarui tabel
            closeModal(); // Menutup modal setelah menyimpan
        })
        .catch(error => {
            console.error('Error saving data:', error);
            alert('Gagal menyimpan data: ' + (error.response ? error.response.data.message : 'Kesalahan tidak diketahui'));
        });
    }
}

        function closeModal() {
            document.getElementById('addMenuModal').style.display = 'none';
        }

        function clearModalFields() {
            document.getElementById('applicationId').value = '';
            document.getElementById('menuName').value = '';
            document.getElementById('description').value = '';
            document.getElementById('status').value = '1'; 
        }

        function confirmDelete(button) {
            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                const row = button.closest('tr');
                const id = row.cells[1].innerText; 
                axios.delete(`http://10.25.200.21:8000/test_cases/${id}`)
                    .then(response => {
                        row.remove();   
                        fetchData();    
                    })
                    .catch(error => {
                        console.error('Error deleting data:', error);
                    });
            }
        }

        function toggleMenu() {
            const menuItems = document.getElementById('menuItems');
            menuItems.style.display = menuItems.style.display === 'none' ? 'block' : 'none';
        }

        function sortTable(columnIndex) {
            const table = document.getElementById("menuTable");
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

        function search() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    console.log('Searching for:', input); 
    const rows = document.querySelectorAll('#menuTableBody tr'); 
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

    const menuTableBody = document.getElementById('menuTableBody');

    console.log('menuTableBody:', menuTableBody);

    if (menuTableBody) {
        menuTableBody.innerHTML = ''; 
        matchedRows.forEach(row => menuTableBody.appendChild(row));
        
        if (input === '') {
            fetchData(); 
        }
    } else {
        console.error('menuTableBody tidak ditemukan');
    }
}
    </script>
</body>
</html>