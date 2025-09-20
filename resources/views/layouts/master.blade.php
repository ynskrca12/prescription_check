<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Reçete Uygunluk Sistemi')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome 6 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom Styles Stack -->
    @stack('styles')

    <!-- Global Styles -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: width 0.6s, height 0.6s, top 0.6s, left 0.6s;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn * {
            position: relative;
            z-index: 1;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Scrollbar Özelleştirmesi */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); z-index: 9999;">
        <div class="text-center text-white">
            <i class="fas fa-prescription-bottle-alt fa-3x mb-3 animate__animated animate__pulse animate__infinite"></i>
            <h4>Reçete Uygunluk Sistemi</h4>
            <div class="loading-spinner mx-auto mt-3"></div>
        </div>
    </div>

    <!-- Header -->
    @include('layouts.header')

    <!-- Main Content -->
    <main class="container-fluid px-4 py-4">
        <!-- Breadcrumb -->
        @if(!empty(trim($__env->yieldContent('breadcrumb'))))
            <nav aria-label="breadcrumb" class="mb-4 px-3">
                <ol class="breadcrumb bg-white p-3 rounded-3 shadow-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Ana Sayfa
                        </a>
                    </li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        @endif

        <!-- Page Title -->
        @if(!empty(trim($__env->yieldContent('page-title'))))
            <div class="row mb-4 px-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0 text-dark fw-bold">@yield('page-title')</h1>
                            @if(!empty(trim($__env->yieldContent('page-description'))))
                                <p class="text-muted mb-0">@yield('page-description')</p>
                            @endif
                        </div>
                        @yield('page-actions')
                    </div>
                </div>
            </div>
        @endif

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content Area -->
        <div class="animate__animated animate__fadeIn">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Back to Top Button -->
    <button id="back-to-top" class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle shadow-lg" style="width: 50px; height: 50px; display: none; z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/tr.min.js"></script>

    <!-- Global Scripts -->
    <script>
        $(document).ready(function() {
            // Loading screen
             setTimeout(function() {
                $('#loading-screen').fadeOut(500, function() {
                    $(this).remove(); // DOM'dan tamamen kaldır
                });
            }, 500);

            // Moment.js Türkçe locale
            moment.locale('tr');

            // Toastr global options
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "timeOut": "4000",
                "extendedTimeOut": "2000",
                "positionClass": "toast-top-right",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Back to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').fadeIn();
                } else {
                    $('#back-to-top').fadeOut();
                }
            });

            $('#back-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });

            // Auto dismiss alerts
            $('.alert:not(.alert-important)').delay(5000).fadeOut(500);

            // Form validation enhancement
            $('form').on('submit', function() {
                const submitBtn = $(this).find('button[type="submit"]');
                if (submitBtn.length) {
                    submitBtn.prop('disabled', true);
                    const originalText = submitBtn.html();
                    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>İşleniyor...');

                    // Re-enable after 5 seconds to prevent permanent disable
                    setTimeout(function() {
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }, 5000);
                }
            });

            // Tooltip initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Popover initialization
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Confirm delete functionality
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const title = $(this).data('title') || 'Bu öğeyi silmek istediğinizden emin misiniz?';

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: title,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, sil!',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Auto-resize textareas
            $('textarea').each(function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });

            $('textarea').on('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });

        // Global CSRF token setup for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global error handler
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('Hata:', msg, 'URL:', url, 'Line:', lineNo);
            return false;
        };

        // Utility functions
        window.showToast = function(type, message, title = '') {
            toastr[type](message, title);
        };

        window.showAlert = function(type, title, text) {
            Swal.fire({
                icon: type,
                title: title,
                text: text,
                confirmButtonText: 'Tamam'
            });
        };

        window.formatCurrency = function(amount) {
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY'
            }).format(amount);
        };

        window.formatDate = function(date) {
            return moment(date).format('DD.MM.YYYY HH:mm');
        };
    </script>

    <!-- Custom Scripts Stack -->
    @stack('scripts')
</body>
</html>
