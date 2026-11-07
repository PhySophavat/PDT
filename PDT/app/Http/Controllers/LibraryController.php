<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookBorrow;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'books');
        $search = trim((string) $request->input('search', ''));

        $books = Book::with('category')
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10, ['*'], 'books_page');

        $borrows = BookBorrow::with('book', 'student.user')
            ->latest()
            ->paginate(10, ['*'], 'borrows_page');

        $categories = BookCategory::all();
        $students = Student::with('user')->get();

        $totalBooks = Book::sum('quantity');
        $availableCopies = Book::sum('available_copies');
        $activeBorrows = BookBorrow::where('status', 'borrowed')->count();
        $overdueBorrows = BookBorrow::where('status', 'borrowed')
            ->where('due_date', '<', Carbon::today())
            ->count();

        return view('library.index', compact(
            'tab',
            'search',
            'books',
            'borrows',
            'categories',
            'students',
            'totalBooks',
            'availableCopies',
            'activeBorrows',
            'overdueBorrows'
        ));
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'book_category_id' => 'nullable|exists:book_categories,id',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'external_pdf_url' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $validated['available_copies'] = $validated['quantity'];

        Book::create($validated);

        return redirect()->route('library.index', ['tab' => 'books'])->with('success', 'បានបញ្ចូលសៀវភៅថ្មីដោយជោគជ័យ!');
    }

    public function borrowBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if ($book->available_copies <= 0) {
            return back()->with('error', 'សៀវភៅនេះអស់ពីស្តុកហើយ មិនអាចខ្ចីបានទៀតទេ!');
        }

        BookBorrow::create([
            'book_id' => $book->id,
            'student_id' => $validated['student_id'],
            'borrow_date' => $validated['borrow_date'],
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
        ]);

        $book->decrement('available_copies');

        return redirect()->route('library.index', ['tab' => 'borrows'])->with('success', 'បានកត់ត្រាការខ្ចីសៀវភៅដោយជោគជ័យ!');
    }

    public function returnBook(BookBorrow $borrow)
    {
        if ($borrow->status === 'returned') {
            return back()->with('error', 'សៀវភៅនេះត្រូវបានប្រគល់រួចហើយ!');
        }

        $borrow->update([
            'return_date' => Carbon::today(),
            'status' => 'returned',
        ]);

        $borrow->book->increment('available_copies');

        return back()->with('success', 'បានកត់ត្រាការប្រគល់សៀវភៅដោយជោគជ័យ!');
    }

    /**
     * Auto-Extract & Convert External PDF URLs (MoEYS Google Sites, Google Drive, Direct PDF).
     */
    public function extractPdf(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string',
        ]);

        $rawUrl = trim($validated['url']);

        // 1. Strip tracking parameters (e.g. ?utm_source=chatgpt.com, fbclid, etc.)
        $cleanUrl = $this->stripTrackingParameters($rawUrl);

        // 2. Direct Google Drive Link
        if (preg_match('/drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:[^&]*&)*id=)([a-zA-Z0-9_-]{25,})/i', $cleanUrl, $match)) {
            $fileId = $match[1];
            $previewUrl = "https://drive.google.com/file/d/{$fileId}/preview";
            return response()->json([
                'success' => true,
                'preview_url' => $previewUrl,
                'all_links' => [
                    [
                        'title' => 'ឯកសារ Google Drive PDF',
                        'file_id' => $fileId,
                        'preview_url' => $previewUrl,
                        'download_url' => "https://drive.google.com/uc?id={$fileId}&export=download",
                    ]
                ],
                'original_url' => $cleanUrl,
                'source_type' => 'google_drive',
            ]);
        }

        // 3. Google Sites Page (sites.google.com) or external web page
        if (str_contains($cleanUrl, 'sites.google.com') || str_contains($cleanUrl, 'moeys.gov.kh') || !str_ends_with(strtolower($cleanUrl), '.pdf')) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'km,en-US;q=0.9,en;q=0.8',
                ])
                ->timeout(12)
                ->get($cleanUrl);

                if ($response->successful()) {
                    $html = $response->body();
                    
                    // Match all Google Drive File IDs
                    preg_match_all('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:[^&]*&)*id=)|docs\.google\.com\/file\/d\/)([a-zA-Z0-9_-]{25,})/i', $html, $matches);
                    
                    $foundIds = array_values(array_unique($matches[1] ?? []));
                    
                    if (!empty($foundIds)) {
                        $allLinks = [];
                        foreach ($foundIds as $fid) {
                            $title = $this->extractNearbyTitle($html, $fid);
                            $preview = "https://drive.google.com/file/d/{$fid}/preview";
                            $allLinks[] = [
                                'title' => $title ?: "សៀវភៅ PDF #{$fid}",
                                'file_id' => $fid,
                                'preview_url' => $preview,
                                'download_url' => "https://drive.google.com/uc?id={$fid}&export=download",
                            ];
                        }

                        return response()->json([
                            'success' => true,
                            'preview_url' => $allLinks[0]['preview_url'],
                            'all_links' => $allLinks,
                            'original_url' => $cleanUrl,
                            'source_type' => 'google_sites',
                            'total_found' => count($allLinks),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // If fetch fails, fall through to direct check
            }
        }

        // 4. Direct PDF Link or fallback URL
        if (preg_match('/\.pdf($|\?)/i', $cleanUrl) || filter_var($cleanUrl, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => true,
                'preview_url' => $cleanUrl,
                'all_links' => [
                    [
                        'title' => 'ឯកសារ PDF ដើម',
                        'preview_url' => $cleanUrl,
                    ]
                ],
                'original_url' => $cleanUrl,
                'source_type' => 'direct_pdf',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'មិនអាចទាញយកតំណភ្ជាប់ PDF ពី URL នេះបានទេ សូមពិនិត្យមើល URL ម្តងទៀត',
            'original_url' => $rawUrl,
        ], 422);
    }

    /**
     * Helper to strip tracking query parameters.
     */
    private function stripTrackingParameters(string $url): string
    {
        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['query'])) {
            return $url;
        }

        parse_str($parsed['query'], $queryParams);

        $trackingKeys = [
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'fbclid', 'gclid', 'msclkid', 'mc_cid', 'mc_eid', '_hsenc', '_hsmi'
        ];

        foreach ($trackingKeys as $key) {
            unset($queryParams[$key]);
        }

        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = $parsed['path'] ?? '';
        $query = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return $scheme . $host . $port . $path . $query . $fragment;
    }

    /**
     * Helper to extract human-readable book title near a Google Drive ID in HTML.
     */
    private function extractNearbyTitle(string $html, string $fileId): string
    {
        $pos = strpos($html, $fileId);
        if ($pos === false) {
            return '';
        }

        $snippet = substr($html, max(0, $pos - 1200), 2400);

        // Pattern 1: Look for .pdf files in snippet
        if (preg_match('/([^\s"\'<>]{2,80}\.pdf)/u', $snippet, $m)) {
            $title = trim(strip_tags($m[1]));
            if (strlen($title) > 3) {
                return $title;
            }
        }

        // Pattern 2: Look for Khmer text inside tags
        if (preg_match_all('/>([^<]{5,80})</u', $snippet, $matches)) {
            foreach ($matches[1] as $text) {
                $text = trim($text);
                if (preg_match('/[\x{1780}-\x{17FF}]/u', $text) && !str_starts_with($text, 'http') && !str_contains($text, 'goog')) {
                    return $text;
                }
            }
        }

        return '';
    }
}
