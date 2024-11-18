<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecaptchaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $recaptchaResponse = $request->input('g-recaptcha-response');
        $secretKey = env('RECAPTCHA_SECRET_KEY');

        $response = Http::post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
        ]);

        $recaptchaResult = $response->json();

        if (!isset($recaptchaResult['success']) || !$recaptchaResult['success']) {
            return response()->json([
                'message' => 'Invalid reCAPTCHA verification.',
                'errors' => $recaptchaResult,
            ], 422);
        }

        return $next($request);
    }
}