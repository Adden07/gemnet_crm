<?php

namespace App\Exceptions;

use Dotenv\Exception\ValidationException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable  $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }
        
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable  $exception)
    {
        if ($request->wantsJson()) {
            if ($exception instanceof ValidationException){
                return response()->json([
                    'message' => 'Validation error', 'errors' => $exception->validator->getMessageBag()
                ], 422); //type your error code.
            }
            
            if($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface){
                $code = $exception->getStatusCode() ?? 404;
            }else{
                $code = $exception->getCode() ?? 404;
            }

            $code = $code > 0 ? $code : 404;

            $message = $exception->getMessage();
            if($message == ''){
                if($code == 401){
                    $message = 'You are not authorized to perform this action.';
                }elseif($code == 404){
                    $message = 'The requested resource was not found.';
                }
            }

            return response([
                'error' => $message ?? 'Not Found'
            ], 400);
            
        }

        return parent::render($request, $exception);
    }
}
