<?php

namespace App\Http\Controllers;

use App\Models\Capital;
use App\Http\Requests\StoreCapitalRequest;
use App\Http\Requests\UpdateCapitalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CapitalController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = Capital::where('user_id', Auth::id());

        // Filter
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        if ($startDate = $request->input('start_date')) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate = $request->input('date')) {
            $query->where('date', '<=', $endDate);
        }

        $totalCapital = (clone $query)->sum('amount');

        $capitals = $query
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('capitals.index', compact('capitals', 'totalCapital'));
    }

    public function create(): View
    {
        return view('capitals.create');
    }

    public function store(StoreCapitalRequest $request): RedirectResponse
    {
        Capital::create(array_merge($request->validated(), [
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('capitals.index')
            ->with('success', 'Entri modal berhasil ditambahkan.');
    }

    public function edit(Capital $capital): View
    {
        $this->authorizeOwner($capital);
        return view('capitals.edit', compact('capital'));
    }

    public function update(UpdateCapitalRequest $request, Capital $capital): RedirectResponse
    {
        $this->authorizeOwner($capital);
        $capital->update($request->validated());

        return redirect()->route('capitals.index')
            ->with('success', 'Entri modal berhasil diperbarui.');
    }

    public function destroy(Capital $capital): RedirectResponse
    {
        $this->authorizeOwner($capital);
        $capital->delete();

        return redirect()->route('capitals.index')
            ->with('success', 'Entri modal berhasil dihapus.');
    }

    private function authorizeOwner(Capital $capital): void
    {
        if ($capital->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }
    }
}
