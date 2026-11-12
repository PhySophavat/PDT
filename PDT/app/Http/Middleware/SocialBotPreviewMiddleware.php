<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SocialBotPreviewMiddleware
{
    /**
     * Handle an incoming request.
     * Serve full Open Graph HTML to social media crawlers without auth redirects.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ua = strtolower((string) $request->header('User-Agent'));
        $bots = [
            'telegram',
            'telegrambot',
            'facebookexternalhit',
            'facebot',
            'twitterbot',
            'whatsapp',
            'linkedinbot',
            'slackbot',
            'skypeuripreview',
            'discordbot',
            'googlebot',
            'bingbot',
            'applebot',
            'viber',
            'vkshare',
            'pinterest',
        ];

        $isBot = false;
        foreach ($bots as $bot) {
            if (str_contains($ua, $bot)) {
                $isBot = true;
                break;
            }
        }

        if ($isBot && ($request->isMethod('GET') || $request->isMethod('HEAD')) && !$request->is('api/*') && !$request->is('portal-sala-access-2026*') && !$request->is('portal-sala-register-2026*')) {
            if ($request->isMethod('HEAD')) {
                return response('', 200, ['Content-Type' => 'text/html; charset=utf-8']);
            }

            // For /portal, /login, / → serve static portal.html OG page instantly (no DB)
            if ($request->is('portal') || $request->is('login') || $request->is('/') || $request->is('')) {
                $htmlPath = public_path('portal.html');
                if (file_exists($htmlPath)) {
                    return response(file_get_contents($htmlPath), 200, [
                        'Content-Type' => 'text/html; charset=UTF-8',
                        'Cache-Control' => 'public, max-age=3600, s-maxage=3600',
                        'X-Robots-Tag' => 'index, follow',
                    ]);
                }
            }

            // Render SPA view with dynamic Open Graph tags for the crawler
            return response()->view('spa');
        }

        return $next($request);
    }
}

