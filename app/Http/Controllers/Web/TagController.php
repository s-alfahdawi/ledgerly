<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\AccountContext;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function index()
    {
        $accountId = $this->requireAccountId();

        $tags = Tag::forAccount($accountId)->withCount('transactions')->orderBy('name')->get();

        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $accountId = $this->requireAccountId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $exists = Tag::forAccount($accountId)->where('name', $data['name'])->exists();
        if ($exists) {
            return back()->with('error', 'A tag with this name already exists.');
        }

        Tag::create([
            'account_id' => $accountId,
            'name' => $data['name'],
            'color' => $data['color'],
        ]);

        return back()->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, Tag $tag)
    {
        $accountId = $this->requireAccountId();
        if ($tag->account_id !== $accountId) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $exists = Tag::forAccount($accountId)
            ->where('name', $data['name'])
            ->where('id', '!=', $tag->id)
            ->exists();
        if ($exists) {
            return back()->with('error', 'A tag with this name already exists.');
        }

        $tag->update($data);

        return back()->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag)
    {
        $accountId = $this->requireAccountId();
        if ($tag->account_id !== $accountId) {
            abort(403);
        }

        $tag->delete();

        return back()->with('success', 'Tag deleted successfully.');
    }
}
