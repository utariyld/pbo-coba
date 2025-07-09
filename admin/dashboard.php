<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - TestimoniApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?= $_SESSION['username'] ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home me-1"></i>Halaman Utama</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="active"><i class="fas fa-chart-bar me-2"></i>Dashboard</a></li>
            <li><a href="#testimoni"><i class="fas fa-comments me-2"></i>Kelola Testimoni</a></li>
            <li><a href="#users"><i class="fas fa-users me-2"></i>Kelola User</a></li>
            <li><a href="#reports"><i class="fas fa-chart-pie me-2"></i>Laporan</a></li>
            <li><a href="#settings"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="mb-2">
                                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                                        Selamat datang, <?= $_SESSION['username'] ?>!
                                    </h2>
                                    <p class="text-muted mb-0">Kelola testimoni dan pengguna dengan mudah melalui dashboard ini.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-primary" onclick="refreshDashboard()">
                                            <i class="fas fa-sync-alt me-1"></i>Refresh
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="exportData()">
                                            <i class="fas fa-download me-1"></i>Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4" id="statsCards">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-card text-center">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-number" id="totalTestimoni">0</div>
                        <div class="stat-label">Total Testimoni</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-card text-center">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number" id="totalUsers">0</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-card text-center">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-number" id="avgRating">0</div>
                        <div class="stat-label">Rating Rata-rata</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="dashboard-card text-center">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-number" id="recentTestimoni">0</div>
                        <div class="stat-label">Testimoni Bulan Ini</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Testimoni per Kategori
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Rating Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="ratingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Aktivitas Terbaru
                            </h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="loadRecentActivity()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="recentActivity">
                                <div class="text-center py-4">
                                    <div class="loading"></div>
                                    <p class="mt-2 text-muted">Memuat aktivitas...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimoni Management -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Kelola Testimoni
                            </h5>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control form-control-sm" placeholder="Cari testimoni..." id="searchTestimoni" style="width: 200px;">
                                <button class="btn btn-sm btn-success" onclick="exportTestimoni()">
                                    <i class="fas fa-file-excel me-1"></i>Export Excel
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="testimoniTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Rating</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="testimoniTableBody">
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="loading"></div>
                                                <p class="mt-2 text-muted">Memuat data...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimoni Detail Modal -->
    <div class="modal fade" id="testimoniModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>Detail Testimoni
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="testimoniModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger" id="deleteBtn">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        let categoryChart, ratingChart;
        
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            loadTestimoniList();
        });
        
        function loadDashboardData() {
            fetch('../api/dashboard_stats.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        updateStats(result.data);
                        createCharts(result.data);
                        loadRecentActivity();
                    }
                })
                .catch(error => console.error('Error loading dashboard data:', error));
        }
        
        function updateStats(data) {
            document.getElementById('totalTestimoni').textContent = data.total_testimoni || 0;
            document.getElementById('totalUsers').textContent = data.total_users || 0;
            document.getElementById('avgRating').textContent = (data.avg_rating || 0).toFixed(1);
            document.getElementById('recentTestimoni').textContent = data.recent_testimoni || 0;
        }
        
        function createCharts(data) {
            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            if (categoryChart) categoryChart.destroy();
            
            categoryChart = new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: data.by_category?.map(item => item.kategori) || [],
                    datasets: [{
                        label: 'Jumlah Testimoni',
                        data: data.by_category?.map(item => item.count) || [],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.8)',
                            'rgba(124, 58, 237, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            'rgba(79, 70, 229, 1)',
                            'rgba(124, 58, 237, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
        
        function loadRecentActivity() {
            const activityContainer = document.getElementById('recentActivity');
            
            fetch('../api/dashboard_stats.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data.recent_activity) {
                        const activities = result.data.recent_activity;
                        if (activities.length > 0) {
                            activityContainer.innerHTML = activities.map(activity => `
                                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                    <div class="testimonial-avatar me-3">
                                        ${activity.username.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${escapeHtml(activity.judul)}</h6>
                                        <small class="text-muted">
                                            oleh ${escapeHtml(activity.username)} • 
                                            ${formatDate(activity.created_at)} • 
                                            ${activity.kategori} • 
                                            ${'⭐'.repeat(activity.rating)}
                                        </small>
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            activityContainer.innerHTML = '<p class="text-muted text-center">Belum ada aktivitas terbaru</p>';
                        }
                    }
                })
                .catch(error => {
                    activityContainer.innerHTML = '<p class="text-danger text-center">Gagal memuat aktivitas</p>';
                });
        }
        
        function loadTestimoniList() {
            const tableBody = document.getElementById('testimoniTableBody');
            
            fetch('../api/admin_testimoni.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        displayTestimoniTable(result.data);
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Gagal memuat data</td></tr>';
                    }
                })
                .catch(error => {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Terjadi kesalahan</td></tr>';
                });
        }
        
        function displayTestimoniTable(data) {
            const tableBody = document.getElementById('testimoniTableBody');
            
            if (data.length > 0) {
                tableBody.innerHTML = data.map(item => `
                    <tr>
                        <td>${item.id}</td>
                        <td>${escapeHtml(item.username)}</td>
                        <td>${escapeHtml(item.judul.substring(0, 50))}${item.judul.length > 50 ? '...' : ''}</td>
                        <td><span class="testimonial-category">${escapeHtml(item.kategori)}</span></td>
                        <td>${'⭐'.repeat(item.rating)}</td>
                        <td>${formatDate(item.created_at)}</td>
                        <td>
                            <span class="badge ${item.status === 'active' ? 'bg-success' : 'bg-secondary'}">
                                ${item.status === 'active' ? 'Aktif' : 'Nonaktif'}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info" onclick="viewTestimoni(${item.id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-warning" onclick="toggleStatus(${item.id}, '${item.status}')">
                                    <i class="fas fa-toggle-${item.status === 'active' ? 'on' : 'off'}"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteTestimoniConfirm(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } else {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Tidak ada data testimoni</td></tr>';
            }
        }
        
        function viewTestimoni(id) {
            // Implementation for viewing testimoni details
            const modal = new bootstrap.Modal(document.getElementById('testimoniModal'));
            modal.show();
        }
        
        function toggleStatus(id, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            if (confirm(`Ubah status testimoni menjadi ${newStatus}?`)) {
                // Implementation for toggling status
                showAlert('Status berhasil diubah!', 'success');
                loadTestimoniList();
            }
        }
        
        function deleteTestimoniConfirm(id) {
            if (confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) {
                deleteTestimoni(id);
            }
        }
        
        function refreshDashboard() {
            loadDashboardData();
            loadTestimoniList();
            showAlert('Dashboard berhasil diperbarui!', 'info');
        }
        
        function exportData() {
            window.open('../api/export_testimoni.php', '_blank');
        }
        
        function exportTestimoni() {
            window.open('../api/export_testimoni.php?format=excel', '_blank');
        }
    </script>
</body>
</html>