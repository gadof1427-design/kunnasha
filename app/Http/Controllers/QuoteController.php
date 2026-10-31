<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\User;
use App\Notifications\UserMentionedInQuote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuoteController extends Controller
{
    private const PER_PAGE = 12;

    public function index(): View
    {
        $quotes = Quote::with(['user', 'mentionedUsers'])
            ->latest()
            ->latest('id')
            ->simplePaginate(self::PER_PAGE);

        return view('welcome', [
            'page' => 'Home',
            'quotes' => $quotes,
            'featured' => $this->quoteOfTheDay(),
        ]);
    }

    public function explore(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'newest');

        // قبول قيم الترتيب المعروفة فقط
        if (! in_array($sort, ['newest', 'oldest'], true)) {
            $sort = 'newest';
        }

        $quotes = Quote::with(['user', 'mentionedUsers'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('content', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                $sort === 'oldest',
                fn ($query) => $query->oldest()->oldest('id'),
                fn ($query) => $query->latest()->latest('id')
            )
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('explore', [
            'page' => 'Explore',
            'quotes' => $quotes,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    /** Permalink for a single quote (used by shares, notifications and link previews). */
    public function show(Quote $quote): View
    {
        $quote->load(['user', 'mentionedUsers']);

        return view('quotes.show', [
            'page' => 'Quote',
            'quote' => $quote,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $quote = $request->user()->quotes()->create($validated);

        $this->notifyMentions($quote, $request->user());

        return back()->with('success', __('quotes.published'));
    }

    public function update(Request $request, Quote $quote): RedirectResponse
    {
        abort_unless(
            $quote->user_id === $request->user()->id,
            403
        );

        $validated = $request->validate($this->rules());

        $quote->update($validated);

        $this->notifyMentions($quote, $request->user());

        return back()->with('success', __('quotes.updated'));
    }

    public function destroy(Request $request, Quote $quote): RedirectResponse
    {
        abort_unless(
            $quote->user_id === $request->user()->id,
            403
        );

        $quote->delete();

        // From the permalink page the quote no longer exists, so go home.
        if (preg_match('#/quotes/'.$quote->id.'(?:[/?\#]|$)#', (string) url()->previous())) {
            return redirect()->route('home')->with('success', __('quotes.deleted'));
        }

        return back()->with('success', __('quotes.deleted'));
    }

    /** The same quote for every visitor for the whole day, picked from the date (works on any database). */
    private function quoteOfTheDay(): ?Quote
    {
        $count = Quote::count();

        if ($count === 0) {
            return null;
        }

        return Quote::with(['user', 'mentionedUsers'])
            ->orderBy('id')
            ->skip(crc32(now()->toDateString()) % $count)
            ->first();
    }

    /** @return array<string,array<int,mixed>> */
    private function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:'.Quote::MAX_LENGTH],
            'author' => ['nullable', 'string', 'max:255'],
            'color' => ['required', Rule::in(Quote::COLORS)],
        ];
    }

    /** Sync @mentions and notify only the users who were newly mentioned. */
    private function notifyMentions(Quote $quote, User $author): void
    {
        $newlyMentionedIds = $quote->syncMentions();

        if ($newlyMentionedIds === []) {
            return;
        }

        foreach (User::whereIn('id', $newlyMentionedIds)->get() as $user) {
            $user->notify(new UserMentionedInQuote($quote, $author));
        }
    }
}
