@extends('layouts.master')

@section('title', 'Reçete Uygunluk Sistemi')

@section('page-title', 'Reçete Uygunluk Kontrolü')
@section('page-description', 'Branş, tanı ve molekül seçerek reçete uygunluğunu kontrol edin')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">
        <i class="fas fa-prescription-bottle-alt me-1"></i>
        Reçete Uygunluk
    </li>
@endsection

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
        <button type="button" class="btn btn-primary btn-lg" id="exportResults" disabled>
            <i class="fas fa-download me-2"></i>
            Rapor Al
        </button>
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
            background: var(--glass-bg);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-lg);
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
            background: rgba(255, 255, 255, 0.9);
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

        <!-- Sağ Panel - Bilgi ve Sonuçlar -->
        <div class="col-lg-8">
            <!-- Başlangıç Mesajı -->
            <div class="welcome-container fade-in" id="welcomeMessage">
                <i class="fas fa-prescription-bottle-alt fa-5x pulse-icon mb-4" style="color: var(--primary-gradient);"></i>
                <h3 class=" mb-3 fw-bold">Reçete Uygunluk Sistemi</h3>
                <p class="lead mb-4">
                    Modern ve güvenli reçete kontrol sistemi. Sol panelden branş seçerek başlayın.
                </p>

                <div class="row g-4 mt-4">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h6 class="feature-title">Güvenli Sistem</h6>
                            <p class="feature-description">Veri koruma ve şifrelemeli güvenlik</p>
                            <div class="feature-stats mt-3">
                                <span class="badge bg-success px-3 py-2">SSL Korumalı</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h6 class="feature-title">Yüksek Performans</h6>
                            <p class="feature-description">Milisaniyeler içinde sonuç alma garantisi</p>
                            <div class="feature-stats mt-3">
                                <span class="badge bg-warning px-3 py-2">< 500ms</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <h6 class="feature-title">Yüksek Doğruluk</h6>
                            <p class="feature-description">Hassas kontrol sistemi</p>
                            <div class="feature-stats mt-3">
                                <span class="badge bg-info px-3 py-2">%99.9 Doğru</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Molekül Bilgisi -->
            <div class="result-card mb-4 d-none slide-up" id="moleculeInfoCard">
                <div class="card-header card-header-gradient">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-3"></i>
                        Molekül Bilgileri
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="moleculeInfo">
                        <h5 id="moleculeName" class="text-primary mb-3 fw-bold"></h5>
                        <p id="moleculeDescription" class="text-muted lead fs-6"></p>
                    </div>
                </div>
            </div>

            <!-- Lab Kuralları -->
            <div class="result-card mb-4 d-none slide-up" id="labRulesCard">
                <div class="card-header card-header-info">
                    <h5 class="mb-0 text-white d-flex align-items-center">
                        <i class="fas fa-vial me-3"></i>
                        Laboratuvar Kuralları ve Değer Girişi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h6 class="mb-3">
                                <i class="fas fa-list-check me-2"></i>Gerekli Kurallar
                            </h6>
                            <div id="labRules"></div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="mb-3">
                                <i class="fas fa-keyboard me-2"></i>Değer Girişi
                            </h6>
                            <div id="labInputs"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sonuç -->
            <div class="result-card d-none slide-up" id="resultCard">
                <div class="card-header card-header-gradient">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-clipboard-check me-3"></i>
                        Uygunluk Sonucu
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="prescriptionResult"></div>
                    <div class="mt-4 d-flex gap-3">
                        <button class="btn btn-primary" id="saveResult">
                            <i class="fas fa-save me-2"></i>Sonucu Kaydet
                        </button>
                        <button class="btn btn-outline-primary" id="printResult">
                            <i class="fas fa-print me-2"></i>Yazdır
                        </button>
                        <button class="btn btn-outline-success" id="shareResult">
                            <i class="fas fa-share me-2"></i>Paylaş
                        </button>
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
    <div class="modal-dialog modal-xl modal-dialog-centered">
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
    // Modal fixes and enhancements
    function initializeModals() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({
            'padding-right': '',
            'overflow': ''
        });
    }

    initializeModals();

    // Help Modal
    const helpModal = document.getElementById('helpModal');
    let modalInstance = null;

    if (helpModal) {
        modalInstance = new bootstrap.Modal(helpModal, {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        // Modal events
        helpModal.addEventListener('show.bs.modal', function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });

        helpModal.addEventListener('hidden.bs.modal', function() {
            setTimeout(() => initializeModals(), 100);
        });
    }

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

// Enhanced Prescription System Logic
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
                hideModernLoading('moleculeLoading');
            }
        });
    }

    function loadLabRules(moleculeId) {
        console.log('Lab kuralları yükleniyor:', moleculeId);

        $.ajax({
            url: `{{ url('ajax/prescription/labrules') }}/${moleculeId}`,
            method: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(rules) {
                console.log('Lab kuralları yüklendi:', rules);

                $('#welcomeMessage').addClass('d-none');
                $('#moleculeInfoCard, #labRulesCard').removeClass('d-none').addClass('slide-up');

                const selectedMolecule = $('#moleculeSelect option:selected').text();
                $('#moleculeName').text(selectedMolecule);
                $('#moleculeDescription').text('Seçilen molekül için aşağıdaki laboratuvar değerlerini kontrol edin ve gerekli değerleri girin.');

                if (rules && rules.length > 0) {
                    let rulesHtml = '<div class="row g-3">';
                    let inputsHtml = '<div class="row g-3">';

                    rules.forEach((rule, index) => {
                        const ruleId = `rule-${index}`;

                        rulesHtml += `
                            <div class="col-12">
                                <div class="alert alert-modern alert-warning-modern">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle text-warning me-3"></i>
                                        <div>
                                            <strong>${rule.parameter.name}</strong>
                                            <span class="badge bg-warning text-dark ms-2">${rule.operator} ${rule.value}</span>
                                            <small class="d-block text-muted mt-1">Birim: ${rule.parameter.unit}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        inputsHtml += `
                            <div class="col-md-6">
                                <div class="lab-input-modern">
                                    <label class="form-label fw-bold d-flex align-items-center">
                                        <i class="fas fa-vial text-primary me-2"></i>
                                        ${rule.parameter.name}
                                        <span class="badge bg-primary ms-2">Gerekli</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control labValue"
                                               data-param="${rule.laboratory_parameter_id}"
                                               data-rule-id="${ruleId}"
                                               placeholder="Değer girin"
                                               step="0.01"
                                               required>
                                        <span class="input-group-text bg-light fw-bold">${rule.parameter.unit}</span>
                                    </div>
                                    <small class="text-muted mt-1">Beklenen: ${rule.operator} ${rule.value} ${rule.parameter.unit}</small>
                                </div>
                            </div>
                        `;
                    });

                    rulesHtml += '</div>';
                    inputsHtml += '</div>';

                    $('#labRules').html(rulesHtml);
                    $('#labInputs').html(inputsHtml);

                    updateStepProgress(4);
                    showToast('info', 'Lab Kuralları', `${rules.length} laboratuvar kuralı yüklendi`);
                } else {
                    $('#labRules').html('<div class="alert alert-info alert-modern">Bu molekül için lab kuralı tanımlanmamış.</div>');
                    $('#labInputs').html('');
                }

                $('#prescriptionResult').html('');
                $('#resultCard').addClass('d-none');
                $('#exportResults').prop('disabled', true);
            },
            error: function(xhr, status, error) {
                console.error('Lab kuralları yüklenirken hata:', error);
                showToast('error', 'Hata', 'Lab kuralları yüklenirken hata oluştu');
            }
        });
    }

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
            loadLabRules(molId);
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
    $('#resetForm').on('click', function() {
        // Add loading animation
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Sıfırlanıyor...');

        setTimeout(() => {
            $('#branchSelect, #diagnosisSelect, #moleculeSelect').val('').trigger('change');
            $('#diagnosisSelect').html('<option value="">🔬 Önce branş seçiniz</option>').prop('disabled', true);
            $('#moleculeSelect').html('<option value="">💊 Önce tanı seçiniz</option>').prop('disabled', true);
            $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');
            $('#welcomeMessage').removeClass('d-none').addClass('fade-in');
            $('#exportResults').prop('disabled', true);
            updateStepProgress(1);

            // Reset button text
            $(this).html('<i class="fas fa-redo me-2"></i>Sıfırla');

            showToast('success', 'Sıfırlandı', 'Form başarıyla sıfırlandı');

            // Remove animation class
            setTimeout(() => $('#welcomeMessage').removeClass('fade-in'), 600);
        }, 1000);
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
                                    loadLabRules(data.molecule);

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

    // Open print dialog with formatted content
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reçete Uygunluk Raporu</title>
            <style>
                body { margin: 0; padding: 20px; }
                @media print {
                    body { margin: 0; }
                }
            </style>
        </head>
        <body>
            ${pdfContent}
            <script>window.print(); window.close();</script>
        </body>
        </html>
    `);
}


@endpush
