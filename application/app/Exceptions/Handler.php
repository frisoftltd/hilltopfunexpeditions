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
     * A 419 on any admin/agency form loses whatever the user typed in -
     * worst on the long ones, but there's no reason to special-case a
     * handful of routes when every admin/agency POST/PUT/PATCH/DELETE can
     * flash its input back the same way. Root cause of the original
     * session-expiry reports was config/app.php's timezone (Africa/Kigali)
     * disagreeing with the host's filesystem clock, which the file session
     * driver's filemtime()-based expiry check is exposed to - fixed by
     * switching SESSION_DRIVER to database (see the create_sessions_table
     * migration), which never touches the filesystem clock. This handler is
     * the backstop for whatever still slips through - a genuinely idle
     * session, a clock hiccup elsewhere, etc.
     */
    private const RECOVERABLE_419_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

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

        if (!$routeName || !in_array($request->method(), self::RECOVERABLE_419_METHODS, true)) {
            return false;
        }

        return str_starts_with($routeName, 'admin.') || str_starts_with($routeName, 'agency.');
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
