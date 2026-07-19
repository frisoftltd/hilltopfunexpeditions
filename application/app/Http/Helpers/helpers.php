<?php

use Carbon\Carbon;
use App\Lib\Captcha;
use App\Models\User;
use App\Notify\Notify;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Frontend;
use App\Models\Refferal;
use App\Models\Extension;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\CommissionLog;
use App\Models\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0)
        return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function sysInfo()
{
    $system['name'] = 'Travela';
    $system['version'] = '1.0.0';
    $system['build_version'] = '1.1.3';
    $system['admin_version'] = '10.3.0';
    return $system;
}

function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset)
        return 'assets/presets/' . $template . '/';
    return 'presets.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $analytics = Extension::where('act', $key)->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function isWishlist($tourPackage = null)
{
    if (!auth()->check()) {
        return '<i class="far fa-heart"></i>';
    }

    $userId = auth()->user()->id;

    if ($tourPackage && $tourPackage->wishlists->where('user_id', $userId)->where('tour_package_id', $tourPackage->id)->isNotEmpty()) {
        return '<i class="fas fa-heart text--base"></i>';
    }

    return '<i class="far fa-heart"></i>';
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.wstacks.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections/builder/builder.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null)
{
    $clean = '';
    try {
        $absolutePath = realpath($image);
        if ($absolutePath && is_readable($absolutePath) && !is_dir($absolutePath)) {
            return asset($image) . $clean;
        }
    } catch (\Exception $e) {
        return asset('assets/images/general/default.png');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/general/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = GeneralSetting::first();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = getColumnName($user);
    $notify->send();
}

function getColumnName($user)
{
    $array = explode("\\", get_class($user));
    return strtolower(end($array)) . '_id';
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3)
        $class = 'side-menu--open';
    elseif ($type == 2)
        $class = 'sidebar-submenu__open';
    else
        $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value))
                return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            if (request()->route($param[0]) == $param[1])
                return $class;
            else
                return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $watermark = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->watermark = $watermark;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function getFileThumbSize($key)
{
    return fileManager()->$key()->thumb;
}
function getFileWatermarkSize($key)
{
    return fileManager()->$key()->watermark;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'M d, Y - h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"]; // Default to REMOTE_ADDR

    // Check for various headers and validate IPs
    if (isset($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (isset($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    // Convert IPv6 localhost (::1) to IPv4 localhost (127.0.0.1)
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}



function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs()
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    return $general;
}


function agency()
{
    return auth()->guard('agency')->user();
}
function agencyId()
{
    return auth()->guard('agency')->id();
}

function isActiveRoute($routes)
{
    return Str::startsWith(Route::currentRouteName(), $routes);
}

function discountShowAmount($amount)
{
    if (fmod($amount, 1) == 0.0) {
        $finalAmount = (int) $amount;
    } else {
        $finalAmount = $amount;
    }
    return $finalAmount;
}

function showRatings($rating)
{

    $ratings = '';
    if ($rating > 0) {
        $avgRating = $rating;
        $integerVal = floor($avgRating);
        $fraction = $avgRating - $integerVal;

        if ($fraction < .25) {
            $avgRating = intval($avgRating);
        }
        if ($fraction > .75) {
            $avgRating = intval($avgRating) + 1;
        }
        for ($i = 1; $i <= $avgRating; $i++) {
            $ratings .= '<i class="fas fa-star"></i>';
        }
        if ($fraction > .25 && $fraction < .75) {
            $avgRating += 1;
            $ratings .= '<i class="fas fa-star-half-alt"></i>';
        }
    } else {
        $avgRating = 0;
    }
    $nonStar = 5 - intval($avgRating);
    for ($k = 1; $k <= $nonStar; $k++) {
        $ratings .= '<i class="far fa-star"></i>';
    }
    return $ratings;
}


function calculateIndividualRating($averageRating)
{
    if (empty($averageRating)) {
        return '';
    }
    $fullStars = floor($averageRating);

    $halfStar = ceil($averageRating - $fullStars);
    $emptyStars = 5 - $fullStars - $halfStar;
    $ratingHtml = '';
    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $ratingHtml .= '<li>';
        $ratingHtml .= '<i class="fas fa-star"></i>';
        $ratingHtml .= '</li>';
    }
    // Half star
    if ($halfStar > 0) {
        $ratingHtml .= '<li>';
        $ratingHtml .= '<i class="fas fa-star-half-alt"></i>';
        $ratingHtml .= '</li>';
    }
    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $ratingHtml .= '<li>';
        $ratingHtml .= '<i class="far fa-star"></i>';
        $ratingHtml .= '</li>';
    }
    return $ratingHtml;
}

function showTourPackageCalculateDiscount($mainPrice,$discountPrice){

    $totalPrice = $mainPrice - ($mainPrice / 100) * $discountPrice ?? 1;
    return $totalPrice;
}

function iconCheck($icon)
{
    $iconHtml = $icon;
    $contains =  Str::contains($iconHtml, 'times');
    if($contains){
        $iconHtml = preg_replace_callback('/class="([^"]+)"/', function ($matches) {
            return 'class="' . $matches[1] . ' text--danger"';
        }, $iconHtml);
    }else{
        $iconHtml = preg_replace_callback('/class="([^"]+)"/', function ($matches) {
            return 'class="' . $matches[1] . ' text--success"';
        }, $iconHtml);
    }
    return $iconHtml;
}

function tourVacationCount($start_date, $end_date)
{
    $startDate = Carbon::parse($start_date);
    $endDate = Carbon::parse($end_date);
    $tourVacationDate = $startDate->diffInDays($endDate);
    return $tourVacationDate;
}

function getFollower()
{
    if (Auth::check()) {
        return Auth::user();
    } elseif (Auth::guard('agency')->check()) {
        return Auth::guard('agency')->user();
    }
    return null;
}


