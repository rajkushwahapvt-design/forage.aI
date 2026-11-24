<?php

use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\WebsiteBuilderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - AI Builds My Website
|--------------------------------------------------------------------------
*/

// Intake & Builder
Route::get('/', [WebsiteBuilderController::class, 'index'])->name('builder.index');
Route::post('/build', [WebsiteBuilderController::class, 'store'])->name('builder.store');

// Visual Editor & Controls
Route::get('/editor/{site}', [WebsiteBuilderController::class, 'editor'])->name('builder.editor');
Route::post('/editor/{site}/style', [WebsiteBuilderController::class, 'updateStyle'])->name('builder.updateStyle');
Route::post('/business/{business}/resolve-conflict', [WebsiteBuilderController::class, 'resolveConflict'])->name('builder.resolveConflict');

// Editor AJAX APIs
Route::post('/api/section/{section}/update', [WebsiteBuilderController::class, 'updateSection'])->name('api.section.update');
Route::post('/api/section/{section}/regenerate', [WebsiteBuilderController::class, 'regenerateSection'])->name('api.section.regenerate');
Route::post('/api/site/{site}/publish', [WebsiteBuilderController::class, 'togglePublish'])->name('api.site.publish');

// Public Website Delivery & Lead Capture
Route::get('/site/{slug}', [PublicSiteController::class, 'show'])->name('public.site.show');
Route::post('/site/{slug}/inquiry', [PublicSiteController::class, 'submitInquiry'])->name('public.site.inquiry');
