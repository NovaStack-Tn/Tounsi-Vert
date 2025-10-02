<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\OrgCategory;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerOrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $organizations = Organization::where('owner_id', Auth::id())
            ->with('category')
            ->paginate(10);

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
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'zipcode' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'logo_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['owner_id'] = Auth::id();

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $request->file('logo_path')->store('logos', 'public');
        }

        Organization::create($data);

        return redirect()->route('organizer.organizations.index')
            ->with('success', 'Organisation créée avec succès!');
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
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'zipcode' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'logo_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $request->file('logo_path')->store('logos', 'public');
        }

        $organization->update($data);

        return redirect()->route('organizer.organizations.index')
            ->with('success', 'Organisation mise à jour avec succès!');
    }
}
