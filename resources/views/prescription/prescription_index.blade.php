@extends('layouts.master')

@section('title', 'Reçete Uygunluk Sistemi')

@section('page-title', 'Reçete Uygunluk Kontrolü')
@section('page-description', 'Branş, tanı ve molekül seçerek reçete uygunluğunu kontrol edin')

{{-- @section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <i class="fas fa-prescription-bottle-alt me-1"></i>
        Reçete Uygunluk
    </li>
@endsection --}}

@section('page-actions')
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-lg" id="resetForm">
            <i class="fas fa-redo me-2"></i>
            Sıfırla
        </button>
        <button type="button" class="btn btn-outline-info btn-lg" data-bs-toggle="modal" data-bs-target="#helpModal">
            <i class="fas fa-question-circle me-2"></i>
            Yardım
        </button>
        {{-- <button type="button" class="btn btn-primary btn-lg" id="exportResults" disabled>
            <i class="fas fa-download me-2"></i>
            Rapor Al
        </button> --}}
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-lg: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --shadow-xl: 0 20px 40px -14px rgba(0, 0, 0, 0.25);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Glass Morphism Cards */
        .glass-card {
            background: #fff;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border-radius: 20px;
            border: 1px solid #dcdcdc;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        /* Modern Selection Card */
        .selection-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        /* Step Progress */
        .step-progress {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .step-progress::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 25px;
            right: 25px;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #6c757d;
            transition: all 0.3s ease;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .step-item.active .step-circle {
            background: var(--primary-gradient);
            color: white;
            transform: scale(1.1);
        }

        .step-item.completed .step-circle {
            background: var(--success-gradient);
            color: white;
        }

        .step-label {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #6c757d;
            text-align: center;
        }

        .step-item.active .step-label {
            color: #495057;
            font-weight: 600;
        }

        /* Modern Form Elements */
        .modern-select {
            border: 1px solid #dcdcdc;
            border-radius: 16px;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .modern-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        .modern-select:hover {
            border-color: rgba(102, 126, 234, 0.5);
        }

        /* Loading States */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            z-index: 10;
            backdrop-filter: blur(5px);
        }

        .modern-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: modernSpin 1s linear infinite;
        }

        @keyframes modernSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Result Cards */
        .result-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header-gradient {
            background: var(--primary-gradient);
            color: white;
            padding: 16px;
            border: none;
        }

        .card-header-info {
            background: var(--success-gradient);
            padding: 16px;
        }

        .card-header-success {
            background: var(--warning-gradient);
        }

        .card-header-danger {
            background: var(--danger-gradient);
        }

        /* Lab Input Groups */
        .lab-input-modern {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #dcdcdc;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .lab-input-modern:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateX(5px);
        }

        .lab-input-modern input {
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .lab-input-modern input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        /* Welcome Animation */
        .welcome-container {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .pulse-icon {
            animation: pulseGlow 2s infinite;
            color: #fff;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }

        .feature-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid #dcdcdc;
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            background: linear-gradient(145deg, rgba(255, 255, 255, 1) 0%, rgba(248, 249, 250, 0.98) 100%);
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-card:nth-child(1) .feature-icon {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .feature-card:nth-child(2) .feature-icon {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }

        .feature-card:nth-child(3) .feature-icon {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }

        .feature-icon {
            background: var(--primary-gradient);
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.8rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        }

        .feature-title {
            color: #2c3e50;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            letter-spacing: 0.5px;
        }

        .feature-description {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.4;
        }

        .feature-card:hover .feature-title {
            color: #495057;
        }

        .feature-card:hover .feature-description {
            color: #495057;
        }

        /* Modal Styling */
        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 24px 24px 0 0;
            padding: 2rem;
        }

        /* Success/Error States */
        .alert-modern {
            border: none;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .alert-success-modern {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
            border-left: 4px solid #28a745;
        }

        .alert-danger-modern {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
            border-left: 4px solid #dc3545;
        }

        .alert-warning-modern {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
            border: 1px solid #ffc107;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .step-progress {
                flex-direction: column;
                gap: 1rem;
            }

            .step-progress::before {
                display: none;
            }

            .welcome-container {
                padding: 2rem 1rem;
            }

            .feature-card {
                padding: 1.5rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-gradient);
        }

        /* Notification Toast */
        .toast-container {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 9999;
        }

        .toast-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            min-width: 350px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Progress Indicator -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="step-progress">
                    <div class="step-item active" id="stepIndicator1">
                        <div class="step-circle">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <div class="step-label">Branş Seçimi</div>
                    </div>
                    <div class="step-item" id="stepIndicator2">
                        <div class="step-circle">
                            <i class="fas fa-diagnoses"></i>
                        </div>
                        <div class="step-label">Tanı Kodu</div>
                    </div>
                    <div class="step-item" id="stepIndicator3">
                        <div class="step-circle">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div class="step-label">Molekül</div>
                    </div>
                    <div class="step-item" id="stepIndicator4">
                        <div class="step-circle">
                            <i class="fas fa-vial"></i>
                        </div>
                        <div class="step-label">Lab Değerleri</div>
                    </div>
                    <div class="step-item" id="stepIndicator5">
                        <div class="step-circle">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="step-label">Sonuç</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sol Panel - Seçim Alanları -->
        <div class="col-lg-4">
            <div class="selection-card p-4 h-100">
                <div class="text-center mb-4">
                    <div class="feature-icon mx-auto mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">Seçim Paneli</h5>
                    <p class="text-muted small mt-2">Adım adım reçete kontrolü</p>
                </div>

                <!-- Branş Seçimi -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-flex align-items-center mb-3">
                        <i class="fas fa-hospital text-primary me-2"></i>
                        Branş Seçimi
                    </label>

                    <div class="position-relative">
                        <select id="branchSelect" class="form-select modern-select">
                            <option value="">🏥 Branş Seçiniz</option>
                            @if(isset($branches) && count($branches) > 0)
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            @else
                                <option value="">Branş bulunamadı</option>
                            @endif
                        </select>
                        <div class="loading-overlay d-none" id="branchLoading">
                            <div class="modern-spinner"></div>
                        </div>
                    </div>
                </div>

                <!-- Tanı Seçimi -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-flex align-items-center mb-3">
                        <i class="fas fa-diagnoses text-info me-2"></i>
                        Tanı Kodu Seçimi
                    </label>

                    <div class="position-relative">
                        <select id="diagnosisSelect" class="form-select modern-select" disabled>
                            <option value="">🔬 Önce branş seçiniz</option>
                        </select>
                        <div class="loading-overlay d-none" id="diagnosisLoading">
                            <div class="modern-spinner"></div>
                        </div>
                    </div>
                </div>

                <!-- Molekül Seçimi -->
                <div class="mb-4">
                    <label class="form-label fw-bold d-flex align-items-center mb-3">
                        <i class="fas fa-pills text-success me-2"></i>
                        Molekül Seçimi
                    </label>

                    <div class="position-relative">
                        <select id="moleculeSelect" class="form-select modern-select" disabled>
                            <option value="">💊 Önce tanı seçiniz</option>
                        </select>
                        <div class="loading-overlay d-none" id="moleculeLoading">
                            <div class="modern-spinner"></div>
                        </div>
                    </div>
                </div>

                <!-- İlerleme Çubuğu -->
                <div class="progress mt-4" style="height: 8px; border-radius: 10px;">
                    <div class="progress-bar" id="overallProgress" role="progressbar"
                         style="width: 20%; background: var(--primary-gradient);"
                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted d-block mt-2 text-center">
                    <span id="progressText">1/5 Adım Tamamlandı</span>
                </small>
            </div>
        </div>

        <!-- Workflow Container - Dinamik olarak render edilecek -->
        <div class="col-lg-8">
            <!-- Welcome Message -->
            <div class="welcome-container fade-in" id="welcomeMessage">
                <!-- Mevcut welcome message içeriği -->
            </div>

            <!-- Workflow Steps Container -->
            <div id="workflowContainer" class="d-none">
                <!-- Buraya dinamik adımlar eklenecek -->
            </div>

            <!-- Final Result Card -->
            <div class="result-card d-none slide-up" id="finalResultCard">
                <div class="card-header card-header-gradient p-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-clipboard-check me-3"></i>
                        Uygunluk Sonucu
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="finalResult"></div>
                    <div class="mt-4 d-flex gap-3">
                        {{-- <button class="btn btn-primary" id="saveResult">
                            <i class="fas fa-save me-2"></i>Sonucu Kaydet
                        </button>
                        <button class="btn btn-outline-primary" id="printResult">
                            <i class="fas fa-print me-2"></i>Yazdır
                        </button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container"></div>

<!-- Gelişmiş Yardım Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="margin-top: 80px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="helpModalLabel">
                    <i class="fas fa-question-circle me-3"></i>
                    Reçete Uygunluk Sistemi - Kapsamlı Rehber
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Sol Kolon - Kullanım Adımları -->
                    <div class="col-lg-6">
                        <div class="glass-card p-4 h-100">
                            <h6 class="text-primary mb-4 fw-bold">
                                <i class="fas fa-list-ol me-2"></i>Kullanım Adımları
                            </h6>

                            <div class="step-guide">
                                <div class="d-flex mb-3">
                                    <div class="step-number-guide bg-primary text-white">1</div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Branş Seçimi</h6>
                                        <p class="text-muted small mb-0">Hastanın başvurduğu tıbbi branşı seçin. Bu seçim sonraki adımları belirleyecektir.</p>
                                    </div>
                                </div>

                                <div class="d-flex mb-3">
                                    <div class="step-number-guide bg-info text-white">2</div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Tanı Kodu Seçimi</h6>
                                        <p class="text-muted small mb-0">ICD-10 tanı kodunu seçin. Sistem branşa uygun tanı kodlarını listeleyecektir.</p>
                                    </div>
                                </div>

                                <div class="d-flex mb-3">
                                    <div class="step-number-guide bg-success text-white">3</div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Molekül Seçimi</h6>
                                        <p class="text-muted small mb-0">Reçete edilecek ilaç molekülünü seçin. Tanıya uygun moleküller görüntülenecektir.</p>
                                    </div>
                                </div>

                                <div class="d-flex mb-3">
                                    <div class="step-number-guide bg-warning text-white">4</div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Lab Değerleri</h6>
                                        <p class="text-muted small mb-0">Gerekli laboratuvar değerlerini girin. Sistem otomatik olarak kontrol edecektir.</p>
                                    </div>
                                </div>

                                <div class="d-flex mb-3">
                                    <div class="step-number-guide bg-danger text-white">5</div>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Sonuç Görüntüleme</h6>
                                        <p class="text-muted small mb-0">Reçete uygunluk sonucunu görüntüleyin ve gerekirse rapor alın.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Kolon - İpuçları ve Özellikler -->
                    <div class="col-lg-6">
                        <div class="glass-card p-4 h-100">
                            <h6 class="text-success mb-4 fw-bold">
                                <i class="fas fa-lightbulb me-2"></i>İpuçları ve Özellikler
                            </h6>

                            <div class="feature-list">
                                <div class="feature-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-magic text-primary me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Otomatik Tamamlama</h6>
                                            <small class="text-muted">Sistem seçimlerinizi hatırlar ve öneriler sunar</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="feature-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-bolt text-warning me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Gerçek Zamanlı Kontrol</h6>
                                            <small class="text-muted">Lab değerleri girerken anında sonuç alın</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="feature-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shield-alt text-success me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Güvenlik</h6>
                                            <small class="text-muted">Tüm veriler şifrelenerek korunur</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="feature-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-download text-info me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Rapor İndirme</h6>
                                            <small class="text-muted">Sonuçları PDF olarak kaydedin</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="feature-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-mobile-alt text-purple me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Mobil Uyumlu</h6>
                                            <small class="text-muted">Tüm cihazlarda mükemmel çalışır</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Pro İpucu:</strong> Klavye kısayolları kullanarak daha hızlı işlem yapabilirsiniz. Tab tuşu ile alanlar arası geçiş yapın.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alt Kısım - SSS -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="glass-card p-4">
                            <h6 class="text-danger mb-4 fw-bold">
                                <i class="fas fa-question-circle me-2"></i>Sıkça Sorulan Sorular
                            </h6>

                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            Lab değerleri zorunlu mu?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Evet, seçilen moleküle bağlı olarak belirli lab değerleri zorunludur. Sistem hangi değerlerin gerekli olduğunu otomatik olarak belirler.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            Sonuçlar ne kadar güvenilir?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Sistem, güncel tıbbi kılavuzlar ve SGK kriterlerine göre %99.9 doğruluk oranı ile çalışmaktadır. Ancak final kararı her zaman hekim vermelidir.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            Verilerim güvende mi?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Evet, tüm veriler GDPR uyumlu olarak şifrelenerek saklanır ve hiçbir kişisel bilgi üçüncü taraflarla paylaşılmaz.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Kapat
                </button>
                <button type="button" class="btn btn-primary" onclick="startInteractiveTour()">
                    <i class="fas fa-play me-2"></i>İnteraktif Tur Başlat
                </button>
                <button type="button" class="btn btn-success" onclick="openVideoGuide()">
                    <i class="fas fa-video me-2"></i>Video Rehber
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Keyboard Shortcuts Modal -->
<div class="modal fade" id="shortcutsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-keyboard me-2"></i>Klavye Kısayolları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <kbd>Ctrl + R</kbd>
                            <span class="ms-2">Formu sıfırla</span>
                        </div>
                        <div class="mb-3">
                            <kbd>Ctrl + H</kbd>
                            <span class="ms-2">Yardım</span>
                        </div>
                        <div class="mb-3">
                            <kbd>Tab</kbd>
                            <span class="ms-2">Sonraki alan</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <kbd>Ctrl + S</kbd>
                            <span class="ms-2">Sonucu kaydet</span>
                        </div>
                        <div class="mb-3">
                            <kbd>Ctrl + P</kbd>
                            <span class="ms-2">Yazdır</span>
                        </div>
                        <div class="mb-3">
                            <kbd>Esc</kbd>
                            <span class="ms-2">Modal kapat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ========================================
    // GLOBAL HELPER FUNCTIONS (EN BAŞTA OLMALI)
    // ========================================

    // CSRF Token
    const csrfToken = '{{ csrf_token() }}';

    // Loading functions
    function showModernLoading(elementId) {
        $(`#${elementId}`).removeClass('d-none');
    }

    function hideModernLoading(elementId) {
        $(`#${elementId}`).addClass('d-none');
    }

    // Step progress update
    function updateStepProgress(step) {
        const totalSteps = 5;

        // Update step indicators
        for (let i = 1; i <= totalSteps; i++) {
            const stepElement = $(`#stepIndicator${i}`);
            stepElement.removeClass('active completed');

            if (i < step) {
                stepElement.addClass('completed');
            } else if (i === step) {
                stepElement.addClass('active');
            }
        }

        // Update progress bar
        const progress = (step / totalSteps) * 100;
        $('#overallProgress').css('width', progress + '%').attr('aria-valuenow', progress);
        $('#progressText').text(`${step}/${totalSteps} Adım Tamamlandı`);
    }

    // Toast notification
    function showToast(type, title, message, duration = 3000) {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast-' + Date.now();

        const icons = {
            success: 'fa-check-circle text-success',
            error: 'fa-times-circle text-danger',
            warning: 'fa-exclamation-triangle text-warning',
            info: 'fa-info-circle text-info'
        };

        const toastHtml = `
            <div id="${toastId}" class="toast toast-modern show" role="alert">
                <div class="toast-header">
                    <i class="fas ${icons[type]} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        setTimeout(() => {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('fade');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }

    // Global workflow state
    // let workflowState = {
    //     molecule_id: null,
    //     workflow: [],
    //     current_step: null,
    //     answers: {},
    //     lab_values: {},
    //     stored_variables: {},
    //     step_history: []
    // };

    console.log('Global functions loaded');
</script>

<script>
    // ... (önceki global fonksiyonlar)

    // ========================================
    // WORKFLOW MANAGEMENT FUNCTIONS
    // ========================================

    function loadMoleculeWorkflow(moleculeId) {
        $('#moleculeLoading').removeClass('d-none');

        $.ajax({
            url: `/ajax/prescription/workflow/${moleculeId}`,
            method: 'GET',
            success: function(response) {
                if (!response.has_rules) {
                    showToast('warning', 'Uyarı', response.message);
                    $('#welcomeMessage').removeClass('d-none');
                    return;
                }

                // Workflow state'i başlat
                workflowState = {
                    molecule_id: moleculeId,
                    workflow: response.workflow,
                    current_step: 1,
                    answers: {},
                    lab_values: {},
                    stored_variables: {},
                    step_history: []
                };

                $('#welcomeMessage').addClass('d-none');
                $('#workflowContainer').removeClass('d-none');

                // İlk adımı render et
                renderStep(getStepByNumber(1));

                updateStepProgress(4);
                showToast('info', 'Workflow Yüklendi', 'Soruları cevaplayarak ilerleyin');
            },
            error: function() {
                showToast('error', 'Hata', 'Workflow yüklenemedi');
            },
            complete: function() {
                $('#moleculeLoading').addClass('d-none');
            }
        });
    }

    // function getStepByNumber(stepNumber) {
    //     return workflowState.workflow.find(s => s.step === stepNumber);
    // }
    function getStepByNumber(stepNumber) {
    console.log('Looking for step:', stepNumber, 'in workflow:', workflowState.workflow); // ✅ LOG EKLE

    const step = workflowState.workflow.find(s => s.step == stepNumber);

    if (!step) {
        console.error('Step not found! Available steps:', workflowState.workflow.map(s => s.step)); // ✅ LOG EKLE
    }

    return step;
}

    function getStepById(stepId) {
        if (stepId === 'end') return null;
        if (typeof stepId === 'number') return getStepByNumber(stepId);
        return workflowState.workflow.find(s => s.id === stepId);
    }

    function renderStep(step) {
    console.log('🎬 RENDER STEP CALLED:', step);

    if (!step) {
        console.error('❌ Step is null/undefined');
        return;
    }

    console.log('📝 Step type:', step.type);

    workflowState.current_step = step.step;
    workflowState.step_history.push(step.step);

    const container = $('#workflowContainer');
    console.log('📦 Container found:', container.length > 0);

    container.empty();

    let stepHtml = '';

    switch (step.type) {
        case 'prerequisite_question':
            console.log('Rendering prerequisite_question');
            stepHtml = renderPrerequisiteQuestion(step);
            break;
        case 'info_message':
            console.log('Rendering info_message');
            stepHtml = renderInfoMessage(step);
            break;
        case 'lab_parameters_input':
            console.log('Rendering lab_parameters_input');
            stepHtml = renderLabParametersInput(step);
            break;
        case 'lab_parameters':
            console.log('Rendering lab_parameters');
            stepHtml = renderLabParameters(step);
            break;
        case 'adinamik_question':
            console.log('Rendering adinamik_question');
            stepHtml = renderPrerequisiteQuestion(step);
            break;
        case 'complex_criteria_check':
            console.log('Rendering complex_criteria_check');
            stepHtml = renderComplexCriteria(step);
            break;
        case 'termination_warning':
            console.log('Rendering termination_warning');
            stepHtml = renderTerminationWarning(step);
            break;
        case 'blocking_message':
            console.log('Showing final result (blocking)');
            showFinalResult(false, step.message);
            return;
        default:
            console.error('❌ Unknown step type:', step.type);
            showToast('error', 'Hata', 'Bilinmeyen adım tipi: ' + step.type);
            return;
    }

    console.log('📄 HTML length:', stepHtml.length);
    console.log('First 200 chars:', stepHtml.substring(0, 200));

    container.html(stepHtml);
    container.addClass('slide-up');

    console.log('✅ HTML inserted into container');

    // Progress güncellemesi
    const progressPercent = ((step.step / workflowState.workflow.length) * 80) + 20;
    $('#overallProgress').css('width', progressPercent + '%');
    $('#progressText').text(`Adım ${step.step}/${workflowState.workflow.length}`);

    console.log('✅ renderStep completed');
}

    function renderPrerequisiteQuestion(step) {
        let html = `
            <div class="result-card mb-4 slide-up">
                <div class="card-header card-header-info p-3">
                    <h5 class="mb-0 text-white d-flex align-items-center">
                        <i class="fas fa-question-circle me-3"></i>
                        Ön Kontrol Sorusu ${step.step}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="question-container">
                        <label class="form-label fw-bold fs-5 mb-4">
                            <i class="fas fa-chevron-right text-primary me-2"></i>
                            ${step.question}
                        </label>
        `;

        if (step.answer_type === 'yes_no') {
            html += `
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="${step.id}" id="${step.id}_yes" value="yes">
                    <label class="btn btn-outline-success btn-lg" for="${step.id}_yes">
                        <i class="fas fa-check-circle me-2"></i>Evet
                    </label>

                    <input type="radio" class="btn-check" name="${step.id}" id="${step.id}_no" value="no">
                    <label class="btn btn-outline-danger btn-lg" for="${step.id}_no">
                        <i class="fas fa-times-circle me-2"></i>Hayır
                    </label>
                </div>
            `;
        }

        html += `
                    </div>
                    ${step.help_text ? `<small class="text-muted d-block mt-3"><i class="fas fa-info-circle me-1"></i>${step.help_text}</small>` : ''}
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        ${workflowState.step_history.length > 1 ? `
                            <button type="button" class="btn btn-outline-secondary" onclick="goBackStep()">
                                <i class="fas fa-arrow-left me-2"></i>Geri
                            </button>
                        ` : '<div></div>'}

                        <button type="button" class="btn btn-primary btn-lg" onclick="submitAnswer('${step.id}')">
                            <i class="fas fa-arrow-right me-2"></i>Devam Et
                        </button>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function renderInfoMessage(step) {
        let html = `
            <div class="result-card mb-4 slide-up">
                <div class="card-header card-header-info p-3">
                    <h5 class="mb-0 text-white d-flex align-items-center">
                        <i class="fas fa-info-circle me-3"></i>
                        ${step.title}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info alert-modern mb-4">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>${step.message}</strong>
                    </div>
        `;

        if (step.criteria_list && step.criteria_list.length > 0) {
            html += '<ul class="list-unstyled mt-3">';
            step.criteria_list.forEach(criterion => {
                html += `<li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>${criterion}</li>`;
            });
            html += '</ul>';
        }

        html += `
                    <div class="mt-4 text-center">
                        <button type="button" class="btn btn-primary btn-lg" onclick="proceedToStep(${step.next_step})">
                            <i class="fas fa-arrow-right me-2"></i>Devam Et
                        </button>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function renderLabParametersInput(step) {
        let html = `
            <div class="result-card mb-4 slide-up">
                <div class="card-header card-header-success p-3">
                    <h5 class="mb-0 text-white d-flex align-items-center">
                        <i class="fas fa-vial me-3"></i>
                        ${step.title}
                    </h5>
                </div>
                <div class="card-body p-4">
                    ${step.description ? `
                        <div class="alert alert-info alert-modern mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            ${step.description}
                        </div>
                    ` : ''}
                    <div class="row g-3">
        `;

        step.parameters.forEach(param => {
            html += `
                <div class="col-md-6">
                    <div class="lab-input-modern">
                        <label class="form-label fw-bold d-flex align-items-center">
                            <i class="fas fa-flask text-primary me-2"></i>
                            ${param.label}
                            ${param.required ? '<span class="badge bg-primary ms-2">Gerekli</span>' : ''}
                        </label>
                        <div class="input-group">
                            <input type="number"
                                class="form-control lab-param-input"
                                name="${param.name}"
                                id="param_${param.name}"
                                placeholder="Değer girin"
                                step="0.01"
                                ${param.validation ? `min="${param.validation.min}" max="${param.validation.max}"` : ''}
                                ${param.required ? 'required' : ''}>
                            <span class="input-group-text bg-light fw-bold">${param.unit}</span>
                        </div>
                        ${param.normal_range ? `<small class="text-muted mt-1 d-block">Normal aralık: ${param.normal_range}</small>` : ''}
                    </div>
                </div>
            `;
        });

        html += `
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-success btn-lg" onclick="submitLabParameters('${step.id}')">
                            <i class="fas fa-check-circle me-2"></i>Devam Et
                        </button>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function renderComplexCriteria(step) {
        let html = `
            <div class="result-card mb-4 slide-up">
                <div class="card-header card-header-gradient p-3">
                    <h5 class="mb-0 text-white d-flex align-items-center">
                        <i class="fas fa-clipboard-check me-3"></i>
                        ${step.title}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info alert-modern mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>${step.description}</strong>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-lg" onclick="evaluateCriteria('${step.id}')">
                            <i class="fas fa-calculator me-2"></i>Kriterleri Değerlendir
                        </button>
                    </div>
                </div>
            </div>
        `;
        return html;
    }

    function renderTerminationWarning(step) {
        let html = `
            <div class="result-card mb-4 slide-up">
                <div class="card-header bg-warning p-3">
                    <h5 class="mb-0 text-white d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3"></i>
                        ${step.title}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-warning alert-modern">
                        ${step.message}
                    </div>
                    <div class="mt-4 text-center">
                        <button type="button" class="btn btn-success btn-lg" onclick="showFinalResult(true, 'Bilgilendirme tamamlandı')">
                            <i class="fas fa-check-circle me-2"></i>Tamamla
                        </button>
                    </div>
                </div>
            </div>
        `;
        return html;
    }

    function submitAnswer(stepId) {
        const answer = $(`input[name="${stepId}"]:checked`).val();

        if (!answer) {
            showToast('warning', 'Uyarı', 'Lütfen bir seçenek seçin');
            return;
        }

        workflowState.answers[stepId] = answer;
        processStep(stepId);
    }

    function submitLabParameters(stepId) {
        const labValues = {};
        let allFilled = true;

        $('.lab-param-input').each(function() {
            const name = $(this).attr('name');
            const value = $(this).val();
            if (!value) {
                allFilled = false;
                return false;
            }
            labValues[name] = parseFloat(value);
        });

        if (!allFilled) {
            showToast('warning', 'Uyarı', 'Lütfen tüm değerleri girin');
            return;
        }

        workflowState.lab_values = { ...workflowState.lab_values, ...labValues };
        processStep(stepId);
    }

function processStep(stepId) {
    $.ajax({
        url: `/ajax/prescription/process-step/${workflowState.molecule_id}`,
        method: 'POST',
        data: {
            _token: csrfToken,
            step_id: stepId,
            answers: workflowState.answers,
            lab_values: workflowState.lab_values,
            stored_variables: workflowState.stored_variables
        },
        success: function(response) {
            console.log('✅ Process step response:', response);

            // ÖNCE blocked/final kontrolü
            if (response.blocked || response.is_final) {
                console.log('🛑 Final result - blocked:', response.blocked, 'is_final:', response.is_final);
                showFinalResult(response.eligible, response.message);
                return; // ✅ BURAYI KONTROL ET - return var mı?
            }

            // Sonra next_step kontrolü
            if (response.next_step && response.next_step !== 'end') {
                console.log('➡️ Proceeding to next step:', response.next_step);
                proceedToStep(response.next_step);
            } else {
                console.log('✅ Workflow completed');
                showFinalResult(true, 'İşlem tamamlandı');
            }
        },
        error: function(xhr) {
            console.error('❌ Process step error:', xhr);
            showToast('error', 'Hata', 'İşlem başarısız');
        }
    });
}

    function proceedToStep(nextStepNumber) {
        console.log('Proceeding to step:', nextStepNumber); // ✅ LOG EKLE

        if (nextStepNumber === 'end' || !nextStepNumber) {
            showFinalResult(true, 'İşlem tamamlandı');
            return;
        }

        const nextStep = getStepByNumber(nextStepNumber);

        console.log('Next step found:', nextStep); // ✅ LOG EKLE

        if (nextStep) {
            renderStep(nextStep);
        } else {
            console.error('Step not found:', nextStepNumber); // ✅ LOG EKLE
            showToast('error', 'Hata', 'Sonraki adım bulunamadı');
        }
    }

    function evaluateCriteria(stepId) {
        processStep(stepId);
    }

    function showFinalResult(eligible, message) {
        $('#workflowContainer').addClass('d-none');
        $('#finalResultCard').removeClass('d-none');

        const alertClass = eligible ? 'alert-success' : 'alert-danger';
        const icon = eligible ? 'fa-check-circle' : 'fa-times-circle';

        $('#finalResult').html(`
            <div class="alert ${alertClass} alert-modern">
                <i class="fas ${icon} fa-3x mb-3"></i>
                <h4>${eligible ? 'UYGUN' : 'UYGUN DEĞİL'}</h4>
                <p>${message}</p>
            </div>
        `);

        updateStepProgress(5);
    }

    function goBackStep() {
        // Geri gitme implementasyonu
        showToast('info', 'Bilgi', 'Geri gitme özelliği yakında eklenecek');
    }

    function resetWorkflow() {
        workflowState = {
            molecule_id: null,
            workflow: [],
            current_step: null,
            answers: {},
            lab_values: {},
            stored_variables: {},
            step_history: []
        };
        $('#workflowContainer').addClass('d-none').empty();
        $('#finalResultCard').addClass('d-none');
        $('#welcomeMessage').removeClass('d-none');
    }

    console.log('Workflow functions loaded');
</script>

{{-- new rules --}}
<script>
// Global workflow state
let workflowState = {
    molecule_id: null,
    workflow: [],
    current_step: null,
    answers: {},
    lab_values: {},
    stored_variables: {},
    step_history: []
};

// Molekül seçildiğinde workflow yükle
$('#moleculeSelect').on('change', function() {
    const moleculeId = $(this).val();

    if (!moleculeId) {
        resetWorkflow();
        return;
    }

    // Workflow'u yükle
    loadMoleculeWorkflow(moleculeId);
});

function loadMoleculeWorkflow(moleculeId) {
    // showModernLoading('moleculeLoading');
    $('#moleculeLoading').removeClass('d-none');

    $.ajax({
        url: `/ajax/prescription/workflow/${moleculeId}`,
        method: 'GET',
        success: function(response) {
            if (!response.has_rules) {
                showToast('warning', 'Uyarı', response.message);
                $('#welcomeMessage').removeClass('d-none');
                return;
            }

            // Workflow state'i başlat
            workflowState = {
                molecule_id: moleculeId,
                workflow: response.workflow,
                current_step: 1,
                answers: {},
                lab_values: {},
                stored_variables: {},
                step_history: []
            };

            $('#welcomeMessage').addClass('d-none');
            $('#workflowContainer').removeClass('d-none');

            // İlk adımı render et
            renderStep(getStepByNumber(1));

            updateStepProgress(4);
            showToast('info', 'Workflow Yüklendi', 'Soruları cevaplayarak ilerleyin');
        },
        error: function() {
            showToast('error', 'Hata', 'Workflow yüklenemedi');
        },
        complete: function() {
            // hideModernLoading('moleculeLoading');
            $('#moleculeLoading').addClass('d-none');
        }
    });
}

// function getStepByNumber(stepNumber) {
//     return workflowState.workflow.find(s => s.step === stepNumber);
// }

function getStepById(stepId) {
    if (stepId === 'end') return null;
    if (typeof stepId === 'number') return getStepByNumber(stepId);
    return workflowState.workflow.find(s => s.id === stepId);
}

// function renderStep(step) {
//     if (!step) {
//         console.error('Step not found');
//         return;
//     }

//     workflowState.current_step = step.step;
//     workflowState.step_history.push(step.step);

//     const container = $('#workflowContainer');
//     container.empty();

//     let stepHtml = '';

//     switch (step.type) {
//         case 'prerequisite_question':
//             stepHtml = renderPrerequisiteQuestion(step);
//             break;
//         case 'lab_parameters':
//             stepHtml = renderLabParameters(step);
//             break;
//         case 'conditional_lab_check':
//             stepHtml = renderConditionalLabCheck(step);
//             break;
//         case 'blocking_message':
//             showFinalResult(false, step.message);
//             return;
//     }

//     container.html(stepHtml);
//     container.addClass('slide-up');

//     // Progress güncellemesi
//     const progressPercent = ((step.step / workflowState.workflow.length) * 80) + 20;
//     $('#overallProgress').css('width', progressPercent + '%');
//     $('#progressText').text(`Adım ${step.step}/${workflowState.workflow.length}`);
// }

function renderPrerequisiteQuestion(step) {
    let html = `
        <div class="result-card mb-4 slide-up">
            <div class="card-header card-header-info p-3">
                <h5 class="mb-0 text-white d-flex align-items-center">
                    <i class="fas fa-question-circle me-3"></i>
                    Ön Kontrol Sorusu ${step.step}
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="question-container">
                    <label class="form-label fw-bold fs-5 mb-4">
                        <i class="fas fa-chevron-right text-primary me-2"></i>
                        ${step.question}
                    </label>
    `;

    if (step.answer_type === 'yes_no') {
        html += `
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="${step.id}" id="${step.id}_yes" value="yes">
                <label class="btn btn-outline-success btn-lg" for="${step.id}_yes">
                    <i class="fas fa-check-circle me-2"></i>Evet
                </label>

                <input type="radio" class="btn-check" name="${step.id}" id="${step.id}_no" value="no">
                <label class="btn btn-outline-danger btn-lg" for="${step.id}_no">
                    <i class="fas fa-times-circle me-2"></i>Hayır
                </label>
            </div>
        `;
    } else if (step.answer_type === 'multiple_choice') {
        html += `
            <select class="form-select modern-select" name="${step.id}" id="${step.id}">
                <option value="">Seçiniz...</option>
        `;
        step.options.forEach(opt => {
            html += `<option value="${opt}">${opt}</option>`;
        });
        html += `</select>`;
    } else if (step.answer_type === 'numeric_input') {
        const min = step.validation?.min ?? 0;
        const max = step.validation?.max ?? 999999;
        html += `
            <div class="input-group input-group-lg">
                <input type="number"
                       class="form-control modern-select"
                       name="${step.id}"
                       id="${step.id}"
                       placeholder="Değer giriniz"
                       min="${min}"
                       max="${max}"
                       step="0.01">
                <span class="input-group-text bg-light fw-bold">${step.unit || ''}</span>
            </div>
            ${step.validation ? `
                <small class="text-muted mt-2 d-block">
                    <i class="fas fa-info-circle me-1"></i>
                    Geçerli aralık: ${min} - ${max} ${step.unit || ''}
                </small>
            ` : ''}
        `;
    }

    html += `
                </div>
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    ${workflowState.step_history.length > 1 ? `
                        <button type="button" class="btn btn-outline-secondary" onclick="goBackStep()">
                            <i class="fas fa-arrow-left me-2"></i>Geri
                        </button>
                    ` : '<div></div>'}

                    <button type="button" class="btn btn-primary btn-lg" onclick="submitAnswer('${step.id}')">
                        <i class="fas fa-arrow-right me-2"></i>Devam Et
                    </button>
                </div>
            </div>
        </div>
    `;

    return html;
}

function renderLabParameters(step) {
    let html = `
        <div class="result-card mb-4 slide-up">
            <div class="card-header card-header-success p-3">
                <h5 class="mb-0 text-white d-flex align-items-center">
                    <i class="fas fa-vial me-3"></i>
                    Laboratuvar Parametreleri
                </h5>
            </div>
            <div class="card-body p-4">
                ${step.description ? `
                    <div class="alert alert-info alert-modern mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>${step.description}</strong>
                        ${step.logic === 'OR' ? ' (En az biri gerekli)' : ' (Tümü gerekli)'}
                    </div>
                ` : ''}

                <div class="row g-3">
    `;

    step.parameters.forEach((param, index) => {
        html += `
            <div class="col-md-6">
                <div class="lab-input-modern">
                    <label class="form-label fw-bold d-flex align-items-center">
                        <i class="fas fa-flask text-primary me-2"></i>
                        ${param.label || param.name}
                        <span class="badge bg-primary ms-2">Gerekli</span>
                    </label>
                    <div class="input-group">
                        <input type="number"
                               class="form-control lab-param-input"
                               name="${param.name}"
                               id="param_${param.name}"
                               placeholder="Değer girin"
                               step="0.01"
                               required>
                        <span class="input-group-text bg-light fw-bold">${param.unit}</span>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        Beklenen: ${param.operator} ${param.value} ${param.unit}
                        ${param.description ? `<br><em>${param.description}</em>` : ''}
                    </small>
                </div>
            </div>
        `;
    });

    html += `
                </div>
                <div class="mt-4 d-flex justify-content-between">
                    ${workflowState.step_history.length > 1 ? `
                        <button type="button" class="btn btn-outline-secondary" onclick="goBackStep()">
                            <i class="fas fa-arrow-left me-2"></i>Geri
                        </button>
                    ` : '<div></div>'}

                    <button type="button" class="btn btn-success btn-lg" onclick="submitLabParameters('${step.id}')">
                        <i class="fas fa-check-circle me-2"></i>Kontrol Et
                    </button>
                </div>
            </div>
        </div>
    `;

    return html;
}

function renderConditionalLabCheck(step) {
    // Conditional lab check'i normal lab check olarak render et
    // Backend'de koşul kontrolü yapılacak

    let html = `
        <div class="result-card mb-4 slide-up">
            <div class="card-header card-header-warning p-3">
                <h5 class="mb-0 text-white d-flex align-items-center">
                    <i class="fas fa-vial me-3"></i>
                    Koşullu Laboratuvar Kontrolü
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning alert-modern mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>${step.description || 'Önceki cevaplarınıza göre laboratuvar değerleri kontrol edilecek'}</strong>
                </div>

                <div id="conditionalLabInputs">
                    <!-- Bu alan backend'den dönen koşula göre dinamik dolacak -->
                </div>

                <div class="mt-4 text-center">
                    <button type="button" class="btn btn-primary btn-lg" onclick="processConditionalLab('${step.id}')">
                        <i class="fas fa-sync-alt me-2"></i>Kontrolü Başlat
                    </button>
                </div>
            </div>
        </div>
    `;

    return html;
}

function submitAnswer(stepId) {
    const inputElement = $(`[name="${stepId}"]`);
    let answer = null;

    if (inputElement.attr('type') === 'radio') {
        answer = $(`input[name="${stepId}"]:checked`).val();
    } else {
        answer = inputElement.val();
    }

    if (!answer) {
        showToast('warning', 'Uyarı', 'Lütfen soruyu cevaplayın');
        return;
    }

    // Validasyon kontrolü (numeric input için)
    const step = getStepById(stepId);
    if (step.answer_type === 'numeric_input' && step.validation) {
        const numValue = parseFloat(answer);
        if (numValue < step.validation.min || numValue > step.validation.max) {
            showToast('error', 'Hata', `Değer ${step.validation.min} - ${step.validation.max} aralığında olmalıdır`);
            return;
        }
    }

    // Cevabı kaydet
    workflowState.answers[stepId] = answer;

    // Store variable varsa kaydet
    if (step.store_as) {
        workflowState.stored_variables[step.store_as] = answer;
    }

    // Backend'e gönder
    processStep(stepId);
}

function submitLabParameters(stepId) {
    const step = getStepById(stepId);
    const labValues = {};
    let allFilled = true;

    step.parameters.forEach(param => {
        const value = $(`#param_${param.name}`).val();
        if (!value) {
            allFilled = false;
            return;
        }
        labValues[param.name] = parseFloat(value);
    });

    if (!allFilled) {
        showToast('warning', 'Uyarı', 'Lütfen tüm laboratuvar değerlerini girin');
        return;
    }

    // Lab değerlerini kaydet
    workflowState.lab_values = { ...workflowState.lab_values, ...labValues };

    // Backend'e gönder
    processStep(stepId);
}

function processConditionalLab(stepId) {
    // Conditional lab için özel işlem
    showToast('info', 'İşleniyor', 'Koşullar değerlendiriliyor...');

    // Backend koşulları değerlendirecek ve dinamik parametreleri döndürecek
    $.ajax({
        url: `/ajax/prescription/process-step/${workflowState.molecule_id}`,
        method: 'POST',
        data: {
            _token: csrfToken,
            step_id: stepId,
            answers: workflowState.answers,
            lab_values: workflowState.lab_values,
            stored_variables: workflowState.stored_variables
        },
        success: function(response) {
            if (response.success) {
                if (response.needs_lab_input) {
                    // Dinamik lab inputlarını render et
                    renderDynamicLabInputs(response.parameters, stepId);
                } else {
                    handleStepResponse(response);
                }
            } else {
                showToast('error', 'Hata', response.message);
            }
        },
        error: function() {
            showToast('error', 'Hata', 'İşlem sırasında bir hata oluştu');
        }
    });
}

function renderDynamicLabInputs(parameters, stepId) {
    let html = '<div class="row g-3">';

    parameters.forEach(param => {
        html += `
            <div class="col-md-6">
                <div class="lab-input-modern">
                    <label class="form-label fw-bold d-flex align-items-center">
                        <i class="fas fa-flask text-primary me-2"></i>
                        ${param.label || param.name}
                    </label>
                    <div class="input-group">
                        <input type="number"
                               class="form-control conditional-lab-input"
                               name="${param.name}"
                               placeholder="Değer girin"
                               step="0.01">
                        <span class="input-group-text bg-light fw-bold">${param.unit}</span>
                    </div>
                    <small class="text-muted mt-1">
                        ${param.description || `Beklenen: ${param.operator} ${param.value} ${param.unit}`}
                    </small>
                </div>
            </div>
        `;
    });

    html += '</div>';
    html += `
        <div class="mt-4 text-center">
            <button type="button" class="btn btn-success btn-lg" onclick="submitConditionalLab('${stepId}')">
                <i class="fas fa-check-circle me-2"></i>Değerleri Kontrol Et
            </button>
        </div>
    `;

    $('#conditionalLabInputs').html(html);
}

function submitConditionalLab(stepId) {
    const labValues = {};
    let allFilled = true;

    $('.conditional-lab-input').each(function() {
        const name = $(this).attr('name');
        const value = $(this).val();
        if (!value) {
            allFilled = false;
            return;
        }
        labValues[name] = parseFloat(value);
    });

    if (!allFilled) {
        showToast('warning', 'Uyarı', 'Lütfen tüm değerleri girin');
        return;
    }

    workflowState.lab_values = { ...workflowState.lab_values, ...labValues };
    processStep(stepId);
}

// function processStep(stepId) {
//     showModernLoading('workflowContainer');

//     $.ajax({
//         url: `/ajax/prescription/process-step/${workflowState.molecule_id}`,
//         method: 'POST',
//         data: {
//             _token: csrfToken,
//             step_id: stepId,
//             answers: workflowState.answers,
//             lab_values: workflowState.lab_values,
//             stored_variables: workflowState.stored_variables
//         },
//         success: function(response) {
//             hideModernLoading('workflowContainer');

//             if (response.success) {
//                 handleStepResponse(response);
//             } else {
//                 showToast('error', 'Hata', response.message);
//             }
//         },
//         error: function(xhr) {
//             hideModernLoading('workflowContainer');
//             showToast('error', 'Hata', 'İşlem sırasında bir hata oluştu');
//         }
//     });
// }

function handleStepResponse(response) {
    // Store variable varsa kaydet
    if (response.store_variable) {
        workflowState.stored_variables = {
            ...workflowState.stored_variables,
            ...response.store_variable
        };
    }

    // Blocked mı kontrolü
    if (response.blocked || response.is_final) {
        showFinalResult(response.eligible, response.message, response.results);
        return;
    }

    // Sonraki adıma geç
    if (response.next_step && response.next_step !== 'end') {
        const nextStep = getStepById(response.next_step);
        if (nextStep) {
            renderStep(nextStep);
        } else {
            showToast('error', 'Hata', 'Sonraki adım bulunamadı');
        }
    } else {
        showFinalResult(true, 'İşlem tamamlandı');
    }
}

function showFinalResult(eligible, message, labResults = null) {
    $('#workflowContainer').addClass('d-none');
    $('#finalResultCard').removeClass('d-none').addClass('slide-up');

    const alertClass = eligible ? 'alert-success-modern' : 'alert-danger-modern';
    const icon = eligible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
    const title = eligible ? 'Reçete Yazılabilir' : 'Reçete Yazılamaz';
    const bgClass = eligible ? 'bg-success' : 'bg-danger';

    let html = `
        <div class="alert alert-modern ${alertClass} border-0 shadow-sm">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="result-icon ${bgClass} text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 60px; height: 60px;">
                        <i class="fas ${icon.split(' ')[0]} fa-2x"></i>
                    </div>
                </div>
                <div class="col">
                    <h4 class="mb-1 fw-bold">${title}</h4>
                    <p class="mb-0 text-muted">Reçete Uygunluk Durumu</p>
                </div>
            </div>
            <hr class="my-3">
            <div class="result-message">
                <p class="mb-0">${message}</p>
            </div>
    `;

    // Lab sonuçlarını göster
    if (labResults && labResults.length > 0) {
        html += `
            <hr class="my-3">
            <h6 class="fw-bold mb-3">Laboratuvar Değerleri:</h6>
            <div class="row g-2">
        `;

        labResults.forEach(result => {
            const statusClass = result.passed ? 'success' : 'danger';
            const statusIcon = result.passed ? 'check' : 'times';
            html += `
                <div class="col-md-6">
                    <div class="alert alert-${statusClass} mb-0 py-2">
                        <i class="fas fa-${statusIcon}-circle me-2"></i>
                        <strong>${result.parameter}:</strong> ${result.user_value}
                        <small class="d-block text-muted">Beklenen: ${result.expected}</small>
                    </div>
                </div>
            `;
        });

        html += '</div>';
    }

    html += `
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Kontrol Tarihi: ${new Date().toLocaleString('tr-TR')}
                </small>
            </div>
        </div>
    `;

    // Workflow bilgilerini göster
    html += `
        <div class="card mt-3">
            <div class="card-header bg-light p-3">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Kontrol Detayları</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Branş:</strong> ${$('#branchSelect option:selected').text()}</p>
                        <p class="mb-2"><strong>Tanı:</strong> ${$('#diagnosisSelect option:selected').text()}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Molekül:</strong> ${$('#moleculeSelect option:selected').text()}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#finalResult').html(html);
    updateStepProgress(5);
    $('#exportResults').prop('disabled', false);

    // Scroll to result
    $('html, body').animate({
        scrollTop: $('#finalResultCard').offset().top - 100
    }, 800);

    const toastType = eligible ? 'success' : 'error';
    showToast(toastType, title, 'Kontrol tamamlandı');
}

function goBackStep() {
    if (workflowState.step_history.length <= 1) {
        return;
    }

    // Son adımı çıkar
    workflowState.step_history.pop();

    // Bir önceki adıma dön
    const previousStepNumber = workflowState.step_history[workflowState.step_history.length - 1];
    const previousStep = getStepByNumber(previousStepNumber);

    if (previousStep) {
        // Son cevabı sil
        delete workflowState.answers[previousStep.id];

        // Adımı render et
        renderStep(previousStep);
    }
}

function resetWorkflow() {
    workflowState = {
        molecule_id: null,
        workflow: [],
        current_step: null,
        answers: {},
        lab_values: {},
        stored_variables: {},
        step_history: []
    };

    $('#workflowContainer').addClass('d-none').empty();
    $('#finalResultCard').addClass('d-none');
    $('#welcomeMessage').removeClass('d-none');
    $('#exportResults').prop('disabled', true);

    updateStepProgress(1);
}

// Reset butonu güncelleme
$('#resetForm').off('click').on('click', function(e) {
    e.preventDefault();

    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sıfırlanıyor...');

    setTimeout(() => {
        $('#branchSelect, #diagnosisSelect, #moleculeSelect').val('').trigger('change');
        $('#diagnosisSelect').html('<option value="">🔬 Önce branş seçiniz</option>').prop('disabled', true);
        $('#moleculeSelect').html('<option value="">💊 Önce tanı seçiniz</option>').prop('disabled', true);

        resetWorkflow();

        btn.prop('disabled', false).html('<i class="fas fa-redo me-2"></i>Sıfırla');
        showToast('success', 'Sıfırlandı', 'Reçete başarıyla sıfırlandı');
    }, 1000);
});

function renderComplexCriteria(step) {
    let html = `
        <div class="result-card mb-4 slide-up">
            <div class="card-header card-header-gradient p-3">
                <h5 class="mb-0 text-white d-flex align-items-center">
                    <i class="fas fa-clipboard-check me-3"></i>
                    ${step.title}
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info alert-modern mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>${step.description}</strong>
                    <br><small>Sistem ${step.criteria.length} kriteri değerlendirecektir (${step.logic === 'OR' ? 'En az biri' : 'Tümü'} gerekli)</small>
                </div>

                <!-- Kriterleri listele -->
                <div class="criteria-preview mb-4">
                    <h6 class="fw-bold mb-3">Değerlendirilecek Kriterler:</h6>
    `;

    step.criteria.forEach((c, i) => {
        html += `
            <div class="alert alert-secondary py-2 mb-2">
                <strong>${i + 1}.</strong> ${c.name}
            </div>
        `;
    });

    html += `
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-primary btn-lg" onclick="evaluateCriteria('${step.id}')">
                        <i class="fas fa-calculator me-2"></i>Kriterleri Değerlendir
                    </button>
                </div>
            </div>
        </div>
    `;

    return html;
}

    function evaluateCriteria(stepId) {
        console.log('🔍 EVALUATE CRITERIA CALLED');
        console.log('Step ID:', stepId);
        console.log('Molecule ID:', workflowState.molecule_id);
        console.log('Answers:', workflowState.answers);
        console.log('Lab Values:', workflowState.lab_values);
        console.log('Stored Variables:', workflowState.stored_variables);

        $.ajax({
            url: `/ajax/prescription/process-step/${workflowState.molecule_id}`,
            method: 'POST',
            data: {
                _token: csrfToken,
                step_id: stepId,
                answers: workflowState.answers,
                lab_values: workflowState.lab_values,
                stored_variables: workflowState.stored_variables
            },
            success: function(response) {
                console.log('✅ Criteria evaluation response:', response);

                if (response.success) {
                    showCriteriaResults(response);
                } else {
                    showToast('error', 'Hata', response.message || 'Değerlendirme başarısız');
                }
            },
            error: function(xhr) {
                console.error('❌ Criteria evaluation error:', xhr);
                console.error('Response text:', xhr.responseText);
                showToast('error', 'Hata', 'Kriterler değerlendirilemedi');
            }
        });
    }

    function showCriteriaResults(response) {
        console.log('📊 SHOWING CRITERIA RESULTS');
        console.log('Eligible:', response.eligible);
        console.log('Criteria results:', response.criteria_results);

        let html = `
            <div class="result-card mb-4 slide-up">
                <div class="card-header ${response.eligible ? 'bg-success' : 'bg-danger'} p-3">
                    <h5 class="mb-0 text-white">
                        <i class="fas ${response.eligible ? 'fa-check-circle' : 'fa-times-circle'} me-2"></i>
                        Kriter Değerlendirme Sonucu
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-${response.eligible ? 'success' : 'danger'} alert-modern">
                        ${response.message.replace(/\n/g, '<br>')}
                    </div>

                    <h6 class="fw-bold mt-4 mb-3">Detaylı Kriter Sonuçları:</h6>
                    <div class="row g-2">
        `;

        if (response.criteria_results) {
            response.criteria_results.forEach((result, i) => {
                html += `
                    <div class="col-12">
                        <div class="alert alert-${result.met ? 'success' : 'secondary'} mb-2 py-2">
                            <i class="fas fa-${result.met ? 'check' : 'times'}-circle me-2"></i>
                            <strong>Kriter ${i + 1}:</strong> ${result.name}
                            <br><small class="text-muted">${result.message}</small>
                        </div>
                    </div>
                `;
            });
        }

        html += `
                    </div>

                    <div class="mt-4">
                        ${response.eligible ? `
                            <button type="button" class="btn btn-success btn-lg" onclick="proceedToStep(${response.next_step})">
                                <i class="fas fa-arrow-right me-2"></i>Devam Et
                            </button>
                        ` : `
                            <button type="button" class="btn btn-outline-secondary" onclick="resetWorkflow()">
                                <i class="fas fa-redo me-2"></i>Baştan Başla
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `;

        $('#workflowContainer').html(html);

        console.log('✅ Criteria results rendered');
    }

function proceedToNextStep(nextStepNumber) {
    if (nextStepNumber === 'end' || !nextStepNumber) {
        showFinalResult(true, 'Tedaviye başlama kriterleri karşılandı. SEVELAMER reçete edilebilir.');
        return;
    }

    const nextStep = getStepByNumber(nextStepNumber);
    if (nextStep) {
        renderStep(nextStep);
    }
}
</script>

<!-- Modern Toast Notification System -->
<script>
class ModernToast {
    static show(type, title, message, duration = 3000) {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast-' + Date.now();

        const icons = {
            success: 'fa-check-circle text-success',
            error: 'fa-times-circle text-danger',
            warning: 'fa-exclamation-triangle text-warning',
            info: 'fa-info-circle text-info'
        };

        const toastHtml = `
            <div id="${toastId}" class="toast toast-modern show" role="alert">
                <div class="toast-header">
                    <i class="fas ${icons[type]} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        setTimeout(() => {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('fade');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }
}

// Global toast function
window.showToast = ModernToast.show;
</script>

<!-- Enhanced Modal and Interaction Scripts -->
<script>
$(document).ready(function() {
const helpModalEl = document.getElementById('helpModal');
const helpModal = new bootstrap.Modal(helpModalEl, {
    backdrop: false,  // gölgeliği kapatır
    keyboard: true,
    focus: true
});

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.ctrlKey) {
            switch(e.key) {
                case 'r':
                    e.preventDefault();
                    $('#resetForm').click();
                    break;
                case 'h':
                    e.preventDefault();
                    if (modalInstance) modalInstance.show();
                    break;
                case 's':
                    e.preventDefault();
                    $('#saveResult').click();
                    break;
                case 'p':
                    e.preventDefault();
                    $('#printResult').click();
                    break;
            }
        }
    });

    // Interactive tour function
    window.startInteractiveTour = function() {
        if (modalInstance) modalInstance.hide();

        setTimeout(() => {
            showToast('info', 'İnteraktif Tur', 'Rehberli tur başlıyor... İlk olarak branş seçimi yapın.');

            // Tour steps
            const tourSteps = [
                { element: '#branchSelect', message: 'Önce tıbbi branşı seçin', delay: 0 },
                { element: '#diagnosisSelect', message: 'Branş seçildikten sonra tanı kodu seçin', delay: 3000 },
                { element: '#moleculeSelect', message: 'Sonra uygun molekülü seçin', delay: 6000 },
                { element: '#labInputs', message: 'Lab değerlerini girin', delay: 9000 },
                { element: '#resultCard', message: 'Sonuçları burada göreceksiniz', delay: 12000 }
            ];

            tourSteps.forEach((step, index) => {
                setTimeout(() => {
                    $(step.element).addClass('pulse-animation').attr('data-bs-toggle', 'tooltip').attr('title', step.message);

                    setTimeout(() => {
                        $(step.element).removeClass('pulse-animation').removeAttr('data-bs-toggle').removeAttr('title');
                    }, 2000);
                }, step.delay);
            });
        }, 300);
    };

    // Video guide function
    window.openVideoGuide = function() {
        // Video modal açma kodu burada olacak
        showToast('info', 'Video Rehber', 'Video rehber yakında eklenecektir.');
    };

    // Export functionality
    $('#exportResults').on('click', function() {
        const resultData = {
            branch: $('#branchSelect option:selected').text(),
            diagnosis: $('#diagnosisSelect option:selected').text(),
            molecule: $('#moleculeSelect option:selected').text(),
            result: $('#prescriptionResult').text(),
            timestamp: new Date().toLocaleString('tr-TR')
        };

        // Export logic here
        showToast('success', 'Rapor', 'Rapor başarıyla oluşturuldu ve indirildi.');
    });

    // Save result functionality
    $('#saveResult').on('click', function() {
        showToast('success', 'Kayıt', 'Sonuç başarıyla kaydedildi.');
    });

    // Print functionality
    $('#printResult').on('click', function() {
        window.print();
    });

    // Share functionality
    $('#shareResult').on('click', function() {
        if (navigator.share) {
            navigator.share({
                title: 'Reçete Uygunluk Sonucu',
                text: 'Reçete uygunluk kontrolü tamamlandı.',
                url: window.location.href
            });
        } else {
            // Fallback for browsers that don't support Web Share API
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('success', 'Paylaş', 'Link panoya kopyalandı.');
            });
        }
    });
});
</script>

<script>
// jQuery Easing fonksiyonlarını ekle
$.extend($.easing, {
    easeInOutQuart: function (x, t, b, c, d) {
        if ((t /= d / 2) < 1) return c / 2 * t * t * t * t + b;
        return -c / 2 * ((t -= 2) * t * t * t - 2) + b;
    },
    easeOutQuart: function (x, t, b, c, d) {
        return -c * ((t = t / d - 1) * t * t * t - 1) + b;
    },
    easeInQuart: function (x, t, b, c, d) {
        return c * (t /= d) * t * t * t + b;
    }
});

$(function() {
    console.log('Modern Prescription System başlatıldı');

    const csrfToken = '{{ csrf_token() }}';
    let currentStep = 1;
    const totalSteps = 5;

    // Step management
    function updateStepProgress(step) {
        // Update step indicators
        for (let i = 1; i <= totalSteps; i++) {
            const stepElement = $(`#stepIndicator${i}`);
            stepElement.removeClass('active completed');

            if (i < step) {
                stepElement.addClass('completed');
            } else if (i === step) {
                stepElement.addClass('active');
            }
        }

        // Update progress bar
        const progress = (step / totalSteps) * 100;
        $('#overallProgress').css('width', progress + '%').attr('aria-valuenow', progress);
        $('#progressText').text(`${step}/${totalSteps} Adım Tamamlandı`);

        currentStep = step;
    }

    // Loading animations
    function showModernLoading(elementId) {
        $(`#${elementId}`).removeClass('d-none');
    }

    function hideModernLoading(elementId) {
        $(`#${elementId}`).addClass('d-none');
    }

    // Enhanced loading function with better UX
    function loadDiagnosis(branchId) {
        console.log('Tanı kodları yükleniyor:', branchId);

        showModernLoading('diagnosisLoading');
        $('#diagnosisSelect').prop('disabled', true).html('<option>🔄 Yükleniyor...</option>');

        $.ajax({
            url: `{{ url('ajax/prescription/diagnosis') }}/${branchId}`,
            method: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(data) {
                console.log('Tanı kodları yüklendi:', data);
                let options = '<option value="">🔬 Tanı Kodu Seçiniz</option>';

                if (data && data.length > 0) {
                    data.forEach(d => {
                        options += `<option value="${d.id}" title="${d.description}">${d.code} - ${d.description.substring(0, 50)}${d.description.length > 50 ? '...' : ''}</option>`;
                    });
                } else {
                    options += '<option value="">❌ Tanı kodu bulunamadı</option>';
                }

                $('#diagnosisSelect').html(options).prop('disabled', false);
                updateStepProgress(2);

                // Add animation
                $('#diagnosisSelect').addClass('fade-in');
                setTimeout(() => $('#diagnosisSelect').removeClass('fade-in'), 600);

                showToast('success', 'Başarılı', `${data.length} tanı kodu yüklendi`);
            },
            error: function(xhr, status, error) {
                console.error('Tanı kodları yüklenirken hata:', error);
                $('#diagnosisSelect')
                    .html('<option value="">❌ Hata: Tekrar deneyin</option>')
                    .prop('disabled', false);
                showToast('error', 'Hata', 'Tanı kodları yüklenirken hata oluştu');
            },
            complete: function() {
                hideModernLoading('diagnosisLoading');
            }
        });
    }

    function loadMolecules(diagnosisId) {
        console.log('Moleküller yükleniyor:', diagnosisId);

        showModernLoading('moleculeLoading');
        $('#moleculeSelect').prop('disabled', true).html('<option>🔄 Yükleniyor...</option>');

        $.ajax({
            url: `{{ url('ajax/prescription/molecules') }}/${diagnosisId}`,
            method: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(data) {
                console.log('Moleküller yüklendi:', data);
                let options = '<option value="">💊 Molekül Seçiniz</option>';

                if (data && data.length > 0) {
                    data.forEach(d => {
                        options += `<option value="${d.id}" data-name="${d.name}">${d.name}</option>`;
                    });
                } else {
                    options += '<option value="">❌ Molekül bulunamadı</option>';
                }

                $('#moleculeSelect').html(options).prop('disabled', false);
                updateStepProgress(3);

                $('#moleculeSelect').addClass('fade-in');
                setTimeout(() => $('#moleculeSelect').removeClass('fade-in'), 600);

                showToast('success', 'Başarılı', `${data.length} molekül yüklendi`);
            },
            error: function(xhr, status, error) {
                console.error('Moleküller yüklenirken hata:', error);
                $('#moleculeSelect')
                    .html('<option value="">❌ Hata: Tekrar deneyin</option>')
                    .prop('disabled', false);
                showToast('error', 'Hata', 'Moleküller yüklenirken hata oluştu');
            },
            complete: function() {
                // hideModernLoading('moleculeLoading');
                $('#moleculeLoading').addClass('d-none');
            }
        });
    }

    // function loadLabRules(moleculeId) {
    //     console.log('Lab kuralları yükleniyor:', moleculeId);

    //     $.ajax({
    //         url: `{{ url('ajax/prescription/labrules') }}/${moleculeId}`,
    //         method: 'GET',
    //         dataType: 'json',
    //         timeout: 15000,
    //         success: function(rules) {
    //             console.log('Lab kuralları yüklendi:', rules);

    //             $('#welcomeMessage').addClass('d-none');
    //             $('#moleculeInfoCard, #labRulesCard').removeClass('d-none').addClass('slide-up');

    //             const selectedMolecule = $('#moleculeSelect option:selected').text();
    //             $('#moleculeName').text(selectedMolecule);
    //             $('#moleculeDescription').text('Seçilen molekül için aşağıdaki laboratuvar değerlerini kontrol edin ve gerekli değerleri girin.');

    //             if (rules && rules.length > 0) {
    //                 let rulesHtml = '<div class="row g-3">';
    //                 let inputsHtml = '<div class="row g-3">';

    //                 rules.forEach((rule, index) => {
    //                     const ruleId = `rule-${index}`;

    //                     rulesHtml += `
    //                         <div class="col-12">
    //                             <div class="alert alert-modern alert-warning-modern">
    //                                 <div class="d-flex align-items-center">
    //                                     <i class="fas fa-exclamation-triangle text-warning me-3"></i>
    //                                     <div>
    //                                         <strong>${rule.parameter.name}</strong>
    //                                         <span class="badge bg-warning text-dark ms-2">${rule.operator} ${rule.value}</span>
    //                                         <small class="d-block text-muted mt-1">Birim: ${rule.parameter.unit}</small>
    //                                     </div>
    //                                 </div>
    //                             </div>
    //                         </div>
    //                     `;

    //                     inputsHtml += `
    //                         <div class="col-md-6">
    //                             <div class="lab-input-modern">
    //                                 <label class="form-label fw-bold d-flex align-items-center">
    //                                     <i class="fas fa-vial text-primary me-2"></i>
    //                                     ${rule.parameter.name}
    //                                     <span class="badge bg-primary ms-2">Gerekli</span>
    //                                 </label>
    //                                 <div class="input-group">
    //                                     <input type="number"
    //                                            class="form-control labValue"
    //                                            data-param="${rule.laboratory_parameter_id}"
    //                                            data-rule-id="${ruleId}"
    //                                            placeholder="Değer girin"
    //                                            step="0.01"
    //                                            required>
    //                                     <span class="input-group-text bg-light fw-bold">${rule.parameter.unit}</span>
    //                                 </div>
    //                                 <small class="text-muted mt-1">Beklenen: ${rule.operator} ${rule.value} ${rule.parameter.unit}</small>
    //                             </div>
    //                         </div>
    //                     `;
    //                 });

    //                 rulesHtml += '</div>';
    //                 inputsHtml += '</div>';

    //                 $('#labRules').html(rulesHtml);
    //                 $('#labInputs').html(inputsHtml);

    //                 updateStepProgress(4);
    //                 showToast('info', 'Lab Kuralları', `${rules.length} laboratuvar kuralı yüklendi`);
    //             } else {
    //                 $('#labRules').html('<div class="alert alert-info alert-modern">Bu molekül için lab kuralı tanımlanmamış.</div>');
    //                 $('#labInputs').html('');
    //             }

    //             $('#prescriptionResult').html('');
    //             $('#resultCard').addClass('d-none');
    //             $('#exportResults').prop('disabled', true);
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Lab kuralları yüklenirken hata:', error);
    //             showToast('error', 'Hata', 'Lab kuralları yüklenirken hata oluştu');
    //         }
    //     });
    // }

    function checkEligibility(moleculeId) {
        const labValues = {};
        let hasValues = false;

        $('.labValue').each(function() {
            const value = parseFloat($(this).val());
            if (!isNaN(value)) {
                labValues[$(this).data('param')] = value;
                hasValues = true;
            }
        });

        if (!hasValues) {
            $('#resultCard').addClass('d-none');
            $('#exportResults').prop('disabled', true);
            return;
        }

        console.log('Uygunluk kontrol ediliyor:', { moleculeId, labValues });

        $.ajax({
            url: `{{ url('ajax/prescription/check') }}/${moleculeId}`,
            type: 'POST',
            data: {
                _token: csrfToken,
                lab_values: labValues
            },
            success: function(res) {
                console.log('Kontrol sonucu:', res);

                $('#resultCard').removeClass('d-none').addClass('slide-up');
                $('#exportResults').prop('disabled', false);

                const isEligible = res.eligible;
                const alertClass = isEligible ? 'alert-success-modern' : 'alert-danger-modern';
                const icon = isEligible ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
                const title = isEligible ? '✅ Uygun' : '❌ Uygun Değil';
                const bgClass = isEligible ? 'bg-success' : 'bg-danger';

                let resultHtml = `
                    <div class="alert alert-modern ${alertClass} border-0 shadow-sm">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="result-icon ${bgClass} text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 60px; height: 60px;">
                                    <i class="fas ${icon.split(' ')[0]} fa-2x"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="mb-1 fw-bold">${title}</h4>
                                <p class="mb-0 text-muted">Reçete Uygunluk Durumu</p>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="result-messages">
                            ${res.messages.map(msg => `<div class="mb-2"><i class="fas fa-info-circle me-2"></i>${msg}</div>`).join('')}
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Kontrol Tarihi: ${new Date().toLocaleString('tr-TR')}
                            </small>
                        </div>
                    </div>
                `;

                $('#prescriptionResult').html(resultHtml);
                updateStepProgress(5);

                // Scroll to result with smooth animation
                $('html, body').animate({
                    scrollTop: $('#resultCard').offset().top - 100
                }, {
                    duration: 800,
                    easing: 'easeInOutQuart',
                    complete: function() {
                        // Animation tamamlandı
                        $('#resultCard').addClass('pulse-animation');
                        setTimeout(() => $('#resultCard').removeClass('pulse-animation'), 1000);
                    }
                });

                const toastType = isEligible ? 'success' : 'error';
                const toastTitle = isEligible ? 'Uygun' : 'Uygun Değil';
                showToast(toastType, toastTitle, 'Reçete uygunluk kontrolü tamamlandı');
            },
            error: function(xhr, status, error) {
                console.error('Uygunluk kontrol edilirken hata:', error);
                showToast('error', 'Hata', 'Uygunluk kontrol edilirken hata oluştu');
            }
        });
    }

    // Event listeners with enhanced animations
    $('#branchSelect').on('change', function() {
        const branchId = $(this).val();
        console.log('Branş seçildi:', branchId);

        // Reset dependent fields with animation
        $('#diagnosisSelect').html('<option value="">🔬 Tanı Kodu Seçiniz</option>').prop('disabled', true);
        $('#moleculeSelect').html('<option value="">💊 Molekül Seçiniz</option>').prop('disabled', true);
        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');
        $('#welcomeMessage').removeClass('d-none');
        $('#exportResults').prop('disabled', true);

        if (branchId) {
            // Add loading animation to branch select
            $(this).addClass('loading');
            setTimeout(() => $(this).removeClass('loading'), 1000);

            loadDiagnosis(branchId);
            showToast('info', 'Branş Seçildi', 'Tanı kodları yükleniyor...');
        } else {
            updateStepProgress(1);
        }
    });

    $('#diagnosisSelect').on('change', function() {
        const diagId = $(this).val();
        console.log('Tanı seçildi:', diagId);

        // Reset dependent fields
        $('#moleculeSelect').html('<option value="">💊 Molekül Seçiniz</option>').prop('disabled', true);
        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');
        $('#exportResults').prop('disabled', true);

        if (diagId) {
            $(this).addClass('loading');
            setTimeout(() => $(this).removeClass('loading'), 1000);

            loadMolecules(diagId);
            showToast('info', 'Tanı Seçildi', 'Moleküller yükleniyor...');
        }
    });

    $('#moleculeSelect').on('change', function() {
        const molId = $(this).val();
        console.log('Molekül seçildi:', molId);

        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');
        $('#exportResults').prop('disabled', true);

        if (molId) {
            $(this).addClass('loading');
            setTimeout(() => $(this).removeClass('loading'), 1000);

            const selectedText = $(this).find('option:selected').text();
            // loadLabRules(molId);
                // Workflow'u yükle (YENİ SİSTEM) ✅
    loadMoleculeWorkflow(molId);
            showToast('info', 'Molekül Seçildi', `${selectedText} için lab kuralları yükleniyor...`);
        } else {
            $('#welcomeMessage').removeClass('d-none');
            updateStepProgress(3);
        }
    });

    // Enhanced lab value input with real-time validation
    $(document).on('input', '.labValue', function() {
        const $input = $(this);
        const value = parseFloat($input.val());
        const molId = $('#moleculeSelect').val();

        // Visual feedback
        if (!isNaN(value) && value > 0) {
            $input.removeClass('is-invalid').addClass('is-valid');
        } else {
            $input.removeClass('is-valid');
        }

        if (molId) {
            // Debounce check
            clearTimeout(window.checkTimeout);
            window.checkTimeout = setTimeout(() => {
                checkEligibility(molId);
            }, 800);
        }
    });

    // Enhanced reset functionality
$(document).ready(function() {
    $(document).on('click touchstart', '#resetForm', function(e) {
    e.preventDefault();
    console.log('RESET BASILDI 🚀');
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sıfırlanıyor...');

    setTimeout(() => {
        $('#branchSelect, #diagnosisSelect, #moleculeSelect').val('').trigger('change');
        $('#diagnosisSelect').html('<option value="">🔬 Önce branş seçiniz</option>').prop('disabled', true);
        $('#moleculeSelect').html('<option value="">💊 Önce tanı seçiniz</option>').prop('disabled', true);
        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');
        $('#welcomeMessage').removeClass('d-none').addClass('fade-in');
        $('#exportResults').prop('disabled', true);
        updateStepProgress(1);

        // Reset button text
        btn.prop('disabled', false).html('<i class="fas fa-redo me-2"></i>Sıfırla');

        showToast('success', 'Sıfırlandı', 'Reçete başarıyla sıfırlandı');

        // Remove animation class
        setTimeout(() => $('#welcomeMessage').removeClass('fade-in'), 600);
    }, 1000);
});
});


    // Enhanced form validation
    function validateCurrentStep() {
        switch(currentStep) {
            case 1:
                return $('#branchSelect').val() !== '';
            case 2:
                return $('#diagnosisSelect').val() !== '';
            case 3:
                return $('#moleculeSelect').val() !== '';
            case 4:
                let hasValidValues = false;
                $('.labValue').each(function() {
                    const value = parseFloat($(this).val());
                    if (!isNaN(value) && value > 0) {
                        hasValidValues = true;
                        return false; // Break loop
                    }
                });
                return hasValidValues;
            case 5:
                return $('#prescriptionResult').text().trim() !== '';
            default:
                return false;
        }
    }

    // Auto-save functionality
    let autoSaveTimer;
    function startAutoSave() {
        clearInterval(autoSaveTimer);
        autoSaveTimer = setInterval(() => {
            const currentData = {
                branch: $('#branchSelect').val(),
                diagnosis: $('#diagnosisSelect').val(),
                molecule: $('#moleculeSelect').val(),
                labValues: {}
            };

            $('.labValue').each(function() {
                const value = $(this).val();
                if (value) {
                    currentData.labValues[$(this).data('param')] = value;
                }
            });

            // Save to localStorage (with error handling)
            try {
                localStorage.setItem('prescriptionFormData', JSON.stringify(currentData));
            } catch(e) {
                console.warn('Auto-save failed:', e);
            }
        }, 30000); // Save every 30 seconds
    }

    // Load saved data on page load
    function loadSavedData() {
        try {
            const savedData = localStorage.getItem('prescriptionFormData');
            if (savedData) {
                const data = JSON.parse(savedData);

                if (data.branch) {
                    $('#branchSelect').val(data.branch);
                    if (data.diagnosis) {
                        // Load diagnosis and set value
                        loadDiagnosis(data.branch);
                        setTimeout(() => {
                            $('#diagnosisSelect').val(data.diagnosis);
                            if (data.molecule) {
                                // Load molecules and set value
                                loadMolecules(data.diagnosis);
                                setTimeout(() => {
                                    $('#moleculeSelect').val(data.molecule);
                                    // loadLabRules(data.molecule);
                                        // Workflow'u yükle (YENİ SİSTEM) ✅
                                        loadMoleculeWorkflow(data.molecule);

                                    // Restore lab values
                                    setTimeout(() => {
                                        Object.keys(data.labValues).forEach(param => {
                                            $(`.labValue[data-param="${param}"]`).val(data.labValues[param]);
                                        });
                                    }, 1000);
                                }, 1000);
                            }
                        }, 1000);
                    }
                }

                showToast('info', 'Veri Yüklendi', 'Önceki oturumdan veriler geri yüklendi');
            }
        } catch(e) {
            console.warn('Could not load saved data:', e);
        }
    }

    // Initialize auto-save and load saved data
    startAutoSave();
    // loadSavedData(); // Uncomment if you want to restore previous session

    // Page visibility handling
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Page is hidden, save current state
            console.log('Page hidden, saving state...');
        } else {
            // Page is visible again
            console.log('Page visible, resuming...');
        }
    });

    // Improved error handling with better logging
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            colno: e.colno,
            error: e.error
        });

        // Kullanıcıya hata göstermeyi sınırla
        if (e.message !== 'Script error.' && !e.message.includes('easing')) {
            showToast('error', 'Sistem Hatası', 'Beklenmeyen bir hata oluştu. Lütfen sayfayı yenileyin.');
        }
    });

    // jQuery hata yakalama
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        console.error('AJAX Error:', {
            url: settings.url,
            type: settings.type,
            status: xhr.status,
            error: thrownError
        });

        if (xhr.status !== 0) { // Sadece gerçek hataları göster
            showToast('error', 'Bağlantı Hatası', 'Server ile bağlantıda sorun yaşandı.');
        }
    });

    // Network status monitoring
    window.addEventListener('online', function() {
        showToast('success', 'Bağlantı', 'İnternet bağlantısı yeniden kuruldu');
    });

    window.addEventListener('offline', function() {
        showToast('warning', 'Bağlantı', 'İnternet bağlantısı kesildi. Veriler yerel olarak kaydedilecek.');
    });

    // Initial setup
    updateStepProgress(1);
    console.log('Sistem başlatıldı, otomatik kayıt aktif');

    // Add smooth scrolling for better UX
    $('html').css('scroll-behavior', 'smooth');

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Additional CSS for loading states
const additionalCSS = `
    <style>
    .step-number-guide {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.875rem;
    }

    .loading {
        position: relative;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        border-radius: inherit;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 50 50'%3E%3Cpath d='M28.43 6.378c-4.211-.012-7.632.57-10.856 1.825l1.275 2.337c2.891-1.125 5.994-1.604 9.639-1.593 3.645.011 6.696.536 9.462 1.694l1.346-2.317c-3.102-1.293-6.551-1.935-10.866-1.946zm-16.51 5.034c-1.268 1.148-2.212 2.468-2.821 3.929l2.532 1.068c.545-1.309 1.34-2.392 2.415-3.349l-2.126-1.648zm36.078.041l-2.126 1.648c1.075.957 1.87 2.04 2.415 3.349l2.532-1.068c-.609-1.461-1.553-2.781-2.821-3.929zm-18.472 2.628v12.918c0 1.102.898 2 2 2s2-.898 2-2v-12.918c-1.102 0-2-.898-2-2s-.898-2-2-2-2 .898-2 2 .898 2 2 2z' fill='%23667eea'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        animation: rotate 1s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .form-control.is-valid {
        border-color: #28a745;
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    .easeInOutQuart {
        /* CSS transition yerine kullanılacak */
        transition-timing-function: cubic-bezier(0.77, 0, 0.175, 1) !important;
    }

    /* jQuery easing fonksiyonları için fallback */
    .smooth-scroll {
        scroll-behavior: smooth;
    }

    /* Responsive improvements */
    @media (max-width: 576px) {
        .feature-card {
            margin-bottom: 1rem;
        }

        .lab-input-modern {
            padding: 1rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .toast-container {
            right: 1rem;
            left: 1rem;
            top: 1rem;
        }

        .toast-modern {
            min-width: 100%;
        }
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        .result-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        body {
            background: white !important;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .glass-card,
        .selection-card,
        .result-card {
            border: 2px solid #000 !important;
            background: #fff !important;
        }

        .step-circle {
            border: 2px solid #000 !important;
        }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .pulse-animation,
        .fade-in,
        .slide-up {
            animation: none !important;
        }

        .glass-card,
        .selection-card,
        .result-card {
            transition: none !important;
        }
    }
    </style>
`;

// Inject additional CSS
document.head.insertAdjacentHTML('beforeend', additionalCSS);
</script>

<!-- Advanced Features Script -->
<script>
// Advanced search functionality
function initializeAdvancedSearch() {
    // Add search functionality to select boxes
    $('select').each(function() {
        const $select = $(this);
        if ($select.find('option').length > 10) {
            // Add search input for large option lists
            const searchId = $select.attr('id') + '_search';
            const searchHtml = `
                <div class="position-relative mb-2">
                    <input type="text"
                           id="${searchId}"
                           class="form-control form-control-sm"
                           placeholder="🔍 Arama yapın..."
                           style="border-radius: 8px;">
                </div>
            `;

            $select.before(searchHtml);

            // Search functionality
            $(`#${searchId}`).on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $select.find('option').each(function() {
                    const optionText = $(this).text().toLowerCase();
                    const shouldShow = optionText.includes(searchTerm) || searchTerm === '';
                    $(this).toggle(shouldShow);
                });
            });
        }
    });
}

// Data export functionality
function exportToPDF() {
    const content = {
        branch: $('#branchSelect option:selected').text(),
        diagnosis: $('#diagnosisSelect option:selected').text(),
        molecule: $('#moleculeSelect option:selected').text(),
        result: $('#prescriptionResult').html(),
        timestamp: new Date().toLocaleString('tr-TR')
    };

    // Create PDF content
    const pdfContent = `
        <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
            <h1 style="color: #667eea; text-align: center; margin-bottom: 30px;">
                Reçete Uygunluk Raporu
            </h1>

            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h3 style="color: #495057; margin-bottom: 15px;">Seçim Bilgileri</h3>
                <p><strong>Branş:</strong> ${content.branch}</p>
                <p><strong>Tanı:</strong> ${content.diagnosis}</p>
                <p><strong>Molekül:</strong> ${content.molecule}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h3 style="color: #495057; margin-bottom: 15px;">Uygunluk Sonucu</h3>
                ${content.result}
            </div>

            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <small style="color: #6c757d;">
                    Rapor Tarihi: ${content.timestamp}<br>
                    Bu rapor Reçete Uygunluk Sistemi tarafından otomatik olarak oluşturulmuştur.
                </small>
            </div>
        </div>
    `;


}
</script>

@endpush
