<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Testimoni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-comments me-2"></i>TestimoniApp
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimoni">Testimoni</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?= $_SESSION['username'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($_SESSION['role'] == 'admin'): ?>
                                    <li><a class="dropdown-item" href="admin/dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard Admin</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="user/dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard User</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Bagikan Pengalaman Anda
                    </h1>
                    <p class="lead text-white mb-4">
                        Platform testimoni terpercaya untuk berbagi pengalaman dan membantu orang lain membuat keputusan yang tepat.
                    </p>
                    <div class="d-flex gap-3">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="#testimoni" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-plus me-2"></i>Tulis Testimoni
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light btn-lg px-4" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="fas fa-user-plus me-2"></i>Bergabung Sekarang
                            </button>
                        <?php endif; ?>
                        <a href="#testimoni" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-eye me-2"></i>Lihat Testimoni
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-quote-left hero-icon text-white"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimoni Section -->
    <section id="testimoni" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Testimoni Pengguna</h2>
                    <p class="lead text-muted">Dengarkan pengalaman dari pengguna lain</p>
                </div>
            </div>

            <!-- Form Testimoni -->
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="row mb-5">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-edit me-2 text-primary"></i>Tulis Testimoni Anda
                            </h5>
                            <form id="testimoniForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rating" class="form-label">Rating</label>
                                        <select class="form-select" id="rating" name="rating" required>
                                            <option value="">Pilih Rating</option>
                                            <option value="5">⭐⭐⭐⭐⭐ (5 Bintang)</option>
                                            <option value="4">⭐⭐⭐⭐ (4 Bintang)</option>
                                            <option value="3">⭐⭐⭐ (3 Bintang)</option>
                                            <option value="2">⭐⭐ (2 Bintang)</option>
                                            <option value="1">⭐ (1 Bintang)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="kategori" class="form-label">Kategori</label>
                                        <select class="form-select" id="kategori" name="kategori" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Layanan">Layanan</option>
                                            <option value="Produk">Produk</option>
                                            <option value="Aplikasi">Aplikasi</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="judul" class="form-label">Judul Testimoni</label>
                                    <input type="text" class="form-control" id="judul" name="judul" required placeholder="Masukkan judul testimoni">
                                </div>
                                <div class="mb-3">
                                    <label for="testimoni" class="form-label">Testimoni</label>
                                    <textarea class="form-control" id="testimoni" name="testimoni" rows="4" required placeholder="Bagikan pengalaman Anda..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Testimoni
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Daftar Testimoni -->
            <div class="row" id="testimoniList">
                <!-- Testimoni akan dimuat di sini via AJAX -->
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="loginUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="loginUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="regUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="regUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="regEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="regEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="regPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="regPassword" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2024 TestimoniApp. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>