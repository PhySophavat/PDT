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
            'telegrambot',
            'facebookexternalhit',
            'twitterbot',
            'whatsapp',
            'linkedinbot',
            'slackbot',
            'skypeuripreview',
            'discordbot',
            'googlebot',
            'bingbot',
        ];

        $isBot = false;
        foreach ($bots as $bot) {
            if (str_contains($ua, $bot)) {
                $isBot = true;
                break;
            }
        }

        if ($isBot && $request->isMethod('GET') && !$request->is('api/*')) {
            // Render SPA view with dynamic Open Graph tags for the crawler
            return response()->view('spa');
        }

        return $next($request);
    }
}
