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
        <button type="button" class="btn btn-outline-primary" id="resetForm">
            <i class="fas fa-redo me-1"></i>
            Sıfırla
        </button>
        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#helpModal">
            <i class="fas fa-question-circle me-1"></i>
            Yardım
        </button>
    </div>
@endsection

@push('styles')
    <style>
        .selection-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: 2px solid #e9ecef;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .selection-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #007bff;
        }

        .selection-step {
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .selection-step.active {
            opacity: 1;
        }

        .selection-step .step-number {
            width: 35px;
            height: 35px;
            background: linear-gradient(45deg, #007bff, #0056b3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .lab-input-group {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }

        .result-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            z-index: 10;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>

    <!-- Modal için CSS düzeltmeleri -->
    <style>
    /* Modal backdrop problemi için CSS fix */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
    }

    .modal.show {
        display: block !important;
    }

    .modal-dialog {
        margin: 1.75rem auto;
    }

    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 3.5rem);
    }

    /* Body modal açık durumu için fix */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    /* Modal animasyonları */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
        transform: translate(0, -50px);
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* Help section styling */
    .help-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        height: 100%;
    }

    .help-section h6 {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
    }

    .list-group-item {
        background: transparent !important;
    }

    .badge {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Modal content styling */
    .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 20px 30px 15px 30px;
    }

    .modal-body {
        padding: 20px 30px;
    }

    .modal-footer {
        padding: 15px 30px 20px 30px;
        border-top: 1px solid #e9ecef;
    }

    /* Responsive modal */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 1rem;
            max-width: calc(100% - 2rem);
        }

        .modal-dialog-centered {
            min-height: calc(100% - 2rem);
        }
    }

    /* Backdrop fade effect */
    .modal-backdrop.show {
        opacity: 0.5;
    }

    /* Prevent body scroll when modal is open */
    html.modal-open,
    body.modal-open {
        overflow: hidden !important;
        height: 100% !important;
    }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <!-- Sol Panel - Seçim Alanları -->
        <div class="col-lg-4">
            <div class="selection-card p-4 h-100">
                <div class="text-center mb-4">
                    <i class="fas fa-clipboard-list fa-3x text-primary mb-2"></i>
                    <h5 class="card-title mb-0">Seçim Paneli</h5>
                </div>

                <!-- Adım 1: Branş Seçimi -->
                <div class="selection-step active" id="step1">
                    <div class="d-flex align-items-center mb-3">
                        <div class="step-number">1</div>
                        <div class="ms-3">
                            <h6 class="mb-1">Branş Seçimi</h6>
                            <small class="text-muted">Önce branş seçiniz</small>
                        </div>
                    </div>

                    <div class="position-relative">
                        <select id="branchSelect" class="form-select form-select-lg mb-4">
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
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>

                <!-- Adım 2: Tanı Seçimi -->
                <div class="selection-step" id="step2">
                    <div class="d-flex align-items-center mb-3">
                        <div class="step-number">2</div>
                        <div class="ms-3">
                            <h6 class="mb-1">Tanı Kodu Seçimi</h6>
                            <small class="text-muted">Uygun tanı kodunu seçin</small>
                        </div>
                    </div>

                    <div class="position-relative">
                        <select id="diagnosisSelect" class="form-select form-select-lg mb-4" disabled>
                            <option value="">🔬 Önce branş seçiniz</option>
                        </select>
                        <div class="loading-overlay d-none" id="diagnosisLoading">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>

                <!-- Adım 3: Molekül Seçimi -->
                <div class="selection-step" id="step3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="step-number">3</div>
                        <div class="ms-3">
                            <h6 class="mb-1">Molekül Seçimi</h6>
                            <small class="text-muted">İlaç molekülünü seçin</small>
                        </div>
                    </div>

                    <div class="position-relative">
                        <select id="moleculeSelect" class="form-select form-select-lg mb-4" disabled>
                            <option value="">💊 Önce tanı seçiniz</option>
                        </select>
                        <div class="loading-overlay d-none" id="moleculeLoading">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Panel - Bilgi ve Sonuçlar -->
        <div class="col-lg-8">
            <!-- Molekül Bilgisi -->
            <div class="card result-card mb-4 d-none" id="moleculeInfoCard">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Molekül Bilgileri
                    </h5>
                </div>
                <div class="card-body">
                    <div id="moleculeInfo">
                        <h4 id="moleculeName" class="text-primary mb-3"></h4>
                        <p id="moleculeDescription" class="text-muted"></p>
                    </div>
                </div>
            </div>

            <!-- Lab Kuralları -->
            <div class="card result-card mb-4 d-none" id="labRulesCard">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-vial me-2"></i>
                        Laboratuvar Kuralları
                    </h5>
                </div>
                <div class="card-body">
                    <div id="labRules" class="mb-3"></div>
                    <div id="labInputs"></div>
                </div>
            </div>

            <!-- Sonuç -->
            <div class="card result-card d-none" id="resultCard">
                <div class="card-header p-3">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Uygunluk Sonucu
                    </h5>
                </div>
                <div class="card-body">
                    <div id="prescriptionResult"></div>
                </div>
            </div>

            <!-- Başlangıç Mesajı -->
            <div class="text-center py-5" id="welcomeMessage">
                <i class="fas fa-prescription-bottle-alt fa-4x text-primary mb-3 pulse-animation"></i>
                <h4 class="text-primary">Reçete Uygunluk Sistemi</h4>
                <p class="text-muted lead">
                    Sol panelden branş seçerek başlayın. Sistem size adım adım rehberlik edecektir.
                </p>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-hospital fa-2x text-info mb-2"></i>
                            <h6>Branş Seç</h6>
                            <small class="text-muted">Tıbbi branş seçimi</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-diagnoses fa-2x text-warning mb-2"></i>
                            <h6>Tanı Belirle</h6>
                            <small class="text-muted">ICD-10 tanı kodu</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-pills fa-2x text-success mb-2"></i>
                            <h6>Molekül Seç</h6>
                            <small class="text-muted">İlaç molekülü</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Yardım Modal -->
<!-- Yardım Modal - Tamamen yeniden yazıldı -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="helpModalLabel">
                    <i class="fas fa-question-circle me-2"></i>
                    Reçete Uygunluk Sistemi Yardımı
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="help-section">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-list-ol me-2"></i>Kullanım Adımları
                            </h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item border-0 px-0">
                                    <span class="badge bg-primary me-2">1</span>
                                    Branş seçimi yapın
                                </div>
                                <div class="list-group-item border-0 px-0">
                                    <span class="badge bg-primary me-2">2</span>
                                    İlgili tanı kodunu seçin
                                </div>
                                <div class="list-group-item border-0 px-0">
                                    <span class="badge bg-primary me-2">3</span>
                                    Uygun molekülü seçin
                                </div>
                                <div class="list-group-item border-0 px-0">
                                    <span class="badge bg-primary me-2">4</span>
                                    Lab değerlerini girin
                                </div>
                                <div class="list-group-item border-0 px-0">
                                    <span class="badge bg-primary me-2">5</span>
                                    Sonucu görüntüleyin
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="help-section">
                            <h6 class="text-info mb-3">
                                <i class="fas fa-lightbulb me-2"></i>İpuçları
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Her seçim bir sonrakini etkiler
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Lab değerleri otomatik kontrol edilir
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Sonuçlar gerçek zamanlıdır
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Sıfırla butonu ile baştan başlayın
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Kapat
                </button>
                <button type="button" class="btn btn-primary" onclick="startTour()">
                    <i class="fas fa-play me-1"></i>Rehberli Tur
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Modal için özel JavaScript -->
<script>
$(document).ready(function() {
    // Modal fix fonksiyonları
    function initializeModalFixes() {
        // Tüm modalleri temizle
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({
            'padding-right': '',
            'overflow': ''
        });
    }

    // Sayfa yüklendiğinde modal durumunu temizle
    initializeModalFixes();

    // Help Modal için özel event handler'lar
    const helpModal = document.getElementById('helpModal');
    let modalInstance = null;

    if (helpModal) {
        // Bootstrap modal instance oluştur
        modalInstance = new bootstrap.Modal(helpModal, {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        // Modal açılmadan önce
        helpModal.addEventListener('show.bs.modal', function (e) {
            console.log('Modal açılıyor...');
            // Önceki backdrop'ları temizle
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });

        // Modal açıldıktan sonra
        helpModal.addEventListener('shown.bs.modal', function (e) {
            console.log('Modal açıldı');
            // Focus'u modal içine al
            $(this).find('button[data-bs-dismiss="modal"]').first().focus();
        });

        // Modal kapanmadan önce
        helpModal.addEventListener('hide.bs.modal', function (e) {
            console.log('Modal kapanıyor...');
        });

        // Modal kapandıktan sonra
        helpModal.addEventListener('hidden.bs.modal', function (e) {
            console.log('Modal kapandı');
            // Backdrop'u manuel olarak kaldır
            setTimeout(function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({
                    'padding-right': '',
                    'overflow': ''
                });
            }, 100);
        });

        // ESC tuşu ile kapatma
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape' && helpModal.classList.contains('show')) {
                modalInstance.hide();
            }
        });

        // Backdrop'a tıklama ile kapatma
        helpModal.addEventListener('click', function(e) {
            if (e.target === this) {
                modalInstance.hide();
            }
        });
    }

    // Yardım butonu click event'i
    $('[data-bs-target="#helpModal"], [data-target="#helpModal"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('Yardım butonu tıklandı');

        // Önceki modal durumlarını temizle
        initializeModalFixes();

        // Modal'ı aç
        if (modalInstance) {
            modalInstance.show();
        } else {
            $('#helpModal').modal('show');
        }
    });

    // Kapatma butonları için ek handler
    $('[data-bs-dismiss="modal"]').on('click', function(e) {
        const targetModal = $(this).closest('.modal');
        if (targetModal.length && targetModal.attr('id') === 'helpModal') {
            e.preventDefault();
            if (modalInstance) {
                modalInstance.hide();
            } else {
                targetModal.modal('hide');
            }
        }
    });

    // Rehberli tur fonksiyonu
    window.startTour = function() {
        // Modal'ı kapat
        if (modalInstance) {
            modalInstance.hide();
        }

        // Tur başlat
        setTimeout(function() {
            showToast('info', 'Rehberli tur başlıyor...', 'Bilgi');
            // Burada rehberli tur kodları olabilir
            $('#branchSelect').focus().addClass('pulse-animation');

            setTimeout(function() {
                $('#branchSelect').removeClass('pulse-animation');
            }, 3000);
        }, 300);
    };

    // Global modal temizleme fonksiyonu
    window.cleanupModals = function() {
        initializeModalFixes();
    };

    // Sayfa değiştiğinde modal'ları temizle
    $(window).on('beforeunload', function() {
        initializeModalFixes();
    });
});
</script>
<script>
$(function() {
    console.log('Prescription Index sayfası yüklendi');

    const csrfToken = '{{ csrf_token() }}';
    let currentStep = 1;

    // Debug için mevcut veriyi kontrol et
    console.log('Branş sayısı:', $('#branchSelect option').length - 1);

    // Sayfa yüklendiğinde veri kontrolü
    if ($('#branchSelect option').length <= 1) {
        showToast('warning', 'Branş verileri yüklenemedi. Lütfen sayfayı yenileyin.', 'Uyarı');
    }

    // Adım göstergelerini güncelle
    function updateSteps(step) {
        $('.selection-step').removeClass('active').addClass('opacity-50');
        for (let i = 1; i <= step; i++) {
            $(`#step${i}`).addClass('active').removeClass('opacity-50');
        }
        currentStep = step;
    }

    // Loading göstergeleri
    function showLoading(elementId) {
        $(`#${elementId}`).removeClass('d-none');
    }

    function hideLoading(elementId) {
        $(`#${elementId}`).addClass('d-none');
    }

    // Branşa bağlı tanı kodlarını yükle
    function loadDiagnosis(branchId) {
        console.log('Tanı kodları yükleniyor, branş ID:', branchId);

        showLoading('diagnosisLoading');
        $('#diagnosisSelect').prop('disabled', true).html('<option>Yükleniyor...</option>');

        $.ajax({
            url: `{{ url('ajax/prescription/diagnosis') }}/${branchId}`,
            method: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                console.log('Tanı kodları yüklendi:', data);
                let options = '<option value="">🔬 Tanı Kodu Seçiniz</option>';
                if (data && data.length > 0) {
                    data.forEach(d => {
                        options += `<option value="${d.id}">${d.code} - ${d.description}</option>`;
                    });
                } else {
                    options += '<option value="">Tanı kodu bulunamadı</option>';
                }
                $('#diagnosisSelect').html(options).prop('disabled', false);
                updateSteps(2);
            },
            error: function(xhr, status, error) {
                console.error('Tanı kodları yüklenirken hata:', error);
                $('#diagnosisSelect').html('<option value="">Hata: Tekrar deneyin</option>').prop('disabled', false);
                showToast('error', 'Tanı kodları yüklenirken hata oluştu', 'Hata');
            },
            complete: function() {
                hideLoading('diagnosisLoading');
            }
        });
    }

    // Tanıya bağlı molekülleri yükle
    function loadMolecules(diagnosisId) {
        console.log('Moleküller yükleniyor, tanı ID:', diagnosisId);

        showLoading('moleculeLoading');
        $('#moleculeSelect').prop('disabled', true).html('<option>Yükleniyor...</option>');

        $.ajax({
            url: `{{ url('ajax/prescription/molecules') }}/${diagnosisId}`,
            method: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                console.log('Moleküller yüklendi:', data);
                let options = '<option value="">💊 Molekül Seçiniz</option>';
                if (data && data.length > 0) {
                    data.forEach(d => {
                        options += `<option value="${d.id}">${d.name}</option>`;
                    });
                } else {
                    options += '<option value="">Molekül bulunamadı</option>';
                }
                $('#moleculeSelect').html(options).prop('disabled', false);
                updateSteps(3);
            },
            error: function(xhr, status, error) {
                console.error('Moleküller yüklenirken hata:', error);
                $('#moleculeSelect').html('<option value="">Hata: Tekrar deneyin</option>').prop('disabled', false);
                showToast('error', 'Moleküller yüklenirken hata oluştu', 'Hata');
            },
            complete: function() {
                hideLoading('moleculeLoading');
            }
        });
    }

    // Molekül seçildiğinde lab kurallarını yükle
    function loadLabRules(moleculeId) {
        console.log('Lab kuralları yükleniyor, molekül ID:', moleculeId);

        $.ajax({
            url: `{{ url('ajax/prescription/labrules') }}/${moleculeId}`,
            method: 'GET',
            dataType: 'json',
            success: function(rules) {
                console.log('Lab kuralları yüklendi:', rules);

                $('#welcomeMessage').addClass('d-none');
                $('#moleculeInfoCard, #labRulesCard').removeClass('d-none');

                let htmlRules = '<div class="row">';
                let htmlInputs = '<div class="row">';

                if (rules && rules.length > 0) {
                    rules.forEach((rule, index) => {
                        htmlRules += `
                            <div class="col-md-6 mb-2">
                                <div class="alert alert-ligh mb-2" style="border:1px solid #dcdcdc;">
                                    <strong>${rule.parameter.name}</strong> ${rule.operator} ${rule.value} ${rule.parameter.unit}
                                </div>
                            </div>
                        `;

                        htmlInputs += `
                            <div class="col-md-6 mb-3">
                                <div class="lab-input-group">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-vial me-1"></i>
                                        ${rule.parameter.name}
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control form-control-lg labValue"
                                               data-param="${rule.laboratory_parameter_id}"
                                               placeholder="Değer girin"
                                               step="0.01">
                                        <span class="input-group-text bg-light">${rule.parameter.unit}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    htmlRules = '<div class="alert alert-warning">Bu molekül için lab kuralı tanımlanmamış.</div>';
                }

                htmlRules += '</div>';
                htmlInputs += '</div>';

                $('#labRules').html(htmlRules);
                $('#labInputs').html(htmlInputs);
                $('#prescriptionResult').html('');
                $('#resultCard').addClass('d-none');
            },
            error: function(xhr, status, error) {
                console.error('Lab kuralları yüklenirken hata:', error);
                showToast('error', 'Lab kuralları yüklenirken hata oluştu', 'Hata');
            }
        });
    }

    // Reçete uygunluğunu kontrol et
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

                $('#resultCard').removeClass('d-none');

                const alertClass = res.eligible ? 'alert-success' : 'alert-danger';
                const icon = res.eligible ? 'fa-check-circle' : 'fa-times-circle';
                const title = res.eligible ? 'Uygun' : 'Uygun Değil';

                let resultHtml = `
                    <div class="alert ${alertClass} border-0 shadow-sm">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas ${icon} fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-0">${title}</h5>
                                <small>Reçete Uygunluk Durumu</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            ${res.messages.join('<br>')}
                        </div>
                    </div>
                `;

                $('#prescriptionResult').html(resultHtml);

                // Scroll to result
                $('html, body').animate({
                    scrollTop: $('#resultCard').offset().top - 100
                }, 500);
            },
            error: function(xhr, status, error) {
                console.error('Uygunluk kontrol edilirken hata:', error);
                showToast('error', 'Uygunluk kontrol edilirken hata oluştu', 'Hata');
            }
        });
    }

    // Event listeners
    $('#branchSelect').on('change', function() {
        const branchId = $(this).val();
        console.log('Branş seçildi:', branchId);

        // Reset dependent fields
        $('#diagnosisSelect').html('<option value="">🔬 Tanı Kodu Seçiniz</option>').prop('disabled', true);
        $('#moleculeSelect').html('<option value="">💊 Molekül Seçiniz</option>').prop('disabled', true);
        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');
        $('#welcomeMessage').removeClass('d-none');

        if (branchId) {
            loadDiagnosis(branchId);
        } else {
            updateSteps(1);
        }
    });

    $('#diagnosisSelect').on('change', function() {
        const diagId = $(this).val();
        console.log('Tanı seçildi:', diagId);

        // Reset dependent fields
        $('#moleculeSelect').html('<option value="">💊 Molekül Seçiniz</option>').prop('disabled', true);
        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');

        if (diagId) {
            loadMolecules(diagId);
        }
    });

    $('#moleculeSelect').on('change', function() {
        const molId = $(this).val();
        console.log('Molekül seçildi:', molId);

        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');

        if (molId) {
            const selectedText = $(this).find('option:selected').text();
            $('#moleculeName').text(selectedText);
            $('#moleculeDescription').text('Seçilen molekül için laboratuvar değerlerini girin.');
            loadLabRules(molId);
        } else {
            $('#welcomeMessage').removeClass('d-none');
        }
    });

    // Lab değer girişi
    $(document).on('input', '.labValue', function() {
        const molId = $('#moleculeSelect').val();
        if (molId) {
            // Debounce ile kontrol et
            clearTimeout(window.checkTimeout);
            window.checkTimeout = setTimeout(() => {
                checkEligibility(molId);
            }, 500);
        }
    });

    // Sıfırla butonu
    $('#resetForm').on('click', function() {
        $('#branchSelect, #diagnosisSelect, #moleculeSelect').val('').trigger('change');
        $('#diagnosisSelect').html('<option value="">🔬 Önce branş seçiniz</option>').prop('disabled', true);
        $('#moleculeSelect').html('<option value="">💊 Önce tanı seçiniz</option>').prop('disabled', true);
        $('#moleculeInfoCard, #labRulesCard, #resultCard').addClass('d-none');
        $('#welcomeMessage').removeClass('d-none');
        updateSteps(1);
        showToast('info', 'Form sıfırlandı', 'Bilgi');
    });

    // Sayfa yüklendiğinde başlangıç durumu
    updateSteps(1);
});
</script>
@endpush
