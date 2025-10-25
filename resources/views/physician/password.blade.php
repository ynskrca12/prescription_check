@extends('layouts.master')

@section('title', 'Şifre Girişi')

@push('styles')
<style>
    .password-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 450px;
        width: 100%;
        padding: 3rem;
    }

    .physician-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        text-align: center;
    }

    .form-control-lg {
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
        border-radius: 12px;
    }

    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="password-card">
        <div class="physician-badge">
            <i class="fas fa-user-md fa-2x mb-2"></i>
            <h5 class="mb-1">{{ $physician->full_name }}</h5>
            <small>{{ $physician->branch->name }}</small>
            <div class="mt-2">
                <span class="badge bg-white text-primary">{{ $physician->physician_code }}</span>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('physician.login.post') }}">
            @csrf
            <input type="hidden" name="physician_code" value="{{ $physician->physician_code }}">

            <div class="mb-4">
                <label for="password" class="form-label fw-bold">
                    <i class="fas fa-lock me-2"></i>Şifre
                </label>
                <input type="password"
                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                    id="password"
                    name="password"
                    placeholder="Şifrenizi giriniz"
                    required
                    autofocus>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        Beni Hatırla
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>
                Giriş Yap
            </button>

            <div class="text-center">
                <a href="{{ route('physician.qr-codes') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>
                    Geri Dön
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
