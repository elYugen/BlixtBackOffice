@extends('authordashboard.layouts.base')

@section('title', 'Connexion')

@section('content')
<div class="d-flex min-vh-100">
    <!-- Left side - Image -->
    <div class="col-lg-5 d-none d-lg-block position-relative overflow-hidden p-0">
        <div class="position-absolute top-0 start-0 w-100 h-100 login-image"></div>
    </div>

    <!-- Right side - Login Form -->
    <div class="col-12 col-lg-7 p-5 d-flex align-items-center bg-light">
        <div class="w-100 px-lg-5">
            <div class="card shadow-sm border-0 p-4">
                <div class="card-body">
                    <div class="mb-5 text-center">
                        <img src="{{ asset('images/Blixt-Edition.svg') }}" alt="Blixt Logo" class="img-fluid mb-4 blixt-logo" style="max-height: 90px;">
                        <h3 class="fw-bold">Bienvenue sur l'Espace Auteur</h3>
                        <p class="text-muted">Connectez-vous pour accéder à votre tableau de bord</p>
                    </div>

                    <form action="{{ route('dashboard.authenticate') }}" method="POST">
                        @csrf
                
                        <div class="mb-4">
                            <label for="email" class="form-label">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control bg-light @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email') }}" 
                                    placeholder="exemple@email.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control bg-light @error('password') is-invalid @enderror" 
                                    id="password" name="password" placeholder="Votre mot de passe" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
body {
    background-color: #ffffff;
}
.login-image {
    background-image: url('{{ asset('images/login-bg.avif') }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
.blixt-logo {
    filter: brightness(0);
}

.card {
    background-color: #ffffff;
    border-radius: 15px;
}
.input-group-text {
    border-right: none;
    background-color: #f8f9fa;
}
.form-control {
    border-left: none;
    background-color: #f8f9fa !important;
}
.form-control:focus {
    border-color: #dee2e6;
    box-shadow: none;
}
.input-group .form-control:focus ~ .input-group-text {
    border-color: #dee2e6;
}
.btn-primary {
    background-color: #0d6efd;
    border: none;
    font-weight: 500;
}
.btn-primary:hover {
    background-color: #0b5ed7;
}
</style>
@endsection
@endsection