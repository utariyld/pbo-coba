// Main JavaScript File
document.addEventListener('DOMContentLoaded', function() {
    // Initialize page
    loadTestimoni();
    setupEventListeners();
});

// Setup Event Listeners
function setupEventListeners() {
    // Login Form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    // Register Form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }

    // Testimoni Form
    const testimoniForm = document.getElementById('testimoniForm');
    if (testimoniForm) {
        testimoniForm.addEventListener('submit', handleTestimoni);
    }

    // Smooth scroll for navbar links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Handle Login
async function handleLogin(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Login...';
        
        const response = await fetch('api/login.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Login berhasil!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert(result.message || 'Login gagal!', 'danger');
        }
    } catch (error) {
        showAlert('Terjadi kesalahan: ' + error.message, 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Handle Register
async function handleRegister(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Register...';
        
        const response = await fetch('api/register.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Registrasi berhasil! Silakan login.', 'success');
            setTimeout(() => {
                const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                registerModal.hide();
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            }, 1000);
        } else {
            showAlert(result.message || 'Registrasi gagal!', 'danger');
        }
    } catch (error) {
        showAlert('Terjadi kesalahan: ' + error.message, 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// Handle Testimoni
async function handleTestimoni(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading"></span> Mengirim...';
        
        const response = await fetch('api/testimoni.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Testimoni berhasil ditambahkan!', 'success');
            e.target.reset();
            loadTestimoni(); // Reload testimoni list
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

// Load Testimoni
async function loadTestimoni() {
    const testimoniList = document.getElementById('testimoniList');
    if (!testimoniList) return;
    
    try {
        testimoniList.innerHTML = '<div class="col-12 text-center"><div class="loading"></div> Memuat testimoni...</div>';
        
        const response = await fetch('api/get_testimoni.php');
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            testimoniList.innerHTML = result.data.map(item => createTestimoniCard(item)).join('');
            
            // Add fade-in animation
            testimoniList.querySelectorAll('.testimonial-card').forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('fade-in');
                }, index * 100);
            });
        } else {
            testimoniList.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada testimoni</h5>
                    <p class="text-muted">Jadilah yang pertama memberikan testimoni!</p>
                </div>
            `;
        }
    } catch (error) {
        testimoniList.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5 class="text-danger">Gagal memuat testimoni</h5>
                <p class="text-muted">${error.message}</p>
            </div>
        `;
    }
}

// Create Testimoni Card
function createTestimoniCard(item) {
    const stars = '⭐'.repeat(parseInt(item.rating));
    const initials = item.username.substring(0, 2).toUpperCase();
    const date = new Date(item.created_at).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    return `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="testimonial-card h-100">
                <div class="testimonial-meta">
                    <div class="testimonial-avatar">${initials}</div>
                    <div>
                        <h6 class="mb-1 fw-bold">${escapeHtml(item.username)}</h6>
                        <small class="text-muted">${date}</small>
                    </div>
                    <div class="ms-auto">
                        <div class="rating-stars">${stars}</div>
                    </div>
                </div>
                <span class="testimonial-category">${escapeHtml(item.kategori)}</span>
                <h5 class="mt-3 mb-2 fw-bold">${escapeHtml(item.judul)}</h5>
                <p class="text-muted mb-0">${escapeHtml(item.testimoni)}</p>
            </div>
        </div>
    `;
}

// Show Alert
function showAlert(message, type = 'info') {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertContainer.style.cssText = 'top: 90px; right: 20px; z-index: 9999; max-width: 400px;';
    alertContainer.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertContainer);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertContainer.remove();
    }, 5000);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Dashboard Functions
function loadDashboardStats() {
    fetch('api/dashboard_stats.php')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                updateDashboardCards(result.data);
            }
        })
        .catch(error => console.error('Error loading dashboard stats:', error));
}

function updateDashboardCards(stats) {
    const elements = {
        totalTestimoni: document.getElementById('totalTestimoni'),
        totalUsers: document.getElementById('totalUsers'),
        avgRating: document.getElementById('avgRating'),
        recentTestimoni: document.getElementById('recentTestimoni')
    };
    
    if (elements.totalTestimoni) elements.totalTestimoni.textContent = stats.total_testimoni || 0;
    if (elements.totalUsers) elements.totalUsers.textContent = stats.total_users || 0;
    if (elements.avgRating) elements.avgRating.textContent = (stats.avg_rating || 0).toFixed(1);
    if (elements.recentTestimoni) elements.recentTestimoni.textContent = stats.recent_testimoni || 0;
}

// Delete Testimoni (Admin only)
async function deleteTestimoni(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) {
        return;
    }
    
    try {
        const response = await fetch('api/delete_testimoni.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Testimoni berhasil dihapus!', 'success');
            loadTestimoni();
        } else {
            showAlert(result.message || 'Gagal menghapus testimoni!', 'danger');
        }
    } catch (error) {
        showAlert('Terjadi kesalahan: ' + error.message, 'danger');
    }
}

// Toggle Sidebar (Mobile)
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Load more testimoni (pagination)
let currentPage = 1;
async function loadMoreTestimoni() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.disabled = true;
        loadMoreBtn.innerHTML = '<span class="loading"></span> Memuat...';
        
        try {
            currentPage++;
            const response = await fetch(`api/get_testimoni.php?page=${currentPage}`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                const testimoniList = document.getElementById('testimoniList');
                const newItems = result.data.map(item => createTestimoniCard(item)).join('');
                testimoniList.insertAdjacentHTML('beforeend', newItems);
                
                // Add fade-in animation to new items
                testimoniList.querySelectorAll('.testimonial-card:not(.fade-in)').forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('fade-in');
                    }, index * 100);
                });
                
                if (!result.has_more) {
                    loadMoreBtn.style.display = 'none';
                }
            } else {
                loadMoreBtn.style.display = 'none';
            }
        } catch (error) {
            showAlert('Gagal memuat testimoni: ' + error.message, 'danger');
        } finally {
            loadMoreBtn.disabled = false;
            loadMoreBtn.innerHTML = 'Muat Lebih Banyak';
        }
    }
}

// Initialize dashboard if on dashboard page
if (window.location.pathname.includes('dashboard')) {
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardStats();
    });
}