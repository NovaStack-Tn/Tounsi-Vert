<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;

class VehiculeController extends Controller
{
    //for admin viewing vehicules
    public function index(Request $request)
    {
        // fetch all rows from DB
    $vehicules = Vehicule::paginate(12); 

        // pass to Blade
        return view('vehicules.index', ['vehicules' => $vehicules]);
    }

   public function indexuser($id)
{
    // fetch only vehicles of this owner
    $vehicules = Vehicule::where('owner_id', $id)->get();

    // pass to Blade
    return view('vehicules.indexuser', ['vehicules' => $vehicules]);
}


    

    // show create form
    public function create()
    {
        return view('vehicules.create');
    }

    // store the new vehicule
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
            'owner_id' => 'nullable|integer',
        ]);

        Vehicule::create($data);

        return redirect('/vehicules/create')->with('success', 'Vehicule created!');
    }

    public function update(Request $request, $id)
{
    $vehicule = Vehicule::findOrFail($id);
    $vehicule->update([
        'type' => $request->type,
        'capacity' => $request->capacity,
        'availability_start' => $request->availability_start,
        'availability_end' => $request->availability_end,
        'description' => $request->description,
        'location' => $request->location,
        'status' => $request->status,
    ]);

    return redirect()->back()->with('success', 'Vehicule updated successfully.');
}

// VehiculeController.php
public function destroy($id)
{
    $vehicule = Vehicule::findOrFail($id);
    $vehicule->delete();

    return redirect()->back()->with('success', 'Vehicule deleted successfully.');
}




}
