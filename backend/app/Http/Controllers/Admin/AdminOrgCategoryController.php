<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrgCategory;
use Illuminate\Http\Request;

class AdminOrgCategoryController extends Controller
{
    /**
     * Display a listing of organization categories
     */
    public function index()
    {
        $categories = OrgCategory::withCount('organizations')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.categories.organizations.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.organizations.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:org_categories,name',
        ]);

        OrgCategory::create($validated);

        return redirect()->route('admin.org-categories.index')
            ->with('success', 'Organization category created successfully.');
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(OrgCategory $orgCategory)
    {
        $organizationsCount = $orgCategory->organizations()->count();
        
        return view('admin.categories.organizations.edit', compact('orgCategory', 'organizationsCount'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, OrgCategory $orgCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:org_categories,name,' . $orgCategory->id,
        ]);

        $orgCategory->update($validated);

        return redirect()->route('admin.org-categories.index')
            ->with('success', 'Organization category updated successfully.');
    }

    /**
     * Remove the specified category
     */
    public function destroy(OrgCategory $orgCategory)
    {
        // Check if category has organizations
        if ($orgCategory->organizations()->count() > 0) {
            return redirect()->route('admin.org-categories.index')
                ->with('error', 'Cannot delete category with existing organizations. Please reassign or delete organizations first.');
        }

        $orgCategory->delete();

        return redirect()->route('admin.org-categories.index')
            ->with('success', 'Organization category deleted successfully.');
    }
}
