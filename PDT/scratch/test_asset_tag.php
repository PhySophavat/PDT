<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::first();
Auth::login($user);

$request = Illuminate\Http\Request::create('/course-subjects', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
$html = $response->getContent();

preg_match_all('/<script.*?>.*?<\/script>|<link.*?>/is', $html, $matches);
foreach ($matches[0] as $tag) {
    if (str_contains($tag, 'app-') || str_contains($tag, 'vite')) {
        echo "Asset Tag: " . $tag . "\n";
    }
}

