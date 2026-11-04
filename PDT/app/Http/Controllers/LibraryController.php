<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookBorrow;
use App\Models\Student;
use Illuminate\Http\Request;
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
}
