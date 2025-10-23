<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicule;
use Illuminate\Http\Request;

class AdminVehiculeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
{
    $vehicules = Vehicule::with(['owner'])
        ->latest()
        ->paginate(20);

    return view('admin.vehicules.index', compact('vehicules'));
}


    public function verify(Vehicule $vehicule)
    {
        $vehicule->update(['is_verified' => true]);

        return back()->with('success', 'Vehicule verified successfully!');
    }

    public function unverify(Vehicule $vehicule)
    {
        $vehicule->update(['is_verified' => false]);

        return back()->with('success', 'Vehicule unverified.');
    }

    public function destroy(Vehicule $vehicule)
    {
        $vehicule->delete();

        return back()->with('success', 'Vehicule deleted successfully!');
    }
}
