<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    protected TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Handle incoming Telegram webhook updates.
     * POST /api/telegram/webhook
     */
    public function webhook(Request $request)
    {
        $this->processUpdate($request->all());
        return response()->json(['ok' => true]);
    }

    /**
     * Process an incoming update from Telegram (used by both webhook and polling command).
     */
    public function processUpdate(array $update): void
    {
        Log::info('TelegramBot update received', $update);

        // Handle regular text messages
        $message = $update['message'] ?? $update['edited_message'] ?? null;

        if (!$message) {
            return;
        }

        $chatId   = $message['chat']['id'] ?? null;
        $text     = trim($message['text'] ?? '');
        $contact  = $message['contact'] ?? null;

        if (!$chatId) {
            return;
        }

        // Handle /start command (supports deep-linking: /start reg_012888999 or /start 012888999)
        if (str_starts_with($text, '/start')) {
            $payload = trim(substr($text, 6)); // strip '/start'
            if (!empty($payload)) {
                $phone = str_ireplace('reg_', '', $payload);
                $cleanPhone = preg_replace('/[\s\-\.\(\)]/', '', $phone);
                if (!empty($cleanPhone)) {
                    $this->handleRegister($chatId, $cleanPhone);
                    return;
                }
            }
            $this->handleStart($chatId);
            return;
        }

        // Handle /register <phone> command
        if (str_starts_with($text, '/register')) {
            $phone = trim(str_replace('/register', '', $text));
            $this->handleRegister($chatId, $phone);
            return;
        }

        // Handle shared contact (Telegram contact share button)
        if ($contact) {
            $phone = $contact['phone_number'] ?? '';
            // Normalize: remove + prefix, add leading 0 if needed
            $phone = ltrim($phone, '+');
            if (str_starts_with($phone, '855')) {
                $phone = '0' . substr($phone, 3);
            }
            $this->handleRegister($chatId, $phone);
            return;
        }

        // Handle plain phone number text (e.g. 012888999 or 012 888 999)
        $cleanPhone = preg_replace('/[\s\-\.\(\)]/', '', $text);
        if (preg_match('/^0\d{8,9}$/', $cleanPhone) || preg_match('/^855\d{8,9}$/', $cleanPhone)) {
            if (str_starts_with($cleanPhone, '855')) {
                $cleanPhone = '0' . substr($cleanPhone, 3);
            }
            $this->handleRegister($chatId, $cleanPhone);
            return;
        }

        // Default response
        $this->telegram->sendMessage($chatId,
            "🤖 <b>Portal Sala Bot (@SalaParentAlertBot)</b>\n\n" .
            "ដើម្បីចុះឈ្មោះទទួលការជូនដំណឹង សូមផ្ញើ:\n\n" .
            "📱 លេខទូរស័ព្ទអាណាព្យាបាល (ឧ. <code>012888999</code>)\n" .
            "<i>ឬ</i> ប្រើពាក្យបញ្ជា: <code>/register 012888999</code>\n\n" .
            "សូមប្រើលេខដូចគ្នាទៅនឹងលេខដែលបានបញ្ចូលពេលចុះឈ្មោះកូនចូលរៀន។"
        );
    }

    /**
     * Handle /start command.
     */
    protected function handleStart(int|string $chatId): void
    {
        $botName = config('services.telegram.bot_username', 'PortalSalaBot');

        $this->telegram->sendMessage($chatId,
            "👋 <b>សូមស្វាគមន៍មក Portal Sala Bot!</b>\n\n" .
            "Bot នេះនឹងផ្ញើការជូនដំណឹងទៅអាណាព្យាបាល នៅពេល:\n" .
            "✅ ការសុំច្បាប់សិស្សត្រូវបាន <b>អនុម័ត</b>\n" .
            "❌ ការសុំច្បាប់សិស្សត្រូវបាន <b>បដិសេធ</b>\n\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "📋 <b>របៀបចុះឈ្មោះ:</b>\n\n" .
            "1️⃣ ផ្ញើលេខទូរស័ព្ទអាណាព្យាបាល (ដូចគ្នាបេះបិទនឹងពេលចុះឈ្មោះ):\n" .
            "   <code>/register 012888999</code>\n\n" .
            "   <i>ឬ</i> ចុចប៊ូតុង «ចែករំលែកលេខទូរស័ព្ទ» ខាងក្រោម\n\n" .
            "2️⃣ ពេល Link ទទួលជោគជ័យ Bot នឹងជូនដំណឹងអ្នក ✅"
        );
    }

    /**
     * Handle phone number registration — link chat_id to student record.
     */
    protected function handleRegister(int|string $chatId, string $phone): void
    {
        $phone = preg_replace('/[\s\-\.\(\)]/', '', $phone);

        if (empty($phone)) {
            $this->telegram->sendMessage($chatId,
                "⚠️ លេខទូរស័ព្ទមិនត្រឹមត្រូវ!\n\nសូម​ប្រើ: <code>/register 012888999</code>"
            );
            return;
        }

        // Find student(s) with this parent_phone
        $students = Student::where('parent_phone', $phone)
            ->orWhere('mother_phone', $phone)
            ->orWhere('father_phone', $phone)
            ->orWhere('emergency_contact', $phone)
            ->orWhere('phone', $phone)
            ->get();

        if ($students->isEmpty()) {
            // Also check SchoolParent table
            $parents = \App\Models\SchoolParent::where('phone', $phone)->get();
            foreach ($parents as $p) {
                $students = $students->merge($p->students);
            }
        }

        if ($students->isEmpty()) {
            // Try with 855 prefix variant
            $phoneAlt = '';
            if (str_starts_with($phone, '0')) {
                $phoneAlt = '855' . substr($phone, 1);
            } elseif (str_starts_with($phone, '855')) {
                $phoneAlt = '0' . substr($phone, 3);
            }
            if ($phoneAlt) {
                $students = Student::where('parent_phone', $phoneAlt)
                    ->orWhere('mother_phone', $phoneAlt)
                    ->orWhere('father_phone', $phoneAlt)
                    ->orWhere('emergency_contact', $phoneAlt)
                    ->orWhere('phone', $phoneAlt)
                    ->get();

                if ($students->isEmpty()) {
                    $parents = \App\Models\SchoolParent::where('phone', $phoneAlt)->get();
                    foreach ($parents as $p) {
                        $students = $students->merge($p->students);
                    }
                }
            }
        }

        if ($students->isEmpty()) {
            $this->telegram->sendMessage($chatId,
                "❌ <b>រកមិនឃើញ</b> ទិន្នន័យសិស្ស ដែលប្រើលេខ <code>{$phone}</code>\n\n" .
                "📌 សូមប្រាកដថា:\n" .
                "• លេខទូរស័ព្ទ <b>ដូចគ្នាបេះបិទ</b> នឹងពេលចុះឈ្មោះ\n" .
                "• ប្រើទម្រង់: <code>012888999</code> (ដោយគ្មាន +855)\n\n" .
                "ប្រសិនបើបញ្ហានៅតែមាន សូមទំនាក់ទំនងអ្នកគ្រប់គ្រង"
            );
            return;
        }

        // Update telegram_chat_id for all matching students
        $count = $students->count();
        foreach ($students as $student) {
            $student->telegram_chat_id = (string) $chatId;
            $student->telegram_verified_at = now();
            $student->save();
        }

        $names = $students->pluck('display_name')->implode(', ');

        $this->telegram->sendMessage($chatId,
            "✅ <b>ចុះឈ្មោះទទួលជោគជ័យ!</b>\n\n" .
            "👨‍👧 អ្នកជា<b>អាណាព្យាបាល</b>របស់:\n" .
            "🎓 <b>{$names}</b>\n\n" .
            "ពីនេះទៅ Bot នឹងផ្ញើជូនអ្នកនៅពេល:\n" .
            "✅ ការសុំច្បាប់ <b>ត្រូវបានអនុម័ត</b>\n" .
            "❌ ការសុំច្បាប់ <b>ត្រូវបានបដិសេធ</b>\n\n" .
            "🔔 ប្រព័ន្ធចូលប្រើហើយ — <i>Portal Sala</i>"
        );
    }

    /**
     * Set Telegram webhook URL (admin only).
     * GET /api/telegram/set-webhook?url=https://...
     */
    public function setWebhook(Request $request)
    {
        $url = $request->input('url', url('/api/telegram/webhook'));
        $result = $this->telegram->setWebhook($url);
        return response()->json($result);
    }

    /**
     * Get bot info (admin diagnostic).
     * GET /api/telegram/me
     */
    public function getMe()
    {
        $result = $this->telegram->getMe();
        return response()->json($result);
    }
}
