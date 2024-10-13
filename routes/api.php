<?php

use App\Http\Controllers\NewsController;
use App\Http\Controllers\ScrapperController;
use App\Http\Controllers\VoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/store-todb' , [NewsController::class , 'storeToDb'])->name('news,storeToDb');
Route::get('/one-news', [NewsController::class,'getOneNews'])->name('news.getOne');
Route::get('/featured-news', [NewsController::class,'getFeaturedNews'])->name('news.getFeatured');
// Route::get('/')

//Testing purpose
Route::post('scrapePageData',[ScrapperController::class, 'scrapePage'])->name('api.scrapePageData');
Route::get('scrapeMainPage', [ScrapperController::class, 'scrapeMainPage'])->name('api.scrapeMainPage');
// Route::get('generateVoice', [VoiceController::class ,'generateVoice'])->name('api.generateVoice');