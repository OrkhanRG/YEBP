<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ArticleCommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    Route::middleware('isAdmin')->group(function () {
        Route::group(['prefix' => 'filemanager'], function () {
            \UniSharp\LaravelFilemanager\Lfm::routes();
        });
        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

        Route::get('/', function () {
            return view('admin.index');
        })->name('admin.index');

        //articles
        Route::get('/articles', [ArticleController::class, 'index'])->name('article.index');
        Route::get('/articles/create', [ArticleController::class, 'create'])->name('article.create');
        Route::post('/articles/create', [ArticleController::class, 'store']);
        Route::get('/articles/{id}/edit', [ArticleController::class, 'edit'])->name('article.edit');
        Route::post('/articles/{id}/edit', [ArticleController::class, 'update']);
        Route::post('/articles/change-status', [ArticleController::class, 'changeStatus'])->name('article.changeStatus');
        Route::delete('/articles/delete', [ArticleController::class, 'delete'])->name('article.delete');

        Route::get('article/pending-approval', [ArticleCommentController::class, 'approvalList'])->name('article.pending-approval');
        Route::get('article/comment-list', [ArticleCommentController::class, 'list'])->name('article.comment.list');
        Route::post('article/pending-approval/change-status', [ArticleCommentController::class, 'changeStatus'])->name('article.pending-approval.changeStatus');
        Route::delete('article/pending-approval/delete', [ArticleCommentController::class, 'delete'])->name('article.pending-approval.delete');
        Route::post('article/comment-restore', [ArticleCommentController::class, 'restore'])->name('article.comment.restore');

        //categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('/categories/create', [CategoryController::class, 'store']);
        Route::post('/categories/change-status', [CategoryController::class, 'changeStatus'])->name('categories.changeStatus');
        Route::post('/categories/change-feature-status', [CategoryController::class, 'changeFeatureStatus'])->name('categories.changeFeatureStatus');
        Route::post('/categories/delete', [CategoryController::class, 'delete'])->name('categories.delete');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->whereNumber('id');
        Route::post('/categories/{id}/edit', [CategoryController::class, 'update'])->whereNumber('id');

        //settings
        Route::get('settings', [SettingsController::class, 'show'])->name('settings');
        Route::post('settings', [SettingsController::class, 'update']);

        //users
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users/create', [UserController::class, 'store']);
        Route::post('/users/change-status', [UserController::class, 'changeStatus'])->name('users.changeStatus');
        Route::post('/users/change-is-admin', [UserController::class, 'changeIsAdmin'])->name('users.changeIsAdmin');
        Route::get('/users/{user:username}/edit', [UserController::class, 'edit'])->name('users.edit')->whereNumber('id');
        Route::post('/users/{user:username}/edit', [UserController::class, 'update'])->whereNumber('id');
        Route::delete('/users/delete', [UserController::class, 'delete'])->name('users.delete');
        Route::post('/users/restore', [UserController::class, 'restore'])->name('users.restore');
    });


    Route::post('/articles/favorite', [ArticleController::class, 'favorite'])->name('article.favorite');
    Route::post('/articles/comment-favorite', [ArticleCommentController::class, 'favorite'])->name('article.comment.favorite');


});
Route::get('admin/login',[LoginController::class, 'showLogin'])->name('login');
Route::post('admin/login',[LoginController::class, 'login']);


//front home page
Route::get('/', [FrontController::class, 'home'])->name('home');
Route::get('/meqaleler', [FrontController::class, 'articleList'])->name('front.articleList');
Route::get('/kateqoriyalar/{category:slug}', [FrontController::class, 'category'])->name('front.categoryArticles');
Route::get('/yazarlar/{user:username}', [FrontController::class, 'authorArticles'])->name('front.authorArticles');
Route::get('/@{user:username}/{article:slug}', [FrontController::class, 'articleDetail'])->name('front.articleDetail')->middleware('visitedArticle');
Route::post('{article:id}/article-comment', [FrontController::class, 'articleComment'])->name('article.comment');
Route::get('/search', [FrontController::class, 'search'])->name('front.search');

//auth
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');


Route::get('/register',[LoginController::class, 'showRegister'])->name('register');
Route::post('/register',[LoginController::class, 'register']);

Route::get('/login',[LoginController::class, 'showLoginUser'])->name('user.login');
Route::post('/login',[LoginController::class, 'loginUser']);

Route::get('/elaqe', [LoginController::class, ''])->name('contact');

Route::get('sifre-sifirla', [LoginController::class, 'showPasswordReset'])->name('passwordReset');
Route::post('sifre-sifirla', [LoginController::class, 'sendPasswordReset']);
Route::get('sifre-sifirla/{token}', [LoginController::class, 'showPasswordResetConfirm'])->name('passwordResetToken');
Route::post('sifre-sifirla/{token}', [LoginController::class, 'passwordReset']);

Route::get('/auth/verify/{token}', [LoginController::class, 'verify'])->name('verify-token');
Route::get('/auth/{driver}/callback', [LoginController::class, 'socialVerify'])->name('socialVerify');
Route::get('/auth/{driver}', [LoginController::class, 'socialLogin'])->name('socialLogin');
