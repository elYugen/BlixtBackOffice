@extends('authordashboard.layouts.base')
@section('title', 'Modifier le chapitre')

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
    .existing-image {
        position: relative;
        margin-bottom: 10px;
    }
    .replace-image-btn {
        margin-top: 10px;
    }
    .image-replacement {
        display: none;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('authordashboard.comics') }}" class="text-decoration-none">Mes bandes dessinées</a></li>
                <li class="breadcrumb-item"><a href="{{ route('authordashboard.comics.show', $chapter->comic) }}" class="text-decoration-none">{{ $chapter->comic->title }}</a></li>
                <li class="breadcrumb-item active">Modifier le chapitre</li>
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
            <h1 class="mb-4">Modifier le chapitre</h1>

            <form action="{{ route('authordashboard.comics.chapters.update', $chapter) }}" method="POST" enctype="multipart/form-data" id="chapter-form">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="chapter_number" class="form-label">Numéro du chapitre</label>
                    <input type="number" class="form-control @error('chapter_number') is-invalid @enderror" 
                           id="chapter_number" name="chapter_number" value="{{ old('chapter_number', $chapter->chapter_number) }}" required min="1">
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
                           id="title" name="title" value="{{ old('title', $chapter->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                
                <h3>Pages du chapitre</h3>
                <p class="text-muted">Modifiez les pages de votre chapitre. Vous pouvez les réorganiser par glisser-déposer.</p>
                
                <div id="page-container">
                    @foreach($chapter->pages as $page)
                    <div class="page-preview" data-page-id="{{ $page->id }}" data-page-number="{{ $page->page_number }}">
                        <div class="d-flex align-items-center mb-2">
                            <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
                            <h5 class="mb-0">Page {{ $page->page_number }}</h5>
                        </div>
                        
                        <div class="existing-image">
                            <img src="{{ url($page->image_url) }}" class="img-fluid mb-2" alt="Page {{ $page->page_number }}">
                        </div>
                        
                        <input type="hidden" name="existing_pages[{{ $page->id }}][page_number]" value="{{ $page->page_number }}" class="page-number-input">
                        <input type="text" name="existing_pages[{{ $page->id }}][caption]" class="form-control caption-input" placeholder="Légende (optionnel)" value="{{ $page->caption }}">
                        
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-primary btn-sm replace-image-btn">Remplacer l'image</button>
                            <button type="button" class="btn btn-danger btn-sm remove-page-btn">Supprimer cette page</button>
                        </div>
                        
                        <div class="image-replacement">
                            <input type="file" name="existing_pages[{{ $page->id }}][new_image]" class="form-control new-image-input" accept="image/*">
                            <div class="form-text">Laissez vide pour conserver l'image actuelle</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mb-4 mt-3">
                    <button type="button" id="add-page-btn" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Ajouter une nouvelle page
                    </button>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('authordashboard.comics.show', $chapter->comic) }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Mettre à jour le chapitre</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    // Gestion des numéros de chapitre déjà utilisés
    const comicId = {{ $chapter->comic_id }};
    const chapterId = {{ $chapter->id }};
    
    fetch(`/api/comics/chapters/used-numbers/${comicId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Filtrer le numéro actuel du chapitre
            const filteredNumbers = data.filter(num => num != {{ $chapter->chapter_number }});
            document.getElementById('usedNumbers').innerHTML = 
                'Numéros déjà utilisés: ' + (filteredNumbers.length ? filteredNumbers.join(', ') : 'aucun');
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('usedNumbers').innerHTML = 
                'Erreur lors de la récupération des numéros';
        });

    // Gestion des pages
    let pageCount = {{ $chapter->pages->count() }};
    let newPageCount = 0;
    const pageContainer = document.getElementById('page-container');
    const addPageBtn = document.getElementById('add-page-btn');
    
    // Fonction pour ajouter une nouvelle page
    function addPage() {
        newPageCount++;
        
        const pageDiv = document.createElement('div');
        pageDiv.className = 'page-preview';
        pageDiv.dataset.pageNumber = pageCount + newPageCount;
        
        pageDiv.innerHTML = `
            <div class="d-flex align-items-center mb-2">
                <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
                <h5 class="mb-0">Nouvelle page</h5>
            </div>
            <input type="file" name="new_pages[${newPageCount}][image]" class="form-control mb-2 page-file" accept="image/*" required>
            <div class="image-preview mb-2" style="display: none;"></div>
            <input type="text" name="new_pages[${newPageCount}][caption]" class="form-control caption-input" placeholder="Légende (optionnel)">
            <input type="hidden" name="new_pages[${newPageCount}][page_number]" value="${pageCount + newPageCount}" class="page-number-input">
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
    
    // Fonction pour mettre à jour les numéros de page après réorganisation
    function updatePageNumbers() {
        const pages = pageContainer.querySelectorAll('.page-preview');
        pages.forEach((page, index) => {
            const newPageNumber = index + 1;
            const pageNumberInput = page.querySelector('.page-number-input');
            
            // Mettre à jour la valeur du numéro de page
            pageNumberInput.value = newPageNumber;
            
            // Mettre à jour l'attribut data-page-number
            page.dataset.pageNumber = newPageNumber;
        });
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
    
    // Gestion des pages existantes
    document.querySelectorAll('.replace-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const pagePreview = this.closest('.page-preview');
            const replacementDiv = pagePreview.querySelector('.image-replacement');
            
            if (replacementDiv.style.display === 'none' || replacementDiv.style.display === '') {
                replacementDiv.style.display = 'block';
                this.textContent = 'Annuler le remplacement';
            } else {
                replacementDiv.style.display = 'none';
                this.textContent = 'Remplacer l\'image';
                // Réinitialiser l'input file
                const fileInput = replacementDiv.querySelector('input[type="file"]');
                fileInput.value = '';
            }
        });
    });
    
    // Gestion de la suppression des pages existantes
    document.querySelectorAll('.remove-page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const pagePreview = this.closest('.page-preview');
            const pageId = pagePreview.dataset.pageId;
            
            if (pageId) {
                // Ajouter un champ caché pour indiquer que cette page doit être supprimée
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'pages_to_delete[]';
                hiddenInput.value = pageId;
                document.getElementById('chapter-form').appendChild(hiddenInput);
            }
            
            pagePreview.remove();
            updatePageNumbers();
        });
    });
    
    // Prévisualisation des nouvelles images pour les pages existantes
    document.querySelectorAll('.new-image-input').forEach(input => {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const pagePreview = this.closest('.page-preview');
                const existingImage = pagePreview.querySelector('.existing-image img');
                
                reader.onload = function(e) {
                    existingImage.src = e.target.result;
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Validation du formulaire
    document.getElementById('chapter-form').addEventListener('submit', function(e) {
        const pages = pageContainer.querySelectorAll('.page-preview');
        if (pages.length === 0) {
            e.preventDefault();
            alert('Votre chapitre doit contenir au moins une page.');
        }
    });
</script>
@endsection