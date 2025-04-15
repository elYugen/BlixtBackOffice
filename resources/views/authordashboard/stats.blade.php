@extends('authordashboard.layouts.base')

@section('title', 'Statistiques')

@section('styles')
<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 15px;
    min-height: 180px;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
}
.text-purple {
    color: #6f42c1;
}
.bg-purple {
    background-color: #6f42c1;
}
.rounded-circle {
    border-radius: 50% !important;
}
.card-body {
    padding: 1.5rem;
}
h3 {
    font-size: 1.75rem;
}
.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}
.icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.card {
    min-height: 120px;
}
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <h1 class="mb-4 fw-bold">
            <i class="bi bi-graph-up-arrow me-2"></i>
            Tableau de bord
        </h1>

        <!-- Main Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="d-flex align-items-center w-100">
                            <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle">
                                <i class="bi bi-book-half text-primary fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Romans publiés</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['novels_count'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="d-flex align-items-center w-100">
                            <div class="icon-wrapper bg-purple bg-opacity-25 rounded-circle">
                                <i class="bi bi-grid fs-3" style="color: #4B0082;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Comics publiés</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['comics_count'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="d-flex align-items-center w-100">
                            <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle">
                                <i class="bi bi-file-text text-success fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Chapitres publiés</h6>
                                <h3 class="mb-0 fw-bold">{{ $stats['novel_chapters_count'] + $stats['comic_chapters_count'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle">
                                <i class="bi bi-eye text-info fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Vues totales</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_views']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle">
                                <i class="bi bi-heart text-danger fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Likes totaux</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_likes']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle">
                                <i class="bi bi-chat-dots text-warning fs-4"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Commentaires totaux</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($stats['novel_comments'] + $stats['comic_comments']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Genres and Tags -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Genres les plus utilisés</h5>
                        @foreach($topGenres as $genre)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $genre->name }}</h6>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-primary">{{ $genre->count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Tags les plus utilisés</h5>
                        @foreach($topTags as $tag)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $tag->name }}</h6>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-info">{{ $tag->count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 15px;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
}
.text-purple {
    color: #6f42c1;
}
.bg-purple {
    background-color: #6f42c1;
}
.rounded-circle {
    border-radius: 50% !important;
}
.card-body {
    padding: 1.5rem;
}
h3 {
    font-size: 1.75rem;
}
.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}
</style>
@endsection