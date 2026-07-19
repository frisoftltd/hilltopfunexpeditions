<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Order;
use App\Models\Agency;
use App\Lib\Searchable;
use App\Models\Artwork;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Withdrawal;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\SupportTicket;
use App\Models\AdminNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $general = gs();
        $activeTemplate = activeTemplate();
        $viewShare['general'] = $general;
        $viewShare['activeTemplate'] = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language'] = Language::all();
        $viewShare['emptyMessage'] = 'No data';
        view()->share($viewShare);


        view()->composer('admin.components.tabs.user', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'   => User::kycUnverified()->count(),
                'kycPendingUsersCount'   => User::kycPending()->count(),
            ]);
        });
        view()->composer('admin.components.tabs.agency', function ($view) {
            $view->with([
                'bannedAgenciesCount'           => Agency::banned()->count(),
                'emailUnverifiedAgenciesCount' => Agency::emailUnverified()->count(),
                'mobileUnverifiedAgenciesCount'   => Agency::mobileUnverified()->count(),
                'kycUnverifiedAgenciesCount'   => Agency::kycUnverified()->count(),
                'kycPendingAgenciesCount'   => Agency::kycPending()->count(),
            ]);
        });

        view()->composer('admin.components.tabs.tour_package', function ($view) {
            $view->with([
                'allTourPackages'      => TourPackage::count(),
                'myTourPackages'      =>  TourPackage::where('user_type','admin')->count(),
                'allAgencyTourPackages'      => TourPackage::where('user_type','agency')->count(),
                'pendingTourPackages' => TourPackage::pending()->count(),
          
            ]);
        });
        view()->composer('admin.components.tabs.deposit', function ($view) {
            $view->with([
                'pendingDepositsCount'    => Deposit::pending()->count(),
            ]);
        });
        view()->composer('admin.components.tabs.withdrawal', function ($view) {
            $view->with([
                'pendingWithdrawCount'    => Withdrawal::pending()->count(),
            ]);
        });
        view()->composer('admin.components.tabs.ticket', function ($view) {
            $view->with([
                'pendingTicketCount'         => SupportTicket::where('user_id', '!=', 0)->whereIN('status', [0, 2])->count(),
            ]);
        });
        view()->composer('admin.components.tabs.agency_ticket', function ($view) {
            $view->with([
                'pendingTicketCount'    =>  SupportTicket::where('agency_id', '!=', 0)->whereIN('status', [0, 2])->count(),
            ]);
        });
      
        view()->composer('admin.components.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'   => User::kycUnverified()->count(),
                'kycPendingUsersCount'   => User::kycPending()->count(),
                'pendingTicketCount'         => SupportTicket::where('user_id', '!=', 0)->whereIN('status', [0,2])->count(),
                'agencyPendingTicketCount'  => SupportTicket::where('agency_id', '!=', 0)->whereIN('status', [0, 2])->count(),
                'pendingDepositsCount'    => Deposit::pending()->count(),
                'pendingWithdrawCount'    => Withdrawal::pending()->count(),

                'agencyBannedUsersCount'           => Agency::banned()->count(),
                'agencyEmailUnverifiedUsersCount' => Agency::emailUnverified()->count(),
                'agencyMobileUnverifiedUsersCount'   => Agency::mobileUnverified()->count(),
                'agencyKycUnverifiedUsersCount'   => Agency::kycUnverified()->count(),
                'agencyKycPendingUsersCount'   => Agency::kycPending()->count(),
                'pendingTourPackages' => TourPackage::pending()->count()
               
            ]);
        });

        view()->composer('admin.components.topnav', function ($view) {
            $view->with([
                'adminNotifications'=>AdminNotification::where('read_status',0)->with('user')->orderBy('id','desc')->take(10)->get(),
                'adminNotificationCount'=>AdminNotification::where('read_status',0)->count(),
            ]);
        });

        view()->composer('includes.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if($general->force_ssl){
            \URL::forceScheme('https');
        }


        Paginator::useBootstrapFour();
    }
}
