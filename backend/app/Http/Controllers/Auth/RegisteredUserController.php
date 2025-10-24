<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Support both 'name' (for Breeze tests) and 'first_name'/'last_name' (for actual form)
        $rules = [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:120', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($request->has('name')) {
            $rules['name'] = ['required', 'string', 'max:200'];
        } else {
            $rules['first_name'] = ['required', 'string', 'max:100'];
            $rules['last_name'] = ['required', 'string', 'max:100'];
            $rules['region'] = ['required', 'string', 'max:120'];
            $rules['city'] = ['required', 'string', 'max:120'];
        }

        $request->validate($rules);

        $userData = [
            'email' => $request->email,
            'role' => 'member', // Default role for new users
            'score' => 0, // Starting score
            'password' => Hash::make($request->password),
        ];

        // Handle 'name' field (will be split by mutator) or first_name/last_name
        if ($request->has('name')) {
            $userData['name'] = $request->name;
        } else {
            $userData['first_name'] = $request->first_name;
            $userData['last_name'] = $request->last_name;
            $userData['region'] = $request->region;
            $userData['city'] = $request->city;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
