<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class AdminOrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $organizations = Organization::with('category', 'owner')
            ->paginate(15);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function verify(Organization $organization)
    {
        $organization->update(['is_verified' => true]);

        return redirect()->back()->with('success', 'Organisation vérifiée avec succès!');
    }

    public function unverify(Organization $organization)
    {
        $organization->update(['is_verified' => false]);

        return redirect()->back()->with('success', 'Vérification de l\'organisation annulée!');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->back()->with('success', 'Organisation supprimée avec succès!');
    }
}
