<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/course-subjects', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();
echo "Head snippet:\n";
if (preg_match_all('/<script.*?>.*?<\/script>|<link.*?>/is', $content, $matches)) {
    foreach ($matches[0] as $tag) {
        echo $tag . "\n";
    }
}
