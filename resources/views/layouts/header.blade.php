<header class="navbar navbar-expand-lg navbar-dark bg-gradient shadow-sm sticky-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center fw-bold" href="{{ url('/') }}">
            <i class="fas fa-prescription-bottle-alt me-2 text-primary fs-4"></i>
            <span class="brand-text">
                Reçete Uygunluk <small class="text-light opacity-75 d-block">Sistemi</small>
            </span>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars text-white"></i>
        </button>

        <!-- Navigation Menu -->
        <nav class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto d-flex align-items-center">
                <!-- Ana Sayfa -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="fas fa-home me-2"></i>
                        <span>Ana Sayfa</span>
                    </a>
                </li>

                <!-- Reçeteler -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover {{ request()->is('receteler*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-prescription me-2"></i>
                        <span>Reçeteler</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border-0">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/receteler') }}">
                                <i class="fas fa-list me-2 text-primary"></i>
                                Tüm Reçeteler
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/receteler/yeni') }}">
                                <i class="fas fa-plus-circle me-2 text-success"></i>
                                Yeni Reçete Ekle
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/receteler/bekleyen') }}">
                                <i class="fas fa-clock me-2 text-warning"></i>
                                Bekleyen Reçeteler
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/receteler/onaylanan') }}">
                                <i class="fas fa-check-circle me-2 text-success"></i>
                                Onaylanan Reçeteler
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/receteler/reddedilen') }}">
                                <i class="fas fa-times-circle me-2 text-danger"></i>
                                Reddedilen Reçeteler
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Hasta Yönetimi -->
                {{-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover {{ request()->is('hastalar*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-injured me-2"></i>
                        <span>Hastalar</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border-0">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/hastalar') }}">
                                <i class="fas fa-users me-2 text-primary"></i>
                                Hasta Listesi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/hastalar/yeni') }}">
                                <i class="fas fa-user-plus me-2 text-success"></i>
                                Yeni Hasta Kaydı
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/hastalar/arama') }}">
                                <i class="fas fa-search me-2 text-info"></i>
                                Hasta Arama
                            </a>
                        </li>
                    </ul>
                </li> --}}

                <!-- İlaçlar -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover {{ request()->is('ilaclar*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-pills me-2"></i>
                        <span>İlaçlar</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border-0">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ilaclar') }}">
                                <i class="fas fa-capsules me-2 text-primary"></i>
                                İlaç Listesi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ilaclar/yeni') }}">
                                <i class="fas fa-plus-square me-2 text-success"></i>
                                Yeni İlaç Ekle
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ilaclar/etkilesim') }}">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                İlaç Etkileşimleri
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ilaclar/stok') }}">
                                <i class="fas fa-warehouse me-2 text-info"></i>
                                Stok Durumu
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Doktorlar -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover {{ request()->is('doktorlar*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-md me-2"></i>
                        <span>Doktorlar</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border-0">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/doktorlar') }}">
                                <i class="fas fa-stethoscope me-2 text-primary"></i>
                                Doktor Listesi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/doktorlar/yeni') }}">
                                <i class="fas fa-user-plus me-2 text-success"></i>
                                Yeni Doktor Kaydı
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/doktorlar/uzmanlik') }}">
                                <i class="fas fa-graduation-cap me-2 text-info"></i>
                                Uzmanlık Alanları
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Raporlar -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover {{ request()->is('raporlar*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-chart-line me-2"></i>
                        <span>Raporlar</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border-0">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/raporlar/genel') }}">
                                <i class="fas fa-chart-bar me-2 text-primary"></i>
                                Genel İstatistikler
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/raporlar/recete') }}">
                                <i class="fas fa-file-alt me-2 text-info"></i>
                                Reçete Raporları
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/raporlar/ilac') }}">
                                <i class="fas fa-pill me-2 text-warning"></i>
                                İlaç Raporları
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/raporlar/gunluk') }}">
                                <i class="fas fa-calendar-day me-2 text-success"></i>
                                Günlük Rapor
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/raporlar/aylik') }}">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                Aylık Rapor
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Ayarlar -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover {{ request()->is('ayarlar*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog me-2"></i>
                        <span>Ayarlar</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow border-0">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ayarlar/sistem') }}">
                                <i class="fas fa-server me-2 text-primary"></i>
                                Sistem Ayarları
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ayarlar/kullanici') }}">
                                <i class="fas fa-users-cog me-2 text-info"></i>
                                Kullanıcı Yönetimi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ayarlar/yedek') }}">
                                <i class="fas fa-database me-2 text-warning"></i>
                                Yedekleme
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ayarlar/log') }}">
                                <i class="fas fa-clipboard-list me-2 text-secondary"></i>
                                Sistem Logları
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Divider -->
                <li class="nav-item">
                    <div class="vr mx-2 d-none d-lg-block" style="height: 30px; opacity: 0.3;"></div>
                </li>

                <!-- Bildirimler -->
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                            3
                            <span class="visually-hidden">okunmamış bildirim</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-0" style="width: 300px;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-bell me-2"></i>Bildirimler</span>
                            <small class="text-muted">3 yeni</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-exclamation-circle text-warning me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="small">Yeni reçete onay bekliyor</div>
                                        <small class="text-muted">2 dakika önce</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="small">Stok uyarısı: Aspirin</div>
                                        <small class="text-muted">1 saat önce</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="#">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-user-plus text-success me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="small">Yeni doktor kaydı</div>
                                        <small class="text-muted">3 saat önce</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="text-center">
                            <a class="dropdown-item py-2 text-primary" href="{{ url('/bildirimler') }}">
                                <small>Tüm bildirimleri görüntüle</small>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Kullanıcı Profil Menüsü -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center px-3 py-2 rounded-3 mx-1 nav-link-hover" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar me-2">
                            <i class="fas fa-user-circle fs-4"></i>
                        </div>
                        <div class="d-flex flex-column align-items-start">
                            <small class="mb-0 fw-semibold">{{ Auth::user()->name ?? 'Kullanıcı' }}</small>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ Auth::user()->role ?? 'Admin' }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-0">
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/profil') }}">
                                <i class="fas fa-user me-2 text-info"></i>
                                Profilim
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/ayarlar/hesap') }}">
                                <i class="fas fa-cog me-2 text-secondary"></i>
                                Hesap Ayarları
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/bildirimler') }}">
                                <i class="fas fa-bell me-2 text-warning"></i>
                                Bildirimler
                                <span class="badge bg-danger ms-auto">3</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ url('/yardim') }}">
                                <i class="fas fa-question-circle me-2 text-primary"></i>
                                Yardım
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="{{ url('/cikis') }}" onclick="event.preventDefault(); confirmLogout();">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Çıkış Yap
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="#" method="POST" class="d-none">
        @csrf
    </form>
</header>

<!-- Header Özel CSS -->
<style>
/* Header Özel Stilleri */
.navbar {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    min-height: 70px;
}

.navbar-brand {
    font-size:  18px;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0.5rem 0;
}

.navbar-brand:hover {
    transform: translateY(-2px);
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.brand-text small {
    font-size:  12px;
    font-weight: 300;
    line-height: 1;
}

.nav-link-hover {
    position: relative;
    transition: all 0.3s ease;
    overflow: hidden;
    border-radius: 8px !important;
}

.nav-link-hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
    z-index: 1;
}

.nav-link-hover:hover::before {
    left: 100%;
}

.nav-link-hover:hover, .nav-link-hover.active {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.nav-link-hover.active {
    background: rgba(52, 152, 219, 0.3);
    border: 1px solid rgba(52, 152, 219, 0.5);
}

.dropdown-menu-dark {
    background: rgba(52, 73, 94, 0.95);
    backdrop-filter: blur(15px);
    border-radius: 12px;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
    animation: slideDown 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.dropdown-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
    border-left-color: #3498db;
}

.dropdown-header {
    padding: 0.5rem 1rem;
    font-weight: 600;
    color: #ecf0f1;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 0.5rem;
}

.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.vr {
    background: rgba(255, 255, 255, 0.2);
}

.navbar-toggler {
    padding: 0.25rem 0.5rem;
    font-size: 1.1rem;
    border-radius: 8px;
}

.navbar-toggler:focus {
    box-shadow: none;
    background: rgba(255, 255, 255, 0.1);
}

/* Notification Badge */
.notification-badge {
    font-size: 0.65rem;
    animation: pulse 2s infinite;
    margin-left: -8px;
    margin-top: 2px;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

/* Responsive Tasarım */
@media (max-width: 991.98px) {
    .navbar-nav {
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 1rem;
    }

    .vr {
        display: none !important;
    }

    .nav-link-hover {
        margin: 0.2rem 0;
        border-radius: 8px;
    }

    .dropdown-menu {
        position: static;
        float: none;
        width: auto;
        margin-top: 0;
        background-color: transparent;
        border: 0;
        box-shadow: none;
        padding-left: 1rem;
    }

    .dropdown-item {
        color: #ecf0f1;
        padding: 0.3rem 1rem;
    }

    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ecf0f1;
    }
}

/* Dark Theme Support */
@media (prefers-color-scheme: dark) {
    .navbar {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
    }
}
</style>

<!-- Header JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logout confirmation
    window.confirmLogout = function() {
        Swal.fire({
            title: 'Çıkış Yapmak İstediğinizden Emin Misiniz?',
            text: 'Oturumunuz sonlandırılacak.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, Çıkış Yap',
            cancelButtonText: 'İptal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    };

    // Active menu highlighting
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href.replace(window.location.origin, ''))) {
            link.classList.add('active');
        }
    });

    // Notification click tracking
    const notificationItems = document.querySelectorAll('.dropdown-menu a[href="#"]');
    notificationItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            // Burada bildirim okundu olarak işaretleme kodu eklenebilir
            console.log('Bildirim tıklandı:', this.textContent.trim());
        });
    });

    // Mobile menu auto-close
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    document.addEventListener('click', function(e) {
        if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
            if (navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        }
    });

    // Search functionality (if needed)
    window.globalSearch = function(query) {
        if (query.length > 2) {
            // Global search implementation
            console.log('Arama yapılıyor:', query);
            // AJAX call to search endpoint
        }
    };
});
</script>
