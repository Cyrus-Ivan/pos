<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login', [
            'branches' => Branch::orderBy('name')->get()
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'photo' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'taken_at' => 'required|integer',
        ]);

        // Enforce "taken now"
        if (abs(now()->timestamp - $request->taken_at) > 60) {
            throw ValidationException::withMessages([
                'email' => 'Photo must be taken live'
            ]);
        }

        // Decode photo
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo);
        $image = base64_decode($image);

        $path = 'login_photos/' . uniqid() . '.jpg';
        Storage::disk('private')->put($path, $image);

        $request->authenticate();
        $request->session()->regenerate();
        $request->session()->put('branch_id', $request->branch_id);

        // Audit log
        auth()->user()->loginAudits()->create([
            'branch_id' => $request->branch_id,
            'photo_path' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_taken_at' => now(),
            'type' => 'in',
        ]);


        return redirect()->intended(route('pos', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        $user = Auth::user();

        if ($user) {
            $user->loginAudits()->create([
                'branch_id' => session('branch_id'), // or nullable
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'type' => 'out',
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
