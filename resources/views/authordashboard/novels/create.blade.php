@extends('authordashboard.layouts.base')
@section('title', 'Créer un nouveau roman')
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
        display: none;
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
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('authordashboard.novels') }}" class="text-decoration-none">Mes romans</a></li>
                <li class="breadcrumb-item active">Créer un nouveau roman</li>
            </ol>
        </nav>

        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-container">
            <h1 class="mb-4">Créer un nouveau roman</h1>

            <form action="{{ route('authordashboard.novels.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="genres" class="form-label">Genres</label>
                            <select class="form-select select2 @error('genres') is-invalid @enderror" 
                                    id="genres" name="genres[]" multiple required>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ in_array($genre->id, old('genres', [])) ? 'selected' : '' }}>
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
                                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
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
                            <div class="image-selection-container" id="imageSelectionContainer">
                                <label class="form-label">Image de couverture</label>
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

                            <div class="image-preview-container" id="imagePreviewContainer">
                                <label class="form-label">Image sélectionnée</label>
                                <img id="preview" src="#" alt="Aperçu" class="preview-image">
                                <button type="button" class="btn btn-outline-primary change-image-btn" id="changeImageBtn">
                                    Changer d'image
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('authordashboard.novels') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le roman</button>
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
            language: {
                noResults: function() {
                    return "Aucun résultat trouvé";
                }
            }
        });
    });

    const UNSPLASH_ACCESS_KEY = '{{ config('services.unsplash.key') }}';
    let debounceTimer;

    function showImagePreview(imageUrl) {
        document.getElementById('preview').src = imageUrl;
        document.getElementById('imageSelectionContainer').style.display = 'none';
        document.getElementById('imagePreviewContainer').style.display = 'block';
    }

    function resetImageSelection() {
        document.getElementById('cover_image').value = '';
        document.getElementById('unsplashUrl').value = '';
        document.getElementById('preview').src = '#';
        document.getElementById('imageSelectionContainer').style.display = 'block';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        document.querySelectorAll('.unsplash-image').forEach(i => i.classList.remove('selected'));
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

    document.getElementById('changeImageBtn').addEventListener('click', resetImageSelection);

    // Charger les images par défaut
    searchUnsplash('book cover');
</script>
@endsection