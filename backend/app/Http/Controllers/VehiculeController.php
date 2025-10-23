<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Auth;

class VehiculeController extends Controller
{
    // Show all vehicles and user's vehicles
    public function index(Request $request)
    {
        $vehicules = Vehicule::paginate(12);
        $myVehicules = Vehicule::where('owner_id', Auth::id())->get();

        return view('vehicules.index', [
            'vehicules' => $vehicules,
            'myVehicules' => $myVehicules
        ]);
    }

    // Show vehicles of a specific user
    public function indexuser($id)
    {
        $vehicules = Vehicule::where('owner_id', $id)->get();
        return view('vehicules.indexuser', ['vehicules' => $vehicules]);
    }

    // Show create form
    public function create()
    {
        return view('vehicules.create');
    }

    // Store new vehicule
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
            'availability_start' => 'required|date',
            'availability_end' => 'required|date|after_or_equal:availability_start',
            'location' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data['owner_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('vehicules', 'public');
        }

        Vehicule::create($data);

        return redirect('/vehicules/create')->with('success', 'Vehicule created!');
    }

    // Update existing vehicule
    public function update(Request $request, $id)
    {
        $vehicule = Vehicule::findOrFail($id);

        $data = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer',
            'availability_start' => 'required|date',
            'availability_end' => 'required|date|after_or_equal:availability_start',
            'location' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('vehicules', 'public');
        }

        $vehicule->update($data);

        return redirect()->back()->with('success', 'Vehicule updated successfully.');
    }

    // Delete vehicule
    public function destroy($id)
    {
        $vehicule = Vehicule::findOrFail($id);
        $vehicule->delete();

        return redirect()->back()->with('success', 'Vehicule deleted successfully.');
    }

    public function confirm(Vehicule $vehicule)
{
    $vehicule->status = 'inactive';
    $vehicule->save();

    return redirect()->route('vehicules.index')->with('success', 'Vehicle confirmed and now inactive.');
}

}
