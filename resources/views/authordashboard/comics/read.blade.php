@extends('authordashboard.layouts.base')
@section('title', 'Lire - ' . $comic->title)
@section('styles')
<style>
    .reader-container {
        max-width: 100%;
        margin: 0 auto;
        background: #f8f9fa;
        min-height: calc(100vh - 70px);
    }
    
    .reader-header {
        background: #fff;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    
    .chapter-selector {
        max-width: 300px;
    }
    
    .carousel-container {
        background: #222;
        padding: 20px 0;
    }
    
    .carousel-inner {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .carousel-item {
        text-align: center;
        height: calc(100vh - 200px);
    }
    
    .carousel-item img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        margin: 0 auto;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 10%;
        opacity: 0.2;
    }
    
    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        opacity: 0.8;
    }
    
    .carousel-caption {
        background: rgba(0,0,0,0.5);
        border-radius: 5px;
        padding: 10px;
        bottom: 20px;
    }
    
    .page-indicator {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        z-index: 10;
    }
    
    .chapter-navigation {
        background: #fff;
        padding: 15px;
        box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
    <!--@include('authordashboard.layouts.navbar')-->

    <div class="reader-container">
        <div class="reader-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ $comic->title }}</h4>
                    </div>
                    <div class="d-flex align-items-center">
                        <select id="chapterSelector" class="form-select chapter-selector me-2">
                            @foreach($comic->chapters as $chap)
                                <option value="chapter-{{ $chap->id }}">
                                    Chapitre {{ $chap->chapter_number }}: {{ $chap->title }}
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('authordashboard.comics.show', $comic) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        @if($comic->chapters->isEmpty())
            <div class="container py-4">
                <div class="alert alert-info">
                    Cette bande dessinée ne contient pas encore de chapitres.
                </div>
            </div>
        @else
            @foreach($comic->chapters as $chapter)
                    
                    @if($chapter->pages->isEmpty())
                        <div class="container">
                            <div class="alert alert-info">
                                Ce chapitre ne contient pas encore de pages.
                            </div>
                        </div>
                    @else
                        <div class="carousel-container">
                            <div id="pagesCarousel-{{ $chapter->id }}" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                                <div class="carousel-inner">
                                    @foreach($chapter->pages as $page)
                                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . str_replace('/storage/', '', $page->image_url)) }}" 
                                                 alt="Page {{ $page->page_number }}" 
                                                 class="d-block">
                                            @if($page->caption)
                                                <div class="carousel-caption d-none d-md-block">
                                                    <p>{{ $page->caption }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                <button class="carousel-control-prev" type="button" data-bs-target="#pagesCarousel-{{ $chapter->id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Précédent</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#pagesCarousel-{{ $chapter->id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Suivant</span>
                                </button>
                                
                                <!--<div class="page-indicator">
                                    Page <span class="current-page">1</span> / {{ $chapter->pages->count() }}
                                </div>-->
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
            
            <div class="chapter-navigation">
                <div class="container">
                    <div class="d-flex justify-content-between">
                        <button id="prevChapter" class="btn btn-primary" {{ $comic->chapters->count() <= 1 ? 'disabled' : '' }}>
                            <i class="bi bi-arrow-left"></i> Chapitre précédent
                        </button>
                        <button id="nextChapter" class="btn btn-primary" {{ $comic->chapters->count() <= 1 ? 'disabled' : '' }}>
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
        const chapterSelector = document.getElementById('chapterSelector');
        const prevChapterBtn = document.getElementById('prevChapter');
        const nextChapterBtn = document.getElementById('nextChapter');
        const chapterContents = document.querySelectorAll('.chapter-content');
        
        let currentChapterIndex = 0;
        
        // Handle chapter selection
        chapterSelector.addEventListener('change', function() {
            const selectedChapterId = this.value;
            
            // Hide all chapters
            chapterContents.forEach(chapter => {
                chapter.style.display = 'none';
            });
            
            // Show selected chapter
            const selectedChapter = document.getElementById(selectedChapterId);
            if (selectedChapter) {
                selectedChapter.style.display = 'block';
                currentChapterIndex = chapterSelector.selectedIndex;
            }
            
            updateNavigationButtons();
        });
        
        // Handle chapter navigation buttons
        prevChapterBtn.addEventListener('click', function() {
            if (currentChapterIndex > 0) {
                chapterSelector.selectedIndex = currentChapterIndex - 1;
                chapterSelector.dispatchEvent(new Event('change'));
            }
        });
        
        nextChapterBtn.addEventListener('click', function() {
            if (currentChapterIndex < chapterSelector.options.length - 1) {
                chapterSelector.selectedIndex = currentChapterIndex + 1;
                chapterSelector.dispatchEvent(new Event('change'));
            }
        });
        
        // Update navigation buttons state
        function updateNavigationButtons() {
            prevChapterBtn.disabled = currentChapterIndex === 0;
            nextChapterBtn.disabled = currentChapterIndex === chapterSelector.options.length - 1;
        }
        
        // Initialize all carousels
        const carousels = document.querySelectorAll('.carousel');
        carousels.forEach(carousel => {
            // Initialize Bootstrap carousel
            const bsCarousel = new bootstrap.Carousel(carousel, {
                interval: false,
                keyboard: false // We'll handle keyboard navigation manually
            });
            
            const pageIndicator = carousel.querySelector('.current-page');
            const slides = carousel.querySelectorAll('.carousel-item');
            
            // Manual navigation buttons
            const prevBtn = carousel.querySelector('.carousel-control-prev');
            const nextBtn = carousel.querySelector('.carousel-control-next');
            
            // Update page counter on manual navigation
            prevBtn.addEventListener('click', function() {
                setTimeout(updatePageCounter, 100);
            });
            
            nextBtn.addEventListener('click', function() {
                setTimeout(updatePageCounter, 100);
            });
            
            // Function to update page counter
            function updatePageCounter() {
                const activeSlide = carousel.querySelector('.carousel-item.active');
                const slideIndex = Array.from(slides).indexOf(activeSlide);
                if (pageIndicator) {
                    pageIndicator.textContent = slideIndex + 1;
                }
            }
            
            // Initial page counter update
            updatePageCounter();
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                e.preventDefault(); // Prevent default scrolling
                
                // Find visible chapter
                let visibleChapter = null;
                for (let i = 0; i < chapterContents.length; i++) {
                    if (chapterContents[i].style.display !== 'none') {
                        visibleChapter = chapterContents[i];
                        break;
                    }
                }
                
                if (!visibleChapter) return;
                
                // Find carousel in visible chapter
                const carousel = visibleChapter.querySelector('.carousel');
                if (!carousel) return;
                
                // Trigger click on appropriate button
                if (e.key === 'ArrowLeft') {
                    carousel.querySelector('.carousel-control-prev').click();
                } else {
                    carousel.querySelector('.carousel-control-next').click();
                }
            }
        });
        
        // Initial update
        updateNavigationButtons();
        
        // Show first chapter by default
        if (chapterContents.length > 0) {
            chapterContents[0].style.display = 'block';
        }
    });
</script>
@endsection