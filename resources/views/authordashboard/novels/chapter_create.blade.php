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
    #content {
        min-height: 400px;
    }
    .tox-tinymce {
        min-height: 500px;
    }
</style>
@endsection

@section('content')
    @include('authordashboard.layouts.navbar')

    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('authordashboard.novels') }}" class="text-decoration-none">Mes romans</a></li>
                @if($selectedNovel)
                    <li class="breadcrumb-item"><a href="{{ route('authordashboard.novels.show', $selectedNovel) }}" class="text-decoration-none">{{ $selectedNovel->title }}</a></li>
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
            <h1 class="mb-4">Créer un nouveau chapitre</h1>

            <form action="{{ route('authordashboard.novels.chapters.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="novel_id" class="form-label">Roman</label>
                    <select class="form-select @error('novel_id') is-invalid @enderror" 
                            id="novel_id" name="novel_id" required 
                            {{ $selectedNovel ? 'disabled' : '' }}>
                        <option value="">Sélectionner un roman</option>
                        @foreach($novels as $novel)
                            <option value="{{ $novel->id }}" 
                                {{ (old('novel_id') == $novel->id || ($selectedNovel && $selectedNovel->id == $novel->id)) ? 'selected' : '' }}>
                                {{ $novel->title }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedNovel)
                        <input type="hidden" name="novel_id" value="{{ $selectedNovel->id }}">
                    @endif
                    @error('novel_id')
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

                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" 
                              id="content" name="content" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    @if($selectedNovel)
                        <a href="{{ route('authordashboard.novels.show', $selectedNovel) }}" class="btn btn-secondary">Annuler</a>
                    @else
                        <a href="{{ route('authordashboard.novels') }}" class="btn btn-secondary">Annuler</a>
                    @endif
                    <button type="submit" class="btn btn-primary">Créer le chapitre</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
<script src="https://cdn.tiny.cloud/1/r4aqjnivkgnc6rgt0lb1njsdd7u48tee65anv6ghb5hmiffi/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.getElementById('novel_id').addEventListener('change', function() {
        const novelId = this.value;
        if (novelId) {
            fetch(`/api/chapters/used-numbers/${novelId}`)
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

    tinymce.init({
        selector: '#content',
        plugins: [
            // Core editing features
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
            // Your account includes a free trial of TinyMCE premium features
            // Try the most popular premium features until Apr 27, 2025:
            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save(); // Synchronise TinyMCE avec le textarea
            });
        },
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
    });

</script>
@endsection