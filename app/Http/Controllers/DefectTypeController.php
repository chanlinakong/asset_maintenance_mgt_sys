<?php

namespace App\Http\Controllers;

use App\Models\DefectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DefectTypeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        if (!in_array($status, ['active', 'inactive'], true)) {
            $status = '';
        }

        $defectTypes = DefectType::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where(
                    'is_active',
                    $status === 'active'
                );
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view(
                'defect-types.partials.table',
                compact('defectTypes')
            );
        }

        return view('defect-types.index', compact(
            'defectTypes',
            'search',
            'status'
        ));
    }

    public function create(): View
    {
        return view('defect-types.create', [
            'defectType' => new DefectType(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:defect_types,name',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        DefectType::create($validated);

        return redirect()
            ->route('defect-types.index')
            ->with('success', 'Defect type created successfully.');
    }

    public function edit(DefectType $defectType): View
    {
        return view('defect-types.edit', compact('defectType'));
    }

    public function update(
        Request $request,
        DefectType $defectType
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('defect_types', 'name')
                    ->ignore($defectType->id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $defectType->update($validated);

        return redirect()
            ->route('defect-types.index')
            ->with('success', 'Defect type updated successfully.');
    }

    public function toggleActive(
        DefectType $defectType
    ): RedirectResponse {
        $defectType->update([
            'is_active' => !$defectType->is_active,
        ]);

        $message = $defectType->is_active
            ? 'Defect type activated successfully.'
            : 'Defect type deactivated successfully.';

        return redirect()
            ->route('defect-types.index')
            ->with('success', $message);
    }
}
