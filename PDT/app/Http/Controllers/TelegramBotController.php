<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentLeaveRequest;
use App\Services\TelegramService;
use Carbon\Carbon;
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

        // Handle /status or /leaves command
        if (str_starts_with($text, '/status') || str_starts_with($text, '/leaves') || str_starts_with($text, '/check')) {
            $this->handleStatus($chatId);
            return;
        }

        // Handle /leave command (apply for leave)
        if (str_starts_with($text, '/leave') || str_starts_with($text, '/apply')) {
            $this->handleLeaveLink($chatId);
            return;
        }

        // Handle /help command
        if (str_starts_with($text, '/help')) {
            $this->handleHelp($chatId);
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
            "🔹 <code>/status</code> — ពិនិត្យព័ត៌មានកូនសិស្ស & ស្ថានភាពច្បាប់\n" .
            "🔹 <code>/leave</code> — បំពេញពាក្យសុំច្បាប់តាមអនឡាញ\n" .
            "🔹 <code>/register 012888999</code> — ភ្ជាប់លេខទូរស័ព្ទអាណាព្យាបាល\n" .
            "🔹 <code>/help</code> — មើលជំនួយ និងការណែនាំ"
        );
    }

    /**
     * Handle /start command.
     */
    protected function handleStart(int|string $chatId): void
    {
        // Check if already registered
        $students = Student::where('telegram_chat_id', (string) $chatId)->get();

        if ($students->isNotEmpty()) {
            $names = $students->pluck('display_name')->implode(', ');
            $this->telegram->sendMessage($chatId,
                "👋 <b>សួស្តី! អ្នកបានភ្ជាប់គណនីរួចរាល់ហើយ</b>\n\n" .
                "👨‍👧 អាណាព្យាបាលរបស់: <b>{$names}</b>\n\n" .
                "💡 ពាក្យបញ្ជាដែលមាន:\n" .
                "🔹 <code>/status</code> — ពិនិត្យស្ថានភាពកូនសិស្ស & ច្បាប់\n" .
                "🔹 <code>/leave</code> — ស្នើសុំច្បាប់ឈប់សម្រាក\n" .
                "🔹 <code>/help</code> — មើលបញ្ជីពាក្យបញ្ជាទាំងអស់\n\n" .
                "<i>រាល់ពេលលោកគ្រូ/អ្នកគ្រូអនុម័តច្បាប់ Bot នឹងជូនដំណឹងអ្នកភ្លាមៗ! ✅</i>"
            );
            return;
        }

        $this->telegram->sendMessage($chatId,
            "👋 <b>សូមស្វាគមន៍មកកាន់ Portal Sala Bot!</b>\n\n" .
            "Bot នេះនឹងផ្ញើការជូនដំណឹងទៅអាណាព្យាបាល នៅពេល:\n" .
            "✅ ការសុំច្បាប់សិស្សត្រូវបាន <b>អនុម័ត</b>\n" .
            "❌ ការសុំច្បាប់សិស្សត្រូវបាន <b>បដិសេធ</b>\n\n" .
            "━━━━━━━━━━━━━━━━━━\n" .
            "📋 <b>របៀបចុះឈ្មោះ:</b>\n\n" .
            "👉 គ្រាន់តែផ្ញើ <b>លេខទូរស័ព្ទអាណាព្យាបាល</b> របស់អ្នកមកកាន់ទីនេះ:\n" .
            "   (ឧ. <code>012888999</code> ឬ <code>/register 012888999</code>)\n\n" .
            "Bot នឹងភ្ជាប់គណនីរបស់អ្នកជាមួយកូនសិស្សដោយស្វ័យប្រវត្តិ! ✅"
        );
    }

    /**
     * Handle /help command.
     */
    protected function handleHelp(int|string $chatId): void
    {
        $this->telegram->sendMessage($chatId,
            "💡 <b>បញ្ជីពាក្យបញ្ជា (Commands):</b>\n\n" .
            "🔹 <code>/status</code> — ពិនិត្យព័ត៌មានកូនសិស្ស និងស្ថានភាពសុំច្បាប់\n" .
            "🔹 <code>/leave</code> — បំពេញពាក្យសុំច្បាប់តាមអនឡាញ\n" .
            "🔹 <code>/register 012888999</code> — ភ្ជាប់លេខទូរស័ព្ទអាណាព្យាបាល\n" .
            "🔹 <code>/help</code> — មើលជំនួយ និងការណែនាំ\n\n" .
            "— <i>Portal Sala • ប្រព័ន្ធគ្រប់គ្រងសាលារៀន</i>"
        );
    }

    /**
     * Handle /status or /leaves command.
     */
    protected function handleStatus(int|string $chatId): void
    {
        $students = Student::where('telegram_chat_id', (string) $chatId)->get();

        if ($students->isEmpty()) {
            $this->telegram->sendMessage($chatId,
                "⚠️ <b>គណនីរបស់អ្នកមិនទាន់បានភ្ជាប់ជាមួយសិស្សណាម្នាក់ឡើយ!</b>\n\n" .
                "សូមផ្ញើលេខទូរស័ព្ទរបស់អ្នកមកកាន់ Bot:\n" .
                "📱 ឧ. <code>012888999</code> ឬ <code>/register 012888999</code>"
            );
            return;
        }

        $msg = "📋 <b>ព័ត៌មានកូនសិស្ស & ស្ថានភាពច្បាប់:</b>\n\n";

        foreach ($students as $st) {
            $msg .= "🎓 <b>សិស្ស:</b> {$st->display_name}\n";
            $msg .= "🏫 <b>ថ្នាក់:</b> " . ($st->grade ? "ថ្នាក់ {$st->grade}" : 'មិនទាន់កំណត់') . "\n";
            $msg .= "🆔 <b>អត្តលេខ:</b> " . ($st->student_id_card ?: 'N/A') . "\n";

            // Recent 3 leave requests
            $leaves = StudentLeaveRequest::where('student_id', $st->id)
                ->orWhere('student_name', $st->name_khmer)
                ->latest()
                ->take(3)
                ->get();

            if ($leaves->isNotEmpty()) {
                $msg .= "📝 <b>សំណើសុំច្បាប់ចុងក្រោយ:</b>\n";
                foreach ($leaves as $l) {
                    $stBadge = match($l->status) {
                        'approved' => '✅ បានអនុម័ត',
                        'rejected' => '❌ បានបដិសេធ',
                        default    => '⏳ កំពុងរង់ចាំពិនិត្យ',
                    };
                    $dateFmt = Carbon::parse($l->start_date)->format('d/m/Y');
                    $daysTxt = $l->days_count > 1 ? "{$l->days_count} ថ្ងៃ" : "1 ថ្ងៃ";
                    $msg .= "  • {$dateFmt} ({$daysTxt}) — <b>{$stBadge}</b>\n";
                    if ($l->reason) {
                        $msg .= "    <i>មូលហេតុ: {$l->reason}</i>\n";
                    }
                }
            } else {
                $msg .= "📝 ពុំទាន់មានសំណើសុំច្បាប់ណាមួយឡើយ\n";
            }
            $msg .= "━━━━━━━━━━━━━━━━━━\n";
        }

        $msg .= "\n🔗 ស្នើសុំច្បាប់ថ្មី: <code>/leave</code>";

        $this->telegram->sendMessage($chatId, $msg);
    }

    /**
     * Handle /leave command.
     */
    protected function handleLeaveLink(int|string $chatId): void
    {
        $leaveUrl = url('/leave-request');
        $this->telegram->sendMessage($chatId,
            "📝 <b>ពាក្យសុំច្បាប់ឈប់សម្រាកតាមអនឡាញ</b>\n\n" .
            "លោកអ្នកអាចចុចលើលីងខាងក្រោមដើម្បីបំពេញពាក្យសុំច្បាប់ឱ្យកូនសិស្ស:\n\n" .
            "👉 <a href=\"{$leaveUrl}\">ចុចទីនេះដើម្បីស្នើសុំច្បាប់ ({$leaveUrl})</a>\n\n" .
            "នៅពេលលោកគ្រូ/អ្នកគ្រូអនុម័ត Bot នឹងផ្ញើសារជូនដំណឹងភ្លាមៗ! ✅"
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
