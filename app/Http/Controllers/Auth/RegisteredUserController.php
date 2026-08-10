<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:farmer,buyer'],
            'privacy_policy' => ['required', 'accepted'],
        ];

        if ($request->role === 'buyer') {
            $rules['buyer_classification'] = ['required', 'string', 'in:trader,miller,wholesaler,retailer,government,cooperative'];
        }

        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email ?: null,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Pre-create a stub shop with the chosen classification so it is
        // immediately available on the map once the buyer fills in their details.
        if ($request->role === 'buyer' && $request->buyer_classification) {
            $user->shop()->create([
                'name' => $user->name."'s Shop",
                'address' => '',
                'latitude' => 0,
                'longitude' => 0,
                'classification' => $request->buyer_classification,
                'is_active' => false,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
