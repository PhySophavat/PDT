<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN') ?: '9017746921:AAF9NDR8pMiYZkb201t9HG4QgeU3DVeyXV4';
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send a text message to a Telegram chat_id.
     */
    public function sendMessage(string|int $chatId, string $text, string $parseMode = 'HTML'): bool
    {
        if (empty($this->token)) {
            Log::warning('TelegramService: TELEGRAM_BOT_TOKEN is not set.');
            return false;
        }

        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => $parseMode,
            ]);

            if (!$response->successful()) {
                Log::error('TelegramService sendMessage failed', [
                    'chat_id'  => $chatId,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('TelegramService exception', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Set webhook URL for the bot.
     */
    public function setWebhook(string $url): array
    {
        $response = Http::post("{$this->apiUrl}/setWebhook", ['url' => $url]);
        return $response->json();
    }

    /**
     * Delete webhook (for polling mode).
     */
    public function deleteWebhook(): array
    {
        $response = Http::post("{$this->apiUrl}/deleteWebhook");
        return $response->json();
    }

    /**
     * Get updates (polling mode).
     */
    public function getUpdates(int $offset = 0, int $timeout = 10): array
    {
        try {
            $response = Http::timeout($timeout + 5)->get("{$this->apiUrl}/getUpdates", [
                'offset'  => $offset,
                'timeout' => $timeout,
            ]);
            return $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::error('TelegramService getUpdates error', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get bot info.
     */
    public function getMe(): array
    {
        $response = Http::get("{$this->apiUrl}/getMe");
        return $response->json();
    }
}
