<?php

// Override Following urls
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('sub-domain-check');
Route::get('email-verification/{code}', 'Auth\LoginController@getEmailVerification')->name('front.get-email-verification');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');


Route::group(['middleware' => 'sub-domain-check', 'as' => 'front.', 'namespace' => '\App\Http\Controllers\Front'], function () {

    Route::get('/', 'HomeController@index')->name('home');

    Route::get('/contact', 'HomeController@contact')->name('contact');
    Route::post('/contact-us', 'HomeController@contactUs')->name('contact-us');

    Route::get('/feature', ['uses' => 'HomeController@feature'])->name('feature');
    Route::get('/pricing', ['uses' => 'HomeController@pricing'])->name('pricing');

    Route::resource('/signup', 'RegisterController', ['only' => ['index', 'store']]);
});

// New Routes specific to sub-domain
Route::group(['middleware' => 'sub-domain-check'], function () {

    Route::get('signin', 'SubdomainController@workspace')->name('front.workspace');
    Route::get('forgot-company', 'SubdomainController@forgotCompany')->name('front.forgot-company');
    Route::post('forgot-company', 'SubdomainController@submitForgotCompany')->name('front.submit-forgot-password');
    Route::get('super-admin-login', 'Auth\LoginController@showSuperAdminLogin')->name('front.super-admin-login');
});

Route::post('check-domain', ['uses' => 'SubdomainController@checkDomain'])->name('front.check-domain');
Route::post('notify/domain', ['uses' => 'SubdomainController@notifyDomain'])->name('notify.domain');
Route::get('push-notify-iframe',['uses' => 'SubdomainController@iframe'])->name('push-notify-iframe');



