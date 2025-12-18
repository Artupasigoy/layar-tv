<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$job = Illuminate\Support\Facades\DB::table('failed_jobs')->orderBy('id', 'desc')->first();
if ($job) {
    echo "Failed Job ID: " . $job->id . "\n";
    echo "Exception:\n" . substr($job->exception, 0, 2000) . "\n";
} else {
    echo "No failed jobs found.\n";
}
