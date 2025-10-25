@extends('layouts.master')

@section('title', 'Hekim Girişi')

@push('styles')
<style>
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 100%;
        padding: 3rem;
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2.5rem;
    }

    .qr-btn {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        padding: 1rem 2rem;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 16px;
        color: white;
        transition: all 0.3s ease;
    }

    .qr-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(79, 172, 254, 0.4);
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <h2 class="fw-bold mb-2">Hekim Girişi</h2>
                <p class="text-muted">Reçete Uygunluk Kontrol Sistemi</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="text-center">
                <a href="{{ route('physician.qr-codes') }}" class="btn qr-btn w-100">
                    <i class="fas fa-qrcode me-2"></i>
                    QR Kod ile Giriş Yap
                </a>

                <div class="mt-4 pt-4 border-top">
                    <p class="text-muted small mb-0">
                        <i class="fas fa-shield-alt me-1"></i>
                        Güvenli giriş sistemi
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
