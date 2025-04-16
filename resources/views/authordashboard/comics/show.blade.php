@extends('authordashboard.layouts.base')
@section('title', 'Détail de la bande dessinée')
@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .novel-cover {
        max-width: 300px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }
    .info-label {
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }
    .badge-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .stats-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .status-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    .description-box {
        background: #fff;
        border: 1px solid #eee;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('authordashboard.comics') }}">Mes Bandes Déssinées</a></li>
                    <li class="breadcrumb-item active">{{ $comic->title }}</li>
                </ol>
            </nav>
            <div>
                <a href="{{ route('authordashboard.comics.read', $comic) }}" class="btn btn-success me-2">
                    <i class="bi bi-book"></i> Lire
                </a>
                <a href="{{ route('authordashboard.comics.chapters.create') }}" class="btn btn-primary me-2">
                    <i class="bi bi-plus"></i> Ajouter un chapitre
                </a>
                <a href="{{ route('authordashboard.comics.edit', $comic) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-pencil"></i> Modifier la bande dessinée
                </a>
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download"></i> Télécharger
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="http://localhost:3000/download/comic/{{ $comic->id }}?format=pdf"
                               target="_blank">
                                PDF
                            </a>
                        </li>
                    </ul>
                </div>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Supprimer
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <img src="{{ url($comic->cover_image) }}" 
                     alt="Couverture de {{ $comic->title }}" 
                     class="img-fluid novel-cover rounded mb-4">
                
                <div class="stats-card mb-4">
                    <h5 class="mb-3">Statistiques</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-eye"></i> Vues</span>
                        <span class="fw-bold">{{ number_format($comic->view_count ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-heart"></i> Likes</span>
                        <span class="fw-bold">{{ number_format($comic->like_count ?? 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-chat"></i> Commentaires</span>
                        <span class="fw-bold">{{ $comic->comments->count() }}</span>
                    </div>
                </div>

                <div class="stats-card">
                    <h5 class="mb-3">Publication</h5>
                    <div class="mb-2">
                        <small class="text-muted">Publié le</small><br>
                        <span>{{ $comic->published_at ? $comic->published_at->format('d/m/Y H:i') : 'Non publié' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Dernière mise à jour</small><br>
                        <span>{{ $comic->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div>
                        <small class="text-muted">Créé le</small><br>
                        <span>{{ $comic->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="mb-0">{{ $comic->title }}</h1>
                    @php
                        $statusMap = [
                            'en_cours' => ['text' => 'En cours', 'class' => 'primary'],
                            'termine' => ['text' => 'Terminé', 'class' => 'success'],
                            'en_pause' => ['text' => 'En pause', 'class' => 'warning'],
                            'abandonne' => ['text' => 'Abandonné', 'class' => 'danger']
                        ];
                        $status = $statusMap[$comic->status] ?? ['text' => $comic->status, 'class' => 'secondary'];
                    @endphp
                    <span class="status-badge badge bg-{{ $status['class'] }}">
                        {{ $status['text'] }}
                    </span>
                </div>

                <div class="description-box mb-4">
                    <p class="info-label mb-2">Description</p>
                    <p class="mb-0">{{ $comic->description }}</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="info-label mb-2">Genres</p>
                        <div class="badge-container">
                            @forelse($comic->genres as $genre)
                                <span class="badge bg-primary">{{ $genre->name }}</span>
                            @empty
                                <span class="text-muted">Aucun genre assigné</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p class="info-label mb-2">Tags</p>
                        <div class="badge-container">
                            @forelse($comic->tags as $tag)
                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                            @empty
                                <span class="text-muted">Aucun tag assigné</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if($comic->chapters->count() > 0)
                    <div class="mb-4">
                        <p class="info-label mb-2">
                            Chapitres ({{ $comic->chapters->count() }})
                        </p>
                        <div class="list-group">
                            @foreach($comic->chapters->sortBy('chapter_number') as $chapter)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="text-muted me-2">Ch.{{ $chapter->chapter_number }}</span>
                                        {{ $chapter->title }}
                                    </span>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('authordashboard.comics.chapters.edit', $chapter) }}" 
                                           class="btn btn-sm btn-link text-decoration-none me-3" 
                                           title="Modifier le chapitre">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <small class="text-muted">{{ $chapter->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <p class="info-label mb-2">Chapitres</p>
                        <div class="text-center py-4">
                            <p>Aucun chapitre pour le moment</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cette bande dessinée ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('authordashboard.comics.destroy', $comic) }}" method="POST" class="d-inline">
                        @csrf
                        @method('put')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection