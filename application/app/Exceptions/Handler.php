<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Route names of the long tour package forms - a 419 on these loses a lot
     * of typed-in work, so it gets its own recovery instead of the blank
     * "Page Expired" page. See the keep-alive ping in
     * App\Traits\TourService::keepAlive() for the primary fix; this is the
     * backstop for whatever slips past it.
     *
     * @var array<int, string>
     */
    private const RECOVERABLE_419_ROUTES = [
        'admin.tour.package.store',
        'admin.tour.package.update',
        'agency.tour.package.store',
        'agency.tour.package.update',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @return mixed
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException && $this->isRecoverable419($request)) {
            return $this->recoverFrom419($request);
        }

        return parent::render($request, $e);
    }

    private function isRecoverable419(Request $request): bool
    {
        $routeName = optional($request->route())->getName();

        return in_array($routeName, self::RECOVERABLE_419_ROUTES, true);
    }

    private function recoverFrom419(Request $request)
    {
        $notify[] = ['error', 'Your session was refreshed for security. Please review your entries and submit again.'];

        //input(), not all()/except() on the full request - a multipart form's
        //uploaded files live in all() too, and those can't be session-flashed
        return redirect()
            ->back()
            ->withInput(Arr::except($request->input(), $this->dontFlash))
            ->withNotify($notify);
    }
}
