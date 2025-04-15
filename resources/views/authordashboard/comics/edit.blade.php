@extends('authordashboard.layouts.base')
@section('title', 'Modifier la bande dessinée')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .form-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 2rem;
    }
    .preview-image {
        max-width: 300px;
        max-height: 400px;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .unsplash-container {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .unsplash-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-top: 1rem;
        max-height: 400px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    .unsplash-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .unsplash-image:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .unsplash-image.selected {
        border: 2px solid #0d6efd;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
    }

    .image-preview-container {
        text-align: center;
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin-top: 1rem;
    }

    .preview-image {
        max-width: 100%;
        height: auto;
        margin-bottom: 1rem;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .change-image-btn {
        width: 100%;
        margin-top: 1rem;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        padding: 0 0.375rem;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #212529;
        padding: 0.25rem 0.5rem;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: #212529;
        margin-right: 0.25rem;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #dc3545;
        background-color: transparent;
    }
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('authordashboard.comics') }}" class="text-decoration-none">Mes Bandes Déssinées</a></li>
                <li class="breadcrumb-item"><a href="{{ route('authordashboard.comics.show', $comic) }}" class="text-decoration-none">{{ $comic->title }}</a></li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-container">
            <h1 class="mb-4">Modifier {{ $comic->title }}</h1>

            <form action="{{ route('authordashboard.comics.update', $comic) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $comic->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="6" required>{{ old('description', $comic->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="en_cours" {{ $comic->status === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminé" {{ $comic->status === 'terminé' ? 'selected' : '' }}>Terminé</option>
                                <option value="en_pause" {{ $comic->status === 'en_pause' ? 'selected' : '' }}>En pause</option>
                                <option value="annulé" {{ $comic->status === 'annulé' ? 'selected' : '' }}>Abandonné</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="genres" class="form-label">Genres</label>
                            <select class="form-select select2 @error('genres') is-invalid @enderror" 
                                    id="genres" name="genres[]" multiple required>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" 
                                        {{ in_array($genre->id, old('genres', $comic->genres->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $genre->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('genres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <select class="form-select select2 @error('tags') is-invalid @enderror" 
                                    id="tags" name="tags[]" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" 
                                        {{ in_array($tag->id, old('tags', $comic->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="image-preview-container" id="imagePreviewContainer">
                                <label class="form-label">Image de couverture actuelle</label>
                                <img id="preview" src="{{ url($comic->cover_image) }}" 
                                     alt="Couverture actuelle" class="preview-image">
                                <button type="button" class="btn btn-outline-primary change-image-btn" id="changeImageBtn">
                                    Changer d'image
                                </button>
                            </div>

                            <div class="image-selection-container" id="imageSelectionContainer" style="display: none;">
                                <label class="form-label">Nouvelle image de couverture</label>
                                <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                       id="cover_image" name="cover_image" accept="image/*">
                                @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <div class="unsplash-container mt-3">
                                    <input type="text" class="form-control" id="searchUnsplash" 
                                           placeholder="Rechercher des images sur Unsplash...">
                                    <input type="hidden" name="unsplash_url" id="unsplashUrl">
                                    <div id="unsplashGrid" class="unsplash-grid mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('authordashboard.comics.show', $comic) }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            language: {
                noResults: function() {
                    return "Aucun résultat trouvé";
                }
            }
        });
    });
</script>
    
    <script>
        const UNSPLASH_ACCESS_KEY = '{{ config('services.unsplash.key') }}';
        let debounceTimer;

        function showImagePreview(imageUrl) {
            document.getElementById('preview').src = imageUrl;
            document.getElementById('imageSelectionContainer').style.display = 'none';
            document.getElementById('imagePreviewContainer').style.display = 'block';
        }

        function showImageSelection() {
            document.getElementById('imageSelectionContainer').style.display = 'block';
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }

        document.getElementById('cover_image').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                showImagePreview(URL.createObjectURL(this.files[0]));
                document.getElementById('unsplashUrl').value = '';
            }
        });

        function searchUnsplash(query) {
            const url = `https://api.unsplash.com/search/photos?query=${query}&per_page=12&orientation=portrait`;
            
            fetch(url, {
                headers: {
                    'Authorization': `Client-ID ${UNSPLASH_ACCESS_KEY}`
                }
            })
            .then(response => response.json())
            .then(data => {
                const grid = document.getElementById('unsplashGrid');
                grid.innerHTML = '';
                
                data.results.forEach(photo => {
                    const img = document.createElement('img');
                    img.src = photo.urls.small;
                    img.classList.add('unsplash-image');
                    
                    img.addEventListener('click', function() {
                        document.querySelectorAll('.unsplash-image').forEach(i => i.classList.remove('selected'));
                        img.classList.add('selected');
                        document.getElementById('unsplashUrl').value = photo.urls.regular;
                        document.getElementById('cover_image').value = '';
                        showImagePreview(photo.urls.regular);
                    });
                    
                    grid.appendChild(img);
                });
            });
        }

        document.getElementById('searchUnsplash').addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (e.target.value.trim()) {
                    searchUnsplash(e.target.value);
                }
            }, 500);
        });

        document.getElementById('changeImageBtn').addEventListener('click', showImageSelection);

        // Charger les images par défaut quand on affiche la sélection
        document.getElementById('changeImageBtn').addEventListener('click', function() {
            searchUnsplash('book cover');
        });
    </script>
@endsection