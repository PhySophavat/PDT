<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramBotController;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramPollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:poll {--once : Run once and exit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll Telegram Bot updates for local development and testing';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram, TelegramBotController $controller): int
    {
        $this->info("🤖 Starting Telegram Long Polling for @SalaParentAlertBot...");
        $this->info("Press Ctrl+C to stop.\n");

        // First remove any existing webhook so polling is permitted by Telegram
        $del = $telegram->deleteWebhook();
        if (!($del['ok'] ?? false)) {
            $this->warn("⚠️  Could not remove webhook: " . json_encode($del));
        } else {
            $this->info("✅ Webhook cleared. Ready for polling updates.");
        }

        $offset = 0;
        $runOnce = $this->option('once');

        while (true) {
            try {
                $response = $telegram->getUpdates($offset, 15);

                if (!empty($response['ok']) && !empty($response['result'])) {
                    foreach ($response['result'] as $update) {
                        $updateId = $update['update_id'];
                        $offset = $updateId + 1;

                        $sender = $update['message']['from']['first_name'] ?? 'Unknown';
                        $text = $update['message']['text'] ?? '[Media/Contact]';
                        $this->line("📩 <fg=cyan>[{$sender}]:</> {$text}");

                        $controller->processUpdate($update);
                    }
                }
            } catch (\Throwable $e) {
                $this->error("Polling error: " . $e->getMessage());
                sleep(2);
            }

            if ($runOnce) {
                break;
            }

            usleep(500000); // 0.5s pause
        }

        return Command::SUCCESS;
    }
}
