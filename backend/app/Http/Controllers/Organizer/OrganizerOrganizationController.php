<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrgCategory;
use Illuminate\Http\Request;

class OrganizerOrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $organizations = auth()->user()->organizationsOwned()->with('category')->get();

        return view('organizer.organizations.index', compact('organizations'));
    }

    public function create()
    {
        $categories = OrgCategory::all();

        return view('organizer.organizations.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'org_category_id' => 'required|exists:org_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'region' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'zipcode' => 'nullable|string|max:20',
            'phone_number' => 'nullable|string|max:30',
            'logo_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('logo_path');
        $data['owner_id'] = auth()->id();

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $request->file('logo_path')->store('logos', 'public');
        }

        $organization = Organization::create($data);

        return redirect()->route('organizer.organizations.show', $organization)->with('success', 'Organization created successfully!');
    }

    public function show(Organization $organization)
    {
        $this->authorize('view', $organization);
        
        $organization->load(['category', 'socialLinks', 'events']);

        return view('organizer.organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        $this->authorize('update', $organization);
        
        $categories = OrgCategory::all();

        return view('organizer.organizations.edit', compact('organization', 'categories'));
    }

    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'org_category_id' => 'required|exists:org_categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'region' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'zipcode' => 'nullable|string|max:20',
            'phone_number' => 'nullable|string|max:30',
            'logo_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('logo_path');

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $request->file('logo_path')->store('logos', 'public');
        }

        $organization->update($data);

        return redirect()->route('organizer.organizations.show', $organization)->with('success', 'Organization updated successfully!');
    }
}
