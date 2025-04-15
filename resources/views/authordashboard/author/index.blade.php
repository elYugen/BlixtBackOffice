@extends('authordashboard.layouts.base')

@section('title', 'Mon Profil Auteur')

@section('styles')
<style>
    .profile-header {
        background: linear-gradient(90deg, #0a549a 60%, #3b82f6 100%);
        color: #fff;
        border-radius: 18px;
        padding: 2.5rem 2rem 2rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 16px rgba(10,84,154,0.08);
        display: flex;
        align-items: center;
        gap: 2rem;
    }
    .profile-header .profile-avatar {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        background: #fff;
    }
    .profile-header .profile-info {
        flex: 1;
    }
    .profile-header .profile-name {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    .profile-header .profile-meta {
        font-size: 1.1rem;
        color: #e0e7ef;
        margin-bottom: 0.7rem;
    }
    .profile-header .profile-bio {
        font-size: 1.1rem;
        color: #e0e7ef;
        margin-bottom: 0.2rem;
    }
    .profile-header .profile-stats span {
        margin-right: 1.2rem;
        font-size: 1rem;
        color: #fff;
        background: rgba(255,255,255,0.13);
        border-radius: 20px;
        padding: 0.3rem 1rem;
        display: inline-block;
    }
    .dashboard-section {
        margin-bottom: 2.5rem;
    }
    .dashboard-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 2rem 2rem 1.5rem 2rem;
        margin-bottom: 2rem;
        min-height: 320px;
    }
    .dashboard-card h5 {
        font-weight: 700;
        color: #0a549a;
        margin-bottom: 1.2rem;
    }
    .option-switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.2rem;
    }
    .option-switch label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0;
    }
    .option-switch .form-check-input {
        width: 2.2em;
        height: 1.2em;
    }
    .dashboard-card .form-label {
        font-weight: 500;
        color: #0a549a;
    }
    .dashboard-card .btn {
        margin-top: 1.2rem;
    }
    @media (max-width: 991px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
            gap: 1.2rem;
        }
        .profile-header .profile-info {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
@include('authordashboard.layouts.navbar')
<div class="container py-4">
    <div class="profile-header mb-5">
        <img src="{{ $author->avatar ? asset('storage/' . $author->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($author->pen_name) }}" 
             alt="Avatar" class="profile-avatar">
        <div class="profile-info">
            <div class="profile-name">{{ $author->pen_name }}</div>
            <div class="profile-meta">
                <i class="bi bi-person"></i> {{ Auth::user()->name }}<br>
                <i class="bi bi-calendar"></i> Inscrit le {{ $author->created_at ? $author->created_at->format('d/m/Y') : '-' }}
            </div>
            <div class="profile-bio">
                {{ $author->bio ? $author->bio : 'Aucune biographie renseignée.' }}
            </div>
            <div class="profile-stats mt-3">
                <span><i class="bi bi-book"></i> Romans : {{ $author->novels->count() }}</span>
                <span><i class="bi bi-grid"></i> Comics : {{ $author->comics->count() }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Options Panel -->
        <div class="col-lg-4 dashboard-section">
            <div class="dashboard-card">
                <h5><i class="bi bi-gear"></i> Options</h5>
                <div class="option-switch">
                    <label for="darkModeSwitch"><i class="bi bi-moon"></i> Activer le mode sombre</label>
                    <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                </div>
                <div class="option-switch">
                    <label for="notifSwitch"><i class="bi bi-bell"></i> Notifications email</label>
                    <input class="form-check-input" type="checkbox" id="notifSwitch" disabled>
                </div>
                <div class="option-switch">
                    <label for="privacySwitch"><i class="bi bi-shield-lock"></i> Profil privé</label>
                    <input class="form-check-input" type="checkbox" id="privacySwitch" disabled>
                </div>
                <div class="option-switch">
                    <label for="betaSwitch"><i class="bi bi-flask"></i> Activer les fonctionnalités bêta</label>
                    <input class="form-check-input" type="checkbox" id="betaSwitch" disabled>
                </div>
                <small class="text-muted d-block mt-3">Plus d'options à venir...</small>
            </div>
        </div>
        <!-- Profile Edit Form -->
        <div class="col-lg-8 dashboard-section">
            <div class="dashboard-card">
                <h5><i class="bi bi-pencil-square"></i> Modifier mon profil</h5>
                <form action="{{ route('authordashboard.user.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="pen_name" class="form-label">Nom d'auteur</label>
                        <input type="text" class="form-control @error('pen_name') is-invalid @enderror" id="pen_name" name="pen_name" value="{{ old('pen_name', $author->pen_name) }}" required>
                        @error('pen_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Biographie</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', $author->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="avatar" class="form-label">Photo de profil</label>
                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Initialize the dark mode switch based on localStorage
    document.addEventListener('DOMContentLoaded', function () {
        const switchEl = document.getElementById('darkModeSwitch');
        if (!switchEl) return;
        // Set initial state
        switchEl.checked = localStorage.getItem('blixt_dark_mode') === '1';

        // Listen for changes
        switchEl.addEventListener('change', function () {
            if (this.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('blixt_dark_mode', '1');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('blixt_dark_mode', '0');
            }
        });
    });
</script>
@endsection