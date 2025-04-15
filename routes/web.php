<?php

use App\Http\Controllers\Api\ComicController;
use App\Http\Controllers\Api\NovelController;
use App\Http\Controllers\AuthorDashboard\AuthController;
use App\Http\Controllers\AuthorDashboard\ComicChapterController;
use App\Http\Controllers\AuthorDashboard\ComicController as AuthorDashboardComicController;
use App\Http\Controllers\AuthorDashboard\DashboardController;
use App\Http\Controllers\AuthorDashboard\NovelChapterController;
use App\Http\Controllers\AuthorDashboard\NovelController as AuthorDashboardNovelController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'index']);
  

Route::prefix('/api')->group(function () {

    // Route lié aux comics
    Route::get('/comics', [ComicController::class, 'index']);
    Route::get('/comics/{comic}', [ComicController::class, 'show']);

    // Route lié aux romans
    Route::get('/novels', [NovelController::class, 'index']);
    Route::get('/novels/{novel}', [NovelController::class, 'show']);

    // Route lié aux chapitres
    Route::get('/chapters/used-numbers/{novel}', [NovelChapterController::class, 'getUsedNumbers']);
    Route::get('/comics/chapters/used-numbers/{comic}', [ComicChapterController::class, 'getUsedNumbers']);


});

Route::prefix('/author/dashboard')->group(function () {

    // Route lié au dashboard
    Route::get('/', [AuthController::class, 'index'])->name('authordashboard.login');
    Route::post('/login/auth', [AuthController::class, 'authenticate'])->name('dashboard.authenticate');

    Route::middleware('auth')->group(function () {
        Route::post('/login/logout', [AuthController::class, 'logout'])->name('authordashboard.logout');
        Route::get('/stats', [DashboardController::class, 'index'])->name('authordashboard.index');

        // Route lié aux romans
        Route::get('/novels', [AuthorDashboardNovelController::class, 'index'])->name('authordashboard.novels');
        Route::get('/novels/create', [AuthorDashboardNovelController::class, 'create'])->name('authordashboard.novels.create');
        Route::get('/novels/{novel}', [AuthorDashboardNovelController::class, 'show'])->name('authordashboard.novels.show');
        Route::post('/novels/store', [AuthorDashboardNovelController::class, 'store'])->name('authordashboard.novels.store');
        Route::get('/novels/edit/{novel}', [AuthorDashboardNovelController::class, 'edit'])->name('authordashboard.novels.edit');
        Route::put('/novels/update/{novel}', [AuthorDashboardNovelController::class,'update'])->name('authordashboard.novels.update');
        Route::put('/novels/destroy/{novel}', [AuthorDashboardNovelController::class,'destroy'])->name('authordashboard.novels.destroy');

        // Route lié aux chapitres de romans
        Route::post('/novels/chapters/store', [NovelChapterController::class, 'store'])->name('authordashboard.novels.chapters.store');
        Route::get('/novels/chapters/create', [NovelChapterController::class, 'create'])->name('authordashboard.novels.chapters.create');
        Route::get('/novels/chapters/edit/{chapter}', [NovelChapterController::class, 'edit'])->name('authordashboard.novels.chapters.edit');
        Route::put('/novels/chapters/update/{chapter}', [NovelChapterController::class, 'update'])->name('authordashboard.novels.chapters.update');

        // Route lié aux comics
        Route::get('/comics', [AuthorDashboardComicController::class, 'index'])->name('authordashboard.comics');
        Route::get('/comics/create', [AuthorDashboardComicController::class, 'create'])->name('authordashboard.comics.create');
        Route::post('/comics/store', [AuthorDashboardComicController::class, 'store'])->name('authordashboard.comics.store');
        Route::get('/comics/{comic}', [AuthorDashboardComicController::class, 'show'])->name('authordashboard.comics.show');
        Route::get('/comics/edit/{comic}', [AuthorDashboardComicController::class, 'edit'])->name('authordashboard.comics.edit');
        Route::put('/comics/update/{comic}', [AuthorDashboardComicController::class,'update'])->name('authordashboard.comics.update');
        Route::put('/comics/destroy/{comic}', [AuthorDashboardComicController::class,'destroy'])->name('authordashboard.comics.destroy');

        // Route lié aux chapitres de comics
        Route::post('/comics/chapters/store', [ComicChapterController::class, 'store'])->name('authordashboard.comics.chapters.store');
        Route::get('/comics/chapters/create', [ComicChapterController::class, 'create'])->name('authordashboard.comics.chapters.create');
        Route::get('/comics/chapters/edit/{chapter}', [ComicChapterController::class, 'edit'])->name('authordashboard.comics.chapters.edit');
        Route::put('/comics/chapters/update/{chapter}', [ComicChapterController::class, 'update'])->name('authordashboard.comics.chapters.update');

        // Route lié a la lecture
        Route::get('/comics/{comic}/read', [App\Http\Controllers\AuthorDashboard\ComicController::class, 'read'])->name('authordashboard.comics.read');
        Route::get('/novels/{novel}/read', [AuthorDashboardNovelController::class, 'read'])->name('authordashboard.novels.read');
    });

});

Route::prefix('/admin/dashboard')->group(function () {

});