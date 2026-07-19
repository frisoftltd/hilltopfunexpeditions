<?php

use Illuminate\Support\Facades\Route;


Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return redirect()->back();
})->name('clear.cache');

Route::namespace('Auth')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout');
    });

    // Admin Password Reset
    Route::controller('ForgotPasswordController')->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'sendResetCodeEmail');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request/report', 'requestReport')->name('request.report');
        Route::post('request/report', 'reportSubmit');

        Route::get('download/attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('manage/users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email/verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email/unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile/unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with/balance', 'usersWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add/sub/balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send/notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send/notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send/notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send/notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification/log/{id}', 'notificationLog')->name('notification.log');

        // kyc
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
    });

    //KYC setting
    Route::controller('KycController')->group(function () {
        Route::get('kyc-setting', 'setting')->name('kyc.setting');
        Route::post('kyc-setting', 'settingUpdate');

        // agency
        Route::get('agency/kyc-setting', 'agencySetting')->name('agency.kyc.setting');
        Route::post('agency/kyc-setting', 'agencySettingUpdate')->name('agency.kyc.setting');
    });

    // agencies Manager
    Route::controller('ManageAgenciesController')->name('agencies.')->prefix('manage/agencies')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email/verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email/unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile/unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with/balance', 'usersWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add/sub/balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send/notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send/notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send/notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send/notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification/log/{id}', 'notificationLog')->name('notification.log');

        // kyc
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
    });

    // location for property
    Route::controller('LocationController')->name('location.')->prefix('location')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
    });

    // Subscriber
    Route::controller('SubscriberController')->group(function () {
        Route::get('subscriber', 'index')->name('subscriber.index');
        Route::get('subscriber/send/email', 'sendEmailForm')->name('subscriber.send.email');
        Route::post('subscriber/remove/{id}', 'remove')->name('subscriber.remove');
        Route::post('subscriber/send/email', 'sendEmail')->name('subscriber.send.email');
    });

    // category
    Route::controller('CategoryController')->name('category.')->prefix('category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update', 'update')->name('update');
        Route::post('status-change/{id}', 'statusChange')->name('status.change');
    });

    //Tour Package
    Route::controller('TourPackageController')->name('tour.package.')->prefix('tour-package')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::put('update/{id}', 'update')->name('update');
        Route::get('status-change/{id}', 'statusChange')->name('status.change');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::get('my-tour', 'myList')->name('my.list');
        Route::get('agency-tour', 'allAgency')->name('all.agency');
        Route::get('search', 'search')->name('search');
        Route::post('image', 'tourPackageImageDelete')->name('image.delete');
        Route::get('pending', 'pending')->name('pending');
        Route::get('cancelled', 'cancelled')->name('cancelled');
        Route::get('active', 'active')->name('active');
        Route::get('expired', 'expired')->name('expired');
        Route::get('running', 'running')->name('running');
    });


    //Booking Controller
    Route::controller('BookingController')->name('tour.package.booking.')->group(function () {
        Route::get('/booking-list', 'bookingTourPackageList')->name('index');
        Route::get('/my-booked', 'myBooked')->name('my.booked');
        Route::post('/booking-preview', 'bookingPreview')->name('preview');
        Route::post('/booking-delete', 'bookingDelete')->name('delete');
        Route::get('/booking-pending', 'bookingTourPackagePending')->name('pending');
        Route::get('/booking-approved', 'bookingTourPackageApproved')->name('approved');
        Route::get('/booking-canceled', 'bookingTourPackageCanceled')->name('canceled');
        Route::get('/booking-list-agencies', 'bookingAgencyList')->name('agency.index');
        Route::get('/booking-user-list/{id}', 'userList')->name('user.list');
        Route::get('/booking-agency-user-list/{id}', 'agencyBookedUserList')->name('agency.user.list');
        Route::get('/booking-details/{id}', 'bookingDetails')->name('details');
    });


    // Deposit Gateway
    Route::name('gateway.')->prefix('payment/gateways')->group(function () {

        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->group(function () {
            Route::get('automatic', 'index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'update')->name('automatic.update');
            Route::post('automatic/remove/{id}', 'remove')->name('automatic.remove');
            Route::post('automatic/activate/{code}', 'activate')->name('automatic.activate');
            Route::post('automatic/deactivate/{code}', 'deactivate')->name('automatic.deactivate');
        });


        // Manual Methods
        Route::controller('ManualGatewayController')->group(function () {
            Route::get('manual', 'index')->name('manual.index');
            Route::get('manual/new', 'create')->name('manual.create');
            Route::post('manual/new', 'store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'update')->name('manual.update');
            Route::post('manual/activate/{code}', 'activate')->name('manual.activate');
            Route::post('manual/deactivate/{code}', 'deactivate')->name('manual.deactivate');
        });
    });


    // DEPOSIT SYSTEM
    Route::name('deposit.')->controller('DepositController')->prefix('manage/deposits')->group(function () {
        Route::get('/', 'deposit')->name('list');
        Route::get('pending', 'pending')->name('pending');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('approved', 'approved')->name('approved');
        Route::get('successful', 'successful')->name('successful');
        Route::get('initiated', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');

        Route::post('reject', 'reject')->name('reject');
        Route::post('approve/{id}', 'approve')->name('approve');
    });


    // WITHDRAW SYSTEM
    Route::name('withdraw.')->prefix('manage/withdrawals')->group(function () {

        Route::controller('WithdrawalController')->group(function () {
            Route::get('pending', 'pending')->name('pending');
            Route::get('approved', 'approved')->name('approved');
            Route::get('rejected', 'rejected')->name('rejected');
            Route::get('log', 'log')->name('log');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('approve', 'approve')->name('approve');
            Route::post('reject', 'reject')->name('reject');
        });


        // Withdraw Method
        Route::controller('WithdrawMethodController')->group(function () {
            Route::get('method/', 'methods')->name('method.index');
            Route::get('method/create', 'create')->name('method.create');
            Route::post('method/create', 'store')->name('method.store');
            Route::get('method/edit/{id}', 'edit')->name('method.edit');
            Route::post('method/edit/{id}', 'update')->name('method.update');
            Route::post('method/activate/{id}', 'activate')->name('method.activate');
            Route::post('method/deactivate/{id}', 'deactivate')->name('method.deactivate');
        });
    });

    // Report
    Route::controller('ReportController')->group(function () {
        Route::get('report/transaction', 'transaction')->name('report.transaction');
        Route::get('report/login/history', 'loginHistory')->name('report.login.history');
        Route::get('report/login/ipHistory/{ip}', 'loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/notification/history', 'notificationHistory')->name('report.notification.history');
        Route::get('report/email/detail/{id}', 'emailDetails')->name('report.email.details');

        // agency
        Route::get('agency/report/transaction', 'agencyTransaction')->name('agency.report.transaction');
        Route::get('agency/report/login/history', 'agencyLoginHistory')->name('agency.report.login.history');
        Route::get('agency/report/login/ipHistory/{ip}', 'agencyLoginIpHistory')->name('agency.report.login.ipHistory');
        Route::get('agency/report/notification/history', 'agencyNotificationHistory')->name('agency.report.notification.history');
        Route::get('agency/report/email/detail/{id}', 'agencyEmailDetails')->name('agency.report.email.details');
    });


    // Admin Support
    Route::controller('SupportTicketController')->prefix('support')->group(function () {
        Route::get('tickets', 'tickets')->name('ticket');
        Route::get('tickets/pending', 'pendingTicket')->name('ticket.pending');
        Route::get('tickets/closed', 'closedTicket')->name('ticket.closed');
        Route::get('tickets/answered', 'answeredTicket')->name('ticket.answered');
        Route::get('tickets/view/{id}', 'ticketReply')->name('ticket.view');
        Route::post('ticket/reply/{id}', 'replyTicket')->name('ticket.reply');
        Route::post('ticket/close/{id}', 'closeTicket')->name('ticket.close');
        Route::get('ticket/download/{ticket}', 'ticketDownload')->name('ticket.download');
        Route::post('ticket/delete/{id}', 'ticketDelete')->name('ticket.delete');

        //agency
        Route::get('agency/tickets', 'agencyTickets')->name('agency.ticket');
        Route::get('agency/tickets/pending', 'agencyPendingTicket')->name('agency.ticket.pending');
        Route::get('agency/tickets/closed', 'agencyClosedTicket')->name('agency.ticket.closed');
        Route::get('agency/tickets/answered', 'agencyAnsweredTicket')->name('agency.ticket.answered');
        Route::get('agency/tickets/view/{id}', 'ticketReply')->name('agency.ticket.view');
    });


    // Language Manager
    Route::controller('LanguageController')->prefix('manage')->group(function () {
        Route::get('languages', 'langManage')->name('language.manage');
        Route::post('language', 'langStore')->name('language.manage.store');
        Route::post('language/delete/{id}', 'langDelete')->name('language.manage.delete');
        Route::post('language/update/{id}', 'langUpdate')->name('language.manage.update');
        Route::get('language/edit/{id}', 'langEdit')->name('language.key');
        Route::post('language/import', 'langImport')->name('language.import.lang');
        Route::post('language/store/key/{id}', 'storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'updateLanguageJson')->name('language.update.key');
        Route::get('language/search/', 'langSearch')->name('language.manage.search');
        Route::get('language/search/replace/', 'langSearchReplace')->name('language.manage.search.replace');
    });

    Route::controller('GeneralSettingController')->group(function () {
        // General Setting
        Route::get('global/settings', 'index')->name('setting.index');
        Route::post('global/settings', 'update')->name('setting.update');

        //configuration
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo', 'logoIconUpdate')->name('setting.logo.icon');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');
        // agency
        Route::get('setting/social/agency/credentials', 'socialiteAgencyCredentials')->name('setting.socialite.agency.credentials');
        Route::post('setting/social/agency/credentials/update/{key}', 'updateAgencySocialiteCredential')->name('setting.socialite.agency.credentials.update');
        Route::post('setting/social/agency/credentials/status/{key}', 'updateAgencySocialiteCredentialStatus')->name('setting.socialite.agency.credentials.status.update');
        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notifications')->group(function () {
        //Template Setting
        Route::get('global', 'global')->name('global');
        Route::post('global/update', 'globalUpdate')->name('global.update');
        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');
    });

    // Plugin
    Route::controller('ExtensionController')->group(function () {
        Route::get('extensions', 'index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'update')->name('extensions.update');
        Route::post('extensions/status/{id}', 'status')->name('extensions.status');
    });
    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');


    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->prefix('manage')->group(function () {
            Route::get('pages', 'managePages')->name('manage.pages');
            Route::post('pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('section/{id}', 'manageSection')->name('manage.section');
            Route::post('section/{id}', 'manageSectionUpdate')->name('manage.section.update');
        });
    });
});
