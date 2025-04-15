@extends('authordashboard.layouts.base')
@section('title', 'Créer un nouveau chapitre')

@section('styles')
<style>
    .form-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 2rem;
    }
    .page-preview {
        position: relative;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
    }
    .page-preview img {
        max-width: 30%;
        height: auto;
        border-radius: 4px;
    }
    .page-number {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #0a549a;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: bold;
    }
    .remove-page {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .caption-input {
        margin-top: 10px;
        width: 100%;
    }
    #page-container {
        margin-top: 20px;
    }
    .drag-handle {
        cursor: move;
        background: #f8f9fa;
        padding: 5px;
        border-radius: 4px;
        margin-right: 10px;
    }
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('authordashboard.comics') }}" class="text-decoration-none">Mes bandes dessinées</a></li>
                @if($selectedComic)
                    <li class="breadcrumb-item"><a href="{{ route('authordashboard.comics.show', $selectedComic) }}" class="text-decoration-none">{{ $selectedComic->title }}</a></li>
                @endif
                <li class="breadcrumb-item active">Nouveau chapitre</li>
            </ol>
        </nav>

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
            <h1 class="mb-4">Créer un nouveau chapitre de BD</h1>

            <form action="{{ route('authordashboard.comics.chapters.store') }}" method="POST" enctype="multipart/form-data" id="chapter-form">
                @csrf

                <div class="mb-3">
                    <label for="comic_id" class="form-label">Bande dessinée</label>
                    <select class="form-select @error('comic_id') is-invalid @enderror" 
                            id="comic_id" name="comic_id" required 
                            {{ $selectedComic ? 'disabled' : '' }}>
                        <option value="">Sélectionner une bande dessinée</option>
                        @foreach($comics as $comic)
                            <option value="{{ $comic->id }}" 
                                {{ (old('comic_id') == $comic->id || ($selectedComic && $selectedComic->id == $comic->id)) ? 'selected' : '' }}>
                                {{ $comic->title }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedComic)
                        <input type="hidden" name="comic_id" value="{{ $selectedComic->id }}">
                    @endif
                    @error('comic_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="chapter_number" class="form-label">Numéro du chapitre</label>
                    <input type="number" class="form-control @error('chapter_number') is-invalid @enderror" 
                           id="chapter_number" name="chapter_number" value="{{ old('chapter_number') }}" required min="1">
                    <div id="usedNumbers" class="form-text">
                        Numéros déjà utilisés : {{ implode(', ', $usedNumbers) }}
                    </div>
                    @error('chapter_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                
                <h3>Pages du chapitre</h3>
                <p class="text-muted">Ajoutez les pages de votre chapitre. Vous pouvez les réorganiser par glisser-déposer.</p>
                
                <div id="page-container">
                    <!-- Les pages seront ajoutées ici dynamiquement -->
                </div>
                
                <div class="mb-4 mt-3">
                    <button type="button" id="add-page-btn" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Ajouter une page
                    </button>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    @if($selectedComic)
                        <a href="{{ route('authordashboard.comics.show', $selectedComic) }}" class="btn btn-secondary">Annuler</a>
                    @else
                        <a href="{{ route('authordashboard.comics') }}" class="btn btn-secondary">Annuler</a>
                    @endif
                    <button type="submit" class="btn btn-primary">Créer le chapitre</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    // Gestion des numéros de chapitre déjà utilisés
    document.getElementById('comic_id').addEventListener('change', function() {
        const comicId = this.value;
        if (comicId) {
            fetch(`/api/comics/chapters/used-numbers/${comicId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('usedNumbers').innerHTML = 
                        'Numéros déjà utilisés: ' + (data.length ? data.join(', ') : 'aucun');
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('usedNumbers').innerHTML = 
                        'Erreur lors de la récupération des numéros';
                });
        } else {
            document.getElementById('usedNumbers').innerHTML = 'Numéros déjà utilisés: aucun';
        }
    });

    // Gestion des pages
    let pageCount = 0;
    const pageContainer = document.getElementById('page-container');
    const addPageBtn = document.getElementById('add-page-btn');
    
    // Fonction pour ajouter une nouvelle page
    function addPage() {
        pageCount++;
        
        const pageDiv = document.createElement('div');
        pageDiv.className = 'page-preview';
        pageDiv.dataset.pageNumber = pageCount;
        
        pageDiv.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
                <h5 class="mb-0">Page ${pageCount}</h5>
            </div>
            <input type="file" name="pages[${pageCount}][image]" class="form-control mb-2 page-file" accept="image/*" required>
            <div class="image-preview mb-2" style="display: none;"></div>
            <input type="text" name="pages[${pageCount}][caption]" class="form-control caption-input" placeholder="Légende (optionnel)">
            <input type="hidden" name="pages[${pageCount}][page_number]" value="${pageCount}">
            <button type="button" class="btn btn-danger btn-sm mt-2 remove-page-btn">Supprimer cette page</button>
        `;
        
        pageContainer.appendChild(pageDiv);
        
        // Ajouter l'événement pour prévisualiser l'image
        const fileInput = pageDiv.querySelector('.page-file');
        const imagePreview = pageDiv.querySelector('.image-preview');
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" class="img-fluid">`;
                    imagePreview.style.display = 'block';
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Ajouter l'événement pour supprimer la page
        const removeBtn = pageDiv.querySelector('.remove-page-btn');
        removeBtn.addEventListener('click', function() {
            pageDiv.remove();
            updatePageNumbers();
        });
    }
    
    // Fonction pour mettre à jour les numéros de page après suppression
    function updatePageNumbers() {
        const pages = pageContainer.querySelectorAll('.page-preview');
        pages.forEach((page, index) => {
            const newPageNumber = index + 1;
            const pageTitle = page.querySelector('h5');
            const pageNumberInput = page.querySelector('input[name$="[page_number]"]');
            
            // Mettre à jour le titre de la page
            pageTitle.textContent = `Page ${newPageNumber}`;
            
            // Mettre à jour la valeur du numéro de page
            pageNumberInput.value = newPageNumber;
            
            // Mettre à jour les noms des champs pour maintenir l'ordre
            const fileInput = page.querySelector('.page-file');
            const captionInput = page.querySelector('.caption-input');
            
            fileInput.name = `pages[${newPageNumber}][image]`;
            captionInput.name = `pages[${newPageNumber}][caption]`;
            pageNumberInput.name = `pages[${newPageNumber}][page_number]`;
            
            // Mettre à jour l'attribut data-page-number
            page.dataset.pageNumber = newPageNumber;
        });
        
        pageCount = pages.length;
    }
    
    // Ajouter l'événement pour ajouter une page
    addPageBtn.addEventListener('click', addPage);
    
    // Initialiser Sortable.js pour le glisser-déposer
    new Sortable(pageContainer, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function() {
            updatePageNumbers();
        }
    });
    
    // Ajouter une première page par défaut
    addPage();
    
    // Validation du formulaire
    document.getElementById('chapter-form').addEventListener('submit', function(e) {
        const pages = pageContainer.querySelectorAll('.page-preview');
        if (pages.length === 0) {
            e.preventDefault();
            alert('Vous devez ajouter au moins une page à votre chapitre.');
        }
    });
</script>
@endsection