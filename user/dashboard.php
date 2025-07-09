<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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
    <title>Dashboard User - TestimoniApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-comments me-2"></i>TestimoniApp
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?= $_SESSION['username'] ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#profile"><i class="fas fa-user-edit me-1"></i>Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="margin-top: 100px;">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">
                                    <i class="fas fa-tachometer-alt text-primary me-2"></i>
                                    Dashboard Saya
                                </h2>
                                <p class="text-muted mb-0">Selamat datang kembali, <strong><?= $_SESSION['username'] ?></strong>! Kelola testimoni Anda dengan mudah.</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-primary" onclick="showTestimoniForm()">
                                    <i class="fas fa-plus me-1"></i>Tulis Testimoni Baru
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="dashboard-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number" id="myTestimoni">0</div>
                    <div class="stat-label">Testimoni Saya</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="dashboard-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-number" id="myAvgRating">0</div>
                    <div class="stat-label">Rating Rata-rata</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="dashboard-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-number" id="joinDate"><?= date('M Y', strtotime($_SESSION['created_at'] ?? 'now')) ?></div>
                    <div class="stat-label">Bergabung Sejak</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="dashboard-card text-center">
                    <div class="stat-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-number" id="rank">-</div>
                    <div class="stat-label">Peringkat</div>
                </div>
            </div>
        </div>

        <!-- Charts & Quick Actions -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Testimoni Saya per Kategori
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-rocket me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-grid gap-3 flex-grow-1">
                            <button class="btn btn-primary" onclick="showTestimoniForm()">
                                <i class="fas fa-plus me-2"></i>Tulis Testimoni Baru
                            </button>
                            <button class="btn btn-outline-primary" onclick="scrollToMyTestimoni()">
                                <i class="fas fa-list me-2"></i>Lihat Testimoni Saya
                            </button>
                            <button class="btn btn-outline-success" onclick="exportMyTestimoni()">
                                <i class="fas fa-download me-2"></i>Export Data Saya
                            </button>
                            <button class="btn btn-outline-info" onclick="showProfileModal()">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </button>
                        </div>
                        
                        <!-- Achievement Badge -->
                        <div class="mt-4 p-3 bg-light rounded text-center">
                            <i class="fas fa-medal fa-2x text-warning mb-2"></i>
                            <h6 class="mb-1">Achievement</h6>
                            <small class="text-muted" id="achievementText">Kontributor Aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Testimoni Baru -->
        <div class="row mb-4" id="testimoniFormRow" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Tulis Testimoni Baru
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="newTestimoniForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="newRating" class="form-label">Rating</label>
                                    <select class="form-select" id="newRating" name="rating" required>
                                        <option value="">Pilih Rating</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (5 Bintang)</option>
                                        <option value="4">⭐⭐⭐⭐ (4 Bintang)</option>
                                        <option value="3">⭐⭐⭐ (3 Bintang)</option>
                                        <option value="2">⭐⭐ (2 Bintang)</option>
                                        <option value="1">⭐ (1 Bintang)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="newKategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="newKategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Layanan">Layanan</option>
                                        <option value="Produk">Produk</option>
                                        <option value="Aplikasi">Aplikasi</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="newJudul" class="form-label">Judul Testimoni</label>
                                <input type="text" class="form-control" id="newJudul" name="judul" required placeholder="Masukkan judul testimoni">
                            </div>
                            <div class="mb-3">
                                <label for="newTestimoni" class="form-label">Testimoni</label>
                                <textarea class="form-control" id="newTestimoni" name="testimoni" rows="4" required placeholder="Bagikan pengalaman Anda..."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Kirim Testimoni
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="hideTestimoniForm()">
                                    <i class="fas fa-times me-1"></i>Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimoni Saya -->
        <div class="row mb-4" id="myTestimoniSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Testimoni Saya
                        </h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="loadMyTestimoni()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="myTestimoniList" class="row">
                            <div class="col-12 text-center py-4">
                                <div class="loading"></div>
                                <p class="mt-2 text-muted">Memuat testimoni Anda...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="profileForm">
                        <div class="mb-3">
                            <label for="profileUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="profileUsername" name="username" value="<?= $_SESSION['username'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="profileEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="profileEmail" name="email" value="<?= $_SESSION['email'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="profilePassword" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" class="form-control" id="profilePassword" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 TestimoniApp. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        let myChart;
        
        document.addEventListener('DOMContentLoaded', function() {
            loadUserStats();
            loadMyTestimoni();
            setupFormHandler();
        });
        
        function loadUserStats() {
            fetch('../api/dashboard_stats.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        updateUserStats(result.data);
                        createMyChart(result.data);
                    }
                })
                .catch(error => console.error('Error loading user stats:', error));
        }
        
        function updateUserStats(data) {
            document.getElementById('myTestimoni').textContent = data.my_testimoni || 0;
            document.getElementById('myAvgRating').textContent = (data.my_avg_rating || 0).toFixed(1);
            
            // Update achievement based on number of testimoni
            const count = data.my_testimoni || 0;
            let achievement = 'Pemula';
            if (count >= 10) achievement = 'Master Reviewer';
            else if (count >= 5) achievement = 'Kontributor Aktif';
            else if (count >= 1) achievement = 'Reviewer Baru';
            
            document.getElementById('achievementText').textContent = achievement;
            
            // Set rank based on average rating
            const avgRating = data.my_avg_rating || 0;
            let rank = 'Unranked';
            if (avgRating >= 4.5) rank = 'Gold';
            else if (avgRating >= 4.0) rank = 'Silver';
            else if (avgRating >= 3.0) rank = 'Bronze';
            
            document.getElementById('rank').textContent = rank;
        }
        
        function createMyChart(data) {
            const ctx = document.getElementById('myChart').getContext('2d');
            if (myChart) myChart.destroy();
            
            if (data.my_by_category && data.my_by_category.length > 0) {
                myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.my_by_category.map(item => item.kategori),
                        datasets: [{
                            data: data.my_by_category.map(item => item.count),
                            backgroundColor: [
                                'rgba(79, 70, 229, 0.8)',
                                'rgba(124, 58, 237, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } else {
                ctx.canvas.parentElement.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Belum ada data untuk ditampilkan</h6>
                        <p class="text-muted">Tulis testimoni pertama Anda!</p>
                    </div>
                `;
            }
        }
        
        function loadMyTestimoni() {
            const container = document.getElementById('myTestimoniList');
            container.innerHTML = `
                <div class="col-12 text-center py-4">
                    <div class="loading"></div>
                    <p class="mt-2 text-muted">Memuat testimoni Anda...</p>
                </div>
            `;
            
            fetch('../api/my_testimoni.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        displayMyTestimoni(result.data);
                    } else {
                        container.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h6 class="text-muted">Gagal memuat testimoni</h6>
                                <p class="text-muted">${result.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    container.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <h6 class="text-danger">Terjadi kesalahan</h6>
                            <p class="text-muted">${error.message}</p>
                        </div>
                    `;
                });
        }
        
        function displayMyTestimoni(data) {
            const container = document.getElementById('myTestimoniList');
            
            if (data.length > 0) {
                container.innerHTML = data.map(item => `
                    <div class="col-lg-6 mb-4">
                        <div class="testimonial-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="testimonial-category">${escapeHtml(item.kategori)}</span>
                                    <div class="rating-stars mt-1">${'⭐'.repeat(item.rating)}</div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="editTestimoni(${item.id})">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteMyTestimoni(${item.id})">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                            <h5 class="mb-2">${escapeHtml(item.judul)}</h5>
                            <p class="text-muted mb-3">${escapeHtml(item.testimoni)}</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>${formatDate(item.created_at)}
                            </small>
                        </div>
                    </div>
                `).join('');
            } else {
                container.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Belum ada testimoni</h6>
                        <p class="text-muted">Tulis testimoni pertama Anda sekarang!</p>
                        <button class="btn btn-primary" onclick="showTestimoniForm()">
                            <i class="fas fa-plus me-1"></i>Tulis Testimoni
                        </button>
                    </div>
                `;
            }
        }
        
        function setupFormHandler() {
            const form = document.getElementById('newTestimoniForm');
            if (form) {
                form.addEventListener('submit', handleNewTestimoni);
            }
        }
        
        async function handleNewTestimoni(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading"></span> Mengirim...';
                
                const response = await fetch('../api/testimoni.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Testimoni berhasil ditambahkan!', 'success');
                    e.target.reset();
                    hideTestimoniForm();
                    loadUserStats();
                    loadMyTestimoni();
                } else {
                    showAlert(result.message || 'Gagal menambahkan testimoni!', 'danger');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan: ' + error.message, 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
        
        function showTestimoniForm() {
            document.getElementById('testimoniFormRow').style.display = 'block';
            document.getElementById('testimoniFormRow').scrollIntoView({ behavior: 'smooth' });
        }
        
        function hideTestimoniForm() {
            document.getElementById('testimoniFormRow').style.display = 'none';
        }
        
        function scrollToMyTestimoni() {
            document.getElementById('myTestimoniSection').scrollIntoView({ behavior: 'smooth' });
        }
        
        function showProfileModal() {
            const modal = new bootstrap.Modal(document.getElementById('profileModal'));
            modal.show();
        }
        
        function editTestimoni(id) {
            // Implementation for editing testimoni
            showAlert('Fitur edit sedang dalam pengembangan', 'info');
        }
        
        function deleteMyTestimoni(id) {
            if (confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) {
                deleteTestimoni(id);
            }
        }
        
        function exportMyTestimoni() {
            window.open('../api/export_my_testimoni.php', '_blank');
        }
    </script>
</body>
</html>