<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Agency\Auth')->name('agency.')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->name('logout');
    });
    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });
    Route::controller('ForgotPasswordController')->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'sendResetCodeEmail')->name('password.email');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });
    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->prefix('social')->group(function () {
        Route::get('login/{provider}', 'socialLogin')->name('social.login');
        Route::get('login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('agency')->name('agency.')->group(function () {
    //authorization
    Route::namespace('Agency')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend/verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify/email', 'emailVerification')->name('verify.email');
        Route::post('verify/mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify/g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['agency.check'])->group(function () {

        Route::get('data', 'Agency\AgencyController@userData')->name('data');
        Route::post('data/submit', 'Agency\AgencyController@userDataSubmit')->name('data.submit');

        Route::middleware('agency.registration.complete')->namespace('Agency')->group(function () {

            Route::controller('AgencyController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');
                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');
            });

              //KYC
              Route::controller('AgencyController')->group(function () {
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile/setting', 'profile')->name('profile.setting');
                Route::post('profile/setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            //Tour Package
           
            Route::controller('TourPackageController')->name('tour.package.')->prefix('tour-package')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create')->middleware('agency.kyc');
                Route::post('store', 'store')->name('store')->middleware('agency.kyc');
                Route::get('edit/{id}', 'edit')->name('edit')->middleware('agency.kyc');
                Route::put('update/{id}', 'update')->name('update')->middleware('agency.kyc');
                Route::get('status-change/{id}', 'statusChange')->name('status.change')->middleware('agency.kyc');
                Route::post('delete/{id}', 'delete')->name('delete')->middleware('agency.kyc');
                Route::get('my-tour', 'myList')->name('my.list');
                Route::get('agency-tour', 'allAgency')->name('all.agency');
                Route::get('search', 'search')->name('search');
                Route::post('image', 'tourPackageImageDelete')->name('image.delete');
                Route::get('active', 'active')->name('active');
                Route::get('pending', 'pending')->name('pending');
                Route::get('expired', 'expired')->name('expired');
                Route::get('running', 'running')->name('running');
            });
          


              //Booking Controller
              Route::controller('BookingController')->name('tour.package.booking.')->group(function () {
                Route::post('/booking-now', 'bookingNow')->name('now')->middleware('kyc');
                Route::get('/booking-list', 'bookingTourPackageList')->name('my.list');
                Route::get('pending', 'pending')->name('pending');
                Route::get('approved', 'approved')->name('approved');
                Route::get('canceled', 'canceled')->name('canceled');
                Route::get('/booking-user-list/{id}', 'userList')->name('user.list');
                Route::get('/booking-details/{id}', 'bookingDetails')->name('details');
                

            });


            // ticket
            Route::controller('TicketController')->prefix('ticket')->group(function () {
                Route::get('all', 'supportTicket')->name('ticket');
                Route::get('new', 'openSupportTicket')->name('ticket.open');
                Route::post('create', 'storeSupportTicket')->name('ticket.store');
                Route::get('view/{ticket}', 'viewTicket')->name('ticket.view');
                Route::post('reply/{ticket}', 'replyTicket')->name('ticket.reply');
                Route::post('close/{ticket}', 'closeTicket')->name('ticket.close');
                Route::get('download/{ticket}', 'ticketDownload')->name('ticket.download');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::get('/', 'withdrawMoney');
                Route::post('/', 'withdrawStore')->name('.money');
                Route::get('preview', 'withdrawPreview')->name('.preview');
                Route::post('preview', 'withdrawSubmit')->name('.submit');
                Route::get('history', 'withdrawLog')->name('.history');
            });
        });
    });
});
