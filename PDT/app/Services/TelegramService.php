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
     * Get current school period label based on Cambodia time.
     */
    public function getCurrentPeriodLabel(): string
    {
        $now = \Carbon\Carbon::now('Asia/Phnom_Penh');
        $time = $now->format('H:i');

        if ($time < '07:55') {
            return '07:00 - 07:50 (ម៉ោងទី១)';
        } elseif ($time < '09:00') {
            return '07:55 - 08:45 (ម៉ោងទី២)';
        } elseif ($time < '10:00') {
            return '09:05 - 09:55 (ម៉ោងទី៣)';
        } elseif ($time < '11:30') {
            return '10:00 - 10:50 (ម៉ោងទី៤)';
        } elseif ($time < '13:55') {
            return '13:00 - 13:50 (ម៉ោងទី៥)';
        } elseif ($time < '15:00') {
            return '13:55 - 14:45 (ម៉ោងទី៦)';
        } elseif ($time < '16:00') {
            return '15:05 - 15:55 (ម៉ោងទី៧)';
        } else {
            return '16:00 - 16:50 (ម៉ោងទី៨)';
        }
    }

    /**
     * Send student absence alert to parent.
     */
    public function sendAbsenceAlert(string|int $chatId, string $studentName, string $className, string $date, ?string $periodTime = null): bool
    {
        $period = $periodTime ?: $this->getCurrentPeriodLabel();
        $message =
            "🔔 <b>សេចក្តីជូនដំណឹងអំពីអវត្តមានសិស្ស</b>\n\n" .
            "🎓 សិស្ស៖ {$studentName}\n" .
            "🏫 ថ្នាក់៖ {$className}\n" .
            "📅 កាលបរិច្ឆេទ៖ {$date}\n" .
            "⏰ ម៉ោង៖ {$period}\n\n" .
            "⚠️ <b>ស្ថានភាព៖ បានអវត្តមាន</b>\n\n" .
            "សូមអាណាព្យាបាលជ្រាបជាព័ត៌មាន។\n\n" .
            "— <b>Portal Sala • ប្រព័ន្ធគ្រប់គ្រងសាលា</b>";

        return $this->sendMessage($chatId, $message);
    }

    /**
     * Send leave request status alert (Approved/Rejected) to parent.
     */
    public function sendLeaveStatusAlert(
        string|int $chatId,
        string $studentName,
        string $className,
        string $date,
        string $time,
        string $status,
        string $reason = ''
    ): bool {
        if ($status === 'approved') {
            $message =
                "🔔 <b>សេចក្តីជូនដំណឹងអំពីការសុំច្បាប់សិស្ស</b>\n\n" .
                "🎓 សិស្ស៖ {$studentName}\n" .
                "🏫 ថ្នាក់៖ {$className}\n" .
                "📅 កាលបរិច្ឆេទ៖ {$date}\n" .
                "⏰ ម៉ោង៖ {$time}\n\n" .
                "✅ <b>ស្ថានភាព៖ បានអនុម័តច្បាប់</b>\n\n" .
                "សូមអាណាព្យាបាលជ្រាបជាព័ត៌មាន។\n\n" .
                "— <b>Portal Sala • ប្រព័ន្ធគ្រប់គ្រងសាលា</b>";
        } else {
            $rejection = $reason ?: 'ពុំមានមូលហេតុគ្រប់គ្រាន់';
            $message =
                "🔔 <b>សេចក្តីជូនដំណឹងអំពីការសុំច្បាប់សិស្ស</b>\n\n" .
                "🎓 សិស្ស៖ {$studentName}\n" .
                "🏫 ថ្នាក់៖ {$className}\n" .
                "📅 កាលបរិច្ឆេទ៖ {$date}\n" .
                "⏰ ម៉ោង៖ {$time}\n\n" .
                "❌ <b>ស្ថានភាព៖ មិនត្រូវបានអនុម័ត</b>\n" .
                "⚠️ <b>មូលហេតុ៖</b> {$rejection}\n\n" .
                "សូមអាណាព្យាបាលជ្រាបជាព័ត៌មាន។\n\n" .
                "— <b>Portal Sala • ប្រព័ន្ធគ្រប់គ្រងសាលា</b>";
        }

        return $this->sendMessage($chatId, $message);
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
