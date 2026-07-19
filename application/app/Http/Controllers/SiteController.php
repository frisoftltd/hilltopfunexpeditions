<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Page;
use App\Models\Agency;
use App\Models\Artwork;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Collection;
use App\Models\Subscriber;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function index()
    {

        $pageTitle = 'Home';
        $sections = Page::where('tempname', $this->activeTemplate)->where('slug', '/')->first();

        // check all tour package running or expired
        $allTourPackages = TourPackage::whereIn('status', [1, 2, 3])->get();
        foreach ($allTourPackages ?? [] as $key => $value) {
            if ($value->tour_start < now() && $value->tour_end > now()) {
                $value->status = 2;
                $value->save();
            }
            if ($value->tour_end < now()) {
                $value->status = 3;
                $value->save();
            }
        }
        return view($this->activeTemplate . 'home', compact('pageTitle', 'sections'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', $slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle', 'sections'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact', compact('pageTitle'));
    }

    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function placeholderImage($size = null)
    {
        $imgWidth = (int) explode('x', $size)[0];
        $imgHeight = (int) explode('x', $size)[1];
        if ($imgWidth <= 0 || $imgWidth > 2000) { $imgWidth = 300; }
        if ($imgHeight <= 0 || $imgHeight > 2000) { $imgHeight = 200; }
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 255, 255, 255);
        $bgFill    = imagecolorallocate($image, 28, 35, 47);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function tourPackageDetails($id, $slug)
    {
        $pageTitle = 'Tour Details';
        $tourPackage = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images'])->findOrFail($id);
        $tourPackage->view += 1;
        $tourPackage->save();

        $tourPackages = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images', 'TourPackagePrimaryImage'])
            ->where('id', '!=', $id)
            ->inRandomOrder()
            ->limit(3)
            ->get();
        return view($this->activeTemplate . 'tour-package.tour_package_details', compact('pageTitle', 'tourPackage', 'tourPackages'));
    }

    public function tourPackageList()
    {
        $pageTitle = 'Choose Your Tour';
        $categories = Category::where('status', 1)
            ->whereHas('tour_packages', function ($query) {
                $query->whereIn('status',[1,2,3]);
            })
            ->with(['tour_packages' => function ($query) {
                $query->whereIn('status',[1,2,3]);
            }])
            ->get();
        $query = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images', 'TourPackagePrimaryImage'])
            ->whereIn('status',[1,2,3]);
        $page = Page::where('tempname', $this->activeTemplate)->where('slug', 'browse')->first();
        $sections = $page->secs;
        $locationSearch = request()->query('location');
        $categorySearch = request()->query('category_id');
        $dateSearch = request()->query('start_date');
        $personSearch = request()->query('person');
        $locationIdSearch = request()->query('location_id');
        $inputLatitude = request()->query('lati');
        $inputLongitude = request()->query('longi');
        if ($locationSearch || $categorySearch || $dateSearch || $personSearch || $locationIdSearch || ($inputLatitude && $inputLongitude)) {
            if ($locationSearch) {
                $query->where('address', 'LIKE', "%{$locationSearch}%")->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($categorySearch) {
                $query->where('category_id', $categorySearch)->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($dateSearch) {
                $carbonDate = Carbon::parse($dateSearch)->format('Y-m-d');
                $query->where('tour_start', '<', $carbonDate)->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($personSearch) {
                $query->where('person_capability', $personSearch)->whereColumn('booking_person', '<=', 'person_capability');
            }

            if ($inputLatitude && $inputLongitude) {

                $query->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$inputLatitude, $inputLongitude, $inputLatitude])
                    ->havingRaw('distance < ?', [100]);
            }

            $tourPackages = $query->orderBy('id', 'desc')->paginate(getPaginate(9));
        } else {
            switch (request()->query('search')) {

                case 'new':
                    $tourPackages = $query->latest()->paginate(getPaginate(9));
                    break;

                case 'rating':
                    $tourPackages = $query->orderBy('average_rating', 'desc')->paginate(getPaginate(9));
                    break;

                case 'trending':
                    $tourPackages = $query->whereHas(
                        'tour_bookings',
                        fn($query) =>
                        $query->where('created_at', '>=', now()->subDays(30))
                    )->withCount('tour_bookings')->orderByDesc('tour_bookings_count', 'desc')->paginate(9);
                    break;

                default:
                    $tourPackages = $query->latest()->paginate(9);
                    break;
            }
        }

        return view($this->activeTemplate . 'tour-package.tour_package_list', compact('pageTitle', 'tourPackages', 'categories', 'sections'));
    }

    public function tourPackageSideFilter(Request $request)
    {

        $pageTitle = 'Searching';
        $searchKey = $request->input('search');
        $star = $request->input('star');


        $categoryId = $request->input('categoryId');

        $priceMin = $request->input('priceMin');
        $priceMax = $request->input('priceMax');

        $query = TourPackage::with(['reviews', 'reviews.user', 'wishlists', 'tour_package_images', 'TourPackagePrimaryImage'])->whereIn('status', [1,2,3])->orderBy('id', 'desc')->limit(9)->latest();
        if (!empty($searchKey) && strlen($searchKey) >= 2) {
            $query->where('title', 'LIKE', "%{$searchKey}%");
        }
        if (!empty($star)) {
            $query->whereIn('average_rating', $star);
        }
        if (!empty($categoryId)) {
            $query->whereIn('category_id', $categoryId);

        }

        if (!empty($priceMin) && !empty($priceMax)) {

            $query->whereBetween('price', [$priceMin, $priceMax]);
        }

        $tourPackages = $query->get();

        $view = view($this->activeTemplate . 'components.single_tour_package', compact('tourPackages', 'pageTitle'))->render();
        return response()->json([
            'html' => $view
        ]);
    }

    public function policyPages($slug, $id)
    {
        $policy = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        return view($this->activeTemplate . 'policy', compact('policy', 'pageTitle'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language)
            $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function blog(Request $request)
    {
        $pageTitle = 'Blog';
        $blogs = Frontend::where('data_keys', 'blog.element')
            ->when($request->search, function ($query) use ($request) {
                $search = strtolower($request->search);
                $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(data_values, '$.title'))) LIKE ?", ["%$search%"]);
            })
            ->orderBy('id', 'desc')
            ->paginate(getPaginate(9));

        $sections = Page::where('tempname', $this->activeTemplate)->where('slug', 'blog')->first();

        return view($this->activeTemplate . 'blog', compact('pageTitle', 'blogs', 'sections'));
    }

    public function blogDetails($slug, $id)
    {
        $blog = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle = 'Blog Details';
        $latests = Frontend::where('data_keys', 'blog.element')->orderBy('id', 'desc')->limit(5)->get();
        return view($this->activeTemplate . 'blog_details', compact('blog', 'pageTitle', 'latests'));
    }
    public function cookieAccept()
    {
        $general = gs();
        Cookie::queue('gdpr_cookie', $general->site_name, 43200);
        return back();
    }
    public function cookiePolicy()
    {
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    public function sellerCollectionFilter(Request $request)
    {
        $pageTitle = 'Seller Collection Filter';
        $agency = Agency::findOrFail($request->agencyId);
        $collections = Collection::active()->with(['artworks', 'agency'])->where('agency_id', $agency->id)->latest()->get();
        $view = view($this->activeTemplate . 'components.collection', compact('collections', 'pageTitle'))->render();
        return response()->json([
            'html' => $view
        ]);
    }



    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:subscribers',
        ]);
        $subscribe = new Subscriber();
        $subscribe->email = $request->email;
        $subscribe->save();
        $notify[] = ['success', 'You have successfully subscribed to the Newsletter'];
        return back()->withNotify($notify);
    }
}
