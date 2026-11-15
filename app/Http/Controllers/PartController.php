<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartController extends Controller
{
    public function index()
    {
        $parts = Part::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('part_code', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when(request('status') === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when(request('status') === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('parts.index', compact('parts'));
    }

    public function create()
    {
        $returnTo = $this->safeReturnTo(
            request('return_to')
        );

        return view('parts.create', compact('returnTo'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePart($request);

        $request->validate([
            'return_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $part = Part::create([
            ...$data,
            'is_active' => true,
        ]);

        $returnTo = $this->safeReturnTo(
            $request->input('return_to')
        );

        $separator = str_contains($returnTo, '?') ? '&' : '?';

        return redirect()->to(
            $returnTo . $separator . 'new_part_id=' . $part->id
        )->with('success', 'Spare part created successfully.');
    }

    public function edit(Part $part)
    {
        return view('parts.edit', compact('part'));
    }

    public function update(Request $request, Part $part)
    {
        $data = $this->validatePart($request, $part);

        // Do not allow editing is_active through this form.
        $part->update($data);

        return redirect()
            ->route('parts.index')
            ->with('success', 'Spare part updated successfully.');
    }

    public function toggleActive(Part $part)
    {
        $part->update([
            'is_active' => !$part->is_active,
        ]);

        return back()->with(
            'success',
            $part->is_active
            ? 'Spare part activated successfully.'
            : 'Spare part deactivated successfully.'
        );
    }

    private function validatePart(
        Request $request,
        ?Part $part = null
    ): array {
        return $request->validate([
            'part_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('parts', 'part_code')
                    ->ignore($part?->id),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'unit' => [
                'required',
                'string',
                'max:50',
            ],

            'default_unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);
    }

    private function safeReturnTo(?string $url): string
    {
        if (
            $url &&
            str_starts_with($url, '/') &&
            !str_starts_with($url, '//') &&
            !str_contains($url, '\\')
        ) {
            return $url;
        }

        return route('parts.index');
    }
}
