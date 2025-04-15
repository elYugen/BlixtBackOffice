@extends('authordashboard.layouts.base')
@section('title', 'Lecture - ' . $novel->title)
@section('styles')
<style>
    .reader-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .reader-header {
        background: #fff;
        padding: 15px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    
    .chapter-selector {
        max-width: 300px;
    }
    
    .chapter-content {
        background: #fff;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-top: 20px;
        line-height: 1.8;
        font-size: 1.1rem;
    }
    
    .chapter-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .chapter-navigation {
        background: #fff;
        padding: 15px;
        box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        position: sticky;
        bottom: 0;
    }
    
    .chapter-text {
        white-space: pre-line;
    }
</style>
@endsection

@section('content')
    <div class="reader-container">
        <div class="reader-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $novel->title }}</h4>
                    </div>
                    <div class="d-flex align-items-center">
                        <select id="chapterSelector" class="form-select chapter-selector me-2">
                            @foreach($novel->chapters as $chap)
                                <option value="chapter-{{ $chap->id }}">
                                    Chapitre {{ $chap->chapter_number }}: {{ $chap->title }}
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('authordashboard.novels.show', $novel) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        @if($novel->chapters->isEmpty())
            <div class="container py-4">
                <div class="alert alert-info">
                    Ce roman ne contient pas encore de chapitres.
                </div>
            </div>
        @else
            {{-- <pre>{{ print_r($novel->chapters->pluck('id', 'chapter_number'), true) }}</pre> --}}
            @foreach($novel->chapters as $chapter)
                <div id="chapter-{{ $chapter->id }}" class="chapter-content" style="{{ $loop->first ? '' : 'display: none;' }}">
                    <h2 class="chapter-title">
                        Chapitre {{ $chapter->chapter_number }}: {{ $chapter->title }}
                    </h2>
                    <div class="chapter-text">
                        {!! $chapter->content !!}
                    </div>
                </div>
            @endforeach
            <div class="chapter-navigation">
                <div class="container">
                    <div class="d-flex justify-content-between">
                        <button id="prevChapter" class="btn btn-primary" {{ $novel->chapters->count() <= 1 ? 'disabled' : '' }}>
                            <i class="bi bi-arrow-left"></i> Chapitre précédent
                        </button>
                        <button id="nextChapter" class="btn btn-primary" {{ $novel->chapters->count() <= 1 ? 'disabled' : '' }}>
                            Chapitre suivant <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Lecture script loaded'); // Debug: Should appear in your browser console

        const chapterSelector = document.getElementById('chapterSelector');
        const prevChapterBtn = document.getElementById('prevChapter');
        const nextChapterBtn = document.getElementById('nextChapter');
        const chapterContents = Array.from(document.querySelectorAll('.chapter-content'));

        if (!chapterSelector || !prevChapterBtn || !nextChapterBtn || chapterContents.length === 0) {
            console.error('Lecture: Some elements are missing from the DOM');
            return;
        }

        const chapterIds = Array.from(chapterSelector.options).map(opt => opt.value.replace('chapter-', '').trim());

        function showChapterById(chapterId) {
            chapterContents.forEach(chapter => {
                chapter.style.display = 'none';
            });
            const chapterDiv = document.getElementById('chapter-' + chapterId);
            if (chapterDiv) chapterDiv.style.display = 'block';
            chapterSelector.value = 'chapter-' + chapterId;
            updateNavigationButtons(chapterId);
        }

        function updateNavigationButtons(currentId) {
            const idx = chapterIds.indexOf(currentId);
            prevChapterBtn.disabled = idx <= 0;
            nextChapterBtn.disabled = idx === -1 || idx >= chapterIds.length - 1;
        }

        let currentChapterId = chapterIds[0];
        showChapterById(currentChapterId);

        chapterSelector.addEventListener('change', function() {
            currentChapterId = this.value.replace('chapter-', '').trim();
            showChapterById(currentChapterId);
        });

        prevChapterBtn.addEventListener('click', function() {
            let idx = chapterIds.indexOf(currentChapterId);
            if (idx > 0) {
                currentChapterId = chapterIds[idx - 1];
                showChapterById(currentChapterId);
            }
        });

        nextChapterBtn.addEventListener('click', function() {
            let idx = chapterIds.indexOf(currentChapterId);
            if (idx < chapterIds.length - 1) {
                currentChapterId = chapterIds[idx + 1];
                showChapterById(currentChapterId);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && !prevChapterBtn.disabled) {
                prevChapterBtn.click();
            } else if (e.key === 'ArrowRight' && !nextChapterBtn.disabled) {
                nextChapterBtn.click();
            }
        });
    });
</script>
@endsection