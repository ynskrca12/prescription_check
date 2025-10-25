@extends('layouts.master')

@section('title', 'Hekim QR Kodları')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .qr-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        padding: 2rem;
    }

    .qr-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
    }

    .qr-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border-color: #667eea;
    }

    .qr-code {
        margin: 1rem 0;
    }

    .physician-info {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="qr-container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">
                <i class="fas fa-qrcode me-2 text-primary"></i>
                Hekim QR Kodları
            </h2>
            <p class="text-muted">QR kodu okutarak giriş yapabilirsiniz</p>
        </div>

        <div class="row g-4">
            @foreach($physicians as $physician)
                <div class="col-md-4 col-lg-3">
                    <div class="qr-card">
                        <div class="qr-code text-center">
                            {{-- Endroid QR Code --}}
                            <img src="{{ $physician->qr }}"
                                alt="QR Code for {{ $physician->physician_code }}"
                                class="img-fluid">
                        </div>

                        <div class="physician-info text-center mt-2">
                            <h6 class="fw-bold mb-1">{{ $physician->full_name }}</h6>
                            <p class="text-muted small mb-1">{{ $physician->branch->name }}</p>
                            <span class="badge bg-primary">{{ $physician->physician_code }}</span>
                        </div>

                        <a href="{{ route('physician.password', $physician->physician_code) }}"
                        class="btn btn-sm btn-outline-primary mt-3 w-100">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Manuel Giriş
                        </a>
                    </div>
                </div>
            @endforeach

        </div>

        <div class="text-center mt-4">
            <a href="{{ route('physician.login') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Geri Dön
            </a>
        </div>
    </div>
</div>
@endsection
